(function($) {
    $(document).ready(function() {
        var uniqueStoreIDs = new Set(); // Initialize a Set to store unique IDs

        var userStoreID = custom_script_vars.store_id;
        // console.log('Store ID:', userStoreID);

        // Add the 'Store ID' column header
        $('.wp-list-table.widefat.fixed.striped.table-view-list.orders.wc-orders-list-table.wc-orders-list-table-shop_order thead tr').append('<th scope="col" class="manage-column column-store_id">Store ID <select id="store_id_filter"><option value="">All</option></select></th>');

        // Add 'Store ID' data to each row
        $('.wp-list-table.widefat.fixed.striped.table-view-list.orders.wc-orders-list-table.wc-orders-list-table-shop_order tbody tr').each(function() {
            var orderID = $(this).attr('id').replace('order-', '');
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
                    $(`#order-${orderID}`).append(`<td class="store_id column-store_id" data-colname="Store ID">${storeID}</td>`);

                    // Add the ID to the Set if it's unique
                    if (storeID !== 'N/A' && !uniqueStoreIDs.has(storeID)) {
                        uniqueStoreIDs.add(storeID);
                        $('#store_id_filter').append(`<option value="${storeID}">${storeID}</option>`);

                    }

                    // var conditionMet = (parseInt(storeID) <= 1);
                    // if (conditionMet) {
                    //     $('.wp-list-table.widefat.fixed.striped.table-view-list.orders.wc-orders-list-table.wc-orders-list-table-shop_order tbody tr').each(function() {
                    //         var rowStoreID = $(this).find('td.store_id').text().trim();
                    //         if (rowStoreID === storeID) {
                    //             $(this).show();
                    //         } else {
                    //             $(this).hide();
                    //         }
                    //     });
                    // }
                },
                error: function() {
                    $(`#order-${orderID}`).append('<td class="store_id column-store_id" data-colname="Store ID">N/A</td>');
                }
            });
        });

        // Filter the table based on selected store ID
        $('#store_id_filter').on('change', function() {
            var selectedStoreID = $(this).val();
            $('.wp-list-table.widefat.fixed.striped.table-view-list.orders.wc-orders-list-table.wc-orders-list-table-shop_order tbody tr').each(function() {
                var rowStoreID = $(this).find('td.store_id').text().trim();
                if (selectedStoreID === '' || rowStoreID === selectedStoreID) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
    });
})(jQuery);
