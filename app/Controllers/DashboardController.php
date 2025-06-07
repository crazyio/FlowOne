<?php

namespace App\Controllers;

use App\Core\Session;

class DashboardController extends BaseController
{
    protected function renderView($viewName, $layoutName, $data = []) {
        if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
        if (!defined('VIEWS_PATH')) { define('VIEWS_PATH', dirname(__DIR__, 2) . DS . 'app' . DS . 'Views'); }

        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';

        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One';

        ob_start();
        $viewFilePath = VIEWS_PATH . DS . str_replace('.', DS, $viewName) . '.php';
        if (file_exists($viewFilePath)) {
            require $viewFilePath;
        } else {
            echo "Error: View file not found at {$viewFilePath}. Please create this file.";
        }
        $content = ob_get_clean();

        $layoutFilePath = VIEWS_PATH . DS . 'layouts' . DS . $layoutName . '.php';
        if (file_exists($layoutFilePath)) {
            require $layoutFilePath;
        } else {
            echo "Error: Layout file not found at {$layoutFilePath}.";
        }
    }

    public function index() {
        Session::start();
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';

        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to view the dashboard.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $userName = Session::get('user_name', 'User');
        $userRoleId = Session::get('user_role_id');

        if ($userRoleId == 3) {
            // For manager users (role_id 3) - redirect to manager dashboard
            $controller = new \App\Controllers\ManagerDashboardController();
            $controller->index();
        } else {
            // For other users (Admin, Team Manager)
            $this->renderView('dashboard.index', 'app', [
                'pageTitle' => 'Dashboard',
                'userName' => $userName,
                'userRoleId' => $userRoleId
            ]);
        }
    }
}