<?php

/*
 * The template for displaying vendor pending shipping table dashboard widget
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard-widgets/wcmp_vendor_pending_shipping.php
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
do_action('before_wcmp_vendor_pending_shipping');
?>
<table class="table">
    <thead>
        <tr>
            <?php $default_headers = apply_filters('wcmp_vendor_pending_shipping_table_header', array(
                'order_id' => __('Order ID', 'dc-woocommerce-multi-vendor'),
                'products_name' => __('Product Name', 'dc-woocommerce-multi-vendor'),
                'order_date' => __('Order Date', 'dc-woocommerce-multi-vendor'),
                //'dimentions' => __('L/B/H/W', 'dc-woocommerce-multi-vendor'),
                'shipping_address' => __('Address', 'dc-woocommerce-multi-vendor'),
                'shipping_amount' => __('Charges', 'dc-woocommerce-multi-vendor'),
                'action' => __('Action', 'dc-woocommerce-multi-vendor'),
            ));
            foreach ($default_headers as $key => $value) {
                echo '<th>'.$value.'</th>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
    <?php 
    if($pending_shippings){
        foreach ($pending_shippings as $row_key => $row) { 
            echo '<tr>';
            foreach ($row as $key => $value) { 
                echo '<td>';
                switch ($key) {
                    case 'order_id': 
                        echo '<a href="'.esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $value)).'">#'.$value.'</a>';
                        break;
                    case 'products_name': 
                        echo implode(' , ', $value);
                        break;
                    case 'order_date':
                        echo wc_string_to_datetime($value)->date(wc_date_format());
                        break;
//                    case 'dimentions':
//                        echo '('.implode(' ) , ( ', $value).' )';
//                        break;
                    case 'shipping_address':
                        echo $value;
                        break;
                    case 'shipping_amount':
                        echo wc_price($value);
                        break;
                    case 'action':
                        echo $value;
                        break;
                }
                echo '</td>';
                do_action('wcmp_vendor_pending_shipping_table_row', $key, $value);
            }
            echo '</tr>';
        }
    }else{
        echo '<td colspan="6" align="center">'.__('You have no pending shipping!', 'dc-woocommerce-multi-vendor').'</td>';
    }
    ?>
    </tbody>
</table>
 <!-- Modal -->
<div id="marke-as-ship-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php _e('Shipment Tracking Details', 'dc-woocommerce-multi-vendor'); ?></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="tracking_url"><?php _e('Enter Tracking Url', 'dc-woocommerce-multi-vendor'); ?> *</label>
                        <input type="url" class="form-control" id="email" name="tracking_url" required="">
                    </div>
                    <div class="form-group">
                        <label for="tracking_id"><?php _e('Enter Tracking ID', 'dc-woocommerce-multi-vendor'); ?> *</label>
                        <input type="text" class="form-control" id="pwd" name="tracking_id" required="">
                    </div>
                </div>
                <input type="hidden" name="order_id" id="wcmp-marke-ship-order-id" />
                <?php if (isset($_POST['wcmp_start_date_order'])) : ?>
                    <input type="hidden" name="wcmp_start_date_order" value="<?php echo $_POST['wcmp_start_date_order']; ?>" />
                <?php endif; ?>
                <?php if (isset($_POST['wcmp_end_date_order'])) : ?>
                    <input type="hidden" name="wcmp_end_date_order" value="<?php echo $_POST['wcmp_end_date_order']; ?>" />
                <?php endif; ?>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="wcmp-submit-mark-as-ship"><?php _e('Submit', 'dc-woocommerce-multi-vendor'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function wcmpMarkeAsShip(self, order_id) {
        jQuery('#wcmp-marke-ship-order-id').val(order_id);
        jQuery('#marke-as-ship-modal').modal('show');
    }
</script>
<?php 
do_action('after_wcmp_vendor_pending_shipping');