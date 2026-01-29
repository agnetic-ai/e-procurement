<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Vendor Management</h3>
                <p class="text-subtitle text-muted">Manage your vendors and suppliers</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
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
                                <input type="text" id="filterName" class="form-control"
                                    placeholder="Search by name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="choices form-select">
                                    <option value="square">Square</option>
                                    <option value="rectangle">Rectangle</option>
                                    <option value="rombo">Rombo</option>
                                    <option value="romboid">Romboid</option>
                                    <option value="trapeze">Trapeze</option>
                                    <option value="traible">Triangle</option>
                                    <option value="polygon">Polygon</option>
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
                    <i data-feather="list" class="me-2"></i> Vendor List
                </h5>
                <div>
                    <a href="<?php echo BASE_URL; ?>vendor/RegisterVendor" class="btn btn-primary btn-sm">
                        <i data-feather="plus" class="me-1"></i> Add Vendor
                    </a>
                </div>
            </div>
            <div class="responsive-container">
                <div class="card-body card-over">
                    <table class='table-custom' id="vendorsTable">
                        <thead>
                            <tr>
                                <th>Vendor Code</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Business Type</th>
                                <th>Status</th>
                                <th>Registered Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="vendorTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo BASE_URL; ?>app/service/vendors/main.js"></script>