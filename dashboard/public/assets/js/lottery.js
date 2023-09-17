$(document).ready(function() {
  $("#selectPriceStatus").change(function() {
    if ($(this).val() == 0) {
      $("#priceOptions").css("display", "none");
      $("#durationOptions").css("display", "block");
    }
    if ($(this).val() == 1) {
      $("#durationOptions").css("display", "none");
      $("#priceOptions").css("display", "block");
    }
  });

  $("select[name='lotteryAwardType[]']").change(function() {
    $input = $(this).parents('tr').find(".variableData .creditData");
    $select = $(this).parents('tr').find(".variableData .productData");
    $pas = $(this).parents('tr').find(".variableData .pas");
    if ($(this).val() == 1) {
      $input.css('display', 'block').find("input").removeAttr('disabled');
      $select.css('display', 'none').find("select").attr('disabled', 'disabled');
      $pas.css('display', 'none').find("input").attr('disabled', 'disabled');
    }
    if ($(this).val() == 2) {
      $input.css('display', 'none').find("input").attr('disabled', 'disabled');
      $pas.css('display', 'none').find("input").attr('disabled', 'disabled');
      $select.css('display', 'block').find("select").removeAttr('disabled');

    }
    if ($(this).val() == 3) {
      $input.css('display', 'none').find("input").attr('disabled', 'disabled');
      $select.css('display', 'none').find("select").attr('disabled', 'disabled');
      $pas.css('display', 'block').find("input").removeAttr('disabled');
    }
  });
});
