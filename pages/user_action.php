<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$userId || !$action) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit;
    }

    try {
        $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
        
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);

        echo json_encode(['status' => 'success', 'message' => 'User status updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
}
?>
