<?php
// app/config/constants.php

// ======================
// APPLICATION CONSTANTS
// ======================
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://150.109.23.12/eprocurement/');
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'e-Procurement System');
}

if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}

if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}

// ======================
// SECURITY CONSTANTS - DIPERLUKAN UNTUK USERMODEL
// ======================
if (!defined('MAX_LOGIN_ATTEMPTS')) {
    define('MAX_LOGIN_ATTEMPTS', 5);           // Maksimum percobaan login
}

if (!defined('LOGIN_TIMEOUT')) {
    define('LOGIN_TIMEOUT', 900);              // 15 menit dalam detik (15 * 60)
}

if (!defined('SESSION_TIMEOUT')) {
    define('SESSION_TIMEOUT', 1800);           // Timeout session (30 menit)
}

// ======================
// DATABASE CONSTANTS
// ======================
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'eprocurement_db');
}


// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
