<?php
/*
$html = "<table width=100% style='border-style:none !important;'>";
$html .= "<tr><td width=200px;><a href='".$product_page_url."'>";
$html .= __('Published Products',WCMp_TEXT_DOMAIN);
$html .= "</a></td><td style='text-align:center;'> ".$publish_products_count;

$html .= "</td></tr>";
$html .= "<tr><td width=200px;><a href='".$product_page_url."'>";
$html .= __('Pending Products',WCMp_TEXT_DOMAIN);
$html .= "</a></td><td style='text-align:center;'> ".$pending_products_count;

$html .= "</td></tr>";
$html .= "<tr><td width=200px;><a href='".$product_page_url."'>";
$html .= __('Trashed Products',WCMp_TEXT_DOMAIN);
$html .= "</a></td><td style='text-align:center;'> ".$trashed_products_count;

$html .= "</td></tr></table>";
echo $html;
*/
$total = '100';
if($total_products == 0){
     $publish_products_percentage = '100';
     $pending_products_percentage = '100';
     $trashed_products_percentage = '100';
}else {
    $publish_products_percentage = number_format((float)$publish_products_count / $total_products * $total, 2, '.', '');
    $pending_products_percentage = number_format((float)$pending_products_count / $total_products * $total, 2, '.', '');
    $trashed_products_percentage = number_format((float)$trashed_products_count / $total_products * $total, 2, '.', '');
}
if($total_products == 0) {
    _e('No products Available.', 'dc-woocommerce-multi-vendor');
} else {
?>

<!--script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script-->
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {

    var data = google.visualization.arrayToDataTable([
      ['Status', 'Percentage'],
      ['<?php printf(__('Publised (%d)', 'dc-woocommerce-multi-vendor'), $publish_products_count); ?>', <?php echo $publish_products_count; ?>],
      ['<?php printf(__('Pending (%d)', 'dc-woocommerce-multi-vendor'), $pending_products_count); ?>', <?php echo $pending_products_count; ?>],
      ['<?php printf(__('Draft (%d)', 'dc-woocommerce-multi-vendor'), $draft_products_count); ?>', <?php echo $draft_products_count; ?>],
      ['<?php printf(__('Not Approved (%d)', 'dc-woocommerce-multi-vendor'), $trashed_products_count); ?>',   <?php echo $trashed_products_count; ?>]
    ]);

    var options = {
        height:'50%',
        colors:['#7fc04c','#fbc343','#bababa','#e67565'], 
        chartArea: {top:10,width:'85%',height:'100%'},
        legend: 'none'
    };

    var chart = new google.visualization.PieChart(document.getElementById('p_stats_piechart'));

    chart.draw(data, options);
  }
</script>
<div class="wcmp_product_stats_wrap">
    <div class="p_stats_chart">
        <div id="p_stats_piechart"></div>
    </div>
    <div class="p_stats_data">
        <ul class="list-group">
            <li class="list-group-item justify-content-between" style="border-left-color:#7fc04c;">
                <p><?php _e('Publised', 'dc-woocommerce-multi-vendor');?></p>
                <span class="badge badge-default badge-pill"><?php echo $publish_products_count; ?></span>
            </li>
            <li class="list-group-item justify-content-between" style="border-left-color:#fbc343;">
                <p><?php _e('Pending', 'dc-woocommerce-multi-vendor');?></p>
                <span class="badge badge-default badge-pill"><?php echo $pending_products_count; ?></span>
            </li>
            <li class="list-group-item justify-content-between" style="border-left-color:#bababa;">
                <p><?php _e('Draft', 'dc-woocommerce-multi-vendor');?></p>
                <span class="badge badge-default badge-pill"><?php echo $draft_products_count; ?></span>
            </li>
            <li class="list-group-item justify-content-between" style="border-left-color:#e67565;">
                <p><?php _e('Not Approved', 'dc-woocommerce-multi-vendor');?></p>
                <span class="badge badge-default badge-pill"><?php echo $trashed_products_count; ?></span>
            </li>
        </ul>
    </div>
</div>

<!--div>
<span><?php _e('Publised Products', 'dc-woocommerce-multi-vendor');?></span>
<svg class="chart" width="90%" height="50">
  <g transform="translate(0,0)">
    <rect width="<?php echo $total;?>%" height="19"></rect>
    <text x="<?php echo $total-3;?>%" y="9.5" dy=".35em"><?php echo $total; ?>%</text>
  </g>
  <g transform="translate(0,20)">
    <rect width="<?php echo $publish_products_percentage;?>%" height="19"></rect><?php
    if($publish_products_count) {?>
    	<text x="<?php echo $publish_products_percentage-3;?>%" y="9.5" dy=".35em"><?php echo $publish_products_percentage; ?>%</text><?php
    }else {
        if($total_products == 0){
               echo '<text x="10%" y="9.5" dy=".35em">100%</text>';
        }else {
            echo '<text x="10%" y="9.5" dy=".35em">0%</text>';
        }
    	
   }?>
    
  </g>
</svg>
<p><?php _e('Publised Products Count', 'dc-woocommerce-multi-vendor'); echo " ".$publish_products_count;?></p>
</div>

<div>
<span><?php _e('Pending Products', 'dc-woocommerce-multi-vendor');?></span>
<svg class="chart" width="90%" height="50">
  <g transform="translate(0,0)">
    <rect width="<?php echo $total;?>%" height="19"></rect>
    <text x="<?php echo $total-3;?>%" y="9.5" dy=".35em"><?php echo $total; ?>%</text>
  </g>
  <g transform="translate(0,20)">
    <rect width="<?php echo $pending_products_percentage;?>%" height="19"></rect><?php
    if($pending_products_count) {?>
    	<text x="<?php echo $pending_products_percentage-3;?>%" y="9.5" dy=".35em"><?php echo $pending_products_percentage; ?>%</text><?php
    } else {
    	if($total_products == 0){
               echo '<text x="10%" y="9.5" dy=".35em">100%</text>';
        }else {
            echo '<text x="10%" y="9.5" dy=".35em">0%</text>';
        }
    }?>
    
  </g>
</svg>
<p><?php _e('Pending Products Count', 'dc-woocommerce-multi-vendor'); echo " ".$pending_products_count;?></p>
</div>

<div>
<span><?php _e('Trashed Products', 'dc-woocommerce-multi-vendor');?></span>
<svg class="chart" width="90%" height="50">
  <g transform="translate(0,0)">
    <rect width="<?php echo $total;?>%" height="19"></rect>
    <text x="<?php echo $total-3;?>%" y="9.5" dy=".35em"><?php echo $total;?>%</text>
  </g>
  <g transform="translate(0,20)">
    <rect width="<?php echo $trashed_products_percentage;?>%" height="19"></rect>
    <?php
    if($trashed_products_count){?>
    	<text x="<?php echo $trashed_products_count-3;?>%" y="9.5" dy=".35em"><?php echo $trashed_products_percentage; ?>%</text><?php
    }else {
    	if($total_products == 0){
               echo '<text x="10%" y="9.5" dy=".35em">100%</text>';
        }else {
            echo '<text x="10%" y="9.5" dy=".35em">0%</text>';
        }
    }?>
      </g>
</svg>
<p><?php _e('Trashed Products Count', 'dc-woocommerce-multi-vendor'); echo " ".$trashed_products_count;?></p>
</div-->
<?php 
}
?>
<!--style type="text/css">
	.chart g:first-child rect {
	  	fill: #1d302c;
	}
	.chart rect {
		fill: green;
	}
	.chart g:first-child text {
	  	fill: white;
	  	font: 10px sans-serif;
	  	text-anchor: end;
	}
	.chart text {
		fill: blue;
	  	font: 10px sans-serif;
	  	text-anchor: end;
	}
</style-->