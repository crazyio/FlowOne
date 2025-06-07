<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\Database;
use PDO;
use Exception;

class ManagerDashboardController {

    protected function renderView($viewName, $layoutName, $data = []) {
        if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
        if (!defined('VIEWS_PATH')) { define('VIEWS_PATH', dirname(__DIR__, 2) . DS . 'app' . DS . 'Views'); }

        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';

        extract($data);
        $pageTitle = $data['pageTitle'] ?? 'Flow One Manager';

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

        if (!Session::has('user_id') || Session::get('user_role_id') != 3) {
            Session::flash('error', 'You must be logged in as a manager to view this page.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $userId = Session::get('user_id');
        $userName = Session::get('user_name', 'User');
        $userRoleId = Session::get('user_role_id');

        // Get manager-specific data
        $managerStats = $this->getManagerStats($userId);
        $assignedClients = $this->getAssignedClients($userId);
        $recentTasks = $this->getRecentTasks($userId);
        $tasksByStatus = $this->getTasksByStatus($userId);
        $notifications = $this->getRecentNotifications($userId);

        $this->renderView('dashboard.manager_index', 'manager', [
            'pageTitle' => 'Manager Dashboard',
            'userName' => $userName,
            'userRoleId' => $userRoleId,
            'managerStats' => $managerStats,
            'assignedClients' => $assignedClients,
            'recentTasks' => $recentTasks,
            'tasksByStatus' => $tasksByStatus,
            'notifications' => $notifications
        ]);
    }

    private function getManagerStats($userId) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Debug: First check if we have any clients at all for this user
            $debugStmt = $db->prepare("SELECT COUNT(*) as count FROM clients WHERE assigned_to_user_id = :user_id");
            $debugStmt->execute(['user_id' => $userId]);
     
            // Get client statistics
            $clientStmt = $db->prepare("
                SELECT 
                    COUNT(*) as total_clients,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_clients,
                    COUNT(CASE WHEN status = 'prospect' THEN 1 END) as prospect_clients,
                    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_clients
                FROM clients 
                WHERE assigned_to_user_id = :user_id
            ");
            $clientStmt->execute(['user_id' => $userId]);
            $clientStats = $clientStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get task statistics
            $taskStmt = $db->prepare("
                SELECT 
                    COUNT(*) as total_tasks,
                    COUNT(CASE WHEN t.status = 'To Do' THEN 1 END) as todo_tasks,
                    COUNT(CASE WHEN t.status = 'In Progress' THEN 1 END) as in_progress_tasks,
                    COUNT(CASE WHEN t.status = 'Pending Client Input' THEN 1 END) as pending_input_tasks,
                    COUNT(CASE WHEN t.status = 'Done' THEN 1 END) as completed_tasks,
                    COUNT(CASE WHEN t.due_date < CURDATE() AND t.status NOT IN ('Done', 'Cancelled') THEN 1 END) as overdue_tasks
                FROM tasks t
                JOIN clients c ON t.client_id = c.id
                WHERE c.assigned_to_user_id = :user_id
            ");
            $taskStmt->execute(['user_id' => $userId]);
            $taskStats = $taskStmt->fetch(PDO::FETCH_ASSOC);
            
            // Merge the results
            $result = array_merge($clientStats, $taskStats);
           
            return $result;
        }
        catch (Exception $e) {
            error_log("Error getting manager stats: " . $e->getMessage());
            return [
                'total_clients' => 0,
                'active_clients' => 0,
                'prospect_clients' => 0,
                'inactive_clients' => 0,
                'total_tasks' => 0,
                'todo_tasks' => 0,
                'in_progress_tasks' => 0,
                'pending_input_tasks' => 0,
                'completed_tasks' => 0,
                'overdue_tasks' => 0
            ];
        }
    }

    private function getAssignedClients($userId, $limit = 10) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT c.*, 
                       COUNT(t.id) as total_tasks,
                       COUNT(CASE WHEN t.status NOT IN ('Done', 'Cancelled') THEN 1 END) as active_tasks,
                       COUNT(CASE WHEN t.due_date < CURDATE() AND t.status NOT IN ('Done', 'Cancelled') THEN 1 END) as overdue_tasks
                FROM clients c
                LEFT JOIN tasks t ON c.id = t.client_id
                WHERE c.assigned_to_user_id = :user_id
                GROUP BY c.id
                ORDER BY c.updated_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            error_log("Error getting assigned clients: " . $e->getMessage());
            return [];
        }
    }

    private function getRecentTasks($userId, $limit = 10) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT t.*, c.client_name, c.company_name,
                       s.service_name,
                       DATEDIFF(t.due_date, CURDATE()) as days_until_due
                FROM tasks t
                JOIN clients c ON t.client_id = c.id
                LEFT JOIN services s ON t.service_id = s.id
                WHERE t.assigned_to_user_id = :user_id OR c.assigned_to_user_id = :user_id
                ORDER BY t.updated_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            error_log("Error getting recent tasks: " . $e->getMessage());
            return [];
        }
    }

    private function getTasksByStatus($userId) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT t.status, COUNT(*) as count
                FROM tasks t
                JOIN clients c ON t.client_id = c.id
                WHERE t.assigned_to_user_id = :user_id OR c.assigned_to_user_id = :user_id
                GROUP BY t.status
                ORDER BY t.status
            ");
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            error_log("Error getting tasks by status: " . $e->getMessage());
            return [];
        }
    }

    private function getRecentNotifications($userId, $limit = 5) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT * FROM notifications 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            error_log("Error getting notifications: " . $e->getMessage());
            return [];
        }
    }

    public function updateTaskStatus() {
        Session::start();
        
        if (!Session::has('user_id') || Session::get('user_role_id') != 3) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $taskId = $_POST['task_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $userId = Session::get('user_id');

        if (!$taskId || !$status) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Missing required parameters']);
            exit;
        }

        // Managers can update to any status except 'Cancelled' (only admins can cancel)
        $allowedStatuses = ['To Do', 'In Progress', 'Pending Client Input', 'Done'];
        if (!in_array($status, $allowedStatuses)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            
            // Verify the task is assigned to this manager or belongs to their client
            $stmt = $db->prepare("
                SELECT t.id 
                FROM tasks t
                JOIN clients c ON t.client_id = c.id
                WHERE t.id = :task_id 
                AND (t.assigned_to_user_id = :user_id OR c.assigned_to_user_id = :user_id)
            ");
            $stmt->execute(['task_id' => $taskId, 'user_id' => $userId]);
            
            if (!$stmt->fetch()) {
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(['error' => 'Task not found or access denied']);
                exit;
            }

            // Update task status
            $stmt = $db->prepare("
                UPDATE tasks 
                SET status = :status, 
                    completed_at = CASE WHEN :status = 'Done' THEN CURRENT_TIMESTAMP ELSE NULL END,
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :task_id
            ");
            $stmt->execute(['status' => $status, 'task_id' => $taskId]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
            
        }
        catch (Exception $e) {
            error_log("Error updating task status: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Failed to update task status']);
        }
    }
}
?>