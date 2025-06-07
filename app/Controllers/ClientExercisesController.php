<?php

namespace App\Controllers;

use App\Core\Session;

class ClientExercisesController {

    protected function renderView($viewName, $layoutName, $data = []) {
        if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
        if (!defined('VIEWS_PATH')) { define('VIEWS_PATH', dirname(__DIR__, 2) . DS . 'app' . DS . 'Views'); }

        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';
        // Fallback for BASE_URL_SEGMENT constant if BASE_URL_SEGMENT_FOR_LINKS is not defined or empty
        if (empty($appBaseLinkPath) && defined('BASE_URL_SEGMENT')) {
             $appBaseLinkPath = (BASE_URL_SEGMENT === '/' || BASE_URL_SEGMENT === '') ? '' : '/' . trim(BASE_URL_SEGMENT, '/');
        }

        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One Client';

        ob_start();
        $viewFilePath = VIEWS_PATH . DS . str_replace('.', DS, $viewName) . '.php';
        if (file_exists($viewFilePath)) {
            require $viewFilePath;
        } else {
            ob_end_clean();
            // It's generally better to throw an exception or log here
            echo "Error: View file not found at {$viewFilePath}. Please create this file.";
            // For robustness, ensure $content is defined even if view is not found
            $content = "View not found: " . htmlspecialchars($viewName);
            // return; // Early return might be problematic if layout still needs to render
        }
        if (!isset($content)) { // Ensure $content is set if require didn't run or produced no output
            $content = ob_get_clean();
        } else {
            ob_get_clean(); // Clean buffer if content was already captured by other means
        }


        $layoutFilePath = VIEWS_PATH . DS . 'layouts' . DS . $layoutName . '.php';
        if (file_exists($layoutFilePath)) {
            require $layoutFilePath;
        } else {
            echo "Error: Layout file not found at {$layoutFilePath}.";
        }
    }

    public function index() {
        Session::start(); // Ensure session is active
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';
        if (empty($appBaseLinkPath) && defined('BASE_URL_SEGMENT')) {
             $appBaseLinkPath = (BASE_URL_SEGMENT === '/' || BASE_URL_SEGMENT === '') ? '' : '/' . trim(BASE_URL_SEGMENT, '/');
        }

        if (!Session::has('user_id') || Session::get('user_role_id') != 3) {
            Session::flash('error', 'You must be logged in as a client to view this page.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $userName = Session::get('user_name', 'User');
        $userRoleId = Session::get('user_role_id');

        $this->renderView('client.exercises', 'client', [
            'pageTitle' => 'Exercises',
            'userName' => $userName,
            'userRoleId' => $userRoleId
        ]);
    }
}
?>

