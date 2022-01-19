<?php
		$projects_data = array();
		$current_user_id = $this->Session->read('Auth.User.id');
		$current_org = $this->Permission->current_org();
		$projects_data = $this->Permission->task_center_filtered_data($prjid, $userid, $page,'',$assigned_user_ids,$assigned_reaction,$filter_task_staus,$assign_sorting,$element_sorting,$wsp_sorting,$selected_dates,$element_title,$dateStartSorttype,$dateEndSorttype,$element_task_type);



		/******************* GET USER DATA ***********************/
		$userDetail = get_user_data( $userid );

		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'Not Available';
		$job_title = 'Not Available';
		$html = '';

		$user_selected_flname = 'N/A';
		if(isset($userDetail) && !empty($userDetail)) {

			$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);


			if( isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name']) ){
				$user_selected_flname = $userDetail['UserDetail']['first_name'];
			}

			if( isset($userDetail['UserDetail']['last_name']) && !empty($userDetail['UserDetail']['last_name']) && isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name'] ) ){
				$user_selected_flname = $userDetail['UserDetail']['first_name']."<br />".$userDetail['UserDetail']['last_name'];
			}


			$profile_pic = $userDetail['UserDetail']['profile_pic'];
			$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

			if( $userid != $current_user_id && !empty($prjid) ) {
				$html = CHATHTML($userid, $prjid);
			}

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
		/*****************************************************************/
if (isset($filter_projects) && !empty($filter_projects)) {
		$projects_count = count($filter_projects);
		foreach ($filter_projects as $pvalue) {
			$defaultVal = 1;

			 $project_premission = $pvalue['user_permissions'];
			 $project_detail = $pvalue['projects'];
			 $prjid = $pvalue['projects']['id'];

			/********************************* GET PROJECT DATA *****************************************/

			$project_permit_type = false;

			if($project_premission['role'] =='Creator'){
				$projectType = $cky = "m_project";
			}
			if($project_premission['role'] =='Owner'){
				$projectType = $cky = "r_project";
			}
			if($project_premission['role'] =='Group Owner'){
				$projectType = $cky = "g_project";
			}
			if($project_premission['role'] =='Group Sharer'){
				$projectType = $cky = "g_project";
			}
			if($project_premission['role'] =='Sharer'){
				$projectType = $cky = "r_project";
			}

			if($project_premission['role'] =='Creator' || $project_premission['role'] =='Owner' || $project_premission['role'] =='Group Owner' ){
				$project_permit_type = true;
			}

			$project_title = htmlentities($project_detail['title']);
			$project_start_date = $project_detail['start_date'];
			$project_end_date = $project_detail['end_date'];
			$project_color_code = $project_detail['color_code'];

			$prj_start_date = (isset($project_start_date) && !empty($project_start_date)) ? $this->Wiki->_displayDate($project_start_date,'m-d-Y') : false;
			$prj_end_date = (isset($project_end_date) && !empty($project_end_date)) ? $this->Wiki->_displayDate($project_end_date,'Y-m-d') : false;
			$project_cc = str_replace("panel", "bg", $project_color_code);

		if (isset($projects_data) && !empty($projects_data)) {
			foreach ($projects_data as $ekey => $elid) {


				/*********************** GET ELEMENT DATA ***********************************/
				if(isset($elid['elements']) && !empty($elid['elements'])) {

				$dependancy_type = array();
				$elm_users = json_decode($elid[0]['user_detail'],TRUE);
				if( isset($elid[0]['dependency_type']) && !empty($elid[0]['dependency_type']) ){
					$dependancy_type = json_decode($elid[0]['dependency_type'],true);
				}

				$elm_users = json_decode($elid[0]['user_detail'],TRUE);

				$element_detail['Element'] = $elid['elements'];
				$element_detail['user_permissions'] = $elid['user_permissions'];

				$element_id = $element_detail['Element']['ele_id'];
				$element_title = htmlentities($element_detail['Element']['ele_title']);
				$element_start_date = $element_detail['Element']['ele_start'];
				$element_end_date = $element_detail['Element']['ele_end'];

				$element_permit_type = false;
				if($element_detail['user_permissions']['role'] =='Creator'){
					$projectType = $cky = "m_project";
				}
				if($element_detail['user_permissions']['role'] =='Owner'){
					$projectType = $cky = "r_project";
				}
				if($element_detail['user_permissions']['role'] =='Group Owner'){
					$projectType = $cky = "g_project";
				}
				if($element_detail['user_permissions']['role'] =='Group Sharer'){
					$projectType = $cky = "g_project";
				}
				if($element_detail['user_permissions']['role'] =='Sharer'){
					$projectType = $cky = "r_project";
				}

				if($element_detail['user_permissions']['role'] =='Creator' || $element_detail['user_permissions']['role'] =='Owner' || $element_detail['user_permissions']['role'] =='Group Owner' ){
					//$element_permit_type = true;
				}
				if( isset($element_detail['user_permissions']) && !empty($element_detail['user_permissions']['permit_edit']) ){
					$element_permit_type = true;
				}

				$el_start_date = (isset($element_start_date) && !empty($element_start_date)) ? $this->Wiki->_displayDate($element_start_date,'Y-m-d') : false;
				$el_end_date = (isset($element_end_date) && !empty($element_end_date)) ? $this->Wiki->_displayDate($element_end_date,'Y-m-d') : false;

				$element_status = $elid[0]['ele_status'];

					/*********************** GET WORKSPACE DATA ******************************/
					$workspace_id = $elid['workspaces']['ws_id'];
					$workspace_title = htmlentities($elid['workspaces']['ws_title']);
					$workspace_start_date = $elid['workspaces']['ws_start'];
					$workspace_end_date = $elid['workspaces']['ws_end'];
					$workspace_permit_type = $elid[0]['wsp_permit_edit'];

					$wsp_start_date = (isset($workspace_start_date) && !empty($workspace_start_date)) ? $this->Wiki->_displayDate($workspace_start_date,'Y-m-d') : false;
					$wsp_end_date = (isset($workspace_end_date) && !empty($workspace_end_date)) ? $this->Wiki->_displayDate($workspace_end_date,'Y-m-d') : false;


                    	$element_assigned = false;
                    	$assigned_user_image = SITEURL . 'uploads/user_images/no_photo_small.png';
                    	$not_assigned_user_image = SITEURL . 'uploads/user_images/no_photo_small.png';
						$element_project = $prjid;
						$click_html = '';
						$hover_html = '';
						$receiver_name = 'N/A';
						$receiver_job_title = 'N/A';
						$assigned_class = 'no-reaction';

						$element_assigned = $elid['element_assignments'];

						$assign_creator = '';
						$assign_receiver = '';
						$receiver_selected_flname = 'N/A';
						$receiver_detail['UserDetail']='';
						$taskAssigned = array();
						$creator_detail['UserDetail'] = '';
						$taskAssignedSender = array();

                    	//if(!empty($element_detail['Element']['ele_date_constraints'])) {

	                    	if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) {

	                    		$hover_html .= '<div class="assign-hover">';
								$assign_creator = $element_assigned['created_by'];
								$assign_receiver = $element_assigned['assigned_to'];
								$reaction = $element_assigned['reaction'];
								$assign_modified = $element_assigned['modified'];

								//$creator_detail = get_user_data($assign_creator);
								//$receiver_detail = get_user_data($assign_receiver);

								if( isset($elid[0]['assign_received_user']) && !empty($elid[0]['assign_received_user']) ){
									$taskAssigned = json_decode($elid[0]['assign_received_user'],TRUE);
								}
								if( isset($elid[0]['assign_created_user']) && !empty($elid[0]['assign_created_user']) ){
									$taskAssignedSender = json_decode($elid[0]['assign_created_user'],TRUE);
								}

								if( isset($taskAssigned) && !empty($taskAssigned) ) {
									$receiver_detail['UserDetail'] = $taskAssigned[0];
								}
								if( isset($taskAssignedSender) && !empty($taskAssignedSender) ) {
									$creator_detail['UserDetail'] = $taskAssignedSender[0];
								}


								$profile_pic = '';
								$receiver_name = 'N/A';
								if( isset($receiver_detail['UserDetail']) && !empty($receiver_detail['UserDetail']) ){
									$profile_pic = $receiver_detail['UserDetail']['profile_pic'];
									$receiver_name = $receiver_detail['UserDetail']['full_name'];

									$receiver_selected_flname = $receiver_detail['UserDetail']['first_name'];
									if(isset($receiver_detail['UserDetail']['last_name']) && !empty($receiver_detail['UserDetail']['last_name'])){
										$receiver_selected_flname = $receiver_detail['UserDetail']['first_name']."<br />".$receiver_detail['UserDetail']['last_name'];
									}

								}

								$receiver_job_title = $userDetail['UserDetail']['job_title'];

								if( $assign_receiver != $current_user_id ) {
									$click_html = CHATHTML($assign_receiver, $element_project);
							  	}
								if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
									$assigned_user_image = SITEURL . USER_PIC_PATH . $profile_pic;
								}

							  	$assigned_label = 'Asssigned to: ';
							  	$assigned_to = $receiver_name;
							  	if( $assign_receiver == $current_user_id ) {
							  		$assigned_to = 'Me';
							  	}
							  	if($reaction == 3){
							  		$assigned_label = 'Disengaged by: ';
							  	}
							  	$hover_html .= '<span>'.$assigned_label.$assigned_to.'</span>';


							  	$assigned_by = $creator_detail['UserDetail']['full_name'];
							  	if( $assign_creator == $current_user_id ) {
							  		$assigned_by = 'Me';
							  	}
							  	$hover_html .= '<span>Assigned by: '.$assigned_by.'</span>';

							  	$reaction_label = '';
							  	if($reaction == 1){
							  		$reaction_label = 'Schedule: Accepted';
							  	}
							  	else if($reaction == 2){
							  		$reaction_label = 'Schedule: Not Accepted';
							  	}
							  	$hover_html .= '<span>'.$this->Wiki->_displayDate($assign_modified, 'd M, Y g:iA').'</span>';
							  	$hover_html .= $reaction_label . '</div>';

							  	if( isset($reaction) && !empty($reaction) && $reaction == 1){
							  		$assigned_class = 'accepted';
							  	}
							  	else if( isset($reaction) && !empty($reaction) && $reaction == 2){
							  		$assigned_class = 'not-accepted';
							  	}
							  	else if( isset($reaction) && !empty($reaction) && $reaction == 3){
							  		$assigned_class = 'disengaged';
							  	} else {
									$assigned_class = 'no-reaction';
								}


								/*============ Assigned To user data ======*/

									if( isset($elid[0]['assign_received_user']) && !empty($elid[0]['assign_received_user']) ){
										$taskAssigned = json_decode($elid[0]['assign_received_user'],TRUE);
									}
									if( isset($elid[0]['assign_created_user']) && !empty($elid[0]['assign_created_user']) ){
										$taskAssignedSender = json_decode($elid[0]['assign_created_user'],TRUE);
									}

									if( isset($taskAssigned) && !empty($taskAssigned) ) {
										$receiver_detail['UserDetail'] = $taskAssigned[0];
									}
									if( isset($taskAssignedSender) && !empty($taskAssignedSender) ) {
										$creator_detail['UserDetail'] = $taskAssignedSender[0];
									}





								/*=========================================*/
	                    	}
	                    //}

	                    if(isset($named_params) && !empty($named_params)) {
							if($named_params == 1) {
								if($element_status != 'overdue') continue;
							}
							else if($named_params == 2) {
								if(completing_tdto($element_detail['Element']['id']) <= 0) continue;
							}
							else if($named_params == 3) {
								$el_is_assigned = element_assigned( $element_id );
								// pr($element_id);
								if( ( !isset($el_is_assigned) || empty($el_is_assigned) ) && ($element_status == 'progress' || $element_status == 'overdue') ){
									// die('hahahahah');
								}
								else {
									continue;
								}
							}
							else if($named_params == 4) {
								if($element_status != 'not_spacified') continue;
							}
							else if($named_params == 5) {
								if($element_status != 'completed') continue;
							}
							else if($named_params == 6) {
								if($element_status != 'not_started') continue;
							}
							else if($named_params == 7) {
								if($element_status != 'progress') continue;
							}
						}

						if($element_status != 'not_spacified' && ( !isset($elid['elements']['ele_date_constraints']) || $elid['elements']['ele_date_constraints'] == 0) ){
							 $element_status = 'not_spacified';
						}

						$workspace_title_tip = $workspace_title;
						$workspace_title_tip = html_entity_decode(htmlentities($workspace_title_tip));


						$assignedUserImage = '';
						if( isset($receiver_detail['UserDetail']['profile_pic']) && !empty($receiver_detail['UserDetail']['profile_pic']) && file_exists(USER_PIC_PATH.$receiver_detail['UserDetail']['profile_pic'])) {
							$assignedUserImage = SITEURL . USER_PIC_PATH . $receiver_detail['UserDetail']['profile_pic'];
						} else {
							$assignedUserImage = SITEURL.'images/placeholders/user/user_1.png';
						}


								/* if(  isset($receiver_detail['UserDetail']['first_name']) && !empty($receiver_detail['UserDetail']['first_name']) ){
										$receiver_selected_flname = $receiver_detail['UserDetail']['first_name'];
									}

								if( isset($receiver_detail['UserDetail']['last_name']) && !empty($receiver_detail['UserDetail']['last_name']) && isset($receiver_detail['UserDetail']['first_name']) && !empty($receiver_detail['UserDetail']['first_name']) ){
									$receiver_selected_flname = $receiver_detail['UserDetail']['first_name']."<br />".$receiver_detail['UserDetail']['last_name'];
								} */

					?>

				<div class="line-inner" data-people-title="<?php echo $user_name; ?>" data-default="<?php echo $defaultVal++; ?>" data-element-title="<?php echo htmlentities($element_title); ?>" data-workspace-title="<?php echo htmlentities($workspace_title_tip); ?>" data-element-status="<?php echo htmlentities($element_status); ?>" <?php if($el_start_date) { ?> data-element-start-date="
                        <?php echo $el_start_date; ?>"
                        <?php } ?>
                        <?php if($el_end_date) { ?> data-element-end-date="
                        <?php echo $el_end_date; ?>"
                        <?php } ?>
                        <?php if($wsp_start_date) { ?> data-workspace-start-date="
                        <?php echo $wsp_start_date; ?>"
                        <?php } ?>
                        <?php if($wsp_end_date) { ?> data-workspace-end-date="
                        <?php echo $wsp_end_date; ?>"
                        <?php } ?>
                        <?php if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) { ?>
                        data-element-assigned="<?php echo htmlentities($receiver_name); ?>"
                        <?php }  ?>
                        data-element-original="<?php echo $user_name; ?>" >
										 <!-- people section -->
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-1 prj-people">
                            <div class="img-box data-block">
                                <div class="thumb original-user" style="text-align: center;">
								<?php 
								//if(!empty($element_detail['Element']['ele_date_constraints'])) {
								if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) {
								$ahtml = '';

								if( $element_assigned['assigned_to'] != $current_user_id  ) {
									$ahtml = CHATHTML($element_assigned['assigned_to'],$prjid);
								}
								?>
									<span class="style-popple-icon-out">
<span class="style-popple-icon">
									<a data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $element_assigned['assigned_to'], 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p class='pop_username'><?php echo htmlentities($receiver_detail['UserDetail']['full_name']); ?></p><p><?php echo htmlentities($receiver_detail['UserDetail']['job_title']); ?></p><?php echo $ahtml; ?></div>">
										<img  src="<?php echo $assignedUserImage; ?>"  align="left" width="36" height="36"  />
									</a>
									<?php 									
									if( $receiver_detail['UserDetail']['org_id'] != $current_org['organization_id']){ ?>	
										<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization" ></i>
									<?php } ?>
									</span>
</span>
								<?php

								if( isset($receiver_selected_flname) && !empty($receiver_selected_flname) &&  $receiver_selected_flname != 'N/A' ){?>
									<div class="user-name-n"><?php
										echo $receiver_selected_flname;
									?></div><?php } else { ?>
									<div class="user-name-n"><?php
										echo $user_selected_flname;
									?></div>
								<?php } ?>
								<?php }  else { ?>
                                    <p>Unassigned</p>
								<?php }
								/* } else { ?>
                                    <p>Unassigned</p>
								<?php } */ ?>


                                </div>
                                <?php
								//if(!empty($element_detail['Element']['ele_date_constraints'])) {
								if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) { ?>
                                <div class="thumb assigned-user" style="text-align: center; display: none;">
                                    <a data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $assign_receiver, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p  class='pop_username'><?php echo htmlentities($receiver_name); ?></p><p><?php echo htmlentities($receiver_job_title); ?></p><?php echo $click_html; ?></div>">
										<img  src="<?php echo $assigned_user_image; ?>" class="user-image" align="left" width="40" height="40"  />
									</a>
								</div>

								<?php } else { ?>
									<div class="thumb assigned-user" style="text-align: center; display: none;">
										<a>
											<img  src="<?php echo $not_assigned_user_image; ?>" class="user-image" align="left" width="40" height="40"  />
										</a>
									</div>
								<?php }  


									
								/* }else{ ?>
									<div class="thumb assigned-user" style="text-align: center; display: none;">
										<a>
											<img  src="<?php echo $not_assigned_user_image; ?>" class="user-image" align="left" width="40" height="40"  />
										</a>
									</div>
								<?php } */ ?>


                            </div>
                        </div>
                        <!-- element section -->
						<?php
							$element_title_tip = htmlentities($element_title);
							$element_title_tip = htmlentities($element_title_tip);
						?>
						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-2 prj-elements">
                            <div class="data-block" data-title="<?php echo html_entity_decode(htmlentities(ucfirst($element_title_tip))); ?>">
                            	<?php 
								 
								if( (empty($element_assigned['created_by']) && empty($element_assigned['assigned_to'])) || empty($element_detail['Element']['ele_date_constraints']) ) {  ?>
                            		<i class="fa fa-times text-black icon-task-assign tipText" title="No Assignment"></i>
                            	<?php } else { ?>
                            		<i class="fa <?php echo $assigned_class; ?> text-black icon-task-assign task-assigned" title="Task Leader" data-content='<?php echo $hover_html; ?>'></i>
                            	<?php }  ?>
                                <a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $element_id), true); ?>#tasks" class="data-block-title pop tipText" title="<?php echo ucfirst($element_title); ?>">
                                    <?php echo html_entity_decode(htmlentities(ucfirst($element_title))); ?>
                                </a>
                                <div class="data-block-in">
                                    <div class="data-block-sec cell_<?php echo $element_status; ?>">
                                        <span>Start: <?php echo (empty($element_start_date)) ? 'N/A' : $this->Wiki->_displayDate($element_start_date,'d M, Y');  ?>
											</span>
                                        <span>End: <?php echo (empty($element_end_date)) ? 'N/A' : $this->Wiki->_displayDate($element_end_date,'d M, Y'); ?>
											</span>
                                    </div>
                                    <div class="pull-right edit-date">
                                        <div class="top-btn but-fa-arrow" style="display: block;">
                                            <?php


												$getCriticalStatus = $elid[0]['dep_is_critical'];

												/* $getDependancyStatus = $this->Common->dependancy_status($element_id);

												$predessorCount = $this->Common->ele_dependency_count($element_id, 1);

												$successorCount = $this->Common->ele_dependency_count($element_id, 2); */

												$predessorCount =0;
												$successorCount =0;
												$getDependancyStatus = '';
												if( isset($dependancy_type) && !empty($dependancy_type) ){
													$newDependancyArray = $dependancy_type;
													$newDependancyArraypre = $dependancy_type;
													if( in_array("predecessor",$dependancy_type) && in_array("successor",$dependancy_type) ){
														$getDependancyStatus = "both";
													} else if ( in_array("predecessor",$dependancy_type) && $dependancy_type > 0 ) {
														$getDependancyStatus = 'predessor';
													} else if ( in_array("successor",$dependancy_type) && $dependancy_type > 0) {
														$getDependancyStatus = 'successor';
													} else {
														$getDependancyStatus = 'none';
													}

													//successor count
													$successorCount = count(array_diff($newDependancyArray, ["predecessor"]));
													//predecessor count
													$predessorCount = count(array_diff($newDependancyArraypre, ["successor"]));

												}


												// Critical Status
												if( isset($getCriticalStatus) && $getCriticalStatus == 1 ){
											?>
                                                <i id="redrightarrow_<?php echo $element_id;?>" class="fa fa-arrow-right red-arrow-task tipText" title="Priority Task" style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: default;"></i>
                                                <?php } ?>
                                                <i id="redrightarrow_<?php echo $element_id;?>" class="fa fa-arrow-right red-arrow-task tipText" title="Critical Task" style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: default; display:none;"></i>
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

                                        </div>
                                        <div class="task-sh-remindar">
                                            <?php
											 
											//if($project_permit_type && ($element_status == 'progress' || $element_status == 'not_started' || $element_status == 'overdue')) {
											if($project_permit_type && ($element_status == 'progress' || $element_status == 'not_started' ) ) {

											$reminder_html = '';

											//$get_element_reminder = get_element_reminder($element_id);
											$get_element_reminder = $elid['Reminder'];
											$reminder_id = 0;
											$remind_btn_class = 'btn-default';
											$remind_icon_class = 'black';
											$remind_tooltip_class = 'tipText';
											$remind_tooltip_attr = 'title="Set Reminder"';
											if( !empty($get_element_reminder['rem_userid']) && !empty($get_element_reminder['rem_elementid'] ) ) {

												$reminder_id = $get_element_reminder['rem_id'];
												$reminder_user = $get_element_reminder['rem_userid'];
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
												$reminder_html .= '<div class="reminder_time"><b>For: </b>'.date('M d, Y g:00A',strtotime($get_element_reminder['rem_date'])).'</div>';
												$reminder_html .= '</div>';
												$remind_tooltip_attr = '';
											}
											 ?>
                                                    <a data-toggle="modal" <?php echo $remind_tooltip_attr; ?> data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "element_reminder", $element_id, $reminder_id), true); ?>"  class="btn btn-xs but-remindar <?php echo $remind_btn_class; ?> <?php echo $remind_tooltip_class; ?> calender_modal" data-content='<?php echo $reminder_html; ?>'  href="#" style="padding: 1px 3px;"><i class="icon_reminder <?php echo $remind_icon_class; ?>"></i></a>
                                              <?php } else if( !empty($elid['Reminder']['rem_id']) && $element_status == 'overdue' ){ 
											  
												$reminder_html = '';

											//$get_element_reminder = get_element_reminder($element_id);
											$get_element_reminder = $elid['Reminder'];
											$reminder_id = 0;
											$remind_btn_class = 'btn-default';
											$remind_icon_class = 'black';
											$remind_tooltip_class = 'tipText';
											$remind_tooltip_attr = 'title="Set Reminder"';
											if( !empty($get_element_reminder['rem_userid']) && !empty($get_element_reminder['rem_elementid'] ) ) {

												$reminder_id = $get_element_reminder['rem_id'];
												$reminder_user = $get_element_reminder['rem_userid'];
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
												$reminder_html .= '<div class="reminder_time"><b>For: </b>'.date('M d, Y g:00A',strtotime($get_element_reminder['rem_date'])).'</div>';
												$reminder_html .= '</div>';
												$remind_tooltip_attr = '';
											}
												
													
												?>
													<a data-toggle="modal" <?php echo $remind_tooltip_attr; ?> data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "element_reminder", $element_id, $reminder_id), true); ?>"  class="btn btn-xs but-remindar <?php echo $remind_btn_class; ?> <?php echo $remind_tooltip_class; ?> calender_modal" data-content='<?php echo $reminder_html; ?>'  href="#" style="padding: 1px 3px;"><i class="icon_reminder <?php echo $remind_icon_class; ?>"></i></a>
											  
											  <?php } ?>
                                                    <?php if($element_permit_type) {  ?>
													<a data-updated="<?php echo $element_id; ?>" id="tasksech_<?php echo $element_id;?>" data-type="element" data-toggle="modal" data-target="#modal_large" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $element_id), true); ?>" data-original-title="Task Manager" class="but-sh calender_modal tipText"  href="#"><i class="btn btn-sm btn-default active">TM</i></a>

                                                    <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

						<?php
								$workspace_title_tip = html_entity_decode(htmlentities($workspace_title));
								$workspace_title_tip = html_entity_decode(htmlentities($workspace_title_tip));
							?>
                        <!-- workspace section -->
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-3 prj-workspaces">
                            <div class="data-block" data-title="<?php echo ucfirst($workspace_title_tip); ?>">
                                <a href="<?php echo Router::Url(array("controller" => "projects", "action" => "manage_elements", $prjid,$workspace_id), true); ?>" class="data-block-title pop tipText" title="<?php echo ucfirst($workspace_title_tip); ?>">
                                    <?php echo html_entity_decode(htmlentities($workspace_title)); ?>
                                </a>
                                <?php $workspace_status = workspace_status($workspace_id); ?>
                                <div class="data-block-in">
                                    <div class="data-block-sec cell_<?php echo $workspace_status; ?>">
                                        <span>Start: <?php echo (empty($workspace_start_date)) ? 'N/A' : $this->Wiki->_displayDate($workspace_start_date,'d M, Y');  ?>
											</span>
                                        <span>End: <?php echo (empty($workspace_end_date)) ? 'N/A' : $this->Wiki->_displayDate($workspace_end_date,'d M, Y'); ?>
											</span>
                                    </div>
                                    <?php if($workspace_permit_type) { ?>
                                   <!-- <div class="pull-right edit-date  task-center-arrow"><a data-updated="<?php echo $workspace_id; ?>" data-type="workspace" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_ws_date", $prjid, $workspace_id), true); ?>" data-original-title="Workspace Schedule" class=" calender_modal tipText" title="Workspace Schedule" href="#"><i class="btn btn-sm btn-default active">SH</i></a></div>-->
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <!-- project section -->
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-4 prj-projects">
                        	<div class="project-block">
								<div class="container1 project-thumb-slider">
                        			<div class="owl-carousel">
                				<?php
                				if(isset($elm_users) && !empty($elm_users)) {
	                        		foreach ($elm_users as $key => $userval) {
										$userId = $userval['user_id'];
										$org_ids = $userval['org_id'];
	                        			$user_chat_popover = user_chat_popover($userId, $prjid);
                    			?>
	                        			<div class="item" >
		                    				<img data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $userId, 'admin' => FALSE ), TRUE ); ?>" class="user_popover" data-user="<?php echo $userId; ?>" src="<?php echo $user_chat_popover['user_image']; ?>" data-content="<div class=''><p  class='pop_username'><?php echo htmlentities($user_chat_popover['user_name']); ?></p><p><?php echo htmlentities($user_chat_popover['job_title']); ?></p><?php echo $user_chat_popover['html']; ?></div>" >
		                    			
											<?php if( $org_ids != $current_org['organization_id']){ ?>	
												<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization" ></i>
											<?php } ?>
											
										</div>
                    			<?php
	                        		}
                        		}
                        		?>
		                        	</div>
		                        </div>
                        	</div>
							<?php
								$project_title_tip = htmlentities($project_title);
								$project_title_tip = htmlentities($project_title_tip);
							?>
                        </div>
				</div>
				<?php
						}
					}
				}
			}
		}

		?>


<script type="text/javascript">
$(function(){

	$('.owl-carousel').owlCarousel({
        loop:false,
        margin:2,
        nav:true,
        dots:false,
        autoWidth: true,
        navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>']
    })


/* 	$('.user_popover').popover({
        placement: 'bottom',
        trigger: 'hover',
        html: true,
        container: 'body',
        delay: { show: 50, hide: 400 }
    });

    $('.users_popovers,.pophover').popover({
        placement: 'bottom',
        trigger: 'hover',
        html: true,
        container: 'body',
        delay: { show: 50, hide: 400 }
    }) */;

    $('.task-assigned').popover({
        placement: 'bottom',
        trigger: 'hover',
        html: true,
        container: 'body'
    });


	 $.selectedDates;

    $(".select_dates").daterange({
        defaultDate: new Date(),
        // defaultDate: "+1w",
        numberOfMonths: 2,
        showButtonPanel: false,
        onSelect: function(selected, inst) {
            var divPane = inst.input
                .datepicker("widget")
        },
        beforeShow: function(input, inst) {
            var divPane = $(input)
                .datepicker("widget")
        },
        onClose: function(dateText, inst) {
        	$('.no-result').hide();
            var $input = inst.input,
                $cal_text = $input.parents('.task-section:first').find('.selected_dates:first'),
                $cal_icon = $('.calendar_trigger');

            if (dateText) {
                var dates = dateText.split(' - ');
                	firstDate = $.datepicker.formatDate('M dd, yy', new Date(dates[0])),
                    secDate = (dates[1]) ? $.datepicker.formatDate('M dd, yy', new Date(dates[1])) : '',
                    dateStr = firstDate + ((secDate) ? ' - ' + secDate : ' - ' + firstDate),
                    data = $cal_icon.data();

                $cal_text.hide().html(dateStr + '<i class="fa fa-times pull-right empty-dates" style=" "></i>').slideDown(500);

                $('.projects-line').find('.line-inner').show();
                var showElements = [];

                var Cal_startDate = new Date(dates[0]),
                    Cal_endDate = (dates[1]) ? new Date(dates[1]) : Cal_startDate;

                $('.projects-line').find('.line-inner').each(function() {
                    if ($(this).attr('data-element-start-date') && $(this).attr('data-element-end-date')) {
                        var contentADate = $.datepicker.formatDate('mm/dd/yy', new Date($(this).attr('data-element-start-date'))),
                            contentBDate = $.datepicker.formatDate('mm/dd/yy', new Date($(this).attr('data-element-end-date')))
	                        elem_sd = new Date(contentADate),
	                        elem_ed = new Date(contentBDate);
                        // console.log(( (elem_sd >= Cal_startDate ) && ( (elem_sd >= Cal_startDate ) || (elem_ed <= Cal_endDate )) && (elem_ed <= Cal_endDate )));
                        // console.log("Cal_startDate", Cal_startDate)
                        if( (elem_sd >= Cal_startDate && elem_sd <= Cal_endDate) || (elem_ed >= Cal_startDate && elem_ed <= Cal_endDate) || (elem_sd <= Cal_startDate && elem_ed >= Cal_endDate) ){
                        	showElements.push($(this));
                        }

                    }
                })

                $('.projects-line').find('.line-inner').hide();

                for (var i = 0; i < showElements.length; i++) {
                    showElements[i].fadeIn(i * 100);
                }

                $('.projects-line').each(function() {
                    if ($(this).find('.line-inner:visible').length <= 0) {
                        // $(this).hide();
                        $(this).find('.no-result').show();
                    }
                })
                if (showElements.length <= 0) {
                    $('.no-result').show();
                }
            } else {
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
        $that.parent().slideUp(500, function() {
            $(this).html('');
        })
        $('.projects-line').show()
        $('.projects-line').find('.line-inner').each(function(i, e) {
            $(this).fadeIn(i * 100);
        })

        $('.no-result').hide();

    });
/*
    $("#status-dropdown li a").click(function(e) {
        e.preventDefault();
        var status = $(this).data('status');
        $("#status-dropdown li a i.fa-check").hide();
        $('#status-drop').html($(this).data('text') + ' <span class="fa fa-times bg-red clear_status_filter"></span>');
        $("i.fa-check", $(this)).show();

        $('.no-result').hide();
        $('.projects-line,.line-inner').show()
        $('.line-inner').each(function() {
            if ($(this).data('element-status') == status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })

        $('.projects-line').each(function() {
            if ($('.line-inner:visible', $(this)).length <= 0) {
                // $(this).hide();
                $(this).find('.no-result').show();
            }
        })
        if ($('.projects-line:visible').length <= 0) {
            if ($('.projects-line:visible').length <= 0)
                $('.no-result').show();
        }
        if (status == 'progress' || status == 'overdue') {
            $('.projects-line:visible').each(function() {
                $('.line-inner[data-element-status="' + status + '"]', $(this)).sort(function(a, b) {
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
*/
 /*   $("#people-dropdown li a").click(function(e) {
        e.preventDefault();
        var status = $(this).data('status');
        $("#people-dropdown li a i.fa-check").hide();
        $('#people-drop').html($(this).data('text') + ' <span class="fa fa-times bg-red clear_people_filter"></span>');
        $("i.fa-check", $(this)).show();

        if(status != '' && status == 'assigned') {
        	var $assigned_rows = $('.line-inner');
        	if($assigned_rows.length > 0) {
        		$assigned_rows.each(function(index, el) {
        			$(this).find('.original-user').hide();
        			$(this).find('.assigned-user').show();
        			if($(this).attr('data-element-assigned') != undefined){
        				$(this).attr('data-people-title', $(this).attr('data-element-assigned'))
        			}
        			else{
        				$(this).attr('data-people-title', "")
        			}
        			// console.log($(this).attr('data-element-assigned'));
        		});
        	}
        }
        else if(status != '' && status == 'shared') {
        	var $assigned_rows = $('.line-inner');
        	if($assigned_rows.length > 0) {
        		$assigned_rows.each(function(index, el) {
        			$(this).find('.original-user').show();
        			$(this).find('.assigned-user').hide();
    				$(this).attr('data-people-title', $(this).attr('data-element-original'));
        		});
        	}
        }
    });

    $('body').delegate(".clear_people_filter", 'click', function(e) {

        $('#people-drop').html('Shared <span class="fa fa-times bg-red clear_people_filter"></span>');
        $("#people-dropdown li a[data-status='shared'] i.fa-check").show();
        $("#people-dropdown li a[data-status='assigned'] i.fa-check").hide();
        $('.projects-line,.line-inner').show();
        $('.no-result').hide();

        var $assigned_rows = $('.line-inner');
    	if($assigned_rows.length > 0) {
    		$assigned_rows.each(function(index, el) {
    			$(this).find('.original-user').show();
    			$(this).find('.assigned-user').hide();
    			$(this).attr('data-people-title', $(this).attr('data-element-original'));
    		});
    	}

        return false;
    });
*/
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

    $(".element_list").on("mouseover", function(e) {
        $that = $(this);
        $(".element_list").popover('destroy');

        var element_id = $that.data("elementid");
        var preSucEleCount = $that.data("elecount");
        var depndncytype = $that.data("dependancytype");
        var ele_list_url = $js_config.base_url + 'dashboards/element_list';

        //Predecessors Successors
        if (depndncytype == 1) {
            //$that.attr('title','Predecessors ('+preSucEleCount+')');
            $that.attr('data-original-title', 'Predecessors (' + preSucEleCount + ')');
        }
        if (depndncytype == 2) {
            //$that.attr('title','Successors ('+preSucEleCount+')');
            $that.attr('data-original-title', 'Successors (' + preSucEleCount + ')');
        }

        $.ajax({
            url: ele_list_url,
            type: 'POST',
            data: { element_id: element_id, dependancytype: depndncytype },
            dataType: 'json',
            success: function(response, status, jxhr) {

                $that.attr('data-content', response);
                $that.attr('rel', 'popover');
                $that.popover({
                    container: 'body',
                    html: true,
                });

                $that.popover('show');
                $that.tooltip('hide');

                if (depndncytype == 1) {
                    //$that.attr('title','Predecessors');
                    $that.attr('data-original-title', 'Predecessors');
                }
                if (depndncytype == 2) {
                    //	$that.attr('title','Successors');
                    $that.attr('data-original-title', 'Successors');
                }

            }
        })
    })


})
