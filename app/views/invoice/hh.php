<?php
// Dummy data sesuai master-vendor.sql-mu
$dummyPOs = [
    1 => [
        'po_number' => 'PO-2025-00001',
        'vendor_id' => 4,
        'vendor_name' => 'PT Global Teknik Indonesia update',
        'po_date' => '2026-01-02',
        'payment_terms_id' => 4, // NET45
        'total_amount' => 45792294.00,
        'items' => [
            [
                'pod_id' => 1,
                'product_id' => 4,
                'product_name' => 'Kertas A4 70gr PaperOne',
                'qty_ordered' => 2,
                'unit' => 'Pcs',
                'unit_price_po' => 45000.00,
                'qty_received' => 2,
            ],
            [
                'pod_id' => 2,
                'product_id' => 13,
                'product_name' => 'DELL Latitude 5240',
                'qty_ordered' => 3,
                'unit' => 'Unit',
                'unit_price_po' => 15234098.00,
                'qty_received' => 3,
            ]
        ]
    ],
    2 => [
        'po_number' => 'PO-2025-00002',
        'vendor_id' => 5,
        'vendor_name' => 'CV Logistik Cepat',
        'po_date' => '2026-01-01',
        'payment_terms_id' => 4, // NET45
        'total_amount' => 100000000.00,
        'items' => [
            [
                'pod_id' => 4,
                'product_id' => 5,
                'product_name' => 'Laptop Dell XPS 13',
                'qty_ordered' => 4,
                'unit' => 'Unit',
                'unit_price_po' => 25000000.00,
                'qty_received' => 4,
            ]
        ]
    ],
    3 => [
        'po_number' => 'PO-2026-00001',
        'vendor_id' => 1,
        'vendor_name' => 'PT Supplier Jaya Abadi',
        'po_date' => '2026-01-02',
        'payment_terms_id' => 5, // NET60
        'total_amount' => 48598872.00,
        'items' => [
            [
                'pod_id' => 5,
                'product_id' => 14,
                'product_name' => 'ASUS ROG UPDATE',
                'qty_ordered' => 1,
                'unit' => 'Unit',
                'unit_price_po' => 12098872.00,
                'qty_received' => 1,
            ],
            [
                'pod_id' => 6,
                'product_id' => 1,
                'product_name' => 'Laptop Dell XPS 13',
                'qty_ordered' => 2,
                'unit' => 'Unit',
                'unit_price_po' => 18500000.00,
                'qty_received' => 2,
            ]
        ]
    ]
];

$paymentTerms = [
    1 => ['name' => 'Net 7', 'days' => 7],
    2 => ['name' => 'Net 14', 'days' => 14],
    3 => ['name' => 'Net 30', 'days' => 30],
    4 => ['name' => 'Net 45', 'days' => 45],
    5 => ['name' => 'Net 60', 'days' => 60],
    6 => ['name' => 'Cash On Delivery', 'days' => 0],
    7 => ['name' => 'Cash Before Delivery', 'days' => 0],
    8 => ['name' => 'Cash In Advance', 'days' => 0],
];
?>

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
                                <select class="form-select" id="poSelect" name="po_id" required>
                                    <option value="">— Choose PO (Status: Completed) —</option>
                                    <?php foreach ($data["PoComplete"] as $id => $po): ?>
                                        <option value="<?= $id ?>">
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

                    <!-- PO Summary Panel -->
                    <div id="poSummary" class="alert alert-light border d-none mb-4">
                        <h6><i data-feather="info"></i> PO Summary</h6>
                        <div id="poSummaryContent"></div>
                    </div>

                    <!-- Step 2: Invoice Header -->
                    <div id="invoiceHeader" class="row g-3 mb-4 d-none">
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
                        <div class="col-md-6">
                            <label>Vendor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vendorName" readonly>
                            <input type="hidden" name="vendor_id" id="vendorId">
                        </div>
                        <div class="col-md-6">
                            <label>Payment Terms</label>
                            <select class="form-control" name="payment_terms_id" id="paymentTermsSelect" disabled>
                                <option value="">Loading…</option>
                                <?php foreach ($paymentTerms as $id => $pt): ?>
                                    <option value="<?= $id ?>"><?= $pt['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Init tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        const poSelect = $('#poSelect');
        const poSummary = $('#poSummary');
        const poSummaryContent = $('#poSummaryContent');
        const invoiceHeader = $('#invoiceHeader');
        const invoiceItemsSection = $('#invoiceItemsSection');
        const invoiceItemsBody = $('#invoiceItemsBody');
        const grandTotalEl = $('#grandTotal');
        const poTotalEl = $('#poTotal');
        const vendorNameEl = $('#vendorName');
        const vendorIdEl = $('#vendorId');
        const paymentTermsSelect = $('#paymentTermsSelect');
        const submitBtn = $('#submitBtn');

        // Dummy data (sesuai DB-mu)
        const dummyPOs = <?= json_encode($dummyPOs) ?>;
        const paymentTerms = <?= json_encode($paymentTerms) ?>;

        poSelect.on('change', function() {
            const poId = $(this).val();
            if (!poId) {
                poSummary.addClass('d-none');
                invoiceHeader.addClass('d-none');
                invoiceItemsSection.addClass('d-none');
                submitBtn.prop('disabled', true);
                return;
            }

            const po = dummyPOs[poId];
            if (!po) return;

            // === Update PO Summary ===
            poSummaryContent.html(`
                <strong>${po.po_number}</strong><br>
                Vendor: <strong>${po.vendor_name}</strong><br>
                PO Date: ${po.po_date}<br>
                ✅ Ordered: <strong>Rp ${Number(po.total_amount).toLocaleString('id-ID')}</strong><br>
                📦 Received: <strong>Rp ${Number(po.total_amount).toLocaleString('id-ID')}</strong> (100%)<br>
                📄 Invoiced: <em>Rp 0 (0%)</em>
            `);
            poSummary.removeClass('d-none');

            // === Update Header ===
            vendorNameEl.val(po.vendor_name);
            vendorIdEl.val(po.vendor_id);
            paymentTermsSelect.val(po.payment_terms_id).prop('disabled', false);

            // Auto-generate invoice number (INV-YYYY-NNNNN)
            const year = new Date().getFullYear();
            const nextNum = '00001';
            $('[name="invoice_number"]').val(`INV-${year}-${nextNum}`);

            // Auto set due date
            const invDate = new Date();
            const pt = paymentTerms[po.payment_terms_id];
            const dueDate = new Date(invDate);
            dueDate.setDate(dueDate.getDate() + (pt?.days || 30));
            $('[name="due_date"]').val(dueDate.toISOString().split('T')[0]);

            // === Render Items ===
            let grandTotal = 0;
            let itemsHtml = '';

            po.items.forEach((item, idx) => {
                const subtotal = item.qty_received * item.unit_price_po;
                grandTotal += subtotal;

                itemsHtml += `
                <tr>
                    <td>${item.product_name}</td>
                    <td class="text-center">${item.qty_ordered} ${item.unit}</td>
                    <td class="text-center">${item.qty_received} ${item.unit}</td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center qty-input"
                            name="items[${idx}][qty]" value="${item.qty_received}" 
                            min="0" max="${item.qty_received}" 
                            data-ordered="${item.qty_ordered}" data-received="${item.qty_received}"
                            required>
                        <input type="hidden" name="items[${idx}][pod_id]" value="${item.pod_id}">
                        <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                    </td>
                    <td class="text-end">
                        <input type="number" step="0.01" class="form-control form-control-sm text-end price-input"
                            name="items[${idx}][unit_price]" value="${item.unit_price_po}"
                            data-po-price="${item.unit_price_po}" required>
                    </td>
                    <td class="text-end align-middle subtotal">Rp ${subtotal.toLocaleString('id-ID')}</td>
                    <td class="text-center align-middle variance"></td>
                </tr>`;
            });

            invoiceItemsBody.html(itemsHtml);
            poTotalEl.text(`Rp ${Number(po.total_amount).toLocaleString('id-ID')}`);
            updateGrandTotal();
            recalculateVariances();

            // Show sections
            invoiceHeader.removeClass('d-none');
            invoiceItemsSection.removeClass('d-none');
            submitBtn.prop('disabled', false);

            // Bind input events
            $('.qty-input, .price-input').on('input', function() {
                recalculateRow($(this).closest('tr'));
                updateGrandTotal();
                recalculateVariances();
            });
        });
        note

        function recalculateRow(row) {
            const qty = parseFloat(row.find('.qty-input').val()) || 0;
            const price = parseFloat(row.find('.price-input').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal').text(`Rp ${subtotal.toLocaleString('id-ID')}`);
        }

        function updateGrandTotal() {
            let total = 0;
            $('#invoiceItemsTable .subtotal').each(function() {
                const text = $(this).text().replace(/[^0-9\-]/g, '');
                total += parseFloat(text) || 0;
            });
            grandTotalEl.text(`Rp ${total.toLocaleString('id-ID')}`);
            $('#totalAmountInput').val(total);
        }

        function recalculateVariances() {
            $('#invoiceItemsTable tbody tr').each(function() {
                const row = $(this);
                const qtyInput = row.find('.qty-input');
                const priceInput = row.find('.price-input');

                const qtyReceived = parseInt(qtyInput.data('received'));
                const qtyInvoice = parseInt(qtyInput.val());
                const poPrice = parseFloat(priceInput.data('po-price'));
                const invoicePrice = parseFloat(priceInput.val());

                let varianceText = '';
                let varianceClass = '';

                // Qty variance
                if (qtyInvoice > qtyReceived) {
                    varianceText += '⚠️ Qty > received<br>';
                    varianceClass = 'text-warning';
                    qtyInput.addClass('is-invalid');
                } else {
                    qtyInput.removeClass('is-invalid');
                }

                // Price variance
                if (invoicePrice !== poPrice) {
                    const diff = invoicePrice - poPrice;
                    const percent = ((diff / poPrice) * 100).toFixed(1);
                    varianceText += `💰 ${percent > 0 ? '+' : ''}${percent}%`;
                    varianceClass = Math.abs(percent) > 5 ? 'text-danger' : 'text-warning';
                }

                row.find('.variance').html(varianceText).addClass(varianceClass);
            });
        }

        // Feather Icons
        feather.replace();
    });
</script>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }
</style>