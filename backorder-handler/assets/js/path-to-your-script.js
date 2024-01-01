jQuery(document).ready(function($) {
    $('#export-button').on('click', function(e) {
        e.preventDefault();
        var tableData = $('.widefat').html();
        $.ajax({
            url: myAjax.ajaxurl, // Use wp_localize_script() to define 'myAjax.ajaxurl'
            type: 'POST',
            data: {
                action: 'export_backorders',
                html_table_data: tableData
            },
            success: function(response) {
                console.log('File downloaded!');
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });
});
