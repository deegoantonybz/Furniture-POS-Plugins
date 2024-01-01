<?php
/*
Plugin Name: Store Plugin
Description: Handles custom store post type and taxonomy.
Version: 1.0
Author: Deego
*/

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/post-types/custom-post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/taxonomies/custom-taxonomies.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-types/store-display-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-order-status/create-approved_order-table.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-order-status/custom-approved-order-menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-order-status/custom-order-status.php';
require_once plugin_dir_path(__FILE__) . 'includes/user-store-relation/user_store_associations-table.php';
require_once plugin_dir_path(__FILE__) . 'includes/user-store-relation/user-store-relation.php';
require_once plugin_dir_path(__FILE__) . 'includes/user-store-relation/store-filter.php';


function enqueue_custom_order_script_based_on_store() {
    // Check if the user is logged in and has a store ID associated
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_store_associations';
        $store_id = $wpdb->get_var($wpdb->prepare(
            "SELECT store_id FROM $table_name WHERE user_id = %d",
            $user_id
        ));

        if ($store_id) {
            $store = get_post($store_id);
            $store_name = ($store) ? $store->post_title : '';
            // Enqueue the new JavaScript file for users with a store ID
            wp_enqueue_script('new-custom-order-script', plugin_dir_url(__FILE__) . 'assets/js/new-custom-order-script.js', array('jquery'), '1.0', true);

            // Localize the script to pass the store ID to JavaScript
            wp_localize_script('new-custom-order-script', 'custom_script_vars', array(
                'store_id' => $store_name
            ));
        } else {
            // Enqueue the old JavaScript file for users without a store ID
            wp_enqueue_script('custom-order-script', plugin_dir_url(__FILE__) . 'assets/js/custom-order-script.js', array('jquery'), '1.0', true);

            // You may want to pass default variables if needed for the old script
            wp_localize_script('custom-order-script', 'custom_script_vars', array(
                'store_id' => '' // Pass default value or handle as needed
            ));
        }
    }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_order_script_based_on_store');

add_action('wp_ajax_update_order_metadata', 'update_order_metadata');
// Function to update order metadata via AJAX
function update_order_metadata() {
    // Get the order ID from the AJAX request
    $order_id = intval($_POST['order_id']);

    // Check if the order ID is valid
    if ($order_id > 0) {
        // Retrieve store ID from session or wherever it's stored
        $store_id = $_SESSION['selectedStoreId']; // Update this line with your session data

        // Update order metadata
        update_post_meta($order_id, 'Store ID', $store_id);

        // Return a response
        echo 'Metadata updated for order ID: ' . $order_id;
    } else {
        // Return an error response
        echo 'Invalid order ID';
    }

    // Always use wp_die() at the end of an AJAX callback function
    wp_die();
}

add_action('wp_ajax_get_store_id', 'get_store_id_callback');
add_action('wp_ajax_nopriv_get_store_id', 'get_store_id_callback');

function get_store_id_callback() {
 
    $order_id = $_POST['order_id'];
    $store_id = get_post_meta($order_id, 'store_id', true);
    $store = get_post($store_id);
    $store_name = ($store) ? $store->post_title : '';

    echo $store_name;
    wp_die();
}


