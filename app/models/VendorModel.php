<?php
class VendorModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetVendorList($filters = [])
    {
        $filter_name = htmlentities($filters['filterName'] ?? '');
        $filter_email = htmlentities($filters['filterStatus'] ?? '');

        $query = "SELECT 
                    v.id AS vendorId,
                    v.vendor_code AS vendorCode,
                    v.company_name AS companyName,
                    v.email,
                    v.phone,
                    CONCAT(v.address, ', ', c.city_name, ', ', c.province_name) AS fullAddress,
                    c.city_name AS cityName,
                    c.province_name AS provinceName,
                    bt.type_name AS businessType,
                    bt.type_code AS businessTypeCode,
                    sc.status_name AS vendorStatus,
                    sc.status_code AS statusCode,
                    sc.color AS statusColor,
                    v.tax_number AS taxNumber,
                    pt.id paymentId,
                    pt.payment_name AS paymentTerms,
                    v.website,
                    DATE_FORMAT(v.created_at, '%d-%m-%Y %H:%i') AS registrationDate,
                    v.updated_at AS updatedAt,
                    vc.contact_name AS primaryContact,
                    vc.contact_email AS primaryContactEmail,
                    vc.contact_phone AS primaryContactPhone,
                    vc.position AS contactPosition,
                    vba.bank_name AS primaryBankName,
                    vba.account_number AS primaryBankAccountNumber,
                    vba.account_name AS primaryBankAccountName,
                    vc_count.contact_count AS totalContacts
                FROM vendors v
                INNER JOIN cities c ON v.city_id = c.id
                INNER JOIN business_types bt ON v.business_type_id = bt.id
                INNER JOIN status_codes sc ON v.status_code = sc.status_code
                LEFT JOIN vendor_contacts vc ON v.id = vc.vendor_id AND vc.is_primary = 1
                LEFT JOIN vendor_bank_accounts vba ON v.id = vba.vendor_id AND vba.is_primary = 1
                LEFT JOIN payment_terms pt ON v.payment_terms_id = pt.id
                LEFT JOIN (
                    SELECT vendor_id, COUNT(*) AS contact_count
                    FROM vendor_contacts
                    GROUP BY vendor_id
                ) vc_count ON v.id = vc_count.vendor_id
                WHERE 1=1";

        $params = [];

        if (!empty($filter_name)) {
            $query .= " AND v.company_name LIKE ?";
            $params[] = "%$filter_name%";
        }

        if (!empty($filter_email)) {
            $query .= " AND v.email LIKE ?";
            $params[] = "%$filter_email%";
        }

        $query .= " ORDER BY v.id DESC";

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

    public function GetVendorActive()
    {
        $query = "SELECT 
                    v.id AS vendorId,
                    v.vendor_code AS vendorCode,
                    v.company_name AS companyName,
                    v.email,
                    v.phone,
                    CONCAT(v.address, ', ', c.city_name, ', ', c.province_name) AS fullAddress,
                    c.city_name AS cityName,
                    c.province_name AS provinceName,
                    bt.type_name AS businessType,
                    bt.type_code AS businessTypeCode,
                    sc.status_name AS vendorStatus,
                    sc.status_code AS statusCode,
                    sc.color AS statusColor,
                    v.tax_number AS taxNumber,
                    pt.id paymentId,
                    pt.payment_name AS paymentTerms,
                    v.website,
                    DATE_FORMAT(v.created_at, '%d-%m-%Y %H:%i') AS registrationDate,
                    v.updated_at AS updatedAt,
                    vc.contact_name AS primaryContact,
                    vc.contact_email AS primaryContactEmail,
                    vc.contact_phone AS primaryContactPhone,
                    vc.position AS contactPosition,
                    vba.bank_name AS primaryBankName,
                    vba.account_number AS primaryBankAccountNumber,
                    vba.account_name AS primaryBankAccountName,
                    vc_count.contact_count AS totalContacts
                FROM vendors v
                INNER JOIN cities c ON v.city_id = c.id
                INNER JOIN business_types bt ON v.business_type_id = bt.id
                INNER JOIN status_codes sc ON v.status_code = sc.status_code
                LEFT JOIN vendor_contacts vc ON v.id = vc.vendor_id AND vc.is_primary = 1
                LEFT JOIN vendor_bank_accounts vba ON v.id = vba.vendor_id AND vba.is_primary = 1
                LEFT JOIN payment_terms pt ON v.payment_terms_id = pt.id
                LEFT JOIN (
                    SELECT vendor_id, COUNT(*) AS contact_count
                    FROM vendor_contacts
                    GROUP BY vendor_id
                ) vc_count ON v.id = vc_count.vendor_id
                WHERE v.status_code = 'VEND_ACTIVE'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetVendorByCode($vendorCode)
    {
        $query = "SELECT 
                    v.id AS vendorId,
                    v.vendor_code AS vendorCode,
                    v.company_name AS companyName,
                    v.email,
                    v.phone,
                    CONCAT(v.address, ', ', c.city_name, ', ', c.province_name) AS fullAddress,
                    c.city_name AS cityName,
                    c.id citiesId,
                    c.province_name AS provinceName,
                    bt.id businessId,
                    bt.type_name AS businessType,
                    bt.type_code AS businessTypeCode,
                    sc.status_name AS vendorStatus,
                    sc.status_code AS statusCode,
                    sc.color AS statusColor,
                    v.tax_number AS taxNumber,
                    pt.id paymentId,
                    pt.payment_name AS paymentTerms,
                    v.website,
                    DATE_FORMAT(v.created_at, '%d-%m-%Y %H:%i') AS registrationDate,
                    v.updated_at AS updatedAt,
                    vc.contact_name AS primaryContact,
                    vc.contact_email AS primaryContactEmail,
                    vc.contact_phone AS primaryContactPhone,
                    vc.position AS contactPosition,
                    vba.bank_name AS primaryBankName,
                    vba.account_number AS primaryBankAccountNumber,
                    vba.account_name AS primaryBankAccountName,
                    vc_count.contact_count AS totalContacts
                FROM vendors v
                INNER JOIN cities c ON v.city_id = c.id
                INNER JOIN business_types bt ON v.business_type_id = bt.id
                INNER JOIN status_codes sc ON v.status_code = sc.status_code
                LEFT JOIN vendor_contacts vc ON v.id = vc.vendor_id AND vc.is_primary = 1
                LEFT JOIN vendor_bank_accounts vba ON v.id = vba.vendor_id AND vba.is_primary = 1
                LEFT JOIN payment_terms pt ON v.payment_terms_id = pt.id
                LEFT JOIN (
                    SELECT vendor_id, COUNT(*) AS contact_count
                    FROM vendor_contacts
                    GROUP BY vendor_id
                ) vc_count ON v.id = vc_count.vendor_id
                WHERE v.vendor_code =:vendor_code";
        $stmt = $this->db->prepare($query);
        $stmt->execute([":vendor_code" => $vendorCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetVendorsProduct($payload = [])
    {
        $query = "SELECT 
                    v.id              AS vendorId,
                    v.vendor_code     AS vendorCode,
                    v.company_name    AS companyName,
                    v.email,
                    v.phone,
                    pvp.unit_price    AS unitPrice,
                    pvp.valid_from    AS validFrom,
                    pvp.valid_to      AS validTo
                FROM product_vendor_prices pvp
                JOIN vendors v
                    ON v.id = pvp.vendor_id
                WHERE pvp.product_id = :product_id
                AND pvp.is_active = 1
                AND v.status_code = 'VEND_ACTIVE';";
        $stmt = $this->db->prepare($query);
        $stmt->execute([":product_id" => $payload["productId"]]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function RegisterVendor($vendorData = [])
    {
        if (empty($vendorData['vendorName'])) {
            return [
                'success' => false,
                'message' => 'Nama vendor harus diisi'
            ];
        }
        if (empty($vendorData['vendorEmail'])) {
            return [
                'success' => false,
                'message' => 'Email vendor harus diisi'
            ];
        }

        if (empty($vendorData['cityId'])) {
            return [
                'success' => false,
                'message' => 'Kota harus dipilih'
            ];
        }

        if (empty($vendorData['businessType'])) {
            return [
                'success' => false,
                'message' => 'Tipe bisnis harus dipilih'
            ];
        }

        if (!filter_var($vendorData['vendorEmail'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Format email tidak valid'
            ];
        }

        $checkEmailQuery = "SELECT id FROM vendors WHERE email = :email";
        $checkStmt = $this->db->prepare($checkEmailQuery);
        $checkStmt->execute(['email' => $vendorData['vendorEmail']]);

        if ($checkStmt->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'Email sudah terdaftar'
            ];
        }

        $vendor_code = $this->generateVendorCode();

        $status_code = 'VEND_ACTIVE';

        try {
            $this->db->beginTransaction();

            $insertQuery = "INSERT INTO vendors (
                vendor_code,
                company_name,
                email,
                phone,
                address,
                city_id,
                business_type_id,
                tax_number,
                payment_terms_id,
                website,
                status_code,
                created_at,
                updated_at
            ) VALUES (
                :vendor_code,
                :company_name,
                :email,
                :phone,
                :address,
                :city_id,
                :business_type_id,
                :tax_number,
                :payment_terms_id,
                :website,
                :status_code,
                NOW(),
                NOW()
            )";

            $insertStmt = $this->db->prepare($insertQuery);

            $params = [
                ':vendor_code' => $vendor_code,
                ':company_name' => trim($vendorData['vendorName']),
                ':email' => trim($vendorData['vendorEmail']),
                ':phone' => trim($vendorData['vendorPhone'] ?? ''),
                ':address' => trim($vendorData['address'] ?? ''),
                ':city_id' => (int)$vendorData['cityId'],
                ':business_type_id' => (int)$vendorData['businessType'],
                ':tax_number' => trim($vendorData['taxNumber'] ?? ''),
                ':payment_terms_id' => trim($vendorData['paymentTerms'] ?? ''),
                ':website' => trim($vendorData['website'] ?? ''),
                ':status_code' => $status_code
            ];

            $insertStmt->execute($params);

            $vendorId = $this->db->lastInsertId();
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Vendor berhasil ditambahkan',
                'vendorId' => (int)$vendorId,
                'vendorCode' => $vendor_code
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Failed menambah vendor: ' . $e->getMessage()
            ];
        }
    }

    private function generateVendorCode()
    {
        $year = date('Y');
        $prefix = 'VND-' . $year . '-';

        $query = "SELECT vendor_code FROM vendors 
                  WHERE vendor_code LIKE :prefix 
                  ORDER BY vendor_code DESC 
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':prefix' => $prefix . '%']);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $lastCode = $result['vendor_code'];
            $lastNumber = (int)substr($lastCode, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    public function UpdateVendor($vendorData = [])
    {
        try {
            $this->db->beginTransaction();
            $updateStmt = $this->db->prepare("UPDATE vendors
                SET company_name = :company_name,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    city_id = :city_id,
                    business_type_id =:business_type_id,
                    tax_number = :tax_number,
                    payment_terms_id = :payment_terms_id,
                    website = :website
                WHERE vendor_code = :vendor_code");

            $updateStmt->execute([
                ':company_name'  => $vendorData['vendorName'],
                ':email' => trim($vendorData['vendorEmail']),
                ':phone' => trim($vendorData['vendorPhone'] ?? ''),
                ':address' => trim($vendorData['address'] ?? ''),
                ':city_id' => (int)$vendorData['cityId'],
                ':business_type_id' => (int)$vendorData['businessType'],
                ':tax_number' => trim($vendorData['taxNumber'] ?? ''),
                ':payment_terms_id' => (int)$vendorData['paymentTerms'],
                ':website' => trim($vendorData['website'] ?? ''),
                ':vendor_code' => $vendorData['vendorCode'],
            ]);
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Update Vendor Successfully',
                'vendorCode' => $vendorData['vendorCode']
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Failed update vendor: ' . $e->getMessage()
            ];
        }
    }
}
