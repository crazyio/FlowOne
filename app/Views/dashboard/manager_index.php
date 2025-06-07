<?php
// app/Views/dashboard/manager_index.php
// Manager dashboard view
?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="card-title mb-1">Welcome back, <?php echo htmlspecialchars($userName ?? 'Manager'); ?>!</h2>
                    <p class="card-text opacity-75 mb-0">Here's an overview of your clients and tasks.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-primary mb-1"><?php echo $managerStats['total_clients'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Total Clients</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x text-primary opacity-50"></i>
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
                            <h3 class="text-success mb-1"><?php echo $managerStats['active_clients'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Active Clients</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x text-success opacity-50"></i>
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
                            <h3 class="text-warning mb-1"><?php echo $managerStats['total_tasks'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Total Tasks</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tasks fa-2x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-danger mb-1"><?php echo $managerStats['overdue_tasks'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Overdue Tasks</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (($managerStats['overdue_tasks'] ?? 0) > 0): ?>
    <!-- Overdue Tasks Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Attention!</strong> You have <?php echo $managerStats['overdue_tasks']; ?> overdue task(s) that need immediate attention.
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Task Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Task Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 text-secondary"><?php echo $managerStats['todo_tasks'] ?? 0; ?></div>
                                <small class="text-muted">To Do</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 text-warning"><?php echo $managerStats['in_progress_tasks'] ?? 0; ?></div>
                                <small class="text-muted">In Progress</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 text-info"><?php echo $managerStats['pending_input_tasks'] ?? 0; ?></div>
                                <small class="text-muted">Pending Input</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 text-success"><?php echo $managerStats['completed_tasks'] ?? 0; ?></div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Tasks -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Tasks</h5>
                    <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/tasks" class="btn btn-sm btn-outline-primary">View All Tasks</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentTasks)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentTasks as $task): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($task['task_title']); ?></h6>
                                            <p class="mb-1 text-muted small">
                                                Client: <?php echo htmlspecialchars($task['client_name']); ?>
                                                <?php if ($task['company_name']): ?>
                                                    (<?php echo htmlspecialchars($task['company_name']); ?>)
                                                <?php endif; ?>
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-<?php 
                                      echo match($task['status']) {
                                      'To Do' => 'secondary',
                                      'In Progress' => 'warning',
                                      'Pending Client Input' => 'info',
                                      'Done' => 'success',
                                      'Cancelled' => 'danger',
                                      default => 'secondary'
                                      };
                                                                      ?> me-2"><?php echo htmlspecialchars($task['status']); ?></span>
                                                
                                                <span class="badge bg-<?php 
                                      echo match($task['priority']) {
                                      'Low' => 'light text-dark',
                                      'Medium' => 'warning',
                                      'High' => 'danger',
                                      default => 'secondary'
                                      };
                                                                      ?> me-2"><?php echo htmlspecialchars($task['priority']); ?></span>
                                                
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
                                        
                                        <?php if ($task['status'] !== 'Done' && $task['status'] !== 'Cancelled'): ?>
                                            <div class="dropdown ms-2">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Update
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($task['status'] !== 'To Do'): ?>
                                                        <li><a class="dropdown-item" href="#" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'To Do')">Mark as To Do</a></li>
                                                    <?php endif; ?>
                                                    <?php if ($task['status'] !== 'In Progress'): ?>
                                                        <li><a class="dropdown-item" href="#" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'In Progress')">Mark as In Progress</a></li>
                                                    <?php endif; ?>
                                                    <?php if ($task['status'] !== 'Pending Client Input'): ?>
                                                        <li><a class="dropdown-item" href="#" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'Pending Client Input')">Mark as Pending Input</a></li>
                                                    <?php endif; ?>
                                                    <?php if ($task['status'] !== 'Done'): ?>
                                                        <li><a class="dropdown-item" href="#" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'Done')">Mark as Done</a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
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

        <!-- Sidebar with Clients and Quick Actions -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/clients/new" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Client
                        </a>
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/tasks/new" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Create Task
                        </a>
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/tasks" class="btn btn-outline-info">
                            <i class="fas fa-tasks me-2"></i>View All Tasks
                        </a>
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/reports" class="btn btn-outline-secondary">
                            <i class="fas fa-chart-bar me-2"></i>View Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">My Clients</h5>
                    <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/clients" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($assignedClients)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($assignedClients, 0, 5) as $client): ?>
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($client['client_name']); ?></h6>
                                            <?php if ($client['company_name']): ?>
                                                <p class="mb-1 small text-muted"><?php echo htmlspecialchars($client['company_name']); ?></p>
                                            <?php endif; ?>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-<?php echo $client['status'] === 'active' ? 'success' : ($client['status'] === 'prospect' ? 'warning' : 'secondary'); ?> me-2">
                                                    <?php echo ucfirst($client['status']); ?>
                                                </span>
                                                <small class="text-muted">
                                                    <?php echo $client['active_tasks']; ?> active tasks
                                                    <?php if ($client['overdue_tasks'] > 0): ?>
                                                        | <span class="text-danger"><?php echo $client['overdue_tasks']; ?> overdue</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-users fa-2x text-muted mb-2"></i>
                            <p class="text-muted small mb-0">No clients assigned yet</p>
                        </div>
                    <?php endif; ?>
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
        if (confirm(`Are you sure you want to update this task to "${status}"?`)) {
            fetch('<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/update-task-status', {
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