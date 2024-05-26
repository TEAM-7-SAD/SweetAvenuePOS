<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require_once FileUtils::normalizeFilePath('db-connector.php');
require_once FileUtils::normalizeFilePath('session-handler.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');
include_once FileUtils::normalizeFilePath('default-timezone.php');

$response = ['success' => false, 'message' => ''];

if($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST['token'] ?? NULL;
    $password = $_POST['newPassword'] ?? NULL;
    $password_confirmation = $_POST['confirmPassword'] ?? NULL;

    if(empty($password) || empty($password_confirmation)) {
        $response['message'] = 'Password input fields cannot be empty.';
        echo json_encode($response);
        exit();      
    }

    if($password !== $password_confirmation) {
        $response['message'] = 'Passwords do not match.';
        echo json_encode($response);
        exit();
    }

    $validate_new_password = newPasswordValidation($password);

    if($validate_new_password) {
        $response['message'] = $validate_new_password;
        echo json_encode($response);
        exit();
    }
    
    $token_hash = hash('sha256', $token);

    $sql = "SELECT email, reset_token_hash, reset_token_expires_at FROM user WHERE reset_token_hash = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) {
        $_SESSION['error_message'] = 'Your password reset link was not found.';
        header("Location: ../login.php");
        exit();
    }
    
    $expiry_time = strtotime($row["reset_token_expires_at"]);
    $current_time = time();

    if ($expiry_time <= $current_time) {
        $_SESSION['error_message'] = 'Your password reset link has expired.';
        header("Location: ../login.php");
        exit();
    }
    
    $new_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "UPDATE user SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $new_password, $row['email']);
    $success = $stmt->execute();
    
    if($success) {
        $response['success'] = true;
        $response['message'] = 'Password reset successful.';
        echo json_encode($response);
        exit();
    }
    else {
        $response['message'] = "Failed to reset your password. Please try again.";
        echo json_encode($response);
        exit();
    }
    exit();    
}

// Validate new password 
function newPasswordValidation($password) {
    if (strlen($password) < 8 || strlen($password) > 20) {
        return "Your password must be between 8 and 20 characters long.";
    }
    if (!preg_match("/\d/", $password)) {
        return "Your password must contain at least 1 number.";
    }
    if (!preg_match("/[A-Z]/", $password)) {
        return "Your password must contain at least 1 uppercase letter.";
    }
    if (!preg_match("/[a-z]/", $password)) {
        return "Your password must contain at least 1 lowercase letter.";
    }
    if (!preg_match("/[\W_]/", $password)) {
        return "Your password must contain at least 1 special character.";
    }
    if (preg_match("/\s/", $password)) {
        return "Your password must not contain any spaces.";
    }
    return ""; 
}