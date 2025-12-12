<?php
class ProductModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetProduct()
    {
        $filter_name = htmlentities($_POST['filterName'] ?? '');

        $query = "SELECT p.id prodcutId,
                        p.name AS productName,
                        v.company_name AS vendorName,
                        FORMAT(pvp.unit_price, 0, 'id_ID') AS unitPrice,
                        p.unit_of_measure AS uof,
                        DATE_FORMAT(pvp.valid_from, '%d-%b-%Y') AS validFrom,
                        DATE_FORMAT(pvp.valid_to, '%d-%b-%Y') AS validTo,
                        sc.status_name AS statusName,
                        sc.status_code AS statusCode,
                        c.name categoryName
                    FROM product_vendor_prices pvp
                        JOIN products p
                            ON pvp.product_id = p.id
                        JOIN vendors v
                            ON pvp.vendor_id = v.id
                        JOIN categories c
                            ON p.category_id = c.id
                            AND category_code = 'products'
                        JOIN status_codes sc
                            ON p.status_code = sc.status_code
                            AND sc.module_code = 'PRODUCT'
                    WHERE pvp.is_active = TRUE";
        $params = [];

        if (!empty($filter_name)) {
            $query .= " AND p.name LIKE ?";
            $params[] = "%$filter_name%";
        }

        $query .= " ORDER BY p.id DESC";

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

    public function AddNewProduct($payload = [])
    {
        if (empty($payload['productName'])) {
            return [
                'success' => false,
                'message' => 'Produk Name harus diisi'
            ];
        }

        if (empty($payload['description'])) {
            return [
                'success' => false,
                'message' => 'Description harus diisi'
            ];
        }

        if (empty($payload['categoryId'])) {
            return [
                'success' => false,
                'message' => 'Category harus diisi'
            ];
        }

        if (empty($payload['uof'])) {
            return [
                'success' => false,
                'message' => 'Unit of Measure harus diisi'
            ];
        }

        if (empty($payload['statusCode'])) {
            return [
                'success' => false,
                'message' => 'Status harus diisi'
            ];
        }

        if (empty($payload['validFrom'])) {
            return [
                'success' => false,
                'message' => 'Valid From harus diisi'
            ];
        }

        if (empty($payload['validTo'])) {
            return [
                'success' => false,
                'message' => 'Valid To harus diisi'
            ];
        }

        $this->db->beginTransaction();
        try {
            $insProduct = "INSERT INTO products
                            (
                                name,
                                description,
                                category_id,
                                unit_of_measure,
                                status_code,
                                created_at
                            )
                            VALUES
                            (
                                :name, 
                                :description, 
                                :category_id, 
                                :unit_of_measure, 
                                :status_code, 
                                NOW()
                            )";
            $stmt = $this->db->prepare($insProduct);
            $stmt->execute([
                ':name' => trim($payload['productName'] ?? ''),
                ':description' => trim($payload['description'] ?? ''),
                ':category_id' => (int)$payload['categoryId'],
                ':unit_of_measure' => trim($payload['uof'] ?? ''),
                ':status_code' => trim($payload['statusCode'] ?? '')
            ]);

            $productId = (int)$this->db->lastInsertId();

            $insProdcutVendor = "INSERT INTO product_vendor_prices
                                (
                                    product_id,
                                    vendor_id,
                                    unit_price,
                                    valid_from,
                                    valid_to,
                                    is_active
                                )
                                VALUES
                                (:product_id, :vendor_id, :unit_price, :valid_from, :valid_to, :is_active)";
            $stmtPrice = $this->db->prepare($insProdcutVendor);
            $stmtPrice->execute([
                ':product_id' => $productId,
                ':vendor_id' => (int)$payload['vendorId'],
                ':unit_price' => $payload['unitPrice'],
                ':valid_from' => trim($payload['validFrom'] ?? ''),
                ':valid_to' => trim($payload['validTo'] ?? ''),
                ':is_active' => 1
            ]);
            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Product berhasil ditambahkan',
                'productName' => $payload['productName']
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed menambah Produk: ' . $e->getMessage()
            ];
        }
    }
}
