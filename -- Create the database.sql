-- Create the database
CREATE DATABASE IF NOT EXISTS bionexg;

-- Use the created database
USE bionexg;

-- Create the 'users' table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL,
    UNIQUE KEY uq_users_username (username)
);

-- Create the 'tasks' table
CREATE TABLE IF NOT EXISTS tasks (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in progress', 'completed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    deadline DATE,
    assigned_to INT(11),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- Create the 'password_reset_requests' table
CREATE TABLE IF NOT EXISTS password_reset_requests (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    request_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (username),
    CONSTRAINT fk_password_reset_username
        FOREIGN KEY (username) REFERENCES users(username)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Insert example admin and employee users
-- If you want a clean reset each import, uncomment the next two lines:
-- DELETE FROM users;
-- ALTER TABLE users AUTO_INCREMENT = 1;

-- Ensure an admin exists (username: admin, password: 123).
-- Uses the unique username constraint to update instead of failing on duplicates.
INSERT INTO users (username, password, role)
VALUES ('admin', MD5('123'), 'admin')
ON DUPLICATE KEY UPDATE
  password = VALUES(password),
  role = VALUES(role);

-- Seed an employee user too.
INSERT INTO users (username, password, role)
VALUES ('employee', MD5('123'), 'employee')
ON DUPLICATE KEY UPDATE
  password = VALUES(password),
  role = VALUES(role);
