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
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="search" class="me-2"></i> Search & Filter
                </h5>
            </div>
            <div class="card-body">
                <form id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i data-feather="search"></i>
                                </span>
                                <input type="text" name="filterName" class="form-control"
                                    placeholder="Search by name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="choices form-select">
                                    <option value="square">Square</option>
                                    <option value="rectangle">Rectangle</option>
                                    <option value="rombo">Rombo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-primary w-100" id="btnRefresh" onclick="GetGoodsReceiptsList();">
                                <i data-feather="search" class="me-2"></i> Search
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-secondary w-100" type="button" onclick="cearFilter();">
                                <i data-feather="refresh-cw" class="me-2"></i> Refresh
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i data-feather="list" class="me-2"></i> Purchase Order List
                </h5>
            </div>
            <div class="card-body card-over">
                <div class="responsive-container">
                    <table class='table-custom' id="poaTable">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>PR Number</th>
                                <th>Vendor</th>
                                <th>Department</th>
                                <th>Total Amount</th>
                                <th>PO Date</th>
                                <th>Received By</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="poaTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>public/js/approvalPo/main.js"></script>