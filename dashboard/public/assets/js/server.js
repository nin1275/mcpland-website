$(document).ready(function() {
  $("#selectMinecraftStatus").change(function() {
    if ($(this).val() == 0) {
      $("#minecraftBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#minecraftBlock").css("display", "block");
    }
  });
});
