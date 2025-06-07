<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\Database;
use App\Core\Language;
use PDO;
use Exception;

class ManagerSettingsController {

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
        Language::init();
        
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';

        if (!Session::has('user_id') || Session::get('user_role_id') != 3) {
            Session::flash('error', 'You must be logged in as a manager to view this page.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        $userId = Session::get('user_id');
        $userName = Session::get('user_name', 'User');
        $userRoleId = Session::get('user_role_id');

        // Get user data
        $userData = $this->getUserData($userId);
        $supportedLanguages = Language::getSupportedLanguages();
        $successMessage = Session::getFlash('success');
        $errorMessage = Session::getFlash('error');

        $this->renderView('manager.settings', 'manager', [
            'pageTitle' => Language::get('settings.title'),
            'userName' => $userName,
            'userRoleId' => $userRoleId,
            'userData' => $userData,
            'supportedLanguages' => $supportedLanguages,
            'currentLanguage' => Language::getCurrentLanguage(),
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage
        ]);
    }

    public function update() {
        Session::start();
        Language::init();
        
        $appBaseLinkPath = defined('BASE_URL_SEGMENT_FOR_LINKS') ? BASE_URL_SEGMENT_FOR_LINKS : '';

        if (!Session::has('user_id') || Session::get('user_role_id') != 3) {
            Session::flash('error', 'Unauthorized access.');
            header('Location: ' . $appBaseLinkPath . '/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $appBaseLinkPath . '/manager/settings');
            exit;
        }

        $userId = Session::get('user_id');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $language = trim($_POST['language'] ?? 'en');

        // Validation
        if (empty($name)) {
            Session::flash('error', 'Name is required.');
            header('Location: ' . $appBaseLinkPath . '/manager/settings');
            exit;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Valid email is required.');
            header('Location: ' . $appBaseLinkPath . '/manager/settings');
            exit;
        }

        if (!Language::isSupported($language)) {
            Session::flash('error', 'Invalid language selected.');
            header('Location: ' . $appBaseLinkPath . '/manager/settings');
            exit;
        }

        // Check if email is already taken by another user
        if (!$this->isEmailAvailable($email, $userId)) {
            Session::flash('error', 'Email is already taken by another user.');
            header('Location: ' . $appBaseLinkPath . '/manager/settings');
            exit;
        }

        // Update user data
        if ($this->updateUserData($userId, $name, $email, $phone, $language)) {
            // Update session data
            Session::set('user_name', $name);
            Session::set('user_email', $email);
            
            // Update language in session
            Language::setLanguage($language);
            
            Session::flash('success', Language::get('settings.success'));
        } else {
            Session::flash('error', 'Failed to update settings. Please try again.');
        }

        header('Location: ' . $appBaseLinkPath . '/manager/settings');
        exit;
    }

    private function getUserData($userId) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT name, email, phone, language FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            error_log("Error getting user data: " . $e->getMessage());
            return null;
        }
    }

    private function isEmailAvailable($email, $userId) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
            $stmt->execute(['email' => $email, 'user_id' => $userId]);
            
            return $stmt->fetch() === false;
        }
        catch (Exception $e) {
            error_log("Error checking email availability: " . $e->getMessage());
            return false;
        }
    }

    private function updateUserData($userId, $name, $email, $phone, $language) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                UPDATE users 
                SET name = :name, email = :email, phone = :phone, language = :language, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :user_id
            ");
            
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'language' => $language,
                'user_id' => $userId
            ]);
        }
        catch (Exception $e) {
            error_log("Error updating user data: " . $e->getMessage());
            return false;
        }
    }
}
?>