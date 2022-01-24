
<?php if(isset($offer_id) && !empty($offer_id)){
    $current_user_id = $this->Session->read('Auth.User.id');
    $currency_symbol = project_currency_symbol($project_id);

    // get project reward setting.
    $project_reward_setting = project_reward_setting($project_id, 1);
    if($project_reward_setting){
        $project_reward_setting = $project_reward_setting['RewardSetting'];
    }

    // Total points given to this user.
    $project_reward_assignments = project_reward_assignments($project_id, $current_user_id);
    $total_assigned = 0;
    if($project_reward_assignments){
        foreach ($project_reward_assignments as $rdKey => $rdVal) {
            $total_assigned += $rdVal['RewardAssignment']['allocated_rewards'];
        }
    }

    // Total points redeemed by this user.
    $project_redeemed = project_redeemed_data($project_id, $current_user_id);
    $total_redeem = 0;
    if($project_redeemed) {
        foreach ($project_redeemed as $rdKey => $rdVal) {
            $total_redeem += $rdVal['RewardRedeem']['redeem_amount'];
        }
    }

    $project_accelerated_points = project_accelerated_points($project_id, $current_user_id);

    if($project_accelerated_points){
        $total_assigned += $project_accelerated_points;
    }

    // Remaining points for this project for this user.
    $total_remaining = $total_assigned - $total_redeem;

    $project_offer_detail = project_offer_detail($offer_id);
    $updated_user = user_full_name($project_offer_detail['RewardOffer']['creator_id']);

    $result = ($project_offer_detail['RewardOffer']['amount'] / $project_reward_setting['price_value']) * $project_reward_setting['ov_exchange'];

?>
<div class="form-group">
    <label class="control-label col-sm-3 nopadding-right padding-left30">OV Available:</label>
    <div class="col-sm-6">
        <input type="text" class="form-control inp-shop-project-available not-editable" name="shop_project_available" value="<?php echo $total_remaining; ?>">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-sm-3 nopadding-right padding-left30">Required to Buy:</label>
    <div class="col-sm-3">
        <div class="input-group">
            <span class="input-group-addon">OV</span>
            <input type="text" class="form-control inp-shop-project-required not-editable" name="shop_project_required" value="<?php echo $result; ?>">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="input-group">
            <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
            <input type="text" class="form-control inp-project-ov-exchange not-editable" name="project_ov_exchange" value="<?php echo $project_offer_detail['RewardOffer']['amount']; ?>">
        </div>
    </div>
    <div class="col-sm-3 nopadding-left" style="line-height: 33px;">
        <a href="#" class="btn btn-xs buy-offer <?php if($total_remaining <= 0 || $total_remaining < $result){ ?>not-editable btn-default <?php }else{ ?>btn-success <?php } ?>">Buy</a>
        <a href="#" class="btn btn-xs btn-danger reject-offer">Clear</a>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        var creator_name = '<?php echo $updated_user; ?>';
        $('.buy-offer').click(function(event) {
            event.preventDefault();
            var $this = $(this);
            var project_id = $('#sel_shop_project').val();
                offer_id = $('#sel_an_offer').val();
                redeemed_value = $('.inp-project-ov-exchange').val();
            var data = {
                project_id: project_id,
                offer_id: offer_id,
                redeemed_value: redeemed_value
            }
            $.reload_data = true;
            $('#sel_an_offer').addClass('not-editable');
            $.ajax({
                url: $.url + 'rewards/offer_shopping',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {

                    if(response.success) {
                        $.information($('.shop-outer'), 'Message sent to: '+creator_name).done(function(message) {
                            $('#sel_an_offer option[value=""]').prop('selected', true);
                            $('#sel_an_offer').removeClass('not-editable');
                        })

                        $('#sel_redeem_ov option[value=""]').prop('selected', true);
                        $('#sel_redeem_ov').trigger('change');
                        $('#sel_history_project option[value=""]').prop('selected', true);
                        $('#sel_history_project').trigger('change');
                        $('#sel_member_history_project option[value=""]').prop('selected', true);
                        $('#sel_member_history_project').trigger('change');
                        $.user_project_shopping(project_id);
                    }
                }
            })
        });

        $('.reject-offer').click(function(event) {
            event.preventDefault();
            $('#sel_an_offer option[value=""]').prop('selected', true);
            $('#sel_an_offer').trigger('change');
        });
    })
</script>

<style type="text/css">
    .received_yes_no {
        display: inline-block;
    }
</style>
<?php } ?>