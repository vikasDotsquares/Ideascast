<?php // USED WHILE UPDATING PROPAGATION ?>
<?php // Used in update_propagation.ctp as Element View ?>

<?php
$model_name = '';

$show_read = $show_edit = $show_delete = $show_copy = $show_move = true;
$show_add_element = false;
$wid = $aid = $id = 0;
$input = '';

$sharing_data = $propagate_data = $selected_propagate_data = null;
$data_modal = $data_propagate_modal = '';

if( isset($project_detail) && !empty($project_detail) ) {

	if( $type == 'project' ) {
		$data_modal = 'ProjectPermission';
		$data_propagate_modal = 'ProjectPropagate';

		$sharing_data = $this->ViewModel->project_sharing($project_id, $shareUser, $project_detail);

		$propagate_data = $this->ViewModel->project_propagation($project_id, $shareUser);
		if( isset($propagate_data) && !empty($propagate_data) ) {
			$selected_propagate_data = $propagate_data[$data_propagate_modal];
		}
	}
	else if( $type == 'workspace' ) {

		$data_modal = 'WorkspacePermission';
		$data_propagate_modal = 'WorkspacePropagate';

		$sharing_data = $this->ViewModel->workspace_sharing($workspace_id, $shareUser, $project_detail);

		$propagate_data = $this->ViewModel->workspace_propagation($workspace_id, $shareUser, $project_detail);
		if( isset($propagate_data) && !empty($propagate_data) ) {
			$selected_propagate_data = $propagate_data[$data_propagate_modal];
		}
	}
	else if( $type == 'element' ) {
		$data_modal = 'ElementPermission';
		$data_propagate_modal = 'ElementPropagate';

		$ids['project_id'] = $project_id;
		$ids['workspace_id'] = $workspace_id;
		$ids['area_id'] = $area_id;
		$ids['element_id'] = $element_id;

			$input = $workspace_id . '_' . $area_id . '_' . $element_id;
			$id = $element_id;

		$sharing_data = $this->ViewModel->element_sharing($ids, $shareUser, $project_detail);

		$propagate_data = $this->ViewModel->element_propagation($ids, $shareUser, $project_detail);
		if( isset($propagate_data) && !empty($propagate_data) ) {
			$selected_propagate_data = $propagate_data[$data_propagate_modal];
			// pr($propagate_data, 1);
		}

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
$dis_permit = 'applied_permissions';
$dis_prop = 'permissions';
$dis_prop_trigger = 'propogation';
if($disable){
	$dis_string = 'disabled';
	$dis_permit = 'dis_permits';
	$dis_prop = 'dis_props';
	$dis_prop_trigger = 'dis_props';
}



if(isset($model) && !empty($model) ) {
	$model_name = $model;
}

if( isset($sharing_data) && !empty($sharing_data)) {
	$sharing_data = ( isset($sharing_data[$data_modal]) && !empty($sharing_data[$data_modal]))
							? $sharing_data[$data_modal]
							: null;
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

					$selected_permit = (isset($existsPermitData[0])) ? $existsPermitData[0] : null;
				}
			}
		}
	}
	$show_propagate = 0;

	$show_propagate += (isset($selected_permit['permit_read']) && !empty($selected_permit['permit_read']) ) ? 1 : 0;
	$show_propagate += (isset($selected_permit['permit_add']) && !empty($selected_permit['permit_add']) ) ? 1 : 0;
	$show_propagate += (isset($selected_permit['permit_edit']) && !empty($selected_permit['permit_edit']) ) ? 1 : 0;
	$show_propagate += (isset($selected_permit['permit_delete']) && !empty($selected_permit['permit_delete']) ) ? 1 : 0;
	$show_propagate += (isset($selected_permit['permit_copy']) && !empty($selected_permit['permit_copy']) ) ? 1 : 0;
	$show_propagate += (isset($selected_permit['permit_move']) && !empty($selected_permit['permit_move']) ) ? 1 : 0;
 ?>

		<div class="sharing-icon">

			<?php if($show_read) : ?>
			<label class="<?php echo $dis_permit; ?> permit_read btn-circle btn-xs tipText <?php echo ((isset($selected_permit['permit_read']) && !empty($selected_permit['permit_read']) )? 'active' : ''); ?>" title="Read"> <i class="fa fa-eye"></i></label>
			<?php endif; ?>

			<?php if($show_edit) : ?>

			<label class="<?php echo $dis_permit; ?> permit_edit btn-circle btn-xs tipText <?php echo ((isset($selected_permit['permit_edit']) && !empty($selected_permit['permit_edit']) )? 'active' : ''); ?>" title="Update">
				<i class="fa fa-pencil"></i>
			</label>
			<?php endif; ?>

			<?php if($show_delete) : ?>

			<label class="<?php echo $dis_permit; ?> permit_delete btn-circle btn-xs tipText <?php echo ((isset($selected_permit['permit_delete']) && !empty($selected_permit['permit_delete']) )? 'active' : ''); ?>" title="Delete">
				<i class="fa fa-trash"></i>
			</label>
			<?php endif; ?>

			<?php if($show_copy) : ?>

			<label class="<?php echo $dis_permit; ?> permit_copy btn-circle btn-xs tipText <?php echo ((isset($selected_permit['permit_copy']) && !empty($selected_permit['permit_copy']) )? 'active' : ''); ?>" title="Copy">
				<i class="fa fa-copy"></i>
			</label>
			<?php endif; ?>

			<?php if($show_move) : ?>

			<label class="<?php echo $dis_permit; ?> permit_move btn-circle btn-xs tipText <?php echo ((isset($selected_permit['permit_move']) && !empty($selected_permit['permit_move']) )? 'active' : ''); ?>" title="Cut & Move">
				<i class="fa fa-cut"></i>
			</label>
			<?php endif; ?>

			<?php if($show_add_element) : ?>
			<label class="<?php echo $dis_permit; ?> permit_add btn-circle btn-xs tipText <?php echo ((isset($selected_permit['permit_add']) && !empty($selected_permit['permit_add']) )? 'active' : ''); ?>" title="Add New Task">
				<i class="fa fa-plus"></i>
			</label>
			<?php endif; ?>


			<?php if( isset($show_propagate) && !empty($show_propagate) ) { ?>
			<div class="propogation-wrapper <?php //echo $dis_string; ?>">
				<a data-remote="" data-trigger="click" data-html="true" rel="popover" data-placement="right" class="propogation <?php echo $dis_string; ?> perm_propogate show_propogate btn-circle btn-xs tipText" title="Propagate Permissions">
					<i class="fab fa-pagelines"></i>
				</a>

				<div class="propogat_permisions">

						<div class="options-arrow right"></div>

						<div class="options-inner">
							<?php if($show_read && isset($selected_permit['permit_read']) && !empty($selected_permit['permit_read']) ) : ?>

							<label class="<?php echo $dis_prop; ?> permit_read btn-circle btn-xs tipText <?php echo ((isset($selected_propagate_data['permit_read']) && !empty($selected_propagate_data['permit_read']) )? 'active' : ''); ?>" title="Read">

								<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_read]" value="<?php echo $input; ?>"/>
								<i class="fa fa-eye"></i>

							</label>
							<?php endif; ?>

							<?php if($show_edit && isset($selected_permit['permit_edit']) && !empty($selected_permit['permit_edit'])) : ?>
							<label class="<?php echo $dis_prop; ?> permit_edit btn-circle btn-xs tipText <?php echo ((isset($selected_propagate_data['permit_edit']) && !empty($selected_propagate_data['permit_edit']) )? 'active' : ''); ?>" title="Update">

								<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_edit]" value="<?php echo $input; ?>"/>
								<i class="fa fa-pencil"></i>
							</label>
							<?php endif; ?>

							<?php if($show_delete && isset($selected_permit['permit_delete']) && !empty($selected_permit['permit_delete'])) : ?>
							<label class="<?php echo $dis_prop; ?> permit_delete btn-circle btn-xs tipText <?php echo ((isset($selected_propagate_data['permit_delete']) && !empty($selected_propagate_data['permit_delete']) )? 'active' : ''); ?>" title="Delete">

								<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_delete]" value="<?php echo $input; ?>"/>
								<i class="fa fa-trash"></i>
							</label>
							<?php endif; ?>

							<?php if($show_copy && isset($selected_permit['permit_copy']) && !empty($selected_permit['permit_copy'])) : ?>
							<label class="<?php echo $dis_prop; ?> permit_copy btn-circle btn-xs tipText <?php echo ((isset($selected_propagate_data['permit_copy']) && !empty($selected_propagate_data['permit_copy']) )? 'active' : ''); ?>" title="Copy">

								<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_copy]" value="<?php echo $input; ?>"/>
								<i class="fa fa-copy"></i>
							</label>

							<?php endif; ?>

							<?php if($show_move && isset($selected_permit['permit_move']) && !empty($selected_permit['permit_move'])) : ?>

							<label class="<?php echo $dis_prop; ?> permit_move btn-circle btn-xs tipText <?php echo ((isset($selected_propagate_data['permit_move']) && !empty($selected_propagate_data['permit_move']) )? 'active' : ''); ?>" title="Cut">

								<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_move]" value="<?php echo $input; ?>"/>
								<i class="fa fa-cut"></i>
							</label>
							<?php endif; ?>

							<?php if($show_add_element && (isset($selected_permit['permit_add']) && !empty($selected_permit['permit_add']) ) ) : ?>

							<label class="<?php echo $dis_prop; ?> permit_add btn-circle btn-xs tipText <?php echo ((isset($selected_propagate_data['permit_add']) && !empty($selected_propagate_data['permit_add']) )? 'active' : ''); ?>" title="Add New Task">

								<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_add]" value="<?php echo $input; ?>"/>
								<i class="fa fa-plus"></i>
							</label>
							<?php endif; ?>

						</div>

				</div>

			</div><!--  -->
			<?php } ?>
		</div>