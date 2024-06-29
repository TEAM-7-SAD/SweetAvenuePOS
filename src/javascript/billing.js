function checkCart() {
  const cartItems = $("#orderCart").children();
  const placeOrderButton = $("#placeOrderButton");
  const subtotal = parseFloat(
    $("#subtotalValue")
      .text()
      .replace(/[^0-9.-]+/g, "")
  );

  placeOrderButton.prop(
    "disabled",
    cartItems.length === 1 && cartItems.text().includes("No items in cart")
  );
}

$(document).ready(function () {
  $("#placeOrderButton").on("click", function () {
    $.ajax({
      url: "process-order.php",
      type: "POST",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Populate order review section and subtotal
          var orderItemsHtml = "";
          response.order_cart.forEach(function (item) {
            orderItemsHtml += '<tr data-item-id="' + item.id + '">';
            item.price = parseFloat(item.price);
            orderItemsHtml += '<td class="align-middle ps-2 pe-3">';
            orderItemsHtml +=
              '<p class="mb-0 text-muted fw-semibold font-14">x' +
              item.quantity +
              "</p>";
            orderItemsHtml += "</td>";
            orderItemsHtml +=
              '<td class="align-middle" style="white-space: nowrap;">';
            orderItemsHtml +=
              '<div class="d-flex flex-column align-items-start py-2">';
            orderItemsHtml +=
              '<p class="fw-semibold text-muted mb-0 text-capitalize font-14">' +
              item.product_name +
              "</p>";
            orderItemsHtml +=
              '<div class="text-tiger-orange fw-semibold text-capitalize" style="font-size: 11px; max-width: 200px;">';
            orderItemsHtml +=
              '<p class="text-capitalize mb-0">' +
              item.serving_or_type +
              "</p>";
            orderItemsHtml += '<p class="mb-0">' + item.flavor_or_size + "</p>";
            orderItemsHtml += "</div>";
            orderItemsHtml += "</div>";
            orderItemsHtml += "</td>";
            orderItemsHtml += '<td class="align-middle text-end pe-2">';
            orderItemsHtml +=
              '<div class="mb-0 fw-semibold fs-6 text-carbon-grey font-14">' +
              item.price.toFixed(2) +
              "</div>";
            orderItemsHtml += "</td>";
            orderItemsHtml += "</tr>";
            console.log(item.id);
          });

          $("#orderReview").html(orderItemsHtml);
          $("#orderSubtotalReview").text(response.subtotal.toFixed(2));
          $("#grandTotalValue").text(response.subtotal.toFixed(2));

          // Show the modal
          $("#orderConfirmationModal").modal("show");
        } else {
          alert("Error: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  // Function to clear the cart
  function clearCart() {
    $("#orderCart").html(
      '<tr><td colspan="3" class="mb-0 fw-medium text-center text-muted font-14">No items in cart</td></tr>'
    );
    $("#subtotalValue").text("0.00");
    checkCart();
  }

  // Run the checkCart function on page load
  checkCart();

  $("#cancelOrder").on("click", clearCart);
  $("#proceedOrder").prop("disabled", true);

  $("#tenderedAmount").on("input", function () {
    const tenderedAmount = parseFloat($(this).val());
    const grandTotal = parseFloat($("#grandTotalValue").text());

    $("#proceedOrder").prop(
      "disabled",
      isNaN(tenderedAmount) || tenderedAmount < grandTotal
    );
  });

  $("#proceedOrder").on("click", function () {
    const tenderedAmount = parseFloat($("#tenderedAmount").val());
    const change = tenderedAmount - parseFloat($("#grandTotalValue").text());

    const items = [];
    $("#orderCart tr").each(function () {
      const itemId = $(this).data("item-id");
      const quantity = parseFloat(
        $(this).find("td:eq(0) p").text().trim().slice(1)
      );
      const price = parseFloat(
        $(this)
          .find("td:eq(2) .text-carbon-grey")
          .text()
          .trim()
          .replace(/[^\d.]/g, "")
      );

      items.push({
        id: itemId,
        quantity: quantity,
        price: price,
      });
    });

    const subtotal = items.reduce(
      (acc, item) => acc + item.quantity * item.price,
      0
    );

    // Log items to verify
    console.log("Items:", items);

    // Log data being sent
    console.log("Sending data to server:", {
      subtotal: subtotal,
      discount: parseFloat($("#discountInput").val()) || 0,
      grand_total: parseFloat($("#grandTotalValue").text()),
      tendered_amount: tenderedAmount,
      change: change,
      items: JSON.stringify(items),
    });

    $.ajax({
      url: "finish-order.php",
      type: "POST",
      dataType: "json",
      data: {
        subtotal: subtotal,
        discount: parseFloat($("#discountInput").val()) || 0,
        grand_total: parseFloat($("#grandTotalValue").text()),
        tendered_amount: tenderedAmount,
        change: change,
        items: JSON.stringify(items),
      },
      success: function (response) {
        if (response.success) {
          console.log("Order processed successfully");
          $.ajax({
            type: "POST",
            url: "clear_cart.php",
            success: function (response) {
              let data = JSON.parse(response);
              if (data.status === "success") {
                $("#subtotalValue").text("0.00");
                $("#orderCart")
                  .empty()
                  .append(
                    '<tr><td colspan="4" class="text-center text-muted table-striped">No items in cart</td></tr>'
                  );
                $("#cancelOrder").on("click", clearCart);
                $("#proceedOrder").prop("disabled", true);
                checkCart();
              } else {
                alert(data.message);
              }
            },
            error: function () {
              alert("An error occurred. Please try again.");
            },
          });
        } else {
          alert("Error: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });

    $("#orderConfirmationModal").modal("hide");
    $("#receiptModal").modal("show");

    $("#tenderedAmountReceipt").text(tenderedAmount.toFixed(2));
    $("#changeDisplayReceipt").text(change.toFixed(2));

    $("#tenderedAmount").val("");
    $("#changeDisplay").html("");

    printOrderConfirmation(tenderedAmount);

    checkCart();
  });

  function printOrderConfirmation(tenderedAmount) {
    const printableContent = generatePrintableContent(tenderedAmount);
    clearOrdersAndSubtotal();

    const printWindow = window.open("", "_blank");
    printWindow.document.write(printableContent);
    printWindow.print();
  }

  function clearOrdersAndSubtotal() {
    $("#orderCart").html("");
    $("#subtotalValue").text("0.00");
  }

  function generatePrintableContent(tenderedAmount) {
    const processedBy = currentUser;
    const orderDetails = {
        items: [],
        subtotal: parseFloat($("#subtotalValue").text().replace(/,/g, "")),
        discountPercentage: parseFloat($("#discountInput").val()) || 0, // Set to 0 if NaN
        grandTotal: parseFloat($("#grandTotalValue").text()),
        tenderedAmount: tenderedAmount,
    };

    const change = tenderedAmount - orderDetails.grandTotal;
    const discountAmount = orderDetails.subtotal * (orderDetails.discountPercentage / 100);

    $("#orderCart tr").each(function () {
        const cells = $(this).find("td");
        orderDetails.items.push({
            productName: cells.eq(1).find(".text-capitalize").text().trim(),
            quantity: cells.eq(0).find("p").text().trim().slice(1),
            price: parseFloat(
                cells
                    .eq(2)
                    .find(".text-carbon-grey")
                    .text()
                    .trim()
                    .replace(/[^\d.]/g, "")
            ),
        });
    });

    return `
        <html>
            <head>
                <title>Receipt</title>
                <style>
                    @media print {
                        .receipt {
                            width: auto;
                            margin: 0 auto;
                            padding: 10px;
                        }
                        .table, .table tr, .table th, .table td {
                            page-break-inside: avoid;
                        }
                    }
                    html, body {
                        width: 100%;
                        height: 100%;
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        font-family: Arial, sans-serif;
                        background-color: #f8f8f8;
                    }
                    .receipt {
                        width: 80mm; /* Width of a typical thermal paper */
                        padding: 10px;
                        box-sizing: border-box;
                        background-color: white;
                        page-break-inside: avoid;
                    }
                    .center-text {
                        text-align: center;
                    }
                    .table {
                        width: 100%;
                        border-collapse: collapse;
                        page-break-inside: avoid;
                    }
                    .table th, .table td {
                        border-bottom: 1px dashed #000;
                        padding: 5px 0;
                        text-align: center;
                        page-break-inside: avoid;
                    }
                    .table th {
                        border-top: 1px dashed #000;
                    }
                    .table .text-end {
                        text-align: right;
                    }
                    .roboto-mono {
                        font-family: 'Roboto Mono', monospace;
                    }
                    h4, h5, p {
                        margin: 5px 0;
                    }
                </style>
            </head>
            <body>
                <div class="receipt">
                    <div class="center-text">
                        <div style="display: flex; align-items: center; justify-content: center;">
                            <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                <h4><strong>SWEET AVENUE</strong></h4>
                                <h5><strong>COFFEE • BAKESHOP</strong></h5>
                            </div>
                        </div>
                        <br>
                        <p class="roboto-mono"><b>${new Date().toLocaleString()}</b></p>
                        <hr>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody class="roboto-mono">
                            ${orderDetails.items
                              .map(
                                (item) => `
                                <tr>
                                    <td>${capitalizeEachWord(item.productName)}</td>
                                    <td>${item.quantity}</td>
                                    <td>₱${item.price.toFixed(2)}</td>
                                </tr>
                            `
                              )
                              .join("")}
                        </tbody>
                    </table>
                    <hr>
                    <div class="roboto-mono">
                        <p><strong>Subtotal:</strong> ₱${orderDetails.subtotal.toFixed(2)}</p>
                        <p><strong>Discount Percentage:</strong> ${orderDetails.discountPercentage.toFixed(2)} %</p>
                        <p><strong>Discount Amount:</strong> - ₱${discountAmount.toFixed(2)}</p>
                        <p><strong>Grand Total:</strong> ₱${orderDetails.grandTotal.toFixed(2)}</p>
                        <p><strong>Tendered Amount:</strong> ₱${orderDetails.tenderedAmount.toFixed(2)}</p>
                        <p><strong>Change:</strong> ₱${change.toFixed(2)}</p>
                    </div>
                    <hr>
                    <p class="roboto-mono"><strong>Processed by: ${processedBy}</strong></p>
                    <p class="roboto-mono"><strong>Wifi Password: Sweet_Avenue123</strong></p><br><br>
                    <hr>
                    <div class="center-text roboto-mono">
                        <p>Thank you for your patronage. We’d love to see you again soon. You're always welcome here!</p>
                    </div>
                </div>
            </body>
        </html>
    `;
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, char => char.toUpperCase());
}

  // Calculate grand total
  function calculateGrandTotal() {
    const subtotal = parseFloat($("#subtotalValue").text().replace(/,/g, ""));
    let discount = parseFloat($("#discountInput").val()) || 0;

    if (isNaN(discount) || discount < 0 || discount > 20) {
      alert("Please enter a valid discount percentage (0-20).");
      $("#discountInput").val(0);
      discount = 0;
    }

    const grandTotal = (subtotal - subtotal * (discount / 100)).toFixed(2);
    $("#grandTotalValue").text(grandTotal);
  }

  function capitalizeEachWord(string) {
    return string.replace(/\b\w/g, (char) => char.toUpperCase());
  }

  function validateDiscountInput(event) {
    const input = event.target.value;

    // Remove non-digit characters
    const cleanedInput = input.replace(/[^\d]/g, "");

    // Reset input value
    event.target.value = cleanedInput;

    // Calculate grand total with the validated input
    calculateGrandTotal();
  }

  // Set discount to 0 if the input is blank on blur
  function resetDiscountIfBlank(event) {
    if (event.target.value === "") {
      event.target.value = 0;
      calculateGrandTotal();
    }
  }

  // Attach the validation function to the input event
  $("#discountInput").on("input", validateDiscountInput);

  // Attach the reset function to the blur event
  $("#discountInput").on("blur", resetDiscountIfBlank);

  // Initial calculation of the grand total
  calculateGrandTotal();

  // Tendered amount validation and change calculation
  function enforceTenDigits(event) {
    const input = event.target.value;
    if (input.length > 10) {
      event.target.value = input.slice(0, 10);
    }
  }

  function calculateChange() {
    const grandTotal = parseFloat($("#grandTotalValue").text());
    const tenderedAmount = parseFloat($("#tenderedAmount").val());

    if (isNaN(tenderedAmount)) {
      $("#changeDisplay").html(
        "<p style='color: red;'>Please enter a valid amount</p>"
      );
      return;
    }

    const change = tenderedAmount - grandTotal;
    if (change >= 0) {
      $("#changeDisplay").html(
        "<p><strong>Change:</strong> " + change.toFixed(2) + "</p>"
      );
    } else {
      $("#changeDisplay").html(
        "<p style='color: red;'>Insufficient amount tendered</p>"
      );
    }
  }

  $("#tenderedAmount")
    .on("input", enforceTenDigits)
    .on("input", calculateChange);

  // Remove order item
  $(document).on("click", ".remove-order", function (event) {
    event.preventDefault();
    const row = $(this).closest("tr");
    const productName = row.find(".text-capitalize").text().trim();

    $.post("remove_order.php", { product_name: productName }, function () {
      row.remove();
      window.location.reload();
    });
  });
});