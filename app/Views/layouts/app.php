<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Flow One'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/css/style.css">
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <div class="logo">Flow One</div>
            <div class="user-menu">
                <span>User Name</span> | <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/logout">Logout</a>
            </div>
        </header>

        <nav class="app-sidebar">
            <p>Sidebar Navigation Placeholder</p>
            <ul>
                <li><a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/dashboard">Dashboard</a></li>
                <li><a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/users">Users</a></li>
                <li><a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/clients">Clients</a></li>
            </ul>
        </nav>

        <main class="app-content">
            <?php echo $content ?? '<h1>Main Content Area Placeholder</h1><p>Error: Content not loaded.</p>'; ?>
        </main>

        <footer class="app-footer">
            <p>&copy; <?php echo date('Y'); ?> Flow One. All rights reserved.</p>
        </footer>
    </div>
    <script src="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/js/jquery.min.js"></script>
    <script src="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
