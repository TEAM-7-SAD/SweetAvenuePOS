<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

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
    include FileUtils::normalizeFilePath('includes/navbar.php');
    include FileUtils::normalizeFilePath('includes/preloader.html');
    ?>

  <body>

    <!--Main Container-->
    <div class="container-fluid px-0 bg-timberwolf">

      <div class="container-fluid px-0">
        <div class="overflow-hidden flex-column">
          <div class="row overflow-y-auto" style="height: calc(100vh - 94px);">

                  <!--Main Content-->
              <div class="col">
                    <div class="container main-content mt-5 mb-4">
                    <h3 class="text-medium-brown fw-bolder text-capitalize">Reports</h3>
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

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="script_save_product.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script src="javascript/preloader.js"></script>

    <script>
 new DataTable('#example', {
    dom: "<'row'<'col-sm-1'l><'col-sm-9'><'col-sm-2'f>>" + // Place search box and buttons in separate columns
         "<'row'<'col-sm-12'tr>>" + // Place the table in a separate row
         "<'row'<'col-sm-3'i><'col-sm-2'><'col-sm-3'p><'col-sm-4'B>>", // Place information and pagination in separate columns
    buttons: {
        buttons: [
          {
                extend: 'copy',
                text: 'Copy',
                className: 'btn btn-tiger-orange' 
            },
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-tiger-orange' 
            },
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-tiger-orange' 
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-tiger-orange' 
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-tiger-orange' 
            }
        ],
        dom: {
            button: {
                tag: 'button',
                className: 'btn btn-secondary'
            }
        }
    }
});

    </script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
