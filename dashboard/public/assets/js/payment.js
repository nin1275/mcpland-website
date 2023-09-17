$(document).ready(function() {
  $("#selectBonusCreditStatus").change(function() {
    if ($(this).val() == 0) {
      $("#bonusCreditOptions").css("display", "none");
    }
    else {
      $("#bonusCreditOptions").css("display", "block");
    }
  });
  checkAPI();
  $("#selectAPIID").on('change', checkAPI);
});

var checkAPI = function() {
  var selectAPIID = $("#selectAPIID").val();
  if (selectAPIID == "batihost") {
    $('#selectType option[value="1"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "paywant") {
    $('#selectType option[value="1"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected');
    $('#selectType option[value="3"]').removeAttr('disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "rabisu") {
    $('#selectType option[value="1"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "shopier") {
    $('#selectType option[value="1"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "keyubu") {
    $('#selectType option[value="1"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "ininal") {
    $('#selectType option[value="1"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "papara") {
    $('#selectType option[value="1"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "shipy") {
    $('#selectType option[value="1"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected');
    $('#selectType option[value="3"]').removeAttr('disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "eft") {
    $('#selectType option[value="1"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="2"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="3"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "paytr") {
    $('#selectType option[value="1"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "slimmweb") {
    $('#selectType option[value="1"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="2"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "stripe") {
    $('#selectType option[value="1"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
  if (selectAPIID == "paypal") {
    $('#selectType option[value="1"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType option[value="2"]').removeAttr('disabled').removeAttr('selected').attr('selected', 'selected');
    $('#selectType option[value="3"]').attr('disabled', 'disabled').removeAttr('selected');
    $('#selectType').select2();
  }
};
