-- Create the database
CREATE DATABASE IF NOT EXISTS `simple_mis`;
USE `simple_mis`;

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

-- Insert default HR account with plain password 'password'
INSERT INTO `users` (`username`, `password`, `role`, `fullname`, `email`, `department`, `position`) VALUES
('hradmin', 'password', 'hr', 'HR Administrator', 'hr@example.com', 'Human Resources', 'HR Manager');

-- Insert sample employees with plain password '0010'
INSERT INTO `users` (`username`, `password`, `role`, `fullname`, `email`, `department`, `position`, `hire_date`) VALUES
('mohammed', '0010', 'employee', 'John Doe', 'john@example.com', 'Engineering', 'Software Developer', '2022-01-15'),
('asim', '0010', 'employee', 'Alice Smith', 'alice@example.com', 'Marketing', 'Marketing Specialist', '2022-03-20'),
('hussain', '0010', 'employee', 'Bob Johnson', 'bob@example.com', 'Finance', 'Accountant', '2022-06-10');

-- Insert sample leave requests
INSERT INTO `leave_requests` (`user_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`) VALUES
(2, 'sick', '2025-04-20', '2025-04-22', 'Medical appointment', 'approved'),
(3, 'vacation', '2025-05-01', '2025-05-10', 'Family vacation', 'pending'),
(2, 'personal', '2025-04-30', '2025-04-30', 'Personal reasons', 'rejected');

-- Insert sample attendance records
INSERT INTO `attendance` (`user_id`, `date`, `time_in`, `time_out`, `status`) VALUES
(2, '2025-04-13', '08:55:00', '17:05:00', 'present'),
(3, '2025-04-13', '09:10:00', '17:00:00', 'late'),
(4, '2025-04-13', '09:00:00', '17:00:00', 'present'),
(2, '2025-04-14', '08:50:00', '17:00:00', 'present'),
(3, '2025-04-14', '08:45:00', '17:10:00', 'present'),
(4, '2025-04-14', NULL, NULL, 'absent');




