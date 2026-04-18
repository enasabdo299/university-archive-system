<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect without database selected first
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS university_archive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'university_archive' created or already exists.<br>";

    // Now switch to the database
    $pdo->exec("USE university_archive");

    // Create Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'student', 'archive') NOT NULL DEFAULT 'student',
        student_id VARCHAR(50) NULL,
        faculty VARCHAR(100) NULL,
        department VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'users' check/create successful.<br>";

    // Create Projects Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS projects (
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
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "Table 'projects' check/create successful.<br>";

    // Create Comments Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        user_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "Table 'comments' check/create successful.<br>";

    // Create Ratings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        user_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_rating (project_id, user_id)
    )");
    echo "Table 'ratings' check/create successful.<br>";

    // Insert Default Admin
    $password_hash = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', $password_hash, 'admin@eiu.edu.ye', 'System Administrator', 'admin']);
        echo "Default Admin created (admin / 123456)<br>";
    }

    // Insert Default Archive Staff
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['archive']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['archive', $password_hash, 'archive@eiu.edu.ye', 'Archive Staff', 'archive']);
        echo "Default Archive created (archive / 123456)<br>";
    }

    // Insert Default Student
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['student']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role, student_id, faculty) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['student', $password_hash, 'student@eiu.edu.ye', 'Test Student', 'student', '2024001', 'Engineering']);
        echo "Default Student created (student / 123456)<br>";
    }

    echo "<hr><h3>تم إعداد قاعدة البيانات بنجاح!</h3>";
    echo "<a href='index.php'>الذهاب للصفحة الرئيسية</a>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
