<?php

// pr($history);
if(isset($history) && !empty($history)){ ?>
<div class="annotate-outer">
<?php
	foreach ($history as $key => $value) {
?>
	<div class="annotate-item" data-id="56">
		<div class="annotate-text-image">

			<?php
			$current_user_id = $this->Session->read('Auth.User.id');
			$userDetail = $this->ViewModel->get_user( $value['ProjectDateHistory']['user_id'], null, 1 );
			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			$user_name = 'Not Available';
			$job_title = 'Not Available';
			$html = '';
			if(isset($userDetail) && !empty($userDetail)) {

				$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

				if( $value['ProjectDateHistory']['user_id'] != $current_user_id ) {
					$html = CHATHTML($value['ProjectDateHistory']['user_id'], $value['ProjectDateHistory']['project_id']);
				}

				if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
					$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
				}
			}
			?>


			<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $value['ProjectDateHistory']['user_id'], 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
				<img  src="<?php echo $user_image; ?>" class="annotate-user-image" align="left" width="40" height="40"  >
			</a>

			<div class="annotate-text">
				<?php
				echo !empty($value['ProjectDateHistory']['comments']) ? $value['ProjectDateHistory']['comments'] : 'N/A';
				?>
			</div>

		</div>
		<div class="date-options">
			<span class="date-text left">
			<?php
			echo $this->TaskCenter->_displayDate_new($value['ProjectDateHistory']['created']);
			?>

			</span>
			<span class="date-text right">Project End:
			<?php
			echo $this->TaskCenter->_displayDate_new($value['ProjectDateHistory']['end_date'],"d M, Y");
			?></span>
		</div>
	</div>

<?php }
?>
</div>
<?php
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