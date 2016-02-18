<?php
if ( !defined( 'ABSPATH' ) ) exit;
if( !class_exists('WPB_Admin_Product') ) {
    class WPB_Admin_Product {
        public function __construct() {
            add_action('woocommerce_product_option_terms', array(&$this, 'product_option_terms'), 10, 2);
            add_action('woocommerce_variation_options',array(&$this,'woocommerce_variation_options'),10,3);
            add_action( 'woocommerce_save_product_variation',array(&$this,'save_variation_settings_fields'), 10, 2 );
        }
        public function product_option_terms($tax, $i){
            global $woocommerce, $thepostid;
            if( in_array( $tax->attribute_type, array( 'carousel', 'size', 'carousel_with_option' ) ) ) {
                $attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
            ?>
            <select multiple="multiple" data-placeholder="<?php _e( 'Select terms', 'yit' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i; ?>][]">
                <?php
                $all_terms = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
                if ( $all_terms ) {
                    foreach ( $all_terms as $term ) {
                        $has_term = has_term( (int) $term->term_id, $attribute_taxonomy_name, $thepostid ) ? 1 : 0;
                        echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $has_term, 1, false ) . '>' . $term->name . '</option>';
                    }
                }
                ?>
            </select>
                <button class="button plus select_all_attributes"><?php _e( 'Select all', 'yit' ); ?></button> <button class="button minus select_no_attributes"><?php _e( 'Select none', 'wpb' ); ?></button>
                <button class="button fr plus add_new_attribute" data-attribute="<?php echo $attribute_taxonomy_name; ?>"><?php _e( 'Add new', 'wpb' ); ?></button>
            <?php
            }
        }
        public function woocommerce_variation_options($loop, $variation_data, $variation){
            $variation_images=get_post_meta( $variation->ID, '_wpb_variation_images', true )
            ?>
            <div class="wpb_variation_images">
                <ul class="wpb_image_thumb">
                    <?php if(!empty($variation_images)){
                        $image_array=explode(',',$variation_images);
                        if(!empty($image_array)){
                           foreach($image_array as $image){
                               $url=wp_get_attachment_url($image);
                               ?>
                               <li class="image" data-attachment_id="<?=$image?>">
                                   <a href="#" class="delete" title="<?=__('Delete Image','wpb')?>"><img src="<?=$url?>"></a>
                               </li>
                          <?php  }
                        }
                        ?>
                    <?php }?>
                </ul>
                <?php
                woocommerce_wp_hidden_input(
                    array(
                        'id'    => 'wpb_variation_images[' . $variation->ID . ']',
                        'value' => $variation_images,
                        'class'=>'wpb_variation_image_gallery'
                    )
                );
                ?>
                <a class="button button-primary wpb_multiple_image_upload"><?=__('Add Additional Images','wpb')?></a>
            </div>
            <?php
        }
        public function save_variation_settings_fields($post_id ){
            $variation_images=$_POST['wpb_variation_images'][$post_id];
            if( ! empty( $variation_images ) ) {
                update_post_meta( $post_id, '_wpb_variation_images', esc_attr( $variation_images ) );
            }
        }
    }
    new WPB_Admin_Product();
}