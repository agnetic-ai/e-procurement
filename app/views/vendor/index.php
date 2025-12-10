<style>
    /* Adjust select agar sama dengan input text */
    .form-select,
    .choices__inner {
        width: 100%;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /* Untuk choices.js khusus */
    .choices {
        margin-bottom: 0;
    }

    .choices__inner {
        min-height: auto;
        padding: 0.375rem 0.75rem;
    }

    /* Responsive adjustment */
    @media (max-width: 768px) {

        .form-select,
        .choices__inner {
            font-size: 16px;
            /* Mencegah zoom di mobile */
        }
    }
</style>
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
                    <a href="<?php echo BASE_URL; ?>vendor/create" class="btn btn-primary btn-sm">
                        <i data-feather="plus" class="me-1"></i> Add Vendor
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class='table table-striped' id="vendorsTable">
                    <thead>
                        <tr>
                            <th>Vendor Code</th>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Business Type</th>
                            <th>Status</th>
                            <th>Registered Date</th>
                        </tr>
                    </thead>
                    <tbody id="vendorTableBody">

                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>


<script>
    $(document).ready(function() {
        console.log('jQuery is ready vendor:');
        loadVendors();
    });

    function loadVendors() {
        $.ajax({
            type: "POST",
            url: "<?php echo BASE_URL; ?>vendor/GetVendorList",
            data: $("#searchForm").serialize(),
            success: function(response) {
                const Header = $('#vendorTableBody');
                let body = '';
                if (response.status == 200) {
                    if ($.fn.DataTable.isDataTable("#vendorsTable")) {
                        $("#vendorsTable").DataTable().destroy();
                    }
                    response.result.forEach(element => {
                        let badge = StatusHandler(element.statusCode);
                        body += ` <tr>
                                    <td class="fw-semibold">${element.vendorCode}</td>
                                    <td>${element.companyName}</td>
                                    <td>${element.email}</td>
                                    <td>${element.phone}</td>
                                    <td>${element.businessType}</td>
                                    <td><label class='status-badge ${badge}'>${element.vendorStatus}</label></td>
                                    <td><small class="text-muted">${element.registrationDate}</small></td>
                                </tr>`;
                    });
                    Header.append(body);
                    $("#vendorsTable").DataTable();
                }
            },
            error: function(err) {
                alert("Error loading data");
            },
        });
    }
</script>
<!-- 
<script>
    // Tunggu DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Load data pertama kali
        loadVendors();

        // Search functionality
        document.getElementById('searchVendor').addEventListener('input', function() {
            loadVendors();
        });

        // Status filter
        document.getElementById('filterStatus').addEventListener('change', function() {
            loadVendors();
        });

        // Refresh button
        document.getElementById('btnRefresh').addEventListener('click', function() {
            loadVendors();
        });
    });

    // Function untuk load vendors via Ajax
    function loadVendors() {
        // Show loading
        document.getElementById('vendorTableBody').innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading vendors...</p>
            </td>
        </tr>
    `;

        // Get filter values
        const searchValue = document.getElementById('searchVendor').value;
        const statusValue = document.getElementById('filterStatus').value;

        // Buat FormData
        const formData = new FormData();
        if (searchValue) formData.append('search', searchValue);
        if (statusValue) formData.append('status', statusValue);

        // Ajax request menggunakan Fetch API
        fetch('<?php echo BASE_URL; ?>vendor/getVendorsAjax', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Data received:', data);

                if (data.success) {
                    renderVendors(data.data);
                    updateStats(data.stats);
                    initializeDataTable();
                } else {
                    showError(data.message || 'Failed to load vendors');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Network error: ' + error.message);
            });
    }

    // Function untuk render vendors
    function renderVendors(vendors) {
        const tbody = document.getElementById('vendorTableBody');

        if (vendors.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i data-feather="inbox" class="me-2" width="48" height="48"></i>
                        <p class="mb-1 mt-3">No vendors found</p>
                        <small>Try adjusting your search criteria</small>
                    </div>
                </td>
            </tr>
        `;

            // Refresh feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            return;
        }

        let html = '';
        vendors.forEach(vendor => {
            // Format status badge
            let statusClass = 'status-badge ';
            let statusText = vendor.vendorStatus || 'Unknown';

            switch (vendor.statusCode) {
                case 'active':
                    statusClass += 'status-active';
                    break;
                case 'pending':
                    statusClass += 'status-pending';
                    break;
                case 'inactive':
                    statusClass += 'status-inactive';
                    break;
                default:
                    statusClass += 'bg-secondary';
            }

            // Format data
            const vendorCode = vendor.vendorCode || 'N/A';
            const companyName = vendor.companyName || 'N/A';
            const businessType = vendor.businessType || 'N/A';
            const email = vendor.email || 'N/A';
            const phone = vendor.phone || 'N/A';
            const regDate = vendor.registrationDate || 'N/A';

            html += `
            <tr>
                <td class="fw-semibold">${vendorCode}</td>
                <td>
                    <div class="fw-medium">${companyName}</div>
                    ${businessType ? `<small class="text-muted">${businessType}</small>` : ''}
                </td>
                <td>${email}</td>
                <td>${phone}</td>
                <td>${businessType}</td>
                <td><span class="${statusClass}">${statusText}</span></td>
                <td><small class="text-muted">${regDate}</small></td>
            </tr>
        `;
        });

        tbody.innerHTML = html;

        // Refresh feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Function untuk update statistics
    function updateStats(stats) {
        document.getElementById('totalVendors').textContent = stats.total || 0;
        document.getElementById('activeVendors').textContent = stats.active || 0;
        document.getElementById('pendingVendors').textContent = stats.pending || 0;
    }

    // Function untuk initialize Simple DataTable
    function initializeDataTable() {
        // Cek jika Simple DataTables tersedia
        if (typeof simpleDatatables !== 'undefined') {
            // Destroy existing DataTable jika ada
            if (window.vendorDataTable) {
                window.vendorDataTable.destroy();
            }

            // Initialize new DataTable
            window.vendorDataTable = new simpleDatatables.DataTable("#vendorsTable", {
                searchable: true,
                fixedHeight: false,
                perPage: 10,
                perPageSelect: [5, 10, 15, 20],
                labels: {
                    placeholder: "Search vendors...",
                    searchTitle: "Search within table",
                    pageTitle: "Page {page}",
                    perPage: "entries per page",
                    noRows: "No entries to found",
                    info: "Showing {start} to {end} of {rows} entries"
                }
            });

            console.log('Simple DataTable initialized');
        } else {
            console.warn('Simple DataTables not available');
        }
    }

    // Function untuk show error
    function showError(message) {
        document.getElementById('vendorTableBody').innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4 text-danger">
                <i data-feather="alert-triangle" class="me-2"></i>
                <span>${message}</span>
            </td>
        </tr>
    `;

        // Refresh feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
</script> -->

<!-- <script>
    $(document).ready(function() {
        alert("ok");
    });
</script> -->