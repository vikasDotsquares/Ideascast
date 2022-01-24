<?php 
$current_user_id = $this->Session->read('Auth.User.id');
if(isset($data) && !empty($data)) {
	foreach($data as $key => $val) {
			
		$html = '';
		if( $val != $current_user_id ) {
			$html = "<p><a class='btn btn-default btn-xs disabled'>Send Message</a> <a class='btn btn-default btn-xs disabled'>Start Chat</a></p>";
		}
		$style = '';
		$isOwner = 0;
		if( $owner['UserProject']['user_id'] == $val ) {
			$style = 'border: 2px solid #333';
			$isOwner = 1;
		}
		
		$userDetail = $this->ViewModel->get_user( $val, null, 1 );
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'N/A';
		$job_title = 'N/A';
		if(isset($userDetail) && !empty($userDetail)) {
			$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
			$profile_pic = $userDetail['UserDetail']['profile_pic'];
			$job_title = $userDetail['UserDetail']['job_title'];
			
			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
	?>
	<li class="list-group-item" data-id="<?php echo $val; ?>" data-name='<?php echo $userDetail['UserDetail']['first_name']; ?>'>
		<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val)); ?>"  data-target="#popup_modal" data-toggle="modal"  class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
			<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
		</a>
		
		<div class="user-detail">
			<p><?php echo $user_name; ?></p>
			<p><?php echo $type; ?></p>
		</div>
		
		<span class="label label-default label-pill pull-right view_data tipText" title="Shows Information" data-placement="left" data-id="<?php echo $val; ?>">
			<i class="fa fa-chevron-right"></i>
		</span>
	</li>
	<?php  
	}
}
?> 