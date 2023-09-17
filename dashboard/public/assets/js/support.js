$(document).ready(function() {
  $("#selectAnswer").change(function() {
    var content = $(this).val();
    $('#textareaMessage').froalaEditor("html.set", content);
  });
  var element = $("#messagesBox");
  element.scrollTop(element.prop("scrollHeight"));
});
