<?php
require_once 'includes/db_connect.php';

try {
    // 1. Add status column if it doesn't exist
    // We check if the column exists first to avoid errors on re-run
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'status'");
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE users ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER role";
        $pdo->exec($sql);
        echo "Column 'status' added successfully.<br>";
        
        // 2. Update existing users to 'approved' so they don't get locked out
        // We only do this immediately after adding the column to be safe
        $pdo->exec("UPDATE users SET status = 'approved' WHERE status = 'pending'"); 
        // Wait, if default is pending, the above update will approve NEW users too if I run this while users are registering? 
        // Better to just update ALL current users since this is a migration.
        // Or strictly: UPDATE users SET status='approved'
        $pdo->exec("UPDATE users SET status='approved'");
        echo "All existing users updated to 'approved'.<br>";
        
    } else {
        echo "Column 'status' already exists.<br>";
    }

} catch (PDOException $e) {
    die("Error updating schema: " . $e->getMessage());
}
?>
