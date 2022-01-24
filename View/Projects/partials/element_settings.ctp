<?php

$project_id = $workspace_id = null;

if( isset($this->params['pass']) && !empty($this->params['pass'])) {
	$project_id = (isset($this->params['pass'][0]))
						? $this->params['pass'][0]
						: (
							( isset($menu_project_id) && !empty($menu_project_id))
								? $menu_project_id
								: null
						);
	$workspace_id = (isset($this->params['pass'][1]))
								? $this->params['pass'][1]
								: null;
}

$participants = $participants_owners = $participantsGpOwner = $participantsGpSharer = [];


$projectwsp_id = workspace_pwid($project_id ,$workspace_id);


$owner = $this->Common->ProjectOwner( $project_id, $this->Session->read('Auth.User.id') );
// pr($owner);
$owner_id = isset($owner) ? $owner['UserProject']['user_id'] : 0;

$participants = wsp_participants( $project_id,$projectwsp_id, $owner['UserProject']['user_id']);

$participants_owners = array_filter(participants_owners( $project_id, $owner['UserProject']['user_id'] ));

$i = 0;
foreach($participants_owners as $nom){

	 if($owner_id != $nom &&  $nom !=''){
		$i++;
	 }

}


//pr($participants_owners);

$participantsGpOwner = participants_group_owner( $project_id );

$participantsGpSharer = wsp_grps_sharer( $project_id ,$projectwsp_id);



$participants = isset($participants) ? array_filter($participants) : $participants;
$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

$all = [];
if( isset($participants) && !empty($participants) ) {
	$all = array_merge($all, $participants);
}
if( isset($participants_owners) && !empty($participants_owners) ) {
	$all = array_merge($all, $participants_owners);
}
if( isset($participantsGpOwner) && !empty($participantsGpOwner) ) {
	$all = array_merge($all, $participantsGpOwner);
}
if( isset($participantsGpSharer) && !empty($participantsGpSharer) ) {
	$all = array_merge($all, $participantsGpSharer);
}



$total = 0;
$total = count(array_unique($all));


 ?>

<section class="content-header clearfix sb_blog_parent">

	<div class="pull-left project-detail">
		<a class="bg-blue sb_blog" data-target="#modal_people" style="margin-left:0;" data-toggle="modal" href="<?php echo SITEURL ?>projects/wsp_people/<?php echo $projectwsp_id; ?>/<?php echo $project_id; ?>">People on Workspace <?php  echo $total; ?></a>
	</div>
			<?php
			$toc = 'bg-green';

			$total_elements = workspace_elements($data['workspace']['Workspace']['id'], true, false);
			$total_completed = workspace_elements($data['workspace']['Workspace']['id'], true, true);

			$percent = 0;
			if( $total_elements > 0 ) {
				$percent = round((( $total_completed/$total_elements ) * 100), 0, 1);
			}
			if(isset($data['workspace']['Workspace']['sign_off']) && !empty($data['workspace']['Workspace']['sign_off'])){
				$percent = 100;
				$toc = 'bg-red';
			}


			 $projects = project_detail($this->params['pass']['0']);

			if(isset($projects['rag_status']) && !empty($projects['rag_status'])){

			  if($projects['rag_status'] == 1){
				$toc = 	'bg-red';
			  }else if($projects['rag_status']==2){
				$toc = 	'bg-yellow';

			  }else if($projects['rag_status'] ==3){
				$toc = 	'bg-green';
			  }

			}


			$total_elem = 0;
			if(isset($total_elements) && ($total_elements > 0)){
			$total_elem = $total_elements - $total_completed;
			}


			   if( isset($project_id) && !empty($project_id) ){


				 $wsp_permission = $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($this->params['pass']['1']),$this->params['pass']['0'],$this->Session->read('Auth.User.id'));

				 //pr($wsp_permission);

				 $p_permission = $this->Common->project_permission_details($this->params['pass']['0'],$this->Session->read('Auth.User.id'));

				 $user_project = $this->Common->userproject($this->params['pass']['0'],$this->Session->read('Auth.User.id'));


		if((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)  ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) )  {

			?>
	<div class="tasks-progress-br">
		<div class="border bg-white ideacast-project-progress" style="max-width:385px;" ><span class="pull-left hidden-md">Workspace Progress</span>
		<div  class="progress tipText" title="<?php echo "Tasks: ".$total_completed." Completed / ".$total_elem." Outstanding"; ?>">
			<div style="width:<?php echo $percent."%"; ?>" aria-valuemax="100" aria-valuemin="50" aria-valuenow="50" role="progressbar" class="progress-bar <?php echo $toc; ?>">

			</div>
		</div><span class="pull-left"><?php echo $percent."%"; ?></span>
		</div>
	</div>
		<?php } } ?>

		<div class="el-filters">
			<div class="dropdown">
				<button class="btn dropdown-toggle filter-control" type="button" data-toggle="dropdown"><span class="selected-filters">All Statuses</span>
				<span class="caret"></span></button>
				<ul class="dropdown-menu filter_elements" id="filter_elements">
					<li><a href="javascript:;"><label class="all-statuses">All Status</label></a></li>
					<li><a href="#"><label><input type="checkbox" name="" value="not_spacified" /> <span>None</span></label></a></li>
					<li><a href="#"><label><input type="checkbox" name="" value="not_started" /> <span>Pending</span></label></a></li>
					<li><a href="#"><label><input type="checkbox" name="" value="progress" /> <span>In Progress</span></label></a></li>
					<li><a href="#"><label><input type="checkbox" name="" value="overdue" /> <span>Overdue</span></label></a></li>
					<li><a href="#"><label><input type="checkbox" name="" value="completed" /> <span>Completed</span></label></a></li>
				</ul>
			</div>
		</div>

		<div class=" pull-right right-options">
			<!-- Project Options -->
				<?php  if( isset($project_id) && !empty($project_id) ){


				 $wsp_permission = $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($this->params['pass']['1']),$this->params['pass']['0'],$this->Session->read('Auth.User.id'));

				 //pr($wsp_permission);

				 $p_permission = $this->Common->project_permission_details($this->params['pass']['0'],$this->Session->read('Auth.User.id'));



					// pr($user_project);
				if ( (isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {


				if( isset($riskelementcount) && $riskelementcount > 0 ){
					$riskcounttext = "Risks In Workspace: ".$riskelementcount;
				?>
				<a data-toggle="modal" class="btn btn-sm exclamation-risk green tipText" title="<?php echo $riskcounttext;?>" href="<?php echo SITEURL ?>projects/wsp_risks/<?php echo $project_id; ?>/<?php echo $this->params['pass']['1']; ?>" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-exclamation" aria-hidden="true"></i></a>
				<?php
				} else {
					$riskcounttext = "No Risks In Workspaces";?>
					<a href="<?php echo Router::url(array('controller' => 'risks', 'action' => 'manage_risk', 0, $project_id )); ?>" data-toggle="modal" class="btn btn-sm exclamation-risk tipText" title="<?php echo $riskcounttext;?>" ><i class="fa fa-exclamation" aria-hidden="true"></i></a>
				<?php } ?>

				<?php


				$grp_id = $this->Group->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));
				if(empty($grp_id)){
				?>
					<a data-toggle="modal" class="btn btn-sm btn-success tipText" title="Quick Workspaces Share" href="<?php echo SITEURL ?>workspaces/quick_share/<?php echo $project_id; ?>/<?php echo $this->params['pass']['1']; ?>" data-target="#modal_medium" rel="tooltip" ><i class="fa fa-user-plus"></i></a>
				<?php }
				}
				//pr($data['workspace']['ProjectWorkspace']['0']['WorkspacePermission']['0']);
				?>
			<div class="btn-group action" style="display:none">


					<?php

					if((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)  ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($wsp_permission) && $wsp_permission==1) ) {
					// SHOW PROJECT EDIT LINK IF PROJECT ID VALUES ARE EXISTS ?>
						<a href="<?php echo Router::url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_id, $workspace_id)); ?>" class="btn btn-sm btn-success tipText" title="<?php echo tipText('Update Workspace Details') ?>" id="menu_open_workspace" ><i class="fa fa-fw fa-pencil"></i> </a>

						<?php } ?>

			</div>
			<div class="btn-group action ">
			<?php //********************* More Button ************************
				echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'projects' )); ?>
			</div>

			<div class="btn-group action "> <?php if($this->request->params['action']=='manage_elements'){ ?>

				<a id="btn_go_back" data-original-title="Go Back" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'index', $this->params['pass']['0'], 'admin' => FALSE ), TRUE); ?>" class="btn btn-warning tipText pull-right btn-sm" > <i class="fa fa-fw fa-chevron-left"></i> Back </a>

				<?php }else{ ?>

				<a id="btn_go_back" data-original-title="Go Back" href="<?php echo $this->request->referer(); ?>" class="btn btn-warning tipText pull-right btn-sm"> <i class="fa fa-fw fa-chevron-left"></i> Back </a>

				<?php }  ?>
			</div>
			<?php } ?>
		</div>

</section>