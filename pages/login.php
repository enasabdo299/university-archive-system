<?php session_start();
if(isset($_SESSION['user_id'])){ header("Location: ../index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>تسجيل الدخول - نظام أرشفة المشاريع الجامعية</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <style>
      /* أنماط إضافية خاصة بصفحة تسجيل الدخول */
      .auth-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        margin: 40px 0;
      }

      @media (max-width: 768px) {
        .auth-container {
          grid-template-columns: 1fr;
          gap: 30px;
        }
      }

      .login-form-container {
        max-width: 100%;
        margin: 0;
      }

      .auth-info {
        background: linear-gradient(
          135deg,
          var(--primary) 0%,
          var(--primary-light) 100%
        );
        color: white;
        border-radius: var(--border-radius);
        padding: 40px;
        box-shadow: var(--box-shadow);
        position: relative;
        overflow: hidden;
      }

      .auth-info::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,192C1248,192,1344,128,1392,96L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        background-position: center;
        z-index: 0;
      }

      .auth-info-content {
        position: relative;
        z-index: 1;
      }

      .auth-info h3 {
        
        margin-bottom: 20px;
        color: white;
        position: relative;
        padding-bottom: 15px;
      }

      .auth-info h3::after {
        content: "";
        position: absolute;
        bottom: 0;
        right: 0;
        width: 60px;
        height: 3px;
        background: var(--secondary);
      }

      .auth-benefits {
        list-style: none;
        padding: 0;
        margin: 30px 0;
      }

      .auth-benefits li {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
      }

      .auth-benefits i {
        color: var(--gold);
        margin-top: 3px;
      }

      .user-type-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 25px;
      }

      .user-type-btn {
        padding: 15px 10px;
        text-align: center;
        border: 2px solid #e1e5eb;
        background: white;
        border-radius: var(--border-radius);
        cursor: pointer;
        
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
      }

      .user-type-btn i {
        
      }

      .user-type-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
      }

      .user-type-btn:hover:not(.active) {
        border-color: var(--primary);
        transform: translateY(-2px);
      }

      .user-type-btn.student.active {
        background: #4caf50;
        border-color: #4caf50;
      }

      .user-type-btn.archive.active {
        background: #ff9800;
        border-color: #ff9800;
      }

      .user-type-btn.admin.active {
        background: var(--secondary);
        border-color: var(--secondary);
      }

      .form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
        flex-wrap: wrap;
        gap: 15px;
      }

      .register-link {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        color: var(--gray);
      }

      .register-link a {
        color: var(--primary);
        
        text-decoration: none;
      }

      .register-link a:hover {
        text-decoration: underline;
      }

      .visitor-section {
        text-align: center;
        margin-top: 40px;
        padding: 25px;
        background: rgba(27, 54, 93, 0.05);
        border-radius: var(--border-radius);
        border-right: 4px solid var(--secondary);
      }

      .visitor-section h4 {
        color: var(--primary);
        margin-bottom: 15px;
      }

      .password-toggle {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--gray);
      }

      .form-group {
        position: relative;
      }

      .permissions-info {
        margin-top: 30px;
        background: #f5f5f547;
        border-radius: var(--border-radius);
        padding: 20px;
        border-right: 4px solid var(--primary);
      }

      .permissions-info h4 {
        color: var(--primary);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .permission-item {
        margin-bottom: 10px;
        padding-right: 10px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
      }

      .permission-item i {
        color: var(--success);
        margin-top: 3px;
      }

      .demo-accounts {
        margin-top: 20px;
        background: rgba(255, 152, 0, 0.1);
        border-radius: var(--border-radius);
        padding: 15px;
        border-right: 4px solid #ff9800;
      }

      .demo-accounts h5 {
        color: #ff9800;
        margin-bottom: 10px;
      }

      .demo-account {
        margin-bottom: 8px;
        
      }

      @media (max-width: 768px) {
        .user-type-selector {
          grid-template-columns: 1fr;
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
              <a href="login.php" class="active"
                ><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a
              >
            </li>
          </ul>
        </nav>
      </div>

      <div class="container">
        <section class="hero">
          <h2>تسجيل الدخول للنظام</h2>
          <p>اختر نوع حسابك للدخول إلى نظام أرشفة المشاريع الجامعية</p>
        </section>

        <div class="auth-container">
          <div class="login-form-container">
            <h2 class="page-title">الدخول إلى النظام</h2>

            <div class="user-type-selector">
              <button class="user-type-btn student active" data-type="student">
                <i class="fas fa-user-graduate"></i>
                <span>طالب</span>
              </button>
              <button class="user-type-btn archive" data-type="archive">
                <i class="fas fa-archive"></i>
                <span>موظف أرشفة</span>
              </button>
              <button class="user-type-btn admin" data-type="admin">
                <i class="fas fa-user-shield"></i>
                <span>مشرف النظام</span>
              </button>
            </div>

            <form id="loginForm" method="POST" action="login_process.php">
              <div id="loginMessage" style="display:none; padding: 10px; margin-bottom: 15px; border-radius: 5px;"></div>
              <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <div style="position: relative;">
                  <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="أدخل اسم المستخدم"
                    style="padding-right: 35px;"
                  />
                  <i class="fas fa-user" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: var(--gray);"></i>
                </div>
              </div>

              <div class="form-group">
                <label for="email">البريد الإلكتروني (اختياري)</label>
                <div style="position: relative;">
                  <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="أدخل البريد الإلكتروني"
                    style="padding-right: 35px;"
                  />
                  <i class="fas fa-envelope" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: var(--gray);"></i>
                </div>
              </div>

              <div class="form-group">
                <label for="password">كلمة المرور</label>
                <div style="position: relative;">
                  <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="أدخل كلمة المرور"
                    required
                  />
                  <span class="password-toggle" id="togglePassword">
                    <i class="fas fa-eye"></i>
                  </span>
                </div>
              </div>

              <div class="form-options">
                <div class="remember-me">
                  <input type="checkbox" id="remember" name="remember" />
                  <label for="remember">تذكرني</label>
                </div>
                <a href="forgot-password.php" class="forgot-password"
                  >نسيت كلمة المرور؟</a
                >
              </div>

              <button
                type="submit"
                class="btn btn-primary"
                style="width: 100%; padding: 14px; "
              >
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
              </button>
            </form>

           

            <div class="register-link">
              <p>طالب جديد؟ <a href="register.php">سجل حساب جديد</a></p>
              <p style=" margin-top: 10px; color: var(--gray)">
                (تسجيل الحساب متاح للطلاب فقط)
              </p>
            </div>

            <div class="visitor-section">
              <h4><i class="fas fa-globe"></i> زيارة النظام كزائر</h4>
              <p>يمكنك تصفح المشاريع والبحث فيها دون الحاجة لتسجيل الدخول</p>
              <a
                href="../index.php"
                class="btn btn-secondary"
                style="margin-top: 15px"
              >
                <i class="fas fa-eye"></i> تصفح كمستخدم زائر
              </a>
            </div>
          </div>

          <div class="auth-info">
            <div class="auth-info-content">
              <h3>صلاحيات المستخدمين</h3>

              <div class="permissions-info">
                <h4 id="permission-title">
                  <i class="fas fa-user-graduate"></i> صلاحيات الطالب
                </h4>
                <div id="student-permissions">
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>إنشاء حساب جديد</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>تسجيل الدخول للنظام</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>رفع المشاريع الشخصية للنظام</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>تصفح المشاريع السابقة</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>البحث في المشاريع المتاحة</span>
                  </div>
                  <div class="permission-item">
                    <i
                      class="fas fa-times-circle"
                      style="color: var(--danger)"
                    ></i>
                    <span>تحميل المشاريع (غير متاح)</span>
                  </div>
                </div>

                <div id="archive-permissions" style="display: none">
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>تسجيل الدخول للنظام</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>الموافقة على رفع المشاريع</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>مراجعة المشاريع المرفوعة</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>تحديث وتعديل بيانات المشاريع</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>إصدار تقارير عن المشاريع</span>
                  </div>
                </div>

                <div id="admin-permissions" style="display: none">
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>تسجيل دخول كامل للنظام</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>إدارة جميع المستخدمين</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>إدارة النظام والإعدادات</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>تعيين الصلاحيات</span>
                  </div>
                  <div class="permission-item">
                    <i class="fas fa-check-circle"></i>
                    <span>عرض جميع المشاريع والتقارير</span>
                  </div>
                </div>
              </div>

              <div style="margin-top: 30px">
                <h4><i class="fas fa-info-circle"></i> معلومات مهمة</h4>
                <ul class="auth-benefits">
                  <li>
                    <i
                      class="fas fa-exclamation-triangle"
                      style="color: #ff9800"
                    ></i>
                    <div>
                      <strong>التحميل مقيد</strong>
                      <p>
                        لا يسمح النظام بتحميل المشاريع للحفاظ على حقوق الملكية
                        الفكرية
                      </p>
                    </div>
                  </li>

                  <li>
                    <i class="fas fa-users"></i>
                    <div>
                      <strong>الزوار</strong>
                      <p>يمكن للزوار تصفح المشاريع والبحث فيها فقط</p>
                    </div>
                  </li>

                  <li>
                    <i class="fas fa-shield-alt"></i>
                    <div>
                      <strong>الأمان</strong>
                      <p>جميع الحسابات محمية ويتم مراجعة الصلاحيات بدقة</p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <section class="page-content" style="margin-top: 40px">
          <h2 class="page-title">دليل المستخدمين</h2>

          <div
            style="
              display: grid;
              grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
              gap: 25px;
              margin-top: 30px;
            "
          >
            <div
              style="
                background: white;
                padding: 25px;
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                border-right: 4px solid #4caf50;
              "
            >
              <h4
                style="
                  color: #4caf50;
                  margin-bottom: 15px;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                "
              >
                <i class="fas fa-user-graduate"></i>
                الطلاب
              </h4>
              <p
                style="
                  color: var(--gray);
                  line-height: 1.7;
                  margin-bottom: 15px;
                "
              >
                يمكن للطلاب تسجيل الدخول لرفع مشاريعهم ومراجعتها، وكذلك تصفح
                المشاريع السابقة للاستفادة منها.
              </p>
              <ul
                style="
                  color: var(--gray);
                  line-height: 1.7;
                  padding-right: 20px;
                "
              >
                <li style="margin-bottom: 8px">إنشاء حساب جديد</li>
                <li style="margin-bottom: 8px">رفع مشاريع التخرج</li>
                <li style="margin-bottom: 8px">تصفح الأرشيف</li>
                <li>البحث المتقدم</li>
              </ul>
            </div>

            <div
              style="
                background: white;
                padding: 25px;
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                border-right: 4px solid #ff9800;
              "
            >
              <h4
                style="
                  color: #ff9800;
                  margin-bottom: 15px;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                "
              >
                <i class="fas fa-archive"></i>
                موظفو الأرشفة
              </h4>
              <p
                style="
                  color: var(--gray);
                  line-height: 1.7;
                  margin-bottom: 15px;
                "
              >
                مسؤولون عن مراجعة المشاريع المرفوعة وضمان جودتها وتصنيفها بشكل
                صحيح في الأرشيف.
              </p>
              <ul
                style="
                  color: var(--gray);
                  line-height: 1.7;
                  padding-right: 20px;
                "
              >
                <li style="margin-bottom: 8px">مراجعة المشاريع</li>
                <li style="margin-bottom: 8px">الموافقة على النشر</li>
                <li style="margin-bottom: 8px">تصنيف المشاريع</li>
                <li>إصدار التقارير</li>
              </ul>
            </div>

            <div
              style="
                background: white;
                padding: 25px;
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                border-right: 4px solid var(--secondary);
              "
            >
              <h4
                style="
                  color: var(--secondary);
                  margin-bottom: 15px;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                "
              >
                <i class="fas fa-user-shield"></i>
                مشرفو النظام
              </h4>
              <p
                style="
                  color: var(--gray);
                  line-height: 1.7;
                  margin-bottom: 15px;
                "
              >
                يتحكمون في إعدادات النظام وإدارة المستخدمين وضمان سير العمل بشكل
                صحيح.
              </p>
              <ul
                style="
                  color: var(--gray);
                  line-height: 1.7;
                  padding-right: 20px;
                "
              >
                <li style="margin-bottom: 8px">إدارة المستخدمين</li>
                <li style="margin-bottom: 8px">تعديل الإعدادات</li>
                <li style="margin-bottom: 8px">مراقبة النظام</li>
                <li>تحديد الصلاحيات</li>
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
            <h4
              style="
                color: var(--primary);
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
              "
            >
              <i class="fas fa-ban" style="color: var(--secondary)"></i>
              سياسة التحميل
            </h4>
            <p
              style="color: var(--gray); line-height: 1.8; margin-bottom: 15px"
            >
              للحفاظ على حقوق الملكية الفكرية للطلاب والباحثين،
              <strong>لا يسمح النظام بتحميل المشاريع</strong>. يمكن للجميع تصفح
              المشاريع وقراءة ملخصاتها، لكن التحميل متاح فقط للموظفين المخولين
              ولأغراض أرشيفية.
            </p>
            <p style="color: var(--gray); line-height: 1.8">
              للاستفادة من مشروع معين، يمكن التواصل مع المؤلفين مباشرة عبر
              المعلومات المتاحة في صفحة المشروع.
            </p>
          </div>
        </section>
         <?php include '../includes/footer.php'; ?>
      </div>

     
    </div>

    <!-- زر العودة للأعلى -->
    <div class="back-to-top">
      <i class="fas fa-arrow-up"></i>
    </div>

    <script src="../js/script.js?v=20240301-v3"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // --- تفاعل أزرار اختيار نوع المستخدم ---
        const userTypeBtns = document.querySelectorAll('.user-type-btn');
        const permissionTitle = document.getElementById('permission-title');
        const studentPermissions = document.getElementById('student-permissions');
        const archivePermissions = document.getElementById('archive-permissions');
        const adminPermissions = document.getElementById('admin-permissions');
        
        userTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // إزالة التفعيل من الكل
                userTypeBtns.forEach(b => b.classList.remove('active'));
                
                // تفعيل الزر الحالي
                this.classList.add('active');
                
                // الحصول على النوع
                const type = this.dataset.type;
                
                // إخفاء جميع الصلاحيات
                studentPermissions.style.display = 'none';
                archivePermissions.style.display = 'none';
                adminPermissions.style.display = 'none';
                
                // تحديث العنوان وإظهار الصلاحيات المناسبة
                if(type === 'student') {
                    permissionTitle.innerHTML = '<i class="fas fa-user-graduate"></i> صلاحيات الطالب';
                    permissionTitle.style.color = 'var(--primary)'; // أو اللون الأخضر #4caf50
                    studentPermissions.style.display = 'block';
                } else if(type === 'archive') {
                    permissionTitle.innerHTML = '<i class="fas fa-archive"></i> صلاحيات موظف الأرشفة';
                    permissionTitle.style.color = '#ff9800';
                    archivePermissions.style.display = 'block';
                } else if(type === 'admin') {
                    permissionTitle.innerHTML = '<i class="fas fa-user-shield"></i> صلاحيات مشرف النظام';
                    permissionTitle.style.color = '#fe798fff';
                    adminPermissions.style.display = 'block';
                }
                
                // تأثير حركي بسيط
                const activeSection = document.querySelector('.permissions-info > div[style*="block"]');
                if(activeSection) {
                    activeSection.style.opacity = '0';
                    activeSection.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        activeSection.style.transition = 'all 0.3s ease';
                        activeSection.style.opacity = '1';
                        activeSection.style.transform = 'translateY(0)';
                    }, 50);
                }
            });
        });

        // إظهار/إخفاء كلمة المرور
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");
        
        if(togglePassword && passwordInput){
            togglePassword.addEventListener("click", function () {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                this.querySelector("i").classList.toggle("fa-eye");
                this.querySelector("i").classList.toggle("fa-eye-slash");
            });
        }

        // AJAX Login Handler
        const loginForm = document.getElementById('loginForm');
        if(loginForm){
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const messageDiv = document.getElementById('loginMessage');
                const submitBtn = this.querySelector('button[type="submit"]');
                
                messageDiv.style.display = 'none';
                messageDiv.className = '';
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تسجيل الدخول...';

                const formData = new FormData(this);

                fetch('login_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        messageDiv.textContent = data.message;
                        messageDiv.style.display = 'block';
                        messageDiv.style.backgroundColor = '#d4edda';
                        messageDiv.style.color = '#155724';
                        
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        messageDiv.textContent = data.message;
                        messageDiv.style.display = 'block';
                        messageDiv.style.backgroundColor = '#f8d7da';
                        messageDiv.style.color = '#721c24';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> تسجيل الدخول';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.textContent = 'حدث خطأ في الاتصال. حاول مرة أخرى.';
                    messageDiv.style.display = 'block';
                    messageDiv.style.backgroundColor = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> تسجيل الدخول';
                });
            });
        }
      });
    </script>
  </body>
</html>
