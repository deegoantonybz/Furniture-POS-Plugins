<?php

add_action('admin_init', 'export_backorders_to_excel');
function export_backorders_to_excel() {
    if (isset($_GET['action']) && $_GET['action'] === 'export_backorders') {
        global $wpdb;
        // require_once  'vendor/autoload.php'; // Adjust this path accordingly
        require_once  __DIR__ . '/../../vendor/autoload.php';

        // Your table generation code
        // ...
        $table_name = $wpdb->prefix . 'approved_orders';
        $backorder_orders = $wpdb->get_results(
            "SELECT order_id, user_id, approved_date FROM $table_name WHERE backorder = 1"
        );

        // Create a new PhpSpreadsheet instance
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headings for each column
        $sheet->setCellValue('A1', 'Order ID');
        $sheet->setCellValue('B1', 'User ID');
        $sheet->setCellValue('C1', 'Product Name');
        $sheet->setCellValue('D1', 'Stock Quantity');
        $sheet->setCellValue('E1', 'Required Quantity');
        $sheet->setCellValue('F1', 'Product Onhand');
        $sheet->setCellValue('G1', 'Approved Date');
        // Add headings for additional columns if needed

        // Extract table data and insert into the Excel sheet
        $rowCount = 2; // Start from the second row after headings
        foreach ($backorder_orders as $order) {
            $orderId = wc_get_order($order->order_id);
            $items = $orderId->get_items();
            foreach ($items as $item) {
                $product = $item->get_product();
                $stock_quantity = $product->get_stock_quantity();
                $product_name = $product->get_name();
                $product_id = $product->get_id(); // Get product ID
    
                // Get the product_onhand meta value
                $product_onhand_key = "product_onhand_$product_id";
                $product_onhand = get_post_meta($product_id, $product_onhand_key, true);
    
                // Get required quantity from the order
                $required_quantity = $item->get_quantity();

                // Assuming $order contains data needed for Excel
                $sheet->setCellValue('A' . $rowCount, $order->order_id);
                $sheet->setCellValue('B' . $rowCount, $order->user_id);
                $sheet->setCellValue('C' . $rowCount, $product_name);
                $sheet->setCellValue('D' . $rowCount, $stock_quantity);
                $sheet->setCellValue('E' . $rowCount, $required_quantity);
                $sheet->setCellValue('F' . $rowCount, $product_onhand);
                $sheet->setCellValue('G' . $rowCount, $order->approved_date);
                // Insert other data in subsequent columns similarly

                $rowCount++;
            }
        }
    
        // Save the Excel file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'backorders_' . date('Y-m-d') . '.xlsx'; // Change the filename as needed
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}