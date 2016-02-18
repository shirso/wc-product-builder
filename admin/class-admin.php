<?php
if ( !defined( 'ABSPATH' ) ) exit;
if( !class_exists('WPB_Admin') ) {
    class WPB_Admin{
        public function __construct() {
            add_action('admin_init',array(&$this,'plugin_admin_init'));
            add_action('admin_enqueue_scripts',array(&$this,'admin_scripts'));
        }
        public function plugin_admin_init(){
            require_once(WPB_PLUGIN_ADMIN_DIR . '/class-admin-attributes.php' );
            require_once(WPB_PLUGIN_ADMIN_DIR . '/class-admin-product.php' );
        }
        public function admin_scripts(){
            wp_enqueue_media();
            wp_register_script('wpb_admin_script',WPB_PLUGIN_ADMIN_ASSETS_DIR.'/js/wpb.admin.js','',false,true);
            wp_enqueue_script('wpb_admin_script');
            wp_enqueue_style('wpc_admin_style',WPB_PLUGIN_ADMIN_ASSETS_DIR.'/css/wpb.admin.css');
        }
    }
    new WPB_Admin();
}