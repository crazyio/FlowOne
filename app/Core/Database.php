<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $conn;

    private $host = 'localhost'; // Get from config
    private $db_name = 'flow_one'; // Get from config
    private $username = 'root'; // Get from config
    private $password = ''; // Get from config

    private function __construct() {
        // DSN (Data Source Name)
        // $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;
        // try {
        //     $this->conn = new PDO($dsn, $this->username, $this->password);
        //     $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //     $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // } catch (PDOException $e) {
        //     echo 'Connection Error: ' . $e->getMessage(); // Handle more gracefully
        // }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        // return $this->conn;
        echo "Database connection (to be implemented).<br>";
        return null;
    }
}
?>
