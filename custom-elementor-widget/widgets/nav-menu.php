<?php

namespace Custome\ElementorWidgets\Widgets;

use Elementor\Widget_Base;

/**
 * Have the widget code for the Custom Elementor Nav Menu.
 */

class Nav_Menu extends Widget_Base
{

    public function get_name()
    {
        return 'store-menu';
    }

    public function get_title()
    {
        return __('Store Menu', 'custom-elementor-widgets');
    }

    public function get_icon()
    {
        return 'eicon-nav-menu';
    }

    public function get_categories()
    {
        return ['basic']; // Customize this as needed
    }

    public function _register_controls()
    {

    }

    protected function render()
    {

        $this->display_store_selection();
    }

    private function display_store_selection()
    {
        // Get the list of stores
        $stores = get_posts(
            array(
                'post_type' => 'store',
                'numberposts' => -1,
            )
        );

        // if ($stores) {
        //     // echo '<label for="store_selection">Select a Store:</label>';
        //     echo '<select name="store_selection" id="store_selection">';
        //     echo '<option value="">Select a store</option>';

        //     foreach ($stores as $store) {
        //         echo '<option value="' . esc_attr($store->ID) . '">' . esc_html($store->post_title) . '</option>';
        //     }

        //     echo '</select>';
        // }
        if ($stores) {
            ?>
            <div class="store-selector">
                <button type="button" class="text-3xl w-full h-full -mt-1" id="storeButton">
                    <div class="pb-1">
                        <span class="sr-only">Select a Store</span>
                        <div class="store-details hidden">
                            
                            <div class="wd-overlay-side wd-fill" style="position: fixed;"></div>

                            <!-- <div class="card-body" style="width: 550px; height: 100%;"> -->
                            <div class="store-widget-side wd-side-hidden wd-right wd-opened">
                                
                                <div class="wd-heading">
                                    <span class="title">Select a Store</span>
                                    <div class="close-side-widget wd-action-btn wd-style-text wd-cross-icon">
                                        <a href="#" rel="nofollow">Close</a>
                                    </div>
                                </div>
                                <div class="store-search">
                                    <input type="text" id="zipCode" placeholder="Enter ZIP code">
                                    <!-- <button type="button" id="searchStores">Search</button> -->
                                    <div class="searchStores"> <a href="#">Search</a></div>
                                </div>

                                <?php foreach ($stores as $store): ?>
                                    <?php
                                    $store_id = $store->ID;
                                    $store_title = esc_html($store->post_title);

                                    // Get store address and phone number
                                    $store_address = get_post_meta($store_id, 'store_address', true);
                                    $store_phone = get_post_meta($store_id, 'store_phone', true);
                                    $store_zipcode = get_post_meta($store_id, 'store_zip', true);

                                    // Get store-related categories
                                    $store_categories = get_the_terms($store_id, 'store_category');
                                    $store_category_names = [];

                                    // Get the featured image URL
                                    // $store_image_url = get_the_post_thumbnail_url($store_id, 'thumbnail');
                                    $store_content = $store->post_content;

                                    // Extract image URLs from the post content
                                    preg_match_all('/<img.+?src="([^"]+)"/', $store_content, $matches);
                                    $store_images = $matches[1]; // Array of image URLs

                                    if ($store_categories) {
                                        foreach ($store_categories as $store_category) {
                                            $store_category_names[] = $store_category->name;
                                        }
                                    }

                                    $store_categories_html = '';
                                    if ($store_category_names) {
                                        $store_categories_html = '<div class="store-categories">';
                                        $store_categories_list = implode(', ', $store_category_names);
                                        $store_categories_html .= $store_categories_list;
                                        $store_categories_html .= '</div>';
                                    }
                                    
                                    ?>
                                    <div class="store" data-store-id="<?= esc_attr($store->ID) ?>">

                                    <div class="button-store"> <a href="#">Make My Store</a></div>
                                        <h4>
                                            <?php echo $store_title; ?>
                                        </h4>

                                        <?php foreach ($store_images as $image_url) : ?>
                                            <img src="<?php echo $image_url; ?>" alt="<?php echo $store_title; ?>" width="150" height="50" />
                                        <?php endforeach; ?>
                                        
                                        <!-- <button class="button-store">Store</button> -->
                                        <p><?php echo $store_address; ?></p>
                                        <p>Ph:<?php echo $store_phone; ?></p>
                                        <p style="color: orange;">Zip: <?php echo $store_zipcode; ?></p>
                                        <?php echo $store_categories_html; ?>
                                    </div>
                                <?php endforeach; ?>
                                <div id="noStoreMessage" style="display: none;">No stores found for the entered ZIP code.</div>
                            </div>
                        </div>
                    </div>
                </button>
            </div>
            <?php
        }
    }

    protected function _content_template()
    {

    }

}