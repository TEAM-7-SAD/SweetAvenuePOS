<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Clear the order cart
unset($_SESSION['order_cart']);

// Return success response
echo json_encode(['status' => 'success']);
?>
