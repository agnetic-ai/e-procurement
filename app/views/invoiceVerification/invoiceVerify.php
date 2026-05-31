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
                            <a href="<?= BASE_URL ?>Invoice">Invoice</a>
                        </li>
                        <li class="breadcrumb-item active">Verify Invoice</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="col-12">
            <form id="invoiceVerifyForm" method="post" onsubmit="return false;">
                <div class="card">
                    <div class="card-header">
                        <h4>Purchase Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PO Number</label>
                                    <input type="text" class="form-control" id="po_number" value="<?= htmlspecialchars($data["InvHeader"]["poNumber"] ?? "") ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PO Date</label>
                                    <input type="text" class="form-control" id="po_date" value="<?= htmlspecialchars($data["InvHeader"]["poDate"] ?? "") ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PO Amount</label>
                                    <input type="text" class="form-control" id="po_amount" value="<?= htmlspecialchars($data["InvHeader"]["poTotal"] ?? "") ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <h4>Invoice Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Invoice Number</label>
                                    <input type="text" class="form-control" id="inv_number" value="<?= htmlspecialchars($data["InvHeader"]["invoiceNumber"] ?? "") ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Invoice Date</label>
                                    <input type="text" class="form-control" id="invoice_date" value="<?= htmlspecialchars($data["InvHeader"]["invoiceDate"] ?? "") ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="text" class="form-control" id="due_date" value="<?= htmlspecialchars($data["InvHeader"]["dueDate"] ?? "") ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label>Vendor</label>
                                    <input type="text" class="form-control" id="vendor_name" value="<?= htmlspecialchars($data["InvHeader"]["vendorName"] ?? "") ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label>Payment Terms</label>
                                    <input type="text" class="form-control" id="payment_terms" value="<?= htmlspecialchars($data["InvHeader"]["paymentTerms"] ?? "") ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label>Invoice Remark</label>
                                    <textarea
                                        class="form-control"
                                        style="resize:none;"
                                        id="notes"
                                        rows="3" disabled><?= htmlspecialchars($data["InvHeader"]["notes"] ?? "") ?></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="card-header">
                        <h4><i data-feather="list"></i> Invoice Verification Items</h4>
                    </div>
                    <div class="card-body">
                        <div class="responsive-container">
                            <table class="table-custom table-preview">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>PO Qty</th>
                                        <th>GR Qty</th>
                                        <th>Invoice Qty</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tableinvoiceBody">
                                    <?php if (!empty($data['InvItem'])): ?>
                                        <?php
                                        $grandTotal = 0;
                                        foreach ($data['InvItem'] as $item):
                                            $subtotalNumeric = FormatMoneyHelper::unformatMoneyValue($item['subtotal']);
                                            $grandTotal += $subtotalNumeric;
                                        ?>
                                            <tr class="<?= $item['priceMismatch'] ? 'table-warning' : '' ?>">
                                                <td><?= htmlspecialchars($item['productName']) ?></td>
                                                <td><?= (int)$item['poQty'] ?></td>
                                                <td><?= (int)$item['grQty'] ?></td>
                                                <td><?= (int)$item['invoiceQty'] ?></td>

                                                <td>
                                                    <?= htmlspecialchars($item['unitPrice']) ?>

                                                    <?php if ($item['priceChangeType'] === 'DISCOUNT'): ?>
                                                        <span class="badge bg-success ms-1">DISCOUNT</span>

                                                    <?php elseif ($item['priceChangeType'] === 'PRICE_ADJUSTMENT'): ?>
                                                        <span class="badge bg-warning text-dark ms-1">PRICE ADJUSTMENT</span>
                                                    <?php endif; ?>

                                                    <?php if ($item['priceMismatch']): ?>
                                                        <br>
                                                        <small class="text-danger">
                                                            PO: <?= htmlspecialchars($item['poUnitPrice']) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>

                                                <td><?= htmlspecialchars($item['subtotal']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No invoice items found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <h5>Grand Total: <strong id="grandTotal"><?= FormatMoneyHelper::formatMoney($grandTotal) ?></strong></h5>
                        </div>
                        <div class="row mt-4">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="required">Finance Remark</label>
                                    <textarea
                                        class="form-control"
                                        id="notes_verify"
                                        style="resize:none;"
                                        rows="3"
                                        placeholder="Required if rejecting invoice or price change"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top text-end">
                    <a href="<?= BASE_URL ?>invoice" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Back
                    </a>

                    <button
                        type="button"
                        class="btn btn-danger"
                        onclick="RejectInvoice();">
                        Reject
                    </button>

                    <button
                        type="button"
                        class="btn btn-success"
                        onclick="VerifyInvoice();">
                        Verify
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script src="<?= BASE_URL ?>public/js/invoiceVerification/InvoiceVerify.js"></script>