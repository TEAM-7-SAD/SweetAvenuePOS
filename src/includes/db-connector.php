<?php
// Creates connection to the database
$server = "localhost";
$username = "root";
$password = "";
$database = "sweet_avenue_db";

$db=mysqli_connect($server, $username, $password, $database);

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>