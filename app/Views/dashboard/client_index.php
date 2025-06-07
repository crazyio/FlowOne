<?php
// app/Views/dashboard/client_index.php
// Fixed client dashboard with correct field names
?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="card-title mb-1">Welcome back, <?php echo htmlspecialchars($userName ?? 'Client'); ?>!</h2>
                    <p class="card-text opacity-75 mb-0">Here's what's happening with your projects today.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-primary mb-1"><?php echo $taskStats['total_tasks'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Total Tasks</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tasks fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-warning mb-1"><?php echo $taskStats['in_progress_tasks'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">In Progress</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-spinner fa-2x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-info mb-1"><?php echo $taskStats['pending_input_tasks'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Awaiting Input</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-success mb-1"><?php echo $taskStats['completed_tasks'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Completed</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (($taskStats['overdue_tasks'] ?? 0) > 0): ?>
    <!-- Overdue Tasks Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Attention!</strong> You have <?php echo $taskStats['overdue_tasks']; ?> overdue task(s) that need your attention.
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Recent Tasks -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Tasks</h5>
                    <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/tasks" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentTasks)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentTasks as $task): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($task['task_title']); ?></h6>
                                            <p class="mb-1 text-muted small"><?php echo htmlspecialchars($task['task_description'] ?? ''); ?></p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-<?php 
                                      echo match($task['task_status']) {
                                      'To Do' => 'secondary',
                                      'In Progress' => 'warning',
                                      'Pending Client Input' => 'info',
                                      'Done' => 'success',
                                      'Cancelled' => 'danger',
                                      default => 'secondary'
                                      };
                                                                      ?> me-2"><?php echo htmlspecialchars($task['task_status']); ?></span>
                                                
                                                <?php if ($task['due_date']): ?>
                                                    <small class="text-muted">
                                                        Due: <?php echo date('M j, Y', strtotime($task['due_date'])); ?>
                                                        <?php if ($task['days_until_due'] !== null): ?>
                                                            <?php if ($task['days_until_due'] < 0): ?>
                                                                <span class="text-danger">(<?php echo abs($task['days_until_due']); ?> days overdue)</span>
                                                            <?php elseif ($task['days_until_due'] <= 3): ?>
                                                                <span class="text-warning">(<?php echo $task['days_until_due']; ?> days left)</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <?php if ($task['task_status'] === 'In Progress'): ?>
                                            <button class="btn btn-sm btn-outline-info ms-2" 
                                                    onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'Pending Client Input')">
                                                Mark as Reviewed
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tasks assigned yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notifications & Quick Actions -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/tasks" class="btn btn-outline-primary">
                            <i class="fas fa-tasks me-2"></i>View All Tasks
                        </a>
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/documents" class="btn btn-outline-info">
                            <i class="fas fa-file-alt me-2"></i>My Documents
                        </a>
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/settings" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Notifications</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($notifications)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-2">
                                            <i class="fas fa-bell text-<?php 
                                      echo match($notification['type'] ?? 'info') {
                                      'success' => 'success',
                                      'warning' => 'warning',
                                      'error' => 'danger',
                                      default => 'info'
                                      };
                                                                       ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <small class="text-muted"><?php echo date('M j, g:i A', strtotime($notification['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                            <p class="text-muted small mb-0">No recent notifications</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    function updateTaskStatus(taskId, status) {
        if (confirm('Are you sure you want to mark this task as reviewed?')) {
            fetch('<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/update-task-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `task_id=${taskId}&status=${encodeURIComponent(status)}`
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Task status updated successfully!');
location.reload();
} else {
    alert('Error: ' + (data.error || 'Failed to update task status'));
}
})
        .catch(error => {
            console.error('Error:', error);
alert('An error occurred while updating the task status');
});
}
}
</script>