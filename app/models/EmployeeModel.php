<?php
class EmployeeModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetEmployeeList($code)
    {
        $query = "
            SELECT e.id AS employeeId,
                e.employee_code AS employeeCode,
                e.full_name AS employeeName
            FROM employees e
            WHERE e.employment_status_code = :employee_code
            ORDER BY e.full_name ASC;
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(
            [":employee_code" => $code]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
