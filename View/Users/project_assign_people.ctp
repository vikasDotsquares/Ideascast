<style>
 .user-image {
    border: 2px solid #000;
     }

.myaccountPic {
    max-width: 40px;
    display: block;
    display: inline;
    margin: 3px;
    cursor: pointer;
    border-radius: 50%;
    border: 2px solid #ccc;
}

</style>

<div class="bg-user-tasks">Task Assignments</div> 
<?php 
$ele_assing = $this->ViewModel->getAssignmentByProject($project_id);

$p_permission = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));

			$user_project = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));

			$grp_id = $this->Group->GroupIDbyUserID($project_id,$this->Session->read('Auth.User.id'));

			if(isset($grp_id) && !empty($grp_id)){

			$group_permission = $this->Group->group_permission_details($project_id,$grp_id);
				if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
				 
			}
			
			/*=======================================================*/
 
if( isset($ele_assing) && !empty($ele_assing) && count($ele_assing) > 0 ) {
?>
<div class="bg-selectuser">
	<p class="selectusers">Select Team Member:</p>
	<?php if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) ) { ?>
	<span class="assignmentfilter tipText" title="No Assignments" data-element="" data-project="<?php echo $project_id; ?>" >
		<i class="fa fa-user" aria-hidden="true"></i>
	</span>
	
	<?php } ?>
	
</div>
<?php } else { ?>
<div class="bg-selectuser" style="padding-bottom:8px;">Select Team Member:</div>	
<?php }
if( isset($data) && !empty($data) ) {
?>
	<div class="myaccountpicsec">
		<?php 
			foreach( $data as $user_id ){
					//$user_id = $assignmentUser['ElementAssignment']['assigned_to'];
					
					$userDetail = $this->Common->userDetail($user_id);
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
						 
						$owner = $this->viewModel->sharingPermitType($project_id, $user_id);
						$ownerClass = '';
						$datapermission = 'data-permission="sharer"';
						if( isset($owner) && $owner == true ){
							$ownerClass = 'user-image';
							$datapermission = 'data-permission="owner"';
						}
						
						$grp_id = $this->Group->GroupIDbyUserID($project_id,$user_id);
						$grp_id = (isset($grp_id) && !empty($grp_id)) ? $grp_id : 0;
						 
				?>
						<img class="myaccountPic <?php echo $ownerClass;?> tipText"   title="<?php echo $user_name; ?>" data-user="<?php echo $user_id; ?>" data-group="<?php echo $grp_id ; ?>"   <?php echo $datapermission;?> alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
						
<?php 			} 	 
			} 
} else {
?>
<div style="text-align:center; font-weight: 545;">There are no task assignments.</div>
<?php } ?>
</div>	 