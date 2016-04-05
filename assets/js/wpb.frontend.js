jQuery(function($){
    var  $variations_form = $('form.variations_form'),
        variations_json = $variations_form.attr('data-product_variations'),
        variations = ( typeof variations_json !== "undefined" ) ? $.parseJSON( variations_json ) : false,
        currentTaxonomy=$("#progress-indicator").find("li:first").data("taxonomy"),
        currentTaxonomytype=$("#progress-indicator").find("li:first").data("type"),
        currentTermId=null,
        visited_tabs=[],
        changed_carousel=[],
        changed_droopdown=[],
        fr=[],
        carousel_length=$(".wpb_carousel").length;

    var justTemp=0;
    visited_tabs.push($("#progress-indicator").find("li:first").data("taxonomy"));
    var tabCount=$("#progress-indicator").find("li").length;
    $("#main").removeClass("clearfix");
    if($('.wpb-body-product').find('.woocommerce-de_price_taxrate').length>0){
        $("#wpb_german_market").append($(".woocommerce-de_price_taxrate"));
    }
    if($('.wpb-body-product').find('.shipping_de_string').length>0){
        $("#wpb_german_market").append($(".shipping_de_string"));
    }
    /****************************Common Functions***********************/
    var triggerFocusin=function(taxonomy,type){
        var container=$("#wpb-steps-"+taxonomy);
        switch (type) {
            case "carousel":
                $("#"+taxonomy).focusin().val($("#"+taxonomy).val()).change();
                break;
            case "dimension":
                $selectBox=$(container).find("select:first");
                var taxonomySelect=$selectBox.data("taxonomy");
                $("#"+taxonomySelect).focusin().val($("#"+taxonomySelect).val()).change();
                break;
            case "extra":
                $carousel=$(container).find(".wpb_carousel:first");
                var taxonomyCarousel=$carousel.data("taxonomy");
                $("#"+taxonomyCarousel).focusin().val($("#"+taxonomyCarousel).val()).change();
                break;
        }
    };
    var checkVariationAttributesCarousel=function(taxonomy){
        $container= $("#wpb_carousel_"+taxonomy);
        $container.find(".film_roll_child").addClass("wpb_disabled");
        $select=$('select#'+taxonomy+'');
        if($select.children('option.active,option.enabled').length>0) {
            $select.children('option.active,option.enabled').each(function (i, option) {
                $anchor = $container.find(".film_roll_child a[data-term=" + option.value + "]");
                $anchor.parent().removeClass("wpb_disabled");
            });
        }else{
            $select.children('option').each(function (i, option) {
                if(option.value!=""){
                    $anchor = $container.find(".film_roll_child a[data-term=" + option.value + "]");
                    $anchor.parent().removeClass("wpb_disabled");
                }
            });
        }
    };
    var checkVariationAttributesDimension=function(taxonomy){
        $selects=$("#wpb-steps-"+taxonomy).find("select");

        $selects.each(function(i,select){
            var taxonomyCurrent=$(select).data("taxonomy");
            $select=$('select#'+taxonomyCurrent+'');

            var allOptions=$select.children("option"),
                selectedValue=$select.val();
            var html="";
            $.each(allOptions,function(i,option){
                if(option.value!=""){
                    var selectedText=option.value==selectedValue? "selected='selected'":"";
                    html+='<option '+selectedText+' value="'+option.value+'">'+option.text+'</option>'
                }
            });
            $(select).html(html);
        });
        rangeSlider();
    };
    var checkVariationAttributesExtra=function(taxonomy){
        $carousels=$("#wpb-steps-"+taxonomy).find(".wpb_carousel");
        $carousels.each(function(i,c){
            var taxonomyCurrent= $(c).data("taxonomy");
            checkVariationAttributesCarousel(taxonomyCurrent);
        });
    };
    var checkVariationAttribute=function(taxonomy,type){
        switch (type){
            case "carousel":
                checkVariationAttributesCarousel(taxonomy);
                break;
            case  "dimension":
                checkVariationAttributesDimension(taxonomy);
                break;
            case "extra":
                checkVariationAttributesExtra(taxonomy);
                break;
            default :
                checkVariationAttributesCarousel(taxonomy);
                break;
        }
    };
   var deleteChekcked=function(taxonomy,type){
       switch(type) {
           case "carousel":
               if(_.contains(changed_carousel,taxonomy)){
               changed_carousel= _.without(changed_carousel,taxonomy);
               }
               break;
           case "dimension":
               $selects=$("#wpb-steps-"+taxonomy).find("select");
               $selects.each(function(i,select){
                   var taxonomyCurrent=$(select).data("taxonomy");
                   if(_.contains(changed_droopdown,taxonomyCurrent)){
                       changed_droopdown= _.without(changed_droopdown,taxonomyCurrent);
                   }
               });
               break;
           case "extra":
               $carousels=$("#wpb-steps-"+taxonomy).find(".wpb_carousel");
               $carousels.each(function(i,c){
                   var taxonomyCurrent= $(c).data("taxonomy");
                   if(_.contains(changed_carousel,taxonomyCurrent)){
                    changed_carousel= _.without(changed_carousel,taxonomyCurrent);
                   }
               });
               break;

       }
   };
    var selectHasValue=function(select,value){
        obj = document.getElementById(select);

        if (obj !== null) {
            return (obj.innerHTML.indexOf('value="' + value + '"') > -1);
        } else {
            return false;
        }
    };
    var refreshZoom=function(){
        $(".pp_pic_holder").remove();
        $(".pp_overlay").remove();
        $(".ppt").remove();
        $("a#wpb_main_image_link").prettyPhoto({
            social_tools: false,
            theme: 'pp_woocommerce',
            horizontal_padding: 20,
            opacity: 0.8,
            deeplinking: false
        });
    };
    var variationSelectChange=function(taxonomyName,term){
        if( selectHasValue(taxonomyName,term)) {
           // console.log(taxonomyName+"|"+term);
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
        if(typeof taxonomyName!="undefined" && !_.contains(visited_tabs,taxonomyName)){
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
        //checkVariationAttributesDimension(currentTaxonomy);
        $(".wbp_slider").each(function () {

            var taxonomy = $(this).data("taxonomy"),
                sliderId = $("#wpb_slider_" + taxonomy),
                options=$(this).children('option'),
                firstOption=options[0],
                lastOption=options[options.length-1],
                selectBox=$("#"+$(this).attr("id")),
                taxonomySelect=$("select#"+taxonomy);
            $("#wpb_regulator_min_"+taxonomy).text($(firstOption).val());
            $("#wpb_regulator_max_"+taxonomy).text($(lastOption).val());
            if(_.contains(changed_droopdown,taxonomy)){
                $variations_form.trigger("wpb_select_change",currentTaxonomy);
                changed_droopdown= _.without(changed_droopdown,taxonomy);
                //changed_droopdown.push(taxonomy);
            }
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
                //console.log($(this));
                 slider.slider( "value", this.selectedIndex + 1 );
                if(!_.contains(changed_droopdown,taxonomy)){
                    //$variations_form.trigger("wpb_select_change",currentTaxonomy);
                    //changed_droopdown= _.without(changed_droopdown,taxonomy);
                   // checkVariationAttributesDimension(currentTaxonomy);
                    changed_droopdown.push(taxonomy);
                }
                if($(this).val()!=taxonomySelect.val()){
                    variationSelectChange(taxonomy,$(this).val());
                }

                selectedIndexChange(taxonomy);
            });
        });
        //checkVariationAttributesDimension(currentTaxonomy);
    }
    $variations_form.on("wpb_select_change",function(taxonomy){
    //   console.log(currentTaxonomy);

    });
    /**************************Tab Functions **************************/
    $(document).on('change','.wpb-rngtxt',function(){
        var taxonomy = $(this).data("taxonomy");
        variationSelectChange(taxonomy,$(this).val());
        checkVariationAttributesDimension(currentTaxonomy);
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
        currentTaxonomytype=tabType;
        deleteChekcked(currentTaxonomy,currentTaxonomytype);
        triggerFocusin(currentTaxonomy,currentTaxonomytype);
        checkVariationAttribute(currentTaxonomy,currentTaxonomytype);
        visitedTabCheck(currentTaxonomy);
        if(tabCount==visited_tabs.length){
            $("#wpb_continue_button").text(wpb_local_params.add_to_cart_text);
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
        var counter=0;
    var carouselFunction=function(){
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
                    type=containerDiv.find('.wpb_terms').data('type'),
                    select=$("select#"+taxonomyName);

                if(containerDiv.hasClass('wpb_disabled')){
                    return false;
                }
                if(select.val()!=term) {
                    variationSelectChange(taxonomyName, term);
                }
                checkVariationAttribute(currentTaxonomy,currentTaxonomytype);
                selectedIndexChange(taxonomyName);

            });
        });
    };

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
        $("#wpb_main_image_link").attr("href",main_image);
        $("#wpb_price_html").html(variation_data.price_html!=""?variation_data.price_html:$('.amount').text());
        $("#wpb_price_html").find(".price").removeAttr("style");
        refreshZoom();
    });
    $( document ).ajaxStop(function() {
        var noP='<p class="wc-no-matching-variations woocommerce-info">' + wc_add_to_cart_variation_params.i18n_no_matching_variations_text + '</p>';
        if($(".variation_id").val()==""){
            $("#wpb_no_found").html(noP);
            $("#wpb_price_html").html("");
        }else{
            $("#wpb_no_found").html("");
        }
    });

    /**************************Aditional Images*************************/
    $(document).on('click','.wpb_additional_image',function(e){
        e.preventDefault();
        var old_url=$(this).attr("src"),
            new_url=$("#wpb_main_images").attr("src");
        $(this).attr("src",new_url);
        $("#wpb_main_images").attr("src",old_url);
        $("#wpb_main_image_link").attr("href",old_url);
        refreshZoom();
    });
    /**************************Carousel Click*************************/
    $(document).on("click",'.wpb_terms',function(e){
        e.preventDefault();

        var carousel_id="wpb_carousel_"+$(this).data("taxonomy"),
            filterd_fr= _.findWhere(fr,{id:carousel_id}),
            move_index=$(this).data("counting"),
            parentDiv=$(this).parent();
        if($(parentDiv).hasClass('wpb_disabled')){
            return false;
        }
        filterd_fr.roll.moveToIndex(move_index);
    });
    /*****************************Rest********************************/
    $(document).on("click",".wpb_reset_button",function(e){
        e.preventDefault();
        var confirmation=confirm(wpb_local_params.resetText);
        if(confirmation){
            location.reload();
        }
    });
    /****************************Zoom*************************************/
    refreshZoom();
    /******************************Update variation values***************/
    $(window).load(function(){
        carouselFunction();
        triggerFocusin(currentTaxonomy,currentTaxonomytype);
        checkVariationAttribute(currentTaxonomy,currentTaxonomytype);
        showSelection(currentTaxonomy,currentTaxonomytype);
    });
});