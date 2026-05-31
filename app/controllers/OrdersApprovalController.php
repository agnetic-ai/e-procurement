<?php

class OrdersApprovalController extends Controller
{
    private $ordersApproval;
    private $purchase;
    private $workflow;
    private $status;
    public function __construct()
    {
        parent::__construct();
        
        // ApproveByEmail works without login (token-based)
        $action = $_GET['action'] ?? ($_POST['action'] ?? '');
        $currentMethod = basename($_SERVER['REQUEST_URI']);
        if (strpos($currentMethod, 'ApproveByEmail') === false) {
            $this->checkLogin();
        }
        
        $this->ordersApproval = new OrdersApprovalModel();
        $this->purchase = new PurchaseModel();
        $this->workflow = new ApprovalWorkflowModel();
        $this->status = new StatusCodeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Order Approval Management',
            'subtitle' => 'Manage your order approval and detail'
        ];
        $this->view('ordersApproval/index', $data);
    }

    public function GetApprovalList()
    {
        try {
            $response = $this->ordersApproval->GetApprovalList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function GetApprovalDetail()
    {
        $prNumber = $_GET['prNumber'] ?? null;
        $data = [
            'title' => 'Order Approval Detail',
            'subtitle' => 'Manage yout order approval detail',
            'purchase' => $this->purchase->GetPurchaseRequest($prNumber),
            'purchaseDetail' => $this->purchase->GetPurchaseDetail($prNumber),
            'workflow' => $this->workflow->GetApprovalPurchaseRequest($prNumber),
            'status' => $this->status->GetStatusByModuleCode("APR"),
            'level' => $this->session->get("level")
        ];

        $this->view('ordersApproval/ApprovalDetail', $data);
    }

    public function SubmitApproval()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->ordersApproval->SubmitApprovalWorkflow($payload);
            if ($result['success']) {
                // Send email notifications based on approval outcome
                try {
                    $emailService = new EmailNotificationService();
                    $prNumber = $payload['prNumber'];

                    // Refresh PR data after approval
                    $refreshedPr = $this->purchase->GetPurchaseRequest($prNumber);

                    if ($payload['approvalStatus'] === 'APR_REJECTED') {
                        // Notify requester of rejection
                        $emailService->notifyRequesterOnRejected($prNumber, $payload['remarks'] ?? '');
                    } elseif ($refreshedPr['statusCode'] === 'PR_APPROVED') {
                        // Fully approved — notify requester
                        $emailService->notifyRequesterOnApproved($prNumber);
                    } else {
                        // Approved but more levels remain — notify next approver
                        $nextLevel = ($payload['level'] ?? 0) + 1;
                        $emailService->notifyNextApprover($prNumber, $nextLevel);
                    }
                } catch (Exception $e) {
                    error_log("Approval email notification failed: " . $e->getMessage());
                }

                ResponseHelper::created(
                    $result['message']
                );
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            ResponseHelper::serverError('Terjadi kesalahan saat memproses data request' . $e->getMessage());
        }
    }

    /**
     * Approve/Reject PR via email link (token-based, no login required)
     * URL: /ordersapproval/ApproveByEmail?token=xxx&action=approve
     */
    public function ApproveByEmail()
    {
        $token = $_GET['token'] ?? '';
        $action = $_GET['action'] ?? '';

        if (empty($token) || !in_array($action, ['approve', 'reject'])) {
            $this->view('email/approval_result', [
                'success' => false,
                'message' => 'Link tidak valid atau sudah expired.',
                'icon' => '❌',
                'title' => 'Link Tidak Valid'
            ]);
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Find approval record by token
            $stmt = $db->prepare("
                SELECT 
                    pra.id AS approvalId,
                    pra.purchase_request_id AS purchaseId,
                    pra.approver_id AS approverId,
                    pra.level,
                    pra.status_code,
                    pra.approval_token,
                    pr.pr_number AS prNumber,
                    pr.title AS prTitle,
                    pr.status_code AS prStatus
                FROM purchase_request_approvals pra
                JOIN purchase_requests pr ON pra.purchase_request_id = pr.id
                WHERE pra.approval_token = :token
            ");
            $stmt->execute([':token' => $token]);
            $approval = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$approval) {
                $this->view('email/approval_result', [
                    'success' => false,
                    'message' => 'Token tidak ditemukan atau sudah digunakan.',
                    'icon' => '❌',
                    'title' => 'Token Tidak Valid'
                ]);
                return;
            }

            // Check if already processed
            if ($approval['status_code'] !== 'APR_PROCESS') {
                $this->view('email/approval_result', [
                    'success' => false,
                    'message' => 'Approval ini sudah diproses sebelumnya.',
                    'icon' => '⚠️',
                    'title' => 'Sudah Diproses',
                    'prNumber' => $approval['prNumber']
                ]);
                return;
            }

            // Check if PR is still pending
            if (in_array($approval['prStatus'], ['PR_APPROVED', 'PR_REJECTED'])) {
                $this->view('email/approval_result', [
                    'success' => false,
                    'message' => 'PR ini sudah final (Approved/Rejected).',
                    'icon' => '⚠️',
                    'title' => 'PR Sudah Final',
                    'prNumber' => $approval['prNumber']
                ]);
                return;
            }

            // Process approval/rejection
            $db->beginTransaction();

            $statusCode = ($action === 'approve') ? 'APR_APPROVED' : 'APR_REJECTED';
            $payload = [
                'prNumber' => $approval['prNumber'],
                'level' => $approval['level'],
                'approvalStatus' => $statusCode,
                'remarks' => ($action === 'approve') ? 'Approved via email' : 'Rejected via email'
            ];

            // Update this approval
            $this->purchase->UpdateCurrentApprovalPr($approval['purchaseId'], $payload);

            if ($action === 'reject') {
                $this->purchase->RejectPurchaseRequest($approval['purchaseId']);
                $db->commit();

                // Send email notification
                try {
                    $emailService = new EmailNotificationService();
                    $emailService->notifyRequesterOnRejected($approval['prNumber'], 'Rejected via email');
                } catch (Exception $e) {
                    error_log("Email notification failed: " . $e->getMessage());
                }

                $this->view('email/approval_result', [
                    'success' => true,
                    'message' => 'PR berhasil direject.',
                    'icon' => '❌',
                    'title' => 'PR Rejected',
                    'prNumber' => $approval['prNumber']
                ]);
                return;
            }

            // Approve flow
            $prStmt = $db->prepare("SELECT max_approval_level FROM purchase_requests WHERE id = :id");
            $prStmt->execute([':id' => $approval['purchaseId']]);
            $maxLevel = (int) $prStmt->fetchColumn();

            if ($approval['level'] < $maxLevel) {
                $nextLevel = $approval['level'] + 1;
                $this->purchase->ActivateNextApproval($approval['purchaseId'], $nextLevel);
                $this->purchase->UpdatePurchaseRequestLevel($approval['purchaseId'], $nextLevel);
                $db->commit();

                // Notify next level approver
                try {
                    $emailService = new EmailNotificationService();
                    $emailService->notifyNextApprover($approval['prNumber'], $nextLevel);
                } catch (Exception $e) {
                    error_log("Email notification failed: " . $e->getMessage());
                }

                $this->view('email/approval_result', [
                    'success' => true,
                    'message' => 'PR berhasil di-approve. Menunggu approval level berikutnya.',
                    'icon' => '✅',
                    'title' => 'Approved (Level ' . $approval['level'] . ')',
                    'prNumber' => $approval['prNumber']
                ]);
                return;
            }

            // Fully approved
            $this->purchase->ApprovePurchaseRequest($approval['purchaseId']);
            $db->commit();

            // Draft PO
            try {
                $po = new PurchaseOrdersModel();
                $po->DraftPurchaseOrder($payload);
            } catch (Exception $e) {
                error_log("Draft PO failed: " . $e->getMessage());
            }

            // Notify requester
            try {
                $emailService = new EmailNotificationService();
                $emailService->notifyRequesterOnApproved($approval['prNumber']);
            } catch (Exception $e) {
                error_log("Email notification failed: " . $e->getMessage());
            }

            $this->view('email/approval_result', [
                'success' => true,
                'message' => 'PR telah fully approved! Purchase Order draft sudah dibuat.',
                'icon' => '🎉',
                'title' => 'Fully Approved!',
                'prNumber' => $approval['prNumber']
            ]);

        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $this->view('email/approval_result', [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => '💥',
                'title' => 'Error'
            ]);
        }
    }
}
