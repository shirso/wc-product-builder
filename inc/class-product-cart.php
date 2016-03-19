<?php if(!defined('ABSPATH')) exit;
if (!class_exists('WPB_Product_Cart')) {
    class WPB_Product_Cart{
        private $wpb_cart_item=null;
        public function __construct(){
           /// add_filter('woocommerce_add_cart_item_data', array(&$this, 'add_cart_item_data'), 10, 2);
         //   add_filter('woocommerce_get_cart_item_from_session', array(&$this, 'get_cart_item_from_session'), 10, 2);
        //    add_filter('woocommerce_get_item_data', array(&$this, 'get_item_data'), 10, 2);
            add_filter('add_to_cart_redirect', array(&$this, 'redirect_to_checkout'));
         //  add_action('woocommerce_add_order_item_meta', array(&$this, 'order_item_meta'), 1, 3);
         //   add_filter('woocommerce_order_items_meta_get_formatted',array(&$this,'woocommerce_order_items_meta_get_formatted'),10,2);
         //   add_action("woocommerce_order_item_meta_start",array(&$this,"woocommerce_order_item_meta_start"),100,3);
          //  add_action('woocommerce_before_order_itemmeta',array(&$this,'woocommerce_before_order_itemmeta'),10,3);
        }
        function add_cart_item_data($cart_item_meta, $product_id){
            if (isset($cart_item_meta['wpb_cart_items'])) {
                return $cart_item_meta;
            }
            if (WPB_Common_Functions::wpb_enabled($product_id) && isset($_POST["wpb_cart_items"])) {
                $cart_item_meta['wpb_cart_items'] = $_POST["wpb_cart_items"];
            }
            return $cart_item_meta;
        }
        function get_cart_item_from_session($cart_item, $values){
            if (isset($values['wpb_cart_items'])) {
                $cart_item['wpb_cart_items'] = $values['wpb_cart_items'];
            }
            return $cart_item;
        }
        function redirect_to_checkout() {
            return wc_get_cart_url();
        }
        function get_item_data($other_data, $cart_item){
            if(isset($cart_item["wpb_cart_items"])) {
                $wpb_cart_items=$cart_item["wpb_cart_items"];
                $other_data = array();
                $variation_data = $cart_item["variation"];

                if (!empty($variation_data)) {
                    foreach ($variation_data as $attribute => $variation) {
                        $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $attribute ) ) );
                        $term = get_term_by( 'slug', $variation, $taxonomy );
                        $attribute_type = WPB_Common_Functions::get_variation_attribute_type($taxonomy);
                        if ($attribute_type == "carousel") {
                            $other_data[] = array('name' =>wc_attribute_label($taxonomy), 'display' => $term->name, 'value' => '','hidden'=>false);
                        }
                        if($attribute_type=="extra"){
                            $extra_options=$wpb_cart_items["extra"];
                            $display_label=!empty($extra_options[$taxonomy]) ? $term->name. '('.$extra_options[$taxonomy].')' : $term->name;
                            $other_data[] = array('name' =>wc_attribute_label($taxonomy), 'display' =>$display_label, 'value' => '','hidden'=>false);
                        }
                        if($attribute_type=="size"){
                            $size_options=$wpb_cart_items["size"];
                            $display_label=!empty($size_options[$taxonomy]) ?$size_options[$taxonomy] : "";
                            $other_data[] = array('name' =>wc_attribute_label($taxonomy), 'display' =>$display_label, 'value' => '','hidden'=>false);
                        }
                    }
                }
            }
            return $other_data;
        }
        public function order_item_meta($item_id,  $values, $cart_item_key){
          //  wc_add_order_item_meta( $item_id, "gift_wrap", 'Yes' );
            if(isset($values["wpb_cart_items"])){
                wc_add_order_item_meta( $item_id, "wpb_cart_items",  $values["wpb_cart_items"] );
            }
        }
        public function woocommerce_order_items_meta_get_formatted($formatted_meta,$obj){
            foreach($formatted_meta as $key=>$meta){
                $taxonomy=$meta["key"];
                $original_value=$meta["value"];
                $attribute_type=WPB_Common_Functions::get_variation_attribute_type($taxonomy);
                if($attribute_type=="extra"){
                   $extra_data=($this->wpb_cart_item["extra"])?$this->wpb_cart_item["extra"]:array();
                   if(isset($extra_data[$taxonomy])){
                       $formatted_meta[$key]["value"]=$original_value." (".$extra_data[$taxonomy].")";
                   }
                }
                if($attribute_type=="size"){
                    $size_data=($this->wpb_cart_item["size"])?$this->wpb_cart_item["size"]:array();
                    if(isset($size_data[$taxonomy])){
                        $formatted_meta[$key]["value"]=$size_data[$taxonomy];
                    }
                }
            }
           // $formatted_meta=array();
          ///  print_r("test");
            return $formatted_meta;
        }
        public function woocommerce_order_item_meta_start($item_id, $item, $order){
            if(isset($item["item_meta"]["wpb_cart_items"])){
                $this->wpb_cart_item=maybe_unserialize($item["item_meta"]["wpb_cart_items"][0]);

            }
        }
        public function woocommerce_before_order_itemmeta($item_id, $item, $_product){
            print_r($item);
        }
    }
    new WPB_Product_Cart();
}
