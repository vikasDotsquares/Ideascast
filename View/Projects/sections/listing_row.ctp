<?php

$current_user = $this->Session->read('Auth.User.id');

if (isset($projects) && !empty($projects)) {
	foreach ($projects as $key => $value) {
		 // pr($value );
		$project = $value['prj_detail'];
		$role = $project['prj_role'];
		$efforts = $value['efforts'];
		$owner_count = (!empty($value['oc']['owner_count'])) ? $value['oc']['owner_count'] : 0;
		$sharer_count = (!empty($value['sc']['sharer_count'])) ? $value['sc']['sharer_count'] : 0;
		$board_count = (!empty($value['board']['pb_count'])) ? $value['board']['pb_count'] : 0;
		$oppt_count = (!empty($value['porgss']['porgs_count'])) ? $value['porgss']['porgs_count'] : 0;

		// TASK
		$enon = (!empty($value['ec']['enon'])) ? $value['ec']['enon'] : 0;
		$epnd = (!empty($value['ec']['epnd'])) ? $value['ec']['epnd'] : 0;
		$eprg = (!empty($value['ec']['eprg'])) ? $value['ec']['eprg'] : 0;
		$eovd = (!empty($value['ec']['eovd'])) ? $value['ec']['eovd'] : 0;
		$ecmp = (!empty($value['ec']['ecmp'])) ? $value['ec']['ecmp'] : 0;
		$total_tasks = (!empty($value['ec']['total_tasks'])) ? $value['ec']['total_tasks'] : 0;

		// WSP
		$wnon = (!empty($value['ew']['wnon'])) ? $value['ew']['wnon'] : 0;
		$wpnd = (!empty($value['ew']['wpnd'])) ? $value['ew']['wpnd'] : 0;
		$wprg = (!empty($value['ew']['wprg'])) ? $value['ew']['wprg'] : 0;
		$wovd = (!empty($value['ew']['wovd'])) ? $value['ew']['wovd'] : 0;
		$wcmp = (!empty($value['ew']['wcmp'])) ? $value['ew']['wcmp'] : 0;
		$total_workspaces = (!empty($value['ew']['total_workspaces'])) ? $value['ew']['total_workspaces'] : 0;

		// COST
		$spcost = (!empty($value['pse']['spcost'])) ? $value['pse']['spcost'] : 0;
		$escost = (!empty($value['pse']['escost'])) ? $value['pse']['escost'] : 0;
		$cost_status = $value['cost_status']['c_status'];

		// RISK
		$high_risk_total = (!empty($value['prh']['high_risk_total'])) ? $value['prh']['high_risk_total'] : 0;
		$severe_risk_total = (!empty($value['prs']['severe_risk_total'])) ? $value['prs']['severe_risk_total'] : 0;
		$ps_risk_total = (!empty($value[0]['ps_risk_total'])) ? $value[0]['ps_risk_total'] : 0;

		// COMMENTS
		$comments_count = (!empty($value['pcc']['comments_count'])) ? $value['pcc']['comments_count'] : 0;

		// RAG
		$rag_status = (!empty($value['prag']['rag_status'])) ? $value['prag']['rag_status'] : 0;
		$rag_color = $value['prag']['p_rag'];
		$ovd_percent = $value['prag']['ovd_percent'];
		$rag_type = $value['prag']['rag_type'];//Manual/Rules
		// pr($ovd_percent);

		////////////////////////////////////
		$project_id = $project['pid'];

		// PROJECT STATUS TIP AND TITLE
		$prj_status = $project['prj_status'];
		$status_title = $status_class = '';
		if($prj_status == '4'){ $status_title = 'Completed'; $status_class = 'completed'; }
		else if($prj_status == '2'){ $status_title = 'In Progress'; $status_class = 'progressing'; }
		else if($prj_status == '3'){ $status_title = 'Not Started'; $status_class = 'not_started'; }
		else if($prj_status == '1'){ $status_title = 'Overdue'; $status_class = 'overdue'; }
		else if($prj_status == '5'){ $status_title = 'Not Set'; $status_class = 'not_set'; }

		// RAG TIP
		$parent_Tip = 'Green';
		$parent_Tip =  ($rag_color == 'red') ? 'Red' : ( ( $rag_color == 'yellow') ? 'Amber' : 'Green' );

		if($rag_type == 'Rules'){
			if($rag_color == 'green-bg'){
				$parent_Tip = 'Below Overdue Tasks Thresholds';
			}else if($rag_color == 'yellow'){
				$parent_Tip = $ovd_percent . '% Overdue Tasks';
			}else if($rag_color == 'red'){
				$parent_Tip = $ovd_percent . '% Overdue Tasks';
			}
		}

		// COST PROGRESS BAR
		$estimatedcost = $escost;
		$spendcost = $spcost;
		$force_cost_percentage = 2;
		$projectbudget = ( !empty($project['pbudget'])) ? $project['pbudget'] : 0;
		$max_budget = max($estimatedcost, $spendcost, $projectbudget);
		$budget_used = ($max_budget > 0) ? ( ( $projectbudget / $max_budget) * 100 ) : 0;
		$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
		$spend_used = ($max_budget > 0) ? ( ( $spendcost / $max_budget) * 100 ) : 0;

		$cost_status_text = $cost_status;

		// CURRENCY
		$projectCurrencyName = $project['csign'];
		$projectCurrencysymbol = "";
		/*if($projectCurrencyName == 'USD') {
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
		$risk_counts = (isset($value['risk_counts']['risk_count']) && !empty($value['risk_counts']['risk_count'])) ? $value['risk_counts']['risk_count'] : 0;

?>
	<div class="ps-col ps-col-1">
		<div class="opp-project-details">
			<div class="opp-project-left <?php echo str_replace('panel-', 'project-', $project['color_code']); ?>" >
				<i class="projectwhite-icon"></i>
			</div>
			<div class="opp-project-middle">
				<a class="opp-project-name" href="<?php echo Router::url(['controller' => 'projects', 'action' => 'index', $project_id, 'admin' => FALSE], TRUE) ?>"><?php echo htmlentities($project['ptitle'], ENT_QUOTES, "UTF-8"); ?></a>
				<span class="opp-project-date">
					<?php
					if( (isset($project['psdate']) && !empty($project['psdate']) )  && (isset($project['pedate']) && !empty($project['pedate']) ) ){
						echo date('d M, Y', strtotime($project['psdate'])); ?> →
					<?php echo date('d M, Y', strtotime($project['pedate']));
					}
					else {
						echo 'No Schedule';
					} ?>
				</span>
			</div>
			<div class="opp-pss fl-icon"> <i class="flag <?php echo $status_class; ?> tipText" title="<?php echo $status_title; ?>"></i>
			<?php if(isset($value['wlevel']) && !empty($value['wlevel']['level'])){
			$level_value = $value['wlevel']['level'] ;
			$level_count = $value['wlevel']['level_count'] ;
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
			<i class="level-ts cost-tooltip <?php echo $value['wlevel']['confidence_arrow']; ?>" title="<?php echo $value['wlevel']['confidence_level']; ?> Confidence Level <br /><?php echo $value['wlevel']['level']; ?>% <?php echo $level_value_tip; ?>"></i>
			<?php }else{ ?>
			<i class="level-ts notsetgrey tipText" title="" data-original-title="Confidence Level Not Set"></i>
			<?php } ?>
			</div>

		</div>
	</div>
	<div class="ps-col ps-col-2 rag-col">
		<?php if($role == 'Creator' || $role == 'Owner' || $role == 'Group Owner'){ ?>
		<div class="progress-col-cont">
			<div class="schedule-bar" data-original-title="" title="">
				<span class="annotation pull-right tipText annotation-black <?php echo $rag_color; ?> barTip" title="<?php echo $parent_Tip; ?>" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo Router::url(['controller' => 'projects', 'action' => 'add_annotate', $project_id, 'admin' => FALSE], TRUE) ?>" style="width:100%"><?php echo $comments_count; ?></span>
			</div>
			<div class="proginfotext"><?php echo $rag_type; ?></div>
		</div>
		<?php }
		else{ ?>
		<div class="progress-col-cont">
			<i class="visible-off tipText" title="This Information Is Only Available To Owners"></i>
		</div>
		<?php } ?>
	</div>
	<div class="ps-col ps-col-3">
		<div class="progress-col-cont views">
			<ul class="workcount people-counter">
				<li class="dark-gray tipText <?php if(!isset($owner_count) || $owner_count == 0){ echo 'zero_class'; }?>" title="<?php echo (($owner_count) == 1) ? ($owner_count).' Owner' : ($owner_count).' Owners'; ?>" data-toggle="modal" <?php if(isset($owner_count) && !empty($owner_count )){ ?>  data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_project', $project_id, 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" <?php } ?>><?php echo $owner_count; ?></li>
				
				<li class="light-gray tipText <?php if(!isset($sharer_count) || $sharer_count == 0){ echo 'zero_class'; }?>" title="<?php echo (($sharer_count) == 1) ? ($sharer_count).' Sharer' : ($sharer_count).' Sharers'; ?>" <?php if(isset($sharer_count) && !empty($sharer_count )){ ?> data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_project', $project_id, 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program" <?php } ?>><?php echo $sharer_count; ?></li>
				
				
				<?php if($role == 'Creator' || $role == 'Owner' || $role == 'Group Owner'){ ?>
				<?php if( $prj_status != 4 && $oppt_count > 0 ){ ?>
				<li class="yellow tipText <?php if(!isset($board_count) || $board_count == 0){ echo 'zero_class'; }?>" title="Pending Opportunity Requests">

					<a <?php if(!empty($board_count)) { ?> href="<?php echo Router::Url( array( 'controller' => 'boards', 'action' => 'opportunity','request', $project_id, 'admin' => FALSE ), TRUE ); ?>" <?php } ?>><?php echo $board_count; ?></a>
				</li>
				<?php }
				}
				?>
			</ul>
			<div class="proginfotext" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'view_project', $project_id, 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_view_program"><?php echo ($role == 'Owner' || $role == 'Group Owner') ? 'Owner' : ( ($role == 'Sharer' || $role == 'Group Sharer') ? 'Sharer' : 'Creator' ); ?></div>
		</div>
	</div>
	<div class="ps-col ps-col-4 work-task-column">
		<div class="progress-col-cont">
			<ul class="workcount">
				<li class="light-gray tipText <?php if(!isset($wnon) || $wnon == 0){ echo 'zero_class'; }?>" title="Not Set"><?php echo $wnon; ?></li>
				<li class="dark-gray tipText <?php if(!isset($wpnd) || $wpnd == 0){ echo 'zero_class'; }?>" title="Not Started"><?php echo $wpnd; ?></li>
				<li class="yellow tipText <?php if(!isset($wprg) || $wprg == 0){ echo 'zero_class'; }?>" title="" data-original-title="In Progress"><?php echo $wprg; ?></li>
				<li class="red tipText <?php if(!isset($wovd) || $wovd == 0){ echo 'zero_class'; }?>" title="" data-original-title="Overdue"><?php echo $wovd; ?></li>
				<li class="green-bg tipText <?php if(!isset($wcmp) || $wcmp == 0){ echo 'zero_class'; }?>" title="" data-original-title="Completed"><?php echo $wcmp; ?></li>
			</ul>
			<div class="proginfotext"><?php echo ($total_workspaces == 1) ? $total_workspaces.' Workspace' : $total_workspaces.' Workspaces'; ?></div>
		</div>
		<div class="progress-col-cont pt-task-column">
			<ul class="workcount">
				<li class="light-gray tipText <?php if(!isset($enon) || $enon == 0){ echo 'zero_class'; }?>" title="Not Set"><?php echo $enon; ?></li>
				<li class="dark-gray tipText <?php if(!isset($epnd) || $epnd == 0){ echo 'zero_class'; }?>" title="Not Started"><?php echo $epnd; ?></li>
				<li class="yellow tipText <?php if(!isset($eprg) || $eprg == 0){ echo 'zero_class'; }?>" title="" data-original-title="In Progress"><?php echo $eprg; ?></li>
				<li class="red tipText <?php if(!isset($eovd) || $eovd == 0){ echo 'zero_class'; }?>" title="" data-original-title="Overdue"><?php echo $eovd; ?></li>
				<li class="green-bg tipText <?php if(!isset($ecmp) || $ecmp == 0){ echo 'zero_class'; }?>" title="" data-original-title="Completed"><?php echo $ecmp; ?></li>
			</ul>
			<div class="proginfotext"><?php echo ($total_tasks == 1) ? $total_tasks.' Task' : $total_tasks.' Tasks'; ?></div>
		</div>
	</div>

	<div class="ps-col ps-col-8">


<?php
//pr($project_progress[0]['efforts']);

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

            <div class="progress-col">
           <?php /* ?> <div class="progress-col-heading">
                <span class="prog-h"><a href="javascript:;" style="cursor: default;">Effort <i class="arrow-down" style="cursor: default !important;"></i></a></span>
                <?php if($effort_bar_top_percentage > 0) { ?>
                    <span class="percent-text" title="Percentage Complete"><?php echo $effort_bar_top_percentage; ?>%</span>
                <?php }else if($effort_bar_total_hours_text > 0){ ?>
                    <span class="percent-text" title="Percentage Complete">0%</span>
                <?php } ?>

            </div><?php */ ?>
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



	<div class="ps-col ps-col-5">
		<?php if($role == 'Creator' || $role == 'Owner' || $role == 'Group Owner'){ ?>
		<div class="progress-col-cont">
			<div class="cost-bar cost-tooltip" title="Budget (<?php echo $projectCurrencyName ?>): <?php echo number_format($projectbudget, 2, '.', ','); ?>">

			<?php
				//pr($estimate_used);

				if(round($projectbudget) > 0){
						$budget_used = ($budget_used < $force_cost_percentage) ? $force_cost_percentage : $budget_used;
				} ?>
				<span class="blue" style="width: <?php echo round($budget_used) ?>%"></span>
			</div>
            <?php /* ?><div class="cost-bar cost-tooltip" title="Budget: <?php echo $projectCurrencysymbol.number_format($estimatedcost, 2, '.', ','); ?>">
			<?php

			if(round($estimatedcost) > 0){
					$estimate_used = ($estimate_used < $force_cost_percentage) ? $force_cost_percentage : $estimate_used;
			}
			?>

				<span class="<?php if($estimatedcost > $projectbudget) { ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo round($estimate_used); ?>%"></span>
			</div><?php */ ?>
            <div class="cost-bar cost-tooltip" title="Actual (<?php echo $projectCurrencyName ?>): <?php echo number_format($spendcost, 2, '.', ','); ?>">
			<?php

			if(round($spendcost) > 0){
					$spend_used = ($spend_used < $force_cost_percentage) ? $force_cost_percentage : $spend_used;
			}
			?>
				<span class="<?php if($spendcost > $estimatedcost) { ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo round($spend_used); ?>%"></span>
			</div>
			<div class="proginfotext"><?php echo $cost_status_text; ?></div>
		</div>
		<?php }else{ ?>
		<div class="progress-col-cont">
			<i class="visible-off tipText" title="This Information Is Only Available To Owners"></i>
		</div>
		<?php } ?>
	</div>
	<div class="ps-col ps-col-6">
		<div class="progress-col-cont">
			<ul class="workcount">
				<li class="darkred tipText <?php if(!isset($high_risk_total) || $high_risk_total == 0){ echo 'zero_class'; }?>" title="High Pending Risks"><?php echo $high_risk_total; ?></li>
				<li class="red tipText <?php if(!isset($severe_risk_total) || $severe_risk_total == 0){ echo 'zero_class'; }?>" title="Severe Pending Risks"><?php echo $severe_risk_total; ?></li>
			</ul>
			<div class="proginfotext"><?php echo( $risk_counts); ?> Total</div>
		</div>
	</div>
	<div class="ps-col ps-col-7">
		<a href="<?php echo Router::url(['controller' => 'projects', 'action' => 'index', $project_id, 'admin' => FALSE], TRUE) ?>" class="open-icon tipText" title="Open"><i class="openblack"></i></a>
		<?php if(!empty($project['permit_edit'])){ ?>
			<a href="#" class="brush-icon tipText prj-paint" title="Change Project Color"><i class="brushblack"></i></a>
		<?php } ?>
	</div>

	<?php } ?>
<?php } ?>
<script type="text/javascript">
	$('.cost-tooltip').tooltip({
		'placement': 'top',
		'container': 'body',
		'html': true
	})
	$('.reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip, .cost-percent, .disable.signedoff, .percent-text').tooltip({
            'template': '<div class="tooltip default-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
            'placement': 'top',
            'container': 'body'
        })
</script>
<style>.views .proginfotext{ cursor:pointer;}
.zero_class{ cursor: default !important; }
</style>