<?php // USED TO SHOW POPUP MODAL BOX TO SHOW ALL THE PERMISSIONS THAT ARE GIVEN TO THE CURRENT USER. ?>
<?php // Used in Projects/share_projects.ctp -> popover box as Element View ?>

<?php echo $this->Html->css('projects/manage_categories'); ?>

<style>

	.modal-header {display: block; height: 70px;}
	.sharing-icon .permissions.active {
	    margin-left: 3px !important;
	}
	#sharing_list .sharing-icon {
		padding-right: 5px;
		width: auto !important;

	}
	.ico_element {
	    background: rgba(204, 204, 204, 0.7) url("../../images/icons/icon_e.png") no-repeat scroll 3px 3px / 60% auto !important;
	    cursor: default;
	    display: inline-block;
	    height: 17px;
	    margin: 5px 5px 0 0;
	    vertical-align: middle;
	    width: 17px;
	}
	.sharing_list .nav.nav-list.tree a i.tree_icons {
	    border-radius: 15px;
	    color: #fff;
	    font-size: 10px;
	    height: 15px;
	    line-height: 1.42;
	    margin-right: 10px;
	    padding: 0 !important;
	    text-align: center;
	    width: 15px;
	}
	.propogat_permisions {
	    background-color: rgba(0, 0, 0, 0.2);
	}
	.options-arrow.right {
		border-right: 8px solid rgba(0, 0, 0, 0.2);
	}
	.options-inner .permissions.active {
	    font-size: 12px;
	    height: 25px;
	    margin: 0 !important;
	    padding: 2px !important;
	    width: 25px;
	}

	.modal-header .sharing-icon {
	    border: 1px solid rgba(0, 0, 0, 0);
	    display: inline;
	    float: none;
	    vertical-align: top;
	    width: 380px;
	}

	h3 .permissions.active {
	    font-size: 12px;
	    height: 22px;
	    margin: 0 !important;
	    padding: px !important;
	    width: 22px;
		color: #222;
	}
	.prop {
	    display: inline-block;
	    height: 30px;
	    padding: 0;
	    width: auto;
	}
	.text-small {
	    font-size: 12px;
		margin: 0;
	    font-weight: 600;
	}
	.text-mid {
	    font-size: 13px;
	}
	/*.nav.nav-list.tree {
	    padding-left: 0px !important;
	}*/
	.nav.nav-list.tree li .sharing-icon label,.nav.nav-list.tree li .sharing-icon a {
	    cursor:default;
	}
</style>
<?php

	$project_title = "";
	$share_permission = false;

	$prDetail = $this->ViewModel->getProjectDetail( $project_id );
	$project_title = strip_tags($prDetail['Project']['title']);
	//echo $user_id;
	$pdata = $this->ViewModel->all_permissions( $user_id, $project_id );

	// pr($pdata['pp_data']['ProjectPermission']);
 ?>

<div class="modal-header">
	
		<h3 class="modal-title popup-title-wrap" id="myModalLabel"><span class="popup-title-ellipsis"> <?php echo ucfirst($project_title); ?></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>	</h3>
</div>
<div class="modal-body">

	<div id="" class="table-responsive">

		<table class="table border-bottom">
			<tr>
				<td  width="25%" class="text-left">
					<p class="text-small">Creator: </p>
					<span class="text-mid">
					<?php
					$owner_data = user_name( $pdata['pp_data']['ProjectPermission']['owner_id'] );

					echo $owner_data['first_name'] . ' ' . $owner_data['last_name']; ?></span>
				</td>
				<td width="25%" class="text-left">
					<p class="text-small">Shared by: </p>
					<span class="text-mid"><?php
					$user_data = user_name( $pdata['pp_data']['ProjectPermission']['share_by_id'] );

					echo $user_data['first_name'] . ' ' . $user_data['last_name']; ?></span>
				</td>
				<td width="25%" class="text-left">
					<p class="text-small">Shared with: </p>
					<span class="text-mid"><?php
					$user_data = user_name( $pdata['pp_data']['ProjectPermission']['user_id'] );

					echo $user_data['first_name'] . ' ' . $user_data['last_name']; ?></span>
				</td>
				<td width="25%" class="text-left">
					<p class="text-small">Sharing Update: </p>
					<span class="text-mid">
						<?php //echo _displayDate($pdata['pp_data']['ProjectPermission']['modified']); ?>
						<?php  echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($pdata['pp_data']['ProjectPermission']['modified'])),$format = 'd M Y'); ?>
					</span>
				</td>
			</tr>
		</table>
	</div>


	<div id="" class="table-responsive">

		<table class="table border-bottom">
			<tr>
				<td  width="25%" class="text-left">
	<div id="sharing_list" class="sharing_list" style="max-height: 500px; overflow-y: scroll;">
		<ul class="nav nav-list tree" style="padding-left: 0px;">

<?php


if( isset($pdata) && !empty($pdata) ) {
	$icon_class = 'tree_icons opened fa fa-minus';
	$ppdata = (isset($pdata['pp_data']) && !empty($pdata['pp_data'])) ? $pdata['pp_data'] : null;
	$wpdata = (isset($pdata['wp_data']) && !empty($pdata['wp_data'])) ? $pdata['wp_data'] : null;
	$epdata = (isset($pdata['ep_data']) && !empty($pdata['ep_data'])) ? $pdata['ep_data'] : null;
	// pr($ppdata, 1);
	$prPermit = null;
	if( isset($ppdata['ProjectPermission']) && !empty($ppdata['ProjectPermission'])) {
		$prPermit = $ppdata['ProjectPermission'];
	}

	if( isset($ppdata['ProjectPermission']['share_permission']) && !empty($ppdata['ProjectPermission']['share_permission'])) {
		$share_permission = true;
	}

	if( isset($ppdata['ProjectPermission']['project_level']) && !empty($ppdata['ProjectPermission']['project_level'])) {
		$share_permission = false;
	}


	?>

	<li class="has-sub-cat">
		<a  class="tipText tree-toggler nav-header tree_links tipText" title="Project"> <i class="<?php echo $icon_class; ?>"></i>
			<span class="tree_text" title="" style=""><?php echo strip_tags($project_title); ?></span>
		</a>
	<div class="sharing-icon">
		<?php
		if( isset($prPermit) && !empty($prPermit) ) {

			if($prPermit['permit_read']){
				echo '<label class="permissions permit_read btn-circle btn-xs active tipText project" title="Read">'.
				'<i class="fa fa-eye"></i>'.
				'</label>';
			}
			if($prPermit['permit_edit']){
				echo '<label class="permissions permit_edit btn-circle btn-xs active tipText project" title="Update">'.
				'<i class="fa fa-pencil"></i>'.
				'</label>';
			}
			if($prPermit['permit_delete']){
				echo '<label class="permissions permit_delete btn-circle btn-xs active tipText project" title="Delete">'.
				'<i class="fa fa-trash"></i>'.
				'</label>';
			}
			if($prPermit['permit_add']){
				echo '<label class="permissions permit_add btn-circle btn-xs active tipText project" title="Add New Task">'.
				'<i class="fa fa-plus"></i>'.
				'</label>';
			}

			if( $share_permission ) {
				$prPropagate = $this->ViewModel->project_propagation( $project_id, $user_id );
				if( isset($prPropagate) && !empty($prPropagate) ) {
					$prProp = $prPropagate['ProjectPropagate'];
?>
				<div class="propogation-wrapper">
					<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">
					<i class="fab fa-pagelines"></i>
					</a>

				<div class="propogat_permisions" style="display: block;">

					<div class="options-arrow right"></div>
					<div class="options-inner">
						<?php if($prProp['permit_read']){ ?>
							<label title="Read" class="permissions permit_read btn-circle btn-xs tipText active"> <i class="fa fa-eye"></i> </label>
						<?php } ?>

						<?php if($prProp['permit_edit']){ ?>
							<label title="" class="permissions permit_edit btn-circle btn-xs tipText active" data-original-title="Update"><i class="fa fa-pencil"></i></label>
						<?php } ?>

						<?php if($prProp['permit_delete']){ ?>
							<label title="" class="permissions permit_delete btn-circle btn-xs tipText active" data-original-title="Delete"><i class="fa fa-trash"></i></label>
						<?php } ?>

						<?php if($prProp['permit_add']){ ?>
							<label title="" class="permissions permit_add btn-circle btn-xs tipText active" data-original-title="Add New Task"><i class="fa fa-plus"></i></label>
						<?php } ?>
					</div>

				</div>

				</div>
				<?php
				}
			}

			 
		}
	?>
	</div>

	<?php

	if(  empty($ppdata['ProjectPermission']['project_level'])) {
	if( !empty($wpdata)) {
		echo '<ul class="nav nav-list tree" style="padding-left: 0px;">';

		foreach($wpdata as $k => $v) {
			$wsPermit = $v['WorkspacePermission'];
			$wsid = pwid_workspace($wsPermit['project_workspace_id'], $project_id);
			$wsDetail = $this->ViewModel->getWorkspaceDetail( $wsid );
			if(isset($wsDetail) && !empty($wsDetail)){
			echo  '<li class="has-sub-cat">';
				echo  '<a ' .
					' data-id="' . $wsDetail['Workspace']['id'] . '"' .
					' class="tipText tree-toggler nav-header tree_links tipText" title="Workspace">' .
					'<i class="'.$icon_class.'"></i> ' .
					'<span class="tree_text" title="" style="">'.strip_tags($wsDetail['Workspace']['title']).'</span>' .
					'</a>' ;
				echo '<div class="sharing-icon"> ';
					if($wsPermit['permit_read']){
						echo '<label class="permissions permit_read btn-circle btn-xs active tipText" title="Read">'.
						'<i class="fa fa-eye"></i>'.
						'</label>';
					}
					if($wsPermit['permit_edit']){
						echo '<label class="permissions permit_edit btn-circle btn-xs active tipText" title="Update">'.
						'<i class="fa fa-pencil"></i>'.
						'</label>';
					}
					if($wsPermit['permit_delete']){
						echo '<label class="permissions permit_delete btn-circle btn-xs active tipText" title="Delete">'.
						'<i class="fa fa-trash"></i>'.
						'</label>';
					}
					if($wsPermit['permit_add']){
						echo '<label class="permissions permit_add btn-circle btn-xs active tipText" title="Add New Task">'.
						'<i class="fa fa-plus"></i>'.
						'</label>';
					}


					if( $share_permission ){
						$wsPropagate = $this->ViewModel->workspace_propagation( $wsid, $user_id, null, $project_id );
						if( isset($wsPropagate) && !empty($wsPropagate) ) {
							$wsProp = $wsPropagate['WorkspacePropagate'];
	?>
						<div class="propogation-wrapper">
							<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">
								<i class="fab fa-pagelines"></i>
							</a>

							<div class="propogat_permisions" style="display: block;">

								<div class="options-arrow right"></div>

								<div class="options-inner">
									<?php if($wsProp['permit_read']){ ?>
										<label title="Read" class="permissions permit_read btn-circle btn-xs tipText active"> <i class="fa fa-eye"></i> </label>
									<?php } ?>

									<?php if($wsProp['permit_edit']){ ?>
										<label title="" class="permissions permit_edit btn-circle btn-xs tipText active" data-original-title="Update"><i class="fa fa-pencil"></i></label>
									<?php } ?>

									<?php if($wsProp['permit_delete']){ ?>
										<label title="" class="permissions permit_delete btn-circle btn-xs tipText active" data-original-title="Delete"><i class="fa fa-trash"></i></label>
									<?php } ?>

									<?php if($wsProp['permit_add']){ ?>
										<label title="" class="permissions permit_add btn-circle btn-xs tipText active" data-original-title="Add New Task"><i class="fa fa-plus"></i></label>
									<?php } ?>
								</div>

							</div>

						</div>
	<?php
						}
					}

				echo '</div> ';

			$edata = arraySearch($epdata, 'workspace_id', $wsDetail['Workspace']['id']);

			if( !empty($edata) ) {
				echo '<ul class="nav nav-list tree" style="padding-left: 0px;">';
				foreach($edata as $k => $v) {
					$elPermit = $v;
					$elDetail = $this->ViewModel->getElementDetail( $elPermit['element_id'] );
					// pr($elDetail, 1);
					echo  '<li class="">';
					echo  '<a ' .
						' data-id="' . $elDetail['Element']['id'] . '"' .
						' class="tipText tree-toggler nav-header tree_links tipText" title="Task">' .
						'<i class="ico_element pull-left" style="pointer-events:none;"></i> ' .
						'<span class="tree_text" title="" style="">'.strip_tags($elDetail['Element']['title']).'</span>' .
						'</a>' ;
					echo '<div class="sharing-icon"> ';
					if($elPermit['permit_read']){
						echo '<label class="permissions permit_read btn-circle btn-xs active tipText" title="Read">'.
						'<i class="fa fa-eye"></i>'.
						'</label>';
					}
					if($elPermit['permit_edit']){
						echo '<label class="permissions permit_edit btn-circle btn-xs active tipText" title="Update">'.
						'<i class="fa fa-pencil"></i>'.
						'</label>';
					}
					if($elPermit['permit_delete']){
						echo '<label class="permissions permit_delete btn-circle btn-xs active tipText" title="Delete">'.
						'<i class="fa fa-trash"></i>'.
						'</label>';
					}
					/* if($elPermit['permit_add']){
						echo '<label class="permissions permit_add btn-circle btn-xs active tipText" title="Add">'.
						'<i class="fa fa-plus"></i>'.
						'</label>';
					} */
					if($elPermit['permit_copy']){
						echo '<label class="permissions permit_copy btn-circle btn-xs active tipText" title="Copy">'.
						'<i class="fa fa-copy"></i>'.
						'</label>';
					}
					if($elPermit['permit_move']){
						echo '<label class="permissions permit_move btn-circle btn-xs active tipText" title="Cut">'.
						'<i class="fa fa-cut"></i>'.
						'</label>';
					}


					// Get all propagations

					if( $share_permission ){
						$elids = ['project_id' => $project_id, 'workspace_id' => $wsid, 'element_id' => $elPermit['element_id'] ];
						$elPropagate = $this->ViewModel->element_propagation( $elids, $user_id );
						if( isset($elPropagate) && !empty($elPropagate) ) {
							$elProp = $elPropagate['ElementPropagate'];
	?>
						<div class="propogation-wrapper">
							<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">
								<i class="fab fa-pagelines"></i>
							</a>

							<div class="propogat_permisions" style="display: block;">

								<div class="options-arrow right"></div>

								<div class="options-inner">

									<?php if($elProp['permit_read']){ ?>
										<label title="Read" class="permissions permit_read btn-circle btn-xs tipText active"> <i class="fa fa-eye"></i> </label>
									<?php } ?>

									<?php if($elProp['permit_edit']){ ?>
										<label title="" class="permissions permit_edit btn-circle btn-xs tipText active" data-original-title="Update"><i class="fa fa-pencil"></i></label>
									<?php } ?>

									<?php if($elProp['permit_delete']){ ?>
										<label title="" class="permissions permit_delete btn-circle btn-xs tipText active" data-original-title="Delete"><i class="fa fa-trash"></i></label>
									<?php } ?>

									<?php if($elProp['permit_copy']){ ?>
										<label title="" class="permissions permit_copy btn-circle btn-xs tipText active" data-original-title="Copy"><i class="fa fa-copy"></i></label>
									<?php } ?>

									<?php if($elProp['permit_move']){ ?>
										<label title="" class="permissions permit_move btn-circle btn-xs tipText active" data-original-title="Cut"><i class="fa fa-cut"></i></label>
									<?php } ?>

								</div>

							</div>

						</div>
					<?php
						}
					}
					echo '</div> ';

					echo '</li>';
				}

				echo '</ul>';

			}

			}
			echo '</li>';
		}
	}
	}
		echo '</ul>';
	echo '</li>';
	// pr($ppdata  );
	// pr($wpdata );
	// pr($epdata );
	// $projectDetail = $this->ViewModel->getProjectDetail( $project_id );
}
// pr($pdata, 1);
?>
		</ul>
	</div>
	</td>
				</tr>
			</table>
		</div>
</div>
	<div class="modal-footer">
	<?php //pr($pdata['pp_data']['ProjectPermission']); ?>
	<?php if($pdata['pp_data']['ProjectPermission']['share_by_id'] == $this->Session->read('Auth.User.id')){ ?>
	    <a href="<?php echo SITEURL.'shares/update_sharing/'.$project_id.'/'.$pdata['pp_data']['ProjectPermission']['user_id'].'/2'; ?>" class="btn btn-sm btn-success"  >Edit Permissions</a>
   <?php }else { ?>
        <button type="button" class="btn btn-sm btn-success disabled"  >Edit Permissions</button>
   <?php }  ?>
		<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
	</div>

<script type="text/javascript" >
$(function(){
	// $('.overlay_loader').show()
	// setTimeout(function(){
		// $('.overlay_loader').hide()
	// }, 500) 
	
	$('#popup_model_box').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });
	$('.sharing-icon').each(function(i, v) {
		// console.log($(this).children('.permissions'))
	})
	
	$('.tipText').tooltip({ container : 'body',placement :'top'})

/* 	$('body').delegate('label.permissions.tipText', 'mouseenter', function(){
		var that = $(this)
		that.tooltip('show');
		setTimeout(function(){

			that.tooltip('hide');
		}, 1000);
	})

	$('body').delegate('label.permissions.tipText', 'mouseleave', function(){
		$(this).tooltip('hide');
	}); */

})
</script>


