<?php
// Include necessary files and connect to the database
require_once 'includes/db-connector.php';

// Check if the required parameters are present
if(isset($_POST['productId']) && isset($_POST['productType'])) {
    $productId = $_POST['productId'];
    $productType = $_POST['productType'];

    // Fetch product details based on product type
    if($productType === 'food') {
        // Query food product details from the database
        $foodSql = "SELECT 
                        fi.name AS item_name, 
                        fv.serving AS serving, 
                        fv.flavor AS flavor, 
                        fi.id AS item_id, 
                        fv.price AS price 
                    FROM 
                        food_item fi 
                    LEFT JOIN 
                        food_variation fv ON fi.id = fv.food_id 
                    WHERE 
                        fi.id = $productId";
        
        $foodResult = $db->query($foodSql);

        if($foodResult->num_rows > 0) {
            $row = $foodResult->fetch_assoc();
            // Construct an array with the retrieved data
            $productDetails = array(
                'name' => $row['item_name'],
                'servingOrType' => $row['serving'],
                'flavorOrSize' => $row['flavor'],
                'price' => $row['price']
            );
        } else {
            // Handle no results found
            $productDetails = array(
                'error' => 'No food product found with the specified ID'
            );
        }
    } elseif($productType === 'drink') {
        // Query drink product details from the database
        $drinkSql = "SELECT 
                        di.name AS item_name, 
                        dv.type AS type, 
                        dv.size AS size, 
                        di.id AS item_id, 
                        dv.price AS price 
                    FROM 
                        drink_item di 
                    LEFT JOIN 
                        drink_variation dv ON di.id = dv.drink_id 
                    WHERE 
                        di.id = $productId";
        
        $drinkResult = $db->query($drinkSql);

        if($drinkResult->num_rows > 0) {
            $row = $drinkResult->fetch_assoc();
            // Construct an array with the retrieved data
            $productDetails = array(
                'name' => $row['item_name'],
                'servingOrType' => $row['type'],
                'flavorOrSize' => $row['size'],
                'price' => $row['price']
            );
        } else {
            // Handle no results found
            $productDetails = array(
                'error' => 'No drink product found with the specified ID'
            );
        }
    } else {
        // Handle unknown product type
        // For example, return an error message
        $productDetails = array(
            'error' => 'Unknown product type'
        );
    }

    // Convert the array to JSON format and output it
    echo json_encode($productDetails);
} else {
    // Handle missing parameters
    // For example, return an error message
    echo json_encode(array('error' => 'Missing parameters'));
}
?>