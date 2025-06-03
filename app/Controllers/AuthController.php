<?php
namespace App\Controllers;

// Make sure VIEWS_PATH and DS are available.
// If not already defined globally in index.php or a bootstrap file,
// you might need to define them here or pass them appropriately.
// For this subtask, we assume index.php defines them and they are accessible.

class AuthController {

    protected function renderView($viewName, $layoutName, $data = []) {
        extract($data); // Make variables available to the view and layout
        $pageTitle = $data['pageTitle'] ?? 'Flow One Login';

        // Global constants like APP_BASE_PATH should be available from index.php
        // If not, they need to be passed or made available through a config/registry
        $appBasePath = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';


        // Capture the view content
        ob_start();
        $viewFilePath = VIEWS_PATH . DS . str_replace('.', DS, $viewName) . '.php';
        if (file_exists($viewFilePath)) {
            // Pass APP_BASE_PATH to the view as well, if it might need it directly
            // (though typically it's more for layouts)
            // extract(['APP_BASE_PATH' => $appBasePath]); // Already available if global
            require $viewFilePath;
        } else {
            ob_end_clean(); // Clean buffer on error
            echo "Error: View file not found at {$viewFilePath}";
            return; // Stop further processing
        }
        $content = ob_get_clean();

        // Include the layout
        $layoutFilePath = VIEWS_PATH . DS . 'layouts' . DS . $layoutName . '.php';
        if (file_exists($layoutFilePath)) {
            // $pageTitle, $content, and $appBasePath should be available to the layout
            require $layoutFilePath;
        } else {
            echo "Error: Layout file not found at {$layoutFilePath}";
        }
    }

    public function showLoginForm() {
        $this->renderView('auth.login', 'guest', ['pageTitle' => 'Login - Flow One']);
    }

    public function login() {
        // Placeholder for login logic
        echo "Login processing... (to be implemented)";
        // For now, just echo. A real app would redirect.
        // header('Location: ' . BASE_URL . '/login?status=attempted');
        // exit;
    }
}
?>
