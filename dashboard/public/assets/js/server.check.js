var checkConnectSpinner = $("#checkConnect .spinner-grow");
var checkConnectButton = $("#checkConnect a");

checkConnectButton.on("click", function() {
  var inputIP = $("#inputIP").val();
  var selectConsoleID = $("#selectConsoleID").val();
  var inputConsolePort = $("#inputConsolePort").val();
  var inputConsolePassword = $("#inputConsolePassword").val();

  checkConnectSpinner.css("display", "inline-block");
  checkConnectButton.text(lang.server_checking);
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=connect&category=console",
    data: {serverIP: inputIP, consoleID: selectConsoleID, consolePort: inputConsolePort, consolePassword: inputConsolePassword},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text(lang.server_check);
        swal.fire({
          type: "error",
          title: lang.alert_title_error,
          text: lang.server_connection_error,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text(lang.server_check);
        swal.fire({
          type: "success",
          title: lang.alert_title_success,
          text: lang.server_connection_success,
          confirmButtonColor: "#02b875",
          confirmButtonText: lang.alert_btn_ok
        });
      }
    }
  });
});
