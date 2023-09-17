$(document).ready(function() {
  $("#selectPageTypeList").change(function() {
    var $form = $("#formNestable");
    var $iconpicker = $('[data-toggle="iconpicker"]');
    if ($(this).val() == "custom") {
      $form.find('input[name="pagetype"]').val("custom");
      $form.find('input[name="title"]').val(null);
      $form.find('input[name="icon"]').val(null);
      $form.find('input[name="url"]').val(null);
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "empty");
    }
    else if ($(this).val() == "home") {
      $form.find('input[name="pagetype"]').val("home");
      $form.find('input[name="title"]').val("Home");
      $form.find('input[name="icon"]').val("fa fa-home");
      $form.find('input[name="url"]').val("/");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-home");
    }
    else if ($(this).val() == "store") {
      $form.find('input[name="pagetype"]').val("store");
      $form.find('input[name="title"]').val("Store");
      $form.find('input[name="icon"]').val("fa fa-shopping-cart");
      $form.find('input[name="url"]').val("/store");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-shopping-cart");
    }
    else if ($(this).val() == "games") {
      $form.find('input[name="pagetype"]').val("games");
      $form.find('input[name="title"]').val("Games");
      $form.find('input[name="icon"]').val("fa fa-gamepad");
      $form.find('input[name="url"]').val("/games");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-gamepad");
    }
    else if ($(this).val() == "lottery") {
      $form.find('input[name="pagetype"]').val("lottery");
      $form.find('input[name="title"]').val("Wheel of Fortune");
      $form.find('input[name="icon"]').val("fa fa-pie-chart");
      $form.find('input[name="url"]').val("/fortune-wheel");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-pie-chart");
    }
    else if ($(this).val() == "credit") {
      $form.find('input[name="pagetype"]').val("credit");
      $form.find('input[name="title"]').val("Credit");
      $form.find('input[name="icon"]').val("fa fa-dollar");
      $form.find('input[name="url"]').val("/credit");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-dollar");
    }
    else if ($(this).val() == "credit-buy") {
      $form.find('input[name="pagetype"]').val("credit");
      $form.find('input[name="title"]').val("Buy Credits");
      $form.find('input[name="icon"]').val("fa fa-dollar");
      $form.find('input[name="url"]').val("/credit/buy");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-dollar");
    }
    else if ($(this).val() == "credit-send") {
      $form.find('input[name="pagetype"]').val("credit");
      $form.find('input[name="title"]').val("Send Credit");
      $form.find('input[name="icon"]').val("fa fa-dollar");
      $form.find('input[name="url"]').val("/credit/send");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-dollar");
    }
    else if ($(this).val() == "leaderboards") {
      $form.find('input[name="pagetype"]').val("leaderboards");
      $form.find('input[name="title"]').val("Leaderboards");
      $form.find('input[name="icon"]').val("fa fa-trophy");
      $form.find('input[name="url"]').val("/leaderboards");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-trophy");
    }
    else if ($(this).val() == "support") {
      $form.find('input[name="pagetype"]').val("support");
      $form.find('input[name="title"]').val("Support");
      $form.find('input[name="icon"]').val("fa fa-life-ring");
      $form.find('input[name="url"]').val("/support");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-life-ring");
    }
    else if ($(this).val() == "chest") {
      $form.find('input[name="pagetype"]').val("chest");
      $form.find('input[name="title"]').val("Chest");
      $form.find('input[name="icon"]').val("fa fa-archive");
      $form.find('input[name="url"]').val("/chest");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-archive");
    }
    else if ($(this).val() == "download") {
      $form.find('input[name="pagetype"]').val("download");
      $form.find('input[name="title"]').val("Download");
      $form.find('input[name="icon"]').val("fa fa-download");
      $form.find('input[name="url"]').val("/download");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-download");
    }
    else if ($(this).val() == "help") {
      $form.find('input[name="pagetype"]').val("help");
      $form.find('input[name="title"]').val("Help Center");
      $form.find('input[name="icon"]').val("fa fa-question-circle");
      $form.find('input[name="url"]').val("/help");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas-question-circle");
    }
    else if ($(this).val() == "bazaar") {
      $form.find('input[name="pagetype"]').val("bazaar");
      $form.find('input[name="title"]').val("Bazaar");
      $form.find('input[name="icon"]').val("fa fa-store");
      $form.find('input[name="url"]').val("/bazaar");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas fa-store");
    }
    else if ($(this).val() == "gaming-night") {
      $form.find('input[name="pagetype"]').val("gaming-night");
      $form.find('input[name="title"]').val("Gaming Night");
      $form.find('input[name="icon"]').val("fa fa-moon");
      $form.find('input[name="url"]').val("/gaming-night");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas fa-moon");
    }
    else if ($(this).val() == "forum") {
      $form.find('input[name="pagetype"]').val("forum");
      $form.find('input[name="title"]').val("Forum");
      $form.find('input[name="icon"]').val("fa fa-comment");
      $form.find('input[name="url"]').val("/forum");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas fa-comment");
    }
    else if ($(this).val() == "rules") {
      $form.find('input[name="pagetype"]').val("rules");
      $form.find('input[name="title"]').val("Rules");
      $form.find('input[name="icon"]').val("fa fa-book");
      $form.find('input[name="url"]').val("/rules");
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "fas fa-book");
    }
    else {
      $form.find('input[name="pagetype"]').val("custom");
      $form.find('input[name="title"]').val(null);
      $form.find('input[name="icon"]').val(null);
      $form.find('input[name="url"]').val(null);
      $form.find('select[name="tabstatus"]').val(0).trigger("change");
      $iconpicker.iconpicker("setIcon", "empty");
    }
  });
});
