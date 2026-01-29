<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Purchase Request Approval</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;">
    <div style="max-width:700px; margin:auto; background:#ffffff; padding:20px; border-radius:6px;">

        <h2 style="color:#2c3e50;">Purchase Request Approval</h2>

        <p>Yth. <strong><?= htmlspecialchars($approverName) ?></strong>,</p>

        <p>
            Terdapat <strong>Purchase Request</strong> baru yang membutuhkan persetujuan Anda.
        </p>

        <!-- HEADER PR -->
        <table width="100%" cellpadding="6" cellspacing="0">
            <tr>
                <td width="30%"><strong>Nomor PR</strong></td>
                <td>: <?= htmlspecialchars($prNumber) ?></td>
            </tr>
            <tr>
                <td><strong>Judul PR</strong></td>
                <td>: <?= htmlspecialchars($prTitle) ?></td>
            </tr>
            <tr>
                <td><strong>Department</strong></td>
                <td>: <?= htmlspecialchars($department) ?></td>
            </tr>
            <tr>
                <td><strong>Requester</strong></td>
                <td>: <?= htmlspecialchars($requesterName) ?></td>
            </tr>
        </table>
        <h3 style="margin-top:20px;">Detail Item</h3>

        <table width="100%" cellpadding="8" cellspacing="0" border="1"
            style="border-collapse:collapse; font-size:13px;">
            <thead style="background:#f1f1f1;">
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Vendor</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($items as $item): ?>
                    <tr>
                        <td align="center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['vendor_name']) ?></td>
                        <td align="center"><?= $item['quantity'] ?></td>
                        <td align="center"><?= htmlspecialchars($item['unit']) ?></td>
                        <td align="right">Rp <?= number_format($item['estimated_price'], 0, ',', '.') ?></td>
                        <td align="right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="margin-top:10px;">
            <strong>Total Estimasi:</strong>
            Rp <?= number_format($totalEstimated, 0, ',', '.') ?>
        </p>
        <p style="text-align:center; margin-top:20px;">
            <a href="<?= $approvalUrl ?>"
                style="background:#0d6efd; color:#ffffff; padding:10px 16px;
                  text-decoration:none; border-radius:4px;">
                Review Purchase Request
            </a>
        </p>
        <p style="font-size:12px; color:#6c757d;">
            Email ini dikirim otomatis oleh sistem <?= SITE_NAME ?>.
        </p>
    </div>
</body>

</html>