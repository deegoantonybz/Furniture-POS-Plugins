<?php
/*
Plugin Name: Backorder Handler
Description: Handles backorder status and updates inventory.
Version: 1.0
Author: Deego
*/

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/admin/backorder-list.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/export-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes/custom-store-meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/product-disable/disable-product.php';
// require_once plugin_dir_path(__FILE__) . 'includes/product-disable/disable-product.php';

// Your other existing code can remain in this file
// Hooks, actions, and filters that tie everything together
// ...

// Remember to adjust the paths according to your folder structure

// Function to get product IDs from the cart
function get_cart_product_ids() {
    $product_ids = array();

    // Get cart object
    $cart = WC()->cart;

    // Loop through cart items and retrieve product IDs
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product_ids[] = $cart_item['product_id'];
    }

    return $product_ids;
}

// Hook into the checkout process
add_action('template_redirect', 'checkout_store_verification');

function checkout_store_verification() {
    error_log('check out....');
    // Check if the current URL contains "/checkout/"
    $current_url = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($current_url);

    // Check if the URL path contains "checkout"
    if (isset($parsed_url['path']) && strpos($parsed_url['path'], '/checkout/') !== false) {

        error_log('Page post...');
        // Get the current user and their selected store
        $current_user_id = get_current_user_id();
        $selected_store_id = get_user_meta($current_user_id, 'selected_store_id', true);

        // Get product IDs from the cart
        $product_ids_in_cart = get_cart_product_ids();

        foreach ($product_ids_in_cart as $product_id) {
            // Get the assigned store for each product in the cart
            $assigned_store_id = get_post_meta($product_id, 'selected_store', true);
            error_log('Selected store id:'.$assigned_store_id);

            // Compare the stores
            if ($selected_store_id && $assigned_store_id && $selected_store_id !== $assigned_store_id) {
                // Stores don't match, show a warning and prevent checkout
                $store_name = get_the_title($assigned_store_id);
                wc_add_notice('The selected store does not have the product only available in. '. esc_html($store_name) , 'error');
                break; // Stop further checking if any product doesn't match the store
            }
        }
    }


    
}
add_action('template_redirect', 'store_verification');
function store_verification() {
    // Check if the user is on a product or category page
    if (is_product() || is_product_category()) {
        error_log('PRODUct PAge...');
        // Get the current product ID
        $product_id = get_the_ID();
        error_log('PROdcuT ID:'. $product_id);
        // Get the assigned store for the product
        $assigned_store_id = get_post_meta($product_id, 'selected_store', true);
        error_log('Assigned sto:'. $assigned_store_id);

        // Get the current user and their selected store
        $current_user_id = get_current_user_id();
        $selected_store_id = get_user_meta($current_user_id, 'selected_store_id', true);
        error_log('Selected sto:'. $selected_store_id);

        if ($assigned_store_id && $selected_store_id && $assigned_store_id !== $selected_store_id) {
            
            wp_enqueue_script('store_verification_script', plugin_dir_url(__FILE__) . 'assets/js/store_verification.js', array('jquery'), '1.0', true);
            wp_localize_script('store_verification_script', 'storeVerification', array('message' => esc_html__('This product is only available at', 'text-domain'), 'storeName' => get_the_title($assigned_store_id)));
            wp_add_inline_script('store_verification_script', 'displayStoreMessage();');
            wp_enqueue_style('store_verification_style', plugin_dir_url(__FILE__) . 'assets/css/store_verification.css');
            wp_add_inline_style('store_verification_style', '.store-message { /* Your custom styling */ }');
        }
    }
}

function store_verification_plugin_enqueue_assets() {
    wp_enqueue_script('store_verification_script');
    wp_enqueue_style('store_verification_style');
}
add_action('wp_enqueue_scripts', 'store_verification_plugin_enqueue_assets');