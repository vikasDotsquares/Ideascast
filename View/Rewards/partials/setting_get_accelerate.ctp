<style type="text/css">
    .inp-sel-cal {
        width: 0;
        height: 0;
        opacity: 0;
        visibility: hidden;
    }
</style>
<?php
    if(isset($project_id) && !empty($project_id)) {
    // get project reward setting.
        $project_reward_setting = project_reward_setting($project_id, 1);
        if($project_reward_setting){
            $project_accelerate = project_accelerate_setting($project_id, 0, 1);
            $project_accelerate = $project_accelerate['RewardAccelerate'];

            $now = new DateTime(date('Y-m-d'));
            $otherDate = new DateTime(date('Y-m-d', strtotime($project_accelerate['accelerate_date'])));
            $diff = $now->diff($otherDate);
            $today = $this->Wiki->_displayDate(date('Y-m-d h:i:s A', strtotime(date('Y-m-d'))), $format = 'Y-m-d');

 ?>

<div class="setting-accelerate-data">
    <input type="hidden" class="form-control inp-setting-accelerate-id" name="setting_accelerate_id" value="<?php echo $project_accelerate['id']; ?>">
    <div class="form-group">
        <label class="control-label col-sm-3 nopadding-right padding-left20">Title:</label>
        <div class="col-sm-8 nopadding-right" style="padding-left: 6px;">
            <textarea rows="2" class="form-control txta-setting-accelerate-title" name="accelerate_title" style="resize: vertical;" placeholder="Max 100 Chars"><?php echo $project_accelerate['title'] ?></textarea>
            <span class="error-message error text-danger"></span>
        </div>
    </div>
    <div class="form-group percent-wrap">
        <label class="control-label col-sm-3 nopadding-right padding-left20">Accelerator:</label>
        <div class="col-sm-2 nopadding-right" style="padding-left: 6px;">
            <input type="text" class="form-control inp-setting-accelerate-percent" name="setting_accelerate_percent" value="<?php echo $project_accelerate['accelerate_percent']; ?>" autocomplete="off">
        </div>
        <label class="control-label col-sm-1  percent-sign" style="padding-left: 3px;font-size: 16px;padding-top: 7px;">%</label>
        <span class="error-message error text-danger col-sm-9 col-md-offset-3"></span>
    </div>
    <div class="form-group cal-wrap">
        <label class="control-label col-sm-3 nopadding-right padding-left20">Apply on:</label>
        <div class="col-sm-2 nopadding-right" style="padding-top: 3px; padding-left: 0;">
            <input id="sel_date" type="text" name="sel_date" class="inp-sel-cal" value="<?php echo (isset($project_accelerate['accelerate_date']) && !empty($project_accelerate['accelerate_date'])) ? date('Y-m-d', strtotime($project_accelerate['accelerate_date'])) : ''; ?>" />
            <a href="#" class="btn btn-xs btn-info trigger-cal tipText" title="Select Date">
                <i class="fa fa-calendar-check-o"></i>
            </a>
            <a href="#" class="btn btn-xs btn-danger tipText clear-date" title="Clear Date">
                <i class="fa fa-times"></i>
            </a>
        </div>
        <div class="col-sm-4 nopadding-left">
            <label class="control-label disp-dates"><?php echo (isset($project_accelerate['accelerate_date']) && !empty($project_accelerate['accelerate_date'])) ? date('d M Y', strtotime($project_accelerate['accelerate_date'])) : 'Day, Month Year' ?></label>
        </div>
        <span class="error-message error text-danger col-sm-9 col-md-offset-3"></span>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3 nopadding-right padding-left20">Reason:</label>
        <div class="col-sm-8 nopadding-right" style="padding-left: 6px;">
            <textarea rows="3" class="form-control txta-accelerate-reason" name="accelerate_reason" style="resize: vertical;" placeholder="Max 160 Chars"><?php echo $project_accelerate['reason'] ?></textarea>
            <span class="error-message error text-danger"></span>
        </div>
    </div>
    <div class="accelerate-data-action">
        <?php
        if( (isset($project_accelerate['accelerate_date']) && !empty($project_accelerate['accelerate_date'])) && (strtotime($project_accelerate['accelerate_date']) > strtotime($today)) ) { ?>
            <a href="#" class="btn btn-xs btn-danger authorize-no"><i class="fa fa-trash"></i> &nbsp;Accelerator</a>
        <?php } ?>
        <a href="#" class="btn btn-success btn-xs save-setting-accelerate">Save</a>
        <a href="#" class="btn btn-danger btn-xs cancel-setting-accelerate">Cancel</a>
    </div>
</div>

<?php
if( (isset($project_accelerate['accelerate_date']) && !empty($project_accelerate['accelerate_date'])) && (strtotime($project_accelerate['accelerate_date']) <= strtotime($today)) ) { ?>
    <div class="form-group" style="padding-top: 10px;">
        <label class="control-label col-sm-3 nopadding-right" style="padding-top: 7px; padding-left: 30px;">Authorize:</label>
        <div class="col-sm-7">
            <a href="#" class="btn btn-sm btn-success authorize-yes">Yes</a>
            <a href="#" class="btn btn-sm btn-danger authorize-no"><i class="fa fa-trash"></i> &nbsp;Accelerator</a>
        </div>
    </div>
    <div class="col-sm-12 ">
        <span class="error-message error text-danger can-not-accelerate"></span>
    </div>
<?php } ?>


<script type="text/javascript">
    $(function(){

        $('.save-setting-accelerate').click(function(event) {
            event.preventDefault();
            $.reload_data = true;
            var accelerate_title = $('.txta-setting-accelerate-title').val(),
                accelerate_percent = parseInt($('.inp-setting-accelerate-percent').val()),
                str_accelerate_percent = $('.inp-setting-accelerate-percent').val(),
                id = $('.inp-setting-accelerate-id').val(),
                accelerate_date = $('.inp-sel-cal').val(),
                reason = $('.txta-accelerate-reason').val(),
                project_id = $('#sel_setting_accelerate').val();

            $('.setting-accelerate-wrap').find('.error-message').text('');

            if(str_accelerate_percent == '' || accelerate_percent <= 0) {
                $('.inp-setting-accelerate-percent').parents('.percent-wrap:first').find('.error-message').text('Required field.');
                return;
            }
            if(accelerate_percent > 100) {
                $('.inp-setting-accelerate-percent').parents('.percent-wrap:first').find('.error-message').text('Percentage should be less than or equal to 100.');
                return;
            }

            if(accelerate_date == '') {
                $('.inp-sel-cal').parents('.cal-wrap').find('.error-message').text('Required field.');
                return;
            }

            var param = {
                id: id,
                accelerate_title: accelerate_title,
                accelerate_percent: accelerate_percent,
                project_id: project_id,
                accelerate_date: accelerate_date,
                reason: reason,
            }

            $.ajax({
                url: $.url + 'rewards/save_project_accelerate',
                type: 'POST',
                dataType: 'json',
                data: param,
                success: function(response) {
                    if(response.success) {
                        $.information($('.setting-accelerate-wrap')).done(function(message) {
                            $('#sel_setting_accelerate option[value=""]').prop('selected', true);
                            $('#sel_setting_accelerate').trigger('change');
                            $('.inp-setting-accelerate-id').val('');
                        })
                    }
                }
            })
        })

        $('.inp-setting-accelerate-percent').keyup(function(event) {
            $(this).onlyNumeric();
            var accelerate_percent = parseInt($(this).val());
            $(this).parents('.percent-wrap:first').find('.error-message').text('');
            if(accelerate_percent > 100) {
                $(this).parents('.percent-wrap:first').find('.error-message').text('Percentage should be less than or equal to 100.');
                return;
            }
        });

        var dpick_opt = {
            // changeMonth: true,
            // changeYear: true,
            minDate: 0,
            dateFormat: 'yy-mm-dd',
            // minDate: '+1d',
            onClose: function(selected, inst){
                var $input = inst.input;
                if(selected) {
                    $(this).parents('.cal-wrap').find('.disp-dates').text(moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY'));
                }
            },
            onSelect: function(selected, inst) {
                var $input = inst.input;
                if(selected) {
                    $(this).parents('.cal-wrap').find('.disp-dates').text(moment(selected, 'YYYY-MM-DD').format('DD, MMM YYYY'));
                 }
            }
        };
        var calndr = $('.inp-sel-cal').datepicker(dpick_opt);


        // Open datepicker on icons
        $('.trigger-cal').on('click', function(event) {
            event.preventDefault();
            var $input = $(this).parents('.cal-wrap:first').find('.inp-sel-cal');

            $input.datepicker(dpick_opt).datepicker('show');
        });

        $('.clear-date').on('click', function(event) {
            event.preventDefault();
            $('.inp-sel-cal').val('').datepicker('destroy');
            calndr = $('.inp-sel-cal').datepicker(dpick_opt);
            $('.disp-dates').text('Day, Month Year');
        });

        $('.txta-setting-accelerate-title').keyup(function(event){
            event.preventDefault();
            $(this).char_count(100);
        })

        var characters = 160;
        $('.txta-accelerate-reason').keyup(function(event){
            event.preventDefault();
            $(this).char_count(characters);
        })

        $('.cancel-setting-accelerate').on('click', function(event) {
            event.preventDefault();
            $('.setting-accelerate-wrap').find('.error-message').text('');
            $('.inp-sel-cal').val('').datepicker('destroy');
            calndr = $('.inp-sel-cal').datepicker(dpick_opt);
            $('.disp-dates').text('Day, Month Year');
            $('.inp-setting-accelerate-percent').val('');
            $('.txta-accelerate-reason').val('');
            $('#sel_setting_accelerate option[value=""]').prop('selected', true);
            $('#sel_setting_accelerate').trigger('change');
        });

        $('.authorize-yes').on('click', function(event) {
            event.preventDefault();
            $.reload_data = true;
            var project_id = $('#sel_setting_accelerate').val();

            var param = {
                project_id: project_id,
            }

            $.ajax({
                url: $.url + 'rewards/authorize_project_accelerate',
                type: 'POST',
                dataType: 'json',
                data: param,
                success: function(response) {
                    if(response.success) {
                        $.information($('.setting-accelerate-wrap')).done(function(message) {
                            $('#sel_setting_accelerate option[value=""]').prop('selected', true);
                            $('#sel_setting_accelerate').trigger('change');
                        })
                        $('#sel_setting_current_ov option[value=""]').prop('selected', true);
                        $('#sel_setting_current_ov').trigger('change');
                        $('#sel_redeem_ov option[value=""]').prop('selected', true);
                        $('#sel_redeem_ov').trigger('change');
                        $('#sel_shop_project option[value=""]').prop('selected', true);
                        $('#sel_shop_project').trigger('change');
                        $('.give-clear').trigger('click');
                        $('.form_chain_confirm').hide('fade', {direction: 'right'}, 400);
                        $('.form_chain_selection').show('fade', {direction: 'right'}, 400);

                        var content = response.content;
                        if(content){
                            $.socket.emit('socket:notification', response.content.socket, function(userdata) {});
                        }
                    }
                }
            })
        });

        $('.authorize-no').on('click', function(event) {
            event.preventDefault();
            $.reload_data = true;
            var project_id = $('#sel_setting_accelerate').val();

            var param = {
                project_id: project_id,
            }

            $.ajax({
                url: $.url + 'rewards/reject_project_accelerate',
                type: 'POST',
                dataType: 'json',
                data: param,
                success: function(response) {
                    if(response.success) {
                        $.information($('.setting-accelerate-wrap')).done(function(message) {
                            $('#sel_setting_accelerate option[value=""]').prop('selected', true);
                            $('#sel_setting_accelerate').trigger('change');
                        })
                    }
                }
            })
        });

    })
</script>

    <?php }else{ ?>
        <div class="col-sm-9 col-md-offset-3 error-message error text-danger" style="font-size: 12px;padding-left: 6px;">Cannot create accelerator because no OV Exchange set</div>
    <?php } ?>

<?php } ?>