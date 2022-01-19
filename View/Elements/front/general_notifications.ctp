<?php
    $user_notifications = get_notifications($this->Session->read('Auth.User.id'));
    // pr($user_notifications, 1);
    $user_notifications1 = get_prj_del_notifications($this->Session->read('Auth.User.id'));
    $user_notifications = array_merge($user_notifications, $user_notifications1);
    arsort($user_notifications);
    $notify_flag = false;
    $unread_flag = 0;
    if(isset($user_notifications) && !empty($user_notifications)){
        $notify_flag = ( isset($user_notifications) && !empty($user_notifications) ) ? count($user_notifications) : 0;
        $unread_counter = arraySearch($user_notifications, 'viewed', '0');
        if(isset($unread_counter) && !empty($unread_counter)){
            $unread_flag = count($unread_counter);
        }
    }
?>
<li class="dropdown" id="notify-dropmenu">
    <a href="javascript:void(0)" class="dropdown-toggle tipText" title="Notifications" <?php if($notify_flag){ ?> data-toggle="dropdown" <?php } ?> id="notify_bell" >
      <span class="nav-icon-all">  <i class="icon-size-nav notification-nav"></i>
        <i class="bg-gray bell-count header-counter" style="<?php if(empty($unread_flag)){ ?>display: none;<?php } ?>"><?php echo $unread_flag; ?></i></span>
    </a>
    <ul class="dropdown-menu" id="notify-sub-drop">
        <div class="clear-all-notify">
            <span class="prj-notify-text text-bold">Notifications</span>
            <span class="pull-right">
                <strong>All</strong>
                <a href="#" class="close-top"><i class="deleteblack"></i></a>
            </span>
        </div>
        <div class="notify-scrollone"><span class="aaaaaaaa"></span>
        <?php if($notify_flag){
        foreach ($user_notifications as $key => $value) {
            $data = $value['UserNotification'];
        ?>
        <li <?php if($data['viewed'] == 0){ ?> class="unread" <?php } ?> data-id="<?php echo $data['id']; ?>">
            <?php
                // add different pages link
                $notify_url = '';

                    // pr($data['type']);
                if (($data['type'] != '' && ($data['type'] == 'program')) ) {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "lists" , 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '' && $data['type'] == 'interest') ) {
                    $notify_url = Router::Url( array( "controller" => "boards", "action" => "project_request" , 'admin' => FALSE ), true );
                }
                else if ( ($data['type'] != '') && ($data['type'] == 'project_sharing' || $data['type'] == 'project_schedule_change' || $data['type'] == 'project_schedule_overdue' || $data['type'] == 'new_project_member' || $data['type'] == 'project_complete' || $data['type'] == 'workspace_deleted') && (isset($data['project_id']) && !empty($data['project_id']))) {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'admin' => FALSE ), true );
                }
                else if ($data['type'] != '' && $data['type'] == 'project_delete') {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "lists", 'admin' => FALSE ), true );
                }
                else if ($data['type'] != '' && ($data['type'] == 'annotation_add') && (isset($data['project_id']) && !empty($data['project_id'])) ) {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'annotate', 'admin' => FALSE ), true );
                }
                else if ($data['type'] != '' && ($data['type'] == 'rag_update') && (isset($data['project_id']) && !empty($data['project_id'])) ) {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '' && $data['type'] == 'group_request') && (isset($data['project_id']) && !empty($data['project_id']))) {
                    $notify_url = Router::Url( array( "controller" => "shares", "action" => "group_requests", 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && ($data['type'] == 'workspace_sharing' || $data['type'] == 'workspace_schedule_change' || $data['type'] == 'workspace_schedule_overdue' || $data['type'] == 'workspace_sign_off' || $data['type'] == 'task_deleted') && (isset($data['project_id']) && !empty($data['project_id'])) && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "manage_elements", $data['project_id'], $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && ($data['type'] == 'task_sharing' || $data['type'] == 'task_schedule_change' || $data['type'] == 'task_overdue' || $data['type'] == 'task_signoff'  || $data['type'] == 'assignment' || $data['type'] == 'assignment_removed') && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "entities", "action" => "update_element", $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && $data['type'] == 'reminder' ) {
                    $notify_url = Router::Url( array( "controller" => "dashboards", "action" => "task_reminder", 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && ($data['type'] == 'feedback_received') && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "entities", "action" => "update_element", $data['refer_id'], '#' => 'feedbacks', 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && ($data['type'] == 'vote_removed') && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "entities", "action" => "update_element", $data['refer_id'], '#' => 'votes', 'admin' => FALSE ), true );
                }
                else if ( $data['type'] != '' &&  ($data['type'] == 'vote_invitation' || $data['type'] == 'vote_reminder')  && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "entities", "action" => "voting", $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if ( $data['type'] != '' && ($data['type'] == 'feedback_invitation' || $data['type'] == 'feedback_reminder')  && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "entities", "action" => "feedbacks", $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && ($data['type'] == 'wiki_created' || $data['type'] == 'wiki_updated' || $data['type'] == 'wiki_page_request' ) && (isset($data['project_id']) && !empty($data['project_id'])) && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "wikies", "action" => "index", 'project_id' => $data['project_id'],  'wiki' => $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && ($data['type'] == 'blog_created' || $data['type'] == 'blog_updated' ) && (isset($data['project_id']) && !empty($data['project_id'])) && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "team_talks", "action" => "index", 'project' => $data['project_id'],  'blog' => $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if ( ($data['type'] != '' && $data['type'] == 'blog_deleted' ) && (isset($data['project_id']) && !empty($data['project_id'])) ) {
                    $notify_url = Router::Url( array( "controller" => "team_talks", "action" => "index", 'project' => $data['project_id'], 'admin' => FALSE ), true );
                }
                else if ( ($data['type'] != '' && $data['type'] == 'todo_request' ) && (isset($data['refer_id']) && !empty($data['refer_id'])) ) {
                    $notify_url = Router::Url( array( "controller" => "todos", "action" => "tododetails", $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if (($data['type'] != '') && ($data['type'] == 'todo_signoff' || $data['type'] == 'todo_overdue' ) && (isset($data['project_id']) && !empty($data['project_id'])) && (isset($data['refer_id']) && !empty($data['refer_id']))) {
                    $notify_url = Router::Url( array( "controller" => "todos", "action" => "index", 'project' => $data['project_id'],  'dolist_id' => $data['refer_id'], 'admin' => FALSE ), true );
                }
                else if ( ($data['type'] != '' && $data['type'] == 'todo_delete' ) && (isset($data['project_id']) && !empty($data['project_id'])) ) {
                    $notify_url = Router::Url( array( "controller" => "todos", "action" => "index", 'project' => $data['project_id'], 'admin' => FALSE ), true );
                }
                else if ( $data['type'] != '' && ($data['type'] == 'risk_delete' || $data['type'] == 'risk_signedoff' || $data['type'] == 'risk_assignment' || $data['type'] == 'risk_overdue') && (isset($data['project_id']) && !empty($data['project_id'])) ) {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'tab' => 'risk', 'admin' => FALSE ), true );
                }
                else {
                    if (isset($data['project_id']) && !empty($data['project_id'])) {
                        $notify_url = Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'admin' => FALSE ), true );
                    }
                }




            /*if( (isset($data['type']) && $data['type'] == 'task_sharing') && (isset($data['refer_id']) && !empty($data['refer_id'])  ) ) {
                $notify_url = Router::Url( array( "controller" => "entities", "action" => "update_element", $data['refer_id'], 'admin' => FALSE ), true );
            }
            else if( (isset($data['type']) && $data['type'] == 'workspace_sharing') && (isset($data['project_id']) && !empty($data['project_id']))  && (isset($data['refer_id']) && !empty($data['refer_id'])  ) ){
                $notify_url = Router::Url( array( "controller" => "projects", "action" => "manage_elements", $data['project_id'], $data['refer_id'], 'admin' => FALSE ), true );
            }
            else {
                if(isset($data['project_id']) && !empty($data['project_id'])) {
                    $notify_url = Router::Url( array( "controller" => "projects", "action" => "index", $data['project_id'], 'admin' => FALSE ), true );
                }
            }*/
            ?>
            <a href="<?php echo $notify_url; ?>" class="notify-link">

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

            <a class="read-bottom <?php if($data['viewed'] > 0){ ?> viewed <?php } ?> " href="#">
               <i class="fa fa-check"></i>
            </a>
            <a class="close-bottom" href="#">
               <i class="deleteblack"></i>
            </a>
        </li>
        <?php }
        } ?>

        </div>
    </ul>
</li>