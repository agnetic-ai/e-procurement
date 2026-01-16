<?php
class GoodsReciptsModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetGoodsReciptsList()
    {
        $filter_name = htmlentities($_POST['filterName'] ?? '');
        $query = "SELECT GR.id grId,
                        GR.gr_number grNumber,
                        PR.id purchaseId,
                        PO.id purchaseOrderId,
                        PO.po_number poNumber,
                        PR.pr_number prNumber,
                        PR.department,
                        SC.status_name statusName,
                        GR.status_code statusCode,
                        DATE_FORMAT(GR.receipt_date, '%d %b %Y') receiptDate,
                        UR.full_name receivedBy
                    FROM goods_receipts GR
                        JOIN purchase_orders PO
                            ON GR.purchase_order_id = PO.id
                        JOIN purchase_requests PR
                        	ON PO.purchase_request_id = PR.id
                        JOIN status_codes SC
                            ON GR.status_code = SC.status_code
                            AND SC.module_code = 'GR'
                        LEFT JOIN users UR
                            ON GR.received_by = UR.id
                    WHERE 1=1";
        $params = [];
        if (!empty($filter_name)) {
            $query .= " AND GR.gr_number LIKE ?";
            $params[] = "%$filter_name%";
        }

        $query .= " ORDER BY GR.id DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetHistoryGoodsReceipts($poNumber)
    {
        $query = "SELECT gr.gr_number AS grNumber,
                    DATE_FORMAT(gr.receipt_date, '%d %b %Y') AS receiptDate,
                    CONCAT(COUNT(grd.id), ' items (', SUM(grd.qty_received), ' units)') AS totalItemsReceived,
                    sc.status_name AS statusName,
                    u.full_name AS receivedBy,
                    sc.status_code statusCode
                FROM goods_receipts gr
                    JOIN purchase_orders po
                        ON po.id = gr.purchase_order_id
                    JOIN goods_receipt_details grd
                        ON grd.goods_receipt_id = gr.id
                    JOIN status_codes sc
                        ON sc.status_code = gr.status_code
                    LEFT JOIN users u
                        ON u.id = gr.received_by
                WHERE po.po_number = :po_number
                GROUP BY gr.id,
                        gr.gr_number,
                        gr.receipt_date,
                        sc.status_name,
                        u.full_name
                ORDER BY gr.receipt_date ASC;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([":po_number" => $poNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetGoodsReciptsByGrNumber($grNumber)
    {
        $query = "SELECT GR.id grId,
                        GR.gr_number grNumber,
                        PR.id purchaseId,
                        PO.id purchaseOrderId,
                        PO.po_number poNumber,
                        PR.pr_number prNumber,
                        PR.department,
                        SC.status_name statusName,
                        GR.status_code statusCode,
                        DATE_FORMAT(GR.receipt_date, '%d %b %Y') receiptDate,
                        UR.full_name receivedBy
                    FROM goods_receipts GR
                        JOIN purchase_orders PO
                            ON GR.purchase_order_id = PO.id
                        JOIN purchase_requests PR
                        	ON PO.purchase_request_id = PR.id
                        JOIN status_codes SC
                            ON GR.status_code = SC.status_code
                            AND SC.module_code = 'GR'
                        LEFT JOIN users UR
                            ON GR.received_by = UR.id
                    WHERE GR.gr_number = :gr_number";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':gr_number' => $grNumber,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetGoodsReceiptsDetailByGrNumber($grNumber)
    {
        $query = "SELECT 
                      GRD.id goodsDetailId,
                      PD.name productName,
                      POD.quantity,
                      POD.unit,
                      GRD.qty_received
                  FROM goods_receipts GR
                  JOIN goods_receipt_details GRD ON GR.id = GRD.goods_receipt_id
                  JOIN purchase_order_details POD ON GRD.purchase_order_detail_id = POD.id
                  JOIN products PD ON POD.product_id = PD.id
                  WHERE GR.gr_number = :gr_number;";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':gr_number' => $grNumber,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetGoodsReceiptsDetailByPoNumber($poNumber)
    {
        $query = "SELECT pod.id AS poDetailId,
                    pod.product_id AS productId,
                    p.name AS productName,
                    p.description AS productDescription,
                    pod.unit AS unit,
                    pod.quantity AS quantityOrdered,
                    IFNULL(SUM(grd.qty_received), 0) AS totalReceived,
                    (pod.quantity - IFNULL(SUM(grd.qty_received), 0)) AS remainingQty,
                    pod.unit_price AS unitPriceRaw,
                    pod.subtotal AS subtotalRaw,
                    FORMAT(pod.unit_price, 'id-ID') AS unitPrice,
                    FORMAT(pod.subtotal, 'id-ID') AS subtotal,
                    FORMAT(SUM(pod.subtotal) OVER (), 'id-ID') AS grandTotal
                FROM purchase_order_details pod
                    JOIN purchase_orders po
                        ON pod.purchase_order_id = po.id
                    JOIN products p
                        ON pod.product_id = p.id
                    LEFT JOIN goods_receipt_details grd
                        ON grd.purchase_order_detail_id = pod.id
                    LEFT JOIN goods_receipts gr
                        ON gr.id = grd.goods_receipt_id
                        AND gr.status_code NOT IN ( 'GR_CANCELLED' )
                WHERE po.po_number = :po_number
                GROUP BY pod.id
                ORDER BY pod.id;";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':po_number' => $poNumber,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetAssetUnitsByPoNumber($poNumber)
    {
        $query = "SELECT 
                        AU.id assetId,
                        GRD.id goodsDetailId,
                        PD.name productName,
                        AU.serial_number serialNumber
                    FROM purchase_orders po
                    LEFT JOIN goods_receipts GR ON GR.purchase_order_id = po.id
                    JOIN goods_receipt_details GRD ON GR.id = GRD.goods_receipt_id
                    JOIN asset_units AU ON GRD.id = AU.goods_receipt_detail_id
                    JOIN products PD ON AU.product_id = PD.id
                    WHERE po.po_number =:po_number;";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':po_number' => $poNumber,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function SubmitGoodsReceipt($payload = [])
    {
        try {
            $this->db->beginTransaction();

            $grNumber = $this->GenerateGrNumber();

            $stmtGr = $this->db->prepare("
                INSERT INTO goods_receipts (
                    gr_number,
                    purchase_order_id,
                    receipt_date,
                    received_by,
                    notes,
                    status_code
                ) VALUES (
                    :gr_number,
                    :purchase_order_id,
                    :receipt_date,
                    :received_by,
                    :notes,
                    'GR_PROCESS'
                )
            ");

            $stmtGr->execute([
                ':gr_number' => $grNumber,
                ':purchase_order_id' => $payload['purchaseOrderId'],
                ':receipt_date' => date('Y-m-d'),
                ':received_by' => $_SESSION['user_id'],
                ':notes' => $payload['notes'] ?? null
            ]);

            $grId = $this->db->lastInsertId();

            $stmtDetail = $this->db->prepare("
                INSERT INTO goods_receipt_details (
                    goods_receipt_id,
                    purchase_order_detail_id,
                    qty_received
                ) VALUES (
                    :goods_receipt_id,
                    :po_detail_id,
                    :qty_received
                )
            ");

            $stmtRemaining = $this->db->prepare("
                    SELECT 
                        pod.quantity - IFNULL(SUM(grd.qty_received), 0) AS remaining
                    FROM purchase_order_details pod
                    LEFT JOIN goods_receipt_details grd 
                        ON grd.purchase_order_detail_id = pod.id
                    LEFT JOIN goods_receipts gr 
                        ON gr.id = grd.goods_receipt_id
                        AND gr.status_code IN ('GR_PROCESS','GR_COMPLETED')
                    WHERE pod.id = :po_detail_id
                    GROUP BY pod.id
                ");

            $stmtAsset = $this->db->prepare("
                        INSERT INTO asset_units (
                            goods_receipt_detail_id,
                            product_id,
                            serial_number,
                            status_code
                        ) VALUES (
                            :goods_receipt_detail_id,
                            :product_id,
                            :serial_number,
                            'ASSET_IN_STOCK'
                        )
                    ");

            foreach ($payload['receivedQty'] as $poDetailId => $qtyReceived) {

                if ($qtyReceived <= 0) {
                    continue;
                }

                $stmtRemaining->execute([':po_detail_id' => $poDetailId]);
                $remaining = (int)$stmtRemaining->fetchColumn();

                if ($qtyReceived > $remaining) {
                    throw new Exception("Qty received melebihi sisa PO.");
                }

                $stmtDetail->execute([
                    ':goods_receipt_id' => $grId,
                    ':po_detail_id' => $poDetailId,
                    ':qty_received' => $qtyReceived
                ]);

                $grDetailId = $this->db->lastInsertId();

                if (!empty($payload['serialNumber'][$poDetailId])) {
                    foreach ($payload['serialNumber'][$poDetailId] as $serial) {
                        if (empty($serial)) {
                            throw new Exception("Serial number wajib diisi.");
                        }

                        $stmtAsset->execute([
                            ':goods_receipt_detail_id' => $grDetailId,
                            ':product_id' => $payload['productMap'][$poDetailId],
                            ':serial_number' => $serial
                        ]);
                    }
                }
            }

            $stmtCheckRemaining = $this->db->prepare("
            SELECT COUNT(*) 
            FROM purchase_order_details pod
            LEFT JOIN (
                SELECT 
                    grd.purchase_order_detail_id,
                    SUM(grd.qty_received) AS total_received
                FROM goods_receipt_details grd
                JOIN goods_receipts gr 
                    ON gr.id = grd.goods_receipt_id
                    AND gr.status_code IN ('GR_PROCESS','GR_COMPLETED')
                GROUP BY grd.purchase_order_detail_id
            ) r ON r.purchase_order_detail_id = pod.id
            WHERE pod.purchase_order_id = :po_id
            AND (pod.quantity - IFNULL(r.total_received, 0)) > 0
        ");

            $stmtCheckRemaining->execute([
                ':po_id' => $payload['purchaseOrderId']
            ]);

            $remainingItems = (int)$stmtCheckRemaining->fetchColumn();

            $poStatus = ($remainingItems === 0)
                ? 'PO_COMPLETED'
                : 'PO_PARTIAL';

            $grStatus = ($poStatus === 'PO_COMPLETED')
                ? 'GR_COMPLETED'
                : 'GR_PROCESS';

            $this->db->prepare("
            UPDATE purchase_orders
            SET status_code = :status
            WHERE id = :po_id
        ")->execute([
                ':status' => $poStatus,
                ':po_id' => $payload['purchaseOrderId']
            ]);

            $this->db->prepare("
            UPDATE goods_receipts
            SET status_code = :status
            WHERE id = :gr_id
        ")->execute([
                ':status' => $grStatus,
                ':gr_id' => $grId
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Goods Receipt berhasil disubmit.',
                'grNumber' => $grNumber
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    function GenerateGrNumber()
    {
        $year   = date('Y');
        $prefix = "GR-$year-";

        $sql = "SELECT gr_number
            FROM goods_receipts
            WHERE gr_number LIKE :prefix
            ORDER BY gr_number DESC
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
