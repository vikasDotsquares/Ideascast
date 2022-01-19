
<?php
if( isset($list_data) && !empty($list_data) ){
	foreach($list_data  as $detail ){
		$departments = $detail['departments'];

		$updatedby = ( isset($detail[0]['updated_by']) && !empty($detail[0]['updated_by']) ) ? $detail[0]['updated_by'] : '';

		$people = ( isset($detail['details_counts']['totalpeople']) && !empty($detail['details_counts']['totalpeople']) ) ? $detail['details_counts']['totalpeople'] : 0;

		$total_story = ( isset($detail['story_counts']['total_story']) && !empty($detail['story_counts']['total_story']) ) ? $detail['story_counts']['total_story'] : 0;

		$deptitle = htmlentities($departments['name'], ENT_QUOTES, "UTF-8");

?>
	<div class="ssd-data-row">
		<div class="ssd-col dep-col-1">
			<span class="dep-info-name">
				<span class="loc-thumb" data-html="false" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'admin' => false)); ?>" data-area="" data-target="#modal_view_dept" data-toggle="modal">
					<?php if(!empty($departments['image'])){ ?>
						<img src="<?php echo SITEURL . COMM_IMAGE_PATH . $departments['image'];?>?<?php echo uniqid(); ?>" >
					<?php } ?>
				</span>
				<div class="loc-info">
					<span data-html="false" class="loc-name" title="<?php echo htmlspecialchars($deptitle); ?>" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'admin' => false)); ?>" data-area="" data-target="#modal_view_dept" data-toggle="modal"><?php echo $deptitle; ?></span>
					<div class="loc-cc-name">
						<span class="sks-city">Department</span>
					</div>
				</div>
			</span>
		</div>
		<div class="ssd-col dep-col-2">
			<?php if( $people > 0  ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'people', 'admin' => false)); ?>" data-area="" data-target="#modal_view_dept" data-toggle="modal" data-type="dept"><?php echo $people;?></span>
			<?php } else {
				echo $people;
			}
			?>
		</div>
		<div class="ssd-col dep-col-3" >
			<span class="competencies-list">
				<span class="competencies-list-bg competencies-list-bg-skill tipText" title="Skills" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_dept" data-toggle="modal" data-type="dept">
					<i class="skills-icon"></i>
					<span class="sks-title" ><?php echo (!empty($detail['skill_counts']['totalskills'])) ? $detail['skill_counts']['totalskills'] : 0;?></span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-subject tipText" title="Subjects" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_dept" data-toggle="modal" data-type="dept">
					<i class="subjects-icon" ></i>
					<span class="sks-title" ><?php echo ($detail['subject_counts']['totalsubjects']) ? $detail['subject_counts']['totalsubjects'] : 0;?></span>
				</span>
				<span class="competencies-list-bg competencies-list-bg-domain tipText" title="Domains" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'competencies', 'admin' => false)); ?>" data-target="#modal_view_dept" data-toggle="modal" data-type="dept">
					<i class="domain-icon" ></i>
					<span class="sks-title"><?php echo ($detail['domain_counts']['totaldomains']) ? $detail['domain_counts']['totaldomains'] : 0;?></span>
				</span>
			</span>
		</div>
		<div class="ssd-col dep-col-5">
			<?php if( $total_story > 0  ) { ?>
			<span class="view_profile" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'stories', 'admin' => false)); ?>" data-area="" data-target="#modal_view_dept" data-toggle="modal" data-type="dept"><?php echo $total_story;?></span>
			<?php } else {
				echo $total_story;
			}
			?>
		</div>
		<div class="ssd-col dep-col-4" >
			<span class="text-ellipsis cursor_pointer tipText"  data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $departments['modified_by']; ?>" data-original-title="<?php echo $updatedby;  ?>"><?php echo $updatedby;  ?></span>
			<div class="com-date">
			<?php

			if(( isset($departments['modified']) && !empty($departments['modified']) && $departments['modified'] != '0000-00-00 00:00:00' )) {
				echo $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($departments['modified'])), $format = 'd M, Y') ;
			}

			?>
		</div>
		</div>
		<!--<div class="ssd-col dep-col-5">

		</div>-->
		<div class="ssd-col dep-col-6">

			<a class="view_subject tipText" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'view', 'dept', $departments['id'], 'admin' => false)); ?>" data-area="" data-target="#modal_view_dept" data-toggle="modal" data-original-title="View" data-type="dept" ><i class="view-icon"></i></a>
			<?php
				if( $user_is_admin ){
			?>
				<a class="tipText edit_subjects" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'edit', 'dept', $departments['id'], 'admin' => false)); ?>" data-area="" data-target="#modal_create" data-toggle="modal" data-original-title="Edit" data-id="<?php echo $departments['id'];?>" data-type="dept"> <i class="edit-icon"></i></a>

				<a class="delete_subject tipText" data-id="<?php echo $departments['id'];?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'trash', 'dept', $departments['id'], 'admin' => false)); ?>" data-type="dept" data-original-title="Delete" > <i class="delete-icon"></i></a>
			<?php } ?>

		</div>
	</div>
	<?php } // END FOREACH ?>
<?php } ?>
