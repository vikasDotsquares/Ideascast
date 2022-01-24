<?php
if(isset($project_id) && !empty($project_id)) {
    $project_reward_setting = project_reward_setting($project_id, 1);
    if($project_reward_setting){
        $project_reward_setting = $project_reward_setting['RewardSetting'];
    }
    // pr($project_reward_setting);
    $currency_symbol = project_currency_symbol($project_id);

 ?>
 <div class="current-data-action" style="text-align: left;">
    <span class="updated-date" style="float: none;">Last updated by: <?php
        $updated_by = user_full_name($project_reward_setting['given_by']);
        echo $updated_by . ': ';
        echo $this->Wiki->_displayDate(date('Y-m-d h:i:s A', strtotime($project_reward_setting['modified'])), $format = 'd M Y g:i A'); ?></span>
</div>
<div class="setting-current-data">
    <input type="hidden" class="form-control inp-setting-current-ov-allocation-id" name="setting_ov_allocation_id" value="<?php echo $project_reward_setting['id']; ?>">
    <div class="form-group">
        <label class="control-label col-sm-3 nopadding-right padding-left20">Remaining OV:</label>
        <div class="col-sm-5" style="padding-left: 6px;">
            <input type="text" class="form-control not-editable inp-setting-remaining-ov" name="" value="<?php echo number_format($project_reward_setting['remaining_ov'],0,',',','); ?>">
        </div>
        <label class="control-label col-sm-4 current-remaining-ov nopadding-left"></label>

        <span class="error-message error text-danger col-md-offset-3 col-sm-9"></span>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3 nopadding-right padding-left20">OV Allocation:</label>
        <div class="col-sm-5" style="padding-left: 6px;">
            <input type="text" class="form-control inp-setting-current-ov-allocation" name="setting_ov_allocation" value="" autocomplete="off">
        </div>
        <div class="col-sm-4">
            <div class="btn-group toggle-group ">
                <a href="#" title="Increase" class="tipText btn btn-sm btn-toggle toggle-on" data-value="increase"><i class="fa fa-plus"></i></a>
                <a href="#" title="Decrease" class="tipText btn btn-sm btn-toggle toggle-off" data-value="decrease"><i class="fa fa-minus"></i></a>
            </div>
        </div>
        <span class="error-message error text-danger col-md-offset-3 col-sm-9" style="padding-left: 6px;"></span>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3 nopadding-right padding-left20">OV Exchange:</label>
        <div class="col-sm-9" style="padding-left: 6px;">
            <div class="row">
                <div class="col-sm-4">
                    <input type="text" class="form-control inp-setting-current-ov-exchange" name="setting_ov_exchange" value="<?php echo $project_reward_setting['ov_exchange']; ?>">
                    <span class="error-message error text-danger"></span>
                </div>
                <div class="col-sm-1">
                    <label class="control-label equals-sign">=</label>
                </div>
                <div class="col-sm-5 soev_wrap">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                        <input type="text" class="form-control inp-setting-current-ov-exchange-value" name="setting_ov_exchange_value" value="<?php echo $project_reward_setting['price_value']; ?>">
                    </div>
                    <span class="error-message error text-danger"></span>
                </div>
            </div>
        </div>
    </div>
     <div class="current-data-action">
        <a href="#" class="btn btn-success btn-xs save-setting-current tipText">Save</a>
        <a href="#" class="btn btn-danger btn-xs cancel-setting-current tipText">Cancel</a>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('.inp-setting-remaining-ov,.inp-setting-current-ov-allocation,.inp-setting-current-ov-exchange,.inp-setting-current-ov-exchange-value').limitText(17);

        $(".btn.btn-toggle").click(function (event) {
            event.preventDefault();
            var group = $(this).parents('.toggle-group:first');
            group.find(".btn.toggle-on").removeClass("toggle-on").addClass("toggle-off");
            $(this).removeClass("toggle-off").addClass("toggle-on");
            $('.inp-setting-current-ov-allocation').parents('.form-group:first').find('.error-message').text("");
            $('.inp-setting-current-ov-allocation').trigger('keyup');
            if($(this).data('value') == 'decrease') {
                var remaining = parseInt($('.inp-setting-remaining-ov').deformat()),
                    ov_allocate = parseInt($('.inp-setting-current-ov-allocation').deformat());
                if(ov_allocate > remaining) {
                    $('.current-remaining-ov').text('');
                    $('.inp-setting-current-ov-allocation').parents('.form-group:first').find('.error-message').text("Allocated value shouldn't be greater than remaining value.");
                }
            }
        });

        $('.save-setting-current').click(function(event) {
            event.preventDefault();
            $.reload_data = true;
            var ov_allocate_id = $('.inp-setting-current-ov-allocation-id').val(),
                ov_allocate = $('.inp-setting-current-ov-allocation').deformat(),
                ov_exchange = $('.inp-setting-current-ov-exchange').deformat(),
                ov_exchange_value = $('.inp-setting-current-ov-exchange-value').deformat(),
                project_id = $('#sel_setting_current_ov').val(),
                remaining_ov = parseInt($('.inp-setting-remaining-ov').deformat()),
                user_id = $('#user_id').val(),
                incr_decr = $('.btn-toggle.toggle-on').data('value');

            $('.setting-current-wrap').find('.error-message').text('');
            $('.inp-setting-current-ov-allocation').parents('.form-group:first').find('.error-message').text('')

            /*if(ov_allocate == '' || ov_allocate <= 0 || isNaN(ov_allocate) ) {
                $('.inp-setting-current-ov-allocation').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }*/
            if(ov_exchange == '' || ov_exchange <= 0 || isNaN(ov_exchange) ) {
                $('.inp-setting-current-ov-exchange').parent().find('.error-message').text('Required field.');
                return;
            }
            if(ov_exchange_value == '' || ov_exchange_value <= 0 || isNaN(ov_exchange_value) ) {
                $('.inp-setting-current-ov-exchange-value').parents('.soev_wrap').find('.error-message').text('Required field.');
                return;
            }

            if(incr_decr == 'decrease') {
                if( parseInt(ov_allocate) > remaining_ov) {
                    $('.inp-setting-current-ov-allocation').parents('.form-group:first').find('.error-message').text("Allocated value shouldn't be greater than remaining value.");
                    return;
                }
            }

            var param = {
                id: ov_allocate_id,
                given_by: user_id,
                project_id: project_id,
                ov_allocation: ov_allocate,
                ov_exchange: ov_exchange,
                price_value: ov_exchange_value,
                updation_type: incr_decr,
            }

            $.ajax({
                url: $.url + 'rewards/save_create_allocation',
                type: 'POST',
                dataType: 'json',
                data: param,
                success: function(response) {
                    if(response.success) {
                        $.get_allocation_projects($('#sel_give_project'));
                        $.information($('.setting-current-wrap')).done(function(message) {
                            $('#sel_setting_current_ov option[value=""]').prop('selected', true);
                            $('#sel_setting_current_ov').trigger('change');
                        })
                        $('#sel_redeem_ov option[value=""]').prop('selected', true);
                        $('#sel_give_project option[value=""]').prop('selected', true);
                        $('#sel_setting_accelerate option[value=""]').prop('selected', true);
                        $('#sel_offers_project option[value=""]').prop('selected', true);
                        $('#sel_shop_project option[value=""]').prop('selected', true);
                        $('#sel_give_project').trigger('change');
                        $('#sel_redeem_ov').trigger('change');
                        $('#sel_setting_accelerate').trigger('change');
                        $('#sel_offers_project').trigger('change');
                        $('#sel_shop_project').trigger('change');
                    }
                }
            })
        })

        $('.inp-setting-current-ov-allocation').keyup(function(event) {
            var value = parseInt($(this).deformat()),
                str_value = ($(this).deformat()),
                remaining = parseInt($('.inp-setting-remaining-ov').deformat()),
                incr_decr = $('.btn-toggle.toggle-on').data('value'),
                ov_allocate = parseInt($('.inp-setting-current-ov-allocation').deformat());
            var display_val = 0;

            $('.inp-setting-current-ov-allocation').parents('.form-group:first').find('.error-message').text('')

            if(incr_decr == 'increase') {
                display_val = remaining + value;
            }
            if(incr_decr == 'decrease') {
                display_val = remaining - value;
            }
            if(incr_decr == 'decrease') {
                if(ov_allocate > remaining) {
                    $('.inp-setting-current-ov-allocation').parents('.form-group:first').find('.error-message').text("Allocated value shouldn't be greater than remaining value.");
                    $('.current-remaining-ov').text('');
                    return;
                }
            }
            if(str_value == '' || str_value == undefined) {
                $('.current-remaining-ov').text('');
                return;
            }
            $('.current-remaining-ov').text(display_val.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        })

        $('.cancel-setting-current').click(function(event) {
            event.preventDefault();
            $('#sel_setting_current_ov option[value=""]').prop('selected', true);
            $('#sel_setting_current_ov').trigger('change');
        })

    })
</script>

<style type="text/css">
    .toggle-group, .toggle-group-selected {
        box-sizing: border-box;
        cursor: pointer;
        display: inline-block !important;
        font-size: 14px;
        height: 100%;
        line-height: 20px;
        transition: margin-left 0.5s ease 0s;
    }
    .toggle-group>a:first-child {
        margin-right: 0 !important;
    }
    span.updated-date {
        padding: 5px 0 0 15px;
        float: left;
        font-size: 11px;
    }


</style>

<?php }else{ ?>
<!-- <div class="info-msg">Select A Project</div> -->
<?php } ?>