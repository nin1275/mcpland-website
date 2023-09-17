$(document).ready(function() {
  $("#selectStoreDiscountStatus").change(function() {
    if ($(this).val() == 0) {
      $("#storeDiscountBlock").css("display", "none");
    }
    else {
      $("#storeDiscountBlock").css("display", "block");
    }
  });
  $("#selectStoreDiscountDurationStatus").change(function() {
    if ($(this).val() == 0) {
      $("#storeDiscountDurationBlock").css("display", "none");
    }
    else {
      $("#storeDiscountDurationBlock").css("display", "block");
    }
  });
  $("#selectStoreDiscountProductsStatus").change(function() {
    if ($(this).val() == 0) {
      $("#storeDiscountProductsBlock").css("display", "none");
    }
    else {
      $("#storeDiscountProductsBlock").css("display", "block");
    }
  });
});
