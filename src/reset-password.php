<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');
include_once FileUtils::normalizeFilePath('includes/default-timezone.php');

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM user WHERE reset_token_hash = ?";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row === NULL) {
    $_SESSION['error_message'] = 'Reset link was not found.';
    header("Location: login.php");
    exit();
}

if (strtotime($row["reset_token_expires_at"]) <= time()) {
    $_SESSION['error_message'] = 'Reset link has expired.';
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <!--Site Meta Information-->
    <meta charset="UTF-8" />
    <title>Sweet Avenue POS</title>
    <!--Mobile Specific Metas-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />   
    <!--CSS-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="styles/main.css" />   
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>
</head>

<body class="bg-image">

    <?php include FileUtils::normalizeFilePath('includes/preloader.html'); ?>

    <div class="container-fluid position-absolute top-50 start-50 translate-middle">
        <div class="row justify-content-center align-items-center">
            <div class="col-sm-10 col-md-7 col-lg-4">
                <div class="text-center password-reset-form-gradient shadow-lg">
                    <div class="d-flex align-items-center">
                        <!-- Shop Logo and Name -->
                        <span class="navbar-brand pe-3">
                            <img src="images/sweet-avenue-logo.png" alt="Sweet Avenue Logo" width="70" height="70">
                        </span>
                        <div class="text-medium-brown text-start">
                            <h3 class="mb-0 fw-bold"><strong>SWEET AVENUE</strong></h3>
                            <h5 class="fw-medium"><strong>COFFEE • BAKESHOP</strong></h5>
                        </div>                        
                    </div><hr>
                    <div class="text-carbon-grey justify-content-center">
                        <div class="fs-5 mb-0 fw-semibold">Recover your account!</div>
                        <div class="mb-4 font-13">Create a new and strong password.</div>
                    </div>
                    <form id="passwordResetForm" class="needs-validation" novalidate>
                        <input type="hidden" name="token" id="token" value="<?= htmlspecialchars($token) ?>">

                        <!-- Display Error Message -->
                        <?php if (isset($errorMessage)) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span class="fw-medium text-danger font-13"><?php echo $errorMessage; ?></span>
                                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="form-floating mb-3"> 
                            <input type="password" class="form-control shadow-sm text-carbon-grey" onkeypress="return avoidSpace(event)" name="new_password" id="newPassword" placeholder="New Password" required>
                            <label for="new_password" class="fw-medium text-carbon-grey font-13">New password<span style="color: red;"> *</span></label>
                            <div class="password-requirements">
                                <ul id="password-requirements-list">
                                    <h6 class="reset-pass-title text-uppercase">Password must contain:</h6>
                                    <li id="length" class="requirement unmet fw-medium"><span class="requirement-circle"></span> Between 8 to 20 characters of length</li>
                                    <li id="uppercase" class="requirement unmet fw-medium"><span class="requirement-circle"></span> At least 1 uppercase letter (A...Z)</li>
                                    <li id="lowercase" class="requirement unmet fw-medium"><span class="requirement-circle"></span> At least 1 lowercase letter (a...z)</li>
                                    <li id="number" class="requirement unmet fw-medium"><span class="requirement-circle"></span> At least 1 number (1...9)</li>
                                    <li id="special" class="requirement unmet fw-medium"><span class="requirement-circle"></span> At least 1 special character (!...$)</li>
                                </ul>
                            </div>
                            <!-- Input validation -->
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback"></div> 
                        </div>
                   
                        <div class="form-floating">
                            <input type="password" class="form-control shadow-sm text-carbon-grey" onkeypress="return avoidSpace(event)" name="confirm_password" id="confirmPassword" placeholder="Confirm new password" required>
                            <label for="password" class="fw-medium text-carbon-grey font-13">Confirm new password<span style="color: red;"> *</span></label>
                            <!-- Input validation -->
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback text-start fw-medium font-13" id="passwordMismatched">
                                <!-- Display error messages here -->
                            </div> 
                        </div>

                        <!-- Show Password Toggle -->
                        <div class="input-group mt-2 align-items-center">
                            <div class="form-check ms-2">
                                <input class="form-check-input fs-5" type="checkbox" id="showPassword" onclick="togglePassword()">
                                <label class="form-check-label text-carbon-grey fw-medium pt-1 font-13" for="showPassword">Show Password</label>
                            </div>
                        </div>     

                        <!-- Change Password Button -->
                        <div for="submitResetPassword" class="justify-content-center d-md-flex mt-4 mb-2">
                            <button type="submit" name="change_password_btn" id="submitResetPassword" class="btn col-12 fw-medium btn-medium-brown py-3 font-14">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="javascript/preloader.js"></script>
    <script src="javascript/reset-password.js"></script>

</body>
</html>