<?php
class InvoiceModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetInvoiceProcurementView()
    {
        $conditions = [];
        $params = [];

        if (!empty($_POST['inv_number'])) {
            $conditions[] = "inv.invoice_number LIKE :invNumber";
            $params[':invNumber'] = '%' . $_POST['inv_number'] . '%';
        }

        if (!empty($_POST['po_number'])) {
            $conditions[] = "po.po_number LIKE :poNumber";
            $params[':poNumber'] = '%' . $_POST['po_number'] . '%';
        }

        if (!empty($_POST['vendor_id'])) {
            $conditions[] = "v.id = :vendorId";
            $params[':vendorId'] = $_POST['vendor_id'];
        }

        if (!empty($_POST['invoice_status'])) {
            $conditions[] = "inv.status_code = :status";
            $params[':status'] = $_POST['invoice_status'];
        }

        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            $conditions[] = "DATE(inv.created_at) BETWEEN :startDate AND :endDate";
            $params[':startDate'] = $_POST['start_date'];
            $params[':endDate']   = $_POST['end_date'];
        }

        $where = '';
        if (!empty($conditions)) {
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }

        $query = "
            SELECT
                inv.invoice_number AS invoiceNumber,
                po.po_number AS poNumber,
                v.company_name AS vendorName,
                sc.status_name AS statusName,
                sc.status_code AS statusCode,
                FORMAT(inv.total_amount, 'id-ID')  AS totalAmount,
                DATE_FORMAT(inv.created_at, '%d %b %Y') AS createdAt,
                DATE_FORMAT(inv.due_date, '%d %b %Y') AS dueDate
            FROM invoices inv
            JOIN purchase_orders po ON inv.purchase_order_id = po.id
            JOIN vendors v ON inv.vendor_id = v.id
            JOIN status_codes sc 
                ON inv.status_code = sc.status_code 
            AND sc.module_code = 'INV'
            $where
            ORDER BY inv.created_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

            $invoiceDate = \DateTime::createFromFormat('Y-m-d', $payload['invoiceDate']);
            $dueDate = \DateTime::createFromFormat('Y-m-d', $payload['dueDate']);

            if (!$invoiceDate || !$dueDate) {
                throw new Exception('Invalid date format. Expected YYYY-MM-DD.');
            }

            if ($invoiceDate > $dueDate) {
                throw new Exception('Due date must be on or after invoice date.');
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
