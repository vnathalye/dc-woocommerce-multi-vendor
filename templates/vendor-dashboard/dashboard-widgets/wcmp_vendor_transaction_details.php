<div class="transaction-details">
	<h4><?php _e('Total Balance', 'dc-woocommerce-multi-vendor');?></h4>
	<span class="wcmp_dashboard_widget_total_transaction"><?php echo wc_price($total_amount);  ?></span>
	<ul class="transaction-list">
	<?php 
		foreach ($transaction_display_array as $key => $value) {
                    //print_r($value);
                    
			echo "<li><b>".$value['transaction_date']."</b><p>#".$key."</p><span class='pull-right'>".wc_price($value['total_amount'])."</span></li>";	
		}?>
	</ul>
</div>
