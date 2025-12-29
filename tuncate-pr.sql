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
TRUNCATE TABLE purchase_requests;

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
