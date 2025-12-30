<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Purchase Request</h3>
                <p class="text-subtitle text-muted">Manage your request and detail</p>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card mb-4">
            <div class="card-body">
                <div class="stepper">
                    <div class="step active" data-step="1">
                        <div class="circle">1</div>
                        <span>Request</span>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-step="2">
                        <div class="circle">2</div>
                        <span>Shipping & Billing</span>
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
            <form id="purchaseForm" method="post" onsubmit="return false;">
                <div id="step-1">
                    <div class="card">
                        <div class="card-header">
                            <h4>Request Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">PR Title</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Department</label>
                                        <select class="choices form-select" name="department_id" required>
                                            <option value="">--Select Department--</option>
                                            <option value="IT & Infrastructure">IT & Infrastructure</option>
                                            <option value="Finance">Finance</option>
                                            <option value="HR">HR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Requst Date</label>
                                        <input type="date" class="form-control" name="request_date">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" name="notes" style="resize: none;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="card-header">
                            <h4>Request Information Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Product Name</label>
                                        <select class="select2 form-control" name="product_id" onchange="SelectedProduct(this);">
                                            <option value="">--Select Prduct--</option>
                                            <?php if (!empty($data['product'])): ?>
                                                <?php foreach ($data['product'] as $product): ?>
                                                    <option value="<?php echo $product['productId']; ?>"
                                                        data-price="<?php echo $product['unitPrice']; ?>"
                                                        data-custom-properties='{"price": "<?php echo $product['unitPrice']; ?>", "vendor": "<?php echo $product['vendorName']; ?>"}'>
                                                        <?php echo htmlspecialchars($product['productName']) . ' - ' . $product['vendorName']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No Productt available</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Vendor / Supplier</label>
                                        <select class="select2 form-control" name="vendor_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Quantity</label>
                                        <input type="text" class="form-control" name="qty" onblur="calculateEstimatedPrice(this);">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Unit Price</label>
                                        <input type="text" class="form-control" name="unit_price" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Unit Of Meansure</label>
                                        <select class="form-control select2" name="uof">
                                            <option value="">-- Select Unit --</option>
                                            <?php if (!empty($data['units'])): ?>
                                                <?php foreach ($data['units'] as $unit): ?>
                                                    <option value="<?php echo $unit['value']; ?>">
                                                        <?php echo $unit['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No Unit available</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Estimated Price</label>
                                        <input type="text" class="form-control" name="estimated" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Product Description</label>
                                        <textarea class="form-control" name="product_desc" style="resize: none;"></textarea>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button class="btn btn-secondary mb-1" id="btnReset">
                                        <i data-feather="refresh-cw" class="me-2"></i> Reset
                                    </button>&nbsp;
                                    <button ntype="button" class="btn btn-primary me-1 mb-1" onclick="PreviewProduct();">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Preview Request</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="responsive-container">
                                        <table class='table-custom table-preview'>
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Product</th>
                                                    <th>Product Description</th>
                                                    <th>Qty</th>
                                                    <th>Unit</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                    <th align="center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="text-end mt-3">
                                        <h5>Grand Total: <strong></strong></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-light" disabled>Back</button>&nbsp;
                        <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                    </div>
                </div>
                <div id="step-2" class="d-none">
                    <div class="card">
                        <div class="card-header">
                            <h4>Shipping & Billing Address</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label>Shipping Address</label>
                                    <textarea class="form-control" name="shipping_address" rows="4" style="resize: none;"></textarea>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Billing Address</label>
                                    <textarea class="form-control" name="billing_address" rows="4" style="resize: none;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-light" onclick="prevStep()">Back</button>&nbsp;
                        <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                    </div>
                </div>
                <div id="step-3" class="d-none">
                    <div class="card">
                        <div class="card-header">
                            <h4>Approval Information</h4>
                        </div>
                        <div class="card-body">
                            <!-- <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Remarks Approver</label>
                                    <textarea class="form-control"
                                        name="remarks"
                                        rows="4"
                                        style="resize: none;"
                                        placeholder="Tambahkan catatan untuk approval (opsional)"></textarea>
                                    <small class="text-muted">
                                        Digunakan jika ada catatan khusus terkait persetujuan atau revisi.
                                    </small>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="responsive-container">
                                        <table class='table-custom table-approval'>
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
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-light" onclick="prevStep()">Back</button>&nbsp;
                        <button type="button" class="btn btn-primary" onclick="SubmitPurchaseForm();">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>app/service/purchase/main.js"></script>