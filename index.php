<?php

define('BASE_URL', 'http://localhost/e-procurement/');
define('SITE_NAME', 'e-Procurement System');
define('APP_PATH', __DIR__ . '/app');
define('VIEWS_PATH', APP_PATH . '/views');
define('PUBLIC_PATH', __DIR__ . '/public');
require_once __DIR__ . '/app/config/autoload.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');

if (empty($url)) {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'dashboard');
    } else {
        header('Location: ' . BASE_URL . 'auth/login');
    }
    exit();
}

function loadController($controllerName)
{
    require_once __DIR__ . '/app/config/constants.php';

    $controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return true;
    }
    return false;
}

$urlParts = explode('/', $url);
$controllerPart = $urlParts[0] ?? '';
$methodPart = $urlParts[1] ?? '';

switch ($controllerPart) {
    case 'auth':
        loadController('AuthController');
        $controller = new AuthController();

        switch ($methodPart) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->processLogin();
                } else {
                    $controller->login();
                }
                break;

            case 'processLogin':
                $controller->processLogin();
                break;

            case 'logout':
                $controller->logout();
                break;

            default:
                $controller->login();
                break;
        }
        break;

    case 'dashboard':
        loadController('DashboardController');
        $session = new Session();

        if (!$session->isLoggedIn()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }

        $controller = new DashboardController();
        $controller->index();
        break;

    case 'login':
        header('Location: ' . BASE_URL . 'auth/login');
        exit();
        break;

    default:
        $controllerName = ucfirst($controllerPart) . 'Controller';
        if (loadController($controllerName)) {
            $controller = new $controllerName();
            if ($methodPart && method_exists($controller, $methodPart)) {
                $controller->$methodPart();
            } elseif (method_exists($controller, 'index')) {
                $controller->index();
            } else {
                show404($url);
            }
        } else {
            show404($url);
        }
        break;
}

function show404($url)
{
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The page '$url' was not found.</p>";
    echo "<a href='" . BASE_URL . "auth/login'>Go to Login</a>";
    exit();
}
