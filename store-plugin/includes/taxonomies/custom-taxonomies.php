<?php

// Register custom taxonomy for store categories
function create_store_taxonomy() {
    $labels = array(
        'name'              => _x( 'Store Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Store Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Store Categories' ),
        'all_items'         => __( 'All Store Categories' ),
        'parent_item'       => __( 'Parent Store Category' ),
        'parent_item_colon' => __( 'Parent Store Category:' ),
        'edit_item'         => __( 'Edit Store Category' ),
        'update_item'       => __( 'Update Store Category' ),
        'add_new_item'      => __( 'Add New Store Category' ),
        'new_item_name'     => __( 'New Store Category Name' ),
        'menu_name'         => __( 'Store Categories' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'store-category' ),
    );

    register_taxonomy( 'store_category', array( 'store' ), $args );
}

// Hook into the 'init' action to register the taxonomy
add_action( 'init', 'create_store_taxonomy', 0 );