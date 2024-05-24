<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require_once FileUtils::normalizeFilePath('session-handler.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');

session_destroy();
header("location: ../login.php");
exit();
?>