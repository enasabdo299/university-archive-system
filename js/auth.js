// js/auth.js

// إضافة تفاعلية خاصة بصفحات المصادقة
document.addEventListener("DOMContentLoaded", function () {
  // تفعيل الأزرار في صفحات المصادقة
  const authButtons = document.querySelectorAll(".btn");
  authButtons.forEach((button) => {
    button.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-3px)";
    });

    button.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)";
    });
  });

  // التحقق من تطابق كلمات المرور في صفحة التسجيل
  const registerForm = document.querySelector('form[action*="register"]');
  if (registerForm) {
    const passwordInput = registerForm.querySelector('input[type="password"]');
    const confirmPasswordInput = registerForm.querySelectorAll(
      'input[type="password"]'
    )[1];

    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener("blur", function () {
        if (passwordInput.value !== confirmPasswordInput.value) {
          this.style.borderColor = "#dc3545";
          this.style.boxShadow = "0 0 0 3px rgba(220, 53, 69, 0.2)";
        } else {
          this.style.borderColor = "#28a745";
          this.style.boxShadow = "0 0 0 3px rgba(40, 167, 69, 0.2)";
        }
      });
    }
  }

  // محاكاة تسجيل الدخول
  const loginForm = document.querySelector('form[action*="login"]');
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const button = this.querySelector(".btn");
      const originalText = button.innerphp;

      button.innerphp =
        '<i class="fas fa-spinner fa-spin"></i> جاري الدخول...';
      button.disabled = true;

      // محاكاة عملية الدخول
      setTimeout(() => {
        button.innerphp = originalText;
        button.disabled = false;
        alert("تم تسجيل الدخول بنجاح!");
        // يمكن توجيه المستخدم لصفحة أخرى هنا
        // window.location.href = "admin_dashboard.php";
      }, 2000);
    });
  }

  // محاكاة إنشاء الحساب
  const registerFormSubmit = document.querySelector('form[action*="register"]');
  if (registerFormSubmit) {
    registerFormSubmit.addEventListener("submit", function (e) {
      e.preventDefault();

      // التحقق من تطابق كلمات المرور
      const passwordInput = this.querySelector('input[type="password"]');
      const confirmPasswordInput = this.querySelectorAll(
        'input[type="password"]'
      )[1];

      if (
        passwordInput &&
        confirmPasswordInput &&
        passwordInput.value !== confirmPasswordInput.value
      ) {
        alert("كلمات المرور غير متطابقة!");
        return;
      }

      const button = this.querySelector(".btn");
      const originalText = button.innerphp;

      button.innerphp =
        '<i class="fas fa-spinner fa-spin"></i> جاري إنشاء الحساب...';
      button.disabled = true;

      // محاكاة عملية التسجيل
      setTimeout(() => {
        button.innerphp = originalText;
        button.disabled = false;
        alert("تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.");
        window.location.href = "login.php";
      }, 2000);
    });
  }

  // محاكاة إرسال رابط الاستعادة
  const resetForm = document.querySelector('form[action*="forgot-password"]');
  if (resetForm) {
    const successMessage = document.getElementById("successMessage");

    resetForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const button = this.querySelector(".btn");
      const originalText = button.innerphp;

      button.innerphp =
        '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
      button.disabled = true;

      // محاكاة عملية الإرسال
      setTimeout(() => {
        button.innerphp = originalText;
        button.disabled = false;

        // إظهار رسالة النجاح
        if (successMessage) {
          successMessage.style.display = "block";
        }
        this.reset();

        // إخفاء الرسالة بعد 5 ثوانٍ
        setTimeout(() => {
          if (successMessage) {
            successMessage.style.display = "none";
          }
        }, 5000);
      }, 2000);
    });
  }
});
