<?php

$current_user_id = $this->Session->read('Auth.User.id');
/*$opt_reward = false;
$opt_reward_table = false;
if(isset($optData) && !empty($optData)) {
    $opt_reward = (isset($optData['reward_opt_status']) && !empty($optData['reward_opt_status'])) ? true : false;
    $opt_reward_table = (isset($optData['reward_table_opt_status']) && !empty($optData['reward_table_opt_status'])) ? true : false;
}*/

// $opt_reward = user_opt_status($user_id);
// $opt_reward_table = user_table_opt_status($user_id);
$opt_reward = $optData['reward_opt_status'];
$opt_reward_table = $optData['reward_table_opt_status'];

$disabled = ($user_id != $current_user_id) ? 'not-editable' : '';
 ?>

<div class="col-opt">
    <div class="txt-light-grn">OV Rewards</div>
    <div class="parent-wrap <?php echo $disabled; ?>">
        <label class="opt-label" for="chk_opt_reward">Opt In:</label> <input type="checkbox" name="chk_opt_reward" id="chk_opt_reward" <?php if($opt_reward) echo 'checked="true"'; ?>>
        <span class="loader-icon fa fa-spinner fa-pulse stop"></span>
    </div>
</div>
<div class="col-opt">
    <div class="txt-light-grn">OV Reward Tables</div>
    <div class="parent-wrap  <?php echo $disabled; ?> <?php if(!$opt_reward){ ?>not-editable<?php } ?>">
        <label class="opt-label" for="chk_opt_reward_table">Opt In:</label> <input type="checkbox" name="chk_opt_reward_table" id="chk_opt_reward_table" <?php if($opt_reward_table) echo 'checked="true"'; ?>>
        <span class="loader-icon fa fa-spinner fa-pulse stop"></span>
    </div>
</div>