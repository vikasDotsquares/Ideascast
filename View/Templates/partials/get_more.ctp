<!-- LIST AND GRID VIEW START	-->

	<?php
	// foreach( $data['templates'] as $key => $val ) {
	foreach( $templates as $key => $val ) {

		$item = $val['Template'];

		?>
		<li class="col-lg-3 col-md-4 col-sm-6">
			<div class="box box-success">

				<div class="box-header"> <h3 class="box-title "><?php echo $item['title'] ?> </h3> </div>
				<div class="box-body clearfix<?php if( isset($item['wsp_imported']) && $item['wsp_imported'] == 1 ){?> wsp_template<?php } ?>">
					<a title="Select" href="<?php echo SITEURL.'templates/popups/workspace/'.$this->params['pass']['0'].'/'.$item['id']; ?>" class="btn btn-jeera btn-sm select-btn btn_select_workspace" id="btn_select_workspace" data-id="<?php echo $item['id']; ?>" data-toggle="modal" data-target="#modal_manage_templates"> <i class="fa fa-check"></i> Select </a>
						<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$item['layout_preview'], ['class' => 'thumb']); ?></div>


				</div>

			</div>

		</li>
	<?php } ?>










<?php  /*
// foreach( $data['templates'] as $key => $val ) {
foreach( $templates as $key => $val ) {

	$item = $val['Template'];
	?>
	<li class="col-lg-3 col-md-4 col-sm-6">
		<div class="box box-success" data-remote="<?php //echo Router::Url(array('controller' => 'templates', 'action' => 'popups', 'workspace', $project_id, $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>" data-toggle="modal" data-target="#popup_modal1">

			<div class="box-header"> <h3 class="box-title "><?php echo $item['title'] ?> </h3> </div>
			<div class="box-body clearfix">
				<a title="Select" href="<?php //echo Router::Url(array('controller' => 'templates', 'action' => 'popups', 'workspace', $project_id, $item['id'], 'admin' => FALSE ), TRUE); ?>" class="btn btn-jeera btn-sm select-btn btn_select_workspace" id="btn_select_workspace" data-id="<?php echo $item['id']; ?>" data-toggle="modal" data-target="#popup_modal"> <i class="fa fa-check"></i> Select </a>
					<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$item['layout_preview'], ['class' => 'thumb']); ?></div>


			</div>

		</div>

	</li>
<?php }  */?>