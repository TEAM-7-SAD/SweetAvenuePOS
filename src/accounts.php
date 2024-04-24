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
                    <h3 class="accounts text-tiger-orange bg-rose-white">Accounts</h3>
                    <button class="add btn btn-tiger-orange text-white" data-bs-toggle="modal" data-bs-target="#addAccountsModal">+ Add Accounts</button>
                    </div>
                    <div class="table container-lg bg-white">
                      <div class="container">
                        <div class="row justify-content-end">
                          <div class="col-md-4 text-center">
                            <br>
                            <div class="container p-2">
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
                                  <th>Last Name</th>
                                  <th>First Name</th>
                                  <th>Middle Name</th>
                                  <th>Username</th>
                                  <th>Password</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td>Arcilla</td>
                                  <td>Juan</td>
                                  <td>Tamad</td>
                                  <td>Juan123</td>
                                  <td>juan456</td>
                              </tr>
                              <tr>
                                  <td>Arc</td>
                                  <td>Romeo</td>
                                  <td>Damat</td>
                                  <td>romeo123</td>
                                  <td>damat456</td>
                              </tr>
                              <tr>
                                  <td>Arilla</td>
                                  <td>Tamad</td>
                                  <td>Juan</td>
                                  <td>tamad123</td>
                                  <td>arilla456</td>
                              </tr>
                              <tr>
                                  <td>Acilla</td>
                                  <td>Shernan</td>
                                  <td>Gomez</td>
                                  <td>gomez456</td>
                                  <td>shernan123</td>
                              </tr>
                              <tr>
                                <td>Sy</td>
                                <td>Henry</td>
                                <td>Arnold</td>
                                <td>Arnold123</td>
                                <td>Sy456</td>
                              </tr>
                              <tr>
                                <td>Bev</td>
                                <td>Pat</td>
                                <td>Rick</td>
                                <td>Rick123</td>
                                <td>Bev456</td>
                              </tr>
                              <tr>
                                <td>Jordan</td>
                                <td>Lebron</td>
                                <td>James</td>
                                <td>James123</td>
                                <td>jordan456</td>
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
          Are you sure you want to delete this sales permanently?
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

        <!-- Add Accounts Modal -->
    <div class="modal fade" id="addAccountsModal" tabindex="-1" aria-labelledby="addAccountsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountsModalLabel ">Add Accounts</h5>
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
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userName" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="text" class="form-control" id="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveChangesBtn" class="btn btn-tiger-orange text-white" onclick="addNewAccount()">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="script_save.js"></script>
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
    modalBody.innerText = "No sales selected.";
  } else {
    // Loop through selected rows and populate modal body
    selectedRows.each(function(rowData, index) {
      // Create a div for each row
      var rowDiv = document.createElement('div');

      // Create elements for each piece of data
      var lastNameParagraph = document.createElement('p');
      var firstNameParagraph = document.createElement('p');
      var middleNameParagraph = document.createElement('p');
      var usernameParagraph = document.createElement('p');
      var passwordParagraph = document.createElement('p');

      // Set the inner text of the paragraphs to the row data
      lastNameParagraph.innerText = "Last Name: " + rowData[0];
      firstNameParagraph.innerText = "First Name: " + rowData[1];
      middleNameParagraph.innerText = "Middle Name: " + rowData[2];
      usernameParagraph.innerText = "Username: " + rowData[3];
      passwordParagraph.innerText = "Password: " + rowData[4];


      // Append the paragraphs to the row div
      rowDiv.appendChild(lastNameParagraph);
      rowDiv.appendChild(firstNameParagraph);
      rowDiv.appendChild(middleNameParagraph);
      rowDiv.appendChild(usernameParagraph);
      rowDiv.appendChild(passwordParagraph);

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
function addNewAccount() {
    // Get the values from the input fields
    var lastName = document.getElementById('lastName').value.trim();
    var firstName = document.getElementById('firstName').value.trim();
    var middleName = document.getElementById('middleName').value.trim();
    var username = document.getElementById('username').value.trim();
    var password = document.getElementById('password').value.trim();

    // Check if any of the fields are empty
    if (lastName === '' || firstName === '' || middleName === '' || username === '' || password === '') {
        // Show an error message indicating that all fields are required
        document.getElementById('errorContainer').style.display = 'block';
        return; // Exit the function early if any field is empty
    }

    // Add the new row to the DataTable at the bottom
    var table = $('#example').DataTable();
    var newRow = table.row.add([lastName, firstName, middleName, username, password]).draw(false).node();

    // Clear form fields
    document.getElementById('lastName').value = '';
    document.getElementById('firstName').value = '';
    document.getElementById('middleName').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';

    // Close the modal
    $('#addAccountsModal').modal('hide');

    // Hide the error message container if it was displayed
    document.getElementById('errorContainer').style.display = 'none';
}



$(document).ready(function() {
  // Bind event handler to reset modal content on close
  $('#deleteConfirmationModal').on('hidden.bs.modal', function () {
    // Reset modal content to default message
    $('#deleteConfirmationModal .modal-body').text('Are you sure you want to delete this sales permanently?');
  });
});

function confirmDelete() {
  // Get all selected rows
  var selectedRows = $('#example').DataTable().rows('.selected');

  // Check if any rows are selected
  if (selectedRows.any()) {
    // Show the confirmation modal with the Continue button
    $('#deleteConfirmationModal .modal-body').text('Are you sure you want to delete the sales permanently?');
    $('#deleteConfirmationModal .btn-tiger-orange').show();
    $('#deleteConfirmationModal').modal('show');
  } else {
    // No rows are selected, show a message indicating that no rows are selected
    $('#deleteConfirmationModal .modal-body').text('No sales selected.');
    
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
</script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
