<?php // USED TO SHOW POPUP MODAL BOX TO SHOW ALL THE PERMISSIONS THAT ARE GIVEN TO THE CURRENT USER. ?>
<?php // Used in Projects/share_projects.ctp -> popover box as Element View ?>

<?php echo $this->Html->css('projects/manage_categories'); ?>

<style>

.modal-header {display: block; height: 70px;}
.sharing-icon .permissions.active {
    margin-left: 3px !important;
}
.ico_element {
    background: rgba(204, 204, 204, 0.7) url("../../images/icons/icon_e.png") no-repeat scroll 3px 3px / 60% auto !important;
    cursor: pointer;
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
.participants {
    display: inline-block;
    margin: 0 0 6px 8px;
    padding: 3px 4px 0 6px;
}

.participants a.btn {
    background-color: #f2f2f2;
    border-color: #cdcdcd;
    margin-right: -3px;
    margin-top: -3px;
}
.table-responsive-update{
  overflow-x : auto;
  overflow-y: visible;
 }
</style>
<?php
$GpPERMIT = $gp_data['ProjectPermission']['project_level'];
//pr($this->group->ProjectGroupDetail($this->params['pass']['1']));
//pr($gp_data);
	$project_title = "";
	$share_permission = false;

	$prDetail = $this->ViewModel->getProjectDetail( $project_id );
	$project_title = strip_tags($prDetail['Project']['title']);

	$pdata = $this->ViewModel->group_all_permissions( null, $project_id , $group_id);

	// pr($pdata['pp_data']['ProjectPermission']);
 ?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel" style="display: inline"><?php echo ucfirst(htmlentities($gp_data['ProjectGroup']['title'],ENT_QUOTES, "UTF-8")); ?>
	</h3>
</div>
<div class="modal-body">

	<div id="" class="table-responsive table-responsive-update">

		<table class="table border-bottom" style="display:block">
		   <tbody style="display:table;width:100%;">
			<tr>
				<td  width="25%" class="text-left">
					<p class="text-small">Owner: </p>
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


					echo htmlentities($gp_data['ProjectGroup']['title'],ENT_QUOTES, "UTF-8"); ?></span>
				</td>

				<td width="25%" class="text-left">
					<p class="text-small">Date Shared: </p>
					<span class="text-mid"><?php echo _displayDate($pdata['pp_data']['ProjectPermission']['created']); ?></span>
				</td>
			</tr>
			</tbody>

		</table>

		<div>


			<?php

			if(isset($gp_dataU) && !empty($gp_dataU)) {

				foreach($gp_dataU as $key => $val ) {
				$keys = $val['ProjectGroupUser']['user_id'];
				?>
				<td><span class="bg-gray participants" style="display:none">
					<?php echo $this->Common->userFullname($keys); ?>
					<a class="show_profiles btn btn-sm" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $keys)); ?>"  data-target="#popup_modal" data-toggle="modal"  ><i class="fa fa-user text-maroon " ></i></a>
				</span></td>
				<?php
				}
				// echo implode(", ", $allparticipOW);
			}
			?>

			</div>
	</div>


	<div id="" class="table-responsive">

		<table class="table border-bottom">
			<tr>
				<td  width="25%" class="text-left">
	<div id="sharing_list" class="sharing_list" >
		<ul class="nav nav-list tree" >

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

	?>

	<li class="has-sub-cat">
		<a  class="tipText tree-toggler nav-header tree_links"> <i class="<?php echo $icon_class; ?>"></i>
			<span class="tree_text" title="" style=""><?php echo $project_title; ?></span>
		</a>
	<div class="sharing-icon">
		<?php //pr($prPermit);
		if( isset($prPermit) && !empty($prPermit) ) {

		if($prPermit['permit_read'] || $prPermit['project_level']){
				echo '<label class="permissions permit_read btn-circle btn-xs active tipText project" title="Read">'.
				'<i class="fa fa-eye"></i>'.
				'</label>';
			}
			if($prPermit['permit_edit'] || $prPermit['project_level']){
				echo '<label class="permissions permit_edit btn-circle btn-xs active tipText project" title="Update">'.
				'<i class="fa fa-pencil"></i>'.
				'</label>';
			}
			if($prPermit['permit_delete'] || $prPermit['project_level']){
				echo '<label class="permissions permit_delete btn-circle btn-xs active tipText project" title="Delete">'.
				'<i class="fa fa-trash"></i>'.
				'</label>';
			}
			if($prPermit['permit_add'] || $prPermit['project_level']){
				echo '<label class="permissions permit_add btn-circle btn-xs active tipText project" title="Add New Task">'.
				'<i class="fa fa-plus"></i>'.
				'</label>';
			}

			if( $share_permission ) {
				$prPropagate = $this->ViewModel->project_propagation( $project_id );
				if( isset($prPropagate) && !empty($prPropagate) ) {
					$prProp = $prPropagate['ProjectPropagate'];
?>
				<div class="propogation-wrapper">
					<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">
					<i class="fa fa-code-fork fa-rotate-180"></i>
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

			//
		}
	?>
	</div>

	<?php
	/*   added below code for client request by vikas at 4th april 2019 for not showing all lines to owner group */

	if(empty($GpPERMIT)){
	if( !empty($wpdata)) {
		echo '<ul class="nav nav-list tree">';

		foreach($wpdata as $k => $v) {
			$wsPermit = $v['WorkspacePermission'];
			$wsid = pwid_workspace($wsPermit['project_workspace_id'], $project_id);
			$wsDetail = $this->ViewModel->getWorkspaceDetail( $wsid );
			echo  '<li class="has-sub-cat">';
				echo  '<a ' .
					' data-id="' . $wsDetail['Workspace']['id'] . '"' .
					' class="tipText tree-toggler nav-header tree_links">' .
					'<i class="'.$icon_class.'"></i> ' .
					'<span class="tree_text" title="" style="">'.$wsDetail['Workspace']['title'].'</span>' .
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
						$wsPropagate = $this->ViewModel->Group_workspace_propagation( $wsid, $gp_data['ProjectGroup'], null, $project_id );
						if( isset($wsPropagate) && !empty($wsPropagate) ) {
							$wsProp = $wsPropagate['WorkspacePropagate'];
	?>
						<div class="propogation-wrapper">
							<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">
								<i class="fa fa-code-fork fa-rotate-180"></i>
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
				echo '<ul class="nav nav-list tree">';
				foreach($edata as $k => $v) {
					$elPermit = $v;
					$elDetail = $this->ViewModel->getElementDetail( $elPermit['element_id'] );
					// pr($elDetail, 1);
					echo  '<li class="">';
					echo  '<a ' .
						' data-id="' . $elDetail['Element']['id'] . '"' .
						' class="tipText tree-toggler nav-header tree_links">' .
						'<i class="ico_element pull-left"></i> ' .
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
					if($elPermit['permit_add']){
						echo '<label class="permissions permit_add btn-circle btn-xs active tipText" title="Add">'.
						'<i class="fa fa-plus"></i>'.
						'</label>';
					}
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
						$elPropagate = $this->ViewModel->Group_element_propagation( $elids, $gp_data['ProjectGroup'] );
						if( isset($elPropagate) && !empty($elPropagate) ) {
							$elProp = $elPropagate['ElementPropagate'];
	?>
						<div class="propogation-wrapper">
							<a title="Propagate Permissions" class="propogation perm_propogate show_propogate btn-circle btn-xs tipText activated" data-placement="right" rel="popover" data-html="true" data-trigger="click" data-remote="">
								<i class="fa fa-code-fork fa-rotate-180"></i>
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
			echo '</li>';
		}
	} }
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
	<?php
	//pr($pdata['pp_data']);
	//pr($this->params['pass']['0']);
	if($pdata['pp_data']['ProjectPermission']['share_by_id'] == $this->Session->read('Auth.User.id')){ ?>
	    <a href="<?php echo SITEURL.'groups/update_permissions/'.$project_id.'/'.$this->params['pass']['0']; ?>" class="btn btn-sm btn-success"  >Edit Permissions</a>
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

	$('body').delegate('label.permissions.tipText', 'mouseenter', function(){
		var that = $(this)
		that.tooltip('show');
		setTimeout(function(){

			that.tooltip('hide');
		}, 1000);
	})

	$('body').delegate('label.permissions.tipText', 'mouseleave', function(){
		$(this).tooltip('hide');
	});

	/* $("#popup_model_box").click(function(e){
	e.stopPropagation();
	}); */



	$('#RecordviewB').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal').find(".modal-content").html('<div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="<?php echo SITEURL ?>images/ajax-loader-1.gif" style="margin: auto;"></div>');
    });

})
</script>


