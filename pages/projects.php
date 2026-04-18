<?php
session_start();
// Database Connection
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
} catch (\PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

// Search and Filter Logic
$query = "SELECT p.*, u.full_name as student_name, 
          (SELECT COUNT(*) FROM evaluations WHERE project_id = p.id AND rating_type = 'like') as likes,
          (SELECT COUNT(*) FROM evaluations WHERE project_id = p.id AND rating_type = 'dislike') as dislikes,
          (SELECT COUNT(*) FROM comments WHERE project_id = p.id) as comments_count
          FROM projects p 
          JOIN users u ON p.student_id = u.id 
          WHERE p.status = 'approved'";

$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $query .= " AND (p.title LIKE ? OR u.full_name LIKE ? OR p.supervisor LIKE ? OR p.description LIKE ?)";
    $params = array_merge($params, [$search, $search, $search, $search]);
}

if (isset($_GET['faculty']) && !empty($_GET['faculty'])) {
    $query .= " AND p.faculty = ?";
    $params[] = $_GET['faculty'];
}

if (isset($_GET['year']) && !empty($_GET['year'])) {
    $query .= " AND p.academic_year = ?";
    $params[] = $_GET['year'];
}

// Sort
$sort = $_GET['sort'] ?? 'newest';
switch($sort) {
    case 'oldest': $query .= " ORDER BY p.created_at ASC"; break;
    case 'title': $query .= " ORDER BY p.title ASC"; break;
    default: $query .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$projects = $stmt->fetchAll();
$total_results = count($projects);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>المشاريع - نظام أرشفة المشاريع الجامعية</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <style>
      /* أنماط إضافية خاصة بصفحة المشاريع */
      .projects-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
      }

      .search-container {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
      }

      .search-box {
        flex: 1;
        min-width: 250px;
        position: relative;
      }

      .search-box input {
        width: 100%;
        padding: 12px 45px 12px 15px;
        border: 2px solid #e1e5eb;
        border-radius: var(--border-radius);
        
        transition: var(--transition);
        background: #f8f9fa;
      }

      .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(27, 54, 93, 0.1);
      }

      .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
      }

      .filter-btn {
        padding: 12px 25px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
      }

      .filter-btn:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
      }

      .sort-options {
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .sort-options select {
        padding: 10px 15px;
        border: 2px solid #e1e5eb;
        border-radius: var(--border-radius);
        background: #f8f9fa;
        cursor: pointer;
      }

      .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
      }

      .project-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        transition: var(--transition);
        border-top: 4px solid var(--primary);
      }

      .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      }

      .project-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
      }

      .project-title {
        
        color: var(--primary);
        margin-bottom: 10px;
        line-height: 1.4;
      }

      .project-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
      }

      .project-meta span {
        background: rgba(27, 54, 93, 0.1);
        padding: 4px 10px;
        border-radius: 20px;
        
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .project-body {
        padding: 20px;
      }

      .project-description {
        color: var(--gray);
        line-height: 1.6;
        margin-bottom: 15px;
        
      }

      .project-footer {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .project-stats {
        display: flex;
        gap: 15px;
      }

      .project-stats span {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--gray);
        
      }

      .view-project-btn {
        padding: 8px 20px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        
        text-decoration: none;
        display: inline-block;
        transition: var(--transition);
        
      }

      .view-project-btn:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
      }

      .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 40px;
      }

      .page-btn {
        padding: 10px 15px;
        background: white;
        border: 2px solid #e1e5eb;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        
        min-width: 40px;
        text-align: center;
      }

      .page-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
      }

      .page-btn:hover:not(.active) {
        background: #f8f9fa;
        border-color: var(--primary);
      }

      .faculty-filter {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin: 30px 0;
        padding: 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
      }

      .faculty-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 10px;
        border-radius: var(--border-radius);
        transition: var(--transition);
      }

      .faculty-item:hover {
        background: #f8f9fa;
      }

      .faculty-item input {
        cursor: pointer;
      }

      .no-results {
        text-align: center;
        padding: 50px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin: 30px 0;
      }

      .project-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--secondary);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        
        
      }

      @media (max-width: 768px) {
        .projects-header {
          flex-direction: column;
          align-items: stretch;
        }

        .search-container {
          width: 100%;
        }

        .search-box {
          min-width: 100%;
        }

        .projects-grid {
          grid-template-columns: 1fr;
        }

        .faculty-filter {
          grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
      }
    </style>
  </head>
  <body>
    
      <!-- التغليف الجديد -->
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
            <p>منصة متكاملة لأرشفة وإدارة مشاريع التخرج والبحوث الجامعية</p>
          </div>
        </header>

        <nav class="fixed-nav">
          <ul class="nav-links">
            <li>
              <a href="../index.php"><i class="fas fa-home"></i> الرئيسية</a>
            </li>
            <li>
              <a href="projects.php" class="active"
                ><i class="fas fa-project-diagram"></i> المشاريع</a
              >
            </li>
            <li>
              <a href="about.php"
                ><i class="fas fa-chalkboard-teacher"></i> عن المنصة</a
              >
            </li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li>
                    <?php 
                    $dashboardContext = ['link' => 'student_dashboard.php', 'text' => 'لوحة الطالب'];
                    
                    if (isset($_SESSION['role'])) {
                        if ($_SESSION['role'] == 'admin') {
                            $dashboardContext = ['link' => 'admin_dashboard.php', 'text' => 'لوحة التحكم'];
                        } elseif ($_SESSION['role'] == 'archive') {
                            $dashboardContext = ['link' => 'archive_dashboard.php', 'text' => 'لوحة الأرشيف'];
                        }
                    }
                    ?>
                    <a href="<?php echo $dashboardContext['link']; ?>"><i class="fas fa-tachometer-alt"></i> <?php echo $dashboardContext['text']; ?></a>
                </li>
                <li>
                    <a href="profile.php"><i class="fas fa-user-circle"></i></a>
                </li>
            <?php else: ?>
                <li>
                <a href="login.php"
                    ><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a
                >
                </li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>

      <div class="container">
        <section class="hero">
          <h2>المشاريع والبحوث الجامعية</h2>
          <p>استعرض وتصفح أرشيف المشاريع البحثية ومشاريع التخرج في الجامعة</p>
        </section>

        <section class="page-content">
          <div class="projects-header">
            <div>
              <h2 class="page-title">المشاريع المتاحة</h2>
              <p style="color: var(--gray); margin-top: 5px">
                عرض 1-12 من 1,250 مشروع
              </p>
            </div>

            <form action="projects.php" method="GET" class="search-container">
              <div class="search-box">
                <input
                  type="text"
                  name="search"
                  placeholder="ابحث بالعنوان، الطالب، أو المشرف..."
                  id="searchInput"
                  value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                />
                <i class="fas fa-search"></i>
              </div>
              <button type="button" class="filter-btn" id="filterToggle">
                <i class="fas fa-filter"></i> تصفية
              </button>
              <div class="sort-options">
                <select name="sort" onchange="this.form.submit()">
                  <option value="newest" <?php echo ($sort == 'newest' ? 'selected' : ''); ?>>الأحدث</option>
                  <option value="oldest" <?php echo ($sort == 'oldest' ? 'selected' : ''); ?>>الأقدم</option>
                  <option value="title" <?php echo ($sort == 'title' ? 'selected' : ''); ?>>العنوان</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary" style="padding: 12px 20px;">
                بحث
              </button>
            </form>
          </div>


          <!-- قسم التصفية (مخفي افتراضياً) -->
          <form action="projects.php" method="GET" class="faculty-filter" id="filterSection" style="<?php echo (isset($_GET['faculty']) || isset($_GET['year']) ? 'display: grid' : 'display: none'); ?>">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; ">الكلية:</label>
                <select name="faculty" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">جميع الكليات</option>
                    <option value="علوم الحاسوب" <?php echo (($_GET['faculty'] ?? '') == 'علوم الحاسوب' ? 'selected' : ''); ?>>كلية الحاسبات</option>
                    <option value="الهندسة" <?php echo (($_GET['faculty'] ?? '') == 'الهندسة' ? 'selected' : ''); ?>>كلية الهندسة</option>
                    <option value="الطب" <?php echo (($_GET['faculty'] ?? '') == 'الطب' ? 'selected' : ''); ?>>كلية الطب</option>
                </select>
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; ">العام الدراسي:</label>
                <select name="year" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">جميع الأعوام</option>
                    <option value="2023-2024" <?php echo (($_GET['year'] ?? '') == '2023-2024' ? 'selected' : ''); ?>>2023-2024</option>
                    <option value="2024-2025" <?php echo (($_GET['year'] ?? '') == '2024-2025' ? 'selected' : ''); ?>>2024-2025</option>
                </select>
            </div>
            <div style="grid-column: 1/-1; display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">تطبيق التصفية</button>
                <a href="projects.php" class="btn btn-secondary">إعادة تعيين</a>
            </div>
          </form>

          <!-- شبكة المشاريع -->
          <div class="projects-grid" id="projectsGrid">
            <?php if (empty($projects)): ?>
                <div class="no-results" style="grid-column: 1/-1;">
                    <i class="fas fa-search" style="font-size: 50px; color: #ddd; margin-bottom: 20px;"></i>
                    <p>لم يتم العثور على مشاريع تطابق بحثك.</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <!-- <div class="project-category"><?php echo htmlspecialchars($project['faculty']); ?></div> -->
                        <div class="project-header">
                            <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                            <div class="project-meta">
                                <span><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($project['academic_year']); ?></span>
                                <span><i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($project['student_name']); ?></span>
                                <span><i class="fas fa-chalkboard-teacher"></i> <?php echo htmlspecialchars($project['supervisor']); ?></span>
                            </div>
                        </div>
                        <div class="project-body">
                            <p class="project-description">
                                <?php echo htmlspecialchars(mb_substr($project['description'], 0, 150)) . '...'; ?>
                            </p>
                        </div>
                        <div class="project-footer">
                            <div class="project-stats">
                                <span><i class="fas fa-thumbs-up"></i> <?php echo $project['likes']; ?></span>
                                <span><i class="fas fa-comment"></i> <?php echo $project['comments_count']; ?></span>
                            </div>
                            <a href="project_view.php?id=<?php echo $project['id']; ?>" class="view-project-btn">عرض التفاصيل</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- ترقيم الصفحات -->
          <div class="pagination">
            <button class="page-btn">
              <i class="fas fa-chevron-right"></i>
            </button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">4</button>
            <button class="page-btn">5</button>
            <span style="padding: 0 10px">...</span>
            <button class="page-btn">12</button>
            <button class="page-btn">
              <i class="fas fa-chevron-left"></i>
            </button>
          </div>
        </section>

        <section class="page-content" style="margin-top: 40px">
          <h2 class="page-title">مشاريع مميزة</h2>

          <div
            class="features"
            style="
              grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
              margin-top: 30px;
            "
          >
            <div
              class="feature-card"
              style="text-align: center; border-top: 4px solid var(--secondary)"
            >
              <div
                style="
                  
                  color: var(--primary);
                  margin-bottom: 15px;
                "
              >
                <i class="fas fa-trophy"></i>
              </div>
              <h3
                style=" color: var(--primary); margin: 15px 0"
              >
                أفضل مشروع 2024
              </h3>
              <p style="color: var(--gray); margin-bottom: 15px">
                نظام التعرف على الأمراض النباتية باستخدام الذكاء الاصطناعي
              </p>
              <a href="#" class="view-project-btn" style="margin-top: 10px"
                >عرض المشروع</a
              >
            </div>

            <div
              class="feature-card"
              style="text-align: center; border-top: 4px solid var(--secondary)"
            >
              <div
                style="
                  
                  color: var(--primary);
                  margin-bottom: 15px;
                "
              >
                <i class="fas fa-star"></i>
              </div>
              <h3
                style=" color: var(--primary); margin: 15px 0"
              >
                الأعلى تقييماً
              </h3>
              <p style="color: var(--gray); margin-bottom: 15px">
                نظام إدارة المستشفيات الذكي - تقييم 4.9/5
              </p>
              <a href="#" class="view-project-btn" style="margin-top: 10px"
                >عرض المشروع</a
              >
            </div>
          </div>
        </section>
     

      <?php include '../includes/footer.php'; ?>
     </div>

    <!-- زر العودة للأعلى -->
    <div class="back-to-top">
      <i class="fas fa-arrow-up"></i>
    </div>

    <script src="../js/script.js?v=20240301-v3"></script>
    <script src="../js/projects.js?v=20240301-v3" defer></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // تفعيل/تعطيل قسم التصفية
        const filterToggle = document.getElementById("filterToggle");
        const filterSection = document.getElementById("filterSection");

        filterToggle.addEventListener("click", function () {
          if (
            filterSection.style.display === "none" ||
            filterSection.style.display === ""
          ) {
            filterSection.style.display = "block";
            filterToggle.innerphp =
              '<i class="fas fa-times"></i> إغلاق التصفية';
          } else {
            filterSection.style.display = "none";
            filterToggle.innerphp =
              '<i class="fas fa-filter"></i> تصفية النتائج';
          }
        });

        // البحث الفوري
        const searchInput = document.getElementById("searchInput");
        const projectsGrid = document.getElementById("projectsGrid");
        const projectCards = projectsGrid.querySelectorAll(".project-card");

        searchInput.addEventListener("input", function () {
          const searchTerm = this.value.toLowerCase();

          projectCards.forEach((card) => {
            const title = card
              .querySelector(".project-title")
              .textContent.toLowerCase();
            const description = card
              .querySelector(".project-description")
              .textContent.toLowerCase();

            if (
              title.includes(searchTerm) ||
              description.includes(searchTerm)
            ) {
              card.style.display = "block";
            } else {
              card.style.display = "none";
            }
          });
        });

        // تطبيق التصفية
        const applyFilterBtn = document.getElementById("applyFilter");
        const resetFilterBtn = document.getElementById("resetFilter");
        const facultyCheckboxes = document.querySelectorAll(
          ".faculty-item input"
        );

        applyFilterBtn.addEventListener("click", function () {
          // هنا يمكن إضافة منطق التصفية الحقيقي
          alert("تم تطبيق التصفية بنجاح!");
          filterSection.style.display = "none";
          filterToggle.innerphp =
            '<i class="fas fa-filter"></i> تصفية النتائج';
        });

        resetFilterBtn.addEventListener("click", function () {
          facultyCheckboxes.forEach((checkbox) => {
            checkbox.checked = true;
          });
        });

        // ترتيب النتائج
        const sortSelect = document.getElementById("sortSelect");
        sortSelect.addEventListener("change", function () {
          // هنا يمكن إضافة منطق الترتيب الحقيقي
          alert(
            "تم تغيير طريقة الترتيب إلى: " +
              this.options[this.selectedIndex].text
          );
        });

        // إضافة تأثيرات للبطاقات عند التمرير
        const observerOptions = {
          threshold: 0.1,
          rootMargin: "0px 0px -50px 0px",
        };

        const observer = new IntersectionObserver((entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.style.opacity = "1";
              entry.target.style.transform = "translateY(0)";
            }
          });
        }, observerOptions);

        // تطبيق التأثير على بطاقات المشاريع
        projectCards.forEach((card) => {
          card.style.opacity = "0";
          card.style.transform = "translateY(20px)";
          card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
          observer.observe(card);
        });

        // إظهار البطاقات بعد تحميل الصفحة مباشرة
        setTimeout(() => {
          projectCards.forEach((card) => {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
          });
        }, 300);

        // تفعيل أزرار الترقيم
        const pageBtns = document.querySelectorAll(
          ".page-btn:not(:first-child):not(:last-child)"
        );
        pageBtns.forEach((btn) => {
          btn.addEventListener("click", function () {
            // إزالة النشط من جميع الأزرار
            pageBtns.forEach((b) => b.classList.remove("active"));
            // إضافة النشط للزر المحدد
            this.classList.add("active");
          });
        });
      });
    </script>
  </body>
</html>
