<div class="main-content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>invoice">Invoice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Create New Invoice</h3>
                <p class="text-subtitle text-muted">Match invoice with PO & Goods Receipt</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i data-feather="file-plus"></i> Invoice Form</h5>
            </div>
            <div class="card-body">
                <form id="invoiceForm" method="post" action="<?= BASE_URL ?>invoice/store">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Select PO Number</strong> <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="poSelect" onchange="ChangePo(this);" require>
                                    <option value="">— Choose PO (Status: Completed) —</option>
                                    <?php foreach ($data["PoComplete"] as $id => $po): ?>
                                        <option value="<?= $po["poNumber"] ?>">
                                            <?= htmlspecialchars($po['poNumber']) ?> —
                                            <?= htmlspecialchars($po['vendorName']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <i class="text-muted">Only PO with status <strong>Completed</strong> are available.</i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="poSummary" class="row alert alert-light border d-none g-3 mb-4">
                        <div class="card-header">
                            <h4>Request Information Details</h4>
                        </div>
                        <div id="poSummaryContent" class="row">
                            <div class="col-md-4">
                                <label>PO Number</label>
                                <input type="text" class="form-control" id="po_number" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>PO Date</label>
                                <input type="text" class="form-control" id="po_date" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>PO Amount</label>
                                <input type="text" class="form-control" id="po_amount" readonly>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2: Invoice Header -->
                    <div id="invoiceHeader" class="row alert alert-light border  g-3 mb-4 d-none">
                        <div class="col-md-4">
                            <label>Invoice Number <span class="text-danger">*</span>
                                <i data-feather="help-circle" class="text-muted ms-1" data-bs-toggle="tooltip"
                                    title="Auto-generated format: INV-YYYY-NNNNN"></i>
                            </label>
                            <input type="text" class="form-control" name="invoice_number" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="invoice_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label>Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="due_date" required>
                        </div>
                        <div class="col-md-4">
                            <label>Vendor</label>
                            <input type="text" class="form-control" id="vendor_name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Payment Terms</label>
                            <input type="text" class="form-control" id="paymentTerms" readonly>
                        </div>
                    </div>

                    <!-- Step 3: Invoice Items -->
                    <div id="invoiceItemsSection" class="d-none">
                        <h5 class="mt-4 mb-3"><i data-feather="list"></i> Invoice Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="invoiceItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Product</th>
                                        <th width="8%" class="text-center">Ordered</th>
                                        <th width="8%" class="text-center">Received</th>
                                        <th width="10%" class="text-center">To Invoice <i data-feather="help-circle" class="text-muted" data-bs-toggle="tooltip"
                                                title="Qty to invoice cannot exceed received qty."></i></th>
                                        <th width="12%" class="text-end">Unit Price <i data-feather="help-circle" class="text-muted" data-bs-toggle="tooltip"
                                                title="Default from PO. Changes require verification."></i></th>
                                        <th width="12%" class="text-end">Subtotal</th>
                                        <th width="15%" class="text-center">Variance</th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceItemsBody"></tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea class="form-control" name="notes" rows="2" placeholder="e.g., Discount, extra charges, remarks..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="mb-0">
                                    <strong>Total:</strong> <span id="grandTotal">Rp 0</span>
                                    <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                                </h5>
                                <small class="text-muted">PO Total: <span id="poTotal">Rp 0</span></small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>invoice" class="btn btn-secondary">
                            <i data-feather="arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i data-feather="check-square"></i> Submit for Verification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo BASE_URL; ?>app/service/invoice/CreateInvoice.js"></script>