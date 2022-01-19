<?php // USED TO SHOW SHARING PERMISSION ICONS WHILE UPDATING SHARING WITH OTHER USERS BY THE OWNER ?>
<?php // Used in index.ctp as Element View ?>

<?php
$model_name = '';

$show_read = $show_edit = $show_delete = $show_copy = $show_move = true;
$show_add_element = false;
$signoff_element = true;
$wid = $aid = $id = 0;
$input = '';

$sharing_data = null;

if( isset($project_detail) && !empty($project_detail) ) {

	if( $type == 'project' ) {
		$sharing_data = $this->ViewModel->project_sharing($project_id, $shareUser, $project_detail);
	}
	else if( $type == 'workspace' ) {
		$sharing_data = $this->ViewModel->workspace_sharing($workspace_id, $shareUser, $project_detail);
	}
	else if( $type == 'element' ) {
		$ids['project_id'] = $project_id;
		$ids['workspace_id'] = $workspace_id;
		$ids['area_id'] = $area_id;
		$ids['element_id'] = $element_id;

			$input = $workspace_id . '_' . $area_id . '_' . $element_id;
			$id = $element_id;

		$sharing_data = $this->ViewModel->element_sharing($ids, $shareUser, $project_detail);

	}
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

$disable = false;
if( $type == 'workspace' ) {
	if($ws_sign_off){
		$disable = true;
	}
}
else if( $type == 'element' ) {
	if($ws_sign_off){
		$disable = true;
	}
	else if($task_sign_off){
		$disable = true;
	}
}

$dis_string = '';
$permit_string = 'permissions';
if($disable){
	$dis_string = 'disabled';
	$permit_string = 'dis_permits';
}


if(isset($model) && !empty($model) ) {
	$model_name = $model;
}
if( isset($sharing_data) && !empty($sharing_data)) {
	$sharing_data = ( isset($sharing_data[$model]) && !empty($sharing_data[$model]))
							? $sharing_data[$model]
							: null;
	// pr($sharing_data );
}


$permit_exists = false;
	// GET ALL EXIST PERMISSIONS OF THE SELECTED USER
	$exist_permit = $selected_permit = null;
	if( isset($exist_permissions) && !empty($exist_permissions) ) {

		// GET PROJECT EXIST PERMISSIONS
		if( isset($exist_permissions['pp_data_count']) && !empty($exist_permissions['pp_data_count']) && $type == 'project') {

			$selected_permit = $exist_permissions['pp_data']['ProjectPermission'];
			// pr($selected_permit, 1);
		}

		// GET WORKSPACE EXIST PERMISSIONS
		if( isset($exist_permissions['wp_data_count']) && !empty($exist_permissions['wp_data_count']) && $type == 'workspace') {

			$exist_permit = $exist_permissions['wp_data'];

			$projectWorkspaceId = getValueByKey($exist_permit, 'project_workspace_id' );

			$wrkPWID =  $this->ViewModel->workspace_pwid($workspace_id);
			if( is_array($projectWorkspaceId) && in_array($wrkPWID, $projectWorkspaceId) ) {
				$permit_exists = true;
				$selected_permit = arraySearch($exist_permit, 'project_workspace_id', $wrkPWID);
				$selected_permit = (isset($selected_permit[0])) ? $selected_permit[0] : null;

			}
		}

		// GET ELEMENTS EXIST PERMISSIONS
		if( isset($exist_permissions['ep_data_count']) && !empty($exist_permissions['ep_data_count']) && $type == 'element') {

			$exist_permit = $exist_permissions['ep_data'];

			$EWId = getValueByKey($exist_permit, 'workspace_id' );

			if( is_array($EWId) && in_array($workspace_id, $EWId) ) {

				$EEId = getValueByKey($exist_permit, 'element_id' );

				if( is_array($EEId) && in_array($element_id, $EEId) ) {

					$ee_permit_exists = true;

					$existsPermitData = arraySearch($exist_permit, 'element_id', $element_id);
					// pr($existsPermitData );
					$selected_permit = (isset($existsPermitData[0])) ? $existsPermitData[0] : null;
				}
			}
		}
	}

 ?>

		<div class="sharing-icon">

			<?php if($show_read) : ?>
			<label class="<?php echo $permit_string; ?> permit_read btn-circle btn-xs tipText  <?php echo $type; ?> <?php echo ((isset($selected_permit['permit_read']) && !empty($selected_permit['permit_read']) )? 'active' : ''); ?> <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'unchangable' : ''); ?> " title="Read">
				<input type="checkbox" <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'disabled' : ''); ?> name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_read]" value="<?php echo $input; ?>" />
				<i class="fa fa-eye"></i>
			</label>
			<?php endif; ?>

			<?php if($show_edit) : ?>

			<label class="<?php echo $permit_string; ?> permit_edit btn-circle btn-xs tipText <?php echo $type; ?> <?php echo ((isset($selected_permit['permit_edit']) && !empty($selected_permit['permit_edit']) )? 'active' : ''); ?> <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'unchangable' : ''); ?> " title="Update">
				<input type="checkbox"  <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'disabled' : ''); ?> name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_edit]" value="<?php echo $input; ?>"/>
				<i class="fa fa-pencil"></i>
			</label>
			<?php endif; ?>

			<?php if($show_delete) : ?>

			<label class="<?php echo $permit_string; ?> permit_delete btn-circle btn-xs tipText <?php echo $type; ?> <?php echo ((isset($selected_permit['permit_delete']) && !empty($selected_permit['permit_delete']) )? 'active' : ''); ?> <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'unchangable' : ''); ?> " title="Delete">
				<input type="checkbox"  <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'disabled' : ''); ?> name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_delete]" value="<?php echo $input; ?>"/>
				<i class="fa fa-trash"></i>
			</label>
			<?php endif; ?>

			<?php if($show_copy) : ?>

			<label class="<?php echo $permit_string; ?> permit_copy btn-circle btn-xs tipText <?php echo $type; ?> <?php echo ((isset($selected_permit['permit_copy']) && !empty($selected_permit['permit_copy']) )? 'active' : ''); ?> <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'unchangable' : ''); ?> " title="Copy">
				<input type="checkbox"  <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'disabled' : ''); ?> name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_copy]" value="<?php echo $input; ?>"/>
				<i class="fa fa-copy"></i>
			</label>
			<?php endif; ?>

			<?php if($show_move) : ?>

			<label class="<?php echo $permit_string; ?> permit_move btn-circle btn-xs tipText <?php echo $type; ?> <?php echo ((isset($selected_permit['permit_move']) && !empty($selected_permit['permit_move']) )? 'active' : ''); ?>  <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'unchangable' : ''); ?> " title="Cut & Move">
				<input type="checkbox"  <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'disabled' : ''); ?> name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_move]" value="<?php echo $input; ?>"/>
				<i class="fa fa-cut"></i>
			</label>
			<?php endif; ?>

			<?php if($show_add_element) : ?>
			<label class="<?php echo $permit_string; ?> permit_add btn-circle btn-xs tipText <?php echo $type; ?> <?php echo ((isset($selected_permit['permit_add']) && !empty($selected_permit['permit_add']) )? 'active' : ''); ?> " title="Add New Task">
				<input type="checkbox"  <?php echo ((isset($selected_permit['is_editable']) && !empty($selected_permit['is_editable']) ) ? 'disabled' : ''); ?> name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_add]" value="<?php echo $input; ?>" />
				<i class="fa fa-plus"></i>
			</label>
			<?php endif; ?>
		</div>