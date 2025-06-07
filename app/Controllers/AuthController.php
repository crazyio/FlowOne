<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

class AuthController {

    protected function renderView($viewName, $layoutName, $data = []) {
        // Ensure critical constants are defined, providing fallbacks if necessary.
        if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
        if (!defined('VIEWS_PATH')) { define('VIEWS_PATH', dirname(__DIR__, 2) . DS . 'app' . DS . 'Views'); }
        if (!defined('BASE_URL_SEGMENT_FOR_LINKS')) {
            $configAppPath = dirname(__DIR__, 2) . DS . 'config' . DS . 'app.php';
            if (file_exists($configAppPath)) {
                $configApp = require $configAppPath;
                define('BASE_URL_SEGMENT_FOR_LINKS', $configApp['base_path_segment_for_links'] ? '/' . trim($configApp['base_path_segment_for_links'], '/') : '');
            } else {
                define('BASE_URL_SEGMENT_FOR_LINKS', '');
            }
        }

        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One';
        $appBaseLinkPath = BASE_URL_SEGMENT_FOR_LINKS;

        ob_start();
        $viewFilePath = VIEWS_PATH . DS . str_replace('.', DS, $viewName) . '.php';
        if (file_exists($viewFilePath)) {
            require $viewFilePath;
        } else {
            echo "DEBUG_RenderView_Error: View file not found at {$viewFilePath}<br>";
        }
        $content = ob_get_clean();

        $layoutFilePath = VIEWS_PATH . DS . 'layouts' . DS . $layoutName . '.php';
        if (file_exists($layoutFilePath)) {
            require $layoutFilePath;
        } else {
            echo "DEBUG_RenderView_Error: Layout file not found at {$layoutFilePath}<br>";
        }
    }

    public function showLoginForm() {
        $errorMessage = Session::getFlash('error');
        $successMessage = Session::getFlash('success');

        $this->renderView('auth.login', 'guest', [
            'pageTitle' => 'Login - Flow One',
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage
        ]);
    }

    public function login() {
        // Ensure BASE_URL_SEGMENT_FOR_LINKS is defined
        if (!defined('BASE_URL_SEGMENT_FOR_LINKS')) {
            $configAppPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
            if (file_exists($configAppPath)) {
                $configApp = require $configAppPath;
                define('BASE_URL_SEGMENT_FOR_LINKS', $configApp['base_path_segment_for_links'] ? '/' . trim($configApp['base_path_segment_for_links'], '/') : '');
            } else {
                define('BASE_URL_SEGMENT_FOR_LINKS', '');
            }
        }
        $appBaseLinkPath = BASE_URL_SEGMENT_FOR_LINKS;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email and password are required.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $user = User::findByEmail($email);

        if ($user) {
            $db_hash = (string) $user->password;
            if (password_verify($password, $db_hash)) {
                // Set session data for successful login
                Session::set('user_id', $user->id);
                Session::set('user_name', $user->name);
                Session::set('user_email', $user->email);
                Session::set('user_role_id', $user->role_id);
                
                // Load user's language preference
                $userLanguage = $user->language ?? 'en';
                Session::set('user_language', $userLanguage);
                
                // Regenerate session ID for security
                Session::regenerateId();

                header('Location: ' . $appBaseLinkPath . '/dashboard');
                exit;
            }
        }

        Session::flash('error', 'Invalid email or password.');
        header('Location: ' . $appBaseLinkPath . '/login');
        exit;
    }

    public function logout() {
        Session::destroy();
        Session::flash('success', 'You have been logged out successfully.');

        if (!defined('BASE_URL_SEGMENT_FOR_LINKS')) {
            $configAppPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
            if (file_exists($configAppPath)) {
                $configApp = require $configAppPath;
                define('BASE_URL_SEGMENT_FOR_LINKS', $configApp['base_path_segment_for_links'] ? '/' . trim($configApp['base_path_segment_for_links'], '/') : '');
            } else {
                define('BASE_URL_SEGMENT_FOR_LINKS', '');
            }
        }
        $appBaseLinkPath = BASE_URL_SEGMENT_FOR_LINKS;

        header('Location: ' . $appBaseLinkPath . '/login');
        exit;
    }
}
?>