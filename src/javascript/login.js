(() => {
  ("use strict");

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll(".needs-validation");

  // Loop over the forms and prevent submission if not valid
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
function preventSpaces(event) {
  let input = event.target;
  let value = $(input).val();
  value = value.replace(/\s/g, "");
  $(input).val(value);
}

// Toggle show password
function togglePassword() {
  const passwordInput = document.getElementById("password");
  const isPassword = passwordInput.type === "password";
  passwordInput.type = isPassword ? "text" : "password";
}

document.addEventListener("DOMContentLoaded", function () {
  const sendButton = document.querySelector("#sendEmailBtn");
  const emailInput = document.querySelector("#email");

  $("#username, #password, #email").on("input", function (event) {
    preventSpaces(event);
  });

  // Clear server-side error message
  $("#submitForm").on("click", function () {
    const serverSideErrorMessage = document.querySelector(
      "#serverSideErrorMessage"
    );
    if (serverSideErrorMessage) {
      serverSideErrorMessage.remove();
    }
  });

  const resetForgotPasswordForm = () => {
    $("#email-error").text("");
    $("#email").removeClass("is-invalid is-valid was-validated");
    $("#email-valid").text("");
    $("#sendEmailBtn").prop("disabled", true);
    $("#forgotPasswordForm")[0].reset();
  };

  $("#cancelSendEmailBtn, #closeSendEmailBtn").on("click", function () {
    resetForgotPasswordForm();
  });

  // Initially disable the Send button
  sendButton.disabled = true;

  const validateEmail = () => {
    const email = emailInput.value;
    const isExistingEmail = existingEmails.includes(email);
    // Email format validation
    const isValid = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(
      email
    );

    if (!isValid) {
      emailInput.classList.add("is-invalid");
      emailInput.classList.remove("was-validated");
      emailInput.classList.remove("is-valid");
      document.querySelector("#email-error").textContent =
        "Please provide a valid email address.";
      document.querySelector("#email-valid").textContent = "";
    } else if (!isExistingEmail) {
      emailInput.classList.add("is-invalid");
      emailInput.classList.remove("was-validated");
      emailInput.classList.remove("is-valid");
      document.querySelector("#email-error").textContent =
        "User with this email cannot be found.";
      document.querySelector("#email-valid").textContent = "";
    } else {
      emailInput.classList.remove("is-invalid");
      emailInput.classList.add("is-valid");
      emailInput.classList.add("was-validated");
      document.querySelector("#email-error").textContent = "";
      document.querySelector("#email-valid").textContent = "Looks right!";
    }

    sendButton.disabled = !isValid || !isExistingEmail;
  };

  // Validate the email input on input and change events
  emailInput.addEventListener("input", validateEmail);
  emailInput.addEventListener("change", validateEmail);

  // Password Reset Link
  $("#sendEmailBtn").click(function (event) {
    event.preventDefault();
    const email = $("#email").val();
    if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)) {
      // Basic email format validation
      const emailError = document.querySelector("#email-error");
      emailError.textContent = "Please provide a valid email address.";
      emailInput.classList.add("is-invalid");
      return;
    }

    // Disable the button while the request is being processed
    sendButton.disabled = true;
    sendButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...`;

    $.ajax({
      url: "includes/send-password-reset.php",
      type: "POST",
      data: { email: email },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#forgotPasswordModal").hide();
          Swal.fire({
            title: "Success!",
            text: response.message,
            icon: "success",
            confirmButtonColor: "#88531E",
            confirmButtonText: "Got it!",
          }).then(() => {
            location.reload();
          });
        } else {
          const emailError = document.querySelector("#email-error");
          emailError.textContent = response.message;
          emailInput.classList.remove("is-valid");
          emailInput.classList.add("is-invalid");
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
      complete: function () {
        // Reset the button content and enable it after the request is complete (whether success or error)
        sendButton.innerHTML = "Send Link";
        sendButton.disabled = false;
      },
    });
  });

  // Disable the "Send" button after it's clicked
  $("#sendEmailBtn").click(function () {
    $(this).prop("disabled", true);
  });
});
