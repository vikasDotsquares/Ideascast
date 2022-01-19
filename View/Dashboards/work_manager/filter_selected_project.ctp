<?php 
	$current_user_id = $this->Session->read('Auth.User.id');

 	// pr($filter_projects);
 	// pr($filter_users);
	
 	$project = getByDbIds('Project', $filter_projects, ['id', 'title', 'start_date', 'end_date','color_code']);
 	//pr($project);


 	if(isset($project) && !empty($project)){
		foreach ($project as $key => $value) { 
			$project_permit_type = $this->TaskCenter->project_permit_type( $value['Project']['id'], $current_user_id );

			$start_date = (isset($value['Project']['start_date']) && !empty($value['Project']['start_date'])) ? $this->TaskCenter->_displayDate_new($value['Project']['start_date'],'Y-m-d') : '';
			$end_date = (isset($value['Project']['end_date']) && !empty($value['Project']['end_date'])) ? $this->TaskCenter->_displayDate_new($value['Project']['end_date'],'Y-m-d') : '';

			?>

			<div class="data-block" data-title="<?php echo strip_tags(ucfirst($value['Project']['title'])); ?>" <?php if(isset($value['Project']['start_date']) && !empty($value['Project']['start_date'])){ ?> data-start="<?php echo $start_date; ?>" <?php } ?> <?php if(isset($value['Project']['end_date']) && !empty($value['Project']['end_date'])){ ?> data-end="<?php echo $end_date; ?>"<?php } ?>> 
 
				<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "index", $value['Project']['id']), true);?>" title="<?php echo strip_tags($value['Project']['title']);?>" class="data-block-title tipText">
				<?php 
				$title = $value['Project']['title'];
				// $t = (strlen($title)>24) ? substr($title,0,24).'...' : $title;
	       		echo strip_tags($title);  
	       		?>
	       		
				</a>
				<div class="data-block-in">
					<div class="data-block-sec border-<?php echo str_replace('panel-', '', $value['Project']['color_code']) ; ?>">
						<span>Start: 
						<?php 
							$start = $value['Project']['start_date'];
							if(empty($start)){
								echo 'N/A';
							}else{
								echo $this->TaskCenter->_displayDate_new($start,'d M, Y');
							}
						?>	
						</span>
						<span>End: 
						<?php 
							$end = $value['Project']['end_date'];
							if(empty($end)){
								echo 'N/A';
							}else{
								echo $this->TaskCenter->_displayDate_new($end,'d M, Y');
							}
						?>
							
						</span>
					</div>
					<?php if($project_permit_type){ ?>
					<div class="pull-right edit-date"><a data-updated="<?php echo $value['Project']['id']; ?>" data-type="project"  data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_project_date", $value['Project']['id']), true); ?>" data-original-title="Project Schedule" class="btn btn-default btn-xs calender_modal" href="#"><i class="btn btn-sm btn-default active">GN</i></a></div>
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
	
</script>