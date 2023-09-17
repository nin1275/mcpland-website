$("#selectCategory").change(function() {
  var template = $(this).find('option:selected').data('template');
  ckeditor.setData(template);
});