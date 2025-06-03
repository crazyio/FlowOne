<?php
// This view is rendered within layouts/app.php
// $userName and $userRoleId are passed from DashboardController::index()
// $appBaseLinkPath is available from renderView
?>

<h1>Welcome to the Dashboard, <?php echo htmlspecialchars($userName ?? 'User'); ?>!</h1>
<p>This is your main dashboard area.</p>

<p>Your User ID is: <?php echo htmlspecialchars(App\Core\Session::get('user_id') ?? 'N/A'); ?></p>
<p>Your Role ID is: <?php echo htmlspecialchars($userRoleId ?? 'N/A'); ?></p>

<?php
// Example: Display different content based on role
// In a real app, you might have a Role model or helper to get role names
$roleName = 'Unknown';
if ($userRoleId == 1) {
    $roleName = 'Admin';
} elseif ($userRoleId == 2) {
    $roleName = 'Team Manager';
} elseif ($userRoleId == 3) {
    $roleName = 'Client'; // Assuming a Client role might also see a dashboard
}
?>
<p>Your Role is: <?php echo htmlspecialchars($roleName); ?></p>

<p><a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/logout">Logout</a></p>
