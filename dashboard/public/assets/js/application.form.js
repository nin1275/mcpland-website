$(document).ready(function() {
  $("select[name='formQuestionType[]']").change(function() {
    $selectData = $(this).parents('tr').find(".variableData .selectData");
    $textData = $(this).parents('tr').find(".variableData .textData");
    if ($(this).val() == 1 || $(this).val() == 2) {
      $textData.css('display', 'block').find("input").removeAttr('disabled');
      $selectData.css('display', 'none').find("input").attr('disabled', 'disabled');
    }
    else {
      $textData.css('display', 'none').find("input").attr('disabled', 'disabled');
      $selectData.css('display', 'block').find("input").removeAttr('disabled');
    }
  });
});
