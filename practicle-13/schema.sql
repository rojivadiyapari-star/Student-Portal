
CREATE DATABASE IF NOT EXISTS practical13_db;
USE practical13_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    course VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (name, email, course)
SELECT 'Aarav Patel', 'aarav@example.com', 'Computer Science'
WHERE NOT EXISTS (
    SELECT 1 FROM students WHERE email = 'aarav@example.com'
);
