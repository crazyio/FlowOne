-- Flow One Back Office System - Database Schema
-- Target Database: MySQL

-- -----------------------------------------------------
-- Table `roles`
-- Stores user roles
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `role_name_UNIQUE` (`role_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `users`
-- Stores user information
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT 'Store hashed passwords',
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


-- -----------------------------------------------------
-- Table `clients`
-- Stores client information
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `client_name` VARCHAR(255) NOT NULL,
  `client_email` VARCHAR(255) NULL,
  `client_phone` VARCHAR(50) NULL,
  `company_name` VARCHAR(255) NULL,
  `address` TEXT NULL,
  `assigned_to_user_id` INT NULL COMMENT 'Could be a Team Manager or Admin',
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


-- -----------------------------------------------------
-- Table `services`
-- Stores services or task templates offered
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `service_name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `default_duration_days` INT NULL COMMENT 'Estimated time to complete',
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


-- -----------------------------------------------------
-- Table `tasks`
-- Stores tasks assigned to clients
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `task_title` VARCHAR(255) NOT NULL,
  `task_description` TEXT NULL,
  `client_id` INT NOT NULL,
  `service_id` INT NULL COMMENT 'If the task is derived from a predefined service',
  `assigned_to_user_id` INT NULL COMMENT 'User responsible for overseeing/completing',
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


-- -----------------------------------------------------
-- Table `task_comments`
-- Stores comments or updates related to a task
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `task_comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `user_id` INT NOT NULL COMMENT 'User who made the comment',
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


-- -----------------------------------------------------
-- Table `documents`
-- Stores information about uploaded documents
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `documents` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(512) NOT NULL COMMENT 'Relative path to the stored file',
  `file_type` VARCHAR(100) NULL,
  `file_size_kb` INT NULL,
  `client_id` INT NULL COMMENT 'If document is general to a client',
  `task_id` INT NULL COMMENT 'If document is specific to a task',
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


-- -----------------------------------------------------
-- Table `notifications`
-- Stores notifications for users
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT 'The recipient of the notification',
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) NULL COMMENT 'e.g., ''task_assigned'', ''status_update'''',
  `related_entity_type` VARCHAR(50) NULL COMMENT 'e.g., ''task'', ''client'''',
  `related_entity_id` INT NULL COMMENT 'ID of the task, client, etc.',
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


-- -----------------------------------------------------
-- Table `activity_logs`
-- Basic audit logs for important actions
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT 'Who performed the action',
  `action_type` VARCHAR(100) NOT NULL COMMENT 'e.g., 'user_created', 'client_updated'',
  `entity_type` VARCHAR(50) NULL COMMENT 'e.g., 'User', 'Client', 'Task'',
  `entity_id` INT NULL COMMENT 'ID of the affected entity',
  `details` TEXT NULL COMMENT 'JSON or text details of the change',
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


-- -----------------------------------------------------
-- Table `settings`
-- Stores system-wide settings
-- -----------------------------------------------------
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

-- -----------------------------------------------------
-- Add some initial data (optional, for basic setup)
-- -----------------------------------------------------

-- Default Roles
INSERT INTO `roles` (`role_name`, `description`) VALUES
('Admin', 'Full system access'),
('Team Manager', 'Manages clients and team tasks'),
('Client', 'Access to own tasks and documents')
ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`); -- Prevents error if run multiple times, ensures description is updated if it changes

-- Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'Flow One Back Office', 'The name of the application, displayed in titles and emails.'),
('default_timezone', 'UTC', 'Default timezone for date and time display.'),
('email_notifications_enabled', '1', 'Enable or disable email notifications (1=enabled, 0=disabled).')
ON DUPLICATE KEY UPDATE `setting_key` = VALUES(`setting_key`); -- Prevents error, ensures value/description updated
