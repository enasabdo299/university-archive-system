// JavaScript - نظام أرشفة المشاريع الجامعية - الجامعة الإماراتية الدولية
document.addEventListener("DOMContentLoaded", function () {
  // تأثير ظهور المحتوى الرئيسي
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

  // تأثير ظهور محتوى الصفحات
  const pageContent = document.querySelector(".page-content");
  if (pageContent && !hero) {
    pageContent.style.opacity = "0";
    pageContent.style.transform = "translateY(20px)";

    setTimeout(() => {
      pageContent.style.transition = "opacity 0.8s ease, transform 0.8s ease";
      pageContent.style.opacity = "1";
      pageContent.style.transform = "translateY(0)";
    }, 300);
  }

  // تحديد الصفحة النشطة في القائمة
  const currentPage = window.location.pathname.split("/").pop() || "index.php";
  const navLinks = document.querySelectorAll(".nav-links a");

  navLinks.forEach((link) => {
    const linkHref = link.getAttribute("href");
    if (
      linkHref === currentPage ||
      (currentPage === "" && linkHref === "index.php")
    ) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });

  // ========== خاصية إخفاء وإظهار الهيدر عند التمرير ==========
  let lastScrollTop = 0;
  function handleScroll() {
    const headerWrapper = document.querySelector(".header-wrapper");
    if (!headerWrapper) return;
    
    const backToTopBtn = document.querySelector(".back-to-top");
    const currentScroll =
      window.pageYOffset || document.documentElement.scrollTop;

    // إخفاء أو إظهار الهيدر
    if (currentScroll > lastScrollTop && currentScroll > 100) {
      // التمرير لأسفل - إخفاء الهيدر
      headerWrapper.classList.add("hidden");
    } else {
      // التمرير لأعلى - إظهار الهيدر
      headerWrapper.classList.remove("hidden");
    }

    // حفظ آخر موضع تمرير
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;

    // إظهار زر العودة للأعلى
    if (backToTopBtn) {
      if (currentScroll > 300) {
        backToTopBtn.classList.add("show");
      } else {
        backToTopBtn.classList.remove("show");
      }
    }
  }

  // زر العودة للأعلى
  const backToTopBtn = document.querySelector(".back-to-top");
  if (backToTopBtn) {
    backToTopBtn.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  // تشغيل جميع الأحداث
  window.addEventListener("scroll", function () {
    handleScroll();
  });

  // بدء التأثير الأولي للتمرير
  handleScroll();
});
