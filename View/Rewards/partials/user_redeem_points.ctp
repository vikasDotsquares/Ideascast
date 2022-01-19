<?php

$logged_user_id = $this->Session->read('Auth.User.id');
$current_user_id = $user_id;

$user_opt_status = user_opt_status($current_user_id);
if($user_opt_status) {

    $total_earned = 0;
    $total_redeem = 0;
    // get other selected users owner level project's data.
    // if user is other than current logged in user.
    // otherwise get logged in user's all data.
    if($logged_user_id != $user_id) {
        $inter = array_intersect($user_projects, $my_projects);

        if(isset($inter) && !empty($inter)) {
            foreach ($inter as $key => $value) {
                $ProjectPermit = $this->ViewModel->projectPermitType($key, $logged_user_id);

                if($ProjectPermit) {

                    $user_epoints = user_reward_assignments($current_user_id, $key);
                    if($user_epoints) {
                        foreach ($user_epoints as $rdKey => $rdVal) {
                            $total_earned += $rdVal['RewardAssignment']['allocated_rewards'];
                        }
                    }

                    $user_rpoints = user_redeemed_data($current_user_id, $key);
                    if($user_rpoints) {
                        foreach ($user_rpoints as $rdKey => $rdVal) {
                            $total_redeem += $rdVal['RewardRedeem']['redeem_amount'];
                        }
                    }

                    $project_accelerated_points = project_accelerated_points($key, $current_user_id);

                    if($project_accelerated_points){
                        $total_earned += $project_accelerated_points;
                    }
                }
            }
        }
    }
    else{

        // Total points earned by this user in all projects.
        $user_earned_points = user_reward_assignments($current_user_id);
        // Total points redeemed by this user in all projects.
        $user_redeemed_points = user_redeemed_data($current_user_id);


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

        $user_accelerated_points = user_accelerated_points( $current_user_id );

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
<?php } ?>