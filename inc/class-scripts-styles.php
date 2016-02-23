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
               // wp_enqueue_style('wpb_style_range_slider', WPB_PLUGIN_ASSETS_DIR . '/css/rangeslider.css', false);
              //  wp_enqueue_style('wpb_style_range_slider', WPB_PLUGIN_ASSETS_DIR . '/css/simple-slider.css', false);
               wp_enqueue_style('wpb_style_range_slider','//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css', false);
                wp_enqueue_style('wpb_style_bx_slider', WPB_PLUGIN_ASSETS_DIR . '/css/jquery.bxslider.css', false);
               // wp_enqueue_style('wpb_style_flex_slider', WPB_PLUGIN_ASSETS_DIR . '/css/flexslider.css', false);
                wp_enqueue_style('wpb_style_styles', WPB_PLUGIN_ASSETS_DIR . '/css/style.css', false);
                //wp_enqueue_script('wpb_script_bxslider',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.bxslider.js',array('jquery'),null,true);
                wp_enqueue_script('wpb_script_toucheswipe',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.touchSwipe.min.js',array('jquery'),null,true);
                wp_enqueue_script('wpb_script_flim_roll',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.film_roll.min.js',array('jquery'),null,true);
                //wp_enqueue_script('wpb_script_flexslider',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.flexslider-min.js',array('jquery'),null,true);
               // wp_enqueue_script('wpb_script_rangeslider',WPB_PLUGIN_ASSETS_DIR.'/js/rangeslider.js',array('jquery'),null,true);
              //  wp_enqueue_script('wpb_script_rangeslider',WPB_PLUGIN_ASSETS_DIR.'/js/simple-slider.min.js',array('jquery'),null,true);
                wp_register_script('wpb_script_frontend',WPB_PLUGIN_ASSETS_DIR.'/js/wpb.frontend.js',array('jquery'),null,true);
                wp_enqueue_script('jquery-ui-slider');
                wp_enqueue_script('wpb_script_frontend');
            }
        }
    }
    new WPB_Scripts_Styles();
}