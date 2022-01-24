<?php
if(isset($project_id) && !empty($project_id)) {
    $current_user_id = $this->Session->read('Auth.User.id');
    $shopping_data = user_offer_shopping($current_user_id, $project_id);
    $currency_symbol = project_currency_symbols($project_id);
    if($shopping_data) {
 ?>
 <fieldset class="Fieldset list-outer">
    <legend class="Legend" id="Legend">My Past Buys</legend>

<!-- <div class="border-top list-outer"> -->
<div class="list-group project-offers shopping-list clearfix">
    <?php foreach ($shopping_data as $key => $value) {
        $data = $value['RewardOfferShop'];
        $project_offer_detail = project_offer_detail($data['offer_id']);
        $offer_detail = $project_offer_detail['RewardOffer'];
        $updated_by = '';
        $updated_user = user_full_name($offer_detail['creator_id']);
        $updated_by .= $updated_user;
    ?>
    <div class="list-group-item" data-offer="<?php echo $data['id']; ?>">
        <h4 class="list-group-item-heading"><?php  echo htmlentities($offer_detail['title'], ENT_QUOTES, "UTF-8"); ?></h4>
        <div class="list-group-item-text clearfix">
            <span class="text-one">Spent: <?php echo $currency_symbol; ?><?php echo $offer_detail['amount']; ?></span>
            <span class="text-two">Spent On: <?php echo $this->Wiki->_displayDate(date('Y-m-d h:i A',strtotime($data['created'])), $format = 'd M Y g:i A'); ?></span>
        </div>
        <div class="list-group-item-text clearfix">
            <span class="text-one">Notification To: <?php echo $updated_by; ?></span>
            <span class="text-two">Received:
                <span class="popup_setting_wrap">
                    <input type="checkbox" id="received_yes_<?php echo $key; ?>" name="received_yes_<?php echo $key; ?>" class="fancy_input received_y_n" value="1" <?php if(isset($data['received']) && !empty($data['received'])){ ?> checked="checked" <?php } ?>>
                    <label class="fancy_label text-black" style="font-weight: 700; height: 27px;" for="received_yes_<?php echo $key; ?>">&nbsp;</label>
                </span>
                <i class="fa fa-spinner fa-pulse loader-icon stop"></i>
            </span>
        </div>
    </div>
    <?php } // end foreach ?>
</div>
<!-- </div> -->
</fieldset>
<?php } // if($shopping_data) { ?>
<script type="text/javascript">
    $(function(){
        $('.received_y_n').change(function(event) {
            $.reload_data = true;
            var checked = $(this).prop('checked'),
                $parent = $(this).parents('.list-group-item'),
                data = $parent.data(),
                offer_id = data.offer;

            var $loader = $parent.find('.loader-icon');
            $loader.loader(1);

            var received = (checked) ? 1 : 0;
            $.ajax({
                url: $js_config.base_url + 'rewards/offer_received_status',
                type: 'POST',
                dataType: 'json',
                data: {offer_id: offer_id, received: received},
                success: function(response) {
                    $loader.loader(0);
                }
            });
        });
    })
</script>
<?php } ?>
<style type="text/css">
    .text-one {
        float: left;
        width: 50%;
        padding-top: 3px;
    }
    .text-two {
        float: left;
        width: 50%;
    }
    .Fieldset
    {
        border-top: 1px solid #CCC;
        margin: 0 !important;
    }
    .Legend
    {
        border: medium none;
        margin-left: 40%;
        padding: 0 10px;
        position: relative;
        text-align: center;
        width: auto;
        font-size: 13px;
        margin-bottom: 0;
    }
</style>