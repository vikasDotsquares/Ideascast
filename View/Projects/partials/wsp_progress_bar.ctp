<?php
$wsp_progress = $this->Scratch->wsp_progress_bar($project_id, $workspace_id);
// pr($wsp_progress);
//pending things
//cost status, total risks
$wsp_progress = $wsp_progress[0];
$workspace_detail = $wsp_progress['pwd'];
$workspace_team = $wsp_progress['wtc'];
$workspace_tasks = $wsp_progress['wec'];
$workspace_others = $wsp_progress[0]; // assets count
$workspace_lndm = $wsp_progress['wdx']; //links, notes, documents, mind map,
$workspace_decision = $wsp_progress['wdc']; //decision counts
$workspace_feedback = $wsp_progress['wfc']; //fb counts
$workspace_vote = $wsp_progress['wvc']; //vote counts
$workspace_costs = $wsp_progress['wse']; //costs
$workspace_hrisk = (!empty($wsp_progress['wrh']['high_risk_total'])) ? $wsp_progress['wrh']['high_risk_total'] : 0; //high risk
$workspace_hr_off = (!empty($wsp_progress['wrh_off']['high_risk_total_off'])) ? $wsp_progress['wrh_off']['high_risk_total_off'] : 0; //high risk signed off
$workspace_srisk = (!empty($wsp_progress['wrs']['severe_risk_total'])) ? $wsp_progress['wrs']['severe_risk_total'] : 0; //severe risk
$workspace_sr_off = (!empty($wsp_progress['wrs_off']['severe_risk_total_off'])) ? $wsp_progress['wrs_off']['severe_risk_total_off'] : 0; //severe risk signed off
$workspace_rcount = (!empty($wsp_progress['risk_counts']['risk_count'])) ? $wsp_progress['risk_counts']['risk_count'] : 0; //severe risk signed off
$workspace_clevel = $wsp_progress['wlevel']; //confidence level
$workspace_efforts = $wsp_progress['efforts']; //efforts

$wspRole = $workspace_detail['role'];
/* Progress Bar */
// $workspace_detail = getByDbId('Workspace', $workspace_id );
// pr($workspace_detail);

$costURL = Router::Url( array( 'controller' => 'projects', 'action' => 'index', $project_id, 'tab' => 'cost', 'admin' => FALSE ), TRUE );

$ganttURL = SITEURL.'users/event_gantt/'.$project_type.':'.$project_id;

// $wspRole = wspRole($project_id, $workspace_id, $this->Session->read('Auth.User.id'));

$wspRoleCurrent = $wspRole;

if($wspRoleCurrent=='Group Owner'){
	$wspRoleCurrent = 'Owner';
}

if($wspRoleCurrent=='Group Sharer'){
	$wspRoleCurrent = 'Sharer';
}
// pr($wspRole, 1);
// owner/sharer
$wspOwnersTotal = $workspace_team['owner_count'];// $this->Permission->workspaceOwners($project_id, $workspace_id, 1);
$wspSharersTotal = $workspace_team['sharer_count'];// $this->Permission->workspaceSharers($project_id, $workspace_id, 1);


// task counter
// $wspTaskCounters = $this->Permission->wspTaskCounters($project_id, $workspace_id);


$confidence_level = 'Not Set';
$level_value = 0;
$level_class = 'dark-gray';
$level_arrow = 'notsetgrey';
// if(isset($wspTaskCounters[0]['wlevel']) && !empty($wspTaskCounters[0]['wlevel'])){
if(isset($workspace_clevel['level']) && !empty($workspace_clevel['level'])){
	$level_data = $workspace_clevel;
	$confidence_level = $level_data['confidence_level'];
	$level_class = $level_data['confidence_class'];
	$level_arrow = $level_data['confidence_arrow'];
	$level_value = $level_data['level'];
	$level_count = $level_data['level_count'];
}
if($level_value > 0){
	$level_value_current = $level_value.'%';
	if($level_count < 2){
		$level_value_tip = 'Confidence Level<br />For '.$level_count.' Task';
	}else{
		$level_value_tip = 'Confidence Level<br />For '.$level_count.' Tasks';
	}
	$level_value_tip_class = 'cost-tooltip';
}else{
	$level_value_current = '';
	$level_value_tip = '';
	$level_value_tip_class = '';
}

$non_task = (isset($workspace_tasks['NON']) && !empty($workspace_tasks['NON'])) ? $workspace_tasks['NON'] : 0;
$prg_task = (isset($workspace_tasks['PRG']) && !empty($workspace_tasks['PRG'])) ? $workspace_tasks['PRG'] : 0;
$ovd_task = (isset($workspace_tasks['OVD']) && !empty($workspace_tasks['OVD'])) ? $workspace_tasks['OVD'] : 0;
$cmp_task = (isset($workspace_tasks['CMP']) && !empty($workspace_tasks['CMP'])) ? $workspace_tasks['CMP'] : 0;
$pnd_task = (isset($workspace_tasks['PND']) && !empty($workspace_tasks['PND'])) ? $workspace_tasks['PND'] : 0;

$totalTasks = $non_task + $prg_task + $ovd_task + $cmp_task + $pnd_task;
$tasksPercent = ($totalTasks > 0) ? ( ($cmp_task/$totalTasks) * 100 ) : 0;

$wsp_risks = $pending_high_risk = $pending_severe_risk = $sign_high_risk = $sign_severe_risk = 0;

/*$wspTasks =  $this->Permission->wspTasks($project_id, $workspace_id);
if(isset($wspTasks) && !empty($wspTasks)){
	$wspTasks = Set::extract($wspTasks, '{n}.elements.id');
}*/

if($wspRole == 'Creator' || $wspRole == 'Owner' || $wspRole == 'Group Owner') {
	// Cost
	$totalestimatedcost = $workspace_costs['escost'];// $this->Permission->wsp_element_cost($wspTasks, 'estimated_cost');
	$totalspendcost = $workspace_costs['spcost'];// $this->Permission->wsp_element_cost($wspTasks, 'spend_cost');

	$projectCurrencyName = $workspace_detail['sign']; //$this->Common->getCurrencySymbolName($project_id);
	$projectCurrencysymbol = "";

	$estimatedcost = ( isset($totalestimatedcost) && $totalestimatedcost > 0 ) ? $totalestimatedcost : 0;
	$spendcost = ( isset($totalspendcost) && $totalspendcost > 0 ) ? $totalspendcost : 0;
	$max_budget = max( $estimatedcost, $spendcost );

	$force_cost_percentage = 2;

	$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
	$spend_used = ($estimatedcost > 0) ? ( ( $spendcost / $estimatedcost) * 100 ) : 0;
// $workspace_others['cost_status'];//
	$cost_status_text = $this->Permission->wsp_cost_status_text( $estimatedcost, $spendcost);


	// risk counters
	// if there are any task in the wsp
	// if(isset($wspTasks) && !empty($wspTasks)){
		// pr($wspTasks);
		// $user_project_risks = user_project_risks($project_id, $this->Session->read('Auth.User.id'));

		$wsp_risks = $workspace_rcount;// wsp_risks($user_project_risks, $project_id, $wspTasks );
		$pending_high_risk = $workspace_hrisk; //wsp_pending_risks($user_project_risks, $project_id, $wspTasks, 'high');
		$pending_severe_risk = $workspace_srisk; //wsp_pending_risks($user_project_risks, $project_id, $wspTasks, 'severe');
		$sign_high_risk = $workspace_hr_off; //wsp_signedoff_risks($user_project_risks, $project_id, $wspTasks, 'high');
		$sign_severe_risk = $workspace_sr_off; //wsp_signedoff_risks($user_project_risks, $project_id, $wspTasks, 'severe');
	// }

}
/* Progress Bar */


?>


				<?php
				//if(isset($workspace_clevel) && !empty($workspace_clevel['level'])){ ?>
				<div class="progress-col confidence-col ">
					<div class="progress-col-heading">
							<span class="prog-h"><a   >CONFIDENCE <i class="arrow-down"></i></a></span>
						</div>
					<div class="progress-col-cont">
						<ul class="workcount confcounters ">

							<li class="<?php echo $level_class; ?> <?php echo $level_value_tip_class; ?>" title="<?php echo $level_value_tip; ?>"><?php echo $level_value_current; ?></li>
							<span><i class="level-ts <?php echo $level_arrow ; ?>"></i></span>
						</ul>
						<div class="proginfotext"><?php echo  $confidence_level ;  ?></div>
					</div>
				</div>
				 <?php //} ?>


	    	<div class="progress-col work-owners-column">
				<div class="progress-col-heading">
					<span class="prog-h"><a href="#" class="open-team-tab">Team <i class="arrow-down"></i></a></span>
				</div>
				<div class="progress-col-cont">
					<ul class="workcount taskcounters people-counter">
						<?php
						$owners = (!empty($wspOwnersTotal)) ? (($wspOwnersTotal == 1) ? $wspOwnersTotal
						: $wspOwnersTotal ) : '0';
						$owners_tip = $owners." Owners";
						if($owners == 1){
							$owners_tip = $owners." Owner";
						}

						?>
						<?php $sharers =  (!empty($wspSharersTotal)) ? (($wspSharersTotal == 1) ? $wspSharersTotal : $wspSharersTotal) : '0';
						$sharers_tip = $sharers." Sharers";
						if($sharers == 1){
							$sharers_tip = $sharers." Sharer";
						}

						$total_users =  $owners+$sharers;

						$total_users_text =  $total_users." People";
						if($total_users == 1){
							$total_users_text =  $total_users." Person";
						}

						?>

						<li class="dark-gray tipText open-team-tab <?php if(!isset($owners) || $owners == 0){ echo 'zero_class'; }?>" title="<?php echo $owners_tip; ?>"><?php echo $owners; ?></li>
						<li class="light-gray tipText open-team-tab <?php if(!isset($sharers) || $sharers == 0){ echo 'zero_class'; }?>" title="<?php echo $sharers_tip; ?>"><?php echo $sharers; ?></li>
					</ul>
					<div class="proginfotext tipText" title="Your Role"><?php echo $wspRoleCurrent; //$total_users_text; ?></div>
				</div>
			</div>

			<div class="progress-col">

				<?php
					// wsp date progress bar
					$db_detail = $workspace_detail;

					$force_percentage = 2;
					$sd_org_1 = $db_detail['start_date'];
					$ed_org_1 = $db_detail['end_date'];
					$sd_org = date('Y-m-d',strtotime($db_detail['start_date']));
					$ed_org = date('Y-m-d',strtotime($db_detail['end_date']));
					$sd_st = strtotime($sd_org);
					$ed_st = strtotime($ed_org);
					$sn_org = $db_detail['sign_off_date'];
					# signed off
					if(isset($db_detail['sign_off']) && !empty($db_detail['sign_off'])) {
						$sn_date = strtotime($db_detail['sign_off_date']);
						# sign off before end date
						if(date('Y-m-d', $ed_st) > date('Y-m-d', $sn_date)){
							$prj_days_left = daysLeft($sd_org, date('Y-m-d', strtotime($sn_org)), 1);
							$to_end_days = daysLeft(date('Y-m-d', strtotime($sn_org.' +1 day')), $ed_org, 1);
							$total_days = $prj_days_left + $to_end_days;
							$first_percentage = ($total_days > 0) ? ($prj_days_left/$total_days)*100 : 0;
							$sec_percentage =  ($total_days > 0) ? ($to_end_days/$total_days)*100 : 0;
							$first_class = 'green-bg'; $sec_class = 'blue';
							?>
							<div class="progress-col-heading">
								<span class="prog-h"><a href="<?php echo $ganttURL; ?>">Schedule <i class="arrow-down"></i></a></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($first_percentage); ?>%</span>
							</div>
							<div class="progress-col-cont">
								<div class="schedule-bar">
									<?php if(round($prj_days_left) > 0){
										$first_percentage = ($first_percentage < $force_percentage) ? $force_percentage : $first_percentage;
										?>
										<span class="<?php echo $first_class; ?> barTip bar-border"  title="<?php echo number_format($prj_days_left, 0, '', ','); ?> <?php signular_plural($prj_days_left); ?>: <?php echo date('d M Y', $sd_st); ?> to <?php echo date('d M Y', $sn_date); ?>" style="width: <?php echo number_format($first_percentage, 2, '.', ''); ?>%" ></span>
									<?php }
									if(round($to_end_days) > 0){
										$sec_percentage = ($sec_percentage < $force_percentage) ? $force_percentage : $sec_percentage;
										?>
										<span class="<?php echo $sec_class; ?> barTip bar-border"  title="<?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?>: <?php echo date('d M Y', strtotime($sn_org.' +1 day')); ?> to <?php echo date('d M Y', $ed_st); ?>"  style="width: <?php echo number_format($sec_percentage, 2, '.', ''); ?>%"></span>
									<?php } ?>
								</div>
								<div class="proginfotext">Completed <?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?> early</div>
							</div>
							<?php
						}
						# end date is equals to the end date
						else if( date('Y-m-d', $ed_st) == date('Y-m-d', $sn_date)){
							$prj_days_left = daysLeft($sd_org, $ed_org, 1);
							?>
							<div class="progress-col-heading">
								<span class="prog-h"><a href="<?php echo $ganttURL; ?>">Schedule <i class="arrow-down"></i></a></span> <span class="percent-text schedule-percent" title="Percentage of Schedule">100%</span>
							</div>
							<div class="progress-col-cont">
								<div class="schedule-bar" >
									<span class="green-bg barTip " title="<?php echo number_format($prj_days_left, 0, '', ','); ?> <?php signular_plural($prj_days_left); ?>: <?php echo date('d M Y', $sd_st); ?> to <?php echo date('d M Y', $sn_date); ?>" style="width: 100%"></span>
								</div>
								<div class="proginfotext">Completed on time</div>
							</div>
							<?php
						}
						# if sign off after end date
						else if( date('Y-m-d', $ed_st) < date('Y-m-d', $sn_date)){
							$prj_days_left = daysLeft($sd_org, $ed_org, 1);
							$to_end_days = daysLeft(date('Y-m-d', strtotime($ed_org.' +1 day')), date('Y-m-d', strtotime($sn_org)),1);
							$total_days = $prj_days_left + $to_end_days;
							$first_percentage = ($total_days > 0) ? ($prj_days_left/$total_days)*100 : 0;
							$sec_percentage = ($total_days > 0) ? ($to_end_days/$total_days)*100 : 0;
							$first_class = 'green-bg'; $sec_class = 'red';
							$show_percentage = ($prj_days_left > 0) ? ($total_days/$prj_days_left)*100 : 0;
							?>
							<div class="progress-col-heading">
								<span class="prog-h"><a href="<?php echo $ganttURL; ?>">Schedule <i class="arrow-down"></i></a></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($show_percentage); ?>%</span>
							</div>
							<div class="progress-col-cont">
								<div class="schedule-bar" >
									<?php if(round($prj_days_left) > 0){
										$first_percentage = ($first_percentage < $force_percentage) ? $force_percentage : $first_percentage;
										?>
										<span class="<?php echo $first_class; ?> barTip bar-border" title="<?php echo number_format($prj_days_left, 0, '', ','); ?> <?php signular_plural($prj_days_left); ?>: <?php echo date('d M Y', $sd_st); ?> to <?php echo date('d M Y', $ed_st); ?>" style="width: <?php echo number_format($first_percentage, 2, '.', ''); ?>%"></span>
									<?php }
									if(round($to_end_days) > 0){
										$sec_percentage = ($sec_percentage < $force_percentage) ? $force_percentage : $sec_percentage;
										?>
										<span class="<?php echo $sec_class; ?> barTip bar-border" title="<?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?>: <?php echo date('d M Y', strtotime($ed_org.' +1 day')); ?> to <?php echo date('d M Y', strtotime($sn_org)); ?>" style="width: <?php echo number_format($sec_percentage, 2, '.', '');; ?>%"></span>
									<?php } ?>
								</div>
								<div class="proginfotext">Completed <?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?> late</div>
							</div>
							<?php
						}
					}
					# has schedule
					else if( (isset($sd_org_1) && !empty($sd_org_1)) && (isset($ed_org_1) && !empty($ed_org_1)) ) {

						# not started
						if(date('Y-m-d')  < date('Y-m-d', $sd_st) && date('Y-m-d') < date('Y-m-d', $ed_st)) {
							$prj_days_left = daysLeft(date('Y-m-d'), $sd_org);
							$to_end_days = daysLeft($sd_org, $ed_org, 1);
							$total_days = $prj_days_left + $to_end_days;
							$first_percentage = ($total_days > 0) ? ($prj_days_left/$total_days)*100 : 0;
							$sec_percentage = ($total_days > 0) ? ($to_end_days/$total_days)*100 : 0;
							$first_class = '';$sec_class = 'blue';
							$tooltip_days = daysLeft(date('Y-m-d', $sd_st), date('Y-m-d', $ed_st));

							?>
							<div class="progress-col-heading">
								<span class="prog-h"><a href="<?php echo $ganttURL; ?>">Schedule <i class="arrow-down"></i></a></span> <span class="percent-text"></span>
							</div>
							<div class="progress-col-cont">
								<div class="schedule-bar">
									<?php if(round($prj_days_left) > 0){
										$first_percentage = ($first_percentage < $force_percentage) ? $force_percentage : $first_percentage;
										?>
										<span class="<?php echo $first_class; ?> barTip bar-border" title="<?php echo number_format($prj_days_left, 0, '', ','); ?> <?php signular_plural($prj_days_left); ?>: <?php echo date('d M Y'); ?> to <?php echo date('d M Y', strtotime($sd_org.' -1 day' )); ?>" style="width: <?php echo number_format($first_percentage, 2, '.', ''); ?>%"></span>
									<?php }
									if(round($to_end_days) > 0){
										$sec_percentage = ($sec_percentage < $force_percentage) ? $force_percentage : $sec_percentage;
										?>
										<span class="<?php echo $sec_class; ?> barTip bar-border" title="<?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?>: <?php echo date('d M Y', $sd_st); ?> to <?php echo date('d M Y', $ed_st); ?>" style="width: <?php echo number_format($sec_percentage, 2, '.', '');; ?>%"></span>
									<?php } ?>
								</div>
								<div class="proginfotext">Starting in <?php echo number_format($prj_days_left, 0, '', ','); ?> <?php signular_plural($prj_days_left); ?></div>
							</div>
							<?php
						}
						# progressing
						else if(date('Y-m-d', $sd_st) <= date('Y-m-d') && date('Y-m-d', $ed_st) >= date('Y-m-d') ) {
							$prj_days_left = daysLeft($sd_org, date('Y-m-d'));
							$to_end_days = daysLeft(date('Y-m-d'), $ed_org, 1);
							$total_days = $prj_days_left + $to_end_days;
							$first_percentage = ($total_days > 0) ? ($prj_days_left/$total_days)*100 : 0;
							$sec_percentage = ($total_days > 0) ? ($to_end_days/$total_days)*100 : 0;
							$first_class = 'yellow';$sec_class = 'blue';

							?>
							<div class="progress-col-heading">
								<span class="prog-h"><a href="<?php echo $ganttURL; ?>">Schedule <i class="arrow-down"></i></a></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($first_percentage); ?>%</span>
							</div>
							<div class="progress-col-cont">
								<div class="schedule-bar" >
									<?php if(round($prj_days_left) > 0){
										$first_percentage = ($first_percentage < $force_percentage) ? $force_percentage : $first_percentage;
										?>
										<span class="<?php echo $first_class; ?> barTip bar-border" title="<?php echo number_format($prj_days_left, 0, '', ','); ?> <?php signular_plural($prj_days_left); ?>: <?php echo date('d M Y', $sd_st); ?> to <?php echo date('d M Y', time() - 86400); ?>" style="width: <?php echo number_format($first_percentage, 2, '.', ''); ?>%"></span>
									<?php }
									if(round($to_end_days) > 0){
										$sec_percentage = ($sec_percentage < $force_percentage) ? $force_percentage : $sec_percentage;
										?>
										<span class="<?php echo $sec_class; ?> barTip bar-border" title="<?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?>: <?php echo date('d M Y'); ?> to <?php echo date('d M Y', $ed_st); ?>" style="width: <?php echo number_format($sec_percentage, 2, '.', '');; ?>%"></span>
									<?php } ?>
								</div>
								<div class="proginfotext">Day <?php echo number_format($prj_days_left+1, 0, '', ','); ?> of <?php echo number_format($total_days, 0, '', ','); ?></div>
							</div>
							<?php
						}
						# overdue
						else if(date('Y-m-d', $sd_st) < date('Y-m-d') && date('Y-m-d', $ed_st) < date('Y-m-d') ) {
							$prj_days_left = daysLeft($sd_org, $ed_org, 1);
							$to_end_days = daysLeft(date('Y-m-d', strtotime($ed_org.' +1 day')), date('Y-m-d'), 1);
							$all_days = daysLeft($sd_org, date('Y-m-d'));

							$total_days = $prj_days_left + $to_end_days;
							$first_percentage = ($total_days > 0) ? ($prj_days_left/$total_days)*100 : 0;
							$sec_percentage = ($total_days > 0) ? ($to_end_days/$total_days)*100 : 0;
							$all_percentage = ($prj_days_left > 0) ? ($all_days/$prj_days_left)*100 : 0;
							$first_class = 'blue';$sec_class = 'red';
							?>
							<div class="progress-col-heading">
								<span class="prog-h"><a href="<?php echo $ganttURL; ?>">Schedule <i class="arrow-down"></i></a></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($all_percentage); ?>%</span>
							</div>
							<div class="progress-col-cont">
								<div class="schedule-bar" >
									<?php if(round($prj_days_left) > 0){
										$first_percentage = ($first_percentage < 2) ? $force_percentage : $first_percentage;
										?>
										<span class="<?php echo $first_class; ?> barTip bar-border" title="<?php echo number_format($prj_days_left, 0, '', ','); ?> <?php signular_plural($prj_days_left); ?>: <?php echo date('d M Y', $sd_st); ?> to <?php echo date('d M Y', $ed_st); ?>" style="width: <?php echo number_format($first_percentage, 2, '.', ''); ?>%"></span>
									<?php }
									if(round($to_end_days) > 0){
										$sec_percentage = ($sec_percentage < 2) ? $force_percentage : $sec_percentage;
										?>
										<span class="<?php echo $sec_class; ?> barTip bar-border" title="<?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?>: <?php echo date('d M Y', strtotime($ed_org.' +1 day')); ?> to <?php echo date('d M Y'); ?>" style="width: <?php echo number_format($sec_percentage, 2, '.', '');; ?>%"></span>
									<?php } ?>
								</div>
								<div class="proginfotext">Overdue by <?php echo number_format($to_end_days, 0, '', ','); ?> <?php signular_plural($to_end_days); ?></div>
							</div>
							<?php
						}
					}
					else{
						 ?>
						<div class="progress-col-heading">
							<span class="prog-h task-schedule">Schedule <i class="arrow-down"></i></span> <span class="percent-text"></span>
						</div>
						<div class="progress-col-cont">
							<div class="schedule-bar">
								<span class=" barTip" style="width: 100%"></span>
								<span class=" barTip bar-border" style="width: 0%"></span>
							</div>
							<div class="proginfotext">No Schedule</div>
						</div>
						<?php
					}

				 ?>
			</div>

			<?php if($wspRole == 'Creator' || $wspRole == 'Owner' || $wspRole == 'Group Owner'){ ?>
		        <div class="progress-col work-column">
		            <div class="progresscol1">
						<div class="progress-col-heading">
							<span class="prog-h">Work <i class="arrow-down"></i></span>
							<span class="percent-text tipText" title="Percentage Complete"><?php echo ceil($tasksPercent); ?>%</span>
						</div>

						<div class="progress-col-cont">
							<ul class="workcount taskcounters">
			                        <li class="light-gray tipText task_count <?php if($non_task == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:4'; ?>" title="Not Set"><?php echo $non_task; ?></li>

			                        <li class="dark-gray tipText task_count <?php if($pnd_task == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:6'; ?>" title="Not Started"><?php echo $pnd_task; ?></li>

			                        <li class="yellow tipText task_count <?php if($prg_task == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:7'; ?>" title="In Progress"><?php echo $prg_task; ?></li>

			                        <li class="red tipText task_count <?php if($ovd_task == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:1'; ?>" title="Overdue"><?php echo $ovd_task; ?></li>

			                        <li class="green-bg tipText task_count <?php if($cmp_task == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:5'; ?>" title="Completed"><?php echo $cmp_task; ?></li>

		                        </ul>
								<?php $total_ts = number_format($totalTasks); ?>
							<div class="proginfotext"><?php echo $total_ts ?> <?php echo ( $totalTasks == 1) ? "Task" : "Tasks"; ?></div>
		                </div>
					</div>
				</div>



<?php

		$wspTaskCounters[0]['efforts'] = $workspace_efforts;
		$effort_bar_total_hours = (isset($wspTaskCounters[0]) && !empty($wspTaskCounters[0]['efforts']['total_hours'])) ? $wspTaskCounters[0]['efforts']['total_hours'] : 0;

		$effort_bar_completed_hours = (isset($wspTaskCounters[0]) && !empty($wspTaskCounters[0]['efforts']['blue_completed_hours'])) ? $wspTaskCounters[0]['efforts']['blue_completed_hours'] : 0;


		$effort_bar_green_remaining_hours = (isset($wspTaskCounters[0]) && !empty($wspTaskCounters[0]['efforts']['green_remaining_hours'])) ? $wspTaskCounters[0]['efforts']['green_remaining_hours'] : 0;

		$effort_bar_amber_remaining_hours = (isset($wspTaskCounters[0]) && !empty($wspTaskCounters[0]['efforts']['amber_remaining_hours'])) ? $wspTaskCounters[0]['efforts']['amber_remaining_hours'] : 0;

		$effort_bar_red_remaining_hours = (isset($wspTaskCounters[0]) && !empty($wspTaskCounters[0]['efforts']['red_remaining_hours'])) ? $wspTaskCounters[0]['efforts']['red_remaining_hours'] : 0;

		$effort_bar_none_remaining_hours = (isset($wspTaskCounters[0]) && !empty($wspTaskCounters[0]['efforts']['none_remaining_hours'])) ? $wspTaskCounters[0]['efforts']['none_remaining_hours'] : 0;



		if($effort_bar_total_hours > 0){

        $effort_bar_top_percentage = round($effort_bar_completed_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_blue_percentage = round($effort_bar_completed_hours /   ($effort_bar_total_hours ) * 100) ;



        $effort_bar_red_percentage = round($effort_bar_red_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_green_percentage = round($effort_bar_green_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_amber_percentage = round($effort_bar_amber_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_none_percentage = round($effort_bar_none_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        }else{
            $effort_bar_top_percentage = 0;
            $effort_bar_blue_percentage = 0;
            $effort_bar_secondbar_percentage = 0;
            $effort_bar_red_percentage = 0;
            $effort_bar_green_percentage = 0;
            $effort_bar_amber_percentage = 0;
			$effort_bar_none_percentage = 0;
        }

        $remaining_color_tip = 'None';
		$remaining_red_color_tip = "Remaining: $effort_bar_red_remaining_hours Of $effort_bar_total_hours Hours Off Track ($effort_bar_red_percentage%)";


		$remaining_amber_color_tip = "Remaining: $effort_bar_amber_remaining_hours Of $effort_bar_total_hours Hours At Risk ($effort_bar_amber_percentage%)";

		$remaining_green_color_tip = "Remaining: $effort_bar_green_remaining_hours Of $effort_bar_total_hours Hours On Track ($effort_bar_green_percentage%)";

		$remaining_none_color_tip = "Remaining: $effort_bar_none_remaining_hours Of $effort_bar_total_hours Hours No Schedule ($effort_bar_none_percentage%)";


        $remaining_color_tip_blue = 'None';

        if(isset($effort_bar_blue_percentage) && !empty($effort_bar_blue_percentage)){
			$remaining_color_tip_blue = "Completed:  $effort_bar_completed_hours Of $effort_bar_total_hours Hours ($effort_bar_blue_percentage%)";
        }



        if($effort_bar_total_hours ==1){
            $effort_bar_total_hours_text = $effort_bar_total_hours.' Hr';
        }else{
            $effort_bar_total_hours_text = $effort_bar_total_hours.' Hrs';
        }

        $effort_changed_hours = (isset($wspTaskCounters[0]) && !empty($wspTaskCounters[0]['efforts']['change_hours'])) ? $wspTaskCounters[0]['efforts']['change_hours'] : 0;

        $effort_changed_icon = '';

        if($effort_changed_hours ==1){
            $effort_changed_hours_text = '+'.$effort_changed_hours.' Hour Change';
            $effort_changed_icon = 'increasegrey';
        }else if($effort_changed_hours ==-1){
            $effort_changed_hours_text = $effort_changed_hours.' Hour Change';
            $effort_changed_icon = 'decreasegrey';
        }else if($effort_changed_hours !=0 && $effort_changed_hours !=1 ){

            if($effort_changed_hours > 0){
                $effort_changed_hours_text = '+'.$effort_changed_hours.' Hours Change';
                $effort_changed_icon = 'increasegrey';
            }else{
                $effort_changed_hours_text = $effort_changed_hours.' Hours Change';
                $effort_changed_icon = 'decreasegrey';
            }

        }else if($effort_changed_hours == 0  && !empty($wspTaskCounters[0]['efforts']['total_hours'])){
            $effort_changed_hours_text = 'Unchanged';
            $effort_changed_icon = 'notsetgrey';
        }else{
            $effort_changed_hours_text = '';
            $effort_changed_icon = '';
        }


        // to make the bar 100% in width if its less than that.
        $tper = $effort_bar_blue_percentage+$effort_bar_green_percentage+$effort_bar_amber_percentage+$effort_bar_red_percentage;
        $rper = 100 - $tper;
        $incr = 0;
        if(!empty($rper) && $rper <= 1){
            $divide = 0;
            if($effort_bar_blue_percentage)$divide += 1;
            if($effort_bar_green_percentage)$divide += 1;
            if($effort_bar_amber_percentage)$divide += 1;
            if($effort_bar_red_percentage)$divide += 1;
            if($effort_bar_none_percentage)$divide += 1;
            $incr = (!empty($divide)) ? $rper/$divide : $rper/4;
        }
        ?>

            <div class="progress-col">
            <div class="progress-col-heading">
                <span class="prog-h"><a href="javascript:;" style="cursor: default;">Effort <i class="arrow-down" style="cursor: default !important;"></i></a></span>
                <?php if($effort_bar_top_percentage > 0) { ?>
                    <span class="percent-text" title="Percentage Complete"><?php echo $effort_bar_top_percentage; ?>%</span>
                <?php }else if($effort_bar_total_hours_text > 0){ ?>
                    <span class="percent-text" title="Percentage Complete">0%</span>
                <?php } ?>

            </div>
            <div class="progress-col-cont team-prog">
                <div class="team-prog-inner">
                    <div class="schedule-bar" data-original-title="" title="">
                        <?php if($effort_bar_blue_percentage){ ?>
                        <span class="blue barTip bar-border" title="" style="width: <?php echo $effort_bar_blue_percentage+$incr; ?>%" data-original-title="<?php echo $remaining_color_tip_blue; ?>"></span>
						<?php } ?>

						<?php if($effort_bar_green_percentage){ ?>
                        <span class="green barTip bar-border" title="" style="width: <?php echo $effort_bar_green_percentage+$incr; ?>%" data-original-title="<?php echo $remaining_green_color_tip; ?>"></span>
						<?php } ?>

						<?php if($effort_bar_amber_percentage){ ?>
                        <span class="amber barTip bar-border" title="" style="width: <?php echo $effort_bar_amber_percentage+$incr; ?>%" data-original-title="<?php echo $remaining_amber_color_tip; ?>"></span>
						<?php } ?>

						<?php if($effort_bar_red_percentage){ ?>
                        <span class="red barTip bar-border" title="" style="width: <?php echo $effort_bar_red_percentage+$incr; ?>%" data-original-title="<?php echo $remaining_red_color_tip; ?>"></span>
						<?php } ?>

						<?php if($effort_bar_none_percentage){ ?>
                        <span class="grey barTip bar-border" title="" style="width: <?php echo $effort_bar_none_percentage+$incr; ?>%" data-original-title="<?php echo $remaining_none_color_tip; ?>"></span>
						<?php } ?>
                    </div>
                    <?php if(!empty($effort_changed_icon)){ ?>
                    <i class="level-ts tipText <?php echo $effort_changed_icon; ?>" title="<?php echo $effort_changed_hours_text; ?>" style="cursor: default !important;" ></i>
                    <?php } ?>
                </div>

                <?php if($effort_bar_total_hours_text > 0) { ?>
                <div class="teaminfotext " ><span class="tipText" title="Total Effort"><?php echo $effort_bar_total_hours_text; ?></span></div>
                <?php }else{ ?>
                <div class="teaminfotext " ><span>Not Set</span></div>
                <?php } ?>
            </div>
            </div>

				<?php
					// $taskCountAst = $this->ViewModel->getTaskCountAsset($workspace_id);
					// pr($workspace_lndm);
					// pr($workspace_decision);
					// pr($workspace_feedback);
					// pr($workspace_vote);
					// pr($taskCountAst);
					$total_assets = $workspace_others['as_tot'];
					$dc_tot = $workspace_others['dc_tot'];
					$fb_tot = $workspace_others['fb_tot'];
					$vt_tot = $workspace_others['vt_tot'];
					$links_tot = $workspace_lndm['el_tot'];
					$notes_tot = $workspace_lndm['en_tot'];
					$docs_tot = $workspace_lndm['ed_tot'];
					$mms_tot = $workspace_lndm['em_tot'];
					$prg_dec_tot = $workspace_decision['dc_prg'];
					$cmp_dec_tot = $workspace_decision['dc_cmp'];
					$nst_fb_tot = $workspace_feedback['fb_nst'];
					$prg_fb_tot = $workspace_feedback['fb_prg'];
					$ovd_fb_tot = $workspace_feedback['fb_ovd'];
					$cmp_fb_tot = $workspace_feedback['fb_cmp'];
					$nst_vot_tot = $workspace_vote['vt_nst'];
					$prg_vot_tot = $workspace_vote['vt_prg'];
					$ovd_vot_tot = $workspace_vote['vt_ovd'];
					$cmp_vot_tot = $workspace_vote['vt_cmp'];


					// $total_assets = (isset($taskCountAst['0']['0']['total_assets']) && !empty($taskCountAst['0']['0']['total_assets'])) ? $taskCountAst['0']['0']['total_assets'] : 0;
					/*$dc_tot = $dc_tot;
					$fb_tot = (isset($taskCountAst['0']['0']['fb_tot']) && !empty($taskCountAst['0']['0']['fb_tot'])) ? $taskCountAst['0']['0']['fb_tot'] : 0;
					$vt_tot = $fb_tot;
					$links_tot = (isset($taskCountAst['0']['wwel']['links_tot']) && !empty($taskCountAst['0']['wwel']['links_tot'])) ? $taskCountAst['0']['wwel']['links_tot'] : 0;
					$notes_tot = (isset($taskCountAst['0']['wwen']['notes_tot']) && !empty($taskCountAst['0']['wwen']['notes_tot'])) ? $taskCountAst['0']['wwen']['notes_tot'] : 0;
					$docs_tot = (isset($taskCountAst['0']['wwed']['docs_tot']) && !empty($taskCountAst['0']['wwed']['docs_tot'])) ? $taskCountAst['0']['wwed']['docs_tot'] : 0;
					$mms_tot = (isset($taskCountAst['0']['wwem']['mms_tot']) && !empty($taskCountAst['0']['wwem']['mms_tot'])) ? $taskCountAst['0']['wwem']['mms_tot'] : 0;

					$prg_dec_tot = (isset($taskCountAst['0']['wdc']['dc_prg']) && !empty($taskCountAst['0']['wdc']['dc_prg'])) ? $taskCountAst['0']['wdc']['dc_prg'] : 0;
					$cmp_dec_tot = (isset($taskCountAst['0']['wdc']['dc_cmp']) && !empty($taskCountAst['0']['wdc']['dc_cmp'])) ? $taskCountAst['0']['wdc']['dc_cmp'] : 0;


					$nst_fb_tot = (isset($taskCountAst['0']['wfc']['fb_nst']) && !empty($taskCountAst['0']['wfc']['fb_nst'])) ? $taskCountAst['0']['wfc']['fb_nst'] : 0;
					$prg_fb_tot = (isset($taskCountAst['0']['wfc']['fb_prg']) && !empty($taskCountAst['0']['wfc']['fb_prg'])) ? $taskCountAst['0']['wfc']['fb_prg'] : 0;
					$ovd_fb_tot = (isset($taskCountAst['0']['wfc']['fb_ovd']) && !empty($taskCountAst['0']['wfc']['fb_ovd'])) ? $taskCountAst['0']['wfc']['fb_ovd'] : 0;
					$cmp_fb_tot = (isset($taskCountAst['0']['wfc']['fb_cmp']) && !empty($taskCountAst['0']['wfc']['fb_cmp'])) ? $taskCountAst['0']['wfc']['fb_cmp'] : 0;

					$nst_vot_tot = (isset($taskCountAst['0']['wvc']['vt_nst']) && !empty($taskCountAst['0']['wvc']['vt_nst'])) ? $taskCountAst['0']['wvc']['vt_nst'] : 0;
					$prg_vot_tot = (isset($taskCountAst['0']['wvc']['vt_prg']) && !empty($taskCountAst['0']['wvc']['vt_prg'])) ? $taskCountAst['0']['wvc']['vt_prg'] : 0;
					$ovd_vot_tot = (isset($taskCountAst['0']['wvc']['vt_ovd']) && !empty($taskCountAst['0']['wvc']['vt_ovd'])) ? $taskCountAst['0']['wvc']['vt_ovd'] : 0;
					$cmp_vot_tot = (isset($taskCountAst['0']['wvc']['vt_cmp']) && !empty($taskCountAst['0']['wvc']['vt_cmp'])) ? $taskCountAst['0']['wvc']['vt_cmp'] : 0;*/

					$deci_class = 'light-gray';
					$dec_total = 0;
					if($prg_dec_tot > 0){
						$deci_class = 'yellow';
						$dec_total = $prg_dec_tot;

					}else if($cmp_dec_tot > 0){
						$deci_class = 'green-bg';
						$dec_total = $cmp_dec_tot;
					}

					$vot_class = 'light-gray';
					$fb_class = 'light-gray';

					$vot_total = $fb_total = 0;

					if($ovd_fb_tot > 0){
						$fb_class = 'red';
						$fb_total = $ovd_fb_tot;
					}else if($prg_fb_tot > 0){
						$fb_class = 'yellow';
						$fb_total = $prg_fb_tot;
					}else if($nst_fb_tot > 0){
						$fb_class = 'dark-gray';
						$fb_total = $nst_fb_tot;
					}else if($cmp_fb_tot > 0){
						$fb_class = 'green-bg';
						$fb_total = $cmp_fb_tot;
					}


					if($ovd_vot_tot > 0){
						$vot_class = 'red';
						$vot_total = $ovd_vot_tot;
					}else if($prg_vot_tot > 0){
						$vot_class = 'yellow';
						$vot_total = $prg_vot_tot;
					}else if($nst_vot_tot > 0){
						$vot_class = 'dark-gray';
						$vot_total = $nst_vot_tot;
					}else if($cmp_vot_tot > 0){
						$vot_class = 'green-bg';
						$vot_total = $cmp_vot_tot;
					}
				 ?>

				 <div class="progress-col">
				 <div class="progress-col-heading progress-dropdown">
					<span class="prog-h"><a href="javascript:void(0);" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="false">ASSETS <i class="arrow-down"></i></a>

					 <ul class="dropdown-menu">
						 <li><a href="<?php echo SITEURL.'users/projects/'.$project_id.'?wsp='.$workspace_id; ?>"><span class="comt-dp-icon"><i class="compet-all-icon asblack"></i></span>  Workspace Assets</a></li>
					 </ul>
					 </span>
					<span class="percent-text tipText" title="Total Assets"><?php echo $total_assets; ?></span>
					</div>
					 <div class="progress-col-cont">
					  <ul class="progress-assets progress-col-cont">
						<li>
						<span class="assets-count blue tipText <?php if(!isset($links_tot) || $links_tot == 0){ echo 'zero_class'; }?>" title="Total Links"><?php echo $links_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-LinkBlack tipText" title="Links"></i> </span>
						</li>

						  <li>
						<span class="assets-count blue tipText <?php if(!isset($notes_tot) || $notes_tot == 0){ echo 'zero_class'; }?>" title="Total Notes"><?php echo $notes_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-NoteBlack tipText" title="Notes"></i> </span>
						</li>
						<li>
						<span class="assets-count blue  tipText <?php if(!isset($docs_tot) || $docs_tot == 0){ echo 'zero_class'; }?>" title="Total Documents"><?php echo $docs_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DocumentBlack tipText" title="Documents"></i> </span>
						</li>
						  <li>
						<span class="assets-count blue tipText <?php if(!isset($mms_tot) || $mms_tot == 0){ echo 'zero_class'; }?>" title="Total Mind Maps"><?php echo $mms_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-MindMapBlack tipText" title="Mind Maps"></i> </span>
						</li>
						  <li>
						<span class="assets-count <?php echo $deci_class; ?> cost-tooltip <?php if(!isset($dec_total) || $dec_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_dec_tot; ?> Completed <br /> <?php echo $prg_dec_tot; ?> In Progress "><?php echo $dec_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DecisionBlack tipText" title="Decisions"></i> </span>
						</li>
						 <li>
						<span class="assets-count <?php echo $fb_class; ?> cost-tooltip <?php if(!isset($fb_total) || $fb_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_fb_tot; ?> Completed <br /> <?php echo $ovd_fb_tot; ?> Overdue <br /> <?php echo $prg_fb_tot; ?> In Progress <br /> <?php echo $nst_fb_tot; ?> Not Started ""><?php echo $fb_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-FeedbackBlack tipText" title="Feedback"></i> </span>
						</li>

						 <li>
						<span class="assets-count  <?php echo $vot_class; ?> cost-tooltip <?php if(!isset($vot_total) || $vot_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_vot_tot; ?> Completed <br /> <?php echo $ovd_vot_tot; ?> Overdue <br /> <?php echo $prg_vot_tot; ?> In Progress <br /> <?php echo $nst_vot_tot; ?> Not Started "><?php echo $vot_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-VoteBlack tipText" title="Votes"></i> </span>
						</li>

					  </ul>
					 </div>

				 </div>


		        <div class="progress-col progress-cost-sec">
					<div class="progress-col-heading">
						<span class="prog-h"><a href="<?php echo $costURL; ?>">Costs <i class="arrow-down"></i></a></span>
						<?php if(!empty($estimatedcost) && !empty($spendcost)){ ?>
							<span class="percent-text cost-percent" title="Percentage Actual of Budget"><?php echo ceil($spend_used); ?>%</span>
						<?php } ?>
					</div>
					<div class="progress-col-cont">
		                <div class="progress-col-cont-min">
			                <div class="cost-bar cost-tooltip" title="Budget (<?php echo $projectCurrencyName; ?>): <?php echo number_format($estimatedcost, 2, '.', ','); ?>">
							<?php
							//pr($estimate_used);

							if(round($estimatedcost) > 0){
									$estimate_used = ($estimate_used < $force_cost_percentage) ? $force_cost_percentage : $estimate_used;
							} ?>

								<span class="blue" style="width: <?php echo round($estimate_used); ?>%"></span>
							</div>
		                    <div class="cost-bar cost-tooltip" title="Actual (<?php echo $projectCurrencyName; ?>): <?php echo number_format($spendcost, 2, '.', ','); ?>">
							<?php
							//pr($estimate_used);

							if(round($spendcost) > 0){
									$spendcost = ($spendcost < $force_cost_percentage) ? $force_cost_percentage : $spendcost;
							} ?>

								<span class="<?php if($spendcost > $estimatedcost) { ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo (!empty($estimatedcost) && !empty($spendcost)) ? round($spend_used) : ((empty($estimatedcost) && !empty($spendcost)) ? '100' : '0'); ?>%"></span>
							</div>
		                </div>
						<div class="proginfotext"><?php echo $cost_status_text; ?></div>
					</div>
				</div>

		        <div class="progress-col work-column">
		            <div class="progresscol1">
					<div class="progress-col-heading">
						<span class="prog-h"><a href="<?php echo Router::Url( array( 'controller' => 'risks', 'action' => 'risk_map', $project_id, 'admin' => FALSE ), TRUE ); ?>">Risks <i class="arrow-down"></i></a></span>
					</div>
					<div class="progress-col-cont">
							<ul class="workcount">
		                        <li class="darkred tipText <?php if(!isset($pending_high_risk) || $pending_high_risk == 0){ echo 'zero_class'; }?>" title="High"><?php echo $pending_high_risk; ?></li>
		                        <li class="red tipText <?php if(!isset($pending_severe_risk) || $pending_severe_risk == 0){ echo 'zero_class'; }?>" title="Severe"><?php echo $pending_severe_risk; ?></li>
		                    </ul>
						<div class="proginfotext">Pending </div>
		                </div>
					</div>
		            <div class="progresscol2">
					<div class="progress-col-heading">
						<span class="prog-h">&nbsp;</span>
						<span class="percent-text tipText" title="Risks in Workspace"><?php echo $wsp_risks; ?></span>


					</div>
					<div class="progress-col-cont">
							<ul class="workcount">
		                        <li class="darkred tipText <?php if(!isset($sign_high_risk) || $sign_high_risk == 0){ echo 'zero_class'; }?>" title="High"><?php echo $sign_high_risk; ?></li>
		                        <li class="red tipText <?php if(!isset($sign_severe_risk) || $sign_severe_risk == 0){ echo 'zero_class'; }?>" title="Severe"><?php echo $sign_severe_risk; ?></li>
		                    </ul>
						<div class="proginfotext">Signed Off </div>
		                </div>
					</div>
				</div>


			<?php } ?>



<script type="text/javascript">
	$(function(){
		$('.task_count').on('click', function(event) {
			event.preventDefault();
			var url = $(this).data('url');
			location.href = url;
		});
		$('.reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip, .cost-percent, .percent-text').tooltip({
			'template': '<div class="tooltip default-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
			'placement': 'top',
			'container': 'body'
		})
		$('.cost-tooltip').tooltip({
			'placement': 'top',
			'container': 'body',
			'html': true
		})
		$('.open-team-tab').on('click', function(event) {
            event.preventDefault();
            $('#wsp_tabs a[href="#tab_teams"]').tab('show');
        });
	})
</script>
<style>
.progress-assets li .assets-count.blue{ background:#3c8dbc; }
.progress-assets li .assets-count.light-gray{ background:#a6a6a6; }
.progress-assets li .assets-count.green-bg{ background:#5f9322; }
.progress-assets li .assets-count.yellow { background:#e3a809; }
.progress-assets li .assets-count.dark-gray{ background:#666666; }
.progress-assets li .assets-count.red { background:#e5030d; }

.workcount.confcounters li{ cursor: default;}
.workcount.people-counter li {
    cursor: pointer !important;
}
</style>