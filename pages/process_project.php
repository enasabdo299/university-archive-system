<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'archive') {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$host = 'localhost';
$db   = 'university_archive';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'DB Error']));
}

$input = json_decode(file_get_contents('php://input'), true);
$project_id = $input['project_id'] ?? null;
$action = $input['action'] ?? null;

if (!$project_id || !$action) {
    die(json_encode(['success' => false, 'message' => 'Missing data']));
}

$status = ($action === 'approve') ? 'approved' : 'rejected';

$stmt = $pdo->prepare("UPDATE projects SET status = ? WHERE id = ?");
if ($stmt->execute([$status, $project_id])) {
    echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
