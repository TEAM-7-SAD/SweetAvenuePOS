<?php
// Include the database connection file
require_once 'includes/db-connector.php';

// Check if account IDs are provided
if (isset($_POST['accountIds']) && is_array($_POST['accountIds'])) {
    // Get the account IDs from the POST data
    $accountIds = $_POST['accountIds'];

    // Prepare and execute the SQL query to delete selected accounts
    $sql = "DELETE FROM user WHERE id IN (" . implode(',', array_fill(0, count($accountIds), '?')) . ")";
    $stmt = $db->prepare($sql);
    $types = str_repeat('i', count($accountIds));
    $stmt->bind_param($types, ...$accountIds);

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
