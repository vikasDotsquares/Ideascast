<?php

$ws_exists = true;
$ws_count = $prj_count = 0;
if (isset($menu_project_id) && !empty($menu_project_id)) {
    // echo $menu_project_id;
    $prj_count = $this->ViewModel->user_project_count();
    $ws_count = $this->ViewModel->project_workspace_count($menu_project_id);
    if (empty($ws_count)) {
        $ws_exists = false;
    }
}
?>
<section class="content-header clearfix">

    <div class=" pull-right">
        <!-- Project Options -->
		<div class="btn-group action">				
				<?php
				$projid = 0;
				if (isset($project_id) && !empty($project_id)) {
					$projid = $project_id;
				} else if (isset($_sidebarProjectId) && !empty($_sidebarProjectId)) {
					$projid = $_sidebarProjectId;
				}
				else if(isset($this->params['named']['project']) && !empty($this->params['named']['project'])){
				$projid = $this->params['named']['project'];
				}
				?>
				<?php
				if (isset($project_id) && !empty($project_id)) {
					$uusde = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
					$pp_perm = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id')); 
						
					//********************* More Button ************************ 
					echo $this->element('more_button', array('project_id' => $projid, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'task_list' ));

				} ?>
		</div>
    </div>
</section>