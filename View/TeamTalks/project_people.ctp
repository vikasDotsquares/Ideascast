 
<?php 
 
if( isset($data) && !empty($data) ) {
		
?>
	<div class="modal-header"> 
		<h3 id="modalTitle" class="modal-title" >People on this Project</h3> 	
	</div>
	<div class="modal-body clearfix people">
	
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
				<div class="row">
					<div class="col-sm-2">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name">Creator: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
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
				<div class="row">
					<div class="col-sm-2">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name">Owner: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
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
				<div class="row">
					<div class="col-sm-2">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name">Group Owner: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><b>Group:&nbsp;
							<?php 
								$gpnd = user_groupsbyUser($userDetail['User']['id'], project_upid($project_id));   
								 
								 echo isset($gpnd['0']['ProjectGroup']['title']) ? $gpnd['0']['ProjectGroup']['title'] : "N/A";
							?>
						</b></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
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
				<div class="row">
					<div class="col-sm-2">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name">Sharer: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
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
					
				?>
				<div class="row">
					<div class="col-sm-2">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name">Group Sharer: <?php echo $user_name; ?></p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><b>Group:&nbsp;
							<?php 
								$gpnd = user_groupsbyUser($userDetail['User']['id'], project_upid($project_id));   
								 
								 echo isset($gpnd['0']['ProjectGroup']['title']) ? $gpnd['0']['ProjectGroup']['title'] : "N/A";
							?>
						</b></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
				<?php } ?>
			
			<?php } ?>
		<?php } ?>
		
		
	</div>
	
	<div class="modal-footer"> 
		<button class="btn btn-danger" data-dismiss="modal">Close</button> 	
	</div>	 

<?php
}
 ?>
        
     

<script type="text/javascript" > 
$(function(){
	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');	
    $('#modal_medium').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
});

	
</script>