//
//$('.bxslider').bxSlider({
//    pager:false
//});
jQuery(function($){
 $(document).on('click','.progress-indicator li p',function(e){
     e.preventDefault();
     var $li=$(this).parent();
     if(!$li.hasClass('completed')){
         var tabId=$li.data('tab');
         $('.wpb_tabs').removeClass('wpb_onedblk');
         $('.wpb_tabs').addClass('wpb_aldnn');
         $(tabId).removeClass('wpb_aldnn');
         $(tabId).addClass('wpb_onedblk');
         $li.parent().find('li').removeClass('completed');
         $li.addClass('completed');
     }
 })
});