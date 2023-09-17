var clickable = true;
function sendChatMessage() {
  var chatMessage = $("#chatMessage").val();
  if (clickable == true && chatMessage != null) {
    $.ajax({
      type: "POST",
      url: "/apps/dashboard/public/ajax/chat.php?action=send",
      data: {message: chatMessage},
      success: function(result) {
        clickable = true;
        $("#chatMessage").val("");
        $("#chatHistory").append(
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
                  result +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>'
        );
        updateChatBoxScroll();
      }
    });
  }
}

/* BIND KEYPRESS EVENT TO INPUT */
$("#chatMessage").on("keypress", function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    sendChatMessage();
    clickable = false;
  }
  e.stopPropagation();
});

/* UPDATE BOX CONTENT */
function updateChatBoxContent(loader = false) {
  if (loader == true) {
    $("#chatBox").addClass("is-loading");
    $("#spinner").css("display", "flex");
  }
  $.ajax({
    type: "GET",
    url: "/apps/dashboard/public/ajax/chat.php?action=update",
    success: function(result) {
      if (loader == true) {
        $("#chatBox").removeClass("is-loading");
        $("#spinner").css("display", "none");
      }
      $("#chatHistory").html(result);
      updateChatBoxScroll();
    }
  });
}

/* UPDATE BOX SCROLL */
var scrollElement = $("#chatBox");
var scrollHeight = scrollElement.prop("scrollHeight");

function updateChatBoxScroll() {
  if (scrollHeight < scrollElement.prop("scrollHeight")) {
    scrollElement.scrollTop(scrollElement.prop("scrollHeight"));
    scrollHeight = scrollElement.prop("scrollHeight");
  }
}

$(document).ready(function() {
  $("#chatRefresh").on("click", function(e) {
    updateChatBoxContent(true);
    e.preventDefault();
  });
  updateChatBoxContent(true);
});
