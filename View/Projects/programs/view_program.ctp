<?php
$program_data = $progs[0]['progs'];
// pr($progs);
$progName = htmlentities($program_data['name'] ,ENT_QUOTES, "UTF-8");
$current_org = $this->Permission->current_org();
?>
<?php
	$stakeholders = (isset($progs[0]['pusr']['stakeholders']) && !empty($progs[0]['pusr']['stakeholders'])) ? json_decode($progs[0]['pusr']['stakeholders'], true) : [];
	$team = (isset($progs[0]['teams']['team']) && !empty($progs[0]['teams']['team'])) ? json_decode($progs[0]['teams']['team'], true) : [];

	function compareASCIIForm($a, $b) {
	    $at = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
	    $bt = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
	    return strcasecmp($at, $bt);
	}

	if(isset($stakeholders) && !empty($stakeholders)){
		usort($stakeholders, 'compareASCIIForm');
		$stakeholders = array_map(function($v){
			$d['id'] = $v['id'];
			$d['title'] = htmlentities($v['title'] ,ENT_QUOTES, "UTF-8");
			$d['org_id'] = $v['org_id'];
			$d['profile_pic'] = $v['profile_pic'];
			$d['job_title'] = $v['job_title'];
			return $d;
		}, $stakeholders);
	}

	if(isset($team) && !empty($team)){
		usort($team, 'compareASCIIForm');
		$team = array_map(function($v){
			$d['id'] = $v['id'];
			$d['title'] = htmlentities($v['title'] ,ENT_QUOTES, "UTF-8");
			$d['org_id'] = $v['org_id'];
			$d['profile_pic'] = $v['profile_pic'];
			$d['job_title'] = $v['job_title'];
			return $d;
		}, $team);
	}
	$team = array_map("unserialize", array_unique(array_map("serialize", $team)));

	$all_projects = $this->Scratch->program_projects_data($progs[0]['progs']['id']);
	$project_list = [];
	if(isset($all_projects) && !empty($all_projects)){
		foreach ($all_projects as $key => $value) {
			$project_list[] = (isset($value['clevels']['conf_level']) && !empty($value['clevels']['conf_level'])) ? json_decode($value['clevels']['conf_level'], true)[0] : [];
		}
	}
	if(isset($project_list) && !empty($project_list)){
		uasort($project_list, function($a, $b) {
		    $at = iconv('UTF-8', 'ASCII//TRANSLIT', $a['project_start_date']);
		    $bt = iconv('UTF-8', 'ASCII//TRANSLIT', $b['project_start_date']);
		    return strcasecmp($at, $bt);
		});
	}
?>
<style type="text/css">
	.non-editable {
		pointer-events: none;
	}
</style>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><i class="programwhite36"></i> <span class="text-ellipsis"><?php echo $progName; ?></span></h3>
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
							<?php
							$profile_pic = $program_data['profile_pic'];
							if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
								$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
							} else {
								$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
							}
							 ?>
							<label class="control-label">Created By:</label>
							<div class="style-people-com">
								<span class="style-popple-icon-out">
									<a class="style-popple-icon show_profile" href="javascript:void(0);"  data-userid="<?php echo $program_data['created_by']; ?>">
										<img src="<?php echo $profilesPic; ?>" class="user-image " align="left" width="40" height="40">
									</a>
									<?php if($current_org['organization_id'] != $program_data['organization_id']){ ?>
									<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization"></i>
									<?php } ?>
								</span>
								<div class="style-people-info">
									<span class="style-people-name" data-userid="<?php echo $program_data['created_by']; ?>"><?php echo $program_data['creator']; ?></span>
									<span class="style-people-title"><?php echo $program_data['job_title']; ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Created On:</label>
							<div class="details-info"><?php echo date('d M, Y', strtotime($program_data['created_on'])); ?></div>
						</div>
						<div class="form-group">
							<label class="control-label">Type:</label>
							<div class="details-info"> <?php echo htmlentities($program_data['type'] ,ENT_QUOTES, "UTF-8"); ?>  </div>
						</div>
						<div class="form-group">
							<label class="control-label">Schedule:</label>
							<div class="details-info">
								<?php if( (isset($progs[0]['pdets']['stdate']) && !empty($progs[0]['pdets']['stdate'])) && (isset($progs[0]['pdets']['endate']) && !empty($progs[0]['pdets']['endate'])) ){ ?>
								<?php echo date('d M, Y', strtotime($progs[0]['pdets']['stdate'])); ?> → <?php echo date('d M, Y', strtotime($progs[0]['pdets']['endate'])); ?>
								<?php }else{
									echo 'No Schedule';
								} ?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Description:</label>
							<div class="details-info">
								<?php echo (!empty($program_data['description'])) ? nl2br(htmlentities($program_data['description'] ,ENT_QUOTES, "UTF-8")) : 'None'; ?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Outcome:</label>
							<div class="details-info">
								<?php echo (!empty($program_data['outcome'])) ? nl2br(htmlentities($program_data['outcome'] ,ENT_QUOTES, "UTF-8")) : 'None';  ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 opp-profile-col2 left-border">
			<ul class="nav nav-tabs" id="details_tabs">
				<li class="active">
					<a data-toggle="tab" class="active" href="#stakeholder-tab" aria-expanded="true">Stakeholders</a>
				</li>
				<li class="">
					<a data-toggle="tab" href="#progprojects-tab" aria-expanded="false">Projects</a>
				</li>
				<li class="">
					<a data-toggle="tab" href="#progteam-tab" aria-expanded="false">Team</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="stakeholder-tab" class="tab-pane fade active in">
					<div class="people-com-list">
						<?php if(isset($stakeholders) && !empty($stakeholders)){ ?>
						<ul>
							<?php foreach ($stakeholders as $key => $value) {
								$profile_pics = $value['profile_pic'];
								if(!empty($profile_pics) && file_exists(USER_PIC_PATH.$profile_pics)){
									$profilesPics = SITEURL.USER_PIC_PATH.$profile_pics;
								} else {
									$profilesPics = SITEURL.'images/placeholders/user/user_1.png';
								}
							?>
							<li>
								<div class="style-people-com">
									<span class="style-popple-icon-out">
										<a class="style-popple-icon show_profile" data-userid="<?php echo $value['id']; ?>" href="#">
											<img src="<?php echo $profilesPics; ?>" class="user-image" align="left" width="36" height="36">
										</a>
										<?php if($current_org['organization_id'] != $value['org_id']){ ?>
										<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization"></i>
										<?php } ?>
									</span>
									<div class="style-people-info">
										<span class="style-people-name" data-userid="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></span>
										<span class="style-people-title"><?php echo $value['job_title']; ?></span>
									</div>
								</div>
							</li>
							<?php } ?>
						</ul>
						<?php }else{ ?>
						No Stakeholders
						<?php } ?>
					</div>
				</div>
				<div id="progprojects-tab" class="tab-pane fade">
					<div class="progprojectstablist">
						<?php if(isset($project_list) && !empty($project_list)){ ?>
						<ul>
							<?php foreach ($project_list as $key => $value) {  ?>
							<?php
							$status_flag = '';
							$status_tip = '';
							if(isset($value['project_status']) && !empty($value['project_status'])){
								if($value['project_status'] == 1){
									$status_flag = 'overdue';
									$status_tip = 'Overdue';
								}
								else if($value['project_status'] == 2){
									$status_flag = 'progressing';
									$status_tip = 'In Progress';
								}
								else if($value['project_status'] == 3){
									$status_flag = 'not_started';
									$status_tip = 'Not Started';
								}
								else if($value['project_status'] == 4){
									$status_flag = 'completed';
									$status_tip = 'Completed';
								}
								else if($value['project_status'] == 5){
									$status_flag = 'not_set';
									$status_tip = 'Not Set';
								}
							}
							 ?>
							<li>
								<div class="opp-project-details">
									<div class="opp-project-left <?php echo str_replace('panel-', 'project-', $value['project_color']); ?>">
										<i class="projectwhite-icon"></i>
									</div>
									<div class="opp-project-middle">
										<a class="opp-project-name" <?php if(empty($value['project_role'])) { ?> style="cursor: default;" <?php } ?> <?php if(!empty($value['project_role'])) { ?> href="<?php echo Router::url(['controller' => 'projects', 'action' => 'index', $value['project_id'], 'admin' => FALSE], TRUE) ?>" <?php } ?>><?php echo htmlentities($value['project_title'] ,ENT_QUOTES, "UTF-8"); ?></a>
										<span class="opp-project-date">
											<?php if( (isset($value['project_start_date']) && !empty($value['project_start_date'])) && (isset($value['project_end_date']) && !empty($value['project_end_date'])) ){ ?>
											<?php echo date('d M, Y', strtotime($value['project_start_date'])); ?> → <?php echo date('d M, Y', strtotime($value['project_end_date'])); ?>
											<?php }else{
												echo 'No Schedule';
											} ?>
										</span>
									</div>
									<div class="opp-pss fl-icon">
										<i class="flag <?php echo $status_flag; ?> tipText" title="<?php echo $status_tip; ?>"></i>
										<?php
										if(isset($value['level']) && !empty($value['level'])){
											$level_value = $value['level'] ;
											$level_count = $value['level_count'] ;
											if($level_value > 0){
												$level_value_current = $level_value.'%';
												if($level_count < 2){
													$level_value_tip = 'For '.$level_count.' Task';
												}else{
													$level_value_tip = 'For '.$level_count.' Tasks';
												}
												$level_value_tip_class = 'cost-tooltip';
											}else{
												$level_value_current = '';
												$level_value_tip = '';
												$level_value_tip_class = '';
											}
									?>
										<i class="level-ts cost-tooltip <?php echo $value['confidence_arrow']; ?>" title="<?php echo $value['confidence_level']; ?> Confidence Level <br /><?php echo $value['level']; ?>% <?php echo $level_value_tip; ?>"></i>
									<?php }else{ ?>
										<i class="level-ts notsetgrey tipText" title="" data-original-title="Confidence Level Not Set"></i>
									<?php } ?>
									</div>
								</div>
							</li>
							<?php } ?>
						</ul>
						<?php }else{ ?>
						No Projects
						<?php } ?>
					</div>
				</div>
				<div id="progteam-tab" class="tab-pane fade">
					<div class="people-com-list">
						<?php if(isset($team) && !empty($team)){ ?>
						<ul>
							<?php foreach ($team as $key => $value) {
								$profile_pics = $value['profile_pic'];
								if(!empty($profile_pics) && file_exists(USER_PIC_PATH.$profile_pics)){
									$profilesPics = SITEURL.USER_PIC_PATH.$profile_pics;
								} else {
									$profilesPics = SITEURL.'images/placeholders/user/user_1.png';
								}
							?>
							<li>
								<div class="style-people-com">
									<span class="style-popple-icon-out">
										<a class="style-popple-icon show_profile" data-userid="<?php echo $value['id']; ?>" href="#" >
											<img src="<?php echo $profilesPics; ?>" class="user-image" align="left" width="36" height="36">
										</a>
										<?php if($current_org['organization_id'] != $value['org_id']){ ?>
										<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization"></i>
										<?php } ?>
									</span>
									<div class="style-people-info">
										<span class="style-people-name" data-userid="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></span>
										<span class="style-people-title"><?php echo $value['job_title']; ?></span>
									</div>
								</div>
							</li>
							<?php } ?>
						</ul>
						<?php }else{ ?>
						No Team
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer clearfix">
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>

<style>
</style>

<script>
$(function(){
	var active_tab = '<?php echo $tab; ?>';
	$('#details_tabs a[href="#'+active_tab+'"]').tab('show');

	$(".prog-details-sec").slimScroll({height: 365, alwaysVisible: true});
	$(".people-com-list").slimScroll({height: 355, alwaysVisible: true});

	$("#details_tabs").on('shown.bs.tab', function(e){
		$(".progprojectstablist").slimScroll({height: 355, alwaysVisible: true});
		$(".people-com-list").slimScroll({height: 355, alwaysVisible: true});
	})

	$('.show_profile, .style-people-name').off('click').on('click', function(event) {
		event.preventDefault();
		var user_id = $(this).data('userid');
		$('#modal_view_program').modal('hide');
		setTimeout(()=>{
			$('#popup_modal').modal({
				remote: $js_config.base_url + 'shares/show_profile/' + user_id
			})
			.modal('show');
		},500)
	});
	$('.cost-tooltip').tooltip({
		'placement': 'top',
		'container': 'body',
		'html': true
	})

})

</script>