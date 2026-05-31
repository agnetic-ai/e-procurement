-- =========================================
-- SIMULASI FULL FLOW
-- PR → PR APPROVAL → PO → PO APPROVAL → GR → ASSET
-- =========================================

START TRANSACTION;

-- =====================================================
-- 1. PURCHASE REQUEST
-- =====================================================
INSERT INTO purchase_requests
(pr_number, title, department, requested_by, request_date, status_code,
 total_estimated, current_approval_level, max_approval_level, notes)
VALUES
('PR-2026-00002','PR SIMULASI INVOICE','IT & Infrastructure',7,'2026-02-01',
 'PR_APPROVED',157281616,3,3,'SIMULASI INVOICE');

SET @pr_id := LAST_INSERT_ID();

-- =====================================================
-- 2. PURCHASE REQUEST DETAILS
-- =====================================================
INSERT INTO purchase_request_details
(purchase_request_id, product_id, vendor_id, quantity, unit, estimated_price, subtotal)
VALUES
(@pr_id,1,1,5,'Unit',18500000,92500000),
(@pr_id,2,1,6,'Unit',3850000,23100000),
(@pr_id,3,1,5,'Pcs',1300000,6500000),
(@pr_id,14,1,3,'Unit',12098872,36296616);

-- =====================================================
-- 3. PR APPROVAL (LEVEL 1–3)
-- =====================================================
INSERT INTO purchase_request_approvals
(purchase_request_id, LEVEL, approver_id, status_code, approved_at, remarks)
VALUES
(@pr_id,1,3,'APR_APPROVED',NOW(),'OK'),
(@pr_id,2,4,'APR_APPROVED',NOW(),'OK'),
(@pr_id,3,5,'APR_APPROVED',NOW(),'OK');

-- =====================================================
-- 4. PURCHASE ORDER
-- =====================================================
INSERT INTO purchase_orders
(po_number, purchase_request_id, vendor_id, po_date, payment_terms_id,
 total_amount, status_code, received_by,
 current_approval_level, max_approval_level, notes)
VALUES
('PO-2026-00003',@pr_id,1,'2026-02-02',1,
 157281616,'PO_APPROVED',1,3,3,'SIMULASI PO');

SET @po_id := LAST_INSERT_ID();

-- =====================================================
-- 5. PURCHASE ORDER DETAILS (AUTO FROM PR)
-- =====================================================
INSERT INTO purchase_order_details
(purchase_order_id, purchase_request_detail_id, product_id,
 quantity, unit, unit_price, subtotal)
SELECT
 @po_id,
 prd.id,
 prd.product_id,
 prd.quantity,
 prd.unit,
 prd.estimated_price,
 prd.subtotal
FROM purchase_request_details prd
WHERE prd.purchase_request_id = @pr_id;

-- =====================================================
-- 6. PO APPROVAL
-- =====================================================
INSERT INTO purchase_orders_approvals
(purchase_order_id, LEVEL, approver_id, status_code, approved_at, remarks)
VALUES
(@po_id,1,3,'APR_APPROVED',NOW(),'OK'),
(@po_id,2,4,'APR_APPROVED',NOW(),'OK'),
(@po_id,3,5,'APR_APPROVED',NOW(),'OK');

-- =====================================================
-- 7. GOODS RECEIPT (POSTED)
-- =====================================================
INSERT INTO goods_receipts
(gr_number, purchase_order_id, receipt_date,
 received_by, status_code, notes)
VALUES
('GR-2026-00002',@po_id,'2026-02-03',1,'GR_POSTED','SIMULASI GR');

SET @gr_id := LAST_INSERT_ID();

-- =====================================================
-- 8. GOODS RECEIPT DETAILS
-- (MEJA dibuat partial: 4 dari 5)
-- =====================================================
INSERT INTO goods_receipt_details
(goods_receipt_id, purchase_order_detail_id, qty_received)
SELECT
 @gr_id,
 pod.id,
 CASE
   WHEN pod.product_id = 3 THEN 4
   ELSE pod.quantity
 END
FROM purchase_order_details pod
WHERE pod.purchase_order_id = @po_id;

-- =====================================================
-- 9. ASSET UNIT - LAPTOP (5 UNIT)
-- =====================================================
INSERT INTO asset_units
(goods_receipt_detail_id, product_id, serial_number, status_code)
SELECT
 grd.id,
 pod.product_id,
 CONCAT('LAP-', LPAD(grd.id*10 + seq.n,3,'0')),
 'ASSET_IN_STOCK'
FROM goods_receipt_details grd
JOIN purchase_order_details pod
  ON pod.id = grd.purchase_order_detail_id
JOIN (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
) seq
WHERE pod.product_id = 1
  AND seq.n <= grd.qty_received;

-- =====================================================
-- 10. ASSET UNIT - PRINTER (6 UNIT)
-- =====================================================
INSERT INTO asset_units
(goods_receipt_detail_id, product_id, serial_number, status_code)
SELECT
 grd.id,
 pod.product_id,
 CONCAT('PRN-', LPAD(grd.id*10 + seq.n,3,'0')),
 'ASSET_IN_STOCK'
FROM goods_receipt_details grd
JOIN purchase_order_details pod
  ON pod.id = grd.purchase_order_detail_id
JOIN (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION
  SELECT 4 UNION SELECT 5 UNION SELECT 6
) seq
WHERE pod.product_id = 2
  AND seq.n <= grd.qty_received;

-- =====================================================
-- 11. ASSET UNIT - ASUS (3 UNIT)
-- =====================================================
INSERT INTO asset_units
(goods_receipt_detail_id, product_id, serial_number, status_code)
SELECT
 grd.id,
 pod.product_id,
 CONCAT('ASUS-', LPAD(grd.id*10 + seq.n,3,'0')),
 'ASSET_IN_STOCK'
FROM goods_receipt_details grd
JOIN purchase_order_details pod
  ON pod.id = grd.purchase_order_detail_id
JOIN (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3
) seq
WHERE pod.product_id = 14
  AND seq.n <= grd.qty_received;

-- =====================================================
-- COMMIT
-- =====================================================
COMMIT;
