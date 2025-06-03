<?php
namespace App\Core;

class Session {
    private const FLASH_KEY_PREFIX = '_flash_';

    /**
     * Start the session securely.
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session cookie parameters
            // These are best set in php.ini or .user.ini / .htaccess for broader effect
            // ini_set('session.use_only_cookies', 1); // Default is 1 already in modern PHP
            // ini_set('session.cookie_httponly', 1); // Prevent JS access to session cookie
            // ini_set('session.cookie_samesite', 'Lax'); // Or 'Strict'. PHP 7.3+
            // if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            //     ini_set('session.cookie_secure', 1); // Send cookie only over HTTPS
            // }
            // Note: Setting ini_set here might not work on all shared hostings or if headers already sent.

            session_start();
        }
    }

    /**
     * Set a session variable.
     */
    public static function set(string $key, $value): void {
        self::start(); // Ensure session is started before setting
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable.
     */
    public static function get(string $key, $default = null) {
        self::start(); // Ensure session is started before getting
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session variable exists.
     */
    public static function has(string $key): bool {
        self::start(); // Ensure session is started
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a specific session variable.
     */
    public static function remove(string $key): void {
        self::start(); // Ensure session is started
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy the entire session.
     */
    public static function destroy(): void {
        self::start(); // Ensure session is active to destroy it

        // Unset all session variables
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Regenerate the session ID (good for security, e.g., after login).
     */
    public static function regenerateId(bool $deleteOldSession = true): void {
        self::start(); // Ensure session is started
        // session_regenerate_id can be called only after session_start()
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Set a flash message (lasts for one subsequent request).
     * Or retrieve a flash message if $message is empty.
     */
    public static function flash(string $key, string $message = ''): ?string {
        self::start(); // Ensure session is started
        $sessionKey = self::FLASH_KEY_PREFIX . $key;
        if (!empty($message)) {
            // Set the flash message
            self::set($sessionKey, $message); // Simplified: just store the message
            return null;
        } else {
            // Get and remove the flash message
            return self::getFlash($key);
        }
    }

    /**
     * Get a flash message and remove it.
     */
    public static function getFlash(string $key, $default = null): ?string {
        self::start(); // Ensure session is started
        $sessionKey = self::FLASH_KEY_PREFIX . $key;
        $message = self::get($sessionKey, $default);
        if (self::has($sessionKey)) {
            self::remove($sessionKey); // Remove after retrieval
        }
        return $message;
    }

    /**
     * Clear all flash messages. (Potentially useful for specific scenarios)
     * This version is simpler than the one proposed in description as getFlash already removes.
     * This would only be for clearing messages that were set but never retrieved.
     */
    public static function clearAllFlashMessages(): void {
        self::start();
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, self::FLASH_KEY_PREFIX) === 0) {
                self::remove($key);
            }
        }
    }
}
?>
