<style>
 .user-image {
    border: 3px solid #000;
     }

.myaccountPic {
    max-width: 49px;
    display: block;
    display: inline;
    margin: 3px;
    cursor : pointer;
}


</style>

<div class="bg-user-tasks">Users on Task</div> 
<div class="bg-selectuser">Select User</div> 

<?php 
 
if( isset($data) && !empty($data) ) {
		 
?>
<div class="myaccountpicsec">
	
<?php /********************************** participants owners *****************************************************/  ?>

<?php 
if(isset($owner) && !empty($owner)){
$userDetail = $this->ViewModel->get_user( $owner, null, 1 ); 
					// pr($userDetail, 1);
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
				?>
				  
						<img class="myaccountPic user-image tipText"   title="<?php echo   $user_name; ?>" data-user="0" data-permission="owner" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
				 
					 
			 
<?php } } ?>


<?php
$owner = isset($owner) ? $owner : 0;
 //pr($owner);
//pr($data['participants_owners']);
 ?>
		<?php if(isset($data['participants_owners']) && !empty($data['participants_owners'])) { ?>
			<?php foreach( $data['participants_owners'] as $key => $val ) { ?>
				
				<?php 
				    if($owner != $val){
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
				?>
				 
					 
						<img class="myaccountPic user-image tipText"   title="<?php echo   $user_name; ?>" data-group="0" data-user="0"  data-permission="owner" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
				 
				 
				<?php } } ?>
			
			<?php } ?>
		<?php } ?>	
		

		
<?php /********************************** participants group owners *****************************************************/  ?>

		<?php if(isset($data['participantsGpOwner']) && !empty($data['participantsGpOwner'])) { ?>
			<?php foreach( $data['participantsGpOwner'] as $key => $val ) { ?>
				
				<?php 
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
				?>
				 
					 
						<img class="myaccountPic user-image tipText"   title="<?php echo   $user_name; ?>" data-group="0" data-user="0"  data-permission="owner" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					 
  
				<?php } ?>
			
			<?php } ?>
		<?php } ?>
				
	
	
	
<?php /********************************** participants *****************************************************/  ?>

		<?php if(isset($data['participants']) && !empty($data['participants'])) { ?>
			<?php foreach( $data['participants'] as $key => $val ) { ?>
				
				<?php 
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
					
				?>
				 
					 
						<img class="myaccountPic tipText"   title="<?php echo   $user_name; ?>" data-group="0" data-user="<?php echo $val; ?>" alt="Logo Image" data-permission="sharer" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
				 
				 
				<?php } ?>
			
		<?php } ?>
			<?php } ?>
		

<?php /********************************** participants group sharers *****************************************************/  ?>

		<?php if(isset($data['participantsGpSharer']) && !empty($data['participantsGpSharer'])) { ?>
			<?php foreach( $data['participantsGpSharer'] as $key => $val ) { ?>
				
				<?php 
				 
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 ); 
					// pr($userDetail, 1);
					
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						
                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }
						
						$grp_id = $this->Group->GroupIDbyUserID($project_id,$val);

								 
					
				?> 
					 
						<img class="myaccountPic tipText"   title="<?php echo   $user_name; ?>" data-group="<?php echo $grp_id; ?>" data-user="<?php echo $val; ?>" alt="Logo Image" style="width: 80%" data-permission="sharer"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					 
				 
				<?php } ?>
			
			<?php } ?>
		<?php } ?>
		
	 
	 

<?php
}
 ?>
      </div>	 