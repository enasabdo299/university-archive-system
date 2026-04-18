// main.js - جميع الـ JavaScript للصفحات

document.addEventListener("DOMContentLoaded", function () {
  // تأثير ظهور الـ Hero Section
  const hero = document.querySelector(".hero");
  if (hero) {
    hero.style.opacity = "0";
    hero.style.transform = "translateY(20px)";

    setTimeout(() => {
      hero.style.transition = "opacity 0.8s ease, transform 0.8s ease";
      hero.style.opacity = "1";
      hero.style.transform = "translateY(0)";
    }, 300);
  }

  // تحديد الصفحة النشطة في القائمة
  const currentPage = window.location.pathname.split("/").pop() || "index.php";
  const navLinks = document.querySelectorAll(".nav-links a");

  navLinks.forEach((link) => {
    const linkHref = link.getAttribute("href");
    // إزالة المسارات لتسهيل المقارنة
    const cleanHref = linkHref.replace("pages/", "").replace("../", "");
    const cleanCurrent = currentPage.replace("pages/", "").replace("../", "");

    if (
      cleanHref === cleanCurrent ||
      (cleanCurrent === "" && cleanHref === "index.php") ||
      (cleanCurrent === "index.php" && cleanHref === "index.php")
    ) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
  // ========== وظائف إضافية للصفحات الأخرى ==========

  // التحقق من نموذج تسجيل الدخول
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const email = document.getElementById("email").value;
      const password = document.getElementById("password").value;

      if (!email || !password) {
        alert("يرجى ملء جميع الحقول");
        return;
      }

      // هنا يمكنك إضافة منطق تسجيل الدخول
      console.log("تسجيل الدخول:", { email, password });
      alert("تم إرسال البيانات بنجاح!");

      // إعادة تعيين النموذج
      this.reset();
    });
  }

  // فلترة المشاريع (إن وجدت)
  const searchInput = document.getElementById("projectSearch");
  const projectCards = document.querySelectorAll(".project-card");

  if (searchInput && projectCards.length > 0) {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();

      projectCards.forEach((card) => {
        const title = card.querySelector("h3").textContent.toLowerCase();
        const description = card.querySelector("p").textContent.toLowerCase();

        if (title.includes(searchTerm) || description.includes(searchTerm)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  }

  // تحميل البيانات الديناميكية للمشاريع (مثال)
  function loadProjects() {
    // هنا يمكنك إضافة منطق لجلب المشاريع من API
    // هذه مجرد بيانات تجريبية
    const projectsData = [
      {
        id: 1,
        title: "نظام إدارة المكتبات",
        description: "نظام متكامل لإدارة المكتبات الجامعية",
        year: "2023",
        department: "علوم الحاسوب",
        supervisor: "د. أحمد محمد",
      },
      {
        id: 2,
        title: "تطبيق تعليمي للأطفال",
        description: "تطبيق تفاعلي لتعليم الأطفال أساسيات البرمجة",
        year: "2024",
        department: "هندسة البرمجيات",
        supervisor: "د. سارة علي",
      },
      {
        id: 3,
        title: "منصة التجارة الإلكترونية",
        description: "منصة متكاملة للتجارة الإلكترونية للشركات الصغيرة",
        year: "2023",
        department: "نظم المعلومات",
        supervisor: "د. خالد حسن",
      },
    ];

    // يمكنك استخدام هذه البيانات لتعبئة صفحة المشاريع
    return projectsData;
  }

  // إذا كانت صفحة المشاريع فارغة، يمكنك تعبئتها
  const projectsSection = document.querySelector(".projects-container");
  if (projectsSection && projectsSection.children.length === 0) {
    const projects = loadProjects();

    projects.forEach((project) => {
      const projectphp = `
        <div class="project-card">
          <div class="project-img">
            <img src="../img/project${project.id}.jpg" alt="${project.title}">
          </div>
          <div class="project-content">
            <h3>${project.title}</h3>
            <p>${project.description}</p>
            <div class="project-meta">
              <span><i class="fas fa-calendar"></i> ${project.year}</span>
              <span><i class="fas fa-building"></i> ${project.department}</span>
              <span><i class="fas fa-user-tie"></i> ${project.supervisor}</span>
            </div>
            <a href="#" class="btn btn-primary">عرض التفاصيل</a>
          </div>
        </div>
      `;

      projectsSection.innerphp += projectphp;
    });
  }

  
});
