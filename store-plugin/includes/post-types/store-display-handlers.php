<?php 

// Hook into the cart display
add_action('woocommerce_cart_totals_after_order_total', 'display_selected_store_in_cart');

function display_selected_store_in_cart() {
    // Retrieve the selected store information from session or user meta
    $selected_store_id = get_user_meta(get_current_user_id(), 'selected_store_id', true);

    if ($selected_store_id) {
        $selected_store = get_post($selected_store_id);
        if ($selected_store) {
            $store_title = $selected_store->post_title;
            $store_address = get_post_meta($selected_store_id, 'store_address', true);
            $store_phone = get_post_meta($selected_store_id, 'store_phone', true);
            $store_zipcode = get_post_meta($selected_store_id, 'store_zip', true);

            // Display the selected store information in the cart
            echo '<div class="selected-store-info">';
            echo '<h3>Selected Store</h3>';
            echo '<p>' . esc_html($store_title) . '</p>';
            echo '<p>' . esc_html($store_address) . '</p>';
            echo '<p>Ph: ' . esc_html($store_phone) . '</p>';
            echo '<p>Zip: ' . esc_html($store_zipcode) . '</p>';
            echo '</div>';
        }
    }
}