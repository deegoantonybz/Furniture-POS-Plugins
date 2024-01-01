(function($) {
    $(document).ready(function() {
      var userStoreID = custom_script_vars.store_id;
      console.log('Store ID:', userStoreID);
  
      // Add the 'Store ID' column header
      $('.wp-list-table.widefat.fixed.striped.table-view-list.orders.wc-orders-list-table.wc-orders-list-table-shop_order thead tr').append('<th scope="col" class="manage-column column-store_id">Store ID</th>');
  
      // Filter and display orders based on the user's store ID
      $('.wp-list-table.widefat.fixed.striped.table-view-list.orders.wc-orders-list-table.wc-orders-list-table-shop_order tbody tr').each(function() {
        var row = $(this);
        var orderID = row.attr('id').replace('order-', '');
        var data = {
          'action': 'get_store_id',
          'order_id': orderID,
        };
  
        // AJAX call to retrieve store ID meta data
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: data,
          success: function(response) {
            var storeID = response !== '' ? response : 'N/A';
            row.append(`<td class="store_id column-store_id" data-colname="Store ID">${storeID}</td>`);
  
            // Show or hide rows based on the user's store ID
            if (userStoreID !== '' && storeID !== userStoreID) {
              row.hide();
            }
          },
          error: function() {
            row.append('<td class="store_id column-store_id" data-colname="Store ID">N/A</td>');
          }
        });
      });
    });
  })(jQuery);
  