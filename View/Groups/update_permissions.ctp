<style>
	.lbl {
		margin-bottom: 10px; padding: 0px; text-align: left; font-weight: 600;
	}
	.sharing-help .applied_permissions {
		margin-right: 3px !important;
	}
	.sharing_list li a.tree-toggler.tree_links .tree_text {
		display: inline-block;
		max-height: 24px;
		height: 24px;
		width: 215px;
	}

	.users_tip_task{ cursor : pointer; }
	 li.tree-options {
	    float: right;
	}

	.shares-list li:first-child{

		padding-top:7px;
	}

	.shares-list li {
	    display: inline-block;
	    clear: both;
	}

	 .user-image {

	     }

	.myaccountPic {
	    max-width: 40px;
	    display: block;
	    display: inline;
	    margin: 3px;
	    cursor : defalut;
		border-radius: 50%;
		border: 2px solid #ccc;
	}

	.pop-content-parent {
	    font-size: 13px !important;
	    font-weight: 600 !important;
	}
</style>
<?php
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/group_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
?>

<?php echo $this->Session->flash(); ?>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
					<p class="text-muted date-time">
						<span>Set Role and Permissions</span>
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



<?php
$project_permit = null;
$project_detail = null;
if(( isset($project_id) && !empty($project_id)) && ( isset($group_id) && !empty($group_id))) {

	$group_detail = $this->Group->ProjectGroupDetail( $group_id );
	$project_detail = $this->ViewModel->getProjectDetail( $project_id, -1 );

	$details ='';
	$flag ='';
	$ppid = 0;
	if( isset($group_detail['ProjectPermission']) && !empty($group_detail['ProjectPermission']) ) {
		$ppid = $group_detail['ProjectPermission']['id'];
		echo $this->Form->input('ProjectPermission.id', ['type' => 'hidden', 'value' => $ppid]);


	  if($group_detail['ProjectPermission']['project_level']==1){
		$details = 'Owners';
		$flag = 1;
	  }else{
		$details = 'Sharers';
		$flag = 0;
	  }

	  }
?>
<?php echo $this->Form->create('ShareTree', array('url' => array('controller' => 'groups', 'action' => 'update_permissions', $project_id, $group_id, $ppid ), 'class' => 'formAddSharing', 'id' => 'frm_share_tree', 'enctype' => 'multipart/form-data')); ?>
	<div class="panel panel-default" id="user_detail_section">
		<div class="panel-heading clearfix" id="">
			<span class="time pull-left">
				Current Role Level: Group <?php echo $details; ?>
			</span>
		</div>

		<div class="table-responsive" style="overflow-y:hidden;">

			<table class="table table-bordered">
				<tr>
					<th width="25%" class="text-left">Group</th>
					<th width="25%" class="text-left">Project</th>
					<th width="25%" class="text-center">Role Level</th>
					<th width="25%" class="text-center">Propagation</th>
				</tr>
				<tr>
					<td class="text-left users_tip_task_parent"><span class="users_tip_task"><?php echo htmlentities($group_detail['ProjectGroup']['title']); ?>  <i class="broupblack text-maroon group-icon"></i></span></td>
					<td class="text-left"><?php echo htmlentities($project_detail['Project']['title']); ?></td>
					<td class="text-center">

						<div class="toggle-group propogation-on-off">
							<a href="" title="All Permissions Granted" data-container="body" class="tipText btn btn-xs btn-toggle toggle-on">Owner</a>

							<input type="checkbox" id="project_level" name="data[Share][project_level]" value="1" checked="checked" >

							<a href="" title="Set Sharing Permissions" data-container="body" class="tipText btn btn-xs btn-toggle toggle-off">Sharer</a>

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
			<h5 class="">Project Sharing</h5>

			<span class="pull-right option-buttons">

					<!-- <label class="goto btn-goto tipText" title="Select All">
						<input type="checkbox" name="select_all_permissions" value="0" id="select_all_permissions"/>
						<i class="fa fa-check"></i>
					</label>
					<span class="label-text">Select All Permissions</span> -->

				<div class="btn-group action-group ">
				<!--	<a class="btn btn-sm action_buttons btn-primary" data-action="collapse_all" href="#" >Collapse All</a>
					<a class="btn btn-sm action_buttons btn-primary disabled" data-action="expand_all" href="#" >Expand All</a>-->
				</div>

					<a href="javascript:" class="btn btn-warning btn-sm  disabled " data-action="collapse_all" id="rst_share_tree">Toggle Tree</a>
					<a href="#" class="btn btn-success btn-sm" id="sbmt_sharing" >Save</a>
					<a href="<?php echo Router::url(['controller' => 'shares', 'action' => 'my_groups' ]); ?>" class="btn btn-danger btn-sm " id="caneel_share" >Cancel</a>

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
								<li class="tree-options">
										<div class="col_exp">
											<div class="btn-group action-group" style="margin-left: 5px;">
												<a class="btn btn-sm action_buttons btn-primary" data-action="collapse_all" href="#" >Collapse All</a>
												<a class="btn btn-sm action_buttons btn-primary disabled" data-action="expand_all" href="#" >Expand All</a>
											</div>
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

$area_icons = '<div class="sharing-icon area-icons">' .
			    '<label class="ap-permissions permit_read btn-circle btn-xs tipText" title="Read" data-class=".permit_read">' .
			        '<input type="checkbox" name="area_permit_read" value="" id="">' .
			        '<i class="fa fa-eye lbl-icn"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_edit btn-circle btn-xs tipText" title="" data-original-title="Update" data-class=".permit_edit">' .
			        '<input type="checkbox" name="area_permit_edit" value="">' .
			        '<i class="fa fa-pencil"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_delete btn-circle btn-xs tipText" title="" data-original-title="Delete" data-class=".permit_delete">' .
			        '<input type="checkbox" name="area_permit_delete" value="">' .
			        '<i class="fa fa-trash"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_copy btn-circle btn-xs tipText not-allowed" title="" data-original-title="Copy" data-class=".permit_copy">' .
			        '<input type="checkbox" name="area_permit_copy" value="">' .
			        '<i class="fa fa-copy"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_move btn-circle btn-xs tipText not-allowed" title="Cut &amp; Move" data-class=".permit_move">' .
			        '<input type="checkbox" name="area_permit_move" value="">' .
			        '<i class="fa fa-cut"></i>' .
			    '</label>' .
			    '<label class="set_unset_all">Set All</label>' .
			'</div>';
	?>
	<ul class="nav nav-list tree" >
	<?php

		$share_file =  'update_sharing';

		$display = '';
		$icon_class = 'tree_icons opened fa fa-minus';

		$prjData = $project_detail['Project'];
		$wrkData = $prjWrks[$project_id]['workspace'];

		echo  '<li class="has-sub-cat">';
			echo  '<a ' .
					' data-id="' . $prjData['id'] . '"' .
					' class="tipText tree-toggler nav-header tree_links">' .
						'<i class="'.$icon_class.'"></i> ' .
						'<span class="tipText tree_text" title="Project" style="">'.$prjData['title'].'</span>' .
				'</a>' ;

			$project_permit = group_has_permissions( $project_id, $group_id, true );

			// show project icons
			echo $this->element('../Groups/partials/'.$share_file, ['type' => 'project', 'model' => 'ProjectPermission',  'project_permit' => $project_permit ]);

	if( isset($prjWrks) && !empty($prjWrks) ) {
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

						$workspace_permit = $this->Group->group_wsp_permission_details( workspace_pwid( $project_id, $val['id']), $project_id, $group_id );

						echo $this->element('../Groups/partials/'.$share_file, ['type' => 'workspace', 'model' => 'WorkspacePermission', 'workspace_id' => $val['id'], 'workspace_permit' => $workspace_permit ] );

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
										echo($area_icons);
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

											$element_permit = $this->Group->group_element_share_permission( $eval['id'], $project_id, $group_id );

											echo $this->element('../Groups/partials/'.$share_file, ['type' => 'element', 'model' => 'ElementPermission', 'workspace_id' => $val['id'], 'area_id' => $akey, 'element_id' => $eval['id'], 'element_permit' => $element_permit ]);

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

	}
		echo '</li>';// end pr
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


<?php
$pplevel = 0;
if(isset($project_permit) && !empty($project_permit)){
$pplevel = $project_permit['ProjectPermission']['project_level'];
} ?>
                  <div class="all-permit" <?php if(empty($pplevel)){ ?> style="display: none;" <?php } ?>>All Permissions Granted</div>


					<?php echo $this->Form->end(); ?>
				<?php } ?>

                </div>
            </div>
        </div>
	</div>
</div>
<script type="text/javascript">
	$(function() {

		// Set checked to all checkboxes that are active, if project_level is zero
		// Also set checked to share_permission if it is 1
		<?php if(isset($group_detail['ProjectPermission']['project_level']) && $group_detail['ProjectPermission']['project_level'] == 0){ ?>
			$('.toggle-group').trigger('click')
			<?php if(isset($group_detail['ProjectPermission']['share_permission']) && $group_detail['ProjectPermission']['share_permission'] == 1){ ?>
				$('.propogation-handle').trigger('click')
			<?php } ?>

			setTimeout(function(){

				$('label.permissions.active').each(function(){

					var $input = $(this).find('input[type=checkbox]')

					$input.prop('checked', true)
				})

			}, 500)


		<?php }else { ?>

			setTimeout(function(){

				$('label.permissions').each(function(){

					var $input = $(this).find('input[type=checkbox]')
					$(this).removeClass('active')
					$input.prop('checked', false)
				})

			}, 500)

		<?php } ?>

	})
</script>

<script type="text/javascript">
	$(function(){

		$.fn.toggleText = function(text1, text2) {
			($(this).text() === text1) ? $(this).text(text2) : $(this).text(text1);
			return this;
		}

/* 		parent.find('input[type=radio]').each( function( i, j ){
			var $p = $(this).parent();
			$('.share_propagation_permission').addClass('block')
			$('#rst_share_tree').addClass('disabled')
			if($p.hasClass('active')) {
				$(this).prop('checked', true);
				if( i == 1 ) {
						$("#share_detail_section").slideFade(2000, 'easeOutBounce')
						$('.share_propagation_permission').removeClass('block')
						$('#rst_share_tree').removeClass('disabled')
				}
				else if( i == 0 ) {
					$("#share_detail_section").slideFade(500)
				}
			}
		}) */

		$('body').delegate('.set_unset_all', 'click', function(event) {
			event.preventDefault();
			var $this = $(this),
				$parent_ul = $(this).parents('.area-icons:first'),
				$parent_li = $parent_ul.parents('li.has-sub-cat:first'),
				$child_ul = $parent_li.find('ul.tree'),
				$area_labels = $parent_ul.find('label'),
				$area_input = $area_labels.find('input');
				$all_el_labels = $child_ul.find('label'),
				$input = $all_el_labels.find('input');

			$input.each(function(){
				$(this).prop("checked", ($this.hasClass('active')?false:true));

				if($(this).prop("checked")) {
					$(this).parent('label:first').addClass('active');
				}
				else {
					$(this).parent('label:first').removeClass('active');
				}
			})

			$area_input.each(function(){
				$(this).prop("checked", ($this.hasClass('active')?false:true));

				if($(this).prop("checked")) {
					$(this).parent('label:first').addClass('active');
				}
				else {
					$(this).parent('label:first').removeClass('active');
				}
			})

			$(this).toggleText('Set All', 'Unselect All');
			$(this).toggleClass('active');

			if($this.hasClass('active')){
				$parent_ul.find('.permit_copy,.permit_move').removeClass('not-allowed');
			}
			else{
				$parent_ul.find('.permit_copy,.permit_move').addClass('not-allowed');
			}


		})


		$('body').delegate('.area-icons label.ap-permissions', 'click', function(event) {
			event.preventDefault();
			var $this = $(this),
				$parent_ul = $(this).parents('.area-icons:first'),
				$parent_li = $parent_ul.parents('li.has-sub-cat:first'),
				$child_ul = $parent_li.find('ul.tree'),
				set_class = $(this).data('class'),
				$this_input = $(this).find('input');

			$this_input.prop("checked", !$this_input.prop("checked"));

			var $all_el_labels = $child_ul.find(set_class),
				$input = $all_el_labels.find('input');

			$input.each(function(){
				$(this).prop("checked", ($this.hasClass('active')?false:true));

				if($(this).prop("checked")) {
					$(this).parent('label:first').addClass('active');
				}
				else {
					$(this).parent('label:first').removeClass('active');
				}
			})

			if($(this).hasClass('active')){
				$(this).removeClass('active');
			}
			else {
				$(this).addClass('active');
			}

			if( $(this).hasClass('permit_edit') ) {
				if( !$(this).hasClass( 'active' ) ) {
					$parent_li.find('.permit_copy,.permit_move').addClass('not-allowed').removeClass('active');
				}
				else {
					$parent_li.find('.permit_copy,.permit_move').removeClass('not-allowed');
				}
			}

			var active_length = ($parent_li.find('label.active').length) ? $parent_li.find('label.active').length : 0;
			if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent_li.find('.permit_read').hasClass('active') ) {

				$parent_li.find('.permit_read').addClass('active');
				$parent_li.find('.permit_read').find('input[type=checkbox]').prop('checked', true);

			}
			// if only one permission is given but its not the read permission
			else if( $(this).is($('.permit_read')) && active_length > 0 ) {

				$parent_li.find('.permit_read').addClass('active');
				$parent_li.find('.permit_read').find('input[type=checkbox]').prop('checked', true);

			}

		})




$( '.users_tip_task .group-icon' ).on( "mouseenter", function( e ) {

        var urlV = $js_config.base_url + "groups/project_people/<?php echo $group_id ?>/<?php echo $project_id ?>/<?php echo $flag ?>";

        var $t = $(this);
         $.ajax({
                url: urlV,
                async: false,
                global: false,
                beforeSend: function () {
                    //$(".ajax_overlay_preloader").fadeIn();
                },
                complete: function () {
                    //$(".ajax_overlay_preloader").fadeOut();
                },
                success: function (response) {
                    $t.popover({
						placement : 'right',
						trigger : 'click',
						html : true,
						template: '<div class="popover pop-content-parent" role="tooltip"><div class="arrow"></div><h3 class="popover-title" style="display:none;"></h3><div class="popover-content pop-content"></div></div>',
						container: 'body',

						delay: {  hide: 1000}
					});
					$t.attr('data-content',response);
					$t.popover('show');
					$t.on('hidden.bs.popover', function(){
							$('.tooltip').hide();
						})

                }
            })
        })
		.on( "mouseout", function( e ) {
			$('.tooltip').hide();
		})

		$('body').on( "mouseout", '.myaccountpicsec .myaccountPic', function( e ) {
			$('.tooltip').hide();
		})

		$( '#share_section_submit,#user_detail_section > .panel-heading' ).on( "mousemove", function( e ) {
				 $( '.users_tip_task' ).popover('hide');
				 $( '.users_tip_task' ).popover('destroy');
				 $( '.popover.fade.right.in' ).remove();
		})

	})


</script>

