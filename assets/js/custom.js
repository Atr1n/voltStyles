jQuery(function($) {


   $(".show-popup, .diler_price").click(function(){
        event.preventDefault();
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
          $(".popup-form").css({'opacity':'1', 'top':'0'});   
        }
        else {
            $(".popup-form").css({'opacity':'1', 'top':'20%'});
        }
        $("#overlayWrapper").css({'display':'block'});
    });

    $("#overlayWrapper , #close_popup ").click(function(){
        $(".popup-form").css({'opacity':'0', 'top':'-200%'});
        $("#overlayWrapper").css({'display':'none'});
        $(".wpcf7-response-output").css({'display':'none'});
    });
    $(".wpcf7-response-output").click(function() {
        $(this).css({'display':'none'});
    });

        $(".show-popup, .diler_price").on("click", function(event) {
        event.preventDefault();
        var y = $('.summary').find('.product_title').html();
        var z = $.trim(y);
        $('#contact-subject').val(z);
    });



    $( ".mega-menu-item-object-product_cat.mega-toggle-on" ).removeClass( "mega-toggle-on" );

});


document.addEventListener("DOMContentLoaded", function(event) { 
var elems = document.querySelectorAll(".mega-menu-item-object-product_cat.mega-toggle-on");

[].forEach.call(elems, function(el) {
    el.classList.remove("mega-toggle-on");
});
});