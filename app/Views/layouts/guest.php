<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Flow One'); ?></title>
    <link rel="stylesheet" href="<?php echo defined('APP_BASE_PATH') ? APP_BASE_PATH : ''; ?>/css/style.css">
</head>
<body class="guest-layout">
    <div class="guest-container">
        <?php echo $content ?? '<p>Error: Content not loaded.</p>'; ?>
    </div>
    <script src="<?php echo defined('APP_BASE_PATH') ? APP_BASE_PATH : ''; ?>/js/jquery.min.js"></script>
    <script src="<?php echo defined('APP_BASE_PATH') ? APP_BASE_PATH : ''; ?>/js/app.js"></script>
</body>
</html>
