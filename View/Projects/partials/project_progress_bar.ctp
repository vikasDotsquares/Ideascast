<?php
$project_progress = $this->Permission->project_progress($project_id);
 // pr($project_progress);
$totalRisk = $project_progress[0][0]['total_risk'];

$projectRole = $project_progress[0]['user_permissions']['prj_role'];

$projectRoleCurrent = $projectRole;

if($projectRoleCurrent=='Group Owner'){
$projectRoleCurrent = 'Owner';
}

if($projectRoleCurrent=='Group Sharer'){
$projectRoleCurrent = 'Sharer';
}

$project_type = $project_progress[0][0]['prj_type'];
$ProjectOwnersTotal = $project_progress[0][0]['owners_count'];
$ProjectSharersTotal = $project_progress[0][0]['sharer_count'];

$totalWsp = $project_progress[0][0]['WNON']+$project_progress[0][0]['WPND']+$project_progress[0][0]['WPRG']+$project_progress[0][0]['WOVD']+$project_progress[0][0]['WCMP'];
$totalTasks = $project_progress[0][0]['NON']+$project_progress[0][0]['PND']+$project_progress[0][0]['PRG']+$project_progress[0][0]['OVD']+$project_progress[0][0]['CMP'];


$costURL = Router::Url( array( 'controller' => 'projects', 'action' => 'index', $project_id, 'tab' => 'cost', 'admin' => FALSE ), TRUE );

$ganttURL = SITEURL.'users/event_gantt/'.$project_type.':'.$project_id;

$wspPercent = ($totalWsp > 0) ? ( ($project_progress[0][0]['WCMP']/$totalWsp) * 100 ) : 0;

$tasksPercent = ($totalTasks > 0) ? ( ($project_progress[0][0]['CMP']/$totalTasks) * 100 ) : 0;


$total_skills = (!empty($project_progress[0][0]['total_skills'])) ? $project_progress[0][0]['total_skills'] : 0;
$total_subjects = (!empty($project_progress[0][0]['total_subjects'])) ? $project_progress[0][0]['total_subjects'] : 0;
$total_domains = (!empty($project_progress[0][0]['total_domains'])) ? $project_progress[0][0]['total_domains'] : 0;

$board_count = (!empty($project_progress[0]['board']['pb_count'])) ? $project_progress[0]['board']['pb_count'] : 0;

if($projectRole == 'Creator' || $projectRole == 'Owner' || $projectRole == 'Group Owner'){
	// Cost
	$totalestimatedcost = $project_progress[0][0]['estimate_total'];
	$totalspendcost = $project_progress[0][0]['spend_total'];

	$projectCurrencyName = $project_progress[0]['currencies']['sign'];
	$projectbudget = $project_progress[0]['projects']['budget'];
	/*$projectCurrencysymbol = '';
	if($projectCurrencyName == 'USD') {
		$projectCurrencysymbol = "&#36;";
	}
	else if($projectCurrencyName == 'GBP') {
		$projectCurrencysymbol = "&#163;";
	}
	else if($projectCurrencyName == 'EUR') {
		$projectCurrencysymbol = "&#8364;";
	}
	else if($projectCurrencyName == 'DKK' || $projectCurrencyName == 'ISK') {
		$projectCurrencysymbol = "Kr";
	}*/

	$force_cost_percentage = 2;
	$estimatedcost = ( isset($totalestimatedcost) && $totalestimatedcost > 0 ) ? $totalestimatedcost : 0;
	$spendcost = ( isset($totalspendcost) && $totalspendcost > 0 ) ? $totalspendcost : 0;
	$projectbudget = ( !empty($projectbudget)) ? $projectbudget : 0;
	$max_budget = max($estimatedcost, $spendcost, $projectbudget);
	$budget_used = ($max_budget > 0) ? ( ( $projectbudget / $max_budget) * 100 ) : 0;
	$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
	$spend_used = ($max_budget > 0) ? ( ( $spendcost / $max_budget) * 100 ) : 0;

	$max_cost = max($spendcost, $projectbudget);
	$cost_spend_used = ($max_cost > 0 && $spendcost > 0 && $projectbudget > 0) ? ( ( $spendcost / $projectbudget) * 100 ) : 0;

	$cost_status_text = $this->Permission->wsp_cost_status_text( $estimatedcost, $spendcost);
	//viewmodel->prjCstSts

	// risk counters
	$pending_high_risk = (!empty($project_progress[0][0]['phigh_risk'])) ? $project_progress[0][0]['phigh_risk'] : 0;
	$pending_severe_risk = (!empty($project_progress[0][0]['psevere_risk'])) ? $project_progress[0][0]['psevere_risk'] : 0;
	$sign_high_risk = (!empty($project_progress[0][0]['shigh_risk'])) ?$project_progress[0][0]['shigh_risk'] : 0;
	$sign_severe_risk = (!empty($project_progress[0][0]['ssevere_risk'])) ? $project_progress[0][0]['ssevere_risk'] : 0;


	// Reward counts
	$total_allocated = $project_progress[0][0]['total_rewards'];
	$by_acclerate = $project_progress[0][0]['total_acc'];
	if(is_project_rewarded($project_id)){
		$project_reward_setting = project_reward_setting($project_id, 1);
		$total_allocations = ($project_reward_setting) ? $project_reward_setting['RewardSetting']['ov_allocation'] :  0;

		$total_amount = $total_allocated + $by_acclerate;

		$reward_percent = ($total_allocations > 0) ? (($total_amount/$total_allocations)*100) : 0;
	}

	$project_ssd = $this->Permission->project_ssd($project_id);

	$total_competency = $total_skills + $total_subjects + $total_domains;
	$total_comp_percent = $project_ssd[0][0]['skill_total'] + $project_ssd[0][0]['sub_total'] + $project_ssd[0][0]['domain_total'];
	$avg_competency = (!empty($total_competency)) ? round(($total_comp_percent/$total_competency)*100) : 0;
}

		$confidence_level = 'Not Set';
		$level_value = 0;
		$level_class = 'dark-gray';
		$level_arrow = 'notsetgrey';
		if(isset($project_progress[0]['wlevel']['level']) && !empty($project_progress[0]['wlevel']['level'])){
			$level_data = $project_progress[0]['wlevel'];
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

?>

			<?php if($projectRoleCurrent=='Owner' || $projectRoleCurrent=='Creator'){ ?>
			<div class="asset_counter">
				<?php echo $this->element('../Projects/partials/el_rag', array('project_id' => $project_id)); ?>
			</div>
			<?php } ?>

		<?php

		//if(isset($project_progress[0]['wlevel']) && !empty($project_progress[0]['wlevel']['level'])){ ?>
		<div class="progress-col confidence-col ">
			<div class="progress-col-heading">
					<span class="prog-h"><a >CONFIDENCE <i class="arrow-down"></i></a></span>
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
					<ul class="workcount taskcounters">
						<?php
						$owners = (!empty($ProjectOwnersTotal)) ? (($ProjectOwnersTotal == 1) ? $ProjectOwnersTotal
						: $ProjectOwnersTotal ) : '0';
						$owners_tip = $owners." Owners";
						if($owners == 1){
							$owners_tip = $owners." Owner";
						}

						?>
						<?php $sharers =  (!empty($ProjectSharersTotal)) ? (($ProjectSharersTotal == 1) ? $ProjectSharersTotal : $ProjectSharersTotal) : '0';
						$sharers_tip = $sharers." Sharers";
						if($sharers == 1){
							$sharers_tip = $sharers." Sharer";
						}


						$total_users =  $owners+$sharers;

						$total_users_text =  $total_users." People";
						if($total_users == 1){
							$total_users_text =  $total_users." Person";
						}


						$show_opp = 'hide';
						$tbclass = "tbdr";
						if ( isset($project_progress[0]['projects']['sign_off']) && $project_progress[0]['projects']['sign_off'] != 1 ) {
							//$board_count : board request count
							if(!empty($opportunity_cnt) ) {
								$show_opp = 'show';
								$tbclass = '';
							}
						}
						//Pending Opportunity Requests
						?>

						<li class="dark-gray tipText open-team-tab <?php if(!isset($owners) || $owners == 0){ echo 'zero_class'; }?>" style="cursor: pointer;" title="<?php echo $owners_tip; ?>"><?php echo $owners; ?></li>
						<li class="light-gray tipText open-team-tab <?php if(!isset($sharers) || $sharers == 0){ echo 'zero_class'; }?>   <?php echo $tbclass; ?>" style="cursor: pointer;" title="<?php echo $sharers_tip; ?>"><?php echo $sharers; ?></li>



						<li class="yellow tipText <?php echo $show_opp;?> <?php if(!isset($board_count) || $board_count == 0){ echo 'zero_class'; }?>" id="count_opp_req" title="Pending Opportunity Requests">
							<?php /* <a <?php if(!empty($board_count)) { ?> href="<?php echo Router::Url( array( 'controller' => 'boards', 'action' => 'project_request', $project_id, '#'=> 'project_view', 'admin' => FALSE ), TRUE ); ?>" <?php } ?>><?php echo $board_count; ?></a> */?>

							<a <?php if(!empty($board_count)) { ?> href="<?php echo Router::Url( array( 'controller' => 'boards', 'action' => 'opportunity','request', $project_id, 'admin' => FALSE ), TRUE ); ?>" <?php } ?>><?php echo $board_count; ?></a>

						</li>

					</ul>
					<div class="proginfotext tipText" title="Your Role"><?php echo $projectRoleCurrent;//$total_users_text; ?></div>
				</div>
			</div>

			<div class="progress-col">

				<?php
				// Project date progress bar
				$db_detail = $projects['Project'];

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

				?>
			</div>

			<?php if($projectRole == 'Creator' || $projectRole == 'Owner' || $projectRole == 'Group Owner'){

			?>
	            <div class="progress-col work-column">
	                <div class="progresscol1">
					<div class="progress-col-heading">
						<span class="prog-h">Work <i class="arrow-down"></i></span>
						<span class="percent-text tipText" title="Percentage Complete"><?php echo ceil($wspPercent); ?>%</span>
					</div>
					<!-- WSP COUNTER -->
					<div class="progress-col-cont">
							<ul class="workcount">
		                        <li class="light-gray tipText <?php if(!isset($project_progress[0][0]['WNON']) || $project_progress[0][0]['WNON'] == 0){ echo 'zero_class'; }?>" title="Not Set"><?php echo (isset($project_progress[0][0]['WNON']) && !empty($project_progress[0][0]['WNON'])) ? $project_progress[0][0]['WNON'] : 0; ?></li>
		                        <li class="dark-gray tipText <?php if(!isset($project_progress[0][0]['WPND']) || $project_progress[0][0]['WPND'] == 0){ echo 'zero_class'; }?>" title="Not Started"><?php echo (isset($project_progress[0][0]['WPND']) && !empty($project_progress[0][0]['WPND'])) ? $project_progress[0][0]['WPND'] : 0; ?></li>
		                        <li class="yellow tipText <?php if(!isset($project_progress[0][0]['WPRG']) || $project_progress[0][0]['WPRG'] == 0){ echo 'zero_class'; }?>" title="In Progress"><?php echo (isset($project_progress[0][0]['WPRG']) && !empty($project_progress[0][0]['WPRG'])) ? $project_progress[0][0]['WPRG'] : 0; ?></li>
		                        <li class="red tipText <?php if(!isset($project_progress[0][0]['WOVD']) || $project_progress[0][0]['WOVD'] == 0){ echo 'zero_class'; }?>" title="Overdue"><?php echo (isset($project_progress[0][0]['WOVD']) && !empty($project_progress[0][0]['WOVD'])) ? $project_progress[0][0]['WOVD'] : 0; ?></li>
		                        <li class="green-bg tipText <?php if(!isset($project_progress[0][0]['WCMP']) || $project_progress[0][0]['WCMP'] == 0){ echo 'zero_class'; }?>" title="Completed"><?php echo (isset($project_progress[0][0]['WCMP']) && !empty($project_progress[0][0]['WCMP'])) ? $project_progress[0][0]['WCMP'] : 0; ?></li>
	                        </ul>

						<?php $total_ws = number_format($totalWsp); ?>
							<div class="proginfotext"><?php echo $total_ws ?> <?php echo ( $totalWsp == 1) ? "Workspace" : "Workspaces"; ?></div>

	                    </div>
					</div>
	                <div class="progresscol2">
					<div class="progress-col-heading">
						<span class="prog-h"></span> <span class="percent-text tipText" title="Percentage Complete"><?php echo ceil($tasksPercent); ?>%</span>
					</div>
					<!-- TASK COUNTERS -->
					<div class="progress-col-cont">
							<ul class="workcount taskcounters">
		                        <li class="light-gray tipText task_count <?php if(!isset($project_progress[0][0]['NON']) || $project_progress[0][0]['NON'] == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:4'; ?>" title="Not Set"><?php echo (isset($project_progress[0][0]['NON']) && !empty($project_progress[0][0]['NON'])) ? $project_progress[0][0]['NON'] : 0; ?></li>
		                        <li class="dark-gray tipText task_count <?php if(!isset($project_progress[0][0]['PND']) || $project_progress[0][0]['PND'] == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:6'; ?>" title="Not Started"><?php echo (isset($project_progress[0][0]['PND']) && !empty($project_progress[0][0]['PND'])) ? $project_progress[0][0]['PND'] : 0; ?></li>
		                        <li class="yellow tipText task_count <?php if(!isset($project_progress[0][0]['PRG']) || $project_progress[0][0]['PRG'] == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:7'; ?>" title="In Progress"><?php echo (isset($project_progress[0][0]['PRG']) && !empty($project_progress[0][0]['PRG'])) ? $project_progress[0][0]['PRG'] : 0; ?></li>
		                        <li class="red tipText task_count <?php if(!isset($project_progress[0][0]['OVD']) || $project_progress[0][0]['OVD'] == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:1'; ?>" title="Overdue"><?php echo (isset($project_progress[0][0]['OVD']) && !empty($project_progress[0][0]['OVD'])) ? $project_progress[0][0]['OVD'] : 0; ?></li>
		                        <li class="green-bg tipText task_count <?php if(!isset($project_progress[0][0]['CMP']) || $project_progress[0][0]['CMP'] == 0){ echo 'zero_class'; }?>" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:5'; ?>" title="Completed"><?php echo (isset($project_progress[0][0]['CMP']) && !empty($project_progress[0][0]['CMP'])) ? $project_progress[0][0]['CMP'] : 0; ?></li>
	                        </ul>

						<?php $total_ts = number_format($totalTasks); ?>
							<div class="proginfotext"><?php echo $total_ts ?> <?php echo ( $totalTasks == 1) ? "Task" : "Tasks"; ?></div>
	                    </div>
					</div>
				</div>




<?php
	//pr($project_progress[0]['efforts']);

		$effort_bar_total_hours = (isset($project_progress[0]) && !empty($project_progress[0]['efforts']['total_hours'])) ? $project_progress[0]['efforts']['total_hours'] : 0;

		$effort_bar_completed_hours = (isset($project_progress[0]) && !empty($project_progress[0]['efforts']['blue_completed_hours'])) ? $project_progress[0]['efforts']['blue_completed_hours'] : 0;


		$effort_bar_green_remaining_hours = (isset($project_progress[0]) && !empty($project_progress[0]['efforts']['green_remaining_hours'])) ? $project_progress[0]['efforts']['green_remaining_hours'] : 0;

		$effort_bar_amber_remaining_hours = (isset($project_progress[0]) && !empty($project_progress[0]['efforts']['amber_remaining_hours'])) ? $project_progress[0]['efforts']['amber_remaining_hours'] : 0;

		$effort_bar_red_remaining_hours = (isset($project_progress[0]) && !empty($project_progress[0]['efforts']['red_remaining_hours'])) ? $project_progress[0]['efforts']['red_remaining_hours'] : 0;

		$effort_bar_none_remaining_hours = (isset($project_progress[0]) && !empty($project_progress[0]['efforts']['none_remaining_hours'])) ? $project_progress[0]['efforts']['none_remaining_hours'] : 0;



		if($effort_bar_total_hours > 0){

        $effort_bar_top_percentage = round($effort_bar_completed_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_blue_percentage = round($effort_bar_completed_hours /   ($effort_bar_total_hours ) * 100) ;



        $effort_bar_red_percentage = round($effort_bar_red_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_green_percentage = round($effort_bar_green_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_none_percentage = round($effort_bar_none_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_amber_percentage = round($effort_bar_amber_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

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

        $effort_changed_hours = (isset($project_progress[0] ) && !empty($project_progress[0]['efforts']['change_hours'])) ? $project_progress[0]['efforts']['change_hours'] : 0;

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

        }else if($effort_changed_hours == 0  && !empty($project_progress[0]['efforts']['total_hours'])){
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



				<!-- COSTS --><?php  //pr($cost_spend_used); ?>
	            <div class="progress-col progress-cost-sec">
					<div class="progress-col-heading">
						<span class="prog-h open-cost"><a href="#">Costs <i class="arrow-down"></i></a></span>
						<?php if(!empty($estimatedcost) && !empty($spendcost)){ ?>
						<span class="percent-text" title="Percentage Actual of Budget"><?php echo ceil($cost_spend_used); ?>%</span>
						<?php } ?>
					</div>
					<div class="progress-col-cont">

						<?php
							if(round($projectbudget) > 0){
								$budget_used = ($budget_used < $force_cost_percentage) ? $force_cost_percentage : $budget_used;
							} ?>

						<div class="cost-bar cost-tooltip" title="Budget (<?php echo $projectCurrencyName ?>): <?php echo number_format($projectbudget, 2, '.', ','); ?>">
							<span class="blue" style="width: <?php echo round($budget_used) ?>%"></span>
						</div>
	                    <div class="cost-bar cost-tooltip" title="Actual (<?php echo $projectCurrencyName ?>): <?php echo number_format($spendcost, 2, '.', ','); ?>">


							<?php
							//pr($spend_used);

							if(round($spendcost) > 0){
									$spend_used = ($spend_used < $force_cost_percentage) ? $force_cost_percentage : $spend_used;
							} ?>
							<span class="<?php if($spendcost <= $projectbudget && $spendcost > $estimatedcost) { ?>yellow<?php }elseif($spendcost > $projectbudget){ ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo round($spend_used); ?>%"></span>
						</div>
						<div class="proginfotext"><?php echo $cost_status_text; ?></div>
					</div>
				</div>
				<!-- RISK -->
	            <div class="progress-col work-column">
	                <div class="progresscol1">
					<div class="progress-col-heading">
						<span class="prog-h open-risk-tab"><a href="#<?php //echo Router::Url( array( 'controller' => 'risks', 'action' => 'risk_map', $project_id, 'admin' => FALSE ), TRUE ); ?>">Risks <i class="arrow-down"></i></a></span>
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
						<span class="percent-text tipText" title="Risks in Project"><?php echo $totalRisk; ?></span>
					</div>
					<div class="progress-col-cont">
							<ul class="workcount">
		                        <li class="darkred tipText <?php if(!isset($sign_high_risk) || $sign_high_risk == 0){ echo 'zero_class'; }?>" title="High"><?php echo $sign_high_risk ?></li>
		                        <li class="red tipText <?php if(!isset($sign_severe_risk) || $sign_severe_risk == 0){ echo 'zero_class'; }?>" title="Severe"><?php echo $sign_severe_risk ?></li>
	                        </ul>
						<div class="proginfotext">Signed Off </div>
	                    </div>
					</div>
				</div>

				<!-- COMPETENCIES  -->
				<?php
				$skill_match_percent = (!empty($project_ssd[0][0]['skill_match_percent'])) ? $project_ssd[0][0]['skill_match_percent'] : 0;
				$subject_match_percent = (!empty($project_ssd[0][0]['subject_match_percent'])) ? $project_ssd[0][0]['subject_match_percent'] : 0;
				$domain_match_percent = (!empty($project_ssd[0][0]['domain_match_percent'])) ? $project_ssd[0][0]['domain_match_percent'] : 0;

				$skill_team_has = (!empty($project_ssd[0][0]['skill_total'])) ? $project_ssd[0][0]['skill_total'] : 0;
				$subject_team_has = (!empty($project_ssd[0][0]['sub_total'])) ? $project_ssd[0][0]['sub_total'] : 0;
				$domain_team_has = (!empty($project_ssd[0][0]['domain_total'])) ? $project_ssd[0][0]['domain_total'] : 0;  ?>
	            <div class="progress-col competencies-proj-bar">

					<div class="progress-col-heading">
						<span class="prog-h"><a href="javascript:void(0);" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="true">COMPETENCIES  <i class="arrow-down"></i></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Router::Url( array( 'controller' => 'subdomains', 'action' => 'knowledge_analytics', 'skill', 'project' => $project_id, 'admin' => FALSE ), TRUE ); ?>"><span class="comt-dp-icon"><i class="compet-all-icon kablack"></i></span>  Skills Profile</a></li>
							<li><a href="<?php echo Router::Url( array( 'controller' => 'subdomains', 'action' => 'knowledge_analytics', 'subject', 'project' => $project_id, 'admin' => FALSE ), TRUE ); ?>"><span class="comt-dp-icon"><i class="compet-all-icon kablack"></i></span>  Subjects Profile</a></li>
							<li><a href="<?php echo Router::Url( array( 'controller' => 'subdomains', 'action' => 'knowledge_analytics', 'domain', 'project' => $project_id, 'admin' => FALSE ), TRUE ); ?>"><span class="comt-dp-icon"><i class="compet-all-icon kablack"></i></span>  Domains Profile</a></li>
						</ul>

						</span>
						<?php if(!empty($avg_competency)){ ?>
							<span class="percent-text tipText" title="Percentage of Competencies"><?php echo $avg_competency; ?>%</span>
						<?php } ?>

					</div>
					<div class="progress-col-cont">
						<div class="compet-proj-bar-col">
							<div class="schedule-bar" data-original-title="" title="">
								<span class="blue barTip bar-border tipText" title="Team has <?php echo $skill_team_has; ?> of <?php echo $total_skills; ?> Project Skills (<?php echo $skill_match_percent; ?>%)" style="width:<?php echo $skill_match_percent; ?>%" data-original-title=""></span>
							</div>
						<div class="proginfotext tipText" title="<?php if(!empty($total_skills)){ ?><?php echo "Team has $skill_team_has of $total_skills Project Skills"; if($skill_team_has > 0){  echo " (".$skill_match_percent."%)";  }   } else{ echo "No Project Skills"; } ?>"><?php echo (!empty($total_skills)) ? $skill_team_has : 'None'; ?></div>
						</div>
						<div class="compet-proj-bar-col">
							<div class="schedule-bar" data-original-title="" title="">
								<span class="red2 barTip bar-border tipText" title="Team has <?php echo $subject_team_has; ?> of <?php echo $total_subjects; ?> Project Subjects (<?php echo $subject_match_percent; ?>%)" style="width: <?php echo $subject_match_percent; ?>%" data-original-title=""></span>
							</div>
							<div class="proginfotext tipText"  title="<?php if(!empty($total_subjects)){ ?><?php echo "Team has $subject_team_has of $total_subjects Project Subjects"; if($subject_team_has > 0){  echo " (".$subject_match_percent."%)";  }  } else{ echo "No Project  Subjects"; } ?>"><?php echo (!empty($total_subjects)) ? $subject_team_has : 'None'; ?></div>
						</div>
						<div class="compet-proj-bar-col">
							<div class="schedule-bar" data-original-title="" title="">
								<span class="green-bg barTip bar-border tipText" title="Team has <?php echo $domain_team_has; ?> of <?php echo $total_domains; ?> Project Domains (<?php echo $domain_match_percent; ?>%)" style="width:<?php echo $domain_match_percent; ?>%" data-original-title=""></span>
							</div>
							<div class="proginfotext tipText"  title="<?php if(!empty($total_domains)){ ?><?php echo "Team has $domain_team_has of $total_domains Project Domains"; if($domain_team_has > 0){  echo " (".$domain_match_percent."%)";  } } else{ echo "No Project Domains"; } ?>"><?php echo (!empty($total_domains)) ? $domain_team_has : 'None'; ?></div>
						</div>
	                    </div>

				</div>


				<!-- REWARDS -->
				<?php if(is_project_rewarded($project_id)){ ?>
	            <div class="progress-col">
					<div class="progress-col-heading">
						<span class="prog-h"><a href="<?php echo Router::Url( array( 'controller' => 'rewards', 'action' => 'index', 'admin' => FALSE ), TRUE ); ?>">Rewards <i class="arrow-down"></i></a></span>
						<span class="percent-text reward-distributed" title="Percentage Distributed from Allocated"><?php echo ceil($reward_percent); ?>%</span>
					</div>
					<div class="progress-col-cont">
						<div class="schedule-bar reward-distributed-from" title="Distributed: <?php echo number_format($total_amount, 0, '', ','); ?> of <?php echo number_format($total_allocations, 0, '', ','); ?>">
							<span class="blue bar-border" style="width: <?php echo round($reward_percent); ?>%"></span>
							<span class="bar-border" style="width: <?php echo round(100-$reward_percent); ?>%"></span>
						</div>
						<div class="proginfotext">Distributed</div>
					</div>
				</div>
				<?php } ?>
			<?php }// if user is owner ?>


<script type="text/javascript" >
	$(function(){
		$('.task_count').on('click', function(event) {
			event.preventDefault();
			var url = $(this).data('url');
			location.href = url;
		});
		$('.reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip, .percent-text').tooltip({
			'template': '<div class="tooltip default-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
			'placement': 'top',
			'container': 'body'
		})
		$('.cost-tooltip').tooltip({
			'placement': 'top',
			'container': 'body',
			'html': true
		})

		$('#model_bx').on('hidden.bs.modal', function(e){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		})
		$('.open-team-tab').on('click', function(event) {
            event.preventDefault();
            $('#summary_tabs a[href="#tab_team"]').tab('show');
        });
	})

</script>
<style>
.workcount.confcounters li{ cursor: default;}
.tbdr{ border-right: 1px solid #bcbcbc !important;}
</style>
