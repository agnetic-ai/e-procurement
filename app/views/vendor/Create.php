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
            <form method="POST" action="<?php echo BASE_URL; ?>vendors/store">
                <div class="row">
                    <!-- Existing fields -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vendor Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" required>
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
                            <label>Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name *</label>
                            <input type="text" class="form-control" name="company_name" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vendor Code *</label>
                            <input type="text" class="form-control" name="vendor_code" required>
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
                            <label>Status Code *</label>
                            <input type="text" class="form-control" name="status_code" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tax Number</label>
                            <input type="text" class="form-control" name="tax_number">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Credit Limit</label>
                            <input type="number" class="form-control" name="credit_limit" step="0.01">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Terms</label>
                            <input type="text" class="form-control" name="payment_terms">
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
                            <label>Established Year</label>
                            <input type="number" class="form-control" name="established_year" min="1900" max="2099">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total Transactions</label>
                            <input type="number" class="form-control" name="total_transactions" min="0">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Transaction Date</label>
                            <input type="date" class="form-control" name="last_transaction_date">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Save Vendor</button>
                        <a href="<?php echo BASE_URL; ?>vendors" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>