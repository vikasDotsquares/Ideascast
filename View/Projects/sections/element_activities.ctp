<?php
$current_org = $this->Permission->current_org();
$current_user_id = $this->Session->read('Auth.User.id');
if(isset($wsp_activities_tasks) && !empty($wsp_activities_tasks)) {
	foreach ($wsp_activities_tasks as $key => $tasks) {
		$element = $tasks['element_data'];
		$el_level = $tasks['el_level'];
		$efforts = $tasks['efforts'];
		$people_count = $tasks['etc'];
		$assigned = $tasks['assigned'];
		$other_details = $tasks[0];
		$cost_count = $tasks['cost_count'];
		$cost_status = $tasks['cost_status'];
		$reminder_count = $tasks['reminder']['reminder_count'];

		$element_id = $element['ele_id'];
		$area_id = $element['area_id'];

		$taskRole = $element['el_role'];
		$taskPermit = $owner = ($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner') ? true : false;

		// PROJECT STATUS TIP AND TITLE
		$el_status = $element['el_status'];
		$status_title = $status_class = '';
		if($el_status == '4'){ $status_title = 'Completed'; $status_class = 'completed'; }
		else if($el_status == '2'){ $status_title = 'In Progress'; $status_class = 'progressing'; }
		else if($el_status == '3'){ $status_title = 'Not Started'; $status_class = 'not_started'; }
		else if($el_status == '1'){ $status_title = 'Overdue'; $status_class = 'overdue'; }
		else if($el_status == '5'){ $status_title = 'Not Set'; $status_class = 'not_set'; }

		// STATUS
		$el_status = $element['el_status'];
		$status_title = $status_class = '';
		if($el_status == '4'){ $status_title = 'Completed'; $status_class = 'completed'; }
		else if($el_status == '2'){ $status_title = 'In Progress'; $status_class = 'progressing'; }
		else if($el_status == '3'){ $status_title = 'Not Started'; $status_class = 'not_started'; }
		else if($el_status == '1'){ $status_title = 'Overdue'; $status_class = 'overdue'; }
		else if($el_status == '5'){ $status_title = 'Not Set'; $status_class = 'not_set'; }

		// ASSIGNMENTS
		$assign_user = $assign_user_id = $assign_user_org = '';
		$assign_class = $assigned['assign_status'];
		$assign_tip = 'Unassigned';
		$job_title = 'N/A';
		$assign_clickable = false;
		if(isset($assigned['assigned_to']) && !empty($assigned['assigned_to'])){
			$assign_user_id = $assigned['assigned_to'];
			$assign_by = $assigned['created_by'];
			$assign_user_org = $assigned['organization_id'];
			$job_title = $assigned['job_title'];
			$profile_pic = $assigned['profile_pic'];
			$assign_user = $assigned['assigned_user'];
			$assign_class = $assigned['assign_status'];
			if($assign_class == 'not-react'){ $assign_tip = 'Assigned to '.$assign_user.'<br >Schedule Acceptance Pending';}
			else if($assign_class == 'accepted'){ $assign_tip = 'Assigned to '.$assign_user.'<br >Schedule Accepted';}
			else if($assign_class == 'not-accept-start'){ $assign_tip = 'Assigned to '.$assign_user.'<br >Schedule Not Accepted';}
			else if($assign_class == 'disengage'){ $assign_tip = 'Unassigned <br >Disengaged By '.$assign_user;}

			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}

			$assign_clickable = true;
			if(($assign_user_id != $current_user_id) && ($assign_by != $current_user_id)){
				$assign_clickable = false;
			}
		}
		else{
			$assign_clickable = true;
		}

		// ASSETS COUNT
		$wdx = $tasks['asset_count'];
		$wdc = $tasks['decision_count'];
		$wfc = $tasks['feedback_count'];
		$wvc = $tasks['vote_count'];

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

		// COST AND COST STATUS
		$projectCurrencyName = $element['csign'];
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
		$totalspendcost = $cost_count['spcost'];
		$totalestimatedcost = $cost_count['escost'];

		$estimatedcost = ( isset($totalestimatedcost) && $totalestimatedcost > 0 ) ? $totalestimatedcost : 0;
		$spendcost = ( isset($totalspendcost) && $totalspendcost > 0 ) ? $totalspendcost : 0;
		$force_cost_percentage = 2;


		$max_budget = max( $estimatedcost, $spendcost );
		$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
		$spend_used = ($estimatedcost > 0) ? ( ( $spendcost / $estimatedcost) * 100 ) : 0;

		$cost_status_text = $cost_status['c_status'];

		$hrisk_total = $other_details['high_risk_total'];
		$srisk_total = $other_details['severe_risk_total'];
		$all_risk_total = $other_details['all_risk_total'];

		$strpos1 = explode('-', $element['color_code']);
		$row_color = '';
		if(isset($strpos1) && !empty($strpos1)) {
			$sz = count($strpos1) - 1;
			$row_color = 'text-panel-'.$strpos1[$sz];
		}
?>
		<div class="ps-col ts-col-1">
			<div class="opp-project-details">
				<div class="opp-project-left <?php echo $row_color; ?>">
					<i class="task-white"></i>
					<?php if(isset($reminder_count) && !empty($reminder_count)){ ?>
						<?php if( $el_status == 1 || $el_status == 2 || $el_status == 3 ){ ?>
							<i class="clockwhite"  data-toggle="modal"  data-target="#modal_reminder" data-remote="<?php echo Router::url(['controller' => 'workspaces', 'action' => 'element_reminder', $element_id, 'admin' => FALSE], TRUE) ?>" ></i>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="opp-project-middle">
					<a class="opp-project-name" href="<?php echo Router::url(['controller' => 'entities', 'action' => 'update_element', $element_id, '#' => 'tasks', 'admin' => FALSE], TRUE) ?>"><?php echo htmlentities($element['ele_title'], ENT_QUOTES, "UTF-8"); ?></a>
					<span class="opp-project-date">
						<?php
						if( (isset($element['start_date']) && !empty($element['start_date']) )  && (isset($element['end_date']) && !empty($element['end_date'])) ){
							echo date('d M, Y', strtotime($element['start_date'])); ?> →
						<?php echo date('d M, Y', strtotime($element['end_date']));
						}
						else {
							echo 'No Schedule';
						} ?>
					</span>
				</div>
				<div class="opp-pss fl-icon">
					<i class="flag <?php echo $status_class; ?> tipText" title="<?php echo $status_title; ?>"></i>
					<?php if(isset($el_level['confidence_level']) && !empty($el_level['confidence_level'])) { ?>
						<i class="level-ts tipText <?php echo $el_level['confidence_arrow']; ?>" title="<?php echo $el_level['confidence_level']; ?> Confidence Level (<?php echo $el_level['level']; ?>%)"></i>
					<?php }else { ?>
						<i class="level-ts notsetgrey tipText" title="Confidence Level Not Set"></i>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="ps-col ts-col-2">
			<div class="area-name">
				<?php echo htmlentities($element['area_title'], ENT_QUOTES, "UTF-8"); ?>
			</div>
		</div>
		<div class="ps-col ts-col-3">
			<div class="assign_wrapper">
				<div class="progress-col-cont">
					<ul class="workcount taskcounters">
						<li class="dark-gray tipText <?php if(!isset($people_count['owner_count']) || $people_count['owner_count'] == 0){ echo 'zero_class'; }?>" data-target="#modal_people" data-toggle="modal" href="<?php echo Router::url(['controller' => 'entities', 'action' => 'element_people', $element_id, 'admin' => FALSE], TRUE) ?>" title="<?php echo (($people_count['owner_count']) == 1) ? ($people_count['owner_count']).' Owner' : ($people_count['owner_count']).' Owners'; ?>"><?php echo $people_count['owner_count']; ?></li>
						<li class="light-gray tipText <?php if(!isset($people_count['sharer_count']) || $people_count['sharer_count'] == 0){ echo 'zero_class'; }?>" data-target="#modal_people" data-toggle="modal" href="<?php echo Router::url(['controller' => 'entities', 'action' => 'element_people', $element_id, 'admin' => FALSE], TRUE) ?>" title="<?php echo (($people_count['sharer_count']) == 1) ? ($people_count['sharer_count']).' Sharer' : ($people_count['sharer_count']).' Sharers'; ?>"><?php echo $people_count['sharer_count']; ?></li>
						<?php //if( (isset($element['start_date']) && !empty($element['start_date']) )  && (isset($element['end_date']) && !empty($element['end_date'])) ){ ?>
						<li class="eassignment">
							<span class="assignments <?php echo $assign_class; ?> cost-tooltip " <?php if($assign_clickable) { ?> data-toggle="modal" data-remote="<?php echo Router::url(['controller' => 'entities', 'action' => 'task_assignment', $element_id, 'admin' => FALSE], TRUE) ?>" data-target="#modal_task_assignment" <?php } ?> title="<?php echo $assign_tip; ?>" data-el="<?php echo $element_id; ?>" data-html="true"></span>
						</li>
						<?php //} ?>
					</ul>
					<div class="proginfotext tipText" title="Your Role"><?php echo $element['el_role']; ?></div>
				</div>
				<?php if(isset($assign_user_id) && !empty($assign_user_id)){
					$html = CHATHTML($assign_user_id, $project_id); ?>
					<?php if($assign_class != 'disengage'){ ?>
					<div class="assign_div">
						<span class="style-popple-icon-out ">
							<a class="style-popple-icon" data-remote="<?php echo Router::url(['controller' => 'shares', 'action' => 'show_profile', $assign_user_id, 'admin' => FALSE], TRUE) ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal" data-original-title="" title="">
								<img src="<?php echo $user_image; ?>" class="user-image u-pophover" data-content="<div><p><?php echo $assign_user; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>" align="left" width="40" height="40" title="<?php echo $assign_user; ?>">
								<?php if($current_org['organization_id'] != $assign_user_org){ ?>
								<i class="communitygray18 tipText community-g" title="Not In Your Organization" ></i>
								<?php } ?>
							</a>
						</span>
					</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>

	<div class="ps-col ts-col-8">
<?php
//pr($project_progress[0]['efforts']);

		$effort_bar_total_hours = (isset($efforts ) && !empty($efforts['total_hours'])) ? $efforts ['total_hours'] : 0;

		$effort_bar_completed_hours = (isset($efforts ) && !empty($efforts ['blue_completed_hours'])) ? $efforts ['blue_completed_hours'] : 0;


		$effort_bar_green_remaining_hours = (isset($efforts) && !empty($efforts['green_remaining_hours'])) ? $efforts['green_remaining_hours'] : 0;

		$effort_bar_amber_remaining_hours = (isset($efforts) && !empty($efforts['amber_remaining_hours'])) ? $efforts['amber_remaining_hours'] : 0;

		$effort_bar_red_remaining_hours = (isset($efforts) && !empty($efforts['red_remaining_hours'])) ? $efforts['red_remaining_hours'] : 0;

		$effort_bar_remaining_hours = (isset($efforts) && !empty($efforts['remaining_hours'])) ? $efforts['remaining_hours'] : 0;



		if($effort_bar_total_hours > 0){

        $effort_bar_top_percentage = round($effort_bar_completed_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_blue_percentage = round($effort_bar_completed_hours /   ($effort_bar_total_hours ) * 100) ;



        $effort_bar_red_percentage = round($effort_bar_red_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_green_percentage = round($effort_bar_green_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

        $effort_bar_none_percentage = round($effort_bar_remaining_hours /    ($effort_bar_total_hours ) * 100) ;

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

		$total_other_per = $effort_bar_blue_percentage + $effort_bar_red_percentage + $effort_bar_green_percentage +  $effort_bar_amber_percentage;

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
						<?php  if($total_other_per < 100){ ?>
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



		<div class="ps-col ts-col-4">
			<div class="wsp-assets">
				<ul class="progress-assets progress-col-cont">
					<li class="list-assets-icons " data-id="<?php echo $element_id; ?>" data-type="links">
						<span class="assets-count blue tipText <?php if(!isset($links_tot) || $links_tot == 0){ echo 'zero_class'; }?>" title="Total Links"><?php echo $links_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-LinkBlack tipText" title="Links"></i> </span>
					</li>
					<li class="list-assets-icons " data-id="<?php echo $element_id; ?>" data-type="notes">
						<span class="assets-count blue tipText <?php if(!isset($notes_tot) || $notes_tot == 0){ echo 'zero_class'; }?>" title="Total Notes"><?php echo $notes_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-NoteBlack tipText" title="Notes"></i> </span>
					</li>
					<li class="list-assets-icons " data-id="<?php echo $element_id; ?>" data-type="documents">
						<span class="assets-count blue  tipText <?php if(!isset($docs_tot) || $docs_tot == 0){ echo 'zero_class'; }?>" title="Total Documents"><?php echo $docs_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DocumentBlack tipText" title="Documents"></i> </span>
					</li>
					<li class="list-assets-icons " data-id="<?php echo $element_id; ?>" data-type="mind_maps">
						<span class="assets-count blue tipText <?php if(!isset($mms_tot) || $mms_tot == 0){ echo 'zero_class'; }?>" title="Total Mind Maps"><?php echo $mms_tot; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-MindMapBlack tipText" title="Mind Maps"></i> </span>
					</li>
					<li class="list-assets-icons " data-id="<?php echo $element_id; ?>" data-type="decisions">
						<span class="assets-count <?php echo $deci_class; ?> cost-tooltip <?php if(!isset($dec_total) || $dec_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_dec_tot; ?> Completed <br /> <?php echo $prg_dec_tot; ?> In Progress "><?php echo $dec_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-DecisionBlack tipText" title="Decisions"></i> </span>
					</li>
					<li class="list-assets-icons " data-id="<?php echo $element_id; ?>" data-type="feedbacks">
						<span class="assets-count <?php echo $fb_class; ?> cost-tooltip <?php if(!isset($fb_total) || $fb_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_fb_tot; ?> Completed <br /> <?php echo $ovd_fb_tot; ?> Overdue <br /> <?php echo $prg_fb_tot; ?> In Progress <br /> <?php echo $nst_fb_tot; ?> Not Started "><?php echo $fb_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-FeedbackBlack tipText" title="Feedback"></i> </span>
					</li>
					<li class="list-assets-icons " data-id="<?php echo $element_id; ?>" data-type="votes">
						<span class="assets-count <?php echo $vot_class; ?> cost-tooltip <?php if(!isset($vot_total) || $vot_total == 0){ echo 'zero_class'; }?>" title="<?php echo $cmp_vot_tot; ?> Completed <br /> <?php echo $ovd_vot_tot; ?> Overdue <br /> <?php echo $prg_vot_tot; ?> In Progress <br /> <?php echo $nst_vot_tot; ?> Not Started"><?php echo $vot_total; ?></span>
						<span class="prg-assets-icon"> <i class="ws-asset-icon re-VoteBlack tipText" title="Votes"></i> </span>
					</li>
				</ul>
			</div>
		</div>
		<div class="ps-col ts-col-5">
			<?php if($taskPermit){ ?>
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
			<?php }else{ ?>
				<div class="progress-col-cont">
					<i class="visible-off tipText" title="This Information Is Only Available To Owners"></i>
				</div>
			<?php } ?>
		</div>
		<div class="ps-col ts-col-6">
			<div class="progress-col-cont">
				<ul class="workcount">
					<li class="darkred tipText <?php if(!isset($hrisk_total) || $hrisk_total == 0){ echo 'zero_class'; }?>" title="High Pending Risks"><?php echo $hrisk_total; ?></li>
					<li class="red tipText <?php if(!isset($srisk_total) || $srisk_total == 0){ echo 'zero_class'; }?>" title="Severe Pending Risks"><?php echo $srisk_total; ?></li>
				</ul>
				<div class="proginfotext"><?php echo $all_risk_total; ?> Total </div>
			</div>
		</div>
		<div class="ps-col ts-col-7">
			<a href="<?php echo Router::url(['controller' => 'entities', 'action' => 'update_element', $element_id, '#' => 'tasks', 'admin' => FALSE], TRUE) ?>" class="open-icon tipText" title="Open Task"><i class="openblack"></i></a>
			<?php if(!empty($element['permit_edit'])){ ?>
				<a href="#" class="brush-icon tipText wsp-paint" title="Change Task Color" data-id="<?php echo $element_id; ?>"><i class="brushblack"></i></a>
			<?php } ?>

			<?php if($el_status == 2 || $el_status == 3){ ?>
				<a href="#" class="tipText" title="Task Reminder" data-toggle="modal" data-target="#modal_reminder" data-remote="<?php echo Router::url(['controller' => 'workspaces', 'action' => 'element_reminder', $element_id, 'admin' => FALSE], TRUE) ?>"><i class="reminderblack"></i></a>
			<?php } /* else if($el_status == 1 && $reminder_count) { ?>
				<a href="#" class="tipText" title="Task Reminder" data-toggle="modal" data-target="#modal_reminder" data-remote="<?php echo Router::url(['controller' => 'workspaces', 'action' => 'element_reminder', $element_id, 'admin' => FALSE], TRUE) ?>"><i class="reminderblack"></i></a>
			<?php } */ ?>

			<?php if(!empty($element['permit_copy'])){ ?>
				<a href="#" class="tipText" title="Duplicate Task" data-toggle="modal" data-target="#popup_model_box" data-remote="<?php echo Router::url(['controller' => 'entities', 'action' => 'duplicate_task', $element_id, $area_id, $workspace_id, $project_id, 'admin' => FALSE], TRUE) ?>"><i class="duplicateblack"></i></a>
			<?php } ?>
			<?php if(!empty($element['permit_delete'])){ ?>
				<a href="#" class="del-task tipText" title="Delete Task" data-id="<?php echo $element_id; ?>" data-remote="<?php echo Router::url(['controller' => 'entities', 'action' => 'delete_an_task', $element_id, 'admin' => FALSE], TRUE) ?>" data-toggle="modal" data-target="#modal_delete"><i class="deleteblack"></i></a>
			<?php } ?>
		</div>
	<?php } ?>
<?php } ?>
<script type="text/javascript">
	$(function(){
		$('.u-pophover').popover({
            placement : 'bottom',
            placement: function (context, source) {
		        var position = $(source).offset(),
		        	top = position.top,
            		bheight = $('body').height(),
            		stopAt = top + 225;
        		if(stopAt > bheight) {
        			return 'top';
        		}
		        return "bottom";
		    },
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400},
            template: '<div class="popover abcd" role="tooltip"><div class="arrow"></div><div class="popover-content user-menus-popoverss"></div></div>'
        })

		$('.cost-tooltip, .reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip, .cost-percent, .disable.signedoff, .percent-text').tooltip({
            'template': '<div class="tooltip default-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
            'placement': 'top',
            'container': 'body'
        })
	})
</script>