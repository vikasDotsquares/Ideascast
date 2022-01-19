<style>.text-dark-g{ color : #aaa;}</style>
<?php if(isset($params) && !empty($params)) {
	$project_id = $params['project_id'];
?>

	<?php if(isset($params['workspaces']) && !empty($params['workspaces'])) { ?>

		<div class="exp-col-wrapper text-right" >
			<a href="#" class="exp-col-ws tipText"  title="Expand/Collapse Workspace" data-target="panels-ws" style="padding: 15px 0">
				<i class="fa fa-plus plus-one" ></i>
				<i class="fa fa-plus plus-two"></i>
				<i class="fa fa-plus plus-three"></i>
			</a>
		</div>


		<?php foreach($params['workspaces'] as $key => $wsp ) {
			$ws_detail = $wsp['Workspace'];
			$pw_detail = $wsp['ProjectWorkspace'];
			$ws_id = $ws_detail['id'];

			$areas = $this->ViewModel->workspace_areas($ws_id, false, false);
			// $areas = get_workspace_areas($ws_id, false);
			// pr($areas);


			$progress_class = 'bg-jeera';

			$total_elements = workspace_elements($ws_id, true, false);
			$total_completed = workspace_elements($ws_id, true, true);

			$percent = 0;
			if( $total_elements > 0 ) {
				$percent = round((( $total_completed/$total_elements ) * 100), 0, 1);
			}
			if(isset($ws_detail['sign_off']) && !empty($ws_detail['sign_off'])) {
				$percent = 100;
				$progress_class = 'bg-red';
			}

			$total_elem = 0;
			if(isset($total_elements) && ($total_elements > 0)){
			$total_elem = $total_elements - $total_completed;
			}


		?>

				<div class="panel panel-<?php echo str_replace('bg-', '', $ws_detail['color_code']) ; ?> panels-workspace " style="clear: both" data-id="panels-ws">
					<div class="panel-heading panels-workspace-heading" >
						<div class="row">
							<div class="col-sm-10 col-md-6 col-lg-8">
								<h4 class="panel-title">
									<span class="trim-text">
										<i class="fa fa-th text-white"></i>&nbsp; <?php echo htmlentities($ws_detail['title']); ?>
									</span>
								</h4>
							</div>
							<div class="col-sm-2 col-md-6 col-lg-4">
								<div class="input-group input-group-bg" style="cursor: default">
									<span class="input-group-addon hidden-xs hidden-sm">Workspace Progress</span>
									<div class="progress-status hidden-sm">
									<div class="progress tipText" title="<?php echo "Tasks: ".$total_completed." Completed / ".$total_elem." Outstanding"; ?>" >
										<div class="progress-bar <?php echo $progress_class ?>" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percent; ?>%">
										</div>
									</div>
									</div>
									<span class="input-group-addon hidden-sm"><?php echo $percent; ?>%</span>
									<span class="input-group-addon btns">
										<a href="<?php echo SITEURL.'projects/manage_elements/'.$params['project_id'].'/'.$ws_id; ?>"><i class="fa  fa-folder-open text-white tipText" title="Open Workspace">&nbsp;</i></a>
										<i class="panel-collapse accordion-toggle text-white tipText" title="Expand/Collapse Workspace" style="cursor:pointer" href="#open_by<?php echo $ws_id;?>" data-parent="#accordion" data-toggle="collapse"></i>
									</span>
								</div>
							</div>
						</div>
					</div>


					<div class="panel-body panel-collapse collapse" id="open_by<?php echo $ws_id;?>" data-toggle="collapse">
						<div class="text-right">

							<a href="#" class="btn btn-primary btn-xs tipText expand-all" title="Expand">
								<i class="fa fa-expand"></i>
							</a>
							<a href="#" class="btn btn-primary btn-xs tipText collapse-all disabled" title="Collapse">
								<i class="fa fa-compress"></i>
							</a>
						</div>

						<div class="input-group" style="margin-bottom: 20px;">
							<div class="input-group-addon" style="vertical-align: top; font-weight: 600">Key Result Target</div>
							<div style=" border-left: 1px solid rgb(88, 148, 194); padding:3px 80px 0 10px; text-align: justify;word-break: break-word; "><?php echo nl2br($ws_detail['description']) ; ?></div>
						</div>


							<div class="row">

							<?php if( isset($areas) && !empty($areas) ) { ?>

								<ul id="tree1" class="tree">

									<?php foreach( $areas as $a => $area ) {

										// Get all elements of the area
										$elements = $this->ViewModel->area_elements( $area['Area']['id'] );
									?>

									<li>
										<a href="#" style="padding: 0px 0px 0px 13px;"><span style="" class="fa fa-list-alt"></span> <?php echo $area['Area']['title']; ?></a>
										<?php if( isset($elements) && !empty($elements) ) { ?>
										<ul>
											<?php foreach( $elements as $e => $element ) { ?>
											<li>
												<a href="#"><span style="" class="icon_element_add_black"></span> <?php echo $element['Element']['title']; ?></a>
												<ul class="summary-wrappers" >
													<div class="el-icons" >
														<a href="#" class="task-summary">Task Outcome</a><br />
														<?php
															$entity_status = $this->Permission->task_status($element['Element']['id']);
															$entity_status = $entity_status[0][0]['ele_status'];

															$tip_text = '';
															$icon_class = 'fa-flag-o';
															if($entity_status == 'progress') {
																$tip_text = 'In Progress';
																$icon_class = 'ps-flag wsp_flagbg bg-progressing';
															}
															else if($entity_status == 'not_spacified') {
																$tip_text = 'Not Set';
																$icon_class = 'ps-flag bg-undefined';
															}
															else if($entity_status == 'not_started') {
																$tip_text = 'Not Started';
																$icon_class = 'ps-flag bg-not_started';
															}
															else if($entity_status == 'overdue') {
																$tip_text = 'Overdue';
																$icon_class = 'ps-flag bg-overdue';
															}
															else if($entity_status == 'completed') {
																$tip_text = 'Signed Off';
																$icon_class = 'ps-flag bg-completed';
															}
														?>
														<i class="btn btn-default btn-xs fa fa-info tipText view-el-sum" title="Task Outcome"></i>
														<i class="btn btn-default btn-xs <?php echo $icon_class; ?> tipText" title="<?php echo $tip_text; ?>"></i>
														<i class="btn btn-default btn-xs fa fa-user-plus view-el-users tipText" title="Team On Task"></i>
														<i class="btn btn-default btn-xs fa fa-folder-open open_el tipText" data-href="<?php echo SITEURL.'entities/update_element/'.$element['Element']['id']; ?>" title="Open Task"></i>
													</div>
													<div class="view-detail" >
														<div class="el-summary " style="display: block;word-break: break-word; " >
															<?php echo $element['Element']['comments']; ?>
														</div>

														<?php

															$users = $this->ViewModel->element_participants($element['Element']['id']);

														?>
														<div class="el-users" >
															<?php
															if(isset($users['participantsOwners']) && !empty($users['participantsOwners'])) {
																$users['participantsOwners'] = array_filter($users['participantsOwners']);
																foreach($users['participantsOwners'] as $ou => $ov) {
																	$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
																	$userDetail = $this->ViewModel->get_user( $ov, $unbind, 1 );
																	$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
															?>
															<a data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL.'shares/show_profile/'.$ov; ?>" href="#"><i class="fa fa-user text-maroon"></i> <?php echo $user_name; ?></a> <br />
															<?php }
															} ?>
															<?php
															if(isset($users['participantsGpOwner']) && !empty($users['participantsGpOwner'])) {
																$users['participantsGpOwner'] = array_filter($users['participantsGpOwner']);
																foreach($users['participantsGpOwner'] as $gou => $gov) {
																	$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
																	$userDetail = $this->ViewModel->get_user( $gov, $unbind, 1 );
																	$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
															?>
															<a data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL.'shares/show_profile/'.$gov; ?>" href="#"><i class="fa fa-user text-maroon"></i> <?php echo $user_name; ?></a> <br />
															<?php }
															} ?>
															<?php
															if(isset($users['sharers']) && !empty($users['sharers'])) {
																$users['sharers'] = array_filter($users['sharers']);
																foreach($users['sharers'] as $su => $sv) {
																	$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
																	$userDetail = $this->ViewModel->get_user( $sv, $unbind, 1 );
																	$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
															?>
															<a data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL.'shares/show_profile/'.$sv; ?>" href="#"><i class="fa fa-user text-maroon"></i> <?php echo $user_name; ?></a> <br />
															<?php }
															} ?>
														</div>
													</div>
												</ul>
											</li>
											<?php } // end element loop ?>
										</ul>
										<?php } // end element check ?>
									</li>
									<?php } // end area loop ?>
								</ul>
							<?php } // end area check ?>
							</div>

					</div>
				</div>

			<?php
	}
}
else { ?>
		<div width="100%" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px"  class="bg-blakish">No Record found.
		</div>
	<?php } ?>
<?php } ?>

 <!-- MODAL BOX WINDOW -->
              <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content"></div>
                </div>
              </div>

              <div class="modal modal-success fade " id="popup_modal_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content"></div>
                </div>
              </div>
<!-- END MODAL BOX -->
<!-- END MODAL BOX -->