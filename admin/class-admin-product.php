<?php
if ( !defined( 'ABSPATH' ) ) exit;
if( !class_exists('WPB_Admin_Product') ) {
    class WPB_Admin_Product {
        public function __construct() {
            add_action('woocommerce_product_option_terms', array(&$this, 'product_option_terms'), 10, 2);
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
    }
    new WPB_Admin_Product();
}