<?php

$current_user = $this->Session->read('Auth.User.id');
$taskID = $element_id;

$task_progress_bar = $this->Scratch->task_progress_bar($project_id, $element_id);
$task_progress_bar = $task_progress_bar[0];

$edata = $task_progress_bar['edata'];
$teams = $task_progress_bar['etc'];
$e_assets = $task_progress_bar['e_assets'];
$e_decision = $task_progress_bar['e_decision'];
$e_fb = $task_progress_bar['e_fb'];
$e_vote = $task_progress_bar['e_vote'];
$e_cost = $task_progress_bar['e_cost'];
$e_hrisk = (!empty($task_progress_bar['e_hrisk']['risk_total'])) ? $task_progress_bar['e_hrisk']['risk_total'] : 0;
$e_srisk = (!empty($task_progress_bar['e_srisk']['risk_total'])) ? $task_progress_bar['e_srisk']['risk_total'] : 0;
$e_hrisk_off = (!empty($task_progress_bar['e_hrisk_off']['risk_total'])) ? $task_progress_bar['e_hrisk_off']['risk_total'] : 0;
$e_srisk_off = (!empty($task_progress_bar['e_srisk_off']['risk_total'])) ? $task_progress_bar['e_srisk_off']['risk_total'] : 0;
$e_levels = $task_progress_bar['e_levels'];
$efforts = $task_progress_bar['efforts'];
$risk_counts = (!empty($task_progress_bar['risk_counts']['risk_count'])) ? $task_progress_bar['risk_counts']['risk_count'] : 0;
$others = $task_progress_bar[0];
$assigned = $task_progress_bar['assigned'];
// pr($assigned);
// $project_type = project_type($project_id, $current_user);

$costURL = Router::Url( array( 'controller' => 'projects', 'action' => 'index', $project_id, 'tab' => 'cost', 'admin' => FALSE ), TRUE );
// $costURL = SITEURL.'costs/index/'.$project_type.':'.$project_id;
$ganttURL = '';
// task role
$taskRole = $edata['role']; // taskRole($taskID, $current_user);

$taskRoleCurrent = $taskRole;

if($taskRole=='Group Owner'){
$taskRoleCurrent = 'Owner';
}

if($taskRole=='Group Sharer'){
$taskRoleCurrent = 'Sharer';
}

// owner/sharer
$taskOwnersTotal = $teams['owner_count']; // $this->Permission->taskOwners($taskID, 1);
$taskSharersTotal = $teams['sharer_count']; // $this->Permission->taskSharers($taskID, 1);

$wsp_risks = $pending_high_risk = $pending_severe_risk = $sign_high_risk = $sign_severe_risk = 0;

$wspTasks = [$taskID];
if($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner'){

    // Cost
    $totalestimatedcost = $e_cost['escost'];// $this->Permission->wsp_element_cost($wspTasks, 'estimated_cost');
    $totalspendcost = $e_cost['spcost'];// $this->Permission->wsp_element_cost($wspTasks, 'spend_cost');
    $projectCurrencyName = $edata['sign'];// $this->Common->getCurrencySymbolName($project_id);


    $force_cost_percentage = 2;
    $estimatedcost = ( isset($totalestimatedcost) && $totalestimatedcost > 0 ) ? $totalestimatedcost : 0;
    $spendcost = ( isset($totalspendcost) && $totalspendcost > 0 ) ? $totalspendcost : 0;
// e($spendcost, 1);
    $max_budget = max( $estimatedcost, $spendcost );
    $estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
    $spend_used = ($estimatedcost > 0) ? ( ( $spendcost / $estimatedcost) * 100 ) : 0;

    $cost_status_text = $others['cost_status'];// $this->Permission->wsp_cost_status_text( $estimatedcost, $spendcost);

    // risk counters
    $wspTasks = [$taskID];
    // if there are any task in the wsp
    // $user_project_risks = user_project_risks($project_id, $current_user);
    // if(isset($user_project_risks) && !empty($user_project_risks)){
        $wsp_risks = $risk_counts;// wsp_risks($user_project_risks, $project_id, $wspTasks);
        $pending_high_risk = $e_hrisk;//wsp_pending_risks($user_project_risks, $project_id, $wspTasks, 'high');
        $pending_severe_risk = $e_srisk; //wsp_pending_risks($user_project_risks, $project_id, $wspTasks, 'severe');
        $sign_high_risk = $e_hrisk_off;// wsp_signedoff_risks($user_project_risks, $project_id, $wspTasks, 'high');
        $sign_severe_risk = $e_srisk_off; // wsp_signedoff_risks($user_project_risks, $project_id, $wspTasks, 'severe');
    // }
}
?>
<?php
    $project_level = 0;

    if($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner'){
        $project_level = 1;
    }

/*    $wsp_signoff = $this->ViewModel->workspace_signoff($workspace_id);
    $signoff_msg = '';
    if($wsp_signoff){
        $signoff_msg = 'This Workspace is signed off';
    }*/

    $disable_risk = '';
    $disable_risk_tip ='';
    $disable_risk_cursor ='';
    if( isset($edata['sign_off']) && !empty($edata['sign_off']) ){
        $disable_risk = 'disable';
        $disable_risk_tip = 'Task Is Signed Off';
        $disable_risk_cursor ='cursor:default !important;';
        $signoff_msg = 'This Task is signed off';
    }

    // TASK ASSIGNMENT
    // $element_assigned = element_assigned( $element_id);
    // pr($element_assigned);
    // pr($assigned);

    $effort_bar[0]['ef'] = $efforts; // $this->Permission->team_task_efforts_progressbar($element_id);
    // $effort_bar = $this->Permission->team_task_efforts_progressbar($element_id);

    // pr($effort_bar);
    // pr($efforts);

    $creator = $receiver = false;
    $assign_class = 'not-avail';
    $assign_tip = 'Unassigned';


    if($assigned) {
        $creator = ($assigned['created_by'] == $this->Session->read('Auth.User.id')) ? true : false;
        $receiver = ($assigned['assigned_to'] == $this->Session->read('Auth.User.id')) ? true : false;

        $current_org = $this->Permission->current_org();
        $receiver_org = $assigned['asi_org'];//$this->Permission->current_org($element_assigned['ElementAssignment']['assigned_to']);

        // $creator_detail = get_user_data($element_assigned['ElementAssignment']['created_by']);
        // pr($creator_detail);
        $creator_name = $assigned['created_user'];// $creator_detail['UserDetail']['full_name'];

        // $receiver_detail = get_user_data($element_assigned['ElementAssignment']['assigned_to']);
        $receiver_name = $assigned['assigned_user'];//$receiver_detail['UserDetail']['full_name'];
        // pr($receiver_detail);

        $html = '';
        if( $assigned['assigned_to'] != $this->Session->read('Auth.User.id') ) {
            $html = CHATHTML($assigned['assigned_to'],$project_id);
        }
        $style = '';



        $user_image = SITEURL . 'images/placeholders/user/user_1.png';
        $user_name = '';
        $job_title = 'N/A';
        // if(isset($receiver_detail) && !empty($receiver_detail)) {
            //$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);

            /*if( isset($receiver_detail['UserDetail']['first_name']) && !empty($receiver_detail['UserDetail']['first_name']) ){
                $user_name .= htmlentities($receiver_detail['UserDetail']['first_name'],ENT_QUOTES);
            }
            if( isset($receiver_detail['UserDetail']['last_name']) && !empty($receiver_detail['UserDetail']['last_name']) ){
                $user_name .= ' '.htmlentities($receiver_detail['UserDetail']['last_name'],ENT_QUOTES);
            }*/
            $user_name = $receiver_name;

            $profile_pic = $assigned['asi_profile'];//$receiver_detail['UserDetail']['profile_pic'];
            $job_title = htmlentities($assigned['asi_job'],ENT_QUOTES);

            if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
                $user_image = SITEURL . USER_PIC_PATH . $profile_pic;

         // }
        }

        if($assigned['reaction'] == 1) {
            $assign_class = 'accepted';
            $assign_tip = "Assigned to ".$receiver_name . '<br /> Schedule Accepted';
        }
        else if($assigned['reaction'] == 2) {
            $assign_class = 'not-accept-start';
            $assign_tip = "Assigned to ".$receiver_name . '<br /> Schedule Not Accepted';
        }
        else if($assigned['reaction'] == 3) {
            $assign_class = 'disengage';
            $assign_tip = "Unassigned <br /> Disengaged By ".$receiver_name;
        }
        else{
            if(!empty($assigned['assigned_to'])) {
                $assign_tip = "Assigned to ".$receiver_name.'<br /> Schedule Acceptance Pending';
                $assign_class = 'not-react';
            }

        }
    }

    // DELETE PERMISSION
    // $elementPermission = $this->Common->element_manage_permission($taskID, $project_id, $current_user);
    $elementPermission[0]['user_permissions'] = $edata;
    $is_editaa = 0;
    $is_add_shares = 0;
    $is_edit_shares = 0;
    $is_read_shares = 0;
    $is_delete_shares = 0;
    if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_edit']) ){
        $is_editaa = $elementPermission[0]['user_permissions']['permit_edit'];
    }
    if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_edit']) ){
        $is_edit_shares = $elementPermission[0]['user_permissions']['permit_edit'];
        $is_edit_share = $elementPermission[0]['user_permissions']['permit_edit'];
    }
    if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_read']) ){
        $is_read_shares = $elementPermission[0]['user_permissions']['permit_read'];
    }
    if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_delete']) ){
        $is_delete_shares = $elementPermission[0]['user_permissions']['permit_delete'];
    }
    if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_add']) ){
        $is_add_shares = $elementPermission[0]['user_permissions']['permit_add'];
    }

    $cost_url = SITEURL.'entities/task_list_el_date_cost/'.$task_detail['id'];

    $assiged = false;
    if(isset($assigned) && !empty($assigned)){
        if($assigned['assigned_to'] == $current_user){
            $assiged = true;
        }
    }

    if($project_level || ($assiged && $assigned['reaction'] != 3) ) {
        // $level_data = $this->Permission->confidence_level($task_detail['id']);

        $confidence_level = 'Not Set';
        $level_value = 0;
        $level_class = 'dark-gray';
        $level_arrow = 'notsetgrey';
        if(isset($e_levels['level_count']) && !empty($e_levels['level_count'])){
            $confidence_level = $e_levels['confidence_level'];
            $level_class = $e_levels['confidence_class'];
            $level_arrow = $e_levels['confidence_arrow'];
            $level_value = $e_levels['level'];
        }
        if($level_value > 0){
            $level_value_current = $level_value.'%';
        }else{
            $level_value_current = '';
        }
    }
?>
        <?php if($project_level || ($assiged && $assigned['reaction'] != 3) ) { ?>
        <div class="progress-col confidence-col ">
            <div class="progress-col-heading">
                    <span class="prog-h"><a data-target="#element_level" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'confidence', $taskID, 'admin' => FALSE ), TRUE ); ?>" href="#">CONFIDENCE <i class="arrow-down"></i></a></span>
                </div>
            <div class="progress-col-cont">
                <ul class="workcount confcounters">

                    <li class="<?php echo $level_class; ?> cost-tooltip" title="Confidence Level<br />
For This Task" data-target="#element_level" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'confidence', $taskID, 'admin' => FALSE ), TRUE ); ?>" ><?php echo $level_value_current; ?></li>
                    <span><i class="level-ts <?php echo $level_arrow; ?>"></i></span>
                </ul>
                <div class="proginfotext"><?php echo $confidence_level; ?></div>
            </div>
        </div>
        <?php } ?>



            <div class="progress-col work-owners-column">
                <div class="progress-col-heading">
                    <span class="prog-h">
                        <a href="#" class="open-team-tab">Team <i class="arrow-down"></i></a>
                    </span>
                </div>

                <div class="assign_wrapper">
                <div class="progress-col-cont">
                    <ul class="workcount taskcounters">
                        <?php
                        $owners = (!empty($taskOwnersTotal)) ? (($taskOwnersTotal == 1) ? $taskOwnersTotal : $taskOwnersTotal ) : '0';
                        $owners_tip = $owners." Owners";
                        if($owners == 1){
                            $owners_tip = $owners." Owner";
                        }

                        ?>
                        <?php $sharers =  (!empty($taskSharersTotal)) ? (($taskSharersTotal == 1) ? $taskSharersTotal : $taskSharersTotal) : '0';
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
                        <li class="eassignment">

                        <?php
                        //if( isset($task_detail['date_constraints']) && !empty($task_detail['date_constraints']) ) { ?>
                        <span class="<?php echo $assign_class; ?> cost-tooltip " title="<?php echo $assign_tip; ?>" data-toggle="modal" <?php if(($creator || $receiver || !$assigned) ) { ?> data-toggle="modal" data-remote="<?php echo Router::Url(array("controller" => "entities", "action" => "task_assignment", $taskID, 'admin' => false), true); ?>" data-target="#modal_task_assignment" <?php } ?> ></span>
                        <?php //} ?>

                        </li>


                    </ul>

                    <div class="proginfotext tipText" title="Your Role"><?php echo $taskRoleCurrent; //$total_users_text; ?></div>


                </div>
                <div class="assign_div">
                <?php
 
                     if($assigned && !empty($assigned['assigned_to']) && $assigned['reaction'] != 3)  { ?>

                        <span class="style-popple-icon-out ">
                                    <a class="style-popple-icon pophoverss" data-content="<div><p><?php echo $receiver_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"     data-remote="<?php echo SITEURL; ?>/shares/show_profile/<?php echo $assigned['assigned_to']; ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal">
                                        <img src="<?php echo $user_image; ?>" class=" tipText" title="<?php echo $receiver_name; ?>" align="left" width="28" height="28">
                                        <?php if($current_org['organization_id'] != $receiver_org){ ?>
                                            <i class="communitygray18 tipText community-g" title="Not In Your Organization" style="cursor: default;"></i>
                                        <?php } ?>
                                    </a>


                        </span>

                        <?php } ?>

                    </div>

                    </div>

            </div>


        <div class="progress-col">

            <?php
            // Task date progress bar
            $db_detail = $task_detail;

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
                            <span class="prog-h task-schedule">Schedule <i class="arrow-down"></i></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($first_percentage); ?>%</span>
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
                            <span class="prog-h task-schedule">Schedule <i class="arrow-down"></i></span> <span class="percent-text schedule-percent" title="Percentage of Schedule">100%</span>
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
                            <span class="prog-h task-schedule">Schedule <i class="arrow-down"></i></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($show_percentage); ?>%</span>
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
                            <span class="prog-h task-schedule">Schedule <i class="arrow-down"></i></span> <span class="percent-text"></span>
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
                            <span class="prog-h task-schedule">Schedule <i class="arrow-down"></i></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($first_percentage); ?>%</span>
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
                            <span class="prog-h task-schedule">Schedule <i class="arrow-down"></i></span> <span class="percent-text schedule-percent" title="Percentage of Schedule"><?php echo ceil($all_percentage); ?>%</span>
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
                    </div>
                    <div class="proginfotext">No Schedule</div>
                </div>
                <?php
            }

             ?>
        </div>


        <?php
//pr($effort_bar[0]);


        $effort_bar_total_hours = (isset($effort_bar[0]) && !empty($effort_bar[0]['ef']['total_hours'])) ? $effort_bar[0]['ef']['total_hours'] : 0;

        $effort_bar_completed_hours = (isset($effort_bar[0]) && !empty($effort_bar[0]['ef']['blue_completed_hours'])) ? $effort_bar[0]['ef']['blue_completed_hours'] : 0;


        $effort_bar_green_remaining_hours = (isset($effort_bar[0]) && !empty($effort_bar[0]['ef']['green_remaining_hours'])) ? $effort_bar[0]['ef']['green_remaining_hours'] : 0;

        $effort_bar_amber_remaining_hours = (isset($effort_bar[0]) && !empty($effort_bar[0]['ef']['amber_remaining_hours'])) ? $effort_bar[0]['ef']['amber_remaining_hours'] : 0;

        $effort_bar_red_remaining_hours = (isset($effort_bar[0]) && !empty($effort_bar[0]['ef']['red_remaining_hours'])) ? $effort_bar[0]['ef']['red_remaining_hours'] : 0;

        $effort_bar_remaining_hours = (isset($effort_bar[0]) && !empty($effort_bar[0]['ef']['remaining_hours'])) ? $effort_bar[0]['ef']['remaining_hours'] : 0;

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

		$total_other_per = $effort_bar_blue_percentage + $effort_bar_red_percentage + $effort_bar_green_percentage +  $effort_bar_amber_percentage;

		//pr($total_other_per );

        $remaining_color_tip = 'None';


		$remaining_red_color_tip = "Remaining: $effort_bar_red_remaining_hours Of $effort_bar_total_hours Hours Off Track ($effort_bar_red_percentage%)";


		$remaining_amber_color_tip = "Remaining: $effort_bar_amber_remaining_hours Of $effort_bar_total_hours Hours At Risk ($effort_bar_amber_percentage%)";

		$remaining_green_color_tip = "Remaining: $effort_bar_green_remaining_hours Of $effort_bar_total_hours Hours On Track ($effort_bar_green_percentage%)";

		$remaining_none_color_tip = "Remaining: $effort_bar_remaining_hours Of $effort_bar_total_hours Hours No Schedule ($effort_bar_none_percentage%)";


        $remaining_color_tip_blue = 'None';

        if(isset($effort_bar_blue_percentage) && !empty($effort_bar_blue_percentage)){
			$remaining_color_tip_blue = "Completed:  $effort_bar_completed_hours Of $effort_bar_total_hours Hours ($effort_bar_blue_percentage%)";
        }



        if($effort_bar_total_hours ==1){
            $effort_bar_total_hours_text = $effort_bar_total_hours.' Hr';
        }else{
            $effort_bar_total_hours_text = $effort_bar_total_hours.' Hrs';
        }

        $effort_changed_hours = (isset($effort_bar[0]) && !empty($effort_bar[0]['ef']['change_hours'])) ? $effort_bar[0]['ef']['change_hours'] : 0;

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

        }else if($effort_changed_hours == 0  && !empty($effort_bar[0]['ef']['total_hours'])){
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

						<?php if($total_other_per < 100){ ?>
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



    <?php if($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner'){ ?>


        <div class="asset_counter">
        <?php echo $this->element('../Entities/element_files/el_assets', array('project_id' => $project_id,'workspace_id' => $workspace_id,'taskID'=>$taskID)); ?>
        </div>

        <!-- COST -->
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
                <span class="percent-text tipText" title="Risks in Task"><?php echo $wsp_risks; ?></span>
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
        $('.reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip, .cost-percent, .disable.signedoff, .percent-text').tooltip({
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
            $('#task-header-tabs a[href="#task_team"]').tab('show');
        });
    })
</script>