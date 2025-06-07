<?php
// Flow One Back Office - Entry Point (Project Root)

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__); // Project root is where this index.php lives
define('APP_PATH', ROOT_PATH . DS . 'app');
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
$configApp = require CONFIG_PATH . DS . 'app.php';

use App\Core\Session;

// Start session
Session::start();

// Define Base URL and Application Base Path
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$base_url_segment_for_links = trim($configApp['base_path_segment_for_links'] ?? '', '/');
define('BASE_URL_SEGMENT_FOR_LINKS', $base_url_segment_for_links ? '/' . $base_url_segment_for_links : '');

define('APP_BASE_PATH_FOR_ROUTER', '');

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

$router = new Router(APP_BASE_PATH_FOR_ROUTER);

$router->addRoute('GET', '/login', [AuthController::class, 'showLoginForm']);
$router->addRoute('POST', '/login', [AuthController::class, 'login']);
$router->addRoute('GET', '/logout', [AuthController::class, 'logout']);
$router->addRoute('GET', '/', [AuthController::class, 'showLoginForm']);

$router->addRoute('GET', '/dashboard', function() {
    Session::start();
    $userRoleId = Session::get('user_role_id');
    
    if ($userRoleId == 3) {
        // Role 3 = Manager
        $controller = new \App\Controllers\ManagerDashboardController();
        $controller->index();
    } else {
        // Role 1 = Admin, Role 2 = Team Manager
        $controller = new \App\Controllers\DashboardController();
        $controller->index();
    }
});

// Manager Workspace Specific Routes (Role 3)
$router->addRoute('GET', '/manager/clients', [\App\Controllers\ManagerClientController::class, 'index']);
$router->addRoute('GET', '/manager/clients/new', [\App\Controllers\ManagerClientController::class, 'create']);
$router->addRoute('POST', '/manager/clients', [\App\Controllers\ManagerClientController::class, 'store']);
$router->addRoute('GET', '/manager/tasks', [\App\Controllers\ManagerTaskController::class, 'index']);
$router->addRoute('GET', '/manager/tasks/new', [\App\Controllers\ManagerTaskController::class, 'create']);
$router->addRoute('POST', '/manager/tasks', [\App\Controllers\ManagerTaskController::class, 'store']);
$router->addRoute('POST', '/manager/update-task-status', [\App\Controllers\ManagerDashboardController::class, 'updateTaskStatus']);
$router->addRoute('GET', '/manager/services', [\App\Controllers\ManagerServiceController::class, 'index']);
$router->addRoute('GET', '/manager/documents', [\App\Controllers\ManagerDocumentController::class, 'index']);
$router->addRoute('GET', '/manager/reports', [\App\Controllers\ManagerReportController::class, 'index']);
$router->addRoute('GET', '/manager/settings', [\App\Controllers\ManagerSettingsController::class, 'index']);

// If you have actual client users (not managers), you might want to add a role 4 for them
// and create separate client routes. For now, commenting out the original client routes:
// $router->addRoute('GET', '/client/settings', [\App\Controllers\ClientSettingsController::class, 'index']);
// ... etc

try {
    $router->dispatch();
}
catch (\Exception $e) {
    error_log($e->getMessage());
    echo "An error occurred.";
    if ($configApp['debug']) {
        echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    }
}
?>