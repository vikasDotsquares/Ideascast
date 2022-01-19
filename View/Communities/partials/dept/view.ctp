<?php
$department = $data[0]['departments'];
$other = $data[0][0];
$skill_counts = (!empty($data[0]['skill_counts']['totalskills'])) ? $data[0]['skill_counts']['totalskills'] : 0;;
$subject_counts = (!empty($data[0]['subject_counts']['totalsubjects'])) ? $data[0]['subject_counts']['totalsubjects'] : 0;;
$domain_counts = (!empty($data[0]['domain_counts']['totaldomains'])) ? $data[0]['domain_counts']['totaldomains'] : 0;;
$updated_by = $other['updated_by'];
$users = json_decode($other['user_detail'], true);

$people = ( isset($data[0]['details_counts']['totalpeople']) && !empty($data[0]['details_counts']['totalpeople']) ) ? $data[0]['details_counts']['totalpeople'] : 0;

$all_stories = (!empty($data[0][0]['all_stories'])) ? json_decode($data[0][0]['all_stories'], true) : [];
function asrt($a, $b) {
	$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
	$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
    return strcasecmp($t1, $t2);
}
usort($all_stories, 'asrt');

$stories_count = count($all_stories);

$current_org = $this->Permission->current_org();

$detail_title = htmlentities($department['name'], ENT_QUOTES, "UTF-8");
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><i class="departmentwhiteicon"></i> <span class="text-ellipsis pl3"><?php echo $detail_title;?></span></h3>
</div>
<div class="modal-body">
	<div class="row d-flex-s">

		<div class="col-sm-3 col-md-3 col-lg-3 col-dep-1 skill-profile-cols">
			<div class="loc-profile-img">
				<?php if(!empty($department['image'])){ ?>
					<img alt="Profile Image" src="<?php echo SITEURL . COMM_IMAGE_PATH . $department['image'];?>" >
				<?php } ?>
			</div>

			<div class="skill-menu-left">

				<h6>Related</h6>
				<ul>
				<?php
					$sk_link = "javascript:void(0);";
					$sk_class = "";
					if( isset($people) && !empty($people) ){
						$sk_link = Router::Url( array( "controller" => "searches", "action" => "people", "dept" => $department['id'], 'admin' => FALSE ), true );
						$sk_class = "cursor: pointer;";
					}
				?>
				<li><a href="<?php echo $sk_link; ?>" style="<?php echo $sk_class; ?>"><span class="skill-menu-left-text"><i class="skill-menu-icon peoplegreenicon"></i> People </span> <span class="count"><?php echo $people; ?></span> </a></li>
					<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon competenciesgreenicon"></i> Competencies </span> <span class="count"><?php echo ($skill_counts + $subject_counts + $domain_counts); ?></span> </a></li>

					<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon storygreenicon"></i> Stories </span> <span class="count"><?php echo $stories_count; ?></span> </a></li>
				</ul>
				<?php if(isset($updated_by) && !empty($updated_by)) { ?>
				<ul>
					<li>
						<a href="javascript:void(0);" class="location_user_name"><span class="skill-menu-left-text"><i class="skill-menu-icon modifiedgreenicon"></i> <?php echo $updated_by; ?></span></a>
					</li>
					<li>
						<a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon dategreenicon"></i> <?php echo ( isset($department['modified']) && !empty($department['modified']) && $department['modified'] != '0000-00-00 00:00:00' ) ? $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($department['modified'])), $format = 'd M, Y h:i A') : ''; ?>
						</span></a>
					</li>
				</ul>
				<?php } ?>

			</div>

		</div>

		<div class="col-sm-9 col-md-9 col-sm-9 col-dep-2">
			<div class="row d-flex-s s-height100">
				<div class="col-sm-12 col-md-12 col-lg-12 common-tab-sec view-skills-tab left-border">
					<ul class="nav nav-tabs tab-list">
						<li class="active">
							<a data-toggle="tab" class="active" href="#people" aria-expanded="true">People</a>
						</li>
						<li>
							<a data-toggle="tab" href="#competencies" aria-expanded="false">Competencies</a>
						</li>
						<li>
							<a data-toggle="tab" href="#stories" aria-expanded="false">Stories</a>
						</li>
					</ul>


					<!--<h5 class="dep-people-title">People</h5>-->
					<div class="tab-content skills-tab-content">
						<div class="tab-pane fade active in" id="people">

							<div class="dep-people-list-sec">
								<div class="skillpeoplelist">
									<ul>
									<?php
									if( isset($users) && !empty($users) ){
										usort($users, function ($item1, $item2) {
										    return $item1['full_name'] <=> $item2['full_name'];
										});
										foreach($users as $user){
											$profile_pic = $user['profile_pic'];
											if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
												$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
											} else {
												$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
											}
									?>
										<li><div class="community-diff-list">
											<span data-user="<?php echo $user['user_id']; ?>" class="skill-popple-icon" >
												<img class="" alt="User Profile Pic" src="<?php echo $profilesPic; ?>">
											</span>
											<?php if($current_org['organization_id'] != $user['organization']){ ?>
											<i class="communitygray18 tipText show-user-popup" data-user="<?php echo $user['user_id']; ?>" title="" data-original-title="Not In Your Organization"></i>
											<?php } ?>
									</div>
											<span class="skill-popple-info">
												<h6 data-user="<?php echo $user['user_id']; ?>"><?php echo $user['full_name']; ?></h6>
												<p><?php echo $user['job_role']; ?></p>
											</span>
										</li>
									<?php }
									} else { ?>
									<li>No People</li>
									<?php } ?>
									</ul>
								</div>
							</div>

						</div>

						<div class="tab-pane fade" id="competencies">
							<?php
							if(($skill_counts + $subject_counts + $domain_counts) > 0){ ?>
							<?php $competencies = $this->Permission->get_dept_competencies( $department['id'] );
							$all_skills = json_decode($competencies[0][0]['all_skills'], true);
							$all_subjects = json_decode($competencies[0][0]['all_subjects'], true);
							$all_domains = json_decode($competencies[0][0]['all_domains'], true);
							// pr($competencies);
							?>
							<div class="com-list-wrap">
							 <ul class="competencies-ul">
							 	<?php if(isset($all_skills) && !empty($all_skills)){ ?>
							 	<?php foreach ($all_skills as $key => $value) {  ?>
							 	<?php foreach ($value as $k => $val) {  ?>
									<li class="skill-border-left open-desired" data-id="<?php echo $k; ?>" data-type="skill">
										<span class="com-list-bg">
											<i class="com-skills-icon"></i>
											<span class="com-sks-title"><?php echo htmlentities($val, ENT_QUOTES, "UTF-8"); ?></span>
										</span>
									</li>
								<?php } ?>
								<?php } ?>
								<?php } ?>

							 	<?php if(isset($all_subjects) && !empty($all_subjects)){ ?>
							 	<?php foreach ($all_subjects as $key => $value) {  ?>
							 	<?php foreach ($value as $k => $val) {  ?>
									<li class="subjects-border-left open-desired" data-id="<?php echo $k; ?>" data-type="subject">
										<span class="com-list-bg">
											<i class="com-subjects-icon"></i>
											<span class="com-sks-title"><?php echo htmlentities($val, ENT_QUOTES, "UTF-8"); ?></span>
										</span>
									</li>
								<?php } ?>
								<?php } ?>
								<?php } ?>

							 	<?php if(isset($all_domains) && !empty($all_domains)){ ?>
							 	<?php foreach ($all_domains as $key => $value) {  ?>
							 	<?php foreach ($value as $k => $val) {  ?>
									<li class="domain-border-left open-desired" data-id="<?php echo $k; ?>" data-type="domain">
										<span class="com-list-bg">
											<i class="com-domain-icon"></i>
											<span class="com-sks-title"><?php echo htmlentities($val, ENT_QUOTES, "UTF-8"); ?></span>
										</span>
									</li>
								<?php } ?>
								<?php } ?>
								<?php } ?>
								</ul>
							</div>
							<?php }else{ ?>
							No Competencies
							<?php } ?>
						</div>

						<div class="tab-pane fade" id="stories">
							<div class="popuplocationlist" style="overflow: hidden; width: auto; height: 328px;">
							<ul class="loc-names-ul">
								<?php if(isset($all_stories) && !empty($all_stories)) { ?>
								<?php
								foreach ($all_stories as $key => $value) {
								?>
								<li class="loc-names-li story-list-item" data-id="<?php echo $value['id']; ?>">
									<span class="loc-name-list">
										<span class="loc-thumb">
											<?php if(!empty($value['image'])){ ?>
												<img src="<?php echo SITEURL . STORY_IMAGE_PATH . $value['image']; ?>" >
											<?php } ?>
										</span>
										<div class="loc-info">
											<span class="loc-name"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
											<div class="loc-cc-name">
												<span class="com-single-name"><?php echo $value['type']; ?></span>
											</div>
										</div>
									</span>
								</li>
								<?php } ?>
								<?php }else{ ?>
									<li>No Stories</li>
								<?php } ?>
							</ul>
						</div>
						</div>
					</div>

				</div>
			</div>
		</div>

	</div>
</div>

<div class="modal-footer clearfix">
	<button type="button" class="btn outline-green-s pull-left filter-selected-dept tipText" title="Go to Community">Community</button>
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>
<style type="text/css">
	.skill-popple-info h6 {
		cursor: pointer;
	}
</style>
<script type="text/javascript" >
$(function(){

	$.selected_view_type = 'dept';
	$.selected_view = '<?php echo addslashes($department['name']); ?>';

	var active_tab = '<?php echo $tab; ?>';
	$('.nav.nav-tabs a[href="#'+active_tab+'"]').tab('show');

	$('.skill-popple-icon, .skill-popple-info h6, .show-user-popup').off('click').on('click', function(e){
		$('#modal_view_dept').modal('hide');
		$("#popup_modal").modal({
			remote: $js_config.base_url + 'shares/show_profile/'+$(this).data('user')
		})
	})


	$('body').on('click', '.filter-selected-dept', function(event) {
			event.preventDefault();
		if( window.location.href.indexOf("communities") <= 0 ) {
			$('#modal_view_dept').modal('hide');
			var id = '<?php echo $department["id"]; ?>';
			window.location.href = $js_config.base_url + 'communities/index/dept/' + id;
		}

	});

	$(".skillpeoplelist").slimScroll({height: 312, alwaysVisible: true});
	$('.tab-list').on('shown.bs.tab', function(){
		$("ul.competencies-ul").slimScroll({height: 328, alwaysVisible: true});
		$(".popuplocationlist").slimScroll({height: 328, alwaysVisible: true});
	})



	$('.story-list-item').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'stories/view/story/' + data.id
		$('#modal_view_dept').modal('hide');
		$('#story_view').modal({
			remote: url
		})
		.modal('show');
	});


	$('.competencies-ul li.open-desired').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'competencies/view_skills/' + data.id
		if(data.type == 'subject'){
			url = $js_config.base_url + 'competencies/view_subjects/' + data.id
		}
		else if(data.type == 'domain'){
			url = $js_config.base_url + 'competencies/view_domains/' + data.id
		}
		$('#modal_view_dept').modal('hide');
		$('#modal_view_skill').modal({
			remote: url
		})
		.modal('show');
	});

})
</script>