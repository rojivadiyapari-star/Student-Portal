
CREATE DATABASE IF NOT EXISTS practical14_db;
USE practical14_db;

-- Users and their roles
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) DEFAULT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student information
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    roll_no VARCHAR(30) UNIQUE,
    course VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
);

-- Events
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    event_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Event registrations
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_registration (user_id, event_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id)
        ON DELETE CASCADE
);

-- Audit trail
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

-- Sample data for dashboard screenshots
INSERT IGNORE INTO users (name, email, role) VALUES
('Admin User', 'admin@example.com', 'admin'),
('Aarav Patel', 'aarav@example.com', 'student'),
('Diya Shah', 'diya@example.com', 'student');

INSERT IGNORE INTO students (user_id, roll_no, course) VALUES
(2, 'STU001', 'Computer Science'),
(3, 'STU002', 'Information Technology');

INSERT INTO events (title, description, event_date) VALUES
('Web Development Workshop', 'Practical web development session', '2026-11-15'),
('Coding Competition', 'Programming and problem-solving event', '2026-11-20');

INSERT IGNORE INTO registrations (user_id, event_id) VALUES
(2, 1),
(3, 2);
