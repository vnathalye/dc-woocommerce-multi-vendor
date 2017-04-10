<?php

if (!function_exists('get_wcmp_vendor_settings')) {

    /**
     * get plugin settings
     * @return array
     */
    function get_wcmp_vendor_settings($name = '', $tab = '', $subtab = '', $default = false) {
        if (empty($tab) && empty($name)) {
            return $default ? $default : '';
        }
        if (empty($tab)) {
            return get_option($name);
        }
        if (empty($name)) {
            return get_option("wcmp_{$tab}_settings_name");
        }
        if (!empty($subtab)) {
            $settings = get_option("wcmp_{$tab}_{$subtab}_settings_name");
        } else {
            $settings = get_option("wcmp_{$tab}_settings_name");
        }
        if (!isset($settings[$name])) {
            return $default ? $default : '';
        }
        return $settings[$name];
    }

}

if (!function_exists('update_wcmp_vendor_settings')) {

    function update_wcmp_vendor_settings($name = '', $value = '', $tab = '', $subtab = '') {
        if (empty($name) || empty($value)) {
            return;
        }
        if (!empty($subtab)) {
            $option_name = "wcmp_{$tab}_{$subtab}_settings_name";
            $settings = get_option("wcmp_{$tab}_{$subtab}_settings_name");
        } else {
            $option_name = "wcmp_{$tab}_settings_name";
            $settings = get_option("wcmp_{$tab}_settings_name");
        }
        $settings[$name] = $value;
        update_option($option_name, $settings);
    }

}

if (!function_exists('delete_wcmp_vendor_settings')) {

    function delete_wcmp_vendor_settings($name = '', $tab = '', $subtab = '') {
        if (empty($name)) {
            return;
        }
        if (!empty($subtab)) {
            $option_name = "wcmp_{$tab}_{$subtab}_settings_name";
            $settings = get_option("wcmp_{$tab}_{$subtab}_settings_name");
        } else {
            $option_name = "wcmp_{$tab}_settings_name";
            $settings = get_option("wcmp_{$tab}_settings_name");
        }
        unset($settings[$name]);
        update_option($option_name, $settings);
    }

}

if (!function_exists('is_user_wcmp_pending_vendor')) {

    /**
     * Check if user is pending vendor
     * @param userid or WP_User object
     * @return boolean
     */
    function is_user_wcmp_pending_vendor($user) {
        if ($user && !empty($user)) {
            if (!is_object($user)) {
                $user = new WP_User(absint($user));
            }
            return ( is_array($user->roles) && in_array('dc_pending_vendor', $user->roles) );
        } else {
            return false;
        }
    }

}


if (!function_exists('is_user_wcmp_rejected_vendor')) {

    /**
     * Check if user is vendor
     * @param userid or WP_User object
     * @return boolean
     */
    function is_user_wcmp_rejected_vendor($user) {
        if ($user && !empty($user)) {
            if (!is_object($user)) {
                $user = new WP_User(absint($user));
            }
            return ( is_array($user->roles) && in_array('dc_rejected_vendor', $user->roles) );
        } else {
            return false;
        }
    }

}

if (!function_exists('is_user_wcmp_vendor')) {

    /**
     * Check if user is vendor
     * @param userid or WP_User object
     * @return boolean
     */
    function is_user_wcmp_vendor($user) {
        if ($user && !empty($user)) {
            if (!is_object($user)) {
                $user = new WP_User(absint($user));
            }
            return apply_filters('is_user_wcmp_vendor', ( is_array($user->roles) && in_array('dc_vendor', $user->roles)), $user);
        } else {
            return false;
        }
    }

}

if (!function_exists('get_wcmp_vendors')) {

    /**
     * Get all vendors
     * @return arr Array of vendors
     */
    function get_wcmp_vendors($args = array()) {
        $vendors_array = array();
        $args = wp_parse_args($args, array('role' => 'dc_vendor', 'fields' => 'ids', 'orderby' => 'registered', 'order' => 'ASC'));
        $user_query = new WP_User_Query($args);
        if (!empty($user_query->results)) {
            foreach ($user_query->results as $vendor_id) {
                $vendors_array[] = get_wcmp_vendor($vendor_id);
            }
        }
        return apply_filters('get_wcmp_vendors', $vendors_array);
    }

}

if (!function_exists('get_wcmp_vendor')) {

    /**
     * Get individual vendor info by ID
     * @param  int $vendor_id ID of vendor
     * @return obj            Vendor object
     */
    function get_wcmp_vendor($vendor_id = 0) {
        $vendor = false;
        if (is_user_wcmp_vendor($vendor_id)) {
            $vendor = new WCMp_Vendor(absint($vendor_id));
        }
        return $vendor;
    }

}

if (!function_exists('get_wcmp_vendor_by_term')) {

    /**
     * Get individual vendor info by term id
     * @param $term_id ID of term
     */
    function get_wcmp_vendor_by_term($term_id) {
        $vendor = false;
        if (!empty($term_id)) {
            $user_id = get_woocommerce_term_meta($term_id, '_vendor_user_id');
            if (is_user_wcmp_vendor($user_id)) {
                $vendor = get_wcmp_vendor($user_id);
            }
        }
        return $vendor;
    }

}

if (!function_exists('get_wcmp_product_vendors')) {

    /**
     * Get vendors for product
     * @param  int $product_id Product ID
     * @return arr             Array of product vendors
     */
    function get_wcmp_product_vendors($product_id = 0) {
        global $WCMp;
        $vendor_data = false;
        if ($product_id > 0) {
            $vendors_data = wp_get_post_terms($product_id, $WCMp->taxonomy->taxonomy_name);
            foreach ($vendors_data as $vendor) {
                $vendor_obj = get_wcmp_vendor_by_term($vendor->term_id);
                if ($vendor_obj) {
                    $vendor_data = $vendor_obj;
                }
            }
            if (!$vendor_data) {
                $product_obj = get_post($product_id);
                if (is_object($product_obj)) {
                    $author_id = $product_obj->post_author;
                    if ($author_id) {
                        $vendor_data = get_wcmp_vendor($author_id);
                    }
                }
            }
        }
        return $vendor_data;
    }

}

if (!function_exists('doProductVendorLOG')) {

    /**
     * Write to log file
     */
    function doProductVendorLOG($str) {
        global $WCMp;
        $file = $WCMp->plugin_path . 'log/product_vendor.log';
        if (file_exists($file)) {
            // Open the file to get existing content
            $current = file_get_contents($file);
            if ($current) {
                // Append a new content to the file
                $current .= "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            } else {
                $current = "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            }
            // Write the contents back to the file
            file_put_contents($file, $current);
        }
    }

}

if (!function_exists('is_vendor_dashboard')) {

    /**
     * check if vendor dashboard page
     * @return boolean
     */
    function is_vendor_dashboard() {
        if (!empty(get_wcmp_vendor_settings('wcmp_vendor', 'vendor', 'general'))) {
            return is_page(get_wcmp_vendor_settings('wcmp_vendor', 'vendor', 'general')) ? true : false;
        }
        return false;
    }

}

if (!function_exists('wcmp_vendor_dashboard_page_id')) {

    /**
     * Get vendor dashboard page id
     * @return int
     */
    function wcmp_vendor_dashboard_page_id() {
        if (!empty(get_wcmp_vendor_settings('wcmp_vendor', 'vendor', 'general'))) {
            return (int) get_wcmp_vendor_settings('wcmp_vendor', 'vendor', 'general');
        }
    }

}

if (!function_exists('is_page_vendor_registration')) {

    /**
     * check if vendor registration page
     * @return boolean
     */
    function is_page_vendor_registration() {
        if (!empty(get_wcmp_vendor_settings('vendor_registration', 'vendor', 'general'))) {
            return is_page(get_wcmp_vendor_settings('vendor_registration', 'vendor', 'general')) ? true : false;
        }
        return false;
    }

}

if (!function_exists('wcmp_vendor_registration_page_id')) {

    /**
     * Get vendor Registration page id
     * @return type
     */
    function wcmp_vendor_registration_page_id() {
        if (!empty(get_wcmp_vendor_settings('vendor_registration', 'vendor', 'general'))) {
            return (int) get_wcmp_vendor_settings('vendor_registration', 'vendor', 'general');
        }
    }

}

if (!function_exists('change_cap_existing_users')) {

    /**
     * Remove Capability of existing users
     * @return void
     */
    function remove_wcmp_users_caps($user_cap) {
        $product_caps = array("edit_product", "delete_product", "edit_products", "delete_products");
        $coupon_caps = array("edit_shop_coupons", "delete_shop_coupons", "edit_shop_coupons", "delete_shop_coupons");
        $wcmp_vendors = get_wcmp_vendors();
        if (!empty($wcmp_vendors) && is_array($wcmp_vendors)) {
            foreach ($wcmp_vendors as $wcmp_vendor) {
                $user = new WP_User($wcmp_vendor->id);
                if ($user) {
                    if ($user_cap == 'is_upload_files') {
                        $user->remove_cap('upload_files');
                    }
                    if ($user_cap == 'is_submit_product') {
                        foreach ($product_caps as $product_cap) {
                            $user->remove_cap($product_cap);
                        }
                    }
                    if ($user_cap == 'edit_delete_published_product') {
                        $user->remove_cap('edit_published_products');
                        $user->remove_cap('delete_published_products');
                    }
                    if ($user_cap == 'is_submit_coupon') {
                        foreach ($coupon_caps as $coupon_cap) {
                            $user->remove_cap($coupon_cap);
                        }
                    }
                    if ($user_cap == 'is_published_product') {
                        $user->remove_cap('publish_products');
                    }
                    if ($user_cap == 'edit_delete_published_coupons') {
                        $user->remove_cap('delete_published_shop_coupons');
                        $user->remove_cap('edit_published_shop_coupons');
                    }
                    if ($user_cap == 'is_published_coupon') {
                        $user->remove_cap('publish_shop_coupons');
                    }
                }
            }
        }
    }

}

if (!function_exists('add_cap_existing_users')) {

    /**
     * Add Capability in existing users
     * @return void
     */
    function add_wcmp_users_caps($user_cap) {
        $wcmp_vendors = get_wcmp_vendors();
        if (!empty($wcmp_vendors) && is_array($wcmp_vendors)) {
            $product_caps = array(
                "edit_product"
                , "delete_product"
                , "edit_products"
                , "delete_products"
            );
            $coupon_caps = array(
                "edit_shop_coupon"
                , "delete_shop_coupon"
                , "edit_shop_coupons"
                , "read_shop_coupons"
                , "delete_shop_coupons"
            );
            foreach ($wcmp_vendors as $wcmp_vendor) {
                $user = new WP_User($wcmp_vendor->id);
                if ($user) {
                    if ($user_cap == 'is_submit_product') {
                        $vendor_submit_products = get_user_meta($user->ID, '_vendor_submit_product', true);
                        if (!empty($vendor_submit_products) && $vendor_submit_products) {
                            foreach ($product_caps as $cap) {
                                $user->add_cap($cap);
                            }
                            $user->add_cap("read_product");
                        }
                    } else if ($user_cap == 'edit_delete_published_product') {
                        $user->add_cap('edit_published_products');
                        $user->add_cap('delete_published_products');
                    } else if ($user_cap == 'edit_delete_published_coupons') {
                        $user->add_cap('edit_published_shop_coupons');
                        $user->add_cap('delete_published_shop_coupons');
                    } else if ($user_cap == 'is_submit_coupon') {
                        $vendor_submit_products = get_user_meta($user->ID, '_vendor_submit_coupon', true);
                        if (!empty($vendor_submit_products) && $vendor_submit_products) {
                            foreach ($coupon_caps as $cap) {
                                $user->add_cap($cap);
                            }
                        }
                        $user->add_cap("edit_posts");
                        $user->add_cap("read_shop_coupon");
                    } else {
                        $user->add_cap($user_cap);
                    }
                }
            }
        }
    }

}


if (!function_exists('get_vendor_from_an_order')) {

    /**
     * Get vendor from a order
     * @param WC_Order $order or order id
     * @return type
     */
    function get_vendor_from_an_order($order) {
        $vendors = array();
        if (!is_object($order)) {
            $order = new WC_Order($order);
        }
        $items = $order->get_items('line_item');
        foreach ($items as $item_id => $item) {
            $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
            if ($vendor_id) {
                $term_id = get_user_meta($vendor_id, '_vendor_term_id', true);
                if (!in_array($term_id, $vendors)) {
                    $vendors[] = $term_id;
                }
            } else {
                $product_id = wc_get_order_item_meta($item_id, '_product_id', true);
                if ($product_id) {
                    $product_vendors = get_wcmp_product_vendors($product_id);
                    if ($product_vendors && !in_array($product_vendors->term_id, $vendors)) {
                        $vendors[] = $product_vendors->term_id;
                    }
                }
            }
        }
        return $vendors;
    }

}

if (!function_exists('is_vendor_page')) {

    /**
     * check if vendor pages
     * @return boolean
     */
    function is_vendor_page() {

        $return = false;
        if (!empty(wcmp_vendor_dashboard_page_id())) {
            if (function_exists('icl_object_id')) {
                if (is_page(icl_object_id(wcmp_vendor_dashboard_page_id(), 'page', false, ICL_LANGUAGE_CODE))) {
                    $return = true;
                }
            } else {
                if (is_vendor_dashboard()) {
                    $return = true;
                }
            }
        }
        return apply_filters('wcmp_plugin_pages_redirect', $return);
    }

}

if (!function_exists('is_vendor_order_by_product_page')) {

    /**
     * Check if vendor order page
     * @return boolean
     */
    function is_vendor_order_by_product_page() {
        return is_wcmp_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'));
    }

}

/* unused function */
if (!function_exists('get_vendor_coupon_amount')) {

    /**
     * get vendor coupon from order.
     * @return boolean
     */
//    function get_vendor_coupon_amount($item_product_id, $order_id, $vendor) {
//        $order = new WC_Order($order_id);
//        $coupons = $order->get_used_coupons();
//        $coupon_used = array();
//        if (!empty($coupons) && is_array($coupons)) {
//            foreach ($coupons as $coupon_code) {
//                $coupon = new WC_Coupon($coupon_code);
//                $coupon_post = get_post($coupon->id);
//                $author_id = $coupon_post->post_author;
//                if (get_current_user_id() != $author_id) {
//                    continue;
//                } else {
//                    $coupon_product_ids = $coupon->product_ids;
//                    if (!in_array($item_product_id, $coupon_product_ids)) {
//                        continue;
//                    } else {
//                        $coupon_used[] = $coupon_code;
//                    }
//                }
//            }
//            if (!empty($coupon_used) && is_array($coupon_used)) {
//                $return_coupon = ' ,   Copoun Used : ';
//                $no_of_coupon_use = false;
//                foreach ($coupon_used as $coupon_use) {
//                    if (!$no_of_coupon_use) {
//                        $return_coupon .= '"' . $coupon_use . '"';
//                    } else                         {
//                        $return_coupon .= ', "' . $coupon_use . '"';
//                    }
//                    $no_of_coupon_use = true;
//                }
//                return $return_coupon;
//            } else {
//                return null;
//            }
//        }
//    }
}

if (!function_exists('wcmp_action_links')) {

    /**
     * Product Vendor Action Links Function
     * @param plugin links
     * @return plugin links
     */
    function wcmp_action_links($links) {
        global $WCMp;
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=wcmp-setting-admin') . '">' . __('Settings', $WCMp->text_domain) . '</a>');
        return array_merge($plugin_links, $links);
    }

}

if (!function_exists('wcmp_get_all_blocked_vendors')) {

    /**
     * wcmp_get_all_blocked_vendors Function
     *
     * @access public
     * @return plugin array
     */
    function wcmp_get_all_blocked_vendors() {
        $vendors = get_wcmp_vendors();
        $blocked_vendor = array();
        if (!empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor_key => $vendor) {
                $is_block = get_user_meta($vendor->id, '_vendor_turn_off', true);
                if (!empty($is_block) && $is_block) {
                    $blocked_vendor[] = $vendor;
                }
            }
        }
        return $blocked_vendor;
    }

}

if (!function_exists('wcmp_get_vendors_due_from_order')) {

    /**
     * Get vendor due from an order.
     * @param WC_Order $order or order id
     * @return array
     */
    function wcmp_get_vendors_due_from_order($order) {
        if (!is_object($order)) {
            $order = new WC_Order($order);
        }
        $items = $order->get_items('line_item');
        $vendors_array = array();
        if ($items) {
            foreach ($items as $item_id => $item) {
                $product_id = wc_get_order_item_meta($item_id, '_product_id', true);
                if ($product_id) {
                    $vendor = get_wcmp_product_vendors($product_id);
                    if (!empty($vendor) && isset($vendor->term_id)) {
                        $vendors_array[$vendor->term_id] = $vendor->wcmp_get_vendor_part_from_order($order, $vendor->term_id);
                    }
                }
            }
        }
        return $vendors_array;
    }

}
if (!function_exists('activate_wcmp_plugin')) {

    /**
     * On activation, include the installer and run it.
     *
     * @access public
     * @return void
     */
    function activate_wcmp_plugin() {
        require_once( 'class-wcmp-install.php' );
        new WCMp_Install();
        update_option('dc_product_vendor_plugin_installed', 1);
    }

}

if (!function_exists('deactivate_wcmp_plugin')) {

    /**
     * On deactivation delete page install option
     */
    function deactivate_wcmp_plugin() {
        delete_option('dc_product_vendor_plugin_page_install');
    }

}




if (!function_exists('wcmp_check_if_another_vendor_plugin_exits')) {

    /**
     * On activation, check if another vendor plugin installed.
     *
     * @access public
     * @return void
     */
    function wcmp_check_if_another_vendor_plugin_exits() {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        $vendor_arr = array();
        $vendor_arr[] = 'dokan-lite/dokan.php';
        $vendor_arr[] = 'wc-vendors/class-wc-vendors.php';
        $vendor_arr[] = 'yith-woocommerce-product-vendors/init.php';
        foreach ($vendor_arr as $plugin) {
            if (is_plugin_active($plugin)) {
                deactivate_plugins('dc-woocommerce-multi-vendor/dc_product_vendor.php');
                exit(__('Another Multivendor Plugin is allready Activated Please deactivate first to install this plugin', 'WCMp'));
            }
        }
    }

}



if (!function_exists('wcmpArrayToObject')) {

    /**
     * Convert php array to object
     * @param array $d
     * @return object
     */
    function wcmpArrayToObject($d) {
        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return (object) array_map(__FUNCTION__, $d);
        } else {
            // Return object
            return $d;
        }
    }

}

if (!function_exists('wcmp_paid_commission_status')) {

    function wcmp_paid_commission_status($commission_id) {
        update_post_meta($commission_id, '_paid_status', 'paid', 'unpaid');
        update_post_meta($commission_id, '_paid_date', time());
    }

}
if (!function_exists('wcmp_rangeWeek')) {

    /**
     * Calculate start date and end date of a week
     * @param date $datestr
     * @return array
     */
    function wcmp_rangeWeek($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        $res['start'] = date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
        $res['end'] = date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
        return $res;
    }

}
if (!function_exists('wcmp_role_exists')) {

    /**
     * Check if role exist or not
     * @param string $role
     * @return boolean
     */
    function wcmp_role_exists($role) {
        if (!empty($role)) {
            return $GLOBALS['wp_roles']->is_role($role);
        }
        return false;
    }

}

if (!function_exists('wcmp_seller_review_enable')) {

    /**
     * Check if vendor review enable or not
     * @param type $vendor_term_id
     * @return type
     */
    function wcmp_seller_review_enable($vendor_term_id) {
        $is_enable = false;
        $current_user = wp_get_current_user();
        if ($current_user->ID > 0) {
            if (get_wcmp_vendor_settings('is_sellerreview', 'general') == 'Enable') {
                if (get_wcmp_vendor_settings('is_sellerreview_varified', 'general') == 'Enable') {
                    $is_enable = wcmp_find_user_purchased_with_vendor($current_user->ID, $vendor_term_id);
                } else {
                    $is_enable = true;
                }
            }
        }
        return apply_filters('wcmp_seller_review_enable', $is_enable);
    }

}

if (!function_exists('wcmp_find_user_purchased_with_vendor')) {

    /**
     * Check if a user purchase product from given vendor or not
     * @param type $user_id
     * @param type $vendor_term_id
     * @return boolean
     */
    function wcmp_find_user_purchased_with_vendor($user_id, $vendor_term_id) {
        $is_purchased_with_vendor = false;
        $order_lits = wcmp_get_all_order_of_user($user_id);
        foreach ($order_lits as $order) {
            $vendors = get_vendor_from_an_order($order->ID);
            if (!empty($vendors) && is_array($vendors)) {
                if (in_array($vendor_term_id, $vendors)) {
                    $is_purchased_with_vendor = true;
                    break;
                }
            }
        }
        return $is_purchased_with_vendor;
    }

}

if (!function_exists('wcmp_get_vendor_dashboard_nav_item_css_class')) {

    function wcmp_get_vendor_dashboard_nav_item_css_class($endpoint, $force_active = false) {
        global $wp;
        $cssClass = array(
            'wcmp-venrod-dashboard-nav-link',
            'wcmp-venrod-dashboard-nav-link--' . $endpoint
        );
        $current = isset($wp->query_vars[$endpoint]);
        if ('dashboard' === $endpoint && ( isset($wp->query_vars['page']) || empty($wp->query_vars) )) {
            $current = true; // Dashboard is not an endpoint, so needs a custom check.
        }
        if ($current || $force_active) {
            $cssClass[] = 'active';
        }
        $cssClass = apply_filters('wcmp_vendor_dashboard_nav_item_css_class', $cssClass, $endpoint);
        return implode(' ', array_map('sanitize_html_class', $cssClass));
    }

}

if (!function_exists('wcmp_get_vendor_dashboard_endpoint_url')) {

    function wcmp_get_vendor_dashboard_endpoint_url($endpoint, $value = '', $withvalue = false) {
        global $wp;
        $permalink = get_permalink(wcmp_vendor_dashboard_page_id());
        if (empty($value)) {
            $value = isset($wp->query_vars[$endpoint]) && !empty($wp->query_vars[$endpoint]) && $withvalue ? $wp->query_vars[$endpoint] : '';
        }
        if (get_option('permalink_structure')) {
            if (strstr($permalink, '?')) {
                $query_string = '?' . parse_url($permalink, PHP_URL_QUERY);
                $permalink = current(explode('?', $permalink));
            } else {
                $query_string = '';
            }
            if ($endpoint == 'dashboard') {
                $url = trailingslashit($permalink) . $query_string;
            } else {
                $url = trailingslashit($permalink) . $endpoint . '/' . $value . $query_string;
            }
        } else {
            if ($endpoint == 'dashboard') {
                $url = $permalink;
            } else {
                $url = add_query_arg($endpoint, $value, $permalink);
            }
        }

        return apply_filters('wcmp_get_vendor_dashboard_endpoint_url', $url, $endpoint, $value, $permalink);
    }

}
if (!function_exists('is_wcmp_endpoint_url')) {

    /**
     * is_wc_endpoint_url - Check if an endpoint is showing.
     * @param  string $endpoint
     * @return bool
     */
    function is_wcmp_endpoint_url($endpoint = false) {
        global $wp, $WCMp;
        $wcmp_endpoints = $WCMp->endpoints->wcmp_query_vars;

        if ($endpoint !== false) {
            if (!isset($wcmp_endpoints[$endpoint])) {
                return false;
            } else {
                $endpoint_var = $wcmp_endpoints[$endpoint];
            }

            return isset($wp->query_vars[$endpoint_var['endpoint']]);
        } else {
            foreach ($wcmp_endpoints as $key => $value) {
                if (isset($wp->query_vars[$key])) {
                    return true;
                }
            }

            return false;
        }
    }

}

if (!function_exists('wcmp_get_all_order_of_user')) {

    /**
     * Get all order of a customer
     * @param int $user_id
     * @return array
     */
    function wcmp_get_all_order_of_user($user_id) {
        $order_lits = array();
        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'meta_value' => $user_id,
            'post_type' => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses()),
        ));
        if (count($customer_orders > 0)) {
            $order_lits = $customer_orders;
        }
        return $order_lits;
    }

}
if (!function_exists('wcmp_review_is_from_verified_owner')) {

    /**
     * Check if given comment from verified customer or not
     * @param object $comment
     * @param int $vendor_term_id
     * @return boolean
     */
    function wcmp_review_is_from_verified_owner($comment, $vendor_term_id) {
        $user_id = $comment->user_id;
        return wcmp_find_user_purchased_with_vendor($user_id, $vendor_term_id);
    }

}

if (!function_exists('wcmp_get_vendor_review_info')) {

    /**
     * Get vendor review information
     * @global type $wpdb
     * @param type $vendor_term_id
     * @return type
     */
    function wcmp_get_vendor_review_info($vendor_term_id) {
        global $wpdb;
        $rating = 0;
        $count = 0;
        $arr = array();
        $vendor = get_wcmp_vendor_by_term($vendor_term_id);
        $results = $wpdb->get_results("SELECT `comment_id` FROM {$wpdb->prefix}commentmeta where meta_key='vendor_rating_id' and meta_value={$vendor->id}");
        $count = count($results);
        foreach ($results as $result) {
            $arr[] = $result->comment_id;
        }
        $comment_ids = implode(', ', $arr);
        if (!empty($comment_ids)) {
            $results_rating = $wpdb->get_results("SELECT SUM(meta_value) as rating_val FROM {$wpdb->prefix}commentmeta where meta_key = 'vendor_rating' and `comment_id` IN ({$comment_ids})");
        }
        if ($count > 0) {
            $rating = $results_rating[0]->rating_val / $count;
        }
        $rating_result_array['total_rating'] = $count;
        $rating_result_array['avg_rating'] = $rating;
        return $rating_result_array;
    }

}

if (!function_exists('wcmp_sort_by_rating_multiple_product')) {

    /**
     * Sort product by products ratings
     * @param type $more_product_array
     * @return type
     */
    function wcmp_sort_by_rating_multiple_product($more_product_array) {
        $more_product_array2 = array();
        $j = 0;
        foreach ($more_product_array as $more_product) {

            if ($j == 0) {
                $more_product_array2[] = $more_product;
            } elseif ($more_product['is_vendor'] == 0) {
                $more_product_array2[] = $more_product;
            } elseif ($more_product['rating_data']['avg_rating'] == 0) {
                $more_product_array2[] = $more_product;
            } elseif ($more_product['rating_data']['avg_rating'] > 0) {
                if (isset($more_product_array2[0]['rating_data']['avg_rating'])) {
                    $i = 0;
                    while ($more_product_array2[$i]['rating_data']['avg_rating'] >= $more_product['rating_data']['avg_rating']) {
                        $i++;
                    }
                    if ($i == 0) {
                        array_unshift($more_product_array2, $more_product);
                    } elseif ($i == (count($more_product_array2) - 1)) {
                        if (isset($more_product_array2[$i]['rating_data']['avg_rating']) && $more_product_array2[$i]['rating_data']['avg_rating'] <= $more_product['rating_data']['avg_rating']) {
                            $temp = $more_product_array2[$i];
                            $more_product_array2[$i] = $more_product;
                            array_push($more_product_array2, $temp);
                        } else {
                            array_push($more_product_array2, $more_product);
                        }
                    } else {
                        $array_1 = array_slice($more_product_array2, 0, $i);
                        $array_2 = array_slice($more_product_array2, $i);
                        array_push($array_1, $more_product);
                        $more_product_array2 = array_merge($array_1, $array_2);
                    }
                } else {
                    array_unshift($more_product_array2, $more_product);
                }
            }
            $j++;
        }
        return $more_product_array2;
    }

}

if (!function_exists('wcmp_remove_comments_section_from_vendor_dashboard')) {

    /**
     * Remove comments from vendor dashbord
     */
    function wcmp_remove_comments_section_from_vendor_dashboard() {
        if (is_vendor_dashboard()) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('div#comments').remove();
                });
            </script>
            <?php

        }
    }

}

if (!function_exists('do_wcmp_data_migrate')) {

    /**
     * Migrate Old WCMp data
     * @param string $previous_plugin_version
     */
    function do_wcmp_data_migrate($previous_plugin_version = '', $new_plugin_version = '') {
        global $WCMp;
        if (!empty($previous_plugin_version) && $previous_plugin_version <= '2.6.0' && empty(get_option('wcmp_database_upgrade'))) {
            /* remove unwanted vendor caps */
            $args = array('role' => 'dc_vendor', 'fields' => 'ids', 'orderby' => 'registered', 'order' => 'ASC');
            $user_query = new WP_User_Query($args);
            if (!empty($user_query->results)) {
                foreach ($user_query->results as $vendor_id) {
                    $user = new WP_User($vendor_id);
                    if ($user) {
                        if ($user->has_cap('edit_others_products')) {
                            $user->remove_cap('edit_others_products');
                        }
                        if ($user->has_cap('delete_others_products')) {
                            $user->remove_cap('delete_others_products');
                        }
                        if ($user->has_cap('edit_others_shop_coupons')) {
                            $user->remove_cap('edit_others_shop_coupons');
                        }
                        if ($user->has_cap('delete_others_shop_coupons')) {
                            $user->remove_cap('delete_others_shop_coupons');
                        }
                    }
                }
            }
            #region settings tab general data migrate
            if (!empty(get_wcmp_vendor_settings('is_singleproductmultiseller', 'general', 'singleproductmultiseller')) && get_wcmp_vendor_settings('is_singleproductmultiseller', 'general', 'singleproductmultiseller') == 'Enable') {
                update_wcmp_vendor_settings('is_singleproductmultiseller', 'Enable', 'general');
            }
            delete_wcmp_vendor_settings('is_singleproductmultiseller', 'general', 'singleproductmultiseller');
            if (!empty(get_wcmp_vendor_settings('is_sellerreview', 'general', 'sellerreview')) && get_wcmp_vendor_settings('is_sellerreview', 'general', 'sellerreview') == 'Enable') {
                update_wcmp_vendor_settings('is_sellerreview', 'Enable', 'general');
            }
            delete_wcmp_vendor_settings('is_sellerreview', 'general', 'sellerreview');
            if (!empty(get_wcmp_vendor_settings('is_sellerreview_varified', 'general', 'sellerreview')) == 'Enable' && get_wcmp_vendor_settings('is_sellerreview_varified', 'general', 'sellerreview')) {
                update_wcmp_vendor_settings('is_sellerreview_varified', 'Enable', 'general');
            }
            delete_wcmp_vendor_settings('is_sellerreview_varified', 'general', 'sellerreview');
            if (!empty(get_wcmp_vendor_settings('is_policy_on', 'general', 'policies')) && get_wcmp_vendor_settings('is_policy_on', 'general', 'policies') == 'Enable') {
                update_wcmp_vendor_settings('is_policy_on', 'Enable', 'general');
            }
            delete_wcmp_vendor_settings('is_policy_on', 'general', 'policies');
            if (!empty(get_wcmp_vendor_settings('is_customer_support_details', 'general', 'customer_support_details')) && get_wcmp_vendor_settings('is_customer_support_details', 'general', 'customer_support_details') == 'Enable') {
                update_wcmp_vendor_settings('is_customer_support_details', 'Enable', 'general');
            }
            delete_wcmp_vendor_settings('is_customer_support_details', 'general', 'customer_support_details');
            #endregion
            
            #region migrate other data
            if (get_wcmp_vendor_settings('can_vendor_edit_policy_tab_label', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('can_vendor_edit_policy_tab_label', 'Enable', 'general', 'policies');
            }
            delete_wcmp_vendor_settings('can_vendor_edit_policy_tab_label', 'capabilities');
            if (get_wcmp_vendor_settings('can_vendor_edit_cancellation_policy', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('can_vendor_edit_cancellation_policy', 'Enable', 'general', 'policies');
            }
            delete_wcmp_vendor_settings('can_vendor_edit_cancellation_policy', 'capabilities');
            if (get_wcmp_vendor_settings('can_vendor_edit_refund_policy', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('can_vendor_edit_refund_policy', 'Enable', 'general', 'policies');
            }
            delete_wcmp_vendor_settings('can_vendor_edit_refund_policy', 'capabilities');
            if (get_wcmp_vendor_settings('can_vendor_edit_shipping_policy', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('can_vendor_edit_shipping_policy', 'Enable', 'general', 'policies');
            }
            delete_wcmp_vendor_settings('can_vendor_edit_refund_policy', 'capabilities');
            if (get_wcmp_vendor_settings('can_vendor_add_customer_support_details', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('can_vendor_add_customer_support_details', 'Enable', 'general', 'customer_support_details');
            }
            delete_wcmp_vendor_settings('can_vendor_add_customer_support_details', 'capabilities');
            /* product tab */
            if (get_wcmp_vendor_settings('inventory', 'product') == 'Enable') {
                update_wcmp_vendor_settings('inventory', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('inventory', 'product');
            if (get_wcmp_vendor_settings('shipping', 'product') == 'Enable') {
                update_wcmp_vendor_settings('shipping', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('shipping', 'product');
            if (get_wcmp_vendor_settings('linked_products', 'product') == 'Enable') {
                update_wcmp_vendor_settings('linked_products', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('linked_products', 'product');
            if (get_wcmp_vendor_settings('attribute', 'product') == 'Enable') {
                update_wcmp_vendor_settings('attribute', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('attribute', 'product');
            if (get_wcmp_vendor_settings('advanced', 'product') == 'Enable') {
                update_wcmp_vendor_settings('advanced', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('advanced', 'product');
            if (get_wcmp_vendor_settings('simple', 'product') == 'Enable') {
                update_wcmp_vendor_settings('simple', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('simple', 'product');
            if (get_wcmp_vendor_settings('variable', 'product') == 'Enable') {
                update_wcmp_vendor_settings('variable', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('variable', 'product');
            if (get_wcmp_vendor_settings('grouped', 'product') == 'Enable') {
                update_wcmp_vendor_settings('grouped', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('grouped', 'product');
            if (get_wcmp_vendor_settings('external', 'product') == 'Enable') {
                update_wcmp_vendor_settings('external', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('external', 'product');
            if (get_wcmp_vendor_settings('virtual', 'product') == 'Enable') {
                update_wcmp_vendor_settings('virtual', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('virtual', 'product');
            if (get_wcmp_vendor_settings('downloadable', 'product') == 'Enable') {
                update_wcmp_vendor_settings('downloadable', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('downloadable', 'product');
            if (get_wcmp_vendor_settings('sku', 'product') == 'Enable') {
                update_wcmp_vendor_settings('sku', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('sku', 'product');
            if (get_wcmp_vendor_settings('taxes', 'product') == 'Enable') {
                update_wcmp_vendor_settings('taxes', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('taxes', 'product');
            if (get_wcmp_vendor_settings('add_comment', 'product') == 'Enable') {
                update_wcmp_vendor_settings('add_comment', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('add_comment', 'product');
            if (get_wcmp_vendor_settings('comment_box', 'product') == 'Enable') {
                update_wcmp_vendor_settings('comment_box', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('comment_box', 'product');
            if (get_wcmp_vendor_settings('stylesheet', 'product') == 'Enable') {
                update_wcmp_vendor_settings('stylesheet', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('stylesheet', 'product');
            /* Capability tab */
            if (get_wcmp_vendor_settings('is_submit_product', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_submit_product', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('is_submit_product', 'capabilities');
            if (get_wcmp_vendor_settings('is_published_product', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_published_product', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('is_published_product', 'capabilities');
            if (get_wcmp_vendor_settings('is_upload_files', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_upload_files', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('is_upload_files', 'capabilities');
            if (get_wcmp_vendor_settings('is_submit_coupon', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_submit_coupon', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('is_submit_coupon', 'capabilities');
            if (get_wcmp_vendor_settings('is_published_coupon', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_published_coupon', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('is_published_coupon', 'capabilities');
            if (get_wcmp_vendor_settings('is_edit_published_product', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_edit_published_product', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('is_edit_published_product', 'capabilities');
            if (get_wcmp_vendor_settings('is_edit_delete_published_coupon', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_edit_delete_published_coupon', 'Enable', 'capabilities', 'product');
            }
            delete_wcmp_vendor_settings('is_edit_delete_published_coupon', 'capabilities');
            /* order tab */
            if (get_wcmp_vendor_settings('is_order_csv_export', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_order_csv_export', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('is_order_csv_export', 'capabilities');
            if (get_wcmp_vendor_settings('is_show_email', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_show_email', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('is_show_email', 'capabilities');
            if (get_wcmp_vendor_settings('show_customer_dtl', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('show_customer_dtl', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('show_customer_dtl', 'capabilities');
            if (get_wcmp_vendor_settings('show_customer_billing', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('show_customer_billing', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('show_customer_billing', 'capabilities');
            if (get_wcmp_vendor_settings('show_customer_shipping', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('show_customer_shipping', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('show_customer_shipping', 'capabilities');
            if (get_wcmp_vendor_settings('show_cust_add', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('show_cust_add', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('show_cust_add', 'capabilities');
            if (get_wcmp_vendor_settings('show_cust_billing_add', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('show_cust_billing_add', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('show_cust_billing_add', 'capabilities');
            if (get_wcmp_vendor_settings('show_cust_shipping_add', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('show_cust_shipping_add', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('show_cust_shipping_add', 'capabilities');
            if (get_wcmp_vendor_settings('show_cust_order_calulations', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('show_cust_order_calulations', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('show_cust_order_calulations', 'capabilities');
            if (get_wcmp_vendor_settings('is_vendor_submit_comment', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_vendor_submit_comment', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('is_vendor_submit_comment', 'capabilities');
            if (get_wcmp_vendor_settings('is_vendor_view_comment', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_vendor_view_comment', 'Enable', 'capabilities', 'order');
            }
            delete_wcmp_vendor_settings('is_vendor_view_comment', 'capabilities');
            /* Mis Tab */
            if (get_wcmp_vendor_settings('can_vendor_add_message_on_email_and_thankyou_page', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('can_vendor_add_message_on_email_and_thankyou_page', 'Enable', 'capabilities', 'miscellaneous');
            }
            delete_wcmp_vendor_settings('can_vendor_add_message_on_email_and_thankyou_page', 'capabilities');
            if (get_wcmp_vendor_settings('is_vendor_add_external_url', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_vendor_add_external_url', 'Enable', 'capabilities', 'miscellaneous');
            }
            delete_wcmp_vendor_settings('is_vendor_add_external_url', 'capabilities');
            if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities') == 'Enable') {
                update_wcmp_vendor_settings('is_hide_option_show', 'Enable', 'capabilities', 'miscellaneous');
            }
            delete_wcmp_vendor_settings('is_hide_option_show', 'capabilities');
            #endregion

            $wcmp_pages = get_option('wcmp_pages_settings_name');
            $wcmp_old_pages = array(
                'vendor_dashboard' => 'wcmp_product_vendor_vendor_dashboard_page_id'
                , 'shop_settings' => 'wcmp_product_vendor_shop_settings_page_id'
                , 'view_order' => 'wcmp_product_vendor_vendor_orders_page_id'
                , 'vendor_order_detail' => 'wcmp_product_vendor_vendor_order_detail_page_id'
                , 'vendor_transaction_thankyou' => 'wcmp_product_vendor_transaction_widthdrawal_page_id'
                , 'vendor_transaction_detail' => 'wcmp_product_vendor_transaction_details_page_id'
                , 'vendor_policies' => 'wcmp_product_vendor_policies_page_id'
                , 'vendor_billing' => 'wcmp_product_vendor_billing_page_id'
                , 'vendor_shipping' => 'wcmp_product_vendor_shipping_page_id'
                , 'vendor_report' => 'wcmp_product_vendor_report_page_id'
                , 'vendor_widthdrawals' => 'wcmp_product_vendor_widthdrawals_page_id'
                , 'vendor_university' => 'wcmp_product_vendor_university_page_id'
                , 'vendor_announcements' => 'wcmp_product_vendor_announcements_page_id'
            );
            foreach ($wcmp_old_pages as $page_slug => $page_option) {
                $trash_status = wp_trash_post(get_option($page_option));
                if ($trash_status) {
                    delete_option($page_option);
                    unset($wcmp_pages[$page_slug]);
                }
            }
            update_option('wcmp_pages_settings_name', $wcmp_pages);

            #region update page option
            if (!empty(get_wcmp_vendor_settings('wcmp_vendor', 'pages'))) {
                update_wcmp_vendor_settings('wcmp_vendor', get_wcmp_vendor_settings('wcmp_vendor', 'pages'), 'vendor', 'general');
            }
            if (!empty(get_wcmp_vendor_settings('vendor_registration', 'pages'))) {
                update_wcmp_vendor_settings('vendor_registration', get_wcmp_vendor_settings('vendor_registration', 'pages'), 'vendor', 'general');
            }
            $WCMp->load_class('endpoints');
            $endpoints = new WCMp_Endpoints();
            $endpoints->add_wcmp_endpoints();
            flush_rewrite_rules();
            delete_option('wcmp_pages_settings_name');
            update_option('wcmp_database_upgrade', 'done');
            #endregion
        }

        if (!empty($previous_plugin_version) && $previous_plugin_version <= '2.4.1') {
            $vendor_role = get_role('dc_vendor');
            $vendor_role->remove_cap('manage_woocommerce');
            /* Update WCMp Options from previous version */
            $prev_general = get_option('dc_general_settings_name');
            $prev_product = get_option('dc_product_settings_name');
            $prev_capability = get_option('dc_capabilities_settings_name');
            $prev_pages = get_option('dc_pages_settings_name');
            $prev_payment = get_option('dc_payment_settings_name');
            $new_general = $new_product = $new_capability = $new_pages = $new_payment = $new_frontend = array();
            $new_payment = $prev_payment;
            if (!empty($prev_general)) {
                if (isset($prev_general['enable_registration'])) {
                    $new_general['enable_registration'] = 'Enable';
                }
                if (isset($prev_general['approve_vendor_manually'])) {
                    $new_general['approve_vendor_manually'] = 'Enable';
                }
                if (isset($prev_general['notify_configure_vendor_store'])) {
                    $new_general['notify_configure_vendor_store'] = $prev_general['notify_configure_vendor_store'];
                }
                if (isset($prev_general['default_commission'])) {
                    $new_payment['default_commission'] = $prev_general['default_commission'];
                }
                if (isset($prev_general['commission_type'])) {
                    $new_payment['commission_type'] = $prev_general['commission_type'];
                }
                if (isset($prev_general['commission_include_coupon'])) {
                    $new_payment['commission_include_coupon'] = $prev_general['commission_include_coupon'];
                }
                if (isset($prev_general['sold_by_catalog'])) {
                    $new_frontend['sold_by_catalog'] = $prev_general['sold_by_catalog'];
                }
                if (isset($prev_general['catalog_colorpicker'])) {
                    $new_frontend['catalog_colorpicker'] = $prev_general['catalog_colorpicker'];
                }
                if (isset($prev_general['catalog_hover_colorpicker'])) {
                    $new_frontend['catalog_hover_colorpicker'] = $prev_general['catalog_hover_colorpicker'];
                }
                if (isset($prev_general['sold_by_cart_and_checkout'])) {
                    $new_frontend['sold_by_cart_and_checkout'] = $prev_general['sold_by_cart_and_checkout'];
                }
                if (isset($prev_general['sold_by_text'])) {
                    $new_frontend['sold_by_text'] = $prev_general['sold_by_text'];
                }
                if (isset($prev_general['block_vendor_desc'])) {
                    $new_frontend['block_vendor_desc'] = $prev_general['block_vendor_desc'];
                }
            }
            if (!empty($prev_capability)) {
                $new_capability = $prev_capability;
                if (isset($new_capability['give_tax'])) {
                    $new_payment['give_tax'] = $new_capability['give_tax'];
                    unset($new_capability['give_tax']);
                }
                if (isset($new_capability['give_shipping'])) {
                    $new_payment['give_shipping'] = $new_capability['give_shipping'];
                    unset($new_capability['give_shipping']);
                }
                if ($previous_plugin_version <= '2.3.3') {
                    $new_capability['is_hide_option_show'] = 'Enable';
                }
            }
            if (!empty($prev_product)) {
                update_option('wcmp_product_settings_name', $prev_product);
            }
            if (!empty($prev_pages)) {
                update_option('wcmp_pages_settings_name', $prev_pages);
            }
            if (!empty($new_general)) {
                update_option('wcmp_general_settings_name', $new_general);
            }
            if (!empty($new_capability)) {
                update_option('wcmp_capabilities_settings_name', $new_capability);
            }
            if (!empty($new_payment)) {
                update_option('wcmp_payment_settings_name', $new_payment);
            }
            if (!empty($new_frontend)) {
                update_option('wcmp_frontend_settings_name', $new_frontend);
            }
            /* delete all previous options */
            delete_option('dc_general_settings_name');
            delete_option('dc_product_settings_name');
            delete_option('dc_capabilities_settings_name');
            delete_option('dc_payment_settings_name');
            delete_option('dc_pages_settings_name');

            $wcmp_payment_settings = get_option('wcmp_payment_settings_name', true);
            $wcmp_payment_paypal_masspay_settings_name = array();
            $wcmp_payment_paypal_payout_settings_name = array();
            /* Update paypal details from previous version */
            if (!empty($wcmp_payment_settings) && is_array($wcmp_payment_settings)) {
                foreach ($wcmp_payment_settings as $wcmp_payment_settings_key => $wcmp_payment_settings_value) {
                    switch ($wcmp_payment_settings_key) {
                        case 'api_username':
                            $wcmp_payment_paypal_masspay_settings_name['api_username'] = $wcmp_payment_settings_value;
                            break;
                        case 'api_pass':
                            $wcmp_payment_paypal_masspay_settings_name['api_pass'] = $wcmp_payment_settings_value;
                            break;
                        case 'api_signature':
                            $wcmp_payment_paypal_masspay_settings_name['api_signature'] = $wcmp_payment_settings_value;
                            break;
                        case 'is_testmode':
                            $wcmp_payment_paypal_masspay_settings_name['api_username'] = $wcmp_payment_settings_value;
                            $wcmp_payment_paypal_payout_settings_name['is_testmode'] = $wcmp_payment_settings_value;
                            break;
                        case 'client_id':
                            $wcmp_payment_paypal_payout_settings_name['client_id'] = $wcmp_payment_settings_value;
                            break;
                        case 'client_secret':
                            $wcmp_payment_paypal_payout_settings_name['client_secret'] = $wcmp_payment_settings_value;
                            break;
                    }
                }
                update_option('wcmp_payment_paypal_masspay_settings_name', $wcmp_payment_paypal_masspay_settings_name);
                update_option('wcmp_payment_paypal_payout_settings_name', $wcmp_payment_paypal_payout_settings_name);
            }

            $WCMp_Calculate_Commission_obj = new WCMp_Calculate_Commission();
            $vendors = get_wcmp_vendors();
            /* Set vendor as product author */
            if (!empty($vendors) && is_array($vendors)) {
                foreach ($vendors as $vendor) {
                    $vendorusers = new WP_User($vendor->id);
                    $vendorusers->remove_cap('manage_woocommerce');
                    $vendor_products = $vendor->get_products();
                    if (!empty($vendor_products)) {
                        foreach ($vendor_products as $vendor_product) {
                            wp_update_post(array('ID' => $vendor_product->ID, 'post_author' => $vendor->id));
                            $product_obj = wc_get_product($vendor_product->ID);
                            if ($product_obj->is_type('variable')) {
                                $childrens = $product_obj->get_children();
                                foreach ($childrens as $child_id) {
                                    wp_update_post(array('ID' => $child_id, 'post_author' => $vendor->id));
                                }
                            }
                        }
                    }
                }
            }
            /* Migrate WCMp vendor Orders */
            if (!empty($vendors) && is_array($vendors) && !get_option('wcmp_vendor_orders_update')) {
                $vendor_orders_array = array();
                foreach ($vendors as $vendor) {
                    $vendor_orders = $vendor->get_orders();
                    if (!empty($vendor_orders) && is_array($vendor_orders)) {
                        foreach ($vendor_orders as $commission_id => $order_id) {
                            $vendor_shipping_array = get_post_meta($order_id, 'dc_pv_shipped', true);
                            $order = new WC_Order($order_id);
                            $commission_array = array();
                            $mark_ship = false;
                            $items = $order->get_items('line_item');
                            foreach ($items as $order_item_id => $item) {
                                $comm_pro_id = $product_id = wc_get_order_item_meta($order_item_id, '_product_id', true);
                                $variation_id = wc_get_order_item_meta($order_item_id, '_variation_id', true);
                                if ($variation_id) {
                                    $comm_pro_id = $variation_id;
                                }
                                if ($product_id) {
                                    $product_vendors = get_wcmp_product_vendors($product_id);
                                    if ($product_vendors) {
                                        if (isset($product_vendors->id)) {
                                            if (isset($vendor_shipping_array) && !empty($vendor_shipping_array)) {
                                                if (in_array($product_vendors->id, $vendor_shipping_array)) {
                                                    $mark_ship = true;
                                                } else {
                                                    $mark_ship = 0;
                                                }
                                            } else {
                                                $mark_ship = 0;
                                            }
                                            $item_commission = $WCMp_Calculate_Commission_obj->get_item_commission($comm_pro_id, $comm_pro_id, $item, $order_id, $order_item_id);
                                            $vendor_shipping_costs = $vendor->get_wcmp_vendor_shipping_total($order_id, $item);
                                            $item_shipping = ($vendor_shipping_costs['shipping_amount'] + $vendor_shipping_costs['shipping_tax']);
                                            $item_tax = get_metadata('order_item', $order_item_id, '_line_tax', true);
                                            $commission_vendor_term_id = get_post_meta($commission_id, '_commission_vendor', true);
                                            $vendor_term_id = get_user_meta($product_vendors->id, '_vendor_term_id', true);
                                            if ($commission_vendor_term_id == $vendor_term_id) {
                                                $vendor_orders_array[] = array($order_id, $commission_id, $product_vendors->id, $mark_ship, $order_item_id, $comm_pro_id, $order->get_date_created(), $item_commission, $item_shipping, $item_tax);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($vendor_orders_array) && is_array($vendor_orders_array)) {
                    usort($vendor_orders_array, 'vendor_orders_sort');
                    foreach ($vendor_orders_array as $vendor_orders) {
                        $wpdb->query(
                                $wpdb->prepare(
                                        "INSERT INTO `{$wpdb->prefix}wcmp_vendor_orders` 
                                            ( order_id
                                            , commission_id
                                            , vendor_id
                                            , shipping_status
                                            , order_item_id
                                            , product_id
                                            , created
                                            , commission_amount
                                            , shipping
                                            , tax 
                                            ) VALUES 
                                            ( %d
                                            , %d
                                            , %d
                                            , %s
                                            , %d
                                            , %d
                                            , %s
                                            , %s
                                            , %s
                                            , %s 
                                            )"
                                        , $vendor_orders[0]
                                        , $vendor_orders[1]
                                        , $vendor_orders[2]
                                        , $vendor_orders[3]
                                        , $vendor_orders[4]
                                        , $vendor_orders[5]
                                        , $vendor_orders[6]
                                        , $vendor_orders[7]
                                        , $vendor_orders[8]
                                        , $vendor_orders[9]
                                )
                        );
                    }
                    update_option('wcmp_vendor_orders_update', 1);
                }
            }
        }

        update_option('dc_product_vendor_plugin_db_version', $new_plugin_version);
    }

    /**
     * 
     * @param type $a
     * @param type $b
     * @return type
     * sort vendor order
     */
    function vendor_orders_sort($a, $b) {
        return $a[0] - $b[0];
    }

    /**
     * Multilevel sort by subarry key
     * @param array $array
     * @param string $subkey
     * @param Boolean $sort_ascending
     */
    function sksort(&$array, $subkey = "id", $sort_ascending = false) {
        if (count($array)) {
            $temp_array[key($array)] = array_shift($array);
        }
        foreach ($array as $key => $val) {
            $offset = 0;
            $found = false;
            foreach ($temp_array as $tmp_key => $tmp_val) {
                if (!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
                    $temp_array = array_merge((array) array_slice($temp_array, 0, $offset), array($key => $val), array_slice($temp_array, $offset)
                    );
                    $found = true;
                }
                $offset++;
            }
            if (!$found) {
                $temp_array = array_merge($temp_array, array($key => $val));
            }
        }
        if ($sort_ascending) {
            $array = array_reverse($temp_array);
        } else {
            $array = $temp_array;
        }
    }

}
