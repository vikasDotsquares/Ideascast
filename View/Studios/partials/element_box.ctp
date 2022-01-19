<?php if(isset($data) && !empty($data)){

	foreach($data as $key => $val) {
		$element = $val['Element'];

		$self_status = $val[0]['task_status'];

		$ele_signoff = '';
		$ele_tip = '';
		$ele_cursor = '';
		if( isset( $element['sign_off'] ) && !empty($element['sign_off']) && $element['sign_off'] == 1 ){
			$ele_signoff = 'disable';
			$ele_tip = "Task Is Signed Off";
			$ele_cursor =" cursor:default; ";
		}

		if( isset( $wsp_sign_off ) && !empty($wsp_sign_off) ){
			$ele_signoff = 'disable';
			$ele_tip = "Task Is Signed Off";
			$ele_cursor =" cursor:default; ";
		}
		if($element['color_code'] == 'panel-gray') {
			$element['color_code'] = 'panel-color-gray';
		}
?>
<div class="panel <?php echo $element['color_code']; ?> panel-element" data-id="<?php echo $element['id']; ?>" data-aid="<?php echo $element['area_id']; ?>" data-type="element" data-panel="element">

	<div class="panel-heading">
		<h4 class="panel-title list-title"><?php echo htmlentities($element['title']); ?></h4>
		<span class="pull-right clickable panel-collapsed"><i class="fa fa-chevron-down"></i></span>
	</div>

	<div class="panel-body " >

		<div class="date-section el_<?php echo $self_status; ?>" style="margin: -5px -10px;">
			<?php if(empty($element['start_date']) && empty($element['end_date'])){ ?>
			<div class="dates mbottom" style="">No Schedule</div>
			<?php }else{ ?>
			<div class="dates" style="">
				<span><b>Start:</b> <?php echo ( isset($element['start_date']) && !empty($element['start_date'])) ? _displayDate($element['start_date'], 'd M, Y') : 'N/A';  ?></span>
			</div>
			<div class="dates mbottom" style="">
				<span><b>End:</b> <?php echo ( isset($element['end_date']) && !empty($element['end_date'])) ? _displayDate($element['end_date'], 'd M, Y') : 'N/A';  ?></span>
			</div>
			<?php } ?>
		</div>

		<div class="detail-section">
			<div class="sub-heading" style="margin-top: 5px;">Task Description:</div>
			<div class="text-detail description">
				<?php echo (isset($element['description']) && !empty($element['description'])) ? nl2br($element['description']) : 'None'; ?>
			</div>

			<div class="sub-heading"  >Task Outcome:</div>
			<div class="text-detail objective">
				<?php echo (isset($element['comments']) && !empty($element['comments'])) ? nl2br($element['comments']) : 'None'; ?>
			</div>
		</div>

	</div>

	<div class="panel-footer">

		<div class="btn-group">

			<?php if( isset($element['studio_status']) && empty($element['studio_status']) ) { ?>
				<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $element['id']), true); ?>" class="btn btn-xs tipText" title="Open Task" type="button">
					<i class="openblack"></i>
				</a>
			<?php } ?>

			<?php
			//$ele_tip  $ele_cursor
			if( isset($ele_signoff) && !empty($ele_signoff) ) { ?>

				<a data-original-title="<?php echo $ele_tip;?>" class="btn btn-xs  tipText <?php echo $ele_signoff;?>" title="" style="<?php echo $ele_cursor;?>"><i class="brushblack"></i></a>

				<a data-original-title="<?php echo $ele_tip;?>" class="btn btn-xs tipText <?php echo $ele_signoff;?>" style="<?php echo $ele_cursor;?>">
					<i class="edit-icon"></i>
				</a>

				<!-- // PASSWORD DELETE -->
				<a data-original-title="<?php echo $ele_tip;?>" class="btn btn-xs tipText  <?php echo $ele_signoff;?>"  id="" data-accept="1" type="button" style="<?php echo $ele_cursor;?>" >
					<i class="deleteblack"></i>
				</a>

				<?php if( isset($element['studio_status']) && empty($element['studio_status']) ) { ?>
					<a  class="btn btn-xs tipText <?php echo $ele_signoff;?>" title="<?php echo $ele_tip;?>" type="button" style="<?php echo $ele_cursor;?>" >
						<i class="share-icon"></i>
					</a>

					<a class="btn btn-xs tipText <?php echo $ele_signoff;?>" title="<?php echo $ele_tip;?>" type="button" style="<?php echo $ele_cursor;?>" >
						<i class="mysharingblack18"></i>
					</a>
				<?php } ?>

			<?php } else { ?>
			<a data-original-title="Color Options" href="#" class="btn btn-xs color_trigger tipText " data-id="<?php echo $element['id']; ?>" title=""><i class="brushblack"></i></a>

			<a data-original-title="Update Task Details" class="btn btn-xs tipText " id="element_add" data-toggle="modal" data-id="<?php echo $element['area_id']; ?>" data-target="#create_model" data-remote="<?php echo Router::Url(array("controller" => "studios", "action" => "create_element", $element['area_id'], $element['id']), true); ?>">
				<i class="edit-icon"></i>
			</a>

			<!-- // PASSWORD DELETE -->
			<a data-original-title="Remove" class="btn btn-xs tipText delete-an-item"  id="" data-accept="1" type="button" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "studios", "action" => "trash_a_task", $element['id'], 'admin' => FALSE ), true ); ?>">
				<i class="deleteblack"></i>
			</a>

			<?php if( isset($element['studio_status']) && empty($element['studio_status']) ) { ?>

				<a href="<?php echo Router::Url(array("controller" => "shares", "action" => "sharing_map", $prjid), true); ?>" class="btn btn-xs tipText" title="Sharing Map" type="button">
					<i class="mysharingblack18"></i>
				</a>
			<?php }
			}
			?>


		</div>
	</div>
</div>

	<?php } ?>
<?php }
else if(isset($project_id) && !empty($project_id)){
	echo '';
}
else {
	echo '<div class="bg-blakish" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" width="100%">No Tasks found
	</div>';
} ?>