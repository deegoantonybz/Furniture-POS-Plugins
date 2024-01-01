<?php

// Hook the function to add the dashboard menu
add_action('admin_menu', 'custom_backorder_menu');

function custom_backorder_menu() {
    add_menu_page(
        'Back Orders',             // Page Title
        'Back Orders',             // Menu Title
        'manage_options',          // Capability
        'custom-backorder-list',   // Menu Slug
        'custom_backorder_page'    // Callback function
    );
}

function custom_backorder_page() {
    global $wpdb;

    // Query to fetch back orders
    $table_name = $wpdb->prefix . 'approved_orders';
    $backorder_orders = $wpdb->get_results(
        "SELECT order_id, user_id, approved_date FROM $table_name WHERE backorder = 1"
    );

    // Display fetched orders in a table
    echo '<div class="wrap">';
    echo '<h1>Back Orders</h1>';
    echo '<table class="widefat">';
    echo '<thead><tr><th>Order ID</th><th>User ID</th><th>Product Name(s)</th><th>Current Stock</th><th>Required Quantity</th><th>Onhand</th><th>Approved Date</th><th>Action</th></tr></thead>';
    echo '<tbody>';
    foreach ($backorder_orders as $order) {
        $orderId = wc_get_order($order->order_id);
        $items = $orderId->get_items();
        foreach ($items as $item) {
            $product = $item->get_product();
            $stock_quantity = $product->get_stock_quantity();
            $product_name = $product->get_name();
            $product_id = $product->get_id(); // Get product ID

            // Get the product_onhand meta value
            $product_onhand_key = "product_onhand_$product_id";
            $product_onhand = get_post_meta($product_id, $product_onhand_key, true);

            // Get required quantity from the order
            $required_quantity = $item->get_quantity();
            echo '<tr>';
            // echo '<td>' . $order->order_id . '</td>';
            echo '<td><a href="' . admin_url('post.php?post=' . $order->order_id . '&action=edit') . '">' . esc_html($order->order_id) . '</a></td>';
            echo '<td>' . $order->user_id . '</td>';
            echo '<td>' . $product_name . '</td>';
            echo '<td>' . $stock_quantity . '</td>';
            echo '<td>' . $required_quantity . '</td>';
            echo '<td>' . $product_onhand . '</td>';
            echo '<td>' . $order->approved_date . '</td>';
            // echo '<td><button class="complete-button" data-order-id="' . $order->order_id . '">Complete</button></td>';
            echo '<td>';
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="order_id" value="' . $order->order_id . '">';
            echo '<input type="hidden" name="required_quantity" value="' . $required_quantity . '">';
            echo '<input type="hidden" name="onhand_quantity" value="' . $product_onhand . '">';
            echo '<input type="hidden" name="product_id" value="' . $product_id . '">';
            echo '<button type="submit" name="complete_order">Complete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    }
    // Add export link
    echo '<a href="' . admin_url('admin.php?action=export_backorders') . '" class="button">Export to Excel</a>';
    echo '</tbody></table></div>';

    if (isset($_POST['complete_order'])) {
        $order_id = $_POST['order_id'];
        $required_quantity = $_POST['required_quantity'];
        $onhand_quantity = $_POST['onhand_quantity'];
        $product_id = $_POST['product_id'];
        $order = wc_get_order($order_id);

        if($required_quantity == $onhand_quantity) {
            // Update order status to completed
            $order->update_status('completed');
            $difference = $required_quantity - $onhand_quantity;

            $product_onhand_key = "product_onhand_$product_id";
            update_post_meta($product_id, $product_onhand_key, $difference);
            
            // Update backorder status in the database
            global $wpdb;
            $table_name = $wpdb->prefix . 'approved_orders';
            $wpdb->update(
                $table_name,
                array('backorder' => 0),
                array('order_id' => $order_id)
            );

        } elseif ($required_quantity > $onhand_quantity) {

            $difference = $required_quantity - $onhand_quantity;

            $product_onhand_key = "product_onhand_$product_id";
            update_post_meta($product_id, $product_onhand_key, $difference);
        } elseif ($required_quantity < $onhand_quantity) {

            $order->update_status('completed');

            $difference = $onhand_quantity - $required_quantity;

            $product_onhand_key = "product_onhand_$product_id";
            update_post_meta($product_id, $product_onhand_key, $difference);
        }
        // Now you have the $order_id, and you can use it as needed
        // echo 'Clicked Order ID: ' . $order_id;
        // echo 'Clicked Required quantity: ' . $required_quantity;
    }
}