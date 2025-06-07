<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\Database;
use PDO;

class ClientDashboardController {

    protected function renderView($viewName, $layoutName, $data = []) {
        if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
        if (!defined('VIEWS_PATH')) { define('VIEWS_PATH', dirname(__DIR__, 2) . DS . 'app' . DS . 'Views'); }

        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';
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
            Session::flash('error', 'You must be logged in as a client to view this page.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $userId = Session::get('user_id');
        $userName = Session::get('user_name', 'User');
        $userRoleId = Session::get('user_role_id');

        // Get client-specific data
        $clientData = $this->getClientData($userId);
        $taskStats = $this->getTaskStats($userId);
        $recentTasks = $this->getRecentTasks($userId);
        $notifications = $this->getRecentNotifications($userId);

        $this->renderView('dashboard.client_index', 'client', [
            'pageTitle' => 'Client Dashboard',
            'userName' => $userName,
            'userRoleId' => $userRoleId,
            'clientData' => $clientData,
            'taskStats' => $taskStats,
            'recentTasks' => $recentTasks,
            'notifications' => $notifications
        ]);
    }

    private function getClientData($userId) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get client information (assuming the user is also a client record)
            $stmt = $db->prepare("
                SELECT c.*, u.name as user_name, u.email as user_email
                FROM clients c 
                LEFT JOIN users u ON c.client_email = u.email 
                WHERE u.id = :user_id 
                LIMIT 1
            ");
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            error_log("Error getting client data: " . $e->getMessage());
            return null;
        }
    }

    private function getTaskStats($userId) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Fixed: Specify table aliases for all columns to avoid ambiguity
            $stmt = $db->prepare("
                SELECT 
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN t.status = 'To Do' THEN 1 ELSE 0 END) as todo_tasks,
                    SUM(CASE WHEN t.status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                    SUM(CASE WHEN t.status = 'Pending Client Input' THEN 1 ELSE 0 END) as pending_input_tasks,
                    SUM(CASE WHEN t.status = 'Done' THEN 1 ELSE 0 END) as completed_tasks,
                    SUM(CASE WHEN t.due_date < CURDATE() AND t.status NOT IN ('Done', 'Cancelled') THEN 1 ELSE 0 END) as overdue_tasks
                FROM tasks t
                JOIN clients c ON t.client_id = c.id
                JOIN users u ON c.client_email = u.email
                WHERE u.id = :user_id
            ");
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            error_log("Error getting task stats: " . $e->getMessage());
            return [
                'total_tasks' => 0,
                'todo_tasks' => 0,
                'in_progress_tasks' => 0,
                'pending_input_tasks' => 0,
                'completed_tasks' => 0,
                'overdue_tasks' => 0
            ];
        }
    }

    private function getRecentTasks($userId, $limit = 5) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Fixed: Specify table aliases for all columns
            $stmt = $db->prepare("
                SELECT t.id, t.task_title, t.task_description, t.status as task_status, 
                       t.priority, t.due_date, t.created_at, t.updated_at,
                       s.service_name, 
                       u_assigned.name as assigned_to_name,
                       DATEDIFF(t.due_date, CURDATE()) as days_until_due
                FROM tasks t
                JOIN clients c ON t.client_id = c.id
                JOIN users u ON c.client_email = u.email
                LEFT JOIN services s ON t.service_id = s.id
                LEFT JOIN users u_assigned ON t.assigned_to_user_id = u_assigned.id
                WHERE u.id = :user_id
                ORDER BY t.created_at DESC
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

    public function getTasks() {
        Session::start();
        
        if (!Session::has('user_id') || Session::get('user_role_id') != 3) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = Session::get('user_id');
        $tasks = $this->getRecentTasks($userId, 50); // Get more tasks for the tasks page
        
        header('Content-Type: application/json');
        echo json_encode(['tasks' => $tasks]);
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

        // Clients can only update to "Pending Client Input" status
        $allowedStatuses = ['Pending Client Input'];
        if (!in_array($status, $allowedStatuses)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid status for client']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            
            // Verify the task belongs to this client
            $stmt = $db->prepare("
                SELECT t.id 
                FROM tasks t
                JOIN clients c ON t.client_id = c.id
                JOIN users u ON c.client_email = u.email
                WHERE t.id = :task_id AND u.id = :user_id
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
                SET status = :status, updated_at = CURRENT_TIMESTAMP 
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