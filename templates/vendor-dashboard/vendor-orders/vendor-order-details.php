<?php
/**
 * The template for displaying vendor order detail and called from vendor_order_item.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-orders/vendor-order-details.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly    
    exit;
}
global $woocommerce, $WCMp;
$vendor = get_current_vendor();
$order = wc_get_order($order_id);
$vendor_items = get_wcmp_vendor_orders(array('order_id' => $order->get_id(), 'vendor_id' => $vendor->id));
$vendor_order_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id(), 'vendor_id' => $vendor->id));
//print_r($vendor_order_amount);die;
$subtotal = 0;
if ($vendor && $order_id && false) {
    $vendor_items = $vendor->get_vendor_items_from_order($order_id, $vendor->term_id);
    $order = new WC_Order($order_id);
    if ($order && sizeof($order->get_items()) > 0) {
        ?>
        <!--        <h2><?php _e('Order Details', 'dc-woocommerce-multi-vendor'); ?></h2>-->
        <table class="customer_order_dtl"> 
            <tbody>
            <th><label for="product_name"><?php _e('Product Title', 'dc-woocommerce-multi-vendor') ?></label></th>
            <th><label for="product_qty"><?php _e('Product Quantity', 'dc-woocommerce-multi-vendor') ?></label></th>
            <th><label for="product_total"><?php _e('Line Subtotal', 'dc-woocommerce-multi-vendor') ?></label></th>
            <?php if (in_array($order->get_status(), array('processing', 'completed')) && ( $purchase_note = get_post_meta($order_id, '_purchase_note', true) )) { ?>
                <th><label for="product_note"><?php _e('Purchase Note', 'dc-woocommerce-multi-vendor') ?></label></th>
            <?php } ?>
            <?php
            if (sizeof($order->get_items()) > 0) {
                foreach ($vendor_items as $item) {
                    $_product = apply_filters('dc_woocommerce_order_item_product', $order->get_product_from_item($item), $item);
                    ?>
                    <tr class="">

                        <td class="product-name">
                            <?php
                            if ($_product && !$_product->is_visible())
                                echo apply_filters('wcmp_order_item_name', $item['name'], $item);
                            else
                                echo apply_filters('wcmp_order_item_name', sprintf('<a href="%s">%s</a>', get_permalink($item['product_id']), $item['name']), $item);
                            wc_display_item_meta($item);
                            ?>
                        </td>
                        <td>	
                            <?php
                            echo $item['qty'];
                            ?>
                        </td>
                        <td>
                            <?php echo $order->get_formatted_line_subtotal($item); ?>
                        </td>
                        <?php
                        if (in_array($order->get_status(), array('processing', 'completed')) && ( $purchase_note = get_post_meta($_product->get_id(), '_purchase_note', true) )) {
                            ?>
                            <td colspan="3"><?php echo apply_filters('the_content', $purchase_note); ?></td>
                            <?php
                        }
                    }
                }
                ?>
            </tr>
        </tbody>
        </table>
        <?php
        $coupons = $order->get_used_coupons();
        if (!empty($coupons)) {
            ?>
            <div class="wcmp_headding2"><?php _e('Coupon Used :', 'dc-woocommerce-multi-vendor'); ?></div>
            <table class="coupon_used"> 
                <tbody>
                    <tr>
                        <?php
                        $coupon_used = false;
                        foreach ($coupons as $coupon_code) {
                            $coupon = new WC_Coupon($coupon_code);
                            $coupon_post = get_post($coupon->get_id());
                            $author_id = $coupon_post->post_author;
                            if (get_current_vendor_id() == $author_id) {
                                $coupon_used = true;
                                echo '<td>"' . $coupon_code . '"</td>';
                            }
                        }
                        if (!$coupon_used)
                            echo '<td>' . __("Sorry No Coupons of yours is used.", 'dc-woocommerce-multi-vendor') . '</td>'
                            ?>
                    </tr>
                </tbody>
            </table>
            <?php
        }
        ?>
        <?php $customer_note = $order->get_customer_note();
        ?>
        <div class="wcmp_headding2"><?php _e('Customer Note', 'dc-woocommerce-multi-vendor'); ?></div>
        <p class="wcmp_headding3">
            <?php echo $customer_note ? $customer_note : __('No customer note.', 'dc-woocommerce-multi-vendor'); ?>
        </p>

        <?php
        if (apply_filters('is_vendor_view_comment_field', true)) {
            $vendor_comments = $order->get_customer_order_notes();
            if ($vendor_comments) {
                ?>
                <div class="wcmp_headding2"><?php _e('Comments', 'dc-woocommerce-multi-vendor'); ?></div>
                <div class="wcmp_headding3">
                    <?php
                    foreach ($vendor_comments as $comment) {
                        $comment_vendor = get_comment_meta($comment->comment_ID, '_vendor_id', true);
                        if ($comment_vendor && $comment_vendor != $vendor->id) {
                            continue;
                        }
                        $last_added = human_time_diff(strtotime($comment->comment_date_gmt), current_time('timestamp', 1));
                        ?>
                        <p>
                            <?php printf(__('Added %s ago', 'dc-woocommerce-multi-vendor'), $last_added); ?>
                            </br>
                            <?php echo $comment->comment_content; ?>
                        </p>
                    <?php } ?>
                </div>
                <?php
            }
        }
        ?>

        <?php
        if (apply_filters('is_vendor_submit_comment_field', true)) {
            ?>
            <div class="wcmp_headding2"><?php _e('Add Comment', 'dc-woocommerce-multi-vendor'); ?></div>
            <form method="post" name="add_comment" id="add-comment_<?php echo $order_id; ?>">
                <?php wp_nonce_field('dc-add-comment'); ?>
                <textarea name="comment_text" style="width:97%; margin-bottom: 10px;"></textarea>
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input class="btn btn-large btn-block" type="submit" name="wcmp_submit_comment" value="<?php _e('Add comment', 'dc-woocommerce-multi-vendor'); ?>">
            </form>
        <?php } ?>

        <?php if (!apply_filters('hide_customer_dtl_field', false)) { ?>

            <div class="wcmp_headding2"><?php _e('Customer Details', 'dc-woocommerce-multi-vendor'); ?></div>
            <div class="wcmp_headding3">
                <dl class="customer_details">
                    <?php
                    $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                    echo '<dt>' . __('Name:', 'dc-woocommerce-multi-vendor') . '</dt><dd>' . $name . '</dd>';
                    if ($order->get_billing_email())
                        echo '<dt>' . __('Email:', 'dc-woocommerce-multi-vendor') . '</dt><dd>' . $order->get_billing_email() . '</dd>';
                    if ($order->get_billing_phone())
                        echo '<dt>' . __('Telephone:', 'dc-woocommerce-multi-vendor') . '</dt><dd>' . $order->get_billing_phone() . '</dd>';

                    // Additional customer details hook
                    do_action('wcmp_order_details_after_customer_details', $order);
                    ?>
                </dl>
            </div>
        <?php } ?>


        <?php if (apply_filters('show_customer_billing_field', true)) { ?>
            <div class="col-1">
                <div class="wcmp_headding2"><?php _e('Billing Address', 'dc-woocommerce-multi-vendor'); ?></div>
                <div class="wcmp_headding3">
                    <address><p>
                            <?php
                            if (!$order->get_formatted_billing_address())
                                _e('N/A', 'dc-woocommerce-multi-vendor');
                            else
                                echo $order->get_formatted_billing_address();
                            ?>
                        </p></address>
                </div>
            </div><!-- /.col-1 -->
            <?php
        }
        if (apply_filters('show_customer_shipping_field', true)) {
            ?>
            <?php if (!wc_ship_to_billing_address_only() && get_option('woocommerce_calc_shipping') !== 'no') { ?>
                <div class="col-2">
                    <div class="wcmp_headding2"><?php _e('Shipping Address', 'dc-woocommerce-multi-vendor'); ?></div>
                    <div class="wcmp_headding3">
                        <address><p>
                                <?php
                                if (!$order->get_formatted_shipping_address())
                                    _e('N/A', 'dc-woocommerce-multi-vendor');
                                else
                                    echo $order->get_formatted_shipping_address();
                                ?>
                            </p></address>
                    </div>

                </div><!-- /.col-2 -->
                <?php
            }
        }
    } else {
        echo __('<div class="wcmp_headding3">No such order found</div>', 'dc-woocommerce-multi-vendor');
    }
}
?>





<div class="col-md-12">
    <header class="text-center">
        <h1>Order #<?php echo $order->get_id(); ?></h1>
    </header>
    <div class="text-center">Order #<?php echo $order->get_id(); ?> was placed on <?php echo $order->get_date_created()->date(wc_date_format()); ?> and is currently <?php echo ucfirst($order->get_status()); ?>.</div>
    <h2 class="text-center"><?php _e('Order Details', 'dc-woocommerce-multi-vendor'); ?></h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?php _e('Product', 'dc-woocommerce-multi-vendor'); ?></th>
                <th><?php _e('Total', 'dc-woocommerce-multi-vendor'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendor_items as $item): $product = wc_get_product($item->product_id); $subtotal += $product->get_price(''); ?>
                <tr>
                    <td><?php echo $product->get_title(); ?> × <?php echo $item->quantity; ?></td>
                    <td><?php echo wc_price($product->get_price()); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td><?php _e('Commission:', 'dc-woocommerce-multi-vendor'); ?></td>
                <td><?php echo wc_price($vendor_order_amount['commission_amount']); ?></td>
            </tr>
            <tr>
                <td><?php _e('Shipping:', 'dc-woocommerce-multi-vendor'); ?></td>
                <td><?php echo wc_price($vendor_order_amount['shipping_amount']); ?> via <?php echo $order->get_shipping_method(); ?></td>
            </tr>
            <tr>
                <td><?php _e('All Tax:', 'dc-woocommerce-multi-vendor'); ?></td>
                <td><?php echo wc_price($vendor_order_amount['tax_amount'] + $vendor_order_amount['shipping_tax_amount']); ?></td>
            </tr>
            <tr>
                <td><?php _e('Payment method:', 'dc-woocommerce-multi-vendor'); ?></td>
                <td><?php echo $order->get_payment_method_title(); ?></td>
            </tr>
            <tr>
                <td><?php _e('Total Earning:', 'dc-woocommerce-multi-vendor'); ?></td>
                <td><?php echo wc_price($vendor_order_amount['total']); ?></td>
            </tr>
            <tr>
                <td><?php _e('Customer Note:', 'dc-woocommerce-multi-vendor'); ?></td>
                <td><?php echo $order->get_customer_note(); ?></td>
            </tr>
        </tfoot>
    </table>
    <div class="row">
        <div class="col-md-12">
            <h3><?php _e('Order notes :', 'dc-woocommerce-multi-vendor'); ?><span class="add-note-wrapper pull-right"><a data-toggle="modal" data-target="#add-order-note-modal" ><?php _e('Add Note', 'dc-woocommerce-multi-vendor'); ?></a></span></h3>
            <ul class="list-group">
            <?php
            if (apply_filters('is_vendor_view_comment_field', true)) {
                $vendor_comments = $order->get_customer_order_notes();
                if ($vendor_comments) {
                    foreach ($vendor_comments as $comment) {
                        $comment_vendor = get_comment_meta($comment->comment_ID, '_vendor_id', true);
                        if ($comment_vendor && $comment_vendor != $vendor->id) { continue; }
                        $last_added = human_time_diff(strtotime($comment->comment_date), current_time('timestamp', 1)); ?>
                        <li class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="pull-right">
                                <small class="text-muted"><?php printf(__('Added %s ago', 'dc-woocommerce-multi-vendor'), $last_added); ?></small>
                            </div>
                            <p><?php echo $comment->comment_content; ?></p>
                        </li>
                    <?php }
                } 
            } ?>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="add-order-note-modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php _e('Add Order Note', 'dc-woocommerce-multi-vendor')?> </h4>
                    </div>
                    <form method="post" name="add_comment">
                        <div class="modal-body">
                            <?php wp_nonce_field('dc-vendor-add-order-comment','vendor_add_order_nonce'); ?>
                            <div class="form-group">
                                <textarea class="form-control" name="comment_text"></textarea>
                                <input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input class="btn btn-default wcmp-add-order-note" type="submit" name="wcmp_submit_comment" value="<?php _e('Add', 'dc-woocommerce-multi-vendor'); ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-center"><?php _e('Billing address', 'dc-woocommerce-multi-vendor'); ?></h2>
            <address class="text-center">
                <?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __('N/A', 'dc-woocommerce-multi-vendor'); ?>
                <?php if ($order->get_billing_phone()) : ?>
                    <p class="woocommerce-customer-details--phone"><?php echo esc_html($order->get_billing_phone()); ?></p>
                <?php endif; ?>
                <?php if ($order->get_billing_email()) : ?>
                    <p class="woocommerce-customer-details--email"><?php echo esc_html($order->get_billing_email()); ?></p>
                <?php endif; ?>
            </address>
        </div>
        <div class="col-md-6">
            <h2 class="text-center"><?php _e('Shipping address', 'dc-woocommerce-multi-vendor'); ?></h2>
            <address class="text-center">
                <?php echo ( $address = $order->get_formatted_shipping_address() ) ? $address : __('N/A', 'dc-woocommerce-multi-vendor'); ?>
            </address>
        </div>
    </div>
</div>