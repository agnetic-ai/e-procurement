<?php
class InvoiceModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetInvoiceList() {}
    public function GetInvoiceByPoNumber($poNumber)
    {
        $query = "SELECT po.po_number AS poNumber,
                        pod.id AS purchaseOrderDetailId,
                        p.name AS productName,
                        pod.quantity AS orderedQty,
                        IFNULL(SUM(grd.qty_received), 0) AS receivedQty,
                        FORMAT(pod.unit_price, 'id-ID') AS unitPrice,
                        FORMAT(pod.subtotal, 'id-ID') AS subtotal,
                        pod.unit
                    FROM purchase_orders po
                        JOIN purchase_order_details pod
                            ON pod.purchase_order_id = po.id
                        JOIN products p
                            ON p.id = pod.product_id
                        LEFT JOIN goods_receipt_details grd
                            ON grd.purchase_order_detail_id = pod.id
                        LEFT JOIN goods_receipts gr
                            ON gr.id = grd.goods_receipt_id
                    WHERE po.po_number = :po_number
                    GROUP BY pod.id,
                            po.po_number,
                            p.name,
                            pod.quantity,
                            pod.unit_price,
                            pod.subtotal
                    ORDER BY pod.id;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(
            [":po_number" => $poNumber]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function DraftInvoice(array $payload)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM invoices 
                WHERE purchase_order_id = :poId
            ");

            $stmt->execute([':poId' => $payload['poId']]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Invoice for this PO already exists.');
            }

            $stmt = $this->db->prepare("
                    SELECT COUNT(*) 
                    FROM goods_receipts
                    WHERE purchase_order_id = :poId
                    AND status_code = 'GR_COMPLETED'
                ");
            $stmt->execute([':poId' => $payload['poId']]);

            if ($stmt->fetchColumn() == 0) {
                throw new Exception('Invoice can only be created after final GR COMPLETED.');
            }


            $queryInvoice = "
                INSERT INTO invoices (
                    invoice_number,
                    purchase_order_id,
                    vendor_id,
                    invoice_date,
                    due_date,
                    status_code,
                    created_by,
                    notes
                )
                SELECT
                    :invoiceNumber,
                    po.id,
                    po.vendor_id,
                    :invoiceDate,
                    :dueDate,
                    'INV_DRAFT',
                    :createdBy,
                    :notes
                FROM purchase_orders po
                WHERE po.id = :poId
            ";

            $stmt = $this->db->prepare($queryInvoice);
            $stmt->execute([
                ':invoiceNumber' => $payload['invoiceNumber'],
                ':invoiceDate'   => $payload['invoiceDate'],
                ':dueDate'       => $payload['dueDate'],
                ':createdBy'     => $payload['createdBy'],
                ':notes'         => $payload['notes'] ?? null,
                ':poId'          => $payload['poId'],
            ]);

            $invoiceId = $this->db->lastInsertId();

            $queryDetails = "
                INSERT INTO invoice_details (
                    invoice_id,
                    purchase_order_detail_id,
                    product_id,
                    qty,
                    unit_price,
                    subtotal
                )
                SELECT
                    :invoiceId,
                    pod.id,
                    pod.product_id,
                    pod.quantity,
                    pod.unit_price,
                    (pod.quantity * pod.unit_price)
                FROM purchase_order_details pod
                WHERE pod.purchase_order_id = :poId
            ";

            $stmt = $this->db->prepare($queryDetails);
            $stmt->execute([
                ':invoiceId' => $invoiceId,
                ':poId'      => $payload['poId']
            ]);

            $queryTotal = "
                UPDATE invoices
                SET total_amount = (
                    SELECT SUM(subtotal)
                    FROM invoice_details
                    WHERE invoice_id = :invoiceIdSub
                )
                WHERE id = :invoiceIdMain
            ";

            $stmt = $this->db->prepare($queryTotal);
            $stmt->execute([
                ':invoiceIdSub'  => $invoiceId,
                ':invoiceIdMain' => $invoiceId
            ]);


            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Invoice draft created successfully.',
                'InvoiceNumber' => $payload['invoiceNumber']
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
