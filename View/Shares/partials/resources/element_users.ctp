<ul data-pid="2" id="element_users_list" class="list-group">
<?php
if( isset($element_id) && !empty($element_id) ) {

	$element_project_id = element_project($element_id);


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
					$totalUsers += count($group_users);

					foreach( $group_users as $gk => $gv) {
						$userDetail = $this->ViewModel->get_user( $gv, $unbind, 1 );

		$user_id = $gv;
		$element_project_id = $elPermit['project_id'];
		$elemt_wp_id = $elPermit['workspace_id'];

		 $wsp_permission = $this->Common->wsp_permission_details($this->ViewModel->workspace_pwid($elemt_wp_id),$element_project_id,$user_id);

		$p_permission = $this->Common->project_permission_details($element_project_id,$user_id);

		$user_project = $this->Common->userproject($element_project_id,$user_id);

		$grp_id = $this->Group->GroupIDbyUserID($element_project_id,$user_id);

		if(isset($grp_id) && !empty($grp_id)){

		$group_permission = $this->Group->group_permission_details($element_project_id,$grp_id);
			if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
				$project_level = $group_permission['ProjectPermission']['project_level'];
			}

			$wsp_permission = $this->Group->group_wsp_permission_details($this->ViewModel->workspace_pwid($elemt_wp_id),$element_project_id,$grp_id);

		}

			if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) || (isset($wsp_permission['0']['WorkspacePermission']['permit_read']) && $wsp_permission['0']['WorkspacePermission']['permit_read']) ) {

						$group_sharer[] = [
								'user_id' => $userDetail['UserDetail']['user_id'],
								'first_name' => $userDetail['UserDetail']['first_name'],
								'last_name' => $userDetail['UserDetail']['last_name'],
								'profile_pic' => $userDetail['UserDetail']['profile_pic'],
								'job_role' => $userDetail['UserDetail']['job_role'],
								'job_title' => $userDetail['UserDetail']['job_title'],
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
								'job_role' => $userDetail['UserDetail']['job_role'],
								'job_title' => $userDetail['UserDetail']['job_title'],
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

								$sharer[] = [
									'user_id' => $userDetail['UserDetail']['user_id'],
									'first_name' => $userDetail['UserDetail']['first_name'],
									'last_name' => $userDetail['UserDetail']['last_name'],
									'profile_pic' => $userDetail['UserDetail']['profile_pic'],
									'job_role' => $userDetail['UserDetail']['job_role'],
									'job_title' => $userDetail['UserDetail']['job_title'],
									'email' => $userDetail['User']['email'],
									'owner' => $elPermit['is_editable'],
								];
							}

						}
				}
			}
		}
		$current_user_id = $this->Session->read('Auth.User.id');
	?>



<?php /********************************** element_owner *****************************************************/  ?>

		<?php




		$show_owner = false;
		if(isset($element_owner) && !empty($element_owner)) {
			foreach( $element_owner as $key => $val ) {
			 if($this->data['user_id']!=$val['user_id']){
					$html = '';
					if( $val['user_id'] != $current_user_id ) {
						$html = "<p><a class='btn btn-default btn-xs disabled'>Send Message</a> <a class='btn btn-default btn-xs disabled'>Start Chat</a></p>";
					}
					$style = '';
					if( $owner['UserProject']['user_id'] == $val['user_id'] ) {
						$style = 'border: 2px solid #333';
					}


					$user_name = $val['first_name'] . ' ' . $val['last_name'];

					$profile_pic = $val['profile_pic'];
					$job_title = $val['job_title'];

					if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)){
						$profilesPic = SITEURL . USER_PIC_PATH . $profile_pic;
					}
					else{
						$profilesPic = SITEURL . 'images/placeholders/user/user_1.png';
					}

				?><li data-name="<?php echo $val['first_name'] ?>" class="list-group-item">

					<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" style="style="float: left;"">
						<img src="<?php echo $profilesPic; ?>" class="user-image" style="<?php echo $style; ?>" >
					</a>
					<div class="user-detail">
						<p><?php echo $user_name; ?></p>
						<p>Creator</p>
					</div>
					</li>


		<?php } }  ?>
		<?php } ?>


		<?php /********************************** participants owners *****************************************************/  ?>

		<?php
		 $cid = isset($element_owner[0]['user_id']) ? $element_owner[0]['user_id'] : 0;

		if(isset($participants_owners) && !empty($participants_owners)) {
				foreach( $participants_owners as $key => $val ) {

			if($this->data['user_id']!=$val){

					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 );

					$html = '';
					if( $val != $current_user_id ) {
						$html = "<p><a class='btn btn-default btn-xs disabled'>Send Message</a> <a class='btn btn-default btn-xs disabled'>Start Chat</a></p>";
					}
					$style = '';
					if( $owner['UserProject']['user_id'] == $val ) {
						$style = 'border: 2px solid #333';
					}

					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						$job_title = $userDetail['UserDetail']['job_title'];

                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }

					?>
					<li data-name="<?php echo $userDetail['UserDetail']['first_name']; ?>" class="list-group-item">
					<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val )); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" style="style="float: left;"">
						<img src="<?php echo $profilesPic; ?>" class="user-image" style="<?php echo $style; ?>" >
					</a>
					<div class="user-detail">
						<p><?php echo $user_name; ?></p>
						<p>Owner</p>
					</div>
					</li>

				<?php } } ?>

			<?php } ?>
		<?php } ?>

		<?php /********************************** participants group owners *****************************************************/  ?>

		<?php if(isset($participantsGpOwner) && !empty($participantsGpOwner)) { ?>
			<?php foreach( $participantsGpOwner as $key => $val ) {

			if($this->data['user_id']!=$val){
			?>

				<?php
					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $this->ViewModel->get_user( $val, $unbind, 1 );


					$html = '';
					if( $val != $current_user_id ) {
						$html = "<p><a class='btn btn-default btn-xs disabled'>Send Message</a> <a class='btn btn-default btn-xs disabled'>Start Chat</a></p>";
					}
					$style = '';
					if( $owner['UserProject']['user_id'] == $val ) {
						$style = 'border: 2px solid #333';
					}


					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						$job_title = $userDetail['UserDetail']['job_title'];

                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }

					?>
					<li data-name="<?php echo $val['first_name'] ?>" class="list-group-item">
					<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" style="style="float: left;"">
						<img src="<?php echo $profilesPic; ?>" class="user-image" style="<?php echo $style; ?>" >
					</a>
				    <div class="user-detail">
						<p><?php echo $user_name; ?></p>
						<p>Group Owner</p>
					</div>
					</li>
				<?php } ?>

			<?php } } ?>
		<?php } ?>

<?php /********************************** sharer *****************************************************/  ?>

		<?php if(isset($sharer) && !empty($sharer)) { ?>
			<?php foreach( $sharer as $key => $val ) {

			if($this->data['user_id']!=$val['user_id']){

					$html = '';
					if( $val['user_id'] != $current_user_id ) {
						$html = "<p><a class='btn btn-default btn-xs disabled'>Send Message</a> <a class='btn btn-default btn-xs disabled'>Start Chat</a></p>";
					}
					$style = '';
					if( $owner['UserProject']['user_id'] == $val['user_id'] ) {
						$style = 'border: 2px solid #333';
					}

						$user_name = $val['first_name'] . ' ' . $val['last_name'];

						$profile_pic = $val['profile_pic'];
						$job_title = $val['job_title'];

                        if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
							$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
						}
						else{
							$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                        }

				?><li data-name="<?php echo $val['first_name'] ?>" class="list-group-item">

					<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" style="style="float: left;"">
						<img src="<?php echo $profilesPic; ?>" class="user-image" style="<?php echo $style; ?>" >
					</a>
					<div class="user-detail">
						<p><?php echo $user_name; ?></p>
						<p> Sharer</p>
					</div>
					</li>

			<?php } } ?>
		<?php } ?>

<?php /********************************** group sharer *****************************************************/  ?>

		<?php if(isset($group_sharer) && !empty($group_sharer)) { ?>
			<?php foreach( $group_sharer as $key => $val ) {

				if($this->data['user_id']!=$val['user_id']){

				$html = '';
				if( $val['user_id'] != $current_user_id ) {
					$html = "<p><a class='btn btn-default btn-xs disabled'>Send Message</a> <a class='btn btn-default btn-xs disabled'>Start Chat</a></p>";
				}
				$style = '';
				if( $owner['UserProject']['user_id'] == $val['user_id'] ) {
					$style = 'border: 2px solid #333';
				}

					$user_name = $val['first_name'] . ' ' . $val['last_name'];

					$profile_pic = $val['profile_pic'];
					$job_title = $val['job_title'];
					if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
						$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
					}
					else{
						$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
					}

				?>
				<li data-name="<?php echo $val['first_name'] ?>" class="list-group-item">
					<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" style="style="float: left;"">
						<img src="<?php echo $profilesPic; ?>" class="user-image" style="<?php echo $style; ?>" >
					</a>
					<div class="user-detail">
						<p><?php echo $user_name; ?></p>
						<p>Group Sharer</p>
					</div>
				</li>

			<?php } } ?>
		<?php } ?>


<?php
}
 ?>
 </ul>
<script>
$(function(){

	/* $('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    })  */

})
</script>

<script>

(function( $ ) {



		setTimeout(function(){
			var numericallyOrderedDivs = $('#element_users #element_users_list .list-group-item').sort(function (a, b) {
			   return $(a).data('name') > $(b).data('name');

			})
			$("#element_users #element_users_list").html(numericallyOrderedDivs);


			$('.pophover').popover({
				placement : 'bottom',
				trigger : 'hover',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
			})

		},100)



})( jQuery );

</script>
