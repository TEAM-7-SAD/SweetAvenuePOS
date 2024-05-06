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
        if($row['password'] == $password) {
            $_SESSION['id'] = $row['id'];
            header("location: index.php");
            exit();
        }
        // If username and password mismatched, display this      
        else {
            $_SESSION['error_message'] = 'Username and Password do not match!';
            header("Location: login.php");
            exit();
            }
    }
    // If there is no user with the username, display this
    else {
        $_SESSION['error_message'] = 'User with this username does not exist.';
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
</head>

<body class="bg-rose-white ">
    <div class="container-fluid position-absolute top-50 start-50 translate-middle">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center">
                    <img src="images/logo-removebg-preview.png" height="250" width="250" style="margin-bottom: 20px;">
                    <div class="text-tiger-orange text-center ">
                        <h1 class="sweet-avenue" style="font-size: 3vw;"><dt><strong>SWEET AVENUE</strong></dt></h1>
                        <h2 class="coffee-bakeshop mb-4" style="font-size: 2vw;"><dt><strong>COFFEE • BAKESHOP</strong></dt></h2>
                    </div>
                    <form action="login.php" method="post" id="login-form" class="needs-validation" novalidate>
                        
                        <?php if (isset($errorMessage)) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><?php echo $errorMessage; ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="form-floating">
                            <input type="text" class="form-control shadow-sm" name="username" id="username" placeholder="Username" required>
                            <label for="username">Username<span style="color: red;"> *</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback text-start">
                            Please enter a Username.
                        </div>                    
                        <div class="mb-4"></div>
                        <div class="form-floating shadow-sm">
                            <input type="password" class="form-control shadow-sm" name="password" id="password" placeholder="Password" required>
                            <label for="password">Password<span style="color: red;"> *</span></label>
                            <div class="invalid-feedback"></div>           
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback text-start">
                            Please enter a Password.
                        </div>
                        <div for="submitForm" class="justify-content-center d-md-flex pt-4">
                            <button type="submit" name="sign_in_btn" id="submitForm" class="btn col-12 btn-tiger-orange text-light py-3 px-3">Sign In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/login.js"></script>
</body>

</html>