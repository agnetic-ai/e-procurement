<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>ordersrequest">Order Request</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php $pr = $data['purchase']; ?>
    <?php if (!$pr): ?>
        <div class="alert alert-danger">Purchase Request not found.</div>
        <a href="<?= BASE_URL ?>ordersrequest" class="btn btn-secondary">Back</a>
    <?php else: ?>

    <!-- PR Info Card -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i data-feather="file-text" class="me-2"></i>Purchase Request Info</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">PR Number</label>
                            <h5 class="fw-bold"><?= htmlspecialchars($pr['prNumber']) ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Status</label>
                            <div>
                                <?php
                                $statusClass = match(true) {
                                    str_contains($pr['statusCode'], 'APPROVED') => 'bg-success',
                                    str_contains($pr['statusCode'], 'PENDING') => 'bg-warning',
                                    str_contains($pr['statusCode'], 'REJECTED') => 'bg-danger',
                                    str_contains($pr['statusCode'], 'DRAFT') => 'bg-secondary',
                                    default => 'bg-info'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?> fs-6"><?= htmlspecialchars($pr['statusCode']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Title</label>
                            <p class="fw-semibold"><?= htmlspecialchars($pr['title']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Department</label>
                            <p><?= htmlspecialchars($pr['department']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Request Date</label>
                            <p><?= date('d-M-Y', strtotime($pr['requestDate'])) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Total Estimated</label>
                            <h5 class="text-primary">Rp <?= number_format($pr['totalEstimated'], 0, ',', '.') ?></h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Approval Progress</label>
                            <p>Level <?= $pr['currentApprovalLevel'] ?> of <?= $pr['maxApprovalLevel'] ?></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted mb-1">Notes</label>
                            <p><?= htmlspecialchars($pr['notes'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Billing Address</label>
                            <p><?= htmlspecialchars($pr['billingAddress'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted mb-1">Shipping Address</label>
                            <p><?= htmlspecialchars($pr['shippingAddress'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Workflow -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i data-feather="check-circle" class="me-2"></i>Approval Workflow</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['workflow'])): ?>
                        <?php foreach ($data['workflow'] as $wf): ?>
                            <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                <div class="me-3">
                                    <?php
                                    $wfStatus = $wf['statusCode'] ?? '';
                                    $wfClass = match(true) {
                                        str_contains($wfStatus, 'APR_APPROVED') => 'bg-success',
                                        str_contains($wfStatus, 'APR_PROCESS') => 'bg-warning',
                                        str_contains($wfStatus, 'APR_REJECTED') => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $wfClass ?> rounded-circle p-2"><?= $wf['level'] ?? '' ?></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= htmlspecialchars($wf['username'] ?? 'Approver') ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($wf['roleName'] ?? '') ?> · <?= htmlspecialchars($wf['statusName'] ?? '') ?></small>
                                    <?php if (!empty($wf['remarks'])): ?>
                                        <br><small class="text-muted fst-italic">"<?= htmlspecialchars($wf['remarks']) ?>"</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No approval data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- PR Detail Items -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i data-feather="list" class="me-2"></i>Request Items</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Vendor</th>
                            <th>Description</th>
                            <th class="text-center">Qty</th>
                            <th>Unit</th>
                            <th class="text-end">Est. Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['purchaseDetail'])): ?>
                            <?php foreach ($data['purchaseDetail'] as $i => $item): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($item['productName'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($item['vendorName'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($item['productDescription'] ?? '-') ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?? 0 ?></td>
                                    <td><?= htmlspecialchars($item['unit'] ?? '-') ?></td>
                                    <td class="text-end">Rp <?= $item['estimatedPrice'] ?? '0' ?></td>
                                    <td class="text-end fw-semibold">Rp <?= $item['subtotal'] ?? '0' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-3">No items</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <a href="<?= BASE_URL ?>ordersrequest" class="btn btn-secondary">
        <i data-feather="chevron-left" class="me-1"></i> Back to List
    </a>

    <?php endif; ?>
</div>
