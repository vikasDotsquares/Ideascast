<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>

<?php
	$userID = null;
	if( isset($userId) && !empty($userId) ) {
		$userID = $userId;
	}

	$project_workspaces = get_project_workspace($project, TRUE);

	$links_activities = $notes_activities = $documents_activities = $mindmaps_activities = $feedback_activities = $votes_activities = $activities = null;
	$all_activities = 0;
	if( isset($project_workspaces) && !empty($project_workspaces) ) {

		$project_workspaces = array_keys($project_workspaces);

		if($section == 'notes'){
			$notes_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'element_notes', $userID);
		}
		else if($section == 'docs'){
			$documents_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'element_documents', $userID);
			// pr($documents_activities);
		}
		else if($section == 'mindmaps'){
			$mindmaps_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'element_mindmaps', $userID);
		}
		else if($section == 'feedbacks'){
			$feedback_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'feedback', $userID);
		}
		else if($section == 'votes'){
			$votes_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'votes', $userID);
		}
	}
?>

 <?php
/* PROJECT AND GROUP PERMISSIONS */

	$user_id = $this->Session->read('Auth.User.id');

	$project_id = $this->request->data['id'];


/* PROJECT AND GROUP PERMISSIONS */

 ?>
<?php if($section == 'notes'){ ?>

		<?php

			if( isset($notes_activities) && !empty($notes_activities)) {
				foreach( $notes_activities as $key => $val ) {
					$row = $val['Activity'];

					$type = $row['element_type'];
					$db_id = $row['relation_id'];
					$user_id = $row['updated_user_id'];
					$message = $row['message'];

					$db_data = getByDbId('ElementNote', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementNote'] : null;
					if( isset($db_data) && !empty($db_data)) {
						$element_id = $db_data['element_id'];
						$dbe_data = getByDbId('Element', $element_id);
						if( isset($dbe_data) && !empty($dbe_data))  {

						$hash_tag = '#notes';

						$userDetail = $this->ViewModel->get_user_data( $user_id, -1 );
						// $userDetail = getByDbId('UserDetail', $user_id);
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						$html = '';
						if( $user_id != $current_user_id ) {
							$html = CHATHTML($user_id, $project);
						}
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
				?>
				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<?php


				$e_permission = $this->ViewModel->getTaskPermission($element_id);

				if((isset($e_permission['0']['user_permissions']['element_id'] ) && !empty($e_permission['0']['user_permissions']['element_id'] )) ) {
		?>

						<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $element_id, 'admin' => FALSE ), TRUE ); ?><?php echo $hash_tag; ?>" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }else{ ?>
		<a href="javascript:void(0)" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }  ?>
						</div>

						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['updated']); ?></span>

					</div>
				</div>
				<?php } }
				}
			}
			else {
			?>
			<div class="no-row-found" >No Notes Activity</div>
		<?php } ?>

<?php }else if($section == 'docs'){ ?>

		<?php

			if( isset($documents_activities) && !empty($documents_activities)) {
				foreach( $documents_activities as $key => $val ) {
					$row = $val['Activity'];

					$type = $row['element_type'];
					$db_id = $row['relation_id'];
					$user_id = $row['updated_user_id'];
					$message = $row['message'];


					$db_data = getByDbId('ElementDocument', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementDocument'] : null;
					if( isset($db_data) && !empty($db_data)) {
						$element_id = $db_data['element_id'];
						$dbe_data = getByDbId('Element', $element_id);
						if( isset($dbe_data) && !empty($dbe_data))  {

						$hash_tag = '#documents';

						$userDetail = $this->ViewModel->get_user_data( $user_id, -1 );
						// $userDetail = getByDbId('UserDetail', $user_id);
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						$html = '';
						if( $user_id != $current_user_id ) {
							$html = CHATHTML($user_id, $project);
						}
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
				?>
				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<?php
			$e_permission = $this->Common->element_share_permission($element_id, $project_id,$this->Session->read('Auth.User.id'));

			if(isset($grp_id) && !empty($grp_id)){

				if((isset($grp_id) && !empty($grp_id))){

					if(isset($e_permission) && !empty($e_permission)){
						$e_permissions =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
						$e_permission = array_merge($e_permission,$e_permissions);
					}else{
						$e_permission =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
					}
				}
			}


			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1)  || (isset($e_permission) && !empty($e_permission))  ) {
		?>

						<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $element_id, 'admin' => FALSE ), TRUE ); ?><?php echo $hash_tag; ?>" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }else{ ?>
		<a href="javascript:void(0)" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }  ?>
						</div>

						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['updated']); ?></span>

					</div>
				</div>
				<?php } }
				}
			}
			else {
			?>
			<div class="no-row-found" >No Document Activity</div>
		<?php } ?>

<?php }else if($section == 'mindmaps'){ ?>

		<?php

			if( isset($mindmaps_activities) && !empty($mindmaps_activities)) {
				foreach( $mindmaps_activities as $key => $val ) {
					$row = $val['Activity'];

					$type = $row['element_type'];
					$db_id = $row['relation_id'];
					$user_id = $row['updated_user_id'];
					$message = $row['message'];

					$db_data = getByDbId('ElementMindmap', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementMindmap'] : null;
					if( isset($db_data) && !empty($db_data)) {
						$element_id = $db_data['element_id'];
						$dbe_data = getByDbId('Element', $element_id);
						if( isset($dbe_data) && !empty($dbe_data))  {

						$hash_tag = '#mind_maps';

						$userDetail = $this->ViewModel->get_user_data( $user_id, -1 );
						// $userDetail = getByDbId('UserDetail', $user_id);
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						$html = '';
						if( $user_id != $current_user_id ) {
							$html = CHATHTML($user_id, $project);
						}
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
				?>
				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<?php
			$e_permission = $this->Common->element_share_permission($element_id, $project_id,$this->Session->read('Auth.User.id'));

			if(isset($grp_id) && !empty($grp_id)){

				if((isset($grp_id) && !empty($grp_id))){

					if(isset($e_permission) && !empty($e_permission)){
						$e_permissions =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
						$e_permission = array_merge($e_permission,$e_permissions);
					}else{
						$e_permission =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
					}
				}
			}


			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1)  || (isset($e_permission) && !empty($e_permission))  ) {
		?>

						<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $element_id, 'admin' => FALSE ), TRUE ); ?><?php echo $hash_tag; ?>" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }else{ ?>
		<a href="javascript:void(0)" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }  ?>
						</div>

						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['updated']); ?></span>

					</div>
				</div>
				<?php } }
				}
			}
			else {
			?>
			<div class="no-row-found" >No Mindmap Activity</div>
		<?php } ?>

<?php }else if($section == 'feedbacks' ){ ?>

		<?php

			if( isset($feedback_activities) && !empty($feedback_activities)) {
				foreach( $feedback_activities as $key => $val ) {
					$row = $val['Activity'];

					$type = $row['element_type'];
					$db_id = $row['relation_id'];
					$user_id = $row['updated_user_id'];
					$message = $row['message'];

					$db_data = getByDbId('Feedback', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['Feedback'] : null;
					if( isset($db_data) && !empty($db_data)) {

						$element_id = $db_data['element_id'];
						$dbe_data = getByDbId('Element', $element_id);
						if( isset($dbe_data) && !empty($dbe_data))  {

					$hash_tag = '#feedbacks';

					$userDetail = $this->ViewModel->get_user_data( $user_id, -1 );
					// $userDetail = getByDbId('UserDetail', $user_id);
					$user_image = SITEURL . 'images/placeholders/user/user_1.png';
					$user_name = 'Not Available';
					$job_title = 'Not Available';
					$html = '';
					if( $user_id != $current_user_id ) {
						$html = CHATHTML($user_id, $project);
					}
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

						if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
							$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
						}
					}
				?>
				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
		<?php
			$e_permission = $this->Common->element_share_permission($element_id, $project_id,$this->Session->read('Auth.User.id'));

			if(isset($grp_id) && !empty($grp_id)){

				if((isset($grp_id) && !empty($grp_id))){

					if(isset($e_permission) && !empty($e_permission)){
						$e_permissions =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
						$e_permission = array_merge($e_permission,$e_permissions);
					}else{
						$e_permission =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
					}
				}
			}


			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1)  || (isset($e_permission) && !empty($e_permission))  ) {
		?>

						<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $element_id, 'admin' => FALSE ), TRUE ); ?><?php echo $hash_tag; ?>" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }else{ ?>
		<a href="javascript:void(0)" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }  ?>
						</div>

						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['updated']); ?></span>

					</div>
				</div>
				<?php } }
				}
			}
			else {
			?>
			<div class="no-row-found" >No Feedback Activity</div>
		<?php } ?>

<?php }else if($section == 'votes'){ ?>

		<?php

			if( isset($votes_activities) && !empty($votes_activities)) {
				foreach( $votes_activities as $key => $val ) {
					$row = $val['Activity'];

					$type = $row['element_type'];
					$db_id = $row['relation_id'];
					$user_id = $row['updated_user_id'];
					$message = $row['message'];

					$db_data = getByDbId('Vote', $db_id);
					$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['Vote'] : null;

					if(isset($db_data) && !empty($db_data)) {

						$element_id = $db_data['element_id'];
						$dbe_data = getByDbId('Element', $element_id);
						if( isset($dbe_data) && !empty($dbe_data))  {

						$hash_tag = '#votes';

						$userDetail = $this->ViewModel->get_user_data( $user_id, -1 );
						// $userDetail = getByDbId('UserDetail', $user_id);
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						$html = '';
						if( $user_id != $current_user_id ) {
							$html = CHATHTML($user_id, $project);
						}
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
				?>
				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<?php
			$e_permission = $this->Common->element_share_permission($element_id, $project_id,$this->Session->read('Auth.User.id'));

			if(isset($grp_id) && !empty($grp_id)){

				if((isset($grp_id) && !empty($grp_id))){

					if(isset($e_permission) && !empty($e_permission)){
						$e_permissions =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
						$e_permission = array_merge($e_permission,$e_permissions);
					}else{
						$e_permission =  $this->Group->group_element_share_permission( $element_id, $project_id, $grp_id);
					}
				}
			}


			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1)  || (isset($e_permission) && !empty($e_permission))  ) {
		?>

						<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $element_id, 'admin' => FALSE ), TRUE ); ?><?php echo $hash_tag; ?>" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }else{ ?>
		<a href="javascript:void(0)" class="open-url">
							<?php echo $db_data['title'];?>

						</a>
		<?php }  ?>
						</div>

						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['updated']); ?></span>

					</div>
				</div>
					<?php }
					}
				}
			}
			else {
			?>
			<div class="no-row-found" >No Vote Activity</div>
		<?php } ?>

<?php } ?>

<script type="text/javascript" >
$(function(){
	// $('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
	// var lcount = $('.links_tab_data .items').length;
	// $('.links_tab_counter span').text(lcount);

	// var ncount = $('.notes_tab_data .items').length;
	// $('.notes_tab_counter span').text(ncount);

	// var dcount = $('.docs_tab_data .items').length;
	// $('.docs_tab_counter span').text(dcount);

	// var mcount = $('.mm_tab_data .items').length;
	// $('.mm_tab_counter span').text(mcount);

	// var fcount = $('.feedbacks_tab_data .items').length;
	// $('.feedbacks_tab_counter span').text(fcount);

	// var vcount = $('.votes_tab_data .items').length;
	// $('.votes_tab_counter span').text(vcount);

	// $('.total_activities .all').text( ( lcount + ncount + dcount + mcount + fcount + vcount ) );

/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */
})
</script>