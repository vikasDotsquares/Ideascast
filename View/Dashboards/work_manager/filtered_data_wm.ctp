<style type="text/css">
	.small-caps.tipText + .tooltip > .tooltip-inner  { text-transform: initial !important; }
</style>
<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>
<div class="buttons-container">
	<div class="col-xs-6 col-md-6 col-lg-6 col-data col-data-1">
		<div class="panel panel-default">
			<div class="panel-heading people-section">Request</div>
		</div>
	</div>

	<div class="col-xs-6 col-md-6 col-lg-6 col-data col-data-3">
		<div class="panel panel-default">
			<div class="panel-heading wsp-section">Workspace

			</div>
		</div>
	</div>

</div>

<!-- sort element by is_engaged value 0, 2, 3, 1 -->
<div class="col-sm-12 projects-line-wrapper">
	<div class="no-result">No Result</div>
	<?php

// e('filter data');
// pr($named_params);

	$element_keys = $element_users = [];

	if (isset($filter_projects) && !empty($filter_projects)) {
		foreach ($filter_projects as $prjid => $pvalue) {
			$defaultVal = 1;
			/********************************* GET PROJECT DATA *****************************************/
			$project_detail = getByDbId('Project', $prjid, ['title', 'id', 'start_date', 'end_date', 'color_code']);
			$project_title = strip_tags($project_detail['Project']['title']);
			$project_start_date = $project_detail['Project']['start_date'];
			$project_end_date = $project_detail['Project']['end_date'];
			$project_color_code = $project_detail['Project']['color_code'];

			$project_permit_type = $this->TaskCenter->project_permit_type( $prjid, $current_user_id );

			$prj_start_date = (isset($project_start_date) && !empty($project_start_date)) ? $this->TaskCenter->_displayDate_new($project_start_date,'m-d-Y') : false;
			$prj_end_date = (isset($project_end_date) && !empty($project_end_date)) ? $this->TaskCenter->_displayDate_new($project_end_date,'Y-m-d') : false;
	?>
	<div class="projects-line" data-project-title="<?php echo strtolower($project_title); ?>"  <?php if($prj_start_date) { ?> data-project-start-date="<?php echo $prj_start_date; ?>" <?php } ?> <?php if($prj_end_date) { ?> data-project-end-date="<?php echo $prj_end_date; ?>" <?php } ?> >

	<?php pr($filter_users);
			foreach ($filter_users as $ukey => $userid) {

				$element_key = [];
				$element_key = $this->TaskCenter->userElements($userid, [$prjid] );
				// $element_keys = (isset($element_key) && !empty($element_key)) ? array_merge($element_keys, $element_key) : $element_keys;

				$element_key = array_unique($element_key);
				if (isset($element_key) && !empty($element_key)) {
					foreach ($element_key as $ekey => $elid) {
				?>
				<?php
				/********************************* GET USER DATA *****************************************/
					$userDetail = $this->ViewModel->get_user( $userid, null, 1 );
					$user_image = SITEURL . 'images/placeholders/user/user_1.png';
					$user_name = 'Not Available';
					$job_title = 'Not Available';
					$html = '';
					if(isset($userDetail) && !empty($userDetail)) {

						$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

						if( $userid != $current_user_id ) {
							$html = CHATHTML($userid, $prjid);
						}

						if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
							$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
						}
					}
				/********************************* GET ELEMENT DATA *****************************************/
				$element_detail = getByDbId('Element', $elid, ['id', 'title', 'start_date', 'end_date', 'area_id', 'is_engaged', 'engaged_by', 'engaged_on', 'date_constraints']);

				if(isset($element_detail) && !empty($element_detail)) {
				$element_id = $element_detail['Element']['id'];
				$element_title = strip_tags($element_detail['Element']['title']);
				$element_start_date = $element_detail['Element']['start_date'];
				$element_end_date = $element_detail['Element']['end_date'];

				$element_permit_type = $this->TaskCenter->element_permit_type( $prjid, $current_user_id, $element_id );

				$el_start_date = (isset($element_start_date) && !empty($element_start_date)) ? $this->TaskCenter->_displayDate_new($element_start_date,'Y-m-d') : false;
				$el_end_date = (isset($element_end_date) && !empty($element_end_date)) ? $this->TaskCenter->_displayDate_new($element_end_date,'Y-m-d') : false;

				$element_participants = $this->ViewModel->element_participants($element_id);
				$element_status = element_status($element_id);

				// IF ANY OF THE ELEMENT/AREA/WORKSPACE STUDIO STATUS IS 1 THEN NOT TO DISPLAY ANY DATA
				$wsp_area_studio_status = wsp_area_studio_status($element_id);
				if( !$wsp_area_studio_status ) {
					/********************************* GET WORKSPACE DATA *****************************************/
					$workspace_id = element_workspace($element_id);
					$workspace_detail = getByDbId('Workspace', $workspace_id, ['id', 'title', 'start_date', 'end_date', 'color_code']);
					$workspace_title = strip_tags($workspace_detail['Workspace']['title']);
					$workspace_start_date = $workspace_detail['Workspace']['start_date'];
					$workspace_end_date = $workspace_detail['Workspace']['end_date'];

					$workspace_permit_type = $this->TaskCenter->workspace_permit_type( $prjid, $current_user_id, $workspace_id );

					$wsp_start_date = (isset($workspace_start_date) && !empty($workspace_start_date)) ? $this->TaskCenter->_displayDate_new($workspace_start_date,'Y-m-d') : false;
					$wsp_end_date = (isset($workspace_end_date) && !empty($workspace_end_date)) ? $this->TaskCenter->_displayDate_new($workspace_end_date,'Y-m-d') : false;

					?>
					<?php
					if( $element_status == 'progress' || $element_status == 'overdue' ) {
						$engClass = 'engaged-not tipText';
						$engTip = 'Not Engaged';
						$engPopover = '';
						if(isset($element_detail['Element']['is_engaged']) && !empty($element_detail['Element']['is_engaged'])) {

							$engaged_user = $this->ViewModel->get_user( $element_detail['Element']['engaged_by'], null, 1 );
							$engaged_by = $engaged_user['UserDetail']['first_name'] . ' ' . $engaged_user['UserDetail']['last_name'];
							$start_chat = '';
							if( $engaged_user['User']['id'] != $current_user_id) {
								$start_chat = '<p style="margin-top: 5px;"><a href="javascript:void(0)" data-project="'.$prjid.'" data-member="'.$engaged_user['User']['id'].'" data-email="'.$engaged_user['User']['email'].'" class="btn btn-jeera btn-xs chat_start_section">Start Chat</a></p>';
							}

							if( $element_detail['Element']['is_engaged'] == 3) {
								$engClass = 'engaged-accept pophover';
								$engTip = 'Task Engaged';
								$engPopover = '<div class="engage-popover"><span>Engaged by: '.$engaged_by.'</span><span>'.date('d M, Y h:iA', strtotime($element_detail['Element']['engaged_on'])).'</span><span>Schedule: Accepted</span>'.$start_chat.'</div>';
							}
							else if( $element_detail['Element']['is_engaged'] == 1) {
								$engClass = 'engaged-disagree pophover';
								$engTip = 'Task Engaged';

								$engPopover = '<div class="engage-popover"><span>Disengaged by: '.$engaged_by.'</span><span>'.date('d M, Y h:iA', strtotime($element_detail['Element']['engaged_on'])).'</span>'.$start_chat.'</div>';
							}
							else if( $element_detail['Element']['is_engaged'] == 2) {
								$engClass = 'engaged-schedule pophover';
								$engTip = 'Task Engaged';

								$engPopover = '<div class="engage-popover"><span>Engaged by: '.$engaged_by.'</span><span>'.date('d M, Y h:iA', strtotime($element_detail['Element']['engaged_on'])).'</span><span>Schedule: Not Accepted</span>'.$start_chat.'</div>';
							}
						}
					}

					if(isset($named_params) && !empty($named_params)) {
						if($named_params == 1) {
							if($element_status != 'overdue') continue;
						}
						else if($named_params == 2) {
							if(completing_tdto($element_detail['Element']['id']) <= 0) continue;
						}
						else if($named_params == 3) {
							if( ( $element_detail['Element']['is_engaged'] == 0 || $element_detail['Element']['is_engaged'] == 1 ) && ($element_status == 'progress' || $element_status == 'overdue') ){

							}
							else {
								continue;
							}
						}
					}
					?>
						<div
							class="line-inner"
							data-people-title="<?php echo strtolower($user_name); ?>"
							data-default="<?php echo $defaultVal++; ?>"
							data-element-title="<?php echo strtolower($element_title); ?>"
							data-workspace-title="<?php echo strtolower($workspace_title); ?>"
							data-element-status="<?php echo strtolower($element_status); ?>"
							<?php if($el_start_date) { ?> data-element-start-date="<?php echo $el_start_date; ?>" <?php } ?>
							<?php if($el_end_date) { ?> data-element-end-date="<?php echo $el_end_date; ?>" <?php } ?>
							<?php if($wsp_start_date) { ?> data-workspace-start-date="<?php echo $wsp_start_date; ?>" <?php } ?>
							<?php if($wsp_end_date) { ?> data-workspace-end-date="<?php echo $wsp_end_date; ?>" <?php } ?>
							<?php if( $element_status == 'progress' || $element_status == 'overdue' ){
								if(isset($element_detail['Element']['is_engaged'])) { ?>
									data-isengaged="<?php echo $element_detail['Element']['is_engaged']; ?>"
							<?php	}
							} ?>
						>
							<!-- people section -->
							<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-1 prj-people">
								<div class="img-box data-block">
									<div class="thumb" style="text-align: center;">
									<a data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $userid, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
										<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  />
									</a>

									<?php
									$user_found = false;
									$el_users_html = '';
									$el_users_html .= "<div class='el_users' >";
									if(isset($element_participants) && !empty($element_participants)) {
										foreach($element_participants as $ptype => $pusers) {
											if(isset($pusers) && !empty($pusers)) {
												foreach($pusers as $ou => $ov) {
													if( $userid != $ov ) {
														$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
														$userDetail = $this->ViewModel->get_user( $ov, $unbind, 1 );
														if(isset($userDetail) && !empty($userDetail)) {
															$user_found = true;
															$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
															$el_users_html .= "<a data-toggle='modal' data-target='#popup_modal' data-remote='".SITEURL."shares/show_profile/".$ov."' href='#'><i class='fa fa-user text-maroon'></i> " . $user_name . "</a><br />";
														}
													}
												}
											}
										}
									}
										$el_users_html .= '</div>';
									?>

									<a class="btn btn-default btn-xs users_popovers" id="" style="margin: 9px 0px 0px;" data-content="<?php if($user_found) {echo $el_users_html;}else{ echo 'N/A'; } ?>">
										<i class="fa fa-user-plus"></i>
									</a>

									</div>
								</div>
							</div>

							<!-- element section -->
							<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-2 prj-elements">
								<div class="data-block" data-title="<?php echo ucfirst($element_title); ?>">

									<?php if( $element_status == 'progress' || $element_status == 'overdue' ) { ?>
										<span class="fa engage-status <?php echo($engClass); ?> " title="<?php echo $engTip; ?>" data-content='<?php echo($engPopover); ?>'></span>
									<?php } ?>
									<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $element_id), true); ?>#tasks" class="data-block-title pop tipText" title="<?php echo ucfirst($element_title); ?>" ><?php echo ucfirst($element_title); ?></a>

									<div class="data-block-in">
										<div class="data-block-sec cell_<?php echo $element_status; ?>">
											<span>Start: <?php echo (empty($element_start_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($element_start_date,'d M, Y');  ?>
											</span>
											<span>End: <?php echo (empty($element_end_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($element_end_date,'d M, Y'); ?>
											</span>
										</div>
										<div class="pull-right edit-date">
											<div class="top-btn but-fa-arrow" style="display: block;">
											<?php
												$getCriticalStatus = $this->Common->critical_status($element_id);
												$getDependancyStatus = $this->Common->dependancy_status($element_id);

												$predessorCount = $this->Common->ele_dependency_count($element_id, 1);

												$successorCount = $this->Common->ele_dependency_count($element_id, 2);

											// Critical Status
												if( isset($getCriticalStatus) && $getCriticalStatus == 1 ){
											?>
										        	<i id="redrightarrow_<?php echo $element_id;?>" class="fa fa-arrow-right red-arrow-task tipText" title="Priority Task"  style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: default;"></i>
											<?php } ?>
											<i id="redrightarrow_<?php echo $element_id;?>" class="fa fa-arrow-right red-arrow-task tipText" title="Critical Task"  style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: default; display:none;"></i>
											<?php
											// Dependancy Status
											if( isset($getDependancyStatus) && $getDependancyStatus == 'predessor' ){

											?>

										        	<i rel="popover" data-popover-content="#myPopoverDependencyElement" id="leftarrow_<?php echo $element_id;?>" data-dependancytype="1" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-left tipTexts element_list" data-original-title="Predecessors" data-elecount="<?php echo $predessorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; "></i>

											<?php } else if(isset($getDependancyStatus) && $getDependancyStatus == 'successor'){ ?>

													<i id="rightarrow_<?php echo $element_id;?>" data-dependancytype="2" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-right tipTexts element_list" data-original-title="Successors" data-elecount="<?php echo $successorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; "></i>

											<?php } else if(isset($getDependancyStatus) && $getDependancyStatus == 'both'){?>
													<i id="botharrow_<?php echo $element_id;?>" data-dependancytype="3" data-elementid="<?php echo $element_id;?>" class="fa double-arrow tipTexts small-caps element_list" data-original-title="Predecessor and Successor" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; "></i>

										    <?php } //else { ?>

												<i rel="popover" data-popover-content="#myPopoverDependencyElement" id="leftarrow_<?php echo $element_id;?>" data-dependancytype="1" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-left tipTexts element_list" data-original-title="Predecessors" data-elecount="<?php echo $predessorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; display:none;"></i>

												<i id="rightarrow_<?php echo $element_id;?>" data-dependancytype="2" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-right tipTexts element_list" data-original-title="Successors" data-elecount="<?php echo $successorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; display:none;"></i>

												<i id="botharrow_<?php echo $element_id;?>" data-dependancytype="3" data-elementid="<?php echo $element_id;?>" class="fa   double-arrow tipTexts small-caps element_list" data-original-title="Predecessor and Successor" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; display:none;"></i>

											 <?php //} ?>
												<?php
												/*<a href="javascript:void(0);" data-toggle="modal" data-target="#modal_large" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $element_id), true); ?>" >
										        	<i class="fa fa-arrow-right" style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: pointer;"></i>
										        </a>
										        <a href="javascript:void(0);" data-toggle="modal" data-target="#modal_large" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $element_id), true); ?>" >
										        	<i class="fa fa-arrow-left" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051;"></i>
										        </a>*/
												?>

										    </div>

											<div class="task-sh-remindar">
											<?php
											if($project_permit_type && ($element_status == 'progress' || $element_status == 'not_started' || $element_status == 'overdue')) { ?>
											<?php
											$reminder_html = '';
											$get_element_reminder = get_element_reminder($element_id);
											$reminder_id = 0;
											$remind_btn_class = 'btn-default';
											$remind_icon_class = 'black';
											$remind_tooltip_class = 'tipText';
											$remind_tooltip_attr = 'title="Set Reminder"';
											if(isset($get_element_reminder) && !empty($get_element_reminder)) {
												$reminder_id = $get_element_reminder['Reminder']['id'];
												$reminder_user = $get_element_reminder['Reminder']['user_id'];
												// pr($get_element_reminder);
												$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
												$userDetail = $this->ViewModel->get_user( $reminder_user, $unbind, 1 );
												$user_name = 'N/A';
												if(isset($userDetail) && !empty($userDetail)) {
													$user_found = true;
													$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
												}

												$remind_btn_class = 'btn-success';
												$remind_icon_class = 'wht';

												$remind_tooltip_class = 'pophover';
												$reminder_html = '<div class="reminder_popup">';
												// $reminder_html .= '<div class="reminder_title">Reminder:</div>';
												$reminder_html .= '<div class="reminder_by"><b>Reminder by: </b>'.$user_name.'</div>';
												$reminder_html .= '<div class="reminder_time"><b>For: </b>'.date('M d, Y g:00A',strtotime($get_element_reminder['Reminder']['reminder_date'])).'</div>';
												$reminder_html .= '</div>';
												$remind_tooltip_attr = '';
											}
											 ?>
												<a data-toggle="modal" <?php echo $remind_tooltip_attr; ?> data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "element_reminder", $element_id, $reminder_id), true); ?>"  class="btn btn-xs but-remindar <?php echo $remind_btn_class; ?> <?php echo $remind_tooltip_class; ?> calender_modal" data-content='<?php echo $reminder_html; ?>'  href="#" style="padding: 1px 3px;"><i class="icon_reminder <?php echo $remind_icon_class; ?>"></i></a><?php } ?><?php if($element_permit_type) { ?><a data-updated="<?php echo $element_id; ?>" id="tasksech_<?php echo $element_id;?>" data-type="element" data-toggle="modal" data-target="#modal_large" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $element_id), true); ?>" data-original-title="Task Schedules" class="but-sh calender_modal tipText" title="Project Schedules" href="#"><i class="btn btn-sm btn-default active">SH</i></a>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
							</div>
							<!-- workspace section -->
							<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-3 prj-workspaces">
								<div class="data-block" data-title="<?php echo strip_tags(ucfirst($workspace_title)); ?>">

									<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "manage_elements", $prjid,$workspace_id), true);; ?>" class="data-block-title pop tipText" title="<?php echo $workspace_title; ?>"> <?php echo $workspace_title; ?></a>

									<?php $workspace_status = workspace_status($workspace_id); ?>
									<div class="data-block-in">
										<div class="data-block-sec cell_<?php echo $workspace_status; ?>">
											<span>Start: <?php echo (empty($workspace_start_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($workspace_start_date,'d M, Y');  ?>
											</span>
											<span>End: <?php echo (empty($workspace_end_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($workspace_end_date,'d M, Y'); ?>
											</span>
										</div>
										<?php if($workspace_permit_type) { ?>
										<div class="pull-right edit-date  task-center-arrow"><a data-updated="<?php echo $workspace_id; ?>" data-type="workspace"  data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_ws_date", $prjid, $workspace_id), true); ?>" data-original-title="Workspace Schedule" class=" calender_modal tipText" title="Workspace Schedule" href="#"><i class="btn btn-sm btn-default active">SH</i></a></div>
										<?php } ?>
									</div>
								</div>
							</div>

							<!-- project section -->
							<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-4 prj-projects">
								<div class="data-block" data-title="<?php echo strip_tags(ucfirst($project_title)); ?>">

									<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "index", $prjid), true);; ?>" class="data-block-title pop tipText" title="<?php echo $project_title; ?>"> <?php echo $project_title; ?></a>

									<div class="data-block-in">
										<div class="data-block-sec border-<?php echo str_replace('panel-', '', $project_color_code) ; ?>">
											<span>Start: <?php echo (empty($project_start_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($project_start_date,'d M, Y');  ?>
											</span>
											<span>End: <?php echo (empty($project_end_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($project_end_date,'d M, Y'); ?>
											</span>
										</div>
										<!-- <div class="pull-right edit-date">  -->
										<?php $cky = $this->requestAction('/projects/CheckProjectType/'.$prjid.'/'.$current_user_id); ?>
											<a data-original-title="Project Schedule" class="ico_gantt_btn tipText prj_gantt_btn" title="Gantt Chart" href="<?php echo Router::Url(array("controller" => "users", "action" => "event_gantt", $cky => $prjid ), true); ?>"><i class="btn btn-sm btn-default active ico_gantts">GN</i></a>
										<?php if($project_permit_type) { ?>
											<a data-updated="<?php echo $prjid; ?>" data-type="project"  data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_project_date", $prjid ), true); ?>" data-original-title="Project Schedule" class=" calender_modal tipText prj_cal" title="Project Schedule" href="#"><i class="btn btn-sm btn-default active">SH</i></a>
										<?php } ?>
										<!-- </div> -->
									</div>
								</div>
							</div>

						</div>
					<?php }
						}
					}
				}
			}
		?>
		</div>



		<?php
		}
	}
	else {
	?>
		<div class="no-row-wrapper">Select Project</div>
	<?php
	}
	?>


</div>
<script type="text/javascript">
function updateElement(element_id){
	var ele_url ='<?php echo SITEURL?>entities/update_element/'+element_id+'#tasks';
	window.location.href=ele_url;
}


	$(function(){

		setTimeout(function(){
			// $('.data-block-title').ellipsis_word();
		}, 500);

		$('.users_popovers,.pophover').popover({
	        placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
	    });

    	$.selectedDates;

    	$(".select_dates").daterange({
    		defaultDate: "+1w",
    		numberOfMonths: 2,
    		showButtonPanel: false,
    		onSelect: function(selected, inst) {
    			var divPane = inst.input
    								.datepicker("widget")
    			// divPane.css('z-index', '20')
    		},
    		beforeShow: function(input, inst) {
    			var divPane = $(input)
    				.datepicker("widget")
    			// divPane.css('z-index', '20')
    		},
    		onClose: function(dateText, inst) {

    			var $input = inst.input,
    				$cal_text = $input.parents('.task-section:first').find('.selected_dates:first'),
    				$cal_icon = $('.calendar_trigger');

    			if( dateText ) {
    				var dates = dateText.split(' - ');
    					firstDate = $.datepicker.formatDate('M dd, yy', new Date(dates[0])),
    					secDate = (dates[1]) ? $.datepicker.formatDate('M dd, yy', new Date(dates[1])) : '',
    					dateStr = firstDate + ( (secDate) ? ' - ' + secDate : ' - ' + firstDate) ,
    					data = $cal_icon.data();

    				$cal_text.hide().html(dateStr + '<i class="fa fa-times pull-right empty-dates" style=" "></i>').slideDown(500);

    				$('.projects-line').find('.line-inner').show();
    				var showElements = [];

					var Cal_startDate = new Date(dates[0]),
    					Cal_endDate = (dates[1]) ? new Date(dates[1]) : Cal_startDate;

    				$('.projects-line').find('.line-inner').each(function(){
    					if($(this).attr('data-element-start-date') && $(this).attr('data-element-end-date') ) {
    						var contentADate = $.datepicker.formatDate('mm/dd/yy', new Date($(this).attr('data-element-start-date'))),
    							contentBDate = $.datepicker.formatDate('mm/dd/yy', new Date($(this).attr('data-element-end-date')))
    							elem_sd = new Date(contentADate),
    							elem_ed = new Date(contentBDate);


/*     						if( (contentA.getTime() >= startDate.getTime() && contentA.getTime() <= endDate.getTime()) || (contentB.getTime() >= startDate.getTime() && contentB.getTime() <= endDate.getTime()) ) {	 */


							if(   (elem_sd.getTime() >= Cal_startDate.getTime() )  &&  ( (elem_sd.getTime() >= Cal_startDate.getTime()) || (elem_ed.getTime() <= Cal_endDate.getTime()) )   && (elem_ed.getTime() <= Cal_endDate.getTime() ) )  {
							//console.log(Cal_endDate);
							//console.log( elem_ed);
    							showElements.push($(this));
    						}
    					}
    				})

    				$('.projects-line').find('.line-inner').hide();

    				for(var i = 0; i < showElements.length; i++) {
    					showElements[i].fadeIn(i * 100);
    				}

    				$('.projects-line').each(function(){
    					if($(this).find('.line-inner:visible').length <= 0) {
    						$(this).hide();
    					}
    				})
    				if(showElements.length <= 0) {
    					$('.no-result').show();
    				}
    			}
    			else {
    				// $cal_text.css('opacity', 0)
    				$cal_text.css('display', 'none')
    				$cal_text.next('.task:first').css('padding', '0px 10px 10px')
    			}
    		},
    		showOptions: { direction: "down" },
    	});

    	$(".calendar_trigger").on('click', function(event) {
    		event.preventDefault();
    		$('.select_dates').trigger('focus');
        });

    	$('body').delegate(".empty-dates", 'click', function(event) {
    		event.preventDefault()

    		var $that = $(this),
    			$input = $('.select_dates'),
    			datePicker = $input.data('datepicker');

    		datePicker.input.val('');
    		$that.parent().slideUp(500, function(){
    			$(this).html('');
    		})
    		$('.projects-line').show()
    		$('.projects-line').find('.line-inner').each(function(i, e){
    			$(this).fadeIn(i * 100);
    		})

    		$('.no-result').hide();
			/* setTimeout(function(){
			$("#status-dropdown li a i:visible").trigger('click');
			console.log('clicked');
			},500) */


        });

    	$("#status-dropdown li a").click(function(e){
    		e.preventDefault();
    		var status = $(this).data('status');
    		$("#status-dropdown li a i.fa-check").hide();
    	  	$('#status-drop').html($(this).data('text') + ' <span class="fa fa-times bg-red clear_status_filter"></span>');
    		$("i.fa-check", $(this)).show();

    		$('.no-result').hide();
    		$('.projects-line,.line-inner').show()
    		$('.line-inner').each(function(){
    			if($(this).data('element-status') == status){
    				$(this).show();
    			}
    			else{
    				$(this).hide();
    			}
    		})

    		$('.projects-line').each(function(){
    			if ($('.line-inner:visible', $(this)).length <= 0) {
    				$(this).hide();
    			}
    		})
    		if ( $('.projects-line:visible').length <= 0 ) {
    			if ( $('.projects-line:visible').length <= 0)
    				$('.no-result').show();
    		}
    		if(status == 'progress' || status == 'overdue') {
				$('.projects-line:visible').each(function() {
				    $('.line-inner[data-element-status="'+status+'"]', $(this)).sort(function(a, b) {
				        var contentA = parseInt($(a).attr('data-isengaged'));
			            var contentB = parseInt($(b).attr('data-isengaged'));
			            return contentA > contentB ? 1 : -1;
				    }).appendTo($(this))
				})
		    }
    	});

    	$('body').delegate(".clear_status_filter", 'click', function(e) {

    		$('#status-drop').html('Status <span class="fa fa-times bg-red clear_status_filter"></span>');
    		$("#status-dropdown li a i.fa-check").hide();
    		$('.projects-line,.line-inner').show();
    		$('.no-result').hide();
			$('.projects-line').each(function() {
			$('.line-inner', $(this)).sort(function(a, b) {

			var contentA = parseInt($(a).attr('data-default'));
			var contentB = parseInt($(b).attr('data-default'));
			return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
			}).appendTo($(this));

			})

    		return false;
    	});

		$("body").on("click", function(e) {
			$(".element_list").popover('destroy');
		});

		$("body").on("mouseover", function(e) {
			//$(".element_list").popover('destroy');
		});

		$(".line-inner").on("mouseover", function(e) {
			 $(".element_list").popover('destroy');
		});

		$(".projects-line").on('scroll', function(event) {
			$(".element_list").popover('hide');
		});

/*
	$('body').tooltip({
    delay: { show: 300, hide: 0 },
    placement: function(tip, element) { //$this is implicit
        var position = $(element).position();
        if (position.left > 515) {
            return "left";
        }
        if (position.left < 515) {
            return "right";
        }
        if (position.top < 110){
            return "bottom";
        }
        return "top";
    },
    selector: '[rel=tooltip]:not([disabled])'
}); */



    $(".element_list").on("mouseover", function(e) {
        $that = $(this);
        $(".element_list").popover('destroy');

        var element_id = $that.data("elementid");
        var preSucEleCount = $that.data("elecount");
        var depndncytype = $that.data("dependancytype");
        var ele_list_url = $js_config.base_url+'dashboards/element_list';

		//Predecessors Successors
		if( depndncytype == 1 ){
			//$that.attr('title','Predecessors ('+preSucEleCount+')');
			$that.attr('data-original-title','Predecessors ('+preSucEleCount+')');
		}
		if( depndncytype == 2 ){
			//$that.attr('title','Successors ('+preSucEleCount+')');
			$that.attr('data-original-title','Successors ('+preSucEleCount+')');
		}

        $.ajax({
            url: ele_list_url,
            type: 'POST',
            data: { element_id:element_id,dependancytype:depndncytype },
            dataType: 'json',
            success: function(response, status, jxhr) {

					$that.attr('data-content',response);
					$that.attr('rel','popover');
					$that.popover({
						container: 'body',
						html: true,
				//		delay: { show: 300, hide: 0 },
/* 						placement: function(tip, element) { //$this is implicit
							var position = $(element).position();
							if (position.left > 320) {
								return "left";
							}
							if (position.left < 320) {
								return "right";
							}
							if (position.top < 110){
								return "bottom";
							}
							return "top";
						},
						selector: '[rel=tooltip]:not([disabled])'	 */
					});

					$that.popover('show');
					$that.tooltip('hide');

					if( depndncytype == 1 ){
						//$that.attr('title','Predecessors');
						$that.attr('data-original-title','Predecessors');
					}
					if( depndncytype == 2 ){
					//	$that.attr('title','Successors');
						$that.attr('data-original-title','Successors');
					}

            }
        })
    })

})

</script>