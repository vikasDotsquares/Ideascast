<?php

$id = $data[0]['subjects']['id'];
$title = $data[0]['subjects']['title'];
$fullname = $data[0][0]['fullname'];

if( isset($data[0]['subjects']['modified']) && !empty($data[0]['subjects']['modified']) && $data[0]['subjects']['modified'] != '0000-00-00 00:00:00' ){
	$modified = $this->Wiki->_displayDate(date('Y-m-d h:i:s A', strtotime($data[0]['subjects']['modified'])), $format = 'd M, Y');
} else {
	$modified = '';
}

//$modified = $this->Wiki->_displayDate(date('Y-m-d h:i:s A', strtotime($data[0]['subjects']['modified'])), $format = 'd M, Y h:iA');

$slinkstotal = ( isset($data[0][0]['linktotal']) && !empty($data[0][0]['linktotal']) ) ? $data[0][0]['linktotal'] : 0;
$sfilestotal = ( isset($data[0][0]['filetotal']) && !empty($data[0][0]['filetotal']) ) ? $data[0][0]['filetotal'] : 0;
$stotalpeople = ( isset($data[0][0]['totalpeople']) && !empty($data[0][0]['totalpeople']) ) ? $data[0][0]['totalpeople'] : 0;
$totalkeyword = ( isset($data[0][0]['totalkeyword']) && !empty($data[0][0]['totalkeyword']) ) ? $data[0][0]['totalkeyword'] : 0;

$totallocation = ( isset($data[0][0]['totallocation']) && !empty($data[0][0]['totallocation']) ) ? $data[0][0]['totallocation'] : 0;
$totalorganization = ( isset($data[0][0]['totalorganization']) && !empty($data[0][0]['totalorganization']) ) ? $data[0][0]['totalorganization'] : 0;
$totaldepartment = ( isset($data[0][0]['totaldepartment']) && !empty($data[0][0]['totaldepartment']) ) ? $data[0][0]['totaldepartment'] : 0;
$total_stories = ( isset($data[0][0]['total_stories']) && !empty($data[0][0]['total_stories']) ) ? $data[0][0]['total_stories'] : 0;

$item_title = htmlentities($title, ENT_QUOTES, "UTF-8");
$tip_title = htmlspecialchars($item_title);
?>
<div class="ssd-col ssd-col-1">


		<span class="competencies-list">
			<span class="competencies-list-bg competencies-list-bg-subject">
				<i class="subjects-icon tipText" title="Skill"></i>
				<span data-html="true" class="sks-title" title="<?php echo $tip_title;?>" data-remote="<?php echo SITEURL; ?>competencies/view_subjects/<?php echo $id; ?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" ><?php echo $item_title ;?></span>
			</span>
		</span>
	</div>
	<div class="ssd-col ssd-col-2">
	<?php if( $stotalpeople > 0  ) { ?>
		<span class="view_profile" data-remote="<?php echo SITEURL; ?>competencies/view_subjects/<?php echo $id; ?>/people" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><?php echo $stotalpeople;?></span>
	<?php } else {
		echo $stotalpeople;
		}
	?>
	</div>

	<div class="ssd-col ssd-col-3">
		<div class="community-list">
			<?php if( $totalorganization > 0  ) { ?>
				<span class="community-list-sec data-exists" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $id;?>/community" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject"><i class="community-icon organizationgreenicon"></i> <?php echo $totalorganization; ?></span>
			<?php }else{ ?>
				<span class="community-list-sec"><i class="community-icon organizationgreenicon"></i> <?php echo $totalorganization; ?></span>
			<?php } ?>

			<?php if( $totallocation > 0  ) { ?>
				<span class="community-list-sec data-exists" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $id;?>/community" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject"><i class="community-icon locationgreenicon"></i> <?php echo $totallocation; ?></span>
			<?php }else{ ?>
				<span class="community-list-sec"><i class="community-icon locationgreenicon"></i> <?php echo $totallocation; ?></span>
			<?php } ?>

			<?php if( $totaldepartment > 0  ) { ?>
				<span class="community-list-sec data-exists" data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $id;?>/community" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject"><i class="community-icon departmentsgreenicon"></i> <?php echo $totaldepartment; ?></span>
			<?php }else{ ?>
				<span class="community-list-sec"><i class="community-icon departmentsgreenicon"></i> <?php echo $totaldepartment; ?></span>
			<?php } ?>
		</div>
	</div>
	<div class="ssd-col ssd-col-5">
		<?php if( $total_stories > 0 ){ ?>
			<span class="view_profile" data-remote="<?php echo SITEURL; ?>competencies/view_subjects/<?php echo $id; ?>/stories" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View"><?php echo $total_stories;?></span>
		<?php } else {
			echo $total_stories;
		 } ?>
	</div>
	<div class="ssd-col ssd-col-6">
	<?php if( $slinkstotal > 0 ){ ?>
	<span class="view_profile" data-remote="<?php echo SITEURL; ?>competencies/view_subjects/<?php echo $id; ?>/links" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><?php echo $slinkstotal;?></span>
	<?php } else {
		echo $slinkstotal;
	 } ?>
	</div>
	<div class="ssd-col ssd-col-7">
		<?php if( $sfilestotal > 0 ){ ?>
		<span class="view_profile" data-remote="<?php echo SITEURL; ?>competencies/view_subjects/<?php echo $id; ?>/files" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><?php echo $sfilestotal;?></span>
		<?php } else {
			echo $sfilestotal;
		}
		?>
	</div>
	<div class="ssd-col ssd-col-8">
		<?php echo $totalkeyword; ?>
	</div>
	<div class="ssd-col ssd-col-9">
		<span class="text-ellipsis tipText user-profile" title="<?php echo $fullname; ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $data[0]['subjects']['modified_by'], 'admin' => false)); ?>"><?php echo $fullname; ?></span></div>
	<div class="ssd-col ssd-col-10">
		<?php echo ( isset($modified) && !empty($modified) )? $modified : ''; ?>
	</div>
	<div class="ssd-col ssd-col-11">
		<a class="view_skill tipText" data-remote="<?php echo SITEURL; ?>competencies/view_subjects/<?php echo $id; ?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-original-title="View" data-type="Subject" ><i class="view-icon"></i></a>
	<?php
		if( $user_is_admin ){
	?>
		<a class="tipText edit_subjects" data-remote="<?php echo SITEURL; ?>competencies/edit_subjects/<?php echo $id;?>" data-area="" data-target="#modal_create_skills" data-toggle="modal" data-original-title="Edit" data-id="<?php echo $id;?>"  data-type="Subject" > <i class="edit-icon"></i></a>

		<a class="delete_subject" data-id="<?php echo $id;?>" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo SITEURL;?>competencies/trash_subject/<?php echo $id;?>" data-original-title="Delete"  data-type="Subject" > <i class="delete-icon"></i></a>
	<?php } ?>
	</div>
<script type="text/javascript">
	$(function(){

		$('body').delegate('.edit_subjects', 'click', function(e){
			$.current_skills_row = $(this);
			$('.edit_subjects').not(this).removeClass('active');
			$(this).addClass('active');

		})
	})
</script>