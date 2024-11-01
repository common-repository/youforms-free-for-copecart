  (function($) {

    //Check if modal is with fade or only modal by button id
    var POSTID = $("#postid").val();

    if ($("#mainclickbtn").length) {
      $("#"+POSTID).addClass("modal");
      $("#"+POSTID).addClass("fade");
      $("#"+POSTID).attr("aria-hidden", "true");
    } else {
      $("#"+POSTID).removeClass("modal");
      $("#"+POSTID).removeClass("fade");
      $("#"+POSTID).removeAttr("style");
    }

    $("#"+POSTID+"_buybutton").click(function(e) {

      var link = $("#"+POSTID+"_copecardurl").val();
      console.log(link);
      createCopeCartLink(link, POSTID);


    })

    function checkEmptyFields(globalid) {
      formArray = ["form_vorname", "form_nachname", "form_email", "form_phone", "form_adresse", "form_city", "shipping_country", "form_laender", "form_hausnummer", "form_plz"];

      var errors = 0;
      $.each(formArray, function(i, data) {

        feldid = globalid + "_" + data;
        //If input exists
        if ($("#" + feldid).val() !== undefined) {
          //if value empty
          if ($("#" + feldid).val() == "") {
            $("#" + feldid).removeAttr("style")
            $("#" + feldid).addClass("border-danger");
            $("#" + feldid).attr("style", "font-size: 16px");
            errors++;
          } else {
            $("#" + feldid).removeClass("border-danger")
            $("#" + feldid).attr("style", "font-size: 16px; border-width: 2px !important; border-color : " + $("#" + globalid + "_form_bordercolor").val() + " !important");
          }
        }
      });
      return errors;
    }

    function createCopeCartLink(copecardurl, globalid) {
      formArray = ["form_vorname", "form_nachname", "form_email", "form_phone", "form_adresse", "form_city", "form_laender", "form_hausnummer", "form_plz"];
      var errors = 0;
      copecardurl += "?";
      $.each(formArray, function(i, data) {

        //If input exists
        if ($("#" + globalid + "_" + data).val() !== undefined) {

          var inputVal = $("#" + globalid + "_" + data).val();
          if (data == "form_vorname") {
            copecardurl += "first_name=" + inputVal + "&";
          } else if (data == "form_nachname") {
            copecardurl += "last_name=" + inputVal + "&";
          } else if (data == "form_email") {
            copecardurl += "email=" + inputVal + "&";
          } else if (data == "form_phone") {
            copecardurl += "phone=" + inputVal + "&";
          } else if (data == "form_adresse") {
            copecardurl += "street=" + inputVal + "&";
          } else if (data == "form_hausnummer") {
            copecardurl += "street_number=" + inputVal + "&";
          } else if (data == "form_city") {
            copecardurl += "city=" + inputVal + "&";
          } else if (data == "form_laender") {
            copecardurl += "country=" + $("#" + globalid + "_" + data + " option:selected").text() + "&";
          } else if (data == "form_plz") {
            copecardurl += "zip_code=" + inputVal + "&";
          }

        }
      })

      if (checkEmptyFields(globalid) == 0) {
        location.href = copecardurl;
      }
    }
  })(jQuery);
