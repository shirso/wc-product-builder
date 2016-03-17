<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('WPB_Scripts_Styles')) {
    class WPB_Scripts_Styles{
        public function __construct(){
            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
        }
        public function enqueue_styles(){
            global $post, $woocommerce;
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            if(WPB_Common_Functions::wpb_enabled($post->ID)){
                wp_enqueue_style('wpb_style_grids', WPB_PLUGIN_ASSETS_DIR . '/css/grid12.css', false);
                wp_enqueue_style('wpb_style_range_slider','//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css', false);
                wp_enqueue_style('wpb_style_bx_slider', WPB_PLUGIN_ASSETS_DIR . '/css/jquery.bxslider.css', false);
                wp_enqueue_style('wpb_style_styles', WPB_PLUGIN_ASSETS_DIR . '/css/style.css', false);
                wp_enqueue_script('wpb_script_toucheswipe',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.touchSwipe.min.js',array('jquery'),null,true);
                wp_enqueue_script('wpb_script_flim_roll',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.film_roll.min.js',array('jquery'),null,true);
                wp_enqueue_script('wpb_script_underscore',WPB_PLUGIN_ASSETS_DIR.'/js/underscore-min.js',array('jquery'),null,true);
                wp_register_script('wpb_script_frontend',WPB_PLUGIN_ASSETS_DIR.'/js/wpb.frontend.js',array('jquery'),null,true);
                wp_enqueue_script('jquery-ui-slider');
                wp_enqueue_script( 'wpb_prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
              //  wp_enqueue_script( 'prettyPhoto-init', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
                wp_enqueue_style( 'wpb_woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css' );
                wp_enqueue_script('wpb_script_touch',WPB_PLUGIN_ASSETS_DIR.'/js/jquery.ui.touch-punch.min.js',array('jquery'),null,true);
                wp_localize_script("wpb_script_frontend","wpb_local_params",array(
                   "continue_text"=>__("Continue","wpb"),
                    "add_to_cart_text"=>__("Add to Cart","wpb"),
                    "productId"=>$post->ID,
                    "ajaxUrl"=>admin_url('admin-ajax.php'),
                    "resetText"=>__("Are you sure to reset?","wpb")
                   // 'defaultSelection'=>get_post_meta($post->ID,'_wpb_defaults',true)
                ));
                wp_enqueue_script('wpb_script_frontend');
            }
        }
    }
    new WPB_Scripts_Styles();
}