jQuery(function($){
    var mediaUploader = null;
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

                }
            });
            $image_gallery_ids.val( attachment_ids );
        });
        product_gallery_frame.open();
        return false;
    });
});
