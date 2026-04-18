<?php
session_start();

// Database Connection for stats
$host = 'localhost';
$db   = 'university_archive';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  
  $project_count = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'approved'")->fetchColumn();
  $student_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
  $supervisor_count = $pdo->query("SELECT COUNT(DISTINCT supervisor) FROM projects")->fetchColumn();
  
} catch (\PDOException $e) {
  // Fallback to static or zero if DB fails
  $project_count = 1250;
  $student_count = 850;
  $supervisor_count = 45;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>نظام أرشفة المشاريع الجامعية - الجامعة الإماراتية الدولية</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="css/style.css?v=20240301-v3" />
  </head>
  <body>
    
      <!-- التغليف الجديد -->
      <div class="header-wrapper">
        <header class="fixed-header">
          <div class="header-content">
            <div class="logo-container">
              <div class="logo-img">
                <!-- هنا سيتم وضع شعار الجامعة -->
                <img
                  src="img/1765888818874.jpg"
                  alt="شعار الجامعة الإماراتية الدولية"
                />
              </div>
              <div class="logo-text">
                <div class="university-name">الجامعة الإماراتية الدولية</div>
                <h1>نظام أرشفة المشاريع الجامعية</h1>
              </div>
            </div>
            <p>منصة متكاملة لأرشفة وإدارة مشاريع التخرج والبحوث الجامعية</p>
          </div>
        </header>

        <nav class="fixed-nav">
          <ul class="nav-links">
            <li>
              <a href="index.php" class="active"
                ><i class="fas fa-home"></i> الرئيسية</a
              >
            </li>
            <li>
              <a href="pages/projects.php"
                ><i class="fas fa-project-diagram"></i> المشاريع</a
              >
            </li>
            <li>
              <a href="pages/about.php"
                ><i class="fas fa-chalkboard-teacher"></i> عن المنصة</a
              >
            </li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li>
                    <?php 
                    $dashboardContext = ['link' => 'pages/student_dashboard.php', 'text' => 'لوحة الطالب'];
                    
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            $dashboardContext = ['link' => 'pages/admin_dashboard.php', 'text' => 'لوحة التحكم'];
                        } elseif ($_SESSION['role'] == 'archive') {
                            $dashboardContext = ['link' => 'pages/archive_dashboard.php', 'text' => 'لوحة الأرشيف'];
                        }
                    }
                    ?>
                    <a href="<?php echo $dashboardContext['link']; ?>"><i class="fas fa-tachometer-alt"></i> <?php echo $dashboardContext['text']; ?></a>
                </li>
                <li>
                    <a href="pages/profile.php"><i class="fas fa-user-circle"></i></a>
                </li>
            <?php else: ?>
                <li>
                <a href="pages/login.php"
                    ><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a
                >
                </li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>

      <div class="container">
        <section class="hero">
          <h2>مرحباً بك في نظام الأرشفة الأكاديمي</h2>
          <p>منصة متكاملة لأرشفة وإدارة مشاريع التخرج والبحوث الجامعية</p>
          <div class="hero-buttons">
            <a href="pages/projects.php" class="btn btn-primary"
              ><i class="fas fa-search"></i> استعرض المشاريع</a
            >
            <a href="pages/about.php" class="btn btn-secondary"
              ><i class="fas fa-chalkboard-teacher"></i> تعرف على المزيد</a
            >
          </div>
        </section>

        <section class="features">
          <div class="feature-card">
            <h3><i class="fas fa-search"></i> بحث متقدم</h3>
            <p>ابحث في المشاريع باستخدام كلمات مفتاحية، سنة، قسم، أو مشرف</p>
          </div>
          <div class="feature-card">
            <h3><i class="fas fa-archive"></i> أرشيف منظم</h3>
            <p>جميع المشاريع مصنفة ومنظمة بشكل يسهل الوصول إليها</p>
          </div>
          <div class="feature-card">
            <h3><i class="fas fa-shield-alt"></i> إدارة آمنة</h3>
            <p>نظام إدارة آمن ومحكم للمشاريع والمحتوى</p>
          </div>
          
        </section>

        <section class="page-content">
          <h2 class="page-title">مميزات المنصة</h2>

          <div
class="adv"
          >
            <div>
              <h3
                style="
                  color: var(--primary);
                  margin-bottom: 15px;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                "
              >
                <i
                  class="fas fa-check-circle"
                  style="color: var(--success)"
                ></i>
                للطلاب
              </h3>
              <ul style="list-style: none; padding: 0">
                <li
                  style="
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i
                    class="fas fa-graduation-cap"
                    style="color: var(--primary)"
                  ></i>
                  الوصول إلى مشاريع سابقة للإلهام والاستفادة
                </li>
                <li
                  style="
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i class="fas fa-book" style="color: var(--primary)"></i>
                  تنظيم وإدارة مشاريع التخرج بسهولة
                </li>
                <li
                  style="
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i class="fas fa-share-alt" style="color: var(--primary)"></i>
                  مشاركة الأعمال مع المجتمع الأكاديمي
                </li>
                <li
                  style="
                    padding: 8px 0;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i class="fas fa-award" style="color: var(--primary)"></i>
                  بناء ملف إنجازات شخصي وأكاديمي
                </li>
              </ul>
            </div>

            <div>
              <h3
                style="
                  color: var(--primary);
                  margin-bottom: 15px;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                "
              >
                <i
                  class="fas fa-check-circle"
                  style="color: var(--success)"
                ></i>
                للأساتذة والمشرفين
              </h3>
              <ul style="list-style: none; padding: 0">
                <li
                  style="
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i class="fas fa-tasks" style="color: var(--primary)"></i>
                  إدارة ومتابعة مشاريع الطلاب بفعالية
                </li>
                <li
                  style="
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i class="fas fa-chart-bar" style="color: var(--primary)"></i>
                  تقارير وإحصائيات عن تقدم المشاريع
                </li>
                <li
                  style="
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i class="fas fa-comments" style="color: var(--primary)"></i>
                  تقييم وتقديم ملاحظات للطلاب
                </li>
                <li
                  style="
                    padding: 8px 0;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                  "
                >
                  <i class="fas fa-database" style="color: var(--primary)"></i>
                  أرشيف شامل للأعمال البحثية السابقة
                </li>
              </ul>
            </div>
          </div>

          <div
            style="
              margin-top: 40px;
              padding: 25px;
              background: rgba(27, 54, 93, 0.05);
              border-radius: var(--border-radius);
              border-right: 4px solid var(--secondary);
            "
          >
            <h3
              style="
                color: var(--primary);
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
              "
            >
              <i class="fas fa-info-circle" style="color: var(--secondary)"></i>
              حول النظام
            </h3>
            <p
              style="color: var(--dark); line-height: 1.8; margin-bottom: 15px"
            >
              نظام أرشفة المشاريع الجامعية هو منصة إلكترونية متكاملة تم تطويرها
              لخدمة المجتمع الأكاديمي في الجامعة الإماراتية الدولية. يهدف النظام
              إلى حفظ وتنظيم مشاريع التخرج والأبحاث العلمية للطلاب، مما يسهل
              عملية الوصول إليها والاستفادة منها في الأبحاث المستقبلية.
            </p>
            <p style="color: var(--dark); line-height: 1.8">
              يتميز النظام بواجهة مستخدم بسيطة وسهلة الاستخدام، مع توفير أدوات
              بحث متقدمة تمكن المستخدمين من العثور على المشاريع المطلوبة بسرعة
              وكفاءة. كما يدعم النظام تصنيف المشاريع حسب التخصص، السنة، المشرف،
              والعديد من المعايير الأخرى.
            </p>
          </div>
        </section>

        <section class="page-content" style="margin-top: 30px">
          <h2 class="page-title">إحصائيات المنصة</h2>

          <div
            class="features"
            style="
              grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
              margin-top: 30px;
            "
          >
            <div class="feature-card" style="text-align: center">
              <div
                style="
                  
                  color: var(--primary);
                  margin-bottom: 10px;
                "
              >
                <i class="fas fa-project-diagram"></i>
              </div>
              <h3
                style=" color: var(--secondary); margin: 10px 0"
              >
                1,250+
              </h3>
              <p>مشروع مكتمل</p>
            </div>

            <div class="feature-card" style="text-align: center">
              <div
                style="
                  
                  color: var(--primary);
                  margin-bottom: 10px;
                "
              >
                <i class="fas fa-user-graduate"></i>
              </div>
              <h3
                style=" color: var(--secondary); margin: 10px 0"
              >
                <?php echo $student_count; ?>+
              </h3>
              <p>طالب مستفيد</p>
            </div>

            <div class="feature-card" style="text-align: center">
              <div
                style="
                  
                  color: var(--primary);
                  margin-bottom: 10px;
                "
              >
                <i class="fas fa-chalkboard-teacher"></i>
              </div>
              <h3
                style=" color: var(--secondary); margin: 10px 0"
              >
                <?php echo $supervisor_count; ?>+
              </h3>
              <p>مشرف أكاديمي</p>
            </div>

            <div class="feature-card" style="text-align: center">
              <div
                style="
                  
                  color: var(--primary);
                  margin-bottom: 10px;
                "
              >
                <i class="fas fa-book-open"></i>
              </div>
              <h3
                style=" color: var(--secondary); margin: 10px 0"
              >
                <?php echo $project_count; ?>+
              </h3>
              <p>مشروع مؤرشف</p>
            </div>
          </div>
        </section>

        
          
      


    <script src="js/script.js?v=20240301-v3"></script>
    <?php include 'includes/footer.php'; ?>
     <!-- زر العودة للأعلى -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>
  </body>
</html>

