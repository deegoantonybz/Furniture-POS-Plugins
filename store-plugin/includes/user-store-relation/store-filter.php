<?php 

function custom_order_column( $columns ) {
    // error_log('Column name...');
    $columns['store_name'] = 'Store Name'; // Add new column for store name
    return $columns;
    // die('Function called');
}
add_filter( 'manage_edit-shop_order_columns', 'custom_order_column', 20 );

function custom_order_column_content( $column, $post_id ) {
    // error_log('Column Details...');
    if ( 'store_name' === $column ) {
        $store_id = get_post_meta( $post_id, 'store_id', true );
        $store = get_post($store_id);
        $store_name = ($store) ? $store->post_title : 'N/A'; // Get the store name
        echo $store_name;
    }
}
add_action( 'manage_shop_order_posts_custom_column', 'custom_order_column_content', 20, 2 );

// Add dropdown filter for store name
function add_store_name_filter() {
    global $typenow, $wpdb;
    if ('shop_order' === $typenow) {
        $args = array(
            'post_type'      => 'store', // Change 'store' to your actual post type name
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC'
        );
        $stores = get_posts($args);

        if ($stores) {
            $selected_store = isset($_GET['store_name_filter']) ? $_GET['store_name_filter'] : '';
            
            echo '<select name="store_name_filter">';
            echo '<option value="">All Stores</option>';
            foreach ($stores as $store) {
                $selected = selected($selected_store, $store->ID, false);
                echo '<option value="' . esc_attr($store->ID) . '" ' . $selected . '>' . esc_html($store->post_title) . '</option>';
            }
            echo '</select>';
        } else {
            // echo '<p>No stores found.</p>';
        }
    }
}
add_action('restrict_manage_posts', 'add_store_name_filter');

// Apply the filter based on store name
function store_name_filter_query($query) {
    global $pagenow;
    $post_type = 'shop_order';
    if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $post_type && isset($_GET['store_name_filter']) && $_GET['store_name_filter'] != '') {
        $query->query_vars['meta_key'] = 'store_id';
        $query->query_vars['meta_value'] = $_GET['store_name_filter'];
    }
}
add_filter('parse_query', 'store_name_filter_query');

// Function to get associated store ID for a user
function get_user_associated_store_id($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_store_associations';
    $store_id = $wpdb->get_var($wpdb->prepare("SELECT store_id FROM $table_name WHERE user_id = %d", $user_id));
    return $store_id;
}

// Add filter for order query based on user permissions
function modify_order_query_by_user_permissions($query) {
    global $pagenow, $typenow, $wpdb;

    // Check if the user is on the order page in admin panel
    if (is_admin() && $pagenow === 'edit.php' && $typenow === 'shop_order') {
        $user_id = get_current_user_id();

        // Check if the user is not an admin and has a specific user ID
        if ($user_id !== 0 && $user_id !== 1) { // Replace '1' with your admin user ID
            $user_store_id = get_user_associated_store_id($user_id);

            if ($user_store_id !== null) {
                $query->query_vars['meta_key'] = 'store_id';
                $query->query_vars['meta_value'] = $user_store_id;
            } else {
                // If no associated store, show no orders (optional)
                $query->query_vars['post__in'] = array(0);
            }
        }
    }
}
add_action('pre_get_posts', 'modify_order_query_by_user_permissions');
