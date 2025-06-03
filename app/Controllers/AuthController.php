<?php
namespace App\Controllers;

class AuthController {
    protected function renderView($viewName, $layoutName, $data = []) {
        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One Login';

        // This constant should be defined in index.php, representing the web path segment (e.g. /admin or empty)
        // It's retrieved from config 'base_path_segment_for_links'
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';
        // Ensure it's an absolute path if not empty, or just empty string
        // $appBaseLinkPath = $appBaseLinkPath ? '/' . trim($appBaseLinkPath, '/') : '';
        // BASE_URL_SEGMENT_FOR_LINKS should already have leading slash or be empty from index.php

        ob_start();
        $viewFilePath = VIEWS_PATH . DS . str_replace('.', DS, $viewName) . '.php';
        if (file_exists($viewFilePath)) {
            require $viewFilePath;
        } else {
            ob_end_clean(); // Clean buffer before echoing error
            echo "Error: View file not found at {$viewFilePath}";
            return;
        }
        $content = ob_get_clean();

        $layoutFilePath = VIEWS_PATH . DS . 'layouts' . DS . $layoutName . '.php';
        if (file_exists($layoutFilePath)) {
            require $layoutFilePath; // $appBaseLinkPath will be available to the layout
        } else {
            echo "Error: Layout file not found at {$layoutFilePath}";
        }
    }

    public function showLoginForm() {
        $this->renderView('auth.login', 'guest', ['pageTitle' => 'Login - Flow One']);
    }
    public function login() {
        echo "Login processing... (to be implemented)";
    }
}
?>
