<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require_once FileUtils::normalizeFilePath('db-connector.php');
require_once FileUtils::normalizeFilePath('session-handler.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');
include_once FileUtils::normalizeFilePath('default-timezone.php');

if(!isset($_POST['send-email-btn'])) {
    $_SESSION['error_message'] = 'Something went wrong.';
    header("Location: ../login.php");
    exit();    
}

$email = $_POST["email"];
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = 'Please provide a valid email';
    header("Location: ../login.php");
    exit();
}

// Check if email exists
$sql = "SELECT email FROM user WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$row = $stmt->get_result();

if($row->num_rows === 0) {
    $_SESSION['error_message'] = 'User with this email does not exist.';
    header("Location: ../login.php");
    exit();
}


$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$sql = "UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($db->affected_rows) {

    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

    Click <a href="http://localhost/SweetAvenuePOS/src/reset-password.php?token=$token">here</a> 
    to reset your password.

    END;

    try {

        $mail->send();

    } catch (Exception $e) {

        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";

    }

}
header("Location: ../login.php");
exit();