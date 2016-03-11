<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('WPB_Common_Functions')) {
    class WPB_Common_Functions{
        public function get_all_image_sizes($imgIds){
            $images = array();
            if(!empty($imgIds)) {
                foreach ($imgIds as $imgId){

                    if (!array_key_exists($imgId, $images)) {
                        $images[] = wp_get_attachment_url($imgId);
                    }

                }
            }
            return $images;
        }
        public function get_all_image_ids($id){

            $allImages = array();
            $show_gallery = false;


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


            if(get_post_type($id) == 'product_variation'){
                $wtAttachments = array_filter(explode(',', get_post_meta($id, '_wpb_variation_images', true)));
                $allImages = array_merge($allImages, $wtAttachments);
            }



            if(get_post_type($id) == 'product' || $show_gallery){
                $product = get_product($id);
                $attachIds = $product->get_gallery_attachment_ids();

                if(!empty($attachIds)){
                    $allImages = array_merge($allImages, $attachIds);
                }
            }

            return $allImages;
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
        public function get_variation_attribute_type( $name ) {
            global $wpdb;
            $attribute_name = substr($name, 3);
            $attribute = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute_name'");
            return  $attribute->attribute_type;
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
        public function wpb_enabled($product_id)
        {
            global $sitepress;
            if ($sitepress && method_exists($sitepress, 'get_original_element_id')) {
                $product_id = $sitepress->get_original_element_id($product_id, 'post_product');
            }
            return get_post_meta($product_id, '_wpb_check', true) == 'yes' && get_post_type($product_id) == 'product';
        }
       public function notAvailableAttributes($productId){
           $allExtras=get_post_meta($productId,'_wpb_extras',true);
           $allSizes=get_post_meta($productId,'_wpb_dimensions',true);
           $extras=($allExtras) ? array_values($allExtras) : array();
           $sizes=($allSizes) ? array_values($allSizes) : array();
           $notAvilable=array();
           if(!empty($extras)){
               foreach($extras as $e){
                   if(is_array($e) && !empty($e)){
                       //if(!in_array())
                       foreach($e as $v){
                           if(!in_array($v,$notAvilable)){
                               array_push($notAvilable,$v);
                           }
                       }
                   }
               }
           }
           if(!empty($sizes)){
               foreach($sizes as $s){
                   if(is_array($s) && !empty($s)){
                      $h= self::flatten($s);
                       foreach($h as $k){
                           if(!in_array($k,$notAvilable)){
                               array_push($notAvilable,$k);
                           }
                       }
                   }
               }
           }

           return $notAvilable;
       }
        public function reorderSize($producId){
            $allDimensions=get_post_meta($producId,'_wpb_dimensions',true);

        }

        public function flatten(array $array) {
            $return = array();
            array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
            return $return;
        }

    }
    new WPB_Common_Functions();
}