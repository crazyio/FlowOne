<?php

namespace App\Controllers;

use App\Models\User; // Assuming you have a User model

class DashboardController extends BaseController
{
    public function index()
    {
        // Example: Get user role from session
        $session = session();
        $user_role_id = $session->get('user_role_id');

        if ($user_role_id == 3) {
            // Load client-specific dashboard
            return view('dashboard/client_index', [], ['layout' => 'client']);
        } else {
            // Load default admin/other dashboard
            return view('dashboard/index');
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
