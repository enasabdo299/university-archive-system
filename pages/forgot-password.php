<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>نسيت كلمة المرور - نظام أرشفة المشاريع الجامعية</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <style>
      /* أنماط إضافية خاصة بصفحة نسيت كلمة المرور */
      .forgot-password-container {
        max-width: 600px;
        margin: 40px auto;
      }

      .forgot-form-container {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 40px;
        border-top: 5px solid var(--secondary);
        position: relative;
        overflow: hidden;
      }

      .forgot-form-container::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(to left, var(--primary), var(--secondary));
      }

      .form-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
      }

      .form-steps::before {
        content: "";
        position: absolute;
        top: 20px;
        right: 0;
        left: 0;
        height: 3px;
        background: #e1e5eb;
        z-index: 1;
      }

      .step {
        text-align: center;
        position: relative;
        z-index: 2;
      }

      .step-circle {
        width: 40px;
        height: 40px;
        background: white;
        border: 3px solid #e1e5eb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        
        color: var(--gray);
        transition: var(--transition);
      }

      .step.active .step-circle {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
      }

      .step.completed .step-circle {
        background: var(--success);
        border-color: var(--success);
        color: white;
      }

      .step-label {
        
        color: var(--gray);
      }

      .step.active .step-label {
        color: var(--primary);
        
      }

      .form-step {
        display: none;
      }

      .form-step.active {
        display: block;
        animation: fadeIn 0.5s ease;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .form-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        gap: 15px;
      }

      .success-message {
        text-align: center;
        padding: 30px;
      }

      .success-icon {
        
        color: var(--success);
        margin-bottom: 20px;
      }

      .info-box {
        background: rgba(27, 54, 93, 0.05);
        border-radius: var(--border-radius);
        padding: 20px;
        margin: 20px 0;
        border-right: 4px solid var(--primary);
      }

      .info-box h4 {
        color: var(--primary);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .form-group {
        margin-bottom: 20px;
      }

      .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--primary);
        
        
      }

      .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e1e5eb;
        border-radius: var(--border-radius);
        
        transition: var(--transition);
        background: #f8f9fa;
      }

      .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(27, 54, 93, 0.1);
      }

      .password-strength {
        margin-top: 10px;
      }

      .strength-meter {
        height: 5px;
        background: #e1e5eb;
        border-radius: 3px;
        margin-bottom: 5px;
        overflow: hidden;
      }

      .strength-fill {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease;
      }

      .strength-text {
        
        color: var(--gray);
      }

      .password-toggle {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--gray);
      }

      .form-group.password-group {
        position: relative;
      }

      .verification-code {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 20px 0;
      }

      .code-input {
        width: 50px;
        height: 60px;
        text-align: center;
        
        
        border: 2px solid #e1e5eb;
        border-radius: var(--border-radius);
        background: #f8f9fa;
        transition: var(--transition);
      }

      .code-input:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(27, 54, 93, 0.1);
      }

      .resend-code {
        text-align: center;
        margin: 20px 0;
        color: var(--gray);
      }

      .resend-code a {
        color: var(--primary);
        
        text-decoration: none;
      }

      .resend-code a:hover {
        text-decoration: underline;
      }

      .timer {
        
        color: var(--secondary);
      }

      .account-type {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 25px;
      }

      .account-type-btn {
        padding: 12px 10px;
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

      .account-type-btn i {
        
      }

      .account-type-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
      }

      .account-type-btn:hover:not(.active) {
        border-color: var(--primary);
      }

      @media (max-width: 768px) {
        .forgot-form-container {
          padding: 25px;
        }

        .account-type {
          grid-template-columns: 1fr;
        }

        .code-input {
          width: 40px;
          height: 50px;
          
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
              <a href="login.php"
                ><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a
              >
            </li>
            <!-- <li>
              <a href="forgot-password.php" class="active"
                ><i class="fas fa-key"></i> نسيت كلمة المرور</a
              >
            </li> -->
          </ul>
        </nav>
      </div>

      <div class="container">
        <section class="hero">
          <h2>استعادة كلمة المرور</h2>
          <p>استعد الوصول إلى حسابك في نظام أرشفة المشاريع الجامعية</p>
        </section>

        <div class="forgot-password-container">
          <div class="forgot-form-container">
            <h2 class="page-title">استعادة كلمة المرور</h2>

            <div class="form-steps">
              <div class="step active" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-label">تحديد الحساب</div>
              </div>
              <div class="step" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-label">التحقق</div>
              </div>
              <div class="step" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-label">كلمة مرور جديدة</div>
              </div>
            </div>

            <form id="forgotPasswordForm">
              <!-- الخطوة 1: تحديد الحساب -->
              <div class="form-step active" id="step1">
                <div class="account-type">
                  <button
                    type="button"
                    class="account-type-btn student active"
                    data-type="student"
                  >
                    <i class="fas fa-user-graduate"></i>
                    <span>طالب</span>
                  </button>
                  <button
                    type="button"
                    class="account-type-btn archive"
                    data-type="archive"
                  >
                    <i class="fas fa-archive"></i>
                    <span>موظف أرشفة</span>
                  </button>
                  <button
                    type="button"
                    class="account-type-btn admin"
                    data-type="admin"
                  >
                    <i class="fas fa-user-shield"></i>
                    <span>مشرف النظام</span>
                  </button>
                </div>

                <div class="form-group">
                  <label for="email">البريد الإلكتروني</label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="أدخل البريد الإلكتروني المسجل"
                    required
                  />
                  <small
                    style="color: var(--gray); display: block; margin-top: 5px"
                  >
                    أدخل البريد الإلكتروني المرتبط بحسابك
                  </small>
                </div>

                <div class="info-box">
                  <h4><i class="fas fa-info-circle"></i> معلومات مهمة</h4>
                  <p
                    style="
                      color: var(--gray);
                      
                      line-height: 1.6;
                    "
                  >
                    ستصلك رسالة تحوي رمز التحقق إلى بريدك الإلكتروني. تأكد من
                    صندوق البريد الإلكتروني والرسائل غير المرغوب فيها.
                  </p>
                </div>

                <div class="form-navigation">
                  <a href="login.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة لتسجيل الدخول
                  </a>
                  <button type="button" class="btn btn-primary btn-next">
                    التالي <i class="fas fa-arrow-left"></i>
                  </button>
                </div>
              </div>

              <!-- الخطوة 2: التحقق -->
              <div class="form-step" id="step2">
                <div class="info-box">
                  <h4><i class="fas fa-envelope"></i> تم إرسال رمز التحقق</h4>
                  <p
                    style="
                      color: var(--gray);
                      
                      line-height: 1.6;
                    "
                  >
                    تم إرسال رمز تحقق مكون من 6 أرقام إلى بريدك الإلكتروني. أدخل
                    الرمز أدناه للتحقق من هويتك.
                  </p>
                  <p
                    style="
                      color: var(--primary);
                      
                      margin-top: 10px;
                    "
                    id="sentToEmail"
                  >
                    example@eiu.edu.ye
                  </p>
                </div>

                <div class="verification-code">
                  <input
                    type="text"
                    class="code-input"
                    maxlength="1"
                    data-index="1"
                    required
                  />
                  <input
                    type="text"
                    class="code-input"
                    maxlength="1"
                    data-index="2"
                    required
                  />
                  <input
                    type="text"
                    class="code-input"
                    maxlength="1"
                    data-index="3"
                    required
                  />
                  <input
                    type="text"
                    class="code-input"
                    maxlength="1"
                    data-index="4"
                    required
                  />
                  <input
                    type="text"
                    class="code-input"
                    maxlength="1"
                    data-index="5"
                    required
                  />
                  <input
                    type="text"
                    class="code-input"
                    maxlength="1"
                    data-index="6"
                    required
                  />
                </div>

                <div class="resend-code">
                  <p>
                    لم تستلم الرمز؟
                    <a href="#" id="resendLink">إعادة إرسال الرمز</a>
                    <span id="timer" class="timer"> (02:00)</span>
                  </p>
                </div>

                <div class="form-navigation">
                  <button type="button" class="btn btn-secondary btn-prev">
                    <i class="fas fa-arrow-right"></i> السابق
                  </button>
                  <button type="button" class="btn btn-primary btn-next">
                    التحقق <i class="fas fa-check"></i>
                  </button>
                </div>
              </div>

              <!-- الخطوة 3: كلمة مرور جديدة -->
              <div class="form-step" id="step3">
                <div class="info-box">
                  <h4>
                    <i
                      class="fas fa-check-circle"
                      style="color: var(--success)"
                    ></i>
                    تم التحقق بنجاح
                  </h4>
                  <p
                    style="
                      color: var(--gray);
                      
                      line-height: 1.6;
                    "
                  >
                    يمكنك الآن إنشاء كلمة مرور جديدة لحسابك. تأكد من اختيار كلمة
                    مرور قوية.
                  </p>
                </div>

                <div class="form-group password-group">
                  <label for="newPassword">كلمة المرور الجديدة</label>
                  <input
                    type="password"
                    id="newPassword"
                    name="newPassword"
                    placeholder="أدخل كلمة مرور جديدة"
                    required
                  />
                  <span class="password-toggle" id="toggleNewPassword">
                    <i class="fas fa-eye"></i>
                  </span>
                  <div class="password-strength">
                    <div class="strength-meter">
                      <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                  </div>
                </div>

                <div class="form-group password-group">
                  <label for="confirmNewPassword"
                    >تأكيد كلمة المرور الجديدة</label
                  >
                  <input
                    type="password"
                    id="confirmNewPassword"
                    name="confirmNewPassword"
                    placeholder="أعد إدخال كلمة المرور الجديدة"
                    required
                  />
                  <span class="password-toggle" id="toggleConfirmNewPassword">
                    <i class="fas fa-eye"></i>
                  </span>
                  <small
                    id="passwordMatchFeedback"
                    style="display: block; margin-top: 5px"
                  ></small>
                </div>

                <div class="form-group">
                  <div
                    style="display: flex; align-items: flex-start; gap: 10px"
                  >
                    <input type="checkbox" id="logoutAll" name="logoutAll" />
                    <label
                      for="logoutAll"
                      style=" margin-bottom: 0"
                    >
                      تسجيل الخروج من جميع الأجهزة الأخرى
                    </label>
                  </div>
                  <small
                    style="color: var(--gray); display: block; margin-top: 5px"
                  >
                    يوصى باختيار هذا الخيار لأسباب أمنية
                  </small>
                </div>

                <div class="form-navigation">
                  <button type="button" class="btn btn-secondary btn-prev">
                    <i class="fas fa-arrow-right"></i> السابق
                  </button>
                  <button type="submit" class="btn btn-primary">
                    حفظ كلمة المرور الجديدة <i class="fas fa-save"></i>
                  </button>
                </div>
              </div>

              <!-- خطوة النجاح -->
              <div class="form-step" id="step4" style="display: none">
                <div class="success-message">
                  <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <h3 style="color: var(--success); margin-bottom: 15px">
                    تم تغيير كلمة المرور بنجاح!
                  </h3>
                  <p
                    style="
                      color: var(--gray);
                      margin-bottom: 20px;
                      line-height: 1.6;
                    "
                  >
                    تم تغيير كلمة المرور لحسابك بنجاح. يمكنك الآن تسجيل الدخول
                    باستخدام كلمة المرور الجديدة.
                  </p>

                  <div class="info-box" style="text-align: right">
                    <h4><i class="fas fa-shield-alt"></i> نصائح أمنية</h4>
                    <ul
                      style="
                        color: var(--gray);
                        
                        line-height: 1.6;
                        padding-right: 15px;
                      "
                    >
                      <li style="margin-bottom: 8px">
                        لا تشارك كلمة المرور مع أي شخص
                      </li>
                      <li style="margin-bottom: 8px">
                        استخدم كلمة مرور مختلفة لكل حساب
                      </li>
                      <li style="margin-bottom: 8px">
                        تغيير كلمة المرور بشكل دوري
                      </li>
                      <li>تسجيل الخروج بعد الانتهاء من استخدام النظام</li>
                    </ul>
                  </div>

                  <div style="margin-top: 30px">
                    <a
                      href="login.php"
                      class="btn btn-primary"
                      style="margin-bottom: 10px"
                    >
                      <i class="fas fa-sign-in-alt"></i> تسجيل الدخول الآن
                    </a>
                    <a href="../index.php" class="btn btn-secondary">
                      <i class="fas fa-home"></i> العودة للرئيسية
                    </a>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="page-content" style="margin-top: 40px">
            <h3
              style="
                color: var(--primary);
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
              "
            >
              <i class="fas fa-question-circle"></i> الأسئلة الشائعة
            </h3>

            <div
              style="
                background: white;
                border-radius: var(--border-radius);
                padding: 20px;
                box-shadow: var(--box-shadow);
              "
            >
              <div style="margin-bottom: 20px">
                <h4
                  style="
                    color: var(--primary);
                    margin-bottom: 8px;
                    
                  "
                >
                  لماذا لم أستلم رمز التحقق؟
                </h4>
                <p
                  style="color: var(--gray);  line-height: 1.6"
                >
                  تحقق من صندوق البريد الإلكتروني والرسائل غير المرغوب فيها. إذا
                  لم تستلم الرمز بعد دقائق قليلة، يمكنك إعادة إرساله.
                </p>
              </div>

              <div style="margin-bottom: 20px">
                <h4
                  style="
                    color: var(--primary);
                    margin-bottom: 8px;
                    
                  "
                >
                  ما الذي يجب فعله إذا نسيت البريد الإلكتروني؟
                </h4>
                <p
                  style="color: var(--gray);  line-height: 1.6"
                >
                  إذا كنت طالباً، يمكنك التواصل مع مكتب التسجيل في كليتك. موظفو
                  الأرشفة ومشرفو النظام يجب أن يتواصلوا مع إدارة النظام.
                </p>
              </div>

              <div>
                <h4
                  style="
                    color: var(--primary);
                    margin-bottom: 8px;
                    
                  "
                >
                  كم مرة يمكنني تغيير كلمة المرور؟
                </h4>
                <p
                  style="color: var(--gray);  line-height: 1.6"
                >
                  يمكنك تغيير كلمة المرور مرة كل 24 ساعة لأسباب أمنية. إذا كنت
                  بحاجة إلى تغييرها أكثر من ذلك، يجب التواصل مع الدعم الفني.
                </p>
              </div>
            </div>

            <div
              style="
                margin-top: 30px;
                padding: 20px;
                background: rgba(27, 54, 93, 0.05);
                border-radius: var(--border-radius);
              "
            >
              <h4
                style="
                  color: var(--primary);
                  margin-bottom: 10px;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                "
              >
                <i class="fas fa-headset"></i> الدعم الفني
              </h4>
              <p style="color: var(--gray);  line-height: 1.6">
                إذا واجهت أي مشكلة في استعادة حسابك، يمكنك التواصل مع فريق الدعم
                الفني:
              </p>
              <div
                style="
                  display: flex;
                  gap: 15px;
                  margin-top: 10px;
                  flex-wrap: wrap;
                "
              >
                <div>
                  <strong>البريد الإلكتروني:</strong>
                  <p style="color: var(--primary)">
                    support-archive@eiu.edu.ye
                  </p>
                </div>
                <div>
                  <strong>الهاتف:</strong>
                  <p style="color: var(--primary)">
                    +967 1 234 567 (تحويلة 124)
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      
    

    <!-- زر العودة للأعلى -->
    <div class="back-to-top">
      <i class="fas fa-arrow-up"></i>
    </div>

    <script src="../js/script.js?v=20240301-v3"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // متغيرات النظام
        let currentStep = 1;
        const totalSteps = 3;
        let timerInterval;
        let timeLeft = 120; // 2 دقيقة بالثواني
        let verificationCode = "";
        let accountType = "student";
        let userEmail = "";

        // عناصر واجهة المستخدم
        const steps = document.querySelectorAll(".step");
        const formSteps = document.querySelectorAll(".form-step");
        const nextButtons = document.querySelectorAll(".btn-next");
        const prevButtons = document.querySelectorAll(".btn-prev");
        const forgotPasswordForm =
          document.getElementById("forgotPasswordForm");
        const sentToEmail = document.getElementById("sentToEmail");
        const timerElement = document.getElementById("timer");
        const resendLink = document.getElementById("resendLink");
        const accountTypeBtns = document.querySelectorAll(".account-type-btn");
        const codeInputs = document.querySelectorAll(".code-input");
        const emailInput = document.getElementById("email");

        // تهيئة الخطوات
        function initializeSteps() {
          // تحديث حالة الخطوات
          steps.forEach((step) => {
            const stepNum = parseInt(step.dataset.step);

            step.classList.remove("active", "completed");

            if (stepNum < currentStep) {
              step.classList.add("completed");
            } else if (stepNum === currentStep) {
              step.classList.add("active");
            }
          });

          // إظهار الخطوة الحالية
          formSteps.forEach((step) => {
            step.classList.remove("active");
            if (step.id === `step${currentStep}`) {
              step.classList.add("active");
            }
          });
        }

        // الانتقال للخطوة التالية
        function goToNextStep() {
          if (currentStep < totalSteps) {
            // التحقق من صحة البيانات قبل الانتقال
            if (validateStep(currentStep)) {
              // حفظ البيانات قبل الانتقال
              if (currentStep === 1) {
                userEmail = emailInput.value;
                sentToEmail.textContent = userEmail;
                // إنشاء رمز تحقق عشوائي (للتجربة فقط)
                verificationCode = Math.floor(
                  100000 + Math.random() * 900000
                ).toString();
                console.log("رمز التحقق (للتجربة):", verificationCode);
                startTimer();
              }

              currentStep++;
              initializeSteps();
              scrollToTop();
            }
          }
        }

        // العودة للخطوة السابقة
        function goToPrevStep() {
          if (currentStep > 1) {
            currentStep--;
            initializeSteps();
            scrollToTop();
          }
        }

        // التحقق من صحة البيانات في الخطوة
        function validateStep(stepNum) {
          let isValid = true;
          const stepElement = document.getElementById(`step${stepNum}`);

          if (stepNum === 1) {
            // التحقق من البريد الإلكتروني
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email) {
              emailInput.style.borderColor = "var(--danger)";
              alert("يرجى إدخال البريد الإلكتروني");
              isValid = false;
            } else if (!emailRegex.test(email)) {
              emailInput.style.borderColor = "var(--danger)";
              alert("يرجى إدخال بريد إلكتروني صحيح");
              isValid = false;
            } else {
              emailInput.style.borderColor = "";
            }
          }

          if (stepNum === 2) {
            // التحقق من رمز التحقق
            let enteredCode = "";
            codeInputs.forEach((input) => {
              enteredCode += input.value;
            });

            if (enteredCode.length !== 6) {
              alert("يرجى إدخال رمز التحقق المكون من 6 أرقام");
              isValid = false;
              highlightCodeInputs("var(--danger)");
            } else if (enteredCode !== verificationCode) {
              alert("رمز التحقق غير صحيح");
              isValid = false;
              highlightCodeInputs("var(--danger)");
            } else {
              highlightCodeInputs("var(--success)");
              isValid = true;
            }
          }

          if (stepNum === 3) {
            // التحقق من كلمة المرور الجديدة
            const newPassword = document.getElementById("newPassword").value;
            const confirmNewPassword =
              document.getElementById("confirmNewPassword").value;

            if (!newPassword || !confirmNewPassword) {
              alert("يرجى ملء جميع الحقول");
              isValid = false;
            } else if (newPassword !== confirmNewPassword) {
              alert("كلمات المرور غير متطابقة");
              isValid = false;
            } else if (newPassword.length < 8) {
              alert("كلمة المرور يجب أن تكون 8 أحرف على الأقل");
              isValid = false;
            } else {
              isValid = true;
            }
          }

          return isValid;
        }

        // تسليط الضوء على حقول رمز التحقق
        function highlightCodeInputs(color) {
          codeInputs.forEach((input) => {
            input.style.borderColor = color;
          });
        }

        // بدء المؤقت
        function startTimer() {
          clearInterval(timerInterval);
          timeLeft = 120;
          updateTimerDisplay();
          timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();

            if (timeLeft <= 0) {
              clearInterval(timerInterval);
              resendLink.style.display = "inline";
              timerElement.textContent = " (انتهى الوقت)";
            }
          }, 1000);
        }

        // تحديث عرض المؤقت
        function updateTimerDisplay() {
          const minutes = Math.floor(timeLeft / 60);
          const seconds = timeLeft % 60;
          timerElement.textContent = ` (${minutes
            .toString()
            .padStart(2, "0")}:${seconds.toString().padStart(2, "0")})`;
        }

        // التمرير للأعلى
        function scrollToTop() {
          window.scrollTo({
            top:
              document.querySelector(".forgot-form-container").offsetTop - 100,
            behavior: "smooth",
          });
        }

        // إضافة مستمعي الأحداث لأزرار التنقل
        nextButtons.forEach((btn) => {
          btn.addEventListener("click", goToNextStep);
        });

        prevButtons.forEach((btn) => {
          btn.addEventListener("click", goToPrevStep);
        });

        // تبديل نوع الحساب
        accountTypeBtns.forEach((btn) => {
          btn.addEventListener("click", function () {
            accountTypeBtns.forEach((b) => b.classList.remove("active"));
            this.classList.add("active");
            accountType = this.getAttribute("data-type");
          });
        });

        // إدارة حقول رمز التحقق
        codeInputs.forEach((input, index) => {
          input.addEventListener("input", function (e) {
            const value = e.target.value;

            // السماح فقط بالأرقام
            if (!/^\d*$/.test(value)) {
              e.target.value = value.replace(/[^\d]/g, "");
              return;
            }

            // الانتقال للحقل التالي عند إدخال رقم
            if (value.length === 1 && index < codeInputs.length - 1) {
              codeInputs[index + 1].focus();
            }

            // إزالة التحديد عند حذف رقم
            if (
              value.length === 0 &&
              index > 0 &&
              e.inputType === "deleteContentBackward"
            ) {
              codeInputs[index - 1].focus();
            }
          });

          // السماح بالتنقل باستخدام الأسهم
          input.addEventListener("keydown", function (e) {
            if (e.key === "ArrowRight" && index < codeInputs.length - 1) {
              codeInputs[index + 1].focus();
            } else if (e.key === "ArrowLeft" && index > 0) {
              codeInputs[index - 1].focus();
            }
          });
        });

        // إعادة إرسال رمز التحقق
        resendLink.addEventListener("click", function (e) {
          e.preventDefault();

          if (timeLeft <= 0 || confirm("هل تريد إعادة إرسال رمز التحقق؟")) {
            verificationCode = Math.floor(
              100000 + Math.random() * 900000
            ).toString();
            console.log("رمز التحقق الجديد (للتجربة):", verificationCode);
            alert("تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني");
            startTimer();
            resendLink.style.display = "none";

            // مسح حقول الإدخال
            codeInputs.forEach((input) => {
              input.value = "";
              input.style.borderColor = "";
            });
            codeInputs[0].focus();
          }
        });

        // إظهار/إخفاء كلمة المرور
        const toggleNewPassword = document.getElementById("toggleNewPassword");
        const newPasswordInput = document.getElementById("newPassword");
        const toggleConfirmNewPassword = document.getElementById(
          "toggleConfirmNewPassword"
        );
        const confirmNewPasswordInput =
          document.getElementById("confirmNewPassword");

        toggleNewPassword.addEventListener("click", function () {
          const type =
            newPasswordInput.getAttribute("type") === "password"
              ? "text"
              : "password";
          newPasswordInput.setAttribute("type", type);
          this.querySelector("i").classList.toggle("fa-eye");
          this.querySelector("i").classList.toggle("fa-eye-slash");
        });

        toggleConfirmNewPassword.addEventListener("click", function () {
          const type =
            confirmNewPasswordInput.getAttribute("type") === "password"
              ? "text"
              : "password";
          confirmNewPasswordInput.setAttribute("type", type);
          this.querySelector("i").classList.toggle("fa-eye");
          this.querySelector("i").classList.toggle("fa-eye-slash");
        });

        // قوة كلمة المرور
        newPasswordInput.addEventListener("input", function () {
          const password = this.value;
          const strengthFill = document.getElementById("strengthFill");
          const strengthText = document.getElementById("strengthText");

          let strength = 0;
          let text = "";
          let color = "";

          if (password.length >= 8) strength++;
          if (/[A-Z]/.test(password)) strength++;
          if (/[a-z]/.test(password)) strength++;
          if (/[0-9]/.test(password)) strength++;
          if (/[^A-Za-z0-9]/.test(password)) strength++;

          switch (strength) {
            case 0:
            case 1:
              text = "ضعيفة جداً";
              color = "#dc3545";
              break;
            case 2:
              text = "ضعيفة";
              color = "#fd7e14";
              break;
            case 3:
              text = "متوسطة";
              color = "#ffc107";
              break;
            case 4:
              text = "قوية";
              color = "#28a745";
              break;
            case 5:
              text = "قوية جداً";
              color = "#20c997";
              break;
          }

          strengthFill.style.width = `${(strength / 5) * 100}%`;
          strengthFill.style.backgroundColor = color;
          strengthText.textContent = `قوة كلمة المرور: ${text}`;
          strengthText.style.color = color;
        });

        // تطابق كلمات المرور
        confirmNewPasswordInput.addEventListener("input", function () {
          const password = document.getElementById("newPassword").value;
          const feedback = document.getElementById("passwordMatchFeedback");

          if (this.value === password) {
            this.style.borderColor = "var(--success)";
            feedback.textContent = "كلمات المرور متطابقة";
            feedback.style.color = "var(--success)";
          } else if (this.value) {
            this.style.borderColor = "var(--danger)";
            feedback.textContent = "كلمات المرور غير متطابقة";
            feedback.style.color = "var(--danger)";
          } else {
            this.style.borderColor = "";
            feedback.textContent = "";
          }
        });

        // معالجة نموذج استعادة كلمة المرور
        forgotPasswordForm.addEventListener("submit", function (e) {
          e.preventDefault();

          // التحقق من صحة الخطوة الأخيرة
          if (validateStep(currentStep)) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerphp;

            submitBtn.innerphp =
              '<i class="fas fa-spinner fa-spin"></i> جاري حفظ كلمة المرور...';
            submitBtn.disabled = true;

            setTimeout(() => {
              // عرض رسالة النجاح
              document.getElementById(`step${currentStep}`).style.display =
                "none";
              document.getElementById("step4").style.display = "block";

              // إعادة تعيين النموذج
              submitBtn.innerphp = originalText;
              submitBtn.disabled = false;

              // تسجيل البيانات في وحدة التحكم (للتجربة فقط)
              console.log("بيانات تغيير كلمة المرور:", {
                accountType: accountType,
                email: userEmail,
                passwordChanged: true,
                logoutAll: document.getElementById("logoutAll").checked,
              });

              // هنا في التطبيق الحقيقي سيتم إرسال البيانات إلى الخادم
              // fetch('/api/reset-password', {
              //     method: 'POST',
              //     headers: { 'Content-Type': 'application/json' },
              //     body: JSON.stringify({
              //         email: userEmail,
              //         newPassword: document.getElementById('newPassword').value,
              //         logoutAll: document.getElementById('logoutAll').checked
              //     })
              // });
            }, 2000);
          }
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

        // تطبيق التأثير على عناصر الصفحة
        const animatedElements = document.querySelectorAll(
          ".forgot-form-container, .page-content > div"
        );
        animatedElements.forEach((el) => {
          el.style.opacity = "0";
          el.style.transform = "translateY(20px)";
          el.style.transition = "opacity 0.5s ease, transform 0.5s ease";
          observer.observe(el);
        });

        // إظهار العناصر بعد تحميل الصفحة مباشرة
        setTimeout(() => {
          animatedElements.forEach((el) => {
            el.style.opacity = "1";
            el.style.transform = "translateY(0)";
          });
        }, 300);

        // تهيئة الخطوات
        initializeSteps();

        // تهيئة المؤقت
        startTimer();
      });
    </script>
  </body>
</html>
