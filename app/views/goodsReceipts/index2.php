<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Goods Receipts</h3>
                <p class="text-subtitle text-muted">Manage goods receipt from purchase orders</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Goods Receipts</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i data-feather="filter"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form method="get" action="<?php echo BASE_URL; ?>goodsReceipts">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>PO Number</label>
                            <input type="text" class="form-control" name="po_number"
                                placeholder="Enter PO Number" value="<?= $_GET['po_number'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="">All Status</option>
                                <option value="GR_DRAFT" <?= ($_GET['status'] ?? '') == 'GR_DRAFT' ? 'selected' : '' ?>>Draft</option>
                                <option value="GR_PROCESS" <?= ($_GET['status'] ?? '') == 'GR_PROCESS' ? 'selected' : '' ?>>Process</option>
                                <option value="GR_COMPLETE" <?= ($_GET['status'] ?? '') == 'GR_COMPLETE' ? 'selected' : '' ?>>Complete</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Vendor</label>
                            <input type="text" class="form-control" name="vendor"
                                placeholder="Search vendor" value="<?= $_GET['vendor'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="reset" class="btn btn-secondary me-2">Reset</button>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Goods Receipt List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i data-feather="truck"></i> Goods Receipt List</h5>
            <div class="header-actions">
                <a href="<?php echo BASE_URL; ?>goodsReceipts/create" class="btn btn-primary">
                    <i data-feather="plus"></i> Create New GR
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="goodsReceiptsTable">
                    <thead>
                        <tr>
                            <th>GR Number</th>
                            <th>PO Number</th>
                            <th>Vendor</th>
                            <th>Receipt Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Data dummy untuk contoh
                        $goodsReceipts = [
                            [
                                'gr_number' => 'GR-2024-001',
                                'po_number' => 'PO-2024-001',
                                'vendor' => 'PT. Supplier Jaya',
                                'receipt_date' => '2024-01-15',
                                'status' => 'GR_COMPLETE',
                                'status_text' => 'Complete'
                            ],
                            [
                                'gr_number' => 'GR-2024-002',
                                'po_number' => 'PO-2024-002',
                                'vendor' => 'CV. Elektronik Maju',
                                'receipt_date' => '2024-01-16',
                                'status' => 'GR_PROCESS',
                                'status_text' => 'Process'
                            ],
                            [
                                'gr_number' => 'GR-2024-003',
                                'po_number' => 'PO-2024-003',
                                'vendor' => 'PT. Bahan Bangunan',
                                'receipt_date' => '2024-01-17',
                                'status' => 'GR_DRAFT',
                                'status_text' => 'Draft'
                            ]
                        ];

                        foreach ($goodsReceipts as $gr):
                        ?>
                            <tr>
                                <td><?= $gr['gr_number'] ?></td>
                                <td><?= $gr['po_number'] ?></td>
                                <td><?= $gr['vendor'] ?></td>
                                <td><?= date('d M Y', strtotime($gr['receipt_date'])) ?></td>
                                <td>
                                    <?php
                                    $badge_class = '';
                                    switch ($gr['status']) {
                                        case 'GR_DRAFT':
                                            $badge_class = 'bg-warning';
                                            break;
                                        case 'GR_PROCESS':
                                            $badge_class = 'bg-info';
                                            break;
                                        case 'GR_COMPLETE':
                                            $badge_class = 'bg-success';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= $gr['status_text'] ?></span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_URL; ?>goodsReceipts/detail/<?= $gr['gr_number'] ?>"
                                            class="btn btn-sm btn-primary">
                                            <i data-feather="eye"></i> View
                                        </a>
                                        <?php if ($gr['status'] == 'GR_DRAFT'): ?>
                                            <a href="<?php echo BASE_URL; ?>goodsReceipts/process/<?= $gr['gr_number'] ?>"
                                                class="btn btn-sm btn-warning">
                                                <i data-feather="edit"></i> Process
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>