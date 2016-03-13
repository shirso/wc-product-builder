jQuery(function($){
   var  $variations_form = $('form.variations_form'),
        variations_json = $variations_form.attr('data-product_variations'),
        variations = ( typeof variations_json !== "undefined" ) ? $.parseJSON( variations_json ) : false,
        currentTaxonomy=$("#progress-indicator").find("li:first").data("taxonomy"),
        currentTermId=null,
        visited_tabs=[],
        fr=[];
    visited_tabs.push($("#progress-indicator").find("li:first").data("tab"));
    var tabCount=$("#progress-indicator").find("li").length;
    /****************************Common Functions***********************/
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

    /**************************Tab Functions **************************/
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
       //  console.log($("#"+taxonomyName).val());
            if( $("#"+taxonomyName).val()!=term) {
               // console.log($("#"+taxonomyName).val());
              //  $("#" + taxonomyName).focusin().val(term).change();
            }
        });
    });
    /**************************WC Variation values Update **************************/
    $variations_form.on("update_variation_values",function(event, variations){
        console.log(variations);
    });

});