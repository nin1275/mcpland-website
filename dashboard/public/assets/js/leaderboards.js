$("#selectMySQLServerStatus").change(function() {
  if ($(this).val() == 0) {
    $("#mysqlServerInfo").css("display", "none");
  }
  else {
    $("#mysqlServerInfo").css("display", "block");
  }
});

var checkConnectSpinner = $("#checkConnect .spinner-grow");
var checkConnectButton = $("#checkConnect a");

checkConnectButton.on("click", function() {
  var inputMySQLServer = $("#inputMySQLServer").val();
  var inputMySQLPort = $("#inputMySQLPort").val();
  var inputMySQLUsername = $("#inputMySQLUsername").val();
  var inputMySQLPassword = $("#inputMySQLPassword").val();
  var inputMySQLDatabase = $("#inputMySQLDatabase").val();

  checkConnectSpinner.css("display", "inline-block");
  checkConnectButton.text("Veritabanı bağlantısı kontrol ediliyor...");
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=connect&category=mysql",
    data: {mysqlServer: inputMySQLServer, mysqlPort: inputMySQLPort, mysqlUsername: inputMySQLUsername, mysqlPassword: inputMySQLPassword, mysqlDatabase: inputMySQLDatabase},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text("Veritabanı Bağlantısını Kontrol Et");
        swal.fire({
          type: "error",
          title: "HATA!",
          text: "Veritabanı bağlantısı kurulamadı!",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text("Veritabanı Bağlantısını Kontrol Et");
        swal.fire({
          type: "success",
          title: "BAŞARILI!",
          text: "Veritabanı bağlantısı kuruldu!",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
    }
  });
});
