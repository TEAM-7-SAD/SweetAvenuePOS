<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');

$subtotal = 0;
if (isset($_SESSION['order_cart'])) {
    foreach ($_SESSION['order_cart'] as $item) {
        if (isset($item['price'])) {
            $subtotal += $item['price'];
        } else {
        }
    }
}

if (isset($_SESSION['order_cart']) && count($_SESSION['order_cart']) > 0) {
    foreach ($_SESSION['order_cart'] as $item) {
        echo '<tr>';
        echo '<td class="align-middle ps-2 pe-3">';
        echo '<p class="mb-0 text-muted fw-semibold font-14">x' . $item['quantity'] . '</p>';
        echo '</td>';
        echo '<td class="align-middle" style="white-space: nowrap;">';
        echo '<div class="d-flex flex-column align-items-start py-2">';
        echo '<p class="fw-semibold text-muted mb-0 text-capitalize font-14">' . htmlspecialchars($item['product_name']) . '</p>';
        echo '<div class="text-tiger-orange fw-semibold text-capitalize" style="font-size: 11px; max-width: 200px;">';
        echo '<p class="text-capitalize mb-0">' . htmlspecialchars($item['serving_or_type']) . '</p>';
        echo '<p class="mb-0">' . htmlspecialchars($item['flavor_or_size']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</td>';
        echo '<td class="align-middle text-end pe-2">';
        if (is_numeric($item['price'])) {
            echo '<div class="mb-0 fw-semibold fs-6 text-carbon-grey font-14">' . number_format($item['price'], 2) . '</div>';
        } else {
            echo '<p class="mb-0 text-muted">Price not available</p>';
        }
        echo '</td>';
        // echo '<td class="visually-hidden">' . htmlspecialchars($item['item_id']) . '</td>';
        echo '</tr>';
    }    
} 
else {
    echo '<tr><td colspan="3" class="mb-0 fw-medium text-center text-muted font-14">No items in cart</td></tr>';
}