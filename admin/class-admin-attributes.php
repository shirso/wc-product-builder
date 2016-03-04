<?php
if ( !defined( 'ABSPATH' ) ) exit;
if( !class_exists('WPB_Admin_Attributes') ) {
    class WPB_Admin_Attributes {
        public function __construct() {
            add_filter('product_attributes_type_selector',array(&$this,'product_attributes_type_selector'),20);
            $attributes=function_exists('wc_get_attribute_taxonomy_names')?wc_get_attribute_taxonomy_names():array();
            if(!empty($attributes)){
                foreach($attributes as $attribute){
                    add_action( $attribute.'_add_form_fields', array( &$this, 'add_attr_type_to_add_form'), 10, 2 );
                    add_action( $attribute.'_edit_form_fields', array( &$this, 'add_image_uploader_to_edit_form'), 10, 1);
                    add_action( 'edited_'.$attribute, array( &$this, 'save_taxonomy_custom_meta'), 10, 2 );
                    add_action( 'create_'.$attribute, array( &$this, 'save_taxonomy_custom_meta'), 10, 2 );
                    add_action( 'delete_'.$attribute, array( &$this, 'delete_taxonomy_custom_meta'), 10, 2 );
                    add_filter('manage_edit-' .$attribute . '_columns', array(&$this, 'woocommerce_product_attribute_columns'));
                    add_filter('manage_' .$attribute . '_custom_column', array(&$this, 'woocommerce_product_attribute_column'), 10, 3);
                }
            }
        }
        public  function product_attributes_type_selector($types){
            $types_custom= array(
                'carousel'=>__('Carousel','wpb'),
                'size'=>__('Size','wpb'),
                'extra'=>__('Extra','wpb'),
            );
            $full_types=array_merge($types,$types_custom);
            return $full_types;
        }
        public function add_attr_type_to_add_form(){
            $taxonomy_name=esc_html($_GET["taxonomy"]);
            $attribute_type=WPB_Common_Functions::get_variation_attribute_type($taxonomy_name);
            ?>
            <?php if($attribute_type== "carousel" || $attribute_type== "extra"){?>
            <div class="form-field">
                <label for="hd_wpb_attribute_image"><?php _e('Image', 'wpc') ?></label>
                <input type="text" class="wide-fat" id="hd_wpb_attribute_image" value="" name="hd_wpb_attribute_image"/>
                <button class="button button-secondary wpb_upload_button"  id="btn_wpb_attribute_image_upload"><?php _e('Upload', 'wpc') ?></button>
            </div>
                <?php }?>
            <?php if($attribute_type=="extra"){?>
                        <script type="text/javascript">
                            var inject_data_options=[],
                                normaal_sheepit=true;

                        </script>
                <div class="form-field">
                    <label for="wpb_normal_sheepit"><?=__('Add Multiple Options','wpb')?></label>
                    <div id="wpb_normal_sheepit">
                        <div id="wpb_normal_sheepit_template">
                            <input type="text"  id="wpb_attribute_options_#index#" class="wide-fat" name="wpb_attribute_options[#index#]">
                        </div>
                        <div id="wpb_normal_sheepit_noforms_template"><?php _e('No Option','wpb'); ?></div>
                         <div id="wpb_normal_sheepit_controls" class="row">
                             <div id="wpb_normal_sheepit_add" class="col-sm-3 wc_dinl"><a class="button button-default"><span><?php _e('Add Option','wpb'); ?></span></a></div>
                             <div id="wpb_normal_sheepit_remove_last" class="col-sm-3 wc_dinl"><a class="button button-default"><span><?php _e('Remove','wpb'); ?> </span></a></div>
                             <div id="wpb_normal_sheepit_remove_all"><a><span><?php _e('Remove All','wpc'); ?></span></a></div>
                             <div id="wpb_normal_sheepit_add_n" class="col-sm-6">
                                 <div class="row">
                                     <div class="col-sm-2"><input id="wpb_normal_sheepit_add_n_input" type="text" size="4" /></div>
                                     <div class="col-sm-10 " id="wpb_normal_sheepit_add_n_button"><a class="button button-default"><span><?php _e('Add','wpb'); ?> </span></a></div></div>
                             </div>
                         </div>
                    </div>
                 </div>
             <?php }?>
            <?php if($attribute_type=="size"){?>
                <script type="text/javascript">
//                    var inject_data_options=[],
//                        normaal_sheepit=true;
                </script>
                <div class="">
                    <label for=""><?=__('Add Dimension Details','wpb')?></label>
                          <table>
                              <tr>
                                  <th>
                                      <label for="wpb_regulator_title"><?=__('Regulator Title','wpb')?></label>
                                  </th>
                                  <td>
                                      <input type="text" class="wide-fat"  name="wpb_size_options[regulator_title]" id="wpb_regulator_title">
                                  </td>
                              </tr>
<!--                              <tr>-->
<!--                                  <th>-->
<!--                                      <label for="wpb_regulator_min">--><?//=__('Regulator Min Value','wpb')?><!--</label>-->
<!--                                  </th>-->
<!--                                  <td>-->
<!--                                      <input type="text" name="wpb_size_options[regulator_min]" size="5" id="wpb_regulator_min">-->
<!--                                  </td>-->
<!--                              </tr>-->
<!--                              <tr>-->
<!--                                  <th>-->
<!--                                      <label for="wpb_regulator_max">--><?//=__('Regulator Max Value','wpb')?><!--</label>-->
<!--                                  </th>-->
<!--                                  <td>-->
<!--                                      <input type="text" name="wpb_size_options[regulator_max]" id="wpb_regulator_max" size="5">-->
<!--                                  </td>-->
<!--                              </tr>-->
                              <tr>
                                  <th>
                                      <label for="wpb_regulator_step"><?=__('Regulator Values','wpb')?></label>
                                  </th>
                                  <td>
                                      <input type="text" name="wpb_size_options[regulator_values]"   id="wpb_regulator_step" size="30">
                                      <br/>
                                      <span class="description"><?=__('Put multiple values separated with comma(,)','wpb')?></span>

                                  </td>
                              </tr>
                              <tr>
                                  <th>
                                      <label for="wpb_regulator_unit"><?=__('Regulator Unit','wpb')?></label>
                                  </th>
                                  <td>
                                      <input type="text" id="wpb_regulator_unit" name="wpb_size_options[regulator_unit]" size="10">
                                  </td>
                              </tr>
                              <tr>
                                  <th>
                                      <label for="wpb_dropdown_title"><?=__('Text Box Title','wpb')?></label>
                                  </th>
                                  <td>
                                      <input type="text" class="widefat" name="wpb_size_options[dropdown_title]" id="wpb_dropdown_title">
                                  </td>
                              </tr>
                              <tr>
                                  <th>
                                      <label for="wpb_dropdown_unit"><?=__('Text Box Unit','wpb')?></label>
                                  </th>
                                  <td>
                                      <input type="text" name="wpb_size_options[dropdown_unit]" size="10" id="wpb_dropdown_unit">
                                  </td>
                              </tr>
<!--                              <tr>-->
<!--                                 <th>-->
<!--                                     <label for="wpb_normal_sheepit">--><?//=__('Dropdown Options','wpb')?><!--</label>-->
<!--                                 </th>-->
<!--                                  <td>-->
<!--                                      <div id="wpb_normal_sheepit">-->
<!--                                          <div id="wpb_normal_sheepit_template">-->
<!--                                              <input type="text"  id="wpb_attribute_options_#index#" class="wide-fat" name="wpb_size_options[dropdown_options][#index#]">-->
<!--                                          </div>-->
<!--                                          <div id="wpb_normal_sheepit_noforms_template">--><?php //_e('No Option','wpb'); ?><!--</div>-->
<!--                                          <div id="wpb_normal_sheepit_controls" class="row">-->
<!--                                              <div id="wpb_normal_sheepit_add" class="col-sm-3 wc_dinl"><a class="button button-default"><span>--><?php //_e('Add Option','wpb'); ?><!--</span></a></div>-->
<!--                                              <div id="wpb_normal_sheepit_remove_last" class="col-sm-3 wc_dinl"><a class="button button-default"><span>--><?php //_e('Remove','wpb'); ?><!-- </span></a></div>-->
<!--                                              <div id="wpb_normal_sheepit_remove_all"><a><span>--><?php //_e('Remove All','wpc'); ?><!--</span></a></div>-->
<!--                                              <div id="wpb_normal_sheepit_add_n" class="col-sm-6">-->
<!--                                                  <div class="row">-->
<!--                                                      <div class="col-sm-2"><input id="wpb_normal_sheepit_add_n_input" type="text" size="4" /></div>-->
<!--                                                      <div class="col-sm-10 " id="wpb_normal_sheepit_add_n_button"><a class="button button-default"><span>--><?php //_e('Add','wpb'); ?><!-- </span></a></div></div>-->
<!--                                              </div>-->
<!--                                          </div>-->
<!--                                      </div>-->
<!--                                  </td>-->
<!--                              </tr>-->
                          </table>
                </div>
              <?php }?>
            <?php
        }
        public function add_image_uploader_to_edit_form($term){
            $term_id=$term->term_id;
            $attr_image= get_option( '_wpb_variation_attr_image_'.$term_id );
            $attr_options= get_option( '_wpb_attribute_options_'.$term_id );
            $attr_size_options=get_option('_wpb_size_options_'.$term_id);
            $taxonomy_name=esc_html($_GET["taxonomy"]);
            $attribute_type=WPB_Common_Functions::get_variation_attribute_type($taxonomy_name);
            ?>
            <?php if($attribute_type== "carousel" || $attribute_type== "extra"){?>
        <tr class="form-field">
            <th>
                <label for="hd_wpc_attribute_image"><?php _e('Image', 'wpc') ?></label>
            </th>
            <td class="wpc-upload-field">
                <input type="text" class="wide-fat" id="hd_wpb_attribute_image" value="<?php echo $attr_image; ?>" name="hd_wpb_attribute_image"/>
                <button class="button button-secondary wpb_upload_button" id="btn_wpc_attribute_image_upload"><?php _e('Upload', 'wpc') ?></button>
            </td>
        </tr>
             <?php } ?>
            <?php if($attribute_type=="extra"){
                $inject_data=array();
                if(!empty($attr_options)){
                    foreach($attr_options as $option){
                        array_push($inject_data,array('wpb_attribute_options_#index#'=>$option));
                    }
                }
                ?>
                <script>
                    var inject_data_options=<?=json_encode($inject_data);?>,
                        normaal_sheepit=true;
                </script>
                <tr class="form-field">
                    <th>
                        <label for="wpb_normal_sheepit"><?=__('Add Multiple Options','wpb')?></label>
                    </th>
                    <td>
                        <div id="wpb_normal_sheepit">
                            <div id="wpb_normal_sheepit_template">
                                <input type="text"  id="wpb_attribute_options_#index#" class="wide-fat" name="wpb_attribute_options[#index#]">
                            </div>
                            <div id="wpb_normal_sheepit_noforms_template"><?php _e('No Option','wpb'); ?></div>
                            <div id="wpb_normal_sheepit_controls" class="row">
                                <div id="wpb_normal_sheepit_add" class="col-sm-3 wc_dinl"><a class="button button-default"><span><?php _e('Add Option','wpb'); ?></span></a></div>
                                <div id="wpb_normal_sheepit_remove_last" class="col-sm-3 wc_dinl"><a class="button button-default"><span><?php _e('Remove','wpb'); ?> </span></a></div>
                                <div id="wpb_normal_sheepit_remove_all"><a><span><?php _e('Remove all','wpc'); ?></span></a></div>
                                <div id="wpb_normal_sheepit_add_n" class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-2"><input id="wpb_normal_sheepit_add_n_input" type="text" size="4" /></div>
                                        <div class="col-sm-10 " id="wpb_normal_sheepit_add_n_button"><a class="button button-default"><span><?php _e('Add','wpb'); ?> </span></a></div></div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
              <?php }?>
                <?php if($attribute_type=="size"){ ?>
                <?php
                $inject_data=array();
                if(!empty($attr_size_options["dropdown_options"])){
                    foreach($attr_size_options["dropdown_options"] as $option){
                        array_push($inject_data,array('wpb_attribute_options_#index#'=>$option));
                    }
                }
                ?>
                    <script type="text/javascript">
                      //  var inject_data_options=<?=json_encode($inject_data);?>,
                       //     normaal_sheepit=true;
                    </script>
                    <tr>
                        <th>
                            <label for="wpb_regulator_title"><?=__('Regulator Title','wpb')?></label>
                        </th>
                        <td>
                            <input value="<?=@$attr_size_options["regulator_title"]?>" type="text" class="wide-fat"  name="wpb_size_options[regulator_title]" id="wpb_regulator_title">
                        </td>
                    </tr>
<!--                    <tr>-->
<!--                        <th>-->
<!--                            <label for="wpb_regulator_min">--><?//=__('Regulator Min Value','wpb')?><!--</label>-->
<!--                        </th>-->
<!--                        <td>-->
<!--                            <input type="text" name="wpb_size_options[regulator_min]" value="--><?//=@$attr_size_options["regulator_min"]?><!--" size="5" id="wpb_regulator_min">-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <th>-->
<!--                            <label for="wpb_regulator_max">--><?//=__('Regulator Max Value','wpb')?><!--</label>-->
<!--                        </th>-->
<!--                        <td>-->
<!--                            <input type="text" name="wpb_size_options[regulator_max]"  value="--><?//=@$attr_size_options["regulator_max"]?><!--" id="wpb_regulator_max" size="5">-->
<!--                        </td>-->
<!--                    </tr>-->
                <tr>
                    <th>
                        <label for="wpb_regulator_step"><?=__('Regulator Values','wpb')?></label>
                    </th>
                    <td>
                        <input type="text" name="wpb_size_options[regulator_values]"  value="<?=@$attr_size_options["regulator_values"]?>" id="wpb_regulator_step" size="30">
                        <br/>
                        <span class="description"><?=__('Put multiple values separated with comma(,)','wpb')?></span>
                    </td>
                </tr>
                    <tr>
                        <th>
                            <label for="wpb_regulator_unit"><?=__('Regulator Unit','wpb')?></label>
                        </th>
                        <td>
                            <input type="text" id="wpb_regulator_unit" name="wpb_size_options[regulator_unit]"  value="<?=@$attr_size_options["regulator_unit"]?>" size="10">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="wpb_dropdown_title"><?=__('Text Box Title','wpb')?></label>
                        </th>
                        <td>
                            <input type="text"  name="wpb_size_options[dropdown_title]"  value="<?=@$attr_size_options["dropdown_title"]?>" id="wpb_dropdown_title">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="wpb_dropdown_unit"><?=__('Text Box Unit','wpb')?></label>
                        </th>
                        <td>
                            <input type="text" name="wpb_size_options[dropdown_unit]"  value="<?=@$attr_size_options["dropdown_unit"]?>" size="10" id="wpb_dropdown_unit">
                        </td>
                    </tr>
<!--                    <tr>-->
<!--                        <th>-->
<!--                            <label for="wpb_normal_sheepit">--><?//=__('Dropdown Options','wpb')?><!--</label>-->
<!--                        </th>-->
<!--                        <td>-->
<!--                            <div id="wpb_normal_sheepit">-->
<!--                                <div id="wpb_normal_sheepit_template">-->
<!--                                    <input type="text"  id="wpb_attribute_options_#index#" class="wide-fat" name="wpb_size_options[dropdown_options][#index#]">-->
<!--                                </div>-->
<!--                                <div id="wpb_normal_sheepit_noforms_template">--><?php //_e('No Option','wpb'); ?><!--</div>-->
<!--                                <div id="wpb_normal_sheepit_controls" class="row">-->
<!--                                    <div id="wpb_normal_sheepit_add" class="col-sm-3 wc_dinl"><a class="button button-default"><span>--><?php //_e('Add Option','wpb'); ?><!--</span></a></div>-->
<!--                                    <div id="wpb_normal_sheepit_remove_last" class="col-sm-3 wc_dinl"><a class="button button-default"><span>--><?php //_e('Remove','wpb'); ?><!-- </span></a></div>-->
<!--                                    <div id="wpb_normal_sheepit_remove_all"><a><span>--><?php //_e('Remove All','wpc'); ?><!--</span></a></div>-->
<!--                                    <div id="wpb_normal_sheepit_add_n" class="col-sm-6">-->
<!--                                        <div class="row">-->
<!--                                            <div class="col-sm-2"><input id="wpb_normal_sheepit_add_n_input" type="text" size="4" /></div>-->
<!--                                            <div class="col-sm-10 " id="wpb_normal_sheepit_add_n_button"><a class="button button-default"><span>--><?php //_e('Add','wpb'); ?><!-- </span></a></div></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </td>-->
<!--                    </tr>-->

                <?php }?>
            <?php
        }
        public function save_taxonomy_custom_meta( $term_id ) {
            if ( isset( $_POST['hd_wpb_attribute_image'] ) ){
                update_option( '_wpb_variation_attr_image_'.$term_id, $_POST['hd_wpb_attribute_image'] );
            }
            if(isset($_POST['hd_wpb_attribute_image']) && !empty($_POST["wpb_attribute_options"])){
                update_option( '_wpb_attribute_options_'.$term_id, $_POST['wpb_attribute_options'] );
            }
            if(isset($_POST['wpb_size_options']) && !empty($_POST['wpb_size_options'])){
                update_option( '_wpb_size_options_'.$term_id, $_POST['wpb_size_options'] );
            }
        }
        public function delete_taxonomy_custom_meta($term_id){
            delete_option( '_wpb_variation_attr_image_'.$term_id );
            delete_option( '_wpb_attribute_options_'.$term_id );
            delete_option( '_wpb_size_options_'.$term_id );
        }
        public function woocommerce_product_attribute_columns($columns){
            $columns['wpb_attr_image']=__('Thumbnail', 'wpb');
            return $columns;
        }
        public function woocommerce_product_attribute_column($columns, $column, $id){
            if ($column == 'wpb_attr_image') {
                $attr_image=get_option('_wpb_variation_attr_image_'.$id);
                if(!empty($attr_image)){
                echo '<img src="' . $attr_image . '" style="height:40px"/>';}
            }
        }
    }
    new WPB_Admin_Attributes();
}