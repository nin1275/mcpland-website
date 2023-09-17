$(document).ready(function() {
  $("#selectPaymentSettingsStatus").change(function() {
    if ($(this).val() == 0) {
      $("#paymentSettingsBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#paymentSettingsBlock").css("display", "block");
    }
  });
});
