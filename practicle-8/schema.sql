
CREATE DATABASE IF NOT EXISTS practical8_db;
USE practical8_db;

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL
);

CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date DATE NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id),
    UNIQUE (student_id, event_id)
);

INSERT INTO students (student_name, email) VALUES
('Aarav Patel', 'aarav@example.com'),
('Diya Shah', 'diya@example.com'),
('Krish Mehta', 'krish@example.com');

INSERT INTO events (event_name, event_date) VALUES
('Web Development Workshop', '2026-11-15'),
('AI and Machine Learning Seminar', '2026-11-20'),
('Coding Competition', '2026-11-25');

INSERT INTO registrations
(student_id, event_id, registration_date) VALUES
(1, 1, '2026-10-01'),
(1, 2, '2026-10-02'),
(2, 1, '2026-10-03'),
(3, 3, '2026-10-04');
