<?php
class CategoriesModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetCategories()
    {
        $query = "SELECT id categoryId,
                    category_code categoryCode,
                    name categoryName,
                    description 
                FROM categories";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
