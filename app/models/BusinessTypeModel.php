<?php
class BusinessTypeModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetBusiness()
    {
        $query = "SELECT id businessId,
                        type_code businessCode,
                        type_name businessName,
                        description businessDesc,
                        created_at createAt
                    FROM business_types";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
