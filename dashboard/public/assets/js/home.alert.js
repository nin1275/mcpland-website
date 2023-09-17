$(document).ready(function() {
  swal.fire({
    type: "error",
    title: lang.alert_title_error,
    text: alertText,
    confirmButtonColor: "#e63757",
    confirmButtonText: lang.alert_btn_ok,
    allowEscapeKey: false,
    allowOutsideClick: false
  }).then(function() {
    window.location = alertLocation;
  });
});
