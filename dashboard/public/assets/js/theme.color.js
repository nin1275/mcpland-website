$(document).ready(function() {
  $("#selectColor").change(function() {
    if ($(this).val() == 0) {
      $("#colorSettingsBlock").css("display", "block");
      $("#colorSettingsButton").css("display", "inline-block");
    }
    else {
      $("#colorSettingsBlock").css("display", "none");
      $("#colorSettingsButton").css("display", "none");
    }
  });
  $("#colorSettingsButton").on("click", function() {
    var dialog = confirm(lang.theme_set_default_color_warning);
    if (dialog == true) {
      var extraColors = jQuery.parseJSON($("input[name='extraColors']").val());
      $.each(extraColors, function(key, value) {
        $("input[name='" + key + "']").val(value).parent("[data-toggle='colorPicker']").colorpicker('setValue', value);
      });
    }
  });
});
