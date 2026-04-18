<?php
session_start();
require_once '../includes/db_connect.php';

// Access control: Admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$full_name_session = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مستخدم جديد - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3">
    <link rel="stylesheet" href="../css/admin.css?v=20240303-v1">
    <style>
        .create-user-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            border-top: 5px solid var(--primary);
        }
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .form-section h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        .student-only-fields {
            display: none;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-submit:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Reusing back button styles */
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
            text-decoration: none;
            font-weight: 600;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background: var(--primary) !important;
            color: white !important;
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .back-button-wrapper { top: 10px; left: 10px; }
            .back-btn { padding: 5px 12px !important; font-size: 0.8rem !important; }
        }
    </style>
</head>
<body>
    <div class="header-wrapper">
        <header class="fixed-header">
            <div class="header-content">
                <div class="logo-container">
                    <div class="logo-img">
                        <img src="../img/1765888818874.jpg" alt="شعار الجامعة">
                    </div>
                    <div class="logo-text">
                        <div class="university-name">الجامعة الإماراتية الدولية</div>
                        <h1>نظام أرشفة المشاريع الجامعية</h1>
                    </div>
                </div>
                <div class="back-button-wrapper">
                    <a href="admin_dashboard.php" class="back-btn">
                        عودة <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </header>
    </div>

    <div class="container" style="margin-top: 50px;">
        <div class="create-user-container">
            <div id="responseMessage" class="message"></div>
            
            <form id="createUserForm">
                <div class="form-section">
                    <h3><i class="fas fa-id-card"></i> المعلومات الأساسية</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fullName">الاسم الكامل</label>
                            <input type="text" id="fullName" name="fullName" required placeholder="أدخل الاسم الرباعي">
                        </div>
                        <div class="form-group">
                            <label for="username">اسم المستخدم</label>
                            <input type="text" id="username" name="username" required placeholder="مثال: ahmed_2024">
                        </div>
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" required placeholder="example@uili.edu.ye">
                        </div>
                        <div class="form-group">
                            <label for="phone">رقم الهاتف</label>
                            <input type="text" id="phone" name="phone" placeholder="7XXXXXXXX">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-user-shield"></i> الصلاحيات والأمان</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="role">نوع المستخدم</label>
                            <select id="role" name="role" required>
                                <option value="student">طالب</option>
                                <option value="archive">موظف أرشيف</option>
                                <option value="admin">مدير نظام</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">كلمة المرور المؤقتة</label>
                            <input type="password" id="password" name="password" required placeholder="أدخل كلمة مرور قوية">
                        </div>
                    </div>
                </div>

                <div id="studentFields" class="form-section student-only-fields" style="display: block;">
                    <h3><i class="fas fa-university"></i> البيانات الجامعية (للطلاب فقط)</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="studentId">الرقم الجامعي</label>
                            <input type="text" id="studentId" name="studentId" placeholder="مثال: 202110123">
                        </div>
                        <div class="form-group">
                            <label for="faculty">الكلية</label>
                            <input type="text" id="faculty" name="faculty" placeholder="مثال: كلية الهندسة">
                        </div>
                        <div class="form-group">
                            <label for="department">القسم</label>
                            <input type="text" id="department" name="department" placeholder="مثال: تقنية المعلومات">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-user-plus"></i> إنشاء الحساب وتفعيله
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const studentFields = document.getElementById('studentFields');
            const createUserForm = document.getElementById('createUserForm');
            const submitBtn = document.getElementById('submitBtn');
            const responseMessage = document.getElementById('responseMessage');

            // Toggle student fields based on role
            roleSelect.addEventListener('change', function() {
                if (this.value === 'student') {
                    studentFields.style.display = 'block';
                } else {
                    studentFields.style.display = 'none';
                }
            });

            // Handle form submission
            createUserForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإنشاء...';
                
                const formData = new FormData(this);
                
                fetch('admin_create_user_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    responseMessage.style.display = 'block';
                    responseMessage.className = 'message ' + data.status;
                    responseMessage.innerHTML = (data.status === 'success' ? '<i class="fas fa-check-circle"></i> ' : '<i class="fas fa-exclamation-circle"></i> ') + data.message;
                    
                    if (data.status === 'success') {
                        createUserForm.reset();
                        studentFields.style.display = 'block'; // Reset to default student view
                    }
                    
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                })
                .catch(error => {
                    console.error('Error:', error);
                    responseMessage.style.display = 'block';
                    responseMessage.className = 'message error';
                    responseMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> حدث خطأ غير متوقع في الاتصال.';
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> إنشاء الحساب وتفعيله';
                });
            });
        });
    </script>
</body>
</html>
