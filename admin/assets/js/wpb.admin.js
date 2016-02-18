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
            title: 'Manage Variation Images',
            button: {
                text: 'Add to variation',
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
								<a href="#" class="delete" title="Delete image"><img src="' + attachment.url + '" /></a>\
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
});
