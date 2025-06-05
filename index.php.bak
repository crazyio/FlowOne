<?php
// Flow One Back Office - Entry Point (Project Root)

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__); // Project root is where this index.php lives
define('APP_PATH', ROOT_PATH . DS . 'app');
define('CONFIG_PATH', ROOT_PATH . DS . 'config');
define('VIEWS_PATH', APP_PATH . DS . 'Views');
// Note: PUBLIC_PATH is no longer needed as assets are at root.

// Basic Autoloader (PSR-4 style for App namespace)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // Not an App class
    }
    $relative_class = substr($class, $len);
    // The ROOT_PATH is already the project root, so app/Core... is correct
    $file = ROOT_PATH . DS . 'app' . DS . str_replace('\\', DS, $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load Configuration
$configApp = require CONFIG_PATH . DS . 'app.php';
// $db_config = require CONFIG_PATH . DS . 'database.php';

use App\Core\Session; // Added for Session management

// Start session
Session::start(); // Initialize session handling

// Define Base URL and Application Base Path
// If this index.php is at the root of what /admin/ serves,
// then APP_BASE_PATH for internal routing logic is effectively empty.
// The web server handles mapping /admin/ to this directory.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost'; // Default host

// This is the segment that might be part of the URL if the app isn't at the domain root (e.g., /admin)
// It's used for generating correct asset links.
// The router itself will operate relative to where index.php is.
$base_url_segment_for_links = trim($configApp['base_path_segment_for_links'] ?? '', '/');
define('BASE_URL_SEGMENT_FOR_LINKS', $base_url_segment_for_links ? '/' . $base_url_segment_for_links : '');

// The router operates relative to this index.php, so its internal base path to ignore is empty.
define('APP_BASE_PATH_FOR_ROUTER', '');


// use App\Core\Session; // Already added above
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController; // Added for Dashboard
// Removed: if (session_status() == PHP_SESSION_NONE) { session_start(); } - Handled by Session::start()

// Router's basePathToIgnore is empty because we assume .htaccess (or server config)
// will make URIs relative to this index.php if it's in a subdirectory like /admin/
$router = new Router(APP_BASE_PATH_FOR_ROUTER);

$router->addRoute('GET', '/login', [AuthController::class, 'showLoginForm']);
$router->addRoute('POST', '/login', [AuthController::class, 'login']);
$router->addRoute('GET', '/logout', [AuthController::class, 'logout']); // Logout route
$router->addRoute('GET', '/', [AuthController::class, 'showLoginForm']); // Default to login
$router->addRoute('GET', '/dashboard', [DashboardController::class, 'index']); // Dashboard route

// The block for simulating $_SERVER variables has been removed.
// The application will now rely on the actual $_SERVER variables provided by the web server
// (or the command line environment if run via CLI, though that's not its primary execution mode).

echo "<fieldset style='border:2px solid red; padding:10px; margin:10px;'>";
echo "<legend>DEBUG: index.php (Before Router Dispatch) - SERVER variables are REAL</legend>";
echo "REQUEST_METHOD: " . htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'NOT SET') . "<br>";
echo "REQUEST_URI: " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "<br>";
echo "SCRIPT_NAME: " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "<br>";
echo "PATH_INFO: " . htmlspecialchars($_SERVER['PATH_INFO'] ?? 'NOT SET') . "<br>";
echo "QUERY_STRING: " . htmlspecialchars($_SERVER['QUERY_STRING'] ?? 'NOT SET') . "<br>";
// Note: BASE_URL_SEGMENT was used in AuthController debug, index.php defines BASE_URL_SEGMENT_FOR_LINKS
echo "BASE_URL_SEGMENT_FOR_LINKS (defined in index.php): " . htmlspecialchars(defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : 'NOT DEFINED') . "<br>";
echo "APP_BASE_PATH_FOR_ROUTER (passed to Router constructor): " . htmlspecialchars(defined('APP_BASE_PATH_FOR_ROUTER') ? APP_BASE_PATH_FOR_ROUTER : 'NOT DEFINED') . "<br>";

// Logic to calculate URI for router (mirroring Router's potential logic for clarity here)
$fullRequestUriForDebug = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$scriptDirForDebug = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptDirForDebug = ($scriptDirForDebug == '/' || $scriptDirForDebug == '\\') ? '' : $scriptDirForDebug;
$scriptDirForDebug = trim($scriptDirForDebug, '/'); // Trim here for accurate comparison / substr

$derivedUriForDebug = $fullRequestUriForDebug;
// Ensure $scriptDirForDebug is not empty before attempting to see if $fullRequestUriForDebug starts with it
if (!empty($scriptDirForDebug) && strpos($fullRequestUriForDebug, $scriptDirForDebug) === 0) {
    $derivedUriForDebug = trim(substr($fullRequestUriForDebug, strlen($scriptDirForDebug)), '/');
} elseif (empty($scriptDirForDebug)) {
    // If script is at root, full request URI is what we use (already trimmed)
    $derivedUriForDebug = $fullRequestUriForDebug;
}
 // else: SCRIPT_NAME is not a prefix of REQUEST_URI, this case should be rare with typical server configs.

 echo "Full Request URI (trimmed): /" . htmlspecialchars($fullRequestUriForDebug) . "<br>";
 echo "Script Directory (dirname SCRIPT_NAME, normalized): /" . htmlspecialchars($scriptDirForDebug) . "<br>";
 echo "Derived URI (for router matching, based on SCRIPT_NAME logic): '" . htmlspecialchars($derivedUriForDebug) . "'<br>";
 echo "PATH_INFO (if available, router might prefer): '" . htmlspecialchars(trim($_SERVER['PATH_INFO'] ?? '', '/')) . "'<br>";
echo "</fieldset>";

try {
    $router->dispatch();
} catch (\Exception $e) {
    error_log($e->getMessage());
    echo "An error occurred.";
    if ($configApp['debug']) {
        echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    }
}
?>
