<?php
$current_user_id = $this->Session->read('Auth.User.id');

if( isset($projects) && !empty($projects) ) {
	$project_type = 'Created';
	// $dd = $this->ViewModel->getProjectPermit( $projects['Project']['id'], $slug );

	$user_project = $projects['UserProject'];
	$project = $projects['Project'];
	$colors = getByDbId('Project', $project['id'], ['color_code']);
	$project_color = $colors['Project']['color_code'];
	$project_color = (isset($project_color) && !empty($project_color)) ? $project_color : 'panel-default';
	$project_color = str_replace("panel", "box", $project_color);
	/***********/
    $project_people = $this->ViewModel->projectPeople($project['id']);

	$userPermissions = $this->ViewModel->userPermissions($project['id']);


	$onr = 0;

	if((isset($userPermissions[0]['user_permissions']['role']) && !empty($userPermissions[0]['user_permissions']['role']))&&  ( ($userPermissions[0]['user_permissions']['role'] !='Sharer') && $userPermissions[0]['user_permissions']['role'] !='Group Sharer' )) {
		$onr = 1;
	}
	else {
		$onr = 0;
	}
	/***************************/
	$share_icon = true;

	$date_label = '';
	$date_text = 'N/A';

	$permit_owner = 0;

	$edit_permit = $delete_permit = false;

	if(isset($userPermissions) && !empty($userPermissions)){
		if((isset($userPermissions[0]['user_permissions']['role']) && !empty($userPermissions[0]['user_permissions']['role']))&&  ( ($userPermissions[0]['user_permissions']['role'] !='Sharer') && $userPermissions[0]['user_permissions']['role'] !='Group Sharer' )) {
			$edit_permit = $delete_permit = true;
		}

		if(isset($userPermissions[0]['user_permissions']['permit_edit']) && $userPermissions[0]['user_permissions']['permit_edit'] ==1){
			$edit_permit = true;
		}
		if(isset($userPermissions[0]['user_permissions']['permit_delete']) && $userPermissions[0]['user_permissions']['permit_delete'] ==1){
			$delete_permit = true;
		}
	}
	if( $slug == 'shared_projects') {
		$date_label = 'Date Shared:';
		$permit_owner = 1;
		$project_type = 'Shared';
		$edit_permit = $delete_permit = true;
	}
	else if( $slug == 'received_projects') {
		$date_label = 'Date Received:';

		$permit_owner = $onr;
		$userdata = $this->Common->userFullname($userPermissions[0]['user_permissions']['shared_by_user_id']);
		$project_type = 'Received from: '.$userdata;

	}
	else if( $slug == 'group_received_projects') {
		$date_label = 'Date Received:';

		$userdata = $this->Common->userFullname($userPermissions[0]['user_permissions']['shared_by_user_id']);

		$permit_owner = $onr;
		$project_type = 'Group Received from: '.$userdata;
	}
	else if( $slug == 'propagated_projects') {
		$date_label = 'Date Shared:';
		$permit_owner = $onr;
		$userdata = $this->Common->userFullname($current_user_id);
		$project_type = 'Propagated by: '.$userdata;
	}
	else {
		$date_label = 'Date Created:';
		$date_text = _displayDate(date('Y-m-d h:i:s', $project['created']), 'd M, Y');
		$permit_owner = 1;
		$edit_permit = $delete_permit = true;
	}

	$start_date = (isset($project['start_date']) && !empty($project['start_date'])) ? _displayDate(date('Y-m-d', strtotime($project['start_date'])), 'd M, Y') : 'N/A';
	$end_date = (isset($project['end_date']) && !empty($project['end_date'])) ? _displayDate(date('Y-m-d', strtotime($project['end_date'])), 'd M, Y') : 'N/A';


	$prj_disabled = '';
	$prj_tip = '';
	$prj_cursor = '';
	if( isset($project['sign_off']) && !empty($project['sign_off']) && $project['sign_off'] == 1 ){
		$prj_disabled = 'disable';
		$prj_tip = 'Project Is Signed Off';
		$prj_cursor = 'cursor:default !important;';
	}

?>
<div class="project_data_wrapper <?php echo $project_color; ?>">
	<div class="wrapper-heading">
		<h3 class="wrapper-title">
			<a class="project-name" href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'index', $project['id'], 'admin' => FALSE ), TRUE ); ?>"><?php echo htmlentities($project['title']) . ' <span class="project-type">(' . $project_type . ')</span>'; ?></a>
			<div class="pull-right buttons-block">
				<a class="btn btn-default btn-xs tipText" title="Project Info" href="" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'project_description', $project['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_small"><i class="fa fa-eye"></i></a>

				<div class="btn-group ">
					<?php
					$param_title = 'm_project';
					if( $slug=='group_received_projects' ) {
						$param_title = 'g_project';
					}
					else if( $slug=='received_projects' ) {
						$param_title = 'r_project';
					}
					?>

					<?php /* <a href="<?php echo Router::Url( array( 'controller' => 'users', 'action' => 'event_calender', $param_title => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Planner" class="btn btn-default btn-xs tipText "><i class="fa fa-calendar"></i></a> */ ?>
					<a href="<?php echo Router::Url( array( 'controller' => 'users', 'action' => 'event_gantt', $param_title => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Gantt" class="btn btn-default btn-xs tipText "><i class="fa fa-calendar"></i></a>
					<?php if( $permit_owner > 0 ) { ?>
						<a href="<?php echo Router::Url( array( 'controller' => 'users', 'action' => 'projects', 'm_project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Project Assets" class="btn btn-default btn-xs tipText "><i class="projectassetsicon"></i></a>

						<?php if( $slug != 'group_received_projects' ) {
								if( isset($prj_disabled) && !empty($prj_disabled) ){
							?>
							<a title="<?php echo $prj_tip;?>" class="btn btn-default btn-xs tipText <?php echo $prj_disabled;?>" style="<?php echo $prj_cursor;?>"><i class="fa fa-user-plus"></i></a>
						<?php } else { /* ?>
							<a href="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'index', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Project Sharing" class="btn btn-default btn-xs tipText "><i class="fa fa-user-plus"></i></a>
						<?php */ }
						} ?>
					<?php }
					if( isset($prj_disabled) && !empty($prj_disabled) ){ ?>
						<a  title="<?php echo $prj_tip;?>" class="btn btn-default btn-xs tipText <?php echo $prj_disabled;?>" style="<?php echo $prj_cursor;?>" ><i class="fa fa-share"></i></a>
					<?php  } else { ?>
						<?php
						$ProjectLevel = ProjectLevel($project['id']);
						if(isset($ProjectLevel) && !empty($ProjectLevel)){
						?>
						<a href="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'sharing_map', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Sharing Map" class="btn btn-default btn-xs tipText "><i class="fa fa-share"></i></a>
						<?php } ?>

					<?php } ?>

					<?php /*<?php if( $permit_owner > 0 ) { ?>
						<a href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'objectives', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Status" class="btn btn-default btn-xs tipText "><i class="fa fa-dashboard"></i></a>
					<?php } ?> */ ?>

					<!-- <a href="<?php //echo Router::Url( array( 'controller' => 'projects', 'action' => 'reports', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Project Report" class="btn btn-default btn-xs tipText "><i class="fa fa-bar-chart-o"></i></a> -->

					<?php /*if( $permit_owner > 0 ) { ?>
						<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'task_list', 'project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Task Lists" class="btn btn-default btn-xs tipText "><i class="fa fa-tasks"></i></a>
					<?php } */ ?>

					<a href="<?php echo Router::Url( array( 'controller' => 'todos', 'action' => 'index', 'project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="To-dos" class="btn btn-default btn-xs   todolist_link"><i class="fa fa-list-ul"></i></a>


					<?php  /* if(project_settings($project['id'], 'is_teamtalk')) { ?>
						<a href="<?php echo Router::Url( array( 'controller' => 'team_talks', 'action' => 'index', 'project' => $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Info Center" class="btn btn-default btn-xs tipText team_talks"><i class="fa fa-microphone"></i></a>
					<?php } */ ?>

					<!--<a href="<?php  echo Router::Url(array('controller' => 'skts', 'action' => 'index', 'project_id'=> $project['id'], 'admin' => FALSE), TRUE); ?>" title="Sketcher" id="Sketchers" class="btn btn-default btn-xs tipText" ><i class="fa fa fa-pencil-square-o" style="font-size: 13px;"></i></a>-->


				</div>
				<div class="btn-group">
					<?php if($edit_permit){
						if( isset($prj_disabled) && !empty($prj_disabled) ){
					?>
						<a title="<?php echo $prj_tip;?>" class="btn btn-default btn-xs tipText <?php echo $prj_disabled;?>" style="<?php echo $prj_cursor;?>"><i class="fa fa-paint-brush"></i></a>
						<?php } else { ?>
						<a href="#" title="Color Options" class="btn btn-default btn-xs tipText color_bucket"><i class="fa fa-paint-brush"></i></a>
						<?php } ?>

							<div class="color_box colorbox_bottom" style="display:none">
								<div class="colors btn-group">
									<a href="#" data-color="box-red" data-set="panel-red" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
									<a href="#" data-color="box-blue" data-set="panel-blue" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
									<a href="#" data-color="box-maroon" data-set="panel-maroon" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
									<a href="#" data-color="box-aqua" data-set="panel-aqua" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
									<a href="#" data-color="box-yellow"  data-set="panel-yellow" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
									<a href="#" data-color="box-green" data-set="panel-green" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
									<a href="#" data-color="box-teal" data-set="panel-teal" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
									<a href="#" data-color="box-purple" data-set="panel-purple" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
									<a href="#" data-color="box-navy" data-set="panel-navy" data-remote="<?php echo SITEURL.'projects/update_color/'.$project['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
								</div>
							</div>

						<?php if( isset($prj_disabled) && !empty($prj_disabled) ){ ?>
						<a title="<?php echo $prj_tip;?>" class="btn btn-default btn-xs tipText <?php echo $prj_disabled;?>" style="<?php echo $prj_cursor;?>" ><i class="fa fa-pencil"></i></a>
						<?php } else {?>
						<a href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'manage_project', $project['id'], 'admin' => FALSE ), TRUE ); ?>" title="Update Project Details" class="btn btn-default btn-xs tipText "><i class="fa fa-pencil"></i></a>
						<?php } ?>

					<?php } ?>

					<?php /*if($delete_permit){ ?>
						<?php if( isset($prj_disabled) && !empty($prj_disabled) ){ ?>
						<a type="button" class="btn btn-default btn-xs tipText <?php echo $prj_disabled;?>" title="<?php echo $prj_tip;?>" style="<?php echo $prj_cursor;?>">
							<i class="fa fa-trash"></i>
						</a>
						<?php } else { ?>
						<a id="confirm_deletes" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "projects", "action" => "delete_an_item", $project['id'], 'admin' => FALSE ), true ); ?>" type="button" class="btn btn-default btn-xs tipText delete-an-item" title="Delete">
							<i class="fa fa-trash"></i>
						</a>
					<?php } }*/ ?>

				</div>
			</div>
			<div class="pull-right status-block" style="display: inline-block; margin-right: 30px;">
				<div class="rag_status_block">
	                <?php
		                $RAG = $this->Common->getRAG($project['id']);
		                $progress_class = $RAG['rag_color']
	                ?>
					<!--<span class="hidden-md hidden-sm">Status</span>-->
					<div class="progress-status progress-status-wid">
						<div class="progress tipText" title="">
							<div class="progress-bar <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:100%;">RAG
							</div>
						</div>
					</div>
				</div>
				<div style="display: inline-block;">
	                <?php
	                $rag_exist = rag_exist($project['id']);
	                if( !empty($rag_exist)) { ?>
					      <span class="rag_update_rules tipText" title="Rules" ></span>
	                <?php }else { ?>
	                        <span class="rag_update_manual tipText" title="Manual" ></span>
	                <?php } ?>
	            </div>
            </div>
		</h3>
	</div>

	<div class="wrapper-body">
		<div class="top-data clearfix">
			<div class="project-image-section-col">
				<?php
					$user_name = $updated_on = '';
					if(isset($project['image_updated_by']) && !empty($project['image_updated_by'])) {
						$userDetail = $this->ViewModel->get_user( $project['image_updated_by'], null, 1 );
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							$updated_on = _displayDate($project['image_updated_on']);
						}
					}
					$project_image = $project['image_file'];

					if( !empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)){

						$project_image = SITEURL . PROJECT_IMAGE_PATH . $project_image;
					?>

					<?php
						echo $this->Image->resize( $project['image_file'], 449, 136, array(), 100);
						if(!empty($permit_owner)){
					?>
					<div class="img-options">
						<a class="btn btn-primary btn-xs image-upload project_image_upload" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $project['id'], 2, 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $project['id']; ?>" style="right: 70px;">Update</a>

						<a class="btn btn-danger btn-xs image-upload remove_center_image" data-id="<?php echo $project['id']; ?>">Remove</a>
					</div>
					<?php }
					}
					else {
						if(!empty($permit_owner)){
					?>
					<span id="" class="" style="display: block;">
						<div class="upload-text-cent">
							Add a photo here to show off your project.<br>
							<a class="btn btn-primary btn-sm project_image_upload" style="margin-top: 10px;" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $project['id'], 2, 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $project['id']; ?>">Upload</a>
						</div>
					</span>
					<?php }
					else{
						?>
						<div class="upload-text-cent" style="padding-top: 55px;">No Project Image</div>
						<?php
					}
					}
				?>
			</div>
			<div class="project-dec-section project-dec-section-col">
				<div class="top-detail">

					<p class="date-para start-date">
						<span>Start Date: </span>
						<span><?php echo $start_date; ?></span>
					</p>

					<p class="date-para end-date">
						<span>End Date: </span>
						<span><?php echo $end_date; ?></span>
					</p>

					<p class="date-para" style="margin-bottom: 5px;">
						<span>Permission: </span>
						<span><?php echo ($permit_owner > 0) ? 'Owner' : 'Sharer'; ?></span>
					</p>
				</div>
				<div class="bottom-detail">
					<b>Team Members</b>
					<div class="project-users ">

						<?php

							if( isset($project_people) && !empty($project_people) ) {

								foreach( $project_people as  $data ) {
									$html = '';
									 $user_id = $data['user_permissions']['user_id'];
									if( $user_id != $current_user_id ) {
										$html = CHATHTML($user_id, $project['id']);
									}
									$style = '';

									if($data['user_permissions']['role'] == 'Creator' ) {
										$style = 'border: 2px solid #333';
									}


									$userDetail =  $data['user_details'];
									$full_name =  $data['0']['fullname'];
									$user_image = SITEURL . 'images/placeholders/user/user_1.png';
									$user_name = 'Not Available';
									$job_title = 'Not Available';
									if(isset($userDetail) && !empty($userDetail)) {
										$user_name = htmlentities($full_name,ENT_QUOTES);
										$profile_pic = $userDetail['profile_pic'];
										$job_title = htmlentities($userDetail['job_title'],ENT_QUOTES);

										if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
											$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
										}
									?>
									<a href="#" class="pophover show_comments" data-project="<?php echo $project['id']; ?>" data-user="<?php echo $user_id; ?>" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
										<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
										<i class="fa fa-check user-check"></i>
									</a>


									<?php
									}
								}
							}
						?>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<div class="col-sm-12 comments_section">
	<ul class="nav nav-tabs pc-tabs none-events">
		<li class="not-triggered"><a  href="#task_activity" data-toggle="tab" data-type="task">Task Activity</a></li>
		<li class="not-triggered"><a href="#blog_activity" data-toggle="tab" data-type="blog">Blog Activity</a></li>
		<li class="not-triggered"><a href="#comment_activity" data-toggle="tab" data-type="comment">Comment Activity</a></li>
		<li class="not-triggered"><a href="#resource_activity" data-toggle="tab" data-type="resource">Asset Activity</a></li>
	</ul>
	<div class="tab-content pc-tab-content">
	  	<div class="tab-pane comment-tabs tab-zero" id="task_activity"></div>
		<div class="tab-pane comment-tabs tab-one" id="blog_activity"></div>
		<div class="tab-pane comment-tabs tab-two" id="comment_activity"></div>
		<div class="tab-pane comment-tabs tab-three" id="resource_activity"></div>
	</div>
</div>
<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<script type="text/javascript" >
$(function(){

	$('.todolist_link').tooltip({
		template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"></div></div>',
		'container': 'body',
		'placement': 'top',
	})
/* 
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */

	$('.show_comments').on('click', function(event){
		event.preventDefault();

		var data = $(this).data(),
			user_id = data.user,
			project_id = data.project;
		var $comments_section = $('.comments_section');
		var $icon = $(this).find('.user-check');

		$('.user-check').not($icon[0]).removeClass('selected-user');
		if( !$icon.hasClass('selected-user') ) {
			$icon.addClass('selected-user');
		}
		else{
			$icon.removeClass('selected-user');
		}
		$('.pc-tabs li').removeClass('active').addClass('not-triggered');
		$('.comment-tabs').html('').removeClass('active');

		$('.pc-tabs li:first a').trigger('click')

	})


	$('.show_comments11').on('click', function(event){
		event.preventDefault();

		var data = $(this).data(),
			user_id = data.user,
			project_id = data.project;
		var $comments_section = $('.comments_section');
		var $icon = $(this).find('.user-check');

		$('.user-check').not($icon[0]).removeClass('selected-user');

		// $comments_section.html('<div class="loader_bar"></div>');
		$comments_section.find('.tab-zero,.tab-one,.tab-two,.tab-three').html('<div class="loader_bar"></div>');
		if( !$icon.hasClass('selected-user') ) {

			var params = { user_id: user_id, id: project_id, slug: '<?php echo $slug; ?>' };
			$.when(
				$.ajax({
					url: $js_config.base_url + 'dashboards/partial_project_tasks/p='+$.now(),
					data: $.param( params ),
					global: false,
					type: 'post',
					dataType: 'JSON',
					success: function(response) {
						$comments_section.find('.tab-zero').html( response );
					}
				})
			).then(function( data, textStatus, jqXHR ) {
				/*$.when(
					$.ajax({
						url: $js_config.base_url + 'dashboards/partial_project_blogs/q='+$.now(),
						data: $.param( params ),
						global: false,
						type: 'post',
						dataType: 'JSON',
						success: function(response) {
							$comments_section.find('.tab-one').html( response );
						}
					})
				).then(function( data, textStatus, jqXHR ) {
					$.when(
						$.ajax({
							url: $js_config.base_url + 'dashboards/partial_project_comments/r='+$.now(),
							data: $.param( params ),
							global: false,
							type: 'post',
							dataType: 'JSON',
							success: function(response) {
								$comments_section.find('.tab-two').html( response );
							}
						})
					).then(function( data, textStatus, jqXHR ) {
						// setTimeout(function(){
						$.ajax({
							url: $js_config.base_url + 'dashboards/partial_project_assets/s='+$.now(),
							data: $.param( params ),
							global: false,
							type: 'post',
							dataType: 'JSON',
							success: function(response) {
								$comments_section.find('.tab-three').html( response );
							}
						})
						// }, 1000)
					})
				})*/

			})

			$icon.addClass('selected-user');
		}
		else {

			var params = { user_id: 0, id: project_id, slug: '<?php echo $slug; ?>' };
			$.when(
				$.ajax({
					url: $js_config.base_url + 'dashboards/partial_project_tasks/p='+$.now(),
					data: $.param( params ),
					global: false,
					type: 'post',
					dataType: 'JSON',
					success: function(response) {
						$comments_section.find('.tab-zero').html( response );
					}
				})
			).then(function( data, textStatus, jqXHR ) {
				/*$.when(
					$.ajax({
						url: $js_config.base_url + 'dashboards/partial_project_blogs/q='+$.now(),
						data: $.param( params ),
						global: false,
						type: 'post',
						dataType: 'JSON',
						success: function(response) {
							$comments_section.find('.tab-one').html( response );
						}
					})
				).then(function( data, textStatus, jqXHR ) {
					$.when(
						$.ajax({
							url: $js_config.base_url + 'dashboards/partial_project_comments/r='+$.now(),
							data: $.param( params ),
							global: false,
							type: 'post',
							dataType: 'JSON',
							success: function(response) {
								$comments_section.find('.tab-two').html( response );
							}
						})
					).then(function( data, textStatus, jqXHR ) {
						// setTimeout(function(){
						$.ajax({
							url: $js_config.base_url + 'dashboards/partial_project_assets/s='+$.now(),
							data: $.param( params ),
							global: false,
							type: 'post',
							dataType: 'JSON',
							success: function(response) {
								$comments_section.find('.tab-three').html( response );
							}
						})
						// }, 1000)
					})
				})*/

			})

			$icon.removeClass('selected-user');
		}

	})

	// COLOR SELECTION CODE
	$('.color_bucket').each(function () {
		$(this).data('color_box', $(this).parent().find('.color_box'))
		$(this).parent().find('.color_box').data('color_bucket', $(this))
	})

	$('.color_bucket').on('click', function (event) {
		event.preventDefault();
		var $color_box = $(this).parent().find('div.color_box'),
			vars = {
				offset: $color_box.offset(),
				top: $color_box.offset().top,
				left: $color_box.offset().left,
				w: $color_box.width(),
				h: $color_box.height(),
				sidebar_width: $('aside.main-sidebar').width(),
				panel: $(".color_bucket:first").parents(".panel:first"),
				panel_offset: $(".color_bucket:first").parents(".panel:first").offset(),
				wWidth: $(window).width(),
				wHeight: $(window).height(),
				dHeight: $(document).height(),
				scroll: $(window).scrollTop()
			}

			$color_box.slideToggle(200)
			/*if( $color_box.offset().left < 230 ) { // set right
				$color_box.removeClass('color_box_left').addClass('color_box_right')
			}
			if( vars.panel_offset > ($(window).width() - 300) ) {// set left
				$color_box.removeClass('color_box_right').addClass('color_box_left')
			}

			var s =  $color_box.isScrolledIntoView();
			var p =  $color_box.parent().isScrolledIntoView();
			if ( ( p.top + $color_box.height() + $color_box.parent().height() ) > ( $(window).height() - 80 ) ) { // set on top
				$color_box.removeClass('color_box_bottom').addClass('color_box_top')
			}
			else if ( ( p.top ) < 100 ) { // set to bottom
				$color_box.removeClass('color_box_top').addClass('color_box_bottom')
			}*/
			// console.log(( p.top + $color_box.height() + $color_box.parent().height() ))

	});

	$(".el_color_box").on('click', function( event ) {
		event.preventDefault();

		var $cb = $(this)
		var $hd = $cb.parents('.project_data_wrapper:first')
		var cls = $hd.attr('class')

		var foundClass = (cls.match (/(^|\s)box-\S+/g) || []).join('')
		if( foundClass != '' ) {
			$hd.removeClass(foundClass)
		}
		var applyClass = $cb.data('color')
		var setClass = $cb.data('set')

		$hd.addClass(applyClass);

		$(this).setPanelColorClass();


		// SEND AJAX HERE TO CHANGE THE COLOR OF THE ELEMENT
	})

	$.fn.setPanelColorClass = function() {

		var url = $(this).data('remote');
		var color_code = $(this).data('set');
		var data = $.param({'color_code': color_code});

		$.ajax({
			type:'POST',
			data: data,
			url: url,
			global: true,
			success: function( response, status, jxhr ) {
				if( status == 'success' ) {
					console.log('success')
				}
				else {
					console.log('error')
				}

			},
		});
	}


	$('body').on('click', function (e) {
		$('.color_bucket').each(function () {
			if ( !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.color_box').has(e.target).length === 0) {
				var color_box = $(this).data('color_box')
				if(color_box.length)
					color_box.hide();
			}
		});
	});

})
</script>