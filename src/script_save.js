document.addEventListener('DOMContentLoaded', function() {
  var saveChangesBtn = document.getElementById('saveChangesBtn');
  var errorContainer = document.getElementById('errorContainer');
  var addAccountsModal = document.getElementById('addAccountsModal');
  var form = document.querySelector('#addAccountsModal form');
  var closeBtn = addAccountsModal.querySelector('.modal-header button[data-bs-dismiss="modal"]');

  saveChangesBtn.addEventListener('click', function() {
    // Retrieve input values from the form
    var lastName = document.getElementById('lastName').value;
    var firstName = document.getElementById('firstName').value;
    var middleName = document.getElementById('middleName').value;
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    // Check if any field is empty
    if (lastName === '' || firstName === '' || middleName === '' || username === '' || password === '') {
      errorContainer.style.display = 'block';
      return; // Exit the function if any field is empty
    }

    // Remove any existing modal backdrop
    removeModalBackdrop();

    // Create a new row for the table
    var newRow = document.createElement('tr');

    // Add the input values to the new row
    newRow.innerHTML = `
      <td>${lastName}</td>
      <td>${firstName}</td>
      <td>${middleName}</td>
      <td>${username}</td>
      <td>${password}</td>
    `;

    // Append the new row to the table body
    var tableBody = document.querySelector('#example tbody');
    tableBody.appendChild(newRow);

    // Close the modal
    var modal = bootstrap.Modal.getInstance(addAccountsModal);
    modal.hide();

    // Reset the form fields
    form.reset();

    // Hide the error message
    errorContainer.style.display = 'none';
  });

  // Event listener for modal hide event
  addAccountsModal.addEventListener('hide.bs.modal', function () {
    // Remove modal backdrop when the modal is hidden
    removeModalBackdrop();
    // Hide the error message when the modal is hidden
    errorContainer.style.display = 'none';
  });

  // Event listener for close button click
  closeBtn.addEventListener('click', function() {
    // Reset the form fields when the modal close button is clicked
    form.reset();
    // Hide the error message
    errorContainer.style.display = 'none';
  });

  // Function to remove modal backdrop
  function removeModalBackdrop() {
    var modalBackdrops = document.querySelectorAll('.modal-backdrop');
    // Remove all modal backdrops
    modalBackdrops.forEach(function(backdrop) {
      backdrop.parentNode.removeChild(backdrop);
    });
  }
});
