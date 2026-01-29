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
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>Assets">Assets</a>
                        </li>
                        <li class="breadcrumb-item active">Assets Stock</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form id="searchForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i data-feather="search"></i>
                            </span>
                            <input type="text" class="form-control" name="serial_number" placeholder="Search Asset / Serial">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex ">
                        <button type="button" class="btn btn-secondary me-2" onclick="ResetFilter();">
                            <i data-feather="rotate-ccw"></i> Reset
                        </button>
                        <button type="button" class="btn btn-primary" onclick="GetAssetsInStockList();">
                            <i data-feather="search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body  card-over">
            <div class="responsive-container">
                <table class="table-custom" id="assetStockTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Asset</th>
                            <th>Serial Number</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody id="assetStockBody">
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>app/service/assets/stock.js"></script>