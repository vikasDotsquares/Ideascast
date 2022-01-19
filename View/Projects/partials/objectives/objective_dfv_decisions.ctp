<?php
if( isset($project_id) && !empty($project_id) ) {
	$workspaces = get_project_workspace($project_id);
}
else if( isset($workspace_id) && !empty($workspace_id) ) {
	$workspaces['Workspace'] = $this->ViewModel->getWorkspaceDetail($workspace_id);
}
// pr($workspaces);
?>

<?php
	$decisions = _decisions();
	$dp = $dc = 0;
	if( isset($workspaces) && !empty($workspaces) ) {
		$area_ids = $decision_data = null;
		foreach( $workspaces as $k => $v ) {
			$areas = $this->ViewModel->workspace_areas($v['Workspace']['id'], false, true);
			if( isset($areas) && !empty($areas) ) {
				if(is_array($area_ids))
					$area_ids = array_merge($area_ids, array_values($areas));
				else
					$area_ids = array_values($areas);
			}

			$dec_data = _workspace_decision_feedbacks($v['Workspace']['id']);
			$dp += (isset($dec_data['progress'])) ? $dec_data['progress'] : 0;
			$dc += (isset($dec_data['complete'])) ? $dec_data['complete'] : 0;

		}

			if( isset($area_ids) && !empty($area_ids) ) {
				$decision_data = _element_decision_and_detail($area_ids, true);
			}



		// pr($decision_data);
	}

?>
<div class="table-responsive">
	<table class="table">
		<?php

	$error_show = true;
// pr($decision_data);
	if( isset($decision_data) && !empty($decision_data) ) {

		foreach($decision_data as $eid => $data ) {


			$el = ( isset($data['element']) && !empty($data['element']) ) ? $data['element'] : null;
			
		 
			$class_name = 'undefined';
			$class_tip = 'Not Set';
			if( isset( $el['date_constraints'] ) && !empty( $el['date_constraints'] ) && $el['date_constraints'] > 0 ) {
				if( (isset( $el['start_date'] ) && !empty( $el['start_date'] )) && date( 'Y-m-d', strtotime( $el['start_date'] ) ) > date( 'Y-m-d' ) ) {
					$class_name = 'not_started';
					$class_tip = 'Not Started';
				}
				else if( (isset( $el['end_date'] ) && !empty( $el['end_date'] )) && date( 'Y-m-d', strtotime( $el['end_date'] ) ) < date( 'Y-m-d' ) &&   $el['sign_off']   != 1 ) {
					$class_name = 'overdue';
					$class_tip = 'Overdue';
				}
				else if( isset( $el['sign_off'] ) &&   $el['sign_off']   == 1 ) {
					$class_name = 'completed';
					$class_tip = 'Completed';
				}
				else if( ((isset( $el['end_date'] ) && !empty( $el['end_date'] )) && (isset( $el['start_date'] ) && !empty( $el['start_date'] ))) && (date( 'Y-m-d', strtotime( $el['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $el['end_date'] ) ) >= date( 'Y-m-d' ) &&   $el['sign_off']   != 1 ) {
					$class_name = 'progressing';
					$class_tip = 'In Progress';
				}
			}
			else {
				$class_name = 'undefined';
				$class_tip = 'Not Set';
			}
			
			
			$decision = ( isset($data['decision']) && !empty($data['decision']) ) ? $data['decision'] : null;

			$decision_detail = ( isset($data['decision_detail']) && !empty($data['decision_detail']) ) ? $data['decision_detail'] : null;

			$show_data = false;

			if( isset($decision) && !empty($decision) ) {

				$detail_count = 6;
				$decid = $decision['ElementDecision']['id'];
				$decision_data = decision_data($decid);

				if( isset($decision_data) && !empty($decision_data) ) {
					$decisions_val = _decisions();
					$decisions_vals = ( isset($decisions_val) && !empty($decisions_val) ) ?  count($decisions_val) : 0;
					$detail_count = $decisions_vals - count($decision_data);
					$decision_detail = $decision_data;
				}

				if($decision['ElementDecision']['sign_off'] ==1) {
						$show_data = true;
				}
				else if($decision['ElementDecision']['sign_off'] !=1 || $decision['ElementDecision']['sign_off'] =="") {
						$show_data = true;
				}
				/*if( $type == 'completed' ) {
				}
				else if( $type == 'progressing' ) {
				}*/

			}
			else {
					$show_data = false;
			}


			if( $show_data == true ){

				$error_show = false;

				if( isset($decision) && !empty($decision) ) {
				$box_class = 'undefined';
				$box_tip = '';
				if( !empty($decision_detail)) {
					//$box_class = ( count($decision_detail) >= 6 ) ? 'completed' : 'progressing';
					$box_class = ( $decision['ElementDecision']['sign_off'] ==1 ) ? 'completed' : 'progressing';
					//$box_tip = ( $decision['ElementDecision']['sign_off'] ==1 ) ? 'Completed' : 'In Progress';
				}
		?>
		<tr>
			<td width="78%">
				<ul class="list-unstyled">
					<li>
						<div class="el-detail">
							<div class="detail-head">
								<!--<div title="<?php echo $box_tip ?>" class="el-box tipText decsion-<?php echo $box_class; ?>">-->
								<div title="<?php echo $class_tip; ?>" class="el-box tipText element-<?php echo $class_name; ?>">
									
								</div>
								<h5 ><a class="tipText" title="Open Task" href="<?php echo SITEURL.'entities/update_element/'.$el['id'].'#decisions' ?>"><?php echo ( isset($el) && !empty($el) ) ? strip_tags($el['title']) : 'N/A';?></a></h5>
							</div>
							<div class="detail-body">
								<p>
									<span class="text-dark-gray text-bold text-capitalize title"><?php echo ( isset($decision) && !empty($decision) ) ? $decision['ElementDecision']['title'] : 'N/A'; ?></span>
								</p>
								<p>
									<span class="text-black text-bold">Stages Remaining: </span>
									<span class="text-red">
									<?php
									if( !empty($decision_detail) ) {
										$decisionCnt = ( isset($decisions) && !empty($decisions) ) ? count($decisions) : 0;
										echo  $decisionCnt - count($decision_detail);
									}
									else echo 'N/A';
									?>
									</span>
								</p>
								<p>
									<span class="text-dark-gray">Start: </span>
									<span class="text-pure-red"><?php echo ( isset($decision) && !empty($decision) ) ? _displayDate($decision['ElementDecision']['created'], 'd M, Y') : 'N/A';?></span>
								</p>
								<p>
									<span class="text-black text-bold">Signed Off: </span>
									<span class="text-pure-red">
									<?php if( isset($decision) && !empty($decision) ) {
										echo ($decision['ElementDecision']['sign_off'] == 1) ? 'Yes' : 'No';
									}else{
											echo 'N/A';
									}?>
									</span>
								</p>
								<p>
									<span class="text-dark-gray">Last Update: </span>
									<span class="text-pure-red">
										<?php echo ( isset($el) && !empty($el) ) ? _displayDate(date('Y-m-d h:i:s A', strtotime($decision['ElementDecision']['modified'] ) ) ) : 'N/A';?>
									</span>
								</p>

								<p>
									<span class="text-dark-gray">Updated By: </span>
									<span class="text-pure-red">
										<?php
											echo ( !empty($decision['ElementDecision']['updated_user_id']) ) ?
											get_user_data($decision['ElementDecision']['updated_user_id'], ['first_name', 'last_name']) :
											get_user_data($this->Session->read('Auth.User.id'), ['first_name', 'last_name']);
										?>
									</span>
								</p>

							</div>
						</div>
					</li>
				</ul>
			</td>
			<td width="22%" style="vertical-align: middle;">

				<div class="status-block" style="max-width: 200px">
					<div class="tbrow" >

						<?php
						if( isset($decisions) && !empty($decisions) ) {
							foreach( $decisions as $k => $v ) {
								if( _decision_state($decision['ElementDecision']['id'], $v) ) {
						?>
								<div class="tbcol bg-check-green">
									<i class="fa fa-check "></i>
								</div>
						<?php
								} else {
						?>
							<div class="tbcol"><?php echo $k+1; ?></div>
						<?php
								}
								if( $k == 2)
									echo '</div><div class="tbrow">';
							}

						}

						?>
					</div>
				</div>

			</td>
		</tr>
		<?php } ?>
		<?php } ?>
		<?php } ?>

		<?php
		if( $error_show ) {
		?>
			<!--<tr>
				<td class="bg-blakish" colspan="2" align="center" style="border-top: medium none; text-align: center; font-size: 16px;">No Decisions</td>
			</tr>-->
		<?php
		}
		?>

		<?php }

		//pr($dp);
		//pr($dc);
		//pr($error_show);
		if(  $error_show && $dp == 0 && $dc == 0 )  {
		?>
		<tr>
			<td class="bg-blakish" colspan="2" align="center" style="border-top: medium none; text-align: center; font-size: 16px;">No Decisions</td>
		</tr>
		<?php
		}
		?>

	</table>
</div>