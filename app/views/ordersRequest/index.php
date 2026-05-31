<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Order Request</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Search Card -->
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
                                <input type="text" name="filterName" id="filterName" class="form-control"
                                    placeholder="Search by PR number or title">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="loadRequest();">
                                <i data-feather="search" class="me-2"></i> Search
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary w-100" onclick="cearFilter();">
                                <i data-feather="refresh-cw" class="me-2"></i> Refresh
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Request Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i data-feather="clipboard" class="me-2"></i> My Purchase Requests
                </h5>
                <a href="<?php echo BASE_URL; ?>purchase/NewPurchase" class="btn btn-primary btn-sm">
                    <i data-feather="plus" class="me-1"></i> New Request
                </a>
            </div>
            <div class="card-body card-over">
                <div class="responsive-container">
                    <table class='table-custom' id="requestTable">
                        <thead>
                            <tr>
                                <th>PR Number</th>
                                <th>Title</th>
                                <th>Department</th>
                                <th>Request Date</th>
                                <th>Total Estimated</th>
                                <th>Approval Level</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="requestTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>public/js/ordersRequest/main.js"></script>
