<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('WPB_Frontend_Product')) {
    class WPB_Frontend_Product{
        public function __construct(){
            add_filter('body_class', array(&$this, 'add_class'));
            add_action('template_redirect', array(&$this, 'remove_main_image'));
            add_filter( 'woocommerce_available_variation',array( $this, 'alter_variation_json' ), 1, 3 );
        }
        public function remove_main_image(){
            global $post;
            if ($this->wpb_enabled($post->ID)) {
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
            }
        }
        public function alter_variation_json($variation_data, $wc_product_variable, $variation_obj){
            $img_ids = $this->get_all_image_ids( $variation_data['variation_id'] );
            unset($img_ids[0]);
            $images = $this->get_all_image_sizes( $img_ids );
           $variation_data['additional_images'] = $images;
            return $variation_data;
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
            ?>
             <div class="col-sm-4">
                 <div class="mtx-sec clearfix">
                     <h2>Aktuelle Auswahl:</h2>
                     <div class="table-responsive mtbl">
                         <table class="table">
                             <tbody>
                             <tr class="grn"><td>Modell:</td><td>Toni</td></tr>
                             <tr class="grn"><td>Holzart:</td><td>Eiche geolt</td></tr>
                             <tr><td>Lange</td><td>180cm</td></tr>
                             <tr><td>Breite:	</td><td>60cm</td></tr>
                             <tr><td>Kantenprofil:</td><td>abgerundet</td></tr>
                             <tr><td>Gestell:</td><td>8 x 4cm Eiche</td></tr>
                             <tr><td>Extras:</td><td>ausziehbar</td></tr>
                             </tbody>
                         </table>
                     </div>
                     <h6>Aktueller Preis : <span> 1299,00 &euro;</span></h6>
                     <p>inkl. 19% MwSt, versandkostenfrei Lieferzeit 4 Wochen</p>
                     <div class="m-cntinu"><a href="#" id="wpb_continue_button"><?=__("Continue","wpb")?></a></div>
                 </div>
             </div>
          <?php
        }
        public function show_product_images(){
            global $post, $woocommerce, $product;
            $default_variation_id=self::get_default_variation_id();
            $initial_product_id = ($default_variation_id) ? $default_variation_id : $product->id;
            $initial_product_id = self::get_selected_varaiton( $initial_product_id );
            $image_ids = self::get_all_image_ids( $initial_product_id );
          //  $default_image_ids = $this->get_all_image_ids( $product->id );
            $default_image=$image_ids[0];
            $other_images=$image_ids;
            unset($other_images[0]);

          ?>
             <div class="im-sd-sec">
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
            $attributes = $product->get_variation_attributes();
            $attribute_types=self::get_variation_attributes_types($attributes);
            ?>
           <div id="wc-product-builder">

               <div id="wpb_steps" class="f-step">
                   <ul class="progress-indicator" id="progress-indicator">
                       <?php if(!empty($attributes)){
                           $c=0;
                           foreach($attributes as $name => $options){
                               $attribute_type=self::get_variation_attribute_type($name);
                           ?>
                            <li data-tab="#wpb-steps-<?=$name?>" data-type="<?=$attribute_type?>" <?php if($c==0){?>class="completed acctive"<?php }?>>
                                <a href="#">
                                <p><?=wc_attribute_label($name);?></p>
                                    <span class="bubble"></span>
                                 </a>
                            </li>
                        <?php $c++;}}?>
                   </ul>
               </div>
                <div id="wpb_step_tab">

            <?php if(!empty($attributes)){
                $c=0;
                foreach($attributes as $name => $options){
                    $classes=$c==0? 'wpb_onedblk' : 'wpb_aldnn'
                  ?>
                    <div id="wpb-steps-<?=$name?>" class="wpb_tabs <?=$classes?>">
                        <?php
                        $all_terms=get_terms($name);
                        $attribute_type=self::get_variation_attribute_type($name);
                        $default_value=$product->get_variation_default_attribute($name);
                        $default=0;
                        ?>
                          <?php if($attribute_type=="carousel"){?>
                              <?php if(!empty($all_terms)){?>
                        <figure class="slt-sldr-sec">
                            <div id='wpb_carousel_<?=$name;?>' class='wpb_carousel'>
                            <?php $counting=0; foreach($all_terms as $term){?>
                                <?php  if (has_term(absint($term->term_id), $name, $post->ID)) {
                                    $term_image=get_option('_wpb_variation_attr_image_'.$term->term_id);
                                    ?>
                                    <?php if(!empty($term_image)){?>
                                        <div id='<?=$name?>_<?=$counting?>'>
                                            <a class="wpb_terms" data-taxonomy="<?=$name;?>" data-term="<?=$term->slug;?>" data-type="<?=$attribute_type;?>" data-counting="<?=$counting?>" href="#"><img src="<?=$term_image?>"><span><?=$term->name;?></span></a>
                                        </div>
                                    <?php }?>
                                <?php if($default_value==$term->slug){$default=$counting;} $counting++;}?>
                            <?php }?>
                             </div>
                            <input type="hidden" id="wpb_carousel_<?=$name;?>_default" value="<?=$default;?>">
                            <a href='#' class='btn-primary1' id='wpb_carousel_<?=$name;?>_left'>&lsaquo;</a>
                            <a href='#' class='btn-primary2' id='wpb_carousel_<?=$name;?>_right'>&rsaquo;</a>
                         </figure>
                            <?php }?>
                        <?php }?>
                        <?php if($attribute_type=="extra"){ ?>
                            <?php if(!empty($all_terms)){?>
                                <figure class="slt-sldr-sec">
                                    <div id='wpb_carousel_<?=$name;?>' class='wpb_carousel'>
                                        <?php $counting=0; foreach($all_terms as $term){?>
                                            <?php  if (has_term(absint($term->term_id), $name, $post->ID)) {
                                                $term_image=get_option('_wpb_variation_attr_image_'.$term->term_id);
                                                ?>
                                                <?php if(!empty($term_image)){?>
                                                    <div id='<?=$name?>_<?=$counting?>'>
                                                        <a class="wpb_terms" data-taxonomy="<?=$name;?>" data-term="<?=$term->slug;?>" data-type="<?=$attribute_type;?>" data-counting="<?=$counting?>" href="#"><img src="<?=$term_image?>"><span><?=$term->name;?></span></a>
                                                        <div class="wpb_button_div wpb_hidden">
                                                            <?php $term_options=get_option('_wpb_attribute_options_'.$term->term_id)?>
                                                                <?php if(!empty($term_options)){ ?>
                                                                    <?php foreach($term_options as $option){ ?>
                                                                        <button class="wpb_extra"><?=$option;?></button>
                                                                        <?php }?>
                                                                 <?php }?>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <?php if($default_value==$term->slug){$default=$counting;} $counting++;}?>
                                        <?php }?>
                                    </div>
                                    <input type="hidden" id="wpb_carousel_<?=$name;?>_default" value="<?=$default;?>">
                                    <a href='#' class='btn-primary1' id='wpb_carousel_<?=$name;?>_left'>&lsaquo;</a>
                                    <a href='#' class='btn-primary2' id='wpb_carousel_<?=$name;?>_right'>&rsaquo;</a>
                                </figure>
                            <?php }?>
                        <?php }?>
                        <?php if($attribute_type=="size"){ ?>
                        <?php if(!empty($all_terms)){?>
                            <?php foreach($all_terms as $term){?>
                                <?php  if (has_term(absint($term->term_id), $name, $post->ID)) {?>
                                   <?php $term_size_option=get_option('_wpb_size_options_'.$term->term_id); ?>
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="rng-sl-sec">
                                    <div class="rngsec">
                                        <h2><?=@$term_size_option["regulator_title"]?></h2>
                                <div class="wbp_slider" id="wpb_slider_<?=$term->term_id;?>" data-text="wpb_slider_value_<?=$term->term_id;?>" data-min="<?=@$term_size_option["regulator_min"]?>" data-step="10" data-max="<?=@$term_size_option["regulator_max"]?>"></div>
                                        <span><?=@$term_size_option["regulator_min"]?> <?=@$term_size_option["regulator_unit"]?></span>
                                        <span class="alr"><?=@$term_size_option["regulator_max"]?> <?=@$term_size_option["regulator_unit"]?></span>
                                     </div>
                                 </div>
                             </div>
                            <div class="col-sm-5">
                                <div class="r-inp-sec clearfix">
                                    <input type="text" id="wpb_slider_value_<?=$term->term_id;?>">
                                    <span><?=@$term_size_option["regulator_unit"]?></span>
                                    <div class="rthtx">
                                        <h2><?=@$term_size_option["dropdown_title"]?>:
                                            <select class="wpb-rngslct">
                                                <option value="">---</option>
                                                <?php if(!empty($term_size_option["dropdown_options"])){ ?>
                                                    <?php foreach($term_size_option["dropdown_options"] as $option){ ?>
                                                        <option value="<?=$option?>"><?=$option?></option>
                                                     <?php }?>
                                                <?php }?>
                                            </select><?=@$term_size_option["dropdown_unit"]?></h2>
                                        <p><?=__('Set','wpb')?></p>
                                    </div>
                                </div>
                             </div>
                        </div>
                                    <?php }?>
                                <?php }?>
                         <?php }?>
                        <?php }?>
                    </div>
                 <?php
                    $c++;
                }
            }?>
                    <section class="s-btn-sec wpb_hidden" id="wpb_extra_options">
                        <div class="container">
                            <h2><?=__('Additional Options','wpb')?></h2>
                            <div class="gbtn" id="wpb_button_div">

                            </div>
                        </div>
                    </section>
                </div>
           </div>
        <?php
        }

        public function add_class($classes)
        {
            global $post;
            if ($this->wpb_enabled($post->ID)) {
                $classes[] = 'wpb-body-product';
            }
            return $classes;
        }
        public function wpb_enabled($product_id)
        {
            global $sitepress;
            if ($sitepress && method_exists($sitepress, 'get_original_element_id')) {
                $product_id = $sitepress->get_original_element_id($product_id, 'post_product');
            }
            return get_post_meta($product_id, '_wpb_check', true) == 'yes' && get_post_type($product_id) == 'product';
        }
        public function wbm_convert_string_value_to_int($value)
        {

            if ($value == 'yes') {
                return 1;
            } else if ($value == 'no') {
                return 0;
            } else {
                return $value;
            }

        }
        public function get_variation_attributes_types( $attributes ) {
            global $wpdb;
            $types = array();
            if( !empty($attributes) ) {
                foreach( $attributes as $name => $options ) {
                    $attribute_name = substr($name, 3);
                    $attribute = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute_name'");
                    $types[$name] = $attribute->attribute_type;
                }
            }
            return $types;
        }
        public function get_variation_attribute_type( $name ) {
            global $wpdb;
            $attribute_name = substr($name, 3);
            $attribute = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute_name'");
            return  $attribute->attribute_type;
        }
        public function get_default_variation_id(){
            global $post, $woocommerce, $product;
            global $post, $woocommerce, $product;

            $defaultVarId = $product->id;

            if($product->product_type == 'variable'){

                $defaults = $product->get_variation_default_attributes();
                $variations = array_reverse($product->get_available_variations());

                if(!empty($defaults)){
                    foreach($variations as $variation){

                        $varCount = count($variation["attributes"]);

                        $attMatch = 0; $partMatch = 0; foreach($defaults as $dAttName => $dAttVal){
                            // $defaultVarId = false;
                            if(isset($variation["attributes"]['attribute_'.$dAttName])) {
                                $theAtt = $variation["attributes"]['attribute_'.$dAttName];
                                if($theAtt == $dAttVal) {
                                    $attMatch++;
                                    $partMatch++;
                                }
                                if($theAtt == ""){
                                    $partMatch++;
                                }
                            }
                        }

                        if($varCount == $partMatch) {
                            $defaultVarId = $variation['variation_id'];
                        }

                        if($varCount == $attMatch) {
                            $defaultVarId = $variation['variation_id'];
                        }
                    }
                }

            }

            return $defaultVarId;
        }
        public function get_selected_varaiton($currId){
            global $post, $woocommerce, $product;

            if($product->product_type == 'variable'){

                $variations = $product->get_available_variations();

                foreach($variations as $variation)
                {
                    $attCount = count($variation['attributes']);
                    $attMatches = 0;

                    foreach($variation['attributes'] as $attKey => $attVal)
                    {
                        if(isset($_GET[$attKey]) && $_GET[$attKey] == $attVal) $attMatches++;
                    }

                    if($attCount == $attMatches) $currId = $variation['variation_id'];
                }

            }

            return $currId;
        }
        public function get_all_image_ids($id){

            $allImages = array();
            $show_gallery = false;

            // Main Image
            if(has_post_thumbnail($id)){

                $allImages[] = get_post_thumbnail_id($id);

            } else {

                $prod = get_post($id);
                $prodParentId = $prod->post_parent;
                if($prodParentId && has_post_thumbnail($prodParentId)){
                    $allImages[] = get_post_thumbnail_id($prodParentId);
                } else {
                    $allImages[] = 'placeholder';
                }

                $show_gallery = true;
            }

            // WooThumb Attachments
            if(get_post_type($id) == 'product_variation'){
                $wtAttachments = array_filter(explode(',', get_post_meta($id, '_wpb_variation_images', true)));
                $allImages = array_merge($allImages, $wtAttachments);
            }

            // Gallery Attachments

            if(get_post_type($id) == 'product' || $show_gallery){
                $product = get_product($id);
                $attachIds = $product->get_gallery_attachment_ids();

                if(!empty($attachIds)){
                    $allImages = array_merge($allImages, $attachIds);
                }
            }

            return $allImages;
        }
        public function get_all_image_sizes($imgIds){
            $images = array();
            if(!empty($imgIds)) {
                foreach ($imgIds as $imgId){

                        if (!array_key_exists($imgId, $images)) {
                          //  $attachment = $this->wp_get_attachment_url($imgId);
                            $images[] = wp_get_attachment_url($imgId);
                        }

                }
            }
            return $images;
        }

    }
    new WPB_Frontend_Product();
}