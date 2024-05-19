<?php

require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';
require_once 'includes/execute-prediction-script.php';
include_once 'includes/default-timezone.php';

if(!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit();
}

// Get the first name of the logged-in user
$sql = "SELECT first_name FROM user WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$user = $row['first_name'];
$stmt->close();

// Get total sales for today
$today = date("Y-m-d");
$sql = "SELECT SUM(total_amount) AS today_sale FROM transaction WHERE DATE(timestamp) = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('s', $today);
$stmt->execute();
$stmt->bind_result($today_sale);
$stmt->fetch();
$stmt->close();

$sql = "SELECT COUNT(timestamp) AS today_order FROM transaction WHERE DATE(timestamp) = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('s', $today);
$stmt->execute();
$stmt->bind_result($order_today);
$stmt->fetch();
$stmt->close();

$sql = "SELECT SUM(total_amount) AS monthly_sale 
        FROM transaction 
        WHERE YEAR(timestamp) = YEAR(CURRENT_DATE()) 
        AND MONTH(timestamp) = MONTH(CURRENT_DATE()) 
        AND DAY(timestamp) <= DAY(CURRENT_DATE())";
$stmt = $db->prepare($sql);
$stmt->execute();
$stmt->bind_result($monthly_sale);
$stmt->fetch();
$stmt->close();

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

      <style>
        .card-gradient {
          background: linear-gradient(to top right, #88531E, #88531E, #88531E, #C57C47);
        }
      </style>
  </head>

  <body class="bg-timberwolf">

    <?php
    // Navbar
    include 'includes/navbar.php';

    // Charts
    include 'includes/charts/weekly-sales.php';
    include 'includes/charts/predicted-weekly-sales.php';
    include 'includes/charts/weekly-top-sold-products.php';
    include 'includes/charts/weekly-top-sold-category.php';
    ?>

    <!-- Main Content -->
    <div class="col px-5 mb-5">
      <div class="col">
        <div class="row">
          <!-- First Quarter: Mini Containers -->
          <div class="col-md-6 mt-5">
            <div class="col-md-12 bg-rose-white rounded-3 mb-4 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 150px;"> <!-- Added box-shadow style for drop shadow -->
                <!-- Larger Container 1 -->
                <div class="row">
                  <div class="col-md-auto">
                    <div class="mx-3">
                      <div class="fw-semibold text-muted fs-3 pb-1 pt-2">
                        Hello there, <span class="text-tiger-orange fw-bold"><?php echo $user . '!'; ?></span>
                      </div>
                      <div class="text-muted fw-semibold">Here's what's happening with the store.</div>
                    </div>                    
                  </div>
                  <div class="col-auto">
                    <img src="images/waving-hand.svg" height="100px" width="100px" alt="waving-icon">
                  </div>
                </div>
            </div>                     
            <div class="bg-medium-brown rounded-3 datetime">
              <div class="d-flex align-items-center fw-semibold ps-3 text-white">
                <div class="pe-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-clock-fill me-2" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                  </svg>                  
                </div>
                <div>
                  <div class="fs-1 time"></div>
                  <div class="fs-4 date"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Second Quarter: Larger Container -->
          <div class="col-md-6 mt-5 mb-4">
            <div class="row">
              <div class="col-md-6">
                <div class="col-md-12 bg-tiger-orange card-gradient rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 1 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-bold">Sales Today</span>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php
                      if ($today_sale !== NULL) {
                        echo number_format($today_sale, 2); 
                      }
                      else {
                        echo "0.00";
                      } 
                      ?>
                    </div> 
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 bg-tiger-orange card-gradient rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 2 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-bold">Orders Today</span>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php 
                      if ($order_today !== NULL) {
                        echo $order_today; 
                      }
                      else {
                        echo "No order.";
                      }
                      ?>
                    </div> 
                  </div>
                </div>
              </div>  
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="col-md-12 card-gradient mt-4 bg-tiger-orange rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 3 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-bold">Weekly Sales</span>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php
                      if ($weekly_sales !== NULL) {
                        echo number_format($weekly_sales, 2); 
                      }
                      else {
                        echo "0.00";
                      }
                      ?>
                    </div> 
                  </div>
              </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 card-gradient mt-4 bg-tiger-orange rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 4 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-bold">Monthly Sales</span>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php
                      if ($monthly_sale !== NULL) {
                        echo number_format($monthly_sale, 2);
                      } 
                      else {
                          echo "0.00";
                      }
                      ?>
                    </div> 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">         
          <div class="col-md-6"> <!-- Increased py-4 for more padding -->
              <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 3 -->
                  <div class="p-2 fw-bold text-uppercase text-muted">
                    weekly sales
                  </div>
                  <div id="weeklySales"></div>
              </div>
          </div>
          <div class="col-md-6"> <!-- Increased py-4 for more padding -->
              <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 3 -->
                  <div class="p-2 fw-bold text-uppercase text-muted">
                    predicted weekly sales
                  </div>
                  <div id="predictedWeeklySales"></div>
              </div>
          </div>

          <!-- Third Quarter: Larger Container -->
          <div class="col-md-6 mt-2"> <!-- Increased py-4 for more padding -->
              <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 2 -->
                  <div class="p-2 mb-2 fw-bold text-uppercase text-muted">
                    weekly top sold products
                  </div>                              
                  <div id="chartdiv1"></div>  
              </div>
          </div>
          
          <!-- Fourth Quarter: Larger Container -->
          <div class="col-md-6 mt-2"> <!-- Increased py-4 for more padding -->
              <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 3 -->
                  <div class="p-2 mb-2 fw-bold text-uppercase text-muted fs-6">
                    weekly top sold category
                  </div>
                    <div id="chartdiv2"></div>
              </div>
          </div>
        </div>
      </div>
    </div>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/index.js"></script>

  </body>
</html>