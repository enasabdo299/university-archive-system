<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}
$full_name = $_SESSION['full_name'];
$student_id = $_SESSION['user_id'];

// Check if student has a project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE student_id = ? LIMIT 1");
$stmt->execute([$student_id]);
$myProject = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate Stats
$comments_count = 0;
$days_left = 0;
$project_progress = 0;

if ($myProject) {
    // Project Progress
    if ($myProject['status'] == 'approved') {
        $project_progress = 100;
    } elseif ($myProject['status'] == 'rejected') {
        $project_progress = 0;
    } else {
        $project_progress = 40; // Pending review
    }

    // Comments Count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE project_id = ?");
    $stmt->execute([$myProject['id']]);
    $comments_count = $stmt->fetchColumn();
    
    $deadline = strtotime('2024-06-01');
    $days_left = ceil(($deadline - time()) / 60 / 60 / 24);
    if ($days_left < 0) $days_left = 0;
    
    // Fetch Recent Feedback
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name as commenter_name 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.project_id = ? 
        ORDER BY c.created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$myProject['id']]);
    $recent_feedback = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $recent_feedback = null;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>لوحة تحكم الطالب - نظام الأرشفة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <style>
        /* أنماط خاصة بلوحة تحكم الطالب */
        .student-dashboard {
            margin-top: 20px;
        }
        
        /* بطاقة ترحيبية */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--box-shadow);
            border-right: 5px solid var(--secondary);
        }
        
        .welcome-card h1 {
            
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            opacity: 0.9;
            
        }
        
        /* بطاقات الإحصائيات */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            text-align: center;
            border-top: 4px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: rgba(27, 54, 93, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            
            color: var(--primary);
        }
        
        .stat-number {
            
            
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--gray);
            
        }
        
        /* تخطيط الشبكة */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* بطاقة المشروع */
        .project-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }
        
        .project-card h2 {
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        
        .project-status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            
            
            margin-bottom: 15px;
        }
        
        .status-approved {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success);
        }
        
        .status-pending {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }
        
        .status-rejected {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }
        
        /* شريط التقدم */
        .progress-container {
            margin: 25px 0;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            
            color: var(--gray);
        }
        
        .progress-bar {
            height: 10px;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--success);
            border-radius: 5px;
            transition: width 0.5s ease;
        }
        
        /* التعليقات */
        .feedback-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
        }
        
        .feedback-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .feedback-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            
        }
        
        .feedback-content {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-right: 3px solid var(--secondary);
        }
        
        /* المواعيد */
        .deadlines-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
        }
        
        .deadline-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: var(--transition);
        }
        
        .deadline-item:last-child {
            border-bottom: none;
        }
        
        .deadline-item:hover {
            background: #f8f9fa;
        }
        
        .deadline-date {
            background: rgba(27, 54, 93, 0.1);
            color: var(--primary);
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            min-width: 70px;
        }
        
        .deadline-date .day {
            
            
            display: block;
        }
        
        .deadline-date .month {
            
        }
        
        /* الإجراءات السريعة */
        .quick-actions {
            margin-top: 30px;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            background: white;
            border: 2px solid #e1e5eb;
            border-radius: var(--border-radius);
            color: var(--primary);
            text-decoration: none;
            
            transition: var(--transition);
            text-align: center;
        }
        
        .action-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-3px);
        }
        
        /* حالة عدم وجود مشروع */
        .no-project {
            text-align: center;
            padding: 50px 30px;
        }
        
        .no-project-icon {
            
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header-wrapper">
        <header class="fixed-header">
            <div class="header-content">
                <div class="logo-container">
                    <div class="logo-img">
                        <img src="../img/1765888818874.jpg" alt="شعار الجامعة" />
                    </div>
                    <div class="logo-text">
                        <div class="university-name">الجامعة الإماراتية الدولية</div>
                        <h1>نظام أرشفة المشاريع الجامعية</h1>
                    </div>
                </div>
                <p>لوحة تحكم الطالب</p>
               
            </div>
        </header>

        <nav class="fixed-nav">
            <ul class="nav-links">
                <li><a href="../index.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> المشاريع</a></li>
                <li><a href="about.php"><i class="fas fa-chalkboard-teacher"></i> عن المنصة</a></li>
                <li><a href="student_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> لوحة الطالب</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i></a></li>
                
            </ul>
        </nav>
    </div>

    <!-- Mobile Menu Structure -->


    <div class="mobile-menu-overlay"></div>
    <div class="mobile-menu">
        

    <div class="container student-dashboard">
        <!-- بطاقة الترحيب -->
        <div class="welcome-card">
            <h1>مرحباً، <?php echo htmlspecialchars($full_name); ?> 👋</h1>
            <p>إليك ملخص سريع لمشروع تخرجك والمهام القادمة.</p>
        </div>

        <!-- الإحصائيات السريعة -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number"><?php echo $project_progress; ?>%</div>
                <div class="stat-label">نسبة الإنجاز</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-number"><?php echo $days_left; ?></div>
                <div class="stat-label">يوم متبقي</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="stat-number"><?php echo $myProject ? '1' : '0'; ?></div>
                <div class="stat-label">المشاريع الحالية</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-number"><?php echo $comments_count; ?></div>
                <div class="stat-label">ملاحظات</div>
            </div>
        </div>

        <!-- الشبكة الرئيسية -->
        <div class="dashboard-grid">
            <!-- العمود الرئيسي -->
            <div class="main-column">
                <!-- حالة المشروع -->
                <div class="project-card">
                    <h2>مشروع التخرج</h2>
                    
                    <?php if ($myProject): ?>
                        <span class="project-status-badge status-<?php echo $myProject['status']; ?>">
                            <?php 
                                if($myProject['status'] == 'approved') echo 'تمت الموافقة';
                                elseif($myProject['status'] == 'pending') echo 'قيد المراجعة';
                                else echo 'مرفوض';
                            ?>
                        </span>
                        
                        <div class="project-details">
                            <h3 style="color: var(--primary); margin-bottom: 15px;">
                                <?php echo htmlspecialchars($myProject['title']); ?>
                            </h3>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="color: var(--gray); margin-bottom: 10px;">
                                    <i class="fas fa-user-tie"></i> المشرف: 
                                    <strong><?php echo htmlspecialchars($myProject['supervisor'] ?? 'لم يحدد'); ?></strong>
                                </p>
                                <p style="color: var(--gray);">
                                    <i class="fas fa-calendar-alt"></i> تاريخ الرفع: 
                                    <?php echo date('Y-m-d', strtotime($myProject['created_at'])); ?>
                                </p>
                            </div>
                            
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                                <p style="color: var(--dark); line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($myProject['description'])); ?>
                                </p>
                            </div>
                            
                            <div class="progress-container">
                                <div class="progress-label">
                                    <span>حالة الإنجاز</span>
                                    <span><?php echo $project_progress; ?>%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $project_progress; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-project">
                            <div class="no-project-icon">
                                <i class="fas fa-folder-plus"></i>
                            </div>
                            <h3 style="color: var(--primary); margin-bottom: 15px;">لم تقم برفع مشروع التخرج بعد</h3>
                            <p style="color: var(--gray); margin-bottom: 25px; max-width: 500px; margin: 0 auto 25px;">
                                ابدأ الآن برفع مقترح مشروعك أو المشروع النهائي ليتم مراجعته من قبل المشرفين.
                            </p>
                            <a href="upload_project.php" class="btn btn-primary" style="text-decoration: none;">
                                <i class="fas fa-upload"></i> رفع مشروع جديد
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- آخر الملاحظات -->
                <?php if ($recent_feedback): ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; color: var(--primary);">آخر الملاحظات </h3>
                            <p style="color: var(--gray);  margin: 5px 0 0;">
                                <?php echo htmlspecialchars($recent_feedback['commenter_name']); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="feedback-content">
                        <p style="margin-bottom: 15px; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($recent_feedback['comment'])); ?>
                        </p>
                        <div style="color: var(--gray); ">
                            <i class="far fa-clock"></i> 
                            <?php echo date('Y-m-d H:i', strtotime($recent_feedback['created_at'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- العمود الجانبي -->
            <div class="sidebar-column">
                <!-- المواعيد القادمة -->
                <div class="deadlines-card">
                    <h3 style="color: var(--primary); margin-bottom: 20px;">
                        <i class="fas fa-calendar-alt"></i> المواعيد القادمة
                    </h3>
                    
                    <div class="deadlines-list">
                        <div class="deadline-item">
                            <div class="deadline-date">
                                <span class="day">15</span>
                                <span class="month">مايو</span>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px; color: var(--dark);">تسليم المسودة الأولية</h4>
                                <p style="margin: 0; color: var(--gray); ">الفصل الأول والثاني</p>
                            </div>
                        </div>
                        
                        <div class="deadline-item">
                            <div class="deadline-date">
                                <span class="day">22</span>
                                <span class="month">مايو</span>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px; color: var(--dark);">عرض التقدم الثاني</h4>
                                <p style="margin: 0; color: var(--gray); ">عرض حي للميزات المكتملة</p>
                            </div>
                        </div>
                        
                        <div class="deadline-item">
                            <div class="deadline-date" style="background: rgba(200, 16, 46, 0.1);">
                                <span class="day">01</span>
                                <span class="month">يونيو</span>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px; color: var(--dark);">التسليم النهائي</h4>
                                <p style="margin: 0; color: var(--secondary);  ">⚠️ موعد نهائي</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإجراءات السريعة -->
                <div class="quick-actions">
                    <h3 style="color: var(--primary); margin-bottom: 20px;">
                        <i class="fas fa-bolt"></i> إجراءات سريعة
                    </h3>
                    
                    <div class="action-buttons">
                        <a href="upload_project.php" class="action-btn">
                            <i class="fas fa-upload"></i>
                            رفع مشروع جديد
                        </a>
                        
                        <a href="#" class="action-btn">
                            <i class="fas fa-envelope"></i>
                            مراسلة المشرف
                        </a>
                        
                        <a href="#" class="action-btn">
                            <i class="fas fa-calendar-check"></i>
                            حجز موعد
                        </a>
                        
                        <a href="projects.php" class="action-btn">
                            <i class="fas fa-book"></i>
                            المكتبة الرقمية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/script.js?v=20240301-v3"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
