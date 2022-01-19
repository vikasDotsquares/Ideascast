<?php
$logged_user_id = $this->Session->read('Auth.User.id');
$reward_points = user_reward_points($logged_user_id);
$opt_status = user_opt_status($logged_user_id);
if($opt_status){

?>

<div class="total-reward-center">
    <div class="total-reward-center-text">
        <div class="c100 p<?php echo (isset($reward_points['total_earned']) && !empty($reward_points['total_earned'])) ? '100' : '0' ?> green-bar c-xl mid notify-circle" style="cursor: pointer;">
            <span></span>
            <div class="slice" style="cursor: pointer;">
                <div class="bar" style="cursor: pointer;"></div>
                <div class="fill" style="cursor: pointer;"></div>
            </div>
                <div class="nub-status txt-grn" style="cursor: pointer;"><?php echo $reward_points['total_earned']; ?></div>
        </div>
        <h6>Achieved</h6>
    </div>
    <div class="total-reward-center-text">
        <div class="c100 p<?php echo (isset($reward_points['redeem_percent']) && !empty($reward_points['redeem_percent'])) ? $reward_points['redeem_percent'] : '0' ?> purple-bar c-xl mid notify-circle" style="cursor: pointer;">
            <span></span>
            <div class="slice" style="cursor: pointer;">
                <div class="bar" style="cursor: pointer;"></div>
                <div class="fill" style="cursor: pointer;"></div>
            </div>
                <div class="nub-status txt-prpl" style="cursor: pointer;"><?php echo $reward_points['total_redeem']; ?></div>
        </div>
        <h6>Redeemed</h6>
    </div>
    <div class="total-reward-center-text">
        <div class="c100 p<?php echo (isset($reward_points['remain_percent']) && !empty($reward_points['remain_percent'])) ? $reward_points['remain_percent'] : '0' ?> c-xl mid notify-circle" style="cursor: pointer;">
            <span></span>
            <div class="slice" style="cursor: pointer;">
                <div class="bar" style="cursor: pointer;"></div>
                <div class="fill" style="cursor: pointer;"></div>
            </div>
                <div class="nub-status txt-red" style="cursor: pointer;"><?php echo $reward_points['total_remaining']; ?></div>
        </div>
        <h6>Available</h6>
    </div>
</div>
<?php }else{ ?>

<div class="total-reward-center">
    <div class="total-reward-center-text">
        <div class="c100 p0 green-bar c-xl mid notify-circle" style="cursor: pointer;">
            <span></span>
            <div class="slice" style="cursor: pointer;">
                <div class="bar" style="cursor: pointer;"></div>
                <div class="fill" style="cursor: pointer;"></div>
            </div>
                <div class="nub-status txt-grn" style="cursor: pointer;">0</div>
        </div>
        <h6>Achieved</h6>
    </div>
    <div class="total-reward-center-text">
        <div class="c100 p0 purple-bar c-xl mid notify-circle" style="cursor: pointer;">
            <span></span>
            <div class="slice" style="cursor: pointer;">
                <div class="bar" style="cursor: pointer;"></div>
                <div class="fill" style="cursor: pointer;"></div>
            </div>
                <div class="nub-status txt-prpl" style="cursor: pointer;">0</div>
        </div>
        <h6>Redeemed</h6>
    </div>
    <div class="total-reward-center-text">
        <div class="c100 p0 c-xl mid notify-circle" style="cursor: pointer;">
            <span></span>
            <div class="slice" style="cursor: pointer;">
                <div class="bar" style="cursor: pointer;"></div>
                <div class="fill" style="cursor: pointer;"></div>
            </div>
                <div class="nub-status txt-red" style="cursor: pointer;">0</div>
        </div>
        <h6>Available</h6>
    </div>
</div>
<?php } ?>
<script type="text/javascript">
    $(function(){
        $('.total-reward-center-text').on('click', function(event) {
            event.preventDefault();
            if(window.location.href.indexOf("rewards") <= 0) {
                location.href = $js_config.base_url + 'rewards';
            }
        });
    })
</script>
<style type="text/css">
    .total-reward-center-text {
        cursor: pointer;
    }
    .notify-circle {
        float: none;
        display: inline-block;
    }
    @media (max-width:1600px) {
        .ov-table-list .col-ov-4 {
                width: 14%;
            }
            .ov-table-list .col-ov-6 {
            width: 28%;
        }
    }
    @media (max-width:1460px) {
        .c100.mid {
            font-size: 60px;
        }

        .ov-achieved-cont {
            padding-right: 65px;
        }
    }

    @media (max-width:1400px) {
        .c100.mid {
            font-size: 55px;
        }

        .ov-achieved-cont {
            padding-right: 60px;
        }

        .colume-1, .colume-2 {
            width: 20%;
        }
        .colume-3 {
            width: 60%;
        }


    }

    @media (max-width:1365px) {
        .ov-project-col {
            width: 50%;
        }

        .border-right3 {
            border-right: 1px solid #ccc;
        }

        .border-right2 {
            border-right: none;
            border-bottom: 1px solid #ccc;
        }

        .border-right1 {
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }

    .ov-table-list .col-ov-4 {
        width: 15%;
    }
    .ov-table-list .col-ov-6 {
        width: 27%;
    }

    }

    @media (max-width:1199px) {
        .colume-1, .colume-2 {
            width: 27%;
        }
        .colume-3 {
            width: 46%;
        }
        .ov-table-list {
            overflow: auto;
        }
          .ov-table-list .table {
            min-width: 870px;
        }
    .ov-table-list .table-fixed thead {
        padding-right: 0;
    }
    }

    @media (max-width:991px) {
        .ov-project-col {
            width: 100%;
        }

        .border-right1,
        .border-right2,
        .border-right3 {
            border-bottom: 1px solid #ccc;
            border-right: none;
        }

        .ov-project-status-in {
            min-height: 60px;
        }


        .colume-1, .colume-2 {
            width: 50%;
        }
        .colume-3 {
            width: 100%;
        }
        .project-thumb-slider {
            padding-top: 20px;
            border-top: 1px solid #ccc;
            margin-bottom: 15px;
        }
        .box .colume-2.border-right {
            border-right: none !important;
        }

    }

    @media (max-width:479px) {
        .c100.mid {
            font-size: 50px;
        }

        .ov-achieved-cont {
            padding-right: 55px;
            font-size: 13px;
        }
        .colume-1, .colume-2 {
            width: 100%;

        }
        .box .colume-1.border-right {
            border-right: none !important;
            border-bottom: 1px solid #ccc !important;
        }
    }
</style>