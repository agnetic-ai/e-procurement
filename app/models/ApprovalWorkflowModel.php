<?php
class ApprovalWorkflowModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetApprovalWorkflows($payload = [])
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
                        AND al.module_code = ar.module_code  
                    JOIN approval_level_roles alr
                        ON alr.approval_level_id = al.id
                        AND alr.module_code = ar.module_code 
                    JOIN roles r
                        ON r.id = alr.role_id
                    JOIN users u
                        ON u.role_id = r.id
                    WHERE 
                        ar.is_active = 1
                        AND r.is_active = 1
                        AND u.is_active = 1
                        AND ar.module_code = :module_code
                        AND ar.min_amount <= :grand_amount
                        AND (ar.max_amount IS NULL OR ar.max_amount >= :amount)
                    ORDER BY al.level, u.username;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':module_code' => $payload['moduleCode'],
            ':grand_amount' => $payload['amount'],
            ':amount'      => $payload['amount']
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetApprovalPurchaseRequest($prNumber)
    {
        $query = "
            SELECT 
                PRA.id,
                PRA.level,
                PRA.status_code,
                UA.full_name AS username,
                RL.role_name AS roleName,
                SC.status_name AS statusName,
                SC.status_code AS statusCode,
                PRA.remarks
            FROM purchase_requests PR
            JOIN purchase_request_approvals PRA 
                ON PR.id = PRA.purchase_request_id
            JOIN users UA 
                ON PRA.approver_id = UA.id
            JOIN roles RL 
                ON UA.role_id = RL.id
            JOIN status_codes SC 
                ON PRA.status_code = SC.status_code 
            AND SC.module_code = 'APR'
            WHERE PR.pr_number = :pr_number
            ORDER BY PRA.level ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":pr_number" => $prNumber
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function GetApprovalPurchaseOrder($poNumber)
    {
        $query = "SELECT 
                    POA.id,
                    POA.level,
                    POA.status_code,
                    UA.full_name username,
                    RL.role_name roleName,
                    SC.status_name statusName,
                    SC.status_code statusCode,
                    POA.remarks
                FROM purchase_orders PO
                JOIN purchase_orders_approvals POA ON PO.id = POA.purchase_order_id
                JOIN users UA ON POA.approver_id = UA.id
                JOIN roles RL ON UA.role_id = RL.id
                JOIN status_codes SC ON POA.status_code = SC.status_code AND SC.module_code = 'APR'
                WHERE PO.po_number = :po_number
                ORDER BY POA.level ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ":po_number" => $poNumber
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // public function GetApprovalApproved($prNumber): int
    // {
    //     $query = "SELECT 
    //             COUNT(*) AS total_approved
    //           FROM purchase_requests PR
    //           JOIN purchase_request_approvals PRA 
    //             ON PR.id = PRA.purchase_request_id
    //           WHERE PR.pr_number = :pr_number
    //             AND PRA.status_code = :code";

    //     $stmt = $this->db->prepare($query);
    //     $stmt->execute([
    //         ":pr_number" => $prNumber,
    //         ":code" => "APR_APPROVE"
    //     ]);

    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return (int) ($result['total_approved'] ?? 0);
    // }

    // public function GetCurrentApprovalLevel($prNumber)
    // {
    //     $query = "SELECT 
    //                MAX(PRA.level) AS current_level
    //              FROM purchase_requests PR
    //              JOIN purchase_request_approvals PRA 
    //                ON PR.id = PRA.purchase_request_id
    //              WHERE PR.pr_number = :pr_number
    //                AND PRA.status_code = :code";

    //     $stmt = $this->db->prepare($query);
    //     $stmt->execute([
    //         ":pr_number" => $prNumber,
    //         ":code" => "APR_APPROVE"
    //     ]);

    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $result['current_level'] !== null ? (int) $result['current_level'] : null;
    // }
}
