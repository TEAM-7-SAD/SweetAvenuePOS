<?php

include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');

// Creates connection to the database
define('PREFIX', 'u155023598_');

$server = "localhost";
$username = PREFIX . "root";
$password = "P0SniReggie";
$database = PREFIX . "sweet_avenue";

$db = mysqli_connect($server, $username, $password, $database);

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>