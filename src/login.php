<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

// If session is set, disallow returning to login page
if(isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
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
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>

    <style>
        body {
            background-image: url('images/sweet_background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>

<body>

    <?php include FileUtils::normalizeFilePath('includes/preloader.html'); ?>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered px-3">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-carbon-grey fw-semibold" id="forgotPasswordModalLabel">Let's recover your account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="location.reload()"></button>
            </div>
            <form class="needs-validation" novalidate>
                <div class="modal-body">
                <p class="px-1 font-14">Please provide your registered email address for sending of reset link.</p>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" onkeypress="return avoidSpace(event)" required>
                    <label for="email" class="text-muted font-13">Email Address<span style="color: red;"> *</span></label>
                    <div class="valid-feedback font-13" id="email-valid">
                        <!-- Display valid email message -->
                    </div>
                    <div class="invalid-feedback font-13" id="email-error">
                        <!-- Display error messages -->
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3 font-14" data-bs-dismiss="modal" aria-label="Close" onclick="location.reload()">Cancel</button>
                    <button type="submit" name="send-email-btn" id="sendEmailBtn" class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4 font-14">Send Link</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="container-fluid position-absolute top-50 start-50 translate-middle">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center login-form-gradient shadow-lg">
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
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="fs-4 mb-0 fw-semibold">Welcome!</div>  
                            <img src="images/waving-hand.svg" height="35" width="45" alt="waving-icon">                          
                        </div>
                        <div class="mb-4 font-14">Sign in to your account to get started.</div>                                                   
                    </div>


                    <form action="includes/login-authenticator.php" method="post" id="login-form" class="needs-validation" novalidate>
                        
                        <?php if (isset($errorMessage)) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span class="text-danger font-13"><?php echo $errorMessage; ?></span>
                                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="form-floating mb-3"> 
                            <input type="text" class="form-control text-carbon-grey shadow-sm" onkeypress="return avoidSpace(event)" name="username" id="username" placeholder="Username" required>
                            <label for="username" class="fw-medium text-carbon-grey font-13">Username<span style="color: red;"> *</span></label>
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback text-start font-13">
                                Please enter a username.
                            </div> 
                        </div>
                   
                        <div class="form-floating">
                            <input type="password" class="form-control text-carbon-grey shadow-sm" onkeypress="return avoidSpace(event)" name="password" id="password" placeholder="Password" required>
                            <label for="password" class="fw-medium text-carbon-grey font-13">Password<span style="color: red;"> *</span></label>          
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback text-start font-13">
                            Please enter a password.
                        </div>
                        <div class="input-group mt-2 align-items-center">
                            <div class="form-check ms-2">
                                <input class="form-check-input fs-5" type="checkbox" id="showPassword" onclick="togglePassword()">
                                <label class="form-check-label text-carbon-grey fw-medium pt-1 font-13" for="showPassword">Show</label>
                            </div>
                            <a href="#" class="text-carbon-grey ms-auto font-13" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                        </div>

                        <div for="submitForm" class="justify-content-center d-md-flex mt-4 mb-2">
                            <button type="submit" name="sign_in_btn" id="submitForm" class="btn col-12 btn-medium-brown py-3 font-14">Sign In</button>
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
    <script src="javascript/login.js"></script>
    <script src="javascript/preloader.js"></script>

</body>

</html>