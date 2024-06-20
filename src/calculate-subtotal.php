<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');

$subtotal = 0;
if (isset($_SESSION['order_cart'])) {
    foreach ($_SESSION['order_cart'] as $item) {
        if (isset($item['price'])) {
            $subtotal += $item['price'];
        }
    }
}
echo number_format($subtotal, 2);