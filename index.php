<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/config/config.php';

spl_autoload_register(function ($class) {
    $paths = [
        CONFIG_PATH . '/' . $class . '.php',
        MODELS_PATH . '/' . $class . '.php',
        CONTROLLERS_PATH . '/' . $class . '.php',
        APP_ROOT . '/' . $class . '.php',
        HELPERS_PATH . '/' . $class . '.php',
        MIDDLEWARE_PATH . '/' . $class . '.php'
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return true;
        }
    }
    return false;
});

require_once HELPERS_PATH . '/helpers.php';

$router = new Router(BASE_URL);

$router->add('GET', 'login', 'auth', 'login');
$router->add('POST', 'auth/processLogin', 'auth', 'processLogin');
$router->add('GET', 'logout', 'auth', 'logout');
$router->add('GET', 'dashboard', 'dashboard', 'index');
$router->add('GET', '', 'dashboard', 'index');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    $router->dispatch($method, $path);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
