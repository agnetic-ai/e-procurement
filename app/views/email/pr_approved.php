<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PR Approved</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px; margin:0;">
    <div style="max-width:700px; margin:auto; background:#ffffff; padding:0; border-radius:8px; overflow:hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">

        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #198754, #20c997); padding: 24px 30px;">
            <h1 style="color:#fff; margin:0; font-size:22px;">✅ Purchase Request Approved</h1>
            <p style="color:rgba(255,255,255,0.85); margin:6px 0 0; font-size:14px;">PR Anda telah disetujui oleh semua approver</p>
        </div>

        <div style="padding: 24px 30px;">
            <p>Yth. <strong><?= htmlspecialchars($requesterName) ?></strong>,</p>

            <p>
                Purchase Request <strong><?= htmlspecialchars($prNumber) ?></strong> telah <span style="color:#198754; font-weight:bold;">FULLY APPROVED</span> oleh seluruh level approval.
            </p>

            <!-- PR Info -->
            <table width="100%" cellpadding="8" cellspacing="0" style="background:#f8f9fa; border-radius:6px; margin:16px 0;">
                <tr>
                    <td width="30%" style="font-weight:600; color:#495057;">Nomor PR</td>
                    <td>: <?= htmlspecialchars($prNumber) ?></td>
                </tr>
                <tr>
                    <td style="font-weight:600; color:#495057;">Judul</td>
                    <td>: <?= htmlspecialchars($prTitle) ?></td>
                </tr>
                <tr>
                    <td style="font-weight:600; color:#495057;">Department</td>
                    <td>: <?= htmlspecialchars($department) ?></td>
                </tr>
                <tr>
                    <td style="font-weight:600; color:#495057;">Total Estimasi</td>
                    <td>: <strong style="color:#198754;">Rp <?= number_format($totalEstimated, 0, ',', '.') ?></strong></td>
                </tr>
            </table>

            <?php if (!empty($items)): ?>
            <h3 style="margin-top:20px; color:#2c3e50; font-size:16px;">Detail Item</h3>
            <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse; font-size:13px;">
                <thead style="background:#e8f5e9;">
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
            <?php endif; ?>

            <div style="margin-top:24px; padding:16px; background:#d1e7dd; border-radius:6px; text-align:center;">
                <p style="margin:0; color:#0f5132; font-weight:600;">
                    🎉 PR ini siap diproses ke tahap Purchase Order
                </p>
            </div>

            <p style="text-align:center; margin-top:20px;">
                <a href="<?= $prUrl ?>"
                    style="display:inline-block; background:#198754; color:#ffffff; padding:12px 24px;
                      text-decoration:none; border-radius:6px; font-weight:600;">
                    View Purchase Request
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
