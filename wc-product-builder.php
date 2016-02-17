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
if (!defined('WPB_PLUGIN_ASSETS_DIR')) define('WPB_PLUGIN_ADMIN_ASSETS_DIR', plugin_dir_url(__FILE__).'/assets');
if (!class_exists('WPB_Booking_Module')) {
    class WPB_Booking_Module{
        public function __construct(){
            require_once(WPB_PLUGIN_ADMIN_DIR . '/class-admin.php');
        }
    }
    new WPB_Booking_Module();
}