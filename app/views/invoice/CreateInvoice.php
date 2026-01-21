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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>Invoice">Invoice</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Invoice</li>
                    </ol>
                </nav>
            </div>
        </div>

    </div>
    <section class="section">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Select PO Number</strong> <span class="text-danger">*</span></label>
                            <select c class="choices form-select" id="poSelect" onchange="ChangePo(this);" require>
                                <option value="">— Choose PO —</option>
                                <?php foreach ($data["PoComplete"] as $id => $po): ?>
                                    <option value="<?= $po["poNumber"] ?>" data-id="<?= $po["purchaseOrderId"] ?>">
                                        <?= htmlspecialchars($po['poNumber']) ?> —
                                        <?= htmlspecialchars($po['vendorName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" hidden id="poId">
                            <div class="form-text">
                                <i class="text-muted">
                                    Only Purchase Orders with received items that are not fully invoiced are available.
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="invoiceContent" class="col-12 d-none">
            <form id="invoiceForm" method="post" onsubmit="return false;">
                <div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Purchase Order Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PO Number</label>
                                        <input type="text" class="form-control" id="po_number" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PO Date</label>
                                        <input type="text" class="form-control" id="po_date" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PO Amount</label>
                                        <input type="text" class="form-control" id="po_amount" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div id="invoiceSummary" class="d-none">
                            <div class="card-header">
                                <h4>Invoice Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <div class="responsive-container">
                                            <table class='table-custom table-preview'>
                                                <thead>
                                                    <tr>
                                                        <th>Invoice Number</th>
                                                        <th>Invoice Date</th>
                                                        <th>Qty</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableInvSummary">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="card-header">
                            <h4>Invoice Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Invoice Number</label>
                                        <input type="text" class="form-control" id="inv_number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Invoice Date</label>
                                        <input type="date" class="form-control" name="invoice_date" id="invoice_date" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Due Date</label>
                                        <input type="date" class="form-control" name="due_date" id="due_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vendor</label>
                                        <input type="text" class="form-control" id="vendor_name" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Terms</label>
                                        <input type="text" class="form-control" id="payment_terms" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="card-header">
                            <h4><i data-feather="list"></i>Invoice Items</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="responsive-container">
                                        <table class='table-custom table-preview'>
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Ordered</th>
                                                    <th>Received</th>
                                                    <th width="15%">To Invoice <i data-feather="help-circle" class="text-muted" data-bs-toggle="tooltip"
                                                            title="Qty to invoice cannot exceed received qty."></i></th>
                                                    <th width="18%">Unit Price <i data-feather="help-circle" class="text-muted" data-bs-toggle="tooltip"
                                                            title="Default from PO. Changes require verification."></i></th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableinvoiceBody">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end mt-3">
                                        <h5>Grand Total: <strong id="grandTotals"></strong></h5>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" name="notes" id="notes" rows="2" placeholder="e.g., Discount, extra charges, remarks..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top text-end justify-content-between">
                        <a href="<?= BASE_URL ?>invoice" class="btn btn-secondary">
                            <i data-feather="arrow-left"></i> Cancel
                        </a>
                        <!-- <button type="button" class="btn btn-success" id="submitBtn" onclick="SubmitDraftInvoice();" disabled>
                            <i data-feather="check-square"></i> Submit for Verification
                        </button> -->
                        <button type="button" class="btn btn-primary" id="submitBtn" onclick="SubmitDraftInvoice();">
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script src="<?php echo BASE_URL; ?>app/service/invoice/CreateInvoice.js"></script>