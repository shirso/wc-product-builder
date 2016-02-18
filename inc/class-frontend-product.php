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
            print_r($attribute_types);
            ?>
           <div id="wc-product-builder">
               <div id="wpb_steps" class="f-step">
                   <ul class="progress-indicator">
                       <?php if(!empty($attributes)){
                           $c=0;
                           foreach($attributes as $name => $options){
                           ?>
                            <li data-tab="#wpb-steps-<?=$name?>" <?php if($c==0){?>class="completed"<?php }?>>
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
                    <div id="wpb-steps-<?=$name?>" class="wpb_tabs <?=$classes?>"><?=wc_attribute_label($name);?>

                    </div>
                 <?php
                    $c++;
                }
            }?>
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
    }
    new WPB_Frontend_Product();
}