<?php
require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';

if(isset($_SESSION['id'])) {

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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css" />   
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>
</head>

  <body class="bg-timberwolf">

    <!--Navbar-->
    <?php
    include 'includes/navbar.php';
    ?>

  <body>

    <!-- Main Container -->
    <div class="container-fluid px-5">

      <div class="overflow-hidden flex-column">
        <div class="row">

          <!-- Food Table -->
          <div class="col-md-12">
            <div class="container mt-5">
              <h3 class="text-medium-brown fw-bolder mb-4 text-capitalize">products</h3> 
              <div class="table container-lg bg-white table-borderless">      
                  <div class="text-carbon-grey pt-4 fs-5 fw-bold text-uppercase">foods</div><hr>
                      <div class="table-container table-transparent">
                          <table id="food" class="table table-transparent">
                              <thead>
                                  <tr>
                                      <th class="text-medium-brown">Name</th>
                                      <th class="text-medium-brown">Category</th>
                                      <th class="text-medium-brown">Serving</th>
                                      <th class="text-medium-brown">Flavor</th>
                                      <th class="text-medium-brown">Price</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php
                                  $query = "SELECT 
                                              fi.name AS Name,
                                              fc.name AS Category,
                                              IFNULL(fv.serving, 'None') AS Serving,
                                              IFNULL(fv.flavor, 'None') AS Flavor,
                                              IFNULL(fv.price, 'None') AS Price
                                          FROM 
                                              food_item fi
                                          INNER JOIN 
                                              food_category fc ON fi.category_id = fc.id
                                          LEFT JOIN 
                                              food_variation fv ON fi.id = fv.food_id";
                                  $result = $db->query($query);
                                  while($row = $result->fetch_assoc()) {
                                      echo "<tr class='text-capitalize text-truncate fw-semibold'>";
                                      echo "<td class='text-carbon-grey'>".$row['Name']."</td>";
                                      echo "<td class='text-carbon-grey'>".$row['Category']."</td>";
                                      echo "<td class='text-carbon-grey'><span class='badge text-bg-medium-brown rounded-4 text-wrap'>".$row['Serving']."</td></span>";
                                      echo "<td class='text-carbon-grey'><span class='badge text-bg-carbon-grey rounded-4 text-wrap'>".$row['Flavor']."</td></span>";
                                      echo "<td class='text-carbon-grey'>".$row['Price']."</td>";
                                      echo "</tr>";
                                  }
                                  ?>
                              </tbody>
                              <tfoot>
                              </tfoot>
                          </table>
                      </div>
                      <br>
                  </div>
              </div>
          </div>

          <!-- Drink Table -->
          <div class="col-md-12">
            <div class="container mt-5">
              <div class="table container-lg bg-white table-borderless">      
                  <div class="text-carbon-grey pt-4 fs-5 fw-bold text-uppercase">drinks</div><hr>
                      <div class="table-container table-transparent">
                          <table id="drink" class="table table-transparent">
                              <thead>
                                  <tr>
                                      <th class="text-medium-brown">Name</th>
                                      <th class="text-medium-brown">Category</th>
                                      <th class="text-medium-brown">Type</th>
                                      <th class="text-medium-brown">Size</th>
                                      <th class="text-medium-brown">Price</th>
                                  </tr>
                              </thead>
                              <tbody>
                              <?php
                              $query = "SELECT 
                                          di.name AS Name,
                                          dc.name AS Category,
                                          IFNULL(dv.type, 'None') AS Type,
                                          dv.size AS Size,
                                          IFNULL(dv.price, 'None') AS Price
                                        FROM 
                                          drink_item di
                                        INNER JOIN 
                                          drink_category dc ON di.category_id = dc.id
                                        LEFT JOIN 
                                          drink_variation dv ON di.id = dv.drink_id";
                              $result = $db->query($query);
                              while($row = $result->fetch_assoc()) {
                                  echo "<tr class='text-capitalize text-truncate fw-semibold'>";
                                  echo "<td class='text-carbon-grey'>".$row['Name']."</td>";
                                  echo "<td class='text-carbon-grey'>".$row['Category']."</td>";
                                  echo "<td class='text-carbon-grey'><span class='badge text-bg-medium-brown rounded-4 text-wrap'>" .$row['Type']."</td></span>";
                                  echo "<td class='text-carbon-grey'><span class='badge text-bg-carbon-grey rounded-4 text-wrap'>" .$row['Size']. 'oz' . "</td></span>";
                                  echo "<td class='text-carbon-grey'>".$row['Price']."</td>";
                                  echo "</tr>";
                              }
                              ?>
                              </tbody>
                              <tfoot>
                              </tfoot>
                          </table>
                      </div>
                      <br>
                  </div>
              </div>
          </div>
        </div>  
      </div>
  </div>

  
  <!-- Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">Success</h5>
        </div>
        <div class="modal-body">
          Sale successfully deleted.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

        <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel ">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="errorContainer" class="alert alert-danger" style="display: none;" role="alert">
                    <div>
                      <img src="images/x-circle.svg">
                       An error occured.
                    </div>
                  </div>                  
                    <form>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" accept=".jpg, .jpeg, .png, .svg" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" required>
                            <option value="">Select Category</option>
                            <option value="Snack and Rice Meals">Snack and Rice Meals </option>
                            <option value="Bread, Cakes, and Pastries">Bread, Cakes, and Pastries</option>
                            <option value="Coffee and Blended/Hot">Coffee and Blended/Hot</option>
                            <option value="Coffee and Blended/Iced">Coffee and Blended/Iced</option>
                            <option value="Froyo">Froyo</option>
                        </select>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="price" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveChangesBtn" class="btn btn-tiger-orange text-white" onclick="addNewProduct()">Add</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                    <form id="editProductForm">
                        <div class="mb-3">
                            <label for="editImage" class="form-label">Image</label>
                            <input type="file" class="form-control" id="editImage" accept=".jpg, .jpeg, .png, .svg">
                            <img id="editImagePreview" src="" alt="Preview Image" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Category</label>
                            <select class="form-select" id="editCategory" required>
                                <option value="Snack and Rice Meals">Snack and Rice Meals </option>
                                <option value="Bread, Cakes, and Pastries">Bread, Cakes, and Pastries</option>
                                <option value="Coffee and Blended/Hot">Coffee and Blended/Hot</option>
                                <option value="Coffee and Blended/Iced">Coffee and Blended/Iced</option>
                                <option value="Froyo">Froyo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="text" class="form-control" id="editPrice" required>
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
        <div class="modal-dialog">
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
        <div class="modal-dialog">
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
    <script>
      $(document).ready(function () {
          $('#food').DataTable();
          $('#drink').DataTable();
      });
  </script>
</script>

    </script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
