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
}
