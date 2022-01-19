
<?php
if(isset($project_id) && !empty($project_id)){
    $user_redeemed = project_redeemed_data($project_id, $user_id);
    $project_charity = project_charity($project_id);
    $currency_symbol = project_currency_symbol($project_id);
?>
<div class="my-history-wrapper">
    <?php if(isset($user_redeemed) && !empty($user_redeemed)) { ?>
    <div class="history-row-header">
        <div class="history-col history-col-1">Activity On</div>
        <div class="history-col history-col-2">OV Amount</div>
        <div class="history-col history-col-3">OV Exchange</div>
        <div class="history-col history-col-4">Received</div>
        <div class="history-col history-col-5"></div>
    </div>
    <div class="history-inner">
        <?php
            foreach ($user_redeemed as $key => $data) {
                $redeem_data = $data['RewardRedeem'];
                // if(!isset($redeem_data['reward_offer_id']) || empty($redeem_data['reward_offer_id'])) {
        ?>
            <div class="history-row <?php if(isset($redeem_data['reward_offer_id']) && !empty($redeem_data['reward_offer_id'])) { ?>offer<?php } ?>">
                <div class="history-col history-col-1">
                    <?php echo $this->Wiki->_displayDate(date('Y-m-d h:i:s A',strtotime($redeem_data['modified'])), $format = 'd, M Y g:i A'); ?>
                </div>
                <div class="history-col history-col-2">
                    <span class="row-box"><?php echo $redeem_data['redeem_amount']; ?></span>
                </div>
                <div class="history-col history-col-3">
                    <div class="ovexchange">
                        <span class="exchange-nub"><?php echo (!empty($redeem_data['ov_exchange'])) ? $redeem_data['ov_exchange'] : 0; ?></span>
                        <span class="fa-gbp-ov"><?php echo $currency_symbol; ?></span>
                        <span class="exchange-nub-two"><?php echo (!empty($redeem_data['ov_exchange_value'])) ? $redeem_data['ov_exchange_value'] : 0; ?></span>
                    </div>
                </div>
                <div class="history-col history-col-4">
                    <div class="ovexchange">
                        <span class="fa-gbp-ov"><?php echo $currency_symbol; ?></span>
                        <span class="exchange-nub-two"><?php echo (!empty($redeem_data['redeemed_value'])) ? $redeem_data['redeemed_value'] : 0; ?></span>
                    </div>
                </div>
                <div class="history-col history-col-5">
                    <?php if(isset($redeem_data['charity_id']) && !empty($redeem_data['charity_id'])) { ?>
                        <span class="icon-charity tip-spend" title="<?php echo htmlentities($project_charity['RewardCharity']['title'], ENT_QUOTES, "UTF-8"); ?>"></span>
                    <?php }
                    else if(isset($redeem_data['reward_offer_id']) && !empty($redeem_data['reward_offer_id'])) {
                        $offer_detail = project_offer_detail($redeem_data['reward_offer_id']);
                     ?>
                        <span class="fa fa-tag tip-spend" title="<?php echo htmlspecialchars($offer_detail['RewardOffer']['title']); ?>" style="margin-left: 3px;"></span>
                    <?php }else{ ?>
                        <span class="icon-redemption tip-spend" title="Redemption"></span>
                    <?php } ?>
                </div>
            </div>
            <?php //} ?>
        <?php } ?>
    </div>
    <?php }else{ ?>
        <div class="info-msg">NO HISTORY</div>
    <?php } ?>
</div>

<script type="text/javascript">
    $(function(){
        $('.tip-spend').tooltip({
            placement: 'left',
            container: 'body'
        })
    })
</script>
<?php }else{ ?>
<!-- <div class="info-msg">Select A Project</div> -->
<?php } ?>