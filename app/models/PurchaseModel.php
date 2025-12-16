<?php
class PurchaseModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
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
                                        level,
                                        status_code
                                    )
                                    VALUES
                                    (
                                        :purchase_request_id,
                                        :level,
                                        :status_code
                                    )";

            $stmtApr = $this->db->prepare($insPurchaseApproval);
            foreach ($approver as $apr) {
                $stmtApr->execute([
                    ":purchase_request_id" => $purchaseId,
                    ":level" => $apr["level"],
                    ":status_code" => "PR_PENDING"
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
}
