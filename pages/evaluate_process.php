<?php
session_start();
header('Content-Type: application/json');

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
$rating_type = $input['rating_type'] ?? null; // 'like' or 'dislike'

if (!$project_id || !$rating_type) {
    die(json_encode(['success' => false, 'message' => 'Missing data']));
}

$user_id = $_SESSION['user_id'] ?? null;
$session_id = $_SESSION['guest_id'] ?? null;

if (!$session_id) {
    $_SESSION['guest_id'] = uniqid('guest_');
    $session_id = $_SESSION['guest_id'];
}

// Check if already rated
if ($user_id) {
    $stmt = $pdo->prepare("SELECT id, rating_type FROM evaluations WHERE project_id = ? AND user_id = ?");
    $stmt->execute([$project_id, $user_id]);
} else {
    $stmt = $pdo->prepare("SELECT id, rating_type FROM evaluations WHERE project_id = ? AND session_id = ? AND user_id IS NULL");
    $stmt->execute([$project_id, $session_id]);
}

$existing = $stmt->fetch();

if ($existing) {
    if ($existing['rating_type'] === $rating_type) {
        // Toggle off
        $stmt = $pdo->prepare("DELETE FROM evaluations WHERE id = ?");
        $stmt->execute([$existing['id']]);
        $current_rating = null;
    } else {
        // Switch
        $stmt = $pdo->prepare("UPDATE evaluations SET rating_type = ? WHERE id = ?");
        $stmt->execute([$rating_type, $existing['id']]);
        $current_rating = $rating_type;
    }
} else {
    // New rating
    $stmt = $pdo->prepare("INSERT INTO evaluations (project_id, user_id, session_id, rating_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$project_id, $user_id, $session_id, $rating_type]);
    $current_rating = $rating_type;
}

// Get final counts
$stmt = $pdo->prepare("SELECT rating_type, COUNT(*) as count FROM evaluations WHERE project_id = ? GROUP BY rating_type");
$stmt->execute([$project_id]);
$evals = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

echo json_encode([
    'success' => true, 
    'likes' => $evals['like'] ?? 0, 
    'dislikes' => $evals['dislike'] ?? 0,
    'user_rating' => $current_rating
]);
