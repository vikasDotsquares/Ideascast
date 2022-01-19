<?php $data = getByDbId('Workspace', $workspace_id, ['sign_off']); ?>


<?php $wsp_permissions =$this->ViewModel->getWspPermission($workspace_id); ?>
<div class="wsp-icon-wrap">

	<?php
	if (isset($project_id) && !empty($project_id)) {

		$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

		$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
	}
	$project_status = $this->Permission->project_status($project_id)[0][0]['prj_status'];
	$worksapce_status = $this->Permission->worksapce_status($workspace_id)[0][0]['prj_status'];

	$wsp_signoff_comment = wsp_signoff_comment($workspace_id);

	$get_progressing_workspace_element = get_progressing_workspace_element($workspace_id);

	?>

	<a href="#" class="tipText ws-filter-button h-common-btn" title="filter Tasks" data-target="#mid_model_box" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'workspaces', 'action' => 'filter_tasks', $project_id, $workspace_id, 'admin' => FALSE ), TRUE ); ?>"><i class="filter-icon filterblack"></i></a>
	<?php
	if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) {

		if($worksapce_status != 'not_spacified' && $worksapce_status != 'not_started' && $worksapce_status != 'completed'){

			if( !empty($get_progressing_workspace_element)  ){
				$signoffmsg = "This Workspace cannot be signed off because it has Tasks in progress.";
			}


			if( !empty($get_progressing_workspace_element)  ){ ?>
				<span class="hlt-sep">
				<a href="#" class="tipText signoff-btn h-common-btn disable element-sign-off-restrict" title="Sign Off" data-msg="<?php echo $signoffmsg;?>" data-type="Share"><i class="signoffblack"></i></a>
				</span>
			<?php
			}  else { ?>
				<span class="hlt-sep">
				<a href="#" class="tipText signoff-btn h-common-btn" data-toggle="modal" data-target="#signoff_comment_box" data-remote="<?php echo SITEURL;?>workspaces/tasks_signoff/<?php echo $workspace_id; ?>" title="Sign Off"  data-type="Share"><i class="signoffblack"></i></a>
			<?php } ?>

		<?php
		}
		else if ($worksapce_status == 'completed') {
			$flipclass = '';
			if( isset($wsp_signoff_comment) && $wsp_signoff_comment != 0 ){
			$flipclass ='fa-rotate-180';
			?>   <span class="hlt-sep">
			<a href="#" class="tipText signoff-btn h-common-btn disable" title="Click To See Comment and Evidence"  data-toggle="modal" data-target="#signoff_comment_show" data-remote="<?php echo SITEURL;?>workspaces/show_signoff/<?php echo $workspace_id; ?>"><i class="signoffblack"></i></a>
			<?php }  if($project_status != 'completed'){ ?>

			<a href="#" class="tipText reopen-btn h-common-btn element-sign-off"  title="Reopen" data-msg="Are you sure you want to reopen this Workspace?" data-toggle="confirmation" data-header="Reopen Workspace"  data-id="<?php echo $workspace_id; ?>"><i class="reopenblack"></i></a>
			</span>
		<?php }
		} ?>

		</span>

	<?php } ?>

	<?php if(isset($wsp_permissions[0]['user_permissions']) && in_array($wsp_permissions[0]['user_permissions']['role'],array('Creator','Group Owner','Owner')) ){
	if($data['Workspace']['sign_off'] != 1){

	?>
		<span class="hlt-sep">
		<a data-toggle="modal" class="share-button h-common-btn wssb tipText" title="" href="<?php echo SITEURL ?>workspaces/quick_share/<?php echo $project_id; ?>/<?php echo $workspace_id; ?>" data-target="#modal_medium" rel="tooltip" data-original-title="Share Workspace"><i class="share-icon"></i></a>	</span>

	<?php }else{ ?>

		<span class="hlt-sep">
		<a class="share-button h-common-btn wssb progres-mt-btn disable" title="This Workspace Is Signed Off" rel="tooltip" style="cursor: default;" ><i class="share-icon"></i></a>	</span>
	<?php
		}
	}?>

		<?php
			$wspPermit = $wsp_permissions[0]['user_permissions'];
			$wspData = $wsp_permissions[0]['workspaces'];
			$modal_title = '<i class="fa fa-exclamation-triangle"></i>&nbsp;Warning';
			$user_id = $this->Session->read('Auth.User.id');
			$message = '';
			if( isset($wspPermit['p_task_add']) && $wspPermit['p_task_add'] == 1 ) {
				if( $wspData['wsp_sign_off'] !=1 ){
					$curdate =  $this->Wiki->_displayDate(date("Y-m-d h:i:s A"),$format = 'Y-m-d');
					$wspStartDate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($wspData['wsp_start_date'])),$format = 'd M, Y h:i:s A');
					if((isset($wspData['wsp_end_date']) && !empty($wspData['wsp_end_date']) && $wspData['wsp_end_date'] != '1970-01-01' && $wspData['wsp_end_date'] < $curdate ) ){
						$message ="You cannot add a Task because the Workspace end date has passed.";
					}
					if(
					( !isset($wspData['wsp_start_date']) || $wspData['wsp_start_date'] == '1970-01-01' ) &&
					( !isset($wspData['wsp_end_date']) ||  $wspData['wsp_end_date'] == '1970-01-01' ) ){
						$message ="You cannot add a Task because the Workspace is not scheduled.";
					}
					if(!isset($wspData['wsp_start_date'])){
						$message ="Please add a schedule to this workspace first.";
					}
				}
				else if(isset($wspData['wsp_start_date'])){
					$message ="You cannot add a Task because the Workspace has been signed off.";
					$modal_title = 'Add Task';
				}
				$t_disabled  = '';
				$t_cursor = '';
				if( isset($wspData['sign_off']) && $wspData['sign_off'] == 1 ){
					$t_disabled = 'disable';
					$modal_title = "Workspace Is Signed Off";
					$t_cursor ="cursor:default !important; ";
					$message ="You cannot add a Task because the Workspace has been signed off.";
				}
				if(isset($message) && !empty($message)){
					if( isset($t_disabled) && !empty($t_disabled) ){
				?>
						<a class="share-button h-common-btn wssb progres-mt-btn tipText disable add-task-disable" title="<?php echo $modal_title;?>" style="<?php echo $t_cursor; ?>" rel="tooltip" ><i class="workspace-icon"></i></a>
					<?php } else { ?>
						<a class="share-button h-common-btn wssb progres-mt-btn tipText disable add-task-disable" title="Add Task" rel="tooltip" data-title="<?php echo $message;?>"><i class="workspace-icon"></i></a>
					<?php } ?>
				<?php } else { ?>
					<a class="share-button h-common-btn wssb progres-mt-btn tipText" title="Add Task" rel="tooltip" data-toggle="modal" data-target="#popup_model_box" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', 0, $workspace_id, 'admin' => FALSE ), TRUE ); ?>"><i class="workspace-icon"></i></a>
				<?php
				}
			}
			 ?>

	<?php
	$wsp_disabled = '';
	$wsp_tip = '';
	$cursor = '';
	if(isset($data['Workspace']['sign_off']) && !empty($data['Workspace']['sign_off']) && $data['Workspace']['sign_off'] == 1 ){

	$wsp_disabled = 'disable';
	$wsp_tip = "Workspace Is Signed Off";
	$cursor =" cursor:default !important; ";

	}

	if( !empty($wsp_permissions[0]['user_permissions']['p_edit']) && $wsp_permissions[0]['user_permissions']['p_edit'] == 1 ) {

		if($data['Workspace']['sign_off'] != 1){
	?>
			<span class="hlt-sep">
			<a href="<?php echo Router::url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_id, $workspace_id)); ?>" class="edit-button h-common-btn tipText " rel="tooltip" title="" data-original-title="Edit Workspace"><i class="edit-icon"></i> </a></span>
			<?php }else{ ?>
			<span class="hlt-sep">
			<a class="edit-button h-common-btn tipText <?php echo $wsp_disabled;?>" title="This Workspace Is Signed Off" rel="tooltip" href="javascript:void(0);" id="btn_select_workspace" style="<?php echo $cursor;?>" >
			<i class="edit-icon"></i>
			</a></span>

	<?php  }
	}


	if( !empty($wsp_permissions[0]['user_permissions']['p_delete']) && $wsp_permissions[0]['user_permissions']['p_delete'] == 1 ) { ?>
		<a  data-toggle="modal" data-target="#modal_delete"    class="workspace-button h-common-btn tipText delete-an-item" data-remote="<?php echo Router::Url( array( "controller" => "workspaces", "action" => "delete_an_item", $project_id, $workspace_id, workspace_pwid($project_id,$workspace_id),  'admin' => FALSE ), true ); ?>" title="" rel="tooltip" data-original-title="Delete Workspace" href=""><i class="deleteblack"></i> </a>
	<?php } ?>


</div>
