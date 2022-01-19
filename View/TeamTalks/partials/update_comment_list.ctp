<?php 
$comments = $data['BlogComment'];
?>
<div class="comment-people-pic">
						<?php
						$user_data = $this->ViewModel->get_user_data($comments['user_id']);
						$pic = $user_data['UserDetail']['profile_pic'];
						$profiles = SITEURL . USER_PIC_PATH . $pic;

						if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
							$profiles = SITEURL . USER_PIC_PATH . $pic;
						} else {
							$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
						$current_org = $this->Permission->current_org();
						$current_org_other = $this->Permission->current_org($comments['user_id']);							
						}
						// echo $profiles;
						?>
						<a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $comments['user_id']; ?>">
							<img src="<?php echo $profiles ?>" class="img-circledd tipText" title="<?php echo htmlentities($user_data['UserDetail']['first_name']) . ' ' . htmlentities($user_data['UserDetail']['last_name']); ?>" alt="Personal Image" />
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
							   <a data-toggle="modal" data-target="#modal_edit_blogComments" id="edit_blog_comment" data-value="<?php echo $comments['id']; ?>" data-remote="<?php echo SITEURL; ?>team_talks/edit_blog_comments_list/blog_id:<?php echo $comments['blog_id']; ?>/comment_id:<?php echo $comments['id']; ?>/com_class:<?php echo "people-info-com-".$comments['id']; ?>" class="btn btn-xs btn-default tipText" data-toggle="modal"  data-original-title="Edit comment"><i class="fa fa-pencil"></i></a>
								
								<a data-value="<?php echo $comments['id']; ?>" id="confirm_coment_delete" class="btn btn-xs btn-danger tipText delete_comment" data-original-title="Delete comment"><i class="fa fa-trash"></i>
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