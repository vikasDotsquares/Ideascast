
<?php
$current_org = $this->Permission->current_org();
if( isset($data) && !empty($data) ) {

?>
	<div class="modal-header">
		<h3 id="modalTitle" class="modal-title" >Group Team Members</h3>
	</div>
	<div class="modal-body clearfix people">


			<?php
			$dsp = $this->Group->group_permission_details($pid,$gid);

			//$data[] = $dsp['ProjectPermission']['share_by_id'];		//pr($dsp );
			foreach( $data as $key => $val ) { ?>

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
					<div class="col-sm-2 ">
					<div class="noavailwith ">
						<img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
						<?php if($userDetail['UserDetail']['organization_id'] != $current_org['organization_id']){ ?>
						<i class="communitygray18 team-meb-com tipText" title="Not In Your Organization"></i>
					<?php } ?>
					</div>
					</div>
					<div class="col-sm-8 user-detail">
						<p class="user_name">
						<?php

						if(isset($dsp['ProjectPermission']['project_level']) && $dsp['ProjectPermission']['project_level']==1){
						  $ownship = 'Owner';
						}else if(isset($dsp['ProjectPermission']['share_by_id']) && ($dsp['ProjectPermission']['share_by_id']==$val)) {
						  $ownship = 'Owner';
						}else{
						  $ownship = 'Sharer';
						}
						echo $ownship;
						?>
						</p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><span class="ucompany">Organization: </span><?php echo ( isset($userDetail['UserDetail']['org_name']) && !empty($userDetail['UserDetail']['org_name']) && strlen(trim($userDetail['UserDetail']['org_name'])) > 0 )? trim($userDetail['UserDetail']['org_name']) : 'Not Given'; ?></p>
						<p><span class="jobrole">Role: </span><?php
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'Not Given';
							?></p>
					</div>
					<div class="col-sm-2">
						<!-- <i class="fa fa-comment"></i> -->
					</div>
				</div>
				<?php }   ?>

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
    $('#modal_medium').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

});


</script>