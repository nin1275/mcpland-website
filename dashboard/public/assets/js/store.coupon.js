$(document).ready(function() {
  $("#selectDurationStatus").change(function() {
    if ($(this).val() == 0) {
      $("#durationBlock").css("display", "none");
    }
    else {
      $("#durationBlock").css("display", "block");
    }
  });
  $("#selectPieceStatus").change(function() {
    if ($(this).val() == 0) {
      $("#pieceBlock").css("display", "none");
    }
    else {
      $("#pieceBlock").css("display", "block");
    }
  });
  $("#selectProductsStatus").change(function() {
    if ($(this).val() == 0) {
      $("#productsBlock").css("display", "none");
    }
    else {
      $("#productsBlock").css("display", "block");
    }
  });
});
