<?php
class CitiesModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetCities()
    {
        $query = "SELECT id citiesId,
                        city_name citiesName,
                        province_name citiesProvince,
                        created_at createAt
                FROM cities";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
