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
        product_gallery_frame = wp.media.frames.downloadable_file = wp.media({
            title: 'Manage Variation Images',
            button: {
                text: 'Add to variation',
            },
            multiple: true

        });
        product_gallery_frame.open();
    });
});
