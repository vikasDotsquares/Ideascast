<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>
<?php
	$userID = null;
	if( isset($userId) && !empty($userId) ) {
		$userID = $userId;
	}
	$blogs = $this->ViewModel->getProjectBlog($project, $userID);
  ?>

<h3 class="tabing-head">Latest Blogs <span class="btn btn-default btn-xs tipText" title="Total Blogs"><i class="check-icon" aria-hidden="true"></i>  <?php echo ( isset($blogs) && !empty($blogs) )? count($blogs) : 0; ?></span></h3>
<div class="blog_wrapper">
<?php if( isset($blogs) && !empty($blogs) ) {  ?>
	<?php foreach($blogs as $key => $row) {

		$blog = $row['Blog'];

			?>
			<?php

				$userDetail = $this->ViewModel->get_user( $blog['user_id'], null, 1 );
				$user_image = SITEURL . 'images/placeholders/user/user_1.png';
				$user_name = 'Not Available';
				$job_title = 'Not Available';
				$html = '';
				if(isset($userDetail) && !empty($userDetail)) {
					$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
					$profile_pic = $userDetail['UserDetail']['profile_pic'];
					$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

					if( $blog['user_id'] != $current_user_id ) {
						$html = CHATHTML($blog['user_id'], $project);
					}

					if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
						$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
					}
				}

			?>
			<div class="items">
				<div class="thumb">
					<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $blog['user_id'], $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
						<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
					</a>

				</div>
				<div class="description ">
					<div style="font-size: 13px; line-height: 16px;" class="description-inner">
						<a href="<?php echo Router::Url( array( 'controller' => 'team_talks', 'action' => 'index', 'project' => $project, 'blog' => $blog['id'], 'admin' => FALSE ), TRUE ); ?>" class="open-url">
						<?php
						echo $blog['title']; ?>
						</a>
					</div>

					<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($blog['created']); ?></span>

					<a data-original-title="Likes" class="btn btn-xs btn-default tipText ">
						<i class="fa fa-thumbs-o-up"></i>
						<span class="label bg-purple"><?php echo $this->ViewModel->getBlogLikes($blog['id']); ?></span>
					</a>

				</div>
			</div>

	<?php } ?>
	<?php } else { ?>
	<div class="no-row-found" >No Blogs</div>
<?php }  ?>
</div>
<script type="text/javascript" >
$(function(){
	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */


})
</script>