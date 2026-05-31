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
                                <input type="text" id="filterName" class="form-control"
                                    placeholder="Search by name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="choices form-select">
                                    <option value="square">Square</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" id="btnRefresh">
                                <i data-feather="refresh-cw" class="me-2"></i> Refresh
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Vendors Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i data-feather="list" class="me-2"></i> Order Approval List
                </h5>
            </div>
            <div class="responsive-container">
                <div class="card-body card-over">
                    <table class='table-custom' id="approvalTable">
                        <thead>
                            <tr>
                                <th>PR Nmber</th>
                                <th>Department</th>
                                <th>Title</th>
                                <th>Request Date</th>
                                <th>Request By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="approvalTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>public/js/orderApproval/main.js"></script>