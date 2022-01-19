<?php

if(isset($data) && !empty($data)){
	foreach($data as $key => $val) {
		$area = $val['Area'];
		$workspace_data = $this->ViewModel->getWorkspaceDetail($area['workspace_id']);


		$wps_signoff = '';
		$wps_tip = '';
		$wps_cursor = '';
		if( isset( $workspace_data['Workspace']['sign_off'] ) && !empty($workspace_data['Workspace']['sign_off']) && $workspace_data['Workspace']['sign_off'] == 1 ){
			$wps_signoff = 'disable';
			$wps_tip = "Workspace Is Signed Off";
			$wps_cursor =" cursor:default; ";
		}
?>

<div class="panel panel-<?php echo str_replace('bg-', '', $workspace_data['Workspace']['color_code']) ; ?> panel-area" data-id="<?php echo $area['id']; ?>" data-wid="<?php echo $area['workspace_id']; ?>" data-panel="area">

	<div class="panel-heading">
		<h4 class="panel-title selectable list-title tipText" title="Click Opens Tasks"><i class="fa fa-check" ></i> <?php echo htmlentities($area['title'], ENT_QUOTES); ?></h4>
		<span class="pull-right clickable panel-collapsed"><i class="fa fa-chevron-down"></i></span>
	</div>

	<div class="panel-body " style="padding-top: 0px; padding-bottom: 0px;">

		<div class="detail-section">
			<div class="sub-heading">Purpose:</div>
			<div class="text-detail objective">
				<?php echo $area['tooltip_text']; ?>
			</div>
		</div>

	</div>

	<div class="panel-footer">
		<div class="btn-group">

			<?php if( isset($area['studio_status']) && empty($area['studio_status']) ) { ?>
				<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "manage_elements", workspace_pid($area['workspace_id']), $area['workspace_id']), true); ?>" class="btn  btn-xs tipText" title="Open Area" type="button">
					<i class="openblack"></i>
				</a>
			<?php } ?>


			<?php if( isset($wps_signoff) && !empty($wps_signoff) ){?>

			<a data-original-title="<?php echo $wps_tip;?>" class="btn btn-xs tipText <?php echo $wps_signoff;?>" id="zone_add" style="<?php echo $wps_cursor;?>" >
				<i class="edit-icon"></i>
			</a>
			<!-- // PASSWORD DELETE -->
			<a data-original-title="<?php echo $wps_tip;?>" class="btn btn-xs tipText    <?php echo $wps_signoff;?>"  id="" data-accept="1" type="button" style="<?php echo $wps_cursor;?>" >
				<i class="deleteblack"></i>
			</a>

			<?php } else { ?>
			<a data-original-title="Update Area Details" class="btn btn-xs tipText " id="zone_add" data-toggle="modal" data-id="<?php echo $area['workspace_id']; ?>" data-target="#create_model" data-remote="<?php echo Router::Url(array("controller" => "studios", "action" => "create_zone", $area['workspace_id'], $area['id']), true); ?>">
				<i class="edit-icon"></i>
			</a>

			<!-- <a data-original-title="Remove" class="btn btn-default btn-xs tipText delete_area"  id="" data-accept="1" type="button">
				<i class="fa  fa-trash"></i>
			</a> -->
			<!-- // PASSWORD DELETE -->

			<a data-original-title="Remove" class="btn  btn-xs tipText  delete-an-item <?php if(count($data) == 1){ ?>disabled<?php } ?>"  id="" data-accept="1" type="button" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "studios", "action" => "trash_an_area", $area['id'], 'admin' => FALSE ), true ); ?>">
				<i class="deleteblack"></i>
			</a>

			<?php } ?>

		</div>
	</div>
</div>

	<?php } ?>
<?php }
else if(isset($project_id) && !empty($project_id)){
	echo '';
}
else {
	echo '<div class="bg-blakish" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" width="100%">No Areas found
	</div>';
} ?>