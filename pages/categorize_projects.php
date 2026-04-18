<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'archive') {
    header("Location: login.php");
    exit;
}

require_once '../includes/db_connect.php';

// Fetch projects grouped by Faculty
$faculties = ['كلية الحاسبات', 'كلية الهندسة', 'كلية الطب', 'كلية الإدارة', 'أخرى'];
$selected_faculty = $_GET['faculty'] ?? '';

$query = "SELECT p.*, u.full_name as student_name FROM projects p LEFT JOIN users u ON p.student_id = u.id";
$params = [];

if ($selected_faculty) {
    if ($selected_faculty == 'أخرى') {
        $query .= " WHERE faculty NOT IN ('كلية الحاسبات', 'كلية الهندسة', 'كلية الطب', 'كلية الإدارة') OR faculty IS NULL";
    } else {
        $query .= " WHERE faculty = ?";
        $params[] = $selected_faculty;
    }
}
$query .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تصنيف المشاريع - نظام الأرشفة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3">
    <link rel="stylesheet" href="../css/archive.css">
    <style>
        .filter-container {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        .filter-label { font-weight: 600; color: var(--dark); margin-left: 10px; }
        
        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .category-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
            border-right: 5px solid var(--primary);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .category-card:hover { transform: translateY(-5px); }
        
        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .card-header h3 { margin: 0; font-size: 1.1rem; color: var(--primary); line-height: 1.4; }
        
        .faculty-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }
        
        .faculty-h { background: #3498db; }
        .faculty-e { background: #e67e22; }
        .faculty-m { background: #e74c3c; }
        .faculty-i { background: #9b59b6; }
        .faculty-o { background: #95a5a6; }
        
        .card-body { margin-bottom: 20px; }
        .card-info { font-size: 0.85rem; color: var(--gray); margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        
        .card-footer { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding-top: 15px; 
            border-top: 1px solid #eee;
        }
        
        .btn-view {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        .btn-view:hover { text-decoration: underline; }
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
                <p>تصنيف وتصفح المشاريع حسب الكلية</p>
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
        <div class="archive-welcome" style="margin-bottom: 30px;">
            <h1><i class="fas fa-folder-tree"></i> تصنيف المشاريع</h1>
            <p>تصفح المشاريع المؤرشفة بناءً على التخصص الأكاديمي</p>
        </div>

        <div class="filter-container">
            <span class="filter-label"><i class="fas fa-filter"></i> تصفية:</span>
            <a href="categorize_projects.php" class="btn btn-sm <?php echo !$selected_faculty ? 'btn-primary' : 'btn-outline-secondary'; ?>" style="border-radius: 20px; padding: 8px 20px;">الكل</a>
            <?php foreach($faculties as $f): ?>
                <a href="?faculty=<?php echo urlencode($f); ?>" 
                   class="btn btn-sm <?php echo $selected_faculty == $f ? 'btn-primary' : 'btn-outline-secondary'; ?>"
                   style="border-radius: 20px; padding: 8px 20px;">
                    <?php echo $f; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($projects)): ?>
            <div class="no-projects" style="display: block; padding: 80px 0;">
                <div class="no-projects-icon"><i class="fas fa-folder-open"></i></div>
                <h3>لا توجد مشاريع في هذا التصنيف حالياً</h3>
                <p>جرب اختيار كلية أخرى أو تصفح جميع المشاريع</p>
            </div>
        <?php else: ?>
            <div class="project-grid">
                <?php foreach($projects as $p): 
                    $f_class = 'faculty-o';
                    if($p['faculty'] == 'كلية الحاسبات') $f_class = 'faculty-h';
                    elseif($p['faculty'] == 'كلية الهندسة') $f_class = 'faculty-e';
                    elseif($p['faculty'] == 'كلية الطب') $f_class = 'faculty-m';
                    elseif($p['faculty'] == 'كلية الإدارة') $f_class = 'faculty-i';
                ?>
                <div class="category-card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                        <span class="faculty-badge <?php echo $f_class; ?>">
                            <?php echo htmlspecialchars($p['faculty'] ?? 'عام'); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="card-info">
                            <i class="fas fa-user-graduate"></i>
                            <span>الطالب: <strong><?php echo htmlspecialchars($p['student_name'] ?? 'غير معروف'); ?></strong></span>
                        </div>
                        <div class="card-info">
                            <i class="fas fa-calendar-alt"></i>
                            <span>العام الدراسي: <?php echo htmlspecialchars($p['academic_year'] ?? '-'); ?></span>
                        </div>
                        <div class="card-info">
                            <i class="fas fa-clock"></i>
                            <span>تاريخ الأرشفة: <?php echo date('Y-m-d', strtotime($p['created_at'])); ?></span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="project_view.php?id=<?php echo $p['id']; ?>" class="btn-view">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                        <div class="status-indicator">
                            <?php if($p['status'] == 'approved'): ?>
                                <span style="color: var(--success); font-size: 0.8rem;"><i class="fas fa-check-circle"></i> معتمد</span>
                            <?php elseif($p['status'] == 'pending'): ?>
                                <span style="color: var(--warning); font-size: 0.8rem;"><i class="fas fa-clock"></i> قيد المراجعة</span>
                            <?php else: ?>
                                <span style="color: var(--danger); font-size: 0.8rem;"><i class="fas fa-times-circle"></i> مرفوض</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php include '../includes/footer.php'; ?>
    </div>

    <!-- زر العودة للأعلى -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    
</body>
</html>
