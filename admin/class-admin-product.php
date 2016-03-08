<?php
if ( !defined( 'ABSPATH' ) ) exit;
if( !class_exists('WPB_Admin_Product') ) {
    class WPB_Admin_Product {
        public function __construct() {
            add_filter('product_type_options', array(&$this, 'product_type_options'));
            add_action('woocommerce_product_option_terms', array(&$this, 'product_option_terms'), 10, 2);
            add_action('woocommerce_variation_options',array(&$this,'woocommerce_variation_options'),10,3);
            add_action( 'woocommerce_save_product_variation',array(&$this,'save_variation_settings_fields'), 10, 2 );
            add_action('woocommerce_process_product_meta', array(&$this, 'save_custom_fields'), 10, 2);
            add_filter('woocommerce_product_data_tabs', array(&$this, 'add_product_data_tab'),1);
            add_action('woocommerce_product_data_panels', array(&$this, 'add_product_data_panel'));
            add_action('woocommerce_process_product_meta', array(&$this, 'save_custom_fields'), 10, 2);
        }
        public function product_type_options($types)
        {
            $types['wpb_check'] = array(
                'id' => '_wpb_check',
                'wrapper_class' => 'show_if_wpb show_if_variable',
                'label' => __('Enable Product Builder', 'wpb'),
                'description' => __('Enable Product Builder for this Product.', 'wpb')
            );
            return $types;
        }
        public function add_product_data_tab($tabs){
            $tabs['wpb_dimension']=array(
                'label'=>__('Dimensions','wpb'),
                'target'=>'wpb_dimension_tab',
                'class' => array('show_if_wpb_panel')
            );
            $tabs['wpb_extra']=array(
                'label'=>__('Extras','wpb'),
                'target'=>'wpb_extra_tab',
                'class' => array('show_if_wpb_panel')
            );
            $tabs['wpb_instructions'] =array(
                'label'=>__('Info Boxes','wpb'),
                'target'=>'wpb_instructions_tab',
                'class' => array('show_if_wpb_panel')
            );

            return $tabs;
        }
        public function add_product_data_panel(){
            global $wpdb, $post;
            $attributes = maybe_unserialize(get_post_meta($post->ID, '_product_attributes', true));
            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'include'          => '',
                'exclude'          => '',
                'meta_key'         => '',
                'meta_value'       => '',
                'post_type'        => 'wpb_info_box',
                'post_mime_type'   => '',
                'post_parent'      => '',
                'author'	   => '',
                'post_status'      => 'publish',
                'suppress_filters' => true
            );
            $all_posts=get_posts($args);
            $info_boxes=get_post_meta($post->ID,"_wpb_info_boxes",true);
            ?>
            <script type="application/javascript">
                var wpb_product_page=true;
            </script>
            <div id="wpb_instructions_tab" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
                <div id="wpb_attribute_tab">
                     <div class="toolbar toolbar-top">
                      <table cellpadding="10" cellspacing="10">
                          <?php if(!empty($attributes)){ foreach($attributes as $attr=>$option){?>
                              <tr>
                                  <td><?=wc_attribute_label($attr);?></td>
                                  <td>
                                      <select name="wpb_info_boxes[<?=$attr;?>]" id="wpb_info_boxes_<?=$attr;?>">
                                        <option value="">---</option>
                                        <?php if(!empty($all_posts)){ foreach($all_posts  as $p){
                                            $selected= $info_boxes[$attr]== $p->ID? "selected" :"";
                                            ?>
                                            <option <?=$selected;?> value="<?=$p->ID;?>"><?=$p->post_title;?></option>
                                        <?php }}?>
                                      </select>
                                  </td>
                              </tr>
                            <?php }}?>
                      </table>
                  </div>
                </div>
            </div>
            <div id="wpb_dimension_tab" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
               <?php if(!empty($attributes)){?>
                   <?php foreach($attributes as $attr=>$option){
                       $attribute_type=WPB_Common_Functions::get_variation_attribute_type($attr);
                        if($attribute_type=="dimension"){
                       ?>
                       <h2><?=__('Attributes For','wpb');?> <?=wc_attribute_label($attr)?></h2>
                         <div id="wpb_dimension_<?=$attr?>" class="wpb_dimension">
                             <div id="wpb_dimension_<?=$attr?>_template">
                                <table cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td class="attribute_name">
                                                <?=__("Choose Multiple Attributes","wpb");?>
                                            </td>
                                            <td>
                                                <select multiple id="wpb_dimension_<?=$attr?>_#index#" name="wpb_dimensions[<?=$attr?>][#index#]" class="wpb_enhanced_select">
                                                    <option value="">----</option>
                                                    <?php foreach($attributes as $n=>$m){
                                                        $opt_type=WPB_Common_Functions::get_variation_attribute_type($n);
                                                        if($opt_type=="regulator" || $opt_type=="select"){
                                                        ?>
                                                      <option value="<?=$n?>"><?=wc_attribute_label($n);?></option>
                                                        <?php }}?>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                             </div>
                             <div id="wpb_dimension_<?=$attr?>_noforms_template"><?=__("No Attribute","wpb");?></div>
                            <div id="wpb_dimension_<?=$attr?>_controls">
                                <div id="wpb_dimension_<?=$attr?>_add" class="alin-btn"><a class="button-primary"><span><?=__('Add Attribute','wpb');?></span></a></div>
                                <div id="wpb_dimension_<?=$attr?>_remove_last" class="alin-btn"><a class="button-primary"><span><?=__('Remove','wpb');?></span></a></div>
                            </div>
                         </div>
                   <?php }}?>
                <?php }?>

            </div>
            <div id="wpb_extra_tab" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
                Extra
            </div>
          <?php
        }

        public function save_custom_fields($post_id, $post){
            update_post_meta($post_id, '_wpb_check', isset($_POST['_wpb_check']) ? 'yes' : 'no');
            if (isset($_POST['wpb_info_boxes'])) {
                update_post_meta($post_id, '_wpb_info_boxes', $_POST['wpb_info_boxes']);
            }
        }
        public function product_option_terms($tax, $i){
            global $woocommerce, $thepostid;
            if( in_array( $tax->attribute_type, array( 'carousel', 'regulator', 'extra', 'dimension' ) ) ) {
                $attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
            ?>
            <select multiple="multiple" data-placeholder="<?php _e( 'Select terms', 'wpb' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i; ?>][]">
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