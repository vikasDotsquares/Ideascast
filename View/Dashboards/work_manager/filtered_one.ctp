<?php $current_user_id = $this->Session->read('Auth.User.id');
/********************************* GET PROJECT DATA *****************************************/
			$project_detail = getByDbId('Project', $prjid, ['title', 'id', 'start_date', 'end_date', 'color_code']);
			$project_title = strip_tags($project_detail['Project']['title']);
			$project_start_date = $project_detail['Project']['start_date'];
			$project_end_date = $project_detail['Project']['end_date'];
			$project_color_code = $project_detail['Project']['color_code'];

			$project_permit_type = $this->TaskCenter->project_permit_type( $prjid, $current_user_id );

			$prj_start_date = (isset($project_start_date) && !empty($project_start_date)) ? $this->TaskCenter->_displayDate_new($project_start_date,'Y-m-d') : false;
			$prj_end_date = (isset($project_end_date) && !empty($project_end_date)) ? $this->TaskCenter->_displayDate_new($project_end_date,'Y-m-d') : false;
?>
<div class="projects-line" data-id="<?php echo($prjid); ?>" data-project-title="<?php echo strtolower($project_title); ?>"  <?php if($prj_start_date) { ?> data-project-start-date="<?php echo $prj_start_date; ?>" <?php } ?> <?php if($prj_end_date) { ?> data-project-end-date="<?php echo $prj_end_date; ?>" <?php } ?> >

<?php
	foreach ($filter_users as $ukey => $userid) {

				$element_key = [];
				$element_key = $this->TaskCenter->userElements($userid, [$prjid] );
				// $element_keys = (isset($element_key) && !empty($element_key)) ? array_merge($element_keys, $element_key) : $element_keys;
				$element_key = array_unique($element_key);

				if (isset($element_key) && !empty($element_key)) {
					foreach ($element_key as $ekey => $elid) {

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
				$element_detail = getByDbId('Element', $elid, ['id', 'title', 'start_date', 'end_date', 'area_id']);
				$element_id = $element_detail['Element']['id'];
				$element_title = strip_tags($element_detail['Element']['title']);
				$element_start_date = $element_detail['Element']['start_date'];
				$element_end_date = $element_detail['Element']['end_date'];

				$element_permit_type = $this->TaskCenter->element_permit_type( $prjid, $current_user_id, $element_id );

				$el_start_date = (isset($element_start_date) && !empty($element_start_date)) ? $this->TaskCenter->_displayDate_new($element_start_date,'Y-m-d') : false;
				$el_end_date = (isset($element_end_date) && !empty($element_end_date)) ? $this->TaskCenter->_displayDate_new($element_end_date,'Y-m-d') : false;

				$element_participants = $this->ViewModel->element_participants($element_id);

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
						<div
							class="line-inner"
							data-people-title="<?php echo strtolower($user_name); ?>"
							data-element-title="<?php echo strtolower($element_title); ?>"
							data-workspace-title="<?php echo strtolower($workspace_title); ?>"
							<?php if($el_start_date) { ?> data-element-start-date="<?php echo $el_start_date; ?>" <?php } ?>
							<?php if($el_end_date) { ?> data-element-end-date="<?php echo $el_end_date; ?>" <?php } ?>
							<?php if($wsp_start_date) { ?> data-workspace-start-date="<?php echo $wsp_start_date; ?>" <?php } ?>
							<?php if($wsp_end_date) { ?> data-workspace-end-date="<?php echo $wsp_end_date; ?>" <?php } ?>
						>
							<!-- people section -->
							<div class="col-sm-6 col-md-3 col-lg-3 line-data line-1 prj-people">
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
							<div class="col-sm-6 col-md-3 col-lg-3 line-data line-2 prj-elements">
								<div class="data-block" data-title="<?php echo ucfirst($element_title); ?>">

									<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $element_id), true); ?>#tasks" class="data-block-title pop tipText" title="<?php echo ucfirst($element_title); ?>" > <?php echo ucfirst($element_title); ?></a>

									<?php $element_status = element_status($element_id); ?>
									<div class="data-block-in">
										<div class="data-block-sec cell_<?php echo $element_status; ?>">
											<span>Start: <?php echo (empty($element_start_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($element_start_date,'d M, Y');  ?>
											</span>
											<span>End: <?php echo (empty($element_end_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($element_end_date,'d M, Y'); ?>
											</span>
										</div>
										<?php if($element_permit_type) { ?>
										<div class="pull-right edit-date"><a data-updated="<?php echo $element_id; ?>" data-type="element" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $element_id), true); ?>" data-original-title="Task Schedules" class="btn btn-default btn-xs calender_modal" href="#"><i class="ico_cal"></i></a></div>
										<?php } ?>
									</div>
								</div>
							</div>

							<!-- workspace section -->
							<div class="col-sm-6 col-md-3 col-lg-3 line-data line-3 prj-workspaces">
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
										<div class="pull-right edit-date"><a data-updated="<?php echo $workspace_id; ?>" data-type="workspace"  data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_ws_date", $prjid, $workspace_id), true); ?>" data-original-title="Workspace Schedule" class="btn btn-default btn-xs calender_modal" href="#"><i class="btn btn-sm btn-default active">SH</i></a></div>
										<?php } ?>
									</div>
								</div>
							</div>

							<!-- project section -->
							<div class="col-sm-6 col-md-3 col-lg-3 line-data line-4 prj-projects">
								<div class="data-block" data-title="<?php echo strip_tags(ucfirst($project_title)); ?>">

									<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "index", $prjid), true);; ?>" class="data-block-title pop tipText" title="<?php echo $project_title; ?>"> <?php echo $project_title; ?></a>

									<div class="data-block-in">
										<div class="data-block-sec border-<?php echo str_replace('panel-', '', $project_color_code) ; ?>">
											<span>Start: <?php echo (empty($project_start_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($project_start_date,'d M, Y');  ?>
											</span>
											<span>End: <?php echo (empty($project_end_date)) ? 'N/A' : $this->TaskCenter->_displayDate_new($project_end_date,'d M, Y'); ?>
											</span>
										</div>
										<?php if($project_permit_type) { ?>
										<div class="pull-right edit-date">
											<a data-updated="<?php echo $prjid; ?>" data-type="project"  data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_project_date", $prjid ), true); ?>" data-original-title="Project Schedule" class="btn btn-default btn-xs calender_modal" href="#"><i class="btn btn-sm btn-default active">GN</i></a>
										</div>
										<?php } ?>
									</div>
								</div>
							</div>

						</div>
					<?php }
					}
				}
			}
 ?>
 </div>