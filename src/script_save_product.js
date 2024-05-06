document.addEventListener('DOMContentLoaded', function() {
  var saveChangesBtn = document.getElementById('saveChangesBtn');
  var errorContainer = document.getElementById('errorContainer'); // Existing error container
  var addProductModal = document.getElementById('addProductModal');
  var form = document.querySelector('#addProductModal form');
  var closeBtn = addProductModal.querySelector('.modal-header button[data-bs-dismiss="modal"]');
  var imageInput = document.getElementById('image'); // Get the image input element
  var table = $('#example').DataTable();

  function addNewProduct() {
    // Check if an image is selected
    if (!imageInput.files[0]) {
      displayError('Please select an image.');
      return; // Exit the function if no image is selected
    }

    // Retrieve input values from the form
    var name = document.getElementById('name').value.trim();
    var category = document.getElementById('category').value.trim();
    var price = document.getElementById('price').value.trim();

    // Check if any field is empty
    if (name === '') {
      displayError('Please enter a name for the product.');
      return; // Exit the function if name is empty
    }

    if (category === '') {
      displayError('Please select a category for the product.');
      return; // Exit the function if category is empty
    }

    if (price === '') {
      displayError('Please enter a price for the product.');
      return; // Exit the function if price is empty
    }

    // Read the selected image file as a data URL
    var reader = new FileReader();
    reader.onload = function(event) {
      var imageDataUrl = event.target.result; // Get the Base64-encoded image data
      addProductRow(imageDataUrl, name, category, price); // Call a function to add the new product row
    };
    reader.readAsDataURL(imageInput.files[0]); // Read the selected image file

    // Close the modal
    var modal = bootstrap.Modal.getInstance(addProductModal);
    modal.hide();

    // Reset the form fields
    form.reset();

    // Hide the error message
    errorContainer.style.display = 'none';

    // Remove any existing modal backdrop
    removeModalBackdrop();
  }

  saveChangesBtn.addEventListener('click', addNewProduct);

  // Event listener for modal hide event
  addProductModal.addEventListener('hide.bs.modal', function() {
    // Hide the error message when the modal is hidden
    errorContainer.style.display = 'none';

    // Remove any existing modal backdrop
    removeModalBackdrop();
  });

  // Event listener for close button click
  closeBtn.addEventListener('click', function() {
    // Reset the form fields when the modal close button is clicked
    form.reset();
    // Hide the error message
    errorContainer.style.display = 'none';

    // Remove any existing modal backdrop
    removeModalBackdrop();
  });

  // Function to add a new product row to the table
  function addProductRow(imageDataUrl, name, category, price) {
    // Create a new row for the table
    var newRow = document.createElement('tr');

    // Add the input values to the new row
    newRow.innerHTML = `
      <td><img src="${imageDataUrl}" style="max-width: 50px; max-height: 50px;" alt="Product Image"></td>
      <td>${name}</td>
      <td>${category}</td>
      <td>${price}</td>
    `;

    // Append the new row to the table body
    var tableBody = document.querySelector('#example tbody');
    tableBody.appendChild(newRow);
  }

  // Function to remove modal backdrop
  function removeModalBackdrop() {
    var modalBackdrops = document.querySelectorAll('.modal-backdrop');
    // Remove all modal backdrops
    modalBackdrops.forEach(function(backdrop) {
      backdrop.parentNode.removeChild(backdrop);
    });
  }

  // Function to display error message within existing error container
  function displayError(message) {
    // Set the error message text within the existing error container
    errorContainer.innerText = message;
    // Make the error container visible
    errorContainer.style.display = 'block';
  }
});
