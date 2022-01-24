<style>
.accepted {
	color: #0073b7;
	margin-top: 5px !important;
}
</style>
<?php

if( isset($data) && !empty($data) ) {
		// pr($data, 1);
	$group_detail = group_detail($gid);
	// $data = Set::extract($data, '/ProjectGroupUser/user_id');
?>
	<div class="modal-header">
		<h3 id="modalTitle" class="modal-title" ><?php echo ucfirst($group_detail['ProjectGroup']['title']); ?></h3>
	</div>
	<div class="modal-body clearfix people">


			<?php
			$dsp = $this->Group->group_permission_details($pid,$gid);
			foreach( $data as $key => $val ) {

					$user_id = $val['ProjectGroupUser']['user_id'];

					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $user_id, $unbind, 1 );
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
						<p class="user_name">
						<?php

						if(isset($dsp['ProjectPermission']['project_level']) && $dsp['ProjectPermission']['project_level']==1){
							$ownship = 'Owner: ';
						}
						else if(isset($dsp['ProjectPermission']['share_by_id']) && ($dsp['ProjectPermission']['share_by_id'] == $user_id)) {
							$ownship = 'Owner: ';
						}
						else{
							$ownship = 'Sharer: ';
						}
						echo $ownship . $user_name;
						?>
						</p>
						<p><?php echo $userDetail['User']['email']; ?></p>
						<p><?php echo $userDetail['UserDetail']['job_role']; ?></p>
						<p class="accepted">Accepted On: <?php echo _displayDate($val['ProjectGroupUser']['modified']); ?></p>
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