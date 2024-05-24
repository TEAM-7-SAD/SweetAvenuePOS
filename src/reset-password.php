<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM user WHERE reset_token_hash = ?";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === NULL) {
    $_SESSION['error_message'] = 'Reset link was not found.';
    header("Location: login.php");
    exit();
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    $_SESSION['error_message'] = 'Reset link has expired.';
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head> 
    <!--CSS-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css" />   
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">

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

<?php include 'includes/preloader.html';?>

<div class="container-fluid position-absolute top-50 start-50 translate-middle">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center solid-color-container">
                    <img src="images/logo-removebg-preview.png" class="mb-3" height="150" width="150">
                    <div class="text-tiger-orange text-center ">
                        <h3 class="sweet-avenue fw-semibold"><strong>Create New Password</strong></h3>
                    </div>
                    <form action="includes/process-reset-password.php" method="post">

                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        
                        <div class="form-floating mb-2"> 
                            <input type="password" class="form-control" onkeypress="return avoidSpace(event)" name="password" id="password" placeholder="New Password" required>
                            <label for="password">New Password<span style="color: red;"> *</span></label>
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback text-start">
                                Please enter a New password.
                            </div> 
                        </div>
                   
                        <div class="form-floating">
                            <input type="password" class="form-control" onkeypress="return avoidSpace(event)" name="password_confirmation" id="password_confirmation" placeholder="New Password" required>
                            <label for="password_confirmation">Repeat password<span style="color: red;"> *</span></label>          
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback text-start">
                            Please enter the new password.
                        </div>

                        <div for="" class="justify-content-center d-md-flex pt-4">
                            <button type="" name="" id="" class="btn col-12 fw-semibold btn-tiger-orange text-capitalize py-3">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="javascript/preloader.js"></script>
</body>
</html>