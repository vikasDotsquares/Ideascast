<?php
if(isset($project_id) && !empty($project_id)) {
    $project_reward_setting = project_reward_setting($project_id, 1);
    if($project_reward_setting){
        $project_reward_setting = $project_reward_setting['RewardSetting'];
    }

    $project_detail = getByDbId("Project", $project_id, ['id', 'title', 'start_date', 'end_date', 'currency_id', 'budget']);
    $project_detail = $project_detail['Project'];
    $currency_symbol = 'GBP';
    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id', 'name', 'sign']);
        $currency_detail = $currency_detail['Currency'];
        $currency_symbol = $currency_detail['sign'];
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

<div class="setting-create-data">
    <div class="form-group">
        <label class="control-label col-sm-3 nopadding-right">OV Allocation:</label>
        <div class="col-sm-5" style="padding-left: 6px;">
            <input type="text" class="form-control inp-setting-ov-allocation" name="setting_ov_allocation" autocomplete="off">
        </div>
        <span class="error-message error text-danger col-md-offset-3 col-sm-9"></span>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3 nopadding-right">OV Exchange:</label>
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-4" style="padding-left: 6px;">
                    <input type="text" class="form-control inp-setting-ov-exchange" name="setting_ov_exchange" autocomplete="off">
                    <span class="error-message error text-danger"></span>
                </div>
                <div class="col-sm-1">
                    <label class="control-label equals-sign">=</label>
                </div>
                <div class="col-sm-5 soev_wrap">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                        <input type="text" class="form-control inp-setting-ov-exchange-value" name="setting_ov_exchange_value" autocomplete="off">
                    </div>
                    <span class="error-message error text-danger"></span>
                </div>
            </div>
        </div>
    </div>
     <div class="create-data-action">
        <a href="#" class="btn btn-success btn-xs save-setting-create ">Save</a>
        <a href="#" class="btn btn-danger btn-xs cancel-setting-create ">Cancel</a>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('.inp-setting-ov-allocation,.inp-setting-ov-exchange,.inp-setting-ov-exchange-value').limitText(17);

        $('.save-setting-create').click(function(event) {
            event.preventDefault();
            $.reload_data = true;
            var ov_allocate = $('.inp-setting-ov-allocation').deformat(),
                ov_exchange = $('.inp-setting-ov-exchange').deformat(),
                ov_exchange_value = $('.inp-setting-ov-exchange-value').deformat(),
                project_id = $('#sel_setting_create_ov').val(),
                user_id = $('#user_id').val();

            $('.setting-create-wrap').find('.error-message').text('');


            if(ov_allocate == '' || ov_allocate <= 0 || isNaN(ov_allocate) ) {
                console.log('first')
                $('.inp-setting-ov-allocation').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }
            if(ov_exchange == '' || ov_exchange <= 0 || isNaN(ov_exchange)) {
                console.log('sec')
                $('.inp-setting-ov-exchange').parent().find('.error-message').text('Required field.');
                return;
            }
            if(ov_exchange_value == '' || ov_exchange_value <= 0 || isNaN(ov_exchange_value)) {
                console.log('th')
                $('.inp-setting-ov-exchange-value').parents('.soev_wrap').find('.error-message').text('Required field.');
                return;
            }

            var param = {
                given_by: user_id,
                project_id: project_id,
                ov_allocation: ov_allocate,
                ov_exchange: ov_exchange,
                price_value: ov_exchange_value,
            }

            $.ajax({
                url: $js_config.base_url + 'rewards/save_create_allocation',
                type: 'POST',
                dataType: 'json',
                data: param,
                success: function(response) {
                    if(response.success) {
                        $.get_allocation_projects($('#sel_give_project'));
                        $.information($('.setting-create-wrap')).done(function(message) {
                            $('#sel_setting_create_ov option[value="'+project_id+'"]').remove();
                            $('#sel_setting_create_ov option[value=""]').prop('selected', true);
                            $('#sel_setting_create_ov').trigger('change');
                        })
                        $('#sel_give_project option[value=""]').prop('selected', true);
                        $('#sel_give_project').trigger('change');
                        $.allocated_projects($('#sel_setting_current_ov'));
                    }
                }
            })
        })


        $('.cancel-setting-create').click(function(event) {
            event.preventDefault();
            $('#sel_setting_create_ov option[value=""]').prop('selected', true);
            $('#sel_setting_create_ov').trigger('change');
        })


    })
</script>

<?php }else{ ?>
<!-- <div class="info-msg">Select A Project</div> -->
<?php } ?>