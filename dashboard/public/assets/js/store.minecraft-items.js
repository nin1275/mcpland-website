function formatState (state) {
  if (!state.id) {
    return state.text;
  }
  if ($(state.element).attr("icon") != undefined) {
    var baseUrl = "/apps/main/public/assets/img/items";
    var $state = $(
      '<span><img src="' + baseUrl + '/' + $(state.element).attr("icon") + '.png" style="margin-right: 6px; width: 32px;" /> ' + state.text + '</span>'
    );
  }
  else {
    var $state = $(
      '<span>' + state.text + '</span>'
    );
  }
  
  return $state;
};

$(document).ready(function() {
  var $url = "/apps/dashboard/public/ajax/minecraft-items.php";
  if ($("#product-minecraftitems").data("type") == "update") {
    $url += "?selectedMinecraftItem=" + $("#product-minecraftitems").data("selected");
  }
  $.ajax({
    type: "GET",
    url: $url,
    success: function(result) {
      $("#selectMinecraftItem").html(result);
      $("#selectMinecraftItem").select2({
        templateResult: formatState
      });
      $("#product-minecraftitems").css("display", "block");
      $("#mi-loading").css("display", "none");
    }
  });
});
