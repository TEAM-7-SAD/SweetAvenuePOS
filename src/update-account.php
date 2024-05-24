<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$username = $_POST['username'];
$password = $_POST['password'];

if(empty($last_name) || empty($first_name) || empty($middle_name) || empty($username) || empty($password)) {
    header("Location: accounts.php");
    exit();
}

$id = $_POST['id'];

$sql = "UPDATE user SET last_name = ?, first_name = ?, middle_name = ?, username = ?, password = ? WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('sssssi', $last_name, $first_name, $middle_name, $username, $password, $id);
$stmt->execute();
$stmt->close();

// Return success message
echo 'success';
exit();
