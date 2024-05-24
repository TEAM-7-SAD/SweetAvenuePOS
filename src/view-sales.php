<?php
// Include necessary files and initialize database connection
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

// Check if sale ID is provided
if (isset($_GET['saleId'])) {
    // Sanitize the input to prevent SQL injection
    $saleId = mysqli_real_escape_string($db, $_GET['saleId']);

    // Query to fetch sale details based on sale ID
    $sql = "SELECT 
                transaction.*,
                CONCAT(user.first_name, ' ', user.middle_name, ' ', user.last_name) AS full_name,
                DATE(transaction.timestamp) AS transaction_date,
                TIME_FORMAT(transaction.timestamp, '%h:%i %p') AS transaction_time
            FROM 
                transaction
            JOIN 
                user ON transaction.user_id = user.id
            WHERE
                transaction.id = '$saleId'";
    $result = $db->query($sql);

    // Check if the query was successful
    if ($result) {
        // Check if sale details are found
        if ($result->num_rows > 0) {
            // Fetch sale details as an associative array
            $saleDetails = $result->fetch_assoc();
            // Output sale details as JSON (you can modify this as per your requirements)
            echo json_encode($saleDetails);
        } else {
            // Sale details not found
            echo "Sale details not found.";
        }
    } else {
        // Query execution failed
        echo "Failed to fetch sale details. Please try again later.";
    }
} else {
    // Sale ID not provided
    echo "Sale ID not provided.";
}
?>
