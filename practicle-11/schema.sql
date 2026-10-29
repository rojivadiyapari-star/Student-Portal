
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    course VARCHAR(100) NOT NULL,
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (name, email, course, status)
SELECT 'Aarav Patel', 'aarav@example.com', 'B.Tech CSE', 'Active'
WHERE NOT EXISTS (
    SELECT 1 FROM students WHERE email = 'aarav@example.com'
);

INSERT INTO students (name, email, course, status)
SELECT 'Diya Shah', 'diya@example.com', 'BCA', 'Active'
WHERE NOT EXISTS (
    SELECT 1 FROM students WHERE email = 'diya@example.com'
);

INSERT INTO students (name, email, course, status)
SELECT 'Krish Mehta', 'krish@example.com', 'B.Tech IT', 'Active'
WHERE NOT EXISTS (
    SELECT 1 FROM students WHERE email = 'krish@example.com'
);
