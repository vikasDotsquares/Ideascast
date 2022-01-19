<?php
//$eldata = getByDbId('Element', $element_id);

//$eldata['Element'] = $eldata;
$current_user = $this->Session->read('Auth.User.id');
$element_data = $eldata['Element'];
$task_details = $eldata['Element'];
$taskID = $task_details['id'];
//$workspace_id =  $this->data['Area']['workspace_id'];
$project_id = element_project($eldata['Element']['id']);
$workspace_id = element_workspace($eldata['Element']['id']);

$wldata = getByDbId('Workspace', $workspace_id);

//pr($wldata);
//$workspace_id =  $this->data['Area']['workspace_id'];
$project_type = project_type($project_id, $current_user);
$costURL = SITEURL.'costs/index/'.$project_type.':'.$project_id;
$ganttURL = '';
//task role
$taskRole = taskRole($taskID, $current_user);


if(isset($eldata['Element']['sign_off']) && !empty($eldata['Element']['sign_off'])){
	$ele_signoff = true;
}else{
	$ele_signoff = false;
}

// owner/sharer
$taskOwnersTotal = $this->Permission->taskOwners($taskID, 1);
$taskSharersTotal = $this->Permission->taskSharers($taskID, 1);

$wsp_risks = $pending_high_risk = $pending_severe_risk = $sign_high_risk = $sign_severe_risk = 0;


$wspTasks = [$taskID];
if($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner'){

	// Cost
	$totalestimatedcost =  $this->Permission->wsp_element_cost($wspTasks, 'estimated_cost');
	$totalspendcost = $this->Permission->wsp_element_cost($wspTasks, 'spend_cost');
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
// e($spendcost, 1);
	$max_budget = max( $estimatedcost, $spendcost );
	$estimate_used = ($max_budget > 0) ? ( ($estimatedcost / $max_budget) * 100 ) : 0;
	$spend_used = ($estimatedcost > 0) ? ( ( $spendcost / $estimatedcost) * 100 ) : 0;

	$cost_status_text = $this->Permission->wsp_cost_status_text( $estimatedcost, $spendcost);

	// risk counters
	$wspTasks = [$taskID];
	// if there are any task in the wsp
	$user_project_risks = user_project_risks($project_id, $current_user);
	if(isset($user_project_risks) && !empty($user_project_risks)){
		$wsp_risks = wsp_risks($user_project_risks, $project_id, $wspTasks);
		$pending_high_risk = wsp_pending_risks($user_project_risks, $project_id, $wspTasks, 'high');
		$pending_severe_risk = wsp_pending_risks($user_project_risks, $project_id, $wspTasks, 'severe');
		$sign_high_risk = wsp_signedoff_risks($user_project_risks, $project_id, $wspTasks, 'high');
		$sign_severe_risk = wsp_signedoff_risks($user_project_risks, $project_id, $wspTasks, 'severe');
	}
}
?>
<?php
	$project_level = 0;

	if($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner'){
		$project_level = 1;
	}

	$wsp_signoff = $this->ViewModel->workspace_signoff($workspace_id);
	$signoff_msg = '';
	if($wsp_signoff){
		$signoff_msg = 'This Workspace is signed off';
	}

	$disable_risk = '';
	$disable_risk_tip ='';
	$disable_risk_cursor ='';
	if( isset($eldata['Element']['sign_off']) && !empty($eldata['Element']['sign_off']) ){
		$disable_risk = 'disable';
		$disable_risk_tip = 'Task Is Signed Off';
		$disable_risk_cursor ='cursor:default !important;';
		$signoff_msg = 'This Task is signed off';
	}

	// TASK ASSIGNMENT
	$element_assigned = element_assigned( $eldata['Element']['id'] );

	$creator = $receiver = false;
	$assign_class = 'not-avail';
	$assign_tip = 'Unassigned';
	if($element_assigned) {
	$creator = ($element_assigned['ElementAssignment']['created_by'] == $this->Session->read('Auth.User.id')) ? true : false;
	$receiver = ($element_assigned['ElementAssignment']['assigned_to'] == $this->Session->read('Auth.User.id')) ? true : false;

	$creator_detail = get_user_data($element_assigned['ElementAssignment']['created_by']);
	$creator_name = $creator_detail['UserDetail']['full_name'];

	$receiver_detail = get_user_data($element_assigned['ElementAssignment']['assigned_to']);
	$receiver_name = $receiver_detail['UserDetail']['full_name'];

	if($element_assigned['ElementAssignment']['reaction'] == 1) {
		$assign_class = 'accepted';
		$assign_tip = "Assigned to ".$receiver_name . '<br /> Schedule Accepted';
	}
	else if($element_assigned['ElementAssignment']['reaction'] == 2) {
		$assign_class = 'not-accept-start';
		$assign_tip = "Assigned to ".$receiver_name . '<br /> Schedule Not Accepted';
	}
	else if($element_assigned['ElementAssignment']['reaction'] == 3) {
		$assign_class = 'disengage';
		$assign_tip = "Unassigned <br /> Disengaged By ".$creator_name;
	}
	else{
		if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
			$assign_tip = "Assigned to ".$receiver_name.'<br /> Schedule Acceptance Pending';
			$assign_class = 'not-react';
		}

	}
}
if($taskRole == 'Creator'){
	$creator = true;
}

	// DELETE PERMISSION
	$elementPermission = $this->Common->element_manage_permission($element_data['id'], $project_id, $current_user);

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

	$cost_url = SITEURL.'entities/task_list_el_date_cost/'.$eldata['Element']['id'];
	$estimated_cost = $this->ViewModel->element_cost($eldata['Element']['id'], 1);
	$spend_cost = $this->ViewModel->element_cost($eldata['Element']['id'], 2);

	// Dependencies
	$tiptext = 'Critical And Relationships';
	$critalandrelatioscss = '';
	$criticalandrelationurl = '';
	$crmodalcls ='notavail';

	if( isset( $eldata['Element']['id'] ) && !empty($eldata['Element']['id']) ){
		$criticalstatus = $this->Common->element_criticalStaus($eldata['Element']['id']);

		$ele_dep_rel_tot = ( isset($criticalstatus['ElementDependancyRelationship']) && !empty($criticalstatus['ElementDependancyRelationship']) ) ? count($criticalstatus['ElementDependancyRelationship']) : 0;

		if( isset($criticalstatus['ElementDependency']['is_critical']) && $criticalstatus['ElementDependency']['is_critical'] > 0 ){
			//$tiptext = 'Dependencies and Critical';
			$tiptext = 'Priority Task, Task Dependencies: '.$ele_dep_rel_tot;
			$critalandrelatioscss = 'television-arrow-red';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$eldata['Element']['id'];

			// added to hide modal box when Dependencies not exists
			if( isset($criticalstatus['ElementDependancyRelationship']) && $ele_dep_rel_tot > 0 )
				$crmodalcls ='modal';
			else
				$crmodalcls ='modal';

		} else if( isset($criticalstatus['ElementDependancyRelationship']) && $ele_dep_rel_tot > 0 && isset($criticalstatus['ElementDependency']['is_critical']) && $criticalstatus['ElementDependency']['is_critical'] > 0  ){

			$tiptext = 'Priority Task, Task Dependencies: '.$ele_dep_rel_tot;
			$critalandrelatioscss = 'television-arrow';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$element_id;
			$crmodalcls ='modal';

		} else if( isset($criticalstatus['ElementDependancyRelationship']) && $ele_dep_rel_tot > 0 ){
			$tiptext = 'Task Dependencies: '.$ele_dep_rel_tot;
			$critalandrelatioscss = 'television-arrow';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$eldata['Element']['id'];
			$crmodalcls ='modal';
		} else /*if( !empty($eldata['Element']['start_date']) && !empty($eldata['Element']['end_date']) )*/ {

			$tiptext = 'Task Dependencies';
			$criticalandrelationurl = SITEURL.'entities/element_dependancy_critical/'.$eldata['Element']['id'];
			$critalandrelatioscss = 'television-arrow-gray';
			$crmodalcls ='modal';

		} /*else {

			$tiptext = '';
			$criticalandrelationurl = '';
			$critalandrelatioscss = '';
			$crmodalcls ='';
		}*/

    }

    $sign_off_flag = false;
    // e('----'.$receiver);
    $edit_sign_off = false;
    if($creator || $receiver) $edit_sign_off = true;
?>

<div class="header-link-top-right">
	 <span class="opt-btn ">
		<?php if($edit_sign_off){ ?>
<div class=" signoff_reopen" style="margin: 0; width:auto; ">

        <?php
		$assignmentElement = user_assigned($this->Session->read('Auth.User.id'), $eldata['Element']['id']);
		// pr($is_owner);
		// pr($project_level);
		// pr($is_editaa);
		// pr($assignmentElement);
		if ((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($is_editaa) && $is_editaa > 0) || (isset($assignmentElement) && !empty($assignmentElement) )) {
			$dsss = '';
		} else {
			$dsss = 'disabled';
		}
		// pr($dsss);


        if (($date_status != STATUS_NOT_SPACIFIED && $date_status != STATUS_NOT_STARTED && $date_status != STATUS_COMPLETED) ) {
            ?><span id="dsptest" style="display:none"><?php echo $this->Common->singoffelement($eldata['Element']['id']); ?></span>

            <?php
			if ($this->Common->singoffelement($eldata['Element']['id']) == '1'  ) {

			// Gate Enabled will be use for sign-off  =======================
			$el_gated = false;
			if(GATE_ENABLED == true){
				$el_gated = $this->Common->element_dependencies_gated($this->Session->read('Auth.User.id'),$eldata['Element']['id']);



				//===============================================================
				if( !empty($el_gated) && $el_gated['success'] == true ){
					$sign_off_flag = true;
			?>

				<a class="<?php echo $dsss; ?> tipText" title="Sign Off Task" data-toggle="modal" data-target="#signoff_comment_box" data-value="1" data-remote="<?php echo SITEURL;?>entities/tasks_signoff/<?php echo $eldata['Element']['id']; ?>" data-id="<?php echo $eldata['Element']['id']; ?>" ><i class="signoffblack"></i></a>

			<?php } else {

					if( isset($el_gated['element']) && !empty($el_gated['element']) ){
						$sign_off_flag = true;
				?>
					<a data-original-title="Sign-off Task" class="<?php echo $dsss; ?>   tipText convertbtn" data-target="#modal_signofftask" data-toggle="modal" data-remote="<?php echo SITEURL;?>entities/signoff_task/<?php echo $eldata['Element']['id']; ?>" ><i class="signoffblack"></i> </a>

					<!-- Do not delete below sign off button has class changesignoff -->
					<a class=" <?php echo $dsss; ?>    tipText hide changesignoff" title="Sign Off Task" data-toggle="modal" data-target="#signoff_comment_box" data-value="1" data-remote="<?php echo SITEURL;?>entities/tasks_signoff/<?php echo $eldata['Element']['id']; ?>" data-id="<?php echo $eldata['Element']['id']; ?>" ><i class="signoffblack"></i></a>

				<?php } else {
					$sign_off_flag = true;
					?>

					<!-- Do not delete below sign off button has class convertbtn -->
					<a data-original-title="Sign-off Task" class="  <?php echo $dsss; ?> tipText convertbtn hide" data-target="#modal_signofftask" data-toggle="modal" data-remote="<?php echo SITEURL;?>entities/signoff_task/<?php echo $eldata['Element']['id']; ?>" ><i class="signoffblack"></i> </a>

					<a class="  <?php echo $dsss; ?> tipText changesignoff" title="Sign Off Task" data-toggle="modal" data-target="#signoff_comment_box" data-value="1" data-remote="<?php echo SITEURL;?>entities/tasks_signoff/<?php echo $eldata['Element']['id']; ?>" data-id="<?php echo $eldata['Element']['id']; ?>" ><i class="signoffblack"></i>  </a>

				<?php } ?>

            <?php }
			} else {
				$sign_off_flag = true;
				?>
				<a class="  <?php echo $dsss; ?> tipText" title="Sign Off Task" data-toggle="modal" data-target="#signoff_comment_box" data-value="1" data-remote="<?php echo SITEURL;?>entities/tasks_signoff/<?php echo $eldata['Element']['id']; ?>" data-id="<?php echo $eldata['Element']['id']; ?>" ><i class="signoffblack"></i> </a>
			<?php }
			} else { $sign_off_flag = true; ?>

                 <a href="#" title="Sign Off Task" class="tipText element-sign-off-restrict"><i class="signoffblack"></i> </a>

            <?php } ?>

        <?php
    	}else if($date_status == STATUS_NOT_STARTED){
    	?>
    		<a href="#" title="Task not started" class="tipText element-no-schedule"><i class="signoffblack"></i> </a>

        <?php
    	}else if($date_status == STATUS_NOT_SPACIFIED){
    	?>
    		<a href="#" title="Task not scheduled" class="tipText element-no-schedule"><i class="signoffblack"></i> </a>

        <?php
        }else if ($date_status == STATUS_COMPLETED ) {
            //pr($wldata['Workspace']['sign_off']);

				$flipclass = '';

				$reopen_disabled = '';
				if( isset($signoff_comment) && $signoff_comment != 0 ){
					$flipclass ='fa-rotate-180';
					$sign_off_flag = true;
                ?>
                <a href="#" title="Click to see Comment and Evidence" class="<?php echo $reopen_disabled; ?> disableS tipText reopen-signoff task_evidence" data-toggle="modal" data-target="#signoff_comment_show" data-remote="<?php echo SITEURL;?>entities/show_signoff/<?php echo $eldata['Element']['id']; ?>" ><i class="signoffblack"></i> </a>
				<?php }



            if (isset($wldata['Workspace']['sign_off']) && $wldata['Workspace']['sign_off'] == 1) {
                $reopen_disabled = 'disable';
                $sign_off_flag = true;
                ?>
                <a href="#" data-msg="Cannot reopen this Task because the Workspace is signed off."  class="tipText  element-sign-off-restrict_Pro disable"  data-original-title="Reopen Task" ><i class="reopenblack"></i> </a>
         <?php
            } else { $sign_off_flag = true; ?>

				<a href="#"  class="<?php echo $reopen_disabled; echo $dsss; ?>  tipText  element-sign-off" data-msg="Are you sure you want to reopen this Task?" data-toggle="confirmation" data-header="Confirmation" data-value="0" data-id="<?php echo $eldata['Element']['id']; ?>"  data-original-title="Reopen Task" ><i class="reopenblack"></i> </a>
            <?php }
        } ?>
    </div>
    <?php }else{ ?>
    <div class=" signoff_reopen" style="margin: 0; width:auto;">
    	<a href="#" title="" class="tipText element-no-schedule"><i class="signoffblack"></i> </a>
    	<?php if ($date_status == STATUS_COMPLETED ) { ?>
    		<a href="#"  class="tipText element-no-schedule" ><i class="reopenblack"></i> </a>
    	<?php } ?>
    </div>
    <?php } ?>

	</span>
	<span class="hlt-sep <?php if(!$sign_off_flag && $date_status != STATUS_NOT_SPACIFIED){ ?> sec-icon-set <?php } ?>">
		<?php $element_assigned = element_assigned($taskID);
		$assiged = false;
		if(isset($element_assigned) && !empty($element_assigned)){
			if($element_assigned['ElementAssignment']['assigned_to'] == $current_user){
				$assiged = true;
			}
		} ?>

		<?php if(!empty($project_level) || ($assiged && $element_assigned['ElementAssignment']['reaction'] != 3) ){ ?>
			<a data-toggle="modal" class="h-common-btn wssb tipText" title="Confidence Level" href="#" data-target="#element_level" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'confidence', $taskID, 'admin' => FALSE ), TRUE ); ?>" >
				<i class="levelblack"></i>
			</a>
		<?php } ?>

		<?php if(!empty($project_level)){ ?>

			<?php if( !empty($estimated_cost) || !empty($spend_cost) ){
			$tipTaskCost = "Task Costs"; ?>
				<a class="h-common-btn wssb tipText" href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'index', $project_id, 'tab' => 'cost', 'admin' => FALSE ), TRUE ); ?>" title="<?php echo $tipTaskCost; ?>" >
					<i class="costsblack"></i>
				</a>
			<?php } else {
				$tipTaskCost = "Task Costs";
				//$project_type = project_type($project_id, $current_user);
				$cparams = $project_type.':'.$project_id;
				$url = Router::Url(array("controller" => "costs", "action" => "index", $project_type => $project_id, 'admin' => false), true);
			 ?>
			 	<a class="h-common-btn wssb tipText " href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'index', $project_id, 'tab' => 'cost', 'admin' => FALSE ), TRUE ); ?>" title="<?php echo $tipTaskCost; ?>" >
					<i class="costsblack"></i>
				</a>
			<?php } ?>

			<?php if( $criticalandrelationurl != "" && !empty($critalandrelatioscss)  ){ ?>
				<a data-toggle="modal" class="h-common-btn wssb tipText" href="#" title="<?php echo $tiptext; ?>"  data-toggle="<?php echo $crmodalcls; ?>" data-remote="<?php echo $criticalandrelationurl;?>"  data-target="#modal_task_status">
					<i class="dependencyblack"></i>
				</a>
			<?php } ?>
		<?php } ?>

		<a  class="h-common-btn wssb tipText" href="#" title="Task Assignment " <?php if($creator || $receiver || !$element_assigned) { ?> data-toggle="modal" data-remote="<?php echo Router::Url(array("controller" => "entities", "action" => "task_assignment", $element_data['id'], 'admin' => false), true); ?>" data-target="#modal_task_assignment" <?php } ?> >
			<i class="assignblack"></i>
		</a>

		<?php if(!empty($project_level)){ ?>
			<?php if(empty($element_data['sign_off'])){ ?>
				<a data-toggle="modal" class="h-common-btn wssb tipText" href="#" data-target="#modal_medium" title="Share Task" data-remote="<?php echo Router::Url(array("controller" => "entities", "action" => "quick_share", $project_id, $element_data['id'], 'admin' => false), true); ?>">
					<i class="share-icon"></i>
				</a>
			<?php } else if(!empty($element_data['sign_off'])){ ?>
				<a data-toggle="modal" class="h-common-btn wssb tipText disable" href="#" title="<?php echo $signoff_msg; ?>">
					<i class="share-icon"></i>
				</a>
			<?php } ?>
		<?php } ?>

	</span>

	<?php if( empty($element_data['sign_off']) ) {
	    if ( (isset($project_level) && $project_level == 1) || (isset($is_editaa) && $is_editaa > 0) || (isset($is_delete_shares) && $is_delete_shares > 0 ) ) { ?>


 		<span class="hlt-sep">
			<a href="#" class="edit-button h-common-btn tipText anchor-update-task" title="Edit Task" data-toggle="modal" data-target="#popup_model_box" data-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "update_task", $eldata['Element']['id'], 'admin' => FALSE ), true ); ?>"><i class="edit-icon"></i> </a>

			<a class="h-common-btn tipText delete-an-item" title="Delete Task" href="#" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "delete_an_item", $eldata['Element']['id'], 1, 'admin' => FALSE ), true ); ?>"><i class="deleteblack"></i> </a></span>
	<?php }

	} ?>

</div>
<style type="text/css">
	.element-no-schedule, .signoff_reopen .disabled {
	    opacity: 0.5;
	}
</style>
<script>
$(function(){

$('.cost-tooltip').tooltip({
'placement': 'top',
'container': 'body',
'html': true
})

})
</script>