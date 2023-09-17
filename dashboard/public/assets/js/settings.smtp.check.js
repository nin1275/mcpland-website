var checkConnectSpinner = $("#testSMTP .spinner-grow");
var checkConnectButton = $("#testSMTP span");

checkConnectButton.on("click", function() {
  var inputSMTPServer = $("#inputSMTPServer").val();
  var inputSMTPPort = $("#inputSMTPPort").val();
  var inputSMTPUsername = $("#inputSMTPUsername").val();
  var inputSMTPPassword = $("#inputSMTPPassword").val();
  var selectSMTPSecure = $("#selectSMTPSecure").val();

  checkConnectSpinner.css("display", "inline-block");
  checkConnectButton.text(lang.smtp_btn_checking);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=connect&category=smtp",
    data: {smtpServer: inputSMTPServer, smtpPort: inputSMTPPort, smtpSecure: selectSMTPSecure, smtpUsername: inputSMTPUsername, smtpPassword: inputSMTPPassword},
    success: function(result) {
      if (result == 'true') {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text(lang.smtp_btn_check);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.smtp_connection_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text(lang.smtp_btn_check);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          html: "<p>" + lang.smtp_connection_error + result + "</p>",
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});
