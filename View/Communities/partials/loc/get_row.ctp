
<?php
if( isset($list_data) && !empty($list_data) ){
	foreach($list_data  as $detail ){

		$locations = $detail['locations'];

		$updatedby = ( isset($detail[0]['updated_by']) && !empty($detail[0]['updated_by']) ) ? $detail[0]['updated_by'] : '';

		$org_count = ( isset($detail['org_counts']['totalorg']) && !empty($detail['org_counts']['totalorg']) ) ? $detail['org_counts']['totalorg'] : 0;
		$people = ( isset($detail['people_counts']['totalpeople']) && !empty($detail['people_counts']['totalpeople']) ) ? $detail['people_counts']['totalpeople'] : 0;
		$total_story = ( isset($detail['story_counts']['total_story']) && !empty($detail['story_counts']['total_story']) ) ? $detail['story_counts']['total_story'] : 0;

		$loctitle = htmlentities($locations['name'], ENT_QUOTES, "UTF-8");

?>
	<div class="ssd-data-row">
		<div class="loc-col loc-col-1">
			<span class="loc-name-list">
				<span class="loc-thumb" data-html="false" title="<?php echo $loctitle;?>" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'details', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc">
					<?php if(!empty($locations['image'])){ ?>
						<img src="<?php echo SITEURL . LOC_IMAGE_PATH . $locations['image'];?>?<?php echo uniqid(); ?>" >
					<?php } ?>
				</span>
				<div class="loc-info">
					<span data-html="true" class="loc-name" data-html="false" title="<?php echo htmlspecialchars($loctitle);?>" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'details', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc"><?php echo $loctitle;?></span>
					<div class="loc-cc-name">
						<span class="sks-city"><?php echo htmlentities($locations['city'], ENT_QUOTES, "UTF-8");?>,</span>
						<span class="sks-country"><?php echo $detail['countries']['countryName'];?></span>
					</div>
				</div>
			</span>
		</div>
		<div class="loc-col loc-col-2">
			<span class="competencies-list">
				<span data-html="true" ><?php echo $detail['location_types']['type'];?></span>
			</span>
		</div>
		<div class="loc-col loc-col-3">
			<?php if( $people > 0 ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'people', 'admin' => false)); ?>"   data-target="#modal_view_loc" data-toggle="modal" data-type="loc"><?php echo $people;?></span>
			<?php } else {
				echo $people;
			}
			?>
		</div>
		<div class="loc-col loc-col-4">
			<?php if( $org_count > 0 ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'organization', 'admin' => false)); ?>"   data-target="#modal_view_loc" data-toggle="modal" data-type="loc"><?php echo $org_count;?></span>
			<?php } else {
				echo $org_count;
			}
			?>
		</div>
		<div class="loc-col loc-col-5">
			<span class="competencies-list">
				<span class="competencies-list-bg competencies-list-bg-skill tipText" title="Skills" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc">
					<i class="skills-icon"></i>
					<span class="sks-title" ><?php echo (!empty($detail['skill_counts']['totalskills'])) ? $detail['skill_counts']['totalskills'] : 0;?></span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-subject tipText" title="Subjects" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc">
					<i class="subjects-icon" ></i>
					<span class="sks-title" ><?php echo ($detail['subject_counts']['totalsubjects']) ? $detail['subject_counts']['totalsubjects'] : 0;?></span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-domain tipText" title="Domains" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc">
					<i class="domain-icon" ></i>
					<span class="sks-title"><?php echo ($detail['domain_counts']['totaldomains']) ? $detail['domain_counts']['totaldomains'] : 0;?></span>
				</span>
			</span>
		</div>
		<div class="loc-col loc-col-9">
			<?php if( $total_story > 0 ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'stories', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc"><?php echo $total_story;?></span>
			<?php } else {
				echo $total_story;
			}
			?>
		</div>
		<div class="loc-col loc-col-6">
			<?php if( !empty($detail['link_counts']['linktotal']) ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'links', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc"><?php echo $detail['link_counts']['linktotal'];?></span>
			<?php } else {
				echo '0';
			}
			?>
		</div>
		<div class="loc-col loc-col-7">
			<?php if( !empty($detail['file_counts']['filetotal']) ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'files', 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-type="loc"><?php echo $detail['file_counts']['filetotal'];?></span>
			<?php } else {
				echo '0';
			}
			?>
		</div>
		<div class="loc-col loc-col-8" >
			<span class="text-ellipsis cursor_pointer"  data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $locations['modified_by']; ?>"><?php echo $updatedby; ?></span>
			<div class="com-date">
			<?php

			if(( isset($locations['modified']) && !empty($locations['modified']) && $locations['modified'] != '0000-00-00 00:00:00' )) {
				echo $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($locations['modified'])), $format = 'd M, Y') ;
			}

			?></div>
		</div>
		<!--<div class="loc-col loc-col-9">

		</div>-->
		<div class="loc-col loc-col-10">

			<a class="view tipText" href="#" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'loc', $locations['id'], 'admin' => false)); ?>" data-target="#modal_view_loc" data-toggle="modal" data-original-title="View" data-type="loc" ><i class="view-icon"></i></a>
			<?php
				if( $user_is_admin ){
			?>
				<a class="tipText edit " href="#" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'edit', 'loc', $locations['id'], 'admin' => false)); ?>" data-target="#modal_create" data-toggle="modal" data-original-title="Edit" data-id="<?php echo $locations['id'];?>" data-type="loc"> <i class="edit-icon"></i></a>

				<a class="delete tipText" href="#" data-id="<?php echo $locations['id'];?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'trash', 'loc', $locations['id'], 'admin' => false)); ?>" data-type="dept" data-original-title="Delete" > <i class="delete-icon"></i></a>
			<?php } ?>

		</div>
	</div>
	<?php } // END FOREACH
	}
	else {
	?>
	<div class="ssd-data-row no-data">
		<div class="competencies-data data-wrapper" style="width:100%;">
			<div class="no-res-found text-center">No Locations</div>
		</div>
	</div>
<?php } ?>
