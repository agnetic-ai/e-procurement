<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>Payment">Payment</a>
                        </li>
                        <li class="breadcrumb-item active">Create Payment</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <form id="paymentForm" method="post" enctype="multipart/form-data" onsubmit="return false;">
            <input type="hidden" name="invoice_id" id="invoice_id" value="<?= $data["invoiceSummary"]["invoiceId"] ?>">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Invoice Summary</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_number" value="<?= htmlspecialchars($data["invoiceSummary"]["invoiceNumber"] ?? "") ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vendor</label>
                                <input type="text" class="form-control" id="vendor_name" value="<?= htmlspecialchars($data["invoiceSummary"]["vendorName"] ?? "") ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Due Date</label>
                                <input type="text" class="form-control" id="due_date" value="<?= htmlspecialchars($data["invoiceSummary"]["dueDate"] ?? "") ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Amount</label>
                                <input type="text" class="form-control" id="total_amount" value="<?= htmlspecialchars($data["invoiceSummary"]["totalAmount"] ?? "") ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Paid Amount</label>
                                <input type="text" class="form-control" id="paid_amount_summary" value="<?= htmlspecialchars($data["invoiceSummary"]["paidAmount"] ?? "") ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Remaining Amount</label>
                                <input type="text" class="form-control text-danger fw-bold" id="remaining_amount" value="<?= htmlspecialchars($data["invoiceSummary"]["remainingAmount"] ?? "") ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Vendor Bank Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text"
                                    class="form-control"
                                    id="bank_name"
                                    value="<?= htmlspecialchars($data["vendorBank"]["bankName"] ?? "") ?>"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text"
                                    class="form-control"
                                    id="account_number"
                                    value="<?= htmlspecialchars($data["vendorBank"]["accountNumber"] ?? "") ?>"
                                    readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Account Name</label>
                                <input type="text"
                                    class="form-control"
                                    id="account_name"
                                    value="<?= htmlspecialchars($data["vendorBank"]["accountName"] ?? "") ?>"
                                    readonly>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Payment Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">Payment Date</label>
                                <input type="date" class="form-control" name="payment_date" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">Paid Amount</label>
                                <input type="number" class="form-control" name="paid_amount" id="paid_amount" required>
                                <small class="text-muted">
                                    Cannot exceed remaining amount
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">Payment Method</label>
                                <select c class="choices form-select" name="payment_method">
                                    <option value="Transfer">Transfer</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">Reference Number</label>
                                <input type="text" class="form-control" name="reference_number"
                                    placeholder="Bank Ref / Giro No">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">Upload Payment Proof</label>
                                <input type="file"
                                    class="form-control"
                                    name="payment_proof"
                                    accept="image/*,application/pdf"
                                    required>
                                <small class="text-muted">
                                    JPG / PNG / PDF, max 2MB
                                </small>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control"
                                    name="notes"
                                    rows="2"
                                    placeholder="Optional payment notes"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top text-end">
                <a href="<?= BASE_URL ?>Payment" class="btn btn-secondary">
                    <i data-feather="arrow-left"></i> Cancel
                </a>
                <button type="button" class="btn btn-success" onclick="SubmitPayment();">
                    <i data-feather="credit-card"></i> Submit Payment
                </button>
            </div>
        </form>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>public/js/payment/PaymentForm.js"></script>