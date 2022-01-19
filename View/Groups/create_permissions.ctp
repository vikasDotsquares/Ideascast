<style>
.lbl {
	margin-bottom: 10px; padding: 0px; text-align: left; font-weight: 600;
}
.sharing-help .applied_permissions {
	margin-right: 3px !important;
}
</style>
<?php
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/group_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
?>

<script type="text/javascript">
$(function() {

	// $('body').delegate('#rst_share_tree', 'click', function(event){
		// event.preventDefault();
		// $(this).trigger( "options:toggle");
	// })

})
</script>

<?php echo $this->Session->flash(); ?>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
					<p class="text-muted date-time">
						<span><?php echo $page_heading; ?></span>
					</p>
				</h1>

			</section>
		</div>

	<div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">
                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
                        </div>

		<div class="box-body clearfix list-shares" style="min-height: 800px">



<?php if(( isset($project_id) && !empty($project_id)) && ( isset($group_id) && !empty($group_id))) {

	$group_detail = $this->Group->ProjectGroupDetail( $group_id );
	$project_detail = $this->ViewModel->getProjectDetail( $project_id, -1 );

?>
<?php echo $this->Form->create('ShareTree', array('url' => array('controller' => 'groups', 'action' => 'create_permissions', $project_id, $group_id ), 'class' => 'formAddSharing', 'id' => 'frm_share_tree', 'enctype' => 'multipart/form-data')); ?>
	<div class="panel panel-default" id="user_detail_section">
		<div class="panel-heading clearfix" id="">
			<span class="time pull-left">
				Group Detail
			</span>
		</div>

		<div class="table-responsive">
			<table class="table table-bordered">
				<tr>
					<th width="25%" class="text-left">Group</th>
					<th width="25%" class="text-left">Project</th>
					<th width="25%" class="text-center">Permission Level</th>
					<th width="25%" class="text-center">Propagation</th>
				</tr>
				<tr>
					<td class="text-left"><?php echo $group_detail['ProjectGroup']['title']; ?></td>
					<td class="text-left"><?php echo $project_detail['Project']['title']; ?></td>
					<td class="text-center">

						<div class="toggle-group propogation-on-off">
							<a href="" title="Full Owner Share Options" data-container="body" class="tipText btn btn-xs btn-toggle toggle-on"><i class="fa fa-arrow-right"></i></a>

							<input type="checkbox" id="project_level" name="data[Share][project_level]" value="1" checked="checked" >

							<a href="" title="Set Share Options" data-container="body" class="tipText btn btn-xs btn-toggle toggle-off"><i class="fa fa-arrow-down"></i></a>

						</div>
					</td>
					<td class="text-center">
						N/A
						<!-- <div data-toggle="toggle" class="toggle" style="">
							<input type="checkbox" name="data[Share][share_permission]"  value="1">
							<div class="toggle-handle propogation-handle off option-disabled">
								<label class="btn btn-success btn-xs tipText" title="Further sharing is allowed">On</label>
								<label class="btn btn-danger btn-xs tipText" title="Further sharing not allowed">Off</label>
							</div>
						</div> -->
					</td>
				</tr>
			</table>
		</div>
	</div>


<!--   stage 2 -->



	<div class="panel panel-default" id="share_section_submit">
		<div class="panel-heading clearfix" id="">
			<h5 class="">Sharer Options</h5>

			<span class="pull-right option-buttons">

					<!-- <label class="goto btn-goto tipText" title="Select All">
						<input type="checkbox" name="select_all_permissions" value="0" id="select_all_permissions"/>
						<i class="fa fa-check"></i>
					</label>
					<span class="label-text">Select All Permissions</span> -->

				<div class="btn-group action-group">
					<a class="btn btn-sm action_buttons btn-primary" data-action="collapse_all" href="#" >Collapse All</a>
					<a class="btn btn-sm action_buttons btn-primary" data-action="expand_all" href="#" >Expand All</a>
				</div>

					<a href="" class="btn btn-warning btn-sm hide" id="rst_share_tree">Toggle Tree</a>
					<a href="#" class="btn btn-success btn-sm" id="sbmt_sharing" >Save</a>

			</span>

		</div>
	</div>


<div class="panel panel-default" id="share_detail_section" style="display: none;">

	<div class="panel-body no-padding" id="">
		<div class="table-responsive">
			<table class="table">
				<tr>
					<th>
						<div class="shares-list">
							<ul>
								<li class="shares-tree-heading">
									<label>Path</label>
									<div>Permissions</div>
									<div class="pull-right">
										<!-- <div class="sharing-icon sharing-help ">
											<label class="applied_permissions permit_read btn-circle btn-xs tipText active" title="Read"> <i class="fa fa-eye"></i></label>
											<label class="applied_permissions permit_edit btn-circle btn-xs tipText active" title="Update"><i class="fa fa-pencil"></i></label>
											<label class="applied_permissions permit_delete btn-circle btn-xs tipText active" title="Delete"><i class="fa fa-trash"></i></label>
											<label class="applied_permissions permit_copy btn-circle btn-xs tipText active" title="Copy"><i class="fa fa-copy"></i></label>
											<label class="applied_permissions permit_move btn-circle btn-xs tipText active" title="Cut & Move"><i class="fa fa-cut"></i></label>
											<label class="applied_permissions permit_add btn-circle btn-xs tipText active" title="Add Element"><i class="fa fa-plus"></i></label>
										</div> -->
									</div>
								</li>
							</ul>
						</div>
					</th>
				</tr>

				<tr>
					<td style="padding-left: 0px;">
						<div id="sharing_list" class="sharing_list" >
						<?php

						if( isset($project_id) && !empty($project_id) ) {

							echo $this->Form->input('Share.project_id', ['type' => 'hidden', 'value' => $project_id]);

							echo $this->Form->input('Share.group_id', ['type' => 'hidden', 'value' => $group_id]);

	$prjWrks = $this->ViewModel->project_workspaces( $project_id );

	 ?>
	<ul class="nav nav-list tree" >
	<?php
	if( isset($prjWrks) && !empty($prjWrks) ) {

		$share_file =  'add_sharing';

		$display = '';
		$icon_class = 'tree_icons opened fa fa-minus';

		$prjData = $prjWrks[$project_id]['project'];
		$wrkData = $prjWrks[$project_id]['workspace'];

		echo  '<li class="has-sub-cat">';
			echo  '<a ' .
					' data-id="' . $prjData['id'] . '"' .
					' class="tipText tree-toggler nav-header tree_links">' .
						'<i class="'.$icon_class.'"></i> ' .
						'<span class="tipText tree_text" title="Project" style="">'.$prjData['title'].'</span>' .
				'</a>' ;

			// show project icons
			echo $this->element('../Groups/partials/'.$share_file, ['type' => 'project', 'model' => 'ProjectPermission']);

		// start ws list
		echo '<ul class="nav nav-list tree" '.$display.' >';

		foreach($wrkData as $key => $val) {
			// pr($val['id']);
			// create ws and el if area exists
			if( isset($val['area']) && !empty($val['area']) ) {

				$areas = array_keys($val['area']);

				echo  '<li class="has-sub-cat">';
					echo  '<a ' .
							' data-id="' . $val['id'] . '"' .
							' class="tree-toggler nav-header tree_links">'.
							'<i class="'.$icon_class.'"></i>' .
							'<span class="tipText tree_text" title="Workspace" style="">'.$val['title'].'</span>' .
						'</a>' ;

						echo $this->element('../Groups/partials/'.$share_file, ['type' => 'workspace', 'model' => 'WorkspacePermission', 'workspace_id' => $val['id']] );

						$wsEls = $this->ViewModel->area_elements($areas);

						if( isset($wsEls) && !empty($wsEls) ) {

							// if elements exists, print area
							echo '<ul class="nav nav-list tree" '.$display.' >';

							foreach($val['area'] as $akey => $aval) {

								// get each area elements
								$arEls = $this->ViewModel->area_elements($akey);

								if( isset($arEls) && !empty($arEls) ) {

									echo  '<li class="has-sub-cat">';
										echo  '<a ' .
												' data-id="' . $akey . '"' .
												' class="tree-toggler nav-header tree_links">'.
												'<i class="'.$icon_class.'"></i>' .
												'<span class="tipText tree_text" title="Area" style="">'.$aval.'</span>' .
											'</a>' ;

										// start el list
										echo '<ul class="nav nav-list tree" '.$display.' >';
									// pr($arEls, 1);
									foreach($arEls as $ekey => $evals) {
										$eval = $evals['Element'];
										// pr($eval);
										echo  '<li class="has-sub-cat">';
										echo  '<a ' .
												' data-id="' . $eval['id'] . '"' .
												' title=""' .
												' class="tree-toggler nav-header tree_links">'.
												'<span class="ico_element pull-left"></span>'.
												'<span class="tipText tree_text" title="Task" >'.$eval['title'].'</span>'.
											'</a>' ;

											echo $this->element('../Groups/partials/'.$share_file, ['type' => 'element', 'model' => 'ElementPermission', 'workspace_id' => $val['id'], 'area_id' => $akey, 'element_id' => $eval['id']]);

										echo  '</li>';// end el
									}
										echo '</ul>';// end el list
								}
								echo  '</li>';// end ar
							}

							echo '</ul>';// end ar list

						}

				echo  '</li>';// end ws
			}
		}
			echo '</ul>';
			// end ws list

		echo '</li>';// end pr
	}
}
?>
</ul>
											<!-- -->
												</div>

											</td>
										</tr>
									</table>
                                </div>
							</div>


                  </div>


					<?php echo $this->Form->end(); ?>
				<?php } ?>

                </div>
            </div>
        </div>
	</div>
</div>
