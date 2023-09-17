var buyModal = $("#buyModal");
var alertCoupon = $("#alertCoupon");
var inputCoupon = $("#inputCoupon");
var discount = $("#discount");
var subtotal = $("#subtotal");
var addCouponButton = $("#addCouponButton");
var deleteCouponButton = $("#deleteCouponButton");
var checkoutButton = $("#checkoutButton");
var checkoutItemCopy = $("#checkoutItemCopy");
var emptyShoppingCart = $("#emptyShoppingCart");

$.ajax({
  type: "POST",
  url: "/apps/main/public/ajax/shopping-cart.php",
  success: function(result) {
    if (result == "error_login") {
      swal.fire({
        title: lang.alert_title_error,
        text: lang.alert_message_login,
        type: "error",
        confirmButtonColor: "#02b875",
        confirmButtonText: lang.alert_btn_ok
      });
    }
    else {
      var data = JSON.parse(result);
      if (data.items != null && data.items.length > 0) {
        for (var i = 0; i < data.items.length; i++) {
          var price = (data.items[i].discountedPrice === 0) ? data.items[i].price : data.items[i].discountedPrice;
          var item = checkoutItemCopy.clone().appendTo("#checkoutItems");
          item.find(".checkoutItemName").text(data.items[i].name);
          item.find(".checkoutItemPrice").text(price);
          item.find(".checkoutItemQuantity input").val(data.items[i].quantity);
          item.find(".checkoutItemTotal").text((data.items[i].quantity * price));
          item.find(".checkoutItemButtons > .openBuyModal").attr("product-id", data.items[i].id);
          item.find(".checkoutItemButtons > .removeItemButton").attr("product-id", data.items[i].id);
          item.find(".checkoutItemQuantity .cartItemAmountUpdateInput").attr("product-id", data.items[i].id);
          item.find(".checkoutItemQuantity .cartItemDecreaseButton").attr("product-id", data.items[i].id);
          item.find(".checkoutItemQuantity .cartItemIncreaseButton").attr("product-id", data.items[i].id);
          item.find(".checkoutItemQuantity input").attr("data-max", data.items[i].stock);
          item.css("display", "table-row");
        }
      }
      
      updateSummary(data.subtotal, data.discount);
  
      $("#shoppingCartTableLoader").removeClass("shopping-cart-loading");
      $(".shoppingCartSpinner").css("display", "none");
      inputCoupon.prop("disabled", false);
      addCouponButton.css("pointer-events", "initial");
  
      if (data.coupon != null) {
        setCoupon(data.coupon);
      }
      
      if (data.items.length == 0) {
        $("#shoppingCartTable").css("display", "none");
        emptyShoppingCart.css("display", "block");
      }
      else {
        $("#shoppingCartTable").css("display", "block");
      }
      $("#shoppingCartSummary").css("display", "block");
      openBuyModal();
      removeItem();
      initUpdateCartItem();
    }
  }
});

addCouponButton.on("click", function() {
  if (inputCoupon.val() === "") return false;
  $.ajax({
    type: "POST",
    url: "/apps/main/public/ajax/shopping-cart.php?action=setCoupon&coupon=" + inputCoupon.val(),
    success: function(result) {
      if (result == "error_coupon_not_found") {
        inputCoupon.css("border-color", "red");
        alertCoupon.attr("class", "form-text text-danger").text(lang.alert_message_checkout_coupon_invalid).css("display", "block");
      }
      else if (result == "error_coupon_no_product") {
        inputCoupon.css("border-color", "red");
        alertCoupon.attr("class", "form-text text-danger").text(lang.alert_message_checkout_coupon_invalid_for_cart).css("display", "block");
      }
      else if (result == "error_coupon_used") {
        inputCoupon.css("border-color", "red");
        alertCoupon.attr("class", "form-text text-danger").text(lang.alert_message_checkout_coupon_used).css("display", "block");
      }
      else if (result == "error_coupon_limit") {
        inputCoupon.css("border-color", "red");
        alertCoupon.attr("class", "form-text text-danger").text(lang.alert_message_checkout_coupon_expired).css("display", "block");
      }
      else if (result.startsWith("error_coupon_min_payment|")) {
        var minPayment = result.split("|")[1];
        inputCoupon.css("border-color", "red");
        alertCoupon.attr("class", "form-text text-danger").text(lang.alert_message_checkout_coupon_price.replace('%amount%', minPayment)).css("display", "block");
      }
      else if (result == "error_login") {
        inputCoupon.css("border-color", "red");
        alertCoupon.attr("class", "form-text text-danger").text(lang.alert_message_checkout_coupon_login).css("display", "block");
      }
      else {
        var data = JSON.parse(result);
        setCoupon(data.coupon);
        updateSummary(data.subtotal, data.discount);
      }
    }
  });
});

deleteCouponButton.on("click", function() {
  $.ajax({
    type: "POST",
    url: "/apps/main/public/ajax/shopping-cart.php?action=removeCoupon",
    success: function(result) {
      var data = JSON.parse(result);
      removeCoupon();
      updateSummary(data.subtotal, data.discount);
    }
  });
});

checkoutButton.on("click", function() {
  var button = $(this);
  button.css("pointer-events", "none");
  $.ajax({
    type: "POST",
    url: "/apps/main/public/ajax/shopping-cart.php?action=pay",
    success: function(result) {
      if (result == "error_credit") {
        swal.fire({
          title: lang.alert_title_error,
          text: lang.alert_message_checkout_credit_error,
          type: "error",
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        }).then(function() {
          window.location = '/credit/buy';
        });
      }
      else if (result == "error_empty") {
        swal.fire({
          title: lang.alert_title_error,
          text: lang.alert_message_checkout_cart_empty_error,
          type: "error",
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        swal.fire({
          title: lang.alert_title_success,
          text: lang.alert_message_checkout_success,
          type: "success",
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        }).then(function() {
          window.location = '/chest';
        });
      }
    },
    complete: function() {
      button.css("pointer-events", "auto");
    }
  });
});

function updateSummary(subtotalPrice, discountedPrice) {
  if (discountedPrice == 0) {
    $("#discountBlock").css("display", "none");
    discount.text(0);
  }
  else {
    $("#discountBlock").css("display", "flex");
    discount.text("-" + lang.credits.replace('%credit%', discountedPrice)).css("display", "block");
  }
  subtotal.html(lang.credits.replace('%credit%', subtotalPrice));
}

function removeItem() {
  var removeItemButton = $(".removeItemButton");
  
  removeItemButton.on("click", function() {
    var productID = $(this).attr("product-id");
    var item = $(this).parents("tr");
    $.ajax({
      type: "GET",
      url: "/apps/main/public/ajax/shopping-cart.php?action=remove&productID=" + productID,
      success: function(result) {
        var data = JSON.parse(result);
        if (data.items.length == 0) {
          $("#shoppingCartTable").css("display", "none");
          emptyShoppingCart.css("display", "block");
        }
        if (data.coupon == null) {
          removeCoupon();
        }
        updateSummary(data.subtotal, data.discount);
        $(".shopping-cart-count").text(data.items.length);
        item.remove();
      }
    });
  });
}

function setCoupon(coupon) {
  inputCoupon.val(coupon);
  inputCoupon.prop("readonly", true);
  inputCoupon.css("border-color", "");
  alertCoupon.attr("class", "form-text text-success").text(lang.alert_message_checkout_coupon_success).css("display", "block");
  addCouponButton.css("display", "none");
  deleteCouponButton.attr("class", "btn btn-danger deleteCouponButton").text(lang.remove).css("display", "block");
}

function removeCoupon() {
  inputCoupon.val("");
  inputCoupon.prop("readonly", false);
  inputCoupon.css("border-color", "");
  alertCoupon.attr("class", null).text(null).css("display", "none");
  addCouponButton.css("display", "block");
  deleteCouponButton.css("display", "none");
}

function initUpdateCartItem() {
  var cartItemDecreaseButton = $(".cartItemDecreaseButton");
  var cartItemIncreaseButton = $(".cartItemIncreaseButton");
  var cartItemAmountUpdateInput = $(".cartItemAmountUpdateInput");
  
  cartItemAmountUpdateInput.on("input", function(event) {
    var cartItemQuantity = $(this);
    var cartItemQuantitySpinner = $(this).parents(".checkoutItemQuantity").find(".checkoutItemQuantitySpinner");
    var cartItemQuantityButtons = $(this).parents(".checkoutItemQuantity").find(".btn");
    var cartItemTotal = $(this).parents("tr").find(".checkoutItemTotal");
    var cartItemPrice = $(this).parents("tr").find(".checkoutItemPrice");
    var cartItemQuantityValue = parseInt(cartItemQuantity.val());
    var cartItemQuantityMaxValue = parseInt(cartItemQuantity.attr("data-max"));
    var productID = cartItemQuantity.attr("product-id");
    if (cartItemQuantityValue > 0) {
      if (cartItemQuantityMaxValue >= cartItemQuantityValue || cartItemQuantityMaxValue === -1) {
        cartItemQuantity.css("display", "none");
        cartItemQuantitySpinner.css("display", "block");
        cartItemQuantityButtons.css("pointer-events", "none");
  
        $.ajax({
          type: "GET",
          url: "/apps/main/public/ajax/shopping-cart.php?action=update&productID=" + productID + "&quantity=" + cartItemQuantityValue,
          success: function(result) {
            if (result == "error_product") {
              swal.fire({
                title: lang.alert_title_error,
                text: lang.alert_message_not_found,
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: lang.alert_btn_ok
              });
            }
            else if (result == "error_value") {
              swal.fire({
                title: lang.alert_title_error,
                text: lang.alert_message_checkout_quantity_error,
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: lang.alert_btn_ok
              });
            }
            else if (result == "error_stock") {
              swal.fire({
                title: lang.alert_title_error,
                text: lang.lang.alert_message_checkout_stock_error2,
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: lang.alert_btn_ok
              });
            }
            else {
              var data = JSON.parse(result);
              cartItemTotal.text(parseInt(cartItemPrice.text()) * cartItemQuantityValue);
              updateSummary(data.subtotal, data.discount);
            }
          },
          complete: function() {
            cartItemQuantity.css("display", "block");
            cartItemQuantitySpinner.css("display", "none");
            cartItemQuantityButtons.css("pointer-events", "auto");
          }
        });
      }
      else {
        swal.fire({
          title: lang.alert_title_error,
          text: lang.lang.alert_message_checkout_stock_error2,
          type: "error",
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
        event.preventDefault();
      }
    }
    else {
      event.preventDefault();
    }
  });
  
  cartItemDecreaseButton.on("click", function() {
    var cartItemQuantity = $(this).parents(".checkoutItemQuantity").find("input");
    var cartItemQuantityValue = parseInt(cartItemQuantity.val());
    if (cartItemQuantityValue > 1) {
      cartItemQuantity.val(cartItemQuantityValue - 1).trigger('input');
    }
  });
  
  cartItemIncreaseButton.on("click", function() {
    var cartItemQuantity = $(this).parents(".checkoutItemQuantity").find("input");
    var cartItemQuantityValue = parseInt(cartItemQuantity.val());
    var cartItemQuantityMaxValue = parseInt(cartItemQuantity.attr("data-max"));
    var cartItemQuantityNewValue = cartItemQuantityValue + 1;
    if (cartItemQuantityMaxValue >= cartItemQuantityNewValue || cartItemQuantityMaxValue === -1) {
      cartItemQuantity.val(cartItemQuantityNewValue).trigger('input');;
    }
    else {
      swal.fire({
        title: lang.alert_title_error,
        text: lang.alert_message_checkout_stock_error,
        type: "error",
        confirmButtonColor: "#02b875",
        confirmButtonText: lang.alert_btn_ok
      });
    }
  });
}