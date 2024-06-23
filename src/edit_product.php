<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files and start session if needed
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['id'])) {
    // Check if the request is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Retrieve product ID, variation ID and category name from POST data
            $productId = $_POST['productId'] ?? '';
            $variationId = $_POST['variationId'] ?? ''; // Assuming variationId is passed in POST data
            $categoryName = $_POST['editCategory'] ?? '';
            $productType = $_POST['productType'] ?? '';

            // Validate product ID and variation ID
            if (empty($productId)) {
                throw new Exception("Product ID is missing or empty");
            }
            if (empty($variationId)) {
                throw new Exception("Variation ID is missing or empty");
            }

            // Validate category name
            if (empty($categoryName)) {
                throw new Exception("Category name is missing or empty");
            }

            // Determine the table name and category validation query based on product type
            if ($productType === 'drink') {
                $tableName = 'drink_item';
                $categoryTable = 'drink_category';
                $variationTable = 'drink_variation';
                $variationColumn = 'drink_id';
            } else {
                $tableName = 'food_item';
                $categoryTable = 'food_category';
                $variationTable = 'food_variation';
                $variationColumn = 'food_id';
            }

            // Fetch category ID based on category name
            $categoryCheckStmt = $db->prepare("SELECT id FROM $categoryTable WHERE name = ?");
            if (!$categoryCheckStmt) {
                throw new Exception("Failed to prepare category query: " . $db->error);
            }
            $categoryCheckStmt->bind_param('s', $categoryName); // Use 's' for string
            $categoryCheckStmt->execute();
            $categoryCheckStmt->bind_result($categoryId);
            $categoryCheckStmt->fetch();
            $categoryCheckStmt->close();

            // Check if category ID was found
            if (!$categoryId) {
                throw new Exception("Category not found: $categoryName");
            }

            // Prepare SQL statement for the main item table
            $sql = "UPDATE $tableName SET ";
            $params = array();
            $types = '';

            // Check and add fields to update if provided
            if (!empty($_FILES['editImage']['name'])) {
                $uploadDirectory = __DIR__ . '/uploads/';

                // Check if the uploads directory exists, if not, create it
                if (!is_dir($uploadDirectory)) {
                    if (!mkdir($uploadDirectory, 0777, true)) {
                        throw new Exception("Failed to create uploads directory");
                    }
                }
                
                $newFilename = basename($_FILES['editImage']['name']);
                $imagePath = 'uploads/' . $newFilename; // Store relative path
                $sql .= "image = ?, ";
                $params[] = $imagePath;
                $types .= 's';
                // Handle file upload here (move the file to the desired location)
                if (!move_uploaded_file($_FILES['editImage']['tmp_name'], $uploadDirectory . $newFilename)) {
                    throw new Exception("Failed to upload image");
                }
            }
            if (!empty($_POST['editName'])) {
                $sql .= "name = ?, ";
                $params[] = $_POST['editName'];
                $types .= 's';
            }

            // Add category_id to SQL statement parameters
            $sql .= "category_id = ?, ";
            $params[] = $categoryId;
            $types .= 'i';

            // Remove trailing comma and space
            $sql = rtrim($sql, ", ");

            // Add WHERE clause
            $sql .= " WHERE id = ?";
            $params[] = $productId;
            $types .= 'i'; // Use 'i' for the product ID at the end

            // Prepare and bind parameters
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $db->error);
            }

            // Bind the parameters
            $stmt->bind_param($types, ...$params);

            // Execute the statement
            if ($stmt->execute()) {
                // Prepare SQL statement for the variation table
                $sqlVariation = "UPDATE $variationTable SET ";
                $paramsVariation = [];
                $typesVariation = '';

                // Check and add fields to update if provided
                if (!empty($_POST['editPrice'])) {
                    $sqlVariation .= "price = ?, ";
                    $paramsVariation[] = $_POST['editPrice'];
                    $typesVariation .= 'd';
                }

                // Extract and handle serving/type and flavor/size fields based on product type
                if ($productType === 'drink') {
                    if (!empty($_POST['editServingType'])) {
                        $sqlVariation .= "type = ?, ";
                        $paramsVariation[] = $_POST['editServingType'];
                        $typesVariation .= 's';
                    }
                    if (!empty($_POST['editFlavorSize'])) {
                        $sqlVariation .= "size = ?, ";
                        $paramsVariation[] = $_POST['editFlavorSize'];
                        $typesVariation .= 's';
                    }
                } else {
                    if (!empty($_POST['editServingType'])) {
                        $sqlVariation .= "serving = ?, ";
                        $paramsVariation[] = $_POST['editServingType'];
                        $typesVariation .= 's';
                    }
                    if (!empty($_POST['editFlavorSize'])) {
                        $sqlVariation .= "flavor = ?, ";
                        $paramsVariation[] = $_POST['editFlavorSize'];
                        $typesVariation .= 's';
                    }
                }

                // Remove trailing comma and space
                $sqlVariation = rtrim($sqlVariation, ", ");

                // Add WHERE clause with variation ID
                $sqlVariation .= " WHERE id = ? AND $variationColumn = ?";
                $paramsVariation[] = $variationId;
                $paramsVariation[] = $productId;
                $typesVariation .= 'i'; // Use 'i' for the variation ID
                $typesVariation .= 'i'; // Use 'i' for the product ID

                // Prepare and bind parameters
                $stmtVariation = $db->prepare($sqlVariation);
                if (!$stmtVariation) {
                    throw new Exception("Failed to prepare variation statement: " . $db->error);
                }

                // Bind the parameters
                $stmtVariation->bind_param($typesVariation, ...$paramsVariation);

                // Execute the variation statement
                if (!$stmtVariation->execute()) {
                    throw new Exception("Failed to update variation: " . $stmtVariation->error);
                }
                $stmtVariation->close();

                echo json_encode(array("success" => true));
            } else {
                throw new Exception("Failed to update item: " . $stmt->error);
            }

            $stmt->close();
        } catch (Exception $e) {
            echo json_encode(array("success" => false, "error" => $e->getMessage()));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "Invalid request method."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "User not logged in."));
}
?>
