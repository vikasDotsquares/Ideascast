<?php
if(isset($project_id) && !empty($project_id)) {
 
$owner = $participants = $participants_owners = $participantsGpOwner = $participantsGpSharer = $users = array();

$owner = $this->Common->ProjectOwner($project_id,$this->Session->read('Auth.User.id')); 

$participants = participants($project_id,$owner['UserProject']['user_id']);

$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);


$participantsGpOwner = participants_group_owner($project_id );

$participantsGpSharer = participants_group_sharer($project_id );
 
$participants = isset($participants) ? array_filter($participants) : $participants;
$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

if(is_array($participants) ) 
	$users =  array_merge($participants, $users);
else
	$users =  $users; 

if(is_array($participants_owners) ) 
	$users =  array_merge($participants_owners, $users);
else
	$users =  $users; 

if(is_array($participantsGpOwner) ) 
	$users =  array_merge($participantsGpOwner, $users);
else
	$users =  $users; 

if(is_array($participantsGpSharer) ) 
	$users =  array_merge($participantsGpSharer, $users);
else
	$users =  $users;
 
$current_user_id = $this->Session->read('Auth.User.id');
 
?>

<ul class="list-group" id="users_list" data-pid="<?php echo $project_id; ?>">
	<?php  echo $this->element('../Shares/partials/resources/users_list', ['data' => $participants_owners, 'owner' => $owner, 'type' => 'Owner']); ?>

	<?php  echo $this->element('../Shares/partials/resources/users_list', ['data' => $participants, 'owner' => $owner, 'type' => 'Sharer']); ?>
	
	<?php  echo $this->element('../Shares/partials/resources/users_list', ['data' => $participantsGpOwner, 'owner' => $owner, 'type' => 'Group Owner']); ?>
	
	<?php  echo $this->element('../Shares/partials/resources/users_list', ['data' => $participantsGpSharer, 'owner' => $owner, 'type' => 'Group Sharer']); ?>
</ul>
<?php } else {?> 
<ul class="list-group" id="users_list" data-pid="<?php echo $project_id; ?>">
	<li>
		<div width="100%" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" class="bg-blakish">No Element found</div>
	</li>
</ul>
<?php } ?>
<script>

(function( $ ) {
	
	 

		setTimeout(function(){
			var numericallyOrderedDivs = $('#owner_user_list #users_list .list-group-item').sort(function (a, b) {
			   return $(a).data('name') > $(b).data('name');
			  
			})					
			$("#owner_user_list #users_list").html(numericallyOrderedDivs);
			
			 
		},400) 
		  	 
	

})( jQuery );

</script>
