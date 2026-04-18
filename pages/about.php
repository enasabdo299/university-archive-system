<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>عن المنصة - نظام أرشفة المشاريع الجامعية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <link rel="stylesheet" href="../css/about.css?v=20240301-v3" />
    
</head>
<body>
   
        <!-- التغليف الجديد -->
        <div class="header-wrapper">
            <header class="fixed-header">
                <div class="header-content">
                    <div class="logo-container">
                        <div class="logo-img">
                            <img src="../img/1765888818874.jpg" alt="شعار الجامعة الإماراتية الدولية" />
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
                        <a href="projects.php"><i class="fas fa-project-diagram"></i> المشاريع</a>
                    </li>
                    <li>
                        <a href="about.php" class="active"><i class="fas fa-chalkboard-teacher"></i> عن المنصة</a>
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
                        <a href="login.php"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a>
                    </li>
            <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="container">
            <section class="hero">
                <h2>عن نظام أرشفة المشاريع الجامعية</h2>
                <p>منصة رقمية متكاملة تهدف إلى حفظ وتنظيم الإنتاج العلمي للجامعة</p>
            </section>

            <section class="page-content">
                <h2 class="page-title">رؤيتنا ورسالتنا</h2>
                
                <div class="mission-vision">
                    <div class="mission-card">
                        <h3><i class="fas fa-bullseye"></i> رسالتنا</h3>
                        <p>توفير منصة رقمية متكاملة تحفظ وتنظم الإنتاج العلمي والأكاديمي للجامعة، وتسهل الوصول إليه، وتسهم في بناء قاعدة معرفية داعمة للبحث العلمي والابتكار.</p>
                        <p>نسعى لتحويل مشاريع التخرج والأبحاث العلمية من أرشيف تقليدي إلى منصة تفاعلية تسهم في تنمية المجتمع الأكاديمي وتعزز ثقافة المشاركة والتطوير.</p>
                    </div>
                    
                    <div class="vision-card">
                        <h3><i class="fas fa-eye"></i> رؤيتنا</h3>
                        <p>أن نكون النظام الرائد في أرشفة وإدارة المشاريع الجامعية على مستوى الجامعات اليمنية، وأن نسهم في تحويل الجامعة الإماراتية الدولية إلى مركز إشعاع علمي ومعرفي.</p>
                        <p>نسعى لأن تصبح منصتنا المرجع الأول للباحثين والطلاب والمهتمين بالمعرفة العلمية في مختلف التخصصات.</p>
                    </div>
                </div>
                
                <h2 class="page-title">قيمنا الأساسية</h2>
                
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>الجودة والموثوقية</h4>
                        <p>نحرص على تقديم محتوى عالي الجودة وموثوق، مع التأكد من دقة المعلومات وجودة الأرشيف العلمي.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4>التعاون العلمي</h4>
                        <p>نؤمن بأهمية التعاون بين الطلاب والأساتذة والباحثين لتحقيق التميز العلمي والابتكار.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4>الابتكار والتطوير</h4>
                        <p>نسعى دائماً لتطوير أدواتنا وتحسين تجربة المستخدم لمواكبة التطورات التكنولوجية.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>التميز الأكاديمي</h4>
                        <p>نهدف إلى دعم التميز الأكاديمي والبحث العلمي من خلال توفير بيئة معرفية غنية.</p>
                    </div>
                </div>
            </section>
            
            <section class="page-content">
                <h2 class="page-title">محطات التطور</h2>
                
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-year">يناير 2023</div>
                        <div class="timeline-content">
                            <h4>فكرة المشروع</h4>
                            <p>انطلاق فكرة إنشاء نظام أرشفة رقمي متكامل للمشاريع الجامعية بناءً على احتياجات كلية الحاسبات وتكنولوجيا المعلومات.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">يونيو 2023</div>
                        <div class="timeline-content">
                            <h4>الدراسة والتصميم</h4>
                            <p>إجراء دراسة شاملة للاحتياجات الأكاديمية وتصميم النظام بالتعاون مع أعضاء هيئة التدريس والطلاب.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">سبتمبر 2023</div>
                        <div class="timeline-content">
                            <h4>بدء التطوير</h4>
                            <p>بدء مرحلة تطوير النظام الفعلية بواسطة فريق من طلاب قسم هندسة البرمجيات.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">ديسمبر 2023</div>
                        <div class="timeline-content">
                            <h4>الإطلاق التجريبي</h4>
                            <p>إطلاق النسخة التجريبية من النظام واختبارها على مجموعة محددة من المشاريع والطلاب.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">مارس 2024</div>
                        <div class="timeline-content">
                            <h4>التوسع والتطوير</h4>
                            <p>توسيع النظام ليشمل جميع كليات الجامعة وإضافة ميزات جديدة بناءً على ملاحظات المستخدمين.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">ديسمبر 2024</div>
                        <div class="timeline-content">
                            <h4>الإطلاق الرسمي</h4>
                            <p>الإطلاق الرسمي للنظام بكافة ميزاته، مع أرشيف يضم أكثر من 1250 مشروعاً جامعياً.</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="page-content team-section">
                <h2 class="page-title">الفريق المسؤول</h2>
                <p style="text-align: center; margin-bottom: 30px; color: var(--gray);">فريق متخصص من الطلاب والأساتذة يعمل على تطوير وصيانة النظام</p>
                
                <div class="team-grid">
                    <div class="team-card">
                        <div class="team-img">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h4>د. أحمد محمد</h4>
                        <div class="team-role">المشرف الأكاديمي</div>
                        <p>أستاذ مشارك في قسم هندسة البرمجيات، ومشرف عام على النظام</p>
                    </div>
                    
                    <div class="team-card">
                        <div class="team-img">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4>سارة علي</h4>
                        <div class="team-role">قائدة فريق التطوير</div>
                        <p>طالبة في السنة النهائية بقسم هندسة البرمجيات، مسؤولة عن تطوير النظام</p>
                    </div>
                    
                    <div class="team-card">
                        <div class="team-img">
                            <i class="fas fa-code"></i>
                        </div>
                        <h4>محمد عبدالله</h4>
                        <div class="team-role">مطور الواجهة الأمامية</div>
                        <p>متخصص في تطوير واجهات المستخدم وتجربة المستخدم</p>
                    </div>
                    
                    <div class="team-card">
                        <div class="team-img">
                            <i class="fas fa-database"></i>
                        </div>
                        <h4>فاطمة عمر</h4>
                        <div class="team-role">مطورة قواعد البيانات</div>
                        <p>مسؤولة عن تصميم وإدارة قواعد البيانات والنظام الخلفي</p>
                    </div>
                </div>
            </section>
            
            <section class="page-content">
                <h2 class="page-title">كيفية استخدام النظام</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 30px;">
                    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--box-shadow); border-right: 4px solid var(--primary);">
                        <h4 style="color: var(--primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-search" style="color: var(--secondary);"></i>
                            البحث عن المشاريع
                        </h4>
                        <p style="color: var(--gray); line-height: 1.7;">يمكنك البحث في الأرشيف باستخدام كلمات مفتاحية، اسم الطالب، السنة، التخصص، أو اسم المشرف. النظام يدعم البحث المتقدم والتصفية حسب معايير متعددة.</p>
                    </div>
                    
                    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--box-shadow); border-right: 4px solid var(--primary);">
                        <h4 style="color: var(--primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-upload" style="color: var(--secondary);"></i>
                            رفع المشاريع
                        </h4>
                        <p style="color: var(--gray); line-height: 1.7;">يمكن للطلاب المسجلين رفع مشاريعهم بسهولة من خلال حسابهم الشخصي. يجب أن تكون المشاريع بصيغ محددة وتحتوي على المعلومات الأساسية المطلوبة.</p>
                    </div>
                </div>
                
                <div style="margin-top: 40px; padding: 25px; background: rgba(27, 54, 93, 0.05); border-radius: var(--border-radius); border-right: 4px solid var(--secondary);">
                    <h4 style="color: var(--primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-info-circle" style="color: var(--secondary);"></i>
                        ملاحظات هامة
                    </h4>
                    <ul style="color: var(--gray); line-height: 1.8; padding-right: 20px;">
                        <li style="margin-bottom: 10px;">جميع المشاريع تخضع لمراجعة أكاديمية قبل النشر.</li>
                        <li style="margin-bottom: 10px;">يجب احترام حقوق الملكية الفكرية عند استخدام المشاريع المنشورة.</li>
                        <li style="margin-bottom: 10px;">يمكن للطلاب الحاليين الاطلاع على المشاريع السابقة للإلهام والاستفادة فقط.</li>
                        <li>لأي استفسارات تقنية، يمكن التواصل مع فريق الدعم الفني من خلال البريد الإلكتروني.</li>
                    </ul>
                </div>
            </section>
            <?php include '../includes/footer.php'; ?>
        </div>

    

    <!-- زر العودة للأعلى -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script src="../js/script.js?v=20240301-v3"></script>
    <script>
        // إضافة الأنماط الديناميكية لهذه الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // تلوين عناصر الجدول الزمني بشكل عشوائي
            const timelineItems = document.querySelectorAll('.timeline-content');
            const colors = [
                'rgba(27, 54, 93, 0.1)',
                'rgba(200, 16, 46, 0.1)',
                'rgba(240, 198, 62, 0.1)',
                'rgba(42, 74, 126, 0.1)'
            ];
            
            timelineItems.forEach((item, index) => {
                item.style.backgroundColor = colors[index % colors.length];
            });
            
            // إضافة تأثيرات للبطاقات عند التمرير
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // تطبيق التأثير على البطاقات
            const cards = document.querySelectorAll('.value-card, .team-card, .mission-card, .vision-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
            
            // إظهار البطاقات بعد تحميل الصفحة مباشرة
            setTimeout(() => {
                cards.forEach(card => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }, 300);
        });
    </script>
</body>
</html>