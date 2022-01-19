<?php
$current_user_id = $this->Session->read('Auth.User.id');

if( isset($data) && !empty($data) ) {    ?>


<div class="div-group comment-box">
	<?php foreach( $data as $key => $comment ) { ?>

	<div class="col-sm-12 items">

		<div class="col-sm-2" style="padding-left: 0px; min-height: 56px; float: left; width: 56px;">
		<?php

		$userDetail = $this->ViewModel->get_user( $comment['WorkspaceComment']['user_id'], null, 1 );
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'Not Available';
		$job_title = 'Not Available';
		if(isset($userDetail) && !empty($userDetail)) {
			$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
			$profile_pic = $userDetail['UserDetail']['profile_pic'];
			$job_title = htmlentities($userDetail['UserDetail']['job_title']);

			$project_id = workspace_pid($comment['WorkspaceComment']['workspace_id']);

			$html = '';
			if( $comment['WorkspaceComment']['user_id'] != $current_user_id ) {
				$html = CHATHTML($comment['WorkspaceComment']['user_id'], $project_id);
			}

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
		?>
			<img src="<?php echo $user_image; ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'show_profile', $comment['WorkspaceComment']['user_id'] ), TRUE); ?>" class="user-image pophover" align="left" data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
		</div>
		<div style="font-size: 13px; line-height: 16px; word-break: break-all;"><?php echo nl2br($comment['WorkspaceComment']['comments']); ?></div>

		<div class="nopadding-left mmt6">
			<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($comment['WorkspaceComment']['modified']); ?></span>
			<a data-original-title="Likes" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'like_comment', $comment['WorkspaceComment']['id'] ), TRUE); ?>" class="btn btn-xs btn-default tipText <?php if( $this->Session->read('Auth.User.id') != $comment['WorkspaceComment']['user_id'] && !commented($comment['WorkspaceComment']['id'], $this->Session->read('Auth.User.id'))) { ?>like_comment<?php } ?>">
				<i class="fa fa-thumbs-o-up"></i>
				<span class="label bg-purple"><?php echo (isset($comment['WorkspaceCommentLike']) && !empty($comment['WorkspaceCommentLike'])) ? count($comment['WorkspaceCommentLike']) : 0; ?></span>
			</a>

			<?php if( $this->Session->read('Auth.User.id') == $comment['WorkspaceComment']['user_id']) { ?>

				<a data-original-title="Edit Comment" data-comment="<?php echo $comment['WorkspaceComment']['id']; ?>" class="btn btn-xs btn-default tipText edit_comment">
					<i class="fa fa-pencil"></i>
				</a>
				<a data-original-title="Delete Comment" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'delete_comment', $comment['WorkspaceComment']['id'] ), TRUE); ?>" class="btn btn-xs btn-danger tipText delete_comment">
					<i class="fa fa-trash"></i>
				</a>

			<?php } ?>

		</div>
	</div>
	<?php } ?>
</div>

<?php }
else {
?>
	<h5 class="no-comments">No comments posted for this Workspace</h5>
<?php
}
 ?>

<script type="text/javascript" >
$(function(){

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