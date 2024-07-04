<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

if(isset($_POST['category'])) {
    $categoryId = $_POST['category'];

    // Query food products for the selected category
    $foodSql = "SELECT 
                    fi.name AS item_name, 
                    fv.serving AS serving, 
                    fv.flavor AS flavor, 
                    fi.id AS item_id, 
                    MIN(fv.price) AS price,
                    fi.image AS image  
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
                    MIN(dv.price) AS price,
                    di.image AS image  
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
            $image = !empty($row['image']) ? $row['image'] : 'uploads/coffee-img-placeholder.png';
            echo '<div class="col">';
            echo '<a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#product" data-product-id="' . $row['item_id'] . '" data-product-type="food" data-product-name="' . $row['item_name'] . '" data-product-serving="' . $row['serving'] . '" data-product-flavor="' . $row['flavor'] . '" data-product-price="' . $row['price'] . '">';
            echo '<div class="card h-100 bg-white shadow-sm rounded-4 zoom-on-hover" style="min-height: 200px">';
            echo '<div class="container">';
            echo '<div class="row">';
            echo '<div class="text-capitalize pt-2">';
            if (!empty($row['serving'])) {
                echo '<span class="badge text-bg-medium-brown rounded-1 text-wrap">' . $row['serving'] . '</span> ';
            }
            if (!empty($row['flavor'])) {
                echo '<span class="badge text-bg-carbon-grey rounded-1 text-wrap">' . $row['flavor'] . '</span><br> ';
            }
            echo '</div>';
            echo '<div class="col px-4">';
            echo '<img src="' . $image . '" alt="Product Image" class="pt-2 card-img-top rounded-circle">';
            echo '</div>';
            echo '<div class="pt-1 card-body">';
            echo '<div class="card-text text-capitalize">';

            echo '<div class="text-center text-carbon-grey text-truncate text-capitalize">';
            echo '<span class="fw-medium font-14"> ' . $row['item_name'] . ' </span><br>';
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
            $image = !empty($row['image']) ? $row['image'] : 'uploads/coffee-img-placeholder.png';
            echo '<div class="col">';
            echo '<a href="#" class="list-group-item" data-bs-toggle="modal" data-bs-target="#product" data-product-id="' . $row['item_id'] . '" data-product-type="drink" data-product-name="' . $row['item_name'] . '" data-product-type-var="' . $row['type'] . '" data-product-size="' . $row['size'] . '" data-product-price="' . $row['price'] . '">';
            echo '<div class="card h-100 bg-white shadow-sm rounded-4 zoom-on-hover" style="min-height: 200px">';
            echo '<div class="container">';
            echo '<div class="row">';

            echo '<div class="text-capitalize pt-2">';
                if (!empty($row['type'])) {
                    echo '<span class="badge text-bg-medium-brown rounded-1 text-wrap">' . $row['type'] . '</span> ';
                }
                if (!empty($row['size'])) {
                    echo '<span class="badge text-bg-carbon-grey rounded-1 text-wrap">' . $row['size'] . 'oz' . '</span><br> ';
                }
            echo '</div>';
            echo '<div class="col px-4">';
            echo '<img src="' . $image . '" alt="Product Image" class="pt-2 card-img-top rounded-circle">';
            echo '</div>';
            echo '<div class="pt-1 card-body">';
            echo '<div class="card-text text-capitalize">';

            echo '<div class="text-center text-carbon-grey text-truncate text-capitalize">';
            echo '<span class="fw-medium font-14"> ' . $row['item_name'] . ' </span><br>';
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
<div class="modal fade" id="product" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="orderForm" method="post">
                <div class="modal-header bg-medium-brown">
                    <h1 class="modal-title fs-6 fw-semibold text-light text-uppercase" id="productName"><!-- Product name will be loaded here --></h1>
                </div>
                <div class="modal-body" id="productVariation">
                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="product_type" id="product_type">
                    <input type="hidden" name="product_name" id="product_name">
                    <input type="hidden" name="serving_or_type" id="serving_or_type">
                    <input type="hidden" name="flavor_or_size" id="flavor_or_size">
                    <input type="hidden" name="price" id="product_price">
                    <input type="hidden" id="defaultServingOrType" value="">
                    <input type="hidden" id="defaultFlavorOrSize" value="">

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
                            <input type="number" id="quantityInput" name="quantity" class="form-control text-center border border-carbon-grey bg-white text-carbon-grey fw-medium py-3 shadow-sm" value="1" min="1">
                            <button class="btn btn-lg btn-outline-carbon-grey input-group-text fw-bold py-3 px-5" type="button" id="quantityPlus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="col-md mb-2">
                        <div class="form-floating py-3">
                            <input type="text" readonly class="form-control fw-bold fs-5 text-carbon-grey border border-carbon-grey bg-white shadow-sm" id="priceDisplay" name="price">
                            <label for="priceDisplay" class="text-carbon-grey fw-medium fs-5">Price</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" id="addToOrderCart" class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">Add to Order Cart</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var productData;

    $('#product').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var productId = button.data('product-id');
        var productType = button.data('product-type');

        $.ajax({
            url: 'get-product-details.php',
            type: 'POST',
            data: { productId: productId, productType: productType },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.error) {
                    alert(data.error);
                } else {
                    productData = data;
                    $('#productName').text(data[0].name);
                    $('#product_id').val(productId);
                    $('#product_type').val(productType);
                    $('#product_name').val(data[0].name);
                    $('#servingOrTypeGroup').empty();
                    $('#flavorOrSizeGroup').empty();
                    $('#priceDisplay').val('');
                    $('#quantityInput').val(1);

                    var defaultServingOrType = 'Default';
                    var defaultFlavorOrSize = 'Default';

                    var servingOrTypeSet = new Set();
                    var flavorOrSizeSet = new Set();

                    data.forEach(function(variation) {
                        if (variation.servingOrType !== defaultServingOrType) {
                            servingOrTypeSet.add(variation.servingOrType);
                        }
                        if (variation.flavorOrSize !== defaultFlavorOrSize) {
                            flavorOrSizeSet.add(variation.flavorOrSize);
                        }
                    });

                    if (servingOrTypeSet.size > 0) {
                        servingOrTypeSet.forEach(function(servingOrType) {
                            $('#servingOrTypeGroup').append('<button type="button" class="btn btn-sm btn-outline-product text-capitalize fw-semibold rounded-4">' + servingOrType + '</button>');
                        });
                    } else {
                        $('#servingOrTypeGroup').append('<span class="text-carbon-grey font-13 fst-italic">No variations available</span>');
                    }

                    if (flavorOrSizeSet.size > 0) {
                        flavorOrSizeSet.forEach(function(flavorOrSize) {
                            $('#flavorOrSizeGroup').append('<button type="button" class="btn btn-sm btn-outline-product text-capitalize fw-semibold rounded-4">' + flavorOrSize + '</button>');
                        });
                    } else {
                        $('#flavorOrSizeGroup').append('<span class="text-carbon-grey font-13 fst-italic">No variations available</span>');
                    }

                    $('#defaultServingOrType').val(defaultServingOrType);
                    $('#defaultFlavorOrSize').val(defaultFlavorOrSize);

                    if ($('#servingOrTypeGroup button').length > 0) {
                        $('#servingOrTypeGroup button:first-child').addClass('active');
                    } else {
                        $('#serving_or_type').val(defaultServingOrType);
                    }

                    if ($('#flavorOrSizeGroup button').length > 0) {
                        $('#flavorOrSizeGroup button:first-child').addClass('active');
                    } else {
                        $('#flavor_or_size').val(defaultFlavorOrSize);
                    }

                    $('#servingOrTypeGroup, #flavorOrSizeGroup').on('click', 'button', function() {
                        $(this).siblings().removeClass('active');
                        $(this).addClass('active');
                        updatePrice();
                    });

                    updatePrice();
                }
            }
        });
    });

    $('#quantityInput').on('input', function() {
        validateQuantity();
        updatePrice();
    });

    $('#quantityMinus').on('click', function() {
        let input = $('#quantityInput');
        let currentValue = parseInt(input.val(), 10);
        if (isNaN(currentValue) || currentValue <= 1) {
            currentValue = 1;
        } else {
            input.val(currentValue - 1);
        }
        updatePrice();
    });

    $('#quantityPlus').on('click', function() {
        let input = $('#quantityInput');
        let currentValue = parseInt(input.val(), 10);
        if (isNaN(currentValue)) {
            currentValue = 1;
        } else {
            input.val(currentValue + 1);
        }
        updatePrice();
    });

    $('#quantityInput').on('keypress', function(event) {
        // Prevent non-numeric input
        if (!/[0-9]/.test(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    $('#quantityInput').on('paste', function(event) {
        // Prevent pasting non-numeric values
        var clipboardData = event.originalEvent.clipboardData.getData('text');
        if (!/^\d+$/.test(clipboardData)) {
            event.preventDefault();
        }
    });

    function validateQuantity() {
        let input = $('#quantityInput');
        let currentValue = parseInt(input.val(), 10);
        if (isNaN(currentValue) || currentValue < 1) {
            input.val(1);
        }
    }

    function updatePrice() {
        var selectedServingOrType = $('#servingOrTypeGroup .btn.active').text() || $('#defaultServingOrType').val();
        var selectedFlavorOrSize = $('#flavorOrSizeGroup .btn.active').text() || $('#defaultFlavorOrSize').val();
        var quantity = parseInt($('#quantityInput').val(), 10);

        var selectedVariation = productData.find(function(variation) {
            return variation.servingOrType === selectedServingOrType && variation.flavorOrSize === selectedFlavorOrSize;
        });

        if (selectedVariation) {
            var price = selectedVariation.price * quantity;
            $('#priceDisplay').val(price.toFixed(2));
            $('#product_price').val(price.toFixed(2));
            $('#serving_or_type').val(selectedServingOrType);
            $('#flavor_or_size').val(selectedFlavorOrSize);
        } else {
            var defaultPrice = productData[0].price;
            var totalPrice = defaultPrice * quantity;
            $('#priceDisplay').val(totalPrice.toFixed(2));
            $('#product_price').val(totalPrice.toFixed(2));
        }
    }

    function addToCart() {
        let productId = $('#product').data('product-id');
        let productType = $('#product').data('product-type');
        let productName = $('#productName').text();
        let servingOrType = $('#servingOrTypeGroup .active').text() || $('#defaultServingOrType').val();
        let flavorOrSize = $('#flavorOrSizeGroup .active').text() || $('#defaultFlavorOrSize').val();
        let quantity = $('#quantityInput').val();
        let price = $('#priceDisplay').val();

        // Exclude default variations from being added to the cart
        if (servingOrType === 'Default') servingOrType = '';
        if (flavorOrSize === 'Default') flavorOrSize = '';

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
                    fetchCartItems();
                    calculateSubtotal();
                    $('#product').modal('hide');
                } else {
                    alert('Failed to add item to order cart');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    }

    $("#addToOrderCart").on("click", function(event) {
        event.preventDefault();
        addToCart();
    });

    function fetchCartItems() {
        $.ajax({
            url: 'display-clicked-items.php',
            type: 'POST',
            success: function(response) {
                $('#orderCart').html(response);
                checkCart();
            },
            error: function() {
                alert('Failed to fetch cart items. Please try again.');
            }
        });
    }

    function calculateSubtotal() {
        $.ajax({
            url: 'calculate-subtotal.php',
            type: 'POST',
            success: function(response) {
                $("#subtotalValue").html(response);
                checkCart();
            },
            error: function() {
                alert('Failed to calculate subtotal. Please try again.');
            }
        });
    }
});
</script>


