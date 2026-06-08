<?php
/**
 * Application Router
 */

class Router
{
    private $routes = [];
    private $baseUrl;

    public function __construct($baseUrl = '/')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Register a route
     */
    public function add($method, $path, $controller, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * Match and dispatch route
     */
    public function dispatch($method, $path)
    {
        // Remove base URL from path
        $path = str_replace($this->baseUrl, '', $path);
        $path = trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if ($this->match($route['path'], $path, $params)) {
                $this->call($route['controller'], $route['action'], $params);
                return;
            }
        }

        http_response_code(404);
        die('Route not found: ' . $path);
    }

    /**
     * Match route pattern with path
     */
    private function match($pattern, $path, &$params)
    {
        $pattern = str_replace('/', '\\/', $pattern);
        $pattern = preg_replace('/\{([a-z_]+)\}/', '(?<$1>[0-9a-z_-]+)', $pattern);
        $pattern = '/^' . $pattern . '$/i';

        if (preg_match($pattern, $path, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    /**
     * Call controller action
     */
    private function call($controller, $action, $params)
    {
        $controllerClass = ucfirst($controller) . 'Controller';
        $controllerPath = CONTROLLERS_PATH . '/' . $controllerClass . '.php';

        if (!file_exists($controllerPath)) {
            die('Controller not found: ' . $controllerClass);
        }

        require $controllerPath;

        $ctrl = new $controllerClass();

        if (!method_exists($ctrl, $action)) {
            die('Action not found: ' . $action);
        }

        call_user_func_array([$ctrl, $action], array_values($params));
    }
}
