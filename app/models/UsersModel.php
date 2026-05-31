<?php
class UsersModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetRoles()
    {
        $stmt = $this->db->query("SELECT id AS roleId, role_code AS roleCode, role_name AS roleName FROM roles WHERE is_active = 1 ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetUserList()
    {
        $filter = htmlentities($_POST['filterName'] ?? '');

        $query = "SELECT 
                    u.id AS userId,
                    u.username,
                    u.full_name AS fullName,
                    u.email,
                    r.role_code AS roleCode,
                    r.role_name AS roleName,
                    u.is_active AS isActive,
                    u.login_attempts AS loginAttempts,
                    DATE_FORMAT(u.created_at, '%d-%b-%Y') AS createdAt,
                    DATE_FORMAT(u.last_login_attempt, '%d-%b-%Y %H:%i') AS lastLogin
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id";

        $params = [];

        if (!empty($filter)) {
            $query .= " WHERE (u.username LIKE :f1 OR u.full_name LIKE :f2 OR u.email LIKE :f3)";
            $params[':f1'] = "%$filter%";
            $params[':f2'] = "%$filter%";
            $params[':f3'] = "%$filter%";
        }

        $query .= " ORDER BY u.id ASC";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user list: " . $e->getMessage());
            return [];
        }
    }

    public function GetUserById($id)
    {
        $query = "SELECT 
                    u.id AS userId,
                    u.username,
                    u.full_name AS fullName,
                    u.email,
                    u.role_id AS roleId,
                    r.role_code AS roleCode,
                    r.role_name AS roleName,
                    u.is_active AS isActive,
                    DATE_FORMAT(u.created_at, '%d-%b-%Y') AS createdAt
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            return null;
        }
    }

    public function CreateUser($payload)
    {
        $username = trim($payload['username'] ?? '');
        $password = $payload['password'] ?? '';
        $fullName = trim($payload['fullName'] ?? '');
        $email = trim($payload['email'] ?? '');
        $roleId = intval($payload['roleId'] ?? 0);
        $isActive = intval($payload['isActive'] ?? 1);

        $errors = [];
        if (empty($username)) $errors[] = 'Username wajib diisi';
        if (empty($password)) $errors[] = 'Password wajib diisi';
        if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
        if (empty($fullName)) $errors[] = 'Nama lengkap wajib diisi';
        if (empty($email)) $errors[] = 'Email wajib diisi';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
        if ($roleId <= 0) $errors[] = 'Role wajib dipilih';

        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(', ', $errors)];
        }

        // Check duplicate
        $check = $this->db->prepare("SELECT id FROM users WHERE username = :u OR email = :e");
        $check->execute([':u' => $username, ':e' => $email]);
        if ($check->fetch()) {
            return ['success' => false, 'message' => 'Username atau email sudah digunakan'];
        }

        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, password, full_name, email, role_id, is_active) 
                                        VALUES (:username, :password, :full_name, :email, :role_id, :is_active)");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hash,
                ':full_name' => $fullName,
                ':email' => $email,
                ':role_id' => $roleId,
                ':is_active' => $isActive
            ]);

            return ['success' => true, 'userId' => $this->db->lastInsertId(), 'message' => 'User berhasil ditambahkan'];
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menambahkan user'];
        }
    }

    public function UpdateUser($payload)
    {
        $userId = intval($payload['userId'] ?? 0);
        $fullName = trim($payload['fullName'] ?? '');
        $email = trim($payload['email'] ?? '');
        $roleId = intval($payload['roleId'] ?? 0);
        $isActive = intval($payload['isActive'] ?? 1);
        $newPassword = $payload['newPassword'] ?? '';

        $errors = [];
        if ($userId <= 0) $errors[] = 'User ID tidak valid';
        if (empty($fullName)) $errors[] = 'Nama lengkap wajib diisi';
        if (empty($email)) $errors[] = 'Email wajib diisi';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
        if ($roleId <= 0) $errors[] = 'Role wajib dipilih';

        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(', ', $errors)];
        }

        // Check duplicate email (exclude current user)
        $check = $this->db->prepare("SELECT id FROM users WHERE email = :e AND id != :id");
        $check->execute([':e' => $email, ':id' => $userId]);
        if ($check->fetch()) {
            return ['success' => false, 'message' => 'Email sudah digunakan oleh user lain'];
        }

        try {
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    return ['success' => false, 'message' => 'Password minimal 6 karakter'];
                }
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $this->db->prepare("UPDATE users SET full_name=:full_name, email=:email, role_id=:role_id, is_active=:is_active, password=:password WHERE id=:id");
                $stmt->execute([
                    ':full_name' => $fullName,
                    ':email' => $email,
                    ':role_id' => $roleId,
                    ':is_active' => $isActive,
                    ':password' => $hash,
                    ':id' => $userId
                ]);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET full_name=:full_name, email=:email, role_id=:role_id, is_active=:is_active WHERE id=:id");
                $stmt->execute([
                    ':full_name' => $fullName,
                    ':email' => $email,
                    ':role_id' => $roleId,
                    ':is_active' => $isActive,
                    ':id' => $userId
                ]);
            }

            return ['success' => true, 'message' => 'User berhasil diupdate'];
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal mengupdate user'];
        }
    }

    public function DeleteUser($userId)
    {
        $userId = intval($userId);

        if ($userId <= 0) {
            return ['success' => false, 'message' => 'User ID tidak valid'];
        }

        // Prevent deleting self
        $currentUserId = $_SESSION['user_id'] ?? 0;
        if ($userId == $currentUserId) {
            return ['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri'];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $userId]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'User berhasil dihapus'];
            }
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menghapus user'];
        }
    }

    public function ResetLoginAttempts($userId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE users SET login_attempts = 0, last_login_attempt = NULL WHERE id = :id");
            $stmt->execute([':id' => $userId]);
            return ['success' => true, 'message' => 'Login attempts berhasil direset'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Gagal reset login attempts'];
        }
    }
}
