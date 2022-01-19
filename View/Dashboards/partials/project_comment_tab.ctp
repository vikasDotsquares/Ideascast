<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>

<?php
	$userID = null;
	if( isset($userId) && !empty($userId) ) {
		$userID = $userId;
	}

	$userPermissions = $this->ViewModel->userPermissions($project, $userID);
	$permit_owner = 0;
	$onr = 0;

	if((isset($userPermissions[0]['user_permissions']['role']) && !empty($userPermissions[0]['user_permissions']['role']))&&  ( ($userPermissions[0]['user_permissions']['role'] !='Sharer') && $userPermissions[0]['user_permissions']['role'] !='Group Sharer' )) {
		$onr = 1;
	}
	else {
		$onr = 0;
	}

	if( $slug == 'shared_projects') {
		$date_label = 'Date Shared:';
		$permit_owner = 1;
		$project_type = 'Shared';
		$edit_permit = $delete_permit = true;
	}
	else if( $slug == 'received_projects') {
		$date_label = 'Date Received:';

		$permit_owner = $onr;
		$userdata = $this->Common->userFullname($userPermissions[0]['user_permissions']['shared_by_user_id']);
		$project_type = 'Received from: '.$userdata;

	}
	else if( $slug == 'group_received_projects') {
		$date_label = 'Date Received:';

		$userdata = $this->Common->userFullname($userPermissions[0]['user_permissions']['shared_by_user_id']);

		$permit_owner = $onr;
		$project_type = 'Group Received from: '.$userdata;
	}
	else if( $slug == 'propagated_projects') {
		$date_label = 'Date Shared:';
		$permit_owner = $onr;
		$userdata = $this->Common->userFullname($current_user_id);
		$project_type = 'Propagated by: '.$userdata;
	}
	else {
		$permit_owner = 1;
	}


	$blog_data = $wiki_data = $project_annotate = $wiki_comments_count =  $project_annotate_count = $workspace_comments_count = 0;

	if( $section == 'blog') {
		// GET PROJECT BLOG COMMENTS
		$blog_data = $this->ViewModel->getBlogComments($project, $userID);
	}
	if( $section == 'wiki') {
		// GET PROJECT WIKI COMMENTS
		$wiki_data = $this->ViewModel->getWikiComments($project, $userID);
	}
	if( $section == 'annotate') {
		// GET PROJECT ANNOTATIONS
		$project_annotate = $this->ViewModel->getProjectComments($project, $userID);
	}
	if( $section == 'mission') {
		// GET PROJECT WORKSPACES
		$project_workspaces = get_project_workspace($project, TRUE);
		$workspace_comments = $this->ViewModel->getWorkspaceComments($project_workspaces, $userID);
	}

	/***** Get Project Owner ******/


////////////////////////////////////////////////////////////
	if( $section == 'blog') {
		if( isset($blog_data) && !empty($blog_data) ) {
			foreach($blog_data as $key => $comment) {

			$comments = $comment['BlogComment'];

			$userDetail = $this->ViewModel->get_user( $comments['user_id'], null, 1 );
			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			$user_name = 'Not Available';
			$job_title = 'Not Available';
			if(isset($userDetail) && !empty($userDetail)) {
				$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

				$html = '';
				if( $comments['user_id'] != $current_user_id ) {
					$html = CHATHTML($comments['user_id'], $project);
				}

				if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
					$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
				}
			}

			$comment_attachments = $this->ViewModel->getBlogCommentAttachments($comments['id']);
			?>
				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $comments['user_id'], $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<a href="<?php echo Router::Url( array( 'controller' => 'team_talks', 'action' => 'index', 'project' => $project, 'blog' => $comments['blog_id'], 'comment' => $comments['id'], 'admin' => FALSE ), TRUE ); ?>/#comments" class="open-url">
								<?php echo nl2br($comments['description']); ?>
							</a>
						</div>
						<div style="font-size: 13px; line-height: 16px;">
							<ul>
								<?php if($comment_attachments) {
									foreach($comment_attachments as $cak => $cav) {
											$ext = pathinfo($cav['BlogDocument']['document_name']);
									?>
									<li>
										<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
											<span class="icon_text"><?php echo $ext['extension']; ?></span>
										</span>
										<?php echo $ext['filename']; ?>
									</li>
									<?php
									}
									}else { ?>
									<li>No attachment</li>
								<?php } ?>
							</ul>
						</div>
						<div class="date-likes">
							<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($comments['updated']); ?></span>

							<a data-original-title="Likes" class="btn btn-xs btn-default tipText ">
								<i class="fa fa-thumbs-o-up"></i>
								<span class="label bg-purple"><?php echo $this->ViewModel->getBlogCommentLikes($comments['id']); ?></span>
							</a>
						</div>
						<?php $rate_val = $comments['rating']; ?>

						<span class="rating">
							<input id="star5" name="rating_<?php echo $comments['id'] ?>" value="5" type="radio" <?php if($rate_val == 5){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star5" title="Awesome - 5 stars"></label>

							<input id="star4half" name="rating_<?php echo $comments['id'] ?>" value="4 and a half" type="radio" <?php if($rate_val == 4.5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>

							<input id="star4" name="rating_<?php echo $comments['id'] ?>" value="4" type="radio" <?php if($rate_val == 4){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star4" title="Pretty good - 4 stars"></label>

							<input id="star3half" name="rating_<?php echo $comments['id'] ?>" value="3 and a half" type="radio" <?php if($rate_val == 3.5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="star3half" title="Meh - 3.5 stars"></label>

							<input id="star3" name="rating_<?php echo $comments['id'] ?>" value="3" type="radio" <?php if($rate_val == 3){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star3" title="Meh - 3 stars"></label>

							<input id="star2half" name="rating_<?php echo $comments['id'] ?>" value="2 and a half" type="radio" <?php if($rate_val == 2.5){ ?>checked="checked" <?php } ?>>
								<label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>

							<input id="star2" name="rating_<?php echo $comments['id'] ?>" value="2" type="radio" <?php if($rate_val == 2){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star2" title="Kinda bad - 2 stars"></label>

							<input id="star1half" name="rating_<?php echo $comments['id'] ?>" value="1 and a half" type="radio" <?php if($rate_val == 1.5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="star1half" title="Meh - 1.5 stars"></label>

							<input id="star1" name="rating_<?php echo $comments['id'] ?>" value="1" type="radio" <?php if($rate_val == 1){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star1" title="Sucks big time - 1 star"></label>

							<input id="starhalf" name="rating_<?php echo $comments['id'] ?>" value="half" type="radio" <?php if($rate_val == .5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
						</span>
					</div>
				</div>
		<?php }// end foreach ?>
		<?php } else { ?>
			<div class="no-row-found" >No Blog Comments</div>
		<?php } ?>
	<?php }elseif( $section == 'wiki') {  ?>
		<?php if( isset($wiki_data) && !empty($wiki_data) ) { ?>
			<?php foreach($wiki_data as $key => $row) { ?>
				<?php
				// pr($row);
					$userDetail = $this->ViewModel->get_user( $row['WikiPageComment']['user_id'], null, 1 );
					$user_image = SITEURL . 'images/placeholders/user/user_1.png';
					$user_name = 'Not Available';
					$job_title = 'Not Available';
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

						$html = '';
						if( $row['WikiPageComment']['user_id'] != $current_user_id ) {
							$html = CHATHTML($row['WikiPageComment']['user_id'], $project);
						}

						if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
							$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
						}
					}

					$comment_attachments = $this->ViewModel->getWikiCommentAttachments($row['WikiPageComment']['id']);
				?>


				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $row['WikiPageComment']['user_id'], $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<a href="<?php echo Router::Url( array( 'controller' => 'wikies', 'action' => 'index', 'project_id' => $project, 'wiki' => $row['WikiPageComment']['wiki_id'], 'comment' => $row['WikiPageComment']['id'], 'admin' => FALSE ), TRUE ); ?>/#comments" class="open-url">
								<?php echo nl2br($row['WikiPageComment']['description']); ?>
							</a>
						</div>
						<div style="font-size: 13px; line-height: 16px;">
							<ul>
								<?php if($comment_attachments) {
									foreach($comment_attachments as $cak => $cav) {
											$ext = pathinfo($cav['WikiPageCommentDocument']['document_name']);
									?>
									<li>
										<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
											<span class="icon_text"><?php echo $ext['extension']; ?></span>
										</span>
										<?php echo $cav['WikiPageCommentDocument']['document_name']; ?>
									</li>
									<?php
									}
									}else { ?>
									<li>No attachment</li>
								<?php } ?>
							</ul>
						</div>
						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['WikiPageComment']['updated']); ?></span>

						<a data-original-title="Likes" class="btn btn-xs btn-default tipText ">
							<i class="fa fa-thumbs-o-up"></i>
							<span class="label bg-purple"><?php echo $this->ViewModel->getWikiCommentLikes($row['WikiPageComment']['id']); ?></span>
						</a>

						<?php $rate_val = $row['WikiPageComment']['rating']; ?>

						<span class="rating">
							<input id="star5" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="5" type="radio" <?php if($rate_val == 5){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star5" title="Awesome - 5 stars"></label>

							<input id="star4half" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="4 and a half" type="radio" <?php if($rate_val == 4.5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>

							<input id="star4" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="4" type="radio" <?php if($rate_val == 4){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star4" title="Pretty good - 4 stars"></label>

							<input id="star3half" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="3 and a half" type="radio" <?php if($rate_val == 3.5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="star3half" title="Meh - 3.5 stars"></label>

							<input id="star3" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="3" type="radio" <?php if($rate_val == 3){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star3" title="Meh - 3 stars"></label>

							<input id="star2half" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="2 and a half" type="radio" <?php if($rate_val == 2.5){ ?>checked="checked" <?php } ?>>
								<label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>

							<input id="star2" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="2" type="radio" <?php if($rate_val == 2){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star2" title="Kinda bad - 2 stars"></label>

							<input id="star1half" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="1 and a half" type="radio" <?php if($rate_val == 1.5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="star1half" title="Meh - 1.5 stars"></label>

							<input id="star1" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="1" type="radio" <?php if($rate_val == 1){ ?>checked="checked" <?php } ?>>
							<label class="full" for="star1" title="Sucks big time - 1 star"></label>

							<input id="starhalf" name="rating_<?php echo $row['WikiPageComment']['id'] ?>" value="half" type="radio" <?php if($rate_val == .5){ ?>checked="checked" <?php } ?>>
							<label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
						</span>

					</div>
				</div>


			<?php } ?>
			<?php }
			else { ?>
			<div class="no-row-found" >No Wiki Comments</div>
		<?php } ?>
	<?php }elseif( $section == 'annotate') { ?>
		<?php if( isset($project_annotate) && !empty($project_annotate) ) { ?>
				<?php foreach($project_annotate as $key => $row) { ?>
					<?php

						$userDetail = $this->ViewModel->get_user( $row['ProjectComment']['user_id'], null, 1 );
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							$html = '';
							if( $row['ProjectComment']['user_id'] != $current_user_id ) {
								$html = CHATHTML($row['ProjectComment']['user_id'], $project);
							}

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
					?>


					<div class="items">
						<div class="thumb">
							<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $row['ProjectComment']['user_id'], $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
								<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
							</a>
						</div>
						<div class="description ">
							<div style="font-size: 13px; line-height: 16px;">
								<a href="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'objectives', $project, 'admin' => FALSE ), TRUE ); ?>" class="open-url">
									<?php echo nl2br($row['ProjectComment']['comments']); ?>
								</a>

							</div>
							<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['ProjectComment']['modified']); ?></span>
						</div>
					</div>


				<?php } ?>
				<?php }
				else { ?>
				<div class="no-row-found" >No Annotations</div>
		<?php } ?>
	<?php }elseif( $section == 'mission') { ?>
		<?php if( isset($workspace_comments) && !empty($workspace_comments) ) { ?>
			<?php foreach($workspace_comments as $key => $row) {  ?>
				<?php

					$userDetail = $this->ViewModel->get_user( $row['WorkspaceComment']['user_id'], null, 1 );
					$user_image = SITEURL . 'images/placeholders/user/user_1.png';
					$user_name = 'Not Available';
					$job_title = 'Not Available';
					if(isset($userDetail) && !empty($userDetail)) {
						$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
						$profile_pic = $userDetail['UserDetail']['profile_pic'];
						$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

						$html = '';
						if( $row['WorkspaceComment']['user_id'] != $current_user_id ) {
							$html = CHATHTML($row['WorkspaceComment']['user_id'], $project);
						}

						if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
							$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
						}
					}

				?>


				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $row['WorkspaceComment']['user_id'], $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<a href="<?php echo Router::Url( array( 'controller' => 'missions', 'action' => 'index', 'project' => $project, 'workspace' => $row['WorkspaceComment']['workspace_id'], 'admin' => FALSE ), TRUE ); ?>" class="open-url">
								<?php echo nl2br($row['WorkspaceComment']['comments']); ?>
							</a>
						</div>

						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($row['WorkspaceComment']['modified']); ?></span>

						<a data-original-title="Likes" class="btn btn-xs btn-default tipText ">
							<i class="fa fa-thumbs-o-up"></i>
							<span class="label bg-purple"><?php echo $this->ViewModel->getWorkspaceCommentLikes($row['WorkspaceComment']['id']); ?></span>
						</a>

					</div>
				</div>


			<?php } ?>
		<?php } else { ?>
			<div class="no-row-found" >No Mission Room Comments</div>
		<?php } ?>
	<?php } ?>
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