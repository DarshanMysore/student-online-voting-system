-- =========================================================
-- Create Database
-- =========================================================


-- =========================================================
-- Departments
-- =========================================================
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS departments;
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Insert Sample Departments
INSERT INTO departments (name) VALUES 
('CSE'),
('ECE'),
('Mechanical'),
('Civil'),
('MBA');

-- =========================================================
-- Voters
-- =========================================================
DROP TABLE IF EXISTS voters;
DROP TABLE IF EXISTS voters;
CREATE TABLE voters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    roll_no VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100) NOT NULL,
    has_voted TINYINT(1) DEFAULT 0
);

-- Insert Sample Voter (password = 123456)
INSERT INTO voters (first_name, roll_no, email, password, department, has_voted) VALUES
('John Doe', 'CSE001', 'john.doe@example.com', 
'$2y$10$WzVvCqW4s3xG3O7lXcJk0O0h5p6hN.F3lzF7QGBKSlO5aYq6gWgQ2', 'CSE', 0);

-- =========================================================
-- Candidates
-- =========================================================
DROP TABLE IF EXISTS candidates;
DROP TABLE IF EXISTS candidates;
CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    vote_count INT DEFAULT 0
);

-- Insert Sample Candidates
INSERT INTO candidates (candidate_name, department, vote_count) VALUES
('Rahul Sharma', 'CSE', 5),
('Ananya Reddy', 'CSE', 3),
('Vikram Rao', 'ECE', 4),
('Priya Verma', 'ECE', 2);

-- =========================================================
-- Admins
-- =========================================================
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS admin;
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert Sample Admin (username: admin, password: admin123)
INSERT INTO admin (username, password) 
VALUES ('admin', 
'$2y$10$WzVvCqW4s3xG3O7lXcJk0O0h5p6hN.F3lzF7QGBKSlO5aYq6gWgQ2');
