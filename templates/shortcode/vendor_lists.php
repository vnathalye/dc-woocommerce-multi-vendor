<?php
/**
 * The template for displaying vendor lists
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_lists.php
 *
 * @author 		WC Marketplace
 * @package 	WCMm/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCMp;
?>
<div class="vendor_list woocommerce">
	<form name="vendor_sort" method="get">
		<div class="vendor_sort">
			<select class="select short" id="vendor_sort_type" name="vendor_sort_type">
				<option value="registered" <?php if( $sort_type == 'registered'){ echo 'selected="selected"'; } ?> ><?php echo __('By date','dc-woocommerce-multi-vendor');?></option>
				<option value="name" <?php if( $sort_type == 'name'){ echo 'selected="selected"'; } ?> ><?php echo __('By Alphabetically','dc-woocommerce-multi-vendor');?></option>
				<option value="category" <?php if( $sort_type == 'category'){ echo 'selected="selected"'; } ?> ><?php echo __('By Category','dc-woocommerce-multi-vendor');?></option>
			</select>
			<?php 
                        $product_category = get_terms('product_cat');
                        $options_html = '';
                        foreach ($product_category as $category){
                            if($category->term_id == $selected_category){
                                $options_html .= '<option value="'.esc_attr($category->term_id).'" selected="selected">'.esc_html($category->name).'</option>';
                            } else{
                                $options_html .= '<option value="'.esc_attr($category->term_id).'">'.esc_html($category->name).'</option>';
                            }
                        }
                        ?>
                        <select name="vendor_sort_category" id="vendor_sort_category" class="select"><?php echo $options_html; ?></select>					
			<input value="<?php echo __('Sort','dc-woocommerce-multi-vendor');?>" type="submit">
		</div>
	</form>
	<?php if(isset($vendor_info) && is_array($vendor_info) && !empty($vendor_info)) {
		foreach( $vendor_info as $vendor ) {
		?>
	<div class="sorted_vendors" style="display:inline-block; margin-right:10%;">
		<center>
			<?php do_action( 'wcmp_vendor_lists_single_before_image', $vendor['term_id'], $vendor['ID'] ); ?>
			<a href="<?php echo $vendor['vendor_permalink']; ?>">
				<img class="vendor_img" src="<?php echo $vendor['vendor_image']; ?>" id="vendor_image_display" width="125">
			</a>
			<br>
			<?php
				$rating_info = wcmp_get_vendor_review_info($vendor['term_id']);
				$WCMp->template->get_template( 'review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
			?>
			<?php do_action( 'wcmp_vendor_lists_single_after_image', $vendor['term_id'], $vendor['ID'] ); ?>
			<?php $button_text = apply_filters('wcmp_vendor_lists_single_button_text',$vendor['vendor_name']); ?>
			<a href="<?php echo $vendor['vendor_permalink']; ?>" class="button"><?php echo $button_text; ?></a>
			<br>
			<br>
			<?php do_action( 'wcmp_vendor_lists_single_after_button', $vendor['term_id'], $vendor['ID'] ); ?>
		</center>
	</div>
	<?php }
	}
	?>
</div>