<!-- File: app/views/dashboard/index.php -->
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard</h3>
                <p class="text-subtitle text-muted">Welcome back, <?= htmlspecialchars($data['session']->get('full_name') ?? 'User') ?>!</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php
    $s = $data['stats'];
    function fmt($n) { return 'Rp ' . number_format($n, 0, ',', '.'); }
    ?>

    <!-- Stat Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i data-feather="truck" width="24" height="24"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Active Vendors</h6>
                            <h3 class="mb-0"><?= $s['vendors_active'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                            <i data-feather="shopping-cart" width="24" height="24"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Purchase Orders</h6>
                            <h3 class="mb-0"><?= $s['po_total'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i data-feather="clock" width="24" height="24"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Pending Approval</h6>
                            <h3 class="mb-0"><?= $s['po_pending'] + $s['pr_pending'] ?></h3>
                            <small class="text-muted"><?= $s['po_pending'] ?> PO · <?= $s['pr_pending'] ?> PR</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-danger bg-opacity-10 text-danger me-3">
                            <i data-feather="alert-triangle" width="24" height="24"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Overdue Invoices</h6>
                            <h3 class="mb-0 <?= $s['invoices_overdue'] > 0 ? 'text-danger' : '' ?>"><?= $s['invoices_overdue'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards Row 2 -->
    <div class="row mb-4">
        <div class="col-6 col-lg-4">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                            <i data-feather="dollar-sign" width="24" height="24"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total PO Value</h6>
                            <h4 class="mb-0"><?= fmt($s['total_spent']) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                            <i data-feather="credit-card" width="24" height="24"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Payments</h6>
                            <h4 class="mb-0"><?= fmt($s['total_paid']) ?></h4>
                            <small class="text-muted"><?= $s['invoices_paid'] ?> of <?= $s['invoices_total'] ?> invoices paid</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-secondary bg-opacity-10 text-secondary me-3">
                            <i data-feather="package" width="24" height="24"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Products</h6>
                            <h4 class="mb-0"><?= $s['products_active'] ?></h4>
                            <small class="text-muted"><?= $s['gr_total'] ?> goods received</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i data-feather="trending-up" class="me-2"></i>Monthly PO Spending</h5>
                </div>
                <div class="card-body">
                    <canvas id="spendingChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i data-feather="pie-chart" class="me-2"></i>PO Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row mb-4">
        <!-- Recent POs -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i data-feather="file-text" class="me-2"></i>Recent Purchase Orders</h5>
                    <a href="<?= BASE_URL ?>purchaseorder" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['recentPOs'])): ?>
                                    <?php foreach ($data['recentPOs'] as $po): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($po['po_number'] ?? $po['id']) ?></td>
                                            <td><?= htmlspecialchars($po['vendor_name']) ?></td>
                                            <td><?= date('d-M-Y', strtotime($po['order_date'])) ?></td>
                                            <td class="text-end"><?= fmt($po['total_amount']) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match(true) {
                                                    str_contains($po['status_code'], 'COMPLETED') => 'bg-success',
                                                    str_contains($po['status_code'], 'PARTIAL') => 'bg-info',
                                                    str_contains($po['status_code'], 'DRAFT') => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($po['status_name'] ?? $po['status_code']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">No purchase orders yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Vendors -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i data-feather="award" class="me-2"></i>Top Vendors by Spend</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th class="text-center">POs</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['topVendors'])): ?>
                                    <?php foreach ($data['topVendors'] as $i => $v): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary me-1">#<?= $i+1 ?></span>
                                                <?= htmlspecialchars($v['company_name']) ?>
                                            </td>
                                            <td class="text-center"><?= $v['po_count'] ?></td>
                                            <td class="text-end fw-semibold"><?= fmt($v['total_spent']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No vendor data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent PRs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i data-feather="clipboard" class="me-2"></i>Recent Purchase Requests</h5>
                    <a href="<?= BASE_URL ?>purchase" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>PR Number</th>
                                    <th>Requester</th>
                                    <th>Date</th>
                                    <th class="text-end">Est. Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['recentPRs'])): ?>
                                    <?php foreach ($data['recentPRs'] as $pr): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($pr['pr_number'] ?? $pr['id']) ?></td>
                                            <td><?= htmlspecialchars($pr['requester_name']) ?></td>
                                            <td><?= date('d-M-Y', strtotime($pr['request_date'])) ?></td>
                                            <td class="text-end"><?= fmt($pr['estimated_total']) ?></td>
                                            <td>
                                                <?php
                                                $prStatusClass = match(true) {
                                                    str_contains($pr['status_code'], 'APPROVED') => 'bg-success',
                                                    str_contains($pr['status_code'], 'DRAFT') => 'bg-warning',
                                                    str_contains($pr['status_code'], 'REJECTED') => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $prStatusClass ?>"><?= htmlspecialchars($pr['status_name'] ?? $pr['status_code']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">No purchase requests yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Spending Chart
    const spendingData = <?= json_encode($data['monthlySpending']) ?>;
    if (spendingData.length > 0) {
        const ctx1 = document.getElementById('spendingChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: spendingData.map(d => d.month_label),
                datasets: [{
                    label: 'PO Spending (Rp)',
                    data: spendingData.map(d => parseFloat(d.total)),
                    backgroundColor: 'rgba(67, 94, 190, 0.7)',
                    borderColor: 'rgba(67, 94, 190, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp ' + ctx.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                if (v >= 1000000) return (v/1000000).toFixed(0) + 'M';
                                if (v >= 1000) return (v/1000).toFixed(0) + 'K';
                                return v;
                            }
                        }
                    }
                }
            }
        });
    }

    // PO Status Distribution Chart
    const statusData = <?= json_encode($data['poStatusDist']) ?>;
    if (statusData.length > 0) {
        const ctx2 = document.getElementById('statusChart').getContext('2d');
        const colors = ['#435ebe', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#0dcaf0'];
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => d.status_name),
                datasets: [{
                    data: statusData.map(d => parseInt(d.cnt)),
                    backgroundColor: colors.slice(0, statusData.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, usePointStyle: true }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>

<style>
.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.card {
    transition: transform 0.2s ease;
}
.card:hover {
    transform: translateY(-2px);
}
</style>
