<?php
class OrdersApprovalModel
{
    private $db;
    private $purchase;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->purchase = new PurchaseModel();
    }

    public function GetApprovalList()
    {
        $filter_name = htmlentities($_POST['filterName'] ?? '');
        $query = 'SELECT
                    PR.id AS requstId,
                    PR.pr_number AS prNumber,
                    PR.title AS title,
                    PR.department AS department,
                    UR.full_name AS requestedBy,
                    PR.request_date AS requestDate,
                    PR.status_code AS statusCode,
                    PR.total_estimated AS totalEstimated,
                    PR.current_approval_level AS currentApprovalLevel,
                    PR.max_approval_level AS maxApprovalLevel,
                    PR.notes AS notes,
                    PR.billing_address AS billingAddress,
                    PR.shipping_address AS shippingAddress,
                    PR.created_at AS createdAt,
                    PR.updated_at AS updatedAt,
                    (SELECT status_name FROM status_codes SC WHERE PR.status_code = SC.status_code AND SC.module_code = "PR") statusName 
                FROM purchase_requests PR
                    JOIN purchase_request_approvals PRA
                        ON PR.id = PRA.purchase_request_id
                    JOIN users UR
                        ON UR.id = PR.requested_by
                WHERE PRA.approver_id = :approverId';

        $params = [":approverId" => $_SESSION['user_id']];
        if (!empty($filter_name)) {
            $query .= " AND PR.pr_number LIKE ?";
            $params[] = "%$filter_name%";
        }

        $query .= " ORDER BY PR.id DESC";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching approval list: " . $e->getMessage());
            return [];
        }
    }

    public function SubmitApprovalWorkflow($payload = [])
    {
        try {
            $this->db->beginTransaction();
            $GetPr =  $this->purchase->GetPurchaseRequest($payload["prNumber"]);

            if (in_array($GetPr['statusCode'], ['PR_APPROVED', 'PR_REJECTED'])) {
                $this->db->rollBack();
                return [
                    'success' => true,
                    'message' => 'PR Sudah di Proses'
                ];
            }

            if ($GetPr["currentApprovalLevel"] != $payload["level"]) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Approval gagal. Anda tidak berada pada level approval yang aktif.'
                ];
            }

            $this->purchase->UpdateCurrentApproval($GetPr['purchaseId'], $payload);

            if ($payload['approvalStatus'] === 'APR_REJECTED') {
                $this->purchase->RejectPurchaseRequest($GetPr['purchaseId']);
                $this->db->commit();
                return [
                    'success' => true,
                    'message' => 'PR berhasil direject'
                ];
            }

            if ($GetPr['currentApprovalLevel'] < $GetPr['maxApprovalLevel']) {
                $nextLevel = $GetPr['currentApprovalLevel'] + 1;

                $this->purchase->ActivateNextApproval($GetPr['purchaseId'], $nextLevel);
                $this->purchase->UpdatePurchaseRequestLevel($GetPr['purchaseId'], $nextLevel);
            } else {
                $this->purchase->ApprovePurchaseRequest($GetPr['purchaseId']);
            }

            $this->db->commit();
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
