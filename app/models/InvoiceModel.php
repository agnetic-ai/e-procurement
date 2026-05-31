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

    public function GetInvoiceItemByPoNumber($poNumber)
    {
        $query = "SELECT
                        po.po_number AS poNumber,
                        pod.id AS purchaseOrderDetailId,
                        p.name AS productName,
                        pod.quantity AS orderedQty,
                        IFNULL(grsum.total_gr, 0) AS receivedQty,
                        IFNULL(invsum.total_inv, 0) AS invoicedQty,
                        (IFNULL(grsum.total_gr, 0) - IFNULL(invsum.total_inv, 0)) AS toInvoiceQty,
                        FORMAT(pod.unit_price, 'id-ID') AS unitPrice,
                        FORMAT(pod.subtotal, 'id-ID') AS subtotal,
                        pod.unit
                    FROM purchase_orders po
                    JOIN purchase_order_details pod
                        ON pod.purchase_order_id = po.id
                    JOIN products p
                        ON p.id = pod.product_id
                    LEFT JOIN (
                        SELECT
                            grd.purchase_order_detail_id,
                            SUM(grd.qty_received) AS total_gr
                        FROM goods_receipt_details grd
                        JOIN goods_receipts gr
                            ON gr.id = grd.goods_receipt_id
                            AND gr.status_code = 'GR_POSTED'
                        GROUP BY grd.purchase_order_detail_id
                    ) grsum
                        ON grsum.purchase_order_detail_id = pod.id
                    LEFT JOIN (
                        SELECT
                            invd.purchase_order_detail_id,
                            SUM(invd.qty) AS total_inv
                        FROM invoice_details invd
                        JOIN invoices inv
                            ON inv.id = invd.invoice_id
                            AND inv.status_code IN ('INV_VERIFIED', 'INV_PAID')
                        GROUP BY invd.purchase_order_detail_id
                    ) invsum
                        ON invsum.purchase_order_detail_id = pod.id
                    WHERE po.po_number = :po_number
                    HAVING toInvoiceQty > 0
                    ORDER BY pod.id;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(
            [":po_number" => $poNumber]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetInvoiceSummary($poNumber)
    {
        $queryPo = "
            SELECT id 
            FROM purchase_orders 
            WHERE po_number = :po_number
        ";

        $stmt = $this->db->prepare($queryPo);
        $stmt->execute([
            ':po_number' => $poNumber
        ]);

        $poId = $stmt->fetchColumn();

        if (!$poId) {
            return [];
        }

        $query = "
            SELECT
                inv.invoice_number AS invoiceNumber,
                DATE_FORMAT(inv.invoice_date, '%d %b %Y') AS invoiceDate,
                SUM(invd.qty) AS totalQty,
                FORMAT(inv.total_amount, 'id-ID') AS totalAmount,
                sc.status_name AS statusName,
                sc.status_code AS statusCode,
                FORMAT(invd.subtotal, 'id-ID') AS subtotal
            FROM invoices inv
            JOIN invoice_details invd 
                ON invd.invoice_id = inv.id
            JOIN status_codes sc 
                ON sc.status_code = inv.status_code
                AND sc.module_code = 'INV'
            WHERE inv.purchase_order_id = :po_id
          AND inv.status_code IN ('INV_VERIFIED', 'INV_PAID')
            GROUP BY
                inv.id,
                inv.invoice_number,
                inv.invoice_date,
                inv.total_amount,
                sc.status_name,
                sc.status_code
            ORDER BY inv.created_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':po_id' => $poId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function DraftInvoice(array $payload)
    {
        try {
            $this->db->beginTransaction();

            if (empty($payload['items']) || !is_array($payload['items'])) {
                throw new Exception('Invoice items cannot be empty.');
            }

            $invoiceDate = DateTime::createFromFormat('Y-m-d', $payload['invoiceDate']);
            $dueDate     = DateTime::createFromFormat('Y-m-d', $payload['dueDate']);

            if (!$invoiceDate || !$dueDate) {
                throw new Exception('Invalid date format.');
            }

            if ($dueDate < $invoiceDate) {
                throw new Exception('Due date must be after invoice date.');
            }

            $stmt = $this->db->prepare("
                SELECT vendor_id 
                FROM purchase_orders 
                WHERE id = :poId
            ");
            $stmt->execute([':poId' => $payload['poId']]);
            $po = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$po) {
                throw new Exception('Purchase Order not found.');
            }

            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM goods_receipts
                WHERE purchase_order_id = :poId
                AND status_code = 'GR_POSTED'
            ");
            $stmt->execute([':poId' => $payload['poId']]);

            if ($stmt->fetchColumn() == 0) {
                throw new Exception('Invoice can only be created after GR POSTED.');
            }

            $stmt = $this->db->prepare("
                INSERT INTO invoices (
                    invoice_number,
                    purchase_order_id,
                    vendor_id,
                    invoice_date,
                    due_date,
                    status_code,
                    created_by,
                    notes
                ) VALUES (
                    :invoiceNumber,
                    :poId,
                    :vendorId,
                    :invoiceDate,
                    :dueDate,
                    'INV_DRAFT',
                    :createdBy,
                    :notes
                )
            ");

            $stmt->execute([
                ':invoiceNumber' => $payload['invoiceNumber'],
                ':poId'          => $payload['poId'],
                ':vendorId'      => $po['vendor_id'],
                ':invoiceDate'   => $payload['invoiceDate'],
                ':dueDate'       => $payload['dueDate'],
                ':createdBy'     => $payload['createdBy'],
                ':notes'         => $payload['notes'] ?? null
            ]);

            $invoiceId = $this->db->lastInsertId();

            $stmtRemaining = $this->db->prepare("
                SELECT
                    pod.product_id,
                    pod.unit_price AS po_price,
                    (
                        IFNULL(SUM(
                            CASE 
                                WHEN gr.status_code = 'GR_POSTED'
                                THEN grd.qty_received 
                                ELSE 0 
                            END
                        ), 0)
                        -
                        IFNULL(SUM(
                            CASE
                                WHEN inv.status_code IN ('INV_VERIFIED','INV_PAID')
                                THEN invd.qty
                                ELSE 0
                            END
                        ), 0)
                    ) AS remaining_qty
                FROM purchase_order_details pod
                LEFT JOIN goods_receipt_details grd 
                    ON grd.purchase_order_detail_id = pod.id
                LEFT JOIN goods_receipts gr 
                    ON gr.id = grd.goods_receipt_id
                LEFT JOIN invoice_details invd 
                    ON invd.purchase_order_detail_id = pod.id
                LEFT JOIN invoices inv 
                    ON inv.id = invd.invoice_id
                WHERE pod.id = :podId
                AND pod.purchase_order_id = :poId
                GROUP BY pod.id;
            ");

            $stmtDetail = $this->db->prepare("
                INSERT INTO invoice_details (
                    invoice_id,
                    purchase_order_detail_id,
                    product_id,
                    qty,
                    unit_price,
                    subtotal
                ) VALUES (
                    :invoice_id,
                    :pod_id,
                    :product_id,
                    :qty,
                    :unit_price,
                    :subtotal
                )
            ");

            $totalAmount = 0;

            foreach ($payload['items'] as $item) {
                if ($item['qty'] <= 0 || $item['unitPrice'] <= 0) {
                    throw new Exception('Invalid qty or unit price.');
                }

                $stmtRemaining->execute([
                    ':podId' => $item['purchaseOrderDetailId'],
                    ':poId'  => $payload['poId']
                ]);

                $row = $stmtRemaining->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    throw new Exception('Invalid PO item.');
                }

                if ($item['qty'] > $row['remaining_qty']) {
                    throw new Exception('Invoice qty exceeds remaining GR quantity.');
                }

                if ((float)$item['unitPrice'] != (float)$row['po_price'] && empty($payload['notes'])) {
                    throw new Exception('Price change requires notes.');
                }

                $subtotal = $item['qty'] * $item['unitPrice'];

                $stmtDetail->execute([
                    ':invoice_id' => $invoiceId,
                    ':pod_id'     => $item['purchaseOrderDetailId'],
                    ':product_id' => $row['product_id'],
                    ':qty'        => $item['qty'],
                    ':unit_price' => $item['unitPrice'],
                    ':subtotal'   => $subtotal
                ]);

                $totalAmount += $subtotal;
                if ((float)$item['unitPrice'] != (float)$row['po_price']) {
                    $invoiceDetailId = $this->db->lastInsertId();
                    $priceDiff = $item['unitPrice'] - $row['po_price'];
                    $changeType = $priceDiff < 0 ? 'DISCOUNT' : 'PRICE_ADJUSTMENT';

                    $stmtAudit = $this->db->prepare("
                    INSERT INTO invoice_price_audits (
                        invoice_id,
                        invoice_detail_id,
                        purchase_order_detail_id,
                        product_id,
                        po_unit_price,
                        invoice_unit_price,
                        price_diff,
                        change_type,
                        reason,
                        created_by
                    ) VALUES (
                        :invoiceId,
                        :invoiceDetailId,
                        :podId,
                        :productId,
                        :poPrice,
                        :invPrice,
                        :diff,
                        :type,
                        :reason,
                        :userId
                    )
                ");

                    $stmtAudit->execute([
                        ':invoiceId'        => $invoiceId,
                        ':invoiceDetailId'  => $invoiceDetailId,
                        ':podId'            => $item['purchaseOrderDetailId'],
                        ':productId'        => $row['product_id'],
                        ':poPrice'          => $row['po_price'],
                        ':invPrice'         => $item['unitPrice'],
                        ':diff'             => $priceDiff,
                        ':type'             => $changeType,
                        ':reason'           => $payload['notes'],
                        ':userId'           => $payload['createdBy']
                    ]);
                }
            }

            $stmt = $this->db->prepare("
                UPDATE invoices
                SET total_amount = :total
                WHERE id = :invoiceId
            ");
            $stmt->execute([
                ':total'     => $totalAmount,
                ':invoiceId' => $invoiceId
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
