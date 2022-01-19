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

				$vedp_data = $vedc_data = 0;
				if( isset($workspaces) && !empty($workspaces) ) {
					$area_ids = $vote_data = null;
					foreach( $workspaces as $k => $v ) {
						$areas = $this->ViewModel->workspace_areas($v['Workspace']['id'], false, true);
						if( isset($areas) && !empty($areas) ) {
							if(is_array($area_ids))
								$area_ids = array_merge($area_ids, array_values($areas));
							else
								$area_ids = array_values($areas);
						}

						if( isset($area_ids) && !empty($area_ids) ) {

							 $vote_data = _element_vote_and_result($area_ids);
						}
					}

				}
			$run = false;
			?>

				<table class="table">
				<?php if( isset($vote_data) && !empty($vote_data) ) {
					  foreach($vote_data as $eid => $data ) {

						$el = ( isset($data['element']) && !empty($data['element']) ) ? $data['element'] : null;
						$vote = ( isset($data['vote']) && !empty($data['vote']) ) ? $data['vote'] : null;
						if(isset($vote) && !empty($vote)){
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
								else if( (isset( $el['end_date'] ) && !empty( $el['end_date'] )) && date( 'Y-m-d', strtotime( $el['end_date'] ) ) < date( 'Y-m-d' )  && $el['sign_off'] !=1 ) {
									$class_name = 'overdue';
									$class_tip = 'Overdue';
								}
								else if( isset( $el['sign_off'] ) &&  $el['sign_off']  == 1 ) {
									$class_name = 'completed';
									$class_tip = 'Completed';
								}
								else if( ((isset( $el['end_date'] ) && !empty( $el['end_date'] )) && (isset( $el['start_date'] ) && !empty( $el['start_date'] ))) && (date( 'Y-m-d', strtotime( $el['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $el['end_date'] ) ) >= date( 'Y-m-d' ) && $el['sign_off'] !=1  ) {
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
							if( isset($vote) && !empty($vote) ) {

								foreach($vote as $vd){

									if( isset($type) && !empty($type) ) {

										if( $type == 'completed' ) {
											if( !empty($vd['Vote']['is_completed']) ) {
												$counter_complete++;
											}
										}
										else if( $type == 'live' ) {
											if( empty($vd['Vote']['is_completed']) ) {
												$counter_live++;
											}
										}

									}
								}
							}
							// echo $el['id'].'-'.$counter_live.'-, -'.$counter_complete.'-<br />';
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
							<ul class="list-unstyled vote_lst">
								<li>
									<div class="el-detail">
										<div class="detail-head">

										<div title="<?php echo $class_tip; ?>" class="el-box tipText element-<?php echo $class_name; ?>">
											
										</div>
										<h5 ><a class="tipText" title="Open Task" href="<?php echo SITEURL.'entities/update_element/'.$el['id'].'#votes' ?>"><?php echo ( isset($el) && !empty($el) ) ? strip_tags($el['title']) : 'N/A';?></a></h5>
										
										</div>
										<?php
										$flag = false;
										if( isset($vote) && !empty($vote) ) {

												foreach($vote as $vd){


													$show_data = false;

													if( isset($type) && !empty($type) ) {


														if( $type == 'completed' ) {
															if( !empty($vd['Vote']['is_completed']) ) {
																$show_data = true;
																$flag = true;
															}
														}
														else if( $type == 'live' ) {
															if( empty($vd['Vote']['is_completed']) ) {
																$show_data = true;
																$flag = true;
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
											$class_nameV = 'undefined';
											$box_classV = 'not_started';

													if( (isset( $vd['Vote']['start_date'] ) && !empty( $vd['Vote']['start_date'] )) && date( 'Y-m-d', strtotime( $vd['Vote']['start_date'] ) ) > date( 'Y-m-d' ) ) {
														$class_nameV = 'not_started';
													}
													else if( (isset( $vd['Vote']['end_date'] ) && !empty( $vd['Vote']['end_date'] )) && date( 'Y-m-d', strtotime( $vd['Vote']['end_date'] ) ) < date( 'Y-m-d' ) &&  empty($vd['Vote']['is_completed']) ) {
														$class_nameV = 'overdue';
													}
													else if( isset( $vd['Vote']['is_completed'] ) &&  $vd['Vote']['is_completed']==1 ) {
														$class_nameV = 'completed';
														$box_classV = 'completed';
													}
													else if( ((isset( $vd['Vote']['end_date'] ) && !empty( $vd['Vote']['end_date'] )) && (isset( $vd['Vote']['start_date'] ) && !empty( $vd['Vote']['start_date'] ))) && (date( 'Y-m-d', strtotime( $vd['Vote']['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $vd['Vote']['end_date'] ) ) >= date( 'Y-m-d' ) &&  empty($vd['Vote']['is_completed']) ) {
														$class_nameV = 'progressing';
														$box_classV = 'progressing';

													}

												else {
													$class_nameV = 'undefined';
												}
											?>

											<?php
												 //pr($class_nameV);
												$staDate = date('Y-m-d');
												$status_class = 'not-start';

												$daysLeft = daysLeft($staDate, $vd['Vote']['end_date']);
												$remainingDays = 100 - $daysLeft;
												$day_text = "N/A";
												$stlo= "top:31px;";
												if(  $class_nameV == 'not_started' ) {
													$daysLeft = daysLeft( date('Y-m-d'), $vd['Vote']['start_date']);
													$remainingDays = 100;
													$day_text = "Start in<br />".$daysLeft." days";
													$stlo= "top:23px;";
													$status_class = 'not-start';
												}
												else if(  $class_nameV == 'progressing' ) {

													$day_text = "Due<br />".$daysLeft." days";
													$stlo= "top:23px;";
													$status_class = 'progressing';
												}
												else if(  $class_nameV == 'completed' ) {
													$remainingDays = 100;
													$daysLeft = 0;
													$day_text = "ENDED";
													$status_class = 'complete';

												}
												else if(  $class_nameV == 'overdue' ) {
													$daysLeft = daysLeft( $vd['Vote']['end_date'], date('Y-m-d'));
													$day_text = "Overdue<br />".$daysLeft." days";
													$stlo= "top:23px;";
													$status_class = 'overdue';
												}
											?>
										<div class="detail-body" data-class="vote-<?php echo $box_classV; ?>">

										<div class="detail-body-inner">
											<p><a href="#" class="vote-<?php echo $box_classV; ?> text-bold text-capitalize title" data-source='<?php echo SITEURL.'entities/update_element/'.$el['id'].'?'.$vd['Vote']['id'].'#votes'; ?>' data-id="<?php echo $vd['Vote']['id']; ?>"><?php echo ( isset($vd) && !empty($vd) ) ? $vd['Vote']['title'] : 'N/A';?></a></p>
											<p>
											<span class="text-dark-gray">Start: </span>
											<span class="text-red"><?php echo ( isset($vd) && !empty($vd) ) ? _displayDate($vd['Vote']['start_date'], 'd M, Y') : 'N/A';?></span>
											</p>
											<p>
											<span class="text-dark-gray">End: </span>
											<span class="text-red"><?php echo ( isset($vd) && !empty($vd) ) ? _displayDate($vd['Vote']['end_date'], 'd M, Y') : 'N/A';?></span>
											</p>
											<p><span class="text-bold ">Signed Off: </span><span class="text-pure-red">
											<?php if( isset($vd) && !empty($vd) ) {
													echo ($vd['Vote']['is_completed'] == 1) ? 'Yes' : 'No';
												}else{
														echo 'N/A';
												}?>

											</span></p>
											<p>
												<span class="text-black">Last Update: </span>
												<span class="text-pure-red"><?php echo ( isset($el) && !empty($el) ) ? _displayDate(date('Y-m-d h:i:s A', $vd['Vote']['modified'])) : 'N/A';
												?></span>
											</p>

											<p>
												<span class="text-dark-gray">Updated By: </span>
												<span class="text-pure-red">
													<?php
														echo ( !empty($vd['Vote']['updated_user_id']) ) ?
														get_user_data($vd['Vote']['updated_user_id'], ['first_name', 'last_name']) :
														get_user_data($this->Session->read('Auth.User.id'), ['first_name', 'last_name']);
													?>
												</span>
											</p>

											</div>

											<div style="max-width: 200px" class="status-block">
											<div class="tbrow">
												<div class="tbcol bg-check-orange1 bg-check-orange1 status-class <?php echo $status_class; ?> days"><?php echo $day_text; ?></div>
											</div>
											<div class="tbrow">
												<div class="tbcol bg-check-black days"><?php echo isset($vd['VoteResults']) ? $total = $this->ViewModel->getTotalVoteResults($vd['Vote']['id']) : 0; ?><br>Voters</div>
											</div>
											</div>

										</div>
										<?php  } ?>

										<?php  } ?>
										<?php if($flag == false){ ?>
										<div class="detail-body">
										<div class="detail-body-inner">No Vote</div>
										</div>
										<?php } ?>
									</div>
								</li>
							</ul>
						</td>

					</tr>
				<?php } } } }  ?>
				<?php }
					if( $run == false )  {
					?>
					<tr>
						<td class="bg-blakish" colspan="2" align="center" style="border-top: medium none; text-align: center; font-size: 16px;">No Votes</td>
					</tr>
					<?php
					}
				?>

				</table>
			</div>

<script type="text/javascript" >
$(function(){
$('#votes .vote_lst .detail-body a').click(function(e){
	e.preventDefault();
	 var src = $(this).attr('data-source');
	window.location.href = src;
})
})
</script>

