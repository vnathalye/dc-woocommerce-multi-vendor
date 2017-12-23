<?php
/**
 * The template for displaying vendor dashboard header content
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/vebdor-dashboard/dashboard-header.php.
 *
 * HOWEVER, on occasion WC Marketplace will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  WC Marketplace
 * @package WCMp/Templates
 * @version 3.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}
global $WCMp;
$vendor = get_wcmp_vendor(get_current_vendor_id());
$vendor_logo = $vendor->get_image() ? $vendor->get_image() : $WCMp->plugin_url . 'assets/images/default-vendor-dp.png';
$site_logo = get_wcmp_vendor_settings('wcmp_dashboard_site_logo', 'vendor', 'dashboard') ? get_wcmp_vendor_settings('wcmp_dashboard_site_logo', 'vendor', 'dashboard') : '';
?>

<!-- Top bar -->
<div class="content-padding top-navbar white-bkg">
    <div class="navbar navbar-default">
        <div class="topbar-left pull-left">
            <div class="site-logo">
                <a href="<?php echo site_url(); ?>"><img src="<?php echo $site_logo; ?>" alt="<?php echo bloginfo(); ?>"></a>
            </div>
        </div>
        <ul class="nav pull-right top-user-nav">
            <li class="dropdown login-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="la la-user"></i>
                    <span><i class="lnr lnr-chevron-down"></i></span>
                </a>
                <ul class="dropdown-menu dropdown-user dropdown-menu-right">
                    <li class="sidebar-logo">
                        <div class="text-center">
                            <div class="vendor-profile-pic-holder">
                                <img src="<?php echo $vendor_logo; ?>" alt="hard crop 130 * 130" class="img-circle">
                            </div>
                            <h4><?php echo $vendor->user_data->display_name;; ?></h4> 
                        </div>
                    </li> 
                    <li class="divider marginTop-0"></li>
                    <li><a href="<?php echo esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_store_settings_endpoint', 'vendor', 'general', 'storefront'))); ?>"><i class="la la-pencil-square"></i> <span>Storefront</span></a>
                    <li class="divider"></li>
                    <li><a href="<?php echo esc_url(wp_logout_url(get_permalink(wcmp_vendor_dashboard_page_id()))); ?>"><i class="la la-sign-out"></i> <span>Logout</span></a>
                    </li>
                </ul>
                <!-- /.dropdown -->
            </li>
        </ul>
        <ul class="nav navbar-top-links navbar-right pull-right btm-nav-fixed">
            <li class="notification-link">
                <a href="<?php echo apply_filters('wcmp_vendor_shop_permalink', esc_url($vendor->permalink)); ?>" target="_blank" title="shop">
                    <i class="la la-globe"></i> <span class="hidden-sm hidden-xs">my shop</span>
                </a>
            </li>
            <li class="notification-link">
                <a href="<?php echo apply_filters('wcmp_vendor_submit_product', esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_add_product_endpoint', 'vendor', 'general', 'add-product')))); ?>" title="add product">
                <i class="la la-cube"></i> <span class="hidden-sm hidden-xs">add product</span>
                </a>
            </li>
            <?php if (apply_filters('wcmp_show_vendor_announcements', true)) : ?>
            <li class="notification-link">
                <a href="<?php echo esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements'))); ?>" title="announcement">
                    <i class="la la-bell"></i> <span class="hidden-sm hidden-xs">announcement</span>
                    <span class="notification-blink"></span>
                </a>
            </li>
            <li class="notification-link">
                <a href="<?php echo wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_withdrawal_endpoint', 'vendor', 'general', 'vendor-withdrawal')); ?>" title="<?php _e('withdrawal', 'dc-woocommerce-multi-vendor'); ?>">
                    <i class="la la-money"></i> <span class="hidden-sm hidden-xs"><?php _e('withdrawal', 'dc-woocommerce-multi-vendor'); ?></span> 
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <!-- /.navbar-top-links -->
    </div>
</div>