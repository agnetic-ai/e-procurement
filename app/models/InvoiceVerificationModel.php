<?php
class InvoiceVerificationModel
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetInvoiceVerifyHeader($invNumber)
    {
        $query = "
            SELECT inv.invoice_number AS invoiceNumber,
                DATE_FORMAT(inv.invoice_date, '%d %M %Y') AS invoiceDate,
                DATE_FORMAT(inv.due_date, '%d %M %Y') AS dueDate,
                FORMAT(inv.total_amount, 'id-ID') AS invoiceTotal,
                inv.status_code AS invoiceStatus,
                po.po_number AS poNumber,
                DATE_FORMAT(po.po_date, '%d %M %Y') AS poDate,
                FORMAT(po.total_amount, 'id-ID') AS poTotal,
                v.company_name AS vendorName,
                pt.payment_name AS paymentTerms,
                pt.payment_days AS paymentDays,
                inv.notes
            FROM invoices inv
                JOIN purchase_orders po
                    ON po.id = inv.purchase_order_id
                JOIN vendors v
                    ON v.id = inv.vendor_id
                LEFT JOIN payment_terms pt
                    ON pt.id = po.payment_terms_id
            WHERE inv.invoice_number = :invoice_number
               AND inv.status_code = 'INV_DRAFT'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(
            [":invoice_number" => $invNumber]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetInvoiceVerifyItem($invNumber)
    {
        $query = "
                SELECT p.name AS productName,
                pod.quantity AS poQty,
                IFNULL(
                (
                    SELECT SUM(grd2.qty_received)
                    FROM goods_receipt_details grd2
                        JOIN goods_receipts gr2
                            ON gr2.id = grd2.goods_receipt_id
                    WHERE grd2.purchase_order_detail_id = pod.id
                            AND gr2.status_code = 'GR_POSTED'
                ),
                0) AS grQty,
                invd.qty AS invoiceQty,
                FORMAT(pod.unit_price, 'id-ID') AS poUnitPrice,
                FORMAT(invd.unit_price, 'id-ID') AS invoiceUnitPrice,
                FORMAT(invd.qty * invd.unit_price, 'id-ID') AS subtotal,
                CASE
                    WHEN invd.unit_price <> pod.unit_price THEN
                        1
                    ELSE
                        0
                END AS priceMismatch,
                CASE
                    WHEN invd.unit_price < pod.unit_price THEN 'DISCOUNT'
                    WHEN invd.unit_price > pod.unit_price THEN 'PRICE_ADJUSTMENT'
                ELSE 'NORMAL'
                END AS priceChangeType,
                FORMAT(invd.unit_price, 'id-ID') unitPrice
            FROM invoices inv
                JOIN invoice_details invd
                    ON inv.id = invd.invoice_id
                JOIN purchase_order_details pod
                    ON pod.id = invd.purchase_order_detail_id
                JOIN products p
                    ON p.id = pod.product_id
            WHERE inv.invoice_number = :invoice_number
        ORDER BY p.name;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(
            [":invoice_number" => $invNumber]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function SubmitVerifyInvoice(array $payload)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
            SELECT id, status_code
                FROM invoices
                WHERE invoice_number = :invoiceNumber
                FOR UPDATE
            ");
            $stmt->execute([
                ':invoiceNumber' => $payload['invoiceNumber']
            ]);

            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$invoice) {
                throw new Exception('Invoice not found.');
            }

            if ($invoice['status_code'] !== 'INV_DRAFT') {
                throw new Exception('Only DRAFT invoice can be verified.');
            }

            $stmt = $this->db->prepare("
                UPDATE invoices
                SET 
                    status_code = 'INV_VERIFIED',
                    verify_notes = :notes,
                    verified_by = :verifiedBy,
                    verified_at = NOW()
                WHERE id = :invoiceId
            ");
            $stmt->execute([
                ':verifiedBy' => $payload['verifiedBy'],
                ':notes' => $payload['notes'],
                ':invoiceId'  => $invoice['id']
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Invoice has been successfully verified.',
                'invoiceNumber' => $payload['invoiceNumber']
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function SubmitRejectInvoice(array $payload)
    {
        try {
            $this->db->beginTransaction();

            if (empty($payload['invoiceNumber']) || empty($payload['notes'])) {
                throw new Exception('Invoice number and reject reason are required.');
            }

            $stmt = $this->db->prepare("
                SELECT id, status_code
                FROM invoices
                WHERE invoice_number = :invoiceNumber
            ");
            $stmt->execute([
                ':invoiceNumber' => $payload['invoiceNumber']
            ]);

            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$invoice) {
                throw new Exception('Invoice not found.');
            }

            if ($invoice['status_code'] !== 'INV_DRAFT') {
                throw new Exception('Only draft invoice can be rejected.');
            }

            $stmt = $this->db->prepare("
            UPDATE invoices
                SET
                    status_code = 'INV_REJECTED',
                    verify_notes = :notes,
                    verified_by = :userId,
                    verified_at = NOW()
                WHERE id = :invoiceId
            ");

            $stmt->execute([
                ':notes'     => $payload['notes'],
                ':userId'    => $payload['userId'],
                ':invoiceId' => $invoice['id']
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Invoice has been rejected successfully.',
                'invoiceNumber' => $payload['invoiceNumber']
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
