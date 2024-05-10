<?php
require_once 'includes/db-connector.php';

$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$username = $_POST['username'];
$password = $_POST['password'];

if(empty($last_name) || empty($first_name) || empty($middle_name) || empty($username) || empty($password)) {
    header("Location: accounts.php");
    exit();
}

$sql = "INSERT INTO user (last_name, first_name, middle_name, username, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
$stmt->bind_param('sssss', $last_name, $first_name, $middle_name, $username, $password);
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
        <td>'.$row['username'].'</td>
        <td>'.$row['password'].'</td>
    </tr>
';

// Return the HTML content
echo $html;
exit();

