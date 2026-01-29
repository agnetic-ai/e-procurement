<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Assets</li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>Assets/Stock">Assets Stock</a>
                        </li>
                        <li class="breadcrumb-item active">Assign Asset</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <form id="assignAssetForm">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Asset Information</h5>
            </div>
            <div class="card-body">
                <input type="hidden" name="asset_unit_id" value="<?= $data["assetsInfo"]['assetUnitId'] ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Asset Name</label>
                            <input type="text"
                                class="form-control"
                                value="<?= $data["assetsInfo"]['assetName'] ?>"
                                readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Serial Number</label>
                            <input type="text"
                                class="form-control"
                                value="<?= $data["assetsInfo"]['serialNumber'] ?>"
                                readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <input type="text"
                                class="form-control"
                                value="<?= $data["assetsInfo"]['categoryName'] ?>"
                                readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Current Status</label>
                            <input type="text"
                                class="form-control"
                                value="<?= $data["assetsInfo"]['statusName'] ?>"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Assignment Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Employee</label>
                            <select class="choices form-select" name="employee_id" required>
                                <option value="">-- Select Employee --</option>
                                <?php if (!empty($data['employee'])): ?>
                                    <?php foreach ($data['employee'] as $emp): ?>
                                        <option value="<?php echo $emp['employeeId']; ?>">
                                            <?php echo $emp['employeeName'] ?> - (<?php echo $emp['employeeCode'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No Employee available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Assign Date</label>
                            <input type="date"
                                class="form-control"
                                name="assigned_date"
                                value="<?= date('Y-m-d') ?>"
                                required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control"
                                name="notes"
                                rows="3"
                                placeholder="Optional notes"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="<?= BASE_URL ?>assets/stock"
                class="btn btn-secondary me-2">
                Cancel
            </a>
            <button type="button"
                class="btn btn-primary"
                onclick="SubmitAssignAsset()">
                Assign Asset
            </button>
        </div>
    </form>
</div>
<script src="<?php echo BASE_URL; ?>app/service/assets/assign.js"></script>