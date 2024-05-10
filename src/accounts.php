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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="styles/main.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css">
  <!--Site Icon-->
  <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png" />
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
                <div class="input-group mt-5 mb-4 d-flex justify-content-between align-items-center">
                  <h3 class="text-medium-brown fw-bolder text-capitalize">accounts</h3>
                </div>
              </div>
              <div class="table container-lg bg-white">
                <div class="container">
                  <div class="row justify-content-end">
                    <div class="col-md-4 text-center">
                      <br>
                      <div class="container p-2">
                        <button class="btn btn-outline-medium-brown fw-semibold px-3 py-2"
                          data-bs-toggle="modal" data-bs-target="#addAccountsModal">+ Add Account</button>
                        <button type="button" class="btn btn-carbon-grey fw-semibold px-3 py-2 edit-account"
                          disabled>Edit</button>
                        <button class="btn btn-danger fw-semibold px-3 py-2 delete-account"
                          data-account-id="<?php echo $row['id']; ?>">Delete</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="successMessage" class="alert alert-success" style="display: none;" role="alert"></div>

                <div class="table-container">
                  <table id="example" class="table">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Email Address</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                              $sql = "SELECT * FROM user";
                              $result = $db->query($sql);

                              while($row = $result->fetch_assoc()) {
                                  echo '
                                  <tr data-id="'.$row['id'].'" class="selectable">
                                      <td><input type="checkbox" class="account-checkbox" data-account-id="'.$row['id'].'"></td>
                                      <td>'.$row['last_name'].'</td>
                                      <td>'.$row["first_name"].'</td>
                                      <td>'.$row['middle_name'].'</td>
                                      <td>'.$row['email'].'</td>
                                  </tr>
                                  ';
                              }
                              ?>
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
    
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmation</h5>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this account permanently?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-tiger-orange text-white delete-selected-accounts"
              id="confirmDeleteBtn">Continue</button>
            <input type="hidden" id="accountIdToDelete">
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
            Account successfully deleted.
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
      <div class="modal-dialog px-3">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addAccountsModalLabel">Add a new account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="addAccountForm">
            <div class="modal-body">
              <div id="errorContainer" class="alert alert-danger" style="display: none;" role="alert">
                <div>
                  <img src="images/x-circle.svg">
                  An error occurred.
                </div>
              </div>

              <div class="form-floating py-3">
                <input type="text" name="last_name" class="form-control" id="lastName" required>
                <label for="lastName" class="form-label text-carbon-grey fw-medium">Last Name<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="text" name="first_name" class="form-control" id="firstName" required>
                <label for="firstName" class="form-label text-carbon-grey fw-medium">First Name<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="text" name="middle_name" class="form-control" id="middleName" required>
                <label for="middleName" class="form-label text-carbon-grey fw-medium">Middle Name<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="text" name="username" class="form-control" id="username" required>
                <label for="userName" class="form-label text-carbon-grey fw-medium">Username<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="password" name="password" class="form-control" id="password" required>
                <label for="password" class="form-label text-carbon-grey fw-medium">Password<span
                    style="color: red;"> *</span></label>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3"
                data-bs-dismiss="modal" aria-label="Close">cancel</button>
              <button type="button" id="saveChangesBtn"
                class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">add account</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModal"
      aria-hidden="true">
      <div class="modal-dialog px-3">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editAccountModal">Edit Account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="updateAccountForm">
            <div class="modal-body">
              <div class="form-floating py-3">
                <input type="text" name="last_name" class="form-control" id="editLastName" required>
                <label for="editLastName" class="form-label text-carbon-grey fw-medium">Last Name<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="text" name="first_name" class="form-control" id="editfirstName" required>
                <label for="editfirstName" class="form-label text-carbon-grey fw-medium">First Name<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="text" name="middle_name" class="form-control" id="editmiddleName" required>
                <label for="editmiddleName" class="form-label text-carbon-grey fw-medium">Middle Name<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="text" name="username" class="form-control" id="editusername" required>
                <label for="editusername" class="form-label text-carbon-grey fw-medium">Username<span
                    style="color: red;"> *</span></label>
              </div>

              <div class="form-floating py-3">
                <input type="password" name="password" class="form-control" id="editpassword" required>
                <label for="editpassword" class="form-label text-carbon-grey fw-medium">Password<span
                    style="color: red;"> *</span></label>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3"
                data-bs-dismiss="modal" aria-label="Close">cancel</button>
              <button type="button" id="updateChangesBtn"
                class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>

    <!-- Adding of Accounts -->
    <script>
      $(document).ready(function () {
          var table = $('#example').DataTable();
      });

      $(document).ready(function () {
          $('#addAccountsModal').on('hidden.bs.modal', function () {
              // Reset the form fields
              $('#addAccountForm').trigger('reset');
              // Clear any previous error messages
              $('#errorContainer').hide().empty();
          });

          $('#saveChangesBtn').click(function () {
              // Validate form inputs
              var lastName = $('#lastName').val();
              var firstName = $('#firstName').val();
              var middleName = $('#middleName').val();
              var username = $('#username').val();
              var password = $('#password').val();

              if (lastName.trim() === '' || firstName.trim() === '' || middleName.trim() === '' || username.trim() === '' || password.trim() === '') {
                  $('#errorContainer').show().html('<div>Please fill in all fields.</div>');
                  return; // Stop form submission if any field is empty
              }

              // Serialize the form data
              var formData = $('#addAccountForm').serialize();

              // Send an AJAX request
              $.ajax({
                  url: 'add-account.php',
                  type: 'POST',
                  data: formData,
                  success: function (response) {
                      // Insert the new row into the table
                      $('#example tbody').append(response);

                      // Close the modal
                      $('#addAccountsModal').modal('hide');

                      // Show success message
                      $('#successMessage').text('Account added successfully').fadeIn().delay(2000).fadeOut();
                  },
                  error: function (xhr, status, error) {
                      // Handle errors if any
                      console.error(xhr.responseText);
                  }
              });
          });
      });
    </script>

    <!-- Deleting of Accounts -->
    <script>
        $(document).ready(function() {
            // Disable delete button by default
            $('.delete-account').prop('disabled', true);

            // Add event listener to table rows for row selection
            $('.selectable').click(function() {
                // Toggle checkbox when clicking anywhere on the row
                $(this).find('.account-checkbox').prop('checked', !$(this).find('.account-checkbox').prop('checked'));
                // Check if at least one checkbox is checked
                var anyChecked = $('.account-checkbox:checked').length > 0;
                // Enable or disable the delete button based on checkbox status
                $('.delete-account').prop('disabled', !anyChecked);
            });

            // Add event listener to delete buttons
            $('.delete-account').click(function() {
                // Show the confirmation modal
                $('#deleteConfirmationModal').modal('show');
                // Set the data-account-id attribute of the continue button in the modal
                $('#confirmDeleteBtn').attr('data-account-id', $(this).data('account-id'));
            });


            // Function to handle single account deletion
            $('#confirmDeleteBtn').click(function() {
                var accountId = $('#deleteConfirmationModal').data('account-id');

                // Send an AJAX request to delete the selected account
                $.ajax({
                    url: 'delete-account.php',
                    method: 'POST',
                    data: {accountIds: [accountId]},
                    success: function(response) {
                        if (response === 'success') {
                            // Hide the confirmation modal
                            $('#deleteConfirmationModal').modal('hide');
                            // Show the success modal
                            $('#successModal').modal('show');
                            // Remove the deleted row from the table
                            $('tr[data-id="' + accountId + '"]').remove();
                        }
                    },
                    error: function() {
                        alert('Failed to delete the account. Please try again later.');
                    }
                });
            });

            // Function to handle batch deletion
            $('.delete-selected-accounts').click(function() {
                var selectedAccounts = [];
                // Iterate over each checked checkbox
                $('.account-checkbox:checked').each(function() {
                    selectedAccounts.push($(this).data('account-id'));
                });

                // Send an AJAX request to delete the selected accounts
                $.ajax({
                    url: 'delete-account.php',
                    method: 'POST',
                    data: {accountIds: selectedAccounts},
                    success: function(response) {
                        if (response === 'success') {
                            // Hide the confirmation modal
                            $('#deleteConfirmationModal').modal('hide');
                            // Show the success modal
                            $('#successModal').modal('show');
                            // Remove the deleted rows from the table
                            selectedAccounts.forEach(function(accountId) {
                                $('tr[data-id="' + accountId + '"]').remove();
                            });
                        } else {
                            alert('Failed to delete the selected accounts.');
                        }
                    },
                    error: function() {
                        alert('Failed to delete the selected accounts. Please try again later.');
                    }
                });
            });
        });
    </script>

    <!-- Updating of Accounts -->
    <script>
      $(document).ready(function() {
          // Disable edit button by default
          $('.edit-account').prop('disabled', true);

          // Add event listener to table rows for row selection
          $('.selectable').click(function() {
              // Toggle checkbox when clicking anywhere on the row
              $(this).find('.account-checkbox').prop('checked', !$(this).find('.account-checkbox').prop('checked'));
              // Check if at least one checkbox is checked
              var anyChecked = $('.account-checkbox:checked').length > 0;
              // Enable or disable the delete and edit buttons based on checkbox status
              $('.delete-account').prop('disabled', !anyChecked);
              $('.edit-account').prop('disabled', anyChecked != 1);
        });

        $(document).ready(function() {
            // Add event listener to edit buttons
            $('.edit-account').click(function() {
                // Fetch data from the selected row and populate the edit modal's input fields
                var row = $(this).closest('tr');
                $('#editLastName').val(row.find('td:eq(1)').text());
                $('#editfirstName').val(row.find('td:eq(2)').text());
                $('#editmiddleName').val(row.find('td:eq(3)').text());
                $('#editusername').val(row.find('td:eq(4)').text());
                $('#editpassword').val(row.find('td:eq(5)').text());

                // Show the edit modal
                $('#editAccountModal').modal('show');
            });

            // Function to handle edit submission
            $('#updateChangesBtn').click(function() {
                // Fetch edited data from the edit modal's input fields
                var editedData = {
                    last_name: $('#editLastName').val(),
                    first_name: $('#editfirstName').val(),
                    middle_name: $('#editmiddleName').val(),
                    username: $('#editusername').val(),
                    password: $('#editpassword').val()
                };

                // Send an AJAX request to update the selected account
                $.ajax({
                    url: 'update-account.php',
                    method: 'POST',
                    data: editedData,
                    success: function(response) {
                        // Assuming the response contains updated data from the server
                        // Update the row in the table with the edited data
                        var row = $('.selectable[data-id="' + response.id + '"]');
                        row.find('td:eq(1)').text(response.last_name);
                        row.find('td:eq(2)').text(response.first_name);
                        row.find('td:eq(3)').text(response.middle_name);
                        row.find('td:eq(4)').text(response.username);
                        row.find('td:eq(5)').text(response.password);

                        // Hide the edit modal
                        $('#editAccountModal').modal('hide');
                    },
                    error: function() {
                        alert('Failed to update the account. Please try again later.');
                    }
                });
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
