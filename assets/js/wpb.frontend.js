jQuery(function($){
   var  $variations_form = $('form.variations_form'),
        variations_json = $variations_form.attr('data-product_variations'),
        variations = ( typeof variations_json !== "undefined" ) ? $.parseJSON( variations_json ) : false,
        currentTaxonomy=$("#progress-indicator").find("li:first").data("taxonomy");
    var valueChange=function($li){
        var tabType=$li.data("type");
        $("#wpb_selections_"+currentTaxonomy).removeClass("wpb_hidden");
        if(tabType!="size"){
            $("#wpb_selections_"+currentTaxonomy).find(".values").text($("#"+currentTaxonomy+" option:selected").text());
        }
    };
    $variations_form.append($(".wpb_cart_items"));
    var sizeOptions=function(taxonomyName){
      var finalHtml="";
      $(".wpb_calculation").each(function(m,n){
        var inputValue=$(this).val(),
            title=$(this).data("title"),
            unit=$(this).data("unit");
          if(inputValue!=null && inputValue!=""){
              finalHtml+=title+":"+inputValue+unit+" / ";
          }
      });
        finalHtml=finalHtml.substring(0,finalHtml.length-2)
      $("#wpb_selections_"+taxonomyName).find(".sizeOptions").html(finalHtml);

      $("#wpb_hidden_size_"+taxonomyName).val(finalHtml);
    };
    var selectIndexChanged=function(){
        $("#wpb_selections_"+currentTaxonomy).find(".values").html($("#"+currentTaxonomy+" option:selected").text());
    };
    $("#main").removeClass("clearfix");
    if($('.wpb-body-product').find('.woocommerce-de_price_taxrate').length>0){
        $("#wpb_german_market").append($(".woocommerce-de_price_taxrate"));
    }
    if($('.wpb-body-product').find('.shipping_de_string').length>0){
        $("#wpb_german_market").append($(".shipping_de_string"));
    }

    var fr=[];
    var visited_tabs=[];
    visited_tabs.push($("#progress-indicator").find("li:first").data("tab"));
    valueChange($("#progress-indicator").find("li:first"));
    $(".wbp_slider").each(function(){
        var min=$(this).data('min'),
            max=$(this).data('max'),
            id=$(this).attr("id"),
            textValue=$(this).data("text"),
            slideId="#"+$(this).data("slider"),
            selectId="#"+$(this).attr("id"),
            selectBox=$("#"+$(this).attr("id"));
        var slider=$("<div id='"+slideId+"'></div>").insertAfter(selectBox).slider({
            min: 1,
            max:max,
            range: "min",
            value: selectBox[0].selectedIndex + 1,
            slide: function (event, ui) {
                selectBox[0].selectedIndex = ui.value - 1;
                $("#"+textValue).val($(selectId+' option:selected').val() );
            }
        });
    });
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
                $("#wpb_selections_"+taxonomyName).find(".options").text("");
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
         tabId=$li.data('tab'),
         tabType=$li.data("type");
     if($li.hasClass('acctive')){ //|| !_.contains(visited_tabs,tabId)){
         return false;
     }
         $('.wpb_tabs').removeClass('wpb_onedblk');
         $('.wpb_tabs').addClass('wpb_aldnn');
         $(tabId).removeClass('wpb_aldnn');
         $(tabId).addClass('wpb_onedblk');
         $li.addClass('completed');
         $li.parent().find('li').removeClass('acctive');
         $li.addClass('acctive');
         if(tabType=="extra"){
            $("#wpb_extra_options").removeClass("wpb_hidden");
         }else{
             $("#wpb_extra_options").addClass("wpb_hidden");
         }
     if($li.hasClass("last_one")){
         $("#wpb_continue_button").addClass("wpb_add_cart");
         $("#wpb_continue_button").text(wpb_local_params.add_to_cart_text);
     }else{
         //$("#wpb_continue_button").removeClass("wpb_add_cart");
         //$("#wpb_continue_button").text(wpb_local_params.continue_text);
     }
     currentTaxonomy=$li.data("taxonomy");
     if(tabType=="size"){
         sizeOptions(currentTaxonomy);
     }
     valueChange($li);
     loadInfoData(wpb_local_params.productId,currentTaxonomy);
 });

    $(document).on("click",".wpb_terms",function(e){
        e.preventDefault();
        var carousel_id="wpb_carousel_"+$(this).data("taxonomy"),
            filterd_fr= _.findWhere(fr,{id:carousel_id}),
            move_index=$(this).data("counting");
            filterd_fr.roll.moveToIndex(move_index),
            taxonomyName=$(this).data("taxonomy"),
            term=$(this).data("term");
        if($(this).data("type")=="extra"){
            $("#wpb_button_div").html($(this).parent().find(".wpb_button_div").html());

        }
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
        var taxonomy=$(this).data("taxonomy"),
            termSlug=$(this).data("slug"),
            buttonValue=$(this).val(),
            valuesText= $("#wpb_selections_"+taxonomy).find(".values").text();
        $(this).parent().find(".wpb_extra").removeClass("activ");
        $(this).addClass("activ");
        $("#wpb_selections_"+taxonomy).find(".options").text("("+buttonValue+")");
        $("#wpb_hidden_extra_"+taxonomy).val(buttonValue);
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
        selectIndexChanged();
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
        if($(this).hasClass("wpb_add_cart")){
            $variations_form.find(".single_add_to_cart_button").trigger("click");
            return false;
        }
        var $activeLi=$('#progress-indicator').find('.acctive'),
            $nextLi=$activeLi.next(),
            $nextLiP=$nextLi.find("a"),
            nextLiTab=$nextLi.data("tab");
        if(!_.contains(visited_tabs,nextLiTab) && typeof nextLiTab!="undefined"){
            visited_tabs.push(nextLiTab);
        }
       $nextLiP.trigger('click');
    });
    $(document).on("keyup",".wpb-rngtxt",function(){
       sizeOptions(currentTaxonomy);
    });
    var loadInfoData=function(productId,taxonomy){
        var data = {
            'action': 'wpb_info_box_load',
            'productId': productId,
            'taxonomy':taxonomy
        };
        $('#wpb_info_box_content').block({message: null,
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.6
            }
        });
        $.post(wpb_local_params.ajaxUrl, data, function(response) {
            $("#wpb_info_box_content").html("");
            $("#wpb_info_box_content").html(response);
            $('#wpb_info_box_content').unblock();
        });
    }
});