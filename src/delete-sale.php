<?php
// Include the database connection file
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

// Check if account IDs are provided
if (isset($_POST['saleIds']) && is_array($_POST['saleIds'])) {
    // Get the account IDs from the POST data
    $saleIds = $_POST['saleIds'];

    // Prepare and execute the SQL query to delete selected accounts
    $sql = "DELETE FROM user WHERE id IN (" . implode(',', array_fill(0, count($saleIds), '?')) . ")";
    $sql = "DELETE FROM transaction WHERE id IN (" . implode(',', array_fill(0, count($saleIds), '?')) . ")";
    $stmt = $db->prepare($sql);
    $types = str_repeat('i', count($saleIds));
    $stmt->bind_param($types, ...$saleIds);
    if ($stmt->execute()) {
        // If the deletion is successful, return a success response
        echo 'success';
    } else {
        // If an error occurs, return an error response
        echo 'error';
    }
} else {
    // If account IDs are not provided, return an error response
    echo 'error';
}
?>