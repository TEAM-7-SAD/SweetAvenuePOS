(() => {
  "use strict";

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

$(document).ready(function () {
  var table = $("#example").DataTable();

  const addAccountButton = document.querySelector("#saveChangesBtn");
  addAccountButton.disabled = true;

  // Function to check if all input fields are valid
  function areAllInputsValid() {
    const inputs = [
      $("#lastName")[0],
      $("#firstName")[0],
      $("#middleName")[0],
      $("#username")[0],
      $("#email")[0],
      $("#password")[0],
    ];

    // Check if any input field is invalid
    for (const input of inputs) {
      if (!input.classList.contains("is-valid")) {
        return false;
      }
    }

    // All input fields are valid
    return true;
  }

  // Function to validate input fields
  function validateInput(
    input,
    regex,
    errorMessage,
    errorContainer,
    validMessage,
    validContainer
  ) {

    let isValid = regex.test(input.value);

    // Check for first character not being whitespace
    if (input === $("#lastName")[0] || input === $("#firstName")[0] || input === $("#middleName")[0]) {
      isValid = isValid && !input.value.startsWith(' ');
    }

    if (!isValid) {
      input.classList.add("is-invalid");
      input.classList.remove("was-validated");
      input.classList.remove("is-valid");
      errorContainer.textContent = errorMessage;
      validContainer.textContent = "";
    } else {
      input.classList.remove("is-invalid");
      input.classList.add("is-valid");
      input.classList.remove("was-validated");
      errorContainer.textContent = "";
      validContainer.textContent = validMessage;
    }

    // Check if all inputs are valid and enable/disable button accordingly
    if (areAllInputsValid()) {
      addAccountButton.disabled = false;
    } else {
      addAccountButton.disabled = true;
    }
    return isValid;
  }

  // Input validation events
  $("#lastName").on("input change", function () {
    validateInput(
      this,
      /^(?!\s)[^\d]+$/,
      "Please use a valid last name.",
      document.getElementById("errorLastName"),
      "Last name looks good!",
      document.getElementById("validLastName")
    );
  });

  $("#firstName").on("input change", function () {
    validateInput(
      this,
      /^(?!\s)[^\d]+$/,
      "Please use a valid first name.",
      document.getElementById("errorFirstName"),
      "First name looks good!",
      document.getElementById("validFirstName")
    );
  });

  $("#middleName").on("input change", function () {
    validateInput(
      this,
      /^(?!\s)[^\d]+$/,
      "Please use a valid middle name.",
      document.getElementById("errorMiddleName"),
      "Middle name looks good!",
      document.getElementById("validMiddleName")
    );
  });

  $("#username").on("input change", function () {
    validateInput(
      this,
      /^[^\s]{1,50}$/,
      "Please provide a valid username.",
      document.getElementById("errorUsername"),
      "Username looks good!",
      document.getElementById("validUsername")
    );
  });

  $("#email").on("input change", function () {
    validateInput(
      this,
      /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
      "Please provide a valid email address.",
      document.getElementById("errorEmailAddress"),
      "Email address looks good!",
      document.getElementById("validEmailAddress")
    );
  });

  $("#password").on("input change", function () {
    validateInput(
      this,
      /^[^\s]{8,20}$/,
      "Please provide a valid password.",
      document.getElementById("errorPassword"),
      "Password looks good!",
      document.getElementById("validPassword")
    );
  });

  // Toggle show password
  $("#showPassword").on("click", function () {
    const passwordInput = $("#password");
    const isPassword = passwordInput.attr("type") === "password";
    passwordInput.attr("type", isPassword ? "text" : "password");
  });

  // Submit button click handler
  $("#saveChangesBtn").click(function (event) {
    event.preventDefault();
    let form = $("#addAccountForm")[0];
    let isValid = form.checkValidity();
    if (!isValid) {
      event.stopPropagation();
    } else {
      // Serialize the form data
      var formData = $("#addAccountForm").serialize();

      // Send an AJAX request
      $.ajax({
        url: "add-account.php",
        type: "POST",
        data: formData,
        success: function (response) {
          // Insert the new row into the table
          table.row.add($(response)).draw();

          // Close the modal
          $("#addAccountsModal").modal("hide");

          // Show success message
          $("#successMessage")
            .text("Account added successfully")
            .fadeIn()
            .delay(2000)
            .fadeOut();
        },
        error: function (xhr, status, error) {
          // Handle errors if any
          console.error(xhr.responseText);
        },
      });
    }

    // Add Bootstrap validation class
    form.classList.add("was-validated");
  });

  // Disable delete button by default
  $(".delete-account").prop("disabled", true);

  // Add event listener to table rows for row selection
  $(".selectable").click(function () {
    // Toggle checkbox when clicking anywhere on the row
    $(this)
      .find(".account-checkbox")
      .prop("checked", !$(this).find(".account-checkbox").prop("checked"));
    // Check if at least one checkbox is checked
    var anyChecked = $(".account-checkbox:checked").length > 0;
    // Enable or disable the delete button based on checkbox status
    $(".delete-account").prop("disabled", !anyChecked);
  });

  // Add event listener to delete buttons
  $(".delete-account").click(function () {
    // Show the confirmation modal
    $("#deleteConfirmationModal").modal("show");
    // Set the data-account-id attribute of the continue button in the modal
    $("#confirmDeleteBtn").attr("data-account-id", $(this).data("account-id"));
  });

  // Function to handle single account deletion
  $("#confirmDeleteBtn").click(function () {
    var accountId = $("#confirmDeleteBtn").data("account-id");

    // Send an AJAX request to delete the selected account
    $.ajax({
      url: "delete-account.php",
      method: "POST",
      data: { accountIds: [accountId] },
      success: function (response) {
        if (response === "success") {
          // Hide the confirmation modal
          $("#deleteConfirmationModal").modal("hide");
          // Show the success modal
          $("#successModal").modal("show");
          // Remove the deleted row from the table
          table
            .row($('tr[data-id="' + accountId + '"]'))
            .remove()
            .draw();
        }
      },
      error: function () {
        alert("Failed to delete the account. Please try again later.");
      },
    });
  });

  // Function to handle batch deletion
  $(".delete-selected-accounts").click(function () {
    let selectedAccounts = [];
    // Iterate over each checked checkbox
    $(".account-checkbox:checked").each(function () {
      selectedAccounts.push($(this).data("account-id"));
    });

    // Send an AJAX request to delete the selected accounts
    $.ajax({
      url: "delete-account.php",
      method: "POST",
      data: { accountIds: selectedAccounts },
      success: function (response) {
        if (response === "success") {
          // Hide the confirmation modal
          $("#deleteConfirmationModal").modal("hide");
          // Show the success modal
          $("#successModal").modal("show");
          // Remove the deleted rows from the table
          $(".account-checkbox:checked").each(function () {
            table.row($(this).closest("tr")).remove().draw();
          });
        }
      },
      error: function () {
        alert(
          "Failed to delete the selected accounts. Please try again later."
        );
      },
    });
  });
});
