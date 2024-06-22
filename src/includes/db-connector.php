<?php
// Creates connection to the database
const PREFIX = 'u155023598_';

$server = "localhost";
$username = PREFIX . "root";
$password = "P0SniReggie";
$database = "sweet_avenue";

$db = mysqli_connect($server, $username, $password, $database);

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>