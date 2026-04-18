<?php
$host = 'localhost';
$db_name = 'university_archive';
$username = 'root';
$password = '';
$port = '3306'; // Standard MySQL port is 3306

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // In a production environment, you might want to log this error instead of showing it
    die("Connection failed: " . $e->getMessage());
}
?>
