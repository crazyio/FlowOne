<?php
namespace App\Core;

class Router {
    protected $routes = [];
    protected $basePathToIgnore = '';

    public function __construct($basePathToIgnore = '') {
        $this->basePathToIgnore = trim($basePathToIgnore, '/');
    }

    public function addRoute($method, $uri, $handler) {
        $uri = trim($uri, '/');
        $this->routes[strtoupper($method)][$uri] = $handler;
    }

    public function dispatch() {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $fullRequestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        $path_info = $_SERVER['PATH_INFO'] ?? null;
        if ($path_info) {
            $uri = trim($path_info, '/');
        } else {
            $raw_script_dir = dirname($_SERVER['SCRIPT_NAME']);

            if ($raw_script_dir === '/' || $raw_script_dir === '\\') {
                $normalized_script_dir = '';
            } else {
                $normalized_script_dir = trim($raw_script_dir, '/');
            }

            if (!empty($normalized_script_dir) && strpos($fullRequestUri, $normalized_script_dir) === 0) {
                $uri = trim(substr($fullRequestUri, strlen($normalized_script_dir) + (strlen($normalized_script_dir) > 0 ? 1 : 0) ), '/');
            } else if (empty($normalized_script_dir)) {
                $uri = $fullRequestUri;
            } else {
                $uri = $fullRequestUri;
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
                        $controller->$action();
                        return;
                    }
                }
            } elseif (is_callable($handler)) {
                call_user_func($handler);
                return;
            }
        }

        // 404 Not Found
        if (!headers_sent()) {
            http_response_code(404);
        }
        echo "<h1>404 Not Found</h1><p>The page you requested could not be found.</p>";
    }
}
?>