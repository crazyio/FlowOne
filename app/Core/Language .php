<?php
namespace App\Core;

use App\Core\Database;
use App\Core\Session;
use PDO;
use Exception;

class Language {
    private static $currentLanguage = 'en';
    private static $translations = [];
    private static $supportedLanguages = [];
    private static $loaded = false;

    /**
     * Initialize the language system
     */
    public static function init() {
        if (self::$loaded) {
            return;
        }

        // Get user's preferred language from session or default to English
        Session::start();
        $userLanguage = Session::get('user_language', 'en');
        self::setLanguage($userLanguage);
        
        self::loadSupportedLanguages();
        self::loadTranslations();
        self::$loaded = true;
    }

    /**
     * Set the current language
     */
    public static function setLanguage($languageCode) {
        if (self::isSupported($languageCode)) {
            self::$currentLanguage = $languageCode;
            Session::set('user_language', $languageCode);
        }
    }

    /**
     * Get the current language code
     */
    public static function getCurrentLanguage() {
        return self::$currentLanguage;
    }

    /**
     * Get current language direction (ltr/rtl)
     */
    public static function getDirection() {
        if (!empty(self::$supportedLanguages[self::$currentLanguage])) {
            return self::$supportedLanguages[self::$currentLanguage]['direction'];
        }
        return 'ltr';
    }

    /**
     * Check if language is RTL
     */
    public static function isRTL() {
        return self::getDirection() === 'rtl';
    }

    /**
     * Get all supported languages
     */
    public static function getSupportedLanguages() {
        if (empty(self::$supportedLanguages)) {
            self::loadSupportedLanguages();
        }
        return self::$supportedLanguages;
    }

    /**
     * Check if a language is supported
     */
    public static function isSupported($languageCode) {
        return isset(self::getSupportedLanguages()[$languageCode]);
    }

    /**
     * Get translation for a key
     */
    public static function get($key, $replacements = []) {
        if (empty(self::$translations)) {
            self::loadTranslations();
        }

        $translation = self::$translations[self::$currentLanguage][$key] ?? 
                      self::$translations['en'][$key] ?? 
                      $key;

        // Handle replacements like {name}
        if (!empty($replacements)) {
            foreach ($replacements as $placeholder => $value) {
                $translation = str_replace('{' . $placeholder . '}', $value, $translation);
            }
        }

        return $translation;
    }

    /**
     * Alias for get() method for shorter syntax
     */
    public static function t($key, $replacements = []) {
        return self::get($key, $replacements);
    }

    /**
     * Load supported languages from database
     */
    private static function loadSupportedLanguages() {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT code, name, native_name, direction FROM languages WHERE is_active = 1");
            $stmt->execute();
            
            $languages = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $languages[$row['code']] = [
                    'name' => $row['name'],
                    'native_name' => $row['native_name'],
                    'direction' => $row['direction']
                ];
            }
            
            self::$supportedLanguages = $languages;
        }
        catch (Exception $e) {
            error_log("Error loading supported languages: " . $e->getMessage());
            // Fallback to default languages
            self::$supportedLanguages = [
                'en' => ['name' => 'English', 'native_name' => 'English', 'direction' => 'ltr'],
                'he' => ['name' => 'Hebrew', 'native_name' => 'עברית', 'direction' => 'rtl']
            ];
        }
    }

    /**
     * Load translations from database
     */
    private static function loadTranslations() {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT `key`, language_code, value FROM translations");
            $stmt->execute();
            
            $translations = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $translations[$row['language_code']][$row['key']] = $row['value'];
            }
            
            self::$translations = $translations;
        }
        catch (Exception $e) {
            error_log("Error loading translations: " . $e->getMessage());
            // Fallback to empty translations
            self::$translations = [];
        }
    }

    /**
     * Update user's language preference in database
     */
    public static function updateUserLanguage($userId, $languageCode) {
        if (!self::isSupported($languageCode)) {
            return false;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET language = :language WHERE id = :user_id");
            $stmt->execute([
                'language' => $languageCode,
                'user_id' => $userId
            ]);
            
            // Update session
            self::setLanguage($languageCode);
            
            return true;
        }
        catch (Exception $e) {
            error_log("Error updating user language: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's language from database
     */
    public static function getUserLanguage($userId) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT language FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['language'] : 'en';
        }
        catch (Exception $e) {
            error_log("Error getting user language: " . $e->getMessage());
            return 'en';
        }
    }

    /**
     * Get CSS class for current language direction
     */
    public static function getDirectionClass() {
        return self::isRTL() ? 'rtl' : 'ltr';
    }

    /**
     * Get appropriate CSS file for current language
     */
    public static function getCSSFile() {
        return self::isRTL() ? 'style-rtl.css' : 'style.css';
    }
}
?>