<?php
class ApprovalWorkflowModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetApprovalWorkflow($amount)
    {
        $query = "SELECT 
                        ar.min_amount        AS minAmount,
                        ar.max_amount        AS maxAmount,
                        ar.total_level       AS totalLevel,
                        al.level             AS approvalLevel,
                        al.level_name        AS approvalLevelName,
                        r.role_code          AS roleCode,
                        r.role_name          AS roleName,
                        u.id                 AS userId,
                        u.username           AS approverUsername,
                        u.full_name          AS approverName
                        FROM approval_rules ar
                        JOIN approval_levels al
                        ON al.level <= ar.total_level
                        JOIN approval_level_roles alr
                        ON alr.approval_level_id = al.id
                        JOIN roles r
                        ON r.id = alr.role_id
                        JOIN users u
                        ON u.role_id = r.id
                        WHERE :amount BETWEEN ar.min_amount 
                        AND IFNULL(ar.max_amount, :amount)
                        AND ar.is_active = 1
                        AND r.is_active = 1
                        AND u.is_active = 1
                        ORDER BY al.level, u.username;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(["amount" => $amount]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
