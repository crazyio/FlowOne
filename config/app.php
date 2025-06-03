<?php
// Application Configuration
return [
    'name' => 'Flow One Back Office',
    'env' => 'development', // development, production
    'debug' => true, // Enable debug mode for development
    'url' => 'http://localhost', // Base URL of the application
    'base_path_segment' => 'admin', // <-- ADD THIS
    'timezone' => 'UTC',
    'key' => 'base64:yourrandomgeneratedkeyhere', // Regenerate this for a real app
];
?>
