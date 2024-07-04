(() => {
  "use strict";

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
  const sendButton = $("#sendEmailBtn");
  const emailInput = $("#email");
  const usernameInput = $("#username");
  const passwordInput = $("#password");

  function truncateExceedingCharacters() {
    let emailValue = emailInput.val();
    let usernameValue = usernameInput.val();
    let passwordValue = passwordInput.val();

    if (emailValue.length > 100) {
      emailValue = emailValue.slice(0, 100);
      emailInput.val(emailValue);
    }

    if (usernameValue.length > 50) {
      usernameValue = usernameValue.slice(0, 50);
      usernameInput.val(usernameValue);
    }

    if (passwordValue.length > 20) {
      passwordValue = passwordValue.slice(0, 20);
      passwordInput.val(passwordValue);
    }
  }

  $("#username, #password, #email").on("input", function (event) {
    preventSpaces(event);
    truncateExceedingCharacters();
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
  sendButton.prop("disabled", true);

  const validateEmail = () => {
    const email = emailInput.val();
    const isExistingEmail = existingEmails.includes(email);
    // Email format validation
    const isValid = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(
      email
    );

    if (!isValid) {
      emailInput.addClass("is-invalid").removeClass("was-validated is-valid");
      $("#email-error").text("Please provide a valid email address.");
      $("#email-valid").text("");
    } else if (!isExistingEmail) {
      emailInput.addClass("is-invalid").removeClass("was-validated is-valid");
      $("#email-error").text("We couldn't find your email address.");
      $("#email-valid").text("");
    } else {
      emailInput.removeClass("is-invalid").addClass("is-valid was-validated");
      $("#email-error").text("");
      // $("#email-valid").text("Looks right!");
    }

    sendButton.prop("disabled", !isValid || !isExistingEmail);
  };

  // Validate the email input on input and change events
  emailInput.on("input change", validateEmail);

  // Password Reset Link
  $("#sendEmailBtn").click(function (event) {
    event.preventDefault();
    const email = emailInput.val();
    if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)) {
      // Basic email format validation
      $("#email-error").text("Please provide a valid email address.");
      emailInput.addClass("is-invalid");
      return;
    }

    // Disable the button while the request is being processed
    sendButton
      .prop("disabled", true)
      .html(
        `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...`
      );

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
          $("#email-error").text(response.message);
          emailInput.removeClass("is-valid").addClass("is-invalid");
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
      complete: function () {
        // Reset the button content and enable it after the request is complete (whether success or error)
        sendButton.html("Send Link").prop("disabled", false);
      },
    });
  });

  // Disable the "Send" button after it's clicked
  $("#sendEmailBtn").click(function () {
    $(this).prop("disabled", true);
  });
});
