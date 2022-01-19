<?php


if (isset($doc_list) && !empty($doc_list)) {
		$i=1;
		foreach ($doc_list as $blogValue) {
		//pr($blogValue,1);
		$comments = $blogValue['BlogDocument'];

		//if($i % 2 == 0){ $pullright = "pull-right"; } else { $pullright = ""; }

?>
				<li id="bcommentlists<?php echo $i;?>"  class="col-md-12 col-lg-4" >
					<div class="list-group-item ">
					<div class="idea-blog-doc-img-sec">
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
						// echo $profiles;
						?>
						<a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $comments['user_id']; ?>">
							<img src="<?php echo $profiles ?>" class="img-circledd tipText" title="<?php echo htmlentities($user_data['UserDetail']['first_name']) . ' ' . htmlentities($user_data['UserDetail']['last_name']); ?>" alt="Personal Image" />
						</a>
					</div>
					<div class="idea-doc-btn">
						<?php
							$up = $blogValue['BlogDocument'];
							$ext = pathinfo($up['document_name']);
							$logedin_user = $this->Session->read("Auth.User.id");
							if ($logedin_user == $comments['user_id']) {
						?>
							<a data-value="<?php echo $up['id']; ?>" id="confirm_admin_doc_delete" data-projectid="<?php echo $blogValue['Blog']['project_id'];?>" data-projectid="<?php echo $blogValue['Blog']['project_id']; ?>" class="btn btn-xs btn-danger tipText delete_document pull-left" data-original-title="Delete Document"><i class="fa fa-trash"></i></a>
						<?php } else { ?>
							<a class="btn btn-xs btn-danger tipText pull-left disabled" data-original-title="Delete Document"><i class="fa fa-trash"></i></a>
						<?php }
						if( isset($comments['blog_id']) && !empty($comments['blog_id']) ){
						?>
						<a class="tipText pull-left" data-original-title="<?php echo $blogValue['Blog']['title'];?>"><img width="22" src="<?php echo SITEURL;?>img/blog-icon-black-300.png"></a><?php } ?>
					</div>
					</div>
					<div class="comment-people-info people-info people-info-com-<?php echo $comments['id']; ?>">
						<p class="doc-type">

									<span class="dolist-document" >
										<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
											<span class="icon_text"><?php echo $ext['extension']; ?></span>
										</span>
										<a href="<?php echo SITEURL . DO_LIST_BLOG_DOCUMENTS . $up['document_name']; ?>" class="tipText" download="<?php echo $up['document_name']; ?>"><?php echo $ext['filename'] ?></a>
									</span>

						</p>
						<ul class="updateDetail">
							<li>Created:<?php
							//echo date('d M Y g:i A',strtotime($comments['created']) );
							echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($comments['created'])),$format = 'd M Y g:i A');
							?></li>
							<li>Created by:<?php echo $this->Common->userFullname($comments['user_id']); ?></li>
						</ul>
					</div>
					</div>
				</li>
		<?php $i++;
		}
    } else {
		echo '<div class="col-sm-12"><li class="borderBT_none">No Documents</li></div>';
	}
?>

<script type="text/javascript" >

/* $(function(e){

	$(".show_comment-toggle").click(function(){
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

	}); */
</script>