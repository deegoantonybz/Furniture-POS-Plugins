<?php 

function create_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'approved_orders';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        order_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        approved_date datetime NOT NULL,
        backorder tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_custom_table');


add_action('woocommerce_order_status_changed', 'so_status_completed', 10, 3);

function so_status_completed($order_id, $old_status, $new_status)
{
    // error_log("order status working...");
    // error_log("Order ID: " . $order_id);
    // Check if the 'id' parameter exists in the URL
    // if (isset($_GET['id'])) {
        // $order_id = absint($_GET['id']); // Retrieve the 'id' parameter value as an integer

        // Now you have the order ID
        // error_log("Order ID: " . $order_id);
        // error_log("New Status: " . $new_status);
        // $approved_date = current_time('mysql');
        // error_log("Current Time: " . $approved_date);

        if ($new_status === 'order-approved') {
            global $wpdb;
            $table_name = $wpdb->prefix . 'approved_orders';

            try {
                $user_id = get_current_user_id();
                $approved_date = current_time('mysql');
                $order = wc_get_order($order_id);
                $items = $order->get_items();

                $is_backorder = 0;
                foreach ($items as $item) {
                    $product = $item->get_product();
                    // error_log('Product:'.$product);
                    if ($product->stock_quantity < 1) {
                        $is_backorder = 1;
                        break;
                    }
                }

                $wpdb->insert(
                    $table_name,
                    array(
                        'order_id' => $order_id,
                        'user_id' => $user_id,
                        'approved_date' => $approved_date,
                        'backorder' => $is_backorder,
                    ),
                    array('%d', '%d', '%s', '%d')
                );
                error_log("Data inserted successfully!");
            } catch (Exception $e) {
                error_log("Error inserting data: " . $e->getMessage());
            }
        }
    // }
}