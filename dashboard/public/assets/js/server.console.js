var clickable = true;
function sendConsoleCommand() {
  var consoleCommand = $("#consoleCommand").val();
  if (clickable == true && consoleCommand != null) {
    $.ajax({
      type: "POST",
      url: "/apps/dashboard/public/ajax/console.php?action=send&id=" + serverID,
      data: {command: consoleCommand},
      success: function(result) {
        clickable = true;
        $("#consoleCommand").val("");
        $("#consoleHistory").append(
          '<div class="media mb-3">' +
            '<img class="rounded-circle mt-1 mr-2" src="https://minotar.net/avatar/' + username + '/20.png" alt="Yetkili">' +
            '<div class="media-body">' +
              '<div class="row">' +
                '<div class="col">' +
                  '<a href="/dashboard/users/view/' + username + '">' +
                    '<strong>' + username + '</strong> ' +
                  '</a>' +
                '</div>' +
                '<div class="col-auto small">' +
                  creationDate +
                '</div>' +
              '</div>' +
              '<div class="row">' +
                '<div class="col-12">' +
                  consoleCommand +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>'
        );
        updateConsoleBoxScroll();
      }
    });
  }
}

/* BIND KEYPRESS EVENT TO INPUT */
$("#consoleCommand").on("keypress", function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    sendConsoleCommand();
    clickable = false;
  }
  e.stopPropagation();
});

/* UPDATE BOX CONTENT */
function updateConsoleBoxContent(loader = false) {
  if (loader == true) {
    $("#consoleBox").addClass("is-loading");
    $("#spinner").css("display", "flex");
  }
  $.ajax({
    type: "GET",
    url: "/apps/dashboard/public/ajax/console.php?action=update&id=" + serverID,
    success: function(result) {
      if (loader == true) {
        $("#consoleBox").removeClass("is-loading");
        $("#spinner").css("display", "none");
      }
      $("#consoleHistory").html(result);
      updateConsoleBoxScroll();
    }
  });
}

/* UPDATE BOX SCROLL */
var scrollElement = $("#consoleBox");
var scrollHeight = scrollElement.prop("scrollHeight");

function updateConsoleBoxScroll() {
  if (scrollHeight < scrollElement.prop("scrollHeight")) {
    scrollElement.scrollTop(scrollElement.prop("scrollHeight"));
    scrollHeight = scrollElement.prop("scrollHeight");
  }
}

$(document).ready(function() {
  $("#consoleRefresh").on("click", function(e) {
    updateConsoleBoxContent(true);
    e.preventDefault();
  });
  updateConsoleBoxContent(true);
  setInterval(updateConsoleBoxContent, 1000);
});
