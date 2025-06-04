<?php
namespace App\Controllers;

use App\Core\Session;
// Assuming VIEWS_PATH, DS, BASE_URL_SEGMENT_FOR_LINKS are defined globally or accessible.
// Note: In previous steps, BASE_URL_SEGMENT was used, let's ensure consistency or use the correct one.
// The index.php defines BASE_URL_SEGMENT_FOR_LINKS.

class DashboardController {

    protected function renderView($viewName, $layoutName, $data = []) {
        // Session::start(); // Session is started globally in index.php
        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One Dashboard';

        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';
        // BASE_URL_SEGMENT_FOR_LINKS should already have a leading slash or be empty.

        ob_start();
        // Ensure VIEWS_PATH and DS are defined and accessible
        if (!defined('VIEWS_PATH') || !defined('DS')) {
            // Fallback or error if critical constants are missing
            ob_end_clean();
            echo "Error: Critical path constants (VIEWS_PATH or DS) not defined.";
            return;
        }
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

    public function index() {
        // Session::start(); // Session is started globally in index.php
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';

        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to view the dashboard.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $userName = Session::get('user_name', 'User');
        $userRoleId = Session::get('user_role_id');

        if ($userRoleId == 3) {
            // For client users (role_id 3)
            $this->renderView('dashboard.client_index', 'client', [
                'pageTitle' => 'Client Dashboard',
                'userName' => $userName,
                'userRoleId' => $userRoleId
            ]);
        } else {
            // For other users
            $this->renderView('dashboard.index', 'app', [
                'pageTitle' => 'Dashboard',
                'userName' => $userName,
                'userRoleId' => $userRoleId
            ]);
        }
    }
}
?>
