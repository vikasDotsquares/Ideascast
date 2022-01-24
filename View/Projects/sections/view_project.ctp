<?php $data = $this->Permission->project_popup_detail($project_id);
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
	$title = (isset($prj_data['title']) && !empty($prj_data['title'])) ? $prj_data['title'] : '';
	$title = htmlentities($title ,ENT_QUOTES, "UTF-8");
	$type = htmlentities($type ,ENT_QUOTES, "UTF-8");
}

 
?>
<style type="text/css">
	.non-editable {
		pointer-events: none;
	}
</style>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><i class="projectwhite36"></i> <span class="text-ellipsis"><?php echo $title; ?></span></h3>
</div>
<div class="modal-body">
	<div class="row d-flex-s opp-profile-popup allpopuptabs">
		<div class="col-sm-6 opp-profile-col1">
			<ul class="nav nav-tabs">
				<li class="active">
					<a data-toggle="tab" class="active" href="#progrm-profiledetails" aria-expanded="true">Details</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="progrm-profiledetails" class="tab-pane fade active in">
					<div class="prog-details-sec">
						<div class="form-group createdby">
							<label class="control-label">Created By:</label>
							<div class="style-people-com">
								<span class="style-popple-icon-out">
									<a class="style-popple-icon show_profile" href="javascript:void(0);"  data-userid="">
										<img src="<?php echo $creater_image;?>" data-userid="<?php echo $creater_id;?>" class="user-image show_profile" align="left" width="40" height="40">
									</a>
									<?php if($projectCreator[0]->org_id != $current_org['organization_id']){ ?>
										<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization"></i>
									<?php } ?>
								</span>
								<div class="style-people-info">
									<span class="style-people-name"><?php echo $creater_name;?></span>
								    <span class="style-people-title"><?php echo $job_role;?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Created On:</label>
							<div class="details-info"><?php echo date('d M, Y', $prj_data['created']); ?></div>
						</div>
						<div class="form-group">
							<label class="control-label">Type:</label>
							<div class="details-info"><?php echo $type; ?></div>
						</div>
						<div class="form-group">
							<label class="control-label">Schedule:</label>
							<div class="details-info">
								<?php echo date('d M, Y', strtotime($prj_data['start_date'])); ?> → <?php echo date('d M, Y', strtotime($prj_data['end_date'])); ?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Description:</label>
							<div class="details-info">
								<?php echo nl2br(htmlentities($prj_data['objective'] ,ENT_QUOTES, "UTF-8"));?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Outcome:</label>
							<div class="details-info">
								<?php echo nl2br(htmlentities($prj_data['description'] ,ENT_QUOTES, "UTF-8")) ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 opp-profile-col2 left-border">
			<ul class="nav nav-tabs" id="details_tabs">
				<li class="active">
					<a data-toggle="tab" class="active" href="#team-tab" aria-expanded="true">Team</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="team-tab" class="tab-pane fade active in">
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
									
									if(isset($user_role) && !empty($user_role)){
										if($user_role == 'Group Owner'){
											 $user_role = 'Owner';
										}	
										if($user_role == 'Group Sharer'){
											 $user_role = 'Sharer';
										}									 
									}
									
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
										<a class="style-popple-icon show_profile toggle-sharer" style="<?php if($user_role !='Sharer' && $user_role !='Group Sharer') echo "border-color:#666;" ?>" data-original-title="Role: <?php echo $user_role; ?> <br />Click to View Profile"  data-userid="<?php echo $user_id;?>" href="#">
											<img src="<?php echo $profilesPic;?>" class="user-image" align="left" width="36" height="36">
										</a>
										<?php if($val['org_id'] != $current_org['organization_id']){ ?>
										<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization"></i>
										<?php } ?>
									</span>
									<div class="style-people-info">
										<span class="style-people-name show_profile" data-userid="<?php echo $user_id;?>"><?php echo ( isset($user_name) && !empty($user_name) )? trim($user_name) : 'None'; ?></span>
										<span class="style-people-title"> <?php echo ( isset($job_title) && !empty($job_title) )? trim($job_title) : 'None'; ?></span>
									</div>              
								</div>
							</li>
						<?php }
						} ?>	 
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer clearfix">
	<a href="<?php echo SITEURL.'projects/index/'.$project_id; ?>"><button type="button"   class="btn btn-open right"  >Open</button></a>
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>

<style>
</style>

<script>
 
$(function(){
 
	/* opportunity more information page start */
	$('.show_profile').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'shares/show_profile/' + data.userid
		$('#modal_view_program').modal('hide');
		
		setTimeout(()=>{
		$('#popup_modal').modal({
			remote: url
		})
		.modal('show');
		},500);
	});
	
	$('.toggle-sharer').tooltip({
		html: true,
		placement: 'top',
		container: 'body',
		template: '<div class="tooltip tooltip-custom"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	})
	
	$(".prog-details-sec").slimScroll({height: 365, alwaysVisible: true});
	$(".people-com-list").slimScroll({height: 355, alwaysVisible: true});
	
});

</script>