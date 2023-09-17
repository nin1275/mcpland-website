$(document).ready(function() {
  var oldButton = null;
  $(".changeSeoPage").on("click", function() {
    var key = $(this).data("key");
    if (oldButton != null) {
      oldButton.removeClass("btn-primary");
      oldButton.addClass("btn-light");
      $("#seoPageBlock_" + oldButton.data("key")).css("display", "none");
    }
    $(this).removeClass("btn-light");
    $(this).addClass("btn-primary");
    $("#seoPageBlock_" + key).css("display", "block");
    $('[name="updateSeoSettings"]').css("display", "inline-block");
    oldButton = $(this);
  });
});
