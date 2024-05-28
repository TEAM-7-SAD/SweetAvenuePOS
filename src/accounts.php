<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

if(isset($_SESSION['id'])) {

  $sql = "SELECT * FROM user";
  $result = $db->query($sql);
  $row = $result->fetch_assoc();

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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
  <!--Site Icon-->
  <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png" />
</head>

<body class="bg-timberwolf">

  <!--Navbar-->
  <?php
    include FileUtils::normalizeFilePath('includes/navbar.php');
    include FileUtils::normalizeFilePath('includes/preloader.html');
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
                
              <div class="row">
                <div class="col mt-4 align-items-end">
                  <div class="d-flex justify-content-end">
                    <button class="btn btn-outline-medium-brown fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#addAccountsModal">+ Add Account</button>
                    <div class="mx-2"></div>
                    <button class="btn btn-danger fw-semibold delete-account" data-account-id="<?php echo $row['id']; ?>">Delete</button>
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
                      $db->close();
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
    <div class="modal fade" id="addAccountsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title text-carbon-grey fw-bold" id="addAccountsModalLabel">NEW ACCOUNT</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="location.reload()"></button>
          </div>
          <form id="addAccountForm" class="needs-validation" novalidate>
            <div class="modal-body mx-4">
              <p class="px-1 fs-5 fw-medium text-carbon-grey">Fill in the credentials needed to create a new account.</p>
              <div class="invalid-feedback fw-medium mb-3 ps-1" id="errorContainer"></div>
              <div class="input-group">
                <div class="form-floating py-3 pe-5">
                  <input type="text" name="last_name" class="form-control rounded-3" id="lastName" required>
                  <label for="lastName" class="form-label text-carbon-grey fw-medium">Last Name<span
                      style="color: red;"> *</span></label>
                  <div class="valid-feedback font-13">
                    Looks right!
                  </div>
                  <div class="invalid-feedback font-13" id="errorLastName">
                    <!-- Display error messages here -->
                  </div>
                </div>
                <div class="form-floating py-3 pe-5">
                  <input type="text" name="first_name" class="form-control rounded-3" id="firstName" required>
                  <label for="firstName" class="form-label text-carbon-grey fw-medium">First Name<span
                      style="color: red;"> *</span></label>
                  <div class="valid-feedback font-13">
                    Looks right!
                  </div>
                  <div class="invalid-feedback font-13" id="errorFirstName">
                    <!-- Display error messages here -->
                  </div>
                </div>  
                <div class="form-floating py-3">
                  <input type="text" name="middle_name" class="form-control rounded-3" id="middleName" required>
                  <label for="middleName" class="form-label text-carbon-grey fw-medium">Middle Name</label>
                  <div class="valid-feedback font-13">
                    Looks right!
                  </div>
                  <div class="invalid-feedback font-13" id="errorMiddleName">
                    <!-- Display error messages here -->
                  </div>
                </div>
              </div>

              <div class="input-group">
                <div class="form-floating py-3 pe-5">
                  <input type="text" name="username" class="form-control rounded-3" id="username" required>
                  <label for="userName" class="form-label text-carbon-grey fw-medium">Username<span
                      style="color: red;"> *</span></label>
                      <div class="valid-feedback font-13">
                    Looks right!
                  </div>
                  <div class="invalid-feedback font-13" id="errorUsername">
                    <!-- Display error messages here -->
                  </div>
                </div> 
                <div class="form-floating py-3">
                  <input type="email" name="email" class="form-control rounded-3" id="email" required>
                  <label for="email" class="form-label text-carbon-grey fw-medium">Email Address<span
                      style="color: red;"> *</span></label>
                      <div class="valid-feedback font-13">
                    Looks right!
                  </div>
                  <div class="invalid-feedback font-13" id="errorEmailAddress">
                    <!-- Display error messages here -->
                  </div>
                </div>                    
              </div>

              <div class="input-group">
                <div class="form-floating py-3 pe-4">
                  <input type="password" name="password" class="form-control rounded-3" id="password" required>
                  <label for="password" class="form-label text-carbon-grey fw-medium">Password<span
                      style="color: red;"> *</span></label>
                  <div class="valid-feedback font-13">
                    Looks right!
                  </div>
                  <div class="invalid-feedback font-13" id="errorPassword">
                    <!-- Display error messages here -->
                  </div>
                </div>
                <div class="form-check align-self-center ms-2">
                  <input class="form-check-input fs-3" type="checkbox" id="showPassword" onclick="togglePassword()">
                  <label class="form-check-label text-carbon-grey fw-medium" for="showPassword">Show</label>
                </div>              
              </div>


            </div>
            <div class="modal-footer">
              <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3"
                data-bs-dismiss="modal" aria-label="Close" onclick="location.reload()">cancel</button>
              <button type="button" id="saveChangesBtn"
                class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">add account</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CDN Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <!-- Custom JavacScript -->
    <script src="javascript/preloader.js"></script>
    <script src="javascript/accounts.js"></script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
