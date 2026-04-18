<?php
session_start();
require_once '../includes/db_connect.php'; // Correct DB connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Basic validation
    if (empty($full_name) || empty($email)) {
        $error = "الاسم والبريد الإلكتروني مطلوبان.";
    } else {
        try {
            // Check if email is taken by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $error = "البريد الإلكتروني مستخدم بالفعل.";
            } else {
                // Update
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
                if ($stmt->execute([$full_name, $email, $user_id])) {
                    $message = "تم تحديث البيانات بنجاح.";
                    $_SESSION['full_name'] = $full_name; // Update session
                } else {
                    $error = "حدث خطأ أثناء التحديث.";
                }
            }
        } catch (PDOException $e) {
            $error = "خطأ في قاعدة البيانات: " . $e->getMessage();
        }
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "جميع حقول كلمة المرور مطلوبة.";
    } elseif ($new_password !== $confirm_password) {
        $error = "كلمة المرور الجديدة غير مطابقة للتأكيد.";
    } elseif (strlen($new_password) < 6) {
        $error = "يجب أن تكون كلمة المرور 6 أحرف على الأقل.";
    } else {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($current_password, $user['password'])) {
                // Update password
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                if ($stmt->execute([$new_hash, $user_id])) {
                    $message = "تم تغيير كلمة المرور بنجاح.";
                } else {
                    $error = "حدث خطأ أثناء تغيير كلمة المرور.";
                }
            } else {
                $error = "كلمة المرور الحالية غير صحيحة.";
            }
        } catch (PDOException $e) {
            $error = "خطأ: " . $e->getMessage();
        }
    }
}

// Fetch User Data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// Determine dashboard link based on role
$dashboard_link = '../index.php';
if ($user['role'] === 'admin') $dashboard_link = 'admin_dashboard.php';
elseif ($user['role'] === 'archive') $dashboard_link = 'archive_dashboard.php';
elseif ($user['role'] === 'student') $dashboard_link = 'student_dashboard.php';

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي - <?php echo htmlspecialchars($user['full_name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css?v=1771762380">
    <!-- Inline styles specific to profile page to match 'Mashriqa' theme -->
    <style>
        .profile-container {
            max-width: 900px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
        
        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            border-top: 4px solid var(--primary-light);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--light) 0%, #fff 100%);
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: rgb(74 144 226 / 10%); /* Primary light with opacity */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            
            color: var(--primary);
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }

        .profile-name {
            
            color: var(--primary);
            margin-bottom: 5px;
            
        }
        
        .profile-role {
            display: inline-block;
            padding: 5px 15px;
            background: var(--primary);
            color: white;
            border-radius: 20px;
            
        }
        
        .profile-role.student { background: var(--success); }
        .profile-role.archive { background: #e67e22; }
        .profile-role.admin { background: var(--secondary); }
        
        .profile-details {
            padding: 20px;
        }
        
        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f9f9f9;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            display: block;
            color: var(--gray);
            
            margin-bottom: 5px;
        }
        
        .detail-value {
            
            color: var(--dark);
            
        }
        
        .edit-section {
            padding: 30px;
        }
        
        .section-title {
            
            color: var(--primary);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 50px;
            height: 2px;
            background: var(--primary-light);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* تحسينات زر العودة للجوال */
        .back-button-wrapper {
            position: absolute; 
            left: 20px; 
            top: 15px; 
            z-index: 2000;
        }
        .back-btn {
            padding: 6px 15px !important;
            font-size: 0.85rem !important;
            border-radius: 20px !important;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.8) !important;
            color: var(--primary) !important;
            border: 1.5px solid var(--primary) !important;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background: var(--primary) !important;
            color: white !important;
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .back-button-wrapper {
                top: 10px; 
                left: 10px;
            }
            .back-btn {
                padding: 5px 12px !important;
                font-size: 0.8rem !important;
            }
        }
    </style>
</head>
<body>

    <div class="header-wrapper">
        <header class="fixed-header">
            <div class="header-content">
                <div class="logo-container">
                    <div class="logo-img">
                        <img src="../img/1765888818874.jpg" alt="شعار الجامعة">
                    </div>
                    <div class="logo-text">
                        <div class="university-name">الجامعة الإماراتية الدولية</div>
                        <h1>نظام أرشفة المشاريع الجامعية</h1>
                    </div>
                    <div class="header-actions">
                    <?php
                    $dashboard_text = 'لوحة الطالب';
                    if ($user['role'] === 'admin') {
                        $dashboard_text = 'لوحة التحكم';
                    }
                    if ($user['role'] === 'archive') {
                        $dashboard_text = 'لوحة الأرشيف';
                    }
                    ?>

                    <div class="back-button-wrapper">
                        <a href="projects.php" onclick="if(window.history.length > 1){window.history.back(); return false;}" class="btn btn-secondary back-btn">
                            عودة <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                </div>
                </div>
                
                
            </div>
        </header>
    </div>

    <div class="container main-content">
    
        <?php if ($message): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="profile-container">
            <!-- Sidebar / Info Card -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <span class="profile-role <?php echo $user['role']; ?>">
                        <?php 
                        if($user['role'] == 'student') echo 'طالب';
                        elseif($user['role'] == 'archive') echo 'موظف أرشيف';
                        elseif($user['role'] == 'admin') echo 'مشرف نظام';
                        ?>
                    </span>
                </div>
                <div class="profile-details">
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-user-tag"></i> اسم المستخدم</span>
                        <div class="detail-value"><?php echo htmlspecialchars($user['username']); ?></div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-envelope"></i> البريد الإلكتروني</span>
                        <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    
                    <?php if ($user['role'] === 'student'): ?>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-id-card"></i> الرقم الجامعي</span>
                        <div class="detail-value"><?php echo htmlspecialchars($user['student_id'] ?? '-'); ?></div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-university"></i> الكلية</span>
                        <div class="detail-value"><?php echo htmlspecialchars($user['faculty'] ?? '-'); ?></div>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-layer-group"></i> القسم</span>
                        <div class="detail-value"><?php echo htmlspecialchars($user['department'] ?? '-'); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar-alt"></i> تاريخ التسجيل</span>
                        <div class="detail-value"><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></div>
                    </div>
                </div>
                <div style="padding: 0 20px 20px;">
                    <a href="logout.php" onclick="return confirm('هل أنت متأكد من تسجيل الخروج؟');" class="btn btn-danger" style="width: 100%; display: block; text-align: center; background: #dc3545; color: white; padding: 10px; border-radius: 8px;">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </a>
                </div>
            </div>
            
            <!-- Forms Card -->
            <div class="profile-card">
                <div class="edit-section">
                    <h3 class="section-title"><i class="fas fa-edit"></i> تعديل البيانات الشخصية</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>الاسم الكامل</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label>البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                    </form>
                    
                    <h3 class="section-title" style="margin-top: 40px;"><i class="fas fa-lock"></i> تغيير كلمة المرور</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>كلمة المرور الحالية</label>
                            <input type="password" name="current_password" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label>كلمة المرور الجديدة</label>
                            <input type="password" name="new_password" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label>تأكيد كلمة المرور الجديدة</label>
                            <input type="password" name="confirm_password" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                        <button type="submit" name="change_password" class="btn btn-secondary">
                            <i class="fas fa-key"></i> تغيير كلمة المرور
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>

</body>
</html>
