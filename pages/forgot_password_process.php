<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Log errors to a file for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    switch ($action) {
        case 'send_code':
            $email = trim($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'error', 'message' => 'البريد الإلكتروني غير صالح']);
                exit;
            }

            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                // Return success even if email not found (security practice), or specific error if requested.
                // For this project, let's be helpful and say if not found.
                echo json_encode(['status' => 'error', 'message' => 'البريد الإلكتروني غير مسجل في النظام']);
                exit;
            }

            // Generate 6-digit code
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Store in DB
            $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $update->execute([$code, $expiry, $user['id']]);

            // Simulation: Return code in response
            echo json_encode(['status' => 'success', 'message' => 'تم إرسال الرمز بنجاح', 'debug_code' => $code]);
            break;

        case 'verify_code':
            $email = trim($_POST['email']);
            $code = trim($_POST['code']);

            $stmt = $pdo->prepare("SELECT id, reset_expiry FROM users WHERE email = ? AND reset_token = ?");
            $stmt->execute([$email, $code]);
            $user = $stmt->fetch();

            if ($user) {
                if (strtotime($user['reset_expiry']) > time()) {
                    echo json_encode(['status' => 'success', 'message' => 'تم التحقق من الرمز']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'انتهت صلاحية الرمز']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'الرمز غير صحيح']);
            }
            break;

        case 'reset_password':
            $email = trim($_POST['email']);
            $code = trim($_POST['code']);
            $newPassword = $_POST['newPassword'];

            if (strlen($newPassword) < 6) {
                echo json_encode(['status' => 'error', 'message' => 'يجب أن تكون كلمة المرور 6 أحرف على الأقل']);
                exit;
            }

            // Verify again before resetting
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()");
            $stmt->execute([$email, $code]);
            $user = $stmt->fetch();

            if ($user) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
                $update->execute([$hash, $user['id']]);

                echo json_encode(['status' => 'success', 'message' => 'تم تغيير كلمة المرور بنجاح']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'الرمز غير صحيح أو منتهي الصلاحية']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ في الخادم: ' . $e->getMessage()]);
}
?>
