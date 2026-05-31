<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/UsersModel.php';

class UsersController extends Controller
{
    private $users;

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->users = new UsersModel();
    }

    private function adminOnly()
    {
        $role = $_SESSION['user_role'] ?? '';
        if ($role !== 'admin') {
            ResponseHelper::forbidden('Akses ditolak. Hanya admin yang bisa mengakses.');
            exit;
        }
    }

    public function index()
    {
        $this->adminOnly();

        $data = [
            'title' => 'User Management',
            'subtitle' => 'Kelola user dan role sistem',
            'session' => $this->session,
            'roles' => $this->users->GetRoles()
        ];
        $this->view('users/index', $data);
    }

    public function GetUserList()
    {
        $this->adminOnly();

        try {
            $response = $this->users->GetUserList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            ResponseHelper::serverError($e->getMessage());
        }
    }

    public function GetUserById()
    {
        $this->adminOnly();

        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            ResponseHelper::badRequest('User ID tidak valid');
            return;
        }

        $user = $this->users->GetUserById($id);
        if ($user) {
            ResponseHelper::success($user, 'Success');
        } else {
            ResponseHelper::notFound('User tidak ditemukan');
        }
    }

    public function GetRoles()
    {
        $this->adminOnly();

        try {
            $roles = $this->users->GetRoles();
            ResponseHelper::success($roles, 'Success');
        } catch (Exception $e) {
            ResponseHelper::serverError($e->getMessage());
        }
    }

    public function CreateUser()
    {
        $this->adminOnly();

        header('Content-Type: application/json');
        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $result = $this->users->CreateUser($payload);

            if ($result['success']) {
                ResponseHelper::created(['userId' => $result['userId']], $result['message']);
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            error_log("Error in CreateUser: " . $e->getMessage());
            ResponseHelper::serverError('Terjadi kesalahan saat membuat user');
        }
    }

    public function UpdateUser()
    {
        $this->adminOnly();

        header('Content-Type: application/json');
        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $result = $this->users->UpdateUser($payload);

            if ($result['success']) {
                ResponseHelper::success([], $result['message']);
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            error_log("Error in UpdateUser: " . $e->getMessage());
            ResponseHelper::serverError('Terjadi kesalahan saat mengupdate user');
        }
    }

    public function DeleteUser()
    {
        $this->adminOnly();

        header('Content-Type: application/json');
        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $userId = intval($payload['userId'] ?? 0);
            $result = $this->users->DeleteUser($userId);

            if ($result['success']) {
                ResponseHelper::success([], $result['message']);
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            error_log("Error in DeleteUser: " . $e->getMessage());
            ResponseHelper::serverError('Terjadi kesalahan saat menghapus user');
        }
    }

    public function ResetLoginAttempts()
    {
        $this->adminOnly();

        header('Content-Type: application/json');
        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $userId = intval($payload['userId'] ?? 0);
            $result = $this->users->ResetLoginAttempts($userId);

            if ($result['success']) {
                ResponseHelper::success([], $result['message']);
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            ResponseHelper::serverError('Terjadi kesalahan');
        }
    }
}
