<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Security check: Only admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بالقيام بهذا الإجراء.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['fullName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'student';
    
    // Student specific
    $studentId = trim($_POST['studentId'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $department = trim($_POST['department'] ?? '');
    
    // Basic Validation
    if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        echo json_encode(['status' => 'error', 'message' => 'جميع الحقول الأساسية مطلوبة (الاسم، اسم المستخدم، البريد، كلمة المرور).']);
        exit;
    }

    // Check if user exists
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'اسم المستخدم أو البريد الإلكتروني مسجل بالفعل لمستخدم آخر.']);
            exit;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare SQL based on role
        if ($role === 'student') {
            $sql = "INSERT INTO users (username, password, email, full_name, role, student_id, faculty, department, status) 
                    VALUES (:username, :password, :email, :full_name, :role, :student_id, :faculty, :department, 'approved')";
            $params = [
                ':username' => $username,
                ':password' => $hashed_password,
                ':email' => $email,
                ':full_name' => $fullName,
                ':role' => $role,
                ':student_id' => $studentId, 
                ':faculty' => $faculty,
                ':department' => $department
            ];
        } else {
            // For admin and archive roles
            $sql = "INSERT INTO users (username, password, email, full_name, role, status) 
                    VALUES (:username, :password, :email, :full_name, :role, 'approved')";
            $params = [
                ':username' => $username,
                ':password' => $hashed_password,
                ':email' => $email,
                ':full_name' => $fullName,
                ':role' => $role
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['status' => 'success', 'message' => 'تم إنشاء حساب ' . ($role === 'admin' ? 'مدير نظام' : ($role === 'archive' ? 'موظف أرشيف' : 'طالب')) . ' بنجاح وهو الآن نشط.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ في قاعدة البيانات: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح.']);
}
?>
