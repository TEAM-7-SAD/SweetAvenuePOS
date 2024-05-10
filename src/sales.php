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
                    <div class="container main-content mt-5 mb-4">
                    <h3 class="text-medium-brown fw-bolder text-capitalize">Sales</h3>  
                    </div>
                    <div class="table container-lg bg-white">
                      <div class="container">
                        <div class="row justify-content-end">
                          <div class="col-md-4 text-center">
                            <br>
                            <div class="container p-2">
                              <button id="deselectAll" style="display: none; cursor: pointer;"   class="btn btn-tiger-orange text-white">Deselect All</button>
                              <button type="button" class="btn btn-carbon-grey fw-semibold px-3 py-2 view-account"
                              disabled>View</button>
                              <button class="btn btn-danger fw-semibold px-3 py-2 delete-sale"
                                data-sale-id="<?php echo $row['id']; ?>">Delete</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="table-container">
                      <table id="example" class="table">
                          <thead>
                              <tr>
                                  <th><th>
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
                                  <td><input type="checkbox" class="sale-checkbox" data-sale-id="'.$row['id'].'"></td>
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
                                <td colspan="5"><strong>0.00</strong></td> 
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

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmation</h5>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this sale permanently?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-tiger-orange text-white delete-selected-sales"
              id="confirmDeleteBtn">Continue</button>
            <input type="hidden" id="saleIdToDelete">
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
    

    <script>
      new DataTable('#example', {
        responsive: true
      });
    </script>
    <!-- Deleting of Sales -->
    <script>
        $(document).ready(function() {
            // Disable delete button by default
            $('.delete-sale').prop('disabled', true);

            // Add event listener to table rows for row selection
            $('.selectable').click(function() {
                // Toggle checkbox when clicking anywhere on the row
                $(this).find('.sale-checkbox').prop('checked', !$(this).find('.sale-checkbox').prop('checked'));
                // Check if at least one checkbox is checked
                var anyChecked = $('.sale-checkbox:checked').length > 0;
                // Enable or disable the delete button based on checkbox status
                $('.delete-sale').prop('disabled', !anyChecked);
            });

            // Add event listener to delete buttons
            $('.delete-sale').click(function() {
                // Show the confirmation modal
                $('#deleteConfirmationModal').modal('show');
                // Set the data-account-id attribute of the continue button in the modal
                $('#confirmDeleteBtn').attr('data-sale-id', $(this).data('sale-id'));
            });


            // Function to handle single account deletion
            $('#confirmDeleteBtn').click(function() {
                var saleId = $('#deleteConfirmationModal').data('sale-id');

                // Send an AJAX request to delete the selected account
                $.ajax({
                    url: 'delete-sale.php',
                    method: 'POST',
                    data: {saleIds: [saleId]},
                    success: function(response) {
                        if (response === 'success') {
                            // Hide the confirmation modal
                            $('#deleteConfirmationModal').modal('hide');
                            // Show the success modal
                            $('#successModal').modal('show');
                            // Remove the deleted row from the table
                            $('tr[data-id="' + saleId + '"]').remove();
                        }
                    },
                    error: function() {
                        alert('Failed to delete the sale. Please try again later.');
                    }
                });
            });

            // Function to handle batch deletion
            $('.delete-selected-sales').click(function() {
                var selectedAccounts = [];
                // Iterate over each checked checkbox
                $('.sale-checkbox:checked').each(function() {
                    selectedAccounts.push($(this).data('sale-id'));
                });

                // Send an AJAX request to delete the selected accounts
                $.ajax({
                    url: 'delete-sale.php',
                    method: 'POST',
                    data: {saleIds: selectedSales},
                    success: function(response) {
                        if (response === 'success') {
                            // Hide the confirmation modal
                            $('#deleteConfirmationModal').modal('hide');
                            // Show the success modal
                            $('#successModal').modal('show');
                            // Remove the deleted rows from the table
                            selectedSales.forEach(function(saleId) {
                                $('tr[data-id="' + accountId + '"]').remove();
                            });
                        } else {
                            alert('Failed to delete the selected sales.');
                        }
                    },
                    error: function() {
                        alert('Failed to delete the selected sales. Please try again later.');
                    }
                });
            });
        });
    </script>
  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
