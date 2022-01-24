<?php // USED TO SHOW SHARING PERMISSION ICONS WHILE CREATE SHARING WITH OTHER USERS BY THE OWNER ?>
<?php // Used in index.ctp as Element View ?>
<style>
pre {width: 610%;}
</style>
<?php

$model_name = '';
$permissionsData = null;
$read = $edit = $delete = $copy = $move =  $add = false;


if(isset($model) && !empty($model) ) {
	$model_name = $model;
}


$show_read = $show_edit = $show_delete = $show_copy = $show_move = true;
$show_add_element = false;
$wid = $aid = $id = 0;
$input = '';

	if( $type == 'project' ) {
		$permissionsData = $project_permit;
	}
	else if( $type == 'workspace' ) {
		$permissionsData = $workspace_permit;
	}
	else if( $type == 'element' ) {

		$permissionsData = $element_permit;


		$ids['project_id'] = $project_id;
		$ids['workspace_id'] = $workspace_id;
		$ids['area_id'] = $area_id;
		$ids['element_id'] = $element_id;

			$input = $workspace_id . '_' . $area_id . '_' . $element_id;
			$id = $element_id;
	}

	$read = ( Set::classicExtract($permissionsData , $model_name.'.permit_read') ) ? true : false;
	$edit = ( Set::classicExtract($permissionsData, $model_name.'.permit_edit') ) ? true : false;
	$move = ( Set::classicExtract($permissionsData, $model_name.'.permit_move') ) ? true : false;
	$copy = ( Set::classicExtract($permissionsData, $model_name.'.permit_copy') ) ? true : false;
	$delete = ( Set::classicExtract($permissionsData, $model_name.'.permit_delete') ) ? true : false;
	$add = ( Set::classicExtract($permissionsData, $model_name.'.permit_add') ) ? true : false;

	if($model_name == 'WorkspacePermission' && isset($permissionsData[0])){
	$read = ( Set::classicExtract($permissionsData[0] , $model_name.'.permit_read') ) ? true : false;
	$edit = ( Set::classicExtract($permissionsData[0], $model_name.'.permit_edit') ) ? true : false;
	$move = ( Set::classicExtract($permissionsData[0], $model_name.'.permit_move') ) ? true : false;
	$copy = ( Set::classicExtract($permissionsData[0], $model_name.'.permit_copy') ) ? true : false;
	$delete = ( Set::classicExtract($permissionsData[0], $model_name.'.permit_delete') ) ? true : false;
	$add = ( Set::classicExtract($permissionsData[0], $model_name.'.permit_add') ) ? true : false;
	}

if( isset($type) && !empty($type) ) {

	if( $type == 'project' ) {
		$show_copy = false;
		$show_move = false;
		$id = $project_id;
		$input = $project_id;
	}
	else if( $type == 'workspace' ) {
		$show_add_element = true;
		$show_copy = false;
		$show_move = false;
		$id = $workspace_id;
		$input = $workspace_id;
	}
}


 ?>

		<div class="sharing-icon">

			<?php if($show_read) : ?>
			<label class="permissions permit_read btn-circle btn-xs tipText <?php if( $type == 'project' ) { ?> project_icon <?php } ?> <?php if( $read ) { ?>active<?php } ?>" title="Read">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_read]" value="<?php echo $input; ?>" id=""/>
				<i class="fa fa-eye lbl-icn"></i>
			</label>
			<?php endif; ?>

			<?php if($show_edit) : ?>

			<label class="permissions permit_edit btn-circle btn-xs tipText <?php if( $edit ) { ?>active<?php } ?>" title="Update">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_edit]" value="<?php echo $input; ?>"/>
				<i class="fa fa-pencil"></i>
			</label>
			<?php endif; ?>

			<?php if($show_delete) : ?>

			<label class="permissions permit_delete btn-circle btn-xs tipText <?php if( $delete ) { ?>active<?php } ?>" title="Delete">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_delete]" value="<?php echo $input; ?>"/>
				<i class="fa fa-trash"></i>
			</label>
			<?php endif; ?>

			<?php if($show_copy) : ?>

			<label class="permissions permit_copy btn-circle btn-xs tipText <?php if( $copy ) { ?>active<?php } ?>" title="Copy">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_copy]" value="<?php echo $input; ?>"/>
				<i class="fa fa-copy"></i>
			</label>
			<?php endif; ?>

			<?php if($show_move) : ?>

			<label class="permissions permit_move btn-circle btn-xs tipText <?php if( $move ) { ?>active<?php } ?>" title="Cut & Move">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_move]" value="<?php echo $input; ?>"/>
				<i class="fa fa-cut"></i>
			</label>
			<?php endif; ?>

			<?php if($show_add_element) : ?>
			<label class="permissions permit_add btn-circle btn-xs tipText <?php if( $add ) { ?>active<?php } ?>" title="Add New Task">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_add]" value="<?php echo $input; ?>" id=""/>
				<i class="fa fa-plus"></i>
			</label>
			<?php endif; ?>
		</div>