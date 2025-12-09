<!-- File: app/views/dashboard/index.php -->
<div class="main-content container-fluid">
    <div class="page-title">
        <h3>Dashboard</h3>
        <p class="text-subtitle text-muted">eProcurement Management System</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Vendors</h5>
                    <h2>45</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Purchase Orders</h5>
                    <h2>128</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Pending</h5>
                    <h2>23</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Spent</h5>
                    <h2>$12,840</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h4>Recent Purchase Orders</h4>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Vendor</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PO-2025-001</td>
                        <td>PT Supplier Jaya</td>
                        <td>2025-01-15</td>
                        <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>