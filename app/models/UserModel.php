<?php
if (!defined('MAX_LOGIN_ATTEMPTS')) {
    define('MAX_LOGIN_ATTEMPTS', 5);
}
if (!defined('LOGIN_TIMEOUT')) {
    define('LOGIN_TIMEOUT', 900); // 15 minutes
}
class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function authenticate($username, $password)
    {
        if ($this->isAccountLocked($username)) {
            return [
                'success' => false,
                'message' => 'Account temporarily locked. Please try again later.'
            ];
        }

        $sql = "SELECT * FROM users WHERE username = :username AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->recordFailedAttempt($username);
            return [
                'success' => false,
                'message' => 'Invalid username or password'
            ];
        }


        if (password_verify($password, $user['password'])) {
            $this->resetLoginAttempts($username);


            $this->updateLastLogin($user['id']);

            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];
        } else {
            $this->recordFailedAttempt($username);

            $attemptsLeft = MAX_LOGIN_ATTEMPTS - ($user['login_attempts'] + 1);

            return [
                'success' => false,
                'message' => 'Invalid username or password' .
                    ($attemptsLeft > 0 ? " ($attemptsLeft attempts remaining)" : '')
            ];
        }
    }

    private function isAccountLocked($username)
    {
        $sql = "SELECT login_attempts, last_login_attempt FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user || !$user['last_login_attempt']) {
            return false;
        }

        if ($user['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $lastAttempt = strtotime($user['last_login_attempt']);
            $currentTime = time();

            if ($currentTime - $lastAttempt < LOGIN_TIMEOUT) {
                return true;
            } else {
                $this->resetLoginAttempts($username);
                return false;
            }
        }

        return false;
    }


    private function recordFailedAttempt($username)
    {
        $sql = "UPDATE users SET 
                login_attempts = login_attempts + 1,
                last_login_attempt = NOW()
                WHERE username = :username";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
    }


    private function resetLoginAttempts($username)
    {
        $sql = "UPDATE users SET 
                login_attempts = 0,
                last_login_attempt = NULL
                WHERE username = :username";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
    }

    public function updateLastLogin($userId)
    {
        $sql = "UPDATE users SET updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }


    public function getUserById($id)
    {
        $sql = "SELECT id, username, full_name, email, role FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function createUser($data)
    {
        $sql = "INSERT INTO users (username, password, full_name, email, role) 
                VALUES (:username, :password, :full_name, :email, :role)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}
