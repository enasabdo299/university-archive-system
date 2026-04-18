<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ((empty($username) && empty($email)) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'يرجى إدخال اسم المستخدم أو البريد الإلكتروني وكلمة المرور.']);
        exit;
    }

    try {
        // Build query based on provided fields
        $conditions = [];
        $params = [];

        if (!empty($username)) {
            $conditions[] = "username = ?";
            $params[] = $username;
        }
        if (!empty($email)) {
            $conditions[] = "email = ?";
            $params[] = $email;
        }

        $sql = "SELECT * FROM users WHERE " . implode(' OR ', $conditions);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        

        if ($user && password_verify($password, $user['password'])) {
            // Check Status
            if ($user['status'] === 'pending') {
                echo json_encode(['status' => 'error', 'message' => 'حسابك قيد المراجعة. يرجى انتظار موافقة المسؤول.']);
                exit;
            } elseif ($user['status'] === 'rejected') {
                echo json_encode(['status' => 'error', 'message' => 'عذراً، تم رفض طلب انضمامك.']);
                exit;
            }

            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Determine redirect URL based on role
            $redirect = '../index.php'; // Fallback

            if ($user['role'] === 'student') {
                $redirect = 'student_dashboard.php';
            } elseif ($user['role'] === 'archive') {
                $redirect = 'archive_dashboard.php';
            } elseif ($user['role'] === 'admin') {
                $redirect = 'admin_dashboard.php';
            }

            echo json_encode(['status' => 'success', 'redirect' => $redirect, 'message' => 'تم تسجيل الدخول بنجاح.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ في النظام.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح.']);
}
?>
