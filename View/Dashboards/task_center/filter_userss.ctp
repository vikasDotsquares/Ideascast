<?php 
$current_user_id = $this->Session->read('Auth.User.id');
 		
	if( isset($filter_users) && !empty($filter_users) ) {
		// pr($filter_users);
		foreach( $filter_users as $key => $user_id ) {

			$html = '';
			if( $user_id != $current_user_id ) {
				$html = CHATHTML($user_id);
			}
			$style = ''; 
			$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			$user_name = 'Not Available';
			$job_title = 'Not Available';
			if(isset($userDetail) && !empty($userDetail)) {
				$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);
				
				if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
					$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
				}
			?> 
			<a href="#" class="pophover user" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true ); ?>"  data-target="#popup_modal"  data-user="<?php echo $user_id; ?>" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
				<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
				
			</a>
			
			<?php 
			}
		}
	}
	else  { 
		// e($owner);
		$userDetail = $this->ViewModel->get_user( $this->Session->read("Auth.User.id"), null, 1 ); 
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'Not Available';
		$job_title = 'Not Available';
		$html = '';
		if(isset($userDetail) && !empty($userDetail)) {

				$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);	
				
				$html = CHATHTML($this->Session->read("Auth.User.id"));
			 
			
			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		} ?>
		<a  class="pophover not-avail" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $this->Session->read("Auth.User.id"), 'admin' => FALSE ), true ); ?>"  data-target="#popup_modal"  data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
			<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
		</a>
		<?php
	}
?>
<script type="text/javascript">
	
/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */
	
</script>