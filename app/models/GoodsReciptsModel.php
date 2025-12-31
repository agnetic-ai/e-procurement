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
                        GR.purchase_request_id purchaseId,
                        PR.pr_number prNumber,
                        PR.department,
                        SC.status_name statusName,
                        GR.status_code statusCode,
                        DATE_FORMAT(GR.receipt_date, '%d %b %Y') receiptDate,
                        UR.full_name receivedBy
                    FROM goods_receipts GR
                        JOIN purchase_requests PR
                            ON GR.purchase_request_id = PR.id
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

    public function GetGoodsReciptsByGrNumber($grNumber)
    {
        $query = "SELECT GR.id grId,
                        GR.gr_number grNumber,
                        GR.purchase_request_id purchaseId,
                        PR.pr_number prNumber,
                        PR.department,
                        SC.status_name statusName,
                        GR.status_code statusCode,
                        DATE_FORMAT(GR.receipt_date, '%Y-%m-%d') receiptDate,
                        UR.full_name receivedBy,
                        GR.notes
                    FROM goods_receipts GR
                        JOIN purchase_requests PR
                            ON GR.purchase_request_id = PR.id
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
                      PRD.quantity,
                      PRD.unit,
                      GRD.qty_received
                  FROM goods_receipts GR
                  JOIN goods_receipt_details GRD ON GR.id = GRD.goods_receipt_id
                  JOIN purchase_request_details PRD ON GRD.purchase_request_detail_id = PRD.id
                  JOIN products PD ON PRD.product_id = PD.id
                  WHERE GR.gr_number = :gr_number;";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':gr_number' => $grNumber,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetAssetUnitsByGrNumber($grNumber)
    {
        $query = "SELECT 
                        AU.id assetId,
                        GRD.id goodsDetailId,
                        PD.name productName,
                        AU.serial_number serialNumber
                    FROM goods_receipts GR
                    JOIN goods_receipt_details GRD ON GR.id = GRD.goods_receipt_id
                    JOIN asset_units AU ON GRD.id = AU.goods_receipt_detail_id
                    JOIN products PD ON AU.product_id = PD.id
                    WHERE GR.gr_number = :gr_number;";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':gr_number' => $grNumber,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function DraftGoodsRecipt($purchaseId)
    {
        try {
            $grNumber = $this->GenerateGrNumber();
            $query = "INSERT INTO goods_receipts (
                        gr_number,
                        purchase_request_id,
                        status_code
                    )
                    SELECT
                        :gr_number,
                        id,
                        'GR_DRAFT'
                    FROM purchase_requests
                    WHERE id = :pr_id
                    AND status_code = 'PR_APPROVED';";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':gr_number' => $grNumber,
                ':pr_id' => $purchaseId,
            ]);

            $goodsId = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO goods_receipt_details  (
                            goods_receipt_id,
                            purchase_request_detail_id,
                            qty_received
                        ) 
                        SELECT 
                            :goods_receipt_id,
                            id,
                            0
                        FROM purchase_request_details
                        WHERE purchase_request_id = :pr_id;";
            $stmtDetail = $this->db->prepare($queryDetail);
            $stmtDetail->execute([
                ':goods_receipt_id' => $goodsId,
                ':pr_id' => $purchaseId,
            ]);

            $isUnit = "SELECT PRD.quantity,
                                PRD.unit,
                                GRD.id AS goods_receipt_detail_id,
                                PRD.product_id
                        FROM purchase_request_details PRD
                        JOIN goods_receipt_details GRD ON PRD.id = GRD.purchase_request_detail_id
                        WHERE purchase_request_id = :pr_id
                        AND unit = 'Unit';";
            $stmtUnit = $this->db->prepare($isUnit);
            $stmtUnit->execute([
                ':pr_id' => $purchaseId,
            ]);

            $unitExists = $stmtUnit->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($unitExists)) {
                $queryUnit = "INSERT INTO asset_units (
                                goods_receipt_detail_id,
                                product_id,
                                status_code
                            ) VALUES (
                                :goods_receipt_detail_id,
                                :product_id,
                                'ASSET_DRAFT'
                            );";

                $stmtUnitInsert = $this->db->prepare($queryUnit);
                foreach ($unitExists as $unit) {
                    for ($i = 1; $i <= $unit['quantity']; $i++) {
                        $stmtUnitInsert->execute([
                            ':goods_receipt_detail_id' => $unit['goods_receipt_detail_id'],
                            ':product_id' => $unit['product_id'],
                        ]);
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'Draft Saved Successfully',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed Submit Goods: ' . $e->getMessage()
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
            JOIN purchase_request_details prd
              ON prd.id = grd.purchase_request_detail_id
            WHERE gr.gr_number = :gr_number
              AND grd.qty_received < prd.quantity");
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
