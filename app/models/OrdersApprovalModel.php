<?php
class OrdersApprovalModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetApprovalList()
    {
        $filter_name = htmlentities($_POST['filterName'] ?? '');
        $query = "SELECT PR.id AS requstId,
                    PR.pr_number AS prNumber,
                    PR.title AS title,
                    PR.department AS department,
                    PR.requested_by AS requestedBy,
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
                    SC.status_name AS statusName
                FROM purchase_requests PR
                    JOIN status_codes SC
                        ON PR.status_code = SC.status_code
                        AND SC.module_code = 'PR'
                WHERE 1=1;";

        $params = [];
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
            return [];
        }
    }
}
