<?php $data = $this->Permission->opportunity_project_detail($pid);
$current_org = $this->Permission->current_org();
// for project request users

if( isset($request_user) && !empty($request_user) ){
	$current_user_id = $request_user;
} else {
	$current_user_id = $this->Session->read('Auth.User.id');
}

$prj_data = array();
$skill_data = array();
$subject_data = array();
$domain_data = array();
$projectUsers =  ( !empty($data[0]['prj']['users']) ) ? json_decode($data[0]['prj']['users'], true) : [];
if( isset($data) && !empty($data) ){
	$prj_data = $data[0]['prj'];
	$skill_data =  ( !empty($data[0]['sks']['skills']) ) ? json_decode($data[0]['sks']['skills'], true) : [];
	$subject_data = ( !empty($data[0]['sbj']['subjects']) ) ? json_decode($data[0]['sbj']['subjects'], true) : [];
	$domain_data = ( !empty($data[0]['dmn']['domains']) ) ? json_decode($data[0]['dmn']['domains'], true) : [];
}

if( !empty($prj_data['id']) ){

	$projectusers = json_decode($prj_data['users']);
	$projectCreator = array();
	if( isset($projectusers) && !empty($projectusers) ){
		foreach($projectusers as $key => $userlist){
			if( isset($userlist->role) && $userlist->role == 'Creator' ){
				$projectCreator[$key] = $userlist;
			}
		}
	}

	$creater_name = (isset($projectCreator[0]->full_name) && !empty($projectCreator[0]->full_name)) ? $projectCreator[0]->full_name : 'N/A';
	$creater_image = (isset($projectCreator[0]->profile_pic) && !empty($projectCreator[0]->profile_pic)) ? SITEURL . USER_PIC_PATH .$projectCreator[0]->profile_pic :  SITEURL . 'images/placeholders/user/user_1.png';
	$job_role = (isset($projectCreator[0]->job_title) && !empty($projectCreator[0]->job_title)) ? $projectCreator[0]->job_title : 'Not Available';
	$creater_id = (isset($projectCreator[0]->user_id) && !empty($projectCreator[0]->user_id)) ? $projectCreator[0]->user_id : 'N/A';

	$type = (isset($prj_data['type']) && !empty($prj_data['type'])) ? $prj_data['type'] : 'N/A';
	$prj_image = (isset($prj_data['image_file']) && !empty($prj_data['image_file'])) ? $prj_data['image_file'] : '';
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><i class="projectwhite48"></i> <span class="text-ellipsis">
	<?php
		echo htmlentities($prj_data['title'], ENT_QUOTES, "UTF-8");
	?></span></h3>
</div>
<div class="modal-body">
	<div class="row d-flex-s opp-profile-popup allpopuptabs">
		<div class="col-sm-6 opp-profile-col1">
			<ul class="nav nav-tabs">
						<li class="active">
							<a data-toggle="tab" class="active" href="#opp-profiledetails" aria-expanded="true">Details</a>
						</li>
						<li class="">
							<a data-toggle="tab" href="#opp-profiledes" aria-expanded="false">Description</a>
						</li>

					</ul>
		<div class="tab-content">
			 <div id="opp-profiledetails" class="tab-pane fade active in">
			 <div class="form-group createdby">
						<label class="control-label">Created By:</label>
						 <div class="style-people-com">
							<span class="style-popple-icon-out">
								<a class="style-popple-icon " href="javascript:void(0);">
									<img src="<?php echo $creater_image;?>" data-userid="<?php echo $creater_id;?>" class="user-image show_profile" align="left" width="40" height="40">
								</a>
							</span>
							<div class="style-people-info">
								<a href="#">
								<span class="style-people-name"><?php echo $creater_name;?></span>
								<span class="style-people-title"><?php echo $job_role;?></span>
								</a>
							</div>
						</div>

					</div>
			 <div class="form-group">
				<label class="control-label">Project Type:</label>
				<div class="details-info">
				<?php
					echo $type;
				?>
				</div>
			 </div>
				<div class="form-group">
				<label class="control-label">Schedule:</label>
				<div class="details-info">
				<?php echo date('d M, Y', strtotime($prj_data['start_date'])); ?> → <?php echo date('d M, Y', strtotime($prj_data['end_date'])); ?>
				</div>
			 </div>
				<div class="form-group">
				<label class="control-label">Outcome:</label>
				<div class="details-info obj-content"><?php echo nl2br($prj_data['objective']);?></div>
			 </div>

			 </div>
			<div id="opp-profiledes" class="tab-pane fade">
			<div class="profiledes-info">
				<?php echo nl2br($prj_data['description']);?>
				</div>
			 </div>
		</div>
		</div>
		<div class="col-sm-6 opp-profile-col2 left-border">

		<ul class="nav nav-tabs">
						<li class="active">
							<a data-toggle="tab" class="active" href="#opppeople" aria-expanded="true">People</a>
						</li>
						<li class="">
							<a data-toggle="tab" href="#oppskills" aria-expanded="false">Skills</a>
						</li>
						<li class="">
							<a data-toggle="tab" href="#oppsubjects" aria-expanded="false">Subjects</a>
						</li>
						<li class="">
							<a data-toggle="tab" href="#oppdomains" aria-expanded="false">Domains</a>
						</li>

		</ul>
		<div class="tab-content">
		<div id="opppeople" class="tab-pane fade active in">
			<div class="people-com-list">
				<ul>
<?php
	if( isset($projectUsers) && !empty($projectUsers) ){
		function asrt($a, $b) {
			$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['full_name']);
			$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['full_name']);
			return strcasecmp($t1, $t2);
		}
		usort($projectUsers, 'asrt');
		foreach( $projectUsers as $key => $val ) {

			$user_role = $val['role'];
			$user_id = $val['user_id'];
			$user_name = $val['full_name'];
			$profile_pic = $val['profile_pic'];
			$job_title = $val['job_title'];
			$org_id = $val['org_id'];
			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
				$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
			} else {
				$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
			}
?>

<li>
	<div class="style-people-com">
		<span class="style-popple-icon-out">
			<a class="style-popple-icon show_profile" data-userid="<?php echo $user_id;?>" href="#">
				<img src="<?php echo $profilesPic;?>" class="user-image" align="left" width="36" height="36">
			</a>
			<?php if($val['org_id'] != $current_org['organization_id']){ ?>
			<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization"></i>
			<?php } ?>
		</span>
		<div class="style-people-info">
			<a href="#">
			<span class="style-people-name"><?php echo ( isset($user_name) && !empty($user_name) )? trim($user_name) : 'None'; ?></span>
			<span class="style-people-title"> <?php echo ( isset($job_title) && !empty($job_title) )? trim($job_title) : 'None'; ?></span>
			</a>
		</div>
	</div>
</li>
<?php }
} ?>
				</ul>
				</div>
			 </div>
			 <div id="oppskills" class="tab-pane fade project_opp_extra">
				<div class="oppprofileskills com-list-wrap">
					<ul>
					<?php
					//pr($request_user);
					//pr($skill_data);
					if( isset($skill_data) && !empty($skill_data) ){

						foreach( $skill_data as $key => $value ){

							//pr($value['team_skill']);
							//pr($value['team_count']);
							//pr($value);
							$currentUserskills = '';
							if(isset($value['users_skill']) && !empty($value['users_skill'])){

								$users_arr = explode(',', $value['users_skill']);
								//echo $current_user_id;
								//pr($users_arr);
								if( in_array($current_user_id, $users_arr) ){
									$currentUserskills = 'user_selected_list';
								}
							}

							$team_count = ( !empty($value['team_count']) && $value['team_count'] > 0 ) ? $value['team_count'] : 0;

					?>
						<li class="skill-border-left">
							<span class="com-list-bg <?php echo $currentUserskills;?>"  >
								<i class="com-skills-icon tipText" title="Skill"></i>

								<?php if(isset($value['team_skill']) && !empty($value['team_skill'])){
									$users_arr = explode(',', $value['team_skill']);
									$total_users = count($users_arr);
								 ?>
									<i class="activegreen tipText" title="<?php echo $team_count; ?> Team <?php echo ($team_count > 1) ? 'Members Have' : 'Member Has'; ?> This Skill"></i>
								<?php }else{ ?>
									<i class="inactivered tipText" title="No Team Members Have This Skill"></i>
								<?php } ?>
								<span class="com-sks-title open-comp-modal" data-type="skill" data-id="<?php echo $value['id']; ?>"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
							</span>
						</li>
					<?php }
					} else { ?>
						<li class="skill-border-left">No Skills</li>
					<?php } ?>
					</ul>
					</div>
			  </div>
			 <div id="oppsubjects" class="tab-pane fade project_opp_extra">
			<div class="oppprofilesubjects com-list-wrap">
				<ul>
				<?php if( isset($subject_data) && !empty($subject_data) ){
					foreach( $subject_data as $key => $value ){

						$currentUserSubject = '';
						if(isset($value['users_subject']) && !empty($value['users_subject'])){
							$users_arr = explode(',', $value['users_subject']);

							if( in_array($current_user_id, $users_arr) ){
								$currentUserSubject = 'user_selected_list';
							}
						}

						$team_count = ( !empty($value['team_count']) && $value['team_count'] > 0 ) ? $value['team_count'] : 0;


				?>
			<li class="subjects-border-left">
				<span class="com-list-bg <?php echo $currentUserSubject;?>" >
					<i class="com-subjects-icon tipText" title="Subject"></i>
					<?php if(isset($value['team_subject']) && !empty($value['team_subject'])){
						$users_arr = explode(',', $value['team_subject']);
						$total_users = count($users_arr);
					 ?>
						<i class="activegreen tipText" title="<?php echo $team_count; ?> Team <?php echo ($team_count > 1) ? 'Members Have' : 'Member Has'; ?> This Subject"></i>
					<?php }else{ ?>
						<i class="inactivered tipText" title="No Team Members Have This Subject"></i>
					<?php } ?>
					<span class="com-sks-title open-comp-modal" data-type="subject" data-id="<?php echo $value['id']; ?>"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
				</span>
			</li>
				<?php }
				} else { ?>
				<li class="subjects-border-left">No Subjects</li>
				<?php } ?>
				</ul>
				</div>
		  </div>
			 <div id="oppdomains" class="tab-pane fade project_opp_extra">
			<div class="oppprofiledomains com-list-wrap">
				<ul>
				<?php if( isset($domain_data) && !empty($domain_data) ){
					foreach( $domain_data as $key => $value ){

						$currentUserDomain = '';
						if(isset($value['users_domain']) && !empty($value['users_domain'])){
							$users_arr = explode(',', $value['users_domain']);
							if( in_array($current_user_id, $users_arr) ){
								$currentUserDomain = 'user_selected_list';
							}
						}
					$team_count = ( !empty($value['team_count']) && $value['team_count'] > 0 ) ? $value['team_count'] : 0;
				?>
					<li class="domain-border-left" >
						<span class="com-list-bg <?php echo $currentUserDomain;?>">
							<i class="com-domain-icon tipText" title="Domain"></i>
							<?php if(isset($value['team_domain']) && !empty($value['team_domain'])){
								$users_arr = explode(',', $value['team_domain']);
								$total_users = count($users_arr);
							 ?>
								<i class="activegreen tipText" title="<?php echo $team_count; ?> Team <?php echo ($team_count > 1) ? 'Members Have' : 'Member Has'; ?> This Domain"></i>
							<?php }else{ ?>
								<i class="inactivered tipText" title="No Team Members Have This Domain"></i>
							<?php } ?>
							<span class="com-sks-title open-comp-modal" data-type="domain" data-id="<?php echo $value['id']; ?>"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
						</span>
					</li>
				<?php }
				} else { ?>
					<li class="domain-border-left">No Domains</li>
				<?php } ?>
				</ul>
				</div>
		  </div>


		</div>
		</div>
	</div>
</div>

<div class="modal-footer clearfix">
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>
<script>
$(function(){
	var active_tab = '<?php echo $tab;?>';
	$('.nav.nav-tabs a[href="#'+active_tab+'"]').tab('show');

	/* opportunity more information page start */
	$('#modal_information .show_profile').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'shares/show_profile/' + data.userid
		$('#modal_information').modal('hide');
		$('#popup_modal').modal({
			remote: url
		})
		.modal('show');
	});

	$('#modal_information .project_opp_extra .open-comp-modal').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'competencies/view_skills/' + data.id
		if(data.type == 'subject'){
			url = $js_config.base_url + 'competencies/view_subjects/' + data.id
		}
		else if(data.type == 'domain'){
			url = $js_config.base_url + 'competencies/view_domains/' + data.id
		}
		$('#modal_information').modal('hide');
		$('#modal_view_skill').modal({
			remote: url
		})
		.modal('show');
	});
	/* opportunity more information page end */
	$(".people-com-list ul").slimScroll({height: 312, alwaysVisible: true});
	$(".obj-content").slimScroll({height: 116, alwaysVisible: true});
})
</script>