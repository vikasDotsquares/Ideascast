<?php  
// pr($response);
if(isset($response['content']) && !empty($response['content'])){
	$totaltask = ( count($response['content']) > 0 ) ? count($response['content']) : 0; 
} else {
	$totaltask = 0; 
} 
?> 			
					<?php //if( isset($response['content']) && !empty($response['content']) ){ 
					$current_user_id = $this->Session->read("Auth.User.id");					
					?>	<div class="col-sm-12">	
						<div class="panel-group panel-custom" id="upaccordion">						
						<?php  
						if( isset($response['projectid']) && !empty($response['projectid']) ){	
						$project_id = $response['projectid'];						
						$projectData = $this->ViewModel->getProjectDetail($project_id);
						
						$projectPermission = $this->ViewModel->projectPermitType($project_id, $response['user_id']);
						$userPtype = 'Sharer';
						if( isset($projectPermission) && !empty($projectPermission) && $projectPermission == 1 ){
							$userPtype = 'Owner';
						}
						
						?>
							<div class="panel panel-default list-panel">                        
								<div class="panel-heading">
									<h4 class="panel-title">
										<a class="accordion-anchor">
											<span class="element-title"><i class="fa fa-briefcase"></i> </span>
												<span class="" style="font-size: 13px;font-weight: 500;" ><?php echo strip_tags($projectData['Project']['title']);?> (<?php echo $userPtype.", ".$totaltask." Tasks";?>)
											</span>
											<div class="right-options">
												<span data-calname="prjavail" class="btn btn-xs btn-white icon-chart show_calendar tipText"  style="display: none;">
													<i class="fa fa-calendar-times-o"></i>
												</span>
												<span class="show-hide-program btn btn-xs btn-white tipText collapsed" data-toggle="collapse" data-parent="#upaccordion" data-accordionid="collapse1" href="#collapseupe" aria-expanded="false" data-original-title="Expand">
													<i class="fa"></i>
												</span>
											</div>
										</a>
									</h4>
								</div>
								<div id="collapseupe" class="panel-collapse collapse" aria-expanded="false" style="max-height:230px;overflow-y: scroll;">
									<div class="panel-body">
										<div class="work-element-info-tab">
											<div class="row work-element-info-mainrow">								   
												<?php
												if( isset($response['content']) && !empty($response['content']) ){
												
												$i=0; 
												foreach($response['content'] as $listElement){
									 
																		 
														$totalElementIds[] = $listElement['Element']['id'];
														/* ============= Start Element Status ================================ */
															$elementStatusName = $this->Common->element_status($listElement['Element']['id']);
															$elementStatus = element_status($listElement['Element']['id']);

															$status_class = 'nill';
															$status_sort = 0;
															switch ($elementStatus) {
																case 'not_started':
																	$status_class = 'cell_not_started';
																	$status_sort = 2;
																	break;
																case 'overdue':
																	$status_class = 'cell_overdue';
																	$status_sort = 1;
																	break;
																case 'completed':
																	$status_class = 'cell_completed';
																	$status_sort = 4;
																	break;
																case 'progress':
																	$status_class = 'cell_progress';
																	$status_sort = 3;
																	break;
																case 'not_spacified':
																	$status_class = 'cell_not_spacified';
																	$status_sort = 5;
																	break;
															}

															$eleStart_date = 'N/A';
															$eleEnd_date = 'N/A';
															if( isset($listElement['Element']['start_date']) && !empty($listElement['Element']['start_date']) ){
																$eleStart_date = date('d M, Y',strtotime($listElement['Element']['start_date']));
															}
															if( isset($listElement['Element']['end_date']) && !empty($listElement['Element']['end_date']) ){
																$eleEnd_date = date('d M, Y',strtotime($listElement['Element']['end_date']));
															}
															/* ============ Start Element Assignment Status =========================== */

																$assigned_class = 'no-reaction';
																$hover_html = '';
																$receiver_name = 'N/A';
																$receiver_job_title = 'N/A';
																$profile_pic = SITEURL.'images/placeholders/user/user_1.png';
																$element_assigned = element_assigned( $listElement['Element']['id'] );
																//pr($element_assigned);
																if(!empty($listElement['Element']['date_constraints'])) {
																	if($element_assigned) {
																		$hover_html .= '<div class="assign-hover">';
																		$assign_creator = $element_assigned['ElementAssignment']['created_by'];
																		$assign_receiver = $element_assigned['ElementAssignment']['assigned_to'];
																		$reaction = $element_assigned['ElementAssignment']['reaction'];

																		$creator_detail = get_user_data($assign_creator);
																		$receiver_detail = get_user_data($assign_receiver);

																		if( isset($receiver_detail) && !empty($receiver_detail) ){
																			$profile_pic = $receiver_detail['UserDetail']['profile_pic'];
																			$receiver_name = $receiver_detail['UserDetail']['full_name'];
																			$receiver_job_title = $receiver_detail['UserDetail']['job_title'];
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

																		$assigned_by = 'N/A';
																		if( isset($creator_detail) && !empty($creator_detail) ){
																			$assigned_by = $creator_detail['UserDetail']['full_name'];
																			if( $assign_creator == $current_user_id ) {
																				$assigned_by = 'Me';
																			}
																		}
																		$hover_html .= '<span>Assigned by: '.$assigned_by.'</span>';

																		$reaction_label = '';
																		if($reaction == 1){
																			$reaction_label = 'Schedule: Accepted';
																		}
																		else if($reaction == 2){
																			$reaction_label = 'Schedule: Not Accepted';
																		}
																		$hover_html .= '<span>'.$this->Wiki->_displayDate($element_assigned['ElementAssignment']['modified'], 'd M, Y g:iA').'</span>';
																		$hover_html .= $reaction_label . '</div>';

																		if($reaction == 1){
																			$assigned_class = 'accepted';
																		}
																		else if($reaction == 2){
																			$assigned_class = 'not-accepted';
																		}
																		else if($reaction == 3){
																			$assigned_class = 'disengaged';
																		}
																	}
																}
															//<i class="fa fa-stop" aria-hidden="true"></i>
															/* ========================================================================== */
															?>

															<div class="work-element-info-col"  data-elestatus="<?php echo $status_sort;?>">
																<div class="info-box <?php //echo $status_class;?>">
																<div class="col-sm-5" style="margin-left: 0; padding-left: 0;">	
																	<div class="info-box-text">
																	
																<?php if(!$element_assigned){  ?>
																		<span class="info-box-icon tipText" title="No Assignment"><i class="fa fa-times text-black icon-task-assign " ></i></span>
																<?php } else { ?>
																		<span class="info-box-icon task-assigned" data-title="Task Leader" data-content='<?php echo $hover_html; ?>' ><i class="fa <?php echo $assigned_class; ?> text-black icon-task-assign " ></i></span>
																<?php } ?>
																		<span class="elementtitle ">
																			<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $listElement['Element']['id']), true); ?>#tasks" class="data-block-title pop tipText" title="<?php echo ucfirst($listElement['Element']['title']); ?>" style="color:#333;"><?php echo strip_tags($listElement['Element']['title']); ?></a>
																		</span>
																	</div>
																</div>
																<div class="col-sm-7 task-date-box">
																	<div class="info-box-content">
																		<span class="info-box-number tipText startdate float-left <?php echo $status_class;?>" title="<?php //echo $elementStatusName;?>"><span class="ele_date">Start:</span> <?php echo $eleStart_date; ?> </span>
																		<span class="info-box-number tipText enddate <?php echo $status_class;?>" title="<?php //echo $elementStatusName;?>"><span class="ele_date">End:</span> <?php echo $eleEnd_date; ?></span>
																	</div>
																</div>	
																</div>
															</div>
													<?php	 
													  $i++; } 
														} else { ?>
															<div class="work-element-info-col"  >
																<span style="text-transform: none;">No record found</span>
															</div>	
														<?php } ?>
												</div>
											</div>
										</div>
									</div>				
								</div>
							<?php } ?>	
							</div>
					</div>
					<?php //} ?>
				
			
			
			

		
 
<script>
$(function() {
	$('.task-assigned').popover({
		placement: 'bottom',
		trigger: 'hover',
		html: true,
		container: 'body',
		delay: { show: 50, hide: 100 }
	});

	$('#active_avail .panel-collapse').on('shown.bs.collapse', function(e) {
        if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
            $(this).parents('.panel').find('.show-hide-program').tooltip('hide').attr('title', 'Expand').attr('data-original-title', 'Expand').tooltip('show');
        } else {
            $(this).parents('.panel').find('.show-hide-program').attr('title', 'Collapse').attr('data-original-title', 'Collapse');
        }
    });
	$('#active_avail .panel-collapse').on('hidden.bs.collapse', function(e) {
        if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
            $(this).parents('.panel').find('.show-hide-program').tooltip('hide').attr('title', 'Expand').attr('data-original-title', 'Expand').tooltip('show');
        } else {
            $(this).parents('.panel').find('.show-hide-program').attr('title', 'Collapse').attr('data-original-title', 'Collapse');
        }
    });	
	
});

</script>
