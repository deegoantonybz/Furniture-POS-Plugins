<?php 

// Register new custom order status
function register_approved_status() {
    register_post_status( 'wc-order-approved', array(
        'label'                     => _x( 'Approved', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Approved <span class="count">(%s)</span>', 'Approved<span class="count">(%s)</span>', 'woocommerce' )
    ) );
  }
  
  // Add new custom order status to list of WC Order statuses
  function add_awaiting_approved_to_order_statuses( $order_statuses ) {
    $new_order_statuses = array();
  
    // Add new order status before processing
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-processing' === $key) {
            $new_order_statuses['wc-order-approved'] = _x( 'Approved', 'Order status', 'woocommerce' );
        }
    }
    return $new_order_statuses;
  }
  
  // Adding custom status to admin order list bulk actions dropdown
  function custom_dropdown_bulk_actions_shop_order( $actions ) {
    $new_actions = array();
  
    // Add new custom order status after processing
    foreach ($actions as $key => $action) {
        $new_actions[$key] = $action;
        if ('mark_processing' === $key) {
            $new_actions['mark_order-approved'] = __( 'Mark Approved', 'woocommerce' );
        }
    }
  
    return $new_actions;
  }
  
  add_action( 'init', 'register_approved_status' );
  add_filter( 'wc_order_statuses', 'add_awaiting_approved_to_order_statuses' );
  add_filter( 'bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1 );