<?php
class HelpersModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    function GenerateRequestNumber($code, $table, $column)
    {
        $year   = date('Y');
        $prefix = "$code-$year-";

        $sql = "SELECT $column
            FROM $table
            WHERE $column LIKE :prefix
            ORDER BY $column DESC
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':prefix' => $prefix . '%'
        ]);

        $lastNumberStr = $stmt->fetchColumn();

        if ($lastNumberStr) {
            $lastNumber = (int) substr($lastNumberStr, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
