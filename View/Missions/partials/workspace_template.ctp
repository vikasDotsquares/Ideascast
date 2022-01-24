<?php echo $this->Html->css('projects/manage_elements');
 // echo $this->Html->script('projects/plugins/context-menu/mission.context', array('inline' => true));
 echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
 ?>

<?php
$project_id = workspace_pid($workspace_id);


	$workspaceData = getByDbId('Workspace', $workspace_id);
	$workspaceArray = $workspaceData['Workspace'];

	$class_name = (isset($workspaceArray['color_code']) && !empty($workspaceArray['color_code'])) ? $workspaceArray['color_code'] : 'bg-gray';
	$workspace_areas = $this->ViewModel->workspace_areas($workspaceArray['id']);

	$w_a_total = $this->ViewModel->workspace_areas($workspaceArray['id'], true);

	$totalAreas = $totalActElements = $totalInActElements = $totalUsedArea = $percent = 0;

	if ($w_a_total > 0) {

		$progress_data = $this->ViewModel->countAreaElements($workspaceArray['id']);
		if (isset($progress_data) && !empty($progress_data)) {
			// pr($progress_data);
			$totalAreas = $progress_data['area_count'];
			$totalUsedArea = $progress_data['area_used'];
			$totalActElements = $progress_data['active_element_count'];
			$totalInActElements = 0;

			$percent = ($totalUsedArea > 0 && $totalAreas > 0) ? ($totalUsedArea * 100) / $totalAreas : 0;
		}
	}

	$wsp_disabled = '';
	$wsp_tip = '';
	$wsp_cursor = '';
	if(isset($workspaceArray['sign_off']) && !empty($workspaceArray['sign_off']) && $workspaceArray['sign_off'] == 1 ){
		$wsp_disabled = 'disable';
		$wsp_tip = "Workspace Is Signed Off";
		$wsp_cursor ="cursor:default !important; ";
	}



// pr($data['workspace'], 1);
?>
<style type="text/css">
	#workspace td .box-body.drop_element {
		position: relative;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
		align-content: flex-start;
		flex-direction: row;
	}
	#workspace td .box-body.drop_element .el.panel{
		border: 1px solid #80848c;
		border-radius: 4px;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-flow: column;
		-webkit-flex-flow: column;
		flex-flow: column;
	}
	#workspace td .box-body.drop_element .el.panel .inner-el{
		width: 100%;
		border: 1px solid #80848c;
		border-radius: 4px;
	}
	.drag-start {
	    z-index: 1049 !important;
	}
</style>
<div id="table-responsive">
	<table class="table" id="workspace_details" >
		<tbody>
			<tr>
				<td width="30%" >
					<div class="small-box panel <?php echo $class_name ?>" style="cursor:default;">

						<a style="cursor:default;"   class="inner  wsp-title" title='<?php echo htmlentities($workspaceArray['title']); ?>' href="#">

							<strong class="ellipsis-word"><?php // workspace_title truncate
							echo htmlentities($workspaceArray['title']); //echo _substr_text($workspaceArray['title'], 29); ?></strong>

							<span class="text-muted date-time">
								<span>Created:
								<?php
								//echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? date('d M Y', strtotime($workspaceArray['created'])) : 'N/A';
								echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['created'])),$format = 'd M Y') : 'N/A';

								?></span>
								<span>Updated:
								<?php
								//echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? date('d M Y', strtotime($workspaceArray['modified'])) : 'N/A';
								echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['modified'])),$format = 'd M Y') : 'N/A';

								?></span>
							</span>
						</a>

					</div>
				</td>
				<td width="29%" style="vertical-align: top ! important; font-size: 12px; line-height: 16px; " class=" ">
					<div style="max-height: 65px; overflow: hidden; max-width:445px; word-break: break-all; " class="key_target">
						<?php echo  nl2br($workspaceArray['description']) ; ?>
					</div>
				</td>
				<td width="8%" >
					<span class="text-center el-icons" >
						<ul class="list-unstyled">
							<li>
								<span class="label bg-mix" title=""><?php echo ($totalActElements ); ?></span>
								<span style="cursor:default;"  class=" btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Tasks ') ?>"><i class="asset-all-icon taskwhite"></i></span>
							</li>
							<li>
								<span class="label bg-mix">
									<?php
										// get areas
										$element_detail = null;
										$sum_value = 0;
										$area_id = $this->ViewModel->workspace_areas($workspaceArray['id'], false, true);
										$el = $this->ViewModel->area_elements($area_id);
										if (!empty($el)) {
											$element_detail = _element_detail(null, $el);

											if (!empty($element_detail)) {
												$filter = arraySearch($element_detail, 'date_constraint_flag');
												if (!empty($filter)) {
													$sum_value = array_sum(array_columns($element_detail, 'date_constraint_flag'));
												}
											}
										}
										echo $sum_value;
									?>
								</span>
								<span style="cursor:default;"  class="btn btn-xs bg-element no-change tipText" title="Tasks Overdue"  href="#"><i class="asset-all-icon overduewhite"></i></span>
							</li>
						</ul>
					</span>

				</td>
				<td width="23%" >
					<span class="text-center el-icons" >
						<ul class="list-unstyled">
							<li>
								<span class="label bg-mix">
									<?php
										echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['links']) && !empty($progress_data['assets_count']['links'])) ? $progress_data['assets_count']['links'] : 0 ) : 0;
									?>
								</span>
								<span style="cursor:default;"  class="btn btn-xs bg-maroon no-change tipText" title="<?php echo tipText('Links ') ?>"  href="#"><i class="asset-all-icon linkwhite"></i></span>
							</li>
							<li>
								<span class="label bg-mix">
									<?php
										echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['notes']) && !empty($progress_data['assets_count']['notes'])) ? $progress_data['assets_count']['notes'] : 0 ) : 0;
									?>
								</span>
								<span style="cursor:default;"  class="btn btn-xs bg-purple no-purple tipText" title="<?php echo tipText('Notes ') ?>"  href="#"><i class="asset-all-icon notewhite"></i></span>
							</li>
							<li>
								<span class="label bg-mix">
									<?php
										echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['docs']) && !empty($progress_data['assets_count']['docs'])) ? $progress_data['assets_count']['docs'] : 0 ) : 0;
									?>
								</span>
								<span style="cursor:default;"  class="btn btn-xs bg-blue no-change tipText" title="<?php echo tipText('Documents ') ?>"  href="#"><i class="asset-all-icon documentwhite"></i></span>
							</li>

							<li>
								<span class="label bg-mix">
									<?php
										echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['mindmaps']) && !empty($progress_data['assets_count']['mindmaps'])) ? $progress_data['assets_count']['mindmaps'] : 0 ) : 0;
									?>
								</span>
								<span style="cursor:default;"  class="btn btn-xs bg-green no-change tipText" title="<?php echo tipText('Mind Maps ') ?>"  href="#"><i class="asset-all-icon mindmapwhite"></i></span>
							</li>


							<li>
								<span class="label bg-mix"><?php echo show_counters($workspaceArray['id'], 'decision'); ?></span>
								<span style="cursor:default;"  class="btn btn-xs bg-orange no-change tipText" title="<?php echo tipText('Live Decisions ') ?>"  href="#"><i class="asset-all-icon decisionwhite"></i></span>
							</li>
							<li>
								<span class="label bg-mix"><?php
									echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['feedbacks']) && !empty($progress_data['assets_count']['feedbacks'])) ? $progress_data['assets_count']['feedbacks'] : 0 ) : 0;
								?></span>
								<span style="cursor:default;"  class="btn btn-xs bg-teal no-change tipText" title="<?php echo tipText('Live Feedbacks ') ?>"  href="#"><i class="asset-all-icon feedbackwhite"></i></span>
							</li>

							<li>
								<span class="label bg-mix">
									<?php
										echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['votes']) && !empty($progress_data['assets_count']['votes'])) ? $progress_data['assets_count']['votes'] : 0 ) : 0;
									?>
								</span>
								<span style="cursor:default;"  class="btn btn-xs bg-yellow no-change tipText" title="<?php echo tipText('Live Votes ') ?>"  href="#"><i class="asset-all-icon votewhite"></i></span>
							</li>
						</ul>
					</span>
				</td>
				<td width="10%" style="vertical-align: middle ! important; text-align: center ! important; " class="ws-actions">
					<div class="btn-group btn-actions">

						<?php  if( isset($wsp_disabled) && !empty($wsp_disabled) ){ ?>
							<a class="btn btn-sm <?php echo $class_name ?> tipText <?php echo $wsp_disabled;?>" title="<?php tipText($wsp_tip, false); ?>"  style="<?php echo $wsp_cursor;?>"  >
								<i class="fa fa-fw fa-pencil"></i>
							</a>

							<a class="btn btn-sm <?php echo $class_name ?> tipText  <?php echo $wsp_disabled;?> " title="<?php tipText($wsp_tip, false); ?>"   style="margin-right: 0 !important; <?php echo $wsp_cursor;?>">
								<i class="fa fa-paint-brush"></i>
							</a>

						<?php } else { ?>
							<a class="btn btn-sm <?php echo $class_name ?> tipText " title="<?php tipText('Update Workspace Details', false); ?>"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_id, $workspaceArray['id'], 'admin' => FALSE), TRUE); ?>" id="btn_select_workspace" >
								<i class="fa fa-fw fa-pencil"></i>
							</a>

							<small class="ws_color_box " style="display: none; ">
								<small class="colors btn-group" style="width:100%;">
									<b data-color="bg-red" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
									<b data-color="bg-blue" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></b>
									<b data-color="bg-maroon" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
									<b data-color="bg-aqua" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
									<b data-color="bg-yellow" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
									<!-- <b data-color="bg-orange" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Orange"><i class="fa fa-square text-orange"></i></b>	-->
									<b data-color="bg-teal" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></b>
									<b  data-color="bg-purple" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></b>
									<b data-color="bg-navy" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></b>
									<b data-color="bg-gray" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Remove Color"><i class="fa fa-times"></i></b>
								</small>
							</small>
							<a class="btn btn-sm <?php echo $class_name ?> tipText open_ws_colors " title="Color Options"  href="#" style="margin-right: 0 !important;">
								<i class="fa fa-paint-brush"></i>
							</a>
						<?php } ?>
					</div>

				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php
/****************** Workspace Detail ******************************/
?>

<div id="workspace" class="table-responsive">
<?php
// pr($data);
if( isset( $data['templateRows'] ) && !empty( $data['templateRows'] ) ) {
?>
	<table class="table table-bordered" id="tbl">

		<tbody>
			<tr>
				<?php
					$max_boxes = max( array_map( 'count', $data['templateRows'] ) );
					$setWidth = 0;
					$row_group = $data['templateRows'];

					foreach( $row_group as $row_id => $row_data ) {
						$setWidth++;

						foreach( $row_data as $row_index => $row_detail ) {

							$last = false;
							$colspan = $rowspan = '';

							if( $row_detail['size_w'] > 0 && $row_detail['size_h'] > 0 ) {
								if( $row_detail['size_w'] > 1 ) {
									$colspan = ' colspan="' . $row_detail['size_w'] . '" ';
								}
								if( $row_detail['size_h'] > 1 ) {
									$rowspan = ' rowspan="' . $row_detail['size_h'] . '" ';
								}
							}

							$tdWidth = 0;
							if( isset( $setWidth ) && !empty( $setWidth ) ) {
								$tdWidth = ( 100 / $max_boxes );
								//echo $tdWidth;
								$tdWidth = number_format( $tdWidth, 4 );
								$tdWidth = $tdWidth . '%';
								//echo $tdWidth ;
							}

						?>

					<?php
					$icon = '<i class="fa fa-check" style="opacity: 0"></i>';
					 ?>

					<td <?php echo (!empty( $colspan )) ? $colspan : ''; ?> <?php echo (!empty( $rowspan )) ? $rowspan : ''; ?> valign="top" class="area_box" id="<?php echo $row_detail['area_id']; ?>"  text <?php if( $setWidth == 1 ) {   ?> width="<?php echo $tdWidth; ?>" <?php } ?> >

						<div class="box box-success box-area no-margin">
							<div class="box-header">

								<a href="#"
								class="btn btn-xs area_elements_toggle el-toggle-tooltip"
								data-toggle="tooltip" data-trigger="hover"
								data-placement="right" role="tooltip"
								data-original-title="Toggle All Tasks" style="float: left; ">
									<i class="fa fa-fw fa-bars" style="font-size: 24px;"></i>
								</a>
								<span class="popover-markup">

									<h3 class="box-title area-box-title truncate trim_text" id="area_<?php echo $row_detail['area_id'] ?>-toggler" style="float: left; width: 55%; text-align: left;">

										<a href="javascript:;"
										data-pop-form="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'popover', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
										data-remote="<?php echo Router::Url( array( 'controller' => 'workspaces', 'action' => 'update_area', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
										rel="areaInfo_<?php echo $row_detail['area_id']; ?>" id="<?php echo $row_detail['area_id']; ?>" class="area_title trim_text"  data-full-title="<?php echo htmlentities($row_detail['title']); ?>"> <?php echo htmlentities($row_detail['title']); ?></a>
									</h3>

									<div id="content_<?php echo $row_detail['area_id']; ?>" class="popover_content" style="display: none">
										<p>
											<input type="text" id="txtTitle" name="data[Area][title]" value="<?php echo $row_detail['title'] ?>" class="form-control input_holder" />
										</p>
										<p class="text-center">
											<input type="submit" name="Submit" value="Update" id="" class="btn btn-jeera-submit" />
											<button type="submit" class="btn btn-jeera-dismiss">Cancel</button>
										</p>
									</div>

								</span>

								<h3 class="box-title area-box-title mob_scrn trim_text"
								id="area_<?php echo $row_detail['area_id'] ?>-toggler" style="float: left; width: 65%; text-align: left;">

									<a href="javascript:;"
									data-pop-form="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'popover', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
									data-remote="<?php echo Router::Url( array( 'controller' => 'workspaces', 'action' => 'update_area', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
									rel="areaInfo_<?php echo $row_detail['area_id']; ?>" id="<?php echo $row_detail['area_id']; ?>" class="area_title trim_text"  data-full-title="<?php echo $row_detail['title']; ?>"> <?php echo $row_detail['title']; ?></a>
								</h3>

								<a class="my-menu toggle_area_menus" href="#" style="float: right; margin-top: 3px !important;">
									<span class="fa fa-list"></span>
								</a>

								<div class="pull-right area_menus" style="">
									<a href="#" class="btn btn-default btn-xs btn-paste fade-out dropdown-toggle" data-toggle="dropdown" style="position: relative; ">
										<i class="far fa-clone"></i>
									</a>
									<ul class="dropdown-menu paste-menus">
										<li><a href="#" name="paste" id="paste"><i class="fa fa-paste"></i> Paste</a></li>
										<li><a href="#" class="cancel-paste" style="color: #c00 !important;"><i class="fa fa-times"></i> Cancel</a></li>
									</ul>
									<div class="btn-group ">
										<?php
											$user_id = $this->Session->read('Auth.User.id');
											$workspace_id = $row_detail['workspace_id'];
											$project_id = workspace_pid($workspace_id);

											$message = '';

											$workspace_detail = getByDbId('Workspace', $workspace_id);

											if($workspace_detail['Workspace']['sign_off'] !=1){

												/* if((isset($workspace_detail['Workspace']['end_date']) && $workspace_detail['Workspace']['end_date']!= NULL && $workspace_detail['Workspace']['end_date'] >= date('Y-m-d')) && (isset($workspace_detail['Workspace']['start_date']) && $workspace_detail['Workspace']['start_date'] != NULL && $workspace_detail['Workspace']['start_date'] <= date('Y-m-d 00:00:00'))){
												?>
												<button
													class="btn btn-success text-white btn-xs tipText add_element "
													data-original-title="<?php echo tipText( 'Quick Task Create' ) ?>"
													data-toggle="modal" data-target="#modal_box"
													id="add_element"
													data-area="<?php echo $row_detail['area_id']; ?>"
													data-id="<?php echo $row_detail['area_id']; ?>"
													data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
													>
														<i class="fa fa-plus"></i>
												</button>
												<?php } */

													if((isset($workspace_detail['Workspace']['start_date']) && $workspace_detail['Workspace']['start_date'] > date('Y-m-d 00:00:00')) ){
														if( FUTURE_DATE == 'off' ){
															$message ="Please add a schedule to this Workspace first.";
														}
													}
													if((isset($workspace_detail['Workspace']['end_date']) && $workspace_detail['Workspace']['end_date'] < date('Y-m-d')) ){
														$message ="You cannot add an Element because Workspace end date has passed.";
													}

												} else if(isset($workspace_detail['Workspace']['start_date'])){
													$message ="You cannot add an Element because Workspace has Signoff.";
												}
												if(!isset($workspace_detail['Workspace']['start_date'])){
													$message ="Please add a schedule to this workspace first.";
												}
												if( empty($message) ){

													if( isset($wsp_disabled) && !empty($wsp_disabled) ){
													?>

													<button
														class="btn btn-success text-white btn-xs tipText <?php echo $wsp_disabled;?> "
														data-original-title="<?php echo tipText( $wsp_tip ) ?>"
														id="add_element"
														style="<?php echo $wsp_cursor;?>"
														>
															<i class="fa fa-plus"></i>
													</button>

													<?php } else {?>
													<button
														class="btn btn-success text-white btn-xs tipText add_element "
														data-original-title="<?php echo tipText( 'Add Task' ) ?>"
														data-toggle="modal" data-target="#modal_box"
														id="add_element"
														data-area="<?php echo $row_detail['area_id']; ?>"
														data-id="<?php echo $row_detail['area_id']; ?>"
														data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
														>
															<i class="fa fa-plus"></i>
													</button>
													<?php } ?>
												<?php
												} else {
													if(isset($message) && !empty($message)){

														if( isset($wsp_disabled) && !empty($wsp_disabled) ){
													?>

													<button
														class="btn btn-success text-white btn-xs tipText <?php echo $wsp_disabled;?> "
														data-original-title="<?php echo tipText( $wsp_tip ) ?>"
														id="add_element"
														style="<?php echo $wsp_cursor;?>"
														>
															<i class="fa fa-plus"></i>
													</button>

													<?php } else { ?>

														<button data-title="<?php echo $message;?>"
														class="btn btn-success text-white btn-xs workspace tipText add_element disable"
														data-original-title="<?php echo tipText( 'Add Task' ) ?>"
														data-area="<?php echo $row_detail['area_id']; ?>"
														data-id="<?php echo $row_detail['area_id']; ?>"
														id="add_element"
														data-remote="">
															<i class="fa fa-plus"></i>
														</button>
													<?php }
													}
												}

										$area_detail = getByDbId('Area', $row_detail['area_id']);
										// pr($area_detail);
										$tooltip_text = ( isset( $area_detail['Area']['tooltip_text'] ) && !empty( $area_detail['Area']['tooltip_text'] ) ) ? $area_detail['Area']['tooltip_text'] : 'Drag and drop is only available within the Workspace. Copy and paste also available across Workspaces only.'; ?>
										<button class="btn btn-default btn-xs tipText area_info"
										data-original-title="<?php echo htmlentities($tooltip_text); ?>"
										data-placement="left">
											<i class="fa fa-fw fa-info"></i>
										</button>

									</div>
								</div>



							</div>
						</div>
							<div class="box-body clearfix in drop_element">
								<div class="ovrelay"></div>
								<?php

									// CREATE ELEMENTS OF EACH AREA IF EXISTS
									if( isset( $row_detail['elements'] ) && !empty( $row_detail['elements'] ) ) {

										$elements_data = $row_detail['elements'];
										foreach( $elements_data as $element_index => $element_detail ) {
										?>
										<?php
											$response = [ 'id' => $element_detail['Element']['id'], 'data' => ['Element' => $element_detail ] ];
										?>
										<?php
										}
									}
									// LOAD PARTIAL ELEMENT FILE
								?>
								<?php //echo $this->element('../Projects/partials/area_elements', [ 'area_id' => $row_detail['area_id'], 'area_detail' => $area_detail]); ?>
								<?php echo $this->element('../Missions/partials/area_elements', [ 'area_id' => $row_detail['area_id'], 'area_detail' => $area_detail,'wsp_signoff'=>$workspaceArray['sign_off']]); ?>
							</div>

					</td>
					<?php

					}

					// end first foreach
					// End table row started just after first foreach
					// It prints till the total number of rows reaches

					$rown_group_cnt = ( isset($row_group) && !empty($row_group) ) ? count($row_group) - 1 : 0;

					if( $row_id < ($rown_group_cnt) )
					echo '</tr><tr>';
				} // end second foreach

			?>
		</tbody>
	</table>
<?php } ?>
</div>


	<?php

		$areas = $this->ViewModel->workspace_areas($workspace_id);
		$areaElements = null;
		if( isset( $areas ) && !empty( $areas ) ) {
			// pr($areas, 1);
			foreach( $areas as $k => $v ) {

				$elements_details_temp = null;
				if((isset($e_permission) && !empty($e_permission)))
				{
					$all_elements = $this->ViewModel->area_elements_permissions($v['Area']['id'], false,$e_permission);
				}

				if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
					$all_elements = $this->ViewModel->area_elements($v['Area']['id']);
				}

				if( isset( $all_elements ) && !empty( $all_elements ) ) {

					foreach( $all_elements as $element_index => $e_data ) {

						$element = $e_data['Element'];

						$element_decisions = $element_feedbacks = [];
						if( isset($element['studio_status']) && empty($element['studio_status']) ) {
							$element_decisions = _element_decisions( $element['id'], 'decision' );
							$element_feedbacks = _element_decisions( $element['id'], 'feedback' );
							$element_statuses = _element_statuses( $element['id'] );

							$element_assets = element_assets( $element['id'], true );
							$arraySearch = arraySearch( $all_elements, 'id', $element['id'] );

							if( isset( $arraySearch ) && !empty( $arraySearch ) ) {
								$elements_details_temp[] = array_merge( $arraySearch[0], $element_assets, $element_decisions, $element_feedbacks, $element_statuses );
							}
						}
					}

					$areaElements[$v['Area']['id']]['el'] = $elements_details_temp;
				}
			}
		}
		// pr($areaElements);
	?>
<script type="text/javascript" >
$(function(){


		$('.wsp-title').tooltip({
			placement: 'top-left'
		})

	// SET ALL DATA PASSED FROM PHP SCRIPT ON PAGE LOAD
	$js_config.elements_details = <?php echo json_encode( $areaElements ); ?>;
	$js_config.workspace_id = <?php echo json_encode( $workspace_id ); ?>;

	$.bind_dragDrop = function() {

		$(".el-draggable").draggable({
            cursor: "move",
            appendTo: 'body',
            containment: "parent",
            // stack: ".el",
            // snap: "#workspace",
            start: function(event, ui) {
            	$('.tool-container').hide();
                $('.btn-settings').removeClass('opened')
                $(this).addClass('drag-start');
                ui.helper.addClass('drag-start');
                var $box_body = $('.box-body.drop_element');
                $box_body.each(function(index, el) {
                    $(this).data('height', $(this).height());
                    $(this).css('min-height', $(this).parents('td.area_box:first').height() - $(this).parents('td.area_box:first').find('.box-area').outerHeight());
                });
                $box_body.addClass('drop_start');
            },
            stop: function(event, ui) {
                $('.box-body').removeClass('drop_start');
                var $box_body = $('.box-body.drop_element');
                $box_body.each(function(index, el) {
                    $(this).removeAttr('style');
                });
            },
            helper: function(e) {
                var original = $(e.target).hasClass("ui-draggable") ? $(e.target) : $(e.target).closest(".ui-draggable");

                return original.clone().css({
                    width: original.width()
                });
            },
            cursorAt: { left: 5, top: 5 },
            revert: function(is_valid_drop) {
                if (!is_valid_drop) {
                    $(this).css("position", "relative");
                    return true;
                } else {
                    $(this).css("position", "relative");
                }
            },

        });

		$(".area_box .box-body").droppable({
			accept: ".el-draggable",
			tolerance: "pointer",
			drop: function(event, ui) {
				$(this).removeClass("over");

				var edata = ui.draggable.data(),
					$area_box = $(this).parents('.area_box:first'),
					move_to_area = $area_box.attr('id'),
					element_id = edata.element,
					area_id = edata.currentArea;

				if( area_id != move_to_area ) {

					// ui.draggable.appemdTo($(this))
					$(".tooltip").remove()

					var post_data = {
						'data[Element][area_id]': move_to_area,
						'sort_area[]': move_to_area,
						'sort_area[]': area_id,
						'sort_area[]': null,
						'workspace_id': $js_config.workspace_id,
						'project_id': $js_config.project_id,
						'element_action': 'drag_drop'
					}

					$.ajax({
						url: $js_config.base_url + "entities/cut_copy_paste/" + element_id,
						type: "POST",
						data: $.param( post_data ),
						dataType: "JSON",
						global: true,
						success: function (response) {
							if(response.success){
								var $selectedList = $('.idea-workspace-carousel li.selectable.selected');
								$selectedList.removeClass('selected');
								$selectedList.trigger('click');
							}
                            else if(response.error != ''){
                                $.open_alerts(response.error);
                            }
						}
					})
				}
				else {

				}
			},
			over: function(event, elem) {
				$(this).addClass("over");
				$(".tooltip").remove()
			},
			out: function(event, elem) {
				$(this).removeClass("over");
				$(".tooltip").remove()
			}
		});
	}

	$.bind_dragDrop();
	/* END $.bind_context_menu() */

})
</script>

<?php
	$context_list = $this->ViewModel->project_workspaces(null, false, 0, $workspace_id); // Get all projects, workspaces and areas
	 ?>

<div id="copy_to_list">
    <?php

	if( isset( $context_list ) && !empty( $context_list ) ) {
	?>
    <ul class="dropdown-menu custom-drop">
        <li class="dropdown-header">Projects</li>
        <?php foreach( $context_list as $key => $data ) { ?>
        <li class="dropdown-submenu" data-id="<?php echo $data['project']['id']; ?>">
            <a tabindex="-1" href="#">
            	<i class="fa fa-briefcase"></i> <?php echo _substr_text( $data['project']['title'], 20, true ); ?>
            </a>
            <?php if( isset( $data['workspace'] ) && !empty( $data['workspace'] ) ) {  ?>
            <ul class="dropdown-menu">
            	<li class="dropdown-header">Workspaces</li>
            	<?php foreach( $data['workspace'] as $wk => $wv ) {
            	if($workspace_id != $wv['id'] && empty($wv['studio_status'])) {  ?>
                <li class="dropdown-submenu" data-id="<?php echo $wv['id']; ?>">
                    <a href="#">
                    	<i class="fa fa-th"></i> <?php echo _substr_text( $wv['title'], 20, true ) ; ?>
                    </a>
                    <?php if( isset( $wv['area'] ) && !empty( $wv['area'] ) ) { ?>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">Areas</li>
                        <?php foreach( $wv['area'] as $ak => $av ) { ?>
                        <li>
                        	<a href="#" data-id="<?php echo $ak ?>"
							data-list-id="<?php echo $data['project']['id'] . '_' . $wv['id'] . '_' . $ak ?>"
							name='copy_to' class="target">
								<i class="fa fa-list-alt"></i> <?php echo _substr_text( $av, 20, true ); ?>
							</a>
						</li>
                        <?php } // foreach $wv['area'] ?>
                    </ul>
                    <?php } // if $wv['area'] ?>
                </li>
                <?php } ?>
                <?php } // foreach $data['workspace'] ?>
            </ul>
            <?php } // if $data['workspace'] ?>
        </li>
        <?php } // foreach context_list ?>
    </ul>
    <?php } // if context_list ?>
</div>


<div id="move_to_list">
    <?php

	if( isset( $context_list ) && !empty( $context_list ) ) {
	?>
    <ul class="dropdown-menu custom-drop">
        <li class="dropdown-header">Projects</li>
        <?php foreach( $context_list as $key => $data ) { ?>
        <li class="dropdown-submenu" data-id="<?php echo $data['project']['id']; ?>">
            <a tabindex="-1" href="#">
            	<i class="fa fa-briefcase"></i> <?php echo _substr_text( $data['project']['title'], 20, true ); ?>
            </a>
            <?php if( isset( $data['workspace'] ) && !empty( $data['workspace'] ) ) { ?>
            <ul class="dropdown-menu">
            	<li class="dropdown-header">Workspaces</li>
            	<?php foreach( $data['workspace'] as $wk => $wv ) {
            	if($workspace_id != $wv['id'] && empty($wv['studio_status'])) {  ?>
                <li class="dropdown-submenu" data-id="<?php echo $wv['id']; ?>">
                    <a href="#">
                    	<i class="fa fa-th"></i> <?php echo _substr_text( $wv['title'], 20, true ); ?>
                    </a>
                    <?php if( isset( $wv['area'] ) && !empty( $wv['area'] ) ) { ?>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">Areas</li>
                        <?php foreach( $wv['area'] as $ak => $av ) { ?>
                        <li>
                        	<a href="#" data-id="<?php echo $ak ?>"
							data-list-id="<?php echo $data['project']['id'] . '_' . $wv['id'] . '_' . $ak ?>"
							name='move_to' class="target">
								<i class="fa fa-list-alt"></i> <?php echo _substr_text( $av, 20, true ); ?>
							</a>
						</li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
                <?php } ?>
            </ul>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>

<div class="modal fade" id="PostCommentsModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times; </span><span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<span class="input-group-addon">@</span> <input type="text" class="form-control" placeholder="Your Name" />
				</div>
				<p></p>
				<div class="input-group">
					<span class="input-group-addon">@</span> <input type="text" class="form-control" placeholder="Your Email" />
				</div>
				<p></p>
				<div class="input-group">
					<span class="input-group-addon">@</span>
					<textarea rows="4" cols="50" class="form-control" placeholder="Your Message"></textarea>
				</div>
				<button type="button" class="btn-primary">Send</button>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="modal-alert" class="modal fade">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-radius-top">
        <div class="modal-header border-radius-top bg-red">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <i class="fa fa-exclamation-triangle"></i>&nbsp;Warning
        </div>
      <!-- dialog body -->

      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        Please set project dates before setting workspace dates.
      </div>
      <!-- dialog buttons -->
      <div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">OK</button></div>
    </div>
  </div>
</div>
<style>
	.table-wrapper {
	    padding: 6px;
	    width: 100%;
	}
	#tbl {
		margin: 0;
	}
	#tbl td {
	    border: 1px solid #ccc;
	   /*  float: left; */
	    padding: 5px;
	    /*min-height: 370px;*/
	   /*  display: table-cell; */
	}
	.tooltip-inner {
	    max-width: 200px;
	}
	#workspace #tbl tbody tr td.area_box {
		border: 3px solid #dcd6c1 !important;
		background: #fff none repeat scroll 0 0;
	}
	#workspace #tbl tbody tr td.area_box .box-body {
	    overflow-x: hidden;
	    overflow-y: auto;
		/*max-height: 200px ;*/
	}
	#workspace td .box-body.over {
		/*background: #f5efda none repeat scroll 0 0;*/
		border: 1px dashed #d9a602;
		transition: all 0.3s ease 0s;
	}
	.btn-actions > .btn {
		margin-right: 0px !important;
		margin-top: 1px;
	}
	#workspace td .box-body {
	    max-height: 5000px;
	}
</style>
<script type="text/javascript">
	$(function(){
		// $('.area-box-title a').textdot();
		// $('.area-box-title a').ellipsis_word();
		$.box_height = 0;
        ($.setMaxHeight = function(){
        	$.box_height = 0;
	        $('#tbl td .box-body').each(function(index, el) {
	        	var h = $(this).find('.el:first').outerHeight(true);
	        	if(h && $(this).find('.el').length > 15){
	        		var new_height = h * 15;
	        		if(new_height > $.box_height){
	        			$.box_height = new_height;
	        		}
	        	}
	        });

			$('#tbl td .box-body').each(function(index, el) {
				if($(this).find('.el').length > 15){
					$(this).css({'height': $.box_height , 'max-height': $.box_height })
				}
			})
		})();

		$(window).on('resize', $.setMaxHeight);
		 $.open_alerts = function(message, type) {
			var type = type || BootstrapDialog.TYPE_DANGER;
			BootstrapDialog.show({
				title: 'Confirmation',
				message: message,
				type: type,
				draggable: true,
				buttons: [{
					label: ' Ok',
					cssClass: 'btn-danger',
					action: function(dialogRef) {
						dialogRef.close();
					}
				}]
			});
		}

	})
</script>