<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Add New Vendor</h3>

                <p class="text-subtitle text-muted">Register a new vendor to the system</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>vendor/index">Vendor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Vendors</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Vendor Information</h4>
        </div>
        <div class="card-body">
            <form id="vendorForm" method="post" onsubmit="return false;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vendor Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>City ID</label>
                            <select class="choices form-control" name="city_id" required>
                                <option value="">-- Select City --</option>
                                <?php if (!empty($data['cities'])): ?>
                                    <?php foreach ($data['cities'] as $city): ?>
                                        <option value="<?php echo $city['citiesId']; ?>">
                                            <?php echo htmlspecialchars($city['citiesName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No cities available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Business Type</label>
                            <select class="choices form-control" name="business_type_id" required>
                                <option value="">-- Select Business --</option>
                                <?php if (!empty($data['business'])): ?>
                                    <?php foreach ($data['business'] as $city): ?>
                                        <option value="<?php echo $city['businessId']; ?>">
                                            <?php echo htmlspecialchars($city['businessName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No business available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tax Number</label>
                            <input type="text" class="form-control" name="tax_number" placeholder="00.000.000.0-000.000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-group">
                                <label>Payment Terms</label>
                                <select class="choices form-control" name="payment_terms" required>
                                    <option value="">-- Select Payment --</option>
                                    <?php if (!empty($data['payment'])): ?>
                                        <?php foreach ($data['payment'] as $payment): ?>
                                            <option value="<?php echo $payment['paymentId']; ?>">
                                                <?php echo htmlspecialchars($payment['paymentName']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No payment available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Website</label>
                            <input type="url" class="form-control" name="website">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="<?php echo BASE_URL; ?>vendors" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>app/service/vendor/RegisterVendor.js"></script>