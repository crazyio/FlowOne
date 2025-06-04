<?php
namespace App\Core;

class Router {
    protected $routes = [];
    // This is effectively not used if server rewrites handle sending paths relative to index.php
    protected $basePathToIgnore = '';

    public function __construct($basePathToIgnore = '') {
        // This argument is largely vestigial if .htaccess handles making paths relative.
        // If index.php is at /foo/index.php, and .htaccess has RewriteBase /foo/
        // then PHP typically sees paths already stripped of /foo/.
        $this->basePathToIgnore = trim($basePathToIgnore, '/');
    }

    public function addRoute($method, $uri, $handler) {
        $uri = trim($uri, '/');
        $this->routes[strtoupper($method)][$uri] = $handler;
    }

    public function dispatch() {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $fullRequestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // This is the URI calculation logic currently in the Router.
        // It might be refined based on index.php debug output.
        $path_info = $_SERVER['PATH_INFO'] ?? null;
        if ($path_info) {
             $uri = trim($path_info, '/');
        } else {
            // $fullRequestUri is already trimmed of leading/trailing slashes, e.g., 'admin/login'
            $raw_script_dir = dirname($_SERVER['SCRIPT_NAME']); // e.g., '/admin' or '/' if in root

            // Normalize script_dir: remove trailing slash if not root, then remove leading slash
            if ($raw_script_dir === '/' || $raw_script_dir === '\\') {
                $normalized_script_dir = ''; // Represents root, no prefix to strip
            } else {
                $normalized_script_dir = trim($raw_script_dir, '/'); // e.g., 'admin'
            }

            if (!empty($normalized_script_dir) && strpos($fullRequestUri, $normalized_script_dir) === 0) {
                // Strip the normalized_script_dir from the beginning of fullRequestUri
                // Add 1 for the slash if normalized_script_dir is not empty
                $uri = trim(substr($fullRequestUri, strlen($normalized_script_dir) + (strlen($normalized_script_dir) > 0 ? 1 : 0) ), '/');
            } else if (empty($normalized_script_dir)) {
                // If script is in root, fullRequestUri is the uri
                $uri = $fullRequestUri;
            } else {
                // Fallback or if script_dir is not part of fullRequestUri (should not happen with .htaccess RewriteBase)
                $uri = $fullRequestUri; // Or handle as an error/special case
            }
        }

        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];

            if (is_array($handler) && count($handler) === 2) {
                $controllerClass = $handler[0];
                $action = $handler[1];

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        $controller->$action(); // Call the action
                        return;
                    } else {
                        // Consider logging this error instead of echo, or throwing an exception
                        // echo "DEBUG_Router_Error: Method {$action} not found in controller {$controllerClass}<br>";
                    }
                } else {
                    // Consider logging this error
                    // echo "DEBUG_Router_Error: Controller class {$controllerClass} not found<br>";
                }
            } elseif (is_callable($handler)) {
                call_user_func($handler);
                return;
            }
        }

        // Basic 404 if no route matched and executed
        if (!headers_sent()) { // Check if controller action already sent output
             http_response_code(404);
        }
        // You might want to render a dedicated 404 view here instead of echoing HTML directly
        echo "<h1>404 Not Found</h1><p>The page you requested could not be found.</p>";
    }
}
?>
