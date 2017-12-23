<?php
/*
 * The template for displaying vendor pending shipping table dashboard widget
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard-widgets/wcmp_customer_review.php
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
$vendor = get_wcmp_vendor();
if (!$vendor) {
    return;
}
$ratings = wcmp_get_vendor_review_info($vendor->term_id);
?>
<div class="row">
    <div class="col-md-12">
        <?php echo wc_get_rating_html($ratings['avg_rating']); ?>
    </div>
    <div class="col-md-12 wcmp-comments">
        <ul class="media-list">
            <?php foreach ($vendor->get_reviews_and_rating(0, 5) as $comment): ?>
                <li class="media">
                    <div class="media-left pull-left">
                        <a href="#">
                            <?php echo get_avatar($comment->user_id, 50, '', '', array('class' => 'img-circle')); ?>
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?php echo get_user_by('ID', $comment->user_id)->display_name; ?> <small><?php echo human_time_diff(strtotime($comment->comment_date)) . ' ago'; ?></small></h4>
                        <?php echo $comment->comment_content; ?>
                        <p><a data-toggle="modal" data-target="#commient-modal-<?php echo $comment->comment_ID ?>"><small>Reply</small></a></p>
                        <!-- Modal -->
                        <div class="modal fade" id="commient-modal-<?php echo $comment->comment_ID ?>" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Reply to <?php echo get_user_by('ID', $comment->user_id)->display_name; ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <textarea class="form-control" rows="5" id="comment-content-<?php echo $comment->comment_ID; ?>" placeholder="Enter reply..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-comment_id="<?php echo $comment->comment_ID; ?>" data-vendor_id="<?php echo get_current_vendor_id(); ?>" class="btn btn-default wcmp-comment-reply">Comment</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>