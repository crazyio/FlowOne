CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `role_name_UNIQUE` (`role_name` ASC))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL,
  `phone` VARCHAR(50) NULL,
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `last_login_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `fk_users_roles_idx` (`role_id` ASC),
  CONSTRAINT `fk_users_roles`
    FOREIGN KEY (`role_id`)
    REFERENCES `roles` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `client_name` VARCHAR(255) NOT NULL,
  `client_email` VARCHAR(255) NULL,
  `client_phone` VARCHAR(50) NULL,
  `company_name` VARCHAR(255) NULL,
  `address` TEXT NULL,
  `assigned_to_user_id` INT NULL,
  `status` ENUM('active', 'inactive', 'prospect') NOT NULL DEFAULT 'active',
  `created_by_user_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `client_email_UNIQUE` (`client_email` ASC),
  INDEX `fk_clients_users_assigned_idx` (`assigned_to_user_id` ASC),
  INDEX `fk_clients_users_created_idx` (`created_by_user_id` ASC),
  CONSTRAINT `fk_clients_users_assigned`
    FOREIGN KEY (`assigned_to_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_clients_users_created`
    FOREIGN KEY (`created_by_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `services` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `service_name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `default_duration_days` INT NULL,
  `status` ENUM('active', 'archived') NOT NULL DEFAULT 'active',
  `created_by_user_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_services_users_idx` (`created_by_user_id` ASC),
  CONSTRAINT `fk_services_users`
    FOREIGN KEY (`created_by_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `task_title` VARCHAR(255) NOT NULL,
  `task_description` TEXT NULL,
  `client_id` INT NOT NULL,
  `service_id` INT NULL,
  `assigned_to_user_id` INT NULL,
  `status` ENUM('To Do', 'In Progress', 'Pending Client Input', 'Done', 'Cancelled') NOT NULL DEFAULT 'To Do',
  `priority` ENUM('Low', 'Medium', 'High') NOT NULL DEFAULT 'Medium',
  `due_date` DATE NULL,
  `completed_at` TIMESTAMP NULL,
  `created_by_user_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_tasks_clients_idx` (`client_id` ASC),
  INDEX `fk_tasks_services_idx` (`service_id` ASC),
  INDEX `fk_tasks_users_assigned_idx` (`assigned_to_user_id` ASC),
  INDEX `fk_tasks_users_created_idx` (`created_by_user_id` ASC),
  CONSTRAINT `fk_tasks_clients`
    FOREIGN KEY (`client_id`)
    REFERENCES `clients` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_tasks_services`
    FOREIGN KEY (`service_id`)
    REFERENCES `services` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_tasks_users_assigned`
    FOREIGN KEY (`assigned_to_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_tasks_users_created`
    FOREIGN KEY (`created_by_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `task_comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `comment_text` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_task_comments_tasks_idx` (`task_id` ASC),
  INDEX `fk_task_comments_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_task_comments_tasks`
    FOREIGN KEY (`task_id`)
    REFERENCES `tasks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_task_comments_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `documents` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(512) NOT NULL,
  `file_type` VARCHAR(100) NULL,
  `file_size_kb` INT NULL,
  `client_id` INT NULL,
  `task_id` INT NULL,
  `uploaded_by_user_id` INT NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_documents_clients_idx` (`client_id` ASC),
  INDEX `fk_documents_tasks_idx` (`task_id` ASC),
  INDEX `fk_documents_users_idx` (`uploaded_by_user_id` ASC),
  CONSTRAINT `fk_documents_clients`
    FOREIGN KEY (`client_id`)
    REFERENCES `clients` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_documents_tasks`
    FOREIGN KEY (`task_id`)
    REFERENCES `tasks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_documents_users`
    FOREIGN KEY (`uploaded_by_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) NULL,
  `related_entity_type` VARCHAR(50) NULL,
  `related_entity_id` INT NULL,
  `read_status` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_notifications_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_notifications_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `action_type` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NULL,
  `entity_id` INT NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_activity_logs_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_activity_logs_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT NULL,
  `description` VARCHAR(255) NULL,
  `updated_by_user_id` INT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `setting_key_UNIQUE` (`setting_key` ASC),
  INDEX `fk_settings_users_idx` (`updated_by_user_id` ASC),
  CONSTRAINT `fk_settings_users`
    FOREIGN KEY (`updated_by_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

INSERT INTO `roles` (`role_name`, `description`) VALUES
('Admin', 'Full system access'),
('Team Manager', 'Manages clients and team tasks'),
('Client', 'Access to own tasks and documents')
ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`);

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'Flow One Back Office', 'The name of the application, displayed in titles and emails.'),
('default_timezone', 'UTC', 'Default timezone for date and time display.'),
('email_notifications_enabled', '1', 'Enable or disable email notifications (1=enabled, 0=disabled).')
ON DUPLICATE KEY UPDATE `setting_key` = VALUES(`setting_key`);


-- -----------------------------------------------------
-- Dummy Data Inserts
-- -----------------------------------------------------

-- Note: Passwords for users are now BCRYPT HASHED. The original plain text was 'password123'.
-- In a real application, these MUST be hashed using password_hash() in PHP.

-- Users (Assuming role IDs: 1=Admin, 2=Team Manager, 3=Client)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@example.com', '$2y$10$N9qo8uLOickq.Z12samgU/hUdKzHcX1uKToffo8I0p5G/A/Q0my', 1, 'active', NOW(), NOW()),
(2, 'Manager Mike', 'manager@example.com', '$2y$10$N9qo8uLOickq.Z12samgU/hUdKzHcX1uKToffo8I0p5G/A/Q0my', 2, 'active', NOW(), NOW()),
(3, 'Client Chris', 'client@example.com', '$2y$10$N9qo8uLOickq.Z12samgU/hUdKzHcX1uKToffo8I0p5G/A/Q0my', 3, 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `email`=VALUES(`email`), `password`=VALUES(`password`), `role_id`=VALUES(`role_id`), `status`=VALUES(`status`), `updated_at`=NOW();

-- Clients (Link to users - e.g., created_by_user_id, assigned_to_user_id)
INSERT INTO `clients` (`id`, `client_name`, `client_email`, `client_phone`, `company_name`, `assigned_to_user_id`, `status`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 'Alpha Corp', 'contact@alphacorp.com', '555-0101', 'Alpha Corporation', 2, 'active', 1, NOW(), NOW()),
(2, 'Beta Solutions', 'info@betasolutions.com', '555-0102', 'Beta Solutions LLC', 2, 'active', 1, NOW(), NOW()),
(3, 'Gamma Industries', 'support@gammaind.com', '555-0103', 'Gamma Industries', NULL, 'prospect', 2, NOW(), NOW())
ON DUPLICATE KEY UPDATE `client_name`=VALUES(`client_name`), `client_email`=VALUES(`client_email`), `assigned_to_user_id`=VALUES(`assigned_to_user_id`), `status`=VALUES(`status`), `created_by_user_id`=VALUES(`created_by_user_id`), `updated_at`=NOW();

-- Services
INSERT INTO `services` (`id`, `service_name`, `description`, `default_duration_days`, `status`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 'Standard Consultation', 'Initial consultation service to assess client needs.', 1, 'active', 1, NOW(), NOW()),
(2, 'Project Setup', 'Service for setting up a new project environment for a client.', 5, 'active', 1, NOW(), NOW()),
(3, 'Monthly Maintenance', 'Ongoing monthly maintenance and support.', 30, 'active', 2, NOW(), NOW())
ON DUPLICATE KEY UPDATE `service_name`=VALUES(`service_name`), `description`=VALUES(`description`), `status`=VALUES(`status`), `created_by_user_id`=VALUES(`created_by_user_id`), `updated_at`=NOW();

-- Tasks (Link to clients, services, and users)
INSERT INTO `tasks` (`id`, `task_title`, `task_description`, `client_id`, `service_id`, `assigned_to_user_id`, `status`, `priority`, `due_date`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 'Initial Call with Alpha Corp', 'Schedule and conduct initial consultation call.', 1, 1, 2, 'To Do', 'High', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 1, NOW(), NOW()),
(2, 'Setup Beta Solutions Project Env', 'Complete project environment setup as per specs.', 2, 2, 2, 'In Progress', 'High', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 1, NOW(), NOW()),
(3, 'Review Gamma Industries Proposal', 'Review their requirements and prepare a proposal.', 3, 1, 2, 'To Do', 'Medium', DATE_ADD(CURDATE(), INTERVAL 5 DAY), 2, NOW(), NOW()),
(4, 'Alpha Corp Follow-up Q1', 'Follow up regarding Q1 deliverables.', 1, NULL, 2, 'Pending Client Input', 'Medium', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 2, NOW(), NOW()),
(5, 'Monthly Maintenance for Beta Solutions - Jan', 'Perform January maintenance tasks.', 2, 3, 2, 'Done', 'Medium', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY))
ON DUPLICATE KEY UPDATE `task_title`=VALUES(`task_title`), `client_id`=VALUES(`client_id`), `service_id`=VALUES(`service_id`), `assigned_to_user_id`=VALUES(`assigned_to_user_id`), `status`=VALUES(`status`), `priority`=VALUES(`priority`), `due_date`=VALUES(`due_date`), `created_by_user_id`=VALUES(`created_by_user_id`), `updated_at`=NOW();
