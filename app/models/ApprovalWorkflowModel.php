<?php
class ApprovalWorkflowModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetApprovalWorkflows($pyaload = [])
    {
        $query = "SELECT ar.min_amount AS minAmount,
                        ar.max_amount AS maxAmount,
                        ar.total_level AS totalLevel,
                        al.level AS approvalLevel,
                        al.level_name AS approvalLevelName,
                        r.role_code AS roleCode,
                        r.role_name AS roleName,
                        u.id AS userId,
                        u.username AS approverUsername,
                        u.full_name AS approverName
                    FROM approval_rules ar
                        JOIN approval_levels al
                            ON al.level <= ar.total_level
                        JOIN approval_level_roles alr
                            ON alr.approval_level_id = al.id
                        JOIN roles r
                            ON r.id = alr.role_id
                        JOIN users u
                            ON u.role_id = r.id
                    WHERE :grand_amount
                        BETWEEN ar.min_amount AND IFNULL(ar.max_amount, :amount)
                        AND ar.is_active = 1
                        AND r.is_active = 1
                        AND u.is_active = 1
                    ORDER BY al.level,
                            u.username;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":grand_amount" => $pyaload['amount'],
            ":amount" => $pyaload['amount']
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetApprovalPurchaseRequest($prNumber)
    {
        $query = "SELECT 
                    PRA.id,
                    PRA.level,
                    PRA.status_code,
                    UA.full_name username,
                    RL.role_name roleName,
                    SC.status_name statusName,
                    SC.status_code statusCode
                FROM purchase_requests PR
                JOIN purchase_request_approvals PRA ON PR.id = PRA.purchase_request_id
                JOIN users UA ON PRA.approver_id = UA.id
                JOIN roles RL ON UA.role_id = RL.id
                JOIN status_codes SC ON PRA.status_code = SC.status_code AND SC.module_code = 'APR'
                WHERE PR.pr_number = :pr_number";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":pr_number" => $prNumber
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetApprovalApproved($prNumber): int
    {
        $query = "SELECT 
                COUNT(*) AS total_approved
              FROM purchase_requests PR
              JOIN purchase_request_approvals PRA 
                ON PR.id = PRA.purchase_request_id
              WHERE PR.pr_number = :pr_number
                AND PRA.status_code = :code";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":pr_number" => $prNumber,
            ":code" => "APR_APPROVE"
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total_approved'] ?? 0);
    }

    public function GetCurrentApprovalLevel($prNumber)
    {
        $query = "SELECT 
                   MAX(PRA.level) AS current_level
                 FROM purchase_requests PR
                 JOIN purchase_request_approvals PRA 
                   ON PR.id = PRA.purchase_request_id
                 WHERE PR.pr_number = :pr_number
                   AND PRA.status_code = :code";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":pr_number" => $prNumber,
            ":code" => "APR_APPROVE"
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['current_level'] !== null ? (int) $result['current_level'] : null;
    }
}
