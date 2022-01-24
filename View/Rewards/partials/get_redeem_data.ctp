<?php
if(isset($project_id) && !empty($project_id)) {
    $project_charity = project_charity($project_id);
    $project_reward_setting = project_reward_setting($project_id, 1);
    if($project_reward_setting){
        $project_reward_setting = $project_reward_setting['RewardSetting'];
    }

    // Total points given to this user.
    $project_reward_assignments = project_reward_assignments($project_id, $user_id);
    $total_assigned = 0;
    if($project_reward_assignments){
        foreach ($project_reward_assignments as $rdKey => $rdVal) {
            $total_assigned += $rdVal['RewardAssignment']['allocated_rewards'];
        }
    }

    // Total points redeemed by this user.
    $project_redeemed = project_redeemed_data($project_id, $user_id);
    $total_redeem = 0;
    if($project_redeemed) {
        foreach ($project_redeemed as $rdKey => $rdVal) {
            $total_redeem += $rdVal['RewardRedeem']['redeem_amount'];
        }
    }

    $project_accelerated_points = project_accelerated_points($project_id, $user_id);

    if($project_accelerated_points){
        $total_assigned += $project_accelerated_points;
    }

    // Remaining points for this project for this user.
    $total_remaining = $total_assigned - $total_redeem;
    // e('total_assigned: '.$total_assigned.', total_redeem: '.$total_redeem.', total_remaining: '.$total_remaining);


    $project_detail = getByDbId("Project", $project_id, ['id', 'title', 'start_date', 'end_date', 'currency_id', 'budget']);
    $project_detail = $project_detail['Project'];
    $currency_symbol = '';
    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id', 'name', 'sign']);
        $currency_detail = $currency_detail['Currency'];
        $currency_symbol = $currency_detail['sign'];
        // pr($currency_detail);
    }
    /*if($currency_symbol == 'USD') {
        $currency_symbol = '<i class="fa fa-dollar"></i>';
    }
    else if($currency_symbol == 'GBP') {
        $currency_symbol = '<i class="fa fa-gbp"></i>';
    }
    else if($currency_symbol == 'EUR') {
        $currency_symbol = '<i class="fa fa-eur"></i>';
    }
    else if($currency_symbol == 'DKK' || $currency_symbol == 'ISK') {
        $currency_symbol = '<span style="font-weight: 600">Kr</span>';
    }*/
 ?>
<div class="form-group">
    <label class="control-label col-sm-3 nopadding-right padding-left30">OV Available:</label>
    <div class="col-sm-5">
        <input type="text" class="form-control inp-the-ov-exchange" name="the_ov_exchange" value="<?php if(!empty($total_remaining)) {echo number_format($total_remaining,0,',',',');}else{ echo '0'; } ?>">
    </div>
    <label class="control-label col-sm-4 nopadding-right remaining-the-ov-exchange"></label>
</div>
<div class="form-group wrap-ov-exchange">
    <label class="control-label col-sm-3 nopadding-right padding-left30">OV Exchange:</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-4">
                <input type="text" class="form-control inp-ov-exchange" name="ov_exchange" value="<?php if(isset($project_reward_setting['ov_exchange']) && !empty($project_reward_setting['ov_exchange'])) echo number_format($project_reward_setting['ov_exchange'],0,',',','); ?>">
            </div>
            <div class="col-sm-1">
                <label class="control-label equals-sign">=</label>
            </div>
            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                    <input type="text" class="form-control inp-ov-exchange-value" name="ov_exchange_value" value="<?php if(isset($project_reward_setting['price_value']) && !empty($project_reward_setting['price_value'])) echo $project_reward_setting['price_value']; ?>">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-3 nopadding-right padding-left30" >OV to Redeem:</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-4">
                <input type="text" class="form-control inp-redeem-ov" name="redeem_ov">
            </div>
            <div class="col-sm-1">
                <label class="control-label equals-sign">=</label>
            </div>
            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                    <input type="text" class="form-control inp-redeem-value" name="redeem_value">
                </div>
            </div>
        </div>
    </div>
    <span class="error-message error text-danger col-md-offset-3 col-sm-9"></span>
</div>
<?php if($project_charity){
$project_charity = $project_charity['RewardCharity'];
?>
<div class="form-group">
    <input type="hidden" class="redeem-charity-id" name="redeem_charity_id" id="redeem_charity_id" value="<?php echo $project_charity['id']; ?>">
    <div class="col-sm-12 padding-left30">
        <input type="checkbox" class="give_to_charity" name="" id="give_to_charity">
        <label class="control-label" for="give_to_charity" style="font-weight: normal;"><span class="icon-charity"></span> <?php echo htmlentities($project_charity['title'], ENT_QUOTES, "UTF-8"); ?></label>
    </div>
</div>
<?php } ?>

<div class="form-group" style="margin-bottom: 0;">
    <div class="col-sm-12" style="text-align: right;">
        <a href="#" class="btn btn-success btn-xs save-redeem-values <?php if(!isset($project_reward_setting) || empty($project_reward_setting)){ ?> not-editable<?php } ?>">Redeem</a>
        <a href="#" class="btn btn-danger btn-xs clear-redeem-values">Clear</a>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $.fn.currencyFormat = function() {
            this.each( function( i ) {
                $(this).change( function( e ){
                    if( isNaN( parseFloat( this.value ) ) ) return;
                    this.value = parseFloat(this.value).toFixed(2);
                });
            });
            return this; //for chaining
        }

        $('.inp-redeem-ov').on('keyup', function(event) {
            var value = parseInt($(this).val(), 10),
                $inp_exchange = $('.inp-the-ov-exchange'),
                total_have = $('.inp-the-ov-exchange').deformat(),
                exchange_price = $('.inp-ov-exchange').deformat(),
                exchange_value = $('.inp-ov-exchange-value').deformat(),
                $redeemed_value = $('.inp-redeem-value');

            $(this).parents('.form-group').find('.error-message').text("");
            $('.remaining-the-ov-exchange').text('');

            if((value % exchange_price) != 0) {
                $(this).parents('.form-group').find('.error-message').text("Redeem OV Amount must be a multiple of OV Exchange.");
                $redeemed_value.val('');
                return
            }

            // if redeem is greater then available value
            if( value > total_have ) {
                $(this).parents('.form-group').find('.error-message').text("Redeem OV Amount can't be greater then OV available amount.");
                $redeemed_value.val('');
                return;
            }
            // calculate the exchange price
            if(value != '' && value > 0) {
                var calculate = Math.round(parseFloat((exchange_value/exchange_price) * value));
                if(calculate == exchange_value && value < exchange_price ) {
                    calculate = 0;
                }
                $redeemed_value.val( calculate );
            }
            else{
                $redeemed_value.val('');
            }

            // show the remaining value
            var remaining = parseInt(total_have) - value;

            remaining = remaining.toString().replace(/\D/g, "")
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $('.remaining-the-ov-exchange').text('Remaining: '+remaining);
        })

        $('.save-redeem-values').click(function(event) {
            event.preventDefault();
            $.reload_data = true;
            var post_data = {
                    given_by: $('#user_id').val(),
                    project_id: $('#sel_redeem_ov').val(),
                    redeem_amount: $('.inp-redeem-ov').val(),
                    redeemed_value: $('.inp-redeem-value').val(),
                    ov_exchange: $('.inp-ov-exchange').val(),
                    ov_exchange_value: $('.inp-ov-exchange-value').val(),
                }
            var to_redeem = $('.inp-redeem-ov').deformat(),
                total_have = $('.inp-the-ov-exchange').deformat();
            // if trying to redeem empty value
            if( $('.inp-redeem-ov').val() == '' || $('.inp-redeem-ov').val() == undefined || parseInt($('.inp-redeem-ov').val(), 10) <= 0) {
                $('.inp-redeem-ov').parents('.form-group').find('.error-message').text("Required field.");
                return;
            }

            if((to_redeem % post_data.ov_exchange) != 0) {
                $('.inp-redeem-ov').parents('.form-group').find('.error-message').text("Redeem OV Amount must be a multiple of OV Exchange.");
                $('.inp-redeem-value').val('');
                return
            }

            if( parseInt(to_redeem) > parseInt(total_have) ) {
                $('.inp-redeem-ov').parents('.form-group').find('.error-message').text("Redeem OV Amount can't be greater then OV available amount.");
                return;
            }

            if($('#give_to_charity').length > 0) {
                if($('#give_to_charity').prop('checked')) {
                    post_data.charity_id = $('#redeem_charity_id').val();
                }
            }
            $.ajax({
                url: $.url + 'rewards/save_redeem_data',
                type: 'POST',
                dataType: 'json',
                data: post_data,
                success: function(response) {
                    if(response.success) {
                        $.get_redeem_projects($('#sel_redeem_ov'));
                        $('#sel_history_project option[value=""]').prop('selected', true);
                        $('#sel_history_project').trigger('change');
                        $('#sel_member_history_project option[value=""]').prop('selected', true);
                        $('#sel_member_history_project').trigger('change');
                        $('#sel_shop_project option[value=""]').prop('selected', true);
                        $('#sel_shop_project').trigger('change');
                        $.information($('.redeem-data')).done(function(message) {
                            $('#sel_redeem_ov option[value=""]').prop('selected', true);
                            $('#sel_redeem_ov').trigger('change');
                        })
                    }
                }
            })
        });


        $('.clear-redeem-values').click(function(event) {
            event.preventDefault();
            $('.inp-redeem-ov, .inp-redeem-value').val('');
            $('.remaining-the-ov-exchange').text('');

            if($('#give_to_charity').length > 0) {
                if($('#give_to_charity').prop('checked')) {
                    $('#give_to_charity').prop('checked', false);
                }
            }
            $('.redeem-data .error-message').text('');
        })

        $('.inp-give-allocated-reward').keyup(function(event) {
            var current_allocated = $('.inp-give-current-avail').deformat(),
                $error = $(this).parents('.form-group:first').find('.error-message');

            $error.text('');
            $('.giv-remaining-ov').text('');

            if(parseInt($(this).deformat()) > parseInt(current_allocated)) {
                $error.text("You can't allocate more than available OV.");
                return;
            }

            var remaining = parseInt(current_allocated) - parseInt($(this).deformat());

            remaining = remaining.toString().replace(/\D/g, "")
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $('.giv-remaining-ov').text(remaining);
        })


    })
</script>

<?php }else{ ?>
    <!-- <div class="info-msg">Select A Project</div> -->
<?php } ?>