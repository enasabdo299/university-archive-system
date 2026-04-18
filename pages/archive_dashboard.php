<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'archive') {
    header("Location: login.php");
    exit;
}
$full_name = $_SESSION['full_name'];

// Database Connection
require_once '../includes/db_connect.php';

try {
     // Statistics
     $total_projects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
     $approved_projects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'approved'")->fetchColumn();
     $rejected_projects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'rejected'")->fetchColumn();
     $pending_projects_count = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'pending'")->fetchColumn();
     
     
     
     // Fetch ALL Projects for Client-side Filtering
     $query = "SELECT p.*, u.full_name as student_name FROM projects p LEFT JOIN users u ON p.student_id = u.id ORDER BY p.created_at DESC";
     $stmt = $pdo->prepare($query);
     $stmt->execute();
     $projects = $stmt->fetchAll();

     // Set default view references for initial PHP render (optional, but good for title)
     $section_title = 'المشاريع قيد المراجعة'; // Default view
     $section_icon = 'fa-clock';
     
} catch (\PDOException $e) {
     $total_projects = 0;
     $approved_projects = 0;
     $rejected_projects = 0;
     $pending_projects_count = 0;
     $pending_projects = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>لوحة تحكم الأرشيف - نظام الأرشفة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <link rel="stylesheet" href="../css/archive.css" />

    <script src="../js/script.js?v=20240301-v3" defer></script>
    <script src="../js/main.js?v=20240301-v3" defer></script>
   
</head>
<body>
    <!-- ========== الهيدر الأصلي ========== -->
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
            <p>لوحة تحكم الأرشيف - إدارة وتصنيف المشاريع</p>
            <!-- <button class="hamburger-btn">
                <i class="fas fa-bars"></i>
            </button> -->
          </div>
        </header>

        <!-- ========== الـ Navigation الأصلي ========== -->
        <nav class="fixed-nav">
          <ul class="nav-links">
            <li>
              <a href="../index.php"><i class="fas fa-home"></i> الرئيسية</a>
            </li>
            <li>
              <a href="projects.php"><i class="fas fa-project-diagram"></i> المشاريع</a>
            </li>
            <li>
              <a href="about.php"><i class="fas fa-chalkboard-teacher"></i> عن المنصة</a>
            </li>
            <li>
              <a href="archive_dashboard.php" class="active">
                <i class="fas fa-tachometer-alt"></i> لوحة الأرشيف</a>
            </li>
            <li>
              <a href="profile.php">
                <i class="fas fa-user-circle"></i></a>
            </li>
          </ul>
        </nav>
    </div>

   

    <div class="container archive-dashboard">
        

        <!-- بطاقة الترحيب -->
        <div class="archive-welcome">
            <span class="archive-badge">
                <i class="fas fa-archive"></i> قسم الأرشيف
            </span>
            <h1>مرحباً، <?php echo htmlspecialchars($full_name); ?> 👋</h1>
            <p>إدارة وأرشفة المشاريع الجامعية بكل سهولة واحترافية</p>
        </div>

        <!-- إحصائيات الأرشيف -->
        <div class="archive-stats-grid">
            <div class="archive-stat-card" onclick="filterProjects('all')" id="card-all" style="cursor: pointer;">
                <div class="archive-stat-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="archive-stat-number"><?php echo $total_projects; ?></div>
                <div class="archive-stat-label">إجمالي المشاريع</div>
            </div>
            
            <div class="archive-stat-card stat-approved" onclick="filterProjects('approved')" id="card-approved" style="cursor: pointer;">
                <div class="archive-stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="archive-stat-number"><?php echo $approved_projects; ?></div>
                <div class="archive-stat-label">مشاريع معتمدة</div>
            </div>
            
            <div class="archive-stat-card stat-pending active" onclick="filterProjects('pending')" id="card-pending" style="cursor: pointer;">
                <div class="archive-stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="archive-stat-number"><?php echo $pending_projects_count; ?></div>
                <div class="archive-stat-label">قيد المراجعة</div>
            </div>
            
            <div class="archive-stat-card stat-rejected" onclick="filterProjects('rejected')" id="card-rejected" style="cursor: pointer;">
                <div class="archive-stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="archive-stat-number"><?php echo $rejected_projects; ?></div>
                <div class="archive-stat-label">مشاريع مرفوضة</div>
            </div>
        </div>


        <!-- القسم الرئيسي (المشاريع - عرض كامل) -->
        <div class="projects-section">
                
                <!-- Project Tabs -->
                <div class="dashboard-card" style="padding: 15px; margin-bottom: 20px;">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="filterProjects('pending')" class="btn btn-sm btn-primary filter-btn active" id="btn-pending" style="border: 1px solid var(--primary); padding: 8px 20px; border-radius: 20px;">
                            <i class="fas fa-clock"></i> قيد المراجعة
                        </button>
                        <button onclick="filterProjects('approved')" class="btn btn-sm btn-outline-success filter-btn" id="btn-approved" style="border: 1px solid var(--success); color: var(--success); padding: 8px 20px; border-radius: 20px;">
                            <i class="fas fa-check-circle"></i> المعتمدة
                        </button>
                        <button onclick="filterProjects('rejected')" class="btn btn-sm btn-outline-danger filter-btn" id="btn-rejected" style="border: 1px solid var(--danger); color: var(--danger); padding: 8px 20px; border-radius: 20px;">
                            <i class="fas fa-times-circle"></i> المرفوضة
                        </button>
                        <button onclick="filterProjects('all')" class="btn btn-sm btn-outline-secondary filter-btn" id="btn-all" style="border: 1px solid var(--secondary); color: var(--secondary); padding: 8px 20px; border-radius: 20px;">
                            <i class="fas fa-list"></i> الكل
                        </button>
                    </div>
                </div>

                <!-- Projects List -->
                <div class="dashboard-card">
                    <h2 id="section-title"><i class="fas <?php echo $section_icon; ?>"></i> <?php echo $section_title; ?></h2>
                    
                    <?php if (empty($projects)): ?>
                        <div class="no-projects" style="display: block;">
                            <div class="no-projects-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h3>لا توجد مشاريع</h3>
                        </div>
                    <?php else: ?>
                        <div class="projects-table-container">
                            <table class="projects-table" id="projectsTable">
                                <thead>
                                    <tr>
                                        <th>عنوان المشروع</th>
                                        <th>اسم الطالب</th>
                                        <th>التخصص</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project): ?>
                                    <tr class="project-row" data-status="<?php echo htmlspecialchars($project['status']); ?>" style="<?php echo $project['status'] === 'pending' ? '' : 'display: none;'; ?>">
                                        <td>
                                            <strong><?php echo htmlspecialchars($project['title']); ?></strong><br>
                                            <small style="color: #666;">رفع: <?php echo date('Y-m-d', strtotime($project['created_at'])); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($project['student_name']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($project['faculty'] ?? 'غير محدد'); ?><br>
                                            <small style="color: #666;"><?php echo htmlspecialchars($project['academic_year'] ?? ''); ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                            $status_class = '';
                                            $status_text = '';
                                            switch($project['status']) {
                                                case 'approved': $status_class = 'success'; $status_text = 'معتمد'; break;
                                                case 'rejected': $status_class = 'danger'; $status_text = 'مرفوض'; break;
                                                default: $status_class = 'warning'; $status_text = 'قيد المراجعة';
                                            }
                                            ?>
                                            <span class="badge badge-<?php echo $status_class; ?>" style="padding: 5px 10px; border-radius: 10px; color: white; background-color: var(--<?php echo $status_class; ?>);">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="../uploads/projects/<?php echo $project['file_path']; ?>" 
                                                   target="_blank" 
                                                   class="btn-action btn-review">
                                                    <i class="fas fa-eye"></i> معاينة
                                                </a>
                                                <?php if($project['status'] == 'pending'): ?>
                                                    <button onclick="processProject(<?php echo $project['id']; ?>, 'approve')" 
                                                            class="btn-action btn-approve">
                                                        <i class="fas fa-check"></i> موافقة
                                                    </button>
                                                    <button onclick="processProject(<?php echo $project['id']; ?>, 'reject')" 
                                                            class="btn-action btn-reject">
                                                        <i class="fas fa-times"></i> رفض
                                                    </button>
                                                <?php elseif($project['status'] == 'rejected'): ?>
                                                    <button onclick="processProject(<?php echo $project['id']; ?>, 'approve')" 
                                                            class="btn-action btn-approve">
                                                        <i class="fas fa-check"></i> إعادة النظر (موافقة)
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div id="no-filtered-projects" class="no-projects">
                                <div class="no-projects-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h3>لا توجد مشاريع في هذه القائمة</h3>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- أرشيف المستندات -->
                <!-- <div class="dashboard-card">
                    <h2><i class="fas fa-folder-open"></i> أرشيف المستندات</h2>
                    
                    <div class="documents-grid">
                        <div class="document-card">
                            <div class="document-header">
                                <i class="fas fa-file-pdf" style="color: var(--danger);"></i>
                                <h3>مشاريع التخرج 2023</h3>
                            </div>
                            <div class="document-info">
                                <p><i class="fas fa-calendar"></i> الفصل: ربيع 2023</p>
                                <p><i class="fas fa-boxes"></i> عدد المشاريع: 42</p>
                                <p><i class="fas fa-tags"></i> التصنيف: جميع التخصصات</p>
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <button class="btn-action btn-review btn-small">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn-action btn-approve btn-small">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="document-card">
                            <div class="document-header">
                                <i class="fas fa-file-word" style="color: var(--primary);"></i>
                                <h3>أبحاث الماجستير</h3>
                            </div>
                            <div class="document-info">
                                <p><i class="fas fa-calendar"></i> الفترة: 2020-2023</p>
                                <p><i class="fas fa-boxes"></i> عدد الأبحاث: 28</p>
                                <p><i class="fas fa-tags"></i> التصنيف: الدراسات العليا</p>
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <button class="btn-action btn-review btn-small">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn-action btn-approve btn-small">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div> -->
        </div>

        <!-- الشبكة السفلية (الأدوات والإجراءات) -->
        <div class="dashboard-layout">
            <!-- أدوات الأرشيف -->
            <div class="dashboard-card">
                <h2><i class="fas fa-tools"></i> أدوات الأرشيف</h2>
                
                <div class="tools-grid">
                    <div class="tool-item" onclick="location.href='categorize_projects.php'">
                        <div class="tool-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="tool-info">
                            <h3>تصنيف المشاريع</h3>
                            <p>تنظيم المشاريع في فئات</p>
                        </div>
                    </div>
                    
                    <!-- <div class="tool-item" onclick="location.href='import_export.php'">
                        <div class="tool-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="tool-info">
                            <h3>استيراد/تصدير</h3>
                            <p>بيانات الأرشيف</p>
                        </div>
                    </div> -->
                    
                    <div class="tool-item" onclick="location.href='reports.php'">
                        <div class="tool-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="tool-info">
                            <h3>التقارير</h3>
                            <p>تقارير وتحليلات المشاريع</p>
                        </div>
                    </div>
                    
                    <!-- <div class="tool-item" onclick="location.href='user_management.php'">
                        <div class="tool-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="tool-info">
                            <h3>إدارة المستخدمين</h3>
                            <p>صلاحيات الأرشيف</p>
                        </div>
                    </div> -->
                </div>
            </div>

            <!-- الإجراءات السريعة -->
            <div class="dashboard-card">
                <h2><i class="fas fa-bolt"></i> إجراءات سريعة</h2>
                
                <div class="quick-actions-grid">
                    <a href="upload_project.php" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>إضافة مشروع جديد</span>
                    </a>
                    
                    <a href="categorize_projects.php" class="quick-action-btn">
                        <i class="fas fa-folder"></i>
                        <span>تصنيف المشاريع</span>
                    </a>
                    
                    <a href="reports.php" class="quick-action-btn">
                        <i class="fas fa-file-alt"></i>
                        <span>تقارير النظام</span>
                    </a>
                    
                    <!-- <a href="backup_system.php" class="quick-action-btn">
                        <i class="fas fa-database"></i>
                        <span>نسخ احتياطي</span>
                    </a> -->
                    
                    <a href="settings_archive.php" class="quick-action-btn">
                        <i class="fas fa-cog"></i>
                        <span>إعدادات الأرشيف</span>
                    </a>
                </div>
            </div>
        </div>

        <?php 
        $footer_about_title = "قسم الأرشيف";
        $footer_about_text = "إدارة وأرشفة المشاريع الجامعية";
        $footer_about_desc = ""; 
        include '../includes/footer.php'; 
        ?>
    </div>

    <!-- زر العودة للأعلى -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>
    
    <!-- ========== الفوتر الأصلي ========== -->
    <!-- <footer>
        <div class="footer-content">
          <div class="footer-section">
            <h3>عن الأرشيف</h3>
            <p>نظام إدارة وأرشفة المشاريع الجامعية</p>
          </div>
          <div class="footer-section">
            <h3>وظائف الأرشيف</h3>
            <ul class="footer-links">
              <li>
                <a href="archive_dashboard.php"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
              </li>
              <li>
                <a href="upload_project.php"><i class="fas fa-plus-circle"></i> إضافة مشروع</a>
              </li>
              <li>
                <a href="reports.php"><i class="fas fa-chart-bar"></i> التقارير</a>
              </li>
              <li>
                <a href="settings_archive.php"><i class="fas fa-cog"></i> إعدادات</a>
              </li>
            </ul>
          </div>
          <div class="footer-section">
            <h3>دعم الأرشيف</h3>
            <ul class="footer-links">
              <li><i class="fas fa-phone"></i> الأرشيف: 5678</li>
              <li><i class="fas fa-envelope"></i> archive@university.edu.sa</li>
              <li><i class="fas fa-clock"></i> ساعات العمل: 8ص - 3م</li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>© 2024 نظام أرشفة المشاريع - قسم الأرشيف الإلكتروني</p>
        </div>
      </footer> -->

    <!-- Custom scripts loaded in head -->
    <script>
      function processProject(projectId, action) {
        if (!confirm('هل أنت متأكد من تنفيذ هذا الإجراء؟')) return;

        fetch('process_project.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            project_id: projectId,
            action: action
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('خطأ: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('حدث خطأ أثناء معالجة الطلب.');
        });
      }

      // وظائف خاصة بالأرشيف
      document.addEventListener('DOMContentLoaded', function() {
        // تحديث العداد التلقائي
        setInterval(updateNotificationCount, 30000);
        
        // تهيئة الأدوات
        initArchiveTools();
      });
      
      function updateNotificationCount() {
        // هنا يمكن إضافة AJAX لجلب عدد الإشعارات الجديدة
        console.log('تحديث إشعارات الأرشيف...');
      }
      
      function initArchiveTools() {
        console.log('تهيئة أدوات الأرشيف...');
        // إضافة أي وظائف JavaScript خاصة بالأرشيف هنا
      }

      function filterProjects(status) {
          // Update active state in Stats Cards
          document.querySelectorAll('.archive-stat-card').forEach(card => card.classList.remove('active'));
          const cardId = 'card-' + status;
          const activeCard = document.getElementById(cardId);
          if(activeCard) activeCard.classList.add('active');

          // Update active state in Filter Buttons
          document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
          // Set button styles back to outline
          document.getElementById('btn-pending').className = 'btn btn-sm btn-outline-primary filter-btn';
          document.getElementById('btn-approved').className = 'btn btn-sm btn-outline-success filter-btn';
          document.getElementById('btn-rejected').className = 'btn btn-sm btn-outline-danger filter-btn';
          document.getElementById('btn-all').className = 'btn btn-sm btn-outline-secondary filter-btn';

          // Set active button style to solid
          const btnName = 'btn-' + status;
          const activeBtn = document.getElementById(btnName);
          if (activeBtn) {
              activeBtn.classList.add('active');
              if (status === 'pending') activeBtn.className = 'btn btn-sm btn-primary filter-btn active';
              if (status === 'approved') activeBtn.className = 'btn btn-sm btn-success filter-btn active';
              if (status === 'rejected') activeBtn.className = 'btn btn-sm btn-danger filter-btn active';
              if (status === 'all') activeBtn.className = 'btn btn-sm btn-secondary filter-btn active';
          } 

          // Update Selection Title and Icon
          const titleMap = {
              'pending': {text: 'المشاريع قيد المراجعة', icon: 'fa-clock'},
              'approved': {text: 'المشاريع المعتمدة', icon: 'fa-check-circle'},
              'rejected': {text: 'المشاريع المرفوضة', icon: 'fa-times-circle'},
              'all': {text: 'جميع المشاريع', icon: 'fa-list'}
          };
          
          const sectionTitle = document.getElementById('section-title');
          if (sectionTitle && titleMap[status]) {
              sectionTitle.innerHTML = `<i class="fas ${titleMap[status].icon}"></i> ${titleMap[status].text}`;
          }

          // Filter Table Rows
          const rows = document.querySelectorAll('.project-row');
          let visibleCount = 0;
          rows.forEach(row => {
              if (status === 'all' || row.getAttribute('data-status') === status) {
                  row.style.display = '';
                  visibleCount++;
              } else {
                  row.style.display = 'none';
              }
          });

          // Show "No Projects" message if count is 0
          const noProjectsMsg = document.getElementById('no-filtered-projects');
          const table = document.getElementById('projectsTable');
          
          if(visibleCount === 0) {
              if(noProjectsMsg) noProjectsMsg.style.display = 'block';
              if(table) table.style.display = 'none';
          } else {
              if(noProjectsMsg) noProjectsMsg.style.display = 'none';
              if(table) table.style.display = 'table';
          }
      }
    </script>
  </body>
</html>