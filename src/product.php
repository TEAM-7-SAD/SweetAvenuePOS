<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

if(isset($_SESSION['id'])) {
    // Function to fetch categories from the database
    function getCategories($db, $table) {
        $sql = "SELECT name FROM $table";
        $result = $db->query($sql);
        $categories = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categories[] = $row['name'];
            }
        }
        return $categories;
    }

    $drinkCategories = getCategories($db, 'drink_category');
    $foodCategories = getCategories($db, 'food_category');
    $allCategories = array_merge($drinkCategories, $foodCategories);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--Site Meta Information-->
    <meta charset="UTF-8" />
    <title>Sweet Avenue POS</title>
    <!--Mobile Specific Metas-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />   
    <!--CSS-->
    <link rel="stylesheet" href="styles/main.css" />   
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>
</head>

  <body class="bg-gainsboro">

    <!--Navbar-->
    <?php
    include FileUtils::normalizeFilePath('includes/navbar.php');
    include FileUtils::normalizeFilePath('includes/preloader.html');
    ?>

  <body>

    <!-- Main Container -->
    <div class="container mb-5">

        <!-- Food Table -->
        <div class="col-lg-12">
            <div class="main-content mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-medium-brown fw-bolder text-capitalize mb-0">Products</h3>
                    <div>
                        <button type="button" class="btn btn-sm py-2 px-3 btn-medium-brown text-white fw-semibold me-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                        <button type="button" class="btn btn-sm py-2 px-3 btn-medium-brown text-white fw-semibold me-2" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
                        <button type="button" class="btn btn-sm py-2 px-3 btn-carbon-grey fw-semibold me-2" id="editButton" data-bs-toggle="modal" data-bs-target="#editProductModal" disabled>Edit</button>
                        <button type="button" class="btn btn-sm py-2 px-3 btn-danger fw-semibold me-2 delete-product" disabled>Delete</button>
                    </div>
                </div>

                <div class="bg-medium-brown px-0 py-2 d-flex justify-content-between align-items-center rounded-top">
                    <div class="text-light fs-5 fw-semibold ps-5">Foods</div><hr>   
                </div>

                <div class="container bg-white shadow px-5 py-4 rounded-bottom"> 
                    <table id="food" class="table table-hover table-striped table-borderless mt-4">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-medium-brown fw-semibold font-15">Image</th>
                                <th class="text-medium-brown fw-semibold font-15">Name</th>
                                <th class="text-medium-brown fw-semibold font-15">Category</th>
                                <th class="text-medium-brown fw-semibold font-15">Serving</th>
                                <th class="text-medium-brown fw-semibold font-15">Flavor</th>
                                <th class="text-medium-brown fw-semibold font-15">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT
                                        fi.id, 
                                        fi.name AS Name,
                                        fi.image AS Image, 
                                        fc.name AS Category,
                                        IFNULL(fv.serving, 'None') AS Serving,
                                        IFNULL(fv.flavor, 'None') AS Flavor,
                                        IFNULL(fv.price, 'None') AS Price,
                                        fv.id AS variation_id
                                    FROM 
                                        food_item fi
                                    INNER JOIN 
                                        food_category fc ON fi.category_id = fc.id
                                    LEFT JOIN 
                                        food_variation fv ON fi.id = fv.food_id";
                            $result = $db->query($query);
                            while($row = $result->fetch_assoc()) {
                                echo "<tr data-id='".$row['id']."' data-variation-id='".$row['variation_id']."' class='text-capitalize text-truncate fw-semibold'>";
                                echo "<td class='text-carbon-grey fw-medium font-14'><input type='checkbox' class='product-checkbox' data-product-id='" . $row['id'] . "' data-variation-id='" . $row['variation_id'] . "'></td>";
                                echo "<td class='text-carbon-grey fw-medium font-14'><img src='" . $row['Image'] . "' alt='Product Image' style='max-width: 50px; max-height: 50px; border-radius: 50%;'></td>";
                                echo "<td class='text-carbon-grey fw-medium font-14'>".$row['Name']."</td>";
                                echo "<td class='text-carbon-grey fw-medium font-14'>".$row['Category']."</td>";
                                echo "<td class='text-carbon-grey fw-medium font-14'><span class='badge text-bg-medium-brown text-wrap'>".$row['Serving']."</td></span>";
                                echo "<td class='text-carbon-grey fw-medium font-14'><span class='badge text-bg-carbon-grey text-wrap'>".$row['Flavor']."</td></span>";
                                echo "<td class='text-carbon-grey fw-medium font-14'>".$row['Price']."</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Drink Table -->
        <div class="col-lg-12 mt-5">
            <div class="bg-medium-brown px-0 py-2 d-flex justify-content-between align-items-center rounded-top">      
                    <div class="text-light fs-5 fw-semibold ps-5">Drinks</div>
                </div>
            <div class="container bg-white shadow px-5 py-4 rounded-bottom">
                <table id="drink" class="table table-hover table-striped table-borderless mt-4">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="text-medium-brown fw-semibold">Image</th>
                            <th class="text-medium-brown fw-semibold">Name</th>
                            <th class="text-medium-brown fw-semibold">Category</th>
                            <th class="text-medium-brown fw-semibold">Type</th>
                            <th class="text-medium-brown fw-semibold">Size</th>
                            <th class="text-medium-brown fw-semibold">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $query = "SELECT
                                di.id,  
                                di.name AS Name,
                                di.image AS Image, 
                                dc.name AS Category,
                                IFNULL(dv.type, 'None') AS Type,
                                dv.size AS Size,
                                IFNULL(dv.price, 'None') AS Price,
                                dv.id AS variation_id
                            FROM 
                                drink_item di
                            INNER JOIN 
                                drink_category dc ON di.category_id = dc.id
                            LEFT JOIN 
                                drink_variation dv ON di.id = dv.drink_id";
                    $result = $db->query($query);
                    while($row = $result->fetch_assoc()) {
                        echo "<tr data-id='".$row['id']."' data-variation-id='".$row['variation_id']."' class='text-capitalize text-truncate fw-semibold'>";
                        echo "<td class='text-carbon-grey fw-medium font-14'><input type='checkbox' class='product-checkbox' data-product-id='" . $row['id'] . "' data-variation-id='" . $row['variation_id'] . "'></td>";
                        echo "<td class='text-carbon-grey fw-medium font-14'><img src='" . $row['Image'] . "' alt='Product Image' style='max-width: 50px; max-height: 50px; border-radius: 50%;'></td>";
                        echo "<td class='text-carbon-grey fw-medium font-14'>".$row['Name']."</td>";
                        echo "<td class='text-carbon-grey fw-medium font-14'>".$row['Category']."</td>";
                        echo "<td class='text-carbon-grey fw-medium font-14'><span class='badge text-bg-medium-brown text-wrap'>" .$row['Type']."</td></span>";
                        echo "<td class='text-carbon-grey fw-medium font-14'><span class='badge text-bg-carbon-grey text-wrap'>" .$row['Size']. 'oz' . "</td></span>";
                        echo "<td class='text-carbon-grey fw-medium font-14'>".$row['Price']."</td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

  
    <!-- Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmation</h5>
            </div>
            <div class="modal-body">
            Are you sure you want to delete this product permanently?
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-tiger-orange text-white" onclick="deleteSelectedRows()">Continue</button>
            </div>
        </div>
        </div>
    </div>
  
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="successModalLabel">Success</h5>
        </div>
        <div class="modal-body">
            Product successfully deleted.
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="errorContainer" class="alert alert-danger" style="display: none;" role="alert">
                        <div>
                            <img src="images/x-circle.svg"> An error occurred.
                        </div>
                    </div>
                    <form id="addProductForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept=".jpg, .jpeg, .png, .svg" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php
                                    foreach ($allCategories as $category) {
                                        echo "<option value=\"" . htmlspecialchars($category) . "\">" . htmlspecialchars($category) . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="servingType" class="form-label">Serving/Type</label>
                            <input type="text" class="form-control" id="servingType" name="servingType">
                        </div>
                        <div class="mb-3">
                            <label for="flavorSize" class="form-label">Flavor/Size</label>
                            <input type="text" class="form-control" id="flavorSize" name="flavorSize">
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required>
                            <div class="invalid-feedback">
                                Price must be greater than zero.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveChangesBtn" class="btn btn-tiger-orange text-white" onclick="addNewProduct()">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <div id="errorContainerCategory" class="alert alert-danger" style="display: none;" role="alert">
                    <div>
                    <img src="images/x-circle.svg">
                    An error occurred.
                    </div>
                </div>                  
                    <form>
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoryType" class="form-label">Category Type</label>
                            <select class="form-select" id="categoryType" required>
                                <option value="food">Food</option>
                                <option value="drink">Drink</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveCategoryBtn" class="btn btn-tiger-orange text-white" onclick="addNewCategory()">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editErrorContainer" class="alert alert-danger" style="display: none;" role="alert">
                        <div>
                            <img src="images/x-circle.svg">
                            An error occurred.
                        </div>
                    </div>
                    <form id="editProductForm" enctype="multipart/form-data">
                        <input type="hidden" id="productId" name="productId" value="">
                        <input type="hidden" id="variationId" name="variationId" value="">
                        <input type="hidden" id="productType" name="productType" value=""> 
                        <div class="mb-3">
                            <label for="editImage" class="form-label">Image</label>
                            <input type="file" class="form-control" id="editImage" name="editImage" accept=".jpg, .jpeg, .png, .svg">
                            <img id="editImagePreview" src="" alt="Preview Image" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="editName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Category</label>
                            <select class="form-select" id="editCategory" name="editCategory" required>
                                <option value="">Select Category</option>
                                <?php
                                    foreach ($allCategories as $category) {
                                        echo "<option value=\"" . htmlspecialchars($category) . "\">" . htmlspecialchars($category) . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editServingType" class="form-label">Serving/Type</label>
                            <input type="text" class="form-control" id="editServingType" name="editServingType">
                        </div>
                        <div class="mb-3">
                            <label for="editFlavorSize" class="form-label">Flavor/Size</label>
                            <input type="text" class="form-control" id="editFlavorSize" name="editFlavorSize">
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="text" class="form-control" id="editPrice" name="editPrice" required>
                            <span id="priceError" class="text-danger" style="display:none;">Price must be greater than 0.</span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveChangesEditBtn" class="btn btn-tiger-orange text-white" onclick="saveChangesEdit()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal for Edit -->
    <div class="modal fade" id="editSuccessModal" tabindex="-1" aria-labelledby="editSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSuccessModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    Product successfully edited.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- No Product Selected Modal -->
    <div class="modal fade" id="noProductSelectedModal" tabindex="-1" aria-labelledby="noProductSelectedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noProductSelectedModalLabel">No Product Selected</h5>
                </div>
                <div class="modal-body">
                    No product has been selected. Please select a product to edit.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="Select_Deselect.js"></script>
    <script src="javascript/preloader.js"></script>
    <script>
      $(document).ready(function () {
          $('#food').DataTable();
          $('#drink').DataTable();
      });
  </script>
<script>
function addNewCategory() {
    var categoryName = document.getElementById('categoryName').value;
    var categoryType = document.getElementById('categoryType').value;

    if (categoryName && categoryType) {
        $.ajax({
            type: 'POST',
            url: 'add_category.php',
            data: {
                categoryName: categoryName,
                categoryType: categoryType
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#addCategoryModal').modal('hide');
                    location.reload();
                    // Append the new category to the dropdown
                    var newCategory = $("<option>").val(categoryName).text(categoryName);
                    $("#category").append(newCategory);
                    $("#editCategory").append(newCategory);
                    alert('Category added successfully');
                } else {
                    $('#errorContainerCategory').show().text(res.message);
                }
            },
            error: function() {
                $('#errorContainerCategory').show().text('An error occurred while adding the category');
            }
        });
    } else {
        $('#errorContainerCategory').show().text('Please fill out all fields');
    }
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('price').addEventListener('input', function () {
        validatePrice();
    });
});

function validatePrice() {
    var priceInput = document.getElementById('price');
    var price = parseFloat(priceInput.value);
    if (price <= 0 || isNaN(price)) {
        priceInput.classList.add('is-invalid');
        return false;
    } else {
        priceInput.classList.remove('is-invalid');
        return true;
    }
}

function addNewProduct() {
    if (!validatePrice()) {
        return;
    }

    var formData = new FormData(document.getElementById('addProductForm'));

    $.ajax({
        type: 'POST',
        url: 'add_product.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            var res = JSON.parse(response);
            if (res.success) {
                $('#addProductModal').modal('hide');
                alert('Product added successfully');
                location.reload(); // Reload the page to see the new product
            } else {
                document.getElementById('errorMessage').innerText = res.message;
                document.getElementById('errorContainer').style.display = 'block';
            }
        },
        error: function() {
            document.getElementById('errorMessage').innerText = 'An error occurred while adding the product';
            document.getElementById('errorContainer').style.display = 'block';
        }
    });
}
</script>
<script>
$(document).ready(function () {
    var selectedProduct = null;

    // Initialize DataTables
    $('#food').DataTable();
    $('#drink').DataTable();

    // Capture product data when a row is clicked
    $('body').on('click', '.product-checkbox', function() {
        var productId = $(this).closest('tr').data('id'); // Fetch the product ID from the data attribute of the closest <tr> element
        var variationId = $(this).closest('tr').data('variation-id'); // Fetch the variation ID from the data attribute of the closest <tr> element
        var row = $(this).closest('tr');
        selectedProduct = {
            id: productId,
            variationId: variationId,
            image: row.find('img').attr('src'),
            name: row.find('td:eq(2)').text().trim(),
            category: row.find('td:eq(3)').text().trim(),
            servingType: row.find('td:eq(4) span').text().trim(),
            flavorSize: row.find('td:eq(5) span').text().trim(),
            price: row.find('td:eq(6)').text().trim()
        };
    });

    // Populate the edit modal with the selected product data
    $('#editProductModal').on('show.bs.modal', function () {
        if (!selectedProduct) {
            $('#noProductSelectedModal').modal('show');
            return false; // prevent the modal from showing
        }

        $('#editImagePreview').attr('src', selectedProduct.image);
        $('#editName').val(selectedProduct.name);
        $('#editCategory').val(selectedProduct.category);
        $('#editServingType').val(selectedProduct.servingType);
        $('#editFlavorSize').val(selectedProduct.flavorSize);
        $('#editPrice').val(selectedProduct.price);
        $('#productId').val(selectedProduct.id); 
        $('#variationId').val(selectedProduct.variationId); // Set variationId in the hidden input field

        // Clear previous error messages
        $('#editErrorContainer').hide().text('');
        $('#priceError').hide();
    });

    // Handle save changes for editing product
    $('#saveChangesEditBtn').on('click', function() {
        saveChangesEdit();
    });
});

function saveChangesEdit() {
    let priceInput = document.getElementById('editPrice').value;
    let priceError = document.getElementById('priceError');
    
    if (priceInput <= 0) {
        priceError.style.display = 'block';
        return;
    } else {
        priceError.style.display = 'none';
    }

    let formData = new FormData(document.getElementById('editProductForm'));

    fetch('edit_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide the edit product modal
            let editProductModal = document.getElementById('editProductModal');
            let editModalInstance = bootstrap.Modal.getInstance(editProductModal);
            editModalInstance.hide();

            // Show the success modal
            let editSuccessModal = new bootstrap.Modal(document.getElementById('editSuccessModal'));
            editSuccessModal.show();

            // Reload the page when the success modal is hidden
            document.getElementById('editSuccessModal').addEventListener('hidden.bs.modal', function () {
                location.reload();
            });
        } else {
            // Show error message
            let errorContainer = document.getElementById('editErrorContainer');
            errorContainer.style.display = 'block';
            errorContainer.innerHTML = `<div><img src="images/x-circle.svg"> ${data.message}</div>`;
        }
    })
    .catch(error => {
        // Handle unexpected error
        let errorContainer = document.getElementById('editErrorContainer');
        errorContainer.style.display = 'block';
        errorContainer.innerHTML = `<div><img src="images/x-circle.svg"> An unexpected error occurred.</div>`;
    });
}
</script>
<script>
    $(document).ready(function () {
        var selectedProducts = []; // Array to store selected product IDs
        
        // Function to update selected products array and toggle edit button state
        function updateEditButtonState() {
            var selectedCount = selectedProducts.length;
            if (selectedCount === 0) {
                $('#editButton').prop('disabled', true); // Disable edit button if no products selected
            } else if (selectedCount === 1) {
                $('#editButton').prop('disabled', false); // Enable edit button if exactly one product selected
            } else {
                $('#editButton').prop('disabled', true); // Disable edit button if more than one product selected
            }
        }
        
        // Handle click events on product checkboxes
        $('body').on('click', '.product-checkbox', function() {
            var productId = $(this).closest('tr').data('id'); // Fetch the product ID from the data attribute of the closest <tr> element
            var productType = $(this).closest('table').attr('id'); // Fetch the product type (food/drink) from the table ID
            
            // Check if the product is already selected
            var index = selectedProducts.findIndex(function(product) {
                return product.id === productId && product.type === productType;
            });
            
            // If not already selected, add to the selected products array; otherwise, remove it
            if (index === -1) {
                selectedProducts.push({ id: productId, type: productType });
            } else {
                selectedProducts.splice(index, 1);
            }
            
            // Update edit button state
            updateEditButtonState();
        });
        
        // Handle click event on the Edit button
        $('#editButton').click(function() {
            // Check the number of selected products
            if (selectedProducts.length === 1) {
                // Proceed with edit action for the first selected product
                var selectedProduct = selectedProducts[0];
                
                // Example: Populate modal fields with selected product details
                $('#editProductId').val(selectedProduct.id);
                $('#editProductType').val(selectedProduct.type);
                // Update other modal fields as needed based on your modal structure
                
                // Show the edit modal
                $('#editProductModal').modal('show');
                
            } else if (selectedProducts.length > 1) {
                // Handle case where multiple products are selected
                // You might want to implement a selection mechanism or choose the first product for editing
                alert('Please select exactly one product to edit.');
                
            } else {
                // No product selected case (though ideally, edit button should be disabled)
                alert('Please select a product to edit.');
            }
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButton = document.querySelector('.delete-product');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    function updateDeleteButtonState() {
        const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
        deleteButton.disabled = selectedProducts.length === 0;
    }

    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButtonState);
    });

    // Initial check to set the state of the delete button
    updateDeleteButtonState();

    deleteButton.addEventListener('click', function () {
        const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
        if (selectedProducts.length > 0) {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            deleteModal.show();
        } else {
            alert('Please select at least one product to delete.');
        }
    });

    document.getElementById('deleteConfirmationModal').querySelector('.btn-tiger-orange').addEventListener('click', function () {
        const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
        const ids = Array.from(selectedProducts).map(checkbox => checkbox.getAttribute('data-product-id'));
        const variationIds = Array.from(selectedProducts).map(checkbox => checkbox.getAttribute('data-variation-id'));

        fetch('delete_products.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: ids, variationIds: variationIds }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
                deleteModal.hide();
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                successModal._element.addEventListener('hidden.bs.modal', function () {
                    location.reload();
                });
            } else {
                alert('Failed to delete the selected products. Please try again.');
                console.error('Server Error:', data.error);
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
        });
    });
});
</script>


  </body>
</html>
<?php 
  } else {
    header("location: login");
  } 
?>
