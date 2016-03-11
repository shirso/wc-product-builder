<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('WPB_Frontend_Product')) {
    class WPB_Frontend_Product{
        public function __construct(){
            add_filter('body_class', array(&$this, 'add_class'));
            add_action('template_redirect', array(&$this, 'remove_main_image'));
            add_filter( 'woocommerce_available_variation',array( $this, 'alter_variation_json' ), 1, 3 );
            add_action( 'wp_ajax_wpb_info_box_load',array(&$this,'wpb_info_box_load'));
            add_action( 'wp_ajax_nopriv_wpb_info_box_load',array(&$this,'wpb_info_box_load'));
        }
        public function remove_main_image(){
            global $post;
            if (WPB_Common_Functions::wpb_enabled($post->ID)) {
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
                add_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 10);
                remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
                add_action('woocommerce_before_single_product_summary', array(&$this, 'add_product_designer'), 20);
                add_action('woocommerce_before_single_product_summary',array(&$this,'summary_wrapper_start'),23);
                add_action('woocommerce_before_single_product_summary',array(&$this,'image_wrapper_start'),26);
                add_action('woocommerce_before_single_product_summary',array(&$this,'show_product_images'), 30);
                add_action('woocommerce_before_single_product_summary', array(&$this,'image_wrapper_end'), 31);
                add_action('woocommerce_before_single_product_summary', array(&$this,'wpb_summary_div'), 35);
                add_action( 'woocommerce_after_single_product_summary', array(&$this,'summary_wrapper_end'), 5 );
                add_action( 'woocommerce_after_single_product_summary', array(&$this,'wpb_info_box'), 6 );
            }
        }
        public function wpb_info_box_load(){
            $postId=absint($_POST["productId"]);
            $taxonomy=esc_attr($_POST["taxonomy"]);
            $info_boxes=get_post_meta($postId,"_wpb_info_boxes",true);
            $first_content_id=($info_boxes[$taxonomy])?$info_boxes[$taxonomy]:null;
            $content_post = get_post($first_content_id);
            $content = $content_post->post_content;
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
            $final_content=($content)? '<div class="dtext-m-sec"><i class="fa fa-info-circle wpb_info_box_icon"></i>'.$content.'</div>' :'';
            echo $final_content;
            exit;
        }
        public function alter_variation_json($variation_data, $wc_product_variable, $variation_obj){
            $img_ids = WPB_Common_Functions::get_all_image_ids( $variation_data['variation_id'] );
            unset($img_ids[0]);
            $images = WPB_Common_Functions::get_all_image_sizes( $img_ids );
           $variation_data['additional_images'] = $images;
            return $variation_data;
        }
        public function wpb_info_box(){
            global $post, $wpdb, $product, $woocommerce;
            $postId=$product->id;
            $info_boxes=get_post_meta($postId,"_wpb_info_boxes",true);
            $attributes = $product->get_variation_attributes();
            $first_key = key($attributes);
            $first_content_id=($info_boxes[$first_key])?$info_boxes[$first_key]:null;
            $content_post = get_post($first_content_id);
            $content = $content_post->post_content;
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
            ?>
            <section class="ltx-msec" id="wpb_info_box_content">
                <?php if($content!=null){?>
                <div class="dtext-m-sec">
                    <i class="fa fa-info-circle wpb_info_box_icon"></i>
                    <?=$content;?>
                <?php }?>
                    </div>
            </section>
            <?php
        }
        public function summary_wrapper_start(){
            ?>
            <section class="result-cl-sec">
            <div class="row">
       <?php }
        public function summary_wrapper_end(){
            ?>
            </div>
            </section>
       <?php }
        public  function image_wrapper_start(){
           ?>
             <div class="col-sm-8">

            <?php
        }
        public function image_wrapper_end(){
          ?>
            </div>
<?php
        }
        public function wpb_summary_div(){
            global $post, $wpdb, $product, $woocommerce;
            $attributes = $product->get_variation_attributes();
            ?>
             <div class="col-sm-4">
                 <div class="mtx-sec clearfix">
                     <h2><?=__("Current Selection","wpb")?>:</h2>
                     <div class="table-responsive mtbl">
                         <?php if(!empty($attributes)){?>
                         <table class="table" cellspacing="10" cellpadding="25">
                             <tbody>
                              <?php foreach($attributes as $name=>$options){
                                  $attribute_type=WPB_Common_Functions::get_variation_attribute_type($name);
                                  ?>
                                  <tr id="wpb_selections_<?=$name;?>" class="wpb_hidden">
                                    <td class="name">
                                       <strong> <?=wc_attribute_label($name);?> </strong>
                                    </td>
                                      <?php if($attribute_type =="carousel"){?>
                                          <td><span class="values"></span></td>
                                        <?php }?>
                                      <?php if($attribute_type=="extra"){?>
                                      <td>
                                      <span class="values"></span> &nbsp; <span class="options"></span>
                                      </td>
                                        <?php }?>
                                      <?php if($attribute_type=="size"){?>
                                          <td class="sizeOptions">

                                          </td>
                                      <?php }?>
                                  </tr>
                                <?php }?>
                             </tbody>
                         </table>
                        <?php }?>
                     </div>
                     <h6><?=__("Total Price","wpb")?> : <span id="wpb_price_html"> </span></h6>
                     <div id="wpb_german_market"></div>
                     <div class="m-cntinu"><a href="#" id="wpb_continue_button"><?=__("Continue","wpb")?></a></div>
                     <a class="wpb_reset_button" href="#"><?=__('Reset','wpb');?></a>
                 </div>
             </div>
          <?php
        }
        public function show_product_images(){
            global $post, $woocommerce, $product;
            $default_variation_id=WPB_Common_Functions::get_default_variation_id();
            $initial_product_id = ($default_variation_id) ? $default_variation_id : $product->id;
            $initial_product_id = WPB_Common_Functions::get_selected_varaiton( $initial_product_id );
            $image_ids = WPB_Common_Functions::get_all_image_ids( $initial_product_id );
            $default_image=$image_ids[0];
            $other_images=$image_ids;
            unset($other_images[0]);

          ?>
             <div class="im-sd-sec" id="im-sd-sec">
                 <?php $default_url=wp_get_attachment_url($default_image);?>
                 <img src="<?=$default_url?>" class="img-responsive" id="wpb_main_images">
                 <?php if(!empty($other_images)){?>
                     <div class="sm-img-cl" id="wpb_additional_images">
                     <?php foreach($other_images as $img){
                         $other_url=wp_get_attachment_url($img);
                     ?>
                         <div class="blk-im">
                             <img src="<?=$other_url;?>" class="wpb_additional_image img-responsive">
                         </div>
                <?php }?>
                </div>
                <?php }?>
             </div>
        <?php
        }
        public function add_product_designer(){
            global $post, $wpdb, $product, $woocommerce;
            //$attributes = $product->get_variation_attributes();
            $attributes=maybe_unserialize(get_post_meta($post->ID, '_product_attributes', true));
            $allExtras=get_post_meta($post->ID,'_wpb_extras',true);
            $allSize=get_post_meta($post->ID,'_wpb_dimensions',true);
            $notAvilables=WPB_Common_Functions::notAvailableAttributes($post->ID);
            $reorderSizes=WPB_Common_Functions::reorderSize($post->ID);
            ?>
           <div id="wc-product-builder">
               <div id="wpb_steps" class="f-step">
                   <ul class="progress-indicator" id="progress-indicator">
                          <?php if(!empty($attributes)){
                              $c=0;
                                foreach($attributes as $name => $options){
                                if(!in_array($name,$notAvilables)){
                                 $attribute_type=WPB_Common_Functions::get_variation_attribute_type($name);
                                 $classes=$c==0 ? "completed acctive" : "";
                              ?>
                                    <li data-tab="#wpb-steps-<?=$name?>" data-taxonomy="<?=$name;?>" data-type="<?=$attribute_type?>" class="<?=$classes?>">
                                        <a href="#">
                                            <p><?=wc_attribute_label($name);?></p>
                                            <span class="bubble"></span>
                                        </a>
                                    </li>

                         <?php $c++; }}} ?>
                   </ul>
               </div>
                <div id="wpb_step_tab">
                    <?php if(!empty($attributes)){
                        $c=0;
                        foreach($attributes as $name => $options){
                            if(!in_array($name,$notAvilables)){
                            $classes=$c==0? 'wpb_onedblk' : 'wpb_aldnn';
                        ?>
                    <div id="wpb-steps-<?=$name?>" class="wpb_tabs <?=$classes?>">
                        <?php
                        $all_terms=get_terms($name);
                        $attribute_type=WPB_Common_Functions::get_variation_attribute_type($name);
                        $default_value=$product->get_variation_default_attribute($name);
                        ?>
                            <?php if($attribute_type=="carousel"){?>
                                <?=self::makeCarousel($all_terms,$name,$post->ID,$attribute_type,$default_value);?>
                             <?php }?>
                            <?php if($attribute_type=="extra"){ ?>
                            <?php if($options["is_variation"]==1){
                                echo self::makeCarousel($all_terms,$name,$post->ID,$attribute_type);
                                ?>
                               <?php }?>
                            <?php $extraCarousel=($allExtras[$name])? $allExtras[$name] : array(); ?>
                            <?php   if($extraCarousel){
                                foreach($extraCarousel as $carousel){
                                    $allTerms=get_terms($carousel);
                                    $carousel_default_value=$product->get_variation_default_attribute($carousel);
                                ?>
                                    <?=self::makeCarousel($allTerms,$carousel,$post->ID,'carousel',$carousel_default_value,true);?>
                              <?php }}?>
                            <?php }?>
                            <?php if($attribute_type=="dimension"){ ?>
                                    <?php $attributeDimensions=$reorderSizes[$name];
                                    if($attributeDimensions){
                                        foreach($attributeDimensions as $dimension){
                                            $regulator=$dimension[0];
                                            $selectBox=$dimension[1];
                                            $regulatorTerms=get_terms($regulator);
                                            $selectBoxTerms=get_terms($selectBox);
                                        ?>
                                      <div class="row">
                                           <div class="col-sm-7">
                                               <div class="rng-sl-sec">
                                                    <div class="rngsec">
                                                        <h2><?=wc_attribute_label($regulator);?></h2>
                                                        <div id="wpb_slider_<?=$regulator;?>"></div>
                                                        <span>123cm</span>
                                                        <span class="alr">150cm</span>
                                                    </div>
                                               </div>
                                           </div>
                                           <div class="col-sm-5">
                                                <div class="r-inp-sec clearfix">
                                                    <select>
                                                        <?php if($regulatorTerms){foreach($regulatorTerms as $r){?>
                                                            <?php if (has_term(absint($r->term_id), $regulator, $post->ID)) {?>
                                                            <option value="<?=$r->slug;?>"><?=$r->name;?></option>
                                                              <?php }?>
                                                        <?php }}?>
                                                     </select>
                                                    <div class="rthtx">
                                                        <h2><?=wc_attribute_label($selectBox);?>: </h2>
                                                         <select class="wpb-rngtxt">
                                                             <?php if($selectBoxTerms){foreach($selectBoxTerms as $s){?>
                                                                 <?php if (has_term(absint($s->term_id), $selectBox, $post->ID)) {?>
                                                                     <option value="<?=$s->slug;?>"><?=$s->name;?></option>
                                                                 <?php }?>
                                                             <?php }}?>
                                                         </select>
                                                        <p><?=__('Set','wpb')?></p>
                                                    </div>
                                                </div>
                                           </div>
                                      </div>
                                    <?php }?>
                                   <?php }?>
                            <?php }?>

                      </div>
                     <?php $c++;}}}?>
                    <section class="s-btn-sec wpb_aldnn" id="wpb_extra_options">
                        <h2><?=__('Additional Options','wpb')?></h2>
                            <?php if(!empty($attributes)){
                                foreach($attributes as $na=>$op){
                                    $attribute_type=WPB_Common_Functions::get_variation_attribute_type($na);
                                    if($attribute_type=="extra"){
                                    ?>
                         <div id="wpb_second_carousel_<?=$na?>" class="wpb_second_carousel wpb_aldnn">
                                   <?php $all_terms=get_terms($na);
                                    foreach($all_terms as $term){
                                     if (has_term(absint($term->term_id), $na, $post->ID)) {
                                         $term_options=get_option('_wpb_attribute_options_'.$term->term_id);
                                         if(!empty($term_options)){
                                ?>
                        <div class="wpb_extra_options_navigation wpb_aldnn" id="wpb_extra_container_<?=$na?>_<?=$term->term_id?>">
                            <div id="wpb_extra_carousel_<?=$term->term_id?>" data-term="<?=$term->term_id?>" class="wpb_extra_carousel">
                                             <?php $co =0; foreach($term_options as $option){ ?>
                                                 <div id="wpb_button_divs_<?=$term->term_id?>_<?=$co?>" class="gbtn wpb_extra" data-counting="<?=$co?>" data-text="<?=$option;?>" data-termid="<?=$term->term_id;?>" data-term="<?=$term->slug;?>" data-taxonomy="<?=$name?>">
                                                     <?=$option;?>
                                                 </div>
                                                 <?php $co++; }?>
                            </div>
                            <a href="#" id="wpb_extra_carousel_<?=$term->term_id?>_left" class="btn-primary1"> &lt; </a>
                            <a href="#"  id="wpb_extra_carousel_<?=$term->term_id?>_right"  class="btn-primary2"> &gt; </a>
                         </div>
                              <?php }}}?>
                         </div>
                            <?php }}?>
                           <?php }?>
                    </section>
                </div>
           </div>
        <?php
        }
        public function makeCarousel($all_terms,$taxonomy,$productId,$attributeType,$default_value,$isExtra=false){
            $default=0;
            if(!empty($all_terms)){
                ?>
                <figure class="slt-sldr-sec">
                    <?php if($isExtra){?>
                        <h2><?=wc_attribute_label($taxonomy);?></h2>
                    <?php }?>
                    <div id='wpb_carousel_<?=$taxonomy;?>' class='wpb_carousel'>
                        <?php $counting=0; foreach($all_terms as $term){
                        if (has_term(absint($term->term_id), $taxonomy, $productId)) {
                            $term_image=get_option('_wpb_variation_attr_image_'.$term->term_id);
                            ?>
                            <?php if(!empty($term_image)){?>
                                <div id='<?=$taxonomy?>_<?=$counting?>'>
                                    <a class="wpb_terms" data-taxonomy="<?=$taxonomy;?>" data-term="<?=$term->slug;?>" data-type="<?=$attributeType;?>" data-counting="<?=$counting?>" href="#"><img src="<?=$term_image?>"><span><?=$term->name;?></span></a>
                                </div>
                            <?php }?>
                            <?php if($default_value==$term->slug){$default=$counting;} $counting++;?>
                            <?php }}?>
                     </div>
                    <input type="hidden" id="wpb_carousel_<?=$taxonomy;?>_default" value="<?=$default;?>">
                    <a href='#' class='btn-primary1' id='wpb_carousel_<?=$taxonomy;?>_left'>&lsaquo;</a>
                    <a href='#' class='btn-primary2' id='wpb_carousel_<?=$taxonomy;?>_right'>&rsaquo;</a>
                </figure>
           <?php }
        }
        public function add_class($classes)
        {
            global $post;
            if (WPB_Common_Functions::wpb_enabled($post->ID)) {
                $classes[] = 'wpb-body-product';
            }
            return $classes;
        }
    }
    new WPB_Frontend_Product();
}