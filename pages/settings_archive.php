<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'archive') {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

$message = '';
$message_type = 'success';

// Fetch current settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    $message = "خطأ في قاعدة البيانات: " . $e->getMessage();
    $message_type = 'danger';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        $updates = [
            'allow_uploads' => isset($_POST['allow_uploads']) ? '1' : '0',
            'max_size' => $_POST['max_size'],
            'allowed_types' => $_POST['allowed_types'],
            'notification_email' => $_POST['notification_email']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        
        foreach ($updates as $key => $val) {
            $stmt->execute([$key, $val]);
            $settings[$key] = $val; // Update local array for display
        }
        
        $pdo->commit();
        $message = "تم حفظ الإعدادات بنجاح!";
        $message_type = 'success';
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "حدث خطأ أثناء الحفظ: " . $e->getMessage();
        $message_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات الأرشيف - نظام الأرشفة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3">
    <link rel="stylesheet" href="../css/archive.css">
    <style>
        .settings-container { max-width: 800px; margin: 0 auto; }
        .settings-card {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            border-right: 5px solid var(--primary);
        }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark); }
        .form-control { 
            width: 100%; 
            padding: 12px; 
            border: 1.5px solid #eee; 
            border-radius: 10px; 
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 55px;
            height: 28px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ddd;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px; width: 20px;
            left: 4px; bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--success); }
        input:checked + .slider:before { transform: translateX(27px); }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="header-wrapper">
        <header class="fixed-header">
            <div class="header-content">
                <div class="logo-container">
                    <div class="logo-img">
                        <img src="../img/1765888818874.jpg" alt="Logo">
                    </div>
                    <div class="logo-text">
                        <div class="university-name">الجامعة الإماراتية الدولية</div>
                        <h1>نظام أرشفة المشاريع الجامعية</h1>
                    </div>
                </div>
                <p>إعدادات نظام الأرشيف</p>
            </div>
        </header>

        <nav class="fixed-nav">
          <ul class="nav-links">
            <li><a href="../index.php"><i class="fas fa-home"></i> الرئيسية</a></li>
            <li><a href="archive_dashboard.php"><i class="fas fa-arrow-right"></i> العودة للوحة الأرشيف</a></li>
          </ul>
        </nav>
    </div>

    <div class="container main-content" style="margin-top: 150px;">
        <div class="settings-container">
            <div class="archive-welcome" style="margin-bottom: 30px;">
                <h1><i class="fas fa-cog"></i> إعدادات الأرشيف</h1>
                <p>تحكم في خصائص رفع المشاريع وتلقي التنبيهات</p>
            </div>

            <?php if($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="settings-card">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <i class="fas fa-upload" style="margin-left: 10px; color: var(--primary);"></i> إعدادات الرفع
                    </h3>
                    
                    <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <label class="form-label">السماح برفع المشاريع</label>
                            <small style="color: var(--gray);">تفعيل أو تعطيل قدرة الطلاب على رفع مشاريع جديدة</small>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="allow_uploads" <?php echo ($settings['allow_uploads'] ?? '1') === '1' ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الحد الأقصى لحجم الملف (ميجابايت)</label>
                        <input type="number" class="form-control" name="max_size" value="<?php echo htmlspecialchars($settings['max_size'] ?? '10'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">أنواع الملفات المسموحة (افصل بفاصلة)</label>
                        <input type="text" class="form-control" name="allowed_types" value="<?php echo htmlspecialchars($settings['allowed_types'] ?? 'pdf, docx, zip'); ?>">
                    </div>
                </div>

                <div class="settings-card">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <i class="fas fa-envelope" style="margin-left: 10px; color: var(--primary);"></i> إعدادات التنبيهات
                    </h3>
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني لاستلام الإشعارات</label>
                        <input type="email" class="form-control" name="notification_email" value="<?php echo htmlspecialchars($settings['notification_email'] ?? 'archive@eiu.edu.ye'); ?>">
                        <small style="color: var(--gray);">سيتم إرسال تنبيه لهذا البريد عند وجود مشاريع جديدة قيد المراجعة</small>
                    </div>
                </div>

                <div style="text-align: left; margin-bottom: 50px;">
                    <button type="submit" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);">
                        <i class="fas fa-save" style="margin-left: 8px;"></i> حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>
         <?php include '../includes/footer.php'; ?>
    </div>
   
    <!-- زر العودة للأعلى -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    
</body>
</html>
