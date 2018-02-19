<?php
/**
 * The template for displaying Customer Q & A
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/wcmp-customer-qna-form.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version    3.0.0
 */
global $WCMp, $product;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
do_action('before_wcmp_customer_qna_form');
?>
<div id="wcmp_customer_qna" class="woocommerce-wcmp_customer_qna">
    <div id="cust_qna_form_wrapper">
        <div id="cust_qna_form">
            <h2 id="custqna-title" class="custqna-title"><?php _e('Questions and Answers', 'dc-woocommerce-multi-vendor');?></h2>			
            <div class="qna-ask-wrap">  
                <form action="" method="post" id="customerqnaform" class="customerqna-form" novalidate="">
                    <?php wp_nonce_field( 'wcmp_customer_qna_form_submit', 'cust_qna_nonce' ); ?>
                    <div id="qna-ask-input">
                        <input type="text" name="cust_question" id="cust_question" placeholder="<?php _e('Have a question? Search for answer', 'dc-woocommerce-multi-vendor');?>">
                        <div id="qna-result-msg"></div>
                        <div id="ask-wrap">
                            <label class="no-answer-lbl"><?php echo apply_filters('wcmp_customer_qna_no_answer_label',__("Haven't found any answer you are looking for", 'dc-woocommerce-multi-vendor'));?></label>
                            <button id="ask-qna" class="btn btn-info btn-lg" type="button"><?php _e('Ask', 'dc-woocommerce-multi-vendor');?></button>
                        </div>
                        <input type="hidden" name="product_ID" value="<?php echo $product->get_id(); ?>" id="product_ID">
                        <input type="hidden" name="cust_ID" id="cust_ID" value="<?php echo get_current_user_id(); ?>">
                    </div>
                </form> 
            </div>
            <div id="qna-result-wrap" class="qna-result-wrap">
            <?php if($cust_qna_data){ 
                foreach ($cust_qna_data as $qna) { ?>
                <div class="qna-item-wrap item-<?php echo $qna->ques_ID; ?>">
                    <div class="qna-block">
                        <div class="qtn-row">
                            <p class="qtn-title-text"><?php _e('Question:', 'dc-woocommerce-multi-vendor'); ?></p>
                            <p class="qna-question"><?php echo $qna->ques_details; ?></p>
                        </div>
                        <div class="qtn-row">
                            <p class="qtn-title-text"><?php _e('Answer:', 'dc-woocommerce-multi-vendor'); ?></p>
                            <p class="qna-answer "><?php echo $qna->ans_details; ?></p>
                        </div>
                        <div class="bottom-qna">
                            <ul class="qna-actions">
                                <?php $count = 0;
                                $ans_vote = maybe_unserialize($qna->ans_vote);
                                if(is_array($ans_vote)){
                                    $count = array_sum($ans_vote);
                                }
                                if(is_user_logged_in()){ ?>
                                <li class="vote">
                                    <a href="" title="<?php _e('Give a thumbs up', 'dc-woocommerce-multi-vendor');?>" class="give-vote-btn give-up-vote" data-vote="up" data-ans="<?php echo $qna->ans_ID; ?>">&#9650;</a>
                                    <span class="vote-count"><?php echo $count; ?></span>
                                    <a href="" title="<?php _e('Give a thumbs down', 'dc-woocommerce-multi-vendor');?>" class="give-vote-btn give-down-vote" data-vote="down" data-ans="<?php echo $qna->ans_ID; ?>">&#9660;</a>
                                </li>
                                <?php }else{ ?>
                                <li class="vote">&#9650;<span class="vote-count"><?php echo $count; ?></span>&#9660;</li>
                                <?php } ?>
                                <li class="qna-user"><?php echo get_userdata($qna->ans_by)->display_name; ?></li>
                                <li class="qna-date"><?php echo date("F j, Y, g:i a", strtotime($qna->ans_created));?></li> 
                            </ul>
                        </div>
                    </div>
                </div>
            <?php }
            }
            ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php do_action('after_wcmp_customer_qna_form');