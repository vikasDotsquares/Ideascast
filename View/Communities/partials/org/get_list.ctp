<?php

if( isset($list_data) && !empty($list_data) ){
	foreach($list_data  as $detail ){
		// pr($detail);

		$organizations = $detail['organizations'];

		$updatedby = ( isset($detail[0]['updated_by']) && !empty($detail[0]['updated_by']) ) ? $detail[0]['updated_by'] : '';

		$totalskills = ( isset($detail['skill_counts']['totalskills']) && !empty($detail['skill_counts']['totalskills']) ) ? $detail['skill_counts']['totalskills'] : 0;
		$totalsubjects = ( isset($detail['subject_counts']['totalsubjects']) && !empty($detail['subject_counts']['totalsubjects']) ) ? $detail['subject_counts']['totalsubjects'] : 0;
		$totaldomains = ( isset($detail['domain_counts']['totaldomains']) && !empty($detail['domain_counts']['totaldomains']) ) ? $detail['domain_counts']['totaldomains'] : 0;
		$linktotal = ( isset($detail['link_counts']['linktotal']) && !empty($detail['link_counts']['linktotal']) ) ? $detail['link_counts']['linktotal'] : 0;
		$filetotal = ( isset($detail['file_counts']['filetotal']) && !empty($detail['file_counts']['filetotal']) ) ? $detail['file_counts']['filetotal'] : 0;
		$totalpeople = ( isset($detail['details_counts']['totalpeople']) && !empty($detail['details_counts']['totalpeople']) ) ? $detail['details_counts']['totalpeople'] : 0;
		$total_location = ( isset($detail['location_counts']['total_location']) && !empty($detail['location_counts']['total_location']) ) ? $detail['location_counts']['total_location'] : 0;
		$total_edomain = ( isset($detail['edomain_counts']['total_edomain']) && !empty($detail['edomain_counts']['total_edomain']) ) ? $detail['edomain_counts']['total_edomain'] : 0;

		$total_story = ( isset($detail['story_counts']['total_story']) && !empty($detail['story_counts']['total_story']) ) ? $detail['story_counts']['total_story'] : 0;

		$org_title = htmlentities($organizations['name'], ENT_QUOTES, "UTF-8");

		$org_count = 0;

?>
	<div class="ssd-data-row">
		<div class="loc-col org-col-1">
			<span class="loc-name-list">
				<span class="loc-thumb" data-html="false" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'details', 'admin' => false)); ?>" data-target="#modal_view_org" data-toggle="modal" data-type="org">
					<?php if(!empty($organizations['image'])){ ?>
						<img src="<?php echo SITEURL . ORG_IMAGE_PATH .  ($organizations['image']); ?>" >
					<?php } ?>
				</span>
				<div class="loc-info">
					<span data-html="true" class="loc-name" data-html="false" title="<?php echo htmlspecialchars( $org_title);?>" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'details', 'admin' => false)); ?>" data-target="#modal_view_org" data-toggle="modal" data-type="org"><?php echo htmlentities($organizations['name'], ENT_QUOTES, "UTF-8");?></span>
					<div class="loc-cc-name">
						<span class="com-single-name"><?php echo $detail['organization_types']['org_type'];?></span>
					</div>
				</div>
			</span>
		</div>

		<div class="loc-col org-col-2">
			<?php if( $totalpeople > 0 ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'people', 'admin' => false)); ?>" data-target="#modal_view_org" data-toggle="modal" data-type="org"><?php echo $totalpeople;?></span>
			<?php } else {
				echo $totalpeople;
			}
			?>
		</div>
		<div class="loc-col org-col-3">
			<?php if( $total_location > 0 ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'locations', 'admin' => false)); ?>" data-target="#modal_view_org" data-toggle="modal" data-type="org"><?php echo $total_location;?></span>
			<?php } else {
				echo $total_location;
			}
			?>
		</div>
		<div class="loc-col org-col-4">
			<span class="competencies-list">
				<span class="competencies-list-bg competencies-list-bg-skill tipText" title="Skills" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_org" data-toggle="modal" data-type="org">
					<i class="skills-icon"></i>
					<span class="sks-title" ><?php echo $totalskills; ?></span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-subject tipText" title="Subjects" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_org" data-toggle="modal" data-type="org">
					<i class="subjects-icon" ></i>
					<span class="sks-title" ><?php echo $totalsubjects; ?></span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-domain tipText" title="Domains" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_org" data-toggle="modal" data-type="org">
					<i class="domain-icon" ></i>
					<span class="sks-title"><?php echo $totaldomains; ?></span>
				</span>
			</span>
		</div>
		<div class="loc-col org-col-10">
            <?php if( $total_story ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'stories', 'admin' => false)); ?>"  data-target="#modal_view_org" data-toggle="modal" data-type="org"><?php echo $total_story;?></span>
			<?php } else {
				echo '0';
			}
			?>
         </div>
		<div class="loc-col org-col-5">
			<?php if( $linktotal ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'links', 'admin' => false)); ?>"  data-target="#modal_view_org" data-toggle="modal" data-type="org"><?php echo $detail['link_counts']['linktotal'];?></span>
			<?php } else {
				echo '0';
			}
			?>
		</div>
		<div class="loc-col org-col-6">
			<?php if( $filetotal ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'files', 'admin' => false)); ?>"  data-target="#modal_view_org" data-toggle="modal" data-type="org"><?php echo $detail['file_counts']['filetotal'];?></span>
			<?php } else {
				echo '0';
			}
			?>
		</div>
		<div class="loc-col org-col-7" >
			<span class="text-ellipsis cursor_pointer"  data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $organizations['modified_by']; ?>"><?php echo $updatedby; ?></span>
			<div class="com-date">
			<?php

			if(( isset($organizations['modified']) && !empty($organizations['modified']) && $organizations['modified'] != '0000-00-00 00:00:00' )) {
				echo $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($organizations['modified'])), $format = 'd M, Y') ;
			}

			?>

			</div>
		</div>
		<!--<div class="loc-col org-col-8">

		</div>-->
		<div class="loc-col org-col-9">

			<a class="view tipText" href="#" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'org', $organizations['id'], 'admin' => false)); ?>"  data-target="#modal_view_org" data-toggle="modal" data-original-title="View" data-type="org" ><i class="view-icon"></i></a>
			<?php
				if( $user_is_admin ){
			?>
				<a class="tipText edit " href="#" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'edit', 'org', $organizations['id'], 'admin' => false)); ?>"  data-target="#modal_create" data-toggle="modal" data-original-title="Edit" data-id="<?php echo $organizations['id'];?>" data-type="org"> <i class="edit-icon"></i></a>

				<a class="delete tipText" href="#" data-id="<?php echo $organizations['id'];?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'trash', 'org', $organizations['id'], 'admin' => false)); ?>" data-type="dept" data-original-title="Delete" > <i class="delete-icon"></i></a>
			<?php } ?>

		</div>
	</div>
	<?php } // END FOREACH
	}
	else {
	?>
	<div class="ssd-data-row no-data">
		<div class="competencies-data data-wrapper" style="width:100%;">
			<div class="no-res-found text-center">No Organizations</div>
		</div>
	</div>
<?php } ?>
