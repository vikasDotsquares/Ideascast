
<?php
$current_user_id = $this->Session->read('Auth.User.id');

$project_rewards = project_reward_assignments($project_id, $user_id);
$by_acclerate = project_accelerated_points($project_id, $user_id);

$total_rewarded = 0;
if($project_rewards) {
    foreach ($project_rewards as $key => $value) {
        $data = $value['RewardAssignment'];
        $amount = $data['allocated_rewards'];

        $total_rewarded += $amount;
    }
}

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">OV Rewards</h3>
</div>
<div class="modal-body clearfix">
    <div class="rows">
        <div class="given-rewards-outer">
            <div class="given-header-outer row">
                <?php $user_chat_popover = user_chat_popover($user_id, $project_id); ?>
                <div class="col-sm-4 nopadding-right">
                    <div class="image-total">
                        <span class="image-outer">
                            <img class="user-image" src="<?php echo $user_chat_popover['user_image']; ?>" />
                        </span>
                        <span class="digits-outer">
                            <span class="user-total"><?php echo $total_rewarded; ?></span>
                            <span class="acceletare-total txt-ong"><span style="font-size: 11px;">Accelerations</span><br /><?php echo $by_acclerate; ?></span>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4 reward-title">Received from<br />Members</div>
                <div class="col-sm-4 reward-title">Given by<br />Me</div>
            </div>
            <div class="given-detail-outer row">
                <?php foreach ($reward_types as $type => $title) { ?>
                    <div class="detail-row">
                        <div class="col-sm-4 reward-type"><?php echo $title ; ?></div>
                        <div class="col-sm-4 reward-amount">
                            <?php
                            $project_rewards = project_reward_assignments($project_id, $user_id, $type);
                            $total_rewarded = 0;
                            $given_popover = [];
                            if($project_rewards) {
                                foreach ($project_rewards as $key => $value) {
                                    $data = $value['RewardAssignment'];
                                    $given_by = $data['given_by'];
                                    $amount = $data['allocated_rewards'];
                                    if($given_by != $current_user_id){
                                        $total_rewarded += $amount;
                                        if(isset($given_popover[$given_by]) && !empty($given_popover[$given_by])) {
                                            $given_popover[$given_by] += $amount;
                                        }
                                        else{
                                            $given_popover[$given_by] = $amount;
                                        }
                                    }
                                }
                            }
                            $add_popover = false;
                            $detail_popover = '';
                            if(isset($given_popover) && !empty($given_popover)) {
                                $add_popover = true;
                                foreach ($given_popover as $user => $amnt) {
                                    $user_chat_popover = user_chat_popover($user);
                                    $detail_popover .= '<span class="user-amount">'.$user_chat_popover['user_name'].': <strong>'.$amnt.'</strong></span>';
                                }
                            }
                            ?>
                                <span class="detail-popover " <?php if($add_popover){ ?> style="cursor: pointer;" data-content='<?php echo $detail_popover; ?>' title="<?php echo $title; ?>" <?php } ?>><?php echo $total_rewarded; ?></span>
                            <?php
                            ?>
                        </div>
                        <div class="col-sm-4 reward-amount">
                            <?php
                            $project_rewards = project_reward_assignments($project_id, $user_id, $type);
                            $total_rewarded = 0;
                            if($project_rewards) {
                                foreach ($project_rewards as $key => $value) {
                                    $data = $value['RewardAssignment'];
                                    $given_by = $data['given_by'];
                                    $amount = $data['allocated_rewards'];
                                    if($given_by == $current_user_id){
                                        $total_rewarded += $amount;
                                    }
                                }
                            }
                            echo $total_rewarded;
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<script type="text/javascript">
    $(function(){
        
        $('.detail-popover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            /*delay: {show: 50, hide: 400}*/
        });
    })
</script>
<style type="text/css">
    .detail-popover {
        /*cursor: pointer;*/
    }
    .user-amount {
        display: block;
        padding-top: 2px;
        font-size: 13px;
    }
    .user-amount strong {
        font-weight: 600;
    }
    .given-rewards-outer {
        border: 1px solid #ccc;
        padding: 10px 15px 0 15px;

    }
    .given-header-outer {
        padding: 10px 0;
    }
    .image-total {
        /*display: flex;
        align-items:center;*/
        display: inline-block;
    }
    .image-outer {
        display: inline-block;
        margin: 0 2px 0 0;
    }
    .user-image {
        width: 50px;
        vertical-align: middle;
    }
    .digits-outer {
        display: inline-block;
        font-size: 13px;
    }
    .user-total {
        font-weight: 600;
        color: #00ad48;
        text-align: left;
        display: block;
        font-size: 16px;
    }
    .acceletare-total {
        font-weight: 600;
        text-align: left;
        display: block;
    }
    .given-header-outer .reward-title {
        text-align: center;
        font-weight: bold;
    }
    .detail-row {
        display: inline-block;
        width: 100%;
        padding: 5px 0;
        border-bottom: 1px solid #ccc;
    }
    .detail-row:last-child {
        border: none;
    }
    .detail-row .reward-type {
        font-weight: 600;
    }
    .detail-row .reward-amount {
        font-weight: 600;
        color: #00ad48;
        text-align: center;
    }
</style>