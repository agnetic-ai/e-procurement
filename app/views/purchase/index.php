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
                                        <select class="choices form-select" name="department" required>
                                            <option value="">--Select Department--</option>
                                            <option value="IT">IT</option>
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
                                        <select class="choices form-select" name="product_id" required onchange="SelectedProduct(this);">
                                            <option value="">--Select Product--</option>
                                            <?php if (!empty($data['product'])): ?>
                                                <?php foreach ($data['product'] as $product): ?>
                                                    <option value="<?php echo $product['productId']; ?>">
                                                        <?php echo htmlspecialchars($product['productName']) . ' - ' . $product['vendorName']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No vendor available</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Vendor / Supplier</label>
                                        <select class="select2 form-control" name="vendor_id" required>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Quantity</label>
                                        <input type="text" class="form-control" name="qty" required>
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
                                        <select class="choices form-control" name="uof" required>
                                            <option value="">-- Select Unit --</option>
                                            <option value="unit">Unit</option>
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
                                    <button ntype="button" class="btn btn-primary me-1 mb-1">
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
                                        <table class='table-custom' table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Product</th>
                                                    <th>Qty</th>
                                                    <th>Unit</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Laptop Lenovo Thinkpad</td>
                                                    <td>2</td>
                                                    <td>PCS</td>
                                                    <td>15,000,000</td>
                                                    <td>30,000,000</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end mt-3">
                                        <h5>Total: <strong>30,000,000</strong></h5>
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
                                    <textarea class="form-control" rows="4"></textarea>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Billing Address</label>
                                    <textarea class="form-control" rows="4"></textarea>
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
                            <h4>Approval Preview</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Level</th>
                                    <th>Role</th>
                                    <th>Approver</th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Manager</td>
                                    <td>Andi / Budi</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Finance</td>
                                    <td>Siti</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-light" onclick="prevStep()">Back</button>&nbsp;
                        <button type="submit" class="btn btn-success">Submit PR</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>app/service/purchase/main.js"></script>