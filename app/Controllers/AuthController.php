<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

class AuthController {

    protected function renderView($viewName, $layoutName, $data = []) {
        // Ensure critical constants are defined, providing fallbacks if necessary.
        // These should ideally be defined once in index.php.
        if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
        if (!defined('VIEWS_PATH')) { define('VIEWS_PATH', dirname(__DIR__, 2) . DS . 'app' . DS . 'Views'); }
        if (!defined('BASE_URL_SEGMENT')) {
            // This is a fallback. BASE_URL_SEGMENT should be defined in index.php
            // based on config 'base_path_segment_for_links'.
            // In index.php it's defined as BASE_URL_SEGMENT_FOR_LINKS. For consistency, this should align.
            // For this specific Turn 51 code, we use BASE_URL_SEGMENT as per its own definition.
            $configAppPath = dirname(__DIR__, 2) . DS . 'config' . DS . 'app.php';
            if (file_exists($configAppPath)) {
                $configApp = require $configAppPath;
                // Assuming the config key is 'base_path_segment_for_links' as per recent changes
                define('BASE_URL_SEGMENT', $configApp['base_path_segment_for_links'] ?? '');
            } else {
                define('BASE_URL_SEGMENT', ''); // Default to empty if config not found
            }
        }

        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One';
        $appBaseLinkPath = (BASE_URL_SEGMENT === '/' || BASE_URL_SEGMENT === '') ? '' : '/' . trim(BASE_URL_SEGMENT, '/');

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
        // Fallback for BASE_URL_SEGMENT if not defined by renderView or globally (e.g. index.php)
        if (!defined('BASE_URL_SEGMENT')) {
            $configAppPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
            if (file_exists($configAppPath)) {
                 $configApp = require $configAppPath;
                 define('BASE_URL_SEGMENT', $configApp['base_path_segment_for_links'] ?? '');
            } else {
                 define('BASE_URL_SEGMENT', '');
            }
        }
        $appBaseLinkPath = (BASE_URL_SEGMENT === '/' || BASE_URL_SEGMENT === '') ? '' : '/' . trim(BASE_URL_SEGMENT, '/');

        echo "DEBUG_LoginCtrl: Reached AuthController login() method.<br>";

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "DEBUG_LoginCtrl: Not a POST request. Redirecting.<br>";
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }
        echo "DEBUG_LoginCtrl: Is a POST request.<br>";
        echo "DEBUG_LoginCtrl: POST Data: <pre>"; var_dump($_POST); echo "</pre><br>";

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        echo "DEBUG_LoginCtrl: Email from POST: '" . htmlspecialchars($email ?? 'NULL') . "'<br>";
        echo "DEBUG_LoginCtrl: Password from POST: '" . (empty($password) ? 'EMPTY' : 'PROVIDED (not shown)') . "'<br>";

        if (empty($email) || empty($password)) {
            echo "DEBUG_LoginCtrl: Email or password empty. Setting flash and redirecting.<br>";
            Session::flash('error', 'Email and password are required.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }
        echo "DEBUG_LoginCtrl: Email and password provided. Attempting User::findByEmail...<br>";

        $user = User::findByEmail($email);

        if ($user) {
            echo "DEBUG_LoginCtrl: User found by email. User Object: <pre>"; var_dump($user); echo "</pre><br>";
            echo "DEBUG_LoginCtrl: Comparing provided password with stored hash: " . htmlspecialchars($user->password) . "<br>";
            if (password_verify($password, $user->password)) {
                echo "DEBUG_LoginCtrl: Password VERIFIED. Logging in and redirecting to dashboard...<br>";
                Session::regenerateId(true);
                Session::set('user_id', $user->id);
                Session::set('user_role_id', $user->role_id);
                Session::set('user_name', $user->name);
                header('Location: ' . $appBaseLinkPath . '/dashboard');
                exit;
            } else {
                echo "DEBUG_LoginCtrl: Password verification FAILED.<br>";
            }
        } else {
            echo "DEBUG_LoginCtrl: User NOT found by email: '" . htmlspecialchars($email ?? 'NULL') . "'<br>";
        }

        echo "DEBUG_LoginCtrl: Login failed (either user not found or password incorrect). Setting flash and redirecting to login.<br>";
        Session::flash('error', 'Invalid email or password.');
        header('Location: ' . $appBaseLinkPath . '/login');
        exit;
    }

    public function logout() {
        Session::destroy(); // This also calls Session::start() internally
        Session::flash('success', 'You have been logged out successfully.');

        if (!defined('BASE_URL_SEGMENT')) {
             $configAppPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
            if (file_exists($configAppPath)) {
                 $configApp = require $configAppPath;
                 define('BASE_URL_SEGMENT', $configApp['base_path_segment_for_links'] ?? '');
            } else {
                 define('BASE_URL_SEGMENT', '');
            }
        }
        $appBaseLinkPath = (BASE_URL_SEGMENT === '/' || BASE_URL_SEGMENT === '') ? '' : '/' . trim(BASE_URL_SEGMENT, '/');

        header('Location: ' . $appBaseLinkPath . '/login');
        exit;
    }
}
?>
