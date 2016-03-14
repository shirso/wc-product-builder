jQuery(function($){
   var  $variations_form = $('form.variations_form'),
        variations_json = $variations_form.attr('data-product_variations'),
        variations = ( typeof variations_json !== "undefined" ) ? $.parseJSON( variations_json ) : false,
        currentTaxonomy=$("#progress-indicator").find("li:first").data("taxonomy"),
        currentTermId=null,
        unavailable_template=wp.template( 'unavailable-variation-template' ),
        visited_tabs=[],
        fr=[];
    console.log(unavailable_template);
    visited_tabs.push($("#progress-indicator").find("li:first").data("tab"));
    var tabCount=$("#progress-indicator").find("li").length;
    $("#main").removeClass("clearfix");
    if($('.wpb-body-product').find('.woocommerce-de_price_taxrate').length>0){
        $("#wpb_german_market").append($(".woocommerce-de_price_taxrate"));
    }
    if($('.wpb-body-product').find('.shipping_de_string').length>0){
        $("#wpb_german_market").append($(".shipping_de_string"));
    }

    /****************************Common Functions***********************/
    var selectHasValue=function(select,value){
        obj = document.getElementById(select);

        if (obj !== null) {
            return (obj.innerHTML.indexOf('value="' + value + '"') > -1);
        } else {
            return false;
        }
    };
    var variationSelectChange=function(taxonomyName,term){
        if( $("#"+taxonomyName).val()!=term && selectHasValue(taxonomyName,term)) {
            $("#" + taxonomyName).focusin().val(term).change();

        }
    };
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
    };
    var visitedTabCheck=function (taxonomyName){
        if(!_.contains(visited_tabs,taxonomyName) && typeof taxonomyName!="undefined"){
            visited_tabs.push(taxonomyName);
        }
    };
    var selectedIndexChange=function(taxonomyName){
        $("#wpb_selections_"+taxonomyName).find(".values").html($("#"+taxonomyName+" option:selected").text());
    };
    var showSelection=function(taxonomyName,type){
     switch (type){
         case "dimension":
             $("#wpb-steps-"+taxonomyName).find("select").each(function(){
                 var taxonomy=$(this).data("taxonomy");
                 $("#wpb_selections_"+taxonomy).removeClass("wpb_hidden");
                 selectedIndexChange(taxonomy);
             });
             break;
         case "carousel":
             $("#wpb_selections_"+taxonomyName).removeClass("wpb_hidden");
             break;
         case "extra":
             $("#wpb-steps-"+taxonomyName).find(".wpb_carousel").each(function(){
                 var taxonomy=$(this).data("taxonomy");
                 $("#wpb_selections_"+taxonomy).removeClass("wpb_hidden");
             });
             break;
     }
    };
    var rangeSlider=function() {
        $(".wbp_slider").each(function () {
            var taxonomy = $(this).data("taxonomy"),
                sliderId = $("#wpb_slider_" + taxonomy),
                options=$(this).children('option'),
                firstOption=options[0],
                lastOption=options[options.length-1],
                selectBox=$("#"+$(this).attr("id"));
            $("#wpb_regulator_min_"+taxonomy).text($(firstOption).val());
            $("#wpb_regulator_max_"+taxonomy).text($(lastOption).val());
            var slider=$(sliderId).slider({
                min: 1,
                max:options.length,
                range: "min",
                value: selectBox[0].selectedIndex + 1,
                slide: function (event, ui) {
                    selectBox[0].selectedIndex = ui.value - 1;
                    $(selectBox).trigger("change");
                    selectedIndexChange(taxonomy);
                }
            });
            $(this).change(function(){
                slider.slider( "value", this.selectedIndex + 1 );
                variationSelectChange(taxonomy,$(this).val());
                selectedIndexChange(taxonomy);
            });
        });
    }
    /**************************Tab Functions **************************/
    $(document).on('change','.wpb-rngtxt',function(){
        var taxonomy = $(this).data("taxonomy");
            variationSelectChange(taxonomy,$(this).val());
            selectedIndexChange(taxonomy);
    });
    $(document).on('click','#progress-indicator li a',function(e){
        e.preventDefault();
        var $li=$(this).parent(),
            tabId=$li.data('tab'),
            tabType=$li.data("type"),
            taxonomy=$li.data("taxonomy"),
            counting=$li.data("counting")+1;
        if($li.hasClass('acctive')){
            return false;
        }
        currentTaxonomy=taxonomy;
        visitedTabCheck(currentTaxonomy);
        if(counting==tabCount){
            $("#wpb_continue_button").text(wpb_local_params.add_to_cart_text);
        }
        if(tabCount==visited_tabs.length){
            $("#wpb_continue_button").addClass("wpb_add_cart");
        }
        $('.wpb_tabs').removeClass('wpb_onedblk');
        $('.wpb_tabs').addClass('wpb_aldnn');
        $(tabId).removeClass('wpb_aldnn');
        $(tabId).addClass('wpb_onedblk');
        $li.addClass('completed');
        $li.parent().find('li').removeClass('acctive');
        $li.addClass('acctive');
        loadInfoData(wpb_local_params.productId,currentTaxonomy);
        showSelection(taxonomy,tabType);
    });
    /**************************Continue Button Functions **************************/
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
        $nextLiP.trigger('click');
    });
    /**************************Carousel Functions **************************/
    $(".wpb_carousel").each(function(){
        var id=$(this).attr("id"),
            right=id+"_right",
            left=id+"_left",
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
        $('#'+id).on('film_roll:moved', function(event) {
            var taxonomyName=id.substr(13,id.length),
                containerDiv=$("#"+taxonomyName+'_'+film_roll.index),
                term=containerDiv.find('.wpb_terms').data('term'),
                termid=containerDiv.find('.wpb_terms').data('termid'),
                type=containerDiv.find('.wpb_terms').data('type');
            variationSelectChange(taxonomyName,term);
            selectedIndexChange(taxonomyName);
        });
    });

    /**************************Range Slider **************************/
    rangeSlider();
    /**************************WC Variation values Update **************************/
    $variations_form.on( 'show_variation', function( event, variation ) {
        var variation_data=(!variations)? variation:_.findWhere(variations,{variation_id:variation.variation_id})
        main_image=variation_data.image_link;
        additional_images=variation_data.additional_images;
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
        $("#wpb_main_images").attr("src",main_image);
        $("#wpb_price_html").html(variation_data.price_html);
        $("#wpb_price_html").find(".price").removeAttr("style");
    });
    /**************************Aditional Images*************************/
    $(document).on('click','.wpb_additional_image',function(e){
        e.preventDefault();
        var old_url=$(this).attr("src"),
            new_url=$("#wpb_main_images").attr("src");
        $(this).attr("src",new_url);
        $("#wpb_main_images").attr("src",old_url);
    });

});