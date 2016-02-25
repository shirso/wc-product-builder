jQuery(function($){
   var  $variations_form = $('form.variations_form'),
        variations_json = $variations_form.attr('data-product_variations'),
        variations = ( typeof variations_json !== "undefined" ) ? $.parseJSON( variations_json ) : false;
    $("#main").removeClass("clearfix");
    if($('.wpb-body-product').find('.shipping_de_string').length>0){
        $("#wpb_german_market").append($(".shipping_de_string"));
    }
    var fr=[];
    var visited_tabs=[];
    visited_tabs.push($("#progress-indicator").find("li:first").data("tab"));
  $(".wpb_carousel").each(function(){
        var id=$(this).attr("id"),
            right=id+"_right",
            left=id+"_left";
            default_value=typeof $("#"+id+"_default")!="undefined"? parseInt($("#"+id+"_default").val()):0;
         var film_roll = new FilmRoll({
          container: '#'+id,
          prev: '#'+left,
          next: '#'+right,
          pager:false,
          scroll:false,
          force_buttons:true,
          animation:500,
          start_index:default_value,
          configure_load: true
      });
      fr.push({id:id,roll:film_roll});
  });
    $.each(fr,function(m,n){
     var id= n.id,
         film_roll= n.roll;
        $('#'+id).on('film_roll:moved', function(event) {
            var taxonomyName=id.substr(13,id.length),
                containerDiv=$("#"+taxonomyName+'_'+film_roll.index),
                term=containerDiv.find('.wpb_terms').data('term'),
                type=containerDiv.find('.wpb_terms').data('type');
            if(type=="extra"){
                $("#wpb_button_div").html(containerDiv.find(".wpb_button_div").html());
            }
            if( $("#"+taxonomyName).val()!=term) {
                $("#" + taxonomyName).focusin().val(term).change();
                $('#im-sd-sec').block({message: null,
                    overlayCSS: {
                        backgroundColor: '#fff',
                        opacity: 0.6
                    }
                });
            }
        });
    });
 $(document).on('click','#progress-indicator li a',function(e){
     e.preventDefault();
     var $li=$(this).parent(),
         tabId=$li.data('tab');
     if($li.hasClass('acctive') || !_.contains(visited_tabs,tabId)){
         return false;
     }
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
     if($li.hasClass("last_one")){
         $("#wpb_continue_button").text(wpb_local_params.add_to_cart_text);
     }else{
         $("#wpb_continue_button").text(wpb_local_params.continue_text);
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
        if($(this).data("type")=="extra"){
            $("#wpb_button_div").html($(this).parent().find(".wpb_button_div").html());
        }
        var carousel_id="wpb_carousel_"+$(this).data("taxonomy"),
            filterd_fr= _.findWhere(fr,{id:carousel_id}),
            move_index=$(this).data("counting");
            filterd_fr.roll.moveToIndex(move_index),
            taxonomyName=$(this).data("taxonomy"),
            term=$(this).data("term");
        $("#"+taxonomyName).val(term).change();
        $('#im-sd-sec').block({message: null,
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.6
            }
        });
    });
    $(document).on("click",".wpb_extra",function(e){
        e.preventDefault();
        $(this).parent().find(".wpb_extra").removeClass("activ");
        $(this).addClass("activ");
    });
    $variations_form.on( 'show_variation', function( event, variation ) {
        var variation_data=(!variations)? variation:_.findWhere(variations,{variation_id:variation.variation_id})
            main_image=variation_data.image_link;
            additional_images=variation_data.additional_images;
        $("#wpb_main_images").attr("src",main_image);
        if(typeof additional_images !="undefined" && additional_images.length>0){
            $("#wpb_additional_images").html("");
            $.each(additional_images,function(k,i){
              var html="";
               html+='<div class="blk-im">';
             html+='<img src="'+i+'" class="img-responsive wpb_additional_image">'
              html+='</div>';
               $("#wpb_additional_images").append($(html));
            });
        }else{
           $("#wpb_additional_images").html("");
        }
        $("#wpb_price_html").html(variation_data.price_html);
        $("#wpb_price_html").find(".price").removeAttr("style");
        $("#im-sd-sec").unblock();
    });
    $(document).on('click','.wpb_additional_image',function(e){
        e.preventDefault();
        var old_url=$(this).attr("src"),
            new_url=$("#wpb_main_images").attr("src");
        $(this).attr("src",new_url);
        $("#wpb_main_images").attr("src",old_url);
    });
    $(document).on("click","#wpb_continue_button",function(e){
        e.preventDefault();
        var $activeLi=$('#progress-indicator').find('.acctive'),
            $nextLi=$activeLi.next(),
            $nextLiP=$nextLi.find("a"),
            nextLiTab=$nextLi.data("tab");
        if(!_.contains(visited_tabs,nextLiTab) && typeof nextLiTab!="undefined"){
            visited_tabs.push(nextLiTab);
        }
       $nextLiP.trigger('click');
    });
});