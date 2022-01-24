<?php
	$wsp_info = $this->Scratch->wsp_info($workspace_id);
	$wsp_info = $wsp_info[0];
	// pr($wsp_info, 1);
	$wsp_detail = $wsp_info['workspaces'];
	$user_detail = $wsp_info['ud'];
	$permit_detail = $wsp_info['up'];
	$others = $wsp_info[0];

	$creator_name = $others['full_name'];
	$creator_id = $user_detail['user_id'];
	$profile_pic = SITEURL . 'images/placeholders/user/user_1.png';
	if(isset($user_detail['profile_pic']) && !empty($user_detail['profile_pic'])){
		$profile_pic = SITEURL . USER_PIC_PATH . $user_detail['profile_pic'];
	}
	$job_title = 'Not Available';
	if(isset($user_detail['job_title']) && !empty($user_detail['job_title'])){
		$job_title = $user_detail['job_title'];
	}

	$current_user_id = $this->Session->read('Auth.User.id');
	$current_org = $this->Permission->current_org();
	$html = '';
	if( $creator_id != $current_user_id ) {
		$html = CHATHTML($creator_id, $project_id);
	}
?>
<div class="task_information_inner">
	<div class="row">
		<div class="col-sm-4">
			<label>Created By:</label>
			<div class="style-people-com ">
				<span class="style-popple-icon-out">
					<div class="style-people-com">
						<a class="style-popple-icons " data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $creator_id, 'admin' => FALSE ), TRUE ); ?>" data-target="#popup_modal" data-toggle="modal">
							<span class="style-popple-icon-out">
								<span class="style-popple-icon" style="cursor: default;">
									<img src="<?php echo $profile_pic; ?>" class="pophoverss" style="cursor:pointer;" align="left" width="36" height="36" data-content="<div><p><?php echo $creator_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
		                        </span>
		                        <?php if($current_org['organization_id'] != $user_detail['organization_id']){ ?>
		                            <i class="communitygray18 community-g tipText"  style="cursor: pointer;" data-user="<?php echo $creator_id; ?>" title="" data-original-title="Not In Your Organization"></i>
		                        <?php } ?>
							</span>
						</a>
					</div>
				</span>
				<div class="style-people-info">
					<a href="#">
						<span class="style-people-name" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $creator_id, 'admin' => FALSE ), TRUE ); ?>" data-target="#popup_modal" data-toggle="modal"><?php echo htmlentities($creator_name, ENT_QUOTES, "UTF-8"); ?></span>
						<span class="style-people-title" style="cursor: default;"><?php echo  (htmlentities($job_title, ENT_QUOTES, "UTF-8")); ?></span>
					</a>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<label>Created On:</label>
			<div class="created-info"><?php echo date('d M, Y', strtotime($wsp_detail['created'])); ?></div>
		</div>
	</div>

    <div class="row">
        <div class="col-lg-12">
		<div class="proj-task-description">
			<label> Description:</label>
			<p><?php echo (isset($wsp_detail['description']) && !empty($wsp_detail['description'])) ? nl2br(htmlentities($wsp_detail['description'], ENT_QUOTES, "UTF-8")) : 'None'; ?></p>
			<label> Outcome:</label>
			<p><?php echo (isset($wsp_detail['outcome']) && !empty($wsp_detail['outcome'])) ? nl2br(htmlentities($wsp_detail['outcome'], ENT_QUOTES, "UTF-8") ) : 'None'; ?></p>
		</div>
		</div>
    </div>
</div>
<div class="prj-detail-task-header"> <h4>Workspace Details</h4></div>