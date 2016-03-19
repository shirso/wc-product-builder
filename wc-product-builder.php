<?php
/*Plugin Name:Woocommerce Product Builder
 Plugin URI: http://wp-theme.eu/de
 Description: Create Multi-step Product with WooCommerce
 Version: 1.0.1
 Author: WP-THEME.EU
 Author URI:http://wp-theme.eu/de*/
if (!defined('ABSPATH')) exit;
if (!defined('WPB_PLUGIN_DIR')) define('WPB_PLUGIN_DIR', dirname(__FILE__));
if (!defined('WPB_PLUGIN_ROOT_PHP')) define('WPB_PLUGIN_ROOT_PHP', dirname(__FILE__) . '/' . basename(__FILE__));
if (!defined('WPB_PLUGIN_ABSOLUTE_PATH')) define('WPB_PLUGIN_ABSOLUTE_PATH', plugin_dir_url(__FILE__));
if (!defined('WPB_PLUGIN_ADMIN_DIR')) define('WPB_PLUGIN_ADMIN_DIR', dirname(__FILE__) . '/admin');
if (!defined('WPB_PLUGIN_ADMIN_ASSETS_DIR')) define('WPB_PLUGIN_ADMIN_ASSETS_DIR', plugin_dir_url(__FILE__).'/admin/assets');
if (!defined('WPB_PLUGIN_ASSETS_DIR')) define('WPB_PLUGIN_ASSETS_DIR', plugin_dir_url(__FILE__).'/assets');
if (!class_exists('WC_Product_Builder')) {
    class WC_Product_Builder{
        public function __construct(){
            require_once(WPB_PLUGIN_DIR.'/inc/class-common-functions.php');
            require_once(WPB_PLUGIN_ADMIN_DIR . '/class-admin.php');
            require_once(WPB_PLUGIN_DIR . '/inc/class-scripts-styles.php');
            add_action( 'init', array( &$this, 'init') );
        }
        public function init(){
            require_once(WPB_PLUGIN_DIR.'/inc/class-frontend-product.php');
            require_once(WPB_PLUGIN_DIR . '/inc/class-product-cart.php');

            load_plugin_textdomain('wpb', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
            $this->custom_post_type();
        }
        public function custom_post_type(){
            $labels = array(
                'name'                => _x( 'Info Box', 'Post Type General Name', 'wpb' ),
                'singular_name'       => _x( 'Info Box', 'Post Type Singular Name', 'wpb' ),
                'menu_name'           => __( 'Info Boxes', 'wpb' ),
                'na me_admin_bar'       => __( 'Info Box', 'wpb' ),
                'all_items'           => __('All Info Boxes', 'wpb' ),
                'add_new_item'        => __( 'Add New Content', 'wpb' ),
                'add_new'             => __( 'Add New', 'wpb' ),
                'new_item'            => __( 'New Content', 'wpb' ),
                'edit_item'           => __( 'Edit Content', 'wpb' ),
                'update_item'         => __( 'Update Content', 'wpb' ),
                'view_item'           => __( 'View Content', 'wpb' ),
                'search_items'        => __( 'Search Content', 'wpb' ),
                'not_found'           => __( 'Not found', 'wpb' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'wpb' ),
            );
            $args = array(
                'label'               => __( 'Info Box', 'wpb' ),
                'labels'              => $labels,
                'supports'            => array( 'title','editor'),
                'hierarchical'        => false,
                'public'              => false,
                'show_in_menu'        => true,
                'show_ui'             => true,

            );
            register_post_type( 'wpb_info_box', $args );
        }

    }
    new WC_Product_Builder();
}