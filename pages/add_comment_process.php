<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول لإضافة تعليق']));
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
$comment = htmlspecialchars($input['comment'] ?? '');

if (!$project_id || empty($comment)) {
    die(json_encode(['success' => false, 'message' => 'Missing data']));
}

$stmt = $pdo->prepare("INSERT INTO comments (project_id, user_id, comment) VALUES (?, ?, ?)");
if ($stmt->execute([$project_id, $_SESSION['user_id'], $comment])) {
    echo json_encode(['success' => true, 'message' => 'تمت إضافة التعليق بنجاح']);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل إضافة التعليق']);
}
