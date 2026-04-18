// js/admin.js

// تفعيل لوحة تحكم المشرفين
document.addEventListener("DOMContentLoaded", function () {
  // إشعار دخول المشرفين
  console.log("مرحباً بك في لوحة تحكم المشرفين");

  // تفعيل أزرار الإجراءات في الجدول
  const actionButtons = document.querySelectorAll(".btn-action");
  actionButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const action = this.classList[1]; // btn-review, btn-approve, etc.
      const projectRow = this.closest("tr");
      const projectName =
        projectRow.querySelector("td:first-child").textContent;

      switch (action) {
        case "btn-review":
          alert(`مراجعة المشروع: ${projectName}\n\nسيتم فتح صفحة المراجعة...`);
          break;
        case "btn-approve":
          if (confirm(`هل تريد قبول المشروع "${projectName}"؟`)) {
            const statusCell = projectRow.querySelector(".status-badge");
            statusCell.textContent = "مقبول";
            statusCell.className = "status-badge completed";
            this.style.display = "none";
            alert(`تم قبول المشروع: ${projectName}`);
          }
          break;
        case "btn-reject":
          if (confirm(`هل تريد رفض المشروع "${projectName}"؟`)) {
            const statusCell = projectRow.querySelector(".status-badge");
            statusCell.textContent = "مرفوض";
            statusCell.className = "status-badge rejected";
            this.style.display = "none";
            alert(`تم رفض المشروع: ${projectName}`);
          }
          break;
        case "btn-message":
          const studentName =
            projectRow.querySelector("td:nth-child(2)").textContent;
          alert(
            `إرسال رسالة إلى الطالب: ${studentName}\nبخصوص المشروع: ${projectName}`
          );
          break;
      }
    });
  });

  // تفعيل أزرار الطلاب
  const studentActions = document.querySelectorAll(
    ".student-actions .btn-action"
  );
  studentActions.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const studentCard = this.closest(".student-card");
      const studentName = studentCard.querySelector("h3").textContent;

      if (this.querySelector(".fa-envelope")) {
        alert(`إرسال بريد إلكتروني إلى: ${studentName}`);
      } else if (this.querySelector(".fa-calendar")) {
        alert(`جدولة موعد مع: ${studentName}`);
      } else if (this.querySelector(".fa-chart-line")) {
        alert(`عرض تقرير أداء: ${studentName}`);
      }
    });
  });

  // تفعيل أزرار التقارير
  const reportLinks = document.querySelectorAll(".report-link");
  reportLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const reportTitle =
        this.closest(".report-card").querySelector("h3").textContent;
      alert(`تحميل تقرير: ${reportTitle}`);
    });
  });

  // تفعيل زر الإشعارات
  const notificationBadge = document.querySelector(".notification-badge");
  if (notificationBadge) {
    notificationBadge.addEventListener("click", function (e) {
      e.preventDefault();
      alert(
        "عرض الإشعارات:\n\n1. رسالة جديدة من طالب\n2. مشروع جديد تحتاج مراجعة\n3. تذكير بموعد اجتماع"
      );

      // إعادة تعيين العداد
      const badgeCount = this.querySelector(".badge-count");
      if (badgeCount) {
        badgeCount.textContent = "0";
        badgeCount.style.background = "#6c757d";
      }
    });
  }

  

  // تحديث الإحصائيات تلقائياً
  function updateAdminStats() {
    const statValues = document.querySelectorAll(".stat-info h3");
    if (statValues.length >= 4) {
      // محاكاة تحديث الإحصائيات
      statValues[0].textContent = Math.floor(Math.random() * 10) + 20; // المشاريع النشطة
      statValues[1].textContent = Math.floor(Math.random() * 5) + 15; // الطلاب
      statValues[2].textContent = Math.floor(Math.random() * 3); // المشاريع المتأخرة
      statValues[3].textContent = Math.floor(Math.random() * 5) + 10; // المشاريع المكتملة
    }
  }

  // تحديث الإحصائيات كل دقيقة
  // setInterval(updateAdminStats, 60000);

  // زر عرض الكل
  /* تم إلغاء التنبيهات التجريبية لروابط "عرض الكل" للسماح بالانتقال المباشر للطلاب وغيرها */
  // const viewAllLinks = document.querySelectorAll(".view-all");
  // viewAllLinks.forEach((link) => {
  //   link.addEventListener("click", function (e) {
  //     e.preventDefault();
  //     const sectionTitle =
  //       this.closest(".section-header").querySelector("h2").textContent;
  //     alert(`فتح صفحة كاملة لـ ${sectionTitle}`);
  //   });
  // });

  // التأكيد على أن الصفحة للمشرفين فقط
  // if (adminNotice) {
  //   adminNotice.addEventListener("click", function () {
  //     alert(
  //       "⚠️ ملاحظة هامة:\nهذه الصفحة خاصة بالمشرفين فقط.\nالطلاب يمكنهم فقط:\n1. تصفح المشاريع\n2. إنشاء حساب\n3. تسجيل الدخول لعرض مشاريعهم فقط"
  //     );
  //   });
  // }
});
