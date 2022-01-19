<?php

		// pr($detail);
if( isset($list_data) && !empty($list_data) ){
	foreach($list_data  as $detail ){

		$stories = $detail['stories'];
		$owner_id = ( isset($stories['created_by']) && !empty($stories['created_by']) ) ? $stories['created_by'] : '';

		$created_by = ( isset($detail[0]['created_by']) && !empty($detail[0]['created_by']) ) ? $detail[0]['created_by'] : '';
		$updated_by = ( isset($detail[0]['updated_by']) && !empty($detail[0]['updated_by']) ) ? $detail[0]['updated_by'] : '';

		$story_type = $detail['story_types']['story_type'];

		$total_people = ( isset($detail['details_counts']['total_people']) && !empty($detail['details_counts']['total_people']) ) ? $detail['details_counts']['total_people'] : 0;
		$total_organization = ( isset($detail['org_counts']['total_organization']) && !empty($detail['org_counts']['total_organization']) ) ? $detail['org_counts']['total_organization'] : 0;
		$total_location = ( isset($detail['location_counts']['total_location']) && !empty($detail['location_counts']['total_location']) ) ? $detail['location_counts']['total_location'] : 0;
		$total_department = ( isset($detail['dept_counts']['total_department']) && !empty($detail['dept_counts']['total_department']) ) ? $detail['dept_counts']['total_department'] : 0;
		$total_skills = ( isset($detail['skill_counts']['total_skills']) && !empty($detail['skill_counts']['total_skills']) ) ? $detail['skill_counts']['total_skills'] : 0;
		$total_subjects = ( isset($detail['subject_counts']['total_subjects']) && !empty($detail['subject_counts']['total_subjects']) ) ? $detail['subject_counts']['total_subjects'] : 0;
		$total_domains = ( isset($detail['domain_counts']['total_domains']) && !empty($detail['domain_counts']['total_domains']) ) ? $detail['domain_counts']['total_domains'] : 0;
		$total_link = ( isset($detail['link_counts']['total_link']) && !empty($detail['link_counts']['total_link']) ) ? $detail['link_counts']['total_link'] : 0;
		$total_file = ( isset($detail['file_counts']['total_file']) && !empty($detail['file_counts']['total_file']) ) ? $detail['file_counts']['total_file'] : 0;
		$total_story = ( isset($detail['story_counts']['total_story']) && !empty($detail['story_counts']['total_story']) ) ? $detail['story_counts']['total_story'] : 0;

		$total_competency = ($total_skills + $total_subjects + $total_domains);

		$detail_title =  htmlentities($stories['name'], ENT_QUOTES, "UTF-8");


?>
	<div class="ssd-data-row">

		<div class="loc-col storie-col-1">
			<div class="storie-data-col1">
			<span class="storiedate-image" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'details', 'admin' => false)); ?>">
				<?php if(!empty($stories['image'])){ ?>
					<img src="<?php echo SITEURL . STORY_IMAGE_PATH . $stories['image'];?>" >
				<?php } ?>
			</span>
			</div>
			<div class="storie-data-col2">
				<div class="storie-data-heading">
					<span class="title-wrap"><a class="title" href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'details', 'admin' => false)); ?>"><?php echo $detail_title; ?></a></span>
						<span class="storie-type"><?php echo $story_type; ?></span>
				</div>
				<p class="stroies-des"><?php echo nl2br(htmlentities($stories['summary'], ENT_QUOTES, "UTF-8")); ?></p>

				<div class="storie-data-info-bottom">
					<ul>
						<li class="tipText" title="Created By"><i class="st-info-icon peoplegreenicon"></i> <?php echo $created_by; ?> </li>
						<li class="tipText" title="Updated By"><i class="st-info-icon modifiedgreenicon"></i> <?php echo $updated_by; ?> </li>
						<li class="tipText" title="Updated On"><i class="st-info-icon dategreenicon"></i>
						<?php if(( isset($stories['modified']) && !empty($stories['modified']) && $stories['modified'] != '0000-00-00 00:00:00' )) {
							echo $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($stories['modified'])), $format = 'd M, Y h:i A') ;
						} ?>
				  		</li>
						<li class="s-border-left">
							<span class="attach-file <?php if($total_link) { ?> data-exists <?php } ?>" <?php if($total_link) { ?> data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'links', 'admin' => false)); ?>" <?php } ?>><i class="st-info-icon linkgreenicon"></i> <?php echo $total_link; ?> </span>
							<span class="attach-file <?php if($total_file) { ?> data-exists <?php } ?>" <?php if($total_file){ ?> data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'files', 'admin' => false)); ?>" <?php } ?>><i class="st-info-icon filegreenicon"></i> <?php echo $total_file; ?> </span>
						</li>
					</ul>
				</div>
			</div>
			<div class="storie-data-col3">
			<div class="storie-list">
			<ul>
				<li><a <?php if($total_people){ ?> href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'people', 'admin' => false)); ?>" <?php } ?>><span class="storie-list-left-text"><i class="skill-menu-icon peoplegreenicon"></i> People </span> <span class="count"><?php echo $total_people; ?></span> </a></li>

				<li><a <?php if($total_organization){ ?> href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'communities', 'admin' => false)); ?>" <?php } ?>><span class="storie-list-left-text"><i class="skill-menu-icon organizationgreenicon"></i> Organizations </span> <span class="count"><?php echo $total_organization; ?></span> </a></li>

				<li><a <?php if($total_location){ ?> href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'communities', 'admin' => false)); ?>" <?php } ?>><span class="storie-list-left-text"><i class="skill-menu-icon locationgreenicon"></i> Locations </span> <span class="count"><?php echo $total_location; ?></span> </a></li>

				<li><a <?php if($total_department){ ?> href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'communities', 'admin' => false)); ?>" <?php } ?>><span class="storie-list-left-text"><i class="skill-menu-icon departmentsgreenicon"></i> Departments </span> <span class="count"><?php echo $total_department; ?></span> </a></li>

				<li><a <?php if($total_competency){ ?> href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'competencies', 'admin' => false)); ?>" <?php } ?>><span class="storie-list-left-text"><i class="skill-menu-icon competenciesgreenicon"></i> Competencies </span> <span class="count"><?php echo $total_competency; ?></span> </a></li>

				<li><a <?php if($total_story){ ?> href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'stories', 'admin' => false)); ?>" <?php } ?>><span class="storie-list-left-text"><i class="skill-menu-icon storygreenicon"></i> Stories </span> <span class="count"><?php echo $total_story; ?></span> </a></li>
			</ul>
			</div>
			</div>
		</div>
		<div class="loc-col storie-col-2">
			<a class="view tipText" title="View" href="#" data-toggle="modal" data-target="#story_view" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'view', 'story', $stories['id'], 'details', 'admin' => false)); ?>"><i class="view-icon"></i></a>
			<?php if($user_is_admin || $owner_id == $this->Session->read('Auth.User.id')){ ?>
				<a class="edit tipText " title="Edit" href="#" data-toggle="modal" data-target="#modal_create" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'edit', 'story', $stories['id'], 'admin' => false)); ?>" data-id="<?php echo $stories['id']; ?>"> <i class="edit-icon"></i></a>
				<a class="delete tipText" title="Delete" href="#" data-id="<?php echo $stories['id'];?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'trash', 'story', $stories['id'], 'admin' => false)); ?>" data-type="story" > <i class="delete-icon"></i></a>
			<?php } ?>
		</div>

	</div>
	<?php } // END FOREACH
	}
	else {
	?>
	<div class="ssd-data-row no-data">
		<div class="competencies-data data-wrapper" style="width:100%;">
			<div class="no-res-found text-center">No Stories</div>
		</div>
	</div>
<?php } ?>

<script type="text/javascript">
	$(function(){

		// $('.stroies-des').multiLineEllipsis()
		/*$('.stroies-des').each(function () {
			var textContainerHeight = $(this).height();
		  var $ellipsisText = $(this);

		  while ($ellipsisText.outerHeight(true) > textContainerHeight) {
		    $ellipsisText.text(function(index, text) {
		      return text.replace(/\W*\s(\S)*$/, '...');
		    });
		  }
		});*/
		/*function ellipsizeTextBox(id) {
			console.log(id)
		    var el = id;
		    var keep = el.innerHTML;
		    while(el.scrollHeight > el.offsetHeight) {
		        el.innerHTML = keep;
		        el.innerHTML = el.innerHTML.substring(0, el.innerHTML.length-1);
		        keep = el.innerHTML;
		        el.innerHTML = el.innerHTML + "...";
		    }
		}
		$('.stroies-des').each(function () {
			ellipsizeTextBox(this)
		});*/
	})
</script>
