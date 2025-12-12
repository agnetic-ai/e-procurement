<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Update Product</h3>
                <p class="text-subtitle text-muted">Manage product and Information</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>product/index">Product</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Update Product</li>
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
            <form id="updateForm" method="post" onsubmit="return false;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Product Name *</label>
                            <label style="display: none;" name="product_id"><?= $data["product"]["prodcutId"] ?></label>
                            <label style="display: none;" name="product_vendor_id"><?= $data["product"]["productVendorId"] ?></label>
                            <input type="text" class="form-control" name="name" value="<?= $data["product"]["productName"] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vendor *</label>
                            <select class="choices form-control" name="vendor_id" required>
                                <option value="">-- Select Vendor --</option>
                                <?php if (!empty($data['vendor'])): ?>
                                    <?php foreach ($data['vendor'] as $vendor): ?>
                                        <option value="<?php echo $vendor['vendorId']; ?>" <?php if ($vendor['vendorId'] == $data['product']["vendorId"]) echo "selected"; ?>>
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
                            <label>Product Category *</label>
                            <select class="choices form-control" name="category" required>
                                <option value="">-- Select Categories --</option>
                                <?php if (!empty($data['categories'])): ?>
                                    <?php foreach ($data['categories'] as $categories): ?>
                                        <option value="<?php echo $categories['categoryId']; ?>" <?php if ($categories['categoryId'] == $data['product']["categoryId"]) echo "selected"; ?>>
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
                            <label>Unit Of Meansure *</label>
                            <select class="choices form-control" name="uof" required>
                                <option value="">-- Select Unit --</option>
                                <option value="unit" <?php if ("unit" == $data['product']["uof"]) echo "selected"; ?>>Unit</option>
                                <option value="pkg" <?php if ("pkg" == $data['product']["uof"]) echo "selected"; ?>>Pkg</option>
                                <option value="rim" <?php if ("rim" == $data['product']["uof"]) echo "selected"; ?>>Rim</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Valid From *</label>
                            <input type="date" class="form-control" name="valid_from" value="<?= $data["product"]["validFrom"] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Valid To *</label>
                            <input type="date" class="form-control" name="valid_to" value="<?= $data["product"]["validTo"] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status *</label>
                            <select class="choices form-control" name="status_code" required>
                                <option value="">-- Select Status --</option>
                                <?php if (!empty($data['status'])): ?>
                                    <?php foreach ($data['status'] as $status): ?>
                                        <option value="<?php echo $status['statusCode']; ?>" <?php if ($status['statusCode'] == $data['product']["statusCode"]) echo "selected"; ?>>
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
                            <label>Unit Price *</label>
                            <input type="text" class="form-control" name="unit_price" value="<?= $data["product"]["unitPrice"] ?>" onblur="formatMoney(this);" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description"><?= $data["product"]["description"] ?></textarea>
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
<script src="<?php echo BASE_URL; ?>app/service/product/UpdateProduct.js"></script>