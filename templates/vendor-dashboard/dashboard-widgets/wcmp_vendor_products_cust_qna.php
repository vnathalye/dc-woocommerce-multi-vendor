<?php

/*
 * The template for displaying customer active questiona dashboard widget
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard-widgets/wcmp_vendor_products_cust_qna.php
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

do_action('before_wcmp_vendor_dashboard_products_cust_qna');
?>
<div class="customer-questions-panel">
    <?php if($active_qna){
        foreach ($active_qna as $key => $data) { 
            $product = wc_get_product($data->product_ID);
    ?>
    <article id="reply-item-<?php echo $key; ?>" class="reply-item">
        <div class="media">
            <div class="media-left">
                <?php echo $product->get_image(); ?>
            </div>
            <div class="media-body">
                <h4 class="media-heading qna-question">
                    <?php echo $data->ques_details; ?>
                </h4>
                <time class="qna-date">
                    <i class="la la-clock-o"></i> 
                    <span><?php echo date("F j, Y, g:i a", strtotime($data->ques_created)); ?></span>
                </time>
                <p><a data-toggle="modal" data-target="#qna-reply-modal-<?php echo $key; ?>" ><small><?php _e('Reply', 'dc-woocommerce-multi-vendor')?></small></a></p>
                <!-- Modal -->
                <div class="modal fade" id="qna-reply-modal-<?php echo $key; ?>" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><?php _e('Product - ', 'dc-woocommerce-multi-vendor')?> <?php echo $product->get_formatted_name(); ?></h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="qna-question"><?php echo $data->ques_details; ?></label>
                                    <textarea class="form-control" rows="5" id="qna-reply-<?php echo $key; ?>" placeholder="<?php _e('Post your answer...', 'dc-woocommerce-multi-vendor')?>"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" data-key="<?php echo $key; ?>" class="btn btn-default wcmp-add-qna-reply"><?php _e('Add', 'dc-woocommerce-multi-vendor')?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </article>
    <?php }
    }else{
        echo '<article class="reply-item"><div class="col-md-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center;">'.__('No customer query found.', 'dc-woocommerce-multi-vendor').'</div></article>';
    }
    ?>
</div>
<?php
do_action('after_wcmp_vendor_dashboard_products_cust_qna');
