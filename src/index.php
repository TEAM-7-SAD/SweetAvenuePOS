<?php

require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';

if(isset($_SESSION['id'])) {

  // Read the JSON file
  $json_file = 'sales-prediction-algorithm/sales_prediction.json';
  $json_data = file_get_contents($json_file);

  // Decode JSON into array
  $predictions = json_decode($json_data, true);

  $sales_sum = 0;

  // Check if decoding is scucessful
  if($predictions != NULL) {
    // Extract predictions
    $prediction_data = $predictions['predictions'];
    $sales_sum = $predictions['sales_sum'];
  }
  
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

    <?php
    // Navbar
    include 'includes/navbar.php';

    // Charts
    include 'includes/charts/weekly-sales.php';
    include 'includes/charts/weekly-top-sold-products.php';
    include 'includes/charts/weekly-top-sold-category.php';
    ?>

    <!-- Main Content -->
    <div class="col px-5 mb-5">
      <div class="col">
        <div class="row">
          <!-- First Quarter: Mini Containers -->
          <div class="col-md-6 px-4 mb-2 mt-5 mb-3">
            <div class="col-md-12 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                <!-- Larger Container 1 -->
                <div class="p-2 mb-4 fw-bold text-muted fs-6">
                  Weekly Sales
                </div>
                <div id="chartdiv"></div>                      
            </div>
          </div>
          <!-- Second Quarter: Larger Container -->
          <div class="col-md-6 mt-5 mb-5">
            <div class="row">
              <div class="col-md-6 px-4">
                <div class="col-md-12 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                  <!-- Mini Container 1 -->
                  <div class="text-center fw-medium py-2 text-medium-brown">
                    <span class="text-muted fw-bold">Today's Sales</span>
                    <div class="text-center py-4 d-flex justify-content-center fw-semibold" style="font-size: 31px;">940.00</div> 
                  </div>
                </div>
              </div>
              <div class="col-md-6 px-4">
                <div class="col-md-12 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                  <!-- Mini Container 2 -->
                  <div class="text-center fw-medium py-2 text-medium-brown">
                    <span class="text-muted fw-bold">Sales Prediction</span>
                    <div class="text-center py-4 d-flex justify-content-center fw-semibold" style="font-size: 31px;"><?php echo number_format($sales_sum, 2); ?></div> 
                  </div>
                </div>
              </div>  
            </div>
            <div class="row">
              <div class="col-md-6 px-4" style="height: 192px;">
                <div class="col-md-12 mt-4 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                  <!-- Mini Container 3 -->
                  <div class="text-center fw-medium py-2 text-medium-brown">
                    <span class="text-muted fw-bold">Weekly Sales</span>
                    <div class="text-center py-4 d-flex justify-content-center fw-semibold" style="font-size: 31px;"><?php echo number_format($weekly_sales, 2); ?></div> 
                  </div>
              </div>
              </div>
              <div class="col-md-6 px-4">
                <div class="col-md-12 mt-4 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                  <!-- Mini Container 4 -->
                  <div class="text-center fw-medium py-2 text-medium-brown">
                    <span class="text-muted fw-bold">Monthly Sales</span>
                    <div class="text-center py-4 d-flex justify-content-center fw-semibold" style="font-size: 31px;">14,340.00</div> 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
            <!-- Third Quarter: Larger Container -->
            <div class="col-md-6 px-4 mt-4"> <!-- Increased py-4 for more padding -->
                <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                    <!-- Larger Container 2 -->
                    <div class="p-2 mb-4 fw-bold text-muted fs-6">
                      Weekly Top Sold Products
                    </div>                              
                    <div id="chartdiv1"></div>  
                </div>
            </div>
            <!-- Fourth Quarter: Larger Container -->
            <div class="col-md-6 px-4 mt-4"> <!-- Increased py-4 for more padding -->
                <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                    <!-- Larger Container 3 -->
                    <div class="p-2 mb-4 fw-bold text-muted fs-6">
                      Weekly Top Sold Category
                    </div>
                      <div id="chartdiv2"></div>
                </div>
            </div>
        </div>
      </div>
    </div>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

  </body>
</html>

<?php 
  } else {
    header("location: login.php");
  } 
?>