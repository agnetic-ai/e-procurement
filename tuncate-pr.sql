SET FOREIGN_KEY_CHECKS = 0;

START TRANSACTION;

-- ===============================
-- 1. DROP FOREIGN KEY
-- ===============================

ALTER TABLE asset_units
    DROP FOREIGN KEY fk_asset_gr_detail,
    DROP FOREIGN KEY fk_asset_product,
    DROP FOREIGN KEY fk_asset_status;

ALTER TABLE goods_receipt_details
    DROP FOREIGN KEY fk_grd_receipt;

ALTER TABLE goods_receipts
    DROP FOREIGN KEY fk_gr_purchase_request,
    DROP FOREIGN KEY fk_gr_status;

ALTER TABLE purchase_request_approvals
    DROP FOREIGN KEY fk_pr_approval_user;

ALTER TABLE purchase_request_details
    DROP FOREIGN KEY fk_pr_detail_vendor;

ALTER TABLE purchase_requests
    DROP FOREIGN KEY fk_pr_requested_by;

-- ===============================
-- 2. TRUNCATE DATA (URUT DARI CHILD)
-- ===============================

TRUNCATE TABLE asset_units;
TRUNCATE TABLE goods_receipt_details;
TRUNCATE TABLE goods_receipts;
TRUNCATE TABLE purchase_request_approvals;
TRUNCATE TABLE purchase_request_details;
TRUNCATE TABLE /* =========================================================
   STEP 1 : MATIKAN FOREIGN KEY CHECK
========================================================= */
SET FOREIGN_KEY_CHECKS = 0;

/* =========================================================
   STEP 2 : DROP FOREIGN KEY & INDEX (URUTAN AMAN)
========================================================= */

/* invoice_details */
ALTER TABLE invoice_details
  DROP FOREIGN KEY fk_inv_id,
  DROP FOREIGN KEY fk_inv_pod,
  DROP FOREIGN KEY fk_inv_prod;

/* invoices */
ALTER TABLE invoices
  DROP FOREIGN KEY fk_inv_po,
  DROP FOREIGN KEY fk_inv_status;

/* asset_units */
ALTER TABLE asset_units
  DROP INDEX uk_asset_serial,
  DROP INDEX idx_asset_gr_detail,
  DROP INDEX idx_asset_status,
  DROP FOREIGN KEY fk_asset_product;

/* goods_receipt_details */
ALTER TABLE goods_receipt_details
  DROP INDEX idx_grd_receipt,
  DROP INDEX idx_pod_id;

/* goods_receipts */
ALTER TABLE goods_receipts
  DROP INDEX idx_gr_status,
  DROP INDEX idx_gr_date,
  DROP INDEX idx_gr_po;

/* purchase_orders_approvals */
ALTER TABLE purchase_orders_approvals
  DROP INDEX uk_po_level;

/* purchase_order_details */
ALTER TABLE purchase_order_details
  DROP FOREIGN KEY fk_pod_po,
  DROP FOREIGN KEY fk_pod_prd,
  DROP FOREIGN KEY fk_pod_product;

/* purchase_orders */
ALTER TABLE purchase_orders
  DROP FOREIGN KEY fk_po_pr,
  DROP FOREIGN KEY fk_po_vendor,
  DROP FOREIGN KEY fk_po_payment,
  DROP FOREIGN KEY fk_po_status,
  DROP FOREIGN KEY fk_po_user;

/* purchase_request_approvals */
ALTER TABLE purchase_request_approvals
  DROP INDEX uk_pr_level;

/* purchase_request_details */
ALTER TABLE purchase_request_details
  DROP FOREIGN KEY fk_pr_detail_vendor;

/* purchase_requests */
ALTER TABLE purchase_requests
  DROP FOREIGN KEY fk_pr_requested_by;

/* =========================================================
   STEP 3 : TRUNCATE TABLE (URUTAN SESUAI REQUEST)
========================================================= */

TRUNCATE invoice_details;
TRUNCATE invoices;
TRUNCATE asset_units;
TRUNCATE goods_receipt_details;
TRUNCATE goods_receipts;
TRUNCATE purchase_orders_approvals;
TRUNCATE purchase_order_details;
TRUNCATE purchase_orders;
TRUNCATE purchase_request_approvals;
TRUNCATE purchase_request_details;
TRUNCATE purchase_requests;

/* =========================================================
   STEP 4 : RECREATE INDEX & FOREIGN KEY
========================================================= */

/* purchase_requests */
ALTER TABLE purchase_requests
  ADD CONSTRAINT fk_pr_requested_by
  FOREIGN KEY (requested_by) REFERENCES users(id);

/* purchase_request_details */
ALTER TABLE purchase_request_details
  ADD CONSTRAINT fk_pr_detail_vendor
  FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE;

/* purchase_orders */
ALTER TABLE purchase_orders
  ADD CONSTRAINT fk_po_pr FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id),
  ADD CONSTRAINT fk_po_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id),
  ADD CONSTRAINT fk_po_payment FOREIGN KEY (payment_terms_id) REFERENCES payment_terms(id),
  ADD CONSTRAINT fk_po_status FOREIGN KEY (status_code) REFERENCES status_codes(status_code),
  ADD CONSTRAINT fk_po_user FOREIGN KEY (received_by) REFERENCES users(id);

/* purchase_order_details */
ALTER TABLE purchase_order_details
  ADD CONSTRAINT fk_pod_po FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_pod_prd FOREIGN KEY (purchase_request_detail_id) REFERENCES purchase_request_details(id),
  ADD CONSTRAINT fk_pod_product FOREIGN KEY (product_id) REFERENCES products(id);

/* goods_receipts */
ALTER TABLE goods_receipts
  ADD INDEX idx_gr_status (status_code),
  ADD INDEX idx_gr_date (receipt_date),
  ADD INDEX idx_gr_po (purchase_order_id);

/* goods_receipt_details */
ALTER TABLE goods_receipt_details
  ADD INDEX idx_grd_receipt (goods_receipt_id),
  ADD INDEX idx_pod_id (purchase_order_detail_id);

/* asset_units */
ALTER TABLE asset_units
  ADD UNIQUE KEY uk_asset_serial (serial_number),
  ADD INDEX idx_asset_gr_detail (goods_receipt_detail_id),
  ADD INDEX idx_asset_status (status_code),
  ADD CONSTRAINT fk_asset_product FOREIGN KEY (product_id) REFERENCES products(id);

/* invoices */
ALTER TABLE invoices
  ADD CONSTRAINT fk_inv_po FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id),
  ADD CONSTRAINT fk_inv_status FOREIGN KEY (status_code) REFERENCES status_codes(status_code);

/* invoice_details */
ALTER TABLE invoice_details
  ADD CONSTRAINT fk_inv_id FOREIGN KEY (invoice_id) REFERENCES invoices(id),
  ADD CONSTRAINT fk_inv_pod FOREIGN KEY (purchase_order_detail_id) REFERENCES purchase_order_details(id),
  ADD CONSTRAINT fk_inv_prod FOREIGN KEY (product_id) REFERENCES products(id);

/* =========================================================
   STEP 5 : AKTIFKAN KEMBALI FK CHECK
========================================================= */
SET FOREIGN_KEY_CHECKS = 1;
;

-- ===============================
-- 3. ADD FOREIGN KEY KEMBALI
-- ===============================

ALTER TABLE purchase_requests
    ADD CONSTRAINT fk_pr_requested_by
    FOREIGN KEY (requested_by) REFERENCES users(id);

ALTER TABLE purchase_request_details
    ADD CONSTRAINT fk_pr_detail_vendor
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
    ON DELETE CASCADE;

ALTER TABLE purchase_request_approvals
    ADD CONSTRAINT fk_pr_approval_user
    FOREIGN KEY (approver_id) REFERENCES users(id);

ALTER TABLE goods_receipts
    ADD CONSTRAINT fk_gr_purchase_request
    FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id)
    ON UPDATE CASCADE,
    ADD CONSTRAINT fk_gr_status
    FOREIGN KEY (status_code) REFERENCES status_codes(status_code)
    ON UPDATE CASCADE;

ALTER TABLE goods_receipt_details
    ADD CONSTRAINT fk_grd_receipt
    FOREIGN KEY (goods_receipt_id) REFERENCES goods_receipts(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE asset_units
    ADD CONSTRAINT fk_asset_gr_detail
    FOREIGN KEY (goods_receipt_detail_id) REFERENCES goods_receipt_details(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    ADD CONSTRAINT fk_asset_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON UPDATE CASCADE,
    ADD CONSTRAINT fk_asset_status
    FOREIGN KEY (status_code) REFERENCES status_codes(status_code)
    ON UPDATE CASCADE;

COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
