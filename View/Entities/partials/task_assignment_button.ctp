<?php
$element_assigned = element_assigned( $element_id );

$edata = getByDbId("Element", $element_id, ['date_constraints']);

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
		$assign_tip = "Unassigned <br /> Disengaged By ".$receiver_name;
	}
	else{
		if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
			$assign_tip = "Assigned to ".$receiver_name.'<br /> Schedule Acceptance Pending';
			$assign_class = 'not-react';
		}

	}
}
 ?>
 <?php //if(isset($edata['Element']['date_constraints']) && !empty($edata['Element']['date_constraints'])){ ?>
		 <span class="<?php echo $assign_class; ?> cost-tooltip " title="<?php echo $assign_tip; ?>" data-toggle="modal"   <?php if(($creator || $receiver || !$element_assigned)  ) { ?> data-toggle="modal" data-remote="<?php echo Router::Url(array("controller" => "entities", "action" => "task_assignment", $element_id, 'admin' => false), true); ?>" data-target="#modal_task_assignment" <?php } ?> ></span>
<?php //} ?>
<script>
$(function(){

$('.cost-tooltip').tooltip({
'placement': 'top',
'container': 'body',
'html': true
})

})
</script>