$(document).ready(function() {
  let defaultCategoryId = $('input[name="category"]:checked').val();
  fetchProducts(defaultCategoryId);

  $('input[name="category"]').change(function() {
    let categoryId = $(this).val();
    fetchProducts(categoryId);
  });

  // Function to fetch products
  function fetchProducts(categoryId) {
    $.ajax({
      url: 'get-products.php',
      type: 'POST',
      data: {category: categoryId},
      success: function(response) {
        $('.product-container').html(response);
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
      }
    });
  }

  // Function to fetch variations
  function fetchVariations(productId, productType) {
    $.ajax({
      url: 'get-product-details.php',
      type: 'POST',
      data: {productId: productId, productType: productType},
      success: function(response) {
        let data = JSON.parse(response);
        // Update modal with variations
        $('#productVariation').html('');
        if (data.variations && data.variations.length > 0) {
          data.variations.forEach(function(variation) {
            // Append each variation to the modal
            let variationHTML = '<label class="btn btn-sm btn-outline-product fw-semibold rounded-4">' + JSON.stringify(variation) + '</label>';
            $('#productVariation').append(variationHTML);
          });
        }
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
      }
    });
  }

  // Show modal on product click and fetch variations
  $('#product').on('show.bs.modal', function(event) {
      let button = $(event.relatedTarget);
      let productId = button.data('product-id');
      let productType = button.data('product-type');

      // Fetch variations for the selected product
      fetchVariations(productId, productType);
  });

});
