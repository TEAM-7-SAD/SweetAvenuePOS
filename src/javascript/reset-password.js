(() => {
  "use strict";

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll(".needs-validation");

  // Loop over them and prevent submission
  Array.from(forms).forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }

        form.classList.add("was-validated");
      },
      false
    );
  });
})();

// Disallow whitespaces from input fields
function avoidSpace(event) {
  if (event.key === " ") {
    event.preventDefault();
  }
}

// Toggle show password
function togglePassword() {
  const passwordInput = document.getElementById("newPassword");
  const confirmPasswordInput = document.getElementById("confirmPassword");
  const isPassword = passwordInput.type === "password";
  passwordInput.type = isPassword ? "text" : "password";
  confirmPasswordInput.type = isPassword ? "text" : "password";
}

document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("newPassword");
  const confirmPasswordInput = document.getElementById("confirmPassword");
  const errorText = document.getElementById("passwordMismatched");
  const passwordRequirements = document.querySelector(".password-requirements");
  const passwordForm = document.getElementById("passwordResetForm");

  function validatePassword() {
    const value = passwordInput.value.trim();
    const requirements = [
      { test: value.length >= 8 && value.length <= 20, elementId: "length" },
      { test: /[A-Z]/.test(value), elementId: "uppercase" },
      { test: /[a-z]/.test(value), elementId: "lowercase" },
      { test: /\d/.test(value), elementId: "number" },
      { test: /[\W_]/.test(value), elementId: "special" },
    ];

    let allMet = true;
    requirements.forEach((req) => {
      const element = document.getElementById(req.elementId);
      if (req.test) {
        element.classList.add("met");
        element.classList.remove("unmet");
      } else {
        element.classList.add("unmet");
        element.classList.remove("met");
        allMet = false;
      }
    });

    if (allMet) {
      passwordInput.classList.add("is-valid");
      passwordInput.classList.remove("is-invalid");
      passwordRequirements.classList.remove("show");
    } else {
      passwordInput.classList.remove("is-valid");
      passwordInput.classList.add("is-invalid");
      passwordRequirements.classList.add("show");
    }

    return allMet;
  }

  function checkPasswordMatch() {
    const passwordValue = passwordInput.value.trim();
    const confirmPasswordValue = confirmPasswordInput.value.trim();

    if (!passwordValue || !confirmPasswordValue) {
      passwordInput.classList.remove("is-valid", "is-invalid");
      confirmPasswordInput.classList.remove("is-valid", "is-invalid");
      errorText.style.display = "none";
      return false;
    }

    if (passwordValue === confirmPasswordValue) {
      passwordInput.classList.add("is-valid");
      confirmPasswordInput.classList.add("is-valid");
      passwordInput.classList.remove("is-invalid");
      confirmPasswordInput.classList.remove("is-invalid");
      errorText.style.display = "none";
      return true;
    } else {
      passwordInput.classList.add("is-invalid");
      confirmPasswordInput.classList.add("is-invalid");
      passwordInput.classList.remove("is-valid");
      confirmPasswordInput.classList.remove("is-valid");
      errorText.style.display = "block";
      errorText.textContent = "Passwords do not match.";
      return false;
    }
  }

  function checkInputs() {
    const passwordValue = passwordInput.value.trim();
    if (passwordValue) {
      passwordRequirements.classList.add("show");
    } else {
      passwordRequirements.classList.remove("show");
    }

    const isPasswordValid = validatePassword();
    const isPasswordMatch = checkPasswordMatch();

    return isPasswordValid && isPasswordMatch;
  }

  passwordInput.addEventListener("input", checkInputs);
  confirmPasswordInput.addEventListener("input", checkInputs);

  passwordForm.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission

    const isFormValid = checkInputs();
    if (isFormValid) {
      // Prepare form data
      const formData = new FormData(passwordForm);
      const newPassword = $("#newPassword").val();
      const confirmPassword = $("#confirmPassword").val();
      const token = $("#token").val();

      // Send AJAX request
      $.ajax({
        url: "includes/process-reset-password.php",
        type: "POST",
        data: {
          newPassword: newPassword,
          confirmPassword: confirmPassword,
          token: token,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            Swal.fire({
              title: "Success!",
              text: response.message,
              icon: "success",
              confirmButtonColor: "#C57C47",
              confirmButtonText: "Continue",
            }).then(() => {
              window.location.href = "login.php";
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: response.message,
              icon: "error",
            });
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          Swal.fire({
            title: "Error!",
            text: "An unexpected error occured.",
            icon: "error",
          });
        },
      });
    }
  });
});
