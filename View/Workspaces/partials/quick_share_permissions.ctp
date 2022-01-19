<?php  
$current_user_id = $this->Session->read('Auth.User.id');

if( isset($project_id) && !empty($project_id) ) {
		
	$p_permission = $this->Common->project_permission_details($project_id, $user_id);
	
	$user_project = $this->Common->userproject($project_id, $user_id);
	
	$wsp_permissions = $this->Common->wsp_permission_details(workspace_pwid($project_id, $workspace_id), $project_id, $user_id); 
	
	$read = $edit = $add = $delete = false;
	
	$ep = null;
	if( isset($wsp_permissions) && !empty($wsp_permissions) ) {
		if( isset($type) && $type != 'all' ) {
			$ep = $wsp_permissions;
			
			$read = ( Set::classicExtract($ep, '0.WorkspacePermission.permit_read') ) ? true : false;
			$edit = ( Set::classicExtract($ep, '0.WorkspacePermission.permit_edit') ) ? true : false;
			$add = ( Set::classicExtract($ep, '0.WorkspacePermission.permit_add') ) ? true : false; 
			$delete = ( Set::classicExtract($ep, '0.WorkspacePermission.permit_delete') ) ? true : false;
			
			$id = Set::extract($ep, '/WorkspacePermission/id') ;
			
			echo $this->Form->input('WorkspacePermission.id', [ 'type' => 'hidden',  'value' => $id[0] ] );
		}
		// pr($wsp_permissions);
	}
	
	
?> 
	<label class="permissions permit_read btn-circle btn-xs tipText <?php if( $read ) { ?>active<?php } ?>" data-original-title="Read"> 
		<input name="data[WorkspacePermission][permit_read]" value="1" id="" type="checkbox" <?php if( $read ) { ?>checked="checked"<?php } ?>> 
		<i class="fa fa-eye lbl-icn"></i> 
	</label> 

	<label class="permissions permit_edit btn-circle btn-xs tipText <?php if( $edit ) { ?>active<?php } ?>" data-original-title="Update"> 
		<input name="data[WorkspacePermission][permit_edit]" value="1" type="checkbox" <?php if( $edit ) { ?>checked="checked"<?php } ?>> 
		<i class="fa fa-pencil"></i> 
	</label> 

	<label class="permissions permit_delete btn-circle btn-xs tipText <?php if( $delete ) { ?>active<?php } ?>" data-original-title="Delete"> 
		<input name="data[WorkspacePermission][permit_delete]" value="1" type="checkbox" <?php if( $delete ) { ?>checked="checked"<?php } ?>> 
		<i class="fa fa-trash"></i> 
	</label> 
	
	<label class="permissions permit_add btn-circle btn-xs tipText <?php if( $add ) { ?>active<?php } ?>" title="" data-original-title="Add Element"> 
		<input name="data[WorkspacePermission][permit_add]" value="226" id="" type="checkbox" <?php if( $add ) { ?>checked="checked"<?php } ?>> 
		<i class="fa fa-plus"></i> 
	</label>
				
<?php } ?> 