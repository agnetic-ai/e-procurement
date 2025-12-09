<?php
// app/core/Session.php

class Session
{
    private $sessionName = 'EPROC_SESSION';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session settings
            $this->configureSession();

            // Start session
            session_start();

            // Regenerate session ID for security (first time only)
            $this->regenerateSessionId();

            // Check session timeout
            $this->checkTimeout();
        }
    }

    /**
     * Configure session settings
     */
    private function configureSession()
    {
        // Session name
        session_name($this->sessionName);

        // Cookie parameters
        $cookieParams = [
            'lifetime' => 86400, // 24 hours
            'path' => '/',
            'domain' => $this->getDomain(),
            'secure' => $this->isSecure(),
            'httponly' => true,
            'samesite' => 'Lax'
        ];

        session_set_cookie_params($cookieParams);

        // Session configuration
        ini_set('session.cookie_lifetime', $cookieParams['lifetime']);
        ini_set('session.gc_maxlifetime', 86400); // 24 hours
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
    }

    /**
     * Get domain for session cookie
     */
    private function getDomain()
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Remove port if present
        if (strpos($host, ':') !== false) {
            $host = substr($host, 0, strpos($host, ':'));
        }

        // For localhost, keep as is
        if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
            return $host;
        }

        // For subdomains, use the main domain
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            return '.' . $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
        }

        return '.' . $host;
    }

    /**
     * Check if connection is secure
     */
    private function isSecure()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
    }

    /**
     * Regenerate session ID (first time only)
     */
    private function regenerateSessionId()
    {
        if (!isset($_SESSION['session_regenerated'])) {
            session_regenerate_id(true);
            $_SESSION['session_regenerated'] = true;
            $_SESSION['session_started'] = time();
        }
    }

    /**
     * Check session timeout
     */
    private function checkTimeout()
    {
        $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 1800; // 30 minutes default

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            // Session expired
            $this->destroy();

            // Start new session for flash message
            session_start();
            $this->setFlash('error', 'Your session has expired. Please login again.');

            // Redirect to login
            header('Location: ' . (defined('BASE_URL') ? BASE_URL : '/') . 'login');
            exit();
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
    }

    /**
     * Set session value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        $_SESSION['last_activity'] = time(); // Update activity on set
    }

    /**
     * Get session value
     */
    public function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Remove session value
     */
    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy session completely
     */
    public function destroy()
    {
        // Clear all session variables
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();
    }

    /**
     * Set flash message
     */
    public function setFlash($key, $message)
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get flash message
     */
    public function getFlash($key)
    {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get user role
     */
    public function getUserRole()
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Get user ID
     */
    public function getUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get username
     */
    public function getUserName()
    {
        return $_SESSION['username'] ?? null;
    }

    /**
     * Get full name
     */
    public function getFullName()
    {
        return $_SESSION['full_name'] ?? null;
    }

    /**
     * Validate session across browsers
     */
    public function validateSession()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        // Check user agent consistency
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (!isset($_SESSION['user_agent'])) {
            $_SESSION['user_agent'] = $currentUserAgent;
        } elseif ($_SESSION['user_agent'] !== $currentUserAgent) {

            $_SESSION['user_agent'] = $currentUserAgent;
        }

        return true;
    }

    /**
     * Start user session after login
     */
    public function startUserSession($userData)
    {
        // Set user data
        $this->set('user_id', $userData['id']);
        $this->set('username', $userData['username']);
        $this->set('full_name', $userData['full_name']);
        $this->set('user_role', $userData['role']);
        $this->set('email', $userData['email']);
        $this->set('last_activity', time());
        $this->set('user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
        $this->set('login_time', time());

        // Regenerate session ID after login for security
        session_regenerate_id(true);
    }
}
