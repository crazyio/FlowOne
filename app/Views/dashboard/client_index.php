
<?php
// app/Views/dashboard/client_index.php
// This view will be rendered within the 'client' layout.
?>
<div class="container mt-4">
    <h1>Client Dashboard</h1>
    <hr>
    <p>Hello, <strong><?php echo htmlspecialchars($userName ?? 'Client User'); ?></strong>!</p>
    <p>Welcome to your personalized workspace. Your assigned role ID is: <strong><?php echo htmlspecialchars($userRoleId ?? 'Not available'); ?></strong>.</p>
    <div class="alert alert-info" role="alert">
        This is the main dashboard area for clients. Future updates will populate this space with relevant information and tools.
    </div>
    <p><em>(Placeholder content for the client dashboard)</em></p>
</div>

