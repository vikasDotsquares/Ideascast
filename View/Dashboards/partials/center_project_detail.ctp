<?php
$current_user_id = $this->Session->read('Auth.User.id');

if( isset($projects) && !empty($projects) ) {
	$user_project = $projects['UserProject'];
	$project = $projects['Project'];

	$share_icon = true;

	$date_label = '';
	$date_text = 'N/A';

	$permit_owner = 0;

	if( $slug == 'shared_projects') {
		$date_label = 'Date Shared:';
		$permit_detail = ( isset($permit_id) && !empty($permit_id) ) ? getByDbId('ProjectPermission', $permit_id) : null;
		$date_text = dateFormat($permit_detail['ProjectPermission']['created']);
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];
		$permit_owner = 1;
	}
	else if( $slug == 'received_projects') {
		$date_label = 'Date Received:';
		$permit_detail = ( isset($permit_id) && !empty($permit_id) ) ? getByDbId('ProjectPermission', $permit_id ) : null;
		$date_text = dateFormat($permit_detail['ProjectPermission']['created']);
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];
	}
	else if( $slug == 'group_received_projects') {
		$date_label = 'Date Received:';
		$group_detail = ( isset($permit_id) && !empty($permit_id) ) ? getByDbId('ProjectGroup', $permit_id ) : null;

		$conditions = [
				'ProjectPermission.project_group_id' => $permit_id,
				'ProjectPermission.user_project_id' => $group_detail['ProjectGroup']['user_project_id']
			];
		$permit_detail = $this->ViewModel->project_permission_detail($conditions );
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];

		$date_text = dateFormat($group_detail['ProjectGroup']['created']);

	}
	else if( $slug == 'propagated_projects') {
		$date_label = 'Date Shared:';

		$permit_detail = $this->Common->project_permission_details($project['id'], $this->Session->read('Auth.User.id') );
		$date_text = dateFormat($permit_detail['ProjectPermission']['created']);
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];
		// pr($permit_detail);
	}
	else {
		$date_label = 'Date Created:';
		$date_text = _displayDate(date('Y-m-d h:i:s', $project['created']), 'd M, Y');
		$permit_owner = 1;
	}
	// e($project['start_date']);
	$start_date = (isset($project['start_date']) && !empty($project['start_date'])) ? _displayDate(date('Y-m-d', strtotime($project['start_date'])), 'd M, Y') : 'N/A';
	$end_date = (isset($project['end_date']) && !empty($project['end_date'])) ? _displayDate(date('Y-m-d', strtotime($project['end_date'])), 'd M, Y') : 'N/A';

	// pr($permit_detail);
?>



<?php

	$owner = $this->Common->ProjectOwner($project['id'], $this->Session->read('Auth.User.id'));

	$participants = participants($project['id'], $owner['UserProject']['user_id']);

	$participants_owners = participants_owners($project['id'], $owner['UserProject']['user_id']);

	$participantsGpOwner = participants_group_owner($project['id'] );

	$participantsGpSharer = participants_group_sharer($project['id'] );



	$participants = isset($participants) ? array_filter($participants) : $participants;
	$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
	$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
	$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

	$tot_participants = ( isset($participants) && !empty($participants) )? count($participants) : 0;
	$tot_participants_owners = ( isset($participants_owners) && !empty($participants_owners) )? count($participants_owners) : 0;
	$tot_participantsGpOwner = ( isset($participantsGpOwner) && !empty($participantsGpOwner) )? count($participantsGpOwner) : 0;
	$tot_participantsGpSharer = ( isset($participantsGpSharer) && !empty($participantsGpSharer) )? count($participantsGpSharer) : 0;


	$total = 0;
	$total = $tot_participants + $tot_participants_owners + $tot_participantsGpOwner + $tot_participantsGpSharer;

?>
<div class="project-detail col-sm-9">
	<?php /* if($share_icon){ ?>
		<p class="icon_center_option_wrapper">

			<span class="os btn btn-xs  <?php echo ($permit_owner > 0) ?  'bg-maroon'  : 'bg-orange' ?>"><?php echo ($permit_owner > 0) ? 'Owner' : 'Sharer'; ?></span>

			<a href="#" class="btn btn-default btn-xs icon_center_option tipText" title="Project Options" data-pid="<?php echo $project['id'] ?>" data-permitid="<?php echo ( isset($permit_id) && !empty($permit_id) ) ? $permit_id : 0 ?>" data-owner="<?php echo $permit_owner ?>" data-slug="<?php echo $slug; ?>">
				<i class="fa fa-ellipsis-v"></i>
			</a>
		</p>
	<?php } */ ?>
	<div class="top-detail">

		<p class="date-para start-date">
			<span>Start Date: </span>
			<span><?php echo $start_date; ?></span>
		</p>

		<p class="date-para end-date">
			<span>End Date: </span>
			<span><?php echo $end_date; ?></span>
		</p>

		<p class="date-para">
			<span>Permission: </span>
			<span><?php echo ($permit_owner > 0) ? 'Owner' : 'Sharer'; ?></span>
		</p>

		<p>
			<span>Alignment: </span>
			<span><?php echo (is_array(get_alignment($project['aligned_id']))) ? get_alignment($project['aligned_id'])['title'] : 'N/A'; ?></span>
		</p>

		<p>
			<span>Objective: </span>
			<span><?php echo $project['objective'] ?></span>
		</p>
	</div>
	<div class="bottom-buttons">
		<div class="btn-group">
			<?php
				$param_title = 'm_project';
				if( $slug=='group_received_projects' ) {
					$param_title = 'g_project';
				}
				else if( $slug=='received_projects' ) {
					$param_title = 'r_project';
				}
			?>

			<a href="<?php echo Router::Url( array( 'controller' => 'users', 'action' => 'event_gantt', $param_title => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Gantt" class="btn btn-default btn-xs tipText "><i class="fa fa-calendar"></i></a>

			<?php if( $permit_owner > 0 ) { ?>
				<a href="<?php echo Router::Url( array( 'controller' => 'users', 'action' => 'projects', 'm_project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Show Resources" class="btn btn-default btn-xs tipText "><i class="fa fa-file"></i></a>

				<?php if( $slug != 'group_received_projects' ) { ?>
					<a href="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'index', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Project Sharing" class="btn btn-default btn-xs tipText "><i class="fa fa-users"></i></a>
				<?php } ?>
			<?php } ?>

			<a href="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'sharing_map', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Sharing Map" class="btn btn-default btn-xs tipText "><i class="fa fa-share"></i></a>

			<?php if( $permit_owner > 0 ) { ?>
				<a href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'objectives', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Dashboard" class="btn btn-default btn-xs tipText "><i class="fa fa-dashboard"></i></a>
			<?php } ?>

			<!-- <a href="<?php //echo Router::Url( array( 'controller' => 'projects', 'action' => 'reports', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Project Report" class="btn btn-default btn-xs tipText "><i class="fa fa-bar-chart-o"></i></a> -->

			<?php if( $permit_owner > 0 ) { ?>
				<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'task_list', 'project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Task Lists" class="btn btn-default btn-xs tipText "><i class="fa fa-tasks"></i></a>
			<?php } ?>

				<a href="<?php echo Router::Url( array( 'controller' => 'todos', 'action' => 'index', 'project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="To-do Lists" class="btn btn-default btn-xs   todolist_link"><i class="fa fa-list-ul"></i></a>


				<?php  if(project_settings($project['id'], 'is_teamtalk')) { ?>
					<a href="<?php echo Router::Url( array( 'controller' => 'team_talks', 'action' => 'index', 'project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Team Talk" class="btn btn-default btn-xs tipText team_talks"><i class="fa fa-microphone"></i></a>
				<?php } ?>


		</div>
	</div>
</div>
<div class="project-users col-sm-3">

	<?php

		if( isset($participants_owners) && !empty($participants_owners) ) {
			foreach( $participants_owners as $key => $user_id ) {
				$html = '';
				if( $user_id != $current_user_id ) {
					$html = CHATHTML($user_id, $project['id']);
				}
				$style = '';

				if( $owner['UserProject']['user_id'] == $user_id ) {
					$style = 'border: 2px solid #333';
				}


				$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
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
					?>
					<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
						<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
					</a>

					<?php

				}
			}
		}
	?>

	<?php
		if( isset($participants) && !empty($participants) ) {
			foreach( $participants as $key => $user_id ) {
				$html = '';
				if( $user_id != $current_user_id ) {
					$html = CHATHTML($user_id, $project['id']);
				}

				$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
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
					?>
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"  class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img src="<?php echo $user_image; ?>" class="user-image "   >
						</a>
					<?php

				}
			}
		}
	?>

	<?php
		if( isset($participantsGpOwner) && !empty($participantsGpOwner) ) {
			foreach( $participantsGpOwner as $key => $user_id ) {
				$html = '';
				if( $user_id != $current_user_id ) {
					$html = CHATHTML($user_id, $project['id']);
				}

				$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
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
					?>
					<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"  class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
						<img src="<?php echo $user_image; ?>" class="user-image" >
					</a>
					<?php

				}
			}
		}
	?>

	<?php
		if( isset($participantsGpSharer) && !empty($participantsGpSharer) ) {
			foreach( $participantsGpSharer as $key => $user_id ) {
				$html = '';
				if( $user_id != $current_user_id ) {
					$html = CHATHTML($user_id, $project['id']);
				}

				$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
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
					?>
					<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"  class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>"  >
						<img src="<?php echo $user_image; ?>" class="user-image "  >
					</a>
					<?php

				}
			}
		}
	?>
</div>

<?php } ?>

<script type="text/javascript" >
$(function(){

	$('.todolist_link').tooltip({
		template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"></div></div>',
		'container': 'body',
		'placement': 'top',
	})

/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */

})
</script>