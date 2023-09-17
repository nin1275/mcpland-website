$(document).ready(function() {
  $('#selectPermission').on('select2:unselecting', function (e) {
    if (e.params.args.data.disabled === true) {
      e.preventDefault();
    }
  });
  
  $("#selectPermission").change(function() {
    $("#extraPermBlock").css("display", "block");
    $('#extraPermBlock input[id^="permission_"]').prop("checked", false).prop("disabled", false);
    var selected = [];
    for (var option of document.getElementById('selectPermission').options) {
      if (option.selected) {
        selected.push(option);
      }
    }
    for (var select of selected) {
      var permissions = String($(select).data("permissions"));
      if (permissions !== "" && permissions !== undefined) {
        permissions = permissions.split(",");
        permissions.forEach(function(permission) {
          $("#permission_" + permission).prop("checked", true).prop("disabled", true);
        });
      }
    }
  
    var extraPermissions = String($("#accountExtraPermissions").val());
    if (extraPermissions !== "" && extraPermissions !== undefined) {
      extraPermissions = extraPermissions.split(",");
      extraPermissions.forEach(function(permission) {
        $("#permission_" + permission).prop("checked", true);
      });
    }
  });
});