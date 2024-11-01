function copyToClipboard($, element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}

function fadeMessage($, appendElement, msg) {

  var msg = $('<label/>', {
    id: "msg",
    style: "margin-bottom: 10px;background-color: #555; padding: 5px; display: inline-block; border: 1px solid; border-radius: 5px; color: white;",
    text: msg
  })

  $(appendElement).before(msg);

  $("*[id*=msg]:visible").each(function() {
    $(this).fadeOut(3500, function() {
      // Animation complete.
      $(this).remove();
    });
  });
}