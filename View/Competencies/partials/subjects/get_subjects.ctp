<?php
if( isset($list_data) && !empty($list_data) ){
		foreach($list_data  as $details ){

		$updatedby = ( isset($details[0]['fullname']) && !empty($details[0]['fullname']) ) ? $details[0]['fullname'] : '';

	$linkstotal = ( isset($details['link_counts']['linktotal']) && !empty($details['link_counts']['linktotal']) ) ? $details['link_counts']['linktotal'] : 0;
	$filestotal = ( isset($details['file_counts']['filetotal']) && !empty($details['file_counts']['filetotal']) ) ? $details['file_counts']['filetotal'] : 0;
	$totalpeople = ( isset($details['user_counts']['totalpeople']) && !empty($details['user_counts']['totalpeople']) ) ? $details['user_counts']['totalpeople'] : 0;
	$totalkeyword = ( isset($details['keyword_counts']['totalkeyword']) && !empty($details['keyword_counts']['totalkeyword']) ) ? $details['keyword_counts']['totalkeyword'] : 0;

	$totallocation = ( isset($details['location_counts']['totallocation']) && !empty($details['location_counts']['totallocation']) ) ? $details['location_counts']['totallocation'] : 0;

	$totalorganization = ( isset($details['org_counts']['totalorganization']) && !empty($details['org_counts']['totalorganization']) ) ? $details['org_counts']['totalorganization'] : 0;
	$totaldepartment = ( isset($details['dept_counts']['totaldepartment']) && !empty($details['dept_counts']['totaldepartment']) ) ? $details['dept_counts']['totaldepartment'] : 0;

	$total_story = ( isset($details['story_counts']['total_story']) && !empty($details['story_counts']['total_story']) ) ? $details['story_counts']['total_story'] : 0;

	$item_title = htmlentities($details['subjects']['title'], ENT_QUOTES, "UTF-8");
	$tip_title = htmlspecialchars($item_title);
?>
	<div class="ssd-data-row">
		<div class="ssd-col ssd-col-1">
			<span class="competencies-list">
				<span class="competencies-list-bg competencies-list-bg-subject">
					<i class="subjects-icon tipText" title="Subject"></i>
					<span data-html="false" class="sks-title" title="<?php echo $tip_title;?>" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><?php echo $item_title;?></span>
				</span>
			</span>
		</div>
		<div class="ssd-col ssd-col-2">
		<?php if( $totalpeople > 0  ) { ?>
		<span class="view_profile" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>/people" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><?php echo $totalpeople;?></span>
		<?php } else {
			echo $totalpeople;
			}
		?>
		</div>

		<div class="ssd-col ssd-col-3">
			<div class="community-list">
				<?php if( $totalorganization > 0  ) { ?>
					<span class="community-list-sec data-exists" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>/community" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject"><i class="community-icon organizationgreenicon"></i> <?php echo $totalorganization; ?></span>
				<?php }else{ ?>
					<span class="community-list-sec"><i class="community-icon organizationgreenicon"></i> <?php echo $totalorganization; ?></span>
				<?php } ?>

				<?php if( $totallocation > 0  ) { ?>
					<span class="community-list-sec data-exists" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>/community" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject"><i class="community-icon locationgreenicon"></i> <?php echo $totallocation; ?></span>
				<?php }else{ ?>
					<span class="community-list-sec"><i class="community-icon locationgreenicon"></i> <?php echo $totallocation; ?></span>
				<?php } ?>

				<?php if( $totaldepartment > 0  ) { ?>
					<span class="community-list-sec data-exists" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>/community" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject"><i class="community-icon departmentsgreenicon"></i> <?php echo $totaldepartment; ?></span>
				<?php }else{ ?>
					<span class="community-list-sec"><i class="community-icon departmentsgreenicon"></i> <?php echo $totaldepartment; ?></span>
				<?php } ?>
			</div>
		</div>
		<div class="ssd-col ssd-col-5">
		<?php if( $total_story > 0  ) { ?>
			<span class="view_profile" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>/stories" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Skill"><?php echo $total_story;?></span>
		<?php } else {
			echo $total_story;
			}
		?>
	</div>
		<div class="ssd-col ssd-col-6">
		<?php if( $linkstotal > 0  ) { ?>
		<span class="view_profile" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>/links" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><?php echo $linkstotal;?></span>
		<?php } else {
			echo $linkstotal;
			}
		?>

		</div>
		<div class="ssd-col ssd-col-7">
		<?php if( $filestotal > 0  ) { ?>
		<span class="view_profile" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>/files" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><?php echo $filestotal;?></span>
		<?php } else {
			echo $filestotal;
			}
		?>
		</div>
		<div class="ssd-col ssd-col-8">
		<?php echo $totalkeyword;?>
		</div>
		<div class="ssd-col ssd-col-9 "  >
			<span class="text-ellipsis tipText user-profile" data-original-title="<?php echo $updatedby;  ?>"  data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $details['subjects']['modified_by'], 'admin' => false)); ?>"><?php echo $updatedby;  ?></span></div>
		<div class="ssd-col ssd-col-10">
			<?php

			$update_date =  ( isset($details['subjects']['modified']) && !empty($details['subjects']['modified']) ) ? $details['subjects']['modified'] : $details['subjects']['created'];


			echo ( isset($update_date) && !empty($update_date) && $update_date!= '0000-00-00 00:00:00' ) ? $this->Wiki->_displayDate(date('Y-m-d H:i:s', strtotime($update_date)), $format = 'd M, Y') : '';


			?>
		</div>
		<div class="ssd-col ssd-col-11">

			<a class="view_subject tipText" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $details['subjects']['id'];?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><i class="view-icon"></i></a>
		<?php
			if( $user_is_admin ){
		?>
			<a class="tipText edit_subjects" data-remote="<?php echo SITEURL;?>competencies/edit_subjects/<?php echo $details['subjects']['id'];?>" data-area="" data-target="#modal_create_skills" data-toggle="modal" data-original-title="Edit" data-id="<?php echo $details['subjects']['id'];?>" data-type="Subject"> <i class="edit-icon"></i></a>

			<a class="tipText delete_subject" data-id="<?php echo $details['subjects']['id'];?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo SITEURL;?>competencies/trash_subject/<?php echo $details['subjects']['id'];?>"  data-type="Subject" data-original-title="Delete" > <i class="delete-icon"></i></a>
		<?php } ?>

		</div>
	</div>
	<?php }
	} else {
	?>
	<div class="ssd-data-row no-data">
		<div class="competencies-data data-wrapper" style="width:100%;">
			<div class="no-res-found text-center">No Subjects</div>
		</div>
	</div>
<?php } ?>

