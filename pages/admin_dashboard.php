<?php
session_start();
require_once '../includes/db_connect.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$full_name = $_SESSION['full_name'];

// Fetch Stats
$stats = [
    'active_projects' => 0,
    'students' => 0,
    'delayed' => 0,
    'completed' => 0
];

try {
    // Active (Approved) Projects
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'approved'");
    $stats['active_projects'] = $stmt->fetchColumn();

    // Students Count
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
    $stats['students'] = $stmt->fetchColumn();

    // Completed implies approved for now, or could be a separate status. Let's assume 'approved' is active/completed.
    $stats['completed'] = $stats['active_projects']; 
    
    // Pending Projects
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'pending'");
    $pending_count = $stmt->fetchColumn();

    // Fetch latest 5 pending projects
    $stmt = $pdo->query("
        SELECT p.*, u.full_name as student_name 
        FROM projects p 
        JOIN users u ON p.student_id = u.id 
        WHERE p.status = 'pending' 
        ORDER BY p.created_at DESC 
        LIMIT 5
    ");
    $latest_pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Pending Users
    $stmt = $pdo->query("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC");
    $pending_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pending_users_count = count($pending_users);

    // Fetch Top Students (with project counts)
    $stmt = $pdo->query("
        SELECT u.*, 
               COUNT(p.id) as project_count, 
               SUM(CASE WHEN p.status='approved' THEN 1 ELSE 0 END) as approved_count,
               SUM(CASE WHEN p.status='pending' THEN 1 ELSE 0 END) as pending_count
        FROM users u 
        LEFT JOIN projects p ON u.id = p.student_id 
        WHERE u.role = 'student' 
        GROUP BY u.id 
        ORDER BY project_count DESC 
        LIMIT 3
    ");
    $top_students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle error silently or log
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo ($_SESSION['role'] === 'archive') ? 'لوحة تحكم الأرشيف' : 'لوحة تحكم المشرفين'; ?> - نظام الأرشفة</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <!-- Scripts -->
    <script src="../js/script.js?v=20240301-v3" defer></script>
    <script src="../js/main.js?v=20240301-v3" defer></script>
    <script src="../js/admin.js" defer></script>
    <link rel="stylesheet" href="../css/admin.css?v=20240303-v1" />
  </head>
  <body>
    
      <div class="header-wrapper">
        <header class="fixed-header">
          <div class="header-content">
            <div class="logo-container">
              <div class="logo-img">
                <img
                  src="../img/1765888818874.jpg"
                  alt="شعار الجامعة الإماراتية الدولية"
                />
              </div>
              <div class="logo-text">
                <div class="university-name">الجامعة الإماراتية الدولية</div>
                <h1>نظام أرشفة المشاريع الجامعية</h1>
              </div>
            </div>
            <p>لوحة تحكم المشرفين - إدارة النظام والأرشفة</p>
           
          </div>
        </header>

        <nav class="fixed-nav">
          <ul class="nav-links">
           <li>
              <a href="../index.php" 
                ><i class="fas fa-home"></i> الرئيسية</a
              >
            </li>
            <li>
              <a href="projects.php"
                ><i class="fas fa-project-diagram"></i> المشاريع</a
              >
            </li>
             <li>
              <a href="about.php"
                ><i class="fas fa-chalkboard-teacher"></i> عن المنصة</a
              >
            </li>
            <li>
              <a href="admin_dashboard.php" class="active">
                <i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
            </li>
            <li>
              <a href="profile.php">
                <i class="fas fa-user-circle"></i></a>
            </li>
          </ul>
        </nav>
      </div>

    


     <div class="container">
        <div class="admin-container">
          
          <!-- شريط تحكم سريع -->
          <div class="admin-toolbar">          
      <div class="admin-notice">
        <div class="notice-content">
          <p>
            هذه الصفحة خاصة بالمشرفين فقط. الطلاب يمكنهم تصفح المشاريع فقط من
            الصفحة الرئيسية.
          </p>
        </div>
      </div>
            <div class="toolbar-actions">
              <button class="btn btn-primary" onclick="window.location.href='admin_create_user.php'">
                <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
              </button>
              <button class="btn btn-primary" onclick="window.location.href='upload_project.php'">
                <i class="fas fa-plus"></i> إضافة مشروع جديد
              </button>
              <button class="btn btn-secondary" onclick="window.location.href='manage_students.php'">
                <i class="fas fa-users"></i> إدارة الطلاب
              </button>
              <button class="btn btn-secondary" style="margin-inline-start: 5px;" onclick="window.location.href='manage_archive.php'">
                <i class="fas fa-user-tie"></i> إدارة الأرشيف
              </button>
              <button class="fas fa-bell notification-badge">
                <span class="badge-count">3</span>
              </button>
            </div>
           <div class="admin-user-info">
    <div class="admin-user-details">
        <span class="user-name"><?php echo htmlspecialchars($full_name); ?></span>
        <span class="user-role"><?php echo $_SESSION['role'] === 'admin' ? 'مدير النظام' : 'موظف أرشيف'; ?></span>
    </div>
   

          </div>
              
          </div>
          

          <!-- محتوى لوحة التحكم -->
          <div class="admin-content">
            <!-- إحصائيات سريعة -->
            <section class="quick-stats">
              <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> إحصائيات سريعة
              </h2>
              <div class="stats-grid">
                <div class="stat-card">
                  <div class="stat-icon">
                    <i class="fas fa-project-diagram"></i>
                  </div>
                  <div class="stat-info">
                    <h3><?php echo $stats['active_projects']; ?></h3>
                    <p>المشاريع المعتمدة</p>
                  </div>
                </div>
                <div class="stat-card">
                  <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                  </div>
                  <div class="stat-info">
                    <h3><?php echo $stats['students']; ?></h3>
                    <p>الطلاب المسجلين</p>
                  </div>
                </div>
                <div class="stat-card">
                  <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="stat-info">
                    <h3><?php echo $pending_count; ?></h3>
                    <p>قيد المراجعة</p>
                  </div>
                </div>
                <div class="stat-card">
                  <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div class="stat-info">
                    <h3><?php echo $stats['completed']; ?></h3>
                    <p>المشاريع المكتملة</p>
                  </div>
                </div>
              </div>
            </section>

            <!-- المشاريع تحت المراجعة -->
            <section class="pending-projects">
              <div class="section-header">
                <h2>
                  <i class="fas fa-hourglass-half"></i> المشاريع تحت المراجعة
                </h2>
                <a href="#" class="view-all">عرض الكل</a>
              </div>

              <div class="projects-table">
                <table>
                  <thead>
                    <tr>
                      <th>اسم المشروع</th>
                      <th>الطالب</th>
                      <th>تاريخ التسليم</th>
                      <th>الحالة</th>
                      <th>الإجراءات</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(empty($latest_pending)): ?>
                        <tr><td colspan="5" style="text-align:center;">لا يوجد مشاريع قيد المراجعة حالياً</td></tr>
                    <?php else: ?>
                        <?php foreach($latest_pending as $project): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($project['title']); ?></td>
                          <td><?php echo htmlspecialchars($project['student_name']); ?></td>
                          <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($project['created_at']))); ?></td>
                          <td>
                            <span class="status-badge pending">قيد المراجعة</span>
                          </td>
                          <td>
                            <button class="btn-action btn-review" onclick="window.location.href='project_view.php?id=<?php echo $project['id']; ?>'">
                              <i class="fas fa-eye"></i> مراجعة
                            </button>
                            <button class="btn-action btn-approve" onclick="if(confirm('هل أنت متأكد من قبول المشروع؟')) window.location.href='process_project.php?id=<?php echo $project['id']; ?>&action=approve'">
                              <i class="fas fa-check"></i> قبول
                            </button>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </section>

            <!-- المستخدمين بانتظار الموافقة -->
            <section class="pending-users">
              <div class="section-header">
                <h2>
                  <i class="fas fa-user-clock"></i> تسجيلات بانتظار الموافقة
                </h2>
                <span class="badge-count"><?php echo $pending_users_count; ?></span>
              </div>

              <div class="projects-table">
                <table>
                  <thead>
                    <tr>
                      <th>اسم الطالب</th>
                      <th>البريد الإلكتروني</th>
                      <th>الكلية / القسم</th>
                      <th>تاريخ التسجيل</th>
                      <th>الإجراءات</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(empty($pending_users)): ?>
                        <tr><td colspan="5" style="text-align:center;">لا يوجد تسجيلات جديدة بانتظار الموافقة</td></tr>
                    <?php else: ?>
                        <?php foreach($pending_users as $user): ?>
                        <tr id="user-row-<?php echo $user['id']; ?>">
                          <td>
                            <div style=""><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <small style="color: #666;">@<?php echo htmlspecialchars($user['username']); ?></small>
                          </td>
                          <td><?php echo htmlspecialchars($user['email']); ?></td>
                          <td>
                            <?php echo htmlspecialchars($user['faculty'] ?? ''); ?>
                            <br>
                            <small><?php echo htmlspecialchars($user['department'] ?? ''); ?></small>
                          </td>
                          <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($user['created_at']))); ?></td>
                          <td>
                            <button class="btn-action btn-approve" onclick="updateUserStatus(<?php echo $user['id']; ?>, 'approve')">
                              <i class="fas fa-check"></i> قبول
                            </button>
                            <button class="btn-action btn-reject" style="background-color: #dc3545; color: white;" onclick="updateUserStatus(<?php echo $user['id']; ?>, 'reject')">
                              <i class="fas fa-times"></i> رفض
                            </button>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </section>

            <!-- إدارة الطلاب -->
            <section class="students-management">
              <div class="section-header">
                <h2><i class="fas fa-users"></i> الطلاب تحت الإشراف</h2>
                <a href="manage_students.php" class="view-all">إدارة جميع الطلاب</a>
              </div>

              <div class="students-grid">
                <?php if (empty($top_students)): ?>
                    <p style="grid-column: 1/-1; text-align: center;">لا يوجد طلاب مسجلين بعد.</p>
                <?php else: ?>
                    <?php foreach($top_students as $student): ?>
                    <div class="student-card">
                      <div class="student-avatar">
                        <i class="fas fa-user-graduate"></i>
                      </div>
                      <div class="student-info">
                        <h3><?php echo htmlspecialchars($student['full_name']); ?></h3>
                        <p><?php echo htmlspecialchars($student['department'] ?? 'غير محدد'); ?> - <?php echo htmlspecialchars($student['faculty'] ?? 'الجامعة'); ?></p>
                        <div class="student-projects">
                          <span class="project-count"><?php echo $student['project_count']; ?> مشاريع</span>
                          <?php if($student['approved_count'] > 0): ?>
                             <span class="project-status active"><?php echo $student['approved_count']; ?> معتمدة</span>
                          <?php elseif($student['pending_count'] > 0): ?>
                             <span class="project-status pending"><?php echo $student['pending_count']; ?> قيد المراجعة</span>
                          <?php else: ?>
                             <span class="project-status" style="background:#eee; color:#666;">لا يوجد نشاط</span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="student-actions">
                        <button class="btn-action btn-small" title="مراسلة">
                          <i class="fas fa-envelope"></i>
                        </button>
                        <button class="btn-action btn-small" title="عرض الملف">
                          <i class="fas fa-user-circle"></i>
                        </button>
                      </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </section>

            <!-- تقارير سريعة -->
            <section class="quick-reports">
              <div class="section-header">
                <h2><i class="fas fa-file-alt"></i> تقارير سريعة</h2>
                <a href="reports.php" class="view-all">عرض التقارير השاملة</a>
              </div>

              <div class="reports-cards">
                <div class="report-card">
                  <div class="report-icon">
                    <i class="fas fa-chart-pie"></i>
                  </div>
                  <div class="report-content">
                    <h3>تقرير أداء الطلاب</h3>
                    <p>تحليل أداء الطلاب خلال الفصل الحالي</p>
                    <a href="#" class="report-link"
                      >عرض التقرير <i class="fas fa-arrow-left"></i
                    ></a>
                  </div>
                </div>

                <div class="report-card">
                  <div class="report-icon">
                    <i class="fas fa-calendar-check"></i>
                  </div>
                  <div class="report-content">
                    <h3>تقرير المواعيد</h3>
                    <p>جدول مواعيد تسليم المشاريع القادمة</p>
                    <a href="#" class="report-link"
                      >عرض التقرير <i class="fas fa-arrow-left"></i
                    ></a>
                  </div>
                </div>

                <div class="report-card">
                  <div class="report-icon">
                    <i class="fas fa-tasks"></i>
                  </div>
                  <div class="report-content">
                    <h3>تقرير التقدم</h3>
                    <p>معدل تقدم المشاريع تحت الإشراف</p>
                    <a href="#" class="report-link"
                      >عرض التقرير <i class="fas fa-arrow-left"></i
                    ></a>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      

      <?php 
      $footer_about_title = "عن النظام";
      $footer_about_text = "نظام إدارة مشاريع التخرج للمشرفين والطلاب";
      $footer_about_desc = "";
      include '../includes/footer.php'; 
      ?>
     </div>
    </div>
     <!-- زر العودة للأعلى -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Custom scripts already loaded in head with defer -->
    <script>
    function updateUserStatus(userId, action) {
        if(!confirm('هل أنت متأكد من ' + (action === 'approve' ? 'قبول' : 'رفض') + ' هذا المستخدم؟')) return;

        fetch('user_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'user_id=' + userId + '&action=' + action
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert('تم التحديث بنجاح');
                // Remove row
                const row = document.getElementById('user-row-' + userId);
                if(row) row.remove();
                // Update counter logic could be added here
                location.reload(); 
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
    </script>
  </body>
</html>
