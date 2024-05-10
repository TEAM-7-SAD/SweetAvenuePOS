<?php
require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';

if(isset($_SESSION['id'])) {

?>

<!DOCTYPE html>
<html lang="en">

  <!--Head elements-->
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

  <style>
    .image-cell img {
    max-width: 50px; /* Set maximum width for the image */
    max-height: 50px; /* Set maximum height for the image */
    width: auto; /* Allow the image to resize proportionally */
    height: auto; /* Allow the image to resize proportionally */
}
  </style>
  <body class="bg-timberwolf">

    <!--Navbar-->
    <?php
    include 'includes/navbar.php';
    ?>

  <body>

    <!--Main Container-->
    <div class="container-fluid px-0 bg-timberwolf">

      <div class="container-fluid px-0">
        <div class="overflow-hidden flex-column">
          <div class="row overflow-y-auto" style="height: calc(100vh - 94px);">

                  <!--Main Content-->
              <div class="col">
                    <div class="container main-content">
                    <h3 class="accounts text-tiger-orange bg-rose-white rounded-3 fw-semibold">Reports</h3>
                    <button class="add btn btn-tiger-orange text-white" data-bs-toggle="modal" data-bs-target="#addProductModal">Generate Report</button>
                    </div>
                    <div class="table container-lg bg-white">
                      <div class="container">
                        <div class="row justify-content-end">
                          <div class="p-3 mt-2 fw-bold text-center" style="color: #5B5B5B; font-size: 20px; border-bottom: 2px solid #5B5B5B;">
                            Weekly Report
                        </div>
                        </div>
                      </div>
                      <div class="table-container">
                      <table id="example" class="table">
                          <thead>
                              <tr>
                                  <th>Item Sets</th>
                                  <th>Support</th>
                                  <th>Confidence</th>
                                  <th>Lift</th>
                                  <th>Conviction</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td>{Caramel Macchiato} -> {Fries Overload}</td>
                                  <td>0.365</td>
                                  <td>0.054</td>
                                  <td>2.698</td>
                                  <td>1.325</td>
                              </tr>
                              <tr>
                                  <td>{Spanish Latte} -> {Classic Ensaymada}</td>
                                  <td>0.598</td>
                                  <td>0.047</td>
                                  <td>2.324</td>
                                  <td>1.254</td>
                              </tr>
                              <tr>
                                  <td>{Tapsilog} -> {White Chocolate}</td>
                                  <td>0.598</td>
                                  <td>0.054</td>
                                  <td>2.135</td>
                                  <td>1.123</td>
                              </tr>
                              <tr>
                                  <td>{Chicken Wings} -> {Caramel Macchiato}</td>
                                  <td>0.478</td>
                                  <td>0.047</td>
                                  <td>1.654</td>
                                  <td>1.025</td>
                              </tr>
                              <tr>
                                <td>{White Chocolate} -> {Spamsilog}</td>
                                <td>0.156</td>
                                <td>0.032</td>
                                <td>1.958</td>
                                <td>0.985</td>
                              </tr>
                              <tr>
                                <td>{Cheesecake} -> {Strawberry}</td>
                                <td>0.975</td>
                                <td>0.021</td>
                                <td>2.136</td>
                                <td>0.865</td>
                              </tr>
                              <tr>
                                <td>{Classic Burger} -> {Caramel Macchiato}</td>
                                <td>0.642</td>
                                <td>0.045</td>
                                <td>1.548</td>
                                <td>0.875</td>
                              </tr>
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
    </div>

        <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel ">Generate Report</h5>
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
                        <label for="startDate" class="form-label">Start Date:</label>
                        <input type="date" class="form-control" id="startDate" name="startDate">
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date:</label>
                        <input type="date" class="form-control" id="endDate" name="endDate">
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveChangesBtn" class="btn btn-tiger-orange text-white" onclick="">Download</button>
                </div>
            </div>
        </div>
    </div>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="script_save_product.js"></script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
