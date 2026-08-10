-- SAFE MASTER DATA CLEANSING
-- Jalankan 01_audit_master_data.sql dan backup database terlebih dahulu.
-- Script ini tidak menghapus atau merge record master.

-- Snapshot hanya diisi sekali agar kondisi sebelum cleansing tetap tersimpan.
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_categories LIKE categories;
INSERT IGNORE INTO cleansing_backup_20260810_categories SELECT * FROM categories;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_products LIKE products;
INSERT IGNORE INTO cleansing_backup_20260810_products SELECT * FROM products;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_vendors LIKE vendors;
INSERT IGNORE INTO cleansing_backup_20260810_vendors SELECT * FROM vendors;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_vendor_contacts LIKE vendor_contacts;
INSERT IGNORE INTO cleansing_backup_20260810_vendor_contacts SELECT * FROM vendor_contacts;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_vendor_bank_accounts LIKE vendor_bank_accounts;
INSERT IGNORE INTO cleansing_backup_20260810_vendor_bank_accounts SELECT * FROM vendor_bank_accounts;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_employees LIKE employees;
INSERT IGNORE INTO cleansing_backup_20260810_employees SELECT * FROM employees;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_users LIKE users;
INSERT IGNORE INTO cleansing_backup_20260810_users SELECT * FROM users;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_menus LIKE menus;
INSERT IGNORE INTO cleansing_backup_20260810_menus SELECT * FROM menus;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_approval_rules LIKE approval_rules;
INSERT IGNORE INTO cleansing_backup_20260810_approval_rules SELECT * FROM approval_rules;

DROP PROCEDURE IF EXISTS cleanse_master_data_safe;
DELIMITER $$
CREATE PROCEDURE cleanse_master_data_safe()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

START TRANSACTION;

-- Kode kategori yang sebelumnya sama semua menjadi kode bisnis unik.
UPDATE categories
SET category_code = CASE
    WHEN LOWER(TRIM(name)) = 'elektronik & it' THEN 'ELECTRONICS_IT'
    WHEN LOWER(TRIM(name)) = 'alat tulis kantor' THEN 'OFFICE_SUPPLIES'
    WHEN LOWER(TRIM(name)) = 'furniture' THEN 'FURNITURE'
    WHEN LOWER(TRIM(name)) = 'jasa' THEN 'SERVICES'
    ELSE category_code
END
WHERE NULLIF(TRIM(category_code), '') IS NULL
   OR LOWER(TRIM(category_code)) = 'products';

-- Buat kode produk deterministik hanya untuk record yang masih kosong.
-- Jika kode hasil generate sudah dipakai produk lain, record dibiarkan untuk review.
UPDATE products product_to_fix
LEFT JOIN products collision
  ON collision.id <> product_to_fix.id
 AND collision.code = CONCAT('PRD-', LPAD(product_to_fix.id, 5, '0'))
SET product_to_fix.code = CONCAT('PRD-', LPAD(product_to_fix.id, 5, '0'))
WHERE NULLIF(TRIM(product_to_fix.code), '') IS NULL
  AND collision.id IS NULL;

UPDATE products
SET code = UPPER(TRIM(code)),
    name = TRIM(name),
    description = NULLIF(TRIM(description), ''),
    unit_of_measure = CASE UPPER(TRIM(unit_of_measure))
        WHEN 'UNIT' THEN 'Unit'
        WHEN 'PCS' THEN 'Pcs'
        WHEN 'RIM' THEN 'Rim'
        ELSE TRIM(unit_of_measure)
    END;

-- Perbaiki typo VD-YYYY-NNN menjadi VND-YYYY-NNN bila target belum digunakan.
UPDATE vendors vendor_to_fix
LEFT JOIN vendors collision
  ON collision.id <> vendor_to_fix.id
 AND collision.vendor_code = CONCAT('VND-', SUBSTRING(vendor_to_fix.vendor_code, 4))
SET vendor_to_fix.vendor_code = CONCAT('VND-', SUBSTRING(vendor_to_fix.vendor_code, 4))
WHERE vendor_to_fix.vendor_code REGEXP '^VD-[0-9]{4}-[0-9]{3}$'
  AND collision.id IS NULL;

-- Nilai 0/ID tidak dikenal menjadi NULL, bukan ditebak ke NET30 atau COD.
UPDATE vendors vendor_to_fix
LEFT JOIN payment_terms pt ON pt.id = vendor_to_fix.payment_terms_id
SET vendor_to_fix.payment_terms_id = NULL
WHERE vendor_to_fix.payment_terms_id IS NOT NULL
  AND pt.id IS NULL;

UPDATE vendors
SET vendor_code = UPPER(TRIM(vendor_code)),
    company_name = TRIM(company_name),
    email = LOWER(TRIM(email)),
    phone = TRIM(phone),
    address = TRIM(address),
    tax_number = NULLIF(TRIM(tax_number), ''),
    website = NULLIF(TRIM(website), '');

UPDATE vendor_contacts
SET contact_name = TRIM(contact_name),
    position = NULLIF(TRIM(position), ''),
    department = NULLIF(TRIM(department), ''),
    contact_phone = NULLIF(TRIM(contact_phone), ''),
    contact_email = LOWER(NULLIF(TRIM(contact_email), '')),
    notes = NULLIF(TRIM(notes), '');

UPDATE vendor_bank_accounts
SET bank_name = TRIM(bank_name),
    bank_code = UPPER(NULLIF(TRIM(bank_code), '')),
    account_number = TRIM(account_number),
    account_name = TRIM(account_name),
    account_type = NULLIF(TRIM(account_type), ''),
    currency = UPPER(TRIM(currency));

UPDATE employees
SET employee_code = UPPER(TRIM(employee_code)),
    full_name = TRIM(full_name),
    email = LOWER(TRIM(email)),
    phone = NULLIF(TRIM(phone), ''),
    department = TRIM(department),
    POSITION = TRIM(POSITION),
    notes = NULLIF(TRIM(notes), '');

UPDATE users
SET username = LOWER(TRIM(username)),
    full_name = TRIM(full_name),
    email = LOWER(TRIM(email));

UPDATE business_types
SET type_code = UPPER(TRIM(type_code)),
    type_name = TRIM(type_name),
    description = NULLIF(TRIM(description), '');

UPDATE cities
SET city_name = TRIM(city_name),
    province_name = TRIM(province_name);

UPDATE payment_terms
SET payment_code = UPPER(TRIM(payment_code)),
    payment_name = TRIM(payment_name),
    payment_description = NULLIF(TRIM(payment_description), '');

-- Parent menu tidak mempunyai route langsung.
UPDATE menus
SET url = NULL
WHERE parent_id IS NULL
  AND (url = '#' OR NULLIF(TRIM(url), '') IS NULL);

UPDATE menus
SET title = TRIM(title),
    url = CASE WHEN parent_id IS NULL THEN url ELSE NULLIF(TRIM(url), '') END;

-- Hilangkan gap nominal desimal antar-level approval.
UPDATE approval_rules current_rule
JOIN approval_rules previous_rule
  ON previous_rule.module_code = current_rule.module_code
 AND previous_rule.total_level = current_rule.total_level - 1
SET current_rule.min_amount = previous_rule.max_amount + 0.01
WHERE current_rule.is_active = 1
  AND previous_rule.is_active = 1
  AND previous_rule.max_amount IS NOT NULL
  AND current_rule.min_amount <> previous_rule.max_amount + 0.01;

-- Spasi internal tidak dihapus karena ada collision serial SN-ASUS-3.
UPDATE asset_units
SET serial_number = NULLIF(TRIM(serial_number), '');

COMMIT;
END$$
DELIMITER ;

CALL cleanse_master_data_safe();
DROP PROCEDURE cleanse_master_data_safe;

-- Semua count idealnya 0; kasus ambigu tetap muncul di audit manual.
SELECT 'blank_product_code_after' AS check_name, COUNT(*) AS total
FROM products WHERE NULLIF(TRIM(code), '') IS NULL
UNION ALL
SELECT 'invalid_vendor_payment_term_after', COUNT(*)
FROM vendors v LEFT JOIN payment_terms pt ON pt.id = v.payment_terms_id
WHERE v.payment_terms_id IS NOT NULL AND pt.id IS NULL
UNION ALL
SELECT 'invalid_vendor_code_after', COUNT(*)
FROM vendors WHERE vendor_code NOT REGEXP '^VND-[0-9]{4}-[0-9]{3}$'
UNION ALL
SELECT 'parent_menu_placeholder_after', COUNT(*)
FROM menus parent_menu
WHERE parent_menu.parent_id IS NULL
  AND NULLIF(TRIM(parent_menu.url), '') IS NOT NULL
  AND EXISTS (SELECT 1 FROM menus child_menu WHERE child_menu.parent_id = parent_menu.id);
