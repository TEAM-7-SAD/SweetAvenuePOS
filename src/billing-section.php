<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/error-reporting.php');
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
    button.text-medium-brown {
    border: none; /* Remove the border */
    background: none; /* Remove the background color */
    padding: 0; /* Remove padding */
    }

    button.text-medium-brown svg {
        vertical-align: middle; /* Align the SVG icon vertically */
    }
</style>

<script>
    const currentUser = <?php echo json_encode(htmlspecialchars($_SESSION['full_name'])); ?>;
</script>

<!-- Billing Section -->
<div class="col-lg-5 ps-5 mb-4">
    <div class="row bg-medium-brown rounded-top">
        <div class="col-lg-12">
            <div class="text-light d-flex justify-content-center py-3">
                <div class="pe-2 d-flex align-content-start flex-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart-check-fill" viewBox="0 0 16 16">
                        <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708"/>
                    </svg>                    
                </div>      
                <div class="fw-medium d-flex align-content-end flex-wrap">Order Cart</div>
            </div>
        </div>
    </div>

    <div class="row bg-white shadow-sm border border-top-0 border-secondary-subtle rounded-bottom">
        <div class="col mx-4 mt-3 mb-4">
            <form id="billingForm" method="post">         
                <table class="table table-sm table-borderless">
                    <thead>
                        <tr class="font-14 fw-semibold">
                        <td class="text-carbon-grey">Qty</td>
                        <td class="text-carbon-grey ps-0">Product</td>
                        <td class="text-end pe-4 text-carbon-grey">Price</td>
                        </tr>
                    </thead>
                </table>

                <div style="max-height: 360px; overflow-y: auto;">
                    <table class="table table-hover table-striped table-transparent table-borderless table-sm">
                        <tbody id="orderCart">
                            <!--Lists of Clicked Products-->
                            <?php include FileUtils::normalizeFilePath('display-clicked-items.php'); ?>
                        </tbody>
                    </table> 
                </div>
                <table class="table table-sm table-transparent table-borderless"><hr> 
                    <!-- Subtotal -->
                    <tr>
                        <td class="text-carbon-grey fw-medium">Subtotal:</td>
                        <td class="text-carbon-grey fw-bold text-end pe-2" id="subtotalValue"><?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                </table>      
                <!--Cancel and Place Order Buttons-->
                <div class="vstack gap-2 col-md-12 mx-auto pt-2 mb-3">
                    <button id="placeOrderButton" type="button" class="btn btn-medium-brown fs-6 fw-semibold py-2" disabled>Place Order</button>
                    <button type="button" class="btn btn-outline-carbon-grey fs-6 fw-semibold py-2" id="cancelOrder">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Order Confirmation Modal -->
<div class="modal fade" id="orderConfirmationModal" tabindex="-1" aria-labelledby="orderConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderConfirmationLabel">Confirm The Order</h5>
            </div>
            <div class="modal-body">
                <p>Review the order:</p>
                <!-- Order details go here -->
                <div style="max-height: 200px; overflow-y: auto;">
                    <form method="post">
                        <table class="table table-hover table-striped table-borderless table-transparent table-sm" role="button">
                                <tbody data-bs-toggle="modal" data-bs-target="#cartProduct" id="orderReview">
                                    <!-- Populate the item -->
                                </tbody> 
                            </table>  
                        </div>
                        <hr>
                        <table class="table table-sm table-transparent table-borderless mt-5">
                            <!-- Subtotal -->
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-carbon-grey fw-bold text-end pe-2" id="orderSubtotalReview"></td>
                            </tr>
                            <!--Discount
                            <tr>
                                <td>Discount (%):</td>
                                <td><input class="form-control text-carbon-grey shadow-sm" type="number" id="discountInput" min="0" max="100" step="0.01" placeholder="Enter discount" oninput="calculateGrandTotal()"></td>
                            </tr>-->
                            <!--Grand Total-->
                            <tr class="fw-bolder h5 text-capitalize">
                                <td class="text-carbon-grey">Grand Total:</td>
                                <td class="text-carbon-grey text-end" id="grandTotalValue"></td>
                            </tr>
                            <tr>
                                <div class="mb-3" style="display: flex; align-items: center; gap: 10px;">
                                    <td>
                                    <label for="tenderedAmount" class="form-label pe-5"><strong>Tendered:<span style="color: red;"> *</span></strong></label>
                                    </td>
                                    <td>
                                    <input type="number" class="form-control shadow-sm" id="tenderedAmount" placeholder="Enter amount tendered" style="max-width: 280px;">
                                    </td>
                                </div>
                            </tr>
                        </table>
                        <!-- Tendered amount input -->

                        <!-- Change calculation -->
                         <td>
                         <div id="changeDisplay">
                            <!-- Change amount will be displayed here -->
                        </div>
                         </td>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                        <button type="button" class="btn btn-tiger-orange fs-6 fw-semibold py-2" id="proceedOrder">Proceed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>