<?php

/*
 * The template for displaying vendor stats reports dashboard widget
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard-widgets/wcmp_vendor_stats_reports.php
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
$t_sale_report = '';
if($vendor_current_stats['total_sales'] > $vendor_previous_stats['total_sales']){
    $t_sale_report = 'up'; 
}else{
    $t_sale_report = 'down';   
}
$t_earning_report = '';
if($vendor_current_stats['total_earning'] && $vendor_previous_stats['total_earning']){
    $t_earning_report = (int)$vendor_current_stats['total_earning'] - (int)$vendor_previous_stats['total_earning']; 
}

do_action('before_wcmp_vendor_stats_reports');
?>
<div class="row stat-panel">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="panel">
            <div class="panel-body">
                <h4><?php echo __('Sales', 'dc-woocommerce-multi-vendor'); ?></h4>
                <div class="stat-counter">
                    <?php echo wc_price($vendor_current_stats['total_sales'],array('decimals'=> 0)); ?>
                    <?php if($vendor_current_stats['total_sales'] != $vendor_previous_stats['total_sales']): ?>
                        <div class="wcmp_stat-status wcmp-stat-<?php echo $t_sale_report; ?>"></div>
                    <?php endif; ?>
                </div>
                <p><!--Nulla cursus tortor--></p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="panel">
            <div class="panel-body">
                <h4><?php echo __('Earning', 'dc-woocommerce-multi-vendor'); ?></h4>
                <div class="stat-counter"><?php echo wc_price($vendor_current_stats['total_earning'],array('decimals'=> 0)); ?></div>
                <?php if($t_earning_report != 0): ?><sub class="<?php if($t_earning_report > 0) echo 'positive' ;else echo 'negetive'; ?>-amount"><?php if($t_earning_report > 0) echo '+' ;else echo ''; ?><?php echo wc_price($t_earning_report,array('decimals'=> 0)); ?></sub><?php endif; ?> <!-- if its negetive add negetive-amount to sub; otherwise don't add the class -->
                <p><!--Nulla cursus tortor--></p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="panel">
            <div class="panel-body">
                <h4><?php echo __('Balance', 'dc-woocommerce-multi-vendor'); ?></h4>
                <div class="stat-counter"><?php echo wc_price($vendor_current_stats['total_balance'],array('decimals'=> 0)); ?></div>
                <p><!--Nulla cursus tortor--></p>
            </div>
        </div>
    </div>
</div>
<?php
do_action('after_wcmp_vendor_stats_reports');
