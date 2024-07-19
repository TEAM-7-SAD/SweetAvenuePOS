<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/error-reporting.php');
include_once FileUtils::normalizeFilePath('includes/default-timezone.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../vendor/autoload.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subtotal = (float) $_POST['subtotal'];
    $grand_total = (float) $_POST['grand_total'];
    $items = $_POST['items'];
    $user_id = $_SESSION['id'];
    $html_content = $_POST['html_content'];

    $items_json = is_string($items) ? $items : json_encode($items);

    try {
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html_content);
        $timestamp = date('Y-m-d H:i:s');
        $receipt_filename = 'receipt_' . date('Ymd_His') . '.pdf';
        $pdf_file_path = "uploads/receipt/$receipt_filename";
        $mpdf->Output($pdf_file_path, \Mpdf\Output\Destination::FILE);

        $sql = "INSERT INTO transaction (user_id, timestamp, total_amount, receipt, items) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('isdss', $user_id, $timestamp, $grand_total, $receipt_filename, $items_json);
        $stmt->execute();

        echo json_encode(['success' => true, 'receipt_path' => $pdf_file_path]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
