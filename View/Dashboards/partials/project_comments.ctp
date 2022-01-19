<style type="text/css">
	.todo-tooltip {
		text-transform: none !important;
	}
</style>
<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>

<?php
	$userID = null;
	if( isset($userId) && !empty($userId) ) {
		$userID = $userId;
	}

	$userPermissions = $this->ViewModel->userPermissions($project);
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


	$all_comments = $todo_comments_count = $blog_comments_count = $wiki_comments_count =  $project_annotate_count = $workspace_comments_count = 0;

		// GET PROJECT TODO COMMENTS
		$todo_comments_count = 0;
		$todo_data = $this->ViewModel->getTodoComments($project, $userID);

		$todo_comments_count = (isset($todo_data) && !empty($todo_data))? count( $todo_data ) :0;
		// pr($todo_data);

		// GET PROJECT BLOG COMMENTS
		$blog_comments_count = 0;
		$blog_data = $this->ViewModel->getBlogComments($project, $userID);
		$blog_comments_count = (isset($blog_data) && !empty($blog_data))? count( $blog_data ) : 0;

		// GET PROJECT WIKI COMMENTS
		$wiki_comments_count = 0;
		$wiki_data = $this->ViewModel->getWikiComments($project, $userID);
		$wiki_comments_count =  (isset($wiki_data) && !empty($wiki_data))? count( $wiki_data ) : 0;

		// GET PROJECT ANNOTATIONS
		$project_annotate = $this->ViewModel->getProjectComments($project, $userID);

		// GET PROJECT WORKSPACES
		$project_workspaces = get_project_workspace($project, TRUE);
		$workspace_comments = $this->ViewModel->getWorkspaceComments($project_workspaces, $userID);


		$project_annotate_count = ( !empty($project_annotate) && !empty($permit_owner) ) ? count($project_annotate) : 0;
		$workspace_comments_count = (isset($workspace_comments) && !empty($workspace_comments) ) ? count($workspace_comments) : 0;
		// pr($workspace_comments);

		$all_comments = $todo_comments_count + $blog_comments_count + $wiki_comments_count + $workspace_comments_count;
		if($permit_owner) {
			$all_comments += $project_annotate_count;
		}
		// pr($todo_comments_count);



	/***** Get Project Owner ******/




		?>
<h3 class="tabing-head">Latest Comments
	<span class="btn btn-default btn-xs tipText" title="Total Comments"><i class="check-icon" aria-hidden="true"></i> <?php echo $all_comments; ?></span>
</h3>
<ul id="tabs" class="nav nav-tabs center_tabs" data-tabs="tabs">

	<li class="active">
		<a href="#<?php echo($slug); ?>comments_todo" data-toggle="tab" class="todo-link" title="To-do Comments" data-section="todo"><i class="fa fa-list-ul border-todo" aria-hidden="true"></i> <?php echo  $todo_comments_count ; ?></a>
	</li>

	<li class="not-triggered">
		<a href="#<?php echo($slug); ?>comments_blog" class="comm-sub-setion blog tipText" data-toggle="tab" title="Blog Comments" data-section="blog"><i class="blog-icon" aria-hidden="true"></i> <?php echo $blog_comments_count; ?></a>
	</li>

	<li class="not-triggered">
		<a href="#<?php echo($slug); ?>comments_wiki" class="comm-sub-setion tipText" data-toggle="tab" title="Wiki Comments"  data-section="wiki"><i class="fab fa-wikipedia-w" aria-hidden="true"></i> <?php echo  $wiki_comments_count; ?></a>
	</li>

	<?php if($permit_owner > 0) { ?>
	<li class="not-triggered">
		<a href="#<?php echo($slug); ?>comments_annotate" class="comm-sub-setion tipText" data-toggle="tab" title="Annotations" data-section="annotate"><i class="annotate-icon" aria-hidden="true"></i> <?php echo $project_annotate_count; ?></a>
	</li>
	<li class="not-triggered">
		<a href="#<?php echo($slug); ?>comments_mission" class="comm-sub-setion tipText" data-toggle="tab" title="Mission Room Comments" data-section="mission"><i class="missions-icon" aria-hidden="true"></i> <?php echo $workspace_comments_count; ?></a>
	</li>
	<?php } ?>
 </ul>

<div id="my-tab-content" class="tab-content comments-tab-content ">
	<div class="tab-pane active" id="<?php echo($slug); ?>comments_todo">
		<?php
			// GET PROJECT TODO COMMENTS ?>
		<?php if( isset($todo_data) && !empty($todo_data) ) { ?>
			<?php foreach($todo_data as $key => $row) {
					$comments = $row['DoListComment'];

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
					$comment_attachments = $this->ViewModel->getTodoCommentAttachments($comments['id']);
				?>
				<div class="items">
					<div class="thumb">
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $comments['user_id'], $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image " align="left" width="40" height="40"  >
						</a>

					</div>
					<div class="description ">
						<div style="font-size: 13px; line-height: 16px;">
							<a href="<?php echo Router::Url( array( 'controller' => 'todos', 'action' => 'index', 'project' => $project, 'dolist_id' => $comments['do_list_id'], 'admin' => FALSE ), TRUE ); ?>" class="open-url">
								<?php echo nl2br($comments['comments']); ?>
							</a>
						</div>
						<div style="font-size: 13px; line-height: 16px;">
								<ul>
							<?php if($comment_attachments) {
									foreach($comment_attachments as $cak => $cav) {
										$ext = pathinfo($cav['DoListCommentUpload']['file_name']);

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
						<span style="font-weight: 600; font-size: 12px"><?php echo _displayDate($comments['modified']); ?></span>

						<a data-original-title="Likes" class="btn btn-xs btn-default tipText ">
							<i class="fa fa-thumbs-o-up"></i>
							<span class="label bg-purple"><?php echo $this->ViewModel->getTodoCommentLikes($comments['id']); ?></span>
						</a>

					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
		<div class="no-row-found" >No To-dos Comments</div>
		<?php } ?>
	</div>

	<div class="tab-pane" id="<?php echo($slug); ?>comments_blog"></div>

	<div class="tab-pane" id="<?php echo($slug); ?>comments_wiki"></div>

	<div class="tab-pane" id="<?php echo($slug); ?>comments_annotate"></div>

	<div class="tab-pane" id="<?php echo($slug); ?>comments_mission"></div>

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
	$('.todo-link').tooltip({
        placement : 'top',
        trigger : 'hover',
		container: 'body',
		template: '<div class="tooltip todo-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
    });
})
</script>