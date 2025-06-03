<?php
namespace App\Core;

use PDO;
use PDOException;
use Exception; // For general exceptions

class Database {
    private static $instance = null;
    private $conn;

    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    private $driver;

    private function __construct() {
        // Ensure CONFIG_PATH and DS are available or adjust pathing
        if (!defined('CONFIG_PATH') || !defined('DS')) {
            // This is a fallback, ideally CONFIG_PATH is globally defined before this class is used.
            // Adjust if your autoloader/bootstrap sequence is different.
            // Assuming app/Core/Database.php, so config is two levels up then into config/
            $configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
        } else {
            $configPath = CONFIG_PATH . DS . 'database.php';
        }

        if (!file_exists($configPath)) {
            throw new Exception("Database configuration file not found at: " . $configPath);
        }
        $config = require $configPath;

        $this->driver = $config['driver'] ?? 'mysql';
        $this->host = $config['host'] ?? 'localhost';
        $this->db_name = $config['database'] ?? '';
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->charset = $config['charset'] ?? 'utf8mb4';

        if (empty($this->db_name)) {
            // Username could be empty for some drivers/setups, password can often be empty for local mysql.
            // db_name is critical.
            throw new Exception("Database name (database) is not configured in config/database.php.");
        }

        $dsn = $this->driver . ':host=' . $this->host . ';dbname=' . $this->db_name . ';charset=' . $this->charset;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Or PDO::FETCH_ASSOC
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // In a real app, log this error and show a user-friendly message
            // For now, rethrow the exception to be caught by a global error handler if any
            throw new PDOException("Database Connection Error: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Convenience method to prevent cloning of the instance
    private function __clone() { }

    // Convenience method to prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton.");
    }
}
?>
