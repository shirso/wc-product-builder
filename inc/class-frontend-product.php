<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('WPB_Frontend_Product')) {
    class WPB_Frontend_Product{
        public function __construct(){
            add_filter('body_class', array(&$this, 'add_class'));
            add_action('template_redirect', array(&$this, 'remove_main_image'));
        }
        public function remove_main_image(){
            global $post;
            if ($this->wpb_enabled($post->ID)) {
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
                add_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 10);
                remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
                add_action('woocommerce_before_single_product_summary', array(&$this, 'add_product_designer'), 20);
                add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 30);
            }
        }
        public function add_product_designer(){
            global $post, $wpdb, $product, $woocommerce;
            $attributes = $product->get_variation_attributes();
            $attribute_types=self::get_variation_attributes_types($attributes);
            ?>
           <div id="wc-product-builder">

               <div id="wpb_steps" class="f-step">
                   <ul class="progress-indicator">
                       <?php if(!empty($attributes)){
                           $c=0;
                           foreach($attributes as $name => $options){
                               $attribute_type=self::get_variation_attribute_type($name);
                           ?>
                            <li data-tab="#wpb-steps-<?=$name?>" data-type="<?=$attribute_type?>" <?php if($c==0){?>class="completed acctive"<?php }?>>
                                <p><?=wc_attribute_label($name);?></p>
                                    <span class="bubble"></span>
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
                                        <div id='<?=$name?>_<?=$counting?>' data-taxonomy="<?=$name;?>" data-term="<?=$term->term_id;?>" data-type="<?=$attribute_type;?>">
                                            <a class="wpb_terms" href="#"><img src="<?=$term_image?>"><span><?=$term->name;?></span></a>
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
                                                    <div id='<?=$name?>_<?=$counting?>' data-taxonomy="<?=$name;?>" data-term="<?=$term->term_id;?>" data-type="<?=$attribute_type;?>">
                                                        <a class="wpb_terms" href="#"><img src="<?=$term_image?>"><span><?=$term->name;?></span></a>
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

    }
    new WPB_Frontend_Product();
}