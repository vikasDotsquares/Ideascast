<?php
// pr($data, 1);
$data = $data[0];
$locations = $data['locations'];
$link_counts = (!empty($data['link_counts']['linktotal'])) ? $data['link_counts']['linktotal'] : 0;
$file_counts = (!empty($data['file_counts']['filetotal'])) ? $data['file_counts']['filetotal'] : 0;
$people_counts = (!empty($data['people_counts']['totalpeople'])) ? $data['people_counts']['totalpeople'] : 0;
$skill_counts = (!empty($data['skill_counts']['totalskills'])) ? $data['skill_counts']['totalskills'] : 0;
$subject_counts = (!empty($data['subject_counts']['totalsubjects'])) ? $data['subject_counts']['totalsubjects'] : 0;
$domain_counts = (!empty($data['domain_counts']['totaldomains'])) ? $data['domain_counts']['totaldomains'] : 0;
$updated_by = $data[0]['updated_by'];
$country = $data['countries']['countryName'];
$states = $data['states']['stateName'];
$location_types = $data['location_types']['type'];

$others = $data[0];
$all_links = (!empty($others['all_links'])) ? json_decode($others['all_links'], true) : [];
$all_files = (!empty($others['all_files'])) ? json_decode($others['all_files'], true) : [];
$all_skills = (!empty($others['all_skills'])) ? json_decode($others['all_skills'], true) : [];
$all_subjects = (!empty($others['all_subjects'])) ? json_decode($others['all_subjects'], true) : [];
$all_domains = (!empty($others['all_domains'])) ? json_decode($others['all_domains'], true) : [];
$all_organizations = (!empty($others['all_organizations'])) ? json_decode($others['all_organizations'], true) : [];
$all_users = (!empty($others['all_users'])) ? json_decode($others['all_users'], true) : [];
$all_stories = (!empty($others['all_stories'])) ? json_decode($others['all_stories'], true) : [];
function asrt($a, $b) {
	$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
	$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
    return strcasecmp($t1, $t2);
}
usort($all_links, 'asrt');
usort($all_files, 'asrt');
usort($all_skills, 'asrt');
usort($all_subjects, 'asrt');
usort($all_domains, 'asrt');
usort($all_organizations, 'asrt');
usort($all_users, function($a, $b) {
	$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['full_name']);
	$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['full_name']);
    return strcasecmp($t1, $t2);
});
usort($all_stories, 'asrt');
// pr($all_users);
$org_count = count($all_organizations);
$users_count = count($all_users);
$stories_count = count($all_stories);

$current_org = $this->Permission->current_org();

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><i class="locwhiteicon"></i> <span class="text-ellipsis pl7"><?php echo htmlentities($locations['name'], ENT_QUOTES, "UTF-8") ; ?></span></h3>
</div>
<div class="modal-body">
	<div class="row d-flex-s">

	<div class="col-sm-3 col-md-3 col-lg-3 col-skill-1 skill-profile-cols">
		<div class="loc-profile-img">
			<?php if(!empty($locations['image'])){ ?>
				<img alt="Profile Image" src="<?php echo SITEURL . LOC_IMAGE_PATH . $locations['image'];?>" >
			<?php } ?>
		</div>
		<div class="skill-menu-left">
			<ul>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon linkgreenicon"></i> Links </span> <span class="count"><?php echo $link_counts ; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon filegreenicon"></i> Files </span> <span class="count"><?php echo $file_counts; ?></span> </a></li>
			</ul>
			<h6>Related</h6>
			<ul>
				<?php
					$sk_link = "javascript:void(0);";
					$sk_class = "";
					if( isset($people_counts) && !empty($people_counts) ){
						$sk_link = Router::Url( array( "controller" => "searches", "action" => "people", "loc" => $locations['id'], 'admin' => FALSE ), true );
						$sk_class = "cursor: pointer;";
					}
				?>
				<li><a href="<?php echo $sk_link; ?>" style="<?php echo $sk_class; ?>"><span class="skill-menu-left-text"><i class="skill-menu-icon peoplegreenicon"></i> People </span> <span class="count"><?php echo $people_counts; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon organizationgreenicon"></i> Organizations </span> <span class="count"><?php echo $org_count; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon competenciesgreenicon"></i> Competencies </span> <span class="count"><?php echo ($skill_counts + $subject_counts + $domain_counts); ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon storygreenicon"></i> Stories </span> <span class="count"><?php echo $stories_count; ?></span> </a></li>
			</ul>

			<?php if(isset($updated_by) && !empty($updated_by)){ ?>
			<ul>
				<li><a href="javascript:void(0);" class="location_user_name"><span class="skill-menu-left-text"><i class="skill-menu-icon modifiedgreenicon"></i> <?php echo $updated_by; ?> </span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon dategreenicon"></i> <?php echo ( isset($locations['modified']) && !empty($locations['modified']) && $locations['modified']!= '0000-00-00 00:00:00' ) ? $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($locations['modified'])), $format = 'd M, Y h:i A') : ''; ?>
				</span>  </a></li>
			</ul>
			<?php } ?>

		</div>

	</div>

	<div class="col-sm-9 col-md-9 col-sm-9 col-skill-2">
		<div class="row d-flex-s s-height100">
			<div class="col-sm-12 col-md-5 col-lg-5 common-tab-sec view-skills-tab left-border v-column-1">
				<ul class="nav nav-tabs tab-list">
					<li class="active">
						<a data-toggle="tab" class="active" href="#details" aria-expanded="true">Details</a>
					</li>
					<li>
						<a data-toggle="tab" href="#informations" aria-expanded="true">Information</a>
					</li>
					<li>
						<a data-toggle="tab" href="#links" aria-expanded="false">Links</a>
					</li>
					<li>
						<a data-toggle="tab" href="#files" aria-expanded="false">Files</a>
					</li>
				</ul>

				<div class="tab-content skills-tab-content">

					<div id="details" class="tab-pane fade active in">
						<div class="loc-profiledetails">
							<label class="control-label">Type:</label>
							<p><?php echo $location_types; ?></p>

							<label class="control-label">Full Address:</label>
							<p><?php echo (!empty($locations['address'])) ? nl2br( htmlentities($locations['address'], ENT_QUOTES, "UTF-8")) .'<br>' : ''; ?>
								<?php echo (!empty($locations['city'])) ? $locations['city'].'<br>' : ''; ?>
								<?php echo (!empty($states)) ? $states.'<br>' : ''; ?>
								<?php echo (!empty($locations['zip'])) ? $locations['zip'].'<br>' : ''; ?>
								<?php echo $country; ?>
							</p>

						</div>
					</div>

					<div id="informations" class="tab-pane fade">
						<div class="description-scroll" style="line-height: 23px;">
						<?php echo (isset($locations['information']) && !empty($locations['information'])) ? nl2br( htmlentities($locations['information'], ENT_QUOTES, "UTF-8")) : "No Information"; ?>
						</div>
					</div>
					<div id="links" class="tab-pane fade">
						<div class="popup-skill-list">
							<ul>
								<?php if(isset($all_links) && !empty($all_links)) { ?>
								<?php foreach ($all_links as $key => $value) {
										$link = $value; ?>
							  	<li><span class="list-text"><?php echo htmlentities($link['title'], ENT_QUOTES, "UTF-8") ; ?></span> <span class="list-icon"> <a href="<?php echo $link['link']; ?>" target="_blank" class="tipText" title="Open Link" ><i class="openlinkicon"></i></a>  </span>
							  	</li>
							  	<?php } ?>
								<?php }else{ ?>
								No Links
								<?php } ?>
							</ul>
						</div>
					</div>
					<div id="files" class="tab-pane fade">
						<div class="popup-skill-list">
							<ul>
								<?php if(isset($all_files) && !empty($all_files)) { ?>
								<?php foreach ($all_files as $key => $value) {
										$file = $value; ?>
							  	<li>
							  		<span class="list-text"><?php echo htmlentities($file['title'], ENT_QUOTES, "UTF-8"); ?></span> <span class="list-icon"> <a href="<?php echo Router::url(array('controller' => 'communities', 'action' => 'download_files', 'loc', $file['id'], 'admin' => false)); ?>" data-type="loc" class="tipText" title="Download File" download><i class="downloadblackicon"></i></a>  </span>
							  	</li>
								<?php } ?>
								<?php }else{ ?>
								No Files
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>

			</div>

			<div class="col-sm-12 col-md-7 col-lg-7 common-tab-sec view-skills-tab left-border v-column-2">
				<ul class="nav nav-tabs tab-list">
					<li class="active">
						<a data-toggle="tab" class="active" href="#people" aria-expanded="true">People</a>
					</li>
					<li >
						<a data-toggle="tab" href="#organization" aria-expanded="false"> Organizations </a>
					</li>
					<li>
						<a data-toggle="tab" href="#competencies" aria-expanded="false"> Competencies </a>
					</li>
					<li >
						<a data-toggle="tab" href="#stories" aria-expanded="false">  Stories  </a>
					</li>
				</ul>

				<div class="tab-content">
					<div id="people" class="tab-pane fade active in">
						<div class="skillpeoplelist">
							<ul>
							<?php if(isset($all_users) && !empty($all_users)){  ?>
								<?php foreach ($all_users as $key => $listuser) {
									$profile_pic = $listuser['profile_pic'];
									if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
										$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
									} else {
										$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
									}
								?>
								<li><div class="community-diff-list">
									<span class="skill-popple-icon" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>">
										<img class="" alt="<?php echo $listuser['full_name']; ?>" src="<?php echo $profilesPic; ?>"></span>
										<?php if($current_org['organization_id'] != $listuser['organization']){ ?>
											<i class="communitygray18 tipText show-user-popup" title="" data-original-title="Not In Your Organization" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>"></i>
										<?php } ?>
									</div>
									<span class="skill-popple-info">
									<h6 data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>"><?php echo $listuser['full_name']; ?></h6>
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
					<div id="organization" class="tab-pane fade">
						<div class="popuplocationlist" style="overflow: hidden; width: auto; height: 312px;">
							<ul class="loc-names-ul">
								<?php if(isset($all_organizations) && !empty($all_organizations)) { ?>
								<?php
								foreach ($all_organizations as $key => $value) {
									$orgdata = $value;
								?>
								<li class="loc-names-li org-list-item" data-id="<?php echo $orgdata['id']; ?>">
									<span class="loc-name-list">
										<span class="loc-thumb">
											<?php if(!empty($orgdata['image'])){ ?>
												<img src="<?php echo SITEURL . ORG_IMAGE_PATH . $orgdata['image']; ?>" >
											<?php } ?>
										</span>
										<div class="loc-info">
											<span class="loc-name"><?php echo htmlentities($orgdata['title'], ENT_QUOTES, "UTF-8"); ?></span>
											<div class="loc-cc-name">
												<span class="com-single-name"><?php echo $value['type']; ?></span>
											</div>
										</div>
									</span>
								</li>
								<?php } ?>
								<?php }else{ ?>
									<li>No Organizations</li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<div id="competencies" class="tab-pane fade">
						<?php if(($skill_counts + $subject_counts + $domain_counts) > 0){ ?>
						<?php //$competencies = $this->Permission->get_loc_competencies( $locations['id'] );
						// $all_skills = json_decode($competencies[0][0]['all_skills'], true);
						// $all_subjects = json_decode($competencies[0][0]['all_subjects'], true);
						// $all_domains = json_decode($competencies[0][0]['all_domains'], true);
						// pr($competencies);
						?>
						<div class="com-list-wrap">
						 <ul class="competencies-ul">
						 	<?php if(isset($all_skills) && !empty($all_skills)){ ?>
						 	<?php //foreach ($all_skills as $key => $value) {  ?>
						 	<?php foreach ($all_skills as $k => $val) {  ?>
								<li class="skill-border-left open-desired" data-id="<?php echo $val['id']; ?>" data-type="skill">
									<span class="com-list-bg">
										<i class="com-skills-icon"></i>
										<span class="com-sks-title"><?php echo htmlentities($val['title'], ENT_QUOTES, "UTF-8"); ?></span>
									</span>
								</li>
							<?php } ?>
							<?php //} ?>
							<?php } ?>

						 	<?php if(isset($all_subjects) && !empty($all_subjects)){ ?>
						 	<?php //foreach ($all_subjects as $key => $value) {  ?>
						 	<?php foreach ($all_subjects as $k => $val) {  ?>
								<li class="subjects-border-left open-desired" data-id="<?php echo $val['id']; ?>" data-type="subject">
									<span class="com-list-bg">
										<i class="com-subjects-icon"></i>
										<span class="com-sks-title"><?php echo htmlentities($val['title'], ENT_QUOTES, "UTF-8"); ?></span>
									</span>
								</li>
							<?php } ?>
							<?php //} ?>
							<?php } ?>

						 	<?php if(isset($all_domains) && !empty($all_domains)){ ?>
						 	<?php //foreach ($all_domains as $key => $value) {  ?>
						 	<?php foreach ($all_domains as $k => $val) {  ?>
								<li class="domain-border-left open-desired" data-id="<?php echo $val['id']; ?>" data-type="domain">
									<span class="com-list-bg">
										<i class="com-domain-icon"></i>
										<span class="com-sks-title"><?php echo htmlentities($val['title'], ENT_QUOTES, "UTF-8"); ?></span>
									</span>
								</li>
							<?php } ?>
							<?php //} ?>
							<?php } ?>
							</ul>
						</div>
						<?php }else{ ?>
						No Competencies
						<?php } ?>
					</div>
					<div id="stories" class="tab-pane fade">
						<div class="popuplocationlist" style="overflow: hidden; width: auto; height: 312px;">
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
	<button type="button" class="btn outline-green-s pull-left filter-selected-loc tipText" title="Go To Community">Community</button>
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>
<style type="text/css">
	.skill-popple-info h6 {
		cursor: pointer;
	}
</style>
<script type="text/javascript" >
$(function(){
	$.selected_view_type = 'loc';
	$.selected_view = '<?php echo addslashes( $locations['name'] ); ?>';

	var active_tab = '<?php echo $tab; ?>';
	$('.nav.nav-tabs a[href="#'+active_tab+'"]').tab('show');

	$('.skill-popple-icon, .skill-popple-info h6, .show-user-popup').off('click').on('click', function(e){
		$('#modal_view_loc').modal('hide');
	})

		$(".skillpeoplelist").slimScroll({height: 312, alwaysVisible: true});
	$('.tab-list').on('shown.bs.tab', function(){
		$(".popuplocationlist").slimScroll({height: 312, alwaysVisible: true});
		$("ul.competencies-ul").slimScroll({height: 328, alwaysVisible: true});
		$(".description-scroll").slimScroll({height: 320, alwaysVisible: true});
	})




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
		$('#modal_view_loc').modal('hide');
		$('#modal_view_skill').modal({
			remote: url
		})
		.modal('show');
	});

	$('.org-list-item').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'communities/view/org/' + data.id
		$('#modal_view_loc').modal('hide');
		$('#modal_view_org').modal({
			remote: url
		})
		.modal('show');
	});

	$('.story-list-item').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'stories/view/story/' + data.id
		$('#modal_view_loc').modal('hide');
		$('#story_view').modal({
			remote: url
		})
		.modal('show');
	});


	$('body').on('click', '.filter-selected-loc', function(event) {
		event.preventDefault();

		$('#modal_view_loc').modal('hide');
		if( window.location.href.indexOf("communities") <= 0 ) {
			var id = '<?php echo $locations["id"]; ?>';
			window.location.href = $js_config.base_url + 'communities/index/loc/' + id;
		}

	});
})
</script>
<style type="text/css">
	/*#competencies .com-sks-title {
		cursor: default;
	}*/
</style>