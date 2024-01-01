<?php 

session_start();
// $store_id = $_SESSION['selectedStoreId'];
// var_dump($store_id);
// error_log('Custom order column code loaded');
add_action( 'woocommerce_thankyou', 'sa_wc_after_order_complete', 10, 1);

function sa_wc_after_order_complete($order_id) {
    
        $order = wc_get_order($order_id);

        // Check if the order object exists
        if ($order) {
            // Retrieve the order ID
            $order_id = $order->get_id();
            // echo '<p>Your Order ID is: ' . $order_id . '</p>';
            // $store_id = $_SESSION['selectedStoreId'];
            $current_user_id = get_current_user_id();
            $selected_store_id = get_user_meta($current_user_id, 'selected_store_id', true);
            // var_dump($_SESSION);
            update_post_meta($order_id, 'store_id', $selected_store_id);
    
        }

}