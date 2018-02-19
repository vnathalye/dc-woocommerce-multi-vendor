<?php
/**
 * The template for displaying vendor transaction details
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-transaction_detail.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $WCMp;
?>
<div class="col-md-12">
    <!--blockquote class="panel-info">
        <div class = "wcmp_mixed_txt some_line"> <span><?php _e(' Showing stats and reports from :', 'dc-woocommerce-multi-vendor');
        ?> </span><b><span id="display_trans_from_dt"></span>&nbsp; <?php _e('to', 'dc-woocommerce-multi-vendor');?> &nbsp;<span id="display_trans_to_dt"></span></b>
        </div>
    </blockquote-->
    <!--div class="panel panel-default">
        <div class="wcmp_form1 ">
            <h3 class="panel-heading"><?php _e('Select Date Range', 'dc-woocommerce-multi-vendor'); ?></h3>
            <div class="panel-body">
                <div id="vendor_transactions_date_filter" class="row">
                    <div class="col-sm-5">
                        <input id="wcmp_from_date" class="form-control" name="from_date" class="pickdate gap1" placeholder="From" value ="<?php echo date('01-m-Y'); ?>"/>
                    </div>
                    <div class="col-sm-5">
                        <input id="wcmp_to_date" class="form-control" name="to_date" class="pickdate" placeholder="To" value ="<?php echo date('t-m-Y'); ?>"/>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" name="order_export_submit" id="do_filter"  class="btn btn-default" ><?php _e('Show', 'dc-woocommerce-multi-vendor') ?></button>
                    </div>
                </div>              
            </div>
        </div>
    </div-->
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="vendor_transactions_date_filter" class="form-inline datatable-date-filder">
                <div class="form-group">
                    <span class="date-inp-wrap">
                        <input id="wcmp_from_date" class="form-control" name="from_date" class="pickdate gap1" placeholder="From" value ="<?php echo date('01-m-Y'); ?>"/>
                    </span>
                </div>
                <div class="form-group">
                    <span class="date-inp-wrap">
                        <input id="wcmp_to_date" class="form-control" name="to_date" class="pickdate" placeholder="To" value ="<?php echo   date('t-m-Y'); ?>"/>
                    </span>
                </div>
                <button type="button" name="order_export_submit" id="do_filter"  class="btn btn-default" ><?php _e('Show', 'dc-woocommerce-multi-vendor') ?></button>
            </div>  
            <form method="post" name="export_transaction">
                <div class="wcmp_table_holder">
                    <table id="vendor_transactions" class="get_wcmp_transactions table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center"><input class="select_all_transaction" type="checkbox" onchange="toggleAllCheckBox(this, 'vendor_transactions');"></th>
                                <th><?php _e('Date', 'dc-woocommerce-multi-vendor'); ?></th>
                                <th><?php _e('Transc.ID', 'dc-woocommerce-multi-vendor'); ?></td>
                                <th><?php _e('Commission IDs', 'dc-woocommerce-multi-vendor'); ?></th>
                                <th><?php _e('Fee', 'dc-woocommerce-multi-vendor'); ?></th>
                                <th><?php _e('Net Earnings', 'dc-woocommerce-multi-vendor'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
                <div id="export_transaction_wrap" class="wcmp-action-container wcmp_table_loader" style="display: none;">
                    <input type="hidden" id="export_transaction_start_date" name="from_date" value="<?php echo date('01-m-Y'); ?>" />
                    <input id="export_transaction_end_date" type="hidden" name="to_date" value="<?php echo date('t-m-Y'); ?>" />
                    <button type="submit" name="export_transaction" class="btn btn-default"><?php _e('Download CSV', 'dc-woocommerce-multi-vendor'); ?></button>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>  
</div>
<script>
jQuery(document).ready(function($) {
    $( "#wcmp_from_date" ).datepicker({ 
        dateFormat: 'dd-mm-yy',
        onClose: function (selectedDate) {
            $("#wcmp_to_date").datepicker("option", "minDate", selectedDate);
        }
    });
    $( "#wcmp_to_date" ).datepicker({ 
        dateFormat: 'dd-mm-yy',
        onClose: function (selectedDate) {
            $("#wcmp_from_date").datepicker("option", "maxDate", selectedDate);
        }
    });
    var vendor_transactions;
    vendor_transactions = $('#vendor_transactions').DataTable({
        ordering  : false,
        searching  : false,
        processing: true,
        serverSide: true,
        language: {
            "emptyTable": "<?php echo __('Sorry. No transactions are available.','dc-woocommerce-multi-vendor'); ?>",
            "processing": "<?php echo __('Processing...', 'dc-woocommerce-multi-vendor'); ?>"
        },
        initComplete: function (settings, json) {
            var info = this.api().page.info();
            if (info.recordsTotal > 0) {
                $('#export_transaction_wrap').show();
            }
            $('#display_trans_from_dt').text($('#wcmp_from_date').val());
            $('#export_transaction_start_date').val($('#wcmp_from_date').val());
            $('#display_trans_to_dt').text($('#wcmp_to_date').val());
            $('#export_transaction_end_date').val($('#wcmp_to_date').val());
        },
        drawCallback: function () {
            $('table.dataTable tr [type="checkbox"]').each(function(){
                if($(this).parent().is('span.checkbox-holder')) return;
                $(this).wrap('<span class="checkbox-holder"></span>').after('<i class="wcmp-font ico-uncheckbox-icon"></i>');
            })
        },
        ajax:{
            url : woocommerce_params.ajax_url+'?action=wcmp_vendor_transactions_list', 
            type: "post",
            data: function (data) {
                data.from_date = $('#wcmp_from_date').val();
                data.to_date = $('#wcmp_to_date').val();
            }
        },
        columns: [
            { data: "select_transaction", className: "text-center" },
            { data: "date" },
            { data: "transaction_id" },
            { data: "commission_ids" },
            { data: "fees" },
            { data: "net_earning" }
        ]
    });
    $(document).on('click', '#vendor_transactions_date_filter #do_filter', function () {
        $('#display_trans_from_dt').text($('#wcmp_from_date').val());
        $('#export_transaction_start_date').val($('#wcmp_from_date').val());
        $('#display_trans_to_dt').text($('#wcmp_to_date').val());
        $('#export_transaction_end_date').val($('#wcmp_to_date').val());
        vendor_transactions.ajax.reload();
    });
});
</script>