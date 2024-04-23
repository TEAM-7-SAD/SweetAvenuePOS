<?php
require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';

if(isset($_SESSION['id'])) {

?>

<!DOCTYPE html>
<html lang="en">

  <!--Head elements-->
  <?php
  include 'includes/head-element.php';
  ?>
  
  <body class="bg-timberwolf">

    <!--Navbar-->
    <?php
    include 'includes/navbar.php';
    ?>
 
    <!--Main Content-->
    <div class="container-fluid ps-5 mt-4 mb-4">          
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
                    echo '<label class="btn btn-outline-tiger-orange fw-semibold shadow-sm py-3" for="category' . $category['id'] . '">' . $category['name'] . '</label>';
                }
                
                echo '</div>';
                ?>
            <!--Products Container-->
            <div class="col-lg-12 col-xl-12 col-xxl-12 mt-3 pb-3 product-container" style="max-height: 630px; overflow-y: auto;">
              <div class="row">
                <?php
                // Fetch and display the default category's list of items
                if ($defaultCategoryId !== null) {
                    include 'get-products.php';
                }
                ?>
              </div>
            </div>

          </div>
        </div>


        <?php
        include 'billing-section.php';
        ?>

      </div>
    </div>
    <!--End of Main Content-->
    

    <!--jQuery via CDN-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!--Custom JavaScript/jQuery-->
    <script src="javascript/get-products.js"></script>

  </body>
</html>
<?php 
  } else {
    header("location: login.php");
  } 
?>
