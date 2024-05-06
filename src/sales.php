<?php
require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';

if(isset($_SESSION['id'])) {

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--Site Meta Information-->
    <meta charset="UTF-8" />
    <title>Sweet Avenue POS</title>
    <!--Mobile Specific Metas-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />   
    <!--CSS-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css" />   
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css">
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>
</head>
  
  <body class="bg-timberwolf">

    <!--Navbar-->
    <?php
    include 'includes/navbar.php';
    ?>

    <!--Main Container-->
    <div class="container-fluid px-0 bg-timberwolf">

      <div class="container-fluid px-0">
        <div class="overflow-hidden flex-column">
          <div class="row overflow-y-auto" style="height: calc(100vh - 94px);">

                  <!--Main Content-->
              <div class="col">
                    <div class="container main-content">
                    <h3 class="sales text-tiger-orange bg-rose-white">All Sales</h3>
                    </div>
                    <div class="table container-lg bg-white">
                      <div class="container">
                        <div class="row justify-content-end">
                          <div class="col-md-4 text-center">
                            <br>
                            <div class="container p-2">
                              <button id="selectAll"  class="btn btn-tiger-orange text-white">Select All</button>
                              <button id="deselectAll" style="display: none; cursor: pointer;"   class="btn btn-tiger-orange text-white">Deselect All</button>
                              <button type="button" class="btn btn-tiger-orange text-white" style="cursor: pointer;"
                              onclick="viewSelected()">View</button>
                              <button type="button" class="btn btn-tiger-orange text-white" style="cursor: pointer;"
                              onclick="confirmDelete()">Delete</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="table-container">
                      <table id="example" class="table">
                          <thead>
                              <tr>
                                  <th>Date</th>
                                  <th>Time</th>
                                  <th>Processed by</th>
                                  <th>Total</th>
                              </tr>
                          </thead>
                          <tbody>
                          <?php 
                          $sql = "SELECT 
                                      transaction.*,
                                      CONCAT(user.first_name, ' ', user.middle_name, ' ', user.last_name) AS full_name,
                                      DATE(transaction.timestamp) AS transaction_date,
                                      TIME_FORMAT(transaction.timestamp, '%h:%i %p') AS transaction_time
                                  FROM 
                                      transaction
                                  JOIN 
                                      user ON transaction.user_id = user.id;";
                          $result = $db->query($sql);

                          while($row = $result->fetch_assoc()) {
                              echo '
                              <tr>
                                  <td>'.$row['transaction_date'].'</td>
                                  <td>'.$row['transaction_time'].'</td>
                                  <td>'.$row['full_name'].'</td>
                                  <td>'.$row['total_amount'].'</td>
                              </tr>
                              ';
                          }                  
                          ?>

                          </tbody>
                          <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total Sales:</strong></td>
                                <td colspan="5"><strong>1000.00</strong></td> 
                            </tr>
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
        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the sales permanently?
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




    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
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
      var dateParagraph = document.createElement('p');
      var timeParagraph = document.createElement('p');
      var processedByParagraph = document.createElement('p');
      var totalParagraph = document.createElement('p');

      // Set the inner text of the paragraphs to the row data
      dateParagraph.innerText = "Date: " + rowData[0];
      timeParagraph.innerText = "Time: " + rowData[1];
      processedByParagraph.innerText = "Processed by: " + rowData[2];
      totalParagraph.innerText = "Total: " + rowData[3];

      // Append the paragraphs to the row div
      rowDiv.appendChild(dateParagraph);
      rowDiv.appendChild(timeParagraph);
      rowDiv.appendChild(processedByParagraph);
      rowDiv.appendChild(totalParagraph);

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
    $('#deleteConfirmationModal .modal-body').text('Are you sure you want to delete the sales permanently?');
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