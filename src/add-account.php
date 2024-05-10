<?php
require_once 'includes/db-connector.php';

$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if(empty($last_name) || empty($first_name) || empty($username) || empty($password)) {
    header("Location: accounts.php");
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
$html = '
    <tr>
    <tr data-id="'.$row['id'].'" class="selectable">
    <td><input type="checkbox" class="account-checkbox" data-account-id="'.$row['id'].'"></td>
        <td>'.$row['last_name'].'</td>
        <td>'.$row["first_name"].'</td>
        <td>'.$row['middle_name'].'</td>
        <td>'.$row['email'].'</td>
    </tr>
';

// Return the HTML content
echo $html;
exit();

