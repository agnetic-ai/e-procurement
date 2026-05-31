<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Add New Product</h3>
                <p class="text-subtitle text-muted">Add a new product to the system</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>product/index">Product</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Product</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Product Information</h4>
        </div>
        <div class="card-body">
            <form id="productForm" method="post" onsubmit="return false;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Product Code</label>
                            <input type="text" class="form-control" name="code" placeholder="e.g. PRD-001" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Product Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Vendor</label>
                            <select class="choices form-control" name="vendor_id" required>
                                <option value="">-- Select Vendor --</option>
                                <?php if (!empty($data['vendor'])): ?>
                                    <?php foreach ($data['vendor'] as $vendor): ?>
                                        <option value="<?php echo $vendor['vendorId']; ?>">
                                            <?php echo htmlspecialchars($vendor['companyName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No vendor available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Product Category</label>
                            <select class="choices form-control" name="category" required>
                                <option value="">-- Select Categories --</option>
                                <?php if (!empty($data['categories'])): ?>
                                    <?php foreach ($data['categories'] as $categories): ?>
                                        <option value="<?php echo $categories['categoryId']; ?>">
                                            <?php echo htmlspecialchars($categories['categoryName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No categories available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Unit Of Meansure</label>
                            <select class="choices form-control" name="uof" required>
                                <option value="">-- Select Unit --</option>
                                <option value="unit">Unit</option>
                                <!-- <option value="pkg">Pkg</option>
                                <option value="rim">Rim</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Valid From</label>
                            <input type="date" class="form-control" name="valid_from" required>
                        </div>
                        <div class="form-group">
                            <label class="required">Valid To</label>
                            <input type="date" class="form-control" name="valid_to" required>
                        </div>
                        <div class="form-group">
                            <label class="required">Status</label>
                            <select class="choices form-control" name="status_code" required>
                                <option value="">-- Select Status --</option>
                                <?php if (!empty($data['status'])): ?>
                                    <?php foreach ($data['status'] as $status): ?>
                                        <option value="<?php echo $status['statusCode']; ?>">
                                            <?php echo htmlspecialchars($status['statusName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No status available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Unit Price</label>
                            <input type="text" class="form-control" name="unit_price" onblur="formatMoney(this);" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description"></textarea>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="<?php echo BASE_URL; ?>product" class="btn btn-secondary">
                            <i data-feather="chevron-left"></i>
                            Back
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>public/js/product/NewProduct.js"></script>