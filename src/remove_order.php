<?php
session_start();

if (isset($_POST['product_name'])) {
    $productName = $_POST['product_name'];
    
    // Loop through the order cart and remove the item with matching product name
    if (isset($_SESSION['order_cart'])) {
        foreach ($_SESSION['order_cart'] as $key => $item) {
            if ($item['product_name'] === $productName) {
                unset($_SESSION['order_cart'][$key]);
                break; // Stop looping after removing the item
            }
        }
    }
}
?>
