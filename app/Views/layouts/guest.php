<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Flow One'); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/css/style.css">
</head>
<body class="guest-layout">
    <div class="guest-container">
        <?php echo $content ?? '<p>Error: Content not loaded.</p>'; ?>
    </div>
    <script src="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/js/jquery.min.js"></script>
    <script src="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/js/app.js"></script>
</body>
</html>
