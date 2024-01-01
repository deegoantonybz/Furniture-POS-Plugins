<?php 

// Add a custom meta box for store selection in product edit page
function add_custom_store_meta_box() {
    add_meta_box(
        'custom_store',
        'Custom Store',
        'display_custom_store_meta_box',
        'product', // Change this to match your product post type
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_custom_store_meta_box');

// Display the custom store meta box content
function display_custom_store_meta_box($post) {
    $stores = get_posts(array(
        'post_type' => 'store', // Replace 'store' with your custom store post type
        'posts_per_page' => -1,
    ));

    $selected_store = get_post_meta($post->ID, 'selected_store', true);

    ?>
    <label for="selected_store">Select Store:</label>
    <select name="selected_store" id="selected_store">
        <option value="">Select a Store</option>
        <?php foreach ($stores as $store) { ?>
            <option value="<?php echo esc_attr($store->ID); ?>" <?php selected($selected_store, $store->ID); ?>>
                <?php echo esc_html($store->post_title); ?>
            </option>
        <?php } ?>
    </select>
    <?php
}

// Save the custom store selection as meta data
function save_custom_store_meta_data($post_id) {
    if (isset($_POST['selected_store'])) {
        update_post_meta($post_id, 'selected_store', sanitize_text_field($_POST['selected_store']));
    }
}
add_action('save_post', 'save_custom_store_meta_data');

// Display the selected store name in product admin table
function display_selected_store_in_admin_table($column) {
    $column['selected_store'] = 'Selected Store';
    return $column;
}
add_filter('manage_product_posts_columns', 'display_selected_store_in_admin_table');

function display_selected_store_data_in_admin_table($column_name, $post_id) {
    if ($column_name === 'selected_store') {
        $selected_store_id = get_post_meta($post_id, 'selected_store', true);
        if ($selected_store_id) {
            $selected_store = get_post($selected_store_id);
            if ($selected_store) {
                echo esc_html($selected_store->post_title);
            } else {
                echo 'Store not found';
            }
        } else {
            echo 'Not selected';
        }
    }
}
add_action('manage_product_posts_custom_column', 'display_selected_store_data_in_admin_table', 10, 2);
