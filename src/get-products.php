<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once 'includes/db-connector.php';
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

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
            echo '<a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#product" data-product-id="' . $row['item_id'] . '" data-product-type="food" data-product-name="' . $row['item_name'] . '" data-product-serving="' . $row['serving'] . '" data-product-flavor="' . $row['flavor'] . '" data-product-price="' . $row['price'] . '">';
            echo '<div class="card h-100 bg-white shadow-sm rounded-4 zoom-on-hover" style="min-height: 200px">';
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
            echo '<a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#product" data-product-id="' . $row['item_id'] . '" data-product-type="drink" data-product-name="' . $row['item_name'] . '" data-product-type-var="' . $row['type'] . '" data-product-size="' . $row['size'] . '" data-product-price="' . $row['price'] . '">';
            echo '<div class="card h-100 bg-white shadow-sm rounded-4 zoom-on-hover" style="min-height: 200px">';
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

<style>
    /* Hide the spinner arrows */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    /* Firefox */
    -moz-appearance: textfield;
}
</style>

<!-- Modal for Product Variations -->
<div class="modal fade" id="product" tabindex="-1" aria-labelledby="productName" aria-hidden="true">
    <div class="modal-dialog px-3">
        <div class="modal-content">
            <form id="orderForm" action="add_to_order_cart.php" method="post">
                <div class="modal-header">
                    <h1 class="modal-title fs-6 fw-semibold text-carbon-grey text-capitalize" id="productName">Product Name</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="productVariation">
                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="product_type" id="product_type">
                    <input type="hidden" name="product_name" id="product_name">
                    <input type="hidden" name="serving_or_type" id="serving_or_type">
                    <input type="hidden" name="flavor_or_size" id="flavor_or_size">
                    <input type="hidden" name="price" id="product_price">

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
                            <button class="btn btn-lg btn-outline-carbon-grey input-group-text fw-bold py-3 px-5" type="button" id="quantityMinus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8"/>
                                </svg>
                            </button>
                            <input type="number" id="quantityInput" name="quantity" class="form-control text-center border border-carbon-grey bg-white text-carbon-grey fw-medium py-3" value="1" min="1">
                            <button class="btn btn-lg btn-outline-carbon-grey input-group-text fw-bold py-3 px-5" type="button" id="quantityPlus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="col-md mb-2">
                        <div class="form-floating py-3">
                            <input type="text" readonly class="form-control fw-bold fs-5 text-carbon-grey border border-carbon-grey bg-white" id="priceDisplay" name="price">
                            <label for="priceDisplay" class="text-carbon-grey fw-medium fs-5">Price</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">Add to Order Cart</button>
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
        let productName = button.data('product-name');
        let productPrice = parseFloat(button.data('product-price')); // Ensure price is a float

        let servingOrType = button.data('product-serving');
        let flavorOrSize = button.data('product-flavor');

        $('#product_id').val(productId);
        $('#product_type').val(productType);
        $('#product_name').val(productName);
        $('#product_price').val(productPrice.toFixed(2)); // Set the value of the price input field
        $('#serving_or_type').val(servingOrType);
        $('#flavor_or_size').val(flavorOrSize);

        $('#productName').text(productName);

        // Clear existing variation buttons
        $('#servingOrTypeGroup').empty();
        $('#flavorOrSizeGroup').empty();

        // Populate serving/type variation buttons
        if (servingOrType) {
            let servingOrTypeArray = servingOrType.split(',');
            servingOrTypeArray.forEach(function(serving) {
                $('#servingOrTypeGroup').append('<label class="btn btn-sm btn-outline-product fw-semibold rounded-4">' + serving.trim() + '</label>');
            });
        }

        // Populate flavor/size variation buttons
        if (flavorOrSize) {
            let flavorOrSizeArray = flavorOrSize.split(',');
            flavorOrSizeArray.forEach(function(flavor) {
                $('#flavorOrSizeGroup').append('<label class="btn btn-sm btn-outline-product fw-semibold rounded-4">' + flavor.trim() + '</label>');
            });
        }

        // Add click event to dynamically created buttons
        $('#servingOrTypeGroup .btn').on('click', function() {
            $('#servingOrTypeGroup .btn').removeClass('active');
            $(this).addClass('active');
        });

        $('#flavorOrSizeGroup .btn').on('click', function() {
            $('#flavorOrSizeGroup .btn').removeClass('active');
            $(this).addClass('active');
        });

        $('#quantityInput').val(1);
        updateTotalPrice();

        function updateTotalPrice() {
            let quantity = parseInt($('#quantityInput').val(), 10);
            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
                $('#quantityInput').val(1);
            }
            let totalPrice = (productPrice * quantity).toFixed(2);
            $('#priceDisplay').val(totalPrice); // Set the value of the input element
        }

        // Update the price when quantity changes
        $('#quantityInput').on('input', function() {
            updateTotalPrice();
        });

        // Initialize the plus and minus buttons
        $('#quantityMinus').off('click').on('click', function() {
            let input = $('#quantityInput');
            let currentValue = parseInt(input.val(), 10);
            if (isNaN(currentValue) || currentValue <= 1) {
                currentValue = 1;
            } else {
                input.val(currentValue - 1);
            }
            updateTotalPrice();
        });

        $('#quantityPlus').off('click').on('click', function() {
            let input = $('#quantityInput');
            let currentValue = parseInt(input.val(), 10);
            if (isNaN(currentValue)) {
                currentValue = 1;
            } else {
                input.val(currentValue + 1);
            }
            updateTotalPrice();
        });
    });

    $('#product').on('hide.bs.modal', function() {
        $('#productName').text('Product Name');
        $('#quantityInput').val(1);
        $('#servingOrTypeGroup').empty();
        $('#flavorOrSizeGroup').empty();
        $('#priceDisplay').val('');
    });
});
</script>

<script>
    $(document).ready(function() {
    $('#product').on('show.bs.modal', function(event) {
        // Existing modal setup code...
    });

    $('#product').on('hide.bs.modal', function() {
        // Existing modal cleanup code...
    });

    $('.addToOrderCartBtn').on('click', function() {
        let productId = $('#product').data('product-id');
        let productType = $('#product').data('product-type');
        let productName = $('#productName').text();
        let servingOrType = $('#servingOrTypeGroup .active').text() || '';
        let flavorOrSize = $('#flavorOrSizeGroup .active').text() || '';
        let quantity = $('#quantityInput').val();
        let price = $('#priceDisplay').val(); // Update to get the price from the correct input

        $.ajax({
            url: 'add_to_order_cart.php',
            type: 'POST',
            data: {
                product_id: productId,
                product_type: productType,
                product_name: productName,
                serving_or_type: servingOrType,
                flavor_or_size: flavorOrSize,
                quantity: quantity,
                price: price
            },
            success: function(response) {
                let data = JSON.parse(response);
                if (data.status === 'success') {
                    alert('Item added to order cart');
                    // Optionally update the billing section dynamically
                } else {
                    alert('Failed to add item to order cart');
                }
                $('#product').modal('hide');
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});

</script>
