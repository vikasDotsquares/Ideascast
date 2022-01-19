<?php
if(isset($data) && !empty($data)){
$data = $data[0];

	$stories = $data['stories'];
	$detail = $data[0];
	// pr($data);
	$owner_id = ( isset($stories['created_by']) && !empty($stories['created_by']) ) ? $stories['created_by'] : '';

	$updated_by = ( isset($detail['updated_by']) && !empty($detail['updated_by']) ) ? $detail['updated_by'] : '';
	$created_by = ( isset($detail['created_by']) && !empty($detail['created_by']) ) ? $detail['created_by'] : '';

	$story_type = $data['story_types']['story_type'];

	$total_people = ( isset($data['details_counts']['total_people']) && !empty($data['details_counts']['total_people']) ) ? $data['details_counts']['total_people'] : 0;
	$total_organization = ( isset($data['org_counts']['total_organization']) && !empty($data['org_counts']['total_organization']) ) ? $data['org_counts']['total_organization'] : 0;
	$total_location = ( isset($data['location_counts']['total_location']) && !empty($data['location_counts']['total_location']) ) ? $data['location_counts']['total_location'] : 0;
	$total_department = ( isset($data['dept_counts']['total_department']) && !empty($data['dept_counts']['total_department']) ) ? $data['dept_counts']['total_department'] : 0;
	$total_skills = ( isset($data['skill_counts']['total_skills']) && !empty($data['skill_counts']['total_skills']) ) ? $data['skill_counts']['total_skills'] : 0;
	$total_subjects = ( isset($data['subject_counts']['total_subjects']) && !empty($data['subject_counts']['total_subjects']) ) ? $data['subject_counts']['total_subjects'] : 0;
	$total_domains = ( isset($data['domain_counts']['total_domains']) && !empty($data['domain_counts']['total_domains']) ) ? $data['domain_counts']['total_domains'] : 0;
	$total_link = ( isset($data['link_counts']['total_link']) && !empty($data['link_counts']['total_link']) ) ? $data['link_counts']['total_link'] : 0;
	$total_file = ( isset($data['file_counts']['total_file']) && !empty($data['file_counts']['total_file']) ) ? $data['file_counts']['total_file'] : 0;
	$total_story = ( isset($data['story_counts']['total_story']) && !empty($data['story_counts']['total_story']) ) ? $data['story_counts']['total_story'] : 0;

	$total_competency = ($total_skills + $total_subjects + $total_domains);

	$detail_title = htmlentities($stories['name'], ENT_QUOTES, "UTF-8");

	$all_links = (!empty($detail['all_links'])) ? json_decode($detail['all_links'], true) : [];
	$all_files = (!empty($detail['all_files'])) ? json_decode($detail['all_files'], true) : [];
	$all_skills = (!empty($detail['all_skills'])) ? json_decode($detail['all_skills'], true) : [];
	$all_subjects = (!empty($detail['all_subjects'])) ? json_decode($detail['all_subjects'], true) : [];
	$all_domains = (!empty($detail['all_domains'])) ? json_decode($detail['all_domains'], true) : [];
	$all_org = (!empty($detail['all_org'])) ? json_decode($detail['all_org'], true) : [];
	$all_locations = (!empty($detail['all_locations'])) ? json_decode($detail['all_locations'], true) : [];
	$all_dept = (!empty($detail['all_dept'])) ? json_decode($detail['all_dept'], true) : [];
	$all_story = (!empty($detail['all_story'])) ? json_decode($detail['all_story'], true) : [];
	$all_users = (!empty($detail['all_users'])) ? json_decode($detail['all_users'], true) : [];

	function asrt($a, $b) {
		$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
	    return strcasecmp($t1, $t2);
	}

	$all_links = htmlentity($all_links, 'title');
	$all_files = htmlentity($all_files, 'title');
	$all_skills = htmlentity($all_skills, 'title');
	$all_subjects = htmlentity($all_subjects, 'title');
	$all_domains = htmlentity($all_domains, 'title');
	$all_org = htmlentity($all_org, 'title');
	$all_locations = htmlentity($all_locations, 'title');
	$all_dept = htmlentity($all_dept, 'title');
	$all_story = htmlentity($all_story, 'title');
	$all_users = htmlentity($all_users, 'title');

	usort($all_links, 'asrt');
	usort($all_files, 'asrt');
	usort($all_skills, 'asrt');
	usort($all_subjects, 'asrt');
	usort($all_domains, 'asrt');
	usort($all_org, 'asrt');
	usort($all_locations, 'asrt');
	usort($all_dept, 'asrt');
	usort($all_story, 'asrt');
	usort($all_users, 'asrt');

// pr($this->Permission->current_org(), 1);
	$current_org = $this->Permission->current_org();
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title popup-ellipsis-h"><i class="storywhiteicon"></i> <span class="text-ellipsis"><?php echo $detail_title; ?></span></h3>
</div>
<div class="modal-body">
	<div class="row d-flex-s">

	<div class="col-sm-3 col-md-3 col-lg-3 col-skill-1 skill-profile-cols">
		<div class="loc-profile-img">
			<?php if(!empty($stories['image'])){ ?>
				<img alt="Profile Image" src="<?php echo SITEURL . STORY_IMAGE_PATH . $stories['image'];?>" >
			<?php } ?>
		</div>
		<div class="skill-menu-left">
			<ul>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon linkgreenicon"></i> Links </span> <span class="count"><?php echo $total_link ; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon filegreenicon"></i> Files </span> <span class="count"><?php echo $total_file; ?></span> </a></li>
			</ul>
			<h6>Related</h6>
			<ul>
				<?php
					$sk_link = "javascript:void(0);";
					$sk_class = "";
					if( isset($total_people) && !empty($total_people) ){
						$sk_link = Router::Url( array( "controller" => "searches", "action" => "people", "story" => $stories['id'], 'admin' => FALSE ), true );
						$sk_class = "cursor: pointer;";
					}
				?>
				<li><a href="<?php echo $sk_link; ?>" style="<?php echo $sk_class; ?>"><span class="skill-menu-left-text"><i class="skill-menu-icon peoplegreenicon"></i> People </span> <span class="count"><?php echo $total_people; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon organizationgreenicon"></i> Organizations </span> <span class="count"><?php echo $total_organization; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon locationgreenicon"></i> Locations </span> <span class="count"><?php echo $total_location; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon departmentsgreenicon"></i> Departments </span> <span class="count"><?php echo $total_department; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon competenciesgreenicon"></i> Competencies </span> <span class="count"><?php echo $total_competency; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon storygreenicon"></i> Stories </span> <span class="count"><?php echo $total_story; ?></span> </a></li>
			</ul>
		</div>

	</div>

	<div class="col-sm-9 col-md-9 col-sm-9 col-skill-2">
		<div class="row d-flex-s s-height100">
			<div class="col-sm-12 col-md-12 col-lg-12 common-tab-sec view-skills-tab left-border ">
				<ul class="nav nav-tabs tab-list">
					<li class="active">
						<a data-toggle="tab" class="active" href="#details" aria-expanded="true">Details</a>
					</li>
					<li>
						<a data-toggle="tab" href="#links" aria-expanded="false">Links</a>
					</li>
					<li>
						<a data-toggle="tab" href="#files" aria-expanded="false">Files</a>
					</li>
					<li>
						<a data-toggle="tab" href="#people" aria-expanded="false">People</a>
					</li>
					<li>
						<a data-toggle="tab" href="#communities" aria-expanded="false">Community</a>
					</li>
					<li>
						<a data-toggle="tab" href="#competencies" aria-expanded="false">Competencies</a>
					</li>
					<li>
						<a data-toggle="tab" href="#stories" aria-expanded="false">Stories</a>
					</li>
				</ul>

				<div class="tab-content skills-tab-content">

					<div id="details" class="tab-pane fade active in">
						<div class="loc-profiledetails story-details-sec">
							<label class="control-label">Type:</label>
							<p><?php echo $story_type; ?></p>
							<label class="control-label">Summary:</label>
							<div class="profile-summary-scroll" style="line-height: 23px;">
							<?php echo nl2br(htmlentities($stories['summary'], ENT_QUOTES, "UTF-8")); ?>
							</div>
							<label class="control-label">Story:</label>
							<div class="profile-story-scroll" style="line-height: 23px;">
							<?php echo nl2br(htmlentities($stories['story'], ENT_QUOTES, "UTF-8")); ?>
							</div>
						</div>
					</div>
					<div id="links" class="tab-pane fade">
						<div class="story-links-list">
						<div class="popup-skill-list">
							<ul>
								<?php if(isset($all_links) && !empty($all_links)) { ?>
								<?php foreach ($all_links as $key => $link) { ?>
							  	<li><span class="list-text"><?php echo $link['title']; ?></span> <span class="list-icon"> <a href="<?php echo $link['link']; ?>" target="_blank" class="tipText" title="Open Link" ><i class="openlinkicon"></i></a>  </span>
							  	</li>
							  	<?php } ?>
								<?php }else{ ?>
								No Links
								<?php } ?>
							</ul>
						</div>
						</div>
					</div>
					<div id="files" class="tab-pane fade">
						<div class="story-files-list">
						<div class="popup-skill-list">
							<ul>
								<?php if(isset($all_files) && !empty($all_files)) { ?>
								<?php foreach ($all_files as $key => $file) { ?>
							  	<li>
							  		<span class="list-text"><?php echo $file['title'] ; ?></span> <span class="list-icon"> <a href="<?php echo Router::url(array('controller' => 'stories', 'action' => 'download_files', 'story', $file['id'], 'admin' => false)); ?>" data-type="story" class="tipText" title="Download File" download><i class="downloadblackicon"></i></a>  </span>
							  	</li>
								<?php } ?>
								<?php }else{ ?>
								No Files
								<?php } ?>
							</ul>
						</div>
						</div>
					</div>
					<div id="people" class="tab-pane fade">
						<div class="storie-people-list">
							<div class="skillpeoplelist">
								<ul>
								<?php if(isset($all_users) && !empty($all_users)){ ?>
									<?php foreach ($all_users as $key => $listuser) {
										$profile_pic = $listuser['profile_pic'];
										if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
											$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
										} else {
											$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
										}
									?>
									<li>
										<div class="community-diff-list">
											<span class="skill-popple-icon user-pop" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>">
												<img class="" alt="<?php echo $listuser['title']; ?>" src="<?php echo $profilesPic; ?>"></span>
												<?php if($current_org['organization_id'] != $listuser['organization']){ ?>
												<i class="communitygray18 tipText show-user-popup" title="" data-original-title="Not In Your Organization" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>"></i>
												<?php } ?>
										</div>
										<span class="skill-popple-info">
											<h6 class="user-pop" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>"><?php echo $listuser['title']; ?></h6>
											<p><?php echo $listuser['job_title']; ?></p>
										</span>
									</li>
									<?php } ?>
								<?php }else{ ?>
									<li>No People</li>
								<?php } ?>
								</ul>
							</div>
						</div>
					</div>
					<div id="communities" class="tab-pane fade">
						<div class="storie-community-list">
						<div class="popuplocationlist">
							<ul class="loc-names-ul">
								<?php if($all_org){ ?>
						 		<?php foreach ($all_org as $key => $value) {  ?>
								<li class="loc-names-li location-list-item">
									<span class="loc-name-list">
										<span class="loc-thumb open-other-modal" data-type="org" data-id="<?php echo $value['id']; ?>">
											<?php if(!empty($value['image'])){ ?>
												<img src="<?php echo SITEURL . ORG_IMAGE_PATH . $value['image']; ?>" >
											<?php } ?>
										</span>
										<div class="loc-info">
											<span class="loc-name open-other-modal" data-type="org" data-id="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></span>
											<div class="loc-cc-name">
												<span class="sks-city"><?php echo $value['type']; ?></span>
											</div>
										</div>
									</span>
								</li>
								<?php } ?>
								<?php }else{ ?>
									<li>No Organizations</li>
								<?php } ?>
							</ul>
							<ul class="loc-names-ul">
								<?php if($all_locations){ ?>
						 		<?php foreach ($all_locations as $key => $value) {  ?>
								<li class="loc-names-li location-list-item">
									<span class="loc-name-list">
										<span class="loc-thumb open-other-modal" data-type="loc" data-id="<?php echo $value['id']; ?>">
											<?php if(!empty($value['image'])){ ?>
												<img src="<?php echo SITEURL . LOC_IMAGE_PATH . $value['image']; ?>" >
											<?php } ?>
										</span>
										<div class="loc-info">
											<span class="loc-name open-other-modal" data-type="loc" data-id="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></span>
											<div class="loc-cc-name">
												<span class="sks-city"><?php echo htmlentities($value['city'], ENT_QUOTES, "UTF-8"); ?>,</span>
												<span class="sks-country"><?php echo $value['country']; ?></span>
											</div>
										</div>
									</span>
								</li>
								<?php } ?>
								<?php }else{ ?>
									<li>No Locations</li>
								<?php } ?>
							</ul>
							<ul class="loc-names-ul">
								<?php if($all_dept){ ?>
						 		<?php foreach ($all_dept as $key => $value) {  ?>
								<li class="loc-names-li location-list-item">
									<span class="loc-name-list">
										<span class="loc-thumb open-other-modal" data-type="dept" data-id="<?php echo $value['id']; ?>">
											<?php if(!empty($value['image'])){ ?>
												<img src="<?php echo SITEURL . COMM_IMAGE_PATH . $value['image']; ?>" >
											<?php } ?>
										</span>
										<div class="loc-info">
											<span class="loc-name open-other-modal" data-type="dept" data-id="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></span>
											<div class="loc-cc-name">
												<span class="sks-city">Department</span>
											</div>
										</div>
									</span>
								</li>
								<?php } ?>
								<?php }else{ ?>
									<li>No Departments</li>
								<?php } ?>
							</ul>
						</div>
						</div>
					</div>
					<div id="competencies" class="tab-pane fade">
						<div class="storie-competencies-list">
						<?php if($total_competency > 0){ ?>
						<div class="com-list-wrap">
						 <ul class="competencies-ul">
						 	<?php if(isset($all_skills) && !empty($all_skills)){ ?>
						 	<?php foreach ($all_skills as $key => $value) {  ?>
								<li class="skill-border-left">
									<span class="com-list-bg open-other-modal" data-id="<?php echo $value['id']; ?>" data-type="skill">
										<i class="com-skills-icon"></i>
										<span class="com-sks-title"><?php echo $value['title']; ?></span>
									</span>
								</li>
							<?php } ?>
							<?php } ?>

						 	<?php if(isset($all_subjects) && !empty($all_subjects)){ ?>
						 	<?php foreach ($all_subjects as $key => $value) {  ?>
								<li class="subjects-border-left">
									<span class="com-list-bg open-other-modal" data-id="<?php echo $value['id']; ?>" data-type="subject">
										<i class="com-subjects-icon"></i>
										<span class="com-sks-title"><?php echo $value['title']; ?></span>
									</span>
								</li>
							<?php } ?>
							<?php } ?>

						 	<?php if(isset($all_domains) && !empty($all_domains)){ ?>
						 	<?php foreach ($all_domains as $key => $value) {  ?>
								<li class="domain-border-left">
									<span class="com-list-bg open-other-modal" data-id="<?php echo $value['id']; ?>" data-type="domain">
										<i class="com-domain-icon"></i>
										<span class="com-sks-title"><?php echo $value['title']; ?></span>
									</span>
								</li>
							<?php } ?>
							<?php } ?>
							</ul>
						</div>
						<?php }else{ ?>
						No Competencies
						<?php } ?>
					</div>
					</div>
					<div id="stories" class="tab-pane fade">
						<div class="stories-popup-list">
							<div class="popuplocationlist">
								<ul class="loc-names-ul">
									<?php if($all_story){ ?>
							 		<?php foreach ($all_story as $key => $value) {  ?>
									<li class="loc-names-li location-list-item">
										<span class="loc-name-list">
											<span class="loc-thumb open-story" data-type="story" data-id="<?php echo $value['id']; ?>">
												<?php if(!empty($value['image'])){ ?>
													<img src="<?php echo SITEURL . STORY_IMAGE_PATH . $value['image']; ?>" >
												<?php } ?>
											</span>
											<div class="loc-info">
												<span class="loc-name open-story" data-type="story" data-id="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></span>
												<div class="loc-cc-name">
													<span class="sks-city"><?php echo $value['type']; ?></span>
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
	</div>

<div class="modal-footer clearfix">
	<button type="button" class="btn outline-green-s pull-left filter-selected-story tipText" title="Go To Stories">Stories</button>
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
$(function(){
	$.selected_view_type = 'story';
	$.selected_view = '<?php echo addslashes( $stories['name'] ); ?>';
	// `~!@#$%^&*()_-=+|]}[{'";:/?.>,<<br>"Hello"World
	var active_tab = '<?php echo $tab; ?>';

	$('.nav.nav-tabs a[href="#'+active_tab+'"]').tab('show');

	$(".story-details-sec").slimScroll({height: 320, alwaysVisible: true});

	$('.tab-list').on('shown.bs.tab', function(){
			$(".story-links-list").slimScroll({height: 320, alwaysVisible: true});
			$(".story-files-list").slimScroll({height: 320, alwaysVisible: true});
			$(".storie-people-list").slimScroll({height: 312, alwaysVisible: true});
			$(".storie-community-list").slimScroll({height: 328, alwaysVisible: true});
			$(".storie-competencies-list").slimScroll({height: 328, alwaysVisible: true });
			$(".stories-popup-list").slimScroll({height: 328, alwaysVisible: true});
	})

	$('.skill-popple-icon, .skill-popple-info h6, .show-user-popup').off('click').on('click', function(e){
		$('#story_view').modal('hide');
	})


	$('.open-other-modal').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data(),
			id = data.id,
			type = data.type,
			url = '',
			modal = '';

		if(type == 'org'){
			url = $js_config.base_url + 'communities/view/org/' + id;
			modal = '#modal_view_org';
		}
		else if(type == 'loc'){
			url = $js_config.base_url + 'communities/view/loc/' + id;
			modal = '#modal_view_loc';
		}
		else if(type == 'dept'){
			url = $js_config.base_url + 'communities/view/dept/' + id;
			modal = '#modal_view_dept';
		}
		else if(type == 'skill'){
			url = $js_config.base_url + 'competencies/view_skills/' + id;
			modal = '#modal_view_skill';
		}
		else if(type == 'subject'){
			url = $js_config.base_url + 'competencies/view_subjects/' + id;
			modal = '#modal_view_skill';
		}
		else if(type == 'domain'){
			url = $js_config.base_url + 'competencies/view_domains/' + id;
			modal = '#modal_view_skill';
		}

		$('#story_view').modal('hide');
		$(modal).modal({
			remote: url
		})
		.modal('show');
	});

	$('.open-story').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'stories/view/story/' + data.id
		$('#story_view').modal('hide');
		setTimeout(function(){
			$('#story_view').modal({
				remote: url
			})
			.modal('show');
		},1000)
	});


	$('.user-pop').off('click').on('click', function(event) {
		event.preventDefault();
		$('#story_view').modal('hide');
	});

	$('body').on('click', '.filter-selected-story', function(event) {
		event.preventDefault();

		$('#story_view').modal('hide');
		if( window.location.href.indexOf("stories") <= 0 ) {
			var id = '<?php echo $stories["id"]; ?>';
			window.location.href = $js_config.base_url + 'stories/index/story/' + id;
		}

	});
})
</script>
<?php }else{ ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><i class="storywhiteicon"></i> <span class="text-ellipsis">Title</span></h3>
</div>
<div class="modal-body">Story View</div>
<div class="modal-footer clearfix">
	<button type="button" class="btn outline-green-s pull-left filter-selected-story tipText" title="Go To Stories">Stories</button>
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>
<?php } ?>
<style type="text/css">
	.skill-popple-info h6 {
		cursor: pointer;
	}
	.modal-footer .outline-green-s {
	    background-color: transparent;
	    color: #5f9323;
	    border-color: #5f9323;
	}
	.modal-footer .outline-green-s:hover{
	    background-color: #67a028;
	    color: #fff;
	}
	.show-user-popup {
		cursor: pointer;
	}


</style>