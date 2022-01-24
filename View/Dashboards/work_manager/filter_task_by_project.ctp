<?php 
	$current_user_id = $this->Session->read('Auth.User.id');
	$all_elements = $this->ViewModel->project_elements(array_values($filter_projects)); 
	$element_keys = (isset($all_elements) && !empty($all_elements)) ? Set::extract($all_elements, '{n}/element/id') : null;

	if(isset($filter_users) && $filter_users != null){
		$element_keys = $this->TaskCenter->usersElements(array_values($filter_users), array_values($filter_projects) );
	}
	else{
		if(isset($first) && !empty($first)){
			$all_elements = $this->TaskCenter->userElements($current_user_id, array_values($filter_projects));
			$element_keys = (isset($all_elements) && !empty($all_elements)) ? $all_elements : null;
		}
		else{
			$ele = $this->ViewModel->project_elements(array_values($filter_projects));
			$element_keys = Set::extract($ele, '{n}/element/id');
		}
	}

	$els = getByDbIds('Element', $element_keys, ['id', 'title', 'start_date', 'end_date', 'area_id']);
			
	
	
if(isset($els) && !empty($els)){
	foreach ($els as $key => $value) {
		$pid = element_project($value['Element']['id']);

		$element_permit_type = $this->TaskCenter->element_permit_type( $pid, $current_user_id, $value['Element']['id'] );

		$project = getByDbId('Project', $pid, ['title', 'id']);

		$start_date = (isset($value['Element']['start_date']) && !empty($value['Element']['start_date'])) ? $this->TaskCenter->_displayDate_new($value['Element']['start_date'],'Y-m-d') : '';
		$end_date = (isset($value['Element']['end_date']) && !empty($value['Element']['end_date'])) ? $this->TaskCenter->_displayDate_new($value['Element']['end_date'],'Y-m-d') : '';

		?>
		<div class="data-block" data-title="<?php echo strip_tags(ucfirst($value['Element']['title'])); ?>" <?php if(isset($value['Element']['start_date']) && !empty($value['Element']['start_date'])){ ?> data-start="<?php echo $start_date; ?>" <?php } ?> <?php if(isset($value['Element']['end_date']) && !empty($value['Element']['end_date'])){ ?> data-end="<?php echo $end_date; ?>"<?php } ?>>
		

			<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $value['Element']['id']), true); ?>#tasks" data-content="<div class='details'><p class='pr-title'><span>Project:</span> <?php echo $project['Project']['title']; ?></p><p class='inner-title'><?php echo strip_tags($value['Element']['title']); ?></p></div>" class="data-block-title pop">
			<?php 
			$title = $value['Element']['title'];
			// $t = (strlen($title)>24) ? substr($title,0,24).'...' : $title;
       		echo strip_tags($title);  
       		?>
				
			</a>
			<?php $element_status = element_status($value['Element']['id']); ?>
			<div class="data-block-in">
				<div class="data-block-sec cell_<?php echo $element_status; ?>">
					<span>Start: <?php echo (empty($value['Element']['start_date'])) ? 'N/A' : $this->TaskCenter->_displayDate_new($value['Element']['start_date'],'d M, Y');  ?>	
					</span>
					<span>End: <?php echo (empty($value['Element']['end_date'])) ? 'N/A' : $this->TaskCenter->_displayDate_new($value['Element']['end_date'],'d M, Y'); ?> 
					</span>
				</div> 
				<?php if($element_permit_type) { ?>
				<div class="pull-right edit-date"><a data-updated="<?php echo $value['Element']['id']; ?>" data-type="element" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $value['Element']['id']), true); ?>" data-original-title="Task Schedules" class="btn btn-default btn-xs calender_modal" href="#"><i class="ico_cal"></i></a></div>
				<?php } ?>
			</div>
		</div>
		<?php
	}
}else{
		echo '<div class="no-row-found">No Record Found</div>';
	}

?>
<script type="text/javascript">
	$(function(){
		$('.data-block-title').ellipsis_word();
	})

	
	$('.data-block-title.pop').popover({
        placement : 'top',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
	
</script>