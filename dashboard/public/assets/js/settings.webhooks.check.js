$("#testWebhookCredit").on("click", function() {
  var checkConnectSpinner = $("#testWebhookCredit .spinner-grow");

  var inputWebhookTitle = $("#inputWebhookCreditTitle").val();
  var inputWebhookColor = $("input[name='webhookCreditColor']").val();
  var inputWebhookURL = $("#inputWebhookCreditURL").val();
  var inputWebhookMessage = $("#inputWebhookCreditMessage").val();
  var inputWebhookEmbed = $("#inputWebhookCreditEmbed").val();
  var inputWebhookImage = $("#inputWebhookCreditImage").val();
  var checkboxWebhookAdStatus = ($("#checkboxWebhookCreditAdStatus").is(':checked') ? '1': '0');

  checkConnectSpinner.css("display", "inline-block");
  $("#testWebhookCredit a").text(lang.webhook_test_message_sending);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=webhook&category=credit",
    data: {webhookTitle: inputWebhookTitle, webhookColor: inputWebhookColor, webhookURL: inputWebhookURL, webhookMessage: inputWebhookMessage, webhookEmbed: inputWebhookEmbed, webhookImage: inputWebhookImage, webhookAdStatus: checkboxWebhookAdStatus},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookCredit a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          text: lang.alert_message_webhook_error,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookCredit a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.alert_message_webhook_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});

$("#testWebhookStore").on("click", function() {
  var checkConnectSpinner = $("#testWebhookStore .spinner-grow");

  var inputWebhookTitle = $("#inputWebhookStoreTitle").val();
  var inputWebhookColor = $("input[name='webhookStoreColor']").val();
  var inputWebhookURL = $("#inputWebhookStoreURL").val();
  var inputWebhookMessage = $("#inputWebhookStoreMessage").val();
  var inputWebhookEmbed = $("#inputWebhookStoreEmbed").val();
  var inputWebhookImage = $("#inputWebhookStoreImage").val();
  var checkboxWebhookAdStatus = ($("#checkboxWebhookStoreAdStatus").is(':checked') ? '1': '0');

  checkConnectSpinner.css("display", "inline-block");
  $("#testWebhookStore a").text(lang.webhook_test_message_sending);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=webhook&category=store",
    data: {webhookTitle: inputWebhookTitle, webhookColor: inputWebhookColor, webhookURL: inputWebhookURL, webhookMessage: inputWebhookMessage, webhookEmbed: inputWebhookEmbed, webhookImage: inputWebhookImage, webhookAdStatus: checkboxWebhookAdStatus},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookStore a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          text: lang.alert_message_webhook_error,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookStore a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.alert_message_webhook_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});

$("#testWebhookSupport").on("click", function() {
  var checkConnectSpinner = $("#testWebhookSupport .spinner-grow");

  var inputWebhookTitle = $("#inputWebhookSupportTitle").val();
  var inputWebhookColor = $("input[name='webhookSupportColor']").val();
  var inputWebhookURL = $("#inputWebhookSupportURL").val();
  var inputWebhookMessage = $("#inputWebhookSupportMessage").val();
  var inputWebhookEmbed = $("#inputWebhookSupportEmbed").val();
  var inputWebhookImage = $("#inputWebhookSupportImage").val();
  var checkboxWebhookAdStatus = ($("#checkboxWebhookSupportAdStatus").is(':checked') ? '1': '0');

  checkConnectSpinner.css("display", "inline-block");
  $("#testWebhookSupport a").text(lang.webhook_test_message_sending);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=webhook&category=support",
    data: {webhookTitle: inputWebhookTitle, webhookColor: inputWebhookColor, webhookURL: inputWebhookURL, webhookMessage: inputWebhookMessage, webhookEmbed: inputWebhookEmbed, webhookImage: inputWebhookImage, webhookAdStatus: checkboxWebhookAdStatus},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookSupport a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          text: lang.alert_message_webhook_error,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookSupport a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.alert_message_webhook_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});

$("#testWebhookNews").on("click", function() {
  var checkConnectSpinner = $("#testWebhookNews .spinner-grow");

  var inputWebhookTitle = $("#inputWebhookNewsTitle").val();
  var inputWebhookColor = $("input[name='webhookNewsColor']").val();
  var inputWebhookURL = $("#inputWebhookNewsURL").val();
  var inputWebhookMessage = $("#inputWebhookNewsMessage").val();
  var inputWebhookEmbed = $("#inputWebhookNewsEmbed").val();
  var inputWebhookImage = $("#inputWebhookNewsImage").val();
  var checkboxWebhookAdStatus = ($("#checkboxWebhookNewsAdStatus").is(':checked') ? '1': '0');

  checkConnectSpinner.css("display", "inline-block");
  $("#testWebhookNews a").text(lang.webhook_test_message_sending);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=webhook&category=news",
    data: {webhookTitle: inputWebhookTitle, webhookColor: inputWebhookColor, webhookURL: inputWebhookURL, webhookMessage: inputWebhookMessage, webhookEmbed: inputWebhookEmbed, webhookImage: inputWebhookImage, webhookAdStatus: checkboxWebhookAdStatus},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookNews a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          text: lang.alert_message_webhook_error,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookNews a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.alert_message_webhook_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});

$("#testWebhookLottery").on("click", function() {
  var checkConnectSpinner = $("#testWebhookLottery .spinner-grow");

  var inputWebhookTitle = $("#inputWebhookLotteryTitle").val();
  var inputWebhookColor = $("input[name='webhookLotteryColor']").val();
  var inputWebhookURL = $("#inputWebhookLotteryURL").val();
  var inputWebhookMessage = $("#inputWebhookLotteryMessage").val();
  var inputWebhookEmbed = $("#inputWebhookLotteryEmbed").val();
  var inputWebhookImage = $("#inputWebhookLotteryImage").val();
  var checkboxWebhookAdStatus = ($("#checkboxWebhookLotteryAdStatus").is(':checked') ? '1': '0');

  checkConnectSpinner.css("display", "inline-block");
  $("#testWebhookLottery a").text(lang.webhook_test_message_sending);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=webhook&category=lottery",
    data: {webhookTitle: inputWebhookTitle, webhookColor: inputWebhookColor, webhookURL: inputWebhookURL, webhookMessage: inputWebhookMessage, webhookEmbed: inputWebhookEmbed, webhookImage: inputWebhookImage, webhookAdStatus: checkboxWebhookAdStatus},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookLottery a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          text: lang.alert_message_webhook_error,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookLottery a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.alert_message_webhook_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});

$("#testWebhookApplication").on("click", function() {
  var checkConnectSpinner = $("#testWebhookApplication .spinner-grow");
  
  var inputWebhookTitle = $("#inputWebhookApplicationTitle").val();
  var inputWebhookColor = $("input[name='webhookApplicationColor']").val();
  var inputWebhookURL = $("#inputWebhookApplicationURL").val();
  var inputWebhookMessage = $("#inputWebhookApplicationMessage").val();
  var inputWebhookEmbed = $("#inputWebhookApplicationEmbed").val();
  var inputWebhookImage = $("#inputWebhookApplicationImage").val();
  var checkboxWebhookAdStatus = ($("#checkboxWebhookApplicationAdStatus").is(':checked') ? '1': '0');
  
  checkConnectSpinner.css("display", "inline-block");
  $("#testWebhookApplication a").text(lang.webhook_test_message_sending);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=webhook&category=application",
    data: {webhookTitle: inputWebhookTitle, webhookColor: inputWebhookColor, webhookURL: inputWebhookURL, webhookMessage: inputWebhookMessage, webhookEmbed: inputWebhookEmbed, webhookImage: inputWebhookImage, webhookAdStatus: checkboxWebhookAdStatus},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookApplication a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          text: lang.alert_message_webhook_error,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        $("#testWebhookApplication a").text(lang.webhook_test_message_send);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.alert_message_webhook_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});
