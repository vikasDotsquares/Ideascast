<?php
if( isset($project_id) && !empty($project_id) ) {
	$workspaces = get_project_workspace($project_id);
}
else if( isset($workspace_id) && !empty($workspace_id) ) {
	$workspaces['Workspace'] = $this->ViewModel->getWorkspaceDetail($workspace_id);
}
// pr($workspaces);
?>

			<div class="table-responsive">
			<?php
				$feedback = _feedback();

				$fedp_data = $fedc_data = 0;

				$area_ids = $feedback_data = null;

				if( isset($workspaces) && !empty($workspaces) ) {
					foreach( $workspaces as $k => $v ) {
						$areas = $this->ViewModel->workspace_areas($v['Workspace']['id'], false, true);
						if( isset($areas) && !empty($areas) ) {
							if(is_array($area_ids))
								$area_ids = array_merge($area_ids, array_values($areas));
							else
								$area_ids = array_values($areas);
						}
					}

						if( isset($area_ids) && !empty($area_ids) ) {
							 $feedback_data = _element_feedback_and_result($area_ids);
						}
				}
			$run = false;
			?>

				<table class="table">
				<?php if( isset($feedback_data) && !empty($feedback_data) ) {

					  foreach($feedback_data as $eid => $data ) {

						$el = ( isset($data['element']) && !empty($data['element']) ) ? $data['element'] : null;
						$feedback = ( isset($data['feedback']) && !empty($data['feedback']) ) ? $data['feedback'] : null;
						if(isset($feedback) && !empty($feedback)){
							$run = true;
					  ?>
						<?php
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

							$spacial_flag = false;

							$counter_live = $counter_complete = 0;
							if( isset($feedback) && !empty($feedback) ) {

								foreach($feedback as $fd){

									if( isset($type) && !empty($type) ) {

										if( $type == 'completed' ) {
											if( !empty($fd['Feedback']['sign_off']) ) {
												$counter_complete++;
											}
										}
										else if( $type == 'live' ) {
											if( empty($fd['Feedback']['sign_off']) ) {
												$counter_live++;
											}
										}

									}
								}
							}

							if( isset($type) && !empty($type) ) {
								if($type == 'completed' && $counter_complete > 0) {
									$spacial_flag = true;
								}
								if($type == 'live' && $counter_live > 0) {
									$spacial_flag = true;
								}
							}
							else {
									$spacial_flag = true;
							}
					if( $spacial_flag == true ) {
						?>


					<tr>
						<td>

							<ul class="list-unstyled">
								<li>
									<div class="el-detail">
										<div class="detail-head">
 
											<div title="<?php echo $class_tip; ?>" class="el-box tipText element-<?php echo $class_name; ?>">
									
											</div>
											<h5 ><a class="tipText" title="Open Task" href="<?php echo SITEURL.'entities/update_element/'.$el['id'].'#feedbacks' ?>"><?php echo ( isset($el) && !empty($el) ) ? strip_tags($el['title']) : 'N/A';?></a></h5>
										</div>
										<?php

										$flag = false;
										if( isset($feedback) && !empty($feedback) ) {

												foreach($feedback as $fd){

													$show_data = false;

													if( isset($type) && !empty($type) ) {

														if( $type == 'completed' ) {
															if( !empty($fd['Feedback']['sign_off']) ) {
																$show_data = true;
																$flag = true;
															}
														}
														else if( $type == 'live' ) {
															if( empty($fd['Feedback']['sign_off']) ) {
																$show_data = true;
																$flag = true;
																//e($fd['Feedback']['sign_off']);
															}
														}

													}
													else {
														$show_data = true;
														$flag = true;
													}

											if( $show_data == true ) {
												$flag = true;
												$box_class = 'undefined';
										?>


											<?php
											$class_nameF = 'undefined';
											$box_classF = 'not_started';

													if( (isset( $fd['Feedback']['start_date'] ) && !empty( $fd['Feedback']['start_date'] )) && date( 'Y-m-d', strtotime( $fd['Feedback']['start_date'] ) ) > date( 'Y-m-d' ) ) {
														$class_nameF = 'not_started';
													}
													else if( (isset( $fd['Feedback']['end_date'] ) && !empty( $fd['Feedback']['end_date'] )) && date( 'Y-m-d', strtotime( $fd['Feedback']['end_date'] ) ) < date( 'Y-m-d' ) && $fd['Feedback']['sign_off']  != 1 ) {
														$class_nameF = 'overdue';
													}
													else if( isset( $fd['Feedback']['sign_off'] ) &&   $fd['Feedback']['sign_off']  == 1 ) {
														$box_classF = $class_nameF = 'completed';
													}
													else if( ((isset( $fd['Feedback']['end_date'] ) && !empty( $fd['Feedback']['end_date'] )) && (isset( $fd['Feedback']['start_date'] ) && !empty( $fd['Feedback']['start_date'] ))) && (date( 'Y-m-d', strtotime( $fd['Feedback']['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $fd['Feedback']['end_date'] ) ) >= date( 'Y-m-d' )  && $fd['Feedback']['sign_off']  != 1) {
														$box_classF = $class_nameF = 'progressing';
													}

												else {
													$class_nameF = 'undefined';
												}
											?>
											<?php
												//pr($post);
												$staDate = date('Y-m-d');
												$status_class = 'not-start';

												$daysLeft = daysLeft($staDate, $fd['Feedback']['end_date']);
												$remainingDays = 100 - $daysLeft;
												$day_text = "N/A";
												$stlo= "top:31px;";
												if(  $class_nameF == 'not_started' ) {
													$daysLeft = daysLeft( date('Y-m-d'), $fd['Feedback']['start_date']);
													$remainingDays = 100;
													$day_text = "Start in<br />".$daysLeft." days";
													$stlo= "top:23px;";
													$status_class = 'not-start';
												}
												else if(  $class_nameF == 'progressing' ) {
													$day_text = "Due<br />".$daysLeft." days";
													$stlo= "top:23px;";
													$status_class = 'progressing';
												}
												else if(  $class_nameF == 'completed' ) {
													$remainingDays = 100;
													$daysLeft = 0;
													$day_text = "ENDED";
													$status_class = 'complete';
												}
												else if(  $class_nameF == 'overdue' ) {
													$daysLeft = daysLeft( $fd['Feedback']['end_date'], date('Y-m-d'));
													$day_text = "Overdue<br />".$daysLeft." days";
													$stlo= "top:23px;";
													$status_class = 'overdue';
												}
											?>
										<div class="detail-body">

										<div class="detail-body-inner">
											<p><a class="feedback-<?php echo $box_classF; ?> text-bold text-capitalize title" data-id="<?php echo $fd['Feedback']['id']; ?>" href="#" data-source='<?php echo SITEURL.'entities/update_element/'.$el['id'].'?'.$fd['Feedback']['id'].'#feedbacks'; ?>'><?php echo ( isset($fd) && !empty($fd) ) ? $fd['Feedback']['title'] : 'N/A';?></a></p>
											<p>
											<span class="text-dark-gray">Start: </span>
											<span class="text-red"><?php echo ( isset($fd) && !empty($fd) ) ? _displayDate($fd['Feedback']['start_date'], 'd M, Y') : 'N/A';?></span>
											</p>
											<p>
											<span class="text-dark-gray">End: </span>
											<span class="text-red"><?php echo ( isset($fd) && !empty($fd) ) ? _displayDate($fd['Feedback']['end_date'], 'd M, Y') : 'N/A';?></span>
											</p>
											<p><span class="text-bold ">Signed Off: </span><span class="text-pure-red">
											<?php if( isset($fd) && !empty($fd) ) {
													echo ($fd['Feedback']['sign_off'] == 1) ? 'Yes' : 'No';
												}else{
														echo 'N/A';
												}?>

											</span></p>
											<p>
												<span class="text-black">Last Update: </span>
												<span class="text-pure-red"><?php echo ( isset($el) && !empty($el) ) ? _displayDate(date('Y-m-d h:i:s A', strtotime($fd['Feedback']['modified']))) : 'N/A';?></span>
											</p>

											<p>
												<span class="text-dark-gray">Updated By: </span>
												<span class="text-pure-red">
													<?php
														echo ( !empty($fd['Feedback']['updated_user_id']) ) ?
														get_user_data($fd['Feedback']['updated_user_id'], ['first_name', 'last_name']) :
														get_user_data($this->Session->read('Auth.User.id'), ['first_name', 'last_name']);
													?>
												</span>
											</p>

											</div>


											<div style="max-width: 200px" class="status-block">
											<div class="tbrow">
												<div class="tbcol bg-check-orange1 status-class <?php echo $status_class; ?> days"><?php echo $day_text; ?></div>
											</div>
											<div class="tbrow">
												<div class="tbcol bg-check-black days"><?php echo isset($fd['FeedbackResults']) ? count($fd['FeedbackResults']) : 0; ?><br>Feedback</div>
											</div>
											</div>

										</div>
										<?php  } ?>

										<?php  }  ?>
										<?php if($flag == false){ ?>
										<div class="detail-body">
										<div class="detail-body-inner">No Feedback</div>
										</div>
										<?php } ?>
									</div>
								</li>
							</ul>
						</td>

					</tr>
				<?php } }
					  }
				}  ?>
				<?php }?>

				<?php
					if( $run == false )  {
					?>
					<tr>
						<td class="bg-blakish" colspan="2" align="center" style="border-top: medium none; text-align: center; font-size: 16px;">No Feedback</td>
					</tr>
					<?php
					}
				?>

				</table>
			</div>
<script type="text/javascript" >
$(function(){
$('#feedbacks .list-unstyled .detail-body a').click(function(e){
	e.preventDefault();
	 var src = $(this).attr('data-source');
	window.location.href = src;
})
})
</script>
<style type="text/css">

</style>