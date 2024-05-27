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

// Toggle show password
function togglePassword() {
  const passwordInput = document.getElementById("password");
  const isPassword = passwordInput.type === "password";
  passwordInput.type = isPassword ? "text" : "password";
}

$(document).ready(function () {
  var table = $("#example").DataTable();

  $("#addAccountsModal").on("hidden.bs.modal", function () {
    // Reset the form fields
    $("#addAccountForm").trigger("reset");
    // Clear any previous error messages
    $("#errorContainer").hide().empty();
    // Remove validation classes
    $("#addAccountForm").removeClass("was-validated");
    // Disable the save changes button
    $("#saveChangesBtn").prop("disabled", true);
  });

  // Validation functions
  const isValidName = (name) => /^[A-Za-z]{1,50}$/.test(name);
  const isValidUsername = (username) => /^[^\s]{1,50}$/.test(username);
  const isValidEmail = (email) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email) && email.length <= 50;
  const isValidPassword = (password) => /^[^\s]{8,20}$/.test(password);

  // Enable/Disable Save Changes button based on form validity
  const toggleSaveChangesBtn = () => {
    const formValid =
      isValidName($("#lastName").val()) &&
      isValidName($("#firstName").val()) &&
      isValidName($("#middleName").val()) &&
      isValidUsername($("#username").val()) &&
      isValidEmail($("#email").val()) &&
      isValidPassword($("#password").val());
    $("#saveChangesBtn").prop("disabled", !formValid);
  };

  // Attach input event listeners for validation
  $("#lastName, #firstName, #middleName, #username, #email, #password").on(
    "input",
    function () {
      const input = $(this);
      let isValid = false;
      let errorMessage = "";

      switch (input.attr("id")) {
        case "lastName":
        case "firstName":
        case "middleName":
          isValid = isValidName(input.val());
          if (!isValid)
            errorMessage = "Invalid name. Only letters, max 50 chars.";
          break;
        case "username":
          isValid = isValidUsername(input.val());
          if (!isValid)
            errorMessage = "Invalid username. No spaces, max 50 chars.";
          break;
        case "email":
          isValid = isValidEmail(input.val());
          if (!isValid)
            errorMessage =
              "Invalid email. Basic format, no spaces, max 50 chars.";
          break;
        case "password":
          isValid = isValidPassword(input.val());
          if (!isValid)
            errorMessage = "Invalid password. No spaces, 8-20 chars.";
          break;
      }

      if (isValid) {
        input.removeClass("is-invalid").addClass("is-valid");
        input.next(".invalid-feedback").text("");
      } else {
        input.removeClass("is-valid").addClass("is-invalid");
        input.next(".invalid-feedback").text(errorMessage);
      }

      toggleSaveChangesBtn();
    }
  );

  $("#saveChangesBtn").click(function (event) {
    // Prevent the default form submission
    event.preventDefault();

    var form = $("#addAccountForm")[0];
    if (form.checkValidity() === false) {
      event.stopPropagation();
      $("#errorContainer").show().html("<div>Please fill in all fields.</div>");
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
    var selectedAccounts = [];
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
          selectedAccounts.forEach(function (accountId) {
            table
              .row($('tr[data-id="' + accountId + '"]'))
              .remove()
              .draw();
          });
        } else {
          alert("Failed to delete the selected accounts.");
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
