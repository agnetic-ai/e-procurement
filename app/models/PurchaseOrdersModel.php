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
    public function GetPoReadyForInvoice()
    {
        $query = "
                SELECT DISTINCT
                    po.id AS purchaseOrderId,
                    po.po_number AS poNumber,
                    v.company_name AS vendorName
                FROM purchase_orders po
                JOIN vendors v ON po.vendor_id = v.id
                WHERE po.status_code = 'PO_COMPLETED'
                AND NOT EXISTS (
                    SELECT 1
                    FROM invoices inv
                    WHERE inv.purchase_order_id = po.id
                )AND EXISTS (
                    SELECT 1
                    FROM goods_receipts gr
                    WHERE gr.purchase_order_id = po.id
                    AND gr.status_code = 'GR_COMPLETED'
                )
                ORDER BY po.po_number ASC
            ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetPoCompleteWithGr()
    {
        $query = "SELECT 
                po.id AS purchaseOrderId,
                po.po_number AS poNumber,
                po.po_date AS poDate,
                v.company_name AS vendorName,
                v.id AS vendorId,
                pt.payment_name AS paymentName,
                pt.payment_days AS paymentDays,
                po.total_amount AS poTotal,
                COUNT(DISTINCT gr.id) AS grCount,
                COALESCE(SUM(grd.qty_received), 0) AS totalQtyReceived,
                COALESCE(SUM(inv.total_amount), 0) AS totalInvoiced,
                CASE 
                    WHEN COALESCE(SUM(inv.total_amount), 0) >= po.total_amount THEN 'FULL'
                    WHEN COALESCE(SUM(inv.total_amount), 0) > 0 THEN 'PARTIAL'
                    ELSE 'NONE'
                END AS invoicingStatus
            FROM purchase_orders po
            INNER JOIN vendors v 
                ON po.vendor_id = v.id
            INNER JOIN payment_terms pt 
                ON po.payment_terms_id = pt.id
            INNER JOIN goods_receipts gr 
                ON po.id = gr.purchase_order_id 
                AND gr.status_code = 'GR_COMPLETED'
            INNER JOIN goods_receipt_details grd 
                ON gr.id = grd.goods_receipt_id
            LEFT JOIN invoices inv 
                ON po.id = inv.purchase_order_id 
                AND inv.status_code IN ('INV_DRAFT', 'INV_VERIFIED', 'INV_PAID')
            WHERE 
                po.status_code = 'PO_COMPLETED'
            GROUP BY 
                po.id, po.po_number, po.po_date, v.company_name, v.id, 
                pt.payment_name, pt.payment_days, po.total_amount
            ORDER BY 
                po.po_date DESC, po.po_number DESC;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetPurchaseOrderNumberApproved()
    {
        $query = "SELECT 
                    po.id purchaseOrderId,
                    po.po_number poNumber
                FROM purchase_orders po 
                WHERE po.status_code IN ('PO_APPROVED' , 'PO_PARTIAL')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
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
                    po.payment_terms_id paymentTermsId,
		            CONCAT(pt.payment_code, ' - ', pt.payment_name, '(', pt.payment_description, ')') paymentTerms,
                    po.current_approval_level currentApprovalLevel,
                    po.max_approval_level maxApprovalLevel
                FROM purchase_orders po 
                JOIN purchase_requests pr ON po.purchase_request_id = pr.id
                JOIN vendors vn ON po.vendor_id = vn.id
                JOIN status_codes sc ON po.status_code = sc.status_code AND sc.module_code ='PO'
                LEFT JOIN users u ON po.received_by = u.id
                LEFT JOIN payment_terms pt ON po.payment_terms_id = pt.id
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


    public function UpdateCurrentApprovalPo(int $poId, array $payload): void
    {
        $stmt = $this->db->prepare("
        UPDATE purchase_orders_approvals
            SET status_code = :status,
                approved_at = NOW(),
                remarks = :remarks
            WHERE purchase_order_id = :po_id
              AND level = :level
              AND status_code = 'APR_PROCESS'
        ");

        $stmt->execute([
            ':status'      => $payload['approvalStatus'],
            ':remarks'     => $payload['remarks'] ?? null,
            ':po_id'       => $poId,
            ':level'       => $payload['level']
        ]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Approval ini sudah diproses atau tidak valid');
        }
    }

    public function RejectPurchaseOrder(int $poId): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_orders
            SET status_code = 'PR_REJECTED'
            WHERE id = :po_id
        ");
        $stmt->execute([':po_id' => $poId]);
    }

    public function ActivateNextApproval(int $poId, int $nextLevel): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_orders_approvals
            SET status_code = 'APR_PROCESS'
            WHERE purchase_order_id = :po_id
              AND level = :level
        ");
        $stmt->execute([
            ':po_id' => $poId,
            ':level' => $nextLevel
        ]);
    }

    public function UpdatePurchaseOrderLevel(int $poId, int $nextLevel): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_orders
            SET current_approval_level = :level,
                status_code = 'PO_PROCESS'
            WHERE id = :po_id
        ");
        $stmt->execute([
            ':level' => $nextLevel,
            ':po_id' => $poId
        ]);
    }

    public function ApprovePurchaseOrder(int $poId): void
    {
        $stmt = $this->db->prepare("
            UPDATE purchase_orders
            SET status_code = 'PO_APPROVED'
            WHERE id = :po_id
        ");
        $stmt->execute([':po_id' => $poId]);
    }
}
