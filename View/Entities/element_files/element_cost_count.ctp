<?php
$estimated_cost = $this->ViewModel->element_cost($element_id, 1);	
$spend_cost = $this->ViewModel->element_cost($element_id, 2);	
$cost_url = SITEURL.'entities/task_list_el_date_cost/'.$element_id; 

$project_id = element_project($element_id);
$cky = $this->requestAction('/projects/CheckProjectType/' . $project_id . '/' . $this->Session->read('Auth.User.id'));
$cparams = $cky.':'.$project_id;

$costClass = "fa-manage-cost";
if( !empty($estimated_cost) || !empty($spend_cost)  ){
	$costClass = "fa-manage-cost-green"; 
	$tipTaskCost = "Task Costs"; ?>
	
	<span class="tipText btn btn-default bg-white btn-sm" style="padding: 4px 0px 6px 13px; margin-left: 2px;" data-title="<?php echo $tipTaskCost; ?>" data-toggle="modal" data-remote="<?php echo $cost_url; ?>"  data-target="#modal_cost_status" >
		<i class="<?php echo $costClass;?>"></i>
	</span>
	
<?php } else {
	$tipTaskCost = "No Task Cost"; ?>
	
	<span class="tipText btn btn-default bg-white btn-sm" style="padding: 4px 0px 6px 13px; margin-left:2px;" data-title="<?php echo $tipTaskCost; ?>" onclick="open_cost('<?php echo $cparams;?>')" >
		<i class="<?php echo $costClass;?>"></i>
	</span>
	
<?php }
?> 
