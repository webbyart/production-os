<?php
/**
 * AuthController
 * Handle authentication
 */

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show login page
     */
    public function login()
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $flash = getFlash();
        $this->render('auth.login', ['flash' => $flash]);
    }

    /**
     * Process login
     */
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        $result = $this->auth->login($username, $password, $rememberMe);

        if ($result['success']) {
            $this->json(['success' => true, 'message' => 'Login successful', 'redirect' => baseUrl('dashboard')]);
        } else {
            $this->json(['success' => false, 'message' => $result['message']], 401);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->auth->logout();
        $this->redirect('login');
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }

        $user = $this->getUser();
        $this->render('auth.profile', ['user' => $user]);
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $userId = $this->auth->getUserId();
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $result = $this->userModel->update($userId, [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone
        ]);

        if ($result) {
            logActivity('update', 'Updated profile', 'users', $userId);
            $this->json(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update profile'], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $userId = $this->auth->getUserId();
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $result = $this->auth->changePassword($userId, $oldPassword, $newPassword, $confirmPassword);

        if ($result['success']) {
            logActivity('update', 'Changed password', 'users', $userId);
            $this->json(['success' => true, 'message' => $result['message']]);
        } else {
            $this->json(['success' => false, 'message' => $result['message']], 400);
        }
    }
}
