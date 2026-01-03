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
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i data-feather="filter"></i> Filter</h5>
            </div>
            <div class="card-body">
                <form method="get" action="<?php echo BASE_URL; ?>goodsReceipts">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PO Number</label>
                                <input type="text" class="form-control" name="po_number"
                                    placeholder="Enter PO Number" value="<?= $_GET['po_number'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <option value="">All Status</option>
                                    <option value="GR_DRAFT" <?= ($_GET['status'] ?? '') == 'GR_DRAFT' ? 'selected' : '' ?>>Draft</option>
                                    <option value="GR_PROCESS" <?= ($_GET['status'] ?? '') == 'GR_PROCESS' ? 'selected' : '' ?>>Process</option>
                                    <option value="GR_COMPLETE" <?= ($_GET['status'] ?? '') == 'GR_COMPLETE' ? 'selected' : '' ?>>Complete</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vendor</label>
                                <input type="text" class="form-control" name="vendor"
                                    placeholder="Search vendor" value="<?= $_GET['vendor'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i data-feather="truck"></i> Goods Receipt List</h5>
                <div class="header-actions">
                    <a href="<?php echo BASE_URL; ?>GoodsReceipts/CreateGoodsReceipts" class="btn btn-primary">
                        <i data-feather="plus"></i> Create New GR
                    </a>
                </div>
            </div>
            <div class="card-body card-over">
                <div class="responsive-container">
                    <table class='table-custom' id="goodsTable">
                        <thead>
                            <tr>
                                <th>GR Number</th>
                                <th>PO Number</th>
                                <th>PR Number</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Received Date</th>
                                <th>Received By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="goodsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>app/service/goodsReceipts/main.js"></script>