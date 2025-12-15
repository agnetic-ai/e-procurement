<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>eProcurement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        :root {
            --primary-blue: #2196f3;
            --dark-blue: #1976d2;
            --light-blue: #e3f2fd;
            --gradient-start: #2196f3;
            --gradient-end: #e3f2fd;
            --white: #ffffff;
            --gray-light: #f5f5f5;
            --gray: #757575;
            --shadow: 0 10px 30px rgba(33, 150, 243, 0.15);
            --shadow-light: 0 5px 15px rgba(33, 150, 243, 0.1);
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: var(--shadow-light);
            color: white;
        }

        .logo-icon i {
            font-size: 28px;
            color: white;
        }

        .logo-section h1 {
            font-size: 24px;
            color: var(--dark-blue);
            font-weight: 700;
            margin-bottom: 5px;
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-section p {
            color: var(--gray);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-field {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: var(--gray-light);
        }

        .input-field:focus {
            outline: none;
            border-color: var(--primary-blue);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }

        .input-field::placeholder {
            color: #9e9e9e;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-blue);
            font-size: 18px;
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            box-shadow: var(--shadow-light);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn i {
            font-size: 18px;
        }

        .additional-links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .additional-links a {
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .additional-links a:hover {
            color: var(--dark-blue);
            text-decoration: underline;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        .error-message i {
            font-size: 18px;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 25px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
            }

            .logo-icon i {
                font-size: 24px;
            }

            .logo-section h1 {
                font-size: 22px;
            }

            .input-field {
                padding: 14px 20px 14px 45px;
            }
        }

        .loading {
            display: none;
        }

        .loading.active {
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <!-- <link rel="stylesheet" href="public/sb-admin/vendor/Font-Awesome/css/all.min.css"> -->
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i data-feather="codesandbox"></i>
                </div>
                <h1>eProcurement</h1>
            </div>
            <?php if (isset($error) && !empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success) && !empty($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <form class="form-login" method="POST" action="<?php echo BASE_URL ?? ''; ?>auth/processLogin" id="loginForm">
                <div class="form-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="input-field" name="username"
                        placeholder="Username" autofocus required
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : 'admin'; ?>">
                </div>
                <div class="form-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="input-field" name="password"
                        placeholder="Password" required
                        value="admin123">
                </div>
                <button class="login-btn" type="submit" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>SIGN IN</span>
                    <i class="fas fa-spinner loading" id="loadingIcon"></i>
                </button>

                <div class="additional-links">
                    <!-- <small class="text-muted">
                        Demo Accounts:<br>
                        <strong>Admin:</strong> admin / admin123<br>
                        <strong>Procurement:</strong> procurement / proc123
                    </small> -->
                </div>
            </form>
        </div>
        <div class="footer-text">
            <p>2025 - Sistem Inventory Management | by _404_</p>
            <p>© 2025 eProcurement. All rights reserved.</p>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>public/voler/assets/js/feather-icons/feather.min.js"></script>

    <script>
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const loadingIcon = document.getElementById('loadingIcon');

            loginForm.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                loadingIcon.classList.add('active');
                submitBtn.querySelector('span').textContent = 'Signing In...';
            });
        });
    </script>
</body>

</html>