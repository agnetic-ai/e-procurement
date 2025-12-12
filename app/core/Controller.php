<?php
// app/core/Controller.php

class Controller
{
    protected $session;
    private $menus;

    public function __construct()
    {
        $this->session = new Session();
        $this->menus = new MenusModel();

        if (!defined('APP_PATH')) {
            define('APP_PATH', dirname(__DIR__));
        }

        if (!defined('VIEWS_PATH')) {
            define('VIEWS_PATH', APP_PATH . '/views');
        }

        if (!defined('PUBLIC_PATH')) {
            define('PUBLIC_PATH', dirname(APP_PATH) . '/public');
        }

        if (!defined('BASE_URL')) {
            define('BASE_URL', 'http://localhost/e-procurement/');
        }
    }

    protected function view($view, $data = [])
    {
        $data['session'] = $this->session;
        $data['current_page'] = explode('/', $view)[0] ?? 'dashboard';

        if (!isset($data['pageIcon'])) {
            $icons = [
                'dashboard' => 'fa-tachometer-alt',
                'vendor' => 'fa-user-tie',
                'login' => 'fa-sign-in-alt',
            ];
            $data['pageIcon'] = $icons[explode('/', $view)[0]] ?? 'fa-file';
        }

        extract($data);

        $isLoginPage = strpos($view, 'login') !== false;

        if (!$isLoginPage) {
            $this->loadVolerHeader($data);
        } else {
            echo '<!DOCTYPE html><html lang="en">';
        }

        if (!$isLoginPage && $this->session->isLoggedIn()) {
            $this->loadVolerLayout($data);
        }
        include VIEWS_PATH . '/' . $view . '.php';

        if (!$isLoginPage) {
            $this->loadVolerFooter();
        } else {
            echo '</html>';
        }
    }

    private function loadVolerHeader($data = [])
    {
        extract($data);
        include VIEWS_PATH . '/layouts/header.php';
    }

    private function loadVolerLayout($data = [])
    {
        extract($data);
        include VIEWS_PATH . '/layouts/sidebar.php';
    }

    private function loadVolerFooter()
    {
        include VIEWS_PATH . '/layouts/footer.php';
    }
    public function getUserMenus($userRole)
    {
        $data = $this->menus->GetMenus($userRole);

        $formattedMenus = $this->formatMenuHierarchy($data);

        return $formattedMenus;
    }


    private function generateVolerMenus($role)
    {
        $menus = [
            [
                'title' => 'Dashboard',
                'icon' => 'home',
                'url' => 'dashboard',
                'active' => false
            ]
        ];

        if (in_array($role, ['admin', 'procurement'])) {
            $menus[] = [
                'title' => 'Vendor Management',
                'icon' => 'users',
                'url' => 'vendor',
                'active' => false
            ];
        }

        if ($role == 'admin') {
            $menus[] = [
                'title' => 'User Management',
                'icon' => 'user-circle',
                'url' => 'users',
                'active' => false
            ];
            $menus[] = [
                'title' => 'Reports',
                'icon' => 'bar-chart',
                'url' => 'reports',
                'active' => false
            ];
        }

        return $menus;
    }

    protected function getUserRoleForVoler()
    {
        return $this->session->getUserRole() ?? 'guest';
    }

    protected function getVolerMenus()
    {
        $role = $this->getUserRoleForVoler();
        $menu = $this->getUserMenus($role);

        return $this->generateVolerMenus($role);
    }

    protected function GenerateMenus()
    {
        $role = $this->getUserRoleForVoler();
        return $this->getUserMenus($role);
    }

    protected function redirect($url)
    {
        $baseUrl = defined('BASE_URL') ? BASE_URL : 'http://localhost/e-procurement/';
        header('Location: ' . $baseUrl . $url);
        exit();
    }

    protected function checkLogin()
    {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }

    protected function checkPermission($allowedRoles)
    {
        $userRole = $this->session->getUserRole();

        if (!in_array($userRole, $allowedRoles)) {
            $this->redirect('dashboard');
        }
    }

    protected function render($view, $data = [])
    {
        $this->view($view, $data);
    }

    protected function setFlash($type, $message)
    {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][$type] = $message;
    }

    protected function getFlash($type)
    {
        if (isset($_SESSION['flash_messages'][$type])) {
            $message = $_SESSION['flash_messages'][$type];
            unset($_SESSION['flash_messages'][$type]);
            return $message;
        }
        return null;
    }

    private function formatMenuHierarchy($menuData)
    {
        $menuMap = [];
        $rootMenus = [];

        foreach ($menuData as $menu) {
            $menuMap[$menu['id']] = $menu;
            $menuMap[$menu['id']]['children'] = [];
        }

        foreach ($menuData as $menu) {
            if ($menu['parentId'] === null) {
                $rootMenus[] = $menu['id'];
            } else {
                if (isset($menuMap[$menu['parentId']])) {
                    $menuMap[$menu['parentId']]['children'][] = $menu;
                }
            }
        }

        $result = [];
        foreach ($rootMenus as $rootId) {
            $menu = $menuMap[$rootId];
            $formattedMenu = [
                'title' => $menu['title'],
                'icon' => $menu['icon'],
                'url' => $menu['url'],
                'active' => false,
                'permissions' => [
                    'canView' => (bool)$menu['canView'],
                    'canCreate' => (bool)$menu['canCreate'],
                    'canEdit' => (bool)$menu['canEdit'],
                    'canDelete' => (bool)$menu['canDelete']
                ]
            ];

            if (!empty($menu['children'])) {
                $formattedMenu['children'] = array_map(function ($child) {
                    return [
                        'title' => $child['title'],
                        'icon' => $child['icon'],
                        'url' => $child['url'],
                        'active' => false,
                        'permissions' => [
                            'canView' => (bool)$child['canView'],
                            'canCreate' => (bool)$child['canCreate'],
                            'canEdit' => (bool)$child['canEdit'],
                            'canDelete' => (bool)$child['canDelete']
                        ]
                    ];
                }, $menu['children']);
            }

            $result[] = $formattedMenu;
        }

        return $result;
    }
}
