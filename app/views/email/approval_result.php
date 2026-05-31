<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Approval Result') ?> - <?= SITE_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            max-width: 480px;
            width: 90%;
            overflow: hidden;
            text-align: center;
        }
        .card-header {
            padding: 40px 30px 20px;
            background: <?= ($success ?? false) ? 'linear-gradient(135deg, #198754, #20c997)' : 'linear-gradient(135deg, #dc3545, #f87171)' ?>;
        }
        .icon {
            font-size: 64px;
            display: block;
            margin-bottom: 12px;
        }
        .card-header h1 {
            color: #fff;
            font-size: 24px;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
        }
        .message {
            font-size: 16px;
            color: #495057;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .pr-number {
            display: inline-block;
            background: #e9ecef;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: #0d6efd;
            color: #fff;
        }
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        .footer {
            padding: 16px 30px;
            background: #f8f9fa;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <span class="icon"><?= $icon ?? '📋' ?></span>
            <h1><?= htmlspecialchars($title ?? 'Result') ?></h1>
        </div>
        <div class="card-body">
            <?php if (!empty($prNumber)): ?>
            <div class="pr-number"><?= htmlspecialchars($prNumber) ?></div>
            <?php endif; ?>
            
            <p class="message"><?= htmlspecialchars($message ?? '') ?></p>

            <a href="<?= BASE_URL ?>" class="btn btn-primary">
                🏠 Buka Sistem E-Procurement
            </a>
        </div>
        <div class="footer">
            <?= SITE_NAME ?> &mdash; <?= date('d M Y H:i') ?> WIB
        </div>
    </div>
</body>
</html>
