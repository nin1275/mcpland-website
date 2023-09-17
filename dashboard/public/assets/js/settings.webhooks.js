$(document).ready(function() {
  $("#selectWebhookCreditStatus").change(function() {
    if ($(this).val() == 0) {
      $("#webhookCreditOptions").css("display", "none");
    }
    else {
      $("#webhookCreditOptions").css("display", "block");
    }
  });
  $("#selectWebhookStoreStatus").change(function() {
    if ($(this).val() == 0) {
      $("#webhookStoreOptions").css("display", "none");
    }
    else {
      $("#webhookStoreOptions").css("display", "block");
    }
  });
  $("#selectWebhookSupportStatus").change(function() {
    if ($(this).val() == 0) {
      $("#webhookSupportOptions").css("display", "none");
    }
    else {
      $("#webhookSupportOptions").css("display", "block");
    }
  });
  $("#selectWebhookNewsStatus").change(function() {
    if ($(this).val() == 0) {
      $("#webhookNewsOptions").css("display", "none");
    }
    else {
      $("#webhookNewsOptions").css("display", "block");
    }
  });
  $("#selectWebhookLotteryStatus").change(function() {
    if ($(this).val() == 0) {
      $("#webhookLotteryOptions").css("display", "none");
    }
    else {
      $("#webhookLotteryOptions").css("display", "block");
    }
  });
  $("#selectWebhookApplicationStatus").change(function() {
    if ($(this).val() == 0) {
      $("#webhookApplicationOptions").css("display", "none");
    }
    else {
      $("#webhookApplicationOptions").css("display", "block");
    }
  });
});
