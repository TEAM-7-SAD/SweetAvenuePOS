<?php
// Include necessary files and connect to the database
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

// Check if the required parameters are present
if (isset($_POST['productId']) && isset($_POST['productType'])) {
    $productId = $_POST['productId'];
    $productType = $_POST['productType'];

    $productDetails = array();
    $uniqueKeys = array(); // Array to keep track of unique keys

    // Fetch product details based on product type
    if ($productType === 'food') {
        // Query food product details from the database
        $foodSql = "SELECT 
                        fi.name AS item_name, 
                        IFNULL(fv.serving, 'Default') AS serving, 
                        IFNULL(fv.flavor, 'Default') AS flavor, 
                        fi.id AS item_id, 
                        MIN(fv.price) AS price 
                    FROM 
                        food_item fi 
                    LEFT JOIN 
                        food_variation fv ON fi.id = fv.food_id 
                    WHERE 
                        fi.id = $productId 
                    GROUP BY 
                        fi.id, fv.serving, fv.flavor";

        $foodResult = $db->query($foodSql);

        if ($foodResult->num_rows > 0) {
            while ($row = $foodResult->fetch_assoc()) {
                $key = $row['serving'] . '-' . $row['flavor']; // Create a unique key for each variation

                if (!in_array($key, $uniqueKeys)) { // Check if the key already exists
                    $uniqueKeys[] = $key; // Add the key to the list of seen keys

                    $productDetails[] = array(
                        // 'id' => $row['item_id'],
                        'name' => $row['item_name'],
                        'servingOrType' => $row['serving'],
                        'flavorOrSize' => $row['flavor'],
                        'price' => $row['price']
                    );
                }
            }
        } else {
            // Handle no results found
            $productDetails = array('error' => 'No food product found with the specified ID');
        }
    } elseif ($productType === 'drink') {
        // Query drink product details from the database
        $drinkSql = "SELECT 
                        di.name AS item_name, 
                        IFNULL(dv.type, 'Default') AS type, 
                        IFNULL(dv.size, 'Default') AS size, 
                        di.id AS item_id, 
                        dv.price AS price 
                    FROM 
                        drink_item di 
                    LEFT JOIN 
                        drink_variation dv ON di.id = dv.drink_id 
                    WHERE 
                        di.id = $productId";

        $drinkResult = $db->query($drinkSql);

        if ($drinkResult->num_rows > 0) {
            while ($row = $drinkResult->fetch_assoc()) {
                $key = $row['type'] . '-' . $row['size']; // Create a unique key for each variation

                if (!in_array($key, $uniqueKeys)) { // Check if the key already exists
                    $uniqueKeys[] = $key; // Add the key to the list of seen keys

                    $sizeWithOz = $row['size'] . 'oz';

                    $productDetails[] = array(
                        // 'id' => $row['item_id'],
                        'name' => $row['item_name'],
                        'servingOrType' => $row['type'],
                        'flavorOrSize' => $sizeWithOz,
                        'price' => $row['price']
                    );
                }
            }
        } else {
            // Handle no results found
            $productDetails = array('error' => 'No drink product found with the specified ID');
        }
    }

    // Return the product details as JSON
    echo json_encode($productDetails);
}
?>
