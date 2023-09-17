$(document).ready(function() {
  $("#selectServerID").change(function() {
    var selectServerID = $("#selectServerID").val();
    $("#product-categories").css("display", "none");
    $("#c-loading").css("display", "block");
    if (selectServerID != null) {
      $.ajax({
        type: "POST",
        url: "/apps/dashboard/public/ajax/categories.php",
        data: {serverID: selectServerID},
        success: function(result) {
          $("#selectCategoryID").html(result);
          $("#product-categories").css("display", "block");
          $("#c-loading").css("display", "none");
        }
      });
    }
  });
  $("#selectDiscountStatus").change(function() {
    if ($(this).val() == 0) {
      $("#discountBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#discountBlock").css("display", "block");
    }
  });
  $("#selectMinecraftStatus").change(function() {
    if ($(this).val() == 0) {
      $("#minecraftBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#minecraftBlock").css("display", "block");
    }
  });
  $("#selectDiscountDurationStatus").change(function() {
    if ($(this).val() == 0 || $(this).val() == -1) {
      $("#discountDurationBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#discountDurationBlock").css("display", "block");
    }
  });
  $("#selectDurationStatus").change(function() {
    if ($(this).val() == 0 || $(this).val() == -1) {
      $("#durationBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#durationBlock").css("display", "block");
    }
  });
  $("#selectStockStatus").change(function() {
    if ($(this).val() == 0) {
      $("#stockBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#stockBlock").css("display", "block");
    }
  });
});
