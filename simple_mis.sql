-- Create new database
CREATE DATABASE IF NOT EXISTS `staffdesk`;
USE `staffdesk`;

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('employee','hr') NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create leave_requests table
CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `leave_type` enum('sick','vacation','personal') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create attendance table
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','absent','late') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert 2 HR users
INSERT INTO `users` (`username`, `password`, `role`, `fullname`, `email`, `department`, `position`, `hire_date`) VALUES
('hradmin1', 'password123', 'hr', 'Liam Johnson', 'liam.hr1@example.com', 'Human Resources', 'HR Manager', '2020-01-10'),
('hradmin2', 'password123', 'hr', 'Emma Williams', 'emma.hr2@example.com', 'Human Resources', 'HR Executive', '2021-05-20');

-- Insert 4 Employee users
INSERT INTO `users` (`username`, `password`, `role`, `fullname`, `email`, `department`, `position`, `hire_date`) VALUES
('employee1', 'emp001', 'employee', 'Noah Brown', 'noah.emp1@example.com', 'Engineering', 'Software Developer', '2022-02-15'),
('employee2', 'emp002', 'employee', 'Olivia Davis', 'olivia.emp2@example.com', 'Marketing', 'Marketing Specialist', '2022-03-22'),
('employee3', 'emp003', 'employee', 'William Miller', 'william.emp3@example.com', 'Finance', 'Accountant', '2022-06-05'),
('employee4', 'emp004', 'employee', 'Ava Wilson', 'ava.emp4@example.com', 'Operations', 'Operations Executive', '2022-07-18');

-- Insert sample leave requests for new users
INSERT INTO `leave_requests` (`user_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`) VALUES
(3, 'sick', '2025-01-10', '2025-01-12', 'Flu and rest', 'approved'),
(4, 'vacation', '2025-02-05', '2025-02-10', 'Family trip', 'pending'),
(5, 'personal', '2025-03-15', '2025-03-15', 'Personal errands', 'rejected'),
(6, 'vacation', '2025-04-20', '2025-04-25', 'Holiday', 'approved');

-- Insert sample attendance records for new users
INSERT INTO `attendance` (`user_id`, `date`, `time_in`, `time_out`, `status`) VALUES
(3, '2025-01-13', '09:00:00', '17:00:00', 'present'),
(4, '2025-01-13', '09:15:00', '17:05:00', 'late'),
(5, '2025-01-13', NULL, NULL, 'absent'),
(6, '2025-01-13', '08:50:00', '17:00:00', 'present'),
(3, '2025-01-14', '08:55:00', '17:05:00', 'present'),
(4, '2025-01-14', '09:05:00', '17:10:00', 'present'),
(5, '2025-01-14', '09:00:00', '17:00:00', 'present'),
(6, '2025-01-14', NULL, NULL, 'absent');