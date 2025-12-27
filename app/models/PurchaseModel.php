<?php
class PurchaseModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetPurchaseRequest($prNumber)
    {
        $query = "SELECT
                    id AS purchaseId,
                    pr_number AS prNumber,
                    title AS title,
                    department AS department,
                    requested_by AS requestedBy,
                    request_date AS requestDate,
                    status_code AS statusCode,
                    FORMAT(total_estimated, 'id-ID') AS totalEstimated,
                    current_approval_level AS currentApprovalLevel,
                    max_approval_level AS maxApprovalLevel,
                    notes AS notes,
                    billing_address AS billingAddress,
                    shipping_address AS shippingAddress,
                    created_at AS createdAt,
                    updated_at AS updatedAt
                FROM purchase_requests
                WHERE pr_number = :pr_number;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([":pr_number" => $prNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetPurchaseDetail($prNumber)
    {
        $query = "SELECT
                    PD.id AS purchaseDetailId,
                    PD.purchase_request_id AS purchaseRequestId,
                    PD.product_id AS productId,
                    PD.product_description AS productDescription,
                    PD.quantity AS quantity,
                    PD.unit AS unit,
                    FORMAT(PD.estimated_price, 'id-ID') AS estimatedPrice,
                    FORMAT(PD.subtotal, 'id-ID') AS subtotal,
                    PD.created_at AS createdAt,
                    PD.updated_at AS updatedAt,
                    VN.company_name AS vendorName,
                    PROD.name productName
                FROM purchase_request_details PD
                JOIN purchase_requests PR
                    ON PD.purchase_request_id = PR.id
                JOIN vendors VN
                    ON PD.vendor_id = VN.id
                JOIN products PROD
 	                ON PD.product_id = PROD.id
                WHERE PR.pr_number =:pr_number;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([":pr_number" => $prNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function GeneratePrNumber()
    {
        $year   = date('Y');
        $prefix = "PR-$year-";

        $sql = "SELECT pr_number
            FROM purchase_requests
            WHERE pr_number LIKE :prefix
            ORDER BY pr_number DESC
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':prefix' => $prefix . '%'
        ]);

        $lastPr = $stmt->fetchColumn();

        if ($lastPr) {
            $lastNumber = (int) substr($lastPr, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function SubmitPurchaseRequest($payload = [])
    {
        $product = $payload["productPreview"] ?? [];
        $approver =  $payload["approvalPreview"] ?? [];
        try {

            $prNumber = $this->GeneratePrNumber();

            $this->db->beginTransaction();

            $insPurchase = "INSERT INTO purchase_requests
                                (
                                    pr_number,
                                    title,
                                    department,
                                    requested_by,
                                    request_date,
                                    status_code,
                                    total_estimated,
                                    current_approval_level,
                                    max_approval_level,
                                    notes,
                                    billing_address,
                                    shipping_address
                                )
                                VALUES
                                (
                                    :pr_number,
                                    :title,
                                    :department,
                                    :requested_by,
                                    :request_date,
                                    :status_code,
                                    :total_estimated,
                                    :current_approval_level,
                                    :max_approval_level,
                                    :notes,
                                    :billing_address,
                                    :shipping_address
                                )";
            $stmtPr = $this->db->prepare($insPurchase);
            $stmtPr->execute([
                ":pr_number" => $prNumber,
                ":title" => $payload["title"],
                ":department" => $payload["department"],
                ":requested_by" => $payload["requestedBy"],
                ":request_date" => $payload["requestDate"],
                ":status_code" => "PR_PENDING",
                ":total_estimated" => $payload["budgetEstimate"],
                ":current_approval_level" => 1,
                ":max_approval_level" => $payload["maxApproval"],
                ":notes" => $payload["notes"],
                ":billing_address" => $payload["billingAddress"],
                ":shipping_address" => $payload["shippingAddress"]
            ]);

            $purchaseId = (int)$this->db->lastInsertId();


            $insPurchaseDetail = "INSERT INTO purchase_request_details
                                    (
                                        purchase_request_id,
                                        product_id,
                                        vendor_id,
                                        product_description,
                                        quantity,
                                        unit,
                                        estimated_price,
                                        subtotal
                                    )
                                    VALUES
                                    (
                                        :purchase_request_id,
                                        :product_id,
                                        :vendor_id,
                                        :product_description,
                                        :quantity,
                                        :unit,
                                        :estimated_price,
                                        :subtotal
                                    )";

            $stmtPrd = $this->db->prepare($insPurchaseDetail);
            foreach ($product as $obj) {
                $stmtPrd->execute([
                    ":purchase_request_id" => $purchaseId,
                    ":product_id" => $obj["productId"],
                    ":vendor_id" => $obj["vendorId"],
                    ":product_description" => $obj["productDesc"],
                    ":quantity" => $obj["quantity"],
                    ":unit" => $obj["unit"],
                    ":estimated_price" => $obj["unitPrice"],
                    ":subtotal" => $obj["subtotal"],
                ]);
            }

            $insPurchaseApproval = "INSERT INTO purchase_request_approvals
                                    (
                                        purchase_request_id,
                                        approver_id,
                                        level,
                                        status_code
                                    )
                                    VALUES
                                    (
                                        :purchase_request_id,
                                        :approver_id,
                                        :level,
                                        :status_code
                                    )";

            $stmtApr = $this->db->prepare($insPurchaseApproval);
            foreach ($approver as $apr) {
                $aprStatus = ($apr["level"] == 1) ? "APR_PROCESS" : "APR_PENDING";

                $stmtApr->execute([
                    ":purchase_request_id" => $purchaseId,
                    ":approver_id" => $apr["approverId"],
                    ":level" => $apr["level"],
                    ":status_code" => $aprStatus
                ]);
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Submit Purchase Request Successfully',
                'RequestNumber' => $prNumber
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Failed Submit PR: ' . $e->getMessage()
            ];
        }
    }

    public function UpdateCurrentApproval(int $prId, array $payload): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_request_approvals
            SET status_code = :status,
                approved_at = NOW(),
                remarks = :remarks
            WHERE purchase_request_id = :pr_id
              AND level = :level
              AND status_code = 'APR_PROCESS'
        ");

        $stmt->execute([
            ':status'      => $payload['approvalStatus'],
            ':remarks'     => $payload['remarks'] ?? null,
            ':pr_id'       => $prId,
            ':level'       => $payload['level']
        ]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Approval ini sudah diproses atau tidak valid');
        }
    }

    public function RejectPurchaseRequest(int $prId): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_requests
            SET status_code = 'PR_REJECTED'
            WHERE id = :pr_id
        ");
        $stmt->execute([':pr_id' => $prId]);
    }

    public function ActivateNextApproval(int $prId, int $nextLevel): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_request_approvals
            SET status_code = 'APR_PROCESS'
            WHERE purchase_request_id = :pr_id
              AND level = :level
        ");
        $stmt->execute([
            ':pr_id' => $prId,
            ':level' => $nextLevel
        ]);
    }

    public function UpdatePurchaseRequestLevel(int $prId, int $nextLevel): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_requests
            SET current_approval_level = :level,
                status_code = 'PR_PENDING'
            WHERE id = :pr_id
        ");
        $stmt->execute([
            ':level' => $nextLevel,
            ':pr_id' => $prId
        ]);
    }

    public function ApprovePurchaseRequest(int $prId): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_requests
            SET status_code = 'PR_APPROVED'
            WHERE id = :pr_id
        ");
        $stmt->execute([':pr_id' => $prId]);
    }
}
