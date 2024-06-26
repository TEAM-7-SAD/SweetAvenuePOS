<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/includes/file-utilities.php');
require_once FileUtils::normalizeFilePath('includes/db-connector.php');
require_once FileUtils::normalizeFilePath('includes/session-handler.php');
include_once FileUtils::normalizeFilePath('includes/error-reporting.php');

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
    <link rel="stylesheet" href="styles/main.css" />   
    <!--Site Icon-->
    <link rel="icon" href="images/sweet-avenue-logo.png" type="image/png"/>

    <!--jQuery via CDN-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    
    <!--Custom JavaScript/jQuery-->
    <script src="javascript/get-products.js" defer></script>
    <script src="javascript/billing.js" defer></script>
    <script src="javascript/preloader.js" defer></script>
</head>

  
  <body class="bg-gainsboro">

    <!--Navbar-->
    <?php
    include FileUtils::normalizeFilePath('includes/navbar.php');
    include FileUtils::normalizeFilePath('includes/preloader.html');
    ?>
 
    <!--Main Content-->
    <div class="container mt-4 mb-4">          
      <div class="row"> 
       
        <!--Products Section-->
        <div class="col-lg-7 mb-4">
          <div class="row">

                <?php 
                // Query the food and drink category table
                $sql = "SELECT id, name FROM food_category 
                UNION 
                SELECT id, name FROM drink_category";
        
                $result = $db->query($sql);
                $categories = array();

                if($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                    $categories[] = $row;
                  }
                }
                else{
                  echo 'No results found';
                }
                
                echo '<div class="btn-group text-capitalize" role="group" aria-label="Basic radio toggle button group">';

                // Variable to store the default category ID
                $defaultCategoryId = null;

                foreach($categories as $category) {
                    // Check if the category is "Snacks and Rice Meals"
                    $isChecked = ($category['name'] === 'snacks & rice meals') ? 'checked' : '';
                    
                    // Set the default category ID
                    if ($isChecked) {
                        $defaultCategoryId = $category['id'];
                    }
                
                    // Output the radio button for each category
                    echo '<input type="radio" class="btn-check" name="category" id="category' . $category['id'] . '" autocomplete="off" value="' . $category['id'] . '" ' . $isChecked . '>';
                    echo '<label class="btn btn-outline-medium-brown fw-semibold shadow-sm py-3 font-14" for="category' . $category['id'] . '">' . $category['name'] . '</label>';
                }
                
                echo '</div>';
                ?>
            <!--Products Container-->
            <div class="col-lg-12 col-xl-12 col-xxl-12 mt-3 pb-3 product-container" style="max-height: 630px; overflow-y: auto;">
              <div class="row">
                <?php
                // Fetch and display the default category's list of items
                if ($defaultCategoryId !== null) {
                    include FileUtils::normalizeFilePath('get-products.php');
                }
                ?>
              </div>
            </div>

          </div>
        </div>


        <?php
        include FileUtils::normalizeFilePath('billing-section.php');
        ?>

      </div>
    </div>
    <!--End of Main Content-->

    <script>
      $(document).ready(function() {
        $('#cancelOrder').on('click', function() {
        $.ajax({
                type: 'POST',
                url: 'clear_cart.php',
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Clear the subtotal
                        $('#subtotalValue').text('0.00');
                        // Clear the order cart display and maintain table striping
                        $('#orderCart').empty().append('<tr><td colspan="4" class="text-center text-muted table-striped">No items in cart</td></tr>');
                    } else {
                        alert(data.message); // Display error message
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
      });
    </script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
