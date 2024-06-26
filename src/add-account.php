<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

$last_name = trim($_POST['last_name']);
$first_name = trim($_POST['first_name']);
$middle_name = trim($_POST['middle_name']) ?? '';
$email = trim($_POST['email']);
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if(empty($last_name) || empty($first_name) || empty($email) || empty($username) || empty($password)) {
    header("Location: accounts");
    exit();
}

$sql = "INSERT INTO user (last_name, first_name, middle_name, email, username, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
$stmt->bind_param('ssssss', $last_name, $first_name, $middle_name, $email, $username, $hashed_password);
$stmt->execute();
$stmt->close();

// Fetch the newly inserted row
$newRowSql = "SELECT * FROM user WHERE id = LAST_INSERT_ID()";
$result = $db->query($newRowSql);
$row = $result->fetch_assoc();

// Prepare the HTML content of the new row
// $html = '
//     <tr>
//     <tr data-id="'.$row['id'].'" class="selectable">
//     <td><input type="checkbox" class="account-checkbox" data-account-id="'.$row['id'].'"></td>
//         <td>'.$row['last_name'].'</td>
//         <td>'.$row["first_name"].'</td>
//         <td>'.$row['middle_name'].'</td>
//         <td>'.$row['email'].'</td>
//     </tr>
// ';

$html = '
    <tr>
        <td>'.$row['last_name'].'</td>
        <td>'.$row["first_name"].'</td>
        <td>'.$row['middle_name'].'</td>
        <td>'.$row['email'].'</td>
    </tr>
';

// Return the HTML content
echo $html;
exit();