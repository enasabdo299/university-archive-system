// js/projects.js

// إضافة تفاعلية خاصة بصفحة المشاريع
document.addEventListener("DOMContentLoaded", function () {
  // تفعيل تأثيرات بطاقات المشاريع
  const projectCards = document.querySelectorAll(".project-card");
  projectCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-5px)";
    });

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)";
    });
  });

  // تفعيل أزرار البحث والتصفية
  const buttons = document.querySelectorAll(".btn");
  buttons.forEach((button) => {
    button.addEventListener("click", function () {
      this.style.transform = "scale(0.98)";
      setTimeout(() => {
        this.style.transform = "";
      }, 150);

      // محاكاة البحث (لأغراض العرض فقط)
      if (this.classList.contains("btn-primary")) {
        const projectCards = document.querySelectorAll(".project-card");
        projectCards.forEach((card) => {
          card.style.opacity = "0.5";
        });

        setTimeout(() => {
          projectCards.forEach((card) => {
            card.style.opacity = "1";
          });
        }, 500);
      }
    });
  });

  // تفعيل البحث الحقيقي (يمكن تطويره لاحقاً)
  const searchButton = document.querySelector(".btn-primary");
  const resetButton = document.querySelector(".btn-secondary");

  if (searchButton) {
    searchButton.addEventListener("click", function (e) {
      e.preventDefault();
      performSearch();
    });
  }

  if (resetButton) {
    resetButton.addEventListener("click", function (e) {
      e.preventDefault();
      resetFilters();
    });
  }
});

// دالة البحث
function performSearch() {
  const yearSelect = document.querySelector(
    ".filter-group:nth-child(1) select"
  );
  const departmentSelect = document.querySelector(
    ".filter-group:nth-child(2) select"
  );
  const supervisorSelect = document.querySelector(
    ".filter-group:nth-child(3) select"
  );

  const selectedYear = yearSelect.value;
  const selectedDepartment = departmentSelect.value;
  const selectedSupervisor = supervisorSelect.value;

  // هنا يمكن إضافة منطق البحث الحقيقي
  console.log("البحث عن:", {
    year: selectedYear,
    department: selectedDepartment,
    supervisor: selectedSupervisor,
  });

  // عرض رسالة توضيحية
  alert(
    `سيتم البحث عن المشاريع حسب:\n- السنة: ${selectedYear}\n- القسم: ${selectedDepartment}\n- المشرف: ${selectedSupervisor}`
  );
}

// دالة إعادة تعيين الفلاتر
function resetFilters() {
  const selects = document.querySelectorAll(".filter-select select");
  selects.forEach((select) => {
    select.selectedIndex = 0;
  });

  // إعادة عرض جميع البطاقات
  const projectCards = document.querySelectorAll(".project-card");
  projectCards.forEach((card) => {
    card.style.display = "block";
  });
}
