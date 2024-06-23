<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
require_once FileUtils::normalizeFilePath('includes/execute-prediction-script.php');
include_once FileUtils::normalizeFilePath('includes/default-timezone.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

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

$sql = "SELECT SUM(total_amount) AS monthly_sale FROM transaction WHERE YEAR(timestamp) = YEAR(CURRENT_DATE()) AND MONTH(timestamp) = MONTH(CURRENT_DATE()) AND DAY(timestamp) <= DAY(CURRENT_DATE())";
$stmt = $db->prepare($sql);
$stmt->execute();
$stmt->bind_result($monthly_sale);
$stmt->fetch();
$stmt->close();

$start_of_month = date('F 01');
$current_day = date('F d, Y');
$date_range = $start_of_month . ' - ' . $current_day;

// Weekly Sale
$current_day_of_week = date('N');

// Calculate the start and end dates of the current week
if ($current_day_of_week == 1) {
    // If today is Monday, start from today (Monday) and end on Saturday
    $start_of_week = date('Y-m-d');
    $end_of_week = date('Y-m-d', strtotime('next Saturday'));
} else {
    // If today is not Monday, start from last Monday and end on next Saturday
    $start_of_week = date('Y-m-d', strtotime('last Monday'));
    $end_of_week = date('Y-m-d', strtotime('next Saturday'));
}

// SQL query to get the sum of total_amount for the current week
$sql = "SELECT SUM(total_amount) AS weekly_sale 
        FROM transaction 
        WHERE timestamp >= ? AND timestamp <= ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $start_of_week, $end_of_week);
$stmt->execute();
$stmt->bind_result($weekly_sale);
$stmt->fetch();
$stmt->close();

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
                        Hello there, <span class="text-medium-brown fw-bold text-capitalize"><?php echo htmlspecialchars($user) . '!'; ?></span>
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
                <div class="col-md-12 bg-medium-brown  rounded-3 px-4 py-2" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 2 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-semibold fs-5">Orders Today</span>
                    <div class="font-12"><?php echo htmlspecialchars($current_day); ?></div>
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
                <div class="col-md-12 mt-4 bg-medium-brown rounded-3 px-4 py-2" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 3 -->
                  <div class="text-center fw-medium py-2 text-white">
                    <span class="fw-semibold fs-5">Weekly Sales</span>
                    <div class="font-12"><?php echo htmlspecialchars($week_duration); ?></div>
                    <div class="pb-4 pt-2 d-flex justify-content-center fw-semibold" style="font-size: 31px;">
                      <?php
                      if ($weekly_sale !== NULL) {
                        echo number_format($weekly_sale, 2); 
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
                <div class="col-md-12 mt-4 bg-medium-brown rounded-3 px-4 py-2" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); height: 150px;">
                  <!-- Mini Container 4 -->
                  <div class="text-center fw-medium text-white py-2">
                    <span class="fw-semibold fs-5">Monthly Sales</span>
                    <div class="font-12"><?php echo htmlspecialchars($date_range); ?></div>
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
              <div class="col-md-12 bg-white mb-3 rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.4); height: 410px;"> <!-- Added box-shadow style for drop shadow -->
                  <!-- Larger Container 3 -->
                  <div class="p-2 fw-semibold text-carbon-grey">
                    WEEKLY SALE
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

          <div class="row p-2">         
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
                                    <th>Popular Combo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Run the apriori_algo.py script
                                    $output = shell_exec('python apriori/apriori_algo.py 2>&1');

                                    // Fetch the specific item with ID 1 from the database
                                    $sql = "SELECT antecedent, consequent FROM frequent_items WHERE id = 1";
                                    $result = $db->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>
                                                        <div class='product-info'>
                                                            <img class='pt-2 card-img-top' src='images/coffee-img-placeholder.png'>
                                                            <span class='spaced-text'>" . htmlspecialchars($row["antecedent"]) . "</span>
                                                        </div>
                                                    </td>
                                                    <td class='plus-sign' style='color: #C57C47;'>+</td>
                                                    <td>
                                                        <div class='product-info'>
                                                            <img class='pt-2 card-img-top' src='images/coffee-img-placeholder.png'>
                                                            <span class='spaced-text'>" . htmlspecialchars($row["consequent"]) . "</span>
                                                        </div>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr>
                                                <td colspan='3' style='text-align: center;'>
                                                    <img class='pt-2 card-img-top' src='images/empty.png' style='max-width: 250px; max-height: 250px;'>
                                                    <br>
                                                    Oops! It looks like there's no data available.
                                                </td>
                                              </tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="col-md-12 bg-white mb-3 rounded-3 p-4 mx-3" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.4); height: 410px;">
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

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="javascript/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script src="javascript/preloader.js"></script>


    <script>
async function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const logo = "images/sweet-avenue-logo.png"; // Adjust the logo path as necessary

    // Create a new PDF document
    const doc = new jsPDF();

    // Add logo
    const img = new Image();
    img.src = logo;
    await new Promise(resolve => img.onload = resolve);
    doc.addImage(img, 'PNG', 15, 10, 18, 18); // Adjust the size and position as needed

    // Set font and colors
    doc.setFont('Helvetica', 'ExtraBold');
    doc.setFontSize(20);
    doc.setTextColor('#88531E');
    doc.text("SWEET AVENUE", 35, 20);
    doc.setFontSize(16);
    doc.text("COFFEE • BAKESHOP", 35, 25);

    // User information
    doc.setFontSize(18);
    doc.setFont('Helvetica', 'bold');
    const today = new Date();
    const reportDateTime = `Reports For (${today.toLocaleString()})`;
    const textWidth = doc.getStringUnitWidth(reportDateTime) * doc.internal.getFontSize() / doc.internal.scaleFactor;
    const center = (doc.internal.pageSize.width - textWidth) / 2;
    doc.text(reportDateTime, center, 40);

    // Sales and order details
    doc.setFontSize(12);
    doc.setFont('Helvetica', 'normal');
    doc.setTextColor('#000000');

    doc.text("Generated by: ", 15, 50);
    doc.setFont('Helvetica', 'bold');
    doc.setTextColor('#88531E');
    doc.text(`<?php echo $user; ?>`, 80, 50);

    doc.setFont('Helvetica', 'normal');
    doc.setTextColor('#000000');
    doc.text("Sales Today:", 15, 60);
    doc.setFont('Helvetica', 'bold');
    doc.setTextColor('#88531E');
    doc.text(`₱<?php echo number_format($today_sale, 2); ?>`, 80, 60);

    doc.setFont('Helvetica', 'normal');
    doc.setTextColor('#000000');
    doc.text("Orders Today:", 15, 70);
    doc.setFont('Helvetica', 'bold');
    doc.setTextColor('#88531E');
    doc.text(`<?php echo $order_today; ?>`, 80, 70);  // Remove formatting for order count

    doc.setFont('Helvetica', 'normal');
    doc.setTextColor('#000000');
    doc.text("Weekly Sales:", 15, 80);
    doc.setFont('Helvetica', 'bold');
    doc.setTextColor('#88531E');
    doc.text(`₱<?php echo number_format($weekly_sale, 2); ?>`, 80, 80);
    doc.setFont('Helvetica', 'semibold');
    doc.text(`(<?php echo htmlspecialchars($week_duration); ?>)`, 15, 85);

    doc.setFont('Helvetica', 'normal');
    doc.setTextColor('#000000');
    doc.text("Monthly Sales:", 15, 90);
    doc.setFont('Helvetica', 'bold');
    doc.setTextColor('#88531E');
    doc.text(`₱<?php echo number_format($monthly_sale, 2); ?>`, 80, 90);
    doc.setFont('Helvetica', 'semibold');
    doc.text(`(<?php echo htmlspecialchars($date_range); ?>)`, 15, 95);


    // Weekly Sales Table (fetched dynamically)
    doc.setFontSize(14);
    doc.setFont('Helvetica', 'bold');
    doc.text("Weekly Sales", 15, 105);

    // Parse the JSON data fetched from PHP
    const weeklySalesData = <?php echo $json_sales_data; ?>;
    const weeklySalesHeaders = ["Date", "Sales"];
    const weeklySalesDataFormatted = weeklySalesData.map(entry => [
        entry.date,
        `₱${entry.total_sales.toFixed(2)}`
    ]);

    // Weekly Sales Table Styling
    doc.autoTable({
        startY: 110,
        head: [weeklySalesHeaders],
        body: weeklySalesDataFormatted,
        theme: 'grid',
        styles: {
            textColor: '#88531E',
            fillColor: '#88531E',
            halign: 'center'
        },
        headStyles: {
            fillColor: '#88531E',
            textColor: '#ffffff'
        },
        bodyStyles: {
            fillColor: '#ffffff',
            textColor: '#88531E'
        }
    });

    // Predicted Weekly Sales Table
    doc.setFontSize(14);
    doc.setFont('Helvetica', 'bold');
    doc.text("Predicted Weekly Sales", 15, doc.previousAutoTable.finalY + 10);

    // Fetching and using predicted sales data
    const predictedSalesData = {
        predictions: [
            {"date": "2024-06-01", "sales_prediction": 4051.77},
            {"date": "2024-06-02", "sales_prediction": 4299.14},
            {"date": "2024-06-03", "sales_prediction": 4546.51},
            {"date": "2024-06-04", "sales_prediction": 4793.89},
            {"date": "2024-06-05", "sales_prediction": 5041.26},
            {"date": "2024-06-06", "sales_prediction": 5288.63}
        ],
        sales_sum: 28021.2
    };

    const predictedWeeklySalesHeaders = ["Date", "Sales"];
    const predictedWeeklySalesData = predictedSalesData.predictions.map(prediction => [
        prediction.date,
        `₱${prediction.sales_prediction.toFixed(2)}`
    ]);

    // Predicted Weekly Sales Table Styling
    doc.autoTable({
        startY: doc.previousAutoTable.finalY + 15,
        head: [predictedWeeklySalesHeaders],
        body: predictedWeeklySalesData,
        theme: 'grid',
        styles: {
            textColor: '#88531E',
            fillColor: '#88531E',
            halign: 'center'
        },
        headStyles: {
            fillColor: '#88531E',
            textColor: '#ffffff'
        },
        bodyStyles: {
            fillColor: '#ffffff',
            textColor: '#88531E'
        }
    });

    // Top Pick and Popular Combo Table
    doc.setFontSize(14);
    doc.setFont('Helvetica', 'bold');
    doc.text("Weekly Top Sold Products", 15, doc.previousAutoTable.finalY + 10);

    const topPickHeaders = ["Top Pick", "", "Popular Combo"];
    const topPickData = [
      <?php
        // Fetch data for Top Pick and Popular Combo from the database
        $sql = "SELECT antecedent, consequent FROM frequent_items WHERE id = 1";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "['" . addslashes($row["antecedent"]) . "', '+', '" . addslashes($row["consequent"]) . "'],";
          }
        } else {
          echo "['No data available'],";
        }
      ?>
    ];

    // Top Pick and Popular Combo Table Styling
    doc.autoTable({
        startY: doc.previousAutoTable.finalY + 15,
        head: [topPickHeaders],
        body: topPickData,
        theme: 'grid',
        styles: {
            textColor: '#88531E',
            fillColor: '#ffffff',
            halign: 'center'
        },
        headStyles: {
            fillColor: '#ffffff',
            textColor: '#88531E'
        },
        bodyStyles: {
            fillColor: '#ffffff',
            textColor: '#88531E'
        }
    });

    // Save the PDF
    doc.save('report.pdf');
}
</script>




  </body>
</html>