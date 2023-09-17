$(document).ready(function() {
  $("#selectServerID").change(function() {
    var selectServerID = $("#selectServerID").val();
    $("#products").css("display", "none");
    $("#c-loading").css("display", "block");
    if (selectServerID != null) {
      $.ajax({
        type: "POST",
        url: "/apps/dashboard/public/ajax/gift.products.php",
        data: {serverID: selectServerID},
        success: function(result) {
          $("#selectProductID").html(result);
          $("#products").css("display", "block");
          $("#c-loading").css("display", "none");
        }
      });
    }
  });
});
