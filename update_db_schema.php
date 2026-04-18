<?php
require_once 'includes/db_connect.php';

try {
    // Add reset_token column
    $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(6) NULL");
    echo "Added reset_token column.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "reset_token column already exists.\n";
    } else {
        echo "Error adding reset_token: " . $e->getMessage() . "\n";
    }
}

try {
    // Add reset_expiry column
    $pdo->exec("ALTER TABLE users ADD COLUMN reset_expiry DATETIME NULL");
    echo "Added reset_expiry column.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "reset_expiry column already exists.\n";
    } else {
        echo "Error adding reset_expiry: " . $e->getMessage() . "\n";
    }
}

echo "Database update completed.";
?>
