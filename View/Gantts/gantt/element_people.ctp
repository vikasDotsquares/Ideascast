<style>



		.gantt_people .not-react .engage-status:before {

			content: "\f00c";
			border: solid 1px blue;
			border-radius: 50%;
			background: blue;
			color: #fff;
			padding: 0;
			width: 17px;
			height: 17px;
			display: inline-block;
			line-height: 14px;
			font-size: 12px;
			text-align: center;

		}
		.gantt_people .not-avail .engage-status:before {
		   display:none;
		}
		.gantt_people .accepted .engage-status:before {
		   content: "\f00c";
			border: solid 1px #67a028;
			border-radius: 50%;
			background: #67a028;
			color: #fff;
			padding: 0;
			width: 17px;
			height: 17px;
			display: inline-block;
			line-height: 14px;
			font-size: 12px;
			text-align: center;
		}
		.gantt_people .not-accept-start .engage-status:before {
		   content: "\f00c";
			border: solid 1px #DF0707;
			border-radius: 50%;
			background: #DF0707;
			color: #fff;
			padding: 0;
			width: 17px;
			height: 17px;
			display: inline-block;
			line-height: 14px;
			font-size: 12px;
			text-align: center;
		}
		.gantt_people .disengage .engage-status:before {
		   display:none;
		}
		.gantt_people li{ position : relative;}
		.gantt_people li i{ position : absolute; right: -7px; top: -9px; }
	</style>
<?php

if( isset($element_id) && !empty($element_id) ) {

	$element_project_id = element_project($element_id);
	$project_id = $element_project_id;

	$owner = $this->Common->ProjectOwner($element_project_id,$this->Session->read('Auth.User.id'));
	$participants_owners = participants_owners($element_project_id, $owner['UserProject']['user_id'] );

	$participantsGpOwner = participants_group_owner($element_project_id );

	$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
	$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;

	//pr($participants_owners);
	//pr($participantsGpOwner);

?>
	<?php
		$element_owner = $group_sharer = $sharer = null;
		$totalUsers = 0;
		$current_user_id = $this->Session->read('Auth.User.id');

		$sharedData = $this->ViewModel->element_users($element_id);
		// pr($sharedData, 1);
		$inc = 0;
		$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
		if(isset($sharedData) && !empty($sharedData)) {
			foreach( $sharedData as $k => $v) {
				$elPermit = $v['ElementPermission'];

				if( isset($elPermit['project_group_id']) && !empty($elPermit['project_group_id']) ) {

					$groupData = $this->Group->ProjectGroupDetail($elPermit['project_group_id'])['ProjectGroup'];

					$group_users = $this->Group->group_users($elPermit['project_group_id'], true);
					$totalUsers += ( isset($group_users) && !empty($group_users) ) ? count($group_users) : 0;

					foreach( $group_users as $gk => $gv) {
						// $userData = $this->ViewModel->get_user_data($gv, 1);
						$userDetail = $this->ViewModel->get_user( $gv, $unbind, 1 );
					/*-----------------------------------------------------------------------*/

		$user_id = $gv;
		$element_project_id = $elPermit['project_id'];
		$elemt_wp_id = $elPermit['workspace_id'];

		 $wsp_permission = $this->Common->wsp_permission_details($this->ViewModel->workspace_pwid($elemt_wp_id),$element_project_id,$user_id);

		 //pr($wsp_permission);

		$p_permission = $this->Common->project_permission_details($element_project_id,$user_id);

		$user_project = $this->Common->userproject($element_project_id,$user_id);

		$grp_id = $this->Group->GroupIDbyUserID($element_project_id,$user_id);

		if(isset($grp_id) && !empty($grp_id)){

		$group_permission = $this->Group->group_permission_details($element_project_id,$grp_id);
			if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
				$project_level = $group_permission['ProjectPermission']['project_level'];
			}

			//$ws_permission =  $this->Group->group_element_permission_details( $ws_id, $project_id['ProjectWorkspace']['project_id'], $grp_id);

			$wsp_permission = $this->Group->group_wsp_permission_details($this->ViewModel->workspace_pwid($elemt_wp_id),$element_project_id,$grp_id);
			// pr($wsp_permission);
		}

			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) || (isset($wsp_permission['0']['WorkspacePermission']['permit_read']) && $wsp_permission['0']['WorkspacePermission']['permit_read']) ) {

						/*-----------------------------------------------------------------------*/



						$group_sharer[] = [
								'user_id' => $userDetail['UserDetail']['user_id'],
								'first_name' => $userDetail['UserDetail']['first_name'],
								'last_name' => $userDetail['UserDetail']['last_name'],
								'profile_pic' => $userDetail['UserDetail']['profile_pic'],
								'job_role' => htmlentities($userDetail['UserDetail']['job_role'],ENT_QUOTES),
								'email' => $userDetail['User']['email'],
								'group' => true,
								'group_name' => $groupData['title'],
								'owner' => 0,
							];
						}

					}
				}
				else
					if( isset($elPermit['user_id']) && !empty($elPermit['user_id']) ) {
						$totalUsers += 1;

						$userDetail = $this->ViewModel->get_user( $elPermit['user_id'], $unbind, 1 );

						if( isset($elPermit['is_editable']) && !empty($elPermit['is_editable'])) {
							$element_owner[] = [
								'user_id' => $userDetail['UserDetail']['user_id'],
								'first_name' => $userDetail['UserDetail']['first_name'],
								'last_name' => $userDetail['UserDetail']['last_name'],
								'profile_pic' => $userDetail['UserDetail']['profile_pic'],
								'job_role' => htmlentities($userDetail['UserDetail']['job_role'],ENT_QUOTES),
								'email' => $userDetail['User']['email'],
								'owner' => $elPermit['is_editable'],
							];
						}
						else {

						/*-----------------------------------------------------------------------*/

		$user_id = $elPermit['user_id'];
		$element_project_id = $elPermit['project_id'];
		$elemt_wp_id = $elPermit['workspace_id'];

		 $wsp_permission = $this->Common->wsp_permission_details($this->ViewModel->workspace_pwid($elemt_wp_id),$element_project_id,$user_id);

		 //pr($wsp_permission);

		$p_permission = $this->Common->project_permission_details($element_project_id,$user_id);

		$user_project = $this->Common->userproject($element_project_id,$user_id);

		$grp_id = $this->Group->GroupIDbyUserID($element_project_id,$user_id);

		if(isset($grp_id) && !empty($grp_id)){

		$group_permission = $this->Group->group_permission_details($element_project_id,$grp_id);
			if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
				$project_level = $group_permission['ProjectPermission']['project_level'];
			}

			//$ws_permission =  $this->Group->group_element_permission_details( $ws_id, $project_id['ProjectWorkspace']['project_id'], $grp_id);

			$wsp_permission = $this->Group->group_wsp_permission_details($this->ViewModel->workspace_pwid($elemt_wp_id),$element_project_id,$grp_id);
			// pr($wsp_permission);
		}

			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) || (isset($wsp_permission['0']['WorkspacePermission']['permit_read']) && $wsp_permission['0']['WorkspacePermission']['permit_read']) ) {

						/*-----------------------------------------------------------------------*/

							$sharer[] = [
									'user_id' => $userDetail['UserDetail']['user_id'],
									'first_name' => $userDetail['UserDetail']['first_name'],
									'last_name' => $userDetail['UserDetail']['last_name'],
									'profile_pic' => $userDetail['UserDetail']['profile_pic'],
									'job_role' => htmlentities($userDetail['UserDetail']['job_role'],ENT_QUOTES),
									'email' => $userDetail['User']['email'],
									'owner' => $elPermit['is_editable'],
								];
				}

						}
				}
			}
		}
		/*  pr($element_owner);
		 pr($sharer);
		 pr($group_sharer, 1); */
	?>
	<div class="modal-body clearfix gantt_people">
	<ul>
<?php /********************************** element_owner *****************************************************/  ?>

		<?php if(isset($element_owner) && !empty($element_owner)) { ?>
			<?php foreach( $element_owner as $key => $val ) { ?>

				<?php



					$user_name = $val['first_name'] . ' ' . $val['last_name'];

					$profile_pic = $val['profile_pic'];

					if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
						$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
					}
					else{
						$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
					}


					// User assignment status ==============
						$element_assigned = element_assigned( $element_id);
						$assign_class = 'not-avail';
						if( $element_assigned['ElementAssignment']['assigned_to'] == $val['user_id'] ){
							if($element_assigned['ElementAssignment']['reaction'] == 1) {
								$assign_class = 'accepted';
							}
							else if($element_assigned['ElementAssignment']['reaction'] == 2) {
								$assign_class = 'not-accept-start';
							}
							else if($element_assigned['ElementAssignment']['reaction'] == 3)
							{
								$assign_class = 'disengage';
							} else {
								if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
									$assign_class = 'not-react';
								}
							}
						}
						/*==========================================*/

				?>

					<li class="black <?php echo $assign_class;?>">
						<i class="fa engage-status"></i>
						<?php
								$html = '';

								if( $val['user_id'] != $current_user_id ) {
									$html = CHATHTML($val['user_id'], $project_id);
								}
								$style = '';

								if( $owner['UserProject']['user_id'] == $val['user_id'] ) {
									$style = 'border: 2px solid #333';
								}

								$userDetail = $this->ViewModel->get_user( $val['user_id'], null, 1 );
								$user_image = SITEURL . 'images/placeholders/user/user_1.png';
								$user_name = 'N/A';
								$job_title = 'N/A';
								if(isset($userDetail) && !empty($userDetail)) {
									$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									$profile_pic = $userDetail['UserDetail']['profile_pic'];
									$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

									if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
										$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

								 } ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
						<img class="myaccountPic" alt="Logo Image" src="<?php echo $profilesPic ?>" alt="Profile Image" /></a>
								<?php } ?>
					</li>


			<?php } ?>
		<?php } ?>


		<?php /********************************** participants owners *****************************************************/  ?>

		<?php
		 $cid = isset($element_owner[0]['user_id']) ? $element_owner[0]['user_id'] : 0;

		if(isset($participants_owners) && !empty($participants_owners)) { ?>
			<?php foreach( $participants_owners as $key => $val ) {

			if($cid !=$val){
			?>

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

						// User assignment status ==============
							$element_assigned = element_assigned( $element_id);
							$assign_class = 'not-avail';
							if( $element_assigned['ElementAssignment']['assigned_to'] == $userDetail['UserDetail']['user_id'] ){
								if($element_assigned['ElementAssignment']['reaction'] == 1) {
									$assign_class = 'accepted';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 2) {
									$assign_class = 'not-accept-start';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 3)
								{
									$assign_class = 'disengage';
								} else {
									if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
										$assign_class = 'not-react';
									}
								}
							}
						/*==========================================*/
					?>

						<li class="black <?php echo $assign_class;?>" >
						<i class="fa engage-status"></i><?php
								$html = '';

								if( $val != $current_user_id ) {
									$html = CHATHTML($val, $project_id);
								}
								$style = '';

								if( $owner['UserProject']['user_id'] == $val ) {
									$style = 'border: 2px solid #333';
								}

								$userDetail = $this->ViewModel->get_user( $val, null, 1 );
								$user_image = SITEURL . 'images/placeholders/user/user_1.png';
								$user_name = 'N/A';
								$job_title = 'N/A';
								if(isset($userDetail) && !empty($userDetail)) {
									$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									$profile_pic = $userDetail['UserDetail']['profile_pic'];
									$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

									if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
										$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

								 } ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
								<img class="myaccountPic" alt="Logo Image"  src="<?php echo $profilesPic ?>" alt="Profile Image" /></a><?php }?>
						</li>

				<?php } } ?>

			<?php } ?>
		<?php } ?>

		<?php /********************************** participants group owners *****************************************************/  ?>

		<?php if(isset($participantsGpOwner) && !empty($participantsGpOwner)) { ?>
			<?php foreach( $participantsGpOwner as $key => $val ) { ?>

				<?php
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 );

					//pr($val);
					//pr($userDetail, 1);

					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

						$profile_pic = $userDetail['UserDetail']['profile_pic'];

                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }

						// User assignment status ==============
							$element_assigned = element_assigned( $element_id);
							$assign_class = 'not-avail';
							if( $element_assigned['ElementAssignment']['assigned_to'] == $userDetail['UserDetail']['user_id'] ){
								if($element_assigned['ElementAssignment']['reaction'] == 1) {
									$assign_class = 'accepted';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 2) {
									$assign_class = 'not-accept-start';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 3)
								{
									$assign_class = 'disengage';
								} else {
									if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
										$assign_class = 'not-react';
									}
								}
							}
						/*==========================================*/

					?>

						<li class="black <?php echo $assign_class;?>" >
						<i class="fa engage-status"></i>
							<?php
								$html = '';

								if( $val != $current_user_id ) {
									$html = CHATHTML($val, $project_id);
								}
								$style = '';

								if( $owner['UserProject']['user_id'] == $val ) {
									$style = 'border: 2px solid #333';
								}

								$userDetail = $this->ViewModel->get_user( $val, null, 1 );
								$user_image = SITEURL . 'images/placeholders/user/user_1.png';
								$user_name = 'N/A';
								$job_title = 'N/A';
								if(isset($userDetail) && !empty($userDetail)) {
									$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									$profile_pic = $userDetail['UserDetail']['profile_pic'];
									$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

									if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
										$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

								 } ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
								<img class="myaccountPic" alt="Logo Image"  src="<?php echo $profilesPic ?>" alt="Profile Image" /></a><?php } ?>
						</li>

				<?php } ?>

			<?php } ?>
		<?php } ?>

<?php /********************************** sharer *****************************************************/  ?>

		<?php if(isset($sharer) && !empty($sharer)) { ?>
			<?php foreach( $sharer as $key => $val ) { ?>

				<?php

						$user_name = $val['first_name'] . ' ' . $val['last_name'];

						$profile_pic = $val['profile_pic'];

                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }

						// User assignment status ==============
							$element_assigned = element_assigned( $element_id);
							$assign_class = 'not-avail';
							if( $element_assigned['ElementAssignment']['assigned_to'] == $val['user_id'] ){
								if($element_assigned['ElementAssignment']['reaction'] == 1) {
									$assign_class = 'accepted';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 2) {
									$assign_class = 'not-accept-start';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 3)
								{
									$assign_class = 'disengage';
								} else {
									if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
										$assign_class = 'not-react';
									}
								}
							}
						/*==========================================*/


				?>

					<li class="<?php echo $assign_class;?>">
						<i class="fa engage-status"></i>
						<?php
								$html = '';

								if( $val['user_id'] != $current_user_id ) {
									$html = CHATHTML($val['user_id'], $project_id);
								}
								$style = '';

								if( $owner['UserProject']['user_id'] == $val['user_id'] ) {
									$style = 'border: 2px solid #333';
								}

								$userDetail = $this->ViewModel->get_user( $val['user_id'], null, 1 );
								$user_image = SITEURL . 'images/placeholders/user/user_1.png';
								$user_name = 'N/A';
								$job_title = 'N/A';
								if(isset($userDetail) && !empty($userDetail)) {
									$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									$profile_pic = $userDetail['UserDetail']['profile_pic'];
									$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

									if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
										$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

								 } ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
								<img class="myaccountPic" alt="Logo Image"  src="<?php echo $profilesPic ?>" alt="Profile Image" /></a><?php } ?>
					</li>


			<?php } ?>
		<?php } ?>

<?php /********************************** group sharer *****************************************************/  ?>

		<?php if(isset($group_sharer) && !empty($group_sharer)) { ?>
			<?php foreach( $group_sharer as $key => $val ) { ?>

				<?php

						$user_name = $val['first_name'] . ' ' . $val['last_name'];

						$profile_pic = $val['profile_pic'];

                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }

						// User assignment status ==============
							$element_assigned = element_assigned( $element_id);
							$assign_class = 'not-avail';
							if( $element_assigned['ElementAssignment']['assigned_to'] == $val['user_id'] ){
								if($element_assigned['ElementAssignment']['reaction'] == 1) {
									$assign_class = 'accepted';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 2) {
									$assign_class = 'not-accept-start';
								}
								else if($element_assigned['ElementAssignment']['reaction'] == 3)
								{
									$assign_class = 'disengage';
								} else {
									if(!empty($element_assigned['ElementAssignment']['assigned_to'])) {
										$assign_class = 'not-react';
									}
								}
							}
						/*==========================================*/


				?>

					<li class="<?php echo $assign_class;?>">
						<i class="fa engage-status"></i><?php
								$html = '';

								if( $val['user_id'] != $current_user_id ) {
									$html = CHATHTML($val['user_id'], $project_id);
								}
								$style = '';

								if( $owner['UserProject']['user_id'] == $val['user_id'] ) {
									$style = 'border: 2px solid #333';
								}

								$userDetail = $this->ViewModel->get_user( $val['user_id'], null, 1 );
								$user_image = SITEURL . 'images/placeholders/user/user_1.png';
								$user_name = 'N/A';
								$job_title = 'N/A';
								if(isset($userDetail) && !empty($userDetail)) {
									$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									$profile_pic = $userDetail['UserDetail']['profile_pic'];
									$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

									if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
										$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

								 } ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
									<img class="myaccountPic" alt="Logo Image"  src="<?php echo $profilesPic ?>" alt="Profile Image" /></a><?php } ?>
					</li>


			<?php } ?>
		<?php } ?>
		</ul>
	</div>
<?php
}
 ?>
 <script type="text/javascript" >
$(function(){
    $('#modal_medium').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });



	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });


})
</script>
<style>

.popover p {
	margin-bottom: 2px !important;
}
.popover p:first-child {
	font-weight: 600 !important;
	width: 170px !important;
	color: #000;
}
.popover p:nth-child(2) {
	font-size: 11px;
	color: #000;
}
</style>