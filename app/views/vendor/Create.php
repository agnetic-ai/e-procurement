<!-- File: app/views/vendors/create.php -->
<div class="main-content container-fluid">
    <div class="page-title">
        <h3>Add New Vendor</h3>
        <p class="text-subtitle text-muted">Register a new vendor to the system</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Vendor Information</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>vendors/store">
                <div class="row">
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
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Save Vendor</button>
                        <a href="<?php echo BASE_URL; ?>vendors" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>