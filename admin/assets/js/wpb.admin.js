jQuery(function($){
    var mediaUploader = null;
    $( ".wpb_image_thumb" ).sortable({
        deactivate: function(en, ui) {
            imageValueChange($(ui.item).parent());
        },
        placeholder: 'ui-state-highlight'
    });

    $(document).on('click','.wpb_upload_button',function(e){
        e.preventDefault();
        var self=$(this);
        mediaUploader = wp.media({
            multiple: false
        });
        mediaUploader.on('select', function () {
            $(self).prev().val(mediaUploader.state().get('selection').toJSON()[0].url);
        });
        mediaUploader.open();
    });
    var product_gallery_frame;
    $(document).on('click','.wpb_multiple_image_upload',function(e){
        e.preventDefault();
        var $image_gallery_ids = $(this).siblings('.wpb_variation_image_gallery');
        var attachment_ids = $image_gallery_ids.val();
        var $wooThumbs = $(this).siblings('.wpb_image_thumb');
        product_gallery_frame = wp.media.frames.downloadable_file = wp.media({
            title: wpb_local_variables.popup_title,
            button: {
                text:wpb_local_variables.button_text ,
            },
            multiple: true

        });
        product_gallery_frame.on( 'select', function() {
            var selection = product_gallery_frame.state().get('selection');
            selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                if ( attachment.id ) {
                    attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
                    $wooThumbs.append('\
							<li class="image" data-attachment_id="' + attachment.id + '">\
								<a href="#" class="delete" title="'+wpb_local_variables.delete_image+'"><img src="' + attachment.url + '" /></a>\
							</li>');
                }
            });
            $image_gallery_ids.val( attachment_ids );
        });
        product_gallery_frame.open();
        $( ".wpb_image_thumb" ).sortable({
            deactivate: function(en, ui) {
                imageValueChange($(ui.item).parent());
            },
            placeholder: 'ui-state-highlight'
        });
        return false;
    });
    $(document).on('mouseenter mouseleave click','.wpb_image_thumb .delete',function(e){
        if (e.type == 'click') {
            var $tableCol = $(this).closest('li').parent();
            $(this).closest('li').remove();
            imageValueChange($tableCol);
            return false;
        }
        if (e.type == 'mouseenter') {
            $(this).find('img').animate({"opacity": 0.3}, 150);
        }
        if (e.type == 'mouseleave') {
            $(this).find('img').animate({"opacity": 1}, 150);
        }
    });
    var imageValueChange=function($tableCol){
        var $selectedImgs = [];
        $tableCol.find('.image').each(function(){
            $selectedImgs.push($(this).attr('data-attachment_id'));
        });
        $tableCol.parent().find('.wpb_variation_image_gallery').val($selectedImgs.join(','));
    }
    if(typeof normaal_sheepit!="undefined") {
        $('#wpb_normal_sheepit').sheepIt({
            separator: '',
            allowRemoveLast: true,
            allowRemoveCurrent: true,
            allowRemoveAll: false,
            allowAdd: true,
            allowAddN: false,
            maxFormsCount: 100,
            minFormsCount: 0,
            iniFormsCount: 0,
            data: inject_data_options
        });
    }
    if(typeof wpb_product_page!="undefined"){
        if($("#_wpb_check").is(':checked')){
            $('.show_if_wpb_panel').removeClass('wpb_attribute_options');
        }else{
            $('.show_if_wpb_panel').addClass('wpb_attribute_options');
        }
        $(document).on('change','#_wpb_check',function(){
            if($(this).is(":checked")){
                $('.show_if_wpb_panel').removeClass('wpb_attribute_options');
            }else{
                $('.show_if_wpb_panel').addClass('wpb_attribute_options');
                if($("#wpb_instructions_tab").is(':visible')){
                    $("#wpb_instructions_tab").hide();
                }
                if($("#wpb_dimension_tab").is(':visible')){
                    $("#wpb_dimension_tab").hide();
                }
                if($("#wpb_extra_tab").is(':visible')){
                    $("#wpb_extra_tab").hide();
                }
            }
        });

        if($('.wpb_dimension').length>0){
            $('.wpb_dimension').each(function(k,v){
               var id=$(this).attr("id"),
                   inject_data=$(this).next();
                console.log($.parseJSON(inject_data.val()));
                var sheepItForm = $('#'+id).sheepIt({
                    separator: '',
                    allowRemoveLast: true,
                    allowRemoveCurrent: true,
                    allowRemoveAll: false,
                    allowAdd: true,
                    allowAddN: false,
                    maxFormsCount: 10,
                    minFormsCount: 0,
                    iniFormsCount: 1,
                    data:$.parseJSON(inject_data.val())
                });
            });
        }

        $(document).on('click','.wpb_save_dimensions',function(e){
            e.preventDefault();
            postData('save_dimension','#wpb_dimension_tab');
        });

        var postData=function(type,tabId){
            $(tabId ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            var data = {
                post_id:  woocommerce_admin_meta_boxes.post_id,
                data:     $( tabId ).find( 'input, select, textarea' ).serialize(),
                action:   'wpb_save_from_admin',
                type:type
            };
            $.post( woocommerce_admin_meta_boxes.ajax_url, data, function(resp) {
                    $(tabId ).unblock();
            });
        }
    }
});
