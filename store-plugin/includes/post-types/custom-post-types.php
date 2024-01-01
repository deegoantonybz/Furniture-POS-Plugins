<?php

// Register custom post type
function create_store_post_type() {
    $labels = array(
        'name'               => _x( 'Stores', 'Post Type General Name', 'text_domain' ),
        'singular_name'      => _x( 'Store', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'          => __( 'Stores', 'text_domain' ),
        'parent_item_colon'  => __( 'Parent Store:', 'text_domain' ),
        'all_items'          => __( 'All Stores', 'text_domain' ),
        'view_item'          => __( 'View Store', 'text_domain' ),
        'add_new_item'       => __( 'Add New Store', 'text_domain' ),
        'add_new'            => __( 'Add New', 'text_domain' ),
        'edit_item'          => __( 'Edit Store', 'text_domain' ),
        'update_item'        => __( 'Update Store', 'text_domain' ),
        'search_items'       => __( 'Search Stores', 'text_domain' ),
        'not_found'          => __( 'Not found', 'text_domain' ),
        'not_found_in_trash' => __( 'Not found in Trash', 'text_domain' ),
    );
    
    $args = array(
        'label'               => __( 'store', 'text_domain' ),
        'description'         => __( 'Stores', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    
    register_post_type( 'store', $args );
}

// Hook into the 'init' action to register the post type
add_action( 'init', 'create_store_post_type', 0 );

// Add meta box for store additional information
function add_store_info_meta_box() {
    add_meta_box(
        'store_info',
        'Store Information',
        'display_store_info_meta_box',
        'store', // Post type where the meta box will appear
        'normal',
        'high'
    );
}

// Display the meta box content
function display_store_info_meta_box( $post ) {
    // Retrieve existing values for address and phone number if they exist
    $store_address = get_post_meta( $post->ID, 'store_address', true );
    $store_phone = get_post_meta( $post->ID, 'store_phone', true );
    $store_zip = get_post_meta($post->ID, 'store_zip', true);

    // Display fields for address and phone number
    ?>
    <p>
        <label for="store_address">Store Address:</label>
        <input type="text" id="store_address" name="store_address" value="<?php echo esc_attr( $store_address ); ?>" style="width: 100%;" />
    </p>
    <p>
        <label for="store_phone">Store Phone Number:</label>
        <input type="text" id="store_phone" name="store_phone" value="<?php echo esc_attr( $store_phone ); ?>" style="width: 100%;" />
    </p>
    <p>
        <label for="store_zip">Zip Code:</label>
        <input type="text" id="store_zip" name="store_zip" value="<?php echo esc_attr($store_zip); ?>" style="width: 100%;" />
    </p>
    <?php
}

// Save meta box data
function save_store_info_meta_data( $post_id ) {
    if ( isset( $_POST['store_address'] ) ) {
        update_post_meta( $post_id, 'store_address', sanitize_text_field( $_POST['store_address'] ) );
    }
    if ( isset( $_POST['store_phone'] ) ) {
        update_post_meta( $post_id, 'store_phone', sanitize_text_field( $_POST['store_phone'] ) );
    }
    if (isset($_POST['store_zip'])) {
        update_post_meta($post_id, 'store_zip', sanitize_text_field($_POST['store_zip']));
    }
}

// Hook into meta box actions
add_action( 'add_meta_boxes', 'add_store_info_meta_box' );
add_action( 'save_post', 'save_store_info_meta_data' );