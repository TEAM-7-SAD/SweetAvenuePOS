<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require_once FileUtils::normalizeFilePath('session-handler.php');
require_once FileUtils::normalizeFilePath('db-connector.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');

if(isset($_POST['sign_in_btn'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password)) {
        $_SESSION['error_message'] = 'Please do not leave the input fields empty.';
        header("Location: ../login.php");
        exit();
    }

    // Query the user table for the entered username
    $stmt = $db->prepare("SELECT id, username, password FROM user WHERE BINARY username = ?");
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
            header("location: ../index.php");
            exit();
        }
        // If username and password mismatched, display this      
        else {
            $_SESSION['error_message'] = 'Username and password mismatched.';
            header("Location: ../login.php");
            exit();
            }
    }
    // If there is no user with the username, display this
    else {
        $_SESSION['error_message'] = 'User with this username does not exist.';
        header("Location: ../login.php");
        exit();
    }
}