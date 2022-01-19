<?php if(isset($data) && !empty($data)){
		// pr($data);
		foreach($data as $key => $val) {
			$workspace = $val['Workspace'];
			// $project_workspace = $val['ProjectWorkspace'];

			if( workspace_exists($workspace['id']) ) {

			$wps_signoff = '';
			$wps_tip = '';
			$wps_cursor = '';
			if( isset( $workspace['sign_off'] ) && !empty($workspace['sign_off']) && $workspace['sign_off'] == 1 ){
				$wps_signoff = 'disable';
				$wps_tip = "Workspace Is Signed Off";
				$wps_cursor =" cursor:default; ";
			}
?>

<?php $color_code = $workspace['color_code'];
if((!isset($workspace['color_code']) || empty($workspace['color_code'])) || $workspace['color_code'] == 'bg-dark-gray') {
		$color_code = 'bg-success';
}

?>
<div class="panel panel-<?php echo str_replace('bg-', '', $color_code) ; ?> panel-workspace" data-id="<?php echo $workspace['id']; ?>" data-pid="<?php echo workspace_pid($workspace['id']); ?>" data-type="workspace" data-panel="workspace">

	<div class="panel-heading">
		<h4 class="panel-title selectable list-title tipText" title="Click Opens Areas"><i class="fa fa-check" ></i> <?php echo htmlentities($workspace['title'], ENT_QUOTES); ?></h4>
		<span class="pull-right clickable panel-collapsed"><i class="fa fa-chevron-down"></i></span>
	</div>

	<div class="panel-body " >

		<div class="date-section" style="">
			<?php if(empty($workspace['start_date']) && empty($workspace['end_date'])){ ?>
			<div class="dates mbottom" style="">No Schedule</div>
			<?php }else{ ?>
			<div class="dates" style="">
				<span><b>Start:</b> <?php echo  _displayDate($workspace['start_date'], 'd M, Y') ;  ?></span>
			</div>
			<div class="dates mbottom" style="">
				<span><b>End:</b> <?php echo _displayDate($workspace['end_date'], 'd M, Y') ;  ?></span>
			</div>
			<?php } ?>
		</div>

		<div class="detail-section">
			<div class="sub-heading">Description:</div>
			<div class="text-detail description"><?php echo ( empty($workspace['description'])) ? 'None' : nl2br($workspace['description']); ?></div>
		</div>
	</div>

	<div class="panel-footer">

		<div class="btn-group">

			<?php
			if( !isset($workspace['studio_status']) || empty($workspace['studio_status']) ) { ?>
				<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "manage_elements", workspace_pid($workspace['id']), $workspace['id']), true); ?>" class="btn  btn-xs tipText open-workspace" title="Open Workspace" type="button">
					<i class="openblack"></i>
				</a>
			<?php } ?>

			<?php if( isset($wps_signoff) && !empty($wps_signoff) ) {?>


				<a data-original-title="<?php echo $wps_tip;?>" class="btn btn-xs  tipText <?php echo $wps_signoff;?>" title="" style="<?php echo $wps_cursor;?>"><i class="brushblack"></i></a>

				<a data-original-title="<?php echo $wps_tip;?>" class="btn btn-xs tipText <?php echo $wps_signoff;?>" style="<?php echo $wps_cursor;?>">
					<i class="edit-icon"></i>
				</a>

				<!-- // PASSWORD DELETE -->
				<a data-original-title="<?php echo $wps_tip;?>" class="btn btn-xs tipText  <?php echo $wps_signoff;?>"  id="" data-accept="1" type="button" style="<?php echo $wps_cursor;?>" >
					<i class="deleteblack"></i>
				</a>

				<?php if( isset($workspace['studio_status']) && empty($workspace['studio_status']) ) { ?>
				<a  class="btn btn-xs tipText <?php echo $wps_signoff;?>" title="<?php echo $wps_tip;?>" type="button" style="<?php echo $wps_cursor;?>" >
					<i class="share-icon"></i>
				</a>

				<a class="btn  btn-xs tipText <?php echo $wps_signoff;?>" title="<?php echo $wps_tip;?>" type="button" style="<?php echo $wps_cursor;?>" >
					<i class="mysharingblack18"></i>
				</a>
				<?php } ?>


			<?php } else {?>
			<a data-original-title="Color Options" href="#" class="btn  btn-xs color_bucket tipText " title=""><i class="brushblack"></i></a>
			<small style="display: none;" class="colors_box">
				<small style="width:100%;" class="colors color-group">
					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-red" data-upcolor="bg-red" data-original-title="Red"><i class="fa fa-square text-red"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-blue" data-upcolor="bg-blue" data-original-title="Blue"><i class="fa fa-square text-blue"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-maroon" data-upcolor="bg-maroon" data-original-title="Maroon"><i class="fa fa-square text-maroon"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-aqua" data-upcolor="bg-aqua" data-original-title="Aqua"><i class="fa fa-square text-aqua"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-yellow" data-upcolor="bg-yellow" data-original-title="Yellow"><i class="fa fa-square text-yellow"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-teal" data-upcolor="bg-teal" data-original-title="Teal"><i class="fa fa-square text-teal"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-purple" data-upcolor="bg-purple" data-original-title="Purple"><i class="fa fa-square text-purple"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-navy" data-upcolor="bg-navy" data-original-title="Navy"><i class="fa fa-square text-navy"></i></b>

					<b title="" class="squares squares-default squares-xs el_color_box" data-remote="<?php echo Router::Url(array("controller" => "workspaces", "action" => "update_color", $workspace['id']), true); ?>" data-color="panel-green" data-upcolor="bg-green" href="#" data-original-title="Green"><i class="fa fa-square text-green"></i></b>
				</small>
			</small>

			<a data-original-title="Update Workspace Details" class="btn  btn-xs tipText  <?php echo $wps_signoff;?>" id="wsp_add" data-toggle="modal" data-id="<?php echo workspace_pid($workspace['id']); ?>" data-target="#create_model" data-remote="<?php echo Router::Url(array("controller" => "studios", "action" => "create_workspace", workspace_pid($workspace['id']), $workspace['id']), true); ?>">
				<i class="edit-icon"></i>
			</a>

			<!-- <a data-original-title="Remove" class="btn btn-default btn-xs tipText delete_workspace"  id="" data-accept="1" type="button">
				<i class="fa  fa-trash"></i>
			</a> -->
			<!-- // PASSWORD DELETE -->
			<a data-original-title="Remove" class="btn btn-xs tipText delete-an-item"  id="" data-accept="1" type="button"  data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "studios", "action" => "trash_a_workspace", $workspace['id'], 'admin' => FALSE ), true ); ?>">
				<i class="deleteblack"></i>
			</a>


			<?php if( isset($workspace['studio_status']) && empty($workspace['studio_status']) ) { ?>
				<?php /* ?><a href="<?php echo Router::Url(array("controller" => "shares", "action" => "index", workspace_pid($workspace['id'])), true); ?>" class="btn btn-default btn-xs tipText" title="Sharing" type="button">
					<i class="fa fa-user-plus"></i>
				</a><?php */ ?>

				<a href="<?php echo Router::Url(array("controller" => "shares", "action" => "sharing_map", workspace_pid($workspace['id'])), true); ?>" class="btn  btn-xs tipText" title="Sharing Map" type="button">
					<i class="mysharingblack18"></i>
				</a>
			<?php } ?>

			<?php } ?>

		</div>
	</div>
</div>


<?php 	}
	}

 }
else if(isset($project_id) && !empty($project_id)){
	echo '';
}
else {
	echo '<div class="bg-blakish" style="border-top: medium none; text-align: center; font-size: 16px; padding:10px" width="100%">No Workspaces found
	</div>';
} ?>