<?php
	$summary_options = $this->Permission->summary_details($project_id);
	$creater_name = (isset($summary_options['0']['0']['full_name']) && !empty($summary_options['0']['0']['full_name'])) ? $summary_options['0']['0']['full_name'] : 'N/A';
	$creater_image = (isset($summary_options['0']['ud']) && !empty($summary_options['0']['ud']['profile_pic'])) ? SITEURL . USER_PIC_PATH .$summary_options['0']['ud']['profile_pic'] :  SITEURL . 'images/placeholders/user/user_1.png';
	$job_role = (isset($summary_options['0']['ud']['job_title']) && !empty($summary_options['0']['ud']['job_title'])) ? $summary_options['0']['ud']['job_title'] : 'Not Available';
	$creater_id = (isset($summary_options['0']['up']['user_id']) && !empty($summary_options['0']['up']['user_id'])) ? $summary_options['0']['up']['user_id'] : 'N/A';
	$project_image = (isset($summary_options['0']['projects']['image_file']) && !empty($summary_options['0']['projects']['image_file'])) ? $summary_options['0']['projects']['image_file'] : '';
	$objective = (isset($summary_options['0']['projects']['objective']) && !empty($summary_options['0']['projects']['objective'])) ? $summary_options['0']['projects']['objective'] : '';
	$description = (isset($summary_options['0']['projects']['description']) && !empty($summary_options['0']['projects']['description'])) ? $summary_options['0']['projects']['description'] : '';
	$type = (isset($summary_options['0']['ad']['title']) && !empty($summary_options['0']['ad']['title'])) ? $summary_options['0']['ad']['title'] : 'N/A';
	$created = (isset($summary_options['0']['projects']['created']) && !empty($summary_options['0']['projects']['created'])) ?  date('Y-m-d',($summary_options['0']['projects']['created'])) : '';
	//pr($summary_options);
	$current_user_id = $this->Session->read('Auth.User.id');
	$current_org = $this->Permission->current_org();
	$html = '';
	if( $creater_id != $current_user_id ) {
		$html = CHATHTML($creater_id,$project_id);
	}
	$current_org_other = $this->Permission->current_org($creater_id);
	$pclass = '';
	$bg = 'url("'.SITEURL.'/uploads/project/'.$project_image.'")';
	if(!isset($project_image) || empty($project_image)){
		$pclass = 'bgtext';
		$bg = 'transparent';
	}
?>

<div class="wsp-task-info-top-left-inner <?php echo $pclass; ?>">
	<div class="wsp-task-info-top-image">
		<style>
		.wsp-task-info-top-image{ background: <?php echo $bg; ?>;
		background-size: cover;
		background-position: center;
		}
		</style>
	</div>
	<div class="wsp-task-info-details">
		<div class="wsp-task-info-details-text">
			<div class="wsp-task-text-scroll">
				<div class="row">
					<div class="col-sm-4">
						<label>Created By:</label>
						<div class="style-people-com ">
							<span class="style-popple-icon-out">
								<div class="style-people-com">
									<a class="style-popple-icons " data-remote="<?php echo SITEURL; ?>/shares/show_profile/<?php echo  $creater_id; ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal">
										<span class="style-popple-icon-out">
											<span class="style-popple-icon" style="cursor: default;">
												<img src="<?php echo $creater_image; ?>" class="pophoverss" style="cursor:pointer;" align="left" width="36" height="36" data-content="<div><p><?php echo $creater_name; ?></p><p><?php echo $job_role; ?></p><?php echo $html; ?></div>">
												<?php   if($current_org['organization_id'] != $current_org_other['organization_id']){ ?>
												<i class="communitygray18 tipText community-g" data-original-title="Not In Your Organization" ></i>
												<?php }  ?>
											</span>
										</span>
									</a>
								</div>
							</span>
							<div class="style-people-info">
								<a href="#">
									<span class="style-people-name" data-remote="<?php echo SITEURL; ?>/shares/show_profile/<?php echo  $creater_id; ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal"><?php echo $creater_name; ?></span>
									<span class="style-people-title" style="cursor: default;"> <?php echo $job_role; ?></span>
								</a>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<label>Created On:</label>
						<div class="created-info"> <?php echo date('d M, Y',strtotime($created)); ?> </div>
					</div>
					<div class="col-sm-4">
						<label>Type:</label>
						<div class="created-info"> <?php echo htmlentities($type, ENT_QUOTES, "UTF-8"); ?> </div>
					</div>
				</div>
				<div class="wsp-description">
					<label> Description:</label>
					<p><?php echo nl2br(htmlentities($description, ENT_QUOTES, "UTF-8") ); ?></p>
					<label> Outcome:</label>
					<p><?php echo nl2br(htmlentities($objective, ENT_QUOTES, "UTF-8") ); ?></p>
				</div>
			</div>
		</div>
		<div class="wsp-task-info-details-heading prj-detail-header "> <h4>Project Details</h4> <i class="down-arrow-white"></i><i class="up-arrow-white"></i></div>
	</div>
</div>