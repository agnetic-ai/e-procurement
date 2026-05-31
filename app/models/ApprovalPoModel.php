<?php
class ApprovalPoModel
{
    private $db;
    private $po;
    private $goods;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->po = new PurchaseOrdersModel();
        $this->goods = new GoodsReciptsModel();
    }

    public function GetApprovalPurchaseOrderList()
    {
        $query = "SELECT 
                    po.po_number poNumber,
                    pr.pr_number prNumber,
                    pr.department,
                    poa.status_code statusCode,
                    sc.status_name statusName,
                    DATE_FORMAT(po.po_date, '%d %b %Y') AS poDate,
                    u.full_name AS receivedBy,
                    vn.company_name companyName,
                    FORMAT(po.total_amount, 'id-ID') totalAmount
                FROM purchase_orders po
                JOIN purchase_requests pr ON po.purchase_request_id = pr.id
                JOIN vendors vn ON po.vendor_id = vn.id
                LEFT JOIN users u ON po.received_by = u.id
                JOIN purchase_orders_approvals poa ON po.id = poa.purchase_order_id
				JOIN status_codes sc ON poa.status_code = sc.status_code AND sc.module_code ='APR'
                WHERE poa.approver_id = :approver_id";

        $params = [":approver_id" => $_SESSION['user_id']];
        if (!empty($filter_name)) {
            $query .= " AND po.po_number LIKE ?";
            $params[] = "%$filter_name%";
        }

        $query .= " ORDER BY po.id DESC";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $res =  $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (PDOException $e) {
            error_log("Error fetching approval list: " . $e->getMessage());
            return [];
        }
    }

    public function SubmitApprovalPurchaseOrder($payload = [])
    {
        try {
            $this->db->beginTransaction();
            $GetPo = $this->po->GetPurchaseOrdersByPoNumber($payload["poNumber"]);
            if (in_array($GetPo['statusCode'], ['PO_APPROVED', 'PO_REJECTED'])) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'PO Sudah di Proses'
                ];
            }

            if ($GetPo["currentApprovalLevel"] != $payload["level"]) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Approval gagal. Anda tidak berada pada level approval yang aktif.'
                ];
            }

            $this->po->UpdateCurrentApprovalPo($GetPo['purchaseOrderId'], $payload);

            if ($payload['approvalStatus'] === 'APR_REJECTED') {
                $this->po->RejectPurchaseOrder($GetPo['purchaseOrderId']);
                $this->db->commit();
                return [
                    'success' => true,
                    'message' => 'PO berhasil direject'
                ];
            }

            if ($GetPo['currentApprovalLevel'] < $GetPo['maxApprovalLevel']) {
                $nextLevel = $GetPo['currentApprovalLevel'] + 1;

                $this->po->ActivateNextApproval($GetPo['purchaseOrderId'], $nextLevel);
                $this->po->UpdatePurchaseOrderLevel($GetPo['purchaseOrderId'], $nextLevel);
            } else {
                $this->po->ApprovePurchaseOrder($GetPo['purchaseOrderId']);
            }
            $this->db->commit();
            // $RefseshPo = $this->po->GetPurchaseOrdersByPoNumber($payload["poNumber"]);

            // if ($RefseshPo['statusCode'] == 'PO_APPROVED') {
            //     $this->goods->DraftGoodsRecipt($RefseshPo["purchaseOrderId"]);
            // }

            return [
                'success' => true,
                'message' => 'Submit Approval Workflow Successfully'
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Failed Submit Approval: ' . $e->getMessage()
            ];
        }
    }
}
