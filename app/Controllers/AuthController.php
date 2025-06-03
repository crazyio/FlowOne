<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

// Assuming VIEWS_PATH and DS are defined globally (e.g. in index.php)
// and BASE_URL_SEGMENT_FOR_LINKS (renamed to BASE_URL_SEGMENT in prev step) is also defined.

class AuthController {

    protected function renderView($viewName, $layoutName, $data = []) {
        // Session::start(); // Session is started globally in index.php, and methods in Session class also call start()
        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One';

        // Use BASE_URL_SEGMENT_FOR_LINKS as defined in index.php
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';
        // It should already have a leading slash or be empty.

        ob_start();
        $viewFilePath = VIEWS_PATH . DS . str_replace('.', DS, $viewName) . '.php';
        if (file_exists($viewFilePath)) {
            require $viewFilePath;
        } else {
            ob_end_clean();
            echo "Error: View file not found at {$viewFilePath}";
            return;
        }
        $content = ob_get_clean();

        $layoutFilePath = VIEWS_PATH . DS . 'layouts' . DS . $layoutName . '.php';
        if (file_exists($layoutFilePath)) {
            require $layoutFilePath;
        } else {
            echo "Error: Layout file not found at {$layoutFilePath}";
        }
    }

    public function showLoginForm() {
        // Session::start(); // Handled by Session class methods
        $errorMessage = Session::getFlash('error');
        $successMessage = Session::getFlash('success'); // For logout message, etc.

        $this->renderView('auth.login', 'guest', [
            'pageTitle' => 'Login - Flow One',
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage
        ]);
    }

    public function login() {
        // Session::start(); // Handled by Session class methods
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // For non-POST requests, redirect to login page, possibly with an error or just silently.
            // Or, if your router is strict, this might not even be reachable for GET.
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $email = $_POST['email'] ?? null;
        $password_input = $_POST['password'] ?? null; // Renamed to avoid confusion with $user->password

        if (empty($email) || empty($password_input)) {
            Session::flash('error', 'Email and password are required.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $user = User::findByEmail($email);

        if ($user) {
            // Verify hashed password
            if (password_verify($password_input, $user->password)) { // Use password_verify()
                Session::regenerateId(true); // Regenerate session ID for security
                Session::set('user_id', $user->id);
                // Ensure 'role_id' and 'name' are selected by findByEmail if they exist on the $user object
                // User model properties suggest they should be if User::findByEmail returns a full user object
                Session::set('user_role_id', $user->role_id ?? null);
                Session::set('user_name', $user->name ?? 'User');

                // Redirect to a dashboard page (assuming /dashboard route will exist)
                header('Location: ' . $appBaseLinkPath . '/dashboard');
                exit;
            }
        }

        Session::flash('error', 'Invalid email or password.');
        header('Location: ' . $appBaseLinkPath . '/login');
        exit;
    }

    public function logout() {
        // Session::start(); // Handled by Session class methods
        Session::destroy();
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';
        Session::flash('success', 'You have been logged out successfully.');
        header('Location: ' . $appBaseLinkPath . '/login');
        exit;
    }
}
?>
