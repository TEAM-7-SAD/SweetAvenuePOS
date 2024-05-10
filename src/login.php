<?php

require_once 'includes/session-handler.php';
require_once 'includes/db-connector.php';

// If session is set, disallow returning to login page
if(isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if(isset($_POST['sign_in_btn'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password)) {
        $_SESSION['error_message'] = 'Please do not leave the input fields empty.';
        header("Location: login.php");
        exit();
    }

    // Query the user table for the entered username
    $stmt = $db->prepare("SELECT id, username, password FROM user WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user of this username exists
    if($result->num_rows > 0) {
    $row = $result->fetch_assoc();

        // Verify if password match the entered username
        if (password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            session_regenerate_id(true);
            header("location: index.php");
            exit();
        }
        // If username and password mismatched, display this      
        else {
            $_SESSION['error_message'] = 'Username and password mismatched.';
            header("Location: login.php");
            exit();
            }
    }
    // If there is no user with the username, display this
    else {
        $_SESSION['error_message'] = 'User is not found.';
        header("Location: login.php");
        exit();
    }
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css" />   
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>

    <style>
        body {
            background-image: url('images/sweet_background.jpg'); /* Replace 'path/to/your/image.jpg' with the actual path to your image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .solid-color-container {
            background-color: #FFF0E9;
            padding: 40px;
            border-radius: 10px; 
        }
    </style>
</head>

<body>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="forgotPasswordModal">Forgot Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="includes/send-password-reset.php" method="post">
                <div class="modal-body">
                    <div class="form-floating py-3">
                        <input type="text" name="email" class="form-control" id="email" required>
                        <label for="email" class="form-label text-carbon-grey fw-medium">Email Adddress<span
                            style="color: red;"> *</span></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3"
                        data-bs-dismiss="modal" aria-label="Close">cancel</button>
                    <button type="submit" name="send-email-btn" class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">Send Link</button>
                </div>
            </form>
            </div>
        </div>
      </div>
    </div>

    <div class="container-fluid position-absolute top-50 start-50 translate-middle">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center solid-color-container">
                    <img src="images/logo-removebg-preview.png" class="mb-3" height="150" width="150">
                    <div class="text-tiger-orange text-center ">
                        <h3 class="sweet-avenue fw-semibold"><strong>SWEET AVENUE</strong></h3>
                        <h5 class="coffee-bakeshop mb-4 fw-medium"><strong>COFFEE • BAKESHOP</strong></h5>
                    </div>
                    <form action="login.php" method="post" id="login-form" class="needs-validation" novalidate>
                        
                        <?php if (isset($errorMessage)) : ?>
                            <div class="alert border borderless bg-transparent alert-danger alert-dismissible fade show" role="alert">
                                <strong><?php echo $errorMessage; ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" onkeypress="return avoidSpace(event)" name="username" id="username" placeholder="Username" required>
                            <label for="username">Username<span style="color: red;"> *</span></label>
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback text-start">
                                Please enter a username.
                            </div> 
                        </div>
                   
                        <div class="form-floating">
                            <input type="password" class="form-control" onkeypress="return avoidSpace(event)" name="password" id="password" placeholder="Password" required>
                            <label for="password">Password<span style="color: red;"> *</span></label>          
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback text-start">
                            Please enter a password.
                        </div>
                        <div class="input-group mt-2 align-items-center">
                            <div class="form-check form-switch ms-3">
                                <input class="form-check-input fs-5" type="checkbox" id="showPassword" onclick="myFunction()">
                                <label class="form-check-label text-carbon-grey fw-semibold pt-1" for="showPassword" style="font-size: 15px;">Show</label>
                            </div>
                            <a href="#" class="text-carbon-grey ms-auto nav-link fw-medium" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" style="font-size: 15px;">Forgot Password?</a>
                        </div>

                        <div for="submitForm" class="justify-content-center d-md-flex pt-4">
                            <button type="submit" name="sign_in_btn" id="submitForm" class="btn col-12 fw-semibold btn-tiger-orange text-capitalize py-3 px-4">Sign In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    


    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/login.js"></script>
    <script>
        // Disallow whitespaces from input fields
        function avoidSpace(event) {
        var k = event ? event.which : window.event.keyCode;
        if (k == 32) return false;
        }

        function myFunction() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
        }

    </script>
</body>

</html>