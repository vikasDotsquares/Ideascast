<div class="bg-user-tasks" style="margin: 0;">Group</div>
<?php

if( isset($data) && !empty($data) ) {
?>
<div class="bg-selectuser" style="padding: 4px 10px 2px 20px; font-weight: normal;">Members</div>
<div class="myaccountpicsec">
		<?php

		$current_user_id = $this->Session->read('Auth.User.id');
		   foreach( $data  as $key => $val ) {   ?>

				<?php

					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 );
					// pr($userDetail, 1);

					$html = '';

					if( $val != $current_user_id ) {
						$html = CHATHTML($val, $project_id);
					}

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



						$user_name = 'N/A';
						$job_title = 'N/A';
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = $userDetail['UserDetail']['job_title'];

					 	}

				?>
<img class="myaccountPic <?php echo ($flag == 1)  ? "user-image" : " " ;?> tipText"  title="<?php echo   $user_name; ?>"  alt="Logo Image" style="width: 80%" data-permission="sharer"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
				<?php } ?>

			<?php } ?>
<?php
}else{
	echo "<div class='padding' style='font-weight: normal;'>No Members</div>";
}
 ?>
</div>