<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PR Rejected</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px; margin:0;">
    <div style="max-width:700px; margin:auto; background:#ffffff; padding:0; border-radius:8px; overflow:hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">

        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #dc3545, #f87171); padding: 24px 30px;">
            <h1 style="color:#fff; margin:0; font-size:22px;">❌ Purchase Request Rejected</h1>
            <p style="color:rgba(255,255,255,0.85); margin:6px 0 0; font-size:14px;">PR Anda memerlukan perbaikan</p>
        </div>

        <div style="padding: 24px 30px;">
            <p>Yth. <strong><?= htmlspecialchars($requesterName) ?></strong>,</p>

            <p>
                Purchase Request <strong><?= htmlspecialchars($prNumber) ?></strong> telah <span style="color:#dc3545; font-weight:bold;">DITOLAK</span> oleh approver.
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
            </table>

            <?php if (!empty($remarks)): ?>
            <div style="margin:16px 0; padding:16px; background:#f8d7da; border-radius:6px; border-left:4px solid #dc3545;">
                <p style="margin:0; font-weight:600; color:#842029;">Alasan Penolakan:</p>
                <p style="margin:8px 0 0; color:#842029;"><?= nl2br(htmlspecialchars($remarks)) ?></p>
            </div>
            <?php endif; ?>

            <p style="text-align:center; margin-top:20px;">
                <a href="<?= $prUrl ?>"
                    style="display:inline-block; background:#dc3545; color:#ffffff; padding:12px 24px;
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
