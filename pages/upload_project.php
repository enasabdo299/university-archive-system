<?php
session_start();
if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['student', 'admin'])){
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع مشروع جديد - نظام أرشفة المشاريع الجامعية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=1771762380" />
    <link rel="stylesheet" href="../css/admin.css" />
    <script src="../js/script.js" defer></script>
    <script src="../js/main.js" defer></script>
    <style>
        .upload-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .form-section h3 {
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .file-drop-area {
            border: 2px dashed #e1e5eb;
            border-radius: var(--border-radius);
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: #f8f9fa;
        }
        .file-drop-area:hover {
            border-color: var(--primary);
            background: rgba(27, 54, 93, 0.05);
        }
        .file-drop-area i {
            
            color: var(--primary);
            margin-bottom: 15px;
        }
        .file-info {
            margin-top: 15px;
            
            color: var(--gray);
        }
        .team-members-list {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }

        /* Back Button Styles */
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
            color: var(--primary);
            border: 1.5px solid var(--primary);
            text-decoration: none;
            font-weight: 600;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background: var(--primary) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
                        <a href="#" onclick="window.history.back(); return false;" class="btn btn-secondary back-btn">
                            عودة <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </header>
        </div>

        <div class="main-content">
            <section class="hero">
                <h2>رفع مشروع تخرج جديد</h2>
                <p>قم بتعبئة بيانات مشروعك ورفعه ليتم مراجعته وأرشفته في النظام</p>
            </section>

            <div class="upload-container">
                <form action="upload_process.php" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3><i class="fas fa-info-circle"></i> المعلومات الأساسية</h3>
                        <div class="form-group">
                            <label for="title" class="required-field">عنوان المشروع</label>
                            <input type="text" id="title" name="title" placeholder="أدخل العنوان الكامل للمشروع" required>
                        </div>
                        <div class="form-group">
                            <label for="abstract" class="required-field">ملخص المشروع</label>
                            <textarea id="abstract" name="abstract" rows="5" placeholder="اكتب ملخصاً موجزاً عن فكرة المشروع وأهدافه" required></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-users"></i> الفريق والإشراف</h3>
                        <div class="form-group">
                            <label for="team_members" class="required-field">الطلاب المشاركون في المشروع</label>
                            <textarea id="team_members" name="team_members" rows="3" placeholder="أدخل أسماء زملائك المشاركين (كل اسم في سطر)" required></textarea>
                            <small class="text-muted">أدخل الأسماء الكاملة لضمان توثيق حقوقهم.</small>
                        </div>
                        <div class="form-group">
                            <label for="supervisor" class="required-field">اسم المشرف الأكاديمي</label>
                            <input type="text" id="supervisor" name="supervisor" placeholder="أدخل اسم الدكتور المشرف" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-graduation-cap"></i> التفاصيل الأكاديمية</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="faculty" class="required-field">الكلية</label>
                                <select id="faculty" name="faculty" required>
                                    <option value="">اختر التخصص </option>
                                    <option value="علوم الحاسوب">  وتكنولوجيا المعلومات</option>
                                    <option value="الهندسة">كلية الهندسة</option>
                                    <option value="الطب">كلية الطب</option>
                                    <option value="الإدارة">كلية العلوم الإدارية</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="academic_year" class="required-field">العام الجامعي</label>
                                <select id="academic_year" name="academic_year" required>
                                    <option value="">اختر العام</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-file-upload"></i> ملف المشروع</h3>
                        <div class="file-drop-area" id="dropArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>اسحب وأفلت ملف المشروع هنا أو انقر للاختيار</p>
                            <p class="file-info">يجب أن يكون الملف بصيغة PDF أو DOCX فقط (الحد الأقصى 20 ميجابايت)</p>
                            <input type="file" name="project_file" id="projectFile" accept=".pdf,.docx" style="display:none" required>
                        </div>
                        <div id="fileStatus" style="margin-top: 10px; color: var(--success);  display: none;">
                            <i class="fas fa-check-circle"></i> تم اختيار ملف: <span id="selectedFileName"></span>
                        </div>
                    </div>

                    <div style="text-align: left; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary" style="padding: 15px 40px; ">
                            <i class="fas fa-paper-plane"></i> إرسال المشروع للمراجعة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('projectFile');
        const fileStatus = document.getElementById('fileStatus');
        const fileNameSpan = document.getElementById('selectedFileName');

        dropArea.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileStatus.style.display = 'block';
                fileNameSpan.textContent = this.files[0].name;
            }
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.style.borderColor = 'var(--primary)', false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.style.borderColor = '#e1e5eb', false);
        });

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            if (files.length > 0) {
                fileStatus.style.display = 'block';
                fileNameSpan.textContent = files[0].name;
            }
        }
    </script>
</body>
</html>
