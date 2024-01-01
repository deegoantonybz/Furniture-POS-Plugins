<?php 

function create_user_store_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_store_associations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        store_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_user_store_table');


// Create the admin menu
function user_store_selection_menu() {
    add_menu_page(
        'User Store Selection',
        'User Store Selection',
        'manage_options',
        'user_store_selection',
        'user_store_selection_page'
    );
}
add_action('admin_menu', 'user_store_selection_menu');

// Callback function for the admin page
function user_store_selection_page() {

    // Fetch users
    $users = get_users();
    
    // Fetch stores using WP_Query
    $args = array(
        'post_type' => 'store',
        'posts_per_page' => -1,
    );
    $stores_query = new WP_Query($args);
    $stores = $stores_query->posts;

    // Handle form submission
    if (isset($_POST['submit'])) {
        global $wpdb;

        $selected_user = isset($_POST['selected_user']) ? intval($_POST['selected_user']) : 0;
        $selected_store = isset($_POST['selected_store']) ? intval($_POST['selected_store']) : 0;

        if ($selected_user && $selected_store) {
            $table_name = $wpdb->prefix . 'user_store_associations';

            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $selected_user,
                    'store_id' => $selected_store,
                ),
                array(
                    '%d',
                    '%d'
                )
            );
        }
    }

        // Handle deletion
        if (isset($_POST['delete_association'])) {
            global $wpdb;
    
            $delete_id = isset($_POST['delete_id']) ? intval($_POST['delete_id']) : 0;
    
            if ($delete_id) {
                $table_name = $wpdb->prefix . 'user_store_associations';
    
                $wpdb->delete(
                    $table_name,
                    array(
                        'id' => $delete_id,
                    ),
                    array(
                        '%d',
                    )
                );
            }
        }

        // Display user-store associations in a table
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_store_associations';
        $associations = $wpdb->get_results(
            "SELECT associations.id, users.display_name AS user_name, stores.post_title AS store_name 
            FROM $table_name AS associations 
            LEFT JOIN {$wpdb->prefix}users AS users ON associations.user_id = users.ID 
            LEFT JOIN {$wpdb->prefix}posts AS stores ON associations.store_id = stores.ID 
            WHERE stores.post_type = 'store'"
        );

        // Fetch users excluding those already associated
        $users = $wpdb->get_results(
            "SELECT DISTINCT users.ID, users.display_name 
            FROM {$wpdb->prefix}users AS users
            LEFT JOIN {$wpdb->prefix}user_store_associations AS associations 
            ON users.ID = associations.user_id 
            WHERE associations.user_id IS NULL"
        );
    ?>
    <div class="wrap">
        <h1>User Store Selection</h1>
        <form method="post">
            <!-- Dropdown for selecting user -->
            <label for="selected_user">Select User:</label>
            <select name="selected_user" id="selected_user">
                <option value="">Select User</option>
                <?php foreach ($users as $user) : ?>
                    <option value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_html($user->display_name); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <!-- Dropdown for selecting store -->
            <label for="selected_store">Select Store:</label>
            <select name="selected_store" id="selected_store">
            <option value="">Select Store</option>
                <?php foreach ($stores as $store) : ?>
                    <option value="<?php echo esc_attr($store->ID); ?>"><?php echo esc_html($store->post_title); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <input type="submit" name="submit" value="Submit">
        </form>

        <!-- Display user-store associations in a table -->
        <h2>User-Store Associations</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Store</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($associations as $association) : ?>
                    <tr>
                        <td><?php echo isset($association->user_name) ? esc_html($association->user_name) : 'N/A'; ?></td>
                        <td><?php echo isset($association->store_name) ? esc_html($association->store_name) : 'N/A'; ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="delete_id" value="<?php echo esc_attr($association->id); ?>">
                                <button type="submit" name="delete_association">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}