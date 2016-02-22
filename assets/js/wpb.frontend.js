jQuery(function($){
    var bx=  $('.bxslider').bxSlider({
        minSlides: 5,
        maxSlides: 5,
        slideWidth: 360,
        slideMargin: 5,
        pager:false
    });
 $(document).on('click','.progress-indicator li p',function(e){
     e.preventDefault();
     var $li=$(this).parent();
     if(!$li.hasClass('acctive')){
         var tabId=$li.data('tab');
         $('.wpb_tabs').removeClass('wpb_onedblk');
         $('.wpb_tabs').addClass('wpb_aldnn');
         $(tabId).removeClass('wpb_aldnn');
         $(tabId).addClass('wpb_onedblk');
         $li.addClass('completed');
         $li.parent().find('li').removeClass('acctive');
         $li.addClass('acctive');
         if($li.data("type")=="extra"){
            $("#wpb_extra_options").removeClass("wpb_hidden");
         }else{
             $("#wpb_extra_options").addClass("wpb_hidden");
         }
     }

 });
    $(".wbp_slider").each(function(){
        var min=$(this).data('min'),
            max=$(this).data('max'),
            step=$(this).data('step'),
            id=$(this).attr("id"),
            textValue=$(this).data("text");
        $("#"+id).slider({
            range: "min",
            value:min,
            min: min,
            max: max,
            step:step,
            slide: function( event, ui ) {
               // $( "#amount" ).val( "$" + ui.value );
                $("#"+textValue).val( ui.value );
            }
        });
    });
    $(document).on("click",".wpb_terms",function(e){
        e.preventDefault();
        var parent=$(this).parent().parent();
        parent.find('li div').removeClass('active');
       $(this).addClass('active');
        if($(this).data("type")=="extra"){
            $("#wpb_button_div").html($(this).parent().find(".wpb_button_div").html());
        }

    });
    $(document).on("click",".wpb_extra",function(e){
        e.preventDefault();
        $(this).parent().find(".wpb_extra").removeClass("activ");
        $(this).addClass("activ");
    });
});