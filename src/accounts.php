<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

if(isset($_SESSION['id'])) {

  $sql = "SELECT email, username FROM user";
  $result = $db->query($sql);

  $emails = array();
  $usernames = array();

  if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $emails[] = $row['email'];
      $usernames[] = $row['username'];
    }
  }

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
  <link rel="stylesheet" href="styles/main.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css">
  <!--Site Icon-->
  <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png" />

    <!-- Bootstrap JavaScript -->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CDN Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <!-- Custom JavacScript -->
    <script src="javascript/preloader.js" defer></script>
    <script src="javascript/accounts.js" defer></script>

  <script>
    // This will be used to check if an email or username is already taken
    const takenEmails = <?php echo json_encode($emails); ?>;
    const takenUsernames = <?php echo json_encode($usernames); ?>;  
  </script>

</head>

<body class="bg-gainsboro">

  <!--Navbar-->
  <?php
    include FileUtils::normalizeFilePath('includes/navbar.php');
    include FileUtils::normalizeFilePath('includes/preloader.html');
    ?>

    <!--Main Container-->
    <div class="container mb-5">

      <!--Main Content-->
      <div class="col-lg-12">
        <div class="main-content">
          <div class="input-group bg-medium-brown py-3 mt-5 d-flex justify-content-between align-items-center rounded-top">
            <div class="text-light fs-4 fw-bold ps-5">ACCOUNTS</div>
            <div class="row">
            <div class="col me-5 align-items-end">
              <div class="d-flex justify-content-end">
                <button class="btn btn-sm btn-outline-light fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#addAccountsModal">              
                  <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-person-fill-add" viewBox="0 0 16 16">
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                    <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                  </svg>
                  <span class="font-13">Add Account</span>
                </button>
                <!-- <div class="mx-2"></div>
                <button class="btn btn-danger fw-semibold delete-account" data-account-id=">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                  </svg>
                  <span>Delete</span>
                </button> -->
              </div>
            </div>
          </div>
          </div>
        </div>

        <div class="container bg-white shadow px-5 py-4 rounded-bottom">                 
          <div id="successMessage" class="alert alert-success" style="display: none;" role="alert"></div>

          <table id="example" class="table table-hover table-striped table-borderless mt-4">
            <thead>
              <tr>
                <th class="text-medium-brown fw-semibold font-15">Last Name</th>
                <th class="text-medium-brown fw-semibold font-15">First Name</th>
                <th class="text-medium-brown fw-semibold font-15">Middle Name</th>
                <th class="text-medium-brown fw-semibold font-15">Email Address</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $sql = "SELECT * FROM user";
              $result = $db->query($sql);
              while($row = $result->fetch_assoc()) {
                  // echo '
                  // <tr data-id="'.$row['id'].'" class="selectable">
                  //     <td><input type="checkbox" class="account-checkbox" data-account-id="'.$row['id'].'"></td>
                  //     <td>'.$row['last_name'].'</td>
                  //     <td>'.$row["first_name"].'</td>
                  //     <td>'.$row['middle_name'].'</td>
                  //     <td>'.$row['email'].'</td>
                  // </tr>
                  // ';
                  echo '
                  <tr>
                      <td class="text-carbon-grey fw-medium font-14">'.$row['last_name'].'</td>
                      <td class="text-carbon-grey fw-medium font-14">'.$row["first_name"].'</td>
                      <td class="text-carbon-grey fw-medium font-14">'.$row['middle_name'].'</td>
                      <td class="text-carbon-grey fw-medium font-14">'.$row['email'].'</td>
                  </tr>
                  ';
              }
              $db->close();
              ?>
            </tbody>

            <tfoot>
            </tfoot>
          </table>
          <br>
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
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-medium-brown">
            <div class="ps-4 d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#FFFF" class="bi bi-person-fill-add" viewBox="0 0 16 16">
                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
              </svg>
              <h5 class="modal-title text-white fw-semibold ps-3" id="addAccountsModalLabel">Add New Account?</h5>  
            </div>

            <button type="button" id="closeAddAccountBtn" class="btn-close bg-white me-2 rounded-5" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="addAccountForm" class="needs-validation" novalidate>
            <div class="modal-body mx-4">
              <p class="fw-medium text-carbon-grey fw-semibold">Fill in the credentials needed to create a new account.</p>

              <div class="input-group mb-3">
                <!-- Last Name -->
                <div class="col pe-5">
                  <label for="lastName" class="form-label text-carbon-grey fw-medium font-13">Last Name<span style="color: red;"> *</span></label>
                  <input type="text" name="last_name" class="form-control shadow-sm" id="lastName" autocomplete="family-name" required>
                  <div class="valid-feedback font-13" id="validLastName">
                    <!-- Display valid messages here -->
                  </div>
                  <div class="invalid-feedback font-13" id="errorLastName">
                    <!-- Display error messages here -->
                  </div>
                </div>

                <!-- First Name -->
                <div class="col pe-5">
                  <label for="firstName" class="form-label text-carbon-grey fw-medium font-13">First Name<span style="color: red;"> *</span></label>
                  <input type="text" name="first_name" class="form-control shadow-sm" id="firstName" autocomplete="given-name" required>
                  <div class="valid-feedback font-13" id="validFirstName">
                    <!-- Display valid messages here -->
                  </div>
                  <div class="invalid-feedback font-13" id="errorFirstName">
                    <!-- Display error messages here -->
                  </div>
                </div>  

                <!-- Middle Name -->
                <div class="col">
                  <label for="middleName" class="form-label text-carbon-grey fw-medium font-13">Middle Name</label>
                  <input type="text" name="middle_name" class="form-control shadow-sm" id="middleName" autocomplete="additional-name" required>
                  <div class="valid-feedback font-13" id="validMiddleName">
                    <!-- Display valid messages here -->
                  </div>
                  <div class="invalid-feedback font-13" id="errorMiddleName">
                    <!-- Display error messages here -->
                  </div>
                </div>
              </div>

              <!-- Username -->
              <div class="input-group mb-3">
                <div class="col pe-5">
                  <label for="username" class="form-label text-carbon-grey fw-medium font-13">Username<span style="color: red;"> *</span></label>
                  <input type="text" name="username" class="form-control shadow-sm" id="username" autocomplete="username" required>
                  <div class="valid-feedback font-13" id="validUsername">
                    <!-- Display valid messages here -->
                  </div>
                  <div class="invalid-feedback font-13" id="errorUsername">
                    <!-- Display error messages here -->
                  </div>
                </div> 

                <!-- Email Address -->
                <div class="col">
                  <label for="email" class="form-label text-carbon-grey fw-medium font-13">Email Address<span style="color: red;"> *</span></label>
                  <input type="email" name="email" class="form-control shadow-sm" id="email" autocomplete="email" required>
                  <div class="valid-feedback font-13" id="validEmailAddress">
                    <!-- Display valid messages here -->
                  </div>
                  <div class="invalid-feedback font-13" id="errorEmailAddress">
                    <!-- Display error messages here -->
                  </div>
                </div>                    
              </div>

              <!-- Password -->
              <div class="input-group mb-2">
                <div class="col-6 pe-4">
                  <label for="password" class="form-label text-carbon-grey fw-medium font-13">Password<span style="color: red;"> *</span></label>
                  <input type="password" name="password" class="form-control shadow-sm" id="password" autocomplete="new-password" required>
                  <div class="valid-feedback font-13" id="validPassword">
                    <!-- Display valid messages here -->
                  </div>
                  <div class="invalid-feedback font-13" id="errorPassword">
                    <!-- Display error messages here -->
                  </div>
                </div>
              </div>

              <div class="input-group">
                <div class="col">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="showPassword">
                    <label class="form-check-label text-carbon-grey fw-medium font-13" for="showPassword">Show Password</label>
                  </div>                  
                </div>      
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" id="cancelAddAccountBtn" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3"
                data-bs-dismiss="modal" aria-label="Close">cancel</button>
              <button type="button" id="saveChangesBtn"
                class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">add account</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </body>
</html>
<?php 
  } else {
    header("Location: login.php");
  } 
?>
