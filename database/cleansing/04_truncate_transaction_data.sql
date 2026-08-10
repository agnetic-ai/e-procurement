-- TRUNCATE ALL TRANSACTION DATA, KEEP MASTER DATA
-- PERINGATAN: TRUNCATE tidak dapat di-rollback.
-- Buat full backup database sebelum menjalankan script ini.
--
-- Preview saja:
--   SOURCE database/cleansing/04_truncate_transaction_data.sql;
--
-- Eksekusi:
--   SET @confirm_truncate = 'TRUNCATE_TRANSACTION_DATA';
--   SOURCE database/cleansing/04_truncate_transaction_data.sql;

SET @confirm_truncate = COALESCE(@confirm_truncate, 'PREVIEW_ONLY');

SELECT 'BEFORE' AS phase, 'purchase_requests' AS table_name, COUNT(*) AS total FROM purchase_requests
UNION ALL SELECT 'BEFORE', 'purchase_request_details', COUNT(*) FROM purchase_request_details
UNION ALL SELECT 'BEFORE', 'purchase_request_approvals', COUNT(*) FROM purchase_request_approvals
UNION ALL SELECT 'BEFORE', 'purchase_orders', COUNT(*) FROM purchase_orders
UNION ALL SELECT 'BEFORE', 'purchase_order_details', COUNT(*) FROM purchase_order_details
UNION ALL SELECT 'BEFORE', 'purchase_orders_approvals', COUNT(*) FROM purchase_orders_approvals
UNION ALL SELECT 'BEFORE', 'goods_receipts', COUNT(*) FROM goods_receipts
UNION ALL SELECT 'BEFORE', 'goods_receipt_details', COUNT(*) FROM goods_receipt_details
UNION ALL SELECT 'BEFORE', 'invoices', COUNT(*) FROM invoices
UNION ALL SELECT 'BEFORE', 'invoice_details', COUNT(*) FROM invoice_details
UNION ALL SELECT 'BEFORE', 'invoice_price_audits', COUNT(*) FROM invoice_price_audits
UNION ALL SELECT 'BEFORE', 'payments', COUNT(*) FROM payments
UNION ALL SELECT 'BEFORE', 'asset_units', COUNT(*) FROM asset_units
UNION ALL SELECT 'BEFORE', 'asset_assignments', COUNT(*) FROM asset_assignments;

DROP PROCEDURE IF EXISTS truncate_transaction_data;
DELIMITER $$
CREATE PROCEDURE truncate_transaction_data()
BEGIN
    DECLARE previous_foreign_key_checks INT DEFAULT 1;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET FOREIGN_KEY_CHECKS = previous_foreign_key_checks;
        RESIGNAL;
    END;

    SET previous_foreign_key_checks = @@FOREIGN_KEY_CHECKS;

    IF DATABASE() IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tidak ada database yang dipilih.';
    END IF;

    IF @confirm_truncate <> 'TRUNCATE_TRANSACTION_DATA' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Preview selesai. Set @confirm_truncate sebelum menjalankan TRUNCATE.';
    END IF;

    SET FOREIGN_KEY_CHECKS = 0;

    -- Asset assignments harus lebih dahulu daripada asset units.
    TRUNCATE TABLE asset_assignments;
    TRUNCATE TABLE asset_units;

    -- Payment dan invoice.
    TRUNCATE TABLE payments;
    TRUNCATE TABLE invoice_price_audits;
    TRUNCATE TABLE invoice_details;
    TRUNCATE TABLE invoices;

    -- Goods receipt.
    TRUNCATE TABLE goods_receipt_details;
    TRUNCATE TABLE goods_receipts;

    -- Purchase order.
    TRUNCATE TABLE purchase_orders_approvals;
    TRUNCATE TABLE purchase_order_details;
    TRUNCATE TABLE purchase_orders;

    -- Purchase request.
    TRUNCATE TABLE purchase_request_approvals;
    TRUNCATE TABLE purchase_request_details;
    TRUNCATE TABLE purchase_requests;

    SET FOREIGN_KEY_CHECKS = previous_foreign_key_checks;
END$$
DELIMITER ;

CALL truncate_transaction_data();
DROP PROCEDURE truncate_transaction_data;

SELECT 'AFTER' AS phase, 'purchase_requests' AS table_name, COUNT(*) AS total FROM purchase_requests
UNION ALL SELECT 'AFTER', 'purchase_request_details', COUNT(*) FROM purchase_request_details
UNION ALL SELECT 'AFTER', 'purchase_request_approvals', COUNT(*) FROM purchase_request_approvals
UNION ALL SELECT 'AFTER', 'purchase_orders', COUNT(*) FROM purchase_orders
UNION ALL SELECT 'AFTER', 'purchase_order_details', COUNT(*) FROM purchase_order_details
UNION ALL SELECT 'AFTER', 'purchase_orders_approvals', COUNT(*) FROM purchase_orders_approvals
UNION ALL SELECT 'AFTER', 'goods_receipts', COUNT(*) FROM goods_receipts
UNION ALL SELECT 'AFTER', 'goods_receipt_details', COUNT(*) FROM goods_receipt_details
UNION ALL SELECT 'AFTER', 'invoices', COUNT(*) FROM invoices
UNION ALL SELECT 'AFTER', 'invoice_details', COUNT(*) FROM invoice_details
UNION ALL SELECT 'AFTER', 'invoice_price_audits', COUNT(*) FROM invoice_price_audits
UNION ALL SELECT 'AFTER', 'payments', COUNT(*) FROM payments
UNION ALL SELECT 'AFTER', 'asset_units', COUNT(*) FROM asset_units
UNION ALL SELECT 'AFTER', 'asset_assignments', COUNT(*) FROM asset_assignments;

-- Master yang tetap dipertahankan:
SELECT 'MASTER_KEPT' AS phase, 'roles' AS table_name, COUNT(*) AS total FROM roles
UNION ALL SELECT 'MASTER_KEPT', 'menus', COUNT(*) FROM menus
UNION ALL SELECT 'MASTER_KEPT', 'role_menus', COUNT(*) FROM role_menus
UNION ALL SELECT 'MASTER_KEPT', 'status_codes', COUNT(*) FROM status_codes
UNION ALL SELECT 'MASTER_KEPT', 'approval_levels', COUNT(*) FROM approval_levels
UNION ALL SELECT 'MASTER_KEPT', 'approval_level_roles', COUNT(*) FROM approval_level_roles
UNION ALL SELECT 'MASTER_KEPT', 'approval_rules', COUNT(*) FROM approval_rules
UNION ALL SELECT 'MASTER_KEPT', 'business_types', COUNT(*) FROM business_types
UNION ALL SELECT 'MASTER_KEPT', 'categories', COUNT(*) FROM categories
UNION ALL SELECT 'MASTER_KEPT', 'cities', COUNT(*) FROM cities
UNION ALL SELECT 'MASTER_KEPT', 'employees', COUNT(*) FROM employees
UNION ALL SELECT 'MASTER_KEPT', 'payment_terms', COUNT(*) FROM payment_terms
UNION ALL SELECT 'MASTER_KEPT', 'products', COUNT(*) FROM products
UNION ALL SELECT 'MASTER_KEPT', 'product_vendor_prices', COUNT(*) FROM product_vendor_prices
UNION ALL SELECT 'MASTER_KEPT', 'users', COUNT(*) FROM users
UNION ALL SELECT 'MASTER_KEPT', 'vendors', COUNT(*) FROM vendors
UNION ALL SELECT 'MASTER_KEPT', 'vendor_bank_accounts', COUNT(*) FROM vendor_bank_accounts
UNION ALL SELECT 'MASTER_KEPT', 'vendor_contacts', COUNT(*) FROM vendor_contacts;

