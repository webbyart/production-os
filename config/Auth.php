<?php
/**
 * Authentication Class
 * Handle user login, logout, and session management
 */

class Auth
{
    private $userModel;
    private $user = null;

    public function __construct()
    {
        $this->userModel = new User();
        $this->checkSession();
    }

    /**
     * Login user
     */
    public function login($username, $password, $rememberMe = false)
    {
        $user = $this->userModel->getByUsername($username);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'User account is inactive'];
        }

        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }

        $this->createSession($user, $rememberMe);
        $this->userModel->updateLastLogin($user['user_id']);

        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    }

    /**
     * Create user session
     */
    private function createSession($user, $rememberMe = false)
    {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['csrf_token'] = $this->generateCSRFToken();

        if ($rememberMe) {
            setcookie('remember_token', bin2hex(random_bytes(32)), time() + (86400 * 30), '/');
        }

        $this->user = $user;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check session validity
     */
    private function checkSession()
    {
        if ($this->isLoggedIn()) {
            $loginTime = $_SESSION['login_time'] ?? 0;
            $currentTime = time();
            
            if ($currentTime - $loginTime > SESSION_TIMEOUT) {
                $this->logout();
                return false;
            }

            // Update session time
            $_SESSION['login_time'] = $currentTime;
            $this->user = $this->userModel->getUserWithRole($_SESSION['user_id']);
        }

        return true;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        $this->user = null;
    }

    /**
     * Get current user
     */
    public function getUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->user ?? $this->userModel->getUserWithRole($_SESSION['user_id']);
    }

    /**
     * Get user ID
     */
    public function getUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get user role
     */
    public function getUserRole()
    {
        return $_SESSION['role_id'] ?? null;
    }

    /**
     * Check permission
     */
    public function hasPermission($permissionName)
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userId = $_SESSION['user_id'];
        $permissions = $this->userModel->getUserPermissions($userId);
        
        foreach ($permissions as $permission) {
            if ($permission['permission_name'] === $permissionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check multiple permissions (OR)
     */
    public function hasAnyPermission(...$permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate CSRF token
     */
    public function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public function validateCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Change password
     */
    public function changePassword($userId, $oldPassword, $newPassword, $confirmPassword)
    {
        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }

        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        $user = $this->userModel->getById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!$this->userModel->verifyPassword($oldPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        $hashedPassword = $this->userModel->hashPassword($newPassword);
        $result = $this->userModel->update($userId, [
            'password_hash' => $hashedPassword,
            'password_changed_at' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }

        return ['success' => false, 'message' => 'Failed to change password'];
    }
}
