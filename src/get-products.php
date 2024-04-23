<?php
require_once 'includes/db-connector.php';

if(isset($_POST['category'])) {
    $categoryId = $_POST['category'];

    // Query food products for the selected category
    $foodSql = "SELECT 
                    fi.name AS item_name, 
                    fv.serving AS serving, 
                    fv.flavor AS flavor, 
                    fi.id AS item_id, 
                    MIN(fv.price) AS price 
                FROM 
                    food_item fi 
                LEFT JOIN 
                    food_variation fv ON fi.id = fv.food_id 
                WHERE 
                    fi.category_id = $categoryId 
                GROUP BY 
                    fi.id";
    
    $foodResult = $db->query($foodSql);

    // Query drink products for the selected category
    $drinkSql = "SELECT 
                    di.name AS item_name, 
                    dv.type AS type, 
                    dv.size AS size, 
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
    
    $drinkResult = $db->query($drinkSql);

    echo '<div class="col-lg-12">';
    echo '<div class="row row-cols-2 row-cols-md-3 g-4 pt-2">';

    // Check if there are any food products
    if($foodResult->num_rows > 0) {
        // Output food products
        while($row = $foodResult->fetch_assoc()) {
            // Output each food product within a column
            echo '<div class="col">';
            echo '<a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#product" data-product-id="' . $row['item_id'] . '" data-product-type="food">';
            echo '<div class="card h-100 bg-rose-white shadow-sm rounded-4 zoom-on-hover" style="min-height: 200px">';
            echo '<div class="container">';
            echo '<div class="row">';
            // Displaying food variations and price
            echo '<div class="text-capitalize pt-2">';
            if (!empty($row['serving'])) {
                echo '<span class="badge text-bg-medium-brown rounded-1 text-wrap">' . $row['serving'] . '</span> ';
            }
            if (!empty($row['flavor'])) {
                echo '<span class="badge text-bg-carbon-grey rounded-1 text-wrap">' . $row['flavor'] . '</span><br> ';
            }
            echo '</div>';
        
            echo '<div class="col pt-1 ps-4 pe-4">';
            echo '<img src="images/coffee-img-placeholder.png" class="pt-2 card-img-top rounded-circle" alt="product-img">';
            echo '</div>';
            echo '<div class="pt-1 card-body">';
            echo '<div class="card-text text-capitalize">';

            echo '<div class="text-center text-carbon-grey text-truncate text-capitalize">';
            echo '<span class="fw-medium " style="font-size: 15px;"> ' . $row['item_name'] . ' </span><br>';
            echo '<span class="fw-bold">' . $row['price'] . '</span>';
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
    }

    // Check if there are any drink products
    if($drinkResult->num_rows > 0) {
        // Output drink products
        while($row = $drinkResult->fetch_assoc()) {
            // Output each drink product within a column
            echo '<div class="col">';
            echo '<a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#product" data-product-id="' . $row['item_id'] . '" data-product-type="drink">';
            echo '<div class="card h-100 bg-rose-white shadow-sm rounded-4 zoom-on-hover" style="min-height: 200px">';
            echo '<div class="container">';
            echo '<div class="row">';
            
            // Displaying drink variations and price
            echo '<div class="text-capitalize pt-2">';
                if (!empty($row['type'])) {
                    echo '<span class="badge text-bg-medium-brown rounded-1 text-wrap">' . $row['type'] . '</span> ';
                }
                if (!empty($row['size'])) {
                    echo '<span class="badge text-bg-carbon-grey rounded-1 text-wrap">' . $row['size'] . 'oz' . '</span><br> ';
                }
            echo '</div>';

            echo '<div class="col pt-1 ps-4 pe-4">';
            echo '<img src="images/coffee-img-placeholder.png" class="pt-2 card-img-top rounded-circle" alt="product-img">';
            echo '</div>';
            echo '<div class="pt-1 card-body">';
            echo '<div class="card-text text-capitalize">';

            echo '<div class="text-center text-carbon-grey text-truncate text-capitalize">';
            echo '<span class="fw-medium " style="font-size: 15px;"> ' . $row['item_name'] . ' </span><br>';
            echo '<span class="fw-bold">' . $row['price'] . '</span>';
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
    }

    // End the container
    echo '</div>';
    echo '</div>';
} else {
    echo 'No category selected';
}
?>

<!-- Modal for Product Variations -->
<div class="modal fade" id="product" tabindex="-1" aria-labelledby="productName" aria-hidden="true">
    <div class="modal-dialog px-3">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-6 fw-semibold text-carbon-grey text-capitalize" id="productName">Product Name</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productVariation">
                <form>
                    <div class="mb-4">
                        <label for="servingOrType" class="col-form-label fw-medium text-carbon-grey mb-2">Serving/Type:</label><br/>
                        <div class="btn-group gap-2" role="group" aria-label="Basic radio toggle button group" id="servingOrTypeGroup">
                            <!-- Serving or Type variations will be populated here -->
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3">
                        <label for="flavorOrSize" class="col-form-label fw-medium text-carbon-grey mb-2">Flavor/Size:</label><br/>
                        <div class="btn-group gap-2 mb-2" role="group" aria-label="Basic radio toggle button group" id="flavorOrSizeGroup">
                            <!-- Flavor or Size variations will be populated here -->
                        </div>
                    </div>
                    <hr>
                    <div class="mb-1">
                        <label for="quantityInput" class="col-form-label fw-medium text-carbon-grey">Quantity:</label>
                        <div class="input-group mt-2 mb-3">
                            <span class="btn btn-lg btn-outline-carbon-grey input-group-text fw-bold py-3 px-5" role="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8"/>
                                </svg>
                            </span>
                            <input type="text" class="form-control text-center border border-carbon-grey bg-white text-carbon-grey fw-medium py-3" placeholder="1" disabled>
                            <span class="btn btn-lg btn-outline-carbon-grey input-group-text fw-bold py-3 px-5" role="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="col-md mb-2">
                        <div class="form-floating py-3">
                            <input type="text" readonly class="form-control fw-bold fs-5 text-carbon-grey border border-carbon-grey bg-white" id="floatingInputGrid" placeholder="100.00" value="100.00">
                            <label for="floatingInputGrid" class="text-carbon-grey fw-medium fs-5">Price</label>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3" data-bs-dismiss="modal" aria-label="Close">cancel</button>
                <button type="button" class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">add to order cart</button>
            </div>
            </form>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    $('#product').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);
        let productId = button.data('product-id');
        let productType = button.data('product-type');

        $.ajax({
            url: 'get-product-details.php',
            type: 'POST',
            data: {productId: productId, productType: productType},
            success: function(response) {
                let data = JSON.parse(response);
                $('#productName').text(data.name);

                // Clear existing variation buttons
                $('#servingOrTypeGroup').empty();
                $('#flavorOrSizeGroup').empty();

                // Populate serving/type variation buttons
                if (data.servingOrType) {
                    $('#servingOrTypeGroup').append('<label class="btn btn-sm btn-outline-product fw-semibold rounded-4">' + data.servingOrType + '</label>');
                }

                // Populate flavor/size variation buttons
                if (data.flavorOrSize) {
                    $('#flavorOrSizeGroup').append('<label class="btn btn-sm btn-outline-product fw-semibold rounded-4">' + data.flavorOrSize + '</label>');
                }

                $('#quantityInput').val(1);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $('#product').on('hide.bs.modal', function() {
        $('#productName').text('Product Name');
        $('#quantityInput').val(1);
    });

    $('#quantityInput').on('input', function() {
        let quantity = parseInt($(this).val());
        if (isNaN(quantity) || quantity < 1) {
            $(this).val(1);
        }
    });
});
</script>