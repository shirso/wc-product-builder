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
});
