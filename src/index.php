<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/execute-prediction-script.php');
include_once FileUtils::normalizeFilePath('includes/default-timezone.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

if(!isset($_SESSION['id'])) {
  header("Location: login");
  exit();
}

$today = date("Y-m-d");

$sql = "
    SELECT 
        SUM(CASE WHEN DATE(timestamp) = ? THEN total_amount ELSE 0 END) AS today_sale,
        COUNT(CASE WHEN DATE(timestamp) = ? THEN 1 ELSE NULL END) AS today_order,
        SUM(CASE WHEN YEAR(timestamp) = YEAR(CURRENT_DATE()) AND MONTH(timestamp) = MONTH(CURRENT_DATE()) AND DAY(timestamp) <= DAY(CURRENT_DATE()) THEN total_amount ELSE 0 END) AS monthly_sale
    FROM transaction";

$stmt = $db->prepare($sql);
$stmt->bind_param('ss', $today, $today);
$stmt->execute();
$stmt->bind_result($today_sale, $today_order, $monthly_sale);
$stmt->fetch();
$stmt->close();

// Proper format and checking if value is not null
$today_sale_formatted = ($today_sale !== NULL) ? number_format($today_sale, 2) : 0.00;
$monthly_sale_formatted = ($monthly_sale !== NULL) ? number_format($monthly_sale, 2) : 0.00;
$today_order_count = ($today_order !== NULL) ? $today_order : 0;

$start_of_month = date('F 01');
$current_day = date('F d, Y');
$date_range = $start_of_month . ' - ' . $current_day;

// Calculate the start and end dates of the current week
$start_of_week = date('Y-m-d', strtotime('last Monday', strtotime('next Sunday')));
$end_of_week = date('Y-m-d', strtotime('next Saturday'));

// Get the sum of total_amount for the current week
$sql = "
    SELECT SUM(total_amount) AS weekly_sale 
    FROM transaction 
    WHERE timestamp >= ? AND timestamp <= ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $start_of_week, $end_of_week);
$stmt->execute();
$stmt->bind_result($weekly_sale);
$stmt->fetch();
$stmt->close();

$weekly_sale_formatted = ($weekly_sale !== NULL) ? number_format($weekly_sale, 2) : 0.00;

$start_of_week_formatted = date('F d', strtotime($start_of_week));
$end_of_week_formatted = date('F d, Y', strtotime($end_of_week));
$week_duration = $start_of_week_formatted . ' - ' . $end_of_week_formatted;

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

      <style>
        .card-gradient {
          background: linear-gradient(to top left, #88531E, #88531E, #C57C47);
        }
      </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    
    <!-- Bootstrap JavaScript -->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- CDN  -->
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="javascript/index.js" defer></script>
    <script src="javascript/preloader.js" defer></script>
    <script src="javascript/reports.js" defer></script>
  </head>

  <body class="bg-gainsboro">

    <?php
    // Navbar
    include 'includes/preloader.html';
    include 'includes/navbar.php';
    
    // Charts
    include FileUtils::normalizeFilePath('includes/charts/weekly-sales.php');
    include FileUtils::normalizeFilePath('includes/charts/predicted-weekly-sales.php');
    ?>

    <!-- Main Content -->
    <div class="container mb-5">
      <div class="col-lg-12">
        <div class="row">
          <!-- First Quarter: Mini Containers -->
          <div class="col-md-6 mt-5">
            <div class="col-md-12 bg-white rounded-3 mb-4 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.4); height: 150px;"> <!-- Added box-shadow style for drop shadow -->
                <!-- Larger Container 1 -->
                <div class="row">
                  <div class="col-md-auto">
                    <div class="mx-3">
                      <div class="fw-semibold text-carbon-grey fs-3 pb-1 pt-2">
                        Hello there, <span class="text-medium-brown fw-bold text-capitalize"><?php echo htmlspecialchars($current_user['first_name']) . '!'; ?></span>
                      </div>
                      <div class="text-carbon-grey fw-medium">Here's what's happening with the store.</div>
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
                <div class="col-md-12 bg-medium-brown rounded-3 px-4 py-2" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 1 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-semibold fs-5">Sales Today</span>
                    <div class="font-12"><?php echo htmlspecialchars($current_day); ?></div>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php echo htmlspecialchars($today_sale_formatted); ?>
                    </div> 
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 bg-medium-brown  rounded-3 px-4 py-2" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 2 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-semibold fs-5">Orders Today</span>
                    <div class="font-12"><?php echo htmlspecialchars($current_day); ?></div>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php echo htmlspecialchars($today_order_count); ?>
                    </div> 
                  </div>
                </div>
              </div>  
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="col-md-12 mt-4 bg-medium-brown rounded-3 px-4 py-2" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 3 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-semibold fs-5">Weekly Sales</span>
                    <div class="font-12"><?php echo htmlspecialchars($week_duration); ?></div>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php echo htmlspecialchars($weekly_sale_formatted); ?>
                    </div> 
                  </div>
              </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 mt-4 bg-medium-brown rounded-3 px-4 py-2" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 4 -->
                  <div class="text-center fw-medium text-white py-2">
                    <span class="fw-semibold fs-5">Monthly Sales</span>
                    <div class="font-12"><?php echo htmlspecialchars($date_range); ?></div>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php echo htmlspecialchars($monthly_sale_formatted) ?>
                    </div> 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">         
          <div class="col-md-6"> <!-- Increased py-4 for more padding -->
              <div class="col-md-12 bg-white mb-3 rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.4); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 3 -->
                  <div class="p-2 fw-semibold text-carbon-grey">
                    FROM PREVIOUS WEEK
                  </div>
                  <div id="weeklySales"></div>
              </div>
          </div>
          <div class="col-md-6"> <!-- Increased py-4 for more padding -->
              <div class="col-md-12 bg-white mb-3 rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.4); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 3 -->
                  <div class="p-2 fw-semibold text-carbon-grey">
                    PREDICTED WEEKLY SALE
                  </div>
                  <div id="predictedWeeklySales"></div>
              </div>
          </div>
        </div>

        <div class="row">         
          <div class="col-md-9"> <!-- Increased py-4 for more padding -->
              <div class="col-md-12 bg-white mb-3 rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.4); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 3 -->
                  <div class="p-2 mb-2 fw-semibold text-carbon-grey">
                      WEEKLY TOP SOLD PRODUCTS
                    </div>                              
                    <div class="table-container">
                      <table id="example" class="styled-table">
                          <thead>
                              <tr>
                                  <th>Top Pick</th>
                                  <th></th>
                                  <th>Likely Paired With</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                                  // Run the apriori_algo.py script
                                  $output = shell_exec('python apriori/apriori_algo.py 2>&1');

                                  // Fetch the specific item with ID 1 from the database
                                  $sql = "SELECT antecedent, consequent FROM frequent_items WHERE id = 1";
                                  $result = $db->query($sql);

                                  $topPickData = [];
                                  if ($result->num_rows > 0) {
                                      while ($row = $result->fetch_assoc()) {
                                        $topPickData[] = [addslashes($row["antecedent"]), '+', addslashes($row["consequent"])];
                                          echo "<tr>
                                                  <td>
                                                      <div class='product-info'>
                                                          <img class='pt-2 card-img-top' src='images/coffee-img-placeholder.png'>
                                                          <span class='spaced-text text-capitalize fw-semibold'>" . htmlspecialchars($row["antecedent"]) . "</span>
                                                      </div>
                                                  </td>
                                                  <td class='plus-sign' style='color: #C57C47;'>+</td>
                                                  <td>
                                                      <div class='product-info'>
                                                          <img class='pt-2 card-img-top' src='images/coffee-img-placeholder.png'>
                                                          <span class='spaced-text text-capitalize fw-semibold'>" . htmlspecialchars($row["consequent"]) . "</span>
                                                      </div>
                                                  </td>
                                              </tr>";
                                      }
                                  } else {
                                      $topPickData[] = ['No data available'];
                                      echo "<tr>
                                              <td colspan='3' style='text-align: center;'>
                                                  <img class='pt-2 card-img-top' src='images/empty.png' style='max-width: 250px; max-height: 250px;'>
                                                  <br>
                                                  Oops! It looks like there's no available data.
                                              </td>
                                            </tr>";
                                  }
                                  $jsonTopPickData = json_encode($topPickData);
                              ?>
                          </tbody>
                      </table>
                  </div>
              </div>
            </div>
            <div class="col-md-3">
                <div class="col-md-12 bg-white mb-3 rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.4); height: 410px;">
                    <div class="p-2 mb-2 fw-semibold text-carbon-grey fs-4 text-center">
                        Generate Report Here
                    </div>
                    <div class="col-auto text-center mb-3"> <!-- Centering the image -->
                        <img src="images/arrow.png" height="175px" width="175px" alt="arrow">
                    </div>
                    <div class="text-center"> <!-- Centering the button -->
                        <button onclick="downloadPDF()" style="background-color: #88531E; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                            Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
    </div>

    <script>
      // Data used for generating reports
      const user = <?php echo json_encode($full_name); ?>;
      const todaySale = <?php echo json_encode($today_sale_formatted); ?>;
      const orderToday = <?php echo json_encode($today_order); ?>;
      const weeklySale = <?php echo json_encode($weekly_sale_formatted); ?>;
      const weekDuration = <?php echo json_encode($week_duration); ?>;
      const monthlySale = <?php echo json_encode($monthly_sale_formatted); ?>;
      const dateRange = <?php echo json_encode($date_range); ?>;
      const weeklySalesData = <?php echo $json_sales_data; ?>;
      const topPickData = <?php echo $jsonTopPickData; ?>;
    </script>

  </body>
</html>