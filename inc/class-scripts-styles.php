<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('WPB_Scripts_Styles')) {
    class WPB_Scripts_Styles{
        public function __construct(){
            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
            //add_action('wp_footer',array(&$this,'wpb_footer'));
        }
        public function enqueue_styles(){
            global $post;
            if(WPB_Frontend_Product::wpb_enabled($post->ID)){
                wp_enqueue_style('wpb_style_grids', WPB_PLUGIN_ASSETS_DIR . '/css/grid12.css', false);
                wp_enqueue_style('wpb_style_range_slider', WPB_PLUGIN_ASSETS_DIR . '/css/rangeslider.css', false);
                wp_enqueue_style('wpb_style_bx_slider', WPB_PLUGIN_ASSETS_DIR . '/css/jquery.bxslider.css', false);
                wp_enqueue_style('wpb_style_styles', WPB_PLUGIN_ASSETS_DIR . '/css/style.css', false);
                wp_enqueue_script('wpb_script_bxslider',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.bxslider.js',array('jquery'),null,true);
                wp_enqueue_script('wpb_script_rangeslider',WPB_PLUGIN_ASSETS_DIR.'/js/rangeslider.js',array('jquery'),null,true);
                wp_register_script('wpb_script_frontend',WPB_PLUGIN_ASSETS_DIR.'/js/wpb.frontend.js',array('jquery'),null,true);
                wp_enqueue_script('wpb_script_frontend');
            }
        }
    }
    new WPB_Scripts_Styles();
}