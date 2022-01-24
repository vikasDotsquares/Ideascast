<?php
// pr($data);
$skillData['title'] = $data[0]['skills']['title'];
$skillData['id'] = $data[0]['skills']['id'];
$skillData['description'] = $data[0]['skills']['description'];
$skillData['modified'] = $data[0]['skills']['modified'];
$skillData['modified_by'] = $data[0]['skills']['modified_by'];
$skillData['image'] = $data[0]['skills']['image'];

$active = null;
if(isset($this->request->params['pass']['1']) && !empty($this->request->params['pass']['1'])){
	 $active = $this->request->params['pass']['1'];
}

$skillData['updatedby'] = $data[0][0]['updatedby'];
$skillData['linktotal'] = $data[0][0]['linktotal'];
$skillData['filetotal'] = $data[0][0]['filetotal'];
$skillData['totalpeople'] = $data[0][0]['totalpeople'];
$skillData['totallocation'] = $data[0][0]['totallocation'];
$skillData['skill_links'] = json_decode($data[0][0]['skill_links'],true);
$skillData['skill_files'] = json_decode($data[0][0]['skill_files'], true);
$skillData['user_detail'] = json_decode($data[0][0]['user_detail'], true);
$skillData['location_skill'] = json_decode($data[0][0]['location_skill'], true);


if(isset($skillData['skill_links']) && !empty($skillData['skill_links'])){
	usort($skillData['skill_links'], function($a, $b) {
	    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['link_name']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['link_name']);
	    return strcasecmp($t1, $t2);
	});
}
if(isset($skillData['skill_files']) && !empty($skillData['skill_files'])){
	usort($skillData['skill_files'], function($a, $b) {
	    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['file_name']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['file_name']);
	    return strcasecmp($t1, $t2);
	});
}



if(isset($skillData['user_detail']) && !empty($skillData['user_detail'])){
	usort($skillData['user_detail'], function($a, $b) {
	    return $a['full_name'] > $b['full_name'];
	});
}

$all_dept = (isset($data[0][0]['department_skill']) && !empty($data[0][0]['department_skill'])) ? json_decode($data[0][0]['department_skill'], true) : [];
$all_org = (isset($data[0][0]['all_org']) && !empty($data[0][0]['all_org'])) ? json_decode($data[0][0]['all_org'], true) : [];
$all_stories = (isset($data[0][0]['all_stories']) && !empty($data[0][0]['all_stories'])) ? json_decode($data[0][0]['all_stories'], true) : [];

	$all_dept = htmlentity($all_dept, 'dept_name');
	$all_org = htmlentity($all_org, 'name');
	$all_stories = htmlentity($all_stories, 'name');

	// uasort($all_dept, "compareASCII");
	// uasort($all_org, "compareASCII");
	// uasort($all_stories, "compareASCII");

$countDept = $countOrg = $countStory = 0;
if(isset($all_dept) && !empty($all_dept)){
	usort($all_dept, function($a, $b) {
	    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['dept_name']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['dept_name']);
	    return strcasecmp($t1, $t2);
	});
	$countDept = count($all_dept);
}

if(isset($all_org) && !empty($all_org)){
	usort($all_org, function($a, $b) {
	    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['name']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['name']);
	    return strcasecmp($t1, $t2);
	});
	$countOrg = count($all_org);
}

if(isset($all_stories) && !empty($all_stories)){
	usort($all_stories, function($a, $b) {
	    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['name']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['name']);
	    return strcasecmp($t1, $t2);
	});
	$countStory = count($all_stories);
}

$current_org = $this->Permission->current_org();

$item_title = htmlentities($skillData['title'], ENT_QUOTES, "UTF-8");
?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><i class="skillwhiteicon"></i> <span class="text-ellipsis"><?php echo $item_title;?></span></h3>
			</div>
			<div class="modal-body">
				<div class="row d-flex-s">

				<div class="col-sm-3 col-md-3 col-lg-3 col-skill-1 skill-profile-cols skill-subject-domain-left">
					<div class="skill-profile-img skills-blue-border">
					<?php if( isset($skillData['image']) && !empty($skillData['image']) ){
						if( file_exists(WWW_ROOT.SKILL_IMAGE_PATH.$skillData['image']) ){
					?>
						<img class="" alt="profile" src="<?php echo SITEURL.SKILL_IMAGE_PATH.$skillData['image'];?>?time=<?php echo time();?>">
					<?php
						} else {
							echo "No Image";
						}
					} else { ?>
						No Image
					<?php } ?>
					</div>
					<div class="skill-menu-left">
					<ul>
					<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon linkgreenicon"></i> Links </span> <span class="count"><?php echo ( isset($skillData['linktotal']) && !empty($skillData['linktotal']) ) ? $skillData['linktotal'] : 0;?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon filegreenicon"></i> Files </span> <span class="count"><?php echo ( isset($skillData['filetotal']) && !empty($skillData['filetotal']) ) ? $skillData['filetotal'] : 0; ?></span> </a></li>
					</ul>
					<h6>Related</h6>
					<ul>
						<?php
						$sk_link = "javascript:void(0);";
						$sk_class = "";
						if( isset($skillData['totalpeople']) && !empty($skillData['totalpeople']) ){
							$sk_link = Router::Url( array( "controller" => "searches", "action" => "people", "skill" => $skillData['id'], 'admin' => FALSE ), true );
							$sk_class = "cursor: pointer;";
						} ?>
						<li><a href="<?php echo $sk_link; ?>" style="<?php echo $sk_class; ?>"><span class="skill-menu-left-text"><i class="skill-menu-icon peoplegreenicon"></i> People </span> <span class="count"><?php echo ( isset($skillData['totalpeople']) && !empty($skillData['totalpeople']) ) ? $skillData['totalpeople'] : 0; ?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon organizationgreenicon"></i> Organizations </span> <span class="count"><?php echo $countOrg; ?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon locationgreenicon"></i> Locations </span> <span class="count"><?php echo ( isset($skillData['totallocation']) && !empty($skillData['totallocation']) ) ? $skillData['totallocation'] : 0; ?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon departmentsgreenicon"></i> Departments </span> <span class="count"><?php echo $countDept; ?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon storygreenicon"></i> Stories </span> <span class="count"><?php echo $countStory; ?></span> </a></li>
					</ul>

					<?php if( isset($skillData['updatedby']) && !empty($skillData['updatedby']) ){ ?>
					<ul>

					<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon modifiedgreenicon"></i> <?php echo ( isset($skillData['updatedby']) && !empty($skillData['updatedby']) ) ? $skillData['updatedby'] : ''; ?> </span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon dategreenicon"></i> <?php echo ( isset($skillData['modified']) && !empty($skillData['modified']) && $skillData['modified']!= '0000-00-00 00:00:00' ) ? $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($skillData['modified'])), $format = 'd M, Y h:i A') : ''; ?>
						</span>  </a></li>
					</ul>
					<?php } ?>
					</div>

				</div>

				<div class="col-sm-9 col-md-9 col-sm-9 col-skill-2">
					<div class="row d-flex-s s-height100">
						<div class="col-sm-12 col-md-5 col-lg-5 common-tab-sec view-skills-tab left-border">
						<ul class="nav nav-tabs tab-list">
							<li class="active">
								<a data-toggle="tab" class="active" href="#profiledetails" aria-expanded="true">Details</a>
							</li>
							<li>
								<a data-toggle="tab" href="#links" aria-expanded="false">Links</a>
							</li>
							<li>
								<a data-toggle="tab" href="#files" aria-expanded="false">Files</a>
							</li>
						</ul>

						<div class="tab-content skills-tab-content">
							<div id="profiledetails" class="tab-pane fade active in">
								<div class="description-scroll" style="line-height: 23px;">
								<?php if( isset($skillData['description']) && !empty($skillData['description']) ){ ?>
								<?php echo nl2br(htmlentities($skillData['description'], ENT_QUOTES, "UTF-8")); ?>
								<?php } else { ?>
								<p>No Description</p>
								<?php } ?>
								</div>
							</div>
							<div id="links" class="tab-pane fade">
								<div class="popup-skill-list">
									<ul>
										<?php if( isset($skillData['skill_links']) && !empty($skillData['skill_links']) ){
											foreach($skillData['skill_links'] as $listlinks){

												$var = $listlinks['web_link'];

											?>
											  <li><span class="list-text"><?php echo htmlentities($listlinks['link_name'], ENT_QUOTES, "UTF-8");?></span> <span class="list-icon"> <a href="<?php echo $var;?>" target="_blank"  class="tipText" title="Open Link"><i class="openlinkicon"></i></a>  </span></li>
										  <?php }
										  } else {
											echo '<li>No Links</li>';
										}
										  ?>
									</ul>
									</div>
							</div>
							<div id="files" class="tab-pane fade">
								<div class="popup-skill-list">
										<ul>
										  <?php if( isset($skillData['skill_files']) && !empty($skillData['skill_files']) ){
											foreach($skillData['skill_files'] as $listfiles){
										?>
										  <li><span class="list-text"><?php echo htmlentities($listfiles['file_name'], ENT_QUOTES, "UTF-8");?></span> <span class="list-icon"> <a href="<?php echo SITEURL?>competencies/download_files/<?php echo $listfiles['id']; ?>/skill" class="tipText" title="Download File" download><i class="downloadblackicon"></i></a>  </span></li>
										<?php }
										} else {
											echo '<li>No Files</li>';
										}
										?>
										</ul>
									</div>
							</div>

						</div>



						</div>

						<div class="col-sm-12 col-md-7 col-lg-7 common-tab-sec view-skills-tab left-border">
							<ul class="nav nav-tabs tab-list">
								<li class="active">
									<a data-toggle="tab" class="active" href="#people" aria-expanded="true">People</a>
								</li>
								<li>
									<a data-toggle="tab" href="#community" aria-expanded="false"> Community </a>
								</li>
								<li >
									<a data-toggle="tab" href="#stories" aria-expanded="false">  Stories  </a>
								</li>
							</ul>

							<div class="tab-content">
								<div id="people" class="tab-pane fade active in">
									<div class="skillpeoplelist">
									<ul>
									<?php
								if( isset($skillData['user_detail']) && !empty($skillData['user_detail']) ){
									foreach($skillData['user_detail'] as $listuser){
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
											<img class="" alt="<?php echo $profilesPic; ?>" src="<?php echo $profilesPic; ?>"></span>
											<?php if($current_org['organization_id'] != $listuser['organization']){ ?>
												<i class="communitygray18 tipText show-user-popup" title="" data-original-title="Not In Your Organization"data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>"></i>
											<?php } ?>
										</div>
										<span class="skill-popple-info">
										<h6 data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $listuser['user_id'], 'admin' => FALSE ), true ); ?>"><?php echo $listuser['full_name']; ?></h6>
										<p><?php echo $listuser['job_role']; ?></p>
										</span>
									</li>
								<?php }
								} else { ?>
								<li>No People</li>
								<?php } ?>
								</ul>
								</div>
								</div>
								<div id="community" class="tab-pane fade">
									<div class="popuplocationlist" style="overflow: hidden; width: auto; height: 312px;">
										<ul class="loc-names-ul">
											<?php if(isset($all_org) && !empty($all_org)) { ?>
											<?php
											foreach ($all_org as $key => $value) {
												$orgdata = $value;
											?>
											<li class="loc-names-li comm-list-item" data-id="<?php echo $orgdata['id']; ?>" data-type="org">
												<span class="loc-name-list">
													<span class="loc-thumb">
														<?php if(!empty($orgdata['image'])){ ?>
															<img src="<?php echo SITEURL . ORG_IMAGE_PATH . $orgdata['image']; ?>" >
														<?php } ?>
													</span>
													<div class="loc-info">
														<span class="loc-name"><?php echo $orgdata['name']; ?></span>
														<div class="loc-cc-name">
															<span class="com-single-name"><?php echo $value['type']; ?></span>
														</div>
													</div>
												</span>
											</li>
											<?php } ?>
											<?php }else{ ?>
											<li class="mo-msg">No Organizations</li>
											<?php } ?>
											<?php if( isset($skillData['location_skill']) && !empty($skillData['location_skill']) ){
												$loc_ids = Set::extract($skillData['location_skill'], '{n}/id');
												$loc_data = $this->Permission->competency_locations($loc_ids);
											?>
													<?php foreach ($loc_data as $key => $value) { ?>
													<li class="loc-names-li comm-list-item" data-id="<?php echo $value['locations']['id']; ?>" data-type="loc">
														<span class="loc-name-list">
															<span class="loc-thumb">
																<?php if(!empty($value['locations']['image'])){ ?>
																	<img alt="Location Image" src="<?php echo SITEURL . LOC_IMAGE_PATH . $value['locations']['image'];?>" >
																<?php } ?>
															</span>
															<div class="loc-info">
																<span class="loc-name"><?php echo htmlentities($value['locations']['name'], ENT_QUOTES, "UTF-8"); ?></span>
																<div class="loc-cc-name">
																	<span class="sks-city"><?php echo htmlentities($value['locations']['city'], ENT_QUOTES, "UTF-8"); ?>,</span>
																	<span class="sks-country"><?php echo $value['countries']['countryName']; ?></span>
																</div>
															</div>
														</span>
													</li>
												<?php } ?>
											<?php }else{ ?>
											<li class="mo-msg no-location">No Locations</li>
											<?php } ?>

											<?php if(isset($all_dept) && !empty($all_dept)) { ?>
											<?php
											foreach ($all_dept as $key => $value) {
												$deptdata = $value;
											?>
											<li class="loc-names-li comm-list-item" data-id="<?php echo $deptdata['id']; ?>" data-type="dept">
												<span class="loc-name-list">
													<span class="loc-thumb">
														<?php if(!empty($deptdata['dept_image'])){ ?>
															<img src="<?php echo SITEURL . COMM_IMAGE_PATH . $deptdata['dept_image']; ?>" >
														<?php } ?>
													</span>
													<div class="loc-info">
														<span class="loc-name"><?php echo $deptdata['dept_name']; ?></span>
														<div class="loc-cc-name">
															<span class="com-single-name">Department</span>
														</div>
													</div>
												</span>
											</li>
											<?php } ?>
											<?php }else{ ?>
											<li class="mo-msg ">No Departments</li>
											<?php } ?>
										</ul>
									</div>
								</div>
								<div id="stories" class="tab-pane fade">
									<div class="popuplocationlist" style="overflow: hidden; width: auto; height: 312px;">
										<ul class="loc-names-ul">
											<?php if(isset($all_stories) && !empty($all_stories)) { ?>
											<?php
											foreach ($all_stories as $key => $value) {
											?>
											<li class="loc-names-li story-list-item" data-id="<?php echo $value['id']; ?>" data-type="org">
												<span class="loc-name-list">
													<span class="loc-thumb">
														<?php if(!empty($value['image'])){ ?>
															<img src="<?php echo SITEURL . STORY_IMAGE_PATH . $value['image']; ?>" >
														<?php } ?>
													</span>
													<div class="loc-info">
														<span class="loc-name"><?php echo $value['name']; ?></span>
														<div class="loc-cc-name">
															<span class="com-single-name"><?php echo $value['type']; ?></span>
														</div>
													</div>
												</span>
											</li>
											<?php } ?>
											<?php }else{ ?>
											<li class="mo-msg">No Stories</li>
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
				<button type="button" class="btn outline-green-s pull-left filter-selected tipText" title="Go to Competencies">Competencies</button>
				<button type="button" id="discard" class="btn btn-success right" data-dismiss="modal">Close</button>
			</div>

<style type="text/css">
	.skill-popple-info h6 {
		cursor: pointer;
	}
</style>
<script type="text/javascript" >
$(function(){

	var activetab = '<?php echo $active; ?>';
	$('.nav.nav-tabs a[href="#'+activetab+'"]').tab('show');

	$.selected_view_type = 'skill';
	$.selected_view = '<?php echo addslashes($skillData['title']); ?>';

	$('.skill-popple-icon, .skill-popple-info h6, .show-user-popup').off('click').on('click', function(e){
		$('#modal_view_skill').modal('hide');
	});

	$(".skillpeoplelist").slimScroll({height: 312, alwaysVisible: true});
	$(".description-scroll").slimScroll({height: 320,alwaysVisible: true});
	$('.tab-list').on('shown.bs.tab', function(){
		$(".popup-skill-list ul").slimScroll({height: 320,width:'100%',alwaysVisible: true});
		$(".skilllocationlist").slimScroll({height: 312, alwaysVisible: true});
		$(".popuplocationlist").slimScroll({height: 312, alwaysVisible: true});
	})

	$('.comm-list-item').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();

		var url = $js_config.base_url + 'communities/view/' + data.type + '/' + data.id;

		var modal_id = '#modal_view_org';
		if(data.type == 'loc') modal_id = '#modal_view_loc';
		if(data.type == 'dept') modal_id = '#modal_view_dept';

		$('#modal_view_skill').modal('hide');
		$(modal_id).modal({
			remote: url
		})
		.modal('show');
	});

	$('.story-list-item').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'stories/view/story/' + data.id
		$('#modal_view_skill').modal('hide');
		$('#story_view').modal({
			remote: url
		})
		.modal('show');
	});

	$('body').on('click', '.filter-selected', function(event) {
		event.preventDefault();

		if( window.location.href.indexOf("competencies") <= 0 ) {
			$('#modal_view_skill').modal('hide');
			var id = '<?php echo $skillData["id"]; ?>';
			window.location.href = $js_config.base_url+'competencies/index/skills/'+id;
		}

	});



})
</script>