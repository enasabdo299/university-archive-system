<?php
$host = 'localhost';
$db   = 'university_archive';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

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
    echo "Comments table created/checked.<br>";

    // Create Evaluations Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS evaluations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        user_id INT NULL,
        session_id VARCHAR(255) NULL,
        rating_type ENUM('like', 'dislike') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "Evaluations table created/checked.<br>";

    // Update Projects Table
    try {
        $pdo->exec("ALTER TABLE projects ADD COLUMN team_members TEXT");
        echo "Added team_members column.<br>";
    } catch (PDOException $e) {
        echo "team_members column likely exists.<br>";
    }

    try {
        $pdo->exec("ALTER TABLE projects ADD COLUMN file_type ENUM('pdf', 'docx') DEFAULT 'pdf'");
        echo "Added file_type column.<br>";
    } catch (PDOException $e) {
        echo "file_type column likely exists.<br>";
    }

    echo "<h3>Database Schema Updated Successfully!</h3>";
    echo "<a href='projects.php'>Go back to Projects</a>";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
