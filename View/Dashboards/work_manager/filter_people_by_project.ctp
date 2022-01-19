<?php
	$current_user_id = $this->Session->read('Auth.User.id');
	$alluserarray = $this->TaskCenter->userByProject(array_values($selected_projects));
	$owner_user = [];


	if(isset($alluserarray['all_userby_type']) && !empty($alluserarray['all_userby_type'])){

		foreach ($alluserarray['all_userby_type'] as $key => $owner) {
			if(isset($owner['owner'][0]) && !empty($owner['owner'][0])){
				$owner_user[$key]['owner'] = $owner['owner'][0];
				$owner_id = $owner['owner'][0];

				$sare_user = [];

				if( isset($owner['participants']) && !empty($owner['participants']) ){
				    $sare_user = array_merge($sare_user, $owner['participants']);
				}
				if( isset($owner['participants_owners']) && !empty($owner['participants_owners']) ){
				    $sare_user = array_merge($sare_user, $owner['participants_owners']);
				}
				if( isset($owner['participantsGpOwner']) && !empty($owner['participantsGpOwner']) ){
				    $sare_user = array_merge($sare_user, $owner['participantsGpOwner']);
				}
				if( isset($owner['participantsGpSharer']) && !empty($owner['participantsGpSharer']) ){
				    $sare_user = array_merge($sare_user, $owner['participantsGpSharer']);
				}
				$sare_user = array_unique($sare_user);


				$userDetail = $this->ViewModel->get_user( $owner_id, null, 1 );
				$user_image = SITEURL . 'images/placeholders/user/user_1.png';
				$user_name = 'Not Available';
				$job_title = 'Not Available';
				$html = '';
				if(isset($userDetail) && !empty($userDetail)) {

					$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
					$profile_pic = $userDetail['UserDetail']['profile_pic'];
					$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

					if( $owner_id != $current_user_id ) {
						$html = CHATHTML($owner_id, $key);
					}

					if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
						$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
					}
				}
?>
<div class="img-box data-block" data-title="<?php echo strip_tags($user_name); ?>">
	<!-- <span style=" display: block; font-size: 11px; "><?php // echo $user_name; ?></span> -->
		<div class="thumb" style="text-align: center;">

			<?php if(isset($owner_id) && !empty($owner_id)) { ?>
				<a  data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $owner_id, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
					<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  />
				</a>
			<?php }
			else { ?>
				<a  class="pophover not-avail" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
					<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  />
				</a>
			<?php } ?>

			<?php
			$project_title = getByDbId('Project', $key, 'title');
			$el_users_html = '';
			$el_users_html .= "<div class='el_users' >";
			$el_users_html .= "<p class='pr-title'><span>Project:</span> ".$project_title['Project']['title']."</p>";
			?>

				<?php
				$user_found = false;
					if(isset($sare_user) && !empty($sare_user)) {
						foreach($sare_user as $ou => $ov) {
							if( $owner_id != $ov ) {
								$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
								$userDetail = $this->ViewModel->get_user( $ov, $unbind, 1 );
								if(isset($userDetail) && !empty($userDetail)) {
									$user_found = true;
									$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									$el_users_html .= "<a data-toggle='modal' data-target='#popup_modal' data-remote='".SITEURL."shares/show_profile/".$ov."' href='#'><i class='fa fa-user text-maroon'></i> " . $user_name . "</a><br />";
								}
							}
						?>
						<?php }
					} ?>

					<?php
						$el_users_html .= '</div>';
					?>

			<a class="btn btn-default btn-xs users_popovers" id="" style="margin: 9px 0px 0px;" data-content="<?php if($user_found) {echo $el_users_html;}else{ echo 'N/A'; } ?>">
				<i class="fa fa-user-plus"></i>
			</a>

		</div>
</div>
<?php
			}
		}

	}else{
		echo '<div class="no-row-found">No Record Found</div>';
	}


?>
<script type="text/javascript">

	$('.users_popovers,.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

</script>