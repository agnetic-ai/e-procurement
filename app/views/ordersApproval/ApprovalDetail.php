<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?></p>
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
            <form id="orderApprovalForm" method="post" onsubmit="return false;">
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
                                        <label hidden name="prNumber"><?= $data["purchase"]['prNumber'] ?></label>
                                        <label hidden name="currentLevel"><?= $data['level'] ?></label>
                                        <input type="text" class="form-control" name="title" value="<?= $data['purchase']["title"] ?? "" ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Department</label>
                                        <input type="text" class="form-control" name="department" value="<?= $data['purchase']["department"] ?? "" ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label class="required">Requst Date</label>
                                        <input type="date" class="form-control" name="request_date" value="<?= $data['purchase']["requestDate"] ?? "" ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" name="notes" style="resize: none;" readonly> <?= $data['purchase']["notes"] ?? "" ?> </textarea>
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
                                <div class="col-md-12 mb-2">
                                    <div class="responsive-container">
                                        <table class='table-custom table-preview'>
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Product</th>
                                                    <th>Product Description</th>
                                                    <th>Vendor/Supplier</th>
                                                    <th>Qty</th>
                                                    <th>Unit</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($data['purchaseDetail'])): ?>
                                                    <?php $no = 1; ?>
                                                    <?php foreach ($data['purchaseDetail'] as $detail): ?>
                                                        <tr>
                                                            <td><?= $no++; ?></td>
                                                            <td><?= htmlspecialchars($detail['productName']); ?></td>
                                                            <td><?= htmlspecialchars($detail['productDescription']); ?></td>
                                                            <td><?= htmlspecialchars($detail['vendorName']); ?></td>
                                                            <td><?= $detail['quantity'] ?></td>
                                                            <td><?= htmlspecialchars($detail['unit']); ?></td>
                                                            <td><?= $detail['estimatedPrice'] ?></td>
                                                            <td><?= $detail['subtotal'] ?> </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">
                                                            No purchase detail available
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>

                                        </table>
                                    </div>
                                    <div class="text-end mt-3">
                                        <h5>Grand Total: <strong><?= $data['purchase']['totalEstimated'] ?></strong></h5>
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
                                    <textarea class="form-control" name="shipping_address" rows="4" style="resize: none;" readonly><?= $data['purchase']['shippingAddress'] ?></textarea>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Billing Address</label>
                                    <textarea class="form-control" name="billing_address" rows="4" style="resize: none;" readonly><?= $data['purchase']['billingAddress'] ?></textarea>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="responsive-container">
                                        <table class='table-custom table-approval'>
                                            <thead>
                                                <tr>
                                                    <th>Level</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Approver</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($data['workflow'])): ?>
                                                    <?php foreach ($data['workflow'] as $workflow): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($workflow['level']); ?></td>
                                                            <td><?= htmlspecialchars($workflow['roleName']); ?></td>
                                                            <td>
                                                                <label class="status-badge <?= StatusHandler::handle($workflow['statusCode']); ?>">
                                                                    <?= htmlspecialchars($workflow['statusName']); ?>
                                                                </label>
                                                            </td>
                                                            <td><?= htmlspecialchars($workflow['username']); ?></td>
                                                            <td><?= htmlspecialchars($workflow['remarks'] ?? ""); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center">
                                                            No approval workflow available
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row mb-3">
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
                            </div>
                            <br>
                            <div class="row mb-3">
                                <div class="form-group">
                                    <label class="required">Action Approver</label>
                                    <select class="select2 form-control" name="action_approval">
                                        <option value="">--Select Action--</option>
                                        <?php if (!empty($data['status'])): ?>
                                            <?php foreach ($data['status'] as $status): ?>
                                                <?php if (in_array($status['statusCode'], ['APR_APPROVED', 'APR_REJECTED'])): ?>
                                                    <option value="<?= $status['statusCode']; ?>">
                                                        <?= htmlspecialchars($status['statusName']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">No Action Approval available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-light" onclick="prevStep()">Back</button>&nbsp;
                        <button type="button" class="btn btn-primary" onclick="submitApproval();">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>app/service/orderApproval/ApprovalDetail.js"></script>