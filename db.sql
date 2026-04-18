-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS university_archive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE university_archive;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'student', 'archive') NOT NULL DEFAULT 'student',
    student_id VARCHAR(50) NULL, -- Only for students
    faculty VARCHAR(100) NULL, -- Only for students
    department VARCHAR(100) NULL, -- Only for students
    reset_token VARCHAR(6) NULL,
    reset_expiry DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects Table (Structure for future upload feature)
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    student_id INT NOT NULL,
    supervisor VARCHAR(100),
    academic_year VARCHAR(20),
    faculty VARCHAR(100),
    department VARCHAR(100),
    file_path VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    team_members TEXT, -- Group members names
    file_type ENUM('pdf', 'docx') DEFAULT 'pdf',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments Table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Evaluations Table (Likes/Dislikes)
CREATE TABLE IF NOT EXISTS evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NULL, -- NULL for guests
    session_id VARCHAR(255) NULL, -- For guest tracking
    rating_type ENUM('like', 'dislike') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Default Users
-- Passwords are: 'password123' (will be hashed in real app, but for this SQL import we use plain or pre-hashed. 
-- For simplicity in this local setup, I will insert plain text and we will handle hashing in PHP, 
-- OR better, I will insert a known hash. 
-- Let's use password_hash('password123', PASSWORD_DEFAULT) -> $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (This is a standard Laravel demo hash, let's generate a new one or just use 'admin123' for testing and I'll update the PHP to verify strictly)

-- WAIT. To make it easier for the user to test immediately, I will create a PHP script to SETUP the database which inserts hashed passwords correctly.
-- But for this SQL file, I'll just put the schema.

-- Actually, let's just insert users with a simple hash that we know, or rely on the register/login process.
-- I will provide the schema here.
