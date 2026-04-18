// js/profile.js - إضافة وظائف للتجاوب

document.addEventListener("DOMContentLoaded", function () {
  // التحكم في تبويبات الملف الشخصي
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const tabId = button.getAttribute("data-tab");

      // إزالة النشاط من جميع الأزرار
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      // إخفاء جميع المحتويات
      tabContents.forEach((content) => content.classList.remove("active"));

      // تفعيل الزر المختار والمحتوى المناسب
      button.classList.add("active");
      document.getElementById(tabId).classList.add("active");
    });
  });

  // إضافة مهارات جديدة
  const addSkillBtn = document.querySelector(".add-skill .btn");
  const skillInput = document.querySelector(".add-skill input");
  const skillsList = document.querySelector(".skills-list");

  if (addSkillBtn && skillInput && skillsList) {
    addSkillBtn.addEventListener("click", function (e) {
      e.preventDefault();
      const skillText = skillInput.value.trim();

      if (skillText) {
        const skillTag = document.createElement("span");
        skillTag.className = "skill-tag";
        skillTag.innerphp = `
          ${skillText}
          <i class="fas fa-times"></i>
        `;

        skillsList.appendChild(skillTag);
        skillInput.value = "";

        // إضافة حدث حذف للمهارة الجديدة
        skillTag.querySelector("i").addEventListener("click", function () {
          skillTag.remove();
        });
      }
    });

    // السماح بإضافة المهارة بالضغط على Enter
    skillInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        addSkillBtn.click();
      }
    });
  }

  // إضافة أحداث حذف للمهارات الموجودة
  document.querySelectorAll(".skill-tag i").forEach((icon) => {
    icon.addEventListener("click", function () {
      this.parentElement.remove();
    });
  });

  // تفعيل إرسال النماذج
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      // هنا يمكن إضافة كود الإرسال عبر AJAX
      alert("تم حفظ التغييرات بنجاح!");
    });
  });

  // تحسين تجربة المستخدم على الأجهزة المحمولة
  if ("ontouchstart" in window) {
    document
      .querySelectorAll(".btn, .tab-btn, .avatar-upload")
      .forEach((element) => {
        element.style.cursor = "pointer";
        element.addEventListener("touchstart", function () {
          this.style.opacity = "0.8";
        });
        element.addEventListener("touchend", function () {
          this.style.opacity = "1";
        });
      });
  }
});
