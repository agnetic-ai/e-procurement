<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Create Goods Receipt</h3>
                <p class="text-subtitle text-muted">Record receipt of goods from an approved Purchase Order</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>goodsReceipts">Goods Receipts</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- STEP 1: Select PO (sesuai struktur lama Anda) -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title"><i data-feather="file-text"></i> Select Purchase Order</h5>
        </div>
        <div class="card-body">
            <form id="formSelectPO">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Approved Purchase Order</label>
                            <select class="form-control select2" name="selPurchaseOrder" id="selPurchaseOrder">
                                <option value="">-- Select Approved PO --</option>
                                <option value="PO-001">PO-001</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" onclick="loadPODetails()">
                            <i data-feather="plus"></i> Create Goods Receipt
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- PO Info & GR History (hidden by default) -->
    <div id="poSection" class="d-none">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title"><i data-feather="file-text"></i> Purchase Order Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="fw-bold">PO Number</p>
                        <p id="poNumberDisplay">—</p>
                    </div>
                    <div class="col-md-3">
                        <p class="fw-bold">Vendor</p>
                        <p id="vendorDisplay">—</p>
                    </div>
                    <div class="col-md-3">
                        <p class="fw-bold">PO Date</p>
                        <p id="poDateDisplay">—</p>
                    </div>
                    <div class="col-md-3">
                        <p class="fw-bold">Department</p>
                        <p id="departmentDisplay">—</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i data-feather="clock"></i> Previous GR History for <span id="poHistoryLabel">—</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>GR Number</th>
                                <th>Receipt Date</th>
                                <th>Received By</th>
                                <th>Status</th>
                                <th>Total Items</th>
                            </tr>
                        </thead>
                        <tbody id="grHistoryBody">
                            <tr>
                                <td colspan="5" class="text-center">No previous GR found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- GR Detail Form -->
    <div id="grFormSection" class="d-none">
        <div class="alert alert-info d-flex align-items-center">
            <i data-feather="info" class="me-2"></i>
            <div>
                <strong>Info:</strong> Items with unit <code>Unit</code> require serial numbers. Partial deliveries are allowed.
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i data-feather="archive"></i> Goods Receipt Details</h5>
            </div>
            <div class="card-body">
                <form id="grForm" method="post" onsubmit="return false;">
                    <input type="hidden" name="po_number" id="hidden_po_number">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Product</th>
                                    <th width="10%" class="text-center">Qty Ordered</th>
                                    <th width="10%" class="text-center">Qty Received</th>
                                    <th width="10%" class="text-center">Remaining</th>
                                    <th width="25%">Serial Numbers (if Unit)</th>
                                </tr>
                            </thead>
                            <tbody id="grItemsBody">
                                <!-- Filled by JS (dummy data) -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="form-group">
                            <label>Goods Receipt Notes</label>
                            <textarea class="form-control" name="notes" id="gr_notes" style="resize: none;" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <a href="<?php echo BASE_URL; ?>goodsReceipts" class="btn btn-secondary">
                            <i data-feather="chevron-left"></i> Back
                        </a>
                        <button type="button" id="btnSubmitDraft" class="btn btn-success" onclick="submitGR('GR_DRAFT')">
                            <i data-feather="save"></i> Save as Draft
                        </button>
                        <button type="button" id="btnSubmitProcess" class="btn btn-primary" onclick="submitGR('GR_PROCESS')">
                            <i data-feather="check-circle"></i> Submit & Process
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        feather.replace();
        $('.select2').select2();
    });

    // Dummy data untuk demo (sesuaikan jika sudah ada API)
    const DUMMY_PO_DETAILS = {
        'PO-2026-001': [{
                id: 1,
                name: 'Laptop Dell XPS 15',
                code: 'LT-DL-XPS15',
                qty: 5,
                uom: 'Unit',
                received_before: 2
            },
            {
                id: 2,
                name: 'Office Chair Ergonomic',
                code: 'CH-ERG-01',
                qty: 10,
                uom: 'Pcs',
                received_before: 0
            },
            {
                id: 3,
                name: 'Printer HP LaserJet',
                code: 'PR-HP-LJ400',
                qty: 3,
                uom: 'Unit',
                received_before: 1
            }
        ]
    };

    const DUMMY_GR_HISTORY = {
        'PO-2026-001': [{
                gr_number: 'GR-2026-001',
                receipt_date: '2026-01-01',
                received_by: 'John Doe',
                status_code: 'GR_COMPLETE',
                status_name: 'Complete',
                total_items_received: 2
            },
            {
                gr_number: 'GR-2026-005',
                receipt_date: '2026-01-02',
                received_by: 'Jane Smith',
                status_code: 'GR_PROCESS',
                status_name: 'Processing',
                total_items_received: 1
            }
        ]
    };

    function loadPODetails() {
        const poNumber = $('#selPurchaseOrder').val();
        if (!poNumber) {
            Swal.fire('Warning', 'Please select a Purchase Order.', 'warning');
            return;
        }

        const $option = $('#selPurchaseOrder option:selected');
        const poData = {
            po_number: poNumber,
            vendor_name: $option.data('vendor'),
            department_name: $option.data('department'),
            po_date: $option.data('po-date')
        };

        // Simulasi: load dari dummy data
        const items = DUMMY_PO_DETAILS[poNumber] || [{
            id: 1,
            name: 'Mouse Wireless',
            code: 'MS-WL-01',
            qty: 20,
            uom: 'Pcs',
            received_before: 0
        }];
        const history = DUMMY_GR_HISTORY[poNumber] || [];

        // Update PO header
        $('#poNumberDisplay').text(poData.po_number);
        $('#vendorDisplay').text(poData.vendor_name);
        $('#poDateDisplay').text(poData.po_date);
        $('#departmentDisplay').text(poData.department_name);
        $('#hidden_po_number').val(poData.po_number);
        $('#poHistoryLabel').text(poData.po_number);

        // Render history & items
        renderGRHistory(history);
        renderGRItems(items);

        // Show sections
        $('#poSection').removeClass('d-none');
        $('#grFormSection').removeClass('d-none');

        // UI: GR_DRAFT (editable)
        setGRStatusUi('GR_DRAFT');
    }

    function renderGRHistory(history) {
        const $tbody = $('#grHistoryBody');
        if (!history.length) {
            $tbody.html(`<tr><td colspan="5" class="text-center">No previous GR found.</td></tr>`);
            return;
        }

        let rows = '';
        history.forEach(item => {
            let badgeClass = 'badge bg-secondary';
            if (item.status_code === 'GR_DRAFT') badgeClass = 'badge bg-warning';
            else if (item.status_code === 'GR_PROCESS') badgeClass = 'badge bg-info';
            else if (item.status_code === 'GR_COMPLETE') badgeClass = 'badge bg-success';

            rows += `
        <tr>
            <td>${item.gr_number}</td>
            <td>${item.receipt_date}</td>
            <td>${item.received_by}</td>
            <td><span class="${badgeClass}">${item.status_name}</span></td>
            <td>${item.total_items_received}</td>
        </tr>`;
        });
        $tbody.html(rows);
    }

    function renderGRItems(items) {
        const $tbody = $('#grItemsBody');
        let rows = '';

        items.forEach((item, idx) => {
            const qtyOrdered = parseFloat(item.qty);
            const qtyReceivedBefore = parseFloat(item.received_before) || 0;
            const remaining = qtyOrdered - qtyReceivedBefore;
            const unit = item.uom || 'Unit';
            const isUnit = unit.toLowerCase() === 'unit';

            rows += `
        <tr>
            <td>
                <strong>${item.name}</strong><br>
                <small class="text-muted">${item.code}</small>
                <input type="hidden" name="detail[${idx}][po_detail_id]" value="${item.id}">
            </td>
            <td class="text-center">${qtyOrdered} ${unit}</td>
            <td class="text-center">
                <input type="number" class="form-control form-control-sm qty-input" 
                    name="detail[${idx}][qty_received]" 
                    value="0"
                    min="0" 
                    max="${remaining}"
                    step="${isUnit ? '1' : 'any'}"
                    data-remaining="${remaining}">
                <small class="text-muted d-block mt-1">Remaining: <span class="text-danger">${remaining.toFixed(2)}</span> ${unit}</small>
            </td>
            <td class="text-center">${remaining.toFixed(2)} ${unit}</td>
            <td>
                ${isUnit 
                    ? `<input type="text" class="form-control form-control-sm serial-input" 
                             name="serials[${item.id}][]" 
                             placeholder="e.g. SN123, SN456"
                             data-po-detail-id="${item.id}">`
                    : `<span class="text-muted">—</span>`
                }
            </td>
        </tr>`;
        });

        $tbody.html(rows);
    }

    function setGRStatusUi(status) {
        const isDraft = (status === 'GR_DRAFT');
        $('.qty-input, .serial-input').prop('disabled', !isDraft);
        $('#btnSubmitDraft, #btnSubmitProcess').toggle(isDraft);
    }

    function submitGR(targetStatus) {
        const poNumber = $('#hidden_po_number').val();
        if (!poNumber) {
            Swal.fire('Error', 'PO not selected.', 'error');
            return;
        }

        // Validasi minimal 1 item
        let hasItems = false;
        $('.qty-input').each(function() {
            if (parseFloat($(this).val()) > 0) {
                hasItems = true;
                return false;
            }
        });

        if (!hasItems) {
            Swal.fire('Validation', 'At least one item must have quantity > 0.', 'warning');
            return;
        }

        // Simulasi sukses (tanpa kirim ke server)
        const action = targetStatus === 'GR_PROCESS' ? 'Submitted' : 'Saved';
        Swal.fire('Success', `GR created successfully as ${action}! (PO: ${poNumber})`, 'success')
            .then(() => {
                // Opsional: redirect ke list GR
                // window.location.href = BASE_URL + "goodsReceipts";
            });
    }
</script>