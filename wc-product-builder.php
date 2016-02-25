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
            load_plugin_textdomain('wpb', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
        }
    }
    new WC_Product_Builder();
}