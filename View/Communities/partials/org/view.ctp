<?php
// pr($data, 1);
$data = $data[0];
$organizations = $data['organizations'];
$link_counts = (!empty($data['link_counts']['linktotal'])) ? $data['link_counts']['linktotal'] : 0;
$file_counts = (!empty($data['file_counts']['filetotal'])) ? $data['file_counts']['filetotal'] : 0;
$skill_counts = (!empty($data['skill_counts']['totalskills'])) ? $data['skill_counts']['totalskills'] : 0;
$subject_counts = (!empty($data['subject_counts']['totalsubjects'])) ? $data['subject_counts']['totalsubjects'] : 0;
$domain_counts = (!empty($data['domain_counts']['totaldomains'])) ? $data['domain_counts']['totaldomains'] : 0;

$totalpeople = (!empty($data['details_counts']['totalpeople'])) ? $data['details_counts']['totalpeople'] : 0;
$total_location = (!empty($data['location_counts']['total_location'])) ? $data['location_counts']['total_location'] : 0;
$total_story = (!empty($data['story_counts']['total_story'])) ? $data['story_counts']['total_story'] : 0;

$updated_by = $data[0]['updated_by'];
$organization_types = $data['organization_types']['org_type'];

$others = $data[0];
$all_links = (!empty($others['all_links'])) ? json_decode($others['all_links'], true) : [];
$all_files = (!empty($others['all_files'])) ? json_decode($others['all_files'], true) : [];
$all_skills = (!empty($others['all_skills'])) ? json_decode($others['all_skills'], true) : [];
$all_subjects = (!empty($others['all_subjects'])) ? json_decode($others['all_subjects'], true) : [];
$all_domains = (!empty($others['all_domains'])) ? json_decode($others['all_domains'], true) : [];
$all_locations = (!empty($others['all_locations'])) ? json_decode($others['all_locations'], true) : [];
$all_email_domains = (!empty($others['all_email_domains'])) ? json_decode($others['all_email_domains'], true) : [];
$all_users = (!empty($others['all_users'])) ? json_decode($others['all_users'], true) : [];
$all_stories = (!empty($others['all_stories'])) ? json_decode($others['all_stories'], true) : [];
// mpr($all_links, $all_files, $all_skills, $all_subjects, $all_domains, $all_locations, $all_users, $all_stories );
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
usort($all_locations, 'asrt');
usort($all_email_domains, 'asrt');
usort($all_users, function($a, $b) {
	$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['full_name']);
	$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['full_name']);
    return strcasecmp($t1, $t2);
});
usort($all_stories, 'asrt');

$current_org = $this->Permission->current_org();
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title"><i class="organizationwhiteicon"></i> <span class="text-ellipsis"><?php echo  (htmlentities($organizations['name'], ENT_QUOTES, "UTF-8")); ?></span></h3>
</div>
<div class="modal-body">
	<div class="row d-flex-s">

	<div class="col-sm-3 col-md-3 col-lg-3 col-skill-1 skill-profile-cols">
		<div class="loc-profile-img">
			<?php if(!empty($organizations['image'])){ ?>
				<img alt="Profile Image" src="<?php echo SITEURL . ORG_IMAGE_PATH . $organizations['image'];?>" >
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
					if( isset($totalpeople) && !empty($totalpeople) ){
						$sk_link = Router::Url( array( "controller" => "searches", "action" => "people", "org" => $organizations['id'], 'admin' => FALSE ), true );
						$sk_class = "cursor: pointer;";
					}
				?>
				<li><a href="<?php echo $sk_link; ?>" style="<?php echo $sk_class; ?>"><span class="skill-menu-left-text"><i class="skill-menu-icon peoplegreenicon"></i> People </span> <span class="count"><?php echo $totalpeople; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon organizationgreenicon"></i> Locations </span> <span class="count"><?php echo $total_location; ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon competenciesgreenicon"></i> Competencies </span> <span class="count"><?php echo ($skill_counts + $subject_counts + $domain_counts); ?></span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon storygreenicon"></i> Stories </span> <span class="count"><?php echo $total_story; ?></span> </a></li>
			</ul>

			<?php if(isset($updated_by) && !empty($updated_by)){ ?>
			<ul>
				<li><a href="javascript:void(0);" class="location_user_name"><span class="skill-menu-left-text"><i class="skill-menu-icon modifiedgreenicon"></i> <?php echo $updated_by; ?> </span> </a></li>
				<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon dategreenicon"></i> <?php echo ( isset($organizations['modified']) && !empty($organizations['modified']) && $organizations['modified']!= '0000-00-00 00:00:00' ) ? $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($organizations['modified'])), $format = 'd M, Y h:i A') : ''; ?>
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
							<p><?php echo $organization_types; ?></p>

							<label class="control-label">Email Domains:</label>
							<p class="dlist">
								<?php if($all_email_domains){ ?>
								<?php foreach ($all_email_domains as $key => $value) { ?>
								<?php echo $value['title']; ?><br>
								<?php } ?>
								<?php } ?>
							</p>

						</div>
					</div>

					<div id="informations" class="tab-pane fade">
						<div class="description-scroll" style="line-height: 23px;">
						<?php echo nl2br( htmlentities($organizations['information'], ENT_QUOTES, "UTF-8")); ?>
						</div>
					</div>
					<div id="links" class="tab-pane fade">
						<div class="popup-skill-list">
							<ul>
								<?php if(isset($all_links) && !empty($all_links)) { ?>
								<?php foreach ($all_links as $key => $link) { ?>
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
								<?php foreach ($all_files as $key => $file) { ?>
							  	<li>
							  		<span class="list-text"><?php echo htmlentities($file['title'], ENT_QUOTES, "UTF-8"); ?></span> <span class="list-icon"> <a href="<?php echo Router::url(array('controller' => 'communities', 'action' => 'download_files', 'org', $file['id'], 'admin' => false)); ?>" data-type="org" class="tipText" title="Download File" download><i class="downloadblackicon"></i></a>  </span>
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
					<li class="active" >
						<a data-toggle="tab" class="active"  href="#people" aria-expanded="false">People</a>
					</li>
					<li >
						<a data-toggle="tab" href="#locations" aria-expanded="false"> Locations </a>
					</li>
					<li>
						<a data-toggle="tab" href="#competencies" aria-expanded="true"> Competencies </a>
					</li>
					<li >
						<a data-toggle="tab" href="#stories" aria-expanded="false">  Stories  </a>
					</li>
				</ul>

				<div class="tab-content">
					<div id="people" class="tab-pane fade active in">
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
					<div id="locations" class="tab-pane fade">
						<div class="popuplocationlist" style="overflow: hidden; width: auto; height: 312px;">
							<ul class="loc-names-ul">
								<?php if($all_locations){ ?>
						 		<?php foreach ($all_locations as $key => $value) {  ?>
								<li class="loc-names-li location-list-item" data-id="<?php echo $value['id']; ?>">
									<span class="loc-name-list">
										<span class="loc-thumb">
											<?php if(!empty($value['image'])){ ?>
												<img src="<?php echo SITEURL . LOC_IMAGE_PATH . $value['image']; ?>" >
											<?php } ?>
										</span>
										<div class="loc-info">
											<span class="loc-name"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
											<div class="loc-cc-name">
												<span class="sks-city"><?php echo htmlentities($value['city'], ENT_QUOTES, "UTF-8"); ?></span>
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
						</div>
					</div>
					<div id="competencies" class="tab-pane fade ">
						<?php if(($skill_counts + $subject_counts + $domain_counts) > 0){ ?>
						<div class="com-list-wrap">
						 <ul class="competencies-ul">
						 	<?php if(isset($all_skills) && !empty($all_skills)){ ?>
						 	<?php foreach ($all_skills as $key => $value) {  ?>
								<li class="skill-border-left open-desired" data-id="<?php echo $value['id']; ?>" data-type="skill">
									<span class="com-list-bg">
										<i class="com-skills-icon"></i>
										<span class="com-sks-title"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
									</span>
								</li>
							<?php } ?>
							<?php } ?>

						 	<?php if(isset($all_subjects) && !empty($all_subjects)){ ?>
						 	<?php foreach ($all_subjects as $key => $value) {  ?>
								<li class="subjects-border-left open-desired" data-id="<?php echo $value['id']; ?>" data-type="subject">
									<span class="com-list-bg">
										<i class="com-subjects-icon"></i>
										<span class="com-sks-title"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
									</span>
								</li>
							<?php } ?>
							<?php } ?>

						 	<?php if(isset($all_domains) && !empty($all_domains)){ ?>
						 	<?php foreach ($all_domains as $key => $value) {  ?>
								<li class="domain-border-left open-desired" data-id="<?php echo $value['id']; ?>" data-type="domain">
									<span class="com-list-bg">
										<i class="com-domain-icon"></i>
										<span class="com-sks-title"><?php echo htmlentities($value['title'], ENT_QUOTES, "UTF-8"); ?></span>
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
					<div id="stories" class="tab-pane fade">
						<div class="popuplocationlist" style="overflow: hidden; width: auto; height: 312px;">
							<ul class="loc-names-ul">
								<?php if($all_stories){ ?>
						 		<?php foreach ($all_stories as $key => $value) {  ?>
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

<div class="modal-footer clearfix">
	<button type="button" class="btn outline-green-s pull-left filter-selected-org tipText" title="Go To Community">Community</button>
	<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
</div>
<style type="text/css">
	.skill-popple-info h6 {
		cursor: pointer;
	}
</style>
<script type="text/javascript" >
$(function(){
	$.selected_view_type = 'org';
	$.selected_view = '<?php echo addslashes( $organizations['name'] ); ?>';

	var active_tab = '<?php echo $tab; ?>';
	$('.nav.nav-tabs a[href="#'+active_tab+'"]').tab('show');

	$('.skill-popple-icon, .skill-popple-info h6, .show-user-popup').off('click').on('click', function(e){
		$('#modal_view_org').modal('hide');
	})

	$(".skillpeoplelist").slimScroll({height: 312, alwaysVisible: true});
	$(".loc-profiledetails p.dlist").slimScroll({height: 230, alwaysVisible: true});
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
		$('#modal_view_org').modal('hide');
		$('#modal_view_skill').modal({
			remote: url
		})
		.modal('show');
	});

	$('.location-list-item').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'communities/view/loc/' + data.id
		$('#modal_view_org').modal('hide');
		$('#modal_view_loc').modal({
			remote: url
		})
		.modal('show');
	});

	$('.story-list-item').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'stories/view/story/' + data.id
		$('#modal_view_org').modal('hide');
		$('#story_view').modal({
			remote: url
		})
		.modal('show');
	});

	$('body').on('click', '.filter-selected-org', function(event) {
		event.preventDefault();

		$('#modal_view_org').modal('hide');
		if( window.location.href.indexOf("communities") <= 0 ) {
			var id = '<?php echo $organizations["id"]; ?>';
			window.location.href = $js_config.base_url + 'communities/index/org/' + id;
		}

	});
})
</script>
<style type="text/css">
	/*#competencies .com-sks-title {
		cursor: default;
	}*/
</style>