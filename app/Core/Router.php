<?php
namespace App\Core;

class Router {
    protected $routes = [];
    protected $basePathSegment = '';

    public function __construct($basePathSegment = '') {
        $this->basePathSegment = trim($basePathSegment, '/');
    }

    public function addRoute($method, $uri, $handler) {
        $uri = trim($uri, '/');
        // Special case for root: if $uri is empty after trimming, ensure it's stored as empty string
        // This helps match the segment root (e.g. /admin/) which becomes an empty $uri after stripping segment
        $this->routes[strtoupper($method)][$uri] = $handler;
    }

    public function dispatch() {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        $uri = $requestUri;
        // Strip the base path segment from the request URI if it exists and matches
        if ($this->basePathSegment && strpos($requestUri, $this->basePathSegment) === 0) {
            $uri = trim(substr($requestUri, strlen($this->basePathSegment)), '/');
        }

        // If after stripping the base path, the URI is empty, it means we are at the root of the segment
        // For example, /admin/ resolves to an empty $uri here.

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
                    } else {
                        throw new \Exception("Method {$action} not found in controller {$controllerClass}");
                    }
                } else {
                    throw new \Exception("Controller class {$controllerClass} not found");
                }
            } elseif (is_callable($handler)) {
                call_user_func($handler);
                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The page you requested could not be found.</p>";
        echo "<p>Request URI (raw): " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
        echo "<p>Request URI (parsed path): " . htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) . "</p>";
        echo "<p>Processed URI (after segment strip): " . htmlspecialchars($uri) . "</p>";
        echo "<p>Base Path Segment: " . htmlspecialchars($this->basePathSegment) . "</p>";
        echo "<p>Looking for Method: " . htmlspecialchars($method) . " and URI: '" . htmlspecialchars($uri) . "' in routes.</p>";
        // For debugging:
        // echo "<pre>Routes available: ";
        // print_r($this->routes);
        // echo "</pre>";
    }
}
?>
