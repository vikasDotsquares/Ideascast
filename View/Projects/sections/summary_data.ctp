<?php
$current_user = $this->Session->read('Auth.User.id');
$project_summary = $this->Permission->summary_data($project_id, $current_page, $limit);

// $project_summary = $this->Permission->project_summary($project_id, $current_page, $limit);
// $project_role = $this->ViewModel->projectPermitType($project_id);


if (isset($project_summary) && !empty($project_summary)) {
	foreach ($project_summary as $key => $value) {
		$wsp_detail = $value['pwd'];
		// pr($wsp_detail );
		$efforts = $value['efforts'];

		$project_role_current = $wsp_detail['role'];
		$project_role = ($project_role_current == 'Creator' || $project_role_current == 'Owner' || $project_role_current == 'Group Owner') ? 1 : 0;
		if($project_role_current=='Group Owner'){
			$project_role_current = 'Owner';
		}

		if($project_role_current=='Group Sharer'){
			$project_role_current = 'Sharer';
		}



		$el_counts = $value['wec'];
		$ws_cost = $value['wse'];
		$workspace_id = $wsp_detail['ws_id'];
		// $project_role = $value['user_permissions']['project_role'];
		$other_details = $value[0];
		$owners_count = (isset($value['wtc']['owner_count']) && !empty($value['wtc']['owner_count'])) ? $value['wtc']['owner_count'] : 0;
		$sharers_count = (isset($value['wtc']['sharer_count']) && !empty($value['wtc']['sharer_count'])) ? $value['wtc']['sharer_count'] : 0;
		// pr($value);
		$risk_counts = (isset($value['risk_counts']['risk_count']) && !empty($value['risk_counts']['risk_count'])) ? $value['risk_counts']['risk_count'] : 0;
		$projectCurrencyName = $wsp_detail['sign'];
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

		$ws_status = $wsp_detail['ws_status'];
		$status_title = $status_class = '';
		if($ws_status == 'completed'){ $status_title = 'Completed'; $status_class = 'completed'; }
		else if($ws_status == 'progressing'){ $status_title = 'In Progress'; $status_class = 'progressing'; }
		else if($ws_status == 'not_started'){ $status_title = 'Not Started'; $status_class = 'not_started'; }
		else if($ws_status == 'overdue'){ $status_title = 'Overdue'; $status_class = 'overdue'; }
		else if($ws_status == 'not_set'){ $status_title = 'Not Set'; $status_class = 'not_set'; }


		// Cost
		$totalspendcost = $ws_cost['spcost'];
		$totalestimatedcost =  $ws_cost['escost'];

		$estimatedcost = ( isset($totalestimatedcost) && $totalestimatedcost > 0 ) ? $totalestimatedcost : 0;
		$spendcost = ( isset($totalspendcost) && $totalspendcost > 0 ) ? $totalspendcost : 0;
		$force_cost_percentage = 2;


		$max_budget = max( $estimatedcost, $spendcost );
		$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
		$spend_used = ($estimatedcost > 0) ? ( ( $spendcost / $estimatedcost) * 100 ) : 0;

		$cost_status_text = $this->Permission->wsp_cost_status_text( $estimatedcost, $spendcost);

		// $pending_high_risk = (issemp($other_details['phigh_risk'])) ? $other_details['phigh_risk'] : 0;
		// $pending_severe_risk = (issemp($other_details['psevere_risk'])) ? $other_details['psevere_risk'] : 0;

		$non = (issemp($el_counts['NON'])) ? $el_counts['NON'] : 0;
		$pnd = (issemp($el_counts['PND'])) ? $el_counts['PND'] : 0;
		$prg = (issemp($el_counts['PRG'])) ? $el_counts['PRG'] : 0;
		$ovd = (issemp($el_counts['OVD'])) ? $el_counts['OVD'] : 0;
		$cmp = (issemp($el_counts['CMP'])) ? $el_counts['CMP'] : 0;
		$total_tasks = $non + $pnd + $prg + $ovd + $cmp;

		$pending_high_risk = $pending_severe_risk = 0;
		$pending_high_risk = (!empty($value['wrh']['high_risk_total'])) ? $value['wrh']['high_risk_total'] : 0;
		$pending_severe_risk = (!empty($value['wrs']['severe_risk_total'])) ? $value['wrs']['severe_risk_total'] : 0;
?>
	<?php
		//$taskCountAst = $this->ViewModel->getTaskCountAsset($workspace_id);

		$wdx = $value['wdx'];
		$wdc = $value['wdc'];
		$wfc = $value['wfc'];
		$wvc = $value['wvc'];

		$total_assets = (!empty($other_details['as_tot'])) ? $other_details['as_tot'] : 0;
		$dc_tot = (!empty($other_details['dc_tot'])) ? $other_details['dc_tot'] : 0;
		$fb_tot = (!empty($other_details['fb_tot'])) ? $other_details['fb_tot'] : 0;
		$vt_tot = (!empty($other_details['vt_tot'])) ? $other_details['vt_tot'] : 0;

		$links_tot = (!empty($wdx['el_tot'])) ? $wdx['el_tot'] : 0;
		$notes_tot = (!empty($wdx['en_tot'])) ? $wdx['en_tot'] : 0;
		$docs_tot = (!empty($wdx['ed_tot'])) ? $wdx['ed_tot'] : 0;
		$mms_tot = (!empty($wdx['em_tot'])) ? $wdx['em_tot'] : 0;

		$prg_dec_tot = ( !empty($wdc['dc_prg'])) ? $wdc['dc_prg'] : 0;
		$cmp_dec_tot = (!empty($wdc['dc_cmp'])) ? $wdc['dc_cmp'] : 0;


		$nst_fb_tot = (!empty($wfc['fb_nst'])) ? $wfc['fb_nst'] : 0;
		$prg_fb_tot = (!empty($wfc['fb_prg'])) ? $wfc['fb_prg'] : 0;
		$ovd_fb_tot = (!empty($wfc['fb_ovd'])) ? $wfc['fb_ovd'] : 0;
		$cmp_fb_tot = (!empty($wfc['fb_cmp'])) ? $wfc['fb_cmp'] : 0;

		$nst_vot_tot = (!empty($wvc['vt_nst'])) ? $wvc['vt_nst'] : 0;
		$prg_vot_tot = (!empty($wvc['vt_prg'])) ? $wvc['vt_prg'] : 0;
		$ovd_vot_tot = (!empty($wvc['vt_ovd'])) ? $wvc['vt_ovd'] : 0;
		$cmp_vot_tot = (!empty($wvc['vt_cmp'])) ? $wvc['vt_cmp'] : 0;

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
		// e($wsp_detail['start_date']);
		// e($wsp_detail['end_date']);
	 ?>
<div class="wsp-data-row" data-id="<?php echo $workspace_id; ?>">
	<div class="wsp-col wsp-col-1">
		<div class="opp-project-details">
			<div class="opp-project-left <?php echo str_replace('bg-', 'project-', $wsp_detail['color_code']); ?> tipText" <?php if($project_role){ ?> title="Click And Drag To Reorder" <?php } ?>>
				<i class="workspacewhite"></i>
			</div>
			<div class="opp-project-middle">
				<a class="opp-project-name" href="<?php echo Router::url(['controller' => 'projects', 'action' => 'manage_elements', $project_id, $workspace_id, 'admin' => FALSE], TRUE) ?>"><?php echo htmlentities($wsp_detail['title'], ENT_QUOTES, "UTF-8"); ?></a>
				<span class="opp-project-date">
					<?php
					if( (isset($wsp_detail['start_date']) && !empty($wsp_detail['start_date']) )  && (isset($wsp_detail['end_date']) && !empty($wsp_detail['end_date']) ) ){ ?>
					<?php echo date('d M, Y',strtotime($wsp_detail['start_date'])); ?> →
					<?php echo date('d M, Y',strtotime($wsp_detail['end_date'])); ?>
					<?php }
					else {
						echo 'No Schedule';
					} ?>
				</span>
			</div>
			<div class="opp-pss fl-icon"> <i class="flag <?php echo $status_class; ?> tipText" title="<?php echo $status_title; ?>"></i>
			<?php  if(isset($value['wlevel']) && !empty($value['wlevel']['level'])){

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
	<div class="wsp-col wsp-col-2">
		<div class="progress-col-cont">
			<ul class="workcount">
				<li class="dark-gray tipText <?php if(!isset($owners_count) || $owners_count == 0){ echo 'zero_class'; }?>" title="<?php echo (($owners_count) == 1) ? ($owners_count).' Owner' : ($owners_count).' Owners'; ?>"><?php echo $owners_count; ?></li>
				<li class="light-gray tipText <?php if(!isset($sharers_count) || $sharers_count == 0){ echo 'zero_class'; }?>" title="<?php echo (($sharers_count) == 1) ? ($sharers_count).' Sharer' : ($sharers_count).' Sharers'; ?>"><?php echo $sharers_count; ?></li>
			</ul>
			<div class="proginfotext "><span class="tipText" title="Your Role"><?php echo $project_role_current;//(($owners_count + $sharers_count) > 1) ? ($owners_count + $sharers_count).' People' : ($owners_count + $sharers_count).' Person'; ?></span></div>
		</div>
	</div>
	<div class="wsp-col wsp-col-3">
		<?php  ?><div class="progress-col-cont">
			<ul class="workcount">
				<li class="light-gray tipText <?php if(!isset($non) || $non == 0){ echo 'zero_class'; }?>" title="Not Set"><?php echo $non; ?></li>
				<li class="dark-gray tipText <?php if(!isset($pnd) || $pnd == 0){ echo 'zero_class'; }?>" title="Not Started"><?php echo $pnd; ?></li>
				<li class="yellow tipText <?php if(!isset($prg) || $prg == 0){ echo 'zero_class'; }?>" title="" data-original-title="In Progress"><?php echo $prg; ?></li>
				<li class="red tipText <?php if(!isset($ovd) || $ovd == 0){ echo 'zero_class'; }?>" title="" data-original-title="Overdue"><?php echo $ovd; ?></li>
				<li class="green-bg tipText <?php if(!isset($cmp) || $cmp == 0){ echo 'zero_class'; }?>" title="" data-original-title="Completed"><?php echo $cmp; ?></li>
			</ul>
			<div class="proginfotext"><?php echo ($total_tasks == 1) ? $total_tasks . ' Task' : $total_tasks . ' Tasks'; ?></div>
		</div><?php  ?>
	</div>
	<div class="wsp-col wsp-col-8">


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

	<div class="wsp-col wsp-col-4">
		<div class="wsp-assets">
			<?php  ?><ul class="progress-assets progress-col-cont">
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
					<span class="assets-count <?php echo $fb_class; ?> cost-tooltip <?php if(!isset($fb_total) || $fb_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_fb_tot; ?> Completed <br /> <?php echo $ovd_fb_tot; ?> Overdue <br /> <?php echo $prg_fb_tot; ?> In Progress <br /> <?php echo $nst_fb_tot; ?> Not Started "><?php echo $fb_total; ?></span>
					<span class="prg-assets-icon"> <i class="ws-asset-icon re-FeedbackBlack tipText" title="Feedback"></i> </span>
				</li>
				<li>
					<span class="assets-count <?php echo $vot_class; ?> cost-tooltip <?php if(!isset($vot_total) || $vot_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_vot_tot; ?> Completed <br /> <?php echo $ovd_vot_tot; ?> Overdue <br /> <?php echo $prg_vot_tot; ?> In Progress <br /> <?php echo $nst_vot_tot; ?> Not Started"><?php echo $vot_total; ?></span>
					<span class="prg-assets-icon"> <i class="ws-asset-icon re-VoteBlack tipText" title="Votes"></i> </span>
				</li>
			</ul><?php  ?>
		</div>
	</div>
	<div class="wsp-col wsp-col-5">
		<?php if($project_role){ ?>
		<div class="progress-col-cont">
			<div class="progress-col-cont-min">
				<div class="cost-bar cost-tooltip" title="Budget (<?php echo $projectCurrencyName; ?>): <?php echo number_format($estimatedcost, 2, '.', ','); ?>">
					<?php
						if(round($estimatedcost) > 0){
							$estimate_used = ($estimate_used < $force_cost_percentage) ? $force_cost_percentage : $estimate_used;
						}
					?>
					<span class="blue" style="width: <?php echo round($estimate_used); ?>%"></span>
				</div>
				<div class="cost-bar cost-tooltip" title="Actual (<?php echo $projectCurrencyName; ?>): <?php echo number_format($spendcost, 2, '.', ','); ?>">
					<?php
						if(round($spendcost) > 0){
							$spend_used = ($spend_used < $force_cost_percentage) ? $force_cost_percentage : $spend_used;
						}
					?>
					<span class="<?php if($spendcost > $estimatedcost) { ?>red<?php }else{ ?>green-bg<?php } ?>" style="width: <?php echo (!empty($estimatedcost) && !empty($spendcost)) ? round($spend_used) : ((empty($estimatedcost) && !empty($spendcost)) ? '100' : '0'); ?>%"></span>
				</div>
			</div>
			<div class="proginfotext"><?php echo $cost_status_text; ?></div>
		</div>
		<?php }
		else{ ?>
		<div class="progress-col-cont">
			<i class="visible-off tipText" title="This Information Is Only Available To Owners"></i>
		</div>
		<?php } ?>
	</div>
	<div class="wsp-col wsp-col-6">
		<div class="progress-col-cont">
			<ul class="workcount">
				<li class="darkred tipText <?php if(!isset($pending_high_risk) || $pending_high_risk == 0){ echo 'zero_class'; }?>" title="High Pending Risks"><?php echo $pending_high_risk; ?></li>
				<li class="red tipText <?php if(!isset($pending_severe_risk) || $pending_severe_risk == 0){ echo 'zero_class'; }?>" title="Severe Pending Risks"><?php echo $pending_severe_risk; ?></li>
			</ul>
			<div class="proginfotext"><?php echo $risk_counts; ?> Total </div>
		</div>
	</div>
	<div class="wsp-col wsp-col-7">
		<a href="<?php echo Router::url(['controller' => 'projects', 'action' => 'manage_elements', $project_id, $workspace_id, 'admin' => FALSE], TRUE) ?>" class="open-icon tipText" title="Open Workspace"><i class="openblack"></i></a>

		<?php if($project_role){ ?>
		<a href="#" class="down-icon tipText" title="Move Down"><i class="downblack"></i></a>
		<a href="#" class="up-icon tipText" title="Move Up"><i class="upblack"></i></a>
		<a href="#" class="brush-icon tipText wsp-paint" title="Change Workspace Color" data-id="<?php echo $workspace_id; ?>"><i class="brushblack"></i></a>
		<?php } ?>
	</div>
</div>
<?php }
}else{ ?>
<div class="no-summary-found">No Workspaces</div>
<?php } ?>

<script type="text/javascript">
	$(function(){
		$('.wsp-data').find('.wsp-data-row:first').find('.up-icon').addClass('not-shown');
		$('.wsp-data').find('.wsp-data-row:last').find('.down-icon').addClass('not-shown');

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
	})
</script>