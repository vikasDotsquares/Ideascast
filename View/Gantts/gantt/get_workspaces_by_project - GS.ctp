<script src="<?php echo SITEURL; ?>plugins/gantt_calender/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo SITEURL; ?>plugins/gantt_calender/codebase/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">
<!--<script src="<?php echo SITEURL; ?>plugins/gantt_calender/codebase/ext/dhtmlxgantt_tooltip.js" type="text/javascript" charset="utf-8"></script>-->

<?php
echo $this->Html->css(array('styles-inner.min'));
// pr($elementsArr);
$mindate = '';
$maxdate = '';
$curr_date = date("Y-m-d");
$data = $link = '';
$i = 1;

$projectCurrencyName=$this->Common->getCurrencySymbolName($project_id);
$crysymbol = html_entity_decode('&pound;');
if($projectCurrencyName == 'USD') {
	$projectCurrencysymbol = html_entity_decode('&dollar;');
}
else if($projectCurrencyName == 'GBP') {
	$projectCurrencysymbol = html_entity_decode('&pound;');
}
else if($projectCurrencyName == 'EUR') {
	$projectCurrencysymbol = html_entity_decode('&euro;');
}
else if($projectCurrencyName == 'DKK' || $projectCurrencyName == 'ISK') {
	$projectCurrencysymbol = 'Kr';
}
if( isset($projectCurrencysymbol) && !empty($projectCurrencysymbol) ){
	$crysymbol = $projectCurrencysymbol;
}

$project_level = ProjectLevel($project_id,$this->Session->read('Auth.User.id'));

//pr($elementsArr);
 //die;
if (isset($elementsArr) && !empty($elementsArr)) {

    foreach ($elementsArr as $key => $workspace) {


		 if(isset($workspace['Workspace']['id']) && !empty($workspace['Workspace']['id']) ){
			 $startdate = empty($workspace['Workspace']['start_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($workspace['Workspace']['start_date']));
         $enddate = empty($workspace['Workspace']['end_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($workspace['Workspace']['end_date']));

			//ye function change ho jayega
        	$tasks_data = $this->Permission->tasks_data($project_id, $workspace['Workspace']['id']);
        	$total_elements = $tasks_data[0][0]['total_tasks'];
        	$total_completed = $tasks_data[0][0]['CMP'];
			// $total_elements = workspace_elements($workspace['Workspace']['id'], true, false);
			// $total_completed = workspace_elements($workspace['Workspace']['id'], true, true);

			$percent = 0;
			if( $total_elements > 0 ) {
				$percent = round((( $total_completed/$total_elements ) * 100), 0, 1);
			}
			if(isset($workspace['Workspace']['sign_off']) && !empty($workspace['Workspace']['sign_off'])){
				$percent = 100;
				$toc = 'bg-red';
			}

			$total_elem = 0;
			if(isset($total_elements) && ($total_elements > 0)){
			$total_elem = $total_elements - $total_completed;
			}

			$percent = $percent/100;
			$percent = ($percent > 0) ? $percent : 0.0010;
			//$tip = "<br/>( Tasks: ".$total_completed." Completed / ".$total_elem." Outstanding )";

			/*========== Start TipText Data ==================================================*/


			$startdate = empty($workspace['Workspace']['start_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($workspace['Workspace']['start_date']));
			$enddate = empty($workspace['Workspace']['end_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($workspace['Workspace']['end_date']));

			$startdate_tip = empty($workspace['Workspace']['start_date']) ? date("d F Y") : date("d F Y", strtotime($workspace['Workspace']['start_date']));

			$enddate_tip = empty($workspace['Workspace']['end_date']) ? date("d F Y") : date("d F Y", strtotime($workspace['Workspace']['end_date']));

			$enddate_tip_o = empty($workspace['Workspace']['end_date']) ? date("d F Y") : date("d F Y", strtotime($workspace['Workspace']['end_date']));

			$durations = 0;
			if( !empty($workspace['Workspace']['start_date']) && !empty($workspace['Workspace']['end_date']) ){
			$enddate_tip = empty($enddate_tip) ? '' : date("d F Y", strtotime("+1 day",strtotime( $enddate_tip)));
			$durations = dateDiff($startdate_tip,$enddate_tip);
			}

			if( empty($workspace['Workspace']['start_date']) ){
				$startdate_tip = 'N/A';
			}

			if( empty($workspace['Workspace']['end_date']) ){
				$enddate_tip_o = 'N/A';
			}


			$workspacEstimateCost = $tasks_data[0][0]['estimate_total'];
			$workspacSpendCost = $tasks_data[0][0]['spend_total'];

			// $workspacEstimateCost = $this->ViewModel->workspace_element_cost($workspace['Workspace']['id'], 1);
			// $workspacSpendCost = $this->ViewModel->workspace_element_cost($workspace['Workspace']['id'], 2);

			//ye bhi change ho sakta hai 20 may
			//$p_permission = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));
			//ye bhi change ho sakta hai 20 may
		//	$user_project = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));

	$all_elements = explode(',', $tasks_data[0][0]['all_tasks']);

					if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) ) {

							 //$this->params['named']
							//costs/index/m_project:36

							$wsp_html = "<a class='perm' href='".SITEURL."costs/index/".$project_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
							$wsp_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/wsp:".$workspace['Workspace']['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
							$wsp_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";

						}else{
							$wsp_html = "<a class='perm' href='".SITEURL."costs/index/".$project_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
							$wsp_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/wsp:".$workspace['Workspace']['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
							$wsp_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";
						}
						$wsp_html .="<a href='".SITEURL."projects/manage_elements/".$project_id."/".$workspace['Workspace']['id']."'><i class='fa fa-th'></i></a>";
			/*=======================================================*/

			$wetip = str_replace("'", "", $workspace['Workspace']['title']);
			$wetip = str_replace('"', "", $wetip);


			$tip = "<div class='gantWorkspace'><span class='wpheader'><span class='ganttDep-tital workspace-title'>Workspace Details</span><div class='anchor_wrap'>".$wsp_html."</div></span><span class='wptitle'>".str_replace("&nbsp;"," ",$wetip)."</span><div class='wpdeails'><span class='wpstdate'>Start: ".$startdate_tip."</span><span class='wpenddate'>End: ".$enddate_tip_o."</span><span class='wpduration'>Duration: ".$durations." days</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wptcomped'>Tasks Completed: ".$total_completed."</span><span class='wptcomped'>Tasks Outstanding: ".$total_elem."</span>";

			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) ) {
			$tip .= "<span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wpbudget'>Estimated: ".$crysymbol.number_format($workspacEstimateCost,2, '.', '')."</span><span class='wpbudget'>Spend: ".$crysymbol.number_format($workspacSpendCost,2, '.', '')."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span>";
			if( isset($all_elements) && !empty($all_elements) ){
				$workspacesElementIds = $all_elements ;
				// $workspacesElementIds = Set::extract($all_elements, '/Element/id');
				$workspaceElementRiskStaus = element_risks($project_id,$workspacesElementIds);
				$tip .= "<span class='wpbudget'>Task Risks: ".$workspaceElementRiskStaus."</span>";
			} else {
				$tip .= "<span class='wpbudget'>Task Risks: 0 SEVERE, 0 HIGH</span>";
			}
			$tip .= "</div></div>";

			}

			$wstatus = 'NON';
			if ((isset($workspace['Workspace']['start_date']) && !empty($workspace['Workspace']['start_date'])) && date('Y-m-d', strtotime($workspace['Workspace']['start_date'])) > date('Y-m-d')) {

				$wstatus = 'PND';
			} else if ((isset($workspace['Workspace']['end_date']) && !empty($workspace['Workspace']['end_date'])) && date('Y-m-d', strtotime($workspace['Workspace']['end_date'])) < date('Y-m-d')) {

				$wstatus = 'OVD';
			} else if (isset($workspace['Workspace']['sign_off']) && !empty($workspace['Workspace']['sign_off']) && $workspace['Workspace']['sign_off'] > 0) {

				$wstatus = 'CMP';
			} else if (((isset($workspace['Workspace']['end_date']) && !empty($workspace['Workspace']['end_date'])) && (isset($workspace['Workspace']['start_date']) && !empty($workspace['Workspace']['start_date']))) && (date('Y-m-d', strtotime($workspace['Workspace']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($workspace['Workspace']['end_date'])) >= date('Y-m-d')) {

				$wstatus = 'PRG';
			}

			/*========== End TipText Data ==================================================*/
		$wtip = str_replace("'", "", $workspace['Workspace']['title']);
		$wtip = str_replace('"', "", $wtip);

        $data .= '{"id": "parent_' . $workspace['Workspace']['id'] . '","start_date":"'.$startdate.'","end_date":"'.$enddate.'","dur":"'.$durations.'","color":"'.$workspace['Workspace']['color_code'] .'", "t_id":"workspace_' . $workspace['Workspace']['id'] . '","status":"'.$wstatus.'","tip": "'.$tip.'","text": "' . str_replace("&nbsp;"," ",$wtip) . '","type":"workspace", "order": "' . $i . '", progress: "'.$percent.'", open: true},';
        if (isset($workspace['Workspace']['Area']) && !empty($workspace['Workspace']['Area'])) {
            foreach ($workspace['Workspace']['Area'] as $keyA => $valueA) {//pr($valueA);
                if (isset($valueA['Element']) && !empty($valueA['Element'])) {

					foreach ($valueA['Element'] as $keyE => $valueE) { //pr($valueE);


                        if((!empty($valueE['start_date']) && date("Y-m-d",strtotime($valueE['start_date'])) < $mindate)  || empty($mindate)){
                            $mindate = !empty($valueE['start_date']) ? date("Y-m-d",strtotime($valueE['start_date'])) : '';
                        }
                        if((!empty($valueE['end_date']) && date("Y-m-d",strtotime($valueE['end_date'])) > $maxdate )  || empty($maxdate) ){
                            $maxdate = !empty($valueE['end_date']) ? date("Y-m-d",strtotime($valueE['end_date'])) : '';
                        }
                        //ye bhi change ho jayega 20 may
                        $satatus = _element_statuses($valueE['id']);

                     //   $start = empty($valueE['start_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($valueE['start_date']));

                        $start = empty($valueE['start_date']) ? $enddate : date("d-m-Y", strtotime($valueE['start_date']));
                       // $end = empty($valueE['end_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($valueE['end_date']));
					    $end = empty($valueE['end_date']) ? $enddate : date("d-m-Y", strtotime($valueE['end_date']));

                        $progress = progressing($start,$end,$satatus['status_short_term']);

                        $days = daysRemaining($start,$end,$satatus['status_short_term']);

						$daysLeft = daysLeft(date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end)));
									$remainingDays = 100 - $daysLeft;
									$day_text = "N/A";


												$class_name = 'undefined';
													if( isset( $valueE['date_constraints'] ) && !empty( $valueE['date_constraints'] ) && $valueE['date_constraints'] > 0 ) {
														if( ((isset( $valueE['start_date'] ) && !empty( $valueE['start_date'] )) && date( 'Y-m-d', strtotime( $valueE['start_date'] ) ) > date( 'Y-m-d' )  )  && $valueE['sign_off'] != 1 ) {
															$class_name = 'not_started';
														}
														else if( ( (isset( $valueE['end_date'] ) && !empty( $valueE['end_date'] )) && date( 'Y-m-d', strtotime( $valueE['end_date'] ) ) < date( 'Y-m-d' ) )  && $valueE['sign_off'] != 1 ) {
															$class_name = 'overdue';
														}
														else if( isset( $valueE['sign_off'] ) && !empty( $valueE['sign_off'] ) ) {
															$class_name = 'completed';
														}
														else if( (((isset( $valueE['end_date'] ) && !empty( $valueE['end_date'] )) && (isset( $valueE['start_date'] ) && !empty( $valueE['start_date'] ))) && (date( 'Y-m-d', strtotime( $valueE['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $valueE['end_date'] ) ) >= date( 'Y-m-d' ) )  && $valueE['sign_off'] != 1 ) {
															$class_name = 'progressing';
														}
													}
													else {
														$class_name = 'undefined';
													}



									if(  $class_name == 'not_started' ) {
										$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($valueE['start_date'])));
										echo "<style>#element_".$valueE['id']." .gantt_task_progress{ display :none ;}</style>";
										echo "<style>#element_".$valueE['id'].".gantt_task_line { border-color: #000 !important; background:none;}</style>";


									}
									else if(  $class_name == 'progressing' ) {
										$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($valueE['end_date'])));

										if($mode=='year'){
											echo "<style>#element_".$valueE['id'].".gantt_task_line .gantt_task_progress { width:100% !important;}</style>";
										}

									}
									else if(  $class_name == 'completed' ) {
										$remainingDays = 100;
										$daysLeft = 0;

									}
									else if(  $class_name == 'overdue' ) {
										$daysLeft = daysLeft( date('Y-m-d', strtotime($valueE['end_date'])), date('Y-m-d'));
									}


                     //   $link .= '{id:"' . $valueE['id'] . '",source:"' . $valueE['id'] . '",target:"' . $workspace['Workspace']['id'] . '",type:"1"},';

						$elelist = $this->Common->element_dependancy_list($valueE['id']);

						$elecrital = $this->Common->element_criticalStaus($valueE['id']);
						 if( isset($elecrital['ElementDependency']['is_critical']) && $elecrital['ElementDependency']['is_critical'] == 1 ){

								$crit = 'television-arrow-red';
							//echo "<style>#element_".$valueE['id']." .gantt_task_progress{ display :block ;}</style>";
						} else {
							//echo "<style>#element_".$valueE['id']." .gantt_task_progress{ display :none ;}</style>";
								$crit = 'television-arrow';
						}

						if( isset($elelist) && !empty($elelist) ){

							foreach($elelist as  $elementdepnedncy){
								$link .= '{id:"' . $valueE['id'] . '",source:"' . $valueE['id'] . '",target:"' . $elementdepnedncy['element_id'] . '",type:"1"},';
							}
						}

						/*========== Start TipText Data ==================================*/
						$eleStart = empty($valueE['start_date']) ? '' : date("d F Y", strtotime($valueE['start_date']));
						$eleEndDate = empty($valueE['end_date']) ? '' : date("d F Y", strtotime($valueE['end_date']));
						$edurations = 0;
						if( !empty($valueE['start_date']) && !empty($valueE['end_date']) ){
							$eleEndDateN = empty($valueE['end_date']) ? '' : date("d F Y", strtotime("+1 day",strtotime( $valueE['end_date'])));
							$edurations = dateDiff($eleStart,$eleEndDateN);
						} else {
							$eleStart = 'N/A';
							$eleEndDate = 'N/A';
						}

						// $workspacEstimateCost = $this->ViewModel->workspace_element_cost($workspace['Workspace']['id'], 1);
						// $workspacSpendCost = $this->ViewModel->workspace_element_cost($workspace['Workspace']['id'], 2);
						$workspacEstimateCost = $tasks_data[0][0]['estimate_total'];
						$workspacSpendCost = $tasks_data[0][0]['spend_total'];

						$elementStatus = 'N/A';
						if($satatus['status_short_term'] == 'PND'){
                            $elementStatus = 'Starts in '.$daysLeft.' days';
                        }else if($satatus['status_short_term'] == 'OVD'){
                            $elementStatus = 'Overdue '.$daysLeft.' days';
                        }else if($satatus['status_short_term'] == 'PRG'){
                            $elementStatus = 'Due '.$daysLeft.' days';
                        }else if($satatus['status_short_term'] == 'CMP'){
                            $elementStatus = 'Completed';
                        }

						$elementPriority = $this->Common->critical_status($valueE['id']);
						$setPriority = 'No';
						if( isset($elementPriority) && $elementPriority == 1 ){
							$setPriority = 'Yes';
						}


						// $elwestimateCost = $this->ViewModel->wsp_element_cost($valueE['id'], 1);
						// $elwspendCost = $this->ViewModel->wsp_element_cost($valueE['id'], 2);
						$elwestimateCost = $tasks_data[0][0]['estimate_total'];
						$elwspendCost = $tasks_data[0][0]['spend_total'];


						$assignedto = 'N/A';
						$assignedto = $this->ViewModel->getassigneduser($project_id,$valueE['id'],'assigned_to');
						$assignedby = 'N/A';
						$assignedby = $this->ViewModel->getassigneduser($project_id,$valueE['id'],'created_by');


						//<i class="fa fa-exclamation" aria-hidden="true"></i>
						if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) ) {

							$task_html = "<a class='perm' href='".SITEURL."costs/index/".$project_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
							$task_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/".$valueE['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
							$task_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";

						}else{
							$task_html = "<a class='perm' href='".SITEURL."costs/index/".$project_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
							$task_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/".$valueE['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
						}

						/*========= Enged Status ============*/
							$engaged_by = '';
							if(isset($valueE['engaged_by']) && !empty($valueE['engaged_by'])){
								$engaged_user = $this->ViewModel->get_user_data( $valueE['engaged_by'] );
								$engaged_by = $engaged_user['UserDetail']['first_name'] . ' ' . $engaged_user['UserDetail']['last_name'];
							}


								$element_assigned = element_assigned( $valueE['id'] );
								$assign_class = 'not-avail';
								$assign_tip = 'No Assignment';
								$engTip = 'N/A';
								if($element_assigned) {
									if($element_assigned['ElementAssignment']['reaction'] == 1) {
										$engTip = "<i class='fa fa-check text-success' aria-hidden='true'></i> Accepted";
									}
									else if($element_assigned['ElementAssignment']['reaction'] == 2) {
										$engTip = "<i class='fa fa-check text-danger' aria-hidden='true'></i> Not accepted";
									}
									else if($element_assigned['ElementAssignment']['reaction'] == 3) {
										$engTip = "<i class='fa fa-square' aria-hidden='true'></i> Disengaged";
									}
									else{
										$engTip = 'N/A';
									}
								}

							$eetip = str_replace("'", "", $valueE['title']);
							$eetip = str_replace('"', "", $eetip);

							$etip_html = "<div class='ganttDep' style='display:none;min-width: 240px'><span class='wpheader'><span class='ganttDep-tital'>Task Details</span><div class='anchor_wrap'>".$task_html."<i class='fa ".$crit." get_depend_new' task_id='".$valueE['id']."'></i><a task_id='".$valueE['id']."' id='element_".$valueE['id']."' data-toggle='modal' data-target='#modal_pop' data-remote='".SITEURL."users/popup_box/".$valueE['id']."/element/".$project_id."' style='cursor:pointer;' ><i class='icon_element_add_black' ></i></a></div></span><div class='dep_data'></div></div>   <div class='gantWorkspace'><span class='wpheader'><span class='ganttDep-tital'>Task Details</span><div class='anchor_wrap'>".$task_html."<i class='fa ".$crit." get_depend' task_id='".$valueE['id']."'></i><a task_id='".$valueE['id']."' id='element_".$valueE['id']."' data-toggle='modal' data-target='#modal_pop' data-remote='".SITEURL."users/popup_box/".$valueE['id']."/element/".$project_id."' style='cursor:pointer;' ><i class='icon_element_add_black' ></i></a></div></span><span class='wptitle'>".str_replace("&nbsp;"," ",$eetip)."</span><div class='wpdeails'><span class='wpstdate'>Start: ".$eleStart."</span><span class='wpenddate'>End: ".$eleEndDate."</span><span class='wpduration'>Duration: ".$edurations." days</span><span class='wptcomped'>Priority: ".$setPriority."</span><span class='wptcomped'>Status: ".$elementStatus."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wptcomped'>Assigned to: ".$assignedto."</span><span class='wptcomped'>Assigned by: ".$assignedby."</span><span class='wptcomped'>Schedule: ".$engTip."</span>";

							if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) ) {
							$etip_html .= "<span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wpbudget'>Estimated: ".$crysymbol.number_format($elwestimateCost,2, '.', '')."</span><span class='wpbudget'>Spend: ".$crysymbol.number_format($elwspendCost,2, '.', '')."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span>";

							$rr = null;
							/*------Its taking time in load---------------*/
							$RTip = element_risks($project_id,$valueE['id']);
							if(isset($RTip) && !empty($RTip)){
								$rr = $RTip;
							}

							$etip_html .= "<span class='wpbudget'>Risks: ".$rr."</span></div></div>";

							}


						$etip = str_replace("'", "", $valueE['title']);
						$etip = str_replace('"', "", $etip);

                        $data .= '{"id": "' . $valueE['id'] . '","element_tip": "'.$etip_html.'","area":"'.$valueA['title'].'","dur":"'.$days['total_days'].'","total_days":"'.$days['total_days'].'","remaining_days":"'.$days['remaining_days'].'","left_days":"'.$days['left_days'].'","color":"'.$valueE['color_code'] .'", "t_id":"element_' . $valueE['id'] . '","text": "' . str_replace("&nbsp;"," ",$etip) . '","start_date": "' . $start . '", "end_date":"' . $end . '", "status":"' . $satatus['status_short_term'] . '", "order": "' . $valueE['id'] . '", "progress": "'.$progress.'", "parent": "parent_' . $valueA['workspace_id'] . '", open: true},';
                        $i++;
                    }


                }
                $i++;
            }
        }
        $i++;
		}
    }
}

//echo $mindate.'=='.$maxdate;
function daysRemaining($start,$end,$status){
    error_reporting(0);
    $end = date('d-m-Y', strtotime($end . ' +1 day'));
    $curr_date = date("d-m-Y");
    $total_days = round(abs(strtotime($end) - strtotime($start))/(60*60*24));
   // $remaining_days = floor(abs(strtotime($end) - strtotime($curr_date))/(60*60*24))+1;
    $remaining_days = round(abs(strtotime($end) - strtotime($curr_date))/(60*60*24));
    $left_days = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24))-1;
    if ($status == 'NON') {
        return array("total_days"=>0,"remaining_days"=>0,"left_days"=>0);
    } else if ($status == 'PND') {
        $remaining = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24));
        return array("total_days"=>$total_days,"remaining_days"=>$remaining,"left_days"=>0);
    } else if ($status == 'PRG') {
        return array("total_days"=>$total_days,"remaining_days"=>$remaining_days,"left_days"=>$left_days);
    } else if ($status == 'OVD') {
        $left = round(abs(strtotime($curr_date) - strtotime($end))/(60*60*24));
        return array("total_days"=>$total_days,"remaining_days"=>0,"left_days"=>$left);
    } else if ($status == 'CMP') {
        return array("total_days"=>$total_days,"remaining_days"=>0,"left_days"=>0);
    } else {
        return array("total_days"=>0,"remaining_days"=>0,"left_days"=>0);
    }
}
function progressing($start,$end,$status){
    error_reporting(0);
    if ($status == 'NON') {
        return $progress = '0.0';
    } else if ($status == 'PND') {
        return $progress = '0.0';
    } else if ($status == 'PRG') {
        $curr_date = date("d-m-Y");
        $total_days = round(abs(strtotime($end) - strtotime($start))/(60*60*24));
        $remaining_days = round(abs(strtotime($end) - strtotime($curr_date))/(60*60*24));
        $left_days = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24));
       // return round( $left_days/$total_days*1, 1, PHP_ROUND_HALF_UP);
        return $left_days/$total_days*1;
    } else if ($status == 'OVD') {
        return $progress = '1.0';
    } else if ($status == 'CMP') {
        return $progress = '1.0';
    } else {
        return '0.0';
    }



}





if(!empty($mindate) && !empty($maxdate)){
    $totaldays = floor(abs(strtotime($maxdate) - strtotime($mindate))/(60*60*24))+1;
}else{
    $totaldays = floor(abs(strtotime($maxdate) - strtotime($mindate))/(60*60*24));

}

if($curr_date <= $maxdate && $mindate <= $curr_date){

    if(!empty($mindate) && !empty($maxdate)){
        $remainingdays = floor(abs(strtotime($maxdate) - strtotime($curr_date))/(60*60*24))+1;
    }else{
        $remainingdays = floor(abs(strtotime($maxdate) - strtotime($curr_date))/(60*60*24));
    }

    $leftdays = floor(abs(strtotime($curr_date) - strtotime($mindate))/(60*60*24));
}else {
    $remainingdays = 0;
    if(!empty($mindate) && !empty($maxdate)  && $mindate <= $curr_date){
        $leftdays = floor(abs(strtotime($maxdate) - strtotime($mindate))/(60*60*24))+1;
    }else if(!empty($mindate) && !empty($maxdate)  && $mindate >= $curr_date){
        $leftdays = 0;
    }else{
        $leftdays = floor(abs(strtotime($maxdate) - strtotime($mindate))/(60*60*24));
    }
}

//echo $mindate.' '.$maxdate;

/*
* Project Total cost details
*/

	$project_detail = getByDbId("Project", $project_id, ['id', 'title', 'start_date', 'end_date', 'currency_id', 'budget']);
    $project_detail = $project_detail['Project'];
    $projectCurrencyNames = "GBP";
    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id', 'country', 'name', 'symbol', 'sign']);
        $currency_detail = $currency_detail['Currency'];
        $projectCurrencyNames = $currency_detail['sign'];
        // pr($currency_detail);
    }

	$crysymbol = html_entity_decode('&pound;');
	if($projectCurrencyNames == 'USD') {
		$projectCurrencysymbol = html_entity_decode('&dollar;');
	}
	else if($projectCurrencyNames == 'GBP') {
		$projectCurrencysymbol = html_entity_decode('&pound;');
	}
	else if($projectCurrencyNames == 'EUR') {
		$projectCurrencysymbol = html_entity_decode('&euro;');
	}
	else if($projectCurrencyNames == 'DKK' || $projectCurrencyNames == 'ISK') {
		$projectCurrencysymbol = 'Kr';
	}
	if( isset($projectCurrencysymbol) && !empty($projectCurrencysymbol) ){
		$currency_symbol = $crysymbol = $projectCurrencysymbol;
	}



    /* if($currency_symbol == 'USD') {
        $currency_symbol = "<i class='fa fa-dollar' style='font-weight: 500'></i>";
    }
    else if($currency_symbol == 'GBP') {
        $currency_symbol = "<i class='fa fa-gbp' style='font-weight: 500'></i>";
    }
    else if($currency_symbol == 'EUR') {
        $currency_symbol = "<i class='fa fa-eur' style='font-weight: 500' ></i>";
    }
    else if($currency_symbol == 'DKK' || $currency_symbol == 'ISK') {
        $currency_symbol = "<strong style='font-weight: 500'>Kr</strong>";
    } */




    $project_wsps = get_project_workspace($project_id);
    $workspaces = Set::extract($project_wsps, '/Workspace/id');
    $all_workspace_elements = workspace_elements($workspaces);
    $westimate_sum = $wspend_sum = 0;
    if(isset($all_workspace_elements) && !empty($all_workspace_elements)){
        $wels = Set::extract($all_workspace_elements, '/Element/id');
        $westimate_sum = $this->ViewModel->wsp_element_cost($wels, 1);
        $wspend_sum = $this->ViewModel->wsp_element_cost($wels, 2);
    }

/*============================================*/
?>
<style type="text/css">
    html, body{ height:100%; padding:0px; margin:0px; }
    .weekend{ background: #f4f7f4 !important;}
    .gantt_selected .weekend{ background:#FFF3A1 !important; }
    .well {
        text-align: right;
    }
    @media (max-width: 991px) {
        .nav-stacked>li{ float: left;}
    }

    .container-fluid .gantt_wrapper {
        height: 700px;
        width: 100%;
    }
    .gantt_container {
        border-radius: 4px;
    }
    .gantt_grid_scale { background-color: transparent; }
    .gantt_hor_scroll { margin-bottom: 1px; }

	.tooltip-inner.adjusted-align{ text-align: left;}

</style>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_medium" class="modal modal-success fade">
        <div class="modal-dialog modal-md modal-sm">
             <div class="modal-content"></div>
        </div>
</div>



<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-12">
                <?php
                $date_array = $this->requestAction(array("controller"=>"users","action"=>"get_project_date",$project_id));
               // pr($date_array);
                ?>


                <div class="gantt_wrappers panel padding"   id="" >
                    <div class="pull-left" style="">
                         <!--<label>Project: </label>-->
                        <span class="completeddays">
                            <?php /* <span  style='cursor:pointer;' data-target='#modal_medium' data-toggle='modal' id='project_<?php echo $project_id; ?>' title='<?php echo $project_name;?>' class='gantt_tree_content1 granttData tipText'><?php echo $project_name;?></span> */?>

							<a class="btn btn-default btn-xs tipText option_blue btn_open __web-inspector-hide-shortcut__" title="" href="<?php echo SITEURL."projects/index/".$project_id; ?>"  data-original-title="Open Project">
								<i class="fa fa-folder-open"></i>
							</a>
							<?php if( isset($priorty_data) && !empty($priorty_data) ){ ?>
							<a class="btn btn-default btn-xs tipText option_blue btn_open __web-inspector-hide-shortcut__ ganttprioritytsk" title="" href="javascript:void(0)" data-original-title="Show Priority Tasks">
							<i class="fa fa-arrow-right red-arrow-task tipText" title="" style="color: #dd4c3a; cursor: pointer;"></i>
                            </a>
							<?php } else { ?>
							<a class="btn btn-default btn-xs tipText  option_blue" data-original-title="Show Priority Tasks" data-toggle="modal" data-target="#modal_priority" >
								<i class="fa fa-arrow-right red-arrow-task tipText" title="" style="color: #dd4c3a; cursor: pointer;"></i>
                            </a>
							<?php } ?>

							<?php
							$flfg = $this->ViewModel->sharingPermitType($project_id,$this->Session->read('Auth.User.id'));

							//if($flfg == true ){ ?>
							<a class="btn btn-default btn-xs tipText option_blue users_tip_task btn_open __web-inspector-hide-shortcut__" title="Task Assignments" href="javascript:" >
								<i class="fa fa-user"></i>
							</a>
							<?php
							//}

							 $estimatedCostSum =  (isset($westimate_sum) && !empty($westimate_sum)) ? number_format($westimate_sum, 2, '.', '') : 0.00;

							 $wspendCstsum =  (isset($wspend_sum) && !empty($wspend_sum)) ? number_format($wspend_sum, 2, '.', '') : 0.00;

							$project_cost_icons = "<a class='perm' href='".SITEURL."costs/index/".$project_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";

							$costBudgetContent = "<div class='ganttDep' style='display:none;min-width:215px'><span class='wpheader'><span class='ganttDep-tital'>Project Cost</span><div class='anchor_wrap'>".$project_cost_icons."</div></span><div class='dep_data'></div></div><div class='gantWorkspace'><span class='wpheader' style='width:211px  !important'><span class='ganttDep-tital'>Project Cost</span><div class='anchor_wrap'>".$project_cost_icons."</div></span><span class='wptitle' style='width:211px'>Total</span><div class='wpdeails'>
							<span class='wpstdate'>Budget: ".$currency_symbol.(isset($project_detail['budget']) && !empty($project_detail['budget'])? $project_detail['budget'] : 0.00 )."</span>
							<span class='wpbudget'>Estimated: ".$currency_symbol.$estimatedCostSum."</span>
							<span class='wpbudget'>Spend: ".$currency_symbol.$wspendCstsum."</span></div></div> ";

							if($flfg == true ){
							?>
							<a class="btn btn-default btn-xs tipText projectCstDetails" title="Cost Totals" href="javascript:" data-content="<?php echo $costBudgetContent;?>" ><i class="fa-manage-cost" style="height:16px;width:15px;margin:-1px;"></i></a>
							<?php } ?>

                        </span>


                    </div>
                    <div class="pull-right project_date_sec" style="">
                        <label>Project Start: </label>
                        <span class="completeddays">
                            <?php
                             //pr($projects);
                            //echo !empty($projects['Project']['start_date']) ? date("d M y",strtotime($projects['Project']['start_date'])) : 'N/A';
                            echo !empty($projects['Project']['start_date']) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projects['Project']['start_date'])),$format = 'd M y') : 'N/A';

                            ?>
                        </span>
                        &nbsp;&nbsp;<label>End: </label><span class="remainingdays">
                            <?php
                            //echo !empty($projects['Project']['end_date']) ? date("d M y",strtotime($projects['Project']['end_date'])) : 'N/A';
                            echo !empty($projects['Project']['end_date']) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projects['Project']['end_date'])),$format = 'd M y') : 'N/A';
                            ?>
                        </span>
                         &nbsp; &nbsp;<label>Days Completed:</label>
                        <span class="completeddays">
                            <?php
                                //echo $leftdays;
                            echo $date_array['leftdays'];
                            ?>
                        </span>
                         &nbsp;&nbsp;<label>Days Remaining: </label><span class="remainingdays">
                            <?php
                                //echo $remainingdays;
                             echo $date_array['remainingdays'];

                            ;?>
                        </span>
                         &nbsp;&nbsp;<label>Total Days: </label><span class="totaldays">
                            <?php
                            //echo $totaldays;
                            echo $date_array['totaldays'];
                            ?>
                        </span>
                    </div>


                </div>




            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">

            <div class="col-xs-12">
                <div class="table-responsive">
                    <table style="width: 100%;">
                        <tr>
                            <td  style="width: 100%;">
                                <div class="gantt_wrapper panel" id="gantt_here" ></div>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_large" class="modal modal-success fade">
   <div class="modal-dialog modal-lg modal-sm">
      <div class="modal-content"></div>
   </div>
</div>







<?php include 'gantt_js_css.ctp';?>
<script>
$(function(){

	setTimeout(function(){
	// $('.gantt_grid_head_duration').trigger('click');
	 $('.gantt_sort.gantt_desc').trigger('click');
	// $('.gantt_sort.gantt_asc').trigger('click');

	},1000);



})
</script>