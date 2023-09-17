$(document).ready(function() {
  var $switch = $("#recaptchaOptions .custom-switch .custom-control-input");
  $("#selectAnalyticsStatus").change(function() {
    if ($(this).val() == 0) {
      $("#analyticsOptions").css("display", "none");
    }
    else {
      $("#analyticsOptions").css("display", "block");
    }
  });
  $("#selectOneSignalStatus").change(function() {
    if ($(this).val() == 0) {
      $("#oneSignalOptions").css("display", "none");
    }
    else {
      $("#oneSignalOptions").css("display", "block");
    }
  });
  $("#selectTawktoStatus").change(function() {
    if ($(this).val() == 0) {
      $("#tawktoOptions").css("display", "none");
    }
    else {
      $("#tawktoOptions").css("display", "block");
    }
  });
  $("#selectBonusCreditStatus").change(function() {
    if ($(this).val() == 0) {
      $("#bonusCreditOptions").css("display", "none");
    }
    else {
      $("#bonusCreditOptions").css("display", "block");
    }
  });
  $("#selectRECAPTCHAStatus").change(function() {
    if ($(this).val() == 0) {
      $("#recaptchaOptions").css("display", "none");
      changeSwitch($switch, 0);
    }
    else {
      $("#recaptchaOptions").css("display", "block");
      changeSwitch($switch, 1);
    }
  });
});
