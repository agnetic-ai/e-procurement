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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>purchaseOrders/index">Purchase Order</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Purchase Orders</li>
                    </ol>
                </nav>
            </div>
        </div>

    </div>
    <section class="section">
        <div class="card mb-4">
            <div class="card-body">
                <div class="stepper">
                    <div class="step active" data-step="1">
                        <div class="circle">1</div>
                        <span>PO Info</span>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-step="2">
                        <div class="circle">2</div>
                        <span>Vendor & Payment</span>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-step="3">
                        <div class="circle">3</div>
                        <span>Approval</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <form id="orderApprovalForm" method="post" onsubmit="return false;">
                <div id="step-1">
                    <div class="card">
                        <div class="card-header">
                            <h4>Purchase Order Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">PO Number</label>
                                        <input type="text" class="form-control" name="po_number" data-id="<?= $data["PoHeader"]["purchaseOrderId"] ?>" value="<?= $data["PoHeader"]["poNumber"] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Reference PR</label>
                                        <input type="text" class="form-control" value="<?= $data["PoHeader"]["prNumber"] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" class="form-control" value="<?= $data["PoHeader"]["department"] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>PO Date</label>
                                        <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control"
                                            <?= in_array($data["PoHeader"]["statusCode"], ["PO_DRAFT"])
                                                ? 'required'
                                                : 'readonly' ?>
                                            name="notes" rows="3" style="resize:none;"><?= $data["PoHeader"]["notes"] ?? ""  ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="<?php echo BASE_URL; ?>PurchaseOrders" class="btn me-1 mb-1 btn-secondary">
                            <i data-feather="chevron-left"></i>Back</a>&nbsp;
                        <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                    </div>
                </div>
                <div id="step-2" class="d-none">
                    <div class="card">
                        <div class="card-header">
                            <h4>Vendor & Payment Information</h4>
                            <p class="text-muted mb-0">
                                Confirm vendor and select payment terms for this Purchase Order
                            </p>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required">Vendor</label>
                                        <input type="text"
                                            class="form-control"
                                            value="<?= $data["PoHeader"]["companyName"] ?>"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required">Payment Terms</label>
                                        <select class="form-control select2"
                                            name="payment_terms_id"
                                            <?= in_array($data["PoHeader"]["statusCode"], ["PO_DRAFT"])
                                                ? 'required'
                                                : 'disabled' ?>>
                                            <option value="">-- Select Payment Terms --</option>
                                            <?php foreach ($data["terms"] as $term) : ?>
                                                <option value="<?= $term["paymentId"] ?>"
                                                    <?= $term["paymentId"] == ($data["PoHeader"]["paymentTermsId"] ?? '') ? 'selected' : '' ?>>
                                                    <?= $term["paymentCode"] ?> - <?= $term["paymentName"] ?>
                                                    (<?= $term["paymentDescription"] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">
                                            Payment terms will be used for vendor invoice settlement
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="responsive-container">
                                <table class="table-custom table-preview">
                                    <thead>
                                        <tr>
                                            <th style="width:5%">No</th>
                                            <th>Product</th>
                                            <th style="width:10%">Qty</th>
                                            <th style="width:10%">Unit</th>
                                            <th style="width:15%">Unit Price</th>
                                            <th style="width:15%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data["PoDetails"] as $index => $detail) : ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <strong><?= $detail["productName"] ?></strong><br>
                                                    <small class="text-muted"><?= $detail["productDescription"] ?></small>
                                                </td>
                                                <td><?= $detail["quantity"] ?></td>
                                                <td><?= $detail["unit"] ?></td>
                                                <td><?= $detail["unitPrice"] ?></td>
                                                <td><?= $detail["subtotal"] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <h5>Grand Total: <strong><?= $data["PoDetails"][0]["grandTotal"] ?></strong></h5>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i data-feather="info" class="me-1"></i>
                                Quantity and price are inherited from the approved Purchase Request and cannot be changed.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-light" onclick="prevStep()">
                            Back
                        </button>&nbsp;
                        <button type="button" class="btn btn-primary" onclick="nextStep()">
                            Next
                        </button>
                    </div>
                </div>
                <div id="step-3" class="d-none">
                    <div class="card">
                        <div class="card-header">
                            <h4>Purchase Order Approval</h4>
                        </div>
                        <div class="card-body">
                            <div class="responsive-container">
                                <table class='table-custom table-approval-po'>
                                    <thead>
                                        <tr>
                                            <th>Level</th>
                                            <th>Role</th>
                                            <th>Approver</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-light" onclick="prevStep()">Back</button>
                        <?php if ($data["PoHeader"]["statusCode"] == "PO_DRAFT") : ?>
                            <button type="button" class="btn btn-primary" onclick="SubmitPurchaseOrder();">Submit</button>
                        <?php endif; ?>
                    </div>
                </div>

            </form>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>public/js/purchaseOrder/PurchaseOrderDetail.js"></script>