$(document).ready(function() {
  $("#selectServerID").change(function() {
    var selectServerID = $("#selectServerID").val();
    $("#c-loading").css("display", "block");
    $("#c-loading2").css("display", "block");
    $("#selectVIP").css("display", "none");
    $("#selectCategoryID").css("display", "none");
    if (selectServerID != null) {
      $.ajax({
        type: "POST",
        url: "/apps/dashboard/public/ajax/server-vips.php",
        data: {serverID: selectServerID},
        success: function(result) {
          result = JSON.parse(result);
          $("#selectVIP").html(result.vips).css("display", "block");
          $("#selectCategoryID").html(result.categories).css("display", "block");
          $("#c-loading").css("display", "none");
          $("#c-loading2").css("display", "none");
        }
      });
    }
  });
});


