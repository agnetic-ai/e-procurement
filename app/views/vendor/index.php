<!-- File: app/views/vendors/index.php -->
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3>Vendor Management</h3>
                <p class="text-subtitle text-muted">Manage your vendors and suppliers</p>
            </div>
            <div class="col-12 col-md-6 text-end">
                <a href="<?php echo BASE_URL; ?>vendor/create" class="btn btn-primary">
                    <i data-feather="plus"></i> Add New Vendor
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Search & Filter</h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                <form class="form">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="first-name-column">First Name</label>
                                <input
                                    type="text"
                                    id="first-name-column"
                                    class="form-control"
                                    placeholder="First Name"
                                    name="fname-column" />
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="last-name-column">Last Name</label>
                                <input type="text" id="last-name-column" class="form-control"
                                    placeholder="Last Name"
                                    name="lname-column" />
                            </div>
                        </div>
                        <div class="buttons">
                            <a href="#" class="btn btn-outline-primary">Search</a>
                            <a href="#" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Vendors Table Card -->
    <div class="card" style="border-radius: 12px; overflow: hidden; border: 1px solid #e3f2fd; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.08);">
        <div class="card-header" style="background: linear-gradient(135deg, #2196f3, #1976d2); border: none; border-radius: 12px 12px 0 0; padding: 1rem 1.5rem;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0" style="color: white; font-weight: 600;">
                        <i data-feather="users" class="me-2"></i> Vendor List
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <span class="badge bg-light text-primary me-2">Total: 45</span>
                    <span class="badge bg-success me-2">Active: 38</span>
                    <span class="badge bg-warning">Pending: 7</span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="vendorsTable">
                    <thead style="background-color: #e3f2fd;">
                        <tr>
                            <th style="padding: 1rem; border-bottom: 2px solid #2196f3; color: #1976d2; font-weight: 600;">
                                <i data-feather="hash" class="me-1"></i> ID
                            </th>
                            <th style="padding: 1rem; border-bottom: 2px solid #2196f3; color: #1976d2; font-weight: 600;">
                                <i data-feather="user" class="me-1"></i> Vendor Name
                            </th>
                            <th style="padding: 1rem; border-bottom: 2px solid #2196f3; color: #1976d2; font-weight: 600;">
                                <i data-feather="mail" class="me-1"></i> Email
                            </th>
                            <th style="padding: 1rem; border-bottom: 2px solid #2196f3; color: #1976d2; font-weight: 600;">
                                <i data-feather="phone" class="me-1"></i> Phone
                            </th>
                            <th style="padding: 1rem; border-bottom: 2px solid #2196f3; color: #1976d2; font-weight: 600;">
                                <i data-feather="activity" class="me-1"></i> Status
                            </th>
                            <th style="padding: 1rem; border-bottom: 2px solid #2196f3; color: #1976d2; font-weight: 600; text-align: center;">
                                <i data-feather="settings" class="me-1"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Vendor 1 -->
                        <tr style="border-bottom: 1px solid #f0f0f0; transition: all 0.3s;">
                            <td style="padding: 1rem; font-weight: 600; color: #2196f3;">V001</td>
                            <td style="padding: 1rem;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3" style="width: 36px; height: 36px; background: linear-gradient(135deg, #2196f3, #1976d2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        SJ
                                    </div>
                                    <div>
                                        <h6 class="mb-0">PT Supplier Jaya</h6>
                                        <small class="text-muted">Supplier</small>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <a href="mailto:jaya@email.com" class="text-primary">jaya@email.com</a>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="text-muted">08123456789</span>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #4caf50, #2e7d32); padding: 0.35rem 1rem; font-size: 0.85rem;">
                                    <i data-feather="check-circle" class="me-1" style="width: 14px; height: 14px;"></i> Active
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" style="border-radius: 6px 0 0 6px; border-color: #2196f3;">
                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" style="border-radius: 0; border-color: #2196f3;">
                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius: 0 6px 6px 0; border-color: #f44336;">
                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Vendor 2 -->
                        <tr style="border-bottom: 1px solid #f0f0f0; transition: all 0.3s;">
                            <td style="padding: 1rem; font-weight: 600; color: #2196f3;">V002</td>
                            <td style="padding: 1rem;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3" style="width: 36px; height: 36px; background: linear-gradient(135deg, #ff9800, #f57c00); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        MS
                                    </div>
                                    <div>
                                        <h6 class="mb-0">CV Mandiri Sejahtera</h6>
                                        <small class="text-muted">Contractor</small>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <a href="mailto:mandiri@email.com" class="text-primary">mandiri@email.com</a>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="text-muted">08234567890</span>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #ff9800, #f57c00); padding: 0.35rem 1rem; font-size: 0.85rem;">
                                    <i data-feather="clock" class="me-1" style="width: 14px; height: 14px;"></i> Pending
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" style="border-radius: 6px 0 0 6px; border-color: #2196f3;">
                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" style="border-radius: 0; border-color: #2196f3;">
                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius: 0 6px 6px 0; border-color: #f44336;">
                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Vendor 3 -->
                        <tr style="border-bottom: 1px solid #f0f0f0; transition: all 0.3s;">
                            <td style="padding: 1rem; font-weight: 600; color: #2196f3;">V003</td>
                            <td style="padding: 1rem;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3" style="width: 36px; height: 36px; background: linear-gradient(135deg, #9c27b0, #7b1fa2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        GT
                                    </div>
                                    <div>
                                        <h6 class="mb-0">PT Global Teknik</h6>
                                        <small class="text-muted">Service Provider</small>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <a href="mailto:global@email.com" class="text-primary">global@email.com</a>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="text-muted">08345678901</span>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #4caf50, #2e7d32); padding: 0.35rem 1rem; font-size: 0.85rem;">
                                    <i data-feather="check-circle" class="me-1" style="width: 14px; height: 14px;"></i> Active
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" style="border-radius: 6px 0 0 6px; border-color: #2196f3;">
                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" style="border-radius: 0; border-color: #2196f3;">
                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius: 0 6px 6px 0; border-color: #f44336;">
                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Footer with Pagination -->
        <div class="card-footer" style="background-color: #f8fafc; border-top: 1px solid #e3f2fd; padding: 1rem 1.5rem;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        Showing <span class="fw-semibold">3</span> of <span class="fw-semibold">45</span> vendors
                    </p>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Page navigation" class="float-end">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">
                                    <i data-feather="chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <i data-feather="chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Feather Icons
    feather.replace();

    // Search functionality
    document.getElementById('searchVendor').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#vendorsTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter functionality
    document.getElementById('filterStatus').addEventListener('change', filterTable);
    document.getElementById('filterType').addEventListener('change', filterTable);

    function filterTable() {
        const statusFilter = document.getElementById('filterStatus').value;
        const typeFilter = document.getElementById('filterType').value;
        const rows = document.querySelectorAll('#vendorsTable tbody tr');

        rows.forEach(row => {
            const status = row.querySelector('.badge').textContent.toLowerCase().trim();
            const type = row.querySelector('small.text-muted').textContent.toLowerCase().trim();

            const statusMatch = !statusFilter || status.includes(statusFilter);
            const typeMatch = !typeFilter || type.includes(typeFilter);

            row.style.display = statusMatch && typeMatch ? '' : 'none';
        });
    }

    // Clear filters
    document.getElementById('btnClearFilters').addEventListener('click', function() {
        document.getElementById('searchVendor').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterType').value = '';

        const rows = document.querySelectorAll('#vendorsTable tbody tr');
        rows.forEach(row => row.style.display = '');
    });

    // Row hover effect
    const rows = document.querySelectorAll('#vendorsTable tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8fafc';
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(33, 150, 243, 0.1)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
</script>