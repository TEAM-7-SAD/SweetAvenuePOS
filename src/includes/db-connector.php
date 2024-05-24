<?php

include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');

// Creates connection to the database
$server = "localhost";
$username = "root";
$password = "";
$database = "sweet_avenue_db";

$db = mysqli_connect($server, $username, $password, $database);

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>