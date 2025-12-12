<?php
class StatusCodeModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function  GetStatusByModuleCode($code)
    {
        $query = "SELECT id statusId,
                    module_code moduleCode,
                    status_code statusCode,
                    status_name statusName,
                    description stausDesc,
                    color,
                    display_order displayOrder,
                    is_active,
                    is_default,
                    created_at
                FROM status_codes
                WHERE module_code = :module_code
                ORDER BY display_order ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':module_code' => $code]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
