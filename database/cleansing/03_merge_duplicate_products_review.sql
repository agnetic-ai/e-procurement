-- OPTIONAL PRODUCT MERGE
-- Default 0 hanya preview. Set 1 pada session sebelum SOURCE setelah disetujui.
SET @apply_product_merge = COALESCE(@apply_product_merge, 0);

DROP TEMPORARY TABLE IF EXISTS product_merge_map;
CREATE TEMPORARY TABLE product_merge_map (
    source_id INT NOT NULL PRIMARY KEY,
    keep_id INT NOT NULL
);

-- Mapping hasil audit dump eprocurement_db (17).sql.
INSERT INTO product_merge_map (source_id, keep_id) VALUES
    (5, 1),
    (6, 2),
    (7, 3),
    (8, 4);

-- valid_mapping harus 1 sebelum merge diaktifkan.
SELECT map.source_id,
       source_product.name AS source_name,
       map.keep_id,
       keep_product.name AS keep_name,
       CASE
           WHEN source_product.id IS NOT NULL
            AND keep_product.id IS NOT NULL
            AND LOWER(TRIM(source_product.name)) = LOWER(TRIM(keep_product.name))
            AND source_product.category_id <=> keep_product.category_id
            AND LOWER(TRIM(source_product.unit_of_measure)) = LOWER(TRIM(keep_product.unit_of_measure))
           THEN 1 ELSE 0
       END AS valid_mapping
FROM product_merge_map map
LEFT JOIN products source_product ON source_product.id = map.source_id
LEFT JOIN products keep_product ON keep_product.id = map.keep_id
ORDER BY map.source_id;

-- Harus kosong. Collision harga tidak dihapus otomatis.
SELECT 'PRICE_COLLISION' AS issue,
       map.source_id, map.keep_id, source_price.vendor_id,
       source_price.id AS source_price_id, keep_price.id AS keep_price_id
FROM product_merge_map map
JOIN product_vendor_prices source_price ON source_price.product_id = map.source_id
JOIN product_vendor_prices keep_price
  ON keep_price.product_id = map.keep_id
 AND keep_price.vendor_id = source_price.vendor_id;

CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_product_vendor_prices LIKE product_vendor_prices;
INSERT IGNORE INTO cleansing_backup_20260810_product_vendor_prices SELECT * FROM product_vendor_prices;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_purchase_request_details LIKE purchase_request_details;
INSERT IGNORE INTO cleansing_backup_20260810_purchase_request_details SELECT * FROM purchase_request_details;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_purchase_order_details LIKE purchase_order_details;
INSERT IGNORE INTO cleansing_backup_20260810_purchase_order_details SELECT * FROM purchase_order_details;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_invoice_details LIKE invoice_details;
INSERT IGNORE INTO cleansing_backup_20260810_invoice_details SELECT * FROM invoice_details;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_invoice_price_audits LIKE invoice_price_audits;
INSERT IGNORE INTO cleansing_backup_20260810_invoice_price_audits SELECT * FROM invoice_price_audits;
CREATE TABLE IF NOT EXISTS cleansing_backup_20260810_asset_units LIKE asset_units;
INSERT IGNORE INTO cleansing_backup_20260810_asset_units SELECT * FROM asset_units;

DROP PROCEDURE IF EXISTS merge_duplicate_products_review;
DELIMITER $$
CREATE PROCEDURE merge_duplicate_products_review()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF @apply_product_merge = 1 AND EXISTS (
        SELECT 1
        FROM product_merge_map map
        LEFT JOIN products source_product ON source_product.id = map.source_id
        LEFT JOIN products keep_product ON keep_product.id = map.keep_id
        WHERE source_product.id IS NULL
           OR keep_product.id IS NULL
           OR LOWER(TRIM(source_product.name)) <> LOWER(TRIM(keep_product.name))
           OR NOT (source_product.category_id <=> keep_product.category_id)
           OR LOWER(TRIM(source_product.unit_of_measure)) <> LOWER(TRIM(keep_product.unit_of_measure))
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Product merge dibatalkan: mapping produk tidak valid.';
    END IF;

    IF @apply_product_merge = 1 AND EXISTS (
        SELECT 1
        FROM product_merge_map map
        JOIN product_vendor_prices source_price ON source_price.product_id = map.source_id
        JOIN product_vendor_prices keep_price
          ON keep_price.product_id = map.keep_id
         AND keep_price.vendor_id = source_price.vendor_id
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Product merge dibatalkan: terdapat collision harga vendor.';
    END IF;

START TRANSACTION;

-- Harga dipindah hanya bila product/vendor tujuan belum ada.
UPDATE product_vendor_prices source_price
JOIN product_merge_map map ON map.source_id = source_price.product_id
JOIN products source_product ON source_product.id = map.source_id
JOIN products keep_product
  ON keep_product.id = map.keep_id
 AND LOWER(TRIM(keep_product.name)) = LOWER(TRIM(source_product.name))
 AND keep_product.category_id <=> source_product.category_id
LEFT JOIN product_vendor_prices collision
  ON collision.product_id = map.keep_id
 AND collision.vendor_id = source_price.vendor_id
SET source_price.product_id = map.keep_id
WHERE @apply_product_merge = 1 AND collision.id IS NULL;

UPDATE purchase_request_details child
JOIN product_merge_map map ON map.source_id = child.product_id
JOIN products source_product ON source_product.id = map.source_id
JOIN products keep_product
  ON keep_product.id = map.keep_id
 AND LOWER(TRIM(keep_product.name)) = LOWER(TRIM(source_product.name))
 AND keep_product.category_id <=> source_product.category_id
SET child.product_id = map.keep_id
WHERE @apply_product_merge = 1;

UPDATE purchase_order_details child
JOIN product_merge_map map ON map.source_id = child.product_id
JOIN products source_product ON source_product.id = map.source_id
JOIN products keep_product
  ON keep_product.id = map.keep_id
 AND LOWER(TRIM(keep_product.name)) = LOWER(TRIM(source_product.name))
 AND keep_product.category_id <=> source_product.category_id
SET child.product_id = map.keep_id
WHERE @apply_product_merge = 1;

UPDATE invoice_details child
JOIN product_merge_map map ON map.source_id = child.product_id
JOIN products source_product ON source_product.id = map.source_id
JOIN products keep_product
  ON keep_product.id = map.keep_id
 AND LOWER(TRIM(keep_product.name)) = LOWER(TRIM(source_product.name))
 AND keep_product.category_id <=> source_product.category_id
SET child.product_id = map.keep_id
WHERE @apply_product_merge = 1;

UPDATE invoice_price_audits child
JOIN product_merge_map map ON map.source_id = child.product_id
JOIN products source_product ON source_product.id = map.source_id
JOIN products keep_product
  ON keep_product.id = map.keep_id
 AND LOWER(TRIM(keep_product.name)) = LOWER(TRIM(source_product.name))
 AND keep_product.category_id <=> source_product.category_id
SET child.product_id = map.keep_id
WHERE @apply_product_merge = 1;

UPDATE asset_units child
JOIN product_merge_map map ON map.source_id = child.product_id
JOIN products source_product ON source_product.id = map.source_id
JOIN products keep_product
  ON keep_product.id = map.keep_id
 AND LOWER(TRIM(keep_product.name)) = LOWER(TRIM(source_product.name))
 AND keep_product.category_id <=> source_product.category_id
SET child.product_id = map.keep_id
WHERE @apply_product_merge = 1;

-- Source hanya dihapus bila seluruh referensi sudah bersih.
DELETE source_product
FROM products source_product
JOIN product_merge_map map ON map.source_id = source_product.id
JOIN products keep_product
  ON keep_product.id = map.keep_id
 AND LOWER(TRIM(keep_product.name)) = LOWER(TRIM(source_product.name))
 AND keep_product.category_id <=> source_product.category_id
WHERE @apply_product_merge = 1
  AND NOT EXISTS (SELECT 1 FROM product_vendor_prices child WHERE child.product_id = source_product.id)
  AND NOT EXISTS (SELECT 1 FROM purchase_request_details child WHERE child.product_id = source_product.id)
  AND NOT EXISTS (SELECT 1 FROM purchase_order_details child WHERE child.product_id = source_product.id)
  AND NOT EXISTS (SELECT 1 FROM invoice_details child WHERE child.product_id = source_product.id)
  AND NOT EXISTS (SELECT 1 FROM invoice_price_audits child WHERE child.product_id = source_product.id)
  AND NOT EXISTS (SELECT 1 FROM asset_units child WHERE child.product_id = source_product.id);

COMMIT;
END$$
DELIMITER ;

CALL merge_duplicate_products_review();
DROP PROCEDURE merge_duplicate_products_review;

-- Jika apply=1, result kosong berarti semua source berhasil di-merge.
SELECT map.source_id, map.keep_id, source_product.name AS source_still_exists
FROM product_merge_map map
JOIN products source_product ON source_product.id = map.source_id
ORDER BY map.source_id;

-- Kasus berikut sengaja hanya ditampilkan untuk keputusan manual.
SELECT 'VENDOR_TAX_REVIEW' AS issue, id, vendor_code, company_name, tax_number
FROM vendors
WHERE REPLACE(REPLACE(REPLACE(REPLACE(TRIM(tax_number), '.', ''), '-', ''), ' ', ''), '/', '') IN (
    SELECT normalized_tax
    FROM (
        SELECT REPLACE(REPLACE(REPLACE(REPLACE(TRIM(tax_number), '.', ''), '-', ''), ' ', ''), '/', '') AS normalized_tax
        FROM vendors
        WHERE NULLIF(TRIM(tax_number), '') IS NOT NULL
        GROUP BY REPLACE(REPLACE(REPLACE(REPLACE(TRIM(tax_number), '.', ''), '-', ''), ' ', ''), '/', '')
        HAVING COUNT(*) > 1
    ) duplicate_tax
)
ORDER BY id;

SELECT 'ASSET_SERIAL_REVIEW' AS issue, id, serial_number, status_code, employee_id
FROM asset_units
WHERE UPPER(REPLACE(TRIM(serial_number), ' ', '')) IN (
    SELECT normalized_serial
    FROM (
        SELECT UPPER(REPLACE(TRIM(serial_number), ' ', '')) AS normalized_serial
        FROM asset_units
        WHERE NULLIF(TRIM(serial_number), '') IS NOT NULL
        GROUP BY UPPER(REPLACE(TRIM(serial_number), ' ', ''))
        HAVING COUNT(*) > 1
    ) duplicate_serial
)
ORDER BY id;
