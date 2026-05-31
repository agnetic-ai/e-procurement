<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Request Approval</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px; margin:0;">
    <div style="max-width:700px; margin:auto; background:#ffffff; padding:0; border-radius:8px; overflow:hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">

        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #0d6efd, #6610f2); padding: 24px 30px;">
            <h1 style="color:#fff; margin:0; font-size:22px;">🔔 New Purchase Request</h1>
            <p style="color:rgba(255,255,255,0.85); margin:6px 0 0; font-size:14px;">Menunggu persetujuan Anda</p>
        </div>

        <div style="padding: 24px 30px;">
            <p>Yth. <strong><?= htmlspecialchars($approverName) ?></strong>,</p>

            <p>
                Terdapat <strong>Purchase Request</strong> baru yang membutuhkan persetujuan Anda.
            </p>

            <!-- PR Info -->
            <table width="100%" cellpadding="8" cellspacing="0" style="background:#f8f9fa; border-radius:6px; margin:16px 0;">
                <tr>
                    <td width="30%" style="font-weight:600; color:#495057;">Nomor PR</td>
                    <td>: <strong><?= htmlspecialchars($prNumber) ?></strong></td>
                </tr>
                <tr>
                    <td style="font-weight:600; color:#495057;">Judul PR</td>
                    <td>: <?= htmlspecialchars($prTitle) ?></td>
                </tr>
                <tr>
                    <td style="font-weight:600; color:#495057;">Department</td>
                    <td>: <?= htmlspecialchars($department) ?></td>
                </tr>
                <tr>
                    <td style="font-weight:600; color:#495057;">Requester</td>
                    <td>: <?= htmlspecialchars($requesterName) ?></td>
                </tr>
            </table>

            <h3 style="margin-top:20px; color:#2c3e50; font-size:16px;">Detail Item</h3>

            <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse; font-size:13px;">
                <thead style="background:#e8eaf6;">
                    <tr>
                        <th style="padding:8px;">No</th>
                        <th style="padding:8px;">Item</th>
                        <th style="padding:8px;">Vendor</th>
                        <th style="padding:8px;">Qty</th>
                        <th style="padding:8px;">Unit</th>
                        <th style="padding:8px;">Harga</th>
                        <th style="padding:8px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($items as $item): ?>
                    <tr>
                        <td align="center" style="padding:6px;"><?= $no++ ?></td>
                        <td style="padding:6px;"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td style="padding:6px;"><?= htmlspecialchars($item['vendor_name']) ?></td>
                        <td align="center" style="padding:6px;"><?= $item['quantity'] ?></td>
                        <td align="center" style="padding:6px;"><?= htmlspecialchars($item['unit']) ?></td>
                        <td align="right" style="padding:6px;">Rp <?= number_format($item['estimated_price'], 0, ',', '.') ?></td>
                        <td align="right" style="padding:6px;">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p style="margin-top:16px; font-size:16px;">
                <strong>Total Estimasi:</strong>
                <span style="color:#0d6efd; font-weight:700;">Rp <?= number_format($totalEstimated, 0, ',', '.') ?></span>
            </p>

            <!-- Quick Action Buttons -->
            <?php if (!empty($approveByEmail)): ?>
            <div style="margin:24px 0; padding:20px; background:#f0f4ff; border-radius:8px; text-align:center;">
                <p style="margin:0 0 12px; font-weight:600; color:#495057;">⚡ Quick Approval (via Email)</p>
                <a href="<?= $approveByEmail ?>"
                    style="display:inline-block; background:#198754; color:#ffffff; padding:12px 28px;
                      text-decoration:none; border-radius:6px; font-weight:600; margin:0 8px;">
                    ✅ Approve
                </a>
                <a href="<?= $rejectByEmail ?>"
                    style="display:inline-block; background:#dc3545; color:#ffffff; padding:12px 28px;
                      text-decoration:none; border-radius:6px; font-weight:600; margin:0 8px;">
                    ❌ Reject
                </a>
                <p style="margin:8px 0 0; font-size:11px; color:#6c757d;">
                    Klik tombol di atas untuk langsung approve/reject tanpa login
                </p>
            </div>
            <?php endif; ?>

            <!-- Full Detail Button -->
            <p style="text-align:center; margin-top:20px;">
                <a href="<?= $approvalUrl ?>"
                    style="display:inline-block; background:#0d6efd; color:#ffffff; padding:12px 24px;
                      text-decoration:none; border-radius:6px; font-weight:600;">
                    📋 Review Detail di Sistem
                </a>
            </p>

            <hr style="border:none; border-top:1px solid #e9ecef; margin:20px 0;">
            <p style="font-size:12px; color:#6c757d; margin:0;">
                Email ini dikirim otomatis oleh sistem <?= SITE_NAME ?>.
            </p>
        </div>
    </div>
</body>
</html>
