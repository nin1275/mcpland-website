$(document).ready(function() {
  swal.fire({
    type: "warning",
    title: lang.alert_title_warning,
    html: alertText,
    confirmButtonColor: "#02b875",
    confirmButtonText: lang.alert_btn_ok,
  });
});
