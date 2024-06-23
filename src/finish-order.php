<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/default-timezone.php');

header('Content-Type: application/json');

if($_SERVER["REQUEST_METHOD"] === "POST") {
    // SQL to insert to transaction and items purchased table
}