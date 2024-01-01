<?php 

// Add a menu item to the WordPress dashboard
add_action('admin_menu', 'custom_approved_orders_menu');

function custom_approved_orders_menu() {
    add_menu_page(
        'Approved Orders', // Page title
        'Approved Orders', // Menu title
        'manage_options', // Capability required to access this menu
        'custom-approved-orders', // Menu slug
        'render_custom_approved_orders' // Function to render the page content
    );
}

// Function to render the content for the 'Approved Orders' menu page
function render_custom_approved_orders() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'approved_orders';
  
    // Define the filter form
    $start_date_filter = isset($_POST['start_date_filter']) && !empty($_POST['start_date_filter']) ? sanitize_text_field($_POST['start_date_filter']) : '';
    $end_date_filter = isset($_POST['end_date_filter']) && !empty($_POST['end_date_filter']) ? sanitize_text_field($_POST['end_date_filter']) : '';
  
    // Build the SQL query
    $sql = "SELECT ao.id, ao.order_id, u.display_name AS user_name, ao.approved_date
            FROM $table_name ao
            INNER JOIN {$wpdb->users} u ON ao.user_id = u.ID";
  
    // Add WHERE clause based on filters
    if ($start_date_filter && $end_date_filter) {
      $sql .= " WHERE ao.approved_date BETWEEN '$start_date_filter' AND '$end_date_filter'";
    } elseif ($start_date_filter) {
      $sql .= " WHERE ao.approved_date >= '$start_date_filter'";
    } elseif ($end_date_filter) {
      $sql .= " WHERE ao.approved_date <= '$end_date_filter'";
    }
  
    $approved_orders = $wpdb->get_results($sql);
  
    echo '<div class="wrap">';
    echo '<h1>Approved Orders';
  
    // Display filter information
    if ($start_date_filter) {
      echo ' - Starting on ' . esc_html($start_date_filter);
    }
    if ($end_date_filter) {
      echo (isset($start_date_filter) ? ' and ' : '') . 'ending on ' . esc_html($end_date_filter);
    }
    echo '</h1>';
  
    // Display filter form
    echo '<form method="post">';
    echo '<label for="start_date_filter">Start Date:</label>';
    echo '<input type="date" id="start_date_filter" name="start_date_filter" value="' . esc_html($start_date_filter) . '">';
    echo '<label for="end_date_filter">End Date:</label>';
    echo '<input type="date" id="end_date_filter" name="end_date_filter" value="' . esc_html($end_date_filter) . '">';
    echo '<button type="submit">Filter</button>';
    echo '</form>';
  
    // Display table
    if ($approved_orders) {
      echo '<table class="wp-list-table widefat fixed striped">';
      echo '<thead><tr><th>ID</th><th>Order ID</th><th>User Name</th><th>Approved Date</th></tr></thead>';
      echo '<tbody>';
      foreach ($approved_orders as $order) {
        echo '<tr>';
        echo '<td>' . esc_html($order->id) . '</td>';
        echo '<td><a href="' . admin_url('post.php?post=' . $order->order_id . '&action=edit') . '">' . esc_html($order->order_id) . '</a></td>';
        echo '<td>' . esc_html($order->user_name) . '</td>';
        echo '<td>' . esc_html($order->approved_date) . '</td>';
        echo '</tr>';
      }
      echo '</tbody>';
      echo '</table>';
    } else {
      echo '<p>No approved orders found.</p>';
    }
  
    echo '</div>';
  }