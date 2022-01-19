<?php if(isset($data) && !empty($data)){

		$project = $data['Project'];

		$prj_signoff = '';
		if( isset( $project['sign_off'] ) && !empty($project['sign_off']) && $project['sign_off'] == 1 ){
			$prj_signoff = 'disabled';
		}

?>
<?php   $color_code = $project['color_code'];
if(!isset($project['color_code']) || empty($project['color_code'])) {
		$color_code = 'panel-success';
} ?>
<div class="panel <?php echo $color_code; ?>" data-id="<?php echo $project['id']; ?>" data-type="project">

	<div class="panel-heading">
		<h4 class="panel-title selectable list-title"><i class="fa fa-check" ></i> <?php echo htmlentities($project['title'],ENT_QUOTES); ?></h4>
		<!-- <span class="pull-right clickable panel-collapsed"><i class="fa fa-chevron-down"></i></span> -->
	</div>

	<div class="panel-body " style="">

		<div class="date-section" style="">
			<div class="dates" style="">
				<span><b>Start:</b> <?php echo ( isset($project['start_date']) && !empty($project['start_date'])) ? _displayDate($project['start_date'], 'd M, Y') : 'N/A';  ?></span>
			</div>
			<div class="dates mbottom" style="">
				<span><b>End:</b> <?php echo ( isset($project['end_date']) && !empty($project['end_date'])) ? _displayDate($project['end_date'], 'd M, Y') : 'N/A';  ?></span>
			</div>
		</div>

		<div class="detail-section"  style="">
			<div class="objective-wrapper">
				<div class="sub-heading">Outcome:</div>
				<div class="text-detail objective">
					<?php echo nl2br($project['objective']); ?>
				</div>
			</div>
			<div class="description-wrapper">
				<div class="sub-heading">Description:</div>
				<div class="text-detail description">
					<?php echo nl2br($project['description']); ?>
				</div>
			</div>

		</div>
	</div>

	<div class="panel-footer">

		<div class="btn-group">
		<?php if( isset($project['studio_status']) && empty($project['studio_status']) ) { ?>
			<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "index", $project['id']), true); ?>" class="btn  btn-xs tipText" title="Open Project" type="button">
				<i class="openblack"></i>
			</a>
		<?php } ?>

			<a data-original-title="Color Options" href="#" class="btn btn-xs color_bucket tipText <?php echo $prj_signoff;?>" title=""><i class="brushblack"></i></a>
			<small style="display: none;" class="colors_box">
				<small style="width:100%;" class="colors color-group">
					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-red" data-original-title="Red"><i class="fa fa-square text-red"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-blue" data-original-title="Blue"><i class="fa fa-square text-blue"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-maroon" data-original-title="Maroon"><i class="fa fa-square text-maroon"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-aqua" data-original-title="Aqua"><i class="fa fa-square text-aqua"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-yellow" data-original-title="Yellow"><i class="fa fa-square text-yellow"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-teal" data-original-title="Teal"><i class="fa fa-square text-teal"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-purple" data-original-title="Purple"><i class="fa fa-square text-purple"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-navy" data-original-title="Navy"><i class="fa fa-square text-navy"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "projects", "action" => "update_color", $project['id']), true); ?>" data-color="panel-green" href="#" data-original-title="Green"><i class="fa fa-square text-green"></i></b>
				</small>
			</small>

			<a data-original-title="Update Project Details" class="btn btn-xs tipText edit_project <?php echo $prj_signoff;?>" id="" data-toggle="modal" data-id="<?php echo $project['id']; ?>" data-target="#create_model" data-remote="<?php echo Router::Url(array("controller" => "studios", "action" => "create_project_modal", $project['id']), true); ?>">
				<i class="edit-icon"></i>
			</a>

			<!-- <a data-original-title="Remove" class="btn btn-default btn-xs tipText delete_project"  id="" data-accept="1" type="button">
				<i class="fa  fa-trash"></i>
			</a> // PASSWORD DELETE-->
			<?php if($is_owner){ ?>
				<a data-original-title="Remove" class="btn btn-xs tipText delete-an-item" data-accept="1" type="button"  data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "studios", "action" => "trash_a_project", $project['id'], 'admin' => FALSE ), true ); ?>">
					<i class="deleteblack"></i>
				</a>
			<?php } ?>

			<?php if( isset($project['studio_status']) && empty($project['studio_status']) ) { ?>
				<?php /* ?><a href="<?php echo Router::Url(array("controller" => "shares", "action" => "index", $project['id']), true); ?>" class="btn btn-default btn-xs tipText" title="Sharing" type="button">
					<i class="fa fa-user-plus"></i>
				</a><?php */ ?>

				<a href="<?php echo Router::Url(array("controller" => "shares", "action" => "sharing_map", $project['id']), true); ?>" class="btn btn-xs tipText" title="Sharing Map" type="button">
					<i class="mysharingblack18"></i>
				</a>
			<?php } ?>

		</div>
	</div>
</div>
<?php }
else if(isset($project_id) && !empty($project_id)){
	echo '<div class="loader"></div>';
}
else {
	echo '<div class="bg-blakish" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" width="100%">Select Project
	</div>';
} ?>