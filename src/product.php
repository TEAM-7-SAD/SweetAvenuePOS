<?php
require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';

if(isset($_SESSION['id'])) {

?>

<!DOCTYPE html>
<html lang="en">

  <!--Head elements-->
  <?php
  include 'includes/head-element.php';
  ?>
  <style>
    .image-cell img {
    max-width: 50px; /* Set maximum width for the image */
    max-height: 50px; /* Set maximum height for the image */
    width: auto; /* Allow the image to resize proportionally */
    height: auto; /* Allow the image to resize proportionally */
}
  </style>
  <body class="bg-timberwolf">

    <!--Navbar-->
    <?php
    include 'includes/navbar.php';
    ?>

  <body>

    <!--Main Container-->
    <div class="container-fluid px-0 bg-timberwolf">

      <div class="container-fluid px-0">
        <div class="overflow-hidden flex-column">
          <div class="row overflow-y-auto" style="height: calc(100vh - 94px);">

                  <!--Main Content-->
              <div class="col">
                    <div class="container main-content">
                    <h3 class="accounts text-tiger-orange bg-rose-white">All Products</h3>
                    <button class="add btn btn-tiger-orange text-white" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Product</button>
                    </div>
                    <div class="table container-lg bg-white">
                      <div class="container">
                        <div class="row justify-content-end">
                          <div class="col-md-4 text-center">
                            <br>
                            <div class="container p-2">
                            <button id="edit" class="btn btn-tiger-orange text-white" onclick="editSelected()">Edit</button>
                              <button id="selectAll"  class="btn btn-tiger-orange text-white">Select All</button>
                              <button id="deselectAll" style="display: none; cursor: pointer;"   class="btn btn-tiger-orange text-white">Deselect All</button>
                              <button id="viewBtn" type="button" class="btn btn-tiger-orange text-white" style="cursor: pointer;"
                              onclick="viewSelected()">View</button>
                              <button id="deleteBtn" type="button" class="btn btn-tiger-orange text-white" style="cursor: pointer;"
                              onclick="confirmDelete()">Delete</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="table-container">
                      <table id="example" class="table">
                          <thead>
                              <tr>
                                  <th>Image</th>
                                  <th>Name</th>
                                  <th>Category</th>
                                  <th>Price</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td class="image-cell"><img src="images/sweet-avenue-logo.png"></td>
                                  <td>Mocha</td>
                                  <td>Coffee and Blended/Hot</td>
                                  <td>90.00</td>
                              </tr>
                              <tr>
                                  <td class="image-cell"><img src="images/sweet-avenue-logo.png"></td>
                                  <td>Spanish Latte</td>
                                  <td>Coffee and Blended/Iced</td>
                                  <td>100.00</td>
                              </tr>
                              <tr>
                                  <td class="image-cell"><img src="images/sweet-avenue-logo.png"></td>
                                  <td>Taro</td>
                                  <td>Froyo</td>
                                  <td>110.00</td>
                              </tr>
                              <tr>
                                  <td class="image-cell"><img src="images/sweet-avenue-logo.png"></td>
                                  <td>Hazelnut</td>
                                  <td>Coffee and Blended/Iced</td>
                                  <td>100.00</td>
                              </tr>
                              <tr>
                                <td class="image-cell"><img src="images/sweet-avenue-logo.png"></td>
                                <td>Fries Overload</td>
                                <td>Snacks and Rice Meals</td>
                                <td>180.00</td>
                              </tr>
                              <tr>
                                <td class="image-cell"><img src="images/sweet-avenue-logo.png"></td>
                                <td>Mocha</td>
                                <td>Coffee and Blended/Iced</td>
                                <td>120.00</td>
                              </tr>
                              <tr>
                                <td class="image-cell"><img src="images/sweet-avenue-logo.png"></td>
                                <td>Salted Caramel</td>
                                <td>Coffee and Blended/Iced</td>
                                <td>100.00</td>
                              </tr>
                          </tbody>
                          <tfoot>
                        </tfoot>
                      </table>
                      </div>
                      <br>
                    </div>    
                </div>
              </div> 
          </div>
        </div>   
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Sale Details</h1>
          </div>
          <div class="modal-body">
            <!-- Modal body content -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  
  
  <!-- Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmation</h5>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this product permanently?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-tiger-orange text-white" onclick="deleteSelectedRows()">Continue</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">Success</h5>
        </div>
        <div class="modal-body">
          Sale successfully deleted.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

        <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel ">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="errorContainer" class="alert alert-danger" style="display: none;" role="alert">
                    <div>
                      <img src="images/x-circle.svg">
                       An error occured.
                    </div>
                  </div>                  
                    <form>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" accept=".jpg, .jpeg, .png, .svg" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" required>
                            <option value="">Select Category</option>
                            <option value="Snack and Rice Meals">Snack and Rice Meals </option>
                            <option value="Bread, Cakes, and Pastries">Bread, Cakes, and Pastries</option>
                            <option value="Coffee and Blended/Hot">Coffee and Blended/Hot</option>
                            <option value="Coffee and Blended/Iced">Coffee and Blended/Iced</option>
                            <option value="Froyo">Froyo</option>
                        </select>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="price" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveChangesBtn" class="btn btn-tiger-orange text-white" onclick="addNewProduct()">Add</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editErrorContainer" class="alert alert-danger" style="display: none;" role="alert">
                        <div>
                            <img src="images/x-circle.svg">
                            An error occurred.
                        </div>
                    </div>
                    <form id="editProductForm">
                        <div class="mb-3">
                            <label for="editImage" class="form-label">Image</label>
                            <input type="file" class="form-control" id="editImage" accept=".jpg, .jpeg, .png, .svg">
                            <img id="editImagePreview" src="" alt="Preview Image" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Category</label>
                            <select class="form-select" id="editCategory" required>
                                <option value="Snack and Rice Meals">Snack and Rice Meals </option>
                                <option value="Bread, Cakes, and Pastries">Bread, Cakes, and Pastries</option>
                                <option value="Coffee and Blended/Hot">Coffee and Blended/Hot</option>
                                <option value="Coffee and Blended/Iced">Coffee and Blended/Iced</option>
                                <option value="Froyo">Froyo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="text" class="form-control" id="editPrice" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveChangesEditBtn" class="btn btn-tiger-orange text-white" onclick="saveChangesEdit()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Success Modal for Edit -->
    <div class="modal fade" id="editSuccessModal" tabindex="-1" aria-labelledby="editSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSuccessModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    Product successfully edited.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- No Product Selected Modal -->
    <div class="modal fade" id="noProductSelectedModal" tabindex="-1" aria-labelledby="noProductSelectedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noProductSelectedModalLabel">No Product Selected</h5>
                </div>
                <div class="modal-body">
                    No product has been selected. Please select a product to edit.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="script_save_product.js"></script>
    <script src="Select_Deselect.js"></script>

<script>
  new DataTable('#example', {
    responsive: true
  });
function viewSelected() {
  // Get all selected rows
  var selectedRows = $('#example').DataTable().rows('.selected').data();
  // Get the modal body element
  var modalBody = document.querySelector('.modal-body');
  
  // Clear previous content
  modalBody.innerHTML = '';

  // Check if there are no selected rows
  if (selectedRows.length === 0) {
    // If no rows are selected, display a message in the modal
    modalBody.innerText = "No product selected.";
  } else {
    // Loop through selected rows and populate modal body
    selectedRows.each(function(rowData, index) {
      // Create a div for each row
      var rowDiv = document.createElement('div');

      // Create elements for each piece of data
      var imageParagraph = document.createElement('p');
      var nameParagraph = document.createElement('p');
      var categoryParagraph = document.createElement('p');
      var priceParagraph = document.createElement('p');

      // Set the inner text of the paragraphs to the row data
      imageParagraph.innerText = "Image: " + rowData[0];
      nameParagraph.innerText = "Name: " + rowData[1];
      categoryParagraph.innerText = "Category: " + rowData[2];
      priceParagraph.innerText = "Price: " + rowData[3];


      // Append the paragraphs to the row div
      rowDiv.appendChild(imageParagraph);
      rowDiv.appendChild(nameParagraph);
      rowDiv.appendChild(categoryParagraph);
      rowDiv.appendChild(priceParagraph);

      // Append the row div to the modal body
      modalBody.appendChild(rowDiv);

      // Add a horizontal line after each row except for the last one
      if (index < selectedRows.length - 1) {
        modalBody.appendChild(document.createElement('hr'));
      }
    });
  }

  // Display the modal
  var modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
  modal.show();
}



$(document).ready(function() {
  // Bind event handler to reset modal content on close
  $('#deleteConfirmationModal').on('hidden.bs.modal', function () {
    // Reset modal content to default message
    $('#deleteConfirmationModal .modal-body').text('Are you sure you want to delete this product permanently?');
  });
});

function confirmDelete() {
  // Get all selected rows
  var selectedRows = $('#example').DataTable().rows('.selected');

  // Check if any rows are selected
  if (selectedRows.any()) {
    // Show the confirmation modal with the Continue button
    $('#deleteConfirmationModal .modal-body').text('Are you sure you want to delete the product permanently?');
    $('#deleteConfirmationModal .btn-tiger-orange').show();
    $('#deleteConfirmationModal').modal('show');
  } else {
    // No rows are selected, show a message indicating that no rows are selected
    $('#deleteConfirmationModal .modal-body').text('No product selected.');
    
    // Hide the Continue button
    $('#deleteConfirmationModal .btn-tiger-orange').hide();

    // Show the confirmation modal without the Continue button
    $('#deleteConfirmationModal').modal('show');
  }
}

function deleteSelectedRows() {
  // Get all selected rows
  var selectedRows = $('#example').DataTable().rows('.selected');

  // Check if any rows are selected
  if (selectedRows.any()) {
    // Remove the selected rows from the DataTable
    selectedRows.remove().draw();

    // Hide the confirmation modal
    $('#deleteConfirmationModal').modal('hide');

    // Show the success modal
    $('#successModal').modal('show');
  } else {
    // If no rows are selected, directly show the confirmation modal
    confirmDelete();
  }
}

// Event listener for input changes in the edit modal for the image input
$('#editImage').on('change', function() {
    // Get the selected image file
    var file = this.files[0];

    // Check if a file is selected
    if (file) {
        // Create a FileReader object to read the file
        var reader = new FileReader();

        // Define the onload function for the FileReader
        reader.onload = function(event) {
            // Set the src attribute of the image preview to the data URL of the selected image
            $('#editImagePreview').attr('src', event.target.result);
        };

        // Read the selected image file as a data URL
        reader.readAsDataURL(file);
    }
});


// Function to check if any changes have been made in the edit modal
function checkChanges() {
    // Retrieve initial data from the edit modal fields
    var initialName = $('#editName').data('initial');
    var initialCategory = $('#editCategory').data('initial');
    var initialPrice = $('#editPrice').data('initial');

    // Retrieve current data from the edit modal fields
    var editedName = $('#editName').val().trim();
    var editedCategory = $('#editCategory').val().trim();
    var editedPrice = $('#editPrice').val().trim();

    // Check if any field has been changed
    var changesMade = (initialName !== editedName) || (initialCategory !== editedCategory) || (initialPrice !== editedPrice);

    // Enable/disable the "Save Changes" button based on changesMade
    $('#saveChangesEditBtn').prop('disabled', !changesMade);
}

// Event listener for input changes in the edit modal
$('#editName, #editCategory, #editPrice').on('input', function() {
    // Check for changes whenever there's an input change
    checkChanges();
});

// Function to populate the edit modal with selected row data
function editSelected() {
    // Get data of the selected row
    var selectedRowsData = $('#example').DataTable().rows('.selected').data();

    // Check if any row is selected
    if (selectedRowsData.length === 0) {
        // If no row is selected, show a modal message
        $('#noProductSelectedModal').modal('show');
    } else {
        // Populate the edit modal fields with the data of the first selected row
        var selectedRowData = selectedRowsData[0];
        $('#editName').val(selectedRowData[1]).data('initial', selectedRowData[1]);
        $('#editCategory').val(selectedRowData[2]).data('initial', selectedRowData[2]);
        $('#editPrice').val(selectedRowData[3]).data('initial', selectedRowData[3]);

        // Set the image preview in the edit modal
        var imageUrl = selectedRowData[0];
        $('#editImagePreview').attr('src', imageUrl);

        // Reset the "Save Changes" button state
        $('#saveChangesEditBtn').prop('disabled', true);

        // Show the edit modal
        $('#editProductModal').modal('show');
    }
}

// Event listener to reset the "Save Changes" button state when the edit modal is closed
$('#editProductModal').on('hide.bs.modal', function() {
    // Reset the "Save Changes" button state
    $('#saveChangesEditBtn').prop('disabled', true);
});

// Event listener to hide error message when edit modal is closed
$('#editProductModal').on('hide.bs.modal', function() {
    $('#editErrorContainer').hide(); // Hide error message when modal is closed
});

// Event listener for the "Save Changes" button click
$('#saveChangesEditBtn').on('click', function() {
    // Save changes if the button is enabled
    if (!$(this).prop('disabled')) {
        saveChangesEdit();
    }
});



// Function to save changes after editing
function saveChangesEdit() {
    // Retrieve edited data from the edit modal fields
    var editedName = document.getElementById('editName').value.trim();
    var editedCategory = document.getElementById('editCategory').value.trim();
    var editedPrice = document.getElementById('editPrice').value.trim();
    var editedImage = document.getElementById('editImage').files[0]; // New image file

    // Validation: Check if Name and Price fields are not empty
    var errorMessage = "";
    if (editedName === "") {
        errorMessage += "Name cannot be blank.<br>";
    }
    if (editedPrice === "") {
        errorMessage += "Price cannot be blank.";
    }

    if (errorMessage!== "") {
        // Show error message if Name or Price is blank
        $('#editErrorContainer').html(errorMessage);
        $('#editErrorContainer').show();
        return; // Stop execution if there's an error
    } else {
        $('#editErrorContainer').hide(); // Hide error message if validation succeeds
    }

    // Update the DataTable with the edited data
    var table = $('#example').DataTable();
    var selectedRow = table.row('.selected');
    var rowData = selectedRow.data();
    rowData[1] = editedName;
    rowData[2] = editedCategory;
    rowData[3] = editedPrice;

    // Update image if a new image is selected
    if (editedImage) {
        // Update image URL in rowData
        // Upload the edited image to server (if needed) and get the new image URL
        // Update the 'src' attribute of the image preview
        var reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('editImagePreview').src = event.target.result;
        };
        reader.readAsDataURL(editedImage);
    }

    // Update DataTable
    selectedRow.data(rowData).draw();

    // Hide the edit modal
    $('#editProductModal').modal('hide');

    // Show the success modal
    $('#editSuccessModal').modal('show');
}

// Event listener to hide error message when edit modal is closed
$('#editProductModal').on('hide.bs.modal', function () {
    $('#editErrorContainer').hide(); // Hide error message when modal is closed
});


</script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
