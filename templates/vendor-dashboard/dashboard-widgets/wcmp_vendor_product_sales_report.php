<?php
?>
<table class="table table-striped product_sold_last_week" id="product_sold_last_week_id">
  	<thead>
	    <tr>
	      <th><span class="dashicons dashicons-format-image"></span></th>
	      <th><?php _e('Product Title',WCMp_TEXT_DOMAIN);?></th>
	      <th><?php _e('Total Product Sale',WCMp_TEXT_DOMAIN);?></th>
	    </tr>
	</thead>
	<tbody align="center"><?php
            if($sold_product_list_sorted) {
		foreach ($sold_product_list_sorted as $key => $value) {
                    echo "<tr>";
                    if($value['exists'] == '0'){
                        echo "<td colspan='2'>".$value['name']." (".__('This product does not exists','dc-woocommerce-multi-vendor').")</td>";
                    } else {    
                        echo "<td>".$value['image']."</td>";
                        echo "<td class='product_sold_last_week_name_class'><a href='".$value['permalink']."'>".$value['name']."</a></td>";
                    }
                    echo "<td>".$value['qty']."</td>";
                    echo "</tr>";
		}
            } else {
                echo "<tr><td colspan='3'><p class='wcmp_no-data'>".__('Not enough data.','dc-woocommerce-multi-vendor')."</p></td></tr>";
            }
?>
	</tbody>
</table>