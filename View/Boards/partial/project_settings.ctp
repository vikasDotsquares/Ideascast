<?php
// $project_type = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id'));
$costURL = SITEURL.'costs/index/'.$project_type.':'.$project_id;
$ganttURL = SITEURL.'users/event_gantt/'.$project_type.':'.$project_id;

$projectRole = projectRole($project_id, $this->Session->read('Auth.User.id'));
// pr($projectRole, 1);
// owner/sharer
$ProjectOwnersTotal = $this->Permission->projectOwners($project_id, 1);
$ProjectSharersTotal = $this->Permission->projectSharer($project_id, 1);



if($projectRole == 'Creator' || $projectRole == 'Owner' || $projectRole == 'Group Owner'){
	// Cost
	$totalestimatedcost =  $this->ViewModel->project_element_ids($project_id, 'estimated_cost');
	$totalspendcost = $this->ViewModel->project_element_ids($project_id, 'spend_cost');
	$projectCurrencyName = $this->Common->getCurrencySymbolName($project_id);
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
	}

	$estimatedcost = ( isset($totalestimatedcost) && $totalestimatedcost > 0 ) ? $totalestimatedcost : 0;
	$spendcost = ( isset($totalspendcost) && $totalspendcost > 0 ) ? $totalspendcost : 0;
	$projectbudget = project_budget($project_id);
	$projectbudget = ( !empty($projectbudget)) ? $projectbudget : 0;
	$max_budget = max($estimatedcost, $spendcost, $projectbudget);
	$budget_used = ($max_budget > 0) ? ( ( $projectbudget / $max_budget) * 100 ) : 0;
	$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
	$spend_used = ($max_budget > 0) ? ( ( $spendcost / $max_budget) * 100 ) : 0;


	$cost_status_text = $this->Permission->project_cost_status_text( $projectbudget, $estimatedcost, $spendcost);

	// risk counters
	$user_project_risks = user_project_risks($project_id, $this->Session->read('Auth.User.id'));
	// pr($user_project_risks, 1);

	$project_risks = ( isset($user_project_risks) && !empty($user_project_risks) )? count($user_project_risks) : 0;

	$pending_high_risk = project_pending_risks($user_project_risks, $project_id, 'high');
	$pending_severe_risk = project_pending_risks($user_project_risks, $project_id, 'severe');
	$sign_high_risk = project_signedoff_risks($user_project_risks, $project_id, 'high');
	$sign_severe_risk = project_signedoff_risks($user_project_risks, $project_id, 'severe');

	// Reward counts
	if(isset($projects['UserProject']['is_rewards']) && !empty($projects['UserProject']['is_rewards'])){
		$project_reward_setting = project_reward_setting($project_id, 1);
		$total_allocations = ($project_reward_setting) ? $project_reward_setting['RewardSetting']['ov_allocation'] :  0;
		### allocated to user ###
		$project_rewards = project_reward_assignments($project_id, $this->Session->read('Auth.User.id'));
		$total_allocated = 0;
		$total_amount = 0;
		if($project_rewards) {
			foreach ($project_rewards as $key => $value) {
				$data = $value['RewardAssignment'];
				$amount = $data['allocated_rewards'];
				$total_allocated += $amount;
			}
		}
		$by_acclerate = project_accelerated_points($project_id, $this->Session->read('Auth.User.id'));
		$total_amount = $total_allocated + $by_acclerate;

		$reward_percent = ($total_allocations > 0) ? (($total_amount/$total_allocations)*100) : 0;
	}
}





################################################################################################################
$ws_exists = true;
$ws_count = $prj_count = 0;
if (isset($menu_project_id) && !empty($menu_project_id)) {
    // echo $menu_project_id;
    $prj_count = $this->ViewModel->user_project_count();
    $ws_count = $this->ViewModel->project_workspace_count($menu_project_id);

    if (empty($ws_count)) {
        $ws_exists = false;
    }
}

$owner = $this->Common->ProjectOwner($project_id,$this->Session->read('Auth.User.id'));

$participants = participants($project_id,$owner['UserProject']['user_id']);

$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);

$participantsGpOwner = participants_group_owner($project_id );

$participantsGpSharer = participants_group_sharer($project_id );



$participants1 = (isset($participants) && !empty($participants)) ? array_filter($participants) :  array();
$participants_owners1 = (isset($participants_owners) && !empty($participants_owners)) ? array_filter($participants_owners) : array();
$participantsGpOwner1 = (isset($participantsGpOwner) && !empty($participantsGpOwner)) ? array_filter($participantsGpOwner) : array();
$participantsGpSharer1 = (isset($participantsGpSharer) && !empty($participantsGpSharer)) ? array_filter($participantsGpSharer) : array();


$participants = (isset($participants1) && !empty($participants1)) ? count($participants1) :  0;
$participants_owners = (isset($participants_owners1) && !empty($participants_owners1)) ? count($participants_owners1) :  0;
$participantsGpOwner = (isset($participantsGpOwner1) && !empty($participantsGpOwner1)) ? count($participantsGpOwner1) :  0;
$participantsGpSharer = (isset($participantsGpSharer1) && !empty($participantsGpSharer1)) ? count($participantsGpSharer1) :  0;

$total = 0;
$total = $participants + $participants_owners + $participantsGpOwner + $participantsGpSharer;



?>
<section class="content-header clearfix nopadding">
    <div class="header-progressbar">
    		<div class="progressbar-sec">
			<div class="progress-col">
				<div class="progress-col-heading">
					<span class="prog-h"><a data-target="#modal_people" data-toggle="modal" href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'project_people', $project_id, 'admin' => FALSE ), TRUE ); ?>">Team <i class="arrow-down"></i></a></span>
				</div>
				<div class="progress-col-cont">
					<ul class="progress-team-list">
						<li class="active"><?php echo (!empty($ProjectOwnersTotal[0][0]['owners'])) ? (($ProjectOwnersTotal[0][0]['owners'] == 1) ? $ProjectOwnersTotal[0][0]['owners'].' Owner' : $ProjectOwnersTotal[0][0]['owners'].' Owners') : 'No Owners'; ?></li>
						<li><?php echo (!empty($ProjectSharersTotal[0][0]['sharers'])) ? (($ProjectSharersTotal[0][0]['sharers'] == 1) ? $ProjectSharersTotal[0][0]['sharers'].' Sharer' : $ProjectSharersTotal[0][0]['sharers'].' Sharers') : 'No Sharers'; ?></li>
					</ul>
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

			<?php if($projectRole == 'Creator' || $projectRole == 'Owner' || $projectRole == 'Group Owner'){ ?>
				<div class="progress-col work-column wsp-task-counters">
	            	<?php echo $this->element('../Boards/partial/counters', array('project_id' => $project_id)); ?>
	            </div>

				<!-- COSTS -->
	            <div class="progress-col progress-cost-sec">
					<div class="progress-col-heading">
						<span class="prog-h"><a href="<?php echo $costURL; ?>">Cost <i class="arrow-down"></i></a></span>
					</div>
					<div class="progress-col-cont">
						<div class="cost-bar cost-tooltip" title="Budget: <?php echo $projectCurrencysymbol.number_format($projectbudget, 2, '.', ','); ?>">
							<span class="blue" style="width: <?php echo round($budget_used) ?>%"></span>
						</div>
	                    <div class="cost-bar cost-tooltip" title="Estimate: <?php echo $projectCurrencysymbol.number_format($estimatedcost, 2, '.', ','); ?>">
							<span class="<?php if($estimatedcost > $projectbudget) { ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo round($estimate_used); ?>%"></span>
						</div>
	                    <div class="cost-bar cost-tooltip" title="Actual: <?php echo $projectCurrencysymbol.number_format($spendcost, 2, '.', ','); ?>">
							<span class="<?php if($spendcost <= $projectbudget && $spendcost > $estimatedcost) { ?>yellow<?php }elseif($spendcost > $projectbudget){ ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo round($spend_used); ?>%"></span>
						</div>
						<div class="proginfotext"><?php echo $cost_status_text; ?></div>
					</div>
				</div>
				<!-- RISK -->
	            <div class="progress-col work-column">
	                <div class="progresscol1">
					<div class="progress-col-heading">
						<span class="prog-h"><a href="<?php echo Router::Url( array( 'controller' => 'risks', 'action' => 'risk_map', $project_id, 'admin' => FALSE ), TRUE ); ?>">Risks <i class="arrow-down"></i></a></span>
					</div>
					<div class="progress-col-cont">
							<ul class="workcount">
		                        <li class="darkred tipText" title="High"><?php echo $pending_high_risk; ?></li>
		                        <li class="red tipText" title="Severe"><?php echo $pending_severe_risk; ?></li>
	                        </ul>
						<div class="proginfotext">Pending </div>
	                    </div>
					</div>
	                <div class="progresscol2">
					<div class="progress-col-heading">
						<span class="prog-h">&nbsp;</span>
						<span class="percent-text tipText" title="Risks in Project"><?php echo $project_risks; ?></span>
					</div>
					<div class="progress-col-cont">
							<ul class="workcount">
		                        <li class="darkred tipText" title="High"><?php echo $sign_high_risk ?></li>
		                        <li class="red tipText" title="Severe"><?php echo $sign_severe_risk ?></li>
	                        </ul>
						<div class="proginfotext">Signed Off </div>
	                    </div>
					</div>
				</div>
				<!-- REWARDS -->
				<?php if(isset($projects['UserProject']['is_rewards']) && !empty($projects['UserProject']['is_rewards'])){ ?>
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
		</div>




	<?php /*?><div class="pull-left project-detail">
		<a class="bg-blue nomargin sb_blog" data-target="#modal_people" data-toggle="modal" href="<?php echo SITEURL ?>projects/project_people/<?php echo $project_id; ?>">People on Project <?php echo $total; ?></a>
		<span class="bg-blakish sb_blog" style="cursor:default;">Start: <?php echo ( isset($projects['Project']['start_date']) && !empty($projects['Project']['start_date'])) ? _displayDate($projects['Project']['start_date'], 'd M, Y') : 'N/A';  ?></span>
		<span class="bg-black nomargin-left sb_blog" style="cursor:default;">End: <?php echo ( isset($projects['Project']['end_date']) && !empty($projects['Project']['start_date'])) ? _displayDate($projects['Project']['end_date'], 'd M, Y') : 'N/A';  ?></span>
	</div><?php */?>

	<?php $toc = 'bg-green';
		$total_daysN = daysLeft(date('Y-m-d',strtotime($projects['Project']['start_date'])), date('Y-m-d',strtotime($projects['Project']['end_date'])));
			if(date('Y-m-d') > date('Y-m-d',strtotime($projects['Project']['start_date']))){
				 $total_complete_days = daysLeft($projects['Project']['start_date'], date('Y-m-d 12:00:00'));
			}else{
				 $total_complete_days = daysLeft($projects['Project']['start_date'], date('Y-m-d 12:00:00'));
			}

			if(date('Y-m-d')  <= date('Y-m-d',strtotime($projects['Project']['end_date'])) && date('Y-m-d')  >= date('Y-m-d',strtotime($projects['Project']['start_date']))){
				 $total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $projects['Project']['end_date'], 1);
			}else{
				 $total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $projects['Project']['end_date'])  ;
			}

			if(!empty($total_daysN) && !empty($total_complete_days)) {
				$dataP =   (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)) > 0 ?   (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)): 0;
				if($dataP > 100){
				$dataP = 100;
				$toc = 'bg-red';
				}

			}else{
				$dataP = 0;
			}

			if(isset($projects['Project']['sign_off']) && !empty($projects['Project']['sign_off'])){
				$dataP = 100;
				$toc = 'bg-red';
			}

	if (isset($project_id) && !empty($project_id)) {

    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

    $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));



$prj_disabled = '';
$prj_tooltip = '';
$prj_cursor =  '';
if( isset($projects['Project']['sign_off']) && !empty($projects['Project']['sign_off']) && $projects['Project']['sign_off'] == 1 ){
	$prj_disabled = 'disable';
	$prj_tooltip = "Project Is Signed Off";
	$prj_cursor = " cursor:default !important; ";
}

    if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {
		?>
	<?php /*?><div class="project-elp-ipad">
		<div class="border bg-white ideacast-project-progress"><span class="pull-left">Project Elapsed</span>
		<div class="progress tipText" title="Days Remaining: <?php echo $total_remain_days; ?>">
			<div style="width:<?php echo $dataP."%"; ?>" aria-valuemax="100" aria-valuemin="50" aria-valuenow="50" role="progressbar" class="progress-bar <?php echo $toc ?>">

			</div>
		</div><span class="pull-left"><?php echo $dataP."%"; ?></span>
		</div>

	</div><?php */?>
	<?php  } } ?>

    <div class=" right-side-progress-bar">

		<?php
			if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {
			?>
			<!--<a class="btn btn-sm btn-success tipText" title="Project Summary" href="<?php echo SITEURL ?>projects/index/<?php echo $project_id;?>" style="padding: 5px 10px;"><i class="fa fa-briefcase"></i></a>-->

			<a data-toggle="modal" class="btn btn-sm btn-success tipText" title="Project Sharing" href="<?php echo SITEURL ?>projects/quick_share/<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-user-plus"></i></a>
			<?php
			}
		?>

        <!-- Project Options -->
<?php
if (isset($project_id) && !empty($project_id)) {

    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

    $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));


    if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) || (isset($p_permission['ProjectPermission']['permit_edit']) && $p_permission['ProjectPermission']['permit_edit'] == 1 )) {

        ?>
                <div class="btn-group action">

                <?php
                if ($ws_exists === true) {
                    if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {
                        ?>

				<?php /*?><a data-toggle="modal" data-modal-width="600" class="btn btn-sm btn-success tipText" title="Project Info" href="<?php echo SITEURL ?>projects/project_description/<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-eye"></i></a><?php */?>
            <?php }
        }
        ?>

                    <?php // SHOW PROJECT EDIT LINK IF PROJECT ID VALUES ARE EXISTS



                    if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) || (isset($p_permission['ProjectPermission']['permit_edit']) && isset($p_permission['ProjectPermission']['permit_edit']) == 1)) {
                        if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )){
                        $user_project = $this->Common->get_project($project_id);

                        }
                        // pr($user_project);
						if( isset($prj_disabled) && !empty($prj_disabled) ){
						?>
						<a class="btn btn-sm btn-success tipText <?php echo $prj_disabled;?>" rel="tooltip"  title="<?php echo tipText($prj_tooltip) ?>" style="<?php echo $prj_cursor;?>"><i class="fa fa-fw fa-pencil"></i> </a>
						<?php } else {?>
                        <a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'manage_project', $project_id, 'admin' => FALSE), TRUE); ?>" class="btn btn-sm btn-success tipText" rel="tooltip"  title="<?php echo tipText('Update Project Details') ?>"><i class="fa fa-fw fa-pencil"></i> </a>
						<?php } ?>

                    <?php } ?>

                    <?php if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {

                       // pr($user_project);
                        $message = null;
                        $startdate = isset($user_project['Project']['start_date']) && !empty($user_project['Project']['start_date']) ? date("Y-m-d",strtotime($user_project['Project']['start_date'])) : '';
                        $enddate = isset($user_project['Project']['end_date']) && !empty($user_project['Project']['end_date']) ? date("Y-m-d",strtotime($user_project['Project']['end_date'])) : '';
                        $curdate = date("Y-m-d");


                        $class = '';
                        $url = SITEURL.'templates/create_workspace/'.$project_id;
						//echo $startdate." ".$curdate;
						//echo $enddate." ".$curdate;
                        if(isset($user_project['Project']['sign_off']) && $user_project['Project']['sign_off'] == 1){
                            $message = 'Project has been Sign off.';
                            $url ='';
							$class = 'workspace disable';

                        }else if(!empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
                            $message = 'Please add a schedule to this Project first.';
                            $url ='';$class = 'workspace disable';
                        }else if(!empty($enddate) && $enddate < $curdate){
                            $message = 'Project date has expired.';
                            $url ='';$class = 'workspace disable';
                        }else if(isset ($startdate) && empty($startdate)){
                            $message = 'Please add a schedule to this Project first.';
                            $url ='';$class = 'workspace disable';
                        }else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
                            $message = 'You are not allowed to add workspace because project hasn\'t started yet.';
                            $url ='';$class = 'workspace disable';
                        }

						//$prj_tooltip   $prj_cursor
                        if( isset($prj_disabled) && !empty($prj_disabled) ){
						?>
                        <a  class="btn btn-sm btn-success tipText <?php echo $class;?> <?php echo $prj_disabled;?>" title="Project Is Signed Off" rel="tooltip"  ><i class="fa fa-fw fa-plus"></i> </a>
						<?php } else { ?>
                        <a  data-title="<?php echo $message;?>" class="btn btn-sm btn-success tipText <?php echo $class;?> <?php echo $prj_disabled;?>" href="<?php echo $url; ?>" title="Add workspace" rel="tooltip"  ><i class="fa fa-fw fa-plus"></i> </a>
						<?php } ?>

<!-- <a  data-title="<?php echo $message;?>" class="btn btn-sm btn-success tipText workspace" href="<?php echo SITEURL ?>templates/create_workspace/<?php echo $project_id; ?>" title="<?php tipText('Add workspace'); ?>" rel="tooltip"  ><i class="fa fa-fw fa-columns"></i> </a>-->
                    <?php } ?>





					<?php if ( (isset($project_id) && !empty($project_id)))
                    $dataOwner = $this->ViewModel->projectPermitType($project_id  , $this->Session->read('Auth.User.id') );
                    if ( (isset($project_id) && !empty($project_id))  && $dataOwner == 1 ) {

						$project_workspace_details = $this->ViewModel->getProjectWorkspaces( $project_id, 1 );
						if (isset($project_workspace_details) && !empty($project_workspace_details)) {
						?>
						<?php /*?><a data-toggle="modal" data-modal-width="600" class="btn btn-sm btn-success tipText" title="<?php tipText('Generate Report'); ?>" href="<?php echo SITEURL ?>export_datas/index/project_id:<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip"  style="padding: 5px 5px 5px 7px !important; vertical-align: top; width: 32px;" > <i class="icon-file-export"></i> </a><?php */?>

					<?php }
					}	?>
                    <?php
                    /* if ($ws_exists === true) {
                        if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {
                            ?>
                            <a data-toggle="modal" data-modal-width="600" class="btn btn-sm btn-success tipText" title="<?php tipText('projects-index-config-ws'); ?>" href="<?php echo SITEURL ?>projects/workspaceConfigPopup/<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-fw fa-wrench"></i></a>
                        <?php }
                    } */
                    ?>

                </div>

            <?php  } ?>
            <?php   if( $ws_exists === true ) { ?>
              <?php  if(((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1)  ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )	) { ?>
              <div class="btn-group action" style="display:none;">
              <a data-toggle="dropdown" class="btn btn-sm btn-success dropdown-toggle tipText" title="Export Options" type="button" href="javascript:void(0);">
              DocBuilder <span class="caret"></span>
              </a>

              <ul class="dropdown-menu">
              <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL?>projects/exportwsp/<?php echo $project_id;?>/doc" data-target="#modal_medium" rel="tooltip" ><i class="halflings-icon user"></i> Word</a></li>

              <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL?>projects/exportwsp/<?php echo $project_id;?>/ppt" data-target="#modal_medium" rel="tooltip" ><i class="halflings-icon user"></i> Power Point</a></li>

              <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL?>projects/exportwsp/<?php echo $project_id;?>" data-target="#modal_medium" rel="tooltip" ><i class="halflings-icon user"></i> PDF</a></li>
              </ul>

              </div>
              <?php } }  ?>

<div class="btn-group action">
            <?php
            if (isset($ws_exists) && $ws_exists != false) {
//pr($projects);
					$projid = 0;
					if (isset($project_id) && !empty($project_id)) {
						$projid = $project_id;
					} else if (isset($_sidebarProjectId) && !empty($_sidebarProjectId)) {
						$projid = $_sidebarProjectId;
					}
					//********************* More Button ************************
					echo $this->element('more_button', array('project_id' => $projid, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'boards' ));
				} ?>
			</div>
		<?php } ?>
    </div>

  </div>
</section>
<style type="text/css">
	.tooltip.default-tooltip {
    text-transform: none;
}
.taskcounters li {
	cursor: pointer;
}
</style>
<script type="text/javascript" >
	$(document).on("click", ".workspace", function(e) {
		e.preventDefault()
		var message = $(this).attr("data-title");
		$(".modal-body").html('<p>'+message+'</p>');
		$.model = $("#modal-alert")
		$.model.modal('show');

	});
	$(function(){
		$('.task_count').on('click', function(event) {
			event.preventDefault();
			var url = $(this).data('url');
			location.href = url;
			/* Act on the event */
		});
		$('.reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip').tooltip({
			'template': '<div class="tooltip default-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
			'placement': 'top',
			'container': 'body'
		})
		$('.cost-tooltip').tooltip({
			'placement': 'top',
			'container': 'body',
			'html': true
		})
	})
</script>
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
.icon-file-export {
   height: 17px;
	width: 17px;
	vertical-align: top;
	/* margin-top: 3px */;
}
</style>