<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $productType = $_POST['product_type'];
    $productName = $_POST['product_name'];
    $servingOrType = $_POST['serving_or_type'];
    $flavorOrSize = $_POST['flavor_or_size'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $orderItem = [
        'product_id' => $productId,
        'product_type' => $productType,
        'product_name' => $productName,
        'serving_or_type' => $servingOrType,
        'flavor_or_size' => $flavorOrSize,
        'quantity' => $quantity,
        'price' => $price
    ];

    if (!isset($_SESSION['order_cart'])) {
        $_SESSION['order_cart'] = [];
    }

    $_SESSION['order_cart'][] = $orderItem;

    // Calculate the updated subtotal
    $subtotal = 0;
    foreach ($_SESSION['order_cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    // Check if the request is an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Return the updated subtotal as JSON for AJAX requests
        echo json_encode(['status' => 'success', 'subtotal' => $subtotal]);
    } else {
        // Redirect back to create-order.php for non-AJAX requests
        header("Location: create-order.php");
        exit();
    }
} else {
    echo 'Invalid request method';
}
?>
