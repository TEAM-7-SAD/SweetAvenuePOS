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
                $sql = "SELECT id, LOWER(REPLACE(name, 'and', '&')) AS name FROM food_category 
                UNION 
                SELECT id, LOWER(REPLACE(name, 'and', '&')) AS name FROM drink_category";
        
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

                foreach($categories as $category) {
                    // Check if the category is "Snacks and Rice Meals"
                    $isChecked = ($category['name'] === 'snacks and rice meals') ? 'checked' : '';
                
                    // Output the radio button for each category
                    echo '<input type="radio" class="btn-check" name="category" id="category' . $category['id'] . '" autocomplete="off" value="' . $category['id'] . '" ' . $isChecked . '>';
                    echo '<label class="btn btn-outline-tiger-orange fw-semibold shadow-sm py-3" for="category' . $category['id'] . '">' . $category['name'] . '</label>';
                }
                
                echo '</div>';
                ?>
                
            
            <!--Products Container-->
            <div class="col-lg-12 col-xl-12 col-xxl-12 mt-3 pb-3 product-container" style="max-height: 630px; overflow-y: auto;">
              <div class="row">
                <!--Products output here through AJAX request-->
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
    
    
    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              // Add event listeners to category radio buttons
              var categoryRadios = document.querySelectorAll('input[name="category"]');
              categoryRadios.forEach(function(radio) {
                  radio.addEventListener('change', function(event) {
                      var categoryId = this.value;
                      fetchProducts(categoryId);
                  });
              });

              function fetchProducts(categoryId) {
                  // Send AJAX request to fetch products for the selected category
                  var xhr = new XMLHttpRequest();
                  xhr.onreadystatechange = function() {
                      if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                          // Update product container with the new products
                          document.querySelector('.product-container').innerHTML = this.responseText;
                      }
                  };
                  xhr.open('GET', 'get-products.php?category=' + categoryId, true);
                  xhr.send();
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