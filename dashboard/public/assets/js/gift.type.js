$(document).ready(function() {
  $("#selectGiftType").change(function() {
    $product = $("#selectProductID");
    $credit = $("#inputCredit");
    if ($(this).val() == 1) {
      $("#productBlock").css("display", "block");
      $("#creditBlock").css("display", "none");
      $product.removeAttr('disabled').select2();
      $credit.attr('disabled', 'disabled');
    }
    if ($(this).val() == 2) {
      $("#creditBlock").css("display", "block");
      $("#productBlock").css("display", "none");
      $product.attr('disabled', 'disabled').select2();
      $credit.removeAttr('disabled');
    }
  });
});
