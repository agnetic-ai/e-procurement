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


    public function DraftGoodsRecipt($purchaseOrderId)
    {
        try {
            $this->db->beginTransaction();

            $grNumber = $this->GenerateGrNumber();

            $query = "INSERT INTO goods_receipts (
                    gr_number,
                    purchase_order_id,
                    status_code
                )
                SELECT
                    :gr_number,
                    id,
                    'GR_DRAFT'
                FROM purchase_orders
                WHERE id = :po_id
                AND status_code = 'PO_APPROVED'";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':gr_number' => $grNumber,
                ':po_id' => $purchaseOrderId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Purchase Order tidak valid atau belum approved.');
            }

            $goodsReceiptId = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO goods_receipt_details (
                            goods_receipt_id,
                            purchase_order_detail_id,
                            qty_received
                        )
                        SELECT
                            :goods_receipt_id,
                            id,
                            0
                        FROM purchase_order_details
                        WHERE purchase_order_id = :po_id";

            $stmtDetail = $this->db->prepare($queryDetail);
            $stmtDetail->execute([
                ':goods_receipt_id' => $goodsReceiptId,
                ':po_id' => $purchaseOrderId,
            ]);

            $queryUnitCheck = "SELECT 
                                POD.quantity,
                                POD.unit,
                                GRD.id AS goods_receipt_detail_id,
                                POD.product_id
                           FROM purchase_order_details POD
                           JOIN goods_receipt_details GRD
                                ON POD.id = GRD.purchase_order_detail_id
                           WHERE POD.purchase_order_id = :po_id
                           AND POD.unit = 'Unit'";

            $stmtUnit = $this->db->prepare($queryUnitCheck);
            $stmtUnit->execute([
                ':po_id' => $purchaseOrderId,
            ]);

            $unitItems = $stmtUnit->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($unitItems)) {
                $queryAsset = "INSERT INTO asset_units (
                                goods_receipt_detail_id,
                                product_id,
                                status_code
                           )
                           VALUES (
                                :goods_receipt_detail_id,
                                :product_id,
                                'ASSET_DRAFT'
                           )";

                $stmtAsset = $this->db->prepare($queryAsset);

                foreach ($unitItems as $item) {
                    for ($i = 1; $i <= (int)$item['quantity']; $i++) {
                        $stmtAsset->execute([
                            ':goods_receipt_detail_id' => $item['goods_receipt_detail_id'],
                            ':product_id' => $item['product_id'],
                        ]);
                    }
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Draft Goods Receipt berhasil dibuat.'
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();

            return [
                'success' => false,
                'message' => 'Gagal membuat Goods Receipt: ' . $e->getMessage()
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

    function SubmitGoodsReceipt($dto = [])
    {
        try {
            $this->db->beginTransaction();
            $allSerialNumbers = [];

            foreach ($dto['goodsDetails'] as $detail) {
                if (!empty($detail['serialNumbers'])) {
                    foreach ($detail['serialNumbers'] as $sn) {
                        $snValue = trim($sn['serialNumber']);

                        if ($snValue === '') continue;
                        if (in_array($snValue, $allSerialNumbers)) {
                            throw new Exception('Duplicate Serial Number detected: ' . $snValue);
                        }

                        $allSerialNumbers[] = $snValue;
                    }
                }
            }

            $stmtCheckSN = $this->db->prepare("
            SELECT COUNT(*) 
            FROM asset_units 
            WHERE serial_number = :serial_number");

            foreach ($allSerialNumbers as $snValue) {
                $stmtCheckSN->execute([':serial_number' => $snValue]);
                if ($stmtCheckSN->fetchColumn() > 0) {
                    throw new Exception('Serial Number already exists: ' . $snValue);
                }
            }

            $stmtGR = $this->db->prepare("
            UPDATE goods_receipts
            SET received_by = :received_by,
                receipt_date = :receipt_date,
                notes = :notes,
                status_code = 'GR_PROCESS'
            WHERE gr_number = :gr_number
              AND status_code = 'GR_DRAFT'");

            $stmtGR->execute([
                ':received_by'  => $dto['receiveBy'],
                ':receipt_date' => $dto['receiveDate'],
                ':notes' => trim($dto["notes"]),
                ':gr_number'    => $dto['grNumber'],
            ]);

            foreach ($dto['goodsDetails'] as $detail) {
                $stmtDetail = $this->db->prepare("
                UPDATE goods_receipt_details
                SET qty_received = :qty_received
                WHERE id = :id");

                $stmtDetail->execute([
                    ':qty_received' => $detail['receivedQty'],
                    ':id' => $detail['goodsDetailId'],
                ]);

                if (!empty($detail['serialNumbers'])) {
                    foreach ($detail['serialNumbers'] as $sn) {
                        $stmtAsset = $this->db->prepare("
                        UPDATE asset_units
                        SET serial_number = :serial_number,
                            status_code = 'ASSET_IN_STOCK'
                        WHERE id = :asset_id
                    ");
                        $stmtAsset->execute([
                            ':serial_number' => $sn['serialNumber'],
                            ':asset_id'      => $sn['assetId'],
                        ]);
                    }
                }
            }

            $stmtQty = $this->db->prepare("
            SELECT COUNT(*) 
            FROM goods_receipts gr
            JOIN goods_receipt_details grd ON gr.id = grd.goods_receipt_id
            JOIN purchase_order_details pod
              ON pod.id = grd.purchase_order_detail_id
            WHERE gr.gr_number = :gr_number
              AND grd.qty_received < pod.quantity");
            $stmtQty->execute([':gr_number' => $dto['grNumber']]);
            $qtyNotComplete = $stmtQty->fetchColumn();

            $stmtSN = $this->db->prepare("
                SELECT COUNT(*)
                FROM asset_units au
                JOIN goods_receipt_details grd
                ON grd.id = au.goods_receipt_detail_id
                JOIN goods_receipts gr
                ON gr.id = grd.goods_receipt_id
                WHERE gr.gr_number = :gr_number
                AND (au.serial_number IS NULL OR au.serial_number = '')
            ");

            $stmtSN->execute([':gr_number' => $dto['grNumber']]);
            $snNotComplete = $stmtSN->fetchColumn();

            $finalStatus = ($qtyNotComplete == 0 && $snNotComplete == 0)
                ? 'GR_COMPLETE'
                : 'GR_PROCESS';

            $stmtFinal = $this->db->prepare("
            UPDATE goods_receipts
            SET status_code = :status,
                notes = :notes
            WHERE gr_number = :gr_number");
            $stmtFinal->execute([
                ':status' => $finalStatus,
                ':notes' => trim($dto["notes"]),
                ':gr_number' => $dto['grNumber'],
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Goods Receipt Submitted Successfully',
                'RequestNumber' => $dto['grNumber']
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
