<?php


$current_user_id = $this->Session->read('Auth.User.id');

echo $this->Html->script(array(
	'star-rating'
	));

echo $this->Html->css(
		array(

			'star-rating'
		)
);

if (isset($blog_list) && !empty($blog_list)) {
	$i=0;
    foreach ($blog_list as $blogValue) {
        ?>
		<li><h4><a data-toggle="collapse" class="show_comment-toggle" href="#blog-collapse<?php echo $i;?>"><?php echo $blogValue['Blog']['title']; ?></a></h4>
		<ul class="list-group panel-collapse collapse" data-parent="#accordion" data-id="<?php echo $i;?>" id="blog-collapse<?php echo $i;?>" >
		<?php
		if( isset($blogValue['BlogComment']) && !empty($blogValue['BlogComment']) &&  count($blogValue['BlogComment']) > 0 ){
			rsort($blogValue['BlogComment']);
			foreach($blogValue['BlogComment'] as $comments){
		?>
				<li id="bcommentlists<?php echo $i;?>"  class="list-group-item">
					<div class="comment-people-pic">
						<?php
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
								$html = CHATHTML($comments['user_id']);
							}
							$user_name = $this->Common->userFullname($comments['user_id']);
							$current_org = $this->Permission->current_org();
							$current_org_other = $this->Permission->current_org($comments['user_id']);							
						?>
						<a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $comments['user_id']; ?>"  >
							<img class="pophover"  data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" src="<?php echo $profiles ?>" class="img-circledd" />
							<?php  if($current_org !=$current_org_other){ ?>
							<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
							<?php } ?>							
						</a>
					</div>

					<div class="comment-people-info people-info people-info-com-<?php echo $comments['id']; ?>">
						<h2><?php echo nl2br($comments['description']); ?></h2>
						<p class="doc-type">
							<?php
							$docList = $this->TeamTalk->getCommentDocuments($comments['id']);
							if (isset($docList) && !empty($docList)) {
								foreach ($docList as $upkey => $up) {
									$up = $up['BlogDocument'];
									$ext = pathinfo($up['document_name']);
									?>
									<span class="dolist-document" >
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
							<?php }
							?>
						</p>
						<p class="created-date">
						<?php echo _displayDate($comments['created']); ?>
						</p>
						<div class="clearfix">
						<p class="created-date">
							<?php


							$logedin_user = $this->Session->read("Auth.User.id");

							$likes = $this->TeamTalk->blog_comment_likes($comments['id']);

							$like_posted = $this->TeamTalk->comment_like_posted($logedin_user, $comments['id']);

							if ($logedin_user == $comments['user_id']) {

							?>
								<a class="btn btn-xs btn-default tipText like_no_comment" disabled="disabled" data-remote="" data-original-title="Likes">
									<i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
								</a>
							   <a data-toggle="modal" data-target="#modal_edit_blogComments_list" id="edit_blog_comment" data-value="<?php echo $comments['id']; ?>" data-remote="<?php echo SITEURL; ?>team_talks/edit_blog_comments_list/blog_id:<?php echo $comments['blog_id']; ?>/comment_id:<?php echo $comments['id']; ?>/com_class:<?php echo "people-info-com-".$comments['id']; ?>" class="btn btn-xs btn-default tipText" data-toggle="modal"  data-original-title="Edit comment"><i class="fa fa-pencil"></i></a>

								<a data-value="<?php echo $comments['id']; ?>" id="confirm_coment_delete" class="btn btn-xs btn-danger tipText delete_comment " data-original-title="Delete comment"><i class="fa fa-trash"></i>
								</a>

						<?php
							} else {
								?>
								<a id="blog_comment_likes" data-value="<?php echo $comments['id']; ?>" class="btn btn-xs btn-default tipText <?php if ($like_posted) { ?>disabled<?php } else { ?>like_comment<?php } ?>" data-original-title="Like comment" ><i class="fa fa-thumbs-o-up">&nbsp;</i><span class="label bg-purple" id="commentcounters<?php echo $comments['id']; ?>"><?php echo ($likes) ? $likes : 0; ?></span></a>
						<?php
						}
					?>
						</p>

					</div>

					</div>
				</li>
		<?php 	}
			} else {
				echo '<li id="bcommentlists'.$i.'" class="list-group-item-nf">Comments not found</li>';
		}
		?>
		</ul>
		</li>
        <?php $i++;
    }
}
?>
<style>
.blog-comment-lists li .comment-people-info > p > span {
  display: inline-block;
  width: 100%;
  margin: 3px 0;
}

.blog-comment-lists li .comment-people-info{
	  margin: 3px 0;
}
.idea-blog-list li p {
    color: #333;
    font-size: 13px;
}

.comment-people-info .created-date{ margin: 2px 0 6px 0; }

.idea-blog-list li {
  border-bottom: 1px solid #ccc;
  display: inline-block;
  width: 100%;
  padding: 9px 0;
}

.idea-blog-list{ max-height:600px; overflow-y:auto;}
.set-margn{ margin-top: 10px !important; width: 40%;float: left ; }
.set-div-align{ width:100%; overflow:hidden ; display:block; }
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

/*	$(".show_comment-toggle").click(function(){
		console.log($(this).attr('href'));


		$($(this).attr('href')).toggleClass('in');


	})

}) */


/* 	$(document).on('show.bs.collapse hide.bs.collapse', '.accordion', function (e) {

		console.log(iconOpen);
		console.log(iconClose);

		var $target = $(e.target).find('em').toggleClass(iconOpen + ' ' + iconClose);
		  if(e.type == 'show')
			$target.prev('.accordion-heading').find('.accordion-toggle').addClass('active');
		  if(e.type == 'hide')
			$(this).find('.accordion-toggle').not($target).removeClass('active');
	*/
	});
</script>