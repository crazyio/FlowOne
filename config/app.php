<?php
// Application Configuration
return [
    'name' => 'Flow One Back Office',
    'env' => 'development', // development, production
    'debug' => true, // Enable debug mode for development
    'url' => 'http://localhost', // Base URL of the application
    // This is the segment if the app is NOT at the domain root, e.g., /admin
    // Used for generating correct asset links.
    // If your .htaccess RewriteBase is /admin/, this might also be /admin
    'base_path_segment_for_links' => 'admin',
    'timezone' => 'UTC',
    'key' => 'base64:yourrandomgeneratedkeyhere',
];
?>
