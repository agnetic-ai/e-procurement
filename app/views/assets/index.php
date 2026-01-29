<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-heading mb-4">
        <h3>Asset Management</h3>
        <p class="text-muted">
            Kelola distribusi dan kepemilikan aset perusahaan
        </p>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Asset</h6>
                    <h4 class="fw-bold"><?= $data["totalAssets"] ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>In Stock</h6>
                    <h4 class="fw-bold text-success"><?= $inStock ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Assigned</h6>
                    <h4 class="fw-bold text-primary"><?= $assigned ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Damaged / Lost</h6>
                    <h4 class="fw-bold text-danger"><?= $damage ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTION MENU -->
    <div class="row mb-4">
        <div class="col-md-4">
            <a href="<?= BASE_URL ?>assets/stock" class="card text-decoration-none">
                <div class="card-body text-center position-relative">
                    <i data-feather="package" class="mb-2"></i>
                    <h5>Asset Stock</h5>
                    <p class="text-muted">Lihat aset yang tersedia di gudang</p>
                    <i data-feather="arrow-right"
                        class="position-absolute bottom-0 end-0 m-3 text-muted">
                    </i>
                </div>

            </a>
        </div>

        <div class="col-md-4">
            <a href="<?= BASE_URL ?>assets/assigned" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i data-feather="user-check" class="mb-2"></i>
                    <h5>Assigned Asset</h5>
                    <p class="text-muted">
                        Aset yang sedang digunakan karyawan
                    </p>
                    <i data-feather="arrow-right"
                        class="position-absolute bottom-0 end-0 m-3 text-muted">
                    </i>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= BASE_URL ?>assets/history" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i data-feather="clock" class="mb-2"></i>
                    <h5>Asset History</h5>
                    <p class="text-muted">
                        Riwayat distribusi dan pengembalian aset
                    </p>
                    <i data-feather="arrow-right"
                        class="position-absolute bottom-0 end-0 m-3 text-muted">
                    </i>
                </div>
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>Recent Asset Assignment</h5>
        </div>
        <div class="card-body card-over">
            <div class="responsive-container">
                <table class='table-custom' id="recent">
                    <thead>
                        <tr>
                            <th>Asset Name</th>
                            <th>Serial Number</th>
                            <th>Employee Name</th>
                            <th>Assigned Date</th>
                            <th>Assigned By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentAssets)) : ?>
                            <?php foreach ($recentAssets as $row) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['assetName']) ?></td>
                                    <td><?= htmlspecialchars($row['serialNumber']) ?></td>
                                    <td><?= htmlspecialchars($row['employeeName']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($row['assignedAt'])) ?></td>
                                    <td><?= htmlspecialchars($row['fullName']) ?></td>
                                    <td>
                                        <label class="status-badge <?= StatusHandler::handle($row['statusCode']); ?>">
                                            <?= htmlspecialchars($row['statusName']); ?>
                                        </label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No recent assignment
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>