/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');

$(document).ready(function() {
    // Transition effect for navbar
    $(window).scroll(function() {
      // checks if window is scrolled more than 500px, adds/removes solid class
      if($(this).scrollTop() > 10) {
          $('.navbar').addClass('solid');
          $('.nav_link').addClass('solid_text');
      } else {
          $('.navbar').removeClass('solid');
          $('.nav_link').removeClass('solid_text');
      }
    });

    // Alert message when password error
    $( ".jquery-focus" ).focus(function() {
      $( "#alert" ).addClass('fading-alert');
    });

    // Burger menu animation
    $( "#menuBurger" ).click(function() {
      $( "#mySidenav" ).css("left", "0px");
    });

    $( "#closeMenuBurger" ).click(function() {
      $( "#mySidenav" ).css("left", "-250px");
    });

    if ( $('#greyBack').length ){
        $( "body" ).css("overflow", "hidden");
    }



    // If scroll AND location cookie is set, and the location is the same
    // scroll to the position saved in the scroll cookie.
    // Using jQuery Cookie
    var location = window.location.href;
    if ( Cookies.get("scroll") !== null && Cookies.get("location") !== null && Cookies.get("location") == location) {
        $(document).scrollTop( Cookies.get("scroll") );
    }

    // When a button is clicked...
    $('.newPhase').on("click", function() {
      var scrollPosition = $(document).scrollTop();
      // Set a cookie that holds the scroll position.
      Cookies.set("scroll", scrollPosition);
      // set a cookie that holds the current location.
      Cookies.set("location", location);
    });
});



$("#removeMessage").on("click", function(event){
    $.ajax({
       url:        '/new-user',
       type:       'POST',

       success: function(data, status) {
         $( "body" ).css("overflow", "auto");
         $('#greyBack').addClass('no_display');
         $('#congrat').addClass('no_display');
       },
       error : function(xhr, textStatus, errorThrown) {
          alert(textStatus);
       }
    });
 });

 $(".downloadBtn").on("click", function(event){
     $(this).find( ".checkmark_2" ).css( "animation", "fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both" );
     $(this).find( ".checkmark__check_2" ).css( "animation", "stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards");
     $(this).find( ".checkmark__circle_2" ).css( "animation", "stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards");

     var relationId = parseInt($(this).attr("value"));
     $.ajax({
        url:        '/file-downloaded',
        type:       'POST',
        dataType: "json",
        data: {
            "relationId": relationId
        },
        async: true,

        success: function(data, status) {
          console.log("OK");
        },
        error : function(xhr, textStatus, errorThrown) {
           alert(textStatus);
        }
     });
  });
