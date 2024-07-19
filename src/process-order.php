<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');

header('Content-Type: application/json');

$response = array('success' => false, 'message' => 'Unknown error.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['order_cart']) && count($_SESSION['order_cart']) > 0) {

        $order_cart = $_SESSION['order_cart'];
        $subtotal = 0;
        $items = [];

        foreach ($order_cart as $item) {
            if (isset($item['price'])) {
                $subtotal += $item['price'];
                $items[] = $item['product_name'];
            }
        }

        $response['success'] = true;
        $response['message'] = 'Order placed successfully!';
        $response['order_cart'] = $order_cart;
        $response['subtotal'] = $subtotal;
        $response['items'] = $items;

    } else {
        $response['message'] = 'No items in the cart.';
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>
