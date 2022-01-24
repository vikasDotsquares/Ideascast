<option value="">Select Area</option>
<?php

$user_id = $this->Session->read('Auth.User.id');

$p_permission = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));

$user_project = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));

if( isset( $areadata ) && !empty( $areadata ) ) {	
	foreach( $areadata as $k => $v ) {	 
		if( !empty($v['Area']['id']) ){
?>
	<option value="<?php echo $v['Area']['id'];?>"><?php echo strip_tags($v['Area']['title']);?></option>
<?php	}	  
	}
} ?>	