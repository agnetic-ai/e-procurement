-- MASTER DATA AUDIT (READ ONLY)
-- Jalankan dengan database target sudah dipilih.

SELECT 'DATABASE' AS section, DATABASE() AS value;

SELECT 'TABLE_COUNTS' AS section, 'roles' AS table_name, COUNT(*) AS total FROM roles
UNION ALL SELECT 'TABLE_COUNTS', 'menus', COUNT(*) FROM menus
UNION ALL SELECT 'TABLE_COUNTS', 'status_codes', COUNT(*) FROM status_codes
UNION ALL SELECT 'TABLE_COUNTS', 'approval_levels', COUNT(*) FROM approval_levels
UNION ALL SELECT 'TABLE_COUNTS', 'approval_rules', COUNT(*) FROM approval_rules
UNION ALL SELECT 'TABLE_COUNTS', 'business_types', COUNT(*) FROM business_types
UNION ALL SELECT 'TABLE_COUNTS', 'cities', COUNT(*) FROM cities
UNION ALL SELECT 'TABLE_COUNTS', 'payment_terms', COUNT(*) FROM payment_terms
UNION ALL SELECT 'TABLE_COUNTS', 'employees', COUNT(*) FROM employees
UNION ALL SELECT 'TABLE_COUNTS', 'users', COUNT(*) FROM users
UNION ALL SELECT 'TABLE_COUNTS', 'vendors', COUNT(*) FROM vendors
UNION ALL SELECT 'TABLE_COUNTS', 'vendor_contacts', COUNT(*) FROM vendor_contacts
UNION ALL SELECT 'TABLE_COUNTS', 'vendor_bank_accounts', COUNT(*) FROM vendor_bank_accounts
UNION ALL SELECT 'TABLE_COUNTS', 'categories', COUNT(*) FROM categories
UNION ALL SELECT 'TABLE_COUNTS', 'products', COUNT(*) FROM products
UNION ALL SELECT 'TABLE_COUNTS', 'product_vendor_prices', COUNT(*) FROM product_vendor_prices
UNION ALL SELECT 'TABLE_COUNTS', 'asset_units', COUNT(*) FROM asset_units;

SELECT 'DUPLICATE_CATEGORY_CODE' AS issue,
       LOWER(TRIM(category_code)) AS duplicate_key,
       COUNT(*) AS total,
       GROUP_CONCAT(CONCAT(id, ':', name) ORDER BY id SEPARATOR ' | ') AS records
FROM categories
WHERE NULLIF(TRIM(category_code), '') IS NOT NULL
GROUP BY LOWER(TRIM(category_code))
HAVING COUNT(*) > 1;

SELECT 'BLANK_PRODUCT_CODE' AS issue, id, name
FROM products
WHERE NULLIF(TRIM(code), '') IS NULL
ORDER BY id;

SELECT 'DUPLICATE_PRODUCT_NAME' AS issue,
       LOWER(TRIM(name)) AS duplicate_key,
       COUNT(*) AS total,
       GROUP_CONCAT(id ORDER BY id) AS product_ids
FROM products
GROUP BY LOWER(TRIM(name))
HAVING COUNT(*) > 1;

SELECT 'INVALID_VENDOR_PAYMENT_TERM' AS issue,
       v.id, v.vendor_code, v.company_name, v.payment_terms_id
FROM vendors v
LEFT JOIN payment_terms pt ON pt.id = v.payment_terms_id
WHERE v.payment_terms_id IS NOT NULL AND pt.id IS NULL
ORDER BY v.id;

SELECT 'INVALID_VENDOR_CODE_FORMAT' AS issue, id, vendor_code, company_name
FROM vendors
WHERE vendor_code NOT REGEXP '^VND-[0-9]{4}-[0-9]{3}$'
ORDER BY id;

SELECT 'BLANK_VENDOR_TAX_NUMBER' AS issue, id, vendor_code, company_name
FROM vendors
WHERE NULLIF(TRIM(tax_number), '') IS NULL
ORDER BY id;

SELECT 'DUPLICATE_VENDOR_TAX_NUMBER' AS issue,
       REPLACE(REPLACE(REPLACE(REPLACE(TRIM(tax_number), '.', ''), '-', ''), ' ', ''), '/', '') AS normalized_tax_number,
       COUNT(*) AS total,
       GROUP_CONCAT(CONCAT(id, ':', vendor_code, ':', company_name) ORDER BY id SEPARATOR ' | ') AS records
FROM vendors
WHERE NULLIF(TRIM(tax_number), '') IS NOT NULL
GROUP BY REPLACE(REPLACE(REPLACE(REPLACE(TRIM(tax_number), '.', ''), '-', ''), ' ', ''), '/', '')
HAVING COUNT(*) > 1;

SELECT 'VENDOR_WITHOUT_CONTACT' AS issue, v.id, v.vendor_code, v.company_name
FROM vendors v
LEFT JOIN vendor_contacts vc ON vc.vendor_id = v.id
WHERE vc.id IS NULL
ORDER BY v.id;

SELECT 'VENDOR_WITHOUT_BANK' AS issue, v.id, v.vendor_code, v.company_name
FROM vendors v
LEFT JOIN vendor_bank_accounts vba ON vba.vendor_id = v.id
WHERE vba.id IS NULL
ORDER BY v.id;

SELECT 'DUPLICATE_ASSET_SERIAL_NORMALIZED' AS issue,
       UPPER(REPLACE(TRIM(serial_number), ' ', '')) AS normalized_serial,
       COUNT(*) AS total,
       GROUP_CONCAT(CONCAT(id, ':', serial_number, ':', status_code) ORDER BY id SEPARATOR ' | ') AS records
FROM asset_units
WHERE NULLIF(TRIM(serial_number), '') IS NOT NULL
GROUP BY UPPER(REPLACE(TRIM(serial_number), ' ', ''))
HAVING COUNT(*) > 1;

SELECT 'APPROVAL_RULE_GAP' AS issue,
       current_rule.module_code,
       previous_rule.total_level AS previous_total_level,
       previous_rule.max_amount AS previous_max_amount,
       current_rule.total_level AS current_total_level,
       current_rule.min_amount AS current_min_amount,
       current_rule.min_amount - previous_rule.max_amount AS gap
FROM approval_rules current_rule
JOIN approval_rules previous_rule
  ON previous_rule.module_code = current_rule.module_code
 AND previous_rule.total_level = current_rule.total_level - 1
WHERE current_rule.is_active = 1
  AND previous_rule.is_active = 1
  AND previous_rule.max_amount IS NOT NULL
  AND current_rule.min_amount <> previous_rule.max_amount + 0.01
ORDER BY current_rule.module_code, current_rule.total_level;

SELECT 'ORPHAN_USER_ROLE' AS issue, COUNT(*) AS total
FROM users child LEFT JOIN roles parent ON parent.id = child.role_id
WHERE parent.id IS NULL
UNION ALL
SELECT 'ORPHAN_PRODUCT_CATEGORY', COUNT(*)
FROM products child LEFT JOIN categories parent ON parent.id = child.category_id
WHERE child.category_id IS NOT NULL AND parent.id IS NULL
UNION ALL
SELECT 'ORPHAN_VENDOR_CITY', COUNT(*)
FROM vendors child LEFT JOIN cities parent ON parent.id = child.city_id
WHERE child.city_id IS NOT NULL AND parent.id IS NULL
UNION ALL
SELECT 'ORPHAN_VENDOR_BUSINESS_TYPE', COUNT(*)
FROM vendors child LEFT JOIN business_types parent ON parent.id = child.business_type_id
WHERE child.business_type_id IS NOT NULL AND parent.id IS NULL
UNION ALL
SELECT 'ORPHAN_VENDOR_STATUS', COUNT(*)
FROM vendors child LEFT JOIN status_codes parent
  ON parent.module_code = 'VEND' AND parent.status_code = child.status_code
WHERE parent.id IS NULL
UNION ALL
SELECT 'ORPHAN_PRODUCT_STATUS', COUNT(*)
FROM products child LEFT JOIN status_codes parent
  ON parent.module_code = 'PRODUCT' AND parent.status_code = child.status_code
WHERE parent.id IS NULL;

SELECT 'PARENT_MENU_URL_NOT_NULL' AS issue, id, title, url
FROM menus parent_menu
WHERE parent_menu.parent_id IS NULL
  AND NULLIF(TRIM(parent_menu.url), '') IS NOT NULL
  AND EXISTS (SELECT 1 FROM menus child_menu WHERE child_menu.parent_id = parent_menu.id)
ORDER BY id;
