<?php
/**
 * Base Controller
 */

class BaseController
{
    protected $auth;
    protected $db;

    public function __construct()
    {
        $this->auth = new Auth();
        $this->db = Database::getInstance();
        
        // Check authentication
        AuthMiddleware::isAuthenticated();
    }

    /**
     * Render view
     */
    protected function render($view, $data = [])
    {
        extract($data);
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            die('View not found: ' . $view);
        }

        include $viewPath;
    }

    /**
     * JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect
     */
    protected function redirect($path)
    {
        header('Location: ' . baseUrl($path));
        exit;
    }

    /**
     * Get current user
     */
    protected function getUser()
    {
        return $this->auth->getUser();
    }

    /**
     * Check permission
     */
    protected function hasPermission($permission)
    {
        return $this->auth->hasPermission($permission);
    }
}
