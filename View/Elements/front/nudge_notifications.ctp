<?php
    $user_notifications = get_nudge_notifications($this->Session->read('Auth.User.id'), 'nudge');
    // pr($user_notifications, 1);
    // $user_notifications1 = get_prj_del_notifications($this->Session->read('Auth.User.id'));
    // $user_notifications = array_merge($user_notifications, $user_notifications1);
    // arsort($user_notifications);
    $notify_flag = false;
    $unread_flag = 0;
    if(isset($user_notifications) && !empty($user_notifications)){
        $notify_flag = ( isset($user_notifications) && !empty($user_notifications) ) ? count($user_notifications) : 0;
        $unread_counter = arraySearch($user_notifications, 'viewed', '0');
        if(isset($unread_counter) && !empty($unread_counter)){
            $unread_flag = count($unread_counter);
        }
    }
    // pr($user_notifications);
?>

<li class="dropdown nudge-notifications-menu" id="nudge-dropmenu" >
    <a <?php if($notify_flag){ ?> href="javascript:void(0)" data-toggle="dropdown" <?php }else{ ?> href="<?php echo Router::Url( array( "controller" => "boards", "action" => "nudge_list", 'admin' => FALSE ), true ); ?>" <?php } ?> id="nudge_menu" class="tipText" title="Nudges" class="dropdown-toggle tipText" >
        <span class="nav-icon-all nudge_icons">
            <i class="icon-size-nav h-icon-nudge"></i>
            <i class="bg-gray counter header-counter nudge-counter" style="<?php if(empty($unread_flag)){ ?>display: none;<?php } ?>"><?php echo $unread_flag; ?></i>
        </span>
    </a>
    <ul class="dropdown-menu nudge-sub-drop" id="nudge-sub-drop">
        <div class="clear-all-nudge-notify">
            <span class="prj-notify-text text-bold">Nudge Notifications</span>
            <span class="pull-right">
                <strong>All</strong>
                <a href="#" class="close-top"><i class="deleteblack"></i></a>
            </span>
        </div>
        <div class="notify-scrollone nudge-notify-wrapper"><span class="nudge-fake"></span>
        <?php if($notify_flag){
            foreach ($user_notifications as $key => $value) {
                $data = $value['UserNotification'];
        ?>
        <li  class="<?php if($data['viewed'] == 0){ ?>unread <?php } ?> nudge-notify-list" data-id="<?php echo $data['id']; ?>">
            <?php $notify_url = ''; ?>
            <a href="<?php echo Router::Url( array( "controller" => "boards", "action" => "nudge_list", 'admin' => FALSE ), true ); ?>" class="notify-link">

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
                    $creator_name ='';// $data['creator_name']; ?>
                        <?php if($data['created_id'] == $this->Session->read('Auth.User.id') && $data['receiver_id'] == $this->Session->read('Auth.User.id')){
                            //$creator_name = 'Me';
                        } ?>
                        <span>Sent: <?php echo $creator_name; ?> </span>
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
            <a class="read-bottom <?php if($data['viewed'] > 0){ ?> viewed <?php } ?> " href="#">
               <i class="fa fa-check"></i>
            </a>
            <a class="close-bottom" href="#">
               <i class="deleteblack"></i>
            </a>
        </li>
        <?php } ?>
        <?php } ?>
    </ul>
</li>






