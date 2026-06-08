<?php
/**
 * CSRF Middleware
 * Protect against Cross-Site Request Forgery attacks
 */

class CSRFMiddleware
{
    /**
     * Validate CSRF token
     */
    public static function validate()
    {
        // Skip validation for GET requests
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        $auth = new Auth();
        
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!$token || !$auth->validateCSRFToken($token)) {
            http_response_code(403);
            die(json_encode(['error' => 'CSRF token validation failed']));
        }

        return true;
    }
}
