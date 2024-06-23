<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/default-timezone.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
    }

    $subtotal = (float) $_POST['subtotal'];
    $discount = (float) $_POST['discount'];
    $grand_total = (float) $_POST['grand_total'];
    $change = (float) $_POST['change'];
    $items = json_decode($_POST['items'], true);
    $user_id = 54;

    // Insert transaction into `transaction` table
    $stmt = $conn->prepare("INSERT INTO `transaction` (user_id, timestamp, total_amount, receipt) VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param('idd', $user_id, $grand_total);

    if ($stmt->execute()) {
        $transaction_id = $stmt->insert_id;

        // Insert items into `items_purchased` table
        $stmt = $conn->prepare("INSERT INTO `items_purchased` (transaction_id, item_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->bind_param('iiid', $transaction_id, $item['id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert transaction']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>