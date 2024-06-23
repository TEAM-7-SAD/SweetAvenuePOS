<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['ids']) && is_array($data['ids']) && isset($data['variationIds']) && is_array($data['variationIds'])) {
        $ids = $data['ids'];
        $variationIds = $data['variationIds'];

        // Convert arrays to comma-separated strings for the SQL query
        $idsList = implode(',', array_map('intval', $ids));
        $variationIdsList = implode(',', array_map('intval', $variationIds));

        // Start a transaction
        $db->begin_transaction();

        try {
            // Delete specific entries in food_variation and drink_variation if variation IDs are provided
            if (!empty($variationIdsList)) {
                $deleteFoodVariationQuery = "DELETE FROM food_variation WHERE id IN ($variationIdsList)";
                $deleteDrinkVariationQuery = "DELETE FROM drink_variation WHERE id IN ($variationIdsList)";
                
                $db->query($deleteFoodVariationQuery);
                $db->query($deleteDrinkVariationQuery);
            }

            // Check if there are any variations left for the food and drink items
            $remainingFoodVariationsQuery = "SELECT COUNT(*) FROM food_variation WHERE food_id IN ($idsList)";
            $remainingDrinkVariationsQuery = "SELECT COUNT(*) FROM drink_variation WHERE drink_id IN ($idsList)";

            $remainingFoodVariationsResult = $db->query($remainingFoodVariationsQuery)->fetch_row()[0];
            $remainingDrinkVariationsResult = $db->query($remainingDrinkVariationsQuery)->fetch_row()[0];

            // Delete entries from food_item and drink_item only if there are no remaining variations
            if ($remainingFoodVariationsResult == 0) {
                $deleteFoodQuery = "DELETE FROM food_item WHERE id IN ($idsList)";
                $db->query($deleteFoodQuery);
            }

            if ($remainingDrinkVariationsResult == 0) {
                $deleteDrinkQuery = "DELETE FROM drink_item WHERE id IN ($idsList)";
                $db->query($deleteDrinkQuery);
            }

            // Commit transaction
            $db->commit();
            $response['success'] = true;
        } catch (Exception $e) {
            // Rollback transaction if something went wrong
            $db->rollback();
            $response['error'] = $e->getMessage();
        }
    } else {
        $response['error'] = 'Invalid input data.';
    }
} else {
    $response['error'] = 'Invalid request method.';
}

echo json_encode($response);
?>
