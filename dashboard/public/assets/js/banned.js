$(document).ready(function() {
  $("#selectDurationStatus").change(function() {
    if ($(this).val() == 0) {
      $("#durationBlock").css("display", "none");
    }
    else {
      $("#durationBlock").css("display", "block");
    }
  });
});
