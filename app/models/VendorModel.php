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
                    v.payment_terms AS paymentTerms,
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
}
