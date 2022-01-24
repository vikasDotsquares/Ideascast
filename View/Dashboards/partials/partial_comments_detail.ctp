<?php 
$current_user_id = $this->Session->read('Auth.User.id');

if( isset($projects) && !empty($projects) ) {
	
	$userID = null;
	if( isset($userId) && !empty($userId) ) {
		$userID = $userId;
	}
	
	$user_project = $projects['UserProject'];
	$project = $projects['Project'];
	
	
	/***** Get Project Owner ******/
	$p_permission = $this->Common->project_permission_details($project['id'], $userID);
	
	$user_project = $this->Common->userproject($project['id'], $userID);
	$onr = 0;
	
	if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) ) {
		$onr = 1;
	}
	else {
		$onr = 0;
	}
	
	
	$permit_owner = 0;
	$permit_id = 0;
	
	if( $slug == 'shared_projects') {
		$permit_detail = ( isset($permit_id) && !empty($permit_id) ) ? getByDbId('ProjectPermission', $permit_id) : null;
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];
		$permit_owner = 1;
	} 
	else if( $slug == 'received_projects') {
		$permit_detail = ( isset($permit_id) && !empty($permit_id) ) ? getByDbId('ProjectPermission', $permit_id ) : null;
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];
		$permit_owner = $onr;
	} 
	else if( $slug == 'group_received_projects') {
		$group_detail = ( isset($permit_id) && !empty($permit_id) ) ? getByDbId('ProjectGroup', $permit_id ) : null;
		
		$conditions = [
		'ProjectPermission.project_group_id' => $permit_id,
		'ProjectPermission.user_project_id' => $group_detail['ProjectGroup']['user_project_id']
		];
		$permit_detail = $this->ViewModel->project_permission_detail($conditions );
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];
		
		$grp_id = $this->Group->GroupIDbyUserID($project['id'], $current_user_id);
		
		if(isset($grp_id) && !empty($grp_id)){
			
			$group_permission = $this->Group->group_permission_details($project['id'],$grp_id); 
			if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
				$onr = $group_permission['ProjectPermission']['project_level'];
			}
			else {
				$onr = 0;
			}
			
		}
		$permit_owner = $onr;
		
	}
	else if( $slug == 'propagated_projects') {
		$permit_detail = $this->Common->project_permission_details($project['id'], $this->Session->read('Auth.User.id') );
		$permit_owner = $permit_detail['ProjectPermission']['project_level'];
		$permit_owner = $onr;
	}
	else {
		$permit_owner = 1;
	}
	
	
?>

		<div class="bottom-data clearfix">
		
			<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-zero">
			
				<?php //echo $this->element('../Dashboards/partials/project_tasks', ['project' => $project['id'], 'slug' => $slug, 'userId' => $userID]); ?>
				
			</div>
			
			<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-one">
			
				<?php //echo $this->element('../Dashboards/partials/project_blogs', ['project' => $project['id'], 'slug' => $slug, 'userId' => $userID]); ?>
				
			</div>
			
			<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-two">
			
				<?php //echo $this->element('../Dashboards/partials/project_comments', ['project' => $project['id'], 'slug' => $slug, 'userId' => $userID, 'permit_owner' => $permit_owner]); ?>
				
			</div>
			<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-three">
				
				<?php //echo $this->element('../Dashboards/partials/project_assets', ['project' => $project['id'], 'slug' => $slug, 'userId' => $userID]); ?>
				
			</div>
			
		</div>
		
<?php } ?>

<script type="text/javascript" >
$(function(){
		
/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
	 */
	
})
</script>