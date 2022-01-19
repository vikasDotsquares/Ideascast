<?php
$current_user_id = $this->Session->read('Auth.User.id');

$project_workspace_detail = $this->ViewModel->projectWorkspaces( $project_id, $limit, $current_page );
// pr($project_workspace_detail);
$total_results = 0;
if (isset($project_workspace_detail) && !empty($project_workspace_detail)) {
	$total_results = count($project_workspace_detail);
	$project_level = 0;

	$p_permission = $this->Common->project_permission_details($project_id, $current_user_id);
	$user_project = $this->Common->userproject($project_id, $current_user_id);
	$grp_id = $this->Group->GroupIDbyUserID($project_id, $current_user_id);
	if (isset($grp_id) && !empty($grp_id)) {
		$group_permission = $this->Group->group_permission_details($project_id, $grp_id);

		if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
			$project_level = $group_permission['ProjectPermission']['project_level'];
		}
	}

	if(isset($gpid) && !empty($gpid)) {
		$wwsid = $this->Group->group_work_permission_details($project_id, $gpid);
	}

	if(isset($p_permission) && !empty($p_permission))
	{
		$wwsid = $this->Common->work_permission_details($project_id, $current_user_id);

	}
	if(((isset($user_project)) && (!empty($user_project))) ||  (isset($p_permission['ProjectPermission']['project_level'])  && 	$p_permission['ProjectPermission']['project_level'] == 1 ) || (isset($project_level) && $project_level==1) || (isset($wwsid))){

		foreach ($project_workspace_detail as $key => $project_workspace) {
			$project_workspace_data = ( isset($project_workspace['ProjectWorkspace']) && !empty($project_workspace['ProjectWorkspace'])) ? $project_workspace['ProjectWorkspace'] : null;
			$workspaceArray = ( isset($project_workspace['Workspace']) && !empty($project_workspace['Workspace'])) ? $project_workspace['Workspace'] : null;

			// Show only the workspaces that are selected to display into the list. This status field is also used to show workspace names in leftbar menus.
			$leftbar_status = $project_workspace_data['leftbar_status'];

			if ($leftbar_status) {
				if(( ((isset($wwsid) && !empty($wwsid))) &&  (in_array($project_workspace_data['id'], $wwsid)))  || ((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) {

					if (isset($workspaceArray['id']) && !empty($workspaceArray['id'])) {
						$workspace_areas = $this->ViewModel->workspace_areas($workspaceArray['id']);

						$totalAreas = $totalActElements = $totalInActElements = $totalUsedArea = $percent = 0;

						if (isset($workspace_areas) && !empty($workspace_areas)) {
							$userProjectId = project_upid($project_id);

							$progress_data = $this->ViewModel->countAreaElements($workspaceArray['id'], null, null, $project_id, $userProjectId, $user_project, $p_permission);
							if (isset($progress_data) && !empty($progress_data)) {
								$totalAreas = $progress_data['area_count'];
								$totalUsedArea = $progress_data['area_used'];
								$totalActElements = $progress_data['active_element_count'];
								$totalInActElements = 0;

								$percent = ($totalUsedArea > 0 && $totalAreas > 0) ? ($totalUsedArea * 100) / $totalAreas : 0;
							}// end if (isset($progress_data)
						}// end if (isset($workspace_areas)

						$class_name = (isset($workspaceArray['color_code']) && !empty($workspaceArray['color_code'])) ? $workspaceArray['color_code'] : 'bg-gray';

						$create_elements_link = Router::url(array('controller' => 'projects', 'action' => 'manage_elements', $project_id, $workspaceArray['id']));

						if( isset($workspaceArray['studio_status']) && empty($workspaceArray['studio_status']) ) {
						?>
							<div class="workspace-tasks-sec-top" id="<?php echo $project_workspace_data['id']; ?>" data-value="<?php echo $workspaceArray['id']; ?>" data-id="<?php echo $workspaceArray['id']; ?>" data-pid="<?php echo $project_id; ?>">
						        <div class="workspace-col-5 padd8 text-center tcont colm-1">

								   <div class="small-box task-inworks panel <?php echo $class_name ?>">
										<a class="inner" href="<?php echo $create_elements_link; ?>">
											<strong class="text-ellipsis tipText" style="text-transform:none !important" title="<?php echo htmlentities($workspaceArray['title'], ENT_QUOTES); ?>" data-text="<?php echo htmlentities($workspaceArray['title'], ENT_QUOTES); ?>">
								 				<?php echo htmlentities($workspaceArray['title'], ENT_QUOTES);?>
								 			</strong>
											<?php
												$templateDataCount = $this->ViewModel->getWorkspaceTemplateDetails($workspaceArray['template_id']);
												$total_areas = (isset($templateDataCount['TemplateDetail']) && !empty($templateDataCount['TemplateDetail'])) ? count($templateDataCount['TemplateDetail']) : 0;

												$content = '<div class="popover-template-detail">
													<small class="timg">'.workspace_template( $workspaceArray['template_id'], true ).'</small>
													<small class="tdetail">
														<small style="font-weight: 500; font-size: 13px; "><b class="num">'.$total_areas.' Area Workspace</small>
													</small>
												</div>';
											?>
						            		<div class="reminder-sharing-d-out">
												<span style="font-size: 14px; float:left;">
													<i class="fa fa-th template-pophover"  data-content='<?php echo $content; ?>' data-html="true"></i>
												</span>
												<div class="reminder-sharing-d-in">
													<span class="text-muted date-time">
														<span>Created:
															<?php
																echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s',strtotime($workspaceArray['created'])),$format = 'd M Y') : 'N/A';
															?>
														</span>
														<span>Updated:
															<?php
															echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s',strtotime($workspaceArray['modified'])),$format = 'd M Y') : 'N/A';
															?>
														</span>
													</span>

													<span class=" sten-date" style=" ">
														<span>Start:
														<?php
														 echo ( isset($workspaceArray['start_date']) && !empty($workspaceArray['start_date'])) ? date('d M, Y',strtotime($workspaceArray['start_date'])) : 'N/A';
														?></span>
														<span>End:
														<?php
														echo ( isset($workspaceArray['end_date']) && !empty($workspaceArray['end_date'])) ? date('d M, Y',strtotime($workspaceArray['end_date'])) : 'N/A';

														?></span>
													</span>
								            	</div>
						            		</div>
										</a>
									</div>
						        </div>
						        <div class="workspace-col-3 padd8 tcont description-wroks colm-2">
						        	<?php
										$workspacetip = $workspaceArray['description'];
									?>
									<div style="max-height: 80px;  overflow: hidden; max-width:408px; text-overflow: ellipsis; word-break: break-word;" data-placement="top" data-content="<div class='template_create'><?php echo nl2br(htmlentities($workspacetip, ENT_QUOTES)) ; ?></div> " class="key_target key_target_wsp   " data-toggle="popover" data-trigger="hover" data-delay="{show: 300, hide: 400}">
										<?php echo nl2br(htmlentities($workspaceArray['description'], ENT_QUOTES)) ; ?>
									</div>
						        </div>
						        <div class="workspace-col-1 padd8 text-center tcont colm-3">
									<span class="text-center el-icons">
										<ul class="list-unstyled">
											<li>
												<span class="label bg-mix" title=""><?php echo (isset($totalActElements) && !empty($totalActElements) ) ? $totalActElements : 0; ?></span>
												<span class="icon_elm btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Tasks') ?>"  ></span>
											</li>
											<li>
												<span class="label bg-mix">

													<?php echo (isset($progress_data['overdue_element_count']) && !empty($progress_data['overdue_element_count']) ) ? $progress_data['overdue_element_count'] : 0; ?>
												</span>
												<span class="btn btn-xs bg-element tipText no-change" title="Tasks Overdue"  href="#"><i class="fa fa-exclamation"></i></span>
											</li>
										</ul>
									</span>
						    	</div>
						    	<div class="workspace-col-4 padd8 text-center tcont colm-4">
									<span class="text-center el-icons">
										<ul class="list-unstyled">
								 			<li>
											  	<span class="label bg-mix">
													<?php
													echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['links']) && !empty($progress_data['assets_count']['links'])) ? $progress_data['assets_count']['links'] : 0 ) : 0;
													?>
											  	</span>
											  	<span class="btn btn-xs bg-maroon tipText no-change" title="<?php echo tipText('Links') ?>"  href="#"><i class="fa fa-link"></i></span>
										 	</li>
								 			<li>
									  			<span class="label bg-mix">
													<?php
													echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['notes']) && !empty($progress_data['assets_count']['notes'])) ? $progress_data['assets_count']['notes'] : 0 ) : 0;
													?>
									  			</span>
									  			<span class="btn btn-xs bg-purple tipText no-change" title="<?php echo tipText('Notes') ?>"  href="#"><i class="fa fa-file-text-o"></i></span>
								 			</li>
								 			<li>
										  		<span class="label bg-mix">
													<?php //pr($progress_data['assets_count']);
													echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['docs']) && !empty($progress_data['assets_count']['docs'])) ? $progress_data['assets_count']['docs'] : 0 ) : 0;
													?>
										  		</span>
										  		<span class="btn btn-xs bg-blue tipText no-change" title="<?php echo tipText('Documents') ?>"  href="#"><i class="fa fa-folder-o"></i></span>
									 		</li>

									 		<li>
										  		<span class="label bg-mix">
												<?php
												echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['mindmaps']) && !empty($progress_data['assets_count']['mindmaps'])) ? $progress_data['assets_count']['mindmaps'] : 0 ) : 0;
												?>
										  		</span>
										  		<span class="btn btn-xs bg-green tipText no-change" title="<?php echo tipText('Mind Maps') ?>"  href="#"><i class="fa fa-sitemap"></i></span>
									 		</li>


									 		<li>
									 			<?php $varDecision =  show_counters($workspaceArray['id'], 'decision'); ?>
										  		<span class="label bg-mix"><?php echo (isset($varDecision) && !empty($varDecision) && $varDecision > 0) ? $varDecision: 0; ?></span>
										  		<span class="btn btn-xs bg-orange tipText no-change" title="<?php echo tipText('Live Decisions') ?>"  href="#"><i class="far fa-arrow-alt-circle-right"></i></span>
									 		</li>
									 		<li>
										  		<span class="label bg-mix">
									  			<?php
										  		echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['feedbacks']) && !empty($progress_data['assets_count']['feedbacks'])) ? $progress_data['assets_count']['feedbacks'] : 0 ) : 0;
										  		?></span>
										  		<span class="btn btn-xs bg-teal tipText no-change" title="<?php echo tipText('Live Feedbacks') ?>"  href="#"><i class="fa fa-bullhorn"></i></span>
									 		</li>
									 		<li>
										  		<span class="label bg-mix">
												<?php
												echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['votes']) && !empty($progress_data['assets_count']['votes'])) ? $progress_data['assets_count']['votes'] : 0 ) : 0;
												?>
										  		</span>
										  		<span class="btn btn-xs bg-yellow tipText no-change" title="<?php echo tipText('Live Votes') ?>"  href="#"><i class="fa fa-inbox"></i></span>
									 		</li>
										</ul>
									</span>
						    	</div>
						    	<div class="workspace-col-2 padd8 text-center tcont colm-5">
									<div class="btn-group btn-actions">
										<?php  $wid = encr($workspaceArray['id']);

									 	if((isset($wwsid) && !empty($wwsid))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission)  && $p_permission['ProjectPermission']['project_level'] ==1 )    )  )

										if(isset($gpid) && (isset($wwsid) && !empty($wwsid))){
										$wsEDDDIT =  $this->Group->group_wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

										$wsDELETE =  $this->Group->group_wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

										}else if((isset($wwsid) && !empty($wwsid))){
										$wsEDDDIT =  $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));

										$wsDELETE =  $this->Common->wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));
										}

								   		if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) ) { ?>
											<a class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php tipText('Edit Workspace', false); ?>"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_id, $workspaceArray['id'], 'admin' => FALSE), TRUE); ?>" id="btn_select_workspace" >
									 			<i class="fa fa-fw fa-pencil"></i>
											</a>
										<?php  } ?>
										<?php
										if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )    ) ) { ?>
											<a class="btn btn-xs <?php echo $class_name ?> tipText color_bucket" title="Color Options"  href="#" style="margin-right: 0 !important;">
												<i class="fa fa-paint-brush"></i>
											</a>
											<small class="ws_color_box" style="display: none; width: 86px">
												<small class="colors btn-group">
													<b data-color="bg-red" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
													<b data-color="bg-blue" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></b>
													<b data-color="bg-maroon" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
													<b data-color="bg-aqua" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
													<b data-color="bg-yellow" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
													<b data-color="bg-teal" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></b>
													<b  data-color="bg-purple" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></b>
													<b data-color="bg-navy" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></b>
													<b data-color="bg-gray" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Remove Color"><i class="fa fa-times"></i></b>
											   	</small>
										  	</small>

							  			<?php  } ?>

											<a class="btn btn-xs <?php echo $class_name ?> tipText open_ws" title="<?php tipText('Open Workspace', false); ?>"  href="#" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'manage_elements', $project_id, $workspaceArray['id'])); ?>" >
												<i class="fa fa-fw fa-folder-open"></i>
											</a>
								 			<?php
											if(((isset($wwsid) && !empty($wwsid)) && ($wsDELETE==1))  || (isset($project_level) && $project_level==1) || (((isset($user_project)) && (!empty($user_project))) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) )  { ?>
											<a data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "workspaces", "action" => "delete_an_item", $project_id, $workspaceArray['id'], $project_workspace_data['id'], 'admin' => FALSE ), true ); ?>" type="button" class="btn btn-xs tipText delete-an-item <?php echo $class_name ?>" title="Delete">
												<i class="fa fa-trash"></i>
											</a>

											<a class="btn btn-xs <?php echo $class_name ?> tipText multi-remove hide-multi-delete" title="Select" href="#" data-wid="<?php echo $workspaceArray['id']; ?>" >
												<i class="fa fa-square-o"></i>
											</a>
										<?php  } ?>

							   		</div>
						    	</div>
						    </div>
						<?php
						}// end if( isset($workspaceArray['studio
					}// end if (isset($workspaceArray['id'])
				} // end if(( ((isset($wwsid) && !empty($wwsid)))
			} // end if ($leftbar_stat
		}// end foreach ($project_workspace_detail
	}// end if(((isset($user_project)) && (!emp
}
?>
<script type="text/javascript">
	$(function(){
		<?php if(isset($total_results) && !empty($total_results)) { ?>
			var total_results = '<?php echo $total_results; ?>';
			$('.show-less').data('results', total_results);
		<?php } ?>

		$('.color_bucket').each(function() {
	        var $color_box = $(this).parent().find('.ws_color_box')

	        $(this).data('ws_color_box', $color_box)
	        $color_box.data('color_bucket', $(this))
	    })

		$('.template-pophover').popover({
			trigger: 'hover',
			placement: 'bottom',
			html: true,
			container: 'body',
			// delay: {show: 50, hide: 400}
		})

		$('.key_target').popover({
			trigger: 'hover',
			placement: 'top',
			html: true,
			container: 'body',
			// delay: {show: 50, hide: 400}
		})
		if( !$('body').hasClass('sidebar-collapse') ) {
			$.popover_hack();
		}
		$('.text-ellipsis').tooltip({
			placement: 'top-left'
		})

	})

</script>