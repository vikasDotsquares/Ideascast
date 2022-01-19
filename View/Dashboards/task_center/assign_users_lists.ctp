<?php if( isset($assign_lists) && !empty($assign_lists) ) {?>
<option value="">Select User</option>
<?php
foreach($assign_lists as $id => $user){
	$assign_users = json_decode($user[0]['user_detail'],TRUE);
	$user_id = $assign_users[0]['user_id'];
	
	if($user_id == 'N/A') continue;
	
	$username = $assign_users[0]['full_name'];
	$current = ($user_id == $assigned_user) ? 'selected="selected"' : '';
	 
?>
	<option value="<?php echo $user_id;?>" <?php echo $current; ?>>
		<?php echo $username;?>
	</option>
<?php } 
}  ?> 