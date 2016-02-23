jQuery(function($){
 $("#main").removeClass("clearfix");
  $(".wpb_carousel").each(function(){
        var id=$(this).attr("id"),
            right=id+"_right",
            left=id+"_left",
            default_value= parseInt($("#"+id+"_default").val());
      fr = new FilmRoll({
          container: '#'+id,
          prev: '#'+left,
          next: '#'+right,
          pager:false,
          scroll:false,
          force_buttons:true,
          animation:500,
          start_index:default_value
      });

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