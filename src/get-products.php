<?php
require_once 'includes/db-connector.php';

if(isset($_GET['category'])) {
    $categoryId = $_GET['category'];
    
    // Query products for the selected category
    $sql = "SELECT 
                fi.name AS item_name, 
                MIN(fv.serving) AS serving, 
                MIN(fv.flavor) AS flavor, 
                fi.id AS item_id, 
                MIN(fv.price) AS price 
            FROM 
                food_item fi 
            LEFT JOIN 
                food_variation fv ON fi.id = fv.food_id 
            WHERE 
                fi.category_id = $categoryId 
            GROUP BY 
                fi.id 

            UNION 

            SELECT 
                di.name AS item_name, 
                MIN(dv.type) AS type, 
                MIN(dv.size) AS size, 
                di.id AS item_id, 
                MIN(dv.price) AS price 
            FROM 
                drink_item di 
            LEFT JOIN 
                drink_variation dv ON di.id = dv.drink_id 
            WHERE 
                di.category_id = $categoryId 
            GROUP BY 
                di.id";
    
    $result = $db->query($sql);

    if($result->num_rows > 0) {
        // Start the first row
        echo '<div class="col-lg-12">';
        echo '<div class="row row-cols-2 row-cols-md-4 g-3 pt-2">';

        while($row = $result->fetch_assoc()) {
            // Output each product within a column
            echo '<div class="col">';
            echo '<a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#product">';
            echo '<div class="card h-100 bg-rose-white shadow-sm rounded-4 zoom-on-hover" style="min-height: 200px">';
            echo '<div class="container">';
            echo '<div class="row">';
            echo '<div class="col pt-2 ps-3 pe-3">';
            echo '<img src="images/coffee-img-placeholder.png" class="card-img-top rounded-circle" alt="product-img">';
            echo '</div>';
            echo '<div class="pt-3 card-body">';
            echo '<div class="card-text lh-1 text-center text-capitalize text-truncate">';
            echo '<span class="fw-medium " style="font-size: 15px;"> ' . $row['item_name'] . ' </span><br>';
            
            // Displaying variations and price
            if (!empty($row['serving'])) {
                echo '<span class="badge text-bg-medium-brown text-wrap">' . $row['serving'] . '</span> ';
            }
            if (!empty($row['flavor'])) {
                echo '<span class="badge text-bg-secondary text-wrap">' . $row['flavor'] . 'oz' . '</span> ';
            }
            if (!empty($row['type'])) {
                echo '<span class="badge text-bg-primary text-wrap">' . $row['type'] . '</span> ';
            }
            if (!empty($row['size'])) {
                echo '<span class="badge text-bg-danger text-wrap">' . $row['size'] . 'oz' . '</span> ';
            }
            echo '<br>';
            echo '<span class="fw-bold text-medium-brown pt-4 mt-3">' . $row['price'] . '</span>';
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }

        // End the first row
        echo '</div>';
        echo '</div>';
    } else {
        echo 'No products found for this category';
    }
}
?>

<!--Modal for Product Variations-->
<div class="modal fade" id="product" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-6" id="exampleModalLabel">Product Name</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form>
            <div class="mb-3">
                <label for="recipient-name" class="col-form-label">Serving</label>
                <input type="text" class="form-control" id="recipient-name">
            </div>
            <div class="mb-3">
                <label for="recipient-name" class="col-form-label">Flavor</label>
                <input type="text" class="form-control" id="recipient-name">
            </div>
            <div class="mb-3">
                <label for="recipient-name" class="col-form-label">Quantity</label>
                
            </div>
            <div class="mb-3">
            <div class="mb-3">
                <label for="recipient-name" class="col-form-label">Price</label>
                
            </div>
            <div class="mb-3">
                <label for="recipient-name" class="col-form-label">Discount</label>
                
            </div>
                <label for="message-text" class="col-form-label">Note:</label>
                <textarea class="form-control"></textarea>
            </div>
            </form>
        </div>
        <button type="button" class="btn rounded-0 rounded-bottom-2 mt-3 py-3 fw-semibold fs-6 btn-medium-brown text-light">Add to Current Order</button>
        </div>
    </div>
</div>
