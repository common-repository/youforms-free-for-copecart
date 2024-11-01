(function($) {

  $("#wp-content-wrap").remove();
  $("#post-status-info").remove();

  init_changeEventsOnInputs();
  init_changeEventsOnBgAndFontColors();

  genform()

  //Copy Shortcode in ClipBoard
  $("#cp_shortcode").click(function() {

    copyToClipboard($, "#cp_shortcode");

    //Show message
    fadeMessage($, "#cp_shortcode", "Info: ShortCode is copied in ClipBoard!")

  });


  //Set CopeCart URL in Form
  $("#copecarturl").on("input", function() {
    $("#copecarturl").val();
    genform();
  });

  $(".popupmodal").removeClass("modal");
  $(".popupmodal").removeClass("fade");
  $(".popupmodal").removeAttr("style");


  $("#hidebtntemplate").click(function() {

    if ($("#hidebtntemplate").is(":checked")) {
      $("#mainclickbtn").hide();
      $("#containerbuttontemp").hide();

      if ($("#hidemodaltemplate").is(":checked")) {
        $("#hidemodaltemplate").click();
      }
    } else {
      $("#mainclickbtn").show();
      $("#containerbuttontemp").show();
    }
    genform()
  })

  $("#hideproductview").click(function() {

    //hide product view
    if ($("#hideproductview").is(":checked")) {
      $("#producturllbl").attr("style", "display: none");

    } else {
      $("#producturllbl").attr("style", "");
    }
    genform()
  });

  if ($("#hidebtntemplate").is(":checked")) {
    $("#mainclickbtn").hide();
    $("#containerbuttontemp").hide();
  } else {
    $("#mainclickbtn").show();
    $("#containerbuttontemp").show();
  }

  if ($("#hidemodaltemplate").is(":checked")) {
    $("#modaltempcontainer").hide();
  } else {
    $("#modaltempcontainer").show();
  }
  genform()

  //Visble Hide ENDE

  function genform() {

    let mainbtn = $("#mainclickbtn")[0].outerHTML;
    let modalbtn = $("#mainmodal")[0].outerHTML;

    //$("#buttoncode").val(mainbtn);
    $("#modalcode").val(modalbtn);

  }

  function trigger(array) {
    $.each(array, function(_ids) {
      $(_ids).change();
    })
    genform()
  }

  function init_changeEventsOnInputs() {
    const inputids = ["#buttonclicktext", "#buttonclicksubtext", "#headertext", "#headersubtext", "#buttonheadertext", "#buttonheadersubtext", "#smalltextlbl", "#productname", "#productprice"];
    const lblids = ["#btnmainlbl", "#buttonlblsubtext", "#step1txt", "#step1subtxt", ".bigbuttontxt", "#elButtonbuySub", "#kleingedrucktes", "#productnamelbl", "#productpricelbl"];

    $.each(inputids, function(i, _inputids) {
      $(_inputids).on("input", function() {
        $(lblids[i]).text($(this).val());
        genform();
      });
    })

    trigger(inputids)

    //product img
    $("#productimg").on("input", function() {
      $(".product_img").attr("src", $(this).val());
      genform()
    });

    trigger(["#productimage"]);

    //producturl
    $("#producturl").on("input", function() {
      $("#producturllbl").attr("href", $(this).val());
      genform()
    });

    trigger(["#producturl"]);

    //Set Background IMG
    $("#bgimgproductview").on("input", function() {

      $("#productinfo").css({
        "background-image": "url(" + $("#bgimgproductview").val() + ")",
        "background-repeat": "no-repeat",
        "background-position": "center center"
      });
      genform()
    })

    trigger(["#productinfo"]);

    //Set Payment picture
    $("#paymentpicurl").on("input", function() {
      $("#payment_pic").attr("src", $(this).val());
      genform()
    })

    trigger(["#paymentpicurl"]);

  }

  function init_changeEventsOnBgAndFontColors() {
    //background
    const colorbgids = ["#buttontempbgcolor", "#headerbgcolor", "#buybuttonbgcolor", "#btnprodviewbgcolor"];
    const elementbgids = ["#popupbutton", "#step1button", ".buybutton", "#productinfo"];

    $.each(colorbgids, function(i, _colorbgids) {
      $(_colorbgids).on("input", function() {
        $(elementbgids[i]).css("background", $(this).val());
        genform();
      });
    })

    trigger(colorbgids)

    //font-color
    const colorbgids2 = ["#buttonfontcolor", "#headerfontcolor", "#buybuttonfontcolor", "#buttonproductfontcolor"];
    const elementbgids2 = ["#popupbutton", "#step1button", ".buybutton", "#producturllbl"];

    $.each(colorbgids2, function(i, _colorbgids) {
      $(_colorbgids).on("input", function() {
        $(elementbgids2[i]).css("color", $(this).val());
        genform();
      });
    })

    trigger(colorbgids);
  }

  function l(text) {
    console.dir(text)
  }

})(jQuery);