<?php
class GoodsReciptsModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function DraftGoodsRecipt($purchaseId)
    {
        try {
            $query = "INSERT INTO goods_receipts (
                        purchase_request_id,
                        status_code
                    )
                    SELECT 
                        id,
                        'GR_DRAFT'
                    FROM purchase_requests
                    WHERE id = :pr_id
                    AND status_code = 'PR_APPROVED';";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
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
}
