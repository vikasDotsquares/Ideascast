<?php // USED TO SHOW SHARING PERMISSION ICONS WHILE CREATE SHARING WITH OTHER USERS BY THE OWNER ?>
<?php // Used in index.ctp as Element View ?>

<?php

$model_name = '';

$show_read = $show_edit = $show_delete = $show_copy = $show_move = true;
$show_add_element = false;
$wid = $aid = $id = 0;
$input = '';

$sharing_data = null;
// pr($shareUser);
if( isset($project_detail) && !empty($project_detail) ) {

	if( $type == 'project' ) {
		$sharing_data = $this->ViewModel->project_sharing($project_id, $shareUser, $project_detail);
	}
	else if( $type == 'workspace' ) {
		$sharing_data = $this->ViewModel->workspace_sharing($workspace_id, $shareUser, $project_detail);

		// pr($sharing_data, 1);
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

$permit_string = 'permissions';
$prop_string = 'propogation';
$outer_string = '';
if($disable){
	$permit_string = 'dis_permit';
	$prop_string = 'dis_prop';
	$outer_string = 'dis_wrapper';
}

if(isset($model) && !empty($model) ) {
	$model_name = $model;
}
if( isset($sharing_data) && !empty($sharing_data)) {
	$sharing_data = ( isset($sharing_data[$model]) && !empty($sharing_data[$model]))
					? $sharing_data[$model]
					: null;
}
 ?>

		<div class="sharing-icon">

			<?php if($show_read) : ?>
			<label class="<?php if(!$disable){ ?> permit_read <?php } ?> btn-circle btn-xs tipText <?php echo $type; ?> <?php echo $permit_string; ?>" title="Read">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_read]" value="<?php echo $input; ?>" />
				<i class="fa fa-eye lbl-icn"></i>
			</label>
			<?php endif; ?>

			<?php if($show_edit) : ?>

			<label class=" <?php if(!$disable){ ?> permit_edit <?php } ?> btn-circle btn-xs tipText <?php echo $type; ?> <?php echo $permit_string; ?>" title="Update">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_edit]" value="<?php echo $input; ?>"/>
				<i class="fa fa-pencil"></i>
			</label>
			<?php endif; ?>

			<?php if($show_delete) : ?>

			<label class=" <?php if(!$disable){ ?> permit_delete <?php } ?> btn-circle btn-xs tipText <?php echo $type; ?> <?php echo $permit_string; ?>" title="Delete">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_delete]" value="<?php echo $input; ?>"/>
				<i class="fa fa-trash"></i>
			</label>
			<?php endif; ?>

			<?php if($show_copy) : ?>

			<label class=" <?php if(!$disable){ ?> permit_copy <?php } ?> btn-circle btn-xs tipText <?php echo $type; ?> <?php echo $permit_string; ?>" title="Copy">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_copy]" value="<?php echo $input; ?>"/>
				<i class="fa fa-copy"></i>
			</label>
			<?php endif; ?>

			<?php if($show_move) : ?>

			<label class=" <?php if(!$disable){ ?> permit_move <?php } ?> btn-circle btn-xs tipText <?php echo $type; ?> <?php echo $permit_string; ?>" title="Cut & Move">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_move]" value="<?php echo $input; ?>"/>
				<i class="fa fa-cut"></i>
			</label>
			<?php endif; ?>

			<?php if($show_add_element) : ?>
			<label class=" <?php if(!$disable){ ?> permit_add <?php } ?> btn-circle btn-xs tipText <?php echo $type; ?> <?php echo $permit_string; ?>" title="Add New Task">
				<input type="checkbox" name="data[<?php echo $model_name; ?>][<?php echo $id; ?>][permit_add]" value="<?php echo $input; ?>" />
				<i class="fa fa-plus"></i>
			</label>
			<?php endif; ?>

			<div class="propogation-wrapper">
				<a data-remote="" data-trigger="click" data-html="true" rel="popover" data-placement="right" class=" <?php echo $prop_string; ?> perm_propogate btn-circle btn-xs tipText not-editable1" title="Propagate Permissions">
					<i class="fab fa-pagelines"></i>
				</a>

				<div class="propogat_permisions <?php echo $outer_string; ?>">
					<div class="options-arrow right"></div>
					<div class="options-inner">
						<?php if($show_read) : ?>
							<label class=" <?php echo $prop_string; ?> <?php if(!$disable){ ?> permit_read <?php } ?> btn-circle btn-xs tipText" title="Read">
								<input type="checkbox" name="data[<?php echo $model_name.'_prop'; ?>][<?php echo $id; ?>][permit_read]" value="<?php echo $input; ?>"/>
								<i class="fa fa-eye"></i>
							</label>
						<?php endif; ?>
						<?php if($show_edit) : ?>
						<label class=" <?php echo $prop_string; ?> <?php if(!$disable){ ?> permit_edit <?php } ?> btn-circle btn-xs tipText" title="Update">
							<input type="checkbox" name="data[<?php echo $model_name.'_prop'; ?>][<?php echo $id; ?>][permit_edit]" value="<?php echo $input; ?>"/>
							<i class="fa fa-pencil"></i>
						</label>
						<?php endif; ?>
						<?php if($show_delete) : ?>
						<label class=" <?php echo $prop_string; ?> <?php if(!$disable){ ?> permit_delete <?php } ?> btn-circle btn-xs tipText" title="Delete">
							<input type="checkbox" name="data[<?php echo $model_name.'_prop'; ?>][<?php echo $id; ?>][permit_delete]" value="<?php echo $input; ?>"/>
							<i class="fa fa-trash"></i>
						</label>
						<?php endif; ?>
						<?php if($show_copy) : ?>
						<label class=" <?php echo $prop_string; ?> <?php if(!$disable){ ?> permit_copy <?php } ?> btn-circle btn-xs tipText" title="Copy">
							<input type="checkbox" name="data[<?php echo $model_name.'_prop'; ?>][<?php echo $id; ?>][permit_copy]" value="<?php echo $input; ?>"/>
							<i class="fa fa-copy"></i>
						</label>
						<?php endif; ?>
						<?php if($show_move) : ?>
						<label class=" <?php echo $prop_string; ?> <?php if(!$disable){ ?> permit_move <?php } ?> btn-circle btn-xs tipText" title="Cut & Move">
							<input type="checkbox" name="data[<?php echo $model_name.'_prop'; ?>][<?php echo $id; ?>][permit_move]" value="<?php echo $input; ?>"/>
							<i class="fa fa-cut"></i>
						</label>
						<?php endif; ?>
						<?php if($show_add_element) : ?>
						<label class=" <?php echo $prop_string; ?> <?php if(!$disable){ ?> permit_add <?php } ?> btn-circle btn-xs tipText" title="Add New Task">
							<input type="checkbox" name="data[<?php echo $model_name.'_prop'; ?>][<?php echo $id; ?>][permit_add]" value="<?php echo $input; ?>"/>
							<i class="fa fa-plus"></i>
						</label>
						<?php endif; ?>
					</div>
				</div>
			</div><!--  -->
		</div>