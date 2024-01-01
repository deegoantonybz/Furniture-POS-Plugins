<?php


/*
Plugin Name: Custom Elementor Widgets
Description: Handles custom store post type and taxonomy.
Version: 1.0
Author: Deego
text-domain: custom-elementor-widgets
*/

namespace Custome\ElementorWidgets;

use Custome\ElementorWidgets\Widgets\Nav_Menu;

if ( ! defined( 'ABSPATH' ) ) exit;

final class ElementorWidgets {

    private static $_instance = null;
    
    public function __construct() {
        add_action( 'init', [$this, 'i18n']);

        add_action( 'plugins_loaded', [$this, 'init_plugin']);

        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets'] );

        // Hook into WooCommerce checkout process
        // add_action('woocommerce_checkout_create_order', [$this, 'save_store_id_to_order_meta']);

    }

    public function i18n() {
        load_plugin_textdomain('custom-elementor-widgets');
    }

    public function init_plugin() {
        
    }
    public function init_controls() {
        
    }

    public function init_widgets() {
        //required the widget class
        require_once __DIR__ . '/widgets/nav-menu.php';

        //register the widget with elemwntor
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Nav_Menu());
    }

    public static function get_instance() {

        if( null == self::$_instance) {
            self::$_instance = new Self();
        }

        return self::$_instance;
    }

    // Function to save store ID to order metadata
    // public function save_store_id_to_order_meta($order) {
    //     if (is_wc_endpoint_url('order-received')) { // Check if on the order received page
    //         $selected_store_id = isset($_SESSION['selectedStoreId']) ? $_SESSION['selectedStoreId'] : '';

    //         if (!empty($selected_store_id)) {
    //             // Add store ID as order metadata
    //             $order->update_meta_data('selected_store_id', $selected_store_id);
    //         }
    //     }
        
    // } 
    
}

// Enqueue CSS
function custom_elementor_widgets_styles() {
    wp_enqueue_style( 'custom-elementor-widgets-style', plugins_url( '/css/custom-elementor-widgets.css', __FILE__ ));
    
}
add_action( 'wp_enqueue_scripts', 'Custome\ElementorWidgets\custom_elementor_widgets_styles' );

// Enqueue JavaScript
// Enqueue JavaScript and localize ajaxurl
function custom_elementor_widgets_scripts() {
    wp_enqueue_script('custom-elementor-widgets-script', plugins_url('/js/custom-elementor-widgets.js', __FILE__), array('jquery'), '1.0', true);

    // Localize the ajaxurl variable for your script
    wp_localize_script('custom-elementor-widgets-script', 'custom_ajax_obj', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'Custome\ElementorWidgets\custom_elementor_widgets_scripts');

// AJAX handler
add_action('wp_ajax_save_selected_store_id', 'Custome\ElementorWidgets\save_selected_store_id');
function save_selected_store_id() {
    $store_id = $_POST['store_id'];
    $current_user_id = get_current_user_id();
    update_user_meta($current_user_id, 'selected_store_id', $store_id);
    wp_die();
}



// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
//   }

// add_action('woocommerce_checkout_create_order', 'Custome\ElementorWidgets\save_store_id_to_order_meta');

// function save_store_id_to_order_meta($order_id) {
//     // if (isset($_SESSION['selectedStoreId'])) {
//         // $store_id = $_SESSION['selectedStoreId'];
//         $order = wc_get_order($order_id);
//         $order->update_meta_data('Store ID', '1432');
//         $order->save();
//     // }
// }

// if (isset($_SESSION['selectedStoreId']) && !empty($_SESSION['selectedStoreId'])) {
//     // Session has the store ID
//     $selectedStoreId = $_SESSION['selectedStoreId'];
//     echo "Selected store ID: $selectedStoreId";
//   } else {
//     // Session does not have the store ID
//     echo "Store ID not found in session";
//   }


ElementorWidgets::get_instance();

