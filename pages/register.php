<?php session_start();
if(isset($_SESSION['user_id'])){ header("Location: ../index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>إنشاء حساب جديد - نظام أرشفة المشاريع</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=1771762380" />
    <style>
        /* أنماط مبسطة للتسجيل */
        .simple-register {
            max-width: 500px;
            margin: 50px auto;
        }
        
        .register-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 40px;
            border-top: 5px solid var(--primary);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header i {
            
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .register-header h1 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: var(--gray);
            
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
            border-radius: 8px;
            
            transition: var(--transition);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(27, 54, 93, 0.1);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .input-with-icon input {
            padding-right: 15px;
            padding-left: 45px;
        }
        
        .password-toggle {
            position: absolute;
            left: 45px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray);
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 25px 0;
        }
        
        .terms-checkbox input {
            margin-top: 3px;
        }
        
        .terms-checkbox label {
            
            color: var(--gray);
            
            line-height: 1.5;
        }
        
        .terms-checkbox a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .terms-checkbox a:hover {
            text-decoration: underline;
        }
        
        .register-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            
            
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: var(--transition);
        }
        
        .register-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .register-btn:disabled {
            background: var(--gray);
            cursor: not-allowed;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #eee;
            color: var(--gray);
        }
        
        .login-link a {
            color: var(--primary);
            
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .success-message {
            text-align: center;
            padding: 40px 20px;
        }
        
        .success-message i {
            
            color: var(--success);
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-right: 4px solid #dc3545;
            display: none;
        }
        
        .success-message {
            display: none;
        }
        
        .password-strength {
            height: 4px;
            background: #e1e5eb;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .simple-register {
                margin: 20px auto;
                padding: 0 15px;
            }
            
            .register-card {
                padding: 25px;
            }
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
    <div class="container">
        <!-- الهيدر المبسط -->
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
                    
                    <div class="header-actions">
                        <div class="back-button-wrapper">
                            <a href="login.php" class="btn btn-secondary back-btn">
                                عودة <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <nav class="fixed-nav">
                <ul class="nav-links">
                   
                    <li><a href="register.php" class="active"><i class="fas fa-user-plus"></i> إنشاء حساب</a></li>
                </ul>
            </nav>
        </div>

        <!-- نموذج التسجيل المبسط -->
        <div class="simple-register">
            <div class="register-card">
                <div class="register-header">
                    <i class="fas fa-user-plus"></i>
                    <h1>إنشاء حساب جديد</h1>
                    <p>سجل حسابك للبدء في رفع مشاريعك الجامعية</p>
                </div>
                
                <!-- رسالة الخطأ -->
                <div id="registerMessage" class="error-message"></div>
                
                <!-- النموذج الأساسي -->
                <form id="registerForm" method="POST" action="register_process.php">
                    <!-- الاسم الكامل -->
                    <div class="form-group">
                        <label for="fullName">الاسم الكامل</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="fullName" name="fullName" 
                                   placeholder="أدخل اسمك الكامل" required>
                        </div>
                    </div>
                    
                    <!-- اسم المستخدم -->
                    <div class="form-group">
                        <label for="username">اسم المستخدم</label>
                        <div class="input-with-icon">
                            <i class="fas fa-at"></i>
                            <input type="text" id="username" name="username" 
                                   placeholder="اختر اسم مستخدم فريد" required>
                        </div>
                        <small id="usernameFeedback" style="color: var(--gray); "></small>
                    </div>
                    
                    <!-- البريد الإلكتروني -->
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" 
                                   placeholder="example@email.com" required>
                        </div>
                    </div>
                    
                    <!-- رقم الهاتف -->
                    <div class="form-group">
                        <label for="phone">رقم الهاتف</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" 
                                   placeholder="1234567890" required>
                        </div>
                    </div>
                    
                    <!-- كلمة المرور -->
                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <span class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                            <input type="password" id="password" name="password" 
                                   placeholder="أنشئ كلمة مرور قوية" required>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                        <small id="passwordHint" style="color: var(--gray); ">
                            يجب أن تكون 8 أحرف على الأقل وتحتوي على حروف وأرقام
                        </small>
                    </div>
                    
                    <!-- تأكيد كلمة المرور -->
                    <div class="form-group">
                        <label for="confirmPassword">تأكيد كلمة المرور</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <span class="password-toggle" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </span>
                            <input type="password" id="confirmPassword" name="confirmPassword" 
                                   placeholder="أعد إدخال كلمة المرور" required>
                        </div>
                        <small id="passwordMatchFeedback" style=""></small>
                    </div>
                    
                    <!-- شروط الاستخدام -->
                    <div class="terms-checkbox">
                        <input type="checkbox" id="agreeTerms" name="agreeTerms" required>
                        <label for="agreeTerms">
                            أوافق على 
                            <a href="terms.php">شروط الاستخدام</a> 
                            و 
                            <a href="privacy.php">سياسة الخصوصية</a>
                        </label>
                    </div>
                    
                    <!-- زر التسجيل -->
                    <button type="submit" class="register-btn" id="registerBtn">
                        <i class="fas fa-user-plus"></i>
                        <span id="btnText">إنشاء حساب</span>
                    </button>
                </form>
                
                <!-- رابط تسجيل الدخول -->
                <div class="login-link">
                    لديك حساب بالفعل؟ 
                    <a href="login.php">سجل الدخول الآن</a>
                </div>
                
                <!-- رسالة النجاح (تظهر بعد التسجيل) -->
                <div id="successMessage" class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <h2>تم إنشاء حسابك بنجاح!</h2>
                    <p>حسابك قيد الانتظار لموافقة الإدارة</p>
                    <!-- <div style="margin-top: 30px;">
                        <a href="login.php" class="btn btn-primary" style="margin-right: 10px;">
                            <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                        </a>
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="fas fa-home"></i> العودة للرئيسية
                        </a>
                    </div> -->
                </div>
            </div>
        </div>
        
        
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // عناصر DOM
            const registerForm = document.getElementById('registerForm');
            const registerBtn = document.getElementById('registerBtn');
            const btnText = document.getElementById('btnText');
            const registerMessage = document.getElementById('registerMessage');
            const successMessage = document.getElementById('successMessage');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const strengthBar = document.getElementById('strengthBar');
            const usernameFeedback = document.getElementById('usernameFeedback');
            const passwordMatchFeedback = document.getElementById('passwordMatchFeedback');
            
            // إظهار/إخفاء كلمة المرور
            document.getElementById('togglePassword').addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
            
            document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
                const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
                confirmPasswordInput.type = type;
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
            
            // قوة كلمة المرور
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                const width = (strength / 5) * 100;
                strengthBar.style.width = width + '%';
                
                if (strength < 2) {
                    strengthBar.style.backgroundColor = '#dc3545';
                } else if (strength < 4) {
                    strengthBar.style.backgroundColor = '#ffc107';
                } else {
                    strengthBar.style.backgroundColor = '#28a745';
                }
            });
            
            // تطابق كلمات المرور
            confirmPasswordInput.addEventListener('input', function() {
                const password = passwordInput.value;
                
                if (this.value === password && password !== '') {
                    this.style.borderColor = '#28a745';
                    passwordMatchFeedback.textContent = 'كلمات المرور متطابقة';
                    passwordMatchFeedback.style.color = '#28a745';
                } else if (this.value !== '') {
                    this.style.borderColor = '#dc3545';
                    passwordMatchFeedback.textContent = 'كلمات المرور غير متطابقة';
                    passwordMatchFeedback.style.color = '#dc3545';
                } else {
                    this.style.borderColor = '';
                    passwordMatchFeedback.textContent = '';
                }
            });
            
            // تحقق من اسم المستخدم
            document.getElementById('username').addEventListener('input', function() {
                const username = this.value.trim();
                
                if (username.length < 3) {
                    usernameFeedback.textContent = 'يجب أن يكون 3 أحرف على الأقل';
                    usernameFeedback.style.color = '#dc3545';
                    this.style.borderColor = '#dc3545';
                } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                    usernameFeedback.textContent = 'يمكن أن يحتوي على أحرف إنجليزية وأرقام و _ فقط';
                    usernameFeedback.style.color = '#dc3545';
                    this.style.borderColor = '#dc3545';
                } else {
                    usernameFeedback.textContent = 'اسم المستخدم متاح';
                    usernameFeedback.style.color = '#28a745';
                    this.style.borderColor = '#28a745';
                }
            });
            
            // معالجة النموذج
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // التحقق الأساسي
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (password !== confirmPassword) {
                    registerMessage.textContent = 'كلمات المرور غير متطابقة!';
                    registerMessage.style.display = 'block';
                    return;
                }
                
                if (password.length < 8) {
                    registerMessage.textContent = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
                    registerMessage.style.display = 'block';
                    return;
                }
                
                // تعطيل الزر وعرض التحميل
                registerBtn.disabled = true;
                btnText.textContent = 'جاري إنشاء الحساب...';
                
                // إرسال البيانات
                const formData = new FormData(this);
                
                fetch('register_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // إخفاء النموذج وإظهار رسالة النجاح
                        registerForm.style.display = 'none';
                        successMessage.style.display = 'block';
                        registerMessage.style.display = 'none';
                    } else {
                        // عرض رسالة الخطأ
                        registerMessage.textContent = data.message;
                        registerMessage.style.display = 'block';
                        
                        // إعادة تمكين الزر
                        registerBtn.disabled = false;
                        btnText.textContent = 'إنشاء حساب';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    registerMessage.textContent = 'حدث خطأ في الاتصال. حاول مرة أخرى.';
                    registerMessage.style.display = 'block';
                    
                    // إعادة تمكين الزر
                    registerBtn.disabled = false;
                    btnText.textContent = 'إنشاء حساب';
                });
            });
        });
    </script>
</body>
</html>