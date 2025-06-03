<?php
// Flow One Back Office - Entry Point

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__)); // This is /flow_one_backoffice
define('APP_PATH', ROOT_PATH . DS . 'app');
define('PUBLIC_PATH', ROOT_PATH . DS . 'public');
define('CONFIG_PATH', ROOT_PATH . DS . 'config');
define('VIEWS_PATH', APP_PATH . DS . 'Views');

// Basic Autoloader (PSR-4 style for App namespace)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // Not an App class
    }
    $relative_class = substr($class, $len);
    $file = ROOT_PATH . DS . 'app' . DS . str_replace('\\', DS, $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load Configuration
$config = require CONFIG_PATH . DS . 'app.php';
$db_config = require CONFIG_PATH . DS . 'database.php'; // Though not used in this step yet

// Define Base URL and Application Base Path
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost'; // Default to localhost if not set (for CLI or testing)
$base_path_segment = !empty($config['base_path_segment']) ? trim($config['base_path_segment'], '/') : '';

define('BASE_URL', $protocol . $host . ($base_path_segment ? '/' . $base_path_segment : ''));
define('APP_BASE_PATH', $base_path_segment ? '/' . $base_path_segment : '');


// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use App\Core\Router;
use App\Controllers\AuthController;

$router = new Router($base_path_segment);

// Define routes
// The router will internally handle stripping the base_path_segment
$router->addRoute('GET', '/login', [AuthController::class, 'showLoginForm']);
$router->addRoute('POST', '/login', [AuthController::class, 'login']); // Placeholder for actual login logic

// Default route if nothing else matches (e.g., redirect to login)
$router->addRoute('GET', '/', [AuthController::class, 'showLoginForm']);


try {
    // Simulate a request to /admin/login for testing purposes
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = ($base_path_segment ? '/' . $base_path_segment : '') . '/login';

    $router->dispatch();
} catch (\Exception $e) {
    // Basic error handling
    error_log($e->getMessage());
    // You could show a generic error page here
    echo "An error occurred. Please try again later.";
    if ($config['debug']) {
        echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    }
}

?>
