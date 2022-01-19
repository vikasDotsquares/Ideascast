<?php
echo $this->Html->css('projects/donut_chart/donut_chart.min');
$logged_user_id = $this->Session->read('Auth.User.id');
$reward_points = user_reward_points($logged_user_id);


$user_notifications = get_notifications($this->Session->read('Auth.User.id'), 'reward');
$notify_flag = false;
$unread_flag = 0;
if(isset($user_notifications) && !empty($user_notifications)){
    $notify_flag = count($user_notifications);
    $unread_counter = arraySearch($user_notifications, 'viewed', '0');
    if(isset($unread_counter) && !empty($unread_counter)){
        $unread_flag = count($unread_counter);
    }
}

?>

<?php
$show_graph = '';
if (user_opt_status($logged_user_id)) {
    $show_graph = '';
}
?>
<style type="text/css">
    .hide-reward-graph {
        display: none !important;
    }
</style>
<li class="dropdown reward-notify-dropmenu">
    <a href="javascript:void(0)"  class="dropdown-toggle tipText drop-icon reward-drop-icon" title="Rewards" data-toggle="dropdown" >
        <span class="nav-icon-all"><i class="icon-size-nav reward-nav"></i>
        <i class="bg-gray reward-count header-counter" <?php if(empty($unread_flag)){ ?> style="display: none;" <?php } ?>><?php echo $unread_flag; ?></i> </span>
    </a>

    <ul class="dropdown-menu reward-dropwon reward-sub-drop">

        <!-- <li class="">
            <div class="total-reward-h">
                <div class="icon-ov"></div> My rewards total
            </div>
        </li>
        <li class="reward-graphs"></li> -->

        <li class="rewards-notify-list" ><!--  -->
            <div class="reward-dropwon-notifications">
                <div class="clear-all-notify">
                    <span class="prj-notify-text text-bold">Reward Notifications</span>
                    <span class="pull-right">
                        <strong>All</strong>
                        <a href="#" class="reward-close-top"><i class="deleteblack"></i></a>
                    </span>
                </div>
                <div class="notify-scrollone"><span class="fake-reward"></span>

                <div class="notify-scrollone notify-wrapper">
                    <?php if($notify_flag){
                        foreach ($user_notifications as $key => $value) {
                            $data = $value['UserNotification'];
                            // pr(date('d M Y', strtotime($data['date_time'])));
                        ?>
                    <div class="notify-listing <?php if($data['viewed'] == 0){ ?> unread<?php } ?>"  data-id="<?php echo $data['id']; ?>">
                        <a class="notify-link" href="<?php echo Router::Url( array( "controller" => "rewards", "action" => "index", 'admin' => FALSE ), true ); ?>">

                            <?php if(isset($data['subject']) && !empty($data['subject'])){ ?>
                                <strong class="title"><?php echo $data['subject']; ?></strong>
                            <?php } ?>

                            <?php if(isset($data['heading']) && !empty($data['heading'])){ ?>
                                <span class="notify-info notify-heading"><?php echo $data['heading']; ?></span>
                            <?php } ?>

                            <?php if(isset($data['sub_heading']) && !empty($data['sub_heading'])){ ?>
                                <span class="notify-info notify-heading"><?php echo $data['sub_heading']; ?></span>
                            <?php } ?>

                            <span class="notify-info">
                                <?php if(isset($data['creator_name']) && !empty($data['creator_name'])){
                                $creator_name = $data['creator_name']; ?>
                                    <?php if($data['created_id'] == $this->Session->read('Auth.User.id') && $data['receiver_id'] == $this->Session->read('Auth.User.id')){
                                        $creator_name = 'Me';
                                    } ?>
                                    <span>By: <?php echo $creator_name; ?>, </span>
                                <?php } ?>
                                <?php if(isset($data['date_time']) && !empty($data['date_time'])){
                                    $socket_date = $socket_country = '';
                                    $socket_date_country = explode("~", $data['date_time']);
                                    if(isset($socket_date_country) && !empty($socket_date_country) && count($socket_date_country) > 1) {
                                        $socket_date = $socket_date_country[0];
                                        $socket_country = (isset($socket_date_country[1])) ? '('.$socket_date_country[1].')' : '';
                                    }
                                    else{
                                        $socket_date = $data['date_time'];
                                    }
                                    ?>
                                    <span>
                                        <?php echo date('d M Y, h:i A', strtotime($socket_date)); ?> <?php echo $socket_country; ?>
                                    </span><?php } ?>
                            </span>
                        </a>
                        <a class="read-bottom <?php if($data['viewed'] > 0){ ?> viewed <?php } ?>" href="#">
                           <i class="fa fa-check"></i>
                        </a>
                        <a class="close-botton" href="#">
                           <i class="deleteblack"></i>
                        </a>
                    </div>
                    <?php }
                    } ?>
                </div>
            </div>
        </li>
    </ul>
</li>