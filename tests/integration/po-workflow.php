<?php

require dirname(__DIR__, 2) . '/app/config/constants.php';
require dirname(__DIR__, 2) . '/app/config/autoload.php';

$db = Database::getInstance()->getConnection();
$successPrNumber = 'PR-E2E-PO-SUCCESS';
$failurePrNumber = 'PR-E2E-PO-ROLLBACK';

function assertCondition(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function removeTestPurchaseRequest(PDO $db, string $prNumber): void
{
    $find = $db->prepare('SELECT id FROM purchase_requests WHERE pr_number = :pr_number');
    $find->execute([':pr_number' => $prNumber]);
    $prId = $find->fetchColumn();

    if (!$prId) {
        return;
    }

    $statements = [
        'DELETE pod FROM purchase_order_details pod JOIN purchase_orders po ON po.id = pod.purchase_order_id WHERE po.purchase_request_id = :pr_id',
        'DELETE FROM purchase_orders_approvals WHERE purchase_order_id IN (SELECT id FROM purchase_orders WHERE purchase_request_id = :pr_id)',
        'DELETE FROM purchase_orders WHERE purchase_request_id = :pr_id',
        'DELETE FROM purchase_request_approvals WHERE purchase_request_id = :pr_id',
        'DELETE FROM purchase_request_details WHERE purchase_request_id = :pr_id',
        'DELETE FROM purchase_requests WHERE id = :pr_id',
    ];

    foreach ($statements as $sql) {
        $statement = $db->prepare($sql);
        $statement->execute([':pr_id' => $prId]);
    }
}

function createTestPurchaseRequest(
    PDO $db,
    string $prNumber,
    int $productId,
    int $vendorId,
    bool $withDetail = true
): int {
    $insertPr = $db->prepare("
        INSERT INTO purchase_requests (
            pr_number, title, department, requested_by, request_date,
            status_code, total_estimated, current_approval_level,
            max_approval_level, notes, billing_address, shipping_address
        )
        SELECT
            :pr_number, 'PO workflow regression test', department, requested_by,
            CURDATE(), 'PR_PENDING', 18500000, 1, 1,
            'isolated integration test', billing_address, shipping_address
        FROM purchase_requests
        ORDER BY id
        LIMIT 1
    ");
    $insertPr->execute([':pr_number' => $prNumber]);
    $prId = (int)$db->lastInsertId();

    if ($withDetail) {
        $insertDetail = $db->prepare("
            INSERT INTO purchase_request_details (
                purchase_request_id, product_id, vendor_id, product_description,
                quantity, unit, estimated_price, subtotal
            ) VALUES (
                :pr_id, :product_id, :vendor_id, 'PO workflow regression test',
                1, 'Unit', 18500000, 18500000
            )
        ");
        $insertDetail->execute([
            ':pr_id' => $prId,
            ':product_id' => $productId,
            ':vendor_id' => $vendorId,
        ]);
    }

    $insertApproval = $db->prepare("
        INSERT INTO purchase_request_approvals (
            purchase_request_id, approver_id, level, status_code
        ) VALUES (:pr_id, 3, 1, 'APR_PROCESS')
    ");
    $insertApproval->execute([':pr_id' => $prId]);

    return $prId;
}

function purchaseRequestState(PDO $db, int $prId): array
{
    $statement = $db->prepare("
        SELECT
            pr.status_code AS pr_status,
            pra.status_code AS approval_status,
            COUNT(DISTINCT po.id) AS po_count,
            COUNT(DISTINCT pod.id) AS po_detail_count
        FROM purchase_requests pr
        JOIN purchase_request_approvals pra ON pra.purchase_request_id = pr.id
        LEFT JOIN purchase_orders po ON po.purchase_request_id = pr.id
        LEFT JOIN purchase_order_details pod ON pod.purchase_order_id = po.id
        WHERE pr.id = :pr_id
        GROUP BY pr.id, pr.status_code, pra.status_code
    ");
    $statement->execute([':pr_id' => $prId]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}

try {
    removeTestPurchaseRequest($db, $successPrNumber);
    removeTestPurchaseRequest($db, $failurePrNumber);

    $successPrId = createTestPurchaseRequest($db, $successPrNumber, 1, 1);
    $workflow = new OrdersApprovalModel();
    $successResult = $workflow->SubmitApprovalWorkflow([
        'prNumber' => $successPrNumber,
        'level' => 1,
        'approvalStatus' => 'APR_APPROVED',
        'remarks' => 'integration test',
    ]);
    $successState = purchaseRequestState($db, $successPrId);

    assertCondition($successResult['success'] === true, 'Final approval should succeed.');
    assertCondition($successState['pr_status'] === 'PR_APPROVED', 'PR should become approved.');
    assertCondition((int)$successState['po_count'] === 1, 'Exactly one draft PO should be created.');
    assertCondition((int)$successState['po_detail_count'] === 1, 'The draft PO should contain its detail.');

    $retryResult = (new PurchaseOrdersModel())->DraftPurchaseOrder([
        'prNumber' => $successPrNumber,
    ]);
    $retryState = purchaseRequestState($db, $successPrId);
    assertCondition($retryResult['success'] === true, 'Retry should be safe.');
    assertCondition((int)$retryResult['existingCount'] === 1, 'Retry should reuse the existing PO.');
    assertCondition((int)$retryState['po_count'] === 1, 'Retry must not create a duplicate PO.');

    // Invalid/corrupt PR without details must not be finalized without a PO.
    $failurePrId = createTestPurchaseRequest($db, $failurePrNumber, 1, 1, false);
    $failureResult = $workflow->SubmitApprovalWorkflow([
        'prNumber' => $failurePrNumber,
        'level' => 1,
        'approvalStatus' => 'APR_APPROVED',
        'remarks' => 'integration rollback test',
    ]);
    $failureState = purchaseRequestState($db, $failurePrId);

    assertCondition($failureResult['success'] === false, 'Approval should fail when PR details are missing.');
    assertCondition($failureState['pr_status'] === 'PR_PENDING', 'Failed PO creation must roll back PR approval.');
    assertCondition($failureState['approval_status'] === 'APR_PROCESS', 'Failed PO creation must roll back approval detail.');
    assertCondition((int)$failureState['po_count'] === 0, 'Failed PO creation must leave no PO header.');

    echo "PO workflow integration test passed.\n";
} finally {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    removeTestPurchaseRequest($db, $successPrNumber);
    removeTestPurchaseRequest($db, $failurePrNumber);
}
