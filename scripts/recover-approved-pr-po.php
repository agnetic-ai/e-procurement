<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$prNumber = trim($argv[1] ?? '');
if ($prNumber === '') {
    fwrite(STDERR, "Usage: php scripts/recover-approved-pr-po.php PR-NUMBER\n");
    exit(1);
}

require dirname(__DIR__) . '/app/config/constants.php';
require dirname(__DIR__) . '/app/config/autoload.php';

try {
    $result = (new PurchaseOrdersModel())->DraftPurchaseOrder([
        'prNumber' => $prNumber,
    ]);

    if (!$result['success']) {
        fwrite(STDERR, "Gagal membuat draft PO: {$result['message']}\n");
        exit(1);
    }

    printf(
        "Draft PO siap. Dibuat: %d, sudah ada: %d.\n",
        $result['createdCount'] ?? 0,
        $result['existingCount'] ?? 0
    );
} catch (Throwable $e) {
    fwrite(STDERR, "Gagal membuat draft PO: {$e->getMessage()}\n");
    exit(1);
}
