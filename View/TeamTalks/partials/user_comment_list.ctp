<?php

if (isset($blog_comments) && !empty($blog_comments)) {
    foreach ($blog_comments as $key => $comment) { //pr($comment);
        $comments = $comment['BlogComment'];
		$project_id = $comment['Blog']['project_id'];
		$current_org = $this->Permission->current_org();
		$current_org_other = $this->Permission->current_org($comments['user_id']);
        ?>
        <li id="bcommentlists<?php echo $comments['id']; ?>">
            <div class="comment-people-pic">
                <?php
				$current_user_id = $this->Session->read('Auth.User.id');
                $user_data = $this->ViewModel->get_user_data($comments['user_id']);
                $pic = $user_data['UserDetail']['profile_pic'];
                $profiles = SITEURL . USER_PIC_PATH . $pic;

                if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                } else {
                    $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }

				$job_title = $user_data['UserDetail']['job_title'];
				$html = '';
				if( $comments['user_id'] != $current_user_id ) {
					$html = CHATHTML($comments['user_id'],$project_id);
				}
				$user_name = $this->Common->userFullname($comments['user_id']);

                ?>
                <a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $comments['user_id']; ?>"  >
				<img class="pophover" data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" src="<?php echo $profiles ?>" class="img-circledd" />
				<?php  if($current_org !=$current_org_other){ ?>
				<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
				<?php } ?>
				</a>
            </div>

            <div class="comment-people-info people-info">
                <p class="created-date" ><?php echo _displayDate($comments['updated']); ?></p><br>
				<p class="created-date" >
                    <?php
                    $logedin_user = $this->Session->read("Auth.User.id");

                    $likes = $this->TeamTalk->blog_comment_likes($comments['id']);

                    $like_posted = $this->TeamTalk->comment_like_posted($logedin_user, $comments['id']);

                    if ($logedin_user == $comments['user_id']) {
					?>
						<a class="btn btn-xs btn-default tipText like_no_comment" data-remote="" data-original-title="Likes"><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span></a>

                        <a data-toggle="modal" data-target="#modal_edit_blogComments" id="edit_blog_comments" data-value="<?php echo $comments['id']; ?>" data-remote="<?php echo SITEURL; ?>team_talks/edit_blog_comments/blog_id:<?php echo $comment['Blog']['id']; ?>/comment_id:<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText" data-toggle="modal"  data-original-title="Edit comment"><i class="fa fa-pencil"></i></a>

                        <a data-value="<?php echo $comments['id']; ?>" id="confirm_coment_delete" class="btn btn-xs btn-danger tipText delete_comment text-white" data-original-title="Delete comment"><i class="fa fa-trash"></i></a>

                <?php
                    } else {
                        ?>
                        <a id="blog_comment_like" data-value="<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText <?php if ($like_posted) { ?>disabled<?php } else { ?>like_comment<?php } ?>" data-original-title="Like comment" ><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple" id="commentcounter<?php echo $comments['id']; ?>"><?php echo ($likes) ? $likes : 0; ?></span></a>
				<?php } ?>
                </p>
				<p class="doc-type"  style="width:100%;">
                    <?php
						//pr($comments);
						$uploads = ( isset($comment['BlogDocument']) && !empty($comment['BlogDocument']) ) ? $comment['BlogDocument'] : null;
						if (isset($uploads) && !empty($uploads)) {
							foreach ($uploads as $upkey => $up) {
								$ext = pathinfo($up['document_name']);
								?>
								<span class="dolist-document">
									<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
										<span class="icon_text"><?php echo $ext['extension']; ?></span>
									</span>
									<a href="<?php echo SITEURL . DO_LIST_BLOG_DOCUMENTS . $up['document_name']; ?>" class="tipText" download="<?php echo $up['document_name']; ?>"><?php echo $ext['filename'] ?></a>
								</span>
								<?php
							}
						} else {
                        ?>
                        <span class="dolist-document">
                            No Attachment
                        </span>
                    <?php } ?>
                </p>
				<p><?php echo nl2br($comments['description']); ?></p>
            </div>
        </li>
        <?php
    }
} else {
    echo '<li>No Comments</li>';
}
?>
<style>
.blog-comment-lists li .comment-people-info > p > span {
  display: inline-block;
  width: 100%;
  margin: 3px 0;
  font-size: 11px;
}

.blog-comment-lists li .comment-people-info{
	  margin: 0 0 3px 0;
}
.idea-blog-list li p {
    color: #333;
    font-size: 12px;
}

.comment-people-info .created-date{ margin: 0 0 6px 0; font-size: 12px; }

.idea-blog-list li {
  border-bottom: 1px solid #ccc;
  display: inline-block;
  width: 100%;
  padding: 9px 0;
}

.idea-blog-list{ max-height:600px; overflow-y:auto;}

</style>
<script type="text/javascript" >

 $(function(e){
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
});
</script>