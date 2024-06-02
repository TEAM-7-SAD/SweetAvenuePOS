<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Calculate the initial subtotal
$subtotal = 0;
if (isset($_SESSION['order_cart'])) {
    foreach ($_SESSION['order_cart'] as $item) {
        // Ensure that the item array contains 'price' key
        if (isset($item['price'])) {
            // Add the price of each item to the subtotal
            $subtotal += $item['price'];
        } else {
            // Handle missing data for an item (optional)
            // You can log an error or take other appropriate action
        }
    }
}
?>

<style>
    .table td, .table th {
        white-space: normal;
        word-wrap: break-word;
        max-width: 105px; /* Adjust as needed */
    }
    .roboto-mono {
            font-family: 'Roboto Mono', monospace;
    }
</style>

<!-- Billing Section -->
<div class="col-lg-5 ps-5 mb-4">
        <div class="row bg-white shadow-sm rounded-3">
            <div class="col m-4">
                <div class="text-medium-brown d-flex justify-content-center mb-4">
                    <span class="pe-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-cart-check-fill" viewBox="0 0 16 16">
                            <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708"/>
                        </svg>                    
                    </span>      
                    <div class="fs-5 text-uppercase fw-semibold align-self-end">Order Cart</div>
                </div>

                <form id="billingForm" action="#" method="post">
                    <!--Lists of Clicked Products-->
                    <div style="max-height: 327px; overflow-y: auto;">
                        <table class="table table-hover table-striped table-borderless table-transparent table-sm" role="button">
                            <tbody data-bs-toggle="modal" data-bs-target="#cartProduct" id="orderCart">
                                <?php
                                // Check if there are items in the order cart session
                                if (isset($_SESSION['order_cart']) && count($_SESSION['order_cart']) > 0) {
                                    // Loop through each item in the order cart
                                    foreach ($_SESSION['order_cart'] as $item) {
                                        echo '<tr>';
                                        echo '<td class="align-middle ps-2 pe-3">';
                                        // Display quantity
                                        echo '<p class="mb-0 text-muted fw-semibold">x' . $item['quantity'] . '</p>';
                                        echo '</td>';
                                        echo '<td class="align-middle" style="white-space: nowrap;">';
                                        echo '<div class="d-flex flex-column align-items-start py-2">';
                                        // Display product name
                                        echo '<p class="fw-bold text-muted mb-0 text-capitalize" style="font-size: 15px;">' . htmlspecialchars($item['product_name']) . '</p>';
                                        // Display serving/type
                                        echo '<div class="text-tiger-orange fw-bolder" style="font-size: 11px; max-width: 200px;">';
                                        echo '<p class="text-capitalize mb-0">' . htmlspecialchars($item['serving_or_type']) . '</p>';
                                        // Display flavor/size
                                        echo '<p class="mb-0">' . htmlspecialchars($item['flavor_or_size']) . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</td>';
                                        echo '<td class="align-middle">';
                                        // Check if price is numeric before formatting
                                        if (is_numeric($item['price'])) {
                                            // Display price
                                            echo '<h5 class="mb-0 fw-bold fs-6 text-carbon-grey">' . number_format($item['price'], 2) . '</h5>';
                                        } else {
                                            // Handle non-numeric or empty price
                                            echo '<p class="mb-0 text-danger">Price not available</p>';
                                        }
                                        echo '</td>';
                                        echo '<td class="align-middle pe-0 ps-2">';
                                        echo '<a class="text-medium-brown remove-order" href="#">';
                                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">';
                                        echo '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>';
                                        echo '</svg>';
                                        echo '</a>';
                                        echo '</td>';                                                                             
                                        echo '</tr>';
                                    }
                                } else {
                                    // If there are no items in the order cart session
                                    echo '<tr><td colspan="4" class="text-center text-muted">No items in cart</td></tr>';
                                }
                                ?>
                            </tbody> 
                        </table>            
                    </div>
                    <table class="table table-sm table-transparent table-borderless mt-5">
                        <!-- Subtotal -->
                        <tr>
                            <td>Subtotal:</td>
                            <td id="subtotalValue"><?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                    </table>      
                    <!--Cancel and Place Order Buttons-->
                    <div class="vstack gap-2 col-md-12 mx-auto pt-2 mb-3">
                        <button id="placeOrderButton" type="button" class="btn btn-medium-brown fs-6 fw-semibold py-2" data-bs-toggle="modal" data-bs-target="#orderConfirmationModal"disabled>Place Order</button>
                        <button type="button" class="btn btn-outline-carbon-grey fs-6 fw-semibold py-2" id="cancelOrder">Cancel Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Billing Section -->

            <!--Modal for Editing Clicked Contents-->
            <!-- <div class="modal fade" id="cartProduct" tabindex="-1" aria-labelledby="selectedProdModal" aria-hidden="true">
                <div class="modal-dialog px-3">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-6 fw-semibold text-carbon-grey" id="selectedProdModal">Product Name</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                        <div class="mb-4" id="billingTable">
                            <label for="recipient-name" class="col-form-label fw-medium text-carbon-grey mb-2">Serving: <span class="text-tiger-orange fw-bold">4 pieces</span></label><br/>
                            <div class="btn-group gap-2" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="serving-btn" id="serving4" autocomplete="off" checked>
                                <label class="btn btn-sm btn-outline-product fw-semibold rounded-4" for="serving4">4 pieces</label>

                                <input type="radio" class="btn-check" name="serving-btn" id="serving5" autocomplete="off">
                                <label class="btn btn-sm btn-outline-product fw-semibold rounded-4" for="serving5">8 pieces</label>

                                <input type="radio" class="btn-check" name="serving-btn" id="serving6" autocomplete="off">
                                <label class="btn btn-sm btn-outline-product fw-semibold rounded-4" for="serving6">12 pieces</label>
                            </div>
                        </div><hr>
                        <div class="mt-3">
                            <label for="recipient-name" class="col-form-label fw-medium text-carbon-grey mb-2">Flavor: <span class="text-tiger-orange fw-bold">Classic</span></label><br/>
                            <div class="btn-group gap-2 mb-2" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="flavor-btn" id="flavor5" autocomplete="off" checked>
                                <label class="btn btn-sm btn-outline-product fw-semibold rounded-4" for="flavor5">Classic</label>

                                <input type="radio" class="btn-check" name="flavor-btn" id="flavor6" autocomplete="off">
                                <label class="btn btn-sm btn-outline-product fw-semibold rounded-4" for="flavor6">Garlic Parmesan</label>

                                <input type="radio" class="btn-check" name="flavor-btn" id="flavor7" autocomplete="off">
                                <label class="btn btn-sm btn-outline-product fw-semibold rounded-4" for="flavor7">Honey Sriracha</label>
                            
                                <input type="radio" class="btn-check" name="flavor-btn" id="flavor8" autocomplete="off">
                                <label class="btn btn-sm btn-outline-product fw-semibold rounded-4" for="flavor8">Buffalo</label>
                            </div><hr>
                        </div>

                        <div class="mb-1">
                            <label for="recipient-name" class="col-form-label fw-medium text-carbon-grey">Quantity</label>        
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
                        <div class="col-md">
                            <div class="form-floating py-3">
                            <input type="text" readonly class="form-control fw-bold fs-5 text-carbon-grey border border-carbon-grey" id="floatingInputGrid" placeholder="100.00" value="100.00">
                            <label for="floatingInputGrid" class="text-carbon-grey fw-medium fs-5">Price </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn fw-medium btn-outline-carbon-grey text-capitalize py-2 px-4 my-3" data-bs-dismiss="modal" aria-label="Close">cancel</button>
                        <button type="button" class="btn fw-medium btn-medium-brown text-capitalize py-2 px-4">save changes</button>
                    </div>
                    </form>
                    </div>
                </div>
            </div> -->

<!-- Order Confirmation Modal -->
<div class="modal fade" id="orderConfirmationModal" tabindex="-1" aria-labelledby="orderConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderConfirmationLabel">Confirm The Order</h5>
            </div>
            <div class="modal-body">
                <p>Review the order:</p>
                <!-- Order details go here -->
                <div style="max-height: 327px; overflow-y: auto;">
                        <table class="table table-hover table-striped table-borderless table-transparent table-sm" role="button">
                            <tbody data-bs-toggle="modal" data-bs-target="#cartProduct" id="orderCart">
                                <?php
                                // Check if there are items in the order cart session
                                if (isset($_SESSION['order_cart']) && count($_SESSION['order_cart']) > 0) {
                                    // Loop through each item in the order cart
                                    foreach ($_SESSION['order_cart'] as $item) {
                                        echo '<tr>';
                                        echo '<td class="align-middle ps-2 pe-3">';
                                        // Display quantity
                                        echo '<p class="mb-0 text-muted fw-semibold">x' . $item['quantity'] . '</p>';
                                        echo '</td>';
                                        echo '<td class="align-middle" style="white-space: nowrap;">';
                                        echo '<div class="d-flex flex-column align-items-start py-2">';
                                        // Display product name
                                        echo '<p class="fw-bold text-muted mb-0 text-capitalize" style="font-size: 15px;">' . htmlspecialchars($item['product_name']) . '</p>';
                                        // Display serving/type
                                        echo '<div class="text-tiger-orange fw-bolder" style="font-size: 11px; max-width: 200px;">';
                                        echo '<p class="text-capitalize mb-0">' . htmlspecialchars($item['serving_or_type']) . '</p>';
                                        // Display flavor/size
                                        echo '<p class="mb-0">' . htmlspecialchars($item['flavor_or_size']) . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</td>';
                                        echo '<td class="align-middle">';
                                        // Check if price is numeric before formatting
                                        if (is_numeric($item['price'])) {
                                            // Display price
                                            echo '<h5 class="mb-0 fw-bold fs-6 text-carbon-grey">' . number_format($item['price'], 2) . '</h5>';
                                        } else {
                                            // Handle non-numeric or empty price
                                            echo '<p class="mb-0 text-danger">Price not available</p>';
                                        }
                                        echo '</td>';                                                                            
                                        echo '</tr>';
                                    }
                                } else {
                                    // If there are no items in the order cart session
                                    echo '<tr><td colspan="4" class="text-center text-muted">No items in cart</td></tr>';
                                }
                                ?>
                            </tbody> 
                        </table>  
                </div>
                <hr>
                <table class="table table-sm table-transparent table-borderless mt-5">
                    <!-- Subtotal -->
                    <tr>
                        <td>Subtotal:</td>
                        <td id="subtotalValue"><?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <!--Discount-->
                    <tr>
                        <td>Discount (%):</td>
                        <td><input type="number" id="discountInput" min="0" max="100" step="0.01" value="0" oninput="calculateGrandTotal()"></td>
                    </tr>
                    <!--Grand Total-->
                    <tr class="fw-bolder h5 text-capitalize">
                        <td class="text-carbon-grey">Grand Total:</td>
                        <td class="text-carbon-grey" id="grandTotalValue"></td>
                    </tr>
                </table>
                <!-- Tendered amount input -->
                <div class="mb-3" style="display: flex; align-items: center; gap: 10px;">
                    <label for="tenderedAmount" class="form-label" style="margin: 0;"><strong>Tendered:<span
                            style="color: red;"> *</span></strong></label>
                    <input type="number" class="form-control" id="tenderedAmount" placeholder="Enter amount tendered" style="max-width: 280px;">
                </div>
                <!-- Change calculation -->
                <div id="changeDisplay">
                    <!-- Change amount will be displayed here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                <button type="button" class="btn btn-tiger-orange fs-6 fw-semibold py-2" id="proceedOrder">Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal (shown after clicking Proceed) -->
 <!--
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div style="display: flex; align-items: center; justify-content: center;">
                        <img src="images/logo-removebg-preview.png" alt="Sweet Avenue Logo" style="max-width: 75px; margin-right: 10px;">
                        <div style="display: flex; flex-direction: column; align-items: flex-start;">
                            <h4 style="margin: 0;"><strong>SWEET AVENUE</strong></h4>
                            <h5 style="margin: 0;"><strong>COFFEE • BAKESHOP</strong></h5>
                        </div>
                    </div>
                    <br>
                    <p class="roboto-mono"><b>Wed, May 27, 2020 • 9:27:53 AM</b></p>
                    <hr>
                </div>
                <table class="table table-borderless text-center">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Serving/<br>Type</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody class="roboto-mono">
                        <tr>
                            <td>Caramel Macchiato</td>
                            <td>16oz</td>
                            <td>Iced</td>
                            <td>1x</td>
                            <td>₱100.00</td>
                        </tr>
                        <tr>
                            <td>White Chocolate Cream</td>
                            <td>22oz</td>
                            <td>Cream-based Frappuccino</td>
                            <td>2x</td>
                            <td>₱240.00</td>
                        </tr>
                        <tr>
                            <td>Cheesecake</td>
                            <td>per slice</td>
                            <td>Blueberry</td>
                            <td>3x</td>
                            <td>₱300.00</td>
                        </tr>
                        <tr>
                            <td>Chicken Wings</td>
                            <td>8 pieces</td>
                            <td>Garlic Parmesan</td>
                            <td>5x</td>
                            <td>₱1,250.00</td>
                        </tr>
                        <tr>
                            <td>Cheesecake</td>
                            <td>22oz</td>
                            <td>Froyo</td>
                            <td>4x</td>
                            <td>₱440.00</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="roboto-mono">
                    <p><strong>Subtotal:</strong> ₱469.00</p>
                    <p><strong>Discount:</strong> -₱46.90</p>
                    <p><strong>Grand Total:</strong> ₱422.10</p>
                     Display for tendered amount 
                    <p><strong>Tendered Amount:</strong> <span id="tenderedAmountReceipt"></span></p>
                    Display for change 
                    <p><strong>Change:</strong> <span id="changeDisplayReceipt"></span></p>
                </div>
                <hr>
                <p class="roboto-mono"><strong>Processed by:</strong> Admin</p><br><br>
                <div class="text-center roboto-mono">
                    <p>Thank you for your patronage. We’d love to see you again soon. You're always welcome here!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-tiger-orange fs-6 fw-semibold py-2" onclick="printReceiptContent()">Print Receipt</button>
            </div>
        </div>
    </div>
</div>-->

<script>
    function checkCart() {
        var cartItems = document.getElementById('orderCart').children;
        var placeOrderButton = document.getElementById('placeOrderButton');
        if (cartItems.length === 1 && cartItems[0].textContent.includes('No items in cart')) {
            placeOrderButton.disabled = true;
        } else {
            placeOrderButton.disabled = false;
        }
    }

    // Function to clear the cart
    function clearCart() {
        var orderCart = document.getElementById('orderCart');
        // Clear the cart
        orderCart.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No items in cart</td></tr>';
        // Recalculate subtotal
        document.getElementById('subtotalValue').innerText = '0.00';
        // Recheck the cart
        checkCart();
    }


    // Run the function on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkCart();
    });

    // Attach the clearCart function to the cancel button
    document.getElementById('cancelOrder').addEventListener('click', function() {
        clearCart();
    });

    // Optionally, call checkCart() whenever items are added/removed dynamically
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initially disable the Proceed button
    var proceedButton = document.getElementById('proceedOrder');
    proceedButton.disabled = true;

    // Add event listener to the tendered amount input
    var tenderedAmountInput = document.getElementById('tenderedAmount');
    tenderedAmountInput.addEventListener('input', function() {
        // Get the entered tendered amount
        var tenderedAmount = parseFloat(tenderedAmountInput.value);
        
        // Get the grand total amount
        var grandTotal = parseFloat(document.getElementById("grandTotalValue").textContent);
        
        // Enable the Proceed button if a valid and sufficient amount is entered
        if (!isNaN(tenderedAmount) && tenderedAmount >= grandTotal) {
            proceedButton.disabled = false;
        } else {
            // Otherwise, disable the Proceed button
            proceedButton.disabled = true;
        }
    });

    // Add event listener to the Proceed button click
    proceedButton.addEventListener('click', function() {
        // Proceed with the order logic
        var tenderedAmount = parseFloat(document.getElementById("tenderedAmount").value);
        var change = tenderedAmount - parseFloat(document.getElementById("grandTotalValue").textContent);

        // Hide confirmation modal
        var confirmationModal = bootstrap.Modal.getInstance(document.getElementById('orderConfirmationModal'));
        confirmationModal.hide();

        // Show receipt modal
        var receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
        receiptModal.show();

        // Update tendered amount and change in the receipt modal
        document.getElementById("tenderedAmountReceipt").textContent = tenderedAmount.toFixed(2);
        document.getElementById("changeDisplayReceipt").textContent = change.toFixed(2);

        // Reset tendered amount input in the confirmation modal
        document.getElementById("tenderedAmount").value = "";
        // Reset change display in the confirmation modal
        document.getElementById("changeDisplay").innerHTML = "";
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('proceedOrder').addEventListener('click', function() {
        printOrderConfirmation();
        checkCartItems();
    });

    function printOrderConfirmation() {
        var printableContent = generatePrintableContent();
        clearOrdersAndSubtotal();
        
        var printWindow = window.open('', '_blank');
        printWindow.document.write(printableContent);
        printWindow.print();
    }

    function checkCartItems() {
        var orderTable = document.getElementById('orderCart');
        var rows = orderTable.getElementsByTagName('tr');
        var placeOrderBtn = document.getElementById('placeOrderButton');

        if (rows.length === 0) {
            placeOrderBtn.disabled = true;
        }
    }

    function clearOrdersAndSubtotal() {
        document.getElementById('orderCart').innerHTML = '';
        document.getElementById('subtotalValue').textContent = '0.00';
    }

    function generatePrintableContent() {
        var orderDetails = {
            items: [],
            subtotal: parseFloat(document.getElementById('subtotalValue').textContent),
            discount: parseFloat(document.getElementById('discountInput').value),
            grandTotal: parseFloat(document.getElementById('grandTotalValue').textContent)
        };

        var orderTable = document.getElementById('orderCart');
        var rows = orderTable.getElementsByTagName('tr');
        for (var i = 0; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName('td');
            var item = {
                productName: cells[1].querySelector('.text-capitalize').textContent.trim(),
                quantity: cells[0].querySelector('p').textContent.trim().slice(1),
                price: parseFloat(cells[2].querySelector('.text-carbon-grey').textContent.trim().replace(/[^\d.]/g, ''))
            };
            orderDetails.items.push(item);
        }

        var printableContent = `
            <html>
                <head>
                    <title>Receipt</title>
                </head>
                <body>
                    <div style="text-align: center;">
                        <div style="display: flex; align-items: center; justify-content: center;">
                            <img src="images/logo-removebg-preview.png" alt="Sweet Avenue Logo" style="max-width: 75px; margin-right: 10px;">
                            <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                <h4 style="margin: 0;"><strong>SWEET AVENUE</strong></h4>
                                <h5 style="margin: 0;"><strong>COFFEE • BAKESHOP</strong></h5>
                            </div>
                        </div>
                        <br>
                        <p class="roboto-mono"><b>${new Date().toLocaleString()}</b></p>
                        <hr>
                    </div>
                    <table class="table table-borderless text-center" style="margin-bottom: 20px;">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Product</th>
                                <th style="text-align: center;">Quantity</th>
                                <th style="text-align: center;">Price</th>
                            </tr>
                        </thead>
                        <tbody class="roboto-mono">
                            ${orderDetails.items.map(item => `
                                <tr>
                                    <td class="center-text">${capitalizeEachWord(item.productName)}</td>
                                    <td style="text-align: center;">${item.quantity}</td>
                                    <td style="text-align: center;">₱${item.price.toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    <hr>
                    <div class="roboto-mono">
                        <p><strong>Subtotal:</strong> ₱${orderDetails.subtotal.toFixed(2)}</p>
                        <p><strong>Discount:</strong> -${orderDetails.discount.toFixed(2)} %</p>
                        <p><strong>Grand Total:</strong> ₱${orderDetails.grandTotal.toFixed(2)}</p>
                    </div>
                    <hr>
                    <p class="roboto-mono"><strong>Processed by:</strong> <?php echo htmlspecialchars($current_user['first_name']); ?></p><br><br>'
                    <hr>
                    <div class="text-center roboto-mono">
                        <p>Thank you for your patronage. We’d love to see you again soon. You're always welcome here!</p>
                    </div>
                </body>
            </html>
        `;

        function capitalizeEachWord(string) {
            return string.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        return printableContent;
    }
});
</script>



</script>

<script>
    // Function to calculate the grand total
function calculateGrandTotal() {
    var subtotal = parseFloat(document.getElementById("subtotalValue").textContent); // Assuming subtotal is already calculated and displayed
    var discountPercentage = parseFloat(document.getElementById("discountInput").value);
    
    // Validate discount percentage
    if (isNaN(discountPercentage) || discountPercentage < 0 || discountPercentage > 100) {
        alert("Please enter a valid discount percentage (0-100)."); 
        document.getElementById("discountInput").value = 0; // Reset to 0 if invalid input
        discountPercentage = 0; // Set discount to 0 if invalid input
    }
    
    var discountAmount = (subtotal * discountPercentage) / 100;
    var grandTotal = subtotal - discountAmount;

    // Update grand total display
    document.getElementById("grandTotalValue").textContent = grandTotal.toFixed(2);
}

// Call calculateGrandTotal initially to set the grand total
calculateGrandTotal();

</script>

<script>
    //TENDERED
    // Function to enforce 10-digit input
    function enforceTenDigits(event) {
        var input = event.target.value;
        if (input.length > 10) {
            event.target.value = input.slice(0, 10);
        }
    }

    // Function to calculate change
    function calculateChange() {
        var grandTotal = parseFloat(document.getElementById("grandTotalValue").textContent); // Get grand total from the page
        var tenderedAmount = parseFloat(document.getElementById("tenderedAmount").value);
        if (isNaN(tenderedAmount)) {
            document.getElementById("changeDisplay").innerHTML = "<p style='color: red;'>Please enter a valid amount</p>";
            return;
        }
        var change = tenderedAmount - grandTotal;
        if (change >= 0) {
            document.getElementById("changeDisplay").innerHTML = "<p><strong>Change:</strong> " + change.toFixed(2) + "</p>";
        } else {
            document.getElementById("changeDisplay").innerHTML = "<p style='color: red;'>Insufficient amount tendered</p>";
        }
    }

    // Event listener for input change to enforce 10-digit input
    document.getElementById("tenderedAmount").addEventListener("input", enforceTenDigits);

    // Event listener for input change to calculate change
    document.getElementById("tenderedAmount").addEventListener("input", calculateChange);
</script>


<script>
// Function to remove an item
function removeItem(event) {
    event.preventDefault(); // Prevent the default action of the link
    
    const tr = this.closest('tr'); // Get the closest <tr> element
    tr.remove(); // Remove the <tr> element from the DOM

    // Update local storage to reflect the removed item
    updateLocalStorage();

    // Check if there are any items left in the billing section
    const itemsLeft = document.querySelectorAll('#billingTable tr').length > 0;

    // If there are no items left in the cart, update order confirmation modal and disable the Place Order button
    if (!itemsLeft) {
        document.getElementById('orderConfirmationModalContent').textContent = "No Items in Cart";
        document.getElementById('placeOrderButton').disabled = true;
    }
}

// Function to update local storage
function updateLocalStorage() {
    const items = document.querySelectorAll('#billingTable tr');
    const cartItems = [];
    items.forEach(item => {
        cartItems.push(item.innerHTML);
    });
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
}

// Get all remove-order links
const removeLinks = document.querySelectorAll('.remove-order');

// Loop through each link and add a click event listener
removeLinks.forEach(link => {
    link.addEventListener('click', removeItem);
});

// Check if cart was empty before page reload
window.addEventListener('load', () => {
    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    const billingTable = document.getElementById('billingTable');
    billingTable.innerHTML = cartItems.join('');

    const placeOrderButton = document.getElementById('placeOrderButton');
    if (cartItems.length === 0) {
        document.getElementById('orderConfirmationModalContent').textContent = "No Items in Cart";
        placeOrderButton.disabled = true;
    }
});
</script>

