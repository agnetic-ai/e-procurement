<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if ($this->session->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $error = $this->session->getFlash('error');
        $success = $this->session->getFlash('success');

        $data = [
            'pageTitle' => 'Login - eProcurement',
            'error' => $error,
            'success' => $success,
            'session' => $this->session
        ];

        $this->view('auth/login', $data);
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->session->setFlash('error', 'Please enter both username and password');
            $this->redirect('login');
        }

        try {
            $result = $this->userModel->authenticate($username, $password);

            if ($result && isset($result['success']) && $result['success'] === true) {
                $userData = $result['user'];

                $this->session->set('user_id', $userData['id']);
                $this->session->set('username', $userData['username']);
                $this->session->set('full_name', $userData['full_name']);
                $this->session->set('user_role', $userData['role']);
                $this->session->set('email', $userData['email']);
                $this->session->set('last_activity', time());
                $this->session->set('level',  $userData['level']);

                $welcomeMessages = [
                    'admin' => 'Welcome back, Administrator!',
                    'procurement' => 'Welcome back, Procurement Staff!',
                    'vendor' => 'Welcome back, Vendor!'
                ];

                $message = $welcomeMessages[$userData['role']] ?? 'Welcome back!';
                $this->session->setFlash('success', $message);

                $this->redirect('dashboard');
            } else {
                $errorMessage = $result['message'] ?? 'Invalid username or password';
                $this->session->setFlash('error', $errorMessage);
                $this->redirect('login');
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $this->session->setFlash('error', 'System error. Please try again later.');
            $this->redirect('login');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        $this->session->setFlash('success', 'You have been logged out successfully');
        $this->redirect('login');
    }
}
