<?php
// تحديد المسار الصحيح للروابط بناءً على مكان الصفحة الحالية
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$prefix = ($current_dir == 'pages') ? '../' : '';

// تخصيص محتوى "عن المنصة" إذا لم يتم تحديده مسبقاً في الصفحة
$footer_about_title = isset($footer_about_title) ? $footer_about_title : "عن المنصة";
$footer_about_text = isset($footer_about_text) ? $footer_about_text : "منصة متكاملة لأرشفة وإدارة مشاريع التخرج والبحوث الجامعية";
$footer_about_desc = isset($footer_about_desc) ? $footer_about_desc : "نسعى لتوفير بيئة أكاديمية متكاملة للطلاب والأساتذة للوصول للمعرفة ومشاركة الإنجازات العلمية.";
?>
<footer>
  <div class="footer-content">
    <div class="footer-section">
      <h3><?php echo $footer_about_title; ?></h3>
      <p><?php echo $footer_about_text; ?></p>
      <p><?php echo $footer_about_desc; ?></p>
    </div>
    <div class="footer-section">
      <h3>روابط سريعة</h3>
      <ul class="footer-links">
        <li>
          <a href="<?php echo $prefix; ?>index.php"><i class="fas fa-home"></i> الرئيسية</a>
        </li>
        <li>
          <a href="<?php echo $prefix; ?>pages/projects.php"
            ><i class="fas fa-project-diagram"></i> المشاريع</a
          >
        </li>
        <li>
          <a href="<?php echo $prefix; ?>pages/about.php"
            ><i class="fas fa-chalkboard-teacher"></i> عن المنصة</a
          >
        </li>
        <li>
          <a href="<?php echo $prefix; ?>pages/login.php"
            ><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a
          >
        </li>
        <!-- <li>
          <a href="#"
            ><i class="fas fa-question-circle"></i> الأسئلة الشائعة</a
          >
        </li> -->
        <li>
          <a href="#"
            ><i class="fas fa-file-contract"></i> الشروط والأحكام</a
          >
        </li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>معلومات التواصل</h3>
      <ul class="footer-links">
        <li>
          <i class="fas fa-map-marker-alt"></i> الجامعة الإماراتية
          الدولية، صنعاء، اليمن
        </li>
        <li><i class="fas fa-phone"></i> +967 1 234 567</li>
        <li><i class="fas fa-envelope"></i> archive@eiu.edu.ye</li>
        <li>
          <i class="fas fa-clock"></i> السبت - الخميس: 8:00 ص - 4:00 م
        </li>
        <li><i class="fas fa-globe"></i> www.eiu.edu.ye/archive</li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>
      جميع الحقوق محفوظة &copy; 2025 الجامعة الإماراتية الدولية - نظام
      أرشفة المشاريع الجامعية
    </p>
    <p style="margin-top: 10px;  opacity: 0.7">
      الإصدار 2.1.0 | آخر تحديث: ديسمبر 2026
    </p>
  </div>
</footer>
