<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require_once FileUtils::normalizeFilePath('db-connector.php');
require_once FileUtils::normalizeFilePath('session-handler.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');
include_once FileUtils::normalizeFilePath('default-timezone.php');

$response = ['success' => false, 'message' => 'An error occurred'];

if($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"] ?? NULL;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please provide a valid email address';
        echo json_encode($response);
        exit();
    }

    // Check if email exists
    $sql = "SELECT email FROM user WHERE BINARY email = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'User with this email does not exist.';
        echo json_encode($response);
        exit();
    }

    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    $sql = "UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $token_hash, $expiry, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $subject = "Password Reset";
        $body = <<<END
        Click <a href="http://localhost/SweetAvenuePOS/src/reset-password.php?token=$token">here</a> 
        to reset your password.
        END;

        if (sendEmail($email, $subject, $body)) {
            $response['success'] = true;
            $response['message'] = 'Password reset link sent successfully.';
            echo json_encode($response);
        } else {
            $response['message'] = 'Failed to send password reset link. Please try again.';
            echo json_encode($response);
            exit();
        }
    } 
    else {
        $response['message'] = 'Failed to generate password reset link. Please try again.';
        echo json_encode($response);
        exit();
    }
}


function sendEmail($recipientEmail, $subject, $body) {

    $mail = require FileUtils::normalizeFilePath(__DIR__ . '/mailer.php');

    try {
        $mail->setFrom('carltabuso2275@gmail.com', 'Sweet Avenue');
        $mail->addAddress($recipientEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}