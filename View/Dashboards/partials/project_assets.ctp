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

		$links_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'element_links', $userID);
		$notes_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'element_notes', $userID);
		$documents_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'element_documents', $userID);
		$mindmaps_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'element_mindmaps', $userID);
		$feedback_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'feedback', $userID);
		$votes_activities = $this->ViewModel->getProjectElementsActivities($project, $project_workspaces, 'votes', $userID);

		$links_activities_total = 0;
		$notes_activities_total = 0;
		$documents_activities_total = 0;
		$mindmaps_activities_total = 0;
		$feedback_activities_total = 0;
		$votes_activities_total = 0;

		if( isset($links_activities) && !empty($links_activities) ){
			$links_activities_total = count($links_activities);
		}
		if( isset($notes_activities) && !empty($notes_activities) ){
			$notes_activities_total = count($notes_activities);
		}
		if( isset($documents_activities) && !empty($documents_activities) ){
			$documents_activities_total = count($documents_activities);
		}
		if( isset($mindmaps_activities) && !empty($mindmaps_activities) ){
			$mindmaps_activities_total = count($mindmaps_activities);
		}
		if( isset($feedback_activities) && !empty($feedback_activities) ){
			$feedback_activities_total = count($feedback_activities);
		}
		if( isset($votes_activities) && !empty($votes_activities) ){
			$votes_activities_total = count($votes_activities);
		}


		$all_activities = $links_activities_total + $notes_activities_total + $documents_activities_total + $mindmaps_activities_total + $feedback_activities_total + $votes_activities_total;


		$activities = array();

		if(is_array($links_activities))
			$activities = array_merge($activities, $links_activities);
		if(is_array($notes_activities ))
			$activities = array_merge($activities, $notes_activities);
		if(is_array($documents_activities ))
			$activities = array_merge($activities, $documents_activities);
		if(is_array($mindmaps_activities ))
			$activities = array_merge($activities, $mindmaps_activities);
		if(is_array($feedback_activities ))
			$activities = array_merge($activities, $feedback_activities);
		if(is_array($votes_activities ))
			$activities = array_merge($activities, $votes_activities);

	}
?>
<h3 class="tabing-head">Latest Asset Updates <span class="btn btn-default btn-xs tipText total_activities" title="Total Resources in Project"><i class="check-icon" aria-hidden="true"></i> <span class="all" style="border: medium none; float: none; margin: 0;"><?php echo $all_activities; ?></span></span></h3>
<ul id="tabs" class="nav nav-tabs resource-tab task-activity-icon" data-tabs="tabs">

	<li class="active"><a href="#<?php echo($slug); ?>assets_link" data-toggle="tab" class="tipText links_tab_counter" title="Link Activities" ><i class="asset-all-icon re-LinkBlack " aria-hidden="true"></i> <span><?php
	echo ( isset($links_activities) && !empty($links_activities) )? count($links_activities) : 0; ?></span></a></li>

	<li class="not-triggered"><a href="#<?php echo($slug); ?>assets_note" data-toggle="tab" class="tipText notes_tab_counter" title="Note Activities" data-section="notes"><i class="asset-all-icon re-NoteBlack" aria-hidden="true"></i> <span><?php
	echo ( isset($notes_activities) && !empty($notes_activities) )? count($notes_activities) : 0; ?></span></a></li>

	<li class="not-triggered"><a href="#<?php echo($slug); ?>assets_doc" data-toggle="tab" class="tipText docs_tab_counter" title="Document Activities" data-section="docs"><i class="asset-all-icon re-DocumentBlack" aria-hidden="true"></i> <span><?php
	echo ( isset($documents_activities) && !empty($documents_activities) )? count($documents_activities) : 0;
	  ?></span></a></li>

	<li class="not-triggered"><a href="#<?php echo($slug); ?>assets_mm" data-toggle="tab" class="tipText mm_tab_counter" title="Mindmap Activities" data-section="mindmaps"><i class="asset-all-icon re-MindMapBlack" aria-hidden="true"></i> <span><?php
	echo ( isset($mindmaps_activities) && !empty($mindmaps_activities) )? count($mindmaps_activities) : 0;
	 ?></span></a></li>

	<li class="not-triggered"><a href="#<?php echo($slug); ?>assets_feedback" data-toggle="tab" class="tipText feedbacks_tab_counter" title="Feedback Activities" data-section="feedbacks"><i class="asset-all-icon re-FeedbackBlack" aria-hidden="true"></i> <span><?php
	echo ( isset($feedback_activities) && !empty($feedback_activities) )? count($feedback_activities) : 0;
	 ?></span></a></li>

	<li class="not-triggered"><a href="#<?php echo($slug); ?>assets_vote" data-toggle="tab" class="tipText votes_tab_counter" title="Vote Activities" data-section="votes"><i class="asset-all-icon re-VoteBlack" aria-hidden="true"></i> <span><?php
	echo ( isset($votes_activities) && !empty($votes_activities) )? count($votes_activities) : 0;
	?></span></a></li>

 </ul>
 <?php
/* PROJECT AND GROUP PERMISSIONS */

	$user_id = $this->Session->read('Auth.User.id');

	$project_id = $this->request->data['id'];


/* PROJECT AND GROUP PERMISSIONS */
 ?>
<div id="my-tab-content" class="tab-content">

	<div class="tab-pane active links_tab_data" id="<?php echo ($slug); ?>assets_link">
		<?php

		if( isset($links_activities) && !empty($links_activities)) {
			foreach( $links_activities as $key => $val ) {
				$row = $val['Activity'];

				$type = $row['element_type'];
				$db_id = $row['relation_id'];
				$user_id = $row['updated_user_id'];
				$message = $row['message'];

				$db_data = getByDbId('ElementLink', $db_id);
				$db_data = ( isset($db_data) && !empty($db_data)) ? $db_data['ElementLink'] : null;
				if( isset($db_data) && !empty($db_data)) {
				$element_id = $db_data['element_id'];
				$dbe_data = getByDbId('Element', $element_id);
				if( isset($dbe_data) && !empty($dbe_data))  {

				$hash_tag = '#links';


				$userDetail = $this->ViewModel->get_user_data( $user_id, -1 );
				// $userDetail = getByDbId('UserDetail', $user_id);
				// $userDetail['UserDetail'] = $userDetails[$user_id];
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
			<?php }}
			}
		}
		else {
		?>
			<div class="no-row-found" >No Link Activity</div>
		<?php } ?>
	</div>

	<div class="tab-pane notes_tab_data" id="<?php echo($slug); ?>assets_note">

	</div>

	<div class="tab-pane docs_tab_data" id="<?php echo($slug); ?>assets_doc">

	</div>

	<div class="tab-pane mm_tab_data" id="<?php echo($slug); ?>assets_mm">

	</div>

	<div class="tab-pane feedbacks_tab_data" id="<?php echo($slug); ?>assets_feedback">

	</div>

	<div class="tab-pane votes_tab_data" id="<?php echo($slug); ?>assets_vote">

	</div>
</div>

<script type="text/javascript" >
$(function(){
	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
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