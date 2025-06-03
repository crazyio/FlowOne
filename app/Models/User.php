<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class User {
    protected static $table = 'users';

    // Properties for a User object (optional, but good for type hinting if you instantiate User objects)
    public $id;
    public $name;
    public $email;
    public $password; // This would be the hashed password
    public $role_id;
    public $phone; // Added based on schema
    public $status;
    public $last_login_at;
    public $created_at;
    public $updated_at;

    /**
     * Find a user by their email address.
     *
     * @param string $email The email address to search for.
     * @return object|null The user object if found, or null otherwise.
     */
    public static function findByEmail(string $email): ?object {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            // Check if connection is null (which it shouldn't be if Database class is correct)
            if ($conn === null) {
                error_log("Database connection is null in User::findByEmail.");
                // This might happen if getInstance() or getConnection() failed silently or was misconfigured
                // Or if the Database class hasn't been initialized properly before this call.
                // Consider throwing an exception here if a null connection is a critical failure.
                // throw new Exception("Database connection not available.");
                return null; // Or handle as appropriate
            }

            $stmt = $conn->prepare("SELECT * FROM " . self::$table . " WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_OBJ); // FETCH_OBJ is default from Database class configuration

            return $user ?: null; // Return user object or null

        } catch (PDOException $e) {
            // Log error or handle as appropriate for your application
            error_log("PDOException in User::findByEmail: " . $e->getMessage());
            // Depending on how strict you want to be, you might rethrow or simply return null
            // throw $e; // Or handle more gracefully
            return null;
        } catch (\Exception $e) {
            // Catch any other general exceptions (e.g., from Database::getInstance() if config is missing)
            error_log("Exception in User::findByEmail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id The user ID to search for.
     * @return object|null The user object if found, or null otherwise.
     */
    public static function findById(int $id): ?object {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            if ($conn === null) {
                error_log("Database connection is null in User::findById.");
                return null;
            }

            $stmt = $conn->prepare("SELECT * FROM " . self::$table . " WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("PDOException in User::findById: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            error_log("Exception in User::findById: " . $e->getMessage());
            return null;
        }
    }

    // Basic CRUD methods (static for simplicity here, could be instance-based)
    // These are just placeholders from the original file, might need full implementation later.
    // public static function find($id) { /* Placeholder */ } - Replaced by findById
    public static function getAll() { /* Placeholder */ }
    public function save() { /* Placeholder for insert/update */ }
    public function delete() { /* Placeholder */ }
}
?>
