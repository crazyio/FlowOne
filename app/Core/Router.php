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
        // Get the full request URI path (e.g., /admin/login/ or /login/)
        $fullRequestUriPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Determine the script's path relative to the document root (e.g., /admin or empty if at root)
        $scriptDirPath = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDirPath = ($scriptDirPath === '.' || $scriptDirPath === '/' || $scriptDirPath === '\\') ? '' : $scriptDirPath;
        $scriptDirPath = trim($scriptDirPath, '/');

        $uri = $fullRequestUriPath;
        // If the script is in a subdirectory (e.g. /admin) and the request URI starts with that path,
        // we want the URI relative to that script directory.
        if ($scriptDirPath !== '' && strpos($fullRequestUriPath, $scriptDirPath) === 0) {
            $uri = trim(substr($fullRequestUriPath, strlen($scriptDirPath)), '/');
        } elseif ($scriptDirPath === '') {
            // Script is at root, so fullRequestUriPath is already relative to it
            $uri = $fullRequestUriPath;
        } else {
            // This case should ideally not be hit if SCRIPT_NAME and REQUEST_URI are sane
            // Or it means SCRIPT_NAME is not a prefix of REQUEST_URI, which is unusual.
            // For safety, use the full request URI path if logic is unclear.
            // This might happen if .htaccess rewrites heavily in a complex way.
             // $uri = $fullRequestUriPath; // Already set
        }

        // The $this->basePathToIgnore passed to constructor is another layer.
        // If it was provided (e.g. if index.php is at root but still handles a /prefix/ segment itself)
        if ($this->basePathToIgnore !== '' && strpos($uri, $this->basePathToIgnore) === 0) {
            $uri = trim(substr($uri, strlen($this->basePathToIgnore)), '/');
        }


        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            if (is_array($handler) && count($handler) === 2) {
                $controllerClass = $handler[0];
                $action = $handler[1];
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        $controller->$action(); return;
                    } else { throw new \Exception("Method {$action} not found in controller {$controllerClass}"); }
                } else { throw new \Exception("Controller class {$controllerClass} not found"); }
            } elseif (is_callable($handler)) { call_user_func($handler); return; }
        }

        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The page you requested could not be found.</p>";
        echo "<ul>";
        echo "<li>Method: " . htmlspecialchars($method) . "</li>";
        echo "<li>URI for matching: '" . htmlspecialchars($uri) . "'</li>";
        echo "<li>Full Request URI Path: '/" . htmlspecialchars($fullRequestUriPath) . "'</li>";
        echo "<li>Script Directory Path: '" . htmlspecialchars($scriptDirPath) . "'</li>";
        echo "<li>Base Path To Ignore (Router constructor): '" . htmlspecialchars($this->basePathToIgnore) . "'</li>";
        echo "<li>Routes available for method {$method}: <pre>" . print_r(array_keys($this->routes[$method] ?? []), true) . "</pre></li>";
        echo "</ul>";
    }
}
?>
