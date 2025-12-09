<?php
class VendorModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetVendorList()
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
                    FORMAT(v.credit_limit, 2) AS creditLimit,
                    v.payment_terms AS paymentTerms,
                    v.website,
                    v.established_year AS establishedYear,
                    FORMAT(v.total_transactions, 2) AS totalTransactions,
                    v.last_transaction_date AS lastTransactionDate,
                    v.notes,
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
                ORDER BY v.id DESC";
    }
}
