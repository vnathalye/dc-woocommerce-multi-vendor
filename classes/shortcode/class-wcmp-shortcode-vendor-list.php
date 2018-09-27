<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WCMp_Shortcode_Vendor_List')) {

    class WCMp_Shortcode_Vendor_List {
        
        /**
         * Filter vendor list
         * @global object $WCMp
         * @param string $orderby
         * @param string $order
         * @param string $product_category
         * @return array
         */
        public static function get_vendors_query($args, $request = array(), $ignore_pagi = false) {
            $block_vendors = wp_list_pluck(wcmp_get_all_blocked_vendors(), 'id');
            $include_vendors = array();
            $default = array (
                'role'      => 'dc_vendor',
                'fields'    => 'ids',
                'exclude'   => $block_vendors,
                'orderby'   => 'registered',
                'order'     => 'ASC',
                'number'    => 12,
            );
            if (isset($request['vendor_sort_type']) && $request['vendor_sort_type'] == 'category' && isset($request['vendor_sort_category'])) {
                $pro_args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'product',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => absint($request['vendor_sort_category'])
                        )
                    )
                );
                $products = get_posts($pro_args);
                $product_ids = wp_list_pluck($products, 'ID');
                foreach ($product_ids as $product_id) {
                    $vendor = get_wcmp_product_vendors($product_id);
                    if ($vendor && !in_array($vendor->id, $block_vendors)) {
                        $include_vendors[] = $vendor->id;
                    }
                }
                //
                $vendor_args = wp_parse_args(array( 'include' => $include_vendors ), $default);
            } else {
                $vendor_args = wp_parse_args($args, $default);
            }
            if(get_query_var('paged')) :
                $current_page = max( 1, get_query_var('paged') );
                $offset = ($current_page - 1) * $vendor_args['number'];
                $vendor_args['offset'] = $offset;
            endif;
            
            if(!$ignore_pagi){
                $vendors_query = new WP_User_Query( $vendor_args );
            }else{
                unset($vendor_args['offset']);
                $vendor_args['number'] = -1;
                $vendors_query = new WP_User_Query( $vendor_args );
            }
            
            return $vendors_query;
            
        }

        /**
         * Filter vendor list
         * @global object $WCMp
         * @param string $orderby
         * @param string $order
         * @param string $product_category
         * @return array
         */
        public static function get_vendor($orderby = 'registered', $order = 'ASC', $product_category = '') {
            global $WCMp;
            $vendor_info = array();
            $block_vendors = wp_list_pluck(wcmp_get_all_blocked_vendors(), 'id');
            if ($product_category) {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'product',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => absint($product_category)
                        )
                    )
                );
                $products = get_posts($args);
                $product_ids = wp_list_pluck($products, 'ID');
                foreach ($product_ids as $product_id) {
                    $vendor = get_wcmp_product_vendors($product_id);
                    if ($vendor && !in_array($vendor->id, $block_vendors)) {
                        $vendor_info[$vendor->id] = array(
                            'vendor_permalink' => $vendor->permalink,
                            'vendor_name' => $vendor->page_title,
                            'vendor_image' => $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png',
                            'ID' => $vendor->id,
                            'term_id' => $vendor->term_id
                        );
                    }
                }
            } else {
                $sort_type = isset($_REQUEST['vendor_sort_type']) ? $_REQUEST['vendor_sort_type'] : '';
                $vendors = get_wcmp_vendors(apply_filters('wcmp_vendor_list_get_wcmp_vendors_args', array('orderby' => $orderby, 'order' => $order), $sort_type, $_GET));
                foreach ($vendors as $vendor) {
                    if (!in_array($vendor->id, $block_vendors)) {
                        $vendor_info[$vendor->id] = array(
                            'vendor_permalink' => $vendor->permalink,
                            'vendor_name' => $vendor->page_title,
                            'vendor_image' => $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png',
                            'ID' => $vendor->id,
                            'term_id' => $vendor->term_id
                        );
                    }
                }
            }
            return $vendor_info;
        }

        /**
         * Output vendor list shortcode
         * @global object $WCMp
         * @param array $atts
         */
        public static function output($atts) {
            global $WCMp;
            $frontend_assets_path = $WCMp->plugin_url . 'assets/frontend/';
            $frontend_assets_path = str_replace(array('http:', 'https:'), '', $frontend_assets_path);
            $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
            $WCMp->library->load_gmap_api();
            wp_register_style('wcmp_vendor_list', $frontend_assets_path . 'css/vendor-list.css', array(), $WCMp->version);
            wp_register_script('wcmp_vendor_list', $frontend_assets_path . 'js/vendor-list.js', array('jquery','wcmp-gmaps-api'), $WCMp->version, true);
            
            wp_enqueue_script('frontend_js');
            wp_enqueue_script('wcmp_vendor_list');
            
            wp_enqueue_style('wcmp_vendor_list');
            extract(shortcode_atts(array('orderby' => 'registered', 'order' => 'ASC'), $atts, 'wcmp_vendorslist'));
            $order_by = isset($_REQUEST['vendor_sort_type']) ? $_REQUEST['vendor_sort_type'] : 'registered';
            
            $query = apply_filters('wcmp_vendor_list_vendors_query_args', array(
                'number' => 12,
                'orderby' => $order_by, 
                'order' => 'ASC',
            ));
            if($order_by == 'name'){
                $query['meta_key'] = '_vendor_page_title';
                $query['orderby'] = 'meta_value';
            }
            // backward supports
            $query = apply_filters('wcmp_vendor_list_get_wcmp_vendors_args', $query, $order_by, $_REQUEST);

            $vendors_query = self::get_vendors_query($query, $_REQUEST);
            $vendors = $vendors_query->get_results();
            $vendors_total = $vendors_query->get_total();
            if(isset($_REQUEST['wcmp_vlist_center_lat']) && isset($_REQUEST['wcmp_vlist_center_lng'])){
                $vendors_query_all = self::get_vendors_query($query, $_REQUEST, true);
                $vendors = $vendors_query_all->get_results();
            }
            // map data
            $listed_stores = wcmp_get_vendor_list_map_store_data($vendors, $_REQUEST);

            if(isset($_REQUEST['wcmp_vlist_center_lat']) && isset($_REQUEST['wcmp_vlist_center_lng'])){
                $vendors = $listed_stores['vendors'];
                $vendors_total = count($listed_stores['vendors']);
            }

            $script_param = array(
                'stores'    => $listed_stores['stores'],
                'lang'      => array(
                    'geolocation_service_failed' => __('Error: The Geolocation service failed.', 'dc-woocommerce-multi-vendor'),
                    'geolocation_doesnt_support' => __('Error: Your browser does not support geolocation.', 'dc-woocommerce-multi-vendor'),
                ),
                'map_data' => array(
                    'map_options' => array('zoom' => 10, 'mapTypeControlOptions' => array('mapTypeIds' => array('roadmap', 'satellite'))),
                    'marker_icon' => $WCMp->plugin_url . 'assets/images/store-marker.png',
                    'map_style' => true,
                    'map_style_data'  => array(
                        array(
                            'stylers' => array(
                                array('saturation' => -100),
                                array('gamma' => 1),
                            ),
                        ),
                        array(
                            'featureType' => 'water',
                            'stylers' => array(
                                array('lightness' => -12)
                            )
                        ),
                    ),
                    'map_style_title' => __('Styled Map', 'dc-woocommerce-multi-vendor'),
                ),
                
            );
            $WCMp->localize_script('wcmp_vendor_list', apply_filters('wcmp_vendor_list_script_data_params',$script_param, $_REQUEST));
            $radius = apply_filters('wcmp_vendor_list_filter_radius_data', array(5,10,20,30,50));
            $data = apply_filters('wcmp_vendor_list_data', array(
                'total'   => ceil($vendors_total/$query['number']),
                'current' => max( 1, get_query_var('paged') ),
                'per_page' => $query['number'],
                'base'    => get_pagenum_link(1) . '%_%',
                'format'  => 'page/%#%/',
                'vendors'   => $vendors,
                'vendor_total' => $vendors_total,
                'radius' => $radius,
                'request' => $_REQUEST,
            ));
            $WCMp->template->get_template('shortcode/vendor_lists.php', $data);
        }
    }

}