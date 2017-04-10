<?php

/**
 * Demo plugin Install
 *
 * Plugin install script which adds default pages, taxonomies, and database tables to WordPress. Runs on activation and upgrade.
 *
 * @author 		WC Marketplace
 * @package 	wcmp/Admin/Install
 * @version    0.0.1
 */
class WCMp_Install {

    public function __construct() {
        global $WCMp;
        if (class_exists('WCMp')) {
            if (!get_option("dc_product_vendor_plugin_page_install")) {
                $this->wcmp_product_vendor_plugin_create_pages();
                update_option("dc_product_vendor_plugin_page_install", 1);
            }
            $this->do_wcmp_migrate();
            $WCMp->load_class('endpoints');
            $endpoints = new WCMp_Endpoints();
            $endpoints->add_wcmp_endpoints();
            flush_rewrite_rules();
        }
        $this->save_default_plugin_settings();
        $this->wcmp_plugin_tables_install();
        $this->remove_other_vendors_plugin_role();
    }

    /**
     * Remove other vendor role created by other plugin
     *
     * @access public
     * @return void
     */
    function remove_other_vendors_plugin_role() {
        $other_vendor_role = array('seller', 'yith_vendor', 'pending_vendor', 'vendor');
        foreach ($other_vendor_role as $element) {
            if (wcmp_role_exists($element)) {
                remove_role($element);
            }
        }
    }

    /**
     * Create a page
     *
     * @access public
     * @param mixed $slug Slug for the new page
     * @param mixed $option Option name to store the page's ID
     * @param string $page_title (default: '') Title for the new page
     * @param string $page_content (default: '') Content for the new page
     * @param int $post_parent (default: 0) Parent for the new page
     * @return void
     */
    function wcmp_product_vendor_plugin_create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0) {
        global $wpdb;
        $option_value = get_option($option);
        if ($option_value > 0 && get_post($option_value)) {
            return;
        }
        $page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;");
        if ($page_found) :
            if (!$option_value) {
                update_option($option, $page_found);
            }
            return;
        endif;
        $page_data = array(
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'post_name' => $slug,
            'post_title' => $page_title,
            'post_content' => $page_content,
            'post_parent' => $post_parent,
            'comment_status' => 'closed'
        );
        $page_id = wp_insert_post($page_data);
        update_option($option, $page_id);
    }

    /**
     * Create pages that the plugin relies on, storing page id's in variables.
     *
     * @access public
     * @return void
     */
    function wcmp_product_vendor_plugin_create_pages() {
        global $WCMp;

        // WCMp Plugin pages
        $this->wcmp_product_vendor_plugin_create_page(esc_sql(_x('wcmp', 'page_slug', $WCMp->text_domain)), 'wcmp_product_vendor_vendor_page_id', __('Vendor Dashboard', $WCMp->text_domain), '[wcmp_vendor]');
        $this->wcmp_product_vendor_plugin_create_page(esc_sql(_x('wcmp_vendor_registration', 'page_slug', $WCMp->text_domain)), 'wcmp_product_vendor_registration_page_id', __('Vendor Registration', $WCMp->text_domain), '[vendor_registration]');
        $wcmp_product_vendor_vendor_page_id = get_option('wcmp_product_vendor_vendor_page_id');
        $wcmp_product_vendor_registration_page_id = get_option('wcmp_product_vendor_registration_page_id');
        update_wcmp_vendor_settings('wcmp_vendor', $wcmp_product_vendor_vendor_page_id, 'vendor', 'general');
        update_wcmp_vendor_settings('vendor_registration', $wcmp_product_vendor_registration_page_id, 'vendor', 'general');
    }

    /**
     * save default product vendor plugin settings
     *
     * @access public
     * @return void
     */
    function save_default_plugin_settings() {
        $general_settings = get_option('wcmp_general_settings_name');
        if (empty($general_settings)) {
            $general_settings = array(
                'enable_registration' => 'Enable',
                'approve_vendor_manually' => 'Enable',
            );
            update_option('wcmp_general_settings_name', $general_settings);
        }
        $product_settings = get_option('wcmp_product_settings_name');
        if (empty($product_settings)) {
            $product_settings = array(
                'inventory' => 'Enable',
                'shipping' => 'Enable',
                'linked_products' => 'Enable',
                'attribute' => 'Enable',
                'advanced' => 'Enable',
                'simple' => 'Enable',
                'variable' => 'Enable',
                'grouped' => 'Enable',
                'virtual' => 'Enable',
                'external' => 'Enable',
                'downloadable' => 'Enable',
                'taxes' => 'Enable',
                'add_comment' => 'Enable',
                'comment_box' => 'Enable',
                'sku' => 'Enable',
            );
            update_option('wcmp_product_settings_name', $product_settings);
        }
        $capabilities_settings = get_option('wcmp_capabilities_settings_name');
        if (empty($capabilities_settings)) {
            $capabilities_settings = array(
                'is_upload_files' => 'Enable',
                'is_submit_product' => 'Enable',
                'is_order_csv_export' => 'Enable',
                'is_show_email' => 'Enable',
                'is_vendor_view_comment' => 'Enable',
                'show_cust_billing_add' => 'Enable',
                'show_cust_shipping_add' => 'Enable',
                'show_cust_order_calulations' => 'Enable',
                'show_customer_dtl' => 'Enable',
                'show_customer_billing' => 'Enable',
                'show_customer_shipping' => 'Enable',
                'show_cust_add' => 'Enable',
                'is_hide_option_show' => 'Enable',
            );
            update_option('wcmp_capabilities_settings_name', $capabilities_settings);
        }

        $payment_settings = get_option('wcmp_payment_settings_name');
        if (empty($payment_settings)) {
            $payment_settings = array(
                'commission_include_coupon' => 'Enable',
                'give_tax' => 'Enable',
                'give_shipping' => 'Enable',
                'commission_type' => 'percent',
            );
            update_option('wcmp_payment_settings_name', $payment_settings);
        }
        $frontend_settings = get_option('wcmp_frontend_settings_name');
        if (empty($frontend_settings)) {
            $frontend_settings = array(
                'sold_by_cart_and_checkout' => 'Enable',
                'sold_by_catalog' => 'Enable',
                'catalog_colorpicker' => '#000000',
                'catalog_hover_colorpicker' => '#000000',
            );
            update_option('wcmp_frontend_settings_name', $frontend_settings);
        }
        if (empty(get_wcmp_vendor_settings('is_singleproductmultiseller', 'general'))) {
            update_wcmp_vendor_settings('is_singleproductmultiseller', 'Enable', 'general');
        }

        if (empty(get_wcmp_vendor_settings('is_edit_delete_published_product', 'capabilities', 'product'))) {
            update_wcmp_vendor_settings('is_edit_delete_published_product', 'Enable', 'capabilities', 'product');
        }
        if (empty(get_wcmp_vendor_settings('is_edit_delete_published_coupon', 'capabilities', 'product'))) {
            update_wcmp_vendor_settings('is_edit_delete_published_coupon', 'Enable', 'capabilities', 'product');
        }
    }

    /**
     * Create WCMp dependency tables
     * @global object $wpdb
     */
    function wcmp_plugin_tables_install() {
        global $wpdb;
        $collate = '';
        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }
        $create_tables_query = array();
        $create_tables_query[] = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "wcmp_vendor_orders` (
		`ID` bigint(20) NOT NULL AUTO_INCREMENT,
		`order_id` bigint(20) NOT NULL,
		`commission_id` bigint(20) NOT NULL,
		`vendor_id` bigint(20) NOT NULL,
		`shipping_status` varchar(255) NOT NULL,
		`order_item_id` bigint(20) NOT NULL,
		`product_id` bigint(20) NOT NULL,
		`commission_amount` varchar(255) NOT NULL,
		`shipping` varchar(255) NOT NULL,
		`tax` varchar(255) NOT NULL,
		`is_trashed` varchar(10) NOT NULL,				
		`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,				
		PRIMARY KEY (`ID`),
		CONSTRAINT vendor_orders UNIQUE (order_id, vendor_id, commission_id, product_id)
		) $collate;";

        $create_tables_query[] = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "wcmp_products_map` (
		`ID` bigint(20) NOT NULL AUTO_INCREMENT,
		`product_title` varchar(255) NOT NULL,
		`product_ids` text NOT NULL,						
		`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,				
		PRIMARY KEY (`ID`)
		) $collate;";

        foreach ($create_tables_query as $create_table_query) {
            $wpdb->query($create_table_query);
        }
    }

    /**
     * Migrate old data
     * @global type $WCMp
     * @global object $wpdb
     */
    function do_wcmp_migrate() {
        global $WCMp, $wpdb;
        $previous_plugin_version = get_option('dc_product_vendor_plugin_db_version');
        #region map existing product in product map table
        if (empty(get_option('is_wcmp_product_sync_with_multivendor'))) {
            $args_multi_vendor = array(
                'posts_per_page' => -1,
                'post_type' => 'product',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $post_array = get_posts($args_multi_vendor);
            foreach ($post_array as $product_post) {
                $results = $wpdb->get_results("select * from {$wpdb->prefix}wcmp_products_map where product_title = '{$product_post->post_title}' ");
                if (is_array($results) && (count($results) > 0)) {
                    $id_of_similar = $results[0]->ID;
                    $product_ids = $results[0]->product_ids;
                    $product_ids_arr = explode(',', $product_ids);
                    if (is_array($product_ids_arr) && !in_array($product_post->ID, $product_ids_arr)) {
                        $product_ids = $product_ids . ',' . $product_post->ID;
                        $wpdb->query("update {$wpdb->prefix}wcmp_products_map set product_ids = '{$product_ids}' where ID = {$id_of_similar}");
                    }
                } else {
                    $wpdb->query("insert into {$wpdb->prefix}wcmp_products_map set product_title='{$product_post->post_title}', product_ids = '{$product_post->ID}' ");
                }
            }
            update_option('is_wcmp_product_sync_with_multivendor', 1);
        }
        #endregion
        do_wcmp_data_migrate($previous_plugin_version, $WCMp->version);
    }

}
