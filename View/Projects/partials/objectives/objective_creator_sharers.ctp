<?php
$current_user_id = $this->Session->read('Auth.User.id');

$project_users = $this->Permission->project_all_users($project_id);


$project_owner_users = array_filter($project_users, function ($var) {
    return ($var['user_permissions']['role'] == 'Creator' || $var['user_permissions']['role'] == 'Owner');
});
$project_group_owner_users = array_filter($project_users, function ($var) {
    return ($var['user_permissions']['role'] == 'Group Owner');
});
$project_sharer_users = array_filter($project_users, function ($var) {
    return ($var['user_permissions']['role'] == 'Sharer');
});
$project_group_sharer_users = array_filter($project_users, function ($var) {
    return ($var['user_permissions']['role'] == 'Group Sharer');
});
?>
	<div class="participant-boxes col-sm-12 col-md-12 col-lg-12" id="box-creator-owners">
		<div class=" project-boxes col-md-3 project-owners">
			<div class="box-wrap">
				<label><span class="hidden-md"> Project</span> Owners: </label>

				<div class="users">

					<?php if(isset($project_owner_users) && !empty($project_owner_users)) { ?>
						<?php foreach($project_owner_users as $key => $val ) {
							$style = '';
							$userDetail = $val['user_details'];
							$userId = $userDetail['user_id'];
							$userPermissions = $val['user_permissions'];

							if( $userPermissions['role'] == 'Creator' ) {
								$style = 'border: 2px solid #333';
							}

							$html = '';
							if( $userDetail['user_id'] != $current_user_id ) {
								$html = CHATHTML($userDetail['user_id'], $project_id);
							}

							// $userDetail = $this->ViewModel->get_user( $key, null, 1 );
							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'N/A';
							$job_title = 'N/A';
							$user_name = htmlentities($userDetail['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['profile_pic'];
							$job_title = htmlentities($userDetail['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
							?>
							<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userId)); ?>"  data-target="#popup_modal" data-toggle="modal"  class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
								<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
							</a>


						<?php } ?>
						<?php }else{ ?>
						<li class="not_avail">None</li>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class=" project-boxes col-md-3 project-owners">
			<div class="box-wrap">
				<label> <span class="hidden-md">Project</span> Sharers: </label>
				<div class="users">
					<?php if(isset($project_sharer_users) && !empty($project_sharer_users)) { ?>
						<?php foreach($project_sharer_users as $key => $val ) {

							$userDetail = $val['user_details'];
							$userId = $userDetail['user_id'];

							$html = '';
							if( $userDetail['user_id'] != $current_user_id ) {
								$html = CHATHTML($userDetail['user_id'], $project_id);
							}

							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'N/A';
							$job_title = 'N/A';
							$user_name = htmlentities($userDetail['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['profile_pic'];
							$job_title = htmlentities($userDetail['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							} ?>
							<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userId)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
								<img src="<?php echo $user_image; ?>" class="user-image  "  >
							</a>

						<?php }// foreach ?>
						<?php }else{ ?>
						<li class="not_avail">None</li>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class=" project-boxes col-md-3 project-owners">
			<div class="box-wrap">
				<label><span class="hidden-md"> Project</span> Group Owners: </label>
				<div class="users">
					<?php if(isset($project_group_owner_users) && !empty($project_group_owner_users)) { ?>
						<?php foreach($project_group_owner_users as $key => $val ) {

							$userDetail = $val['user_details'];
							$userId = $userDetail['user_id'];

							$html = '';
							if( $userDetail['user_id'] != $current_user_id ) {
								$html = CHATHTML($userDetail['user_id'], $project_id);
							}

							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'N/A';
							$job_title = 'N/A';
							$user_name = htmlentities($userDetail['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['profile_pic'];
							$job_title = htmlentities($userDetail['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							} ?>
							<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userId)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
								<img src="<?php echo $user_image; ?>" class="user-image" >
							</a>

						<?php } ?>
						<?php }else{ ?>
						<li class="not_avail">None</li>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class=" project-boxes col-md-3 project-owners">
			<div class="box-wrap">
				<label> <span class="hidden-md">Project</span> Group Sharers: </label>
				<div class="users">
					<?php if(isset($project_group_sharer_users) && !empty($project_group_sharer_users)) { ?>
						<?php foreach($project_group_sharer_users as $key => $val ) {

							$userDetail = $val['user_details'];
							$userId = $userDetail['user_id'];

							$html = '';
							if( $userDetail['user_id'] != $current_user_id ) {
								$html = CHATHTML($userDetail['user_id'], $project_id);
							}

							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'N/A';
							$job_title = 'N/A';
							$user_name = htmlentities($userDetail['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['profile_pic'];
							$job_title = htmlentities($userDetail['job_title'],ENT_QUOTES);

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							} ?>
							<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userId)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
								<img src="<?php echo $user_image; ?>" class="user-image" >
							</a>

						<?php } ?>
						<?php }else{ ?>
						<li class="not_avail">None</li>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>


<style>
#box-sharers {
		border-top: 1px solid #ececec;
		padding-top: 5px;
}

</style>

<script type="text/javascript" >
	$(function(){
		$('a[href="#"],a[href=""]').attr('href', 'javascript:;');
		$('.pophover').popover({
	        placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
	    })
		$('body').on('click', function (e) {
			$('.pophover').each(function () {
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					var $that = $(this);
						$that.popover('hide');
				}
			});
		});

	})
</script>