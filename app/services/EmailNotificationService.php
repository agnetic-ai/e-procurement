<?php

class EmailNotificationService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Send notification to approver(s) when a new PR is submitted
     */
    public function notifyApproversOnSubmit($prNumber, $purchaseId)
    {
        try {
            // Get PR header
            $pr = $this->getPrHeader($prNumber);
            if (!$pr) return false;

            // Get PR items
            $items = $this->getPrItems($prNumber);

            // Get level 1 approvers (currently active)
            $approvers = $this->getActiveApprovers($purchaseId);

            if (empty($approvers)) {
                error_log("No approvers found for PR $prNumber");
                return false;
            }

            $sentCount = 0;
            foreach ($approvers as $approver) {
                if (empty($approver['email'])) continue;

                $approvalUrl = BASE_URL . 'ordersapproval/GetApprovalDetail?prNumber=' . urlencode($prNumber);
                $approveByEmail = BASE_URL . 'ordersapproval/ApproveByEmail?token=' . urlencode($approver['token'] ?? '') . '&action=approve';
                $rejectByEmail = BASE_URL . 'ordersapproval/ApproveByEmail?token=' . urlencode($approver['token'] ?? '') . '&action=reject';

                $body = $this->renderTemplate('email/pr_submitted', [
                    'approverName'   => $approver['fullName'],
                    'prNumber'       => $pr['prNumber'],
                    'prTitle'        => $pr['title'],
                    'department'     => $pr['department'],
                    'requesterName'  => $pr['requesterName'],
                    'items'          => $items,
                    'totalEstimated' => $pr['totalEstimated'],
                    'approvalUrl'    => $approvalUrl,
                    'approveByEmail' => $approveByEmail,
                    'rejectByEmail'  => $rejectByEmail
                ]);

                $sent = EmailHelper::send(
                    $approver['email'],
                    "🔔 Approval Required - PR {$pr['prNumber']} - {$pr['title']}",
                    $body
                );

                if ($sent) $sentCount++;
            }

            error_log("PR Submit Email: $sentCount/" . count($approvers) . " sent for $prNumber");
            return $sentCount > 0;

        } catch (Exception $e) {
            error_log("Email notifyApproversOnSubmit error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to requester when PR is fully approved
     */
    public function notifyRequesterOnApproved($prNumber)
    {
        try {
            $pr = $this->getPrHeader($prNumber);
            if (!$pr) return false;

            $requester = $this->getUserById($pr['requestedBy']);
            if (!$requester || empty($requester['email'])) return false;

            $items = $this->getPrItems($prNumber);

            $prUrl = BASE_URL . 'ordersRequest/GetRequestDetail?prNumber=' . urlencode($prNumber);

            $body = $this->renderTemplate('email/pr_approved', [
                'requesterName'  => $requester['fullName'],
                'prNumber'       => $pr['prNumber'],
                'prTitle'        => $pr['title'],
                'department'     => $pr['department'],
                'items'          => $items,
                'totalEstimated' => $pr['totalEstimated'],
                'prUrl'          => $prUrl
            ]);

            $sent = EmailHelper::send(
                $requester['email'],
                "✅ PR Approved - {$pr['prNumber']} - {$pr['title']}",
                $body
            );

            error_log("PR Approved Email: " . ($sent ? 'sent' : 'failed') . " for $prNumber to {$requester['email']}");
            return $sent;

        } catch (Exception $e) {
            error_log("Email notifyRequesterOnApproved error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to requester when PR is rejected
     */
    public function notifyRequesterOnRejected($prNumber, $remarks = '')
    {
        try {
            $pr = $this->getPrHeader($prNumber);
            if (!$pr) return false;

            $requester = $this->getUserById($pr['requestedBy']);
            if (!$requester || empty($requester['email'])) return false;

            $prUrl = BASE_URL . 'ordersRequest/GetRequestDetail?prNumber=' . urlencode($prNumber);

            $body = $this->renderTemplate('email/pr_rejected', [
                'requesterName'  => $requester['fullName'],
                'prNumber'       => $pr['prNumber'],
                'prTitle'        => $pr['title'],
                'department'     => $pr['department'],
                'remarks'        => $remarks,
                'prUrl'          => $prUrl
            ]);

            $sent = EmailHelper::send(
                $requester['email'],
                "❌ PR Rejected - {$pr['prNumber']} - {$pr['title']}",
                $body
            );

            error_log("PR Rejected Email: " . ($sent ? 'sent' : 'failed') . " for $prNumber");
            return $sent;

        } catch (Exception $e) {
            error_log("Email notifyRequesterOnRejected error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to next-level approver when previous level approves
     */
    public function notifyNextApprover($prNumber, $nextLevel)
    {
        try {
            $pr = $this->getPrHeader($prNumber);
            if (!$pr) return false;

            $items = $this->getPrItems($prNumber);

            // Get approvers for the next level
            $nextApprovers = $this->getApproversByLevel($pr['purchaseId'], $nextLevel);

            if (empty($nextApprovers)) {
                error_log("No approvers found for PR $prNumber level $nextLevel");
                return false;
            }

            $sentCount = 0;
            foreach ($nextApprovers as $approver) {
                if (empty($approver['email'])) continue;

                $approvalUrl = BASE_URL . 'ordersapproval/GetApprovalDetail?prNumber=' . urlencode($prNumber);
                $approveByEmail = BASE_URL . 'ordersapproval/ApproveByEmail?token=' . urlencode($approver['token'] ?? '') . '&action=approve';
                $rejectByEmail = BASE_URL . 'ordersapproval/ApproveByEmail?token=' . urlencode($approver['token'] ?? '') . '&action=reject';

                $body = $this->renderTemplate('email/pr_submitted', [
                    'approverName'   => $approver['fullName'],
                    'prNumber'       => $pr['prNumber'],
                    'prTitle'        => $pr['title'],
                    'department'     => $pr['department'],
                    'requesterName'  => $pr['requesterName'],
                    'items'          => $items,
                    'totalEstimated' => $pr['totalEstimated'],
                    'approvalUrl'    => $approvalUrl,
                    'approveByEmail' => $approveByEmail,
                    'rejectByEmail'  => $rejectByEmail
                ]);

                $sent = EmailHelper::send(
                    $approver['email'],
                    "🔔 Approval Required (Level $nextLevel) - PR {$pr['prNumber']}",
                    $body
                );

                if ($sent) $sentCount++;
            }

            error_log("PR Next Level Email: $sentCount/" . count($nextApprovers) . " sent for $prNumber level $nextLevel");
            return $sentCount > 0;

        } catch (Exception $e) {
            error_log("Email notifyNextApprover error: " . $e->getMessage());
            return false;
        }
    }

    // ========== HELPER METHODS ==========

    private function getPrHeader($prNumber)
    {
        $stmt = $this->db->prepare("
            SELECT 
                pr.id AS purchaseId,
                pr.pr_number AS prNumber,
                pr.title,
                pr.department,
                pr.requested_by AS requestedBy,
                pr.total_estimated AS totalEstimated,
                pr.current_approval_level AS currentLevel,
                pr.max_approval_level AS maxLevel,
                u.full_name AS requesterName
            FROM purchase_requests pr
            JOIN users u ON pr.requested_by = u.id
            WHERE pr.pr_number = :pr_number
        ");
        $stmt->execute([':pr_number' => $prNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getPrItems($prNumber)
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.name AS product_name,
                v.company_name AS vendor_name,
                prd.quantity,
                prd.unit,
                prd.estimated_price,
                prd.subtotal
            FROM purchase_request_details prd
            JOIN purchase_requests pr ON prd.purchase_request_id = pr.id
            JOIN products p ON prd.product_id = p.id
            JOIN vendors v ON prd.vendor_id = v.id
            WHERE pr.pr_number = :pr_number
        ");
        $stmt->execute([':pr_number' => $prNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getActiveApprovers($purchaseId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.full_name AS fullName,
                u.email,
                pra.level,
                pra.approval_token AS token
            FROM purchase_request_approvals pra
            JOIN users u ON pra.approver_id = u.id
            WHERE pra.purchase_request_id = :pr_id
              AND pra.status_code = 'APR_PROCESS'
            ORDER BY pra.level ASC
        ");
        $stmt->execute([':pr_id' => $purchaseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getApproversByLevel($purchaseId, $level)
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.full_name AS fullName,
                u.email,
                pra.level,
                pra.approval_token AS token
            FROM purchase_request_approvals pra
            JOIN users u ON pra.approver_id = u.id
            WHERE pra.purchase_request_id = :pr_id
              AND pra.level = :level
              AND pra.status_code = 'APR_PROCESS'
        ");
        $stmt->execute([':pr_id' => $purchaseId, ':level' => $level]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUserById($userId)
    {
        $stmt = $this->db->prepare("SELECT id, full_name AS fullName, email FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function renderTemplate($view, $data = [])
    {
        $path = VIEWS_PATH . '/' . $view . '.php';
        if (!file_exists($path)) {
            error_log("Email template not found: $path");
            return '';
        }
        extract($data);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
