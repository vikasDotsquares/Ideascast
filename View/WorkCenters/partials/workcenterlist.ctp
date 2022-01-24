<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script type="text/javascript" > var SITEURL='<?php echo SITEURL; ?>'</script>
<?php
        echo $this->Html->meta('icon');
        echo $this->Html->css(
                array(

				/* 	'/twitter-cal/components/bootstrap3/css/bootstrap.min',
					'/twitter-cal/components/bootstrap3/css/bootstrap-theme', */
					'/twitter-cal/css/calendar',

                )
        );

		 echo $this->Html->css('/plugins/fullcalendar/fullcalendar.print', array('media' => 'print'));

	?>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
      <?php  echo $this->Html->script(array(
           // '/plugins/jQuery/jQuery-2.1.3.min',
			//'jquery-ui.min.js',
			'moment.min',
			'/twitter-cal/components/underscore/underscore-min',
			'/twitter-cal/js/calendar',
			'dashboard',
			'jstz',

			)
		   );

	   ?>


<style>
.ui-datepicker-inline{display: none !important; }
#workspace {
    margin-top: 0px;
}
.cell_progress a{ color :#000 !important;}
</style>

	<?php
		$current_user_id = $this->Session->read("Auth.User.id");
		$projectlists = $this->Common->getProjectbyDetails($projects);
		$totalElementIds = array();
		$projectIds = array();
		$userSignedOffelement = array();

		if( isset($projects) && !empty($projects) && isset($user_id) && !empty($user_id) ){
			$userSignedOffelement = $this->TaskCenter->userElements($user_id, $projects);
		}
		// pr($userSignedOffelement);		die;
		
		 //$noproject = noTaskWorkinUserProject($user_id,$start_Date = '2025-09-27', $end_Date = '2026-01-31');
		 //pr($noproject);
		 //die;
	?>
    <!-------------------------------- divid--------------------------------------- -->
    <div class="opt">
		<a href="#" id="" class="btn btn-primary btn-xs toggle-accordion tipText" title="" accordion-id="#accordion" data-original-title="Expand All">
            <i class="fa "></i>
        </a>		
        <a href="" id="projectfreeusers" data-projectids="<?php echo implode(',',$projects); ?>" data-pstartdate="<?php echo $start_date;?>"  data-penddate="<?php echo $end_date;?>" class="btn btn-success btn-xs life-saver-icon tipText" data-title="Task Free" ><i class="fa fa-life-ring"></i></a>	

	</div>

    <div class="panel-group panel-custom" id="accordion">
        <?php
			if( isset($projectlists) && !empty($projectlists) ){
			$i=0;

			// free task users by projects
			$usersLists = $this->ViewModel->noTaskWorkingUsers($projectlists,$start_date,$end_date);
			// pr($usersLists);
			
			//pr($projects); die;
			
			foreach($projectlists as  $projectdetail){
			// get Project Total Tasks =============================================
			$totalEle = $totalWs = 0;
			$totalAssets = null;
			// $projectData = $this->ViewModel->getProjectDetail($projectdetail['Project']['id']);
			//pr($projectData);die;
			$project_id = $projectdetail['Project']['id'];


			$p_permission = $this->Common->project_permission_details($project_id,$user_id);
			$user_project = $this->Common->userprojectwc($project_id,$user_id);
			//pr($user_project);
			$grp_id = $this->Group->GroupIDbyUserID($project_id,$user_id);

			if(isset($grp_id) && !empty($grp_id)){

			$group_permission = $this->Group->group_permission_details($project_id,$grp_id);
				if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
			}

			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) ) {

				$wsList = get_project_workspace($projectdetail['Project']['id'],1);
				$wsp_ids = array();
				if( isset($wsList) && !empty($wsList) ){
					$wsp_ids = array_keys($wsList);
				}

			}else if((isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level']!=1)  || (isset($p_permission['ProjectPermission']) && $p_permission['ProjectPermission']['project_level']!=1)){
				$wsp_permission = $this->Common->work_permission_details($project_id, $user_id);
				if(isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level']!=1){
					$wsp_permission = $this->Group->group_work_permission_details($project_id, $grp_id);
				}
				$wsp_ids = pwid_workspace($wsp_permission,$project_id);
			}
			$projectIds[] = $project_id;
			$wsp_elements = array();
			$elements_by_date = [];
			$projectElementCount = 0;

			if(isset($wsp_ids) && !empty($wsp_ids)){

				foreach($wsp_ids as $id){
					$all_elements = null;
						$arealist = $this->ViewModel->workspace_areas($id);

					foreach($arealist as $v){

						$e_permission = $this->Common->element_permission_details($id,$project_id,$user_id);

						if((isset($grp_id) && !empty($grp_id))){

							if(isset($e_permission) && !empty($e_permission)){
								$e_permissions =  $this->Group->group_element_permission_details( $id, $project_id, $grp_id);
								$e_permission = array_merge($e_permission,$e_permissions);
							}else{
								$e_permission =  $this->Group->group_element_permission_details( $id, $project_id, $grp_id);
							}
						}

						if((isset($e_permission) && !empty($e_permission)))
						{
							$all_elements = $this->ViewModel->area_elements_permissions($v['Area']['id'], false,$e_permission);
						}

						if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
							$all_elements = $this->ViewModel->area_elements($v['Area']['id']);
						}

						$wsp_elements[] = $all_elements;
						$all_elements = Set::extract($all_elements, '{n}.Element.id');
						// pr($all_elements);
						// $elements_by_date = null;
						if(isset($all_elements) && !empty($all_elements) ) {
							$dates = $start_date." = ".$end_date;
							// $dates = "2020-09-05 = 2020-08-08";
							$elements_by_date = array_merge($elements_by_date, $this->ViewModel->elementsbydate_workcenter($all_elements, $dates));

						}
						if( isset($elements_by_date) && !empty($elements_by_date) ){
							$projectElementCount = count($elements_by_date);
						} else {
							$projectElementCount = '';
						}

					}
				}
			}

			$projectPermission = $this->ViewModel->projectPermitType( $project_id,$user_id);
			$projectPermissionSelf = $this->ViewModel->projectPermitType( $project_id,$current_user_id);
			
			$SelfuserPtype = 'Sharer';
			if( isset($projectPermissionSelf) && !empty($projectPermissionSelf) && $projectPermissionSelf == 1 ){
				$SelfuserPtype = 'Owner';
			}
			
			$userPtype = 'Sharer';
			if( isset($projectPermission) && !empty($projectPermission) && $projectPermission == 1 ){
				$userPtype = 'Owner';
			}

			//pr($elements_by_date);
			$elementforCalendar = Set::extract($elements_by_date, '{n}.Element.id');
			$elementidsstring = '';
			if( isset($elementforCalendar) && !empty($elementforCalendar) ){
				$elementidsstring = implode(",",$elementforCalendar);
			}
			/*====================================================================== */
				$listdates = '';
				$sel_month = date('m');
				$sel_year = date('Y');
				$listdates = $this->ViewModel->not_available_dateswithstatus($user_id, $sel_month , $sel_year);

		?>
                    <div class="panel panel-default list-panel <?php echo ($userPtype == 'Sharer')? "sharerslider":"ownerslider"; ?> <?php if( !isset($projectElementCount) || empty($projectElementCount) ){ ?> no-data-avail <?php } ?> ">
                        <div class="panel-heading" >
                            <h4 class="panel-title">
								<a class="accordion-anchor" >
									<?php
										$stp_date = ( isset($projectdetail['Project']['start_date']) && !empty($projectdetail['Project']['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectdetail['Project']['start_date'])),$format = 'd M Y') : 'N/A';

										$enp_date = ( isset($projectdetail['Project']['end_date']) && !empty($projectdetail['Project']['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectdetail['Project']['end_date'])),$format = 'd M Y') : 'N/A';
									?>
									<span class="program-title" data-pid="<?php echo $projectdetail['Project']['id'];?>"><i class="fa fa-briefcase"></i> <strong><?php echo strip_tags($projectdetail['Project']['title']);?></strong><span class="<?php if(empty($projectElementCount)){ echo 'blank'; } ?>"> (<?php echo "Start: ".$stp_date." "."End: ".$enp_date." - ".$userPtype;?>, <?php echo (!empty($projectElementCount)) ? $projectElementCount : 0; ?> Tasks)</span></span>
									<?php if( isset($projectElementCount) && !empty($projectElementCount) ){ ?>
									<div class="right-options">
										<span data-calname="prjavail" class="btn btn-xs btn-white icon-chart show_calendar tipText" data-title="Project Availability" data-original-title="Project Availability" data-listdata="<?php echo $listdates;?>" data-eleids="<?php echo $elementidsstring; ?>" data-pid="<?php echo $projectdetail['Project']['id']; ?>" data-userid="<?php echo $current_user_id; //echo $user_id; ?>" style="display: none;">
											<i class="fa fa-calendar-times-o"></i>
										</span>
										<span class="show-hide-program btn btn-xs btn-white tipText" title="" data-toggle="collapse" data-parent="#accordion" data-accordionid="collapse<?php echo $i;?>" href="#collapse<?php echo $i;?>" aria-expanded="false" data-original-title="Expand">
											<i class="fa"></i>
										</span>
									</div>
									<?php } else { ?>
									<div class="right-options disable">
										<!-- <span class="btn btn-xs btn-white icon-chart" style="display: none;">
											<i class="fa fa-calendar-times-o"></i>
										</span> -->
										<span class="show-hide-program btn btn-xs btn-white tipText" data-elementcnt="0"  title="" data-toggle="collapse" href="" aria-expanded="false" data-original-title="No Elements">
											<i class="fa"></i>
										</span>
									</div>
									<?php } ?>
								</a>
							</h4>
                    	</div>



                        <div id="collapse<?php echo $i;?>" class="panel-collapse collapse"  >
                            <div class="panel-body">
                                <div class="work-project-info-tab">
                                   <div class="row work-project-info-mainrow">
									 <?php
										// pr($elements_by_date);
										if( isset($elements_by_date) && !empty($elements_by_date) ){
											foreach($elements_by_date as $listElement){
													$totalElementIds[] = $listElement['Element']['id'];
													/* ============= Start Element Status ================================ */
													$elementStatusName = $this->Common->element_status($listElement['Element']['id']);
													$elementStatus = element_status($listElement['Element']['id']);

													$status_class = 'nill';
													$status_sort = 0;
													switch ($elementStatus) {
														case 'not_started':
															$status_class = 'cell_not_started';
															$status_sort = 3;
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
															$status_sort = 2;
															break;
														case 'not_spacified':
															$status_class = 'cell_not_spacified';
															$status_sort = 5;
															break;
													}

													$eleStart_date = 'N/A';
													$eleEnd_date = 'N/A';
													if( isset($listElement['Element']['start_date']) && !empty($listElement['Element']['start_date']) ){
														$eleStart_date = date('d F, Y',strtotime($listElement['Element']['start_date']));
													}
													if( isset($listElement['Element']['end_date']) && !empty($listElement['Element']['end_date']) ){
														$eleEnd_date = date('d F, Y',strtotime($listElement['Element']['end_date']));
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

													<div class="work-project-info-col"  data-elestatus="<?php echo $status_sort;?>">
														<div class="info-box <?php echo $status_class;?>">
															<span class="info-box-text"><a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $listElement['Element']['id']), true); ?>#tasks" class="data-block-title pop tipText" title="<?php echo ucfirst(strip_tags($listElement['Element']['title'])); ?>" style="color:#fff;display: inline-block; font-size: 12px;"><?php echo strip_tags($listElement['Element']['title']); ?></a></span>
														<?php if(!$element_assigned){  ?>
																<span class="info-box-icon bg-light-gray tipText" title="No Assignment"><i class="fa fa-times text-black icon-task-assign " ></i></span>
														<?php } else { ?>
																<span class="info-box-icon bg-light-gray task-assigned" data-title="Task Leader" data-content='<?php echo $hover_html; ?>' ><i class="fa <?php echo $assigned_class; ?> text-black icon-task-assign " ></i></span>
														<?php } ?>
															<div class="info-box-content">

																<span class="info-box-number tipText" title="<?php //echo $elementStatusName;?>"><span class="ele_date">Start:</span> <?php echo $eleStart_date; ?></span>
																<span class="info-box-number tipText" title="<?php //echo $elementStatusName;?>"><span class="ele_date">End:</span> <?php echo $eleEnd_date; ?></span>
															</div>
														</div>
													</div>
                                            <?php	}
												}
											?>
									</div>
                                </div>

								<div class="work-project-info-tab-calendar" style="display:none;">

								   <div class="row col-md-12">
										<div class="col-sm-6 col-md-4 col-lg-3 text-xs-center" style="padding-left:0;">
											<div class="btn-group" style="margin-top:5px;">
												<button class="btn  btn-sm btn-primary" data-calendar-nav="prev"><< Prev</button>
												<button class="btn  btn-sm btn-default" data-calendar-nav="today">Today</button>
												<button class="btn  btn-sm btn-primary" data-calendar-nav="next">Next >></button>
											</div>
										</div>
										<div class="col-sm-6 col-md-8 col-lg-9 month-header">
											<div class="page-header">
												<h3></h3>
											</div>
										</div>
								   </div>

								   <div class="row work-project-info-mainrow" >
										<div class="col-sm-12 col-md-8 col-lg-8" >
											<div class="calendars" style="margin-top:18px;" ></div>
										</div>


										<div class="col-sm-12 col-md-4 col-lg-4  notavailusers">
											<div class="nots">Not Available</div>
											<div class="row">
												<div data-projectid="<?php echo $project_id; ?>" data-pstartdate="<?php echo $start_date;?>"  data-penddate="<?php echo $end_date;?>" data-userid="<?php echo $user_id;?>" class="saldom" style="display:none;">
													<input type="radio" data-projectid="<?php echo $project_id; ?>" data-pstartdate="<?php echo $start_date;?>"  data-penddate="<?php echo $end_date;?>" data-userid="<?php echo $user_id;?>" class="notavailmyself" name="myself<?php echo $project_id; ?>" id="myselfid<?php echo $project_id; ?>" >&nbsp;<label class="me-label" for="myselfid<?php echo $project_id; ?>">Me</label>
													<?php if($projectPermissionSelf == 'Owner'){?>
													<input type="radio" class="memberid" name="myself<?php echo $project_id; ?>" id="memberid<?php echo $project_id; ?>" data-projectid="<?php echo $project_id; ?>" data-pstartdate="<?php echo $start_date;?>"  data-penddate="<?php echo $end_date;?>" >&nbsp;<label for="memberid<?php echo $project_id; ?>">Member</label>
													<?php } ?>
												</div>

											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="freeuserlists">

													</div>

												</div>
											</div>
											<div class="row">
												<div class="col-sm-12 freeuserdatereasonstatus">
													<?php
													//userAvaiabilityforcalendar
													// $loguseravaildata = $this->ViewModel->userAvaiability($user_id);
													$loguseravaildata = $this->ViewModel->userAvaiabilityWithPast($user_id);

													if( isset($loguseravaildata['current']) && !empty($loguseravaildata['current']) ){
														foreach($loguseravaildata['current'] as $listavaildata){
													?>
													<div class="con" >

														<div class="col-sm-6">
															<div class="datestartlist">Start: <?php
															if( isset($listavaildata['availabilities']['avail_start_date']) && !empty($listavaildata['availabilities']['avail_start_date']) ){
															echo date('d-m-Y h:i A',strtotime($listavaildata['availabilities']['avail_start_date']));
															} else { echo "N/A"; }
															?></div>
															<div class="dateendlist">End: <?php
															if( isset($listavaildata['availabilities']['avail_end_date']) && !empty($listavaildata['availabilities']['avail_end_date']) ){
															echo date('d-m-Y h:i A',strtotime($listavaildata['availabilities']['avail_end_date']));
															} else { echo "N/A"; } ?></div>
														 </div>
														<div class="col-sm-6">
															<div class="noavailreason ">Reason:
															<?php if( isset($listavaildata['availabilities']['avail_reason']) && !empty($listavaildata['availabilities']['avail_reason']) ){?>
															<?php echo strip_tags($listavaildata['availabilities']['avail_reason']); } else {
											 					echo "N/A";
															} ?></div>
														</div>

														</div>
													<?php }
													}
													if( isset($loguseravaildata['upcoming']) && !empty($loguseravaildata['upcoming']) ){
														foreach($loguseravaildata['upcoming'] as $listavaildata){
													?>
													<div class="con" >

														<div class="col-sm-6">
															<div class="datestartlist">Start: <?php
															if( isset($listavaildata['availabilities']['avail_start_date']) && !empty($listavaildata['availabilities']['avail_start_date']) ){
															echo date('d-m-Y h:i A',strtotime($listavaildata['availabilities']['avail_start_date']));
															} else { echo "N/A"; }
															?></div>
															<div class="dateendlist">End: <?php
															if( isset($listavaildata['availabilities']['avail_end_date']) && !empty($listavaildata['availabilities']['avail_end_date']) ){
															echo date('d-m-Y h:i A',strtotime($listavaildata['availabilities']['avail_end_date']));
															} else { echo "N/A"; } ?></div>
														 </div>
														<div class="col-sm-6">
															<div class="noavailreason ">Reason:
															<?php if( isset($listavaildata['availabilities']['avail_reason']) && !empty($listavaildata['availabilities']['avail_reason']) ){?>
															<?php echo strip_tags($listavaildata['availabilities']['avail_reason']); } else {
											 					echo "N/A";
															} ?></div>
														</div>

														</div>
													<?php }
													} if( isset($loguseravaildata['past']) && !empty($loguseravaildata['past']) ){
														foreach($loguseravaildata['past'] as $listavaildata){
													?>
													<div class="con" >

														<div class="col-sm-6">
															<div class="datestartlist">Start: <?php
															if( isset($listavaildata['availabilities']['avail_start_date']) && !empty($listavaildata['availabilities']['avail_start_date']) ){
															echo date('d-m-Y h:i A',strtotime($listavaildata['availabilities']['avail_start_date']));
															} else { echo "N/A"; }
															?></div>
															<div class="dateendlist">End: <?php
															if( isset($listavaildata['availabilities']['avail_end_date']) && !empty($listavaildata['availabilities']['avail_end_date']) ){
															echo date('d-m-Y h:i A',strtotime($listavaildata['availabilities']['avail_end_date']));
															} else { echo "N/A"; } ?></div>
														 </div>
														<div class="col-sm-6">
															<div class="noavailreason ">Reason:
															<?php if( isset($listavaildata['availabilities']['avail_reason']) && !empty($listavaildata['availabilities']['avail_reason']) ){?>
															<?php echo strip_tags($listavaildata['availabilities']['avail_reason']); } else {
											 					echo "N/A";
															} ?></div>
														</div>

														</div>
													<?php }
													} ?>
												</div>

											</div>

										</div>
								   </div>
								</div>

							</div>
                        </div>
                    </div>
                <?php
				$i++;
            }
		}
		?>
    </div>
<?php
	// $totaltasks have element ids
	// pr($totalElementIds);

	$threemonthsdate = $this->ViewModel->threeMonthsDate();
	$noElementDates = array();
	$yesElementDates = array();
	if( isset($threemonthsdate) && !empty($threemonthsdate) ){

		foreach($threemonthsdate as $datelist){

			$elementCount = $this->ViewModel->total_todays_tasks_workcenter($totalElementIds, $datelist);

			// echo $elementCount[0][0]['totaltasks']."=".$datelist."<br /	>";
			if( $elementCount[0][0]['totaltasks'] <= 0 ){
				$noElementDates[] = $datelist;
			}
		}
	}

	$dateRangelist = array();
	if( isset($noElementDates) && !empty($noElementDates) ){
		$notEleCount = count($noElementDates);
		
		$totaldaysinmonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')); // 31
		$i = date('d');
		$continuousDates = '';
		foreach($noElementDates as $datalist){
			$expldate = explode('-',$datalist);			
			$totaldaysinmonth = cal_days_in_month(CAL_GREGORIAN, $expldate[1], date('Y')); // 31
			if( $expldate[2] ){
				$continuousDates .=$datalist.',';
				$dateRangelist[$expldate[1]][] = $datalist;
			}

		$i++;
		}

		//  pr($dateRangelist);



	}
// pr($dateRangelist, 1);
	// pr($yesElementDates);
	//pr($elementCount);
	//pr($dd);
	$element_status = _elements_status($totalElementIds);
	$complitingElements= $this->ViewModel->complitingElementsCount($totalElementIds,$start_date,$end_date);

	// pr($element_status); die;
	$non = arraySearch($element_status, 'status', 'NON');
	$pnd = arraySearch($element_status, 'status', 'PND');
	$prg = arraySearch($element_status, 'status', 'PRG');
	$ovd = arraySearch($element_status, 'status', 'OVD');
	$cmp = arraySearch($element_status, 'status', 'CMP');
	$compliting = arraySearch($element_status, 'status', 'CMP');

	$totalGroupOwner = array();
	$totalPermissionOwner = array();
	$totalCreatedOwner = array();

	$totalGroupSharer = array();
	$totalPermissionSharer = array();

	if( isset($OwnerSharerUsers['group']['owner']) && !empty($OwnerSharerUsers['group']['owner']) ){
		$totalGroupOwner = $OwnerSharerUsers['group']['owner'];
	}
	if( isset($OwnerSharerUsers['created']['owner']) && !empty($OwnerSharerUsers['created']['owner']) ){
		$totalCreatedOwner = $OwnerSharerUsers['created']['owner'];
	}
	if( isset($OwnerSharerUsers['permission']['owner']) && !empty($OwnerSharerUsers['permission']['owner']) ){
		$totalPermissionOwner = $OwnerSharerUsers['permission']['owner'];
	}
	if( isset($OwnerSharerUsers['group']['sharer']) && !empty($OwnerSharerUsers['group']['sharer']) ){
		$totalGroupSharer = $OwnerSharerUsers['group']['sharer'];
	}
	if( isset($OwnerSharerUsers['permission']['sharer']) && !empty($OwnerSharerUsers['permission']['sharer']) ){
		$totalPermissionSharer = $OwnerSharerUsers['permission']['sharer'];
	}
		
	$totalGroupOwner_tot = ( isset($totalGroupOwner) && !empty($totalGroupOwner) ) ? count($totalGroupOwner) : 0;
	$totalPermissionOwner_tot = ( isset($totalPermissionOwner) && !empty($totalPermissionOwner) ) ? count($totalPermissionOwner) : 0;
	$totalCreatedOwner_tot = ( isset($totalCreatedOwner) && !empty($totalCreatedOwner) ) ? count($totalCreatedOwner) : 0;
	
	$totalGroupSharer_tot = ( isset($totalGroupSharer) && !empty($totalGroupSharer) ) ? count($totalGroupSharer) : 0;
	$totalPermissionSharer_tot = ( isset($totalPermissionSharer) && !empty($totalPermissionSharer) ) ? count($totalPermissionSharer) : 0;
		
	$totalOwner = $totalGroupOwner_tot + $totalPermissionOwner_tot) + $totalCreatedOwner_tot;
	$totalSharer = $totalGroupSharer_tot + $totalPermissionSharer_tot;	
	
	$current_user_id = $this->Session->read('Auth.User.id');
	$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );

	// Get Task leader
	$taskleaders = $this->ViewModel->elementTaskLeader($totalElementIds);
	// pr($dateRangelist);

	$dataRangeforUsers = array();
	/* if( isset($dateRangelist) && !empty($dateRangelist) ){
		foreach($dateRangelist as $key => $prevsvalue) {
			if( isset($prevsvalue) && !empty($prevsvalue) ){
				foreach($prevsvalue as $value ){
					$dataRangeforUsers[] = $value;
				//	$this->ViewModel->noTaskWorkingUsers($value);
				}
			}
		}
		asort($dataRangeforUsers);
	} */


	//pr($dataRangeforUsers);
?>


    <div class="work-center-task" style="display: none;" >
        <div class="col-one">
            <?php

			if(isset($userDetail) && !empty($userDetail)) {
				$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
				$profile_pic = $userDetail['UserDetail']['profile_pic'];

				$html = '';
				if( isset($projects) && !empty($projects) && count($projects) == 1 ){
					if( isset($project_id) && !empty($project_id) ){
						if( $userDetail['UserDetail']['user_id'] != $current_user_id ) {
							$html = CHATHTML($userDetail['UserDetail']['user_id'], $project_id);
						}
					}
				} else {
						$html = CHATHTML($userDetail['UserDetail']['user_id']);
					}

				if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
					$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
				} else {
					$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
				}
			}
		?>
                <a data-target="#modal_small" data-toggle="modal" data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $user_id;?>" class="pophover profileimg" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $userDetail['UserDetail']['job_title'];?></p><p><?php echo $html; ?></p></div>" data-original-title="" title=""><img src="<?php echo $profilesPic ?>" alt="profile"></a>
                <div class="workinfo-wrap">
                    <span class="workinfo totalprojects"  style="cursor:pointer;"><strong>Projects:</strong> <?php echo ( isset($projectIds) && !empty($projectIds) ) ? count($projectIds) : 0;?></span>


                    <span class="workinfo <?php if(isset($totalOwner) && !empty($totalOwner)){ ?>ownerslideracordian<?php } ?>" <?php  if(isset($totalOwner) && !empty($totalOwner)){ ?>style="cursor:pointer; font-weight: 550; color:#67a028;"<?php } ?> >Owner: <?php echo (isset($totalOwner) && !empty($totalOwner))? $totalOwner : 0;?></span>


                    <span class="workinfo <?php if (isset($totalSharer) && !empty($totalSharer)) { ?>sharerslideracordian<?php } ?>" <?php if (isset($totalSharer) && !empty($totalSharer)) { ?>style="cursor:pointer; font-weight: 550; color:#67a028;"<?php } ?> >Sharer: <?php echo (isset($totalSharer) && !empty($totalSharer))? $totalSharer : 0;?></span>
                </div>
        </div>
		<?php $completedElements = $this->TaskCenter->total_signoff($user_id,$projectIds); ?>
        <div class="col-one col-three">
            <div class="workinfo-wrap">
                <span class="workinfo"><strong>Active Tasks:</strong> <?php echo ( isset($totalElementIds) && !empty($totalElementIds) ) ? count($totalElementIds) : 0; ?></span>
                <span class="workinfo">Task Leader: <?php echo $taskleaders; ?></span>
                <span class="workinfo">Completed: <?php echo $completedElements;?></span>
            </div>
        </div>
        <div class="col-one col-four">
            <div class="workinfo-wrap">
                <span class="workinfo"><strong>Within Date Range:</strong></span>
                <span class="workinfo">Not Started: <?php echo echo ( isset($pnd) && !empty($pnd) ) ? count($pnd) : 0 ;?></span>
                <span class="workinfo">Progressing: <?php echo echo ( isset($prg) && !empty($prg) ) ? count($prg) : 0;?></span>
                <span class="workinfo">Overdue: <?php echo echo ( isset($ovd) && !empty($ovd) ) ? count($ovd) : 0;?></span>
				<span class="workinfo">Completing: <?php echo $complitingElements;?></span>
            </div>
        </div>
        <div class="col-two">
            <div class="workinfo-wrap">
                <span class="workinfo"><strong>Task Free Days (Next 3 Mth):</strong> </span>
                <?php

				if( isset($dateRangelist) && !empty($dateRangelist) ){
					$datelist =  $this->ViewModel->check_continuous_dates($dateRangelist);
					if( isset($datelist) && !empty($datelist) ){
						echo $datelist;
					} else {
						echo '<span class="workinfo">None</span>';
					}
				} else {
					echo '<span class="workinfo">None</span>';
				} ?>
            </div>

        </div>
        <div class="col-two">
            <div class="workinfo-wrap not_available_dates_wrap">
                <?php
                 echo $this->element('../WorkCenters/partials/not_available_dates');
                ?>
				<!-- <span class="workinfo"><strong>Unavailable (> 3 months):</strong> </span> -->
				<?php

				/* $noAvailDates = $this->ViewModel->not_available_dates($user_id);
				//pr($noAvailDates);
				if( isset($noAvailDates) && !empty($noAvailDates) ){
					 $datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates);
					if( isset($datelists) && !empty($datelists) ){
						echo $datelists;
					} else {
						echo '<span class="workinfo">N/A</span>';
					}
				} else {
					echo '<span class="workinfo">N/A</span>';
				}

			    */?>
            </div>

        </div>
    </div>
    <script>
    $(function() {

		$('.pophover').popover({
            placement: 'bottom',
            trigger: 'hover',
            html: true,
            container: 'body',
            delay: { show: 50, hide: 400 }
        });

        $('.task-assigned').popover({
            placement: 'bottom',
            trigger: 'hover',
            html: true,
            container: 'body',
            delay: { show: 50, hide: 100 }
        });
        $('.work-center-task').prependTo('#work-center-task-list').show()
        $('.panel .panel-heading .program-title').each(function(index, el) {
        	if($(this).find('span.blank').length > 0) {
        		//$(this).parents('.list-panel:first').find('.right-options').hide();
        		// $(this).parents('.list-panel:first').remove();
        	}
        });


        $('.show-hide-program').on('click', function(event) {
            var $this = $(this),
                $panel = $(this).parents('.panel:first'),
                $rightOption = $(this).parents('.right-options:first');

            $panel.toggleClass('opened');
            if(!$('.toggle-accordion').hasClass('toggle-active')){
                $('.panel.list-panel.opened').not($panel).removeClass('opened');
                $('.panel.list-panel').not($panel).each(function(index, el) {
                    $('.icon-chart,.project-multi-select', $(this)).hide();
                    if(!$(this).hasClass('opened') && $(this).hasClass('map-view')){
                        // $('.icon-chart', $(this)).trigger('click');
                        // $.reset_project_list($(this));
                    }

                });
                if(!$panel.hasClass('opened')) {
                    $('.icon-chart,.project-multi-select', $panel).hide();
                    if($panel.hasClass('map-view')) {
                        // $('.icon-chart', $panel).trigger('click');
                        // $.reset_project_list($(this));
                    }
                }
                else{
                    $('.icon-chart', $panel).show();
                }
            }

            if($('.toggle-accordion').hasClass('toggle-active')){
                $('.panel.list-panel:not(.opened)').each(function(index, el) {
                    if($(this).hasClass('map-view')){
                        // $('.icon-chart', $(this)).trigger('click');
                        // $.reset_project_list($(this));
                    }
                    $('.icon-chart,.project-multi-select', $(this)).hide();
                });
            }
            // $(this).tooltip('hide').tooltip('show');
        })

        $('.panel-collapse').on('hidden.bs.collapse', function(e) {
            $('.show-hide-program',$('.panel.list-panel:not(.no-data-avail)')).each(function(index, el) {
                if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
                    $(this).tooltip('hide').attr('title', 'Expand').attr('data-original-title', 'Expand');
					$(".toggle-accordion").removeClass('toggle-active').attr('data-original-title','Expand All');
                } else {
                    $(this).attr('title', 'Collapse').attr('data-original-title', 'Collapse');
                }
            });
            $('.icon-chart,.project-multi-select', $(this).parents('.panel.list-panel')).hide();
        });
        $('.panel-collapse').on('shown.bs.collapse', function(e) {
            var $parent = $(this);
            $('.show-hide-program',$('.panel.list-panel:not(.no-data-avail)')).each(function(index, el) {
                if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
                    $(this).tooltip('hide').attr('title', 'Expand').attr('data-original-title', 'Expand');
					//$(".toggle-accordion").addClass('toggle-active').data('original-title','Collapse All');
                } else {
                    $(this).tooltip('hide').attr('title', 'Collapse').attr('data-original-title', 'Collapse');
                }
            });
            $('.icon-chart', $(this).parents('.panel.list-panel')).show();

			// console.log($(".toggle-accordion"));
        });


        $('.icon-chart').on('click', function(event) {
            event.preventDefault();
            var $parent = $(this).parents('.panel:first');
            $parent.toggleClass('map-view');
            // $.reset_project_list($parent);

            if($parent.hasClass('map-view')) {
                $parent.find('.project-multi-select').show();
                $parent.find('.scroll-horizontal').fadeOut('slow', function(){
                    $parent.find('.cost-risk-map').fadeIn('slow');
                })
            }
            else{
                $parent.find('.project-multi-select').hide();
                $parent.find('.cost-risk-map').fadeOut('slow', function(){
                    $parent.find('.scroll-horizontal').fadeIn('slow');
                })
            }


        })


		$('.ownerslideracordian').on('click', function(e){
			$('.sharerslider').show();
			$('.ownerslider').show();
			var query = $.trim('ownerslider').toLowerCase();
			var $this = $("#accordion");
			// console.log($this.text().indexOf(query));

			if($this.text().indexOf(query) === -1){
				 $('.sharerslider').hide();
			} else { $('.ownerslider').show(); }

			// });
		});

		$('.sharerslideracordian').on('click', function(e){
			$('.sharerslider').show();
			$('.ownerslider').show();
			var query = $.trim('sharerslider').toLowerCase();
			var $this = $("#accordion");
			// console.log($this.text().indexOf(query));

			if($this.text().indexOf(query) === -1){
				 $('.ownerslider').hide();
			} else { $('.sharerslider').show(); }

		});

		$('.totalprojects').on('click', function(e){
			$('.sharerslider').show();
			$('.ownerslider').show();
		});

		// div sorting
		$('.ownerslider').prependTo('#accordion');

		// ELEMENT SORTING
		$(".work-project-info-mainrow").each(function(index, el) {
			var container = $(this);
			$('.work-project-info-col', $(this)).sort(function(a, b) {
	            var contentA = $(a).attr('data-elestatus');
	            var contentB = $(b).attr('data-elestatus');
	            return (contentA.toLowerCase() < contentB.toLowerCase()) ? -1 : (contentA.toLowerCase() > contentB.toLowerCase()) ? 1 : 0;

	        }).appendTo($(this))
		});


	// show project users, when user click on CIRCLE ICON WHICH IS IN RIGHT SIDE
    $("body").on("click", "#projectfreeusersqq", function(event) {
        event.preventDefault();

        var projectids = $(this).data('projectids');
        var start_date = $(this).data('pstartdate');
        var end_date = $(this).data('penddate');

		console.log(projectids);
		console.log(start_date);
		console.log(end_date);

		if ( projectids != undefined && start_date != undefined && end_date != undefined) {
			$.when(
				$.ajax({
					type: 'POST',
					data: $.param({ 'pids': projectids, 'start_date': start_date, 'end_date': end_date }),
					url: $js_config.base_url + 'work_centers/project_users',
					global: true,
					success: function(response) {
						$("#project_free_user_modal .modal-content").html(response);
						$("#project_free_user_modal").modal('show');
					}
				})

			).then(function(rdata, textStatus, jqXHR) {

			})
		}


    });


		$("body").on("click", ".notavailmyself", function(event) {

			var $thiss = $(this);
			var user_id = $thiss.data('userid');
			var $panel = $thiss.parents('.list-panel:first');
			$panel.find('.freeuserdatereasonstatus').html('');
			$panel.find('.freeuserlists').html('');
			$("#ajax_overlay").show();
			setTimeout(function(){
				$(".show_calendar", $panel).trigger('click','triggerfunction');
			},100);

		});	

 

		$("body").on("click", ".showelementlist", function(event) {
			var $thiss = $(this);
			var elementids = $thiss.data('id');
			var eventid = $thiss.data('eventid');
			var daynumber = $thiss.data('daynumber');
			var taskurl = $thiss.data('url');
			var remaingusers = $thiss.data('url');
			var userid = $("#project_owner_users").val();
			 
			var userdata = $thiss.parents('.list-panel').find(".saldom");			 
			var projectid = userdata.data('projectid');
			var pstartdate = userdata.data('pstartdate');
			var penddate = userdata.data('penddate');
			 
			$.ajax({
					type: 'POST',
					data: $.param({ 'eleids': eventid, 'project_id':projectid,'start_date':pstartdate,'end_date':penddate,'cdaynumber':daynumber,'user_id':userid }),
					url: $js_config.base_url + 'work_centers/projectelements',
					global: false,
					success: function(response) {
						$('#events-element-modal').find('.modal-content').html(response);
						$('#events-element-modal').modal('show');
					}
			});

		});
		
		$("body").on("click", ".showallelementlist", function(event) {
			var $thiss = $(this);
			var elementids = $thiss.data('id');
			var eventid = $thiss.data('eventid');
			var daynumber = $thiss.data('daynumber');
			var taskurl = $thiss.data('url');
			var remaingusers = $thiss.data('url');
			var projectids = $("#projectfreeusers").data('projectids');
			var userid = $("#project_owner_users").val();
			 
			var userdata = $thiss.parents('.list-panel').find(".saldom");			 
			var projectid = userdata.data('projectid');
			var pstartdate = userdata.data('pstartdate');
			var penddate = userdata.data('penddate');
			 
			$.ajax({
					type: 'POST',
					data: $.param({ 'eleids': eventid, 'project_id':projectid,'start_date':pstartdate,'end_date':penddate,'cdaynumber':daynumber,'user_id':userid,'projectids':projectids }),
					url: $js_config.base_url + 'work_centers/projectallelements',
					global: false,
					success: function(response) {
						$('#events-element-modal').find('.modal-content').html(response);
						$('#events-element-modal').modal('show');
					}
			});

		});

		$('#events-element-modal').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		});


});
</script>


