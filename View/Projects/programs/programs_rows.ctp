<?php
$current_user_id = $this->Session->read('Auth.User.id');
// $dd = $this->Scratch->program_team(6);
// pr($dd);

if(isset($programs_list) && !empty($programs_list)) {
	foreach ($programs_list as $key => $value) {
		$progs = $value['progs'];
		$team = $value['team'];
		// pr($team);
		$others = $value[0];
		$projects = $value['prjs'];
		$dates = $value['pdets'];
		// pr($value['rag_status']);
		$rag_status = (!empty($value['rag_status']['rag_totals'])) ? json_decode($value['rag_status']['rag_totals'], true)[0] : [];
		if(!isset($rag_status['red'])){
			$rag_status = [
				'yellow' => 0,
			    'red' => 0,
			    'green' => 0
			];
		}
		$ws = (!empty($value['ws']['prws_counts'])) ? json_decode($value['ws']['prws_counts'], true)[0] : [];
		$task = (!empty($value['task']['prts_counts'])) ? json_decode($value['task']['prts_counts'], true)[0] : [];
		if(!isset($ws['non'])){
			$ws = [
				'cmp' => 0,
			    'non' => 0,
			    'ovd' => 0,
			    'pnd' => 0,
			    'prg' => 0,
			    'total' => 0
			];
		}
		if(!isset($task['non'])){
			$task = [
				'cmp' => 0,
			    'non' => 0,
			    'ovd' => 0,
			    'pnd' => 0,
			    'prg' => 0,
			    'total' => 0
			];
		}
		$efforts = $value['efforts'];
		$cost_statuses = (!empty($value['cost_statuses']['cost_totals'])) ? json_decode($value['cost_statuses']['cost_totals'], true)[0] : [];
		if(!isset($cost_statuses['cost_not_set'])){
			$cost_statuses = [
				'cost_not_set' => 0,
			    'cost_incurred' => 0,
			    'cost_on_budget' => 0,
			    'cost_budget_set' => 0,
			    'cost_over_budget' => 0
			];
		}
		$cost_total = (!empty($value['cost_total']['cost_totals'])) ? json_decode($value['cost_total']['cost_totals'], true)[0] : [];
		if(!isset($cost_total['sp_sum'])){
			$cost_total = [
				'sp_sum' => 0,
			    'est_sum' => 0
			];
		}

		// COST PROGRESS BAR
		$estimatedcost = $cost_total['est_sum'];
		$spendcost = $cost_total['sp_sum'];
		$force_cost_percentage = 2;

		$projectbudget = ( !empty($dates['pbudget'])) ? $dates['pbudget'] : 0;
		$max_budget = max($estimatedcost, $spendcost, $projectbudget);
		$budget_used = ($max_budget > 0) ? ( ( $projectbudget / $max_budget) * 100 ) : 0;
		$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
		$spend_used = ($max_budget > 0) ? ( ( $spendcost / $max_budget) * 100 ) : 0;

		if(round($projectbudget) > 0){
			$budget_used = ($budget_used < $force_cost_percentage) ? $force_cost_percentage : $budget_used;
		}
		if(round($spendcost) > 0){
			$spend_used = ($spend_used < $force_cost_percentage) ? $force_cost_percentage : $spend_used;
		}

		$currencySign = $cost_status_text = $budget_tip = $actual_tip = '';
		// if no project
		// pr($projects['total_projects']);
		if(empty($projects['total_projects'])){
			$currencySign = '';
			$cost_status_text = 'Not Set';
			$budget_tip = "";
			$actual_tip = "";
		}
		else if(!empty($dates['csign'])){
			$signs = explode(",", $dates['csign']);
			// if all currencies in all projects are same
			if(count($signs) == 1){
				$currencySign = '';
				$cost_status_text = 'Overall Costs';
				$budget_tip = "Budget (".$dates['csign']."): ".number_format($projectbudget, 2, '.', ',');
				$actual_tip = "Actual (".$dates['csign']."): ". number_format($spendcost, 2, '.', ',');
			}
			// if all currencies in all projects are different
			else{
				$currencySign = "";
				$cost_status_text = 'Not Available';
				$budget_tip = "";
				$actual_tip = "";
				$budget_used = "";
				$spend_used = "";
			}
		}


		$srisk = $value['srisk']['severe_risk_total'];
		$hrisk = $value['hrisk']['high_risk_total'];
		//$risk_totals = $value['risk_totals'];
		$conf_levels = (!empty($value['conf_levels']['conf_level'])) ? json_decode($value['conf_levels']['conf_level'], true)[0] : [];

		if(!isset($conf_levels['level'])){
			$conf_levels = [
				'level' => 0,
			    'level_count' => 0,
			    'confidence_arrow' => '',
			    'confidence_class' => '',
			    'confidence_level' => '',
			    'confidence_order' => 0,
			    'confidence_order_asc' => 0,
			];
		}
		$risk_statuses = $value['risk_statuses'];

		$status_flag = '';
		$status_tip = '';
		if(isset($others['status']) && !empty($others['status'])){
			if($others['status'] == 1){
				$status_flag = 'overdue';
				$status_tip = 'Overdue';
			}
			else if($others['status'] == 2){
				$status_flag = 'progressing';
				$status_tip = 'In Progress';
			}
			else if($others['status'] == 3){
				$status_flag = 'not_started';
				$status_tip = 'Not Started';
			}
			else if($others['status'] == 4){
				$status_flag = 'completed';
				$status_tip = 'Completed';
			}
			else if($others['status'] == 5){
				$status_flag = 'not_set';
				$status_tip = 'Not Set';
			}
		}

		// pr($rag_status);
?>
	<?php
		$effort_bar_total_hours = (isset($efforts ) && !empty($efforts['total_hours'])) ? $efforts ['total_hours'] : 0;

		$effort_bar_completed_hours = (isset($efforts ) && !empty($efforts ['blue_completed_hours'])) ? $efforts ['blue_completed_hours'] : 0;


		$effort_bar_green_remaining_hours = (isset($efforts) && !empty($efforts['green_remaining_hours'])) ? $efforts['green_remaining_hours'] : 0;

		$effort_bar_amber_remaining_hours = (isset($efforts) && !empty($efforts['amber_remaining_hours'])) ? $efforts['amber_remaining_hours'] : 0;

		$effort_bar_red_remaining_hours = (isset($efforts) && !empty($efforts['red_remaining_hours'])) ? $efforts['red_remaining_hours'] : 0;

		$effort_bar_none_remaining_hours = (isset($efforts) && !empty($efforts['none_remaining_hours'])) ? $efforts['none_remaining_hours'] : 0;

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
		$remaining_none_color_tip = "Remaining: $effort_bar_none_remaining_hours Of $effort_bar_total_hours Hours No Schedule ($effort_bar_none_percentage%)";;
        $remaining_color_tip_blue = 'None';

        if(isset($effort_bar_blue_percentage) && !empty($effort_bar_blue_percentage)){
			$remaining_color_tip_blue = "Completed:  $effort_bar_completed_hours Of $effort_bar_total_hours Hours ($effort_bar_blue_percentage%)";
        }

        if($effort_bar_total_hours ==1){
            $effort_bar_total_hours_text = $effort_bar_total_hours.' Hr';
        }else{
            $effort_bar_total_hours_text = $effort_bar_total_hours.' Hrs';
        }

        $effort_changed_hours = (isset($efforts) && !empty($efforts['change_hours'])) ? $efforts['change_hours'] : 0;

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

        }else if($effort_changed_hours == 0  && !empty($efforts['total_hours'])){
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
<div class="pg-data-row" data-prog="<?php echo $progs['id']; ?>">
	<div class="prog-col prog-col-1">
		<div class="prog-project-details">
			<div class="prog-project-left programs-<?php echo $progs['color_code']; ?>">
				<i class="pcwhite18"></i>
			</div>
			<div class="prog-project-inner">
				<div class="prog-project-details-sec">
					<div class="prog-project-middle">
						<a class="prog-project-name" href="" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program"><?php echo htmlentities($progs['name'], ENT_QUOTES, "UTF-8"); ?></a>
						<span class="prog-project-date">
							<?php if( (isset($dates['stdate']) && !empty($dates['stdate'])) && (isset($dates['endate']) && !empty($dates['endate'])) ){ ?>
							<?php echo date('d M, Y', strtotime($dates['stdate'])); ?> → <?php echo date('d M, Y', strtotime($dates['endate'])); ?>
							<?php }else{
								echo 'No Schedule';
							} ?>
						</span>
					</div>
					<div class="prog-pss prog-fl-icon">
						<i class="flag <?php echo $status_flag; ?> tipText" title="<?php echo $status_tip; ?>"></i>
						<?php
						if(isset($conf_levels['level']) && !empty($conf_levels['level'])){
							$level_value = $conf_levels['level'] ;
							$level_count = $conf_levels['level_count'] ;
							if($level_value > 0){
								$level_value_current = $level_value.'%';
								if($level_count < 2){
									$level_value_tip = 'For '.$level_count.' Task';
								}else{
									$level_value_tip = 'For '.$level_count.' Tasks';
								}
								$level_value_tip_class = 'cost-tooltip';
							}else{
								$level_value_current = '';
								$level_value_tip = '';
								$level_value_tip_class = '';
							}
						?>
						<i class="level-ts cost-tooltip <?php echo $conf_levels['confidence_arrow']; ?>" title="<?php echo $conf_levels['confidence_level']; ?> Confidence Level <br /><?php echo $conf_levels['level']; ?>% <?php echo $level_value_tip; ?>"></i>
						<?php }else{ ?>
						<i class="level-ts notsetgrey tipText" title="" data-original-title="Confidence Level Not Set"></i>
						<?php } ?>
					</div>
				</div>
				<div class="prog-project-details-sec">
					<div class="prog-project-middle prog-pt0">
						<a class="prog-project-name-sub" ><?php echo htmlentities($progs['type'], ENT_QUOTES, "UTF-8"); ?></a>
						<span class="prog-project-date" <?php if(!empty($value['stake']['stakeholders'])){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" style="cursor: pointer;" <?php } ?>>
							<?php echo  ($value['stake']['stakeholders'] == 1) ? '1 Stakeholder' : ((empty($value['stake']['stakeholders'])) ? '0 Stakeholders' : $value['stake']['stakeholders'].' Stakeholders'); ?>
						</span>
					</div>
					<div class="prog-pss prog-fl-icon">
						<?php if(!empty($value['stake']['stakeholders'])){ ?>
							<i class="blank-icon"></i>
							<i class="nudgeblack tipText" title="Nudge Program Team" data-remote="<?php echo Router::Url( array( 'controller' => 'boards', 'action' => 'send_nudge_board', 'program' => $progs['id'], 'type' => 'program_stakeholders', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_nudge"></i>
							<?php /* ?><i class="nudgeblack tipText" title="Nudge Program Team" ></i><?php */ ?>
						<?php } ?>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="prog-col prog-col-2">
		<div class="prog-in-col">
			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="dark-gray tipText <?php if(empty($team['team_count'])) echo 'zero_class'; ?>" title="<?php echo (!empty($team['team_count'])&&$team['team_count']==1)?"1 Team Member":(empty($team['team_count']) ? '0 Team Members':$team['team_count'].' Team Members'); ?>"
						 <?php if(!empty($team['team_count'])){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'progteam-tab', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" style="cursor: pointer;" <?php } ?>
						><?php echo (empty($team['team_count'])) ? 0 : $team['team_count']; ?></li>
				</ul>
				<div class="proginfotext "><span><?php echo ($progs['created_by'] == $current_user_id) ? 'Creator' : 'Stakeholder'; ?></span></div>
			</div>
		</div>
		<div class="prog-in-col">
			<div class="progress-col">
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
		</div>
	</div>
	<div class="prog-col prog-col-3">
		<div class="prog-in-col">
			<div class="progress-col-cont">
				<ul class="workcount">

					<li class="dark-gray tipText <?php if(empty($projects['nos_count'])) {echo 'zero_class';} ?>" data-prog="<?php echo $progs['id']; ?>" data-status="3" title="Not Started"
						<?php if(!empty($projects['nos_count'])){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'progprojects-tab', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" style="cursor: pointer;" <?php } ?>
						><?php echo (!empty($projects['nos_count'])) ? $projects['nos_count'] : 0; ?></li>

					<li class="yellow tipText <?php if(empty($projects['prg_count']))  {echo 'zero_class';} ?>" data-prog="<?php echo $progs['id']; ?>" data-status="2" title="" data-original-title="In Progress"
						<?php if(!empty($projects['prg_count'])){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'progprojects-tab', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" style="cursor: pointer;" <?php } ?>
							><?php echo (!empty($projects['prg_count'])) ? $projects['prg_count'] : 0; ?></li>

					<li class="red tipText <?php if(empty($projects['ovd_count']))  {echo 'zero_class';} ?>" data-prog="<?php echo $progs['id']; ?>" data-status="1" title="" data-original-title="Overdue"
						<?php if(!empty($projects['ovd_count'])){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'progprojects-tab', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" style="cursor: pointer;" <?php } ?>
							><?php echo (!empty($projects['ovd_count'])) ? $projects['ovd_count'] : 0; ?></li>

					<li class="green-bg tipText <?php if(empty($projects['cmp_count']))  {echo 'zero_class';} ?>" data-prog="<?php echo $progs['id']; ?>" data-status="4" title="" data-original-title="Completed"
						<?php if(!empty($projects['cmp_count'])){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'progprojects-tab', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" style="cursor: pointer;" <?php } ?>
							><?php echo (!empty($projects['cmp_count'])) ? $projects['cmp_count'] : 0; ?></li>

				</ul>
				<div class="proginfotext" <?php if(!empty($projects['total_projects'])){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'progprojects-tab', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" style="cursor: pointer;" <?php } ?>><?php echo ($projects['total_projects'] == 1) ? '1 Project' : (empty($projects['total_projects']) ? '0 Projects' :  $projects['total_projects'].' Projects'); ?></div>
			</div>
		</div>
		<div class="prog-in-col">
			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="red tipText <?php if(empty($rag_status['red'])) echo 'zero_class'; ?>" title="" data-original-title="Red"><?php echo $rag_status['red']; ?></li>
					<li class="yellow tipText <?php if(empty($rag_status['yellow'])) echo 'zero_class'; ?>" title="" data-original-title="Amber"><?php echo $rag_status['yellow']; ?></li>
					<li class="green-bg tipText <?php if(empty($rag_status['green'])) echo 'zero_class'; ?>" title="" data-original-title="Green"><?php echo $rag_status['green']; ?></li>
				</ul>
				<div class="proginfotext">RAG</div>
			</div>
		</div>
	</div>
	<div class="prog-col prog-col-4">
		<div class="prog-in-col">
			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="light-gray tipText <?php if(empty($ws['non'])) echo 'zero_class'; ?>" title="Not Set"><?php echo $ws['non']; ?></li>
					<li class="dark-gray tipText <?php if(empty($ws['pnd'])) echo 'zero_class'; ?>" title="Not Started"><?php echo $ws['pnd']; ?></li>
					<li class="yellow tipText <?php if(empty($ws['prg'])) echo 'zero_class'; ?>" title="" data-original-title="In Progress"><?php echo $ws['prg']; ?></li>
					<li class="red tipText <?php if(empty($ws['ovd'])) echo 'zero_class'; ?>" title="" data-original-title="Overdue"><?php echo $ws['ovd']; ?></li>
					<li class="green-bg tipText <?php if(empty($ws['cmp'])) echo 'zero_class'; ?>" title="" data-original-title="Completed"><?php echo $ws['cmp']; ?></li>
				</ul>
				<div class="proginfotext"><?php echo ($ws['total'] == 1) ? '1 Workspace' : $ws['total'].' Workspaces'; ?></div>
			</div>
		</div>
		<div class="prog-in-col">
			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="light-gray tipText <?php if(empty($task['non'])) echo 'zero_class'; ?>" title="Not Set"><?php echo $task['non']; ?></li>
					<li class="dark-gray tipText <?php if(empty($task['pnd'])) echo 'zero_class'; ?>" title="Not Started"><?php echo $task['pnd']; ?></li>
					<li class="yellow tipText <?php if(empty($task['prg'])) echo 'zero_class'; ?>" title="" data-original-title="In Progress"><?php echo $task['prg']; ?></li>
					<li class="red tipText <?php if(empty($task['ovd'])) echo 'zero_class'; ?>" title="" data-original-title="Overdue"><?php echo $task['ovd']; ?></li>
					<li class="green-bg tipText <?php if(empty($task['cmp'])) echo 'zero_class'; ?>" title="" data-original-title="Completed"><?php echo $task['cmp']; ?></li>
				</ul>
				<div class="proginfotext"><?php echo ($task['total'] == 1) ? '1 Task' : $task['total'].' Tasks'; ?></div>
			</div>
		</div>
	</div>
	<div class="prog-col prog-col-5">
		<div class="prog-in-col cost-prog">
			<div class="progress-col-cont">
				<div class="progress-col-cont-min">
					<div class="cost-bar cost-tooltip ctip" title="" data-original-title="<?php echo $budget_tip ?>">
						<span class="blue" style="width: <?php echo round($budget_used) ?>%"></span>
					</div>
					<div class="cost-bar cost-tooltip ctip" title="<?php echo $actual_tip; ?>" aria-describedby="tooltip653447">
						<span class="<?php if($spendcost > $estimatedcost) { ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo round($spend_used); ?>%"></span>
					</div>
				</div>
				<div class="proginfotext"><?php echo $cost_status_text; ?></div>
			</div>
		</div>
		<div class="prog-in-col ">
			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="light-gray tipText <?php if(empty($cost_statuses['cost_not_set']))echo 'zero_class'; ?>" title="Not Set"><?php echo $cost_statuses['cost_not_set']; ?></li>
					<li class="dark-gray tipText <?php if(empty($cost_statuses['cost_budget_set']))echo 'zero_class'; ?>" title="Budget Set"><?php echo $cost_statuses['cost_budget_set']; ?></li>
					<li class="yellow tipText <?php if(empty($cost_statuses['cost_incurred']))echo 'zero_class'; ?>" title="" data-original-title="Costs Incurred"><?php echo $cost_statuses['cost_incurred']; ?></li>
					<li class="red tipText <?php if(empty($cost_statuses['cost_over_budget']))echo 'zero_class'; ?>" title="" data-original-title="Over Budget"><?php echo $cost_statuses['cost_over_budget']; ?></li>
					<li class="green-bg tipText <?php if(empty($cost_statuses['cost_on_budget']))echo 'zero_class'; ?>" title="" data-original-title="On Budget"><?php echo $cost_statuses['cost_on_budget']; ?></li>
				</ul>
				<div class="proginfotext">Status</div>
			</div>
		</div>
	</div>
	<div class="prog-col prog-col-6">
		<div class="prog-in-col">
			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="dark-gray tipText <?php echo (empty($risk_statuses['ropen_count']))?'zero_class':''; ?>" title="Open"><?php echo (empty($risk_statuses['ropen_count']))?'0':$risk_statuses['ropen_count']; ?></li>
					<li class="yellow tipText <?php echo (empty($risk_statuses['rprg_count']))?'zero_class':''; ?>" title="" data-original-title="In Progress"><?php echo (empty($risk_statuses['rprg_count']))?'0':$risk_statuses['rprg_count']; ?></li>
					<li class="red tipText <?php echo (empty($risk_statuses['rovd_count']))?'zero_class':''; ?>" title="" data-original-title="Overdue"><?php echo (empty($risk_statuses['rovd_count']))?'0':$risk_statuses['rovd_count']; ?></li>
					<li class="green-bg tipText <?php echo (empty($risk_statuses['rcmp_count']))?'zero_class':''; ?>" title="" data-original-title="Completed"><?php echo (empty($risk_statuses['rcmp_count']))?'0':$risk_statuses['rcmp_count']; ?></li>
				</ul>
				<div class="proginfotext"><?php echo empty($risk_statuses['risk_total']) ? 0 : $risk_statuses['risk_total']; ?> Total</div>
			</div>
		</div>

		<div class="prog-in-col">

			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="darkred tipText <?php echo empty($hrisk) ? 'zero_class' : ''; ?>"  title="High Pending Risk"><?php echo empty($hrisk) ? '0' : $hrisk; ?></li>
					<li class="red tipText <?php echo empty($srisk) ? 'zero_class' : ''; ?>" title="Severe Pending Risk"><?php echo empty($srisk) ? '0' : $srisk; ?></li>
				</ul>
				<div class="proginfotext "><span>Exposure</span></div>
			</div>
		</div>
	</div>
	<div class="prog-col prog-col-7 pg-actions">
		<a href="#" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_program', $progs['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" class="open-icon tipText" title="Open"><i class="openblack"></i></a>
		<?php if(!empty($projects['total_projects'])){ ?>
			<a href="#" class=" tipText goto-projects" title="Show Projects" data-prog="<?php echo $progs['id']; ?>"><i class="projectsfilterblack"></i></a>
		<?php } ?>
		<?php if($progs['created_by'] != $current_user_id){
 		/*data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'remove_stakeholder', $progs['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_delete"*/
			?>
			<a href="#" class="tipText " title="Remove Me" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'remove_stakeholder', $progs['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_delete"><i class="clearblackicon"></i></a>
		<?php } ?>
		<?php if($progs['created_by'] == $current_user_id){ // ?>
			<a href="#" class="brush-icon tipText prog-paint" title="Change Program Color"><i class="brushblack"></i></a>
		<?php } ?>
		<?php if(!empty($projects['total_projects'])){ ?>
		<a href="#" class="tipText " title="Nudge Projects Team" data-remote="<?php echo Router::Url( array( 'controller' => 'boards', 'action' => 'send_nudge_board', 'program' => $progs['id'], 'type' => 'program_team', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_nudge"><i class="nudgeblack"></i></a>
		<?php /* ?><a href="#" class="tipText " title="Nudge Projects Team" ><i class="nudgeblack"></i></a><?php */ ?>
		<?php } ?>
		<?php if($progs['created_by'] == $current_user_id){ ?>
			<a href="#" class="tipText " title="Edit" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'edit_program', $progs['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_program"><i class="edit-icon"></i></a>
			<a href="#" class="tipText " title="Delete" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'delete_programs', $progs['id'], 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_delete"><i class="deleteblack"></i></a>
		<?php } ?>

	</div>
</div>


	<?php } ?>
<?php }else{ ?>
<div class="no-summary-found">No Programs</div>
<?php } ?>
<script type="text/javascript">
	$(()=>{
		$('.cost-tooltip, .barTip').tooltip({
			'placement': 'top',
			'container': 'body',
			'html': true
		})
	})
</script>