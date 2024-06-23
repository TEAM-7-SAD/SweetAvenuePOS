<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/error-reporting.php');
include_once FileUtils::normalizeFilePath('includes/default-timezone.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subtotal = (float) $_POST['subtotal'];
    $grand_total = (float) $_POST['grand_total'];
    $items = json_decode($_POST['items'], true);
    $user_id = $_SESSION['id'];
    $receipt = 'receipt.pdf';

    try {
        $timestamp = date('Y-m-d H:i:s');
        $sql = "INSERT INTO transaction (user_id, timestamp, total_amount, receipt) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('isds', $user_id, $timestamp, $grand_total, $receipt);
        $stmt->execute();

        $transaction_id = $stmt->insert_id;
        $sql_items = "INSERT INTO items_purchased (transaction_id, item_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)";
        $stmt_items = $db->prepare($sql_items);

        foreach ($items as $item) {
            $stmt_items->bind_param('iiid', $transaction_id, $item['id'], $item['quantity'], $item['price']);
            $stmt_items->execute();
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} 
else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}