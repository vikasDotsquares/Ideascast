<?php 
	$current_user_id = $this->Session->read('Auth.User.id');

 	// pr($filter_projects);
 	// pr($filter_users);
 	$userProjectId = null;
	if(isset($filter_projects) &&  !empty($filter_projects)){

 		foreach ($filter_projects as $key => $value) {

 			$userProjectId[] = project_upid($value);

 		}

$wsp_ids = $this->TaskCenter->usersWorkspaces($filter_users, $filter_projects );
// pr($wsp_ids);


 		// $wsp_permit = $this->ViewModel->project_workspace_sharing($userProjectId, $filter_users);
 		// $wsp_keys = (isset($wsp_permit) && !empty($wsp_permit)) ? Set::extract($wsp_permit, '{n}/WorkspacePermission/project_workspace_id') : null;

 		// $wsp_keys = isset($wsp_keys) && !empty($wsp_keys) ? array_unique($wsp_keys) : null;
 		// $wsp_ids = workspace_2_pwid($wsp_keys);
 		// pr($wsp_ids);
 		$workspaces = getByDbIds('Workspace', $wsp_ids, ['id', 'title', 'start_date', 'end_date', 'color_code']);
 		// pr($workspaces);
 		
 	}

	if(isset($workspaces) && !empty($workspaces)){
		foreach ($workspaces as $key => $value) {
			$pid = workspace_pid($value['Workspace']['id']);

			$workspace_permit_type = $this->TaskCenter->workspace_permit_type( $pid, $current_user_id, $value['Workspace']['id'] );

			$project = getByDbId('Project', $pid, ['title','id']);

			$start_date = (isset($value['Workspace']['start_date']) && !empty($value['Workspace']['start_date'])) ? $this->TaskCenter->_displayDate_new($value['Workspace']['start_date'],'Y-m-d') : '';
			$end_date = (isset($value['Workspace']['end_date']) && !empty($value['Workspace']['end_date'])) ? $this->TaskCenter->_displayDate_new($value['Workspace']['end_date'],'Y-m-d') : '';

			?>
			<div class="data-block" data-title="<?php echo strip_tags(ucfirst($value['Workspace']['title'])); ?>" <?php if(isset($value['Workspace']['start_date']) && !empty($value['Workspace']['start_date'])){ ?> data-start="<?php echo $start_date; ?>" <?php } ?> <?php if(isset($value['Workspace']['end_date']) && !empty($value['Workspace']['end_date'])){ ?> data-end="<?php echo $end_date; ?>"<?php } ?>>
			
				<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "manage_elements", $project['Project']['id'],$value['Workspace']['id']), true);; ?>" data-content="<div class='details'><p class='pr-title'><span>Project:</span> <?php echo $project['Project']['title']; ?></p><p class='inner-title'><?php echo strip_tags($value['Workspace']['title']); ?></p></div>" class="data-block-title pop">
				<?php 
				$title = $value['Workspace']['title'];
				// $t = (strlen($title)>24) ? substr($title,0,24).'...' : $title;
	       		echo strip_tags($title);  
	       		?>
				
				</a>
				<div class="data-block-in">
					<!-- <div class="data-block-sec border-<?php echo str_replace('bg-', '', $value['Workspace']['color_code']) ; ?>"> -->
					<div class="data-block-sec wsp_<?php echo workspace_status($value['Workspace']['id']); ?> ">
						<span>Start: 
						<?php 
						$start = $value['Workspace']['start_date'];
						if(empty($start)){
							echo 'N/A';
						}else{
							echo $this->TaskCenter->_displayDate_new($start,'d M, Y');
						}
						
						
						?>	
						</span>
						<span>End: 
						<?php 
						$end = $value['Workspace']['end_date'];
						if(empty($end)){
							echo 'N/A';
						}else{
							echo $this->TaskCenter->_displayDate_new($end,'d M, Y');
						}
						
						
						?>
							
						</span>
					</div>
					<?php /*if($workspace_permit_type){ ?>
					<div class="pull-right edit-date"><a data-updated="<?php echo $value['Workspace']['id']; ?>" data-type="workspace"  data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_ws_date", $project['Project']['id'], $value['Workspace']['id']), true); ?>" data-original-title="Workspace Schedule" class="btn btn-default btn-xs calender_modal" href="#"><i class="ico_wsp"></i></a></div>
					<?php }*/ ?>
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
		// setTimeout(function(){
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