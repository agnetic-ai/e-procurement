<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?? 'Invoice Management' ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?? 'Manage vendor invoices and verify against PO & GR' ?></p>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Filter Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i data-feather="filter"></i> Filter</h5>
            </div>
            <div class="card-body">
                <form id="searchForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Invoice Number</label>
                                <input type="text" class="form-control" name="inv_number" value="<?= htmlspecialchars($_GET['inv_number'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PO Number</label>
                                <input type="text" class="form-control" name="po_number" value="<?= htmlspecialchars($_GET['po_number'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vendor</label>
                                <input type="text" class="form-control" name="vendor" value="<?= htmlspecialchars($_GET['vendor'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control select2" name="invoice_status">
                                    <option value="">All Status</option>
                                    <?php
                                    $invoiceStatuses = $data['invoice_statuses'] ?? [];
                                    foreach ($invoiceStatuses as $status):
                                    ?>
                                        <option value="<?= $status['statusCode'] ?>">
                                            <?= $status['statusName'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date Range</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="start_date" value="<?= $_GET['start_date'] ?? '' ?>">
                                    <span class="input-group-text">to</span>
                                    <input type="date" class="form-control" name="end_date" value="<?= $_GET['end_date'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="ResetInvoiceFilter();">
                                <i data-feather="rotate-ccw"></i> Reset
                            </button>

                            <button type="button" class="btn btn-primary" onclick="SearchInvoice();">
                                <i data-feather="search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i data-feather="file-text"></i> Invoice List</h5>
            </div>
            <div class="card-body card-over">
                <div class="responsive-container">
                    <table class="table-custom" id="invoiceTable">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>PO Number</th>
                                <th>Vendor</th>
                                <th class="text-end">Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>app/service/invoiceVerification/main.js"></script>