<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['fullName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $studentId = trim($_POST['studentId'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $department = trim($_POST['department'] ?? ''); // You might need to handle this if it comes from a select that is populated via JS
    $academicYear = trim($_POST['academicYear'] ?? '');
    
    // Basic Validation
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'جميع الحقول المطلوبة يجب ملؤها.']);
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'اسم المستخدم أو البريد الإلكتروني مسجل بالفعل.']);
        exit;
    }

    // Insert new student
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, email, full_name, role, student_id, faculty, department, status) 
                VALUES (:username, :password, :email, :full_name, 'student', :student_id, :faculty, :department, 'pending')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':email' => $email,
            ':full_name' => $fullName,
            ':student_id' => $studentId,
            ':faculty' => $faculty,
            ':department' => $department
        ]);

        echo json_encode(['status' => 'success', 'message' => 'تم إنشاء الحساب بنجاح.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء التسجيل: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح.']);
}
?>
