<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require_once FileUtils::normalizeFilePath('db-connector.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM user WHERE reset_token_hash = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row === NULL) {
    $_SESSION['error_message'] = 'Your password reset link was not found.';
    header("Location: ../login.php");
    exit();
}

if (strtotime($row["reset_token_expires_at"]) <= time()) {
    $_SESSION['error_message'] = 'Your password reset link has expired.';
    header("Location: ../login.php");
    exit();
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$sql = "UPDATE user SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $password_hash, $row["id"]);
$stmt->execute();

header("Location: ../login.php");
exit();