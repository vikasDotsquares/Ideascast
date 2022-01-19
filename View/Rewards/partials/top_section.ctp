<?php
$current_user_id = $this->Session->read('Auth.User.id');
$html = '';
if( $user_id != $current_user_id ) {
    $html = CHATHTML($user_id, null);
}
$userDetail = $this->ViewModel->get_user_data($user_id, -1, 'taskcenter');
$user_image = SITEURL . 'images/placeholders/user/user_1.png';
$user_name = 'Not Available';
$job_title = 'Not Available';
if(isset($userDetail) && !empty($userDetail)) {
	$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
	$profile_pic = $userDetail['UserDetail']['profile_pic'];
	$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

    if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
        $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
    }
}
?>
<div class="top-cols top-cols-1 user-project-section">
    <div class="col-sec-img">
        <a href="#"
            class="pophover-popup user-image"
            data-toggle="modal"
            data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true ); ?>"
            data-target="#popup_modal"  data-user="<?php echo $user_id; ?>"
            data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
            <img src="<?php echo $user_image; ?>" >
        </a>
    </div>
    <?php


    $own_projects = $shr_projects = 0;
    if($current_user_id != $user_id) {
        $inter = array_intersect($user_projects, $my_projects);
        if(isset($inter) && !empty($inter)) {
            // pr($inter);
            foreach ($inter as $key => $value) {
                // $ProjectPermit = $this->ViewModel->projectPermitType($key, $current_user_id);
                $projectRole = projectRole($key, $user_id);
                // pr($projectRole, 1);
                if($projectRole == 'Creator' || $projectRole == 'Owner' || $projectRole == 'Group Owner') {
                    $own_projects += 1;
                }
                else{
                    $shr_projects += 1;
                }

            }
        }
    }
    else{
        // $own_projects = (isset($ownerSharer['owner']) && !empty($ownerSharer['owner'])) ? count($ownerSharer['owner']) : 0;
        // $shr_projects = (isset($ownerSharer['sharer']) && !empty($ownerSharer['sharer'])) ? count($ownerSharer['sharer']) : 0;

        $own_projects = projectOwnerSharerTotal(1, $current_user_id);
        $shr_projects = projectOwnerSharerTotal(null, $current_user_id);
    }
    $total = $own_projects + $shr_projects;

    ?>
    <div class="col-sec-detail">
        <div class="pdata <?php if(empty($total)) { ?>no-pointer<?php }else{ ?> show-hide-projects <?php } ?>" data-target="all">
            <span>Projects: </span><span><?php echo $total; ?></span>
        </div>
        <div class="pdata txt-light-grn <?php if(empty($own_projects)) { ?>no-pointer<?php }else{ ?> show-hide-projects <?php } ?>" data-target="owner">
            <span>Owner: </span><span><?php echo $own_projects; ?></span>
        </div>
        <div class="pdata <?php if(empty($shr_projects)) { ?>no-pointer<?php }else{ ?> show-hide-projects <?php } ?>" data-target="sharer" >
            <span>Sharer: </span><span><?php echo $shr_projects; ?></span>
        </div>
    </div>
</div>

<!-- user opt setting -->
<?php
$opt_reward = $optData['reward_opt_status'];
$opt_reward_table = $optData['reward_table_opt_status'];

$disabled = ($user_id != $current_user_id) ? 'not-editable' : '';
 ?>
<div class="top-cols top-cols-2 user-opt-section">
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
</div>

<!-- user earned points -->
<div class="top-cols top-cols-3 user-earned-section">
<?php
$total_earned = 0;
$total_redeem = 0;
$total_remaining = 0;
if($opt_reward) {

    // get other selected users owner level project's data.
    // if user is other than current logged in user.
    // otherwise get logged in user's all data.
    if($current_user_id != $user_id) {
        $inter = array_intersect($user_projects, $my_projects);

        if(isset($inter) && !empty($inter)) {
            foreach ($inter as $key => $value) {
                $ProjectPermit = $this->ViewModel->projectPermitType($key, $current_user_id);

                if($ProjectPermit) {

                    $user_epoints = user_reward_assignments($user_id, $key);
                    if($user_epoints) {
                        foreach ($user_epoints as $rdKey => $rdVal) {
                            $total_earned += $rdVal['RewardAssignment']['allocated_rewards'];
                        }
                    }

                    $user_rpoints = user_redeemed_data($user_id, $key);
                    if($user_rpoints) {
                        foreach ($user_rpoints as $rdKey => $rdVal) {
                            $total_redeem += $rdVal['RewardRedeem']['redeem_amount'];
                        }
                    }


                    $project_accelerated_points = project_accelerated_points($key, $user_id);

                    if($project_accelerated_points){
                        $total_earned += $project_accelerated_points;
                    }
                }
            }
        }
    }
    else{

        // Total points earned by this user in all projects.
        $user_earned_points = user_reward_assignments($user_id);
        // Total points redeemed by this user in all projects.
        $user_redeemed_points = user_redeemed_data($user_id);


        if($user_earned_points) {
            foreach ($user_earned_points as $rdKey => $rdVal) {
                $total_earned += $rdVal['RewardAssignment']['allocated_rewards'];
            }
        }


        if($user_redeemed_points) {
            foreach ($user_redeemed_points as $rdKey => $rdVal) {
                $total_redeem += $rdVal['RewardRedeem']['redeem_amount'];
            }
        }

        $user_accelerated_points = user_accelerated_points( $user_id );

        if($user_accelerated_points){
            $total_earned += $user_accelerated_points;
        }
    }


    $total_remaining = $total_earned - $total_redeem;

    $total_remaining_percent = $total_redeem_percent = 0;
    if( (isset($total_earned) && !empty($total_earned)) && (isset($total_redeem) && !empty($total_redeem)) ) {
        $total_redeem_percent = ($total_redeem / $total_earned) * 100;
    }
    if( (isset($total_earned) && !empty($total_earned)) && (isset($total_remaining) && !empty($total_remaining)) ) {
        $total_remaining_percent = ($total_remaining / $total_earned) * 100;
    }
?>

    <div class="col-section">
        <div class="total-numbers txt-grn"><?php echo (isset($total_earned) && !empty($total_earned)) ? $total_earned : '0' ?></div>
        <div class="total-text">Total <br> Achieved</div>
    </div>
    <div class="col-section">
        <div class="rew-right">
            <div class="c100 p<?php echo (isset($total_earned) && !empty($total_earned)) ? '100' : '0' ?> green-bar c-xl large">
                <span></span>
                <div class="icon-ov"></div>
                <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div>


<!-- user redeem points -->
<?php

if($opt_reward) {
?>
<div class="top-cols top-cols-4 user-redeem-section">
    <div class="col-section">
        <div class="total-numbers txt-prpl"><?php echo (isset($total_redeem) && !empty($total_redeem)) ? $total_redeem : '0' ?></div>
        <div class="total-text">Total <br> Redeemed</div>
    </div>
    <div class="col-section">
        <div class="rew-right">
            <div class="c100 p<?php echo round($total_redeem_percent); ?> purple-bar c-xl large">
                <span></span>
                <div class="icon-ov"></div>
                <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>


<!-- user remaining points -->
<?php

if($opt_reward) {
?>
<div class="top-cols top-cols-5 user-remaining-section">
    <div class="col-section">
        <div class="total-numbers txt-red"><?php echo (isset($total_remaining) && !empty($total_remaining)) ? $total_remaining : '0' ?></div>
        <div class="total-text">Total <br> Available</div>
    </div>
    <div class="col-section">
        <div class="rew-right">
            <div class="c100 p<?php echo round($total_remaining_percent); ?> c-xl large">
                <span></span>
                <div class="icon-ov"></div>
                <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>


<script type="text/javascript">
    $(function(){
/*         $('.pophover-popup').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        }); */
    })
</script>
<style type="text/css">
    .no-pointer {
        cursor: default !important;
    }
</style>