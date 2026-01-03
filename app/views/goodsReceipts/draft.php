<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Create Goods Receipt</h3>
                <p class="text-subtitle text-muted">Select approved PO to create goods receipt</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>goodsReceipts">Goods Receipts</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create GR</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Approved PO List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i data-feather="shopping-cart"></i> Approved Purchase Orders</h5>
            <p class="card-subtitle">Only PO with status APPROVED can create Goods Receipt</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Vendor</th>
                            <th>Order Date</th>
                            <th>Total Items</th>
                            <th>Total Qty</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Data dummy PO approved
                        $approvedPOs = [
                            [
                                'po_number' => 'PO-2024-001',
                                'vendor' => 'PT. Supplier Jaya',
                                'order_date' => '2024-01-10',
                                'total_items' => 5,
                                'total_qty' => 100,
                                'status' => 'APPROVED'
                            ],
                            [
                                'po_number' => 'PO-2024-004',
                                'vendor' => 'PT. Elektronik Nusantara',
                                'order_date' => '2024-01-12',
                                'total_items' => 3,
                                'total_qty' => 25,
                                'status' => 'APPROVED'
                            ],
                            [
                                'po_number' => 'PO-2024-005',
                                'vendor' => 'CV. Material Utama',
                                'order_date' => '2024-01-14',
                                'total_items' => 8,
                                'total_qty' => 150,
                                'status' => 'APPROVED'
                            ]
                        ];

                        foreach ($approvedPOs as $po):
                        ?>
                            <tr>
                                <td><?= $po['po_number'] ?></td>
                                <td><?= $po['vendor'] ?></td>
                                <td><?= date('d M Y', strtotime($po['order_date'])) ?></td>
                                <td><?= $po['total_items'] ?> items</td>
                                <td><?= $po['total_qty'] ?> units</td>
                                <td>
                                    <span class="badge bg-success">APPROVED</span>
                                </td>
                                <td>
                                    <form method="post" action="<?php echo BASE_URL; ?>goodsReceipts/createFromPO">
                                        <input type="hidden" name="po_number" value="<?= $po['po_number'] ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i data-feather="file-plus"></i> Create GR
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>