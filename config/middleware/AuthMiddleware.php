<?php
/**
 * Authentication Middleware
 * Check if user is logged in
 */

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated()
    {
        $auth = new Auth();
        
        if (!$auth->isLoggedIn()) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(401);
                die(json_encode(['error' => 'Unauthorized']));
            }
            redirect('login');
        }

        return true;
    }

    /**
     * Check if user has permission
     */
    public static function hasPermission($permissionName)
    {
        $auth = new Auth();
        
        if (!$auth->hasPermission($permissionName)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(403);
                die(json_encode(['error' => 'Permission denied']));
            }
            
            http_response_code(403);
            die('Access Denied');
        }

        return true;
    }

    /**
     * Check if user has any permission
     */
    public static function hasAnyPermission(...$permissions)
    {
        $auth = new Auth();
        
        if (!$auth->hasAnyPermission(...$permissions)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(403);
                die(json_encode(['error' => 'Permission denied']));
            }
            
            http_response_code(403);
            die('Access Denied');
        }

        return true;
    }
}
