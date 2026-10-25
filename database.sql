CREATE DATABASE IF NOT EXISTS student_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE student_management;

CREATE TABLE IF NOT EXISTS students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  roll_number VARCHAR(30) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  course VARCHAR(100) NOT NULL,
  semester TINYINT UNSIGNED NOT NULL,
  phone VARCHAR(20) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Optional demo records. Remove these INSERT statements if you want an empty database.
INSERT IGNORE INTO students (name, roll_number, email, course, semester, phone) VALUES
('Aarya Patel', 'CSE023', 'aarya.patel@example.com', 'B.Tech CSE', 3, '9876543210'),
('Rohan Shah', 'CSE041', 'rohan.shah@example.com', 'B.Tech CSE', 3, '9876501234'),
('Diya Desai', 'IT017', 'diya.desai@example.com', 'B.Tech IT', 4, NULL);
