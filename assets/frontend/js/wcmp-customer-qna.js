/* global product_qna */

(function ($) {
    var block = function( $node ) {
        if ( ! is_blocked( $node ) ) {
            $node.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
        }
    };
    var is_blocked = function( $node ) {
        return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
    };

    var unblock = function( $node ) {
        $node.removeClass( 'processing' ).unblock();
    };
    
    var keyup_timeout;
    $('#cust_question').on('keyup', function () {
        var this_ele_val = $(this).val();
   	clearTimeout(keyup_timeout);
   	keyup_timeout = setTimeout(function(){
            $('#qna-result-msg').html('');
            block( $('#cust_qna_form') );
            if(this_ele_val.length > 3){
                var data = {
                    action: 'wcmp_customer_ask_qna_handler',
                    handler: 'search',
                    product_ID: $('#product_ID').val(),
                    keyword: this_ele_val
                };
                $.post(woocommerce_params.ajax_url, data, function (response) {
                    unblock($('#cust_qna_form') );
                    if (response.no_data == 1) {
                        $('#qna-result-msg').html(response.message);
                        $('#qna-result-wrap').html('');
                        $('#ask-wrap').show();
                    }else{
                        $('#qna-result-wrap').html(response.data);
                    }
                });
            }else{
                $('#ask-wrap').hide();
                var data = {
                    action: 'wcmp_customer_ask_qna_handler',
                    handler: 'search',
                    product_ID: $('#product_ID').val(),
                    keyword: ''
                };
                $.post(woocommerce_params.ajax_url, data, function (response) {
                    unblock($('#cust_qna_form') );
                    if (response.no_data == 1) {
                        $('#qna-result-msg').html(response.message);
                        $('#qna-result-wrap').html('');
                        $('#ask-wrap').show();
                    }else{
                        $('#qna-result-wrap').html(response.data);
                    }
                });
            }
        }, 500);
    });
   
    $('body').on('click', '#ask-qna', function () {
        $('#qna-result-msg').html('');
        block( $('#cust_qna_form') );
        var data = {
            action: 'wcmp_customer_ask_qna_handler',
            handler: 'submit',
            customer_qna_data: $('#customerqnaform').serialize()
        };
        $.post(woocommerce_params.ajax_url, data, function (response) {
            if (response.no_data == 0) {
                unblock($('#cust_qna_form') );
                setTimeout(function(){
                    $('#ask-wrap').hide();
                    $('#cust_question').val('');
                    $('#qna-result-msg').html(response.message);
                    window.location.reload();
                },3000);
            }
        });
    });
    
    $('body').on('click', 'button.wcmp-add-qna-reply', function () {
        var key = $(this).attr('data-key');
        var reply = $('#qna-reply-'+key).val();
        if (reply === '') {
            return false;
        }
        var data = {
            action: 'wcmp_customer_ask_qna_handler',
            handler: 'answer',
            reply: reply,
            key: key
        };
        $.post(woocommerce_params.ajax_url, data, function (response) {
            if (response.no_data == 0) {
                $('#reply-item-'+key).hide();
                if(response.remain_data == 0){
                    $('.customer-questions-panel').html('');
                    $('.customer-questions-panel').html(response.msg);
                }
                setTimeout($('#qna-reply-modal-'+key).modal('hide'),3000);
            }
        });
    });
    
    $('body').on('click', '.qna-actions .give-vote-btn', function (e) {
        e.preventDefault();
        block( $('#cust_qna_form') );
        var vote = $(this).attr('data-vote');
        var ans_ID = $(this).attr('data-ans');
        if (vote === '') {
            return false;
        }
        var data = {
            action: 'wcmp_customer_ask_qna_handler',
            handler: 'vote_answer',
            vote: vote,
            ans_ID: ans_ID
        };
        $.post(woocommerce_params.ajax_url, data, function (response) {
            unblock( $('#cust_qna_form') );
            if (response.no_data == 0) {
                setTimeout(function(){
                    window.location.reload();
                },1000);
            }
        });
    });

})(jQuery); 
