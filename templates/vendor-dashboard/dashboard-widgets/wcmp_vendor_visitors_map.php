<?php

/*
 * The template for displaying visitors map dashboard widget
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard-widgets/wcmp_vendor_visitors_map.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   3.0.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMp;
do_action('before_wcmp_vendor_visitors_map');
?>
<div class="panel-body">
    <div class="row no-margin" data-sync-height>
        <div class="col-sm-5 col-md-4 no-padding">
            <div id="donutchart" style="width: 100%;"></div>
        </div>
        <div class="col-sm-7 col-md-8 no-padding">
            <div id="vmap" style="height: 270px;"></div>
        </div>
    </div>
</div>
<?php 
do_action('after_wcmp_vendor_visitors_map');