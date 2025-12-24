-- ============================================
-- RESET PURCHASE REQUEST TABLES
-- Remove FK -> Truncate -> Add FK Again
-- Database: eprocurement_db
-- ============================================

START TRANSACTION;

-- ============================================
-- 1. DROP FOREIGN KEY CONSTRAINTS
-- ============================================

-- FK: purchase_request_details -> purchase_requests
ALTER TABLE purchase_request_details
DROP FOREIGN KEY fk_pr_detail_pr;

-- FK: purchase_request_approvals -> purchase_requests
ALTER TABLE purchase_request_approvals
DROP FOREIGN KEY fk_pr_approval_pr;

-- ============================================
-- 2. TRUNCATE TABLES
-- ============================================

TRUNCATE TABLE purchase_request_details;
TRUNCATE TABLE purchase_request_approvals;
TRUNCATE TABLE purchase_requests;

-- ============================================
-- 3. ADD FOREIGN KEY CONSTRAINTS AGAIN
-- ============================================

-- FK: purchase_request_details -> purchase_requests
ALTER TABLE purchase_request_details
ADD CONSTRAINT fk_pr_detail_pr
FOREIGN KEY (purchase_request_id)
REFERENCES purchase_requests(id)
ON DELETE CASCADE;

-- FK: purchase_request_approvals -> purchase_requests
ALTER TABLE purchase_request_approvals
ADD CONSTRAINT fk_pr_approval_pr
FOREIGN KEY (purchase_request_id)
REFERENCES purchase_requests(id)
ON DELETE CASCADE;

COMMIT;
