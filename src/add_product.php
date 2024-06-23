<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $category = $_POST['category'] ?? null;
    $servingType = $_POST['servingType'] ?? null;
    $flavorSize = $_POST['flavorSize'] ?? null;
    $price = $_POST['price'] ?? null;
    $image = $_FILES['image'] ?? null;

    if ($name && $category && $price) { // Removed image check here, see below
        // Determine the table based on the category type
        $categoryLower = strtolower($category);
        if (strpos($categoryLower, 'drink') !== false || in_array($categoryLower, ['froyo', 'coffee & blended'])) {
            $categoryTable = 'drink_item';
            $categoryField = 'drink_category';
            $variationTable = 'drink_variation';
            $servingField = 'type';
            $flavorField = 'size';
            $idField = 'drink_id';  // Add this line for dynamic ID field
        } else {
            $categoryTable = 'food_item';
            $categoryField = 'food_category';
            $variationTable = 'food_variation';
            $servingField = 'serving';
            $flavorField = 'flavor';
            $idField = 'food_id';  // Add this line for dynamic ID field
        }

        // Check if the product already exists
        $sql = "SELECT id, image FROM $categoryTable WHERE name = ? AND category_id = (SELECT id FROM $categoryField WHERE name = ?)";
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $name, $category);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($productId, $existingImagePath);
                $stmt->fetch();
            } else {
                $productId = null;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Failed to check product existence: ' . $db->error;
            echo json_encode($response);
            exit();
        }

        // Handle file upload if a new image is provided
        if ($image && $image['tmp_name']) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($image["name"]);
            if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                $response['message'] = 'Failed to upload image';
                echo json_encode($response);
                exit();
            }
        } else {
            $imagePath = $existingImagePath ?? null;
        }

        // Insert the product if it doesn't already exist
        if (!$productId) {
            $sql = "INSERT INTO $categoryTable (name, category_id, image) VALUES (?, (SELECT id FROM $categoryField WHERE name = ?), ?)";
            $stmt = $db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sss", $name, $category, $imagePath);
                if ($stmt->execute()) {
                    $productId = $stmt->insert_id;
                } else {
                    $response['message'] = 'Failed to insert product: ' . $stmt->error;
                    echo json_encode($response);
                    exit();
                }
                $stmt->close();
            } else {
                $response['message'] = 'Failed to prepare product insertion statement: ' . $db->error;
                echo json_encode($response);
                exit();
            }
        }

        // Insert into variations table
        if ($servingType || $flavorSize) {
            $sql = "INSERT INTO $variationTable ($idField, $servingField, $flavorField, price) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("issd", $productId, $servingType, $flavorSize, $price);
                if ($stmt->execute()) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Failed to insert product variations: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'Failed to prepare variation insertion statement: ' . $db->error;
            }
        } else {
            $response['success'] = true;
        }
    } else {
        $response['message'] = 'Name, category, and price are required';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>
