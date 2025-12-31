<?php
class PurchaseOrdersModel
{
    private $db;
    private $purchase;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->purchase = new PurchaseModel();
    }

    public function GetPurchaseOrdersList()
    {
        $poNumber = htmlentities($_POST['poNumber'] ?? '');
        $prNumber = htmlentities($_POST['prNumber'] ?? '');
        $query = "SELECT 
                    po.po_number poNumber,
                    pr.pr_number prNumber,
                    pr.department,
                    po.status_code statusCode,
                    sc.status_name statusName,
                    DATE_FORMAT(po.po_date, '%d %b %Y') AS poDate,
                    u.full_name AS receivedBy,
                    vn.company_name companyName,
                    FORMAT(po.total_amount, 'id-ID') totalAmount
                FROM purchase_orders po
                JOIN purchase_requests pr ON po.purchase_request_id = pr.id
                JOIN vendors vn ON po.vendor_id = vn.id
				JOIN status_codes sc ON po.status_code = sc.status_code AND sc.module_code ='PO'
                LEFT JOIN users u ON po.received_by = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($poNumber)) {
            $query .= " AND po.po_number LIKE :poNumber";
            $params[':poNumber'] = '%' . $poNumber . '%';
        }
        if (!empty($prNumber)) {
            $query .= " AND pr.pr_number LIKE :prNumber";
            $params[':prNumber'] = '%' . $prNumber . '%';
        }
        $query .= " ORDER BY po.id DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetPurchaseOrdersByPoNumber($poNumber)
    {
        $query = "SELECT 
                    po.id purchaseOrderId,
                    po.po_number poNumber,
                    pr.pr_number prNumber,
                    pr.department,
                    po.status_code statusCode,
                    sc.status_name statusName,
                    DATE_FORMAT(po.po_date, '%d %b %Y') AS poDate,
                    u.full_name AS receivedBy,
                    vn.company_name companyName,
                    FORMAT(po.total_amount, 'id-ID') totalAmount,
                    po.notes,
                    po.payment_terms_id paymentTermsId
                FROM purchase_orders po
                JOIN purchase_requests pr ON po.purchase_request_id = pr.id
                JOIN vendors vn ON po.vendor_id = vn.id
                JOIN status_codes sc ON po.status_code = sc.status_code AND sc.module_code ='PO'
                LEFT JOIN users u ON po.received_by = u.id
                WHERE po.po_number = :poNumber";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':poNumber' => $poNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetPurchaseOrderDetailsByPoNumber($poNumber)
    {
        $query = "SELECT 
                    pod.product_id productId,
                    p.name productName,
                    P.description productDescription,
                    pod.quantity,
                    pod.unit,
                    FORMAT(pod.unit_price, 'id-ID') unitPrice,
                    FORMAT(pod.subtotal, 'id-ID') subtotal,
                    FORMAT(SUM(pod.subtotal) OVER (), 'id-ID') AS grandTotal
                FROM purchase_order_details pod
                JOIN purchase_orders po ON pod.purchase_order_id = po.id
                JOIN products p ON pod.product_id = p.id
                JOIN product_vendor_prices pvp ON p.id = pvp.product_id AND po.vendor_id = pvp.vendor_id
                WHERE po.po_number = :poNumber";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':poNumber' => $poNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function SubmitedPurchaseOrder($payload = [])
    {
        $approver =  $payload["approvalPo"] ?? [];
        try {
            $this->db->beginTransaction();
            $queryPo = "UPDATE purchase_orders
                        SET po_date = :po_date,
                            payment_terms_id = :payment_terms_id,
                            notes = :notes,
                            received_by = :received_by,
                            current_approval_level = 1,
                            max_approval_level =:max_approval_level,
                            status_code = :status_code
                        WHERE po_number = :po_number";
            $stmtPo = $this->db->prepare($queryPo);
            $stmtPo->execute([
                ':po_date' => date('Y-m-d'),
                ':payment_terms_id' => $payload['paymentTermsId'],
                ':notes' => $payload['notes'],
                ':received_by' => $payload["receiveBy"],
                ':max_approval_level' => $payload['maxApprovalLevel'],
                ':status_code' => 'PO_SUBMITTED',
                ':po_number' => $payload['poNumber']
            ]);

            $insertApproval = "INSERT INTO purchase_orders_approvals (
                                    purchase_order_id,
                                    approver_id,
                                    level,
                                    status_code
                                )
                               VALUES
                               (
                                    :purchase_order_id,
                                    :approver_id,
                                    :level,
                                    :status_code
                                )";
            $stmtApproval = $this->db->prepare($insertApproval);
            foreach ($approver as $apr) {
                $aprStatus = ($apr["level"] == 1) ? "APR_PROCESS" : "APR_PENDING";

                $stmtApproval->execute([
                    ':purchase_order_id' => $payload["poId"],
                    ':approver_id' => $apr['approverId'],
                    ':level' => $apr['level'],
                    ':status_code' => $aprStatus
                ]);
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Purchase Order submitted successfully.'
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function DraftPurchaseOrder($payload = [])
    {
        try {

            $this->db->beginTransaction();
            $GetPr = $this->purchase->GetPurchaseRequest($payload["prNumber"]);
            if ($GetPr["statusCode"] != "PR_APPROVED") {
                throw new Exception("Purchase Request is not approved.");
            }

            $GetPrVendors = $this->purchase->GetPurchaseDetail($payload["prNumber"]);
            $vendorIds = array_values(
                array_unique(
                    array_column($GetPrVendors, 'vendorId')
                )
            );

            $queryPo = "INSERT INTO purchase_orders (
                            po_number,
                            purchase_request_id,
                            vendor_id,
                            total_amount,
                            status_code
                        )
                        SELECT
                            :po_number,
                            pr.id,
                            prd.vendor_id,
                            SUM(prd.subtotal),
                            'PO_DRAFT'
                        FROM purchase_requests pr
                        JOIN purchase_request_details prd
                        ON pr.id = prd.purchase_request_id
                        WHERE pr.id = :pr_id
                        AND prd.vendor_id = :vendor_id
                        GROUP BY prd.vendor_id;";
            $stmtPo = $this->db->prepare($queryPo);

            foreach ($vendorIds as $vendorId) {
                $poNumber = $this->GeneratePoNumber();

                $stmtPo->execute([
                    ':po_number' => $poNumber,
                    ':pr_id' => $GetPr['purchaseId'],
                    ':vendor_id' => $vendorId
                ]);

                $poId = $this->db->lastInsertId();
                $queryPrd = "INSERT INTO purchase_order_details (
                                    purchase_order_id,
                                    purchase_request_detail_id,
                                    product_id,
                                    quantity,
                                    unit,
                                    unit_price,
                                    subtotal
                                )
                                SELECT
                                    :po_id,
                                    prd.id,
                                    prd.product_id,
                                    prd.quantity,
                                    prd.unit,
                                    pvp.unit_price,
                                    prd.subtotal
                                FROM purchase_request_details prd
                                JOIN product_vendor_prices pvp
                                    ON prd.product_id = pvp.product_id
                                    AND prd.vendor_id = pvp.vendor_id
                                WHERE prd.purchase_request_id = :pr_id
                                AND prd.vendor_id = :vendor_id;";

                $stmtPrd = $this->db->prepare($queryPrd);
                $stmtPrd->execute([
                    ':po_id' => $poId,
                    ':pr_id' => $GetPr['purchaseId'],
                    ':vendor_id' => $vendorId
                ]);
            }
            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Purchase Order(s) drafted successfully.'
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    function GeneratePoNumber()
    {
        $year   = date('Y');
        $prefix = "PO-$year-";

        $sql = "SELECT po_number
            FROM purchase_orders
            WHERE po_number LIKE :prefix
            ORDER BY po_number DESC
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
}
