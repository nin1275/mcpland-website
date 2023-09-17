$(document).ready(function() {
  $("#selectHeaderLogoType").change(function() {
    if ($(this).val() == 1) {
      $("#headerLogoOptions").css("display", "none");
    }
    else if ($(this).val() == 2) {
      $("#headerLogoOptions").css("display", "block");
    }
    else {
      $("#headerLogoOptions").css("display", "none");
    }
  });
});
