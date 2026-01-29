<?php
class AssetsModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetListAssetsInStock()
    {
        $serial_number = htmlentities($_POST['serial_number'] ?? '');
        $query = "
            SELECT au.id AS assetUnitId,
                p.name AS assetName,
                au.serial_number AS serialNumber,
                c.name AS categoryName,
                au.status_code AS statusCode,
                sc.status_name AS statusName
            FROM asset_units au
                JOIN products p
                    ON p.id = au.product_id
                LEFT JOIN categories c
                    ON c.id = p.category_id
                JOIN status_codes sc
                    ON sc.status_code = au.status_code
            WHERE au.status_code = 'ASSET_IN_STOCK'
        ";

        $params = [];

        if (!empty($filter_name)) {
            $query .= " AND au.serial_number LIKE ?";
            $params[] = "%$serial_number%";
        }

        $query .= " ORDER BY p.name ASC, au.serial_number ASC;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            error_log("Query: " . $query);
            error_log("Params: " . print_r($params, true));
            return [];
        }
    }

    public function GetTotalAssets(): int
    {
        $query = "
            SELECT COUNT(*) AS total_asset
            FROM asset_units
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function GetAssetsInStock(): int
    {
        $query = "
            SELECT COUNT(*) AS in_stock
            FROM asset_units
            WHERE status_code = 'ASSET_IN_STOCK';
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function GetAssetsAssigned(): int
    {
        $query = "
            SELECT COUNT(*) AS assigned
            FROM asset_units
            WHERE status_code = 'ASSET_ASSIGNED';
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function GetAssetsDamage(): int
    {
        $query = "
            SELECT COUNT(*) AS damaged_lost
            FROM asset_units
            WHERE status_code IN ('ASSET_DAMAGED', 'ASSET_LOST');
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function GetRecentAssets()
    {
        $query = "
            SELECT
                p.name AS assetName,
                au.serial_number AS serialNumber,
                e.full_name AS employeeName,
                aa.assigned_at AS assignedAt,
                sc.status_code AS statusCode,
                sc.status_name AS statusName,
                us.full_name AS fullName
            FROM asset_assignments aa
            JOIN asset_units au ON au.id = aa.asset_unit_id
            JOIN products p     ON p.id = au.product_id
            JOIN employees e    ON e.id = aa.employee_id
            JOIN status_codes sc ON au.status_code = sc.status_code
            JOIN users us ON aa.assigned_by = us.id
            ORDER BY aa.assigned_at DESC
            LIMIT 5
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetAssetUnitsDetail($assetId)
    {
        $query = "
            SELECT au.id AS assetUnitId,
                p.name AS assetName,
                au.serial_number AS serialNumber,
                c.name AS categoryName,
                au.status_code AS statusCode,
                sc.status_name AS statusName
            FROM asset_units au
                JOIN products p
                    ON p.id = au.product_id
                LEFT JOIN categories c
                    ON c.id = p.category_id
                JOIN status_codes sc
                    ON sc.status_code = au.status_code
            WHERE au.id = :assetId
                AND au.status_code = 'ASSET_IN_STOCK'
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([":assetId" => $assetId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetAssetByIdForAssign($assetUnitId)
    {
        $sql = "
            SELECT id
            FROM asset_units
            WHERE id = :id
            AND status_code = 'ASSET_IN_STOCK'
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $assetUnitId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function SubmitAssignAsset(array $payload)
    {
        try {
            $this->db->beginTransaction();
            $asset = $this->GetAssetByIdForAssign($payload["assetId"]);

            if (!$asset) {
                throw new Exception('Asset is no longer available');
            }

            $stmt = $this->db->prepare("
                INSERT INTO asset_assignments (
                        asset_unit_id,
                        employee_id,
                        returned_at,
                        notes,
                        assigned_by,
                        created_at
                    ) VALUES (
                        :asset_unit_id,
                        :employee_id,
                        :assigned_date,
                        :notes,
                        :assigned_by,
                        NOW()
                    )
            ");

            $stmt->execute([
                ':asset_unit_id' => $payload['assetId'],
                ':employee_id'   => $payload['employeeId'],
                ':assigned_date'      => $payload['assignedDate'],
                ':notes'   => $payload['notes'],
                ':assigned_by'       => $payload['assignedBy']
            ]);

            $sql = "
                UPDATE asset_units
                SET status_code = 'ASSET_ASSIGNED',
                    employee_id = :employees_id,
                    updated_at = NOW()
                WHERE id = :id
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(
                [
                    'employees_id' =>  $payload['employeeId'],
                    'id' =>  $payload['assetId']
                ]
            );
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Asset successfully assigned'
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
