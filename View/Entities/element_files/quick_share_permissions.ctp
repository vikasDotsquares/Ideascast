<?php  
$current_user_id = $this->Session->read('Auth.User.id');

if( isset($project_id) && !empty($project_id) ) {
		
	$p_permission = $this->Common->project_permission_details($project_id, $user_id);
	
	$user_project = $this->Common->userproject($project_id, $user_id);
	
	$group_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
	
	if( isset($group_id) && !empty($group_id) ){
		
		$group_permission = $this->Group->group_permission_details($project_id, $group_id); 
		if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
			$p_permission = $group_permission['ProjectPermission'];
		}
	}
	
	
	$element_permissions = $this->Common->element_share_permission($element_id, $project_id, $user_id); 
	
    if ((isset($group_id) && !empty($group_id))) {
		
        $element_permissions = $this->Group->group_element_share_permission($element_id, $project_id, $group_id);
    }
	
	$read = $edit = $move = $copy = $delete = false;
	
	$ep = null;
	if( isset($element_permissions) && !empty($element_permissions) ) {
		$ep = $element_permissions;
		
		$read = ( Set::classicExtract($ep, 'ElementPermission.permit_read') ) ? true : false;
		$edit = ( Set::classicExtract($ep, 'ElementPermission.permit_edit') ) ? true : false;
		$move = ( Set::classicExtract($ep, 'ElementPermission.permit_move') ) ? true : false;
		$copy = ( Set::classicExtract($ep, 'ElementPermission.permit_copy') ) ? true : false;
		$delete = ( Set::classicExtract($ep, 'ElementPermission.permit_delete') ) ? true : false;
		
		$id = Set::extract($ep, '/ElementPermission/id') ;
		
		echo $this->Form->input('ElementPermission.id', [ 'type' => 'hidden',  'value' => $id[0] ] );
	}
	
	
?> 
<label class="permissions permit_read btn-circle btn-xs tipText <?php if( $read ) { ?>active<?php } ?>" data-original-title="Read"> 
	<input name="data[ElementPermission][permit_read]" value="1" id="" type="checkbox" <?php if( $read ) { ?>checked="checked"<?php } ?>> 
	<i class="fa fa-eye lbl-icn"></i> 
</label> 

<label class="permissions permit_edit btn-circle btn-xs tipText <?php if( $edit ) { ?>active<?php } ?>" data-original-title="Update"> 
	<input name="data[ElementPermission][permit_edit]" value="1" type="checkbox" <?php if( $edit ) { ?>checked="checked"<?php } ?>> 
	<i class="fa fa-pencil"></i> 
</label> 

<label class="permissions permit_delete btn-circle btn-xs tipText <?php if( $delete ) { ?>active<?php } ?>" data-original-title="Delete"> 
	<input name="data[ElementPermission][permit_delete]" value="1" type="checkbox" <?php if( $delete ) { ?>checked="checked"<?php } ?>> 
	<i class="fa fa-trash"></i> 
</label> 

<label class="permissions permit_copy btn-circle btn-xs tipText <?php if( $copy ) { ?>active<?php } ?>" data-original-title="Copy"> 
	<input name="data[ElementPermission][permit_copy]" value="1" type="checkbox" <?php if( $copy ) { ?>checked="checked"<?php } ?>> 
	<i class="fa fa-copy"></i> 
</label> 

<label class="permissions permit_move btn-circle btn-xs tipText <?php if( $move ) { ?>active<?php } ?>" data-original-title="Cut &amp; Move"> 
	<input name="data[ElementPermission][permit_move]" value="1" type="checkbox" <?php if( $move ) { ?>checked="checked"<?php } ?>> 
	<i class="fa fa-cut"></i> 
</label> 
				
				
<?php } ?> 