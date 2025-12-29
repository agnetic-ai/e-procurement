<?php
class UnitOfMeansureModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetAllUnits()
    {
        return [
            ['value' => "Unit", 'name' => 'Unit'],
            ['value' => "Pcs", 'name' => 'Pcs']
        ];
    }
}
