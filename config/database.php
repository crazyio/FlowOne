<?php
// Database Configuration

// IMPORTANT SECURITY WARNING:
// It is strongly recommended NOT to hardcode sensitive credentials like database
// passwords directly in version-controlled files.
// For production environments, use environment variables (e.g., via $_ENV or getenv())
// or include a configuration file that is NOT committed to the repository (e.g., by adding it to .gitignore).
// Example using environment variables (preferred):
// 'username' => $_ENV['DB_USERNAME'] ?? 'default_user',
// 'password' => $_ENV['DB_PASSWORD'] ?? 'default_pass',

return [
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'flowone_admin', // Updated
    'username' => 'flowone_admin', // Updated
    'password' => 'Red!Hot@5tgbbgt5', // Updated - VERY SENSITIVE!
    'charset'  => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'   => '',
];
?>
