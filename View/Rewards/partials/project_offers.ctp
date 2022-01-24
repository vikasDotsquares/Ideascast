<?php
$current_user_id = $this->Session->read('Auth.User.id');

if(isset($project_id) && !empty($project_id)) {
    $currency_symbol = project_currency_symbol($project_id);
    $project_offers = project_offers($project_id);

    // get project reward setting.
    $project_reward_setting = project_reward_setting($project_id, 1);

?>
<div class="col-sm-12 offer-msg-box"></div>
<?php if($project_reward_setting){
    $project_reward_setting = $project_reward_setting['RewardSetting'];
?>
    <div class="wrap-btn">
        <a href="#" class="btn btn-sm btn-warning btn-create-offer"><i class="fa fa-plus"></i> Offer</a>
        <!-- <a href="#" class="btn btn-sm btn-warning refresh-offers"><i class="fa fa-refresh"></i> Refresh List</a> -->
        <input type="hidden" class="form-control inp-project-ex-price" value="<?php echo $project_reward_setting['price_value']; ?>">
    </div>

    <div class="create-offer-wrap clearfix" style="">
        <div class="current-data-action">
            <span class="offer-updated-date"></span>
            <!-- <a href="#" class="btn btn-success btn-xs save-offer tipText">Save</a>
            <a href="#" class="btn btn-danger btn-xs cancel-offer tipText">Cancel</a> -->
        </div>
        <div class="create-offer">
            <div class="form-group">
                <label class="control-label col-sm-3 nopadding-right">Offer:</label>
                <div class="col-sm-9" style="padding-left: 6px;">
                    <input type="hidden" class="inp-offer-id" name="offer_id" value="">
                    <div class="input-group offer-input-wrap">
                        <input type="text" class="form-control inp-offer-title" name="offer_title" placeholder="100 Chars max" autocomplete="off">
                        <span class="input-group-addon bg-red clear-offer-title" style="border-color: #dd4b39;"><i class="fa fa-times"></i></span>
                    </div>
                    <span class="error-message error text-danger"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 nopadding-right ">OV Exchange:</label>
                <div class="col-sm-9" style="padding-left: 6px;">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="text" class="form-control not-editable" name="offer_ov_exchange" value="<?php echo $project_reward_setting['ov_exchange']; ?>">
                            <span class="error-message error text-danger"></span>
                        </div>
                        <div class="col-sm-1">
                            <label class="control-label equals-sign">=</label>
                        </div>
                        <div class="col-sm-5 soev_wrap">
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                                <input type="text" class="form-control not-editable" name="offer_ov_exchange_value" value="<?php echo $project_reward_setting['price_value']; ?>">
                            </div>
                            <span class="error-message error text-danger"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 nopadding-right">Required to Buy:</label>
                <div class="col-sm-4 nopadding-right" style="padding-left: 6px;">
                    <div class="input-group offer-input-wrap">
                        <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                        <input type="text" class="form-control inp-offer-amount" name="offer_required" autocomplete="off">
                    </div>
                </div>
                <div class="col-sm-5 text-right" style="margin-top: 10px;">
                    <a href="#" class="btn btn-success btn-xs save-offer tipText">Save</a>
                    <a href="#" class="btn btn-danger btn-xs cancel-offer tipText">Cancel</a>
                </div>
                <span class="error-message error text-danger col-md-offset-3 col-sm-9" style="padding-left: 6px;"></span>
            </div>
        </div>
    </div>
<?php }else{ ?>
    <div class="col-sm-12 error-message error text-danger text-center" style="font-size: 12px;">Cannot create offer because no OV Exchange set</div>
<?php } ?>

<div class="project-offers-list clearfix"></div>
<script type="text/javascript">
    $(function(){

        $('.inp-offer-amount').keyup(function(event){
            event.preventDefault();
            var value = $(this).deformat(),
                exchange_price = $('.inp-project-ex-price').val();

            $(this).parents('.form-group').find('.error-message').text('');
            if((value % exchange_price) != 0) {
                $(this).parents('.form-group').find('.error-message').text("Required amount must be multiply of project's OV Exchange.");
                return
            }
        })

        $('.inp-offer-title').keyup(function(event){
            event.preventDefault();
            $(this).char_count(100);
        })

        $('.btn-create-offer').click(function(event){
            event.preventDefault();
            $('.create-offer-wrap').slideDown(400);
        })

        var project_id = $('#sel_offers_project').val();
        $.project_offers_list(project_id);
        $('.refresh-offers').click(function(event){
            event.preventDefault();
            $.project_offers_list(project_id);
            $('.cancel-offer').trigger('click', ['show']);
        })

        $('.cancel-offer').click(function(event, data){
            event.preventDefault();

            if(data == '' || data == undefined){
                $('.create-offer-wrap').slideUp(400);
            }
            $('.save-offer').addClass('not-editable');
            $('.inp-offer-id').val('');
            $('.inp-offer-title').val('');
            $('.inp-offer-amount').val('');
            $('.offer-updated-date').text('');
            $('.create-offer .error-message').text('');

            $('.project-offers .list-group-item').removeClass('edit');
        });

        $('.save-offer').click(function(event) {
            event.preventDefault();
            var id = $('.inp-offer-id').val(),
                title = $('.inp-offer-title').val(),
                amount = $('.inp-offer-amount').deformat(),
                exchange_price = $('.inp-project-ex-price').val();

            $('.create-offer .error-message').text('');
            if($.trim(title) == '') {
                $('.inp-offer-title').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }
            if(amount == '' || amount == 0 || isNaN(amount)) {
                $('.inp-offer-amount').parents('.form-group:first').find('.error-message').text('Required field.');
                return;
            }

            if((amount % exchange_price) != 0) {
                $('.inp-offer-amount').parents('.form-group').find('.error-message').text("Required amount must be multiply of project's OV Exchange.");
                return
            }

            $(this).addClass('not-editable');

            $.reload_data = true;
            var data = {
                id: id,
                title: title,
                amount: amount,
                project_id: project_id,
            }
            $.ajax({
                url: $.url + 'rewards/save_project_offer',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {
                    $.reload_data = true;
                    $('#sel_shop_project option[value=""]').prop('selected', true);
                    $('#sel_shop_project').trigger('change');
                    $.information($('.offer-msg-box')).done(function(message) {
                        $.project_offers_list(project_id);
                        $.get_offering_project($('#sel_shop_project'));
                        $('.save-offer').removeClass('not-editable');
                    })
                    $('.cancel-offer').trigger('click', ['show']);
                    if(response.hasOwnProperty('content')) {
                        if(response.content.hasOwnProperty('socket')) {
                            $.socket.emit('socket:notification', response.content.socket, function(userdata) {});
                        }
                    }
                }
            })

        });
    })
</script>
<?php } ?>