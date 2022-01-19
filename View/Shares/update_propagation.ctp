<?php
$exist_project_level = 0;
if( isset($exist_permissions) && !empty($exist_permissions) ) {
	if( isset($exist_permissions['pp_data']['ProjectPermission']) && !empty($exist_permissions['pp_data']['ProjectPermission']) ) {
		$ppermit = $exist_permissions['pp_data']['ProjectPermission'];
		$exist_project_level = ( isset($ppermit['project_level']) && !empty($ppermit['project_level']) ) ? 1 : 0;
	}
}
// Set it to true to collapse all tree levels on page load.
// Also assign this value to the global js variable
$collapse = false;
?>

<script type="text/javascript">
jQuery(function($) {

	$js_config.start_collapse = '<?php echo $collapse; ?>';

})
</script>

<?php
echo $this->Html->css('projects/tooltip');
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/manage_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
// echo $this->Html->script('projects/plugins/search.select.list', array('inline' => true));
?>
<style>
	.goto.block.unchange {
	    border-radius: 0 4px 4px 0;
	    padding: 1px 3px 0 4px;
		pointer-events: none;
	}
	.nav.nav-list.tree a.tree_links {
	    cursor: pointer;
	    display: inline-block;
	    font-size: 13px;
	    line-height: 24px;
	    margin: 0;
	    padding: 5px 5px 5px 6px;
	    color: #333333;
	    overflow: hidden;
	    text-overflow: ellipsis;
	}


</style>

<script type="text/javascript">
jQuery(function($) {

	$('input[type=checkbox]').prop('checked', false);

	$('body').delegate('label.permissions', 'click', function(event) {
			var e = $(this);

			var $input = $(this).find('input[type=checkbox]'),
				iName = $input.attr('name'),
				$options = $('.propogate-options');

			$input.prop("checked", !$input.prop("checked"));

			if($input.prop("checked")) {
				$(this).addClass('active')
			}
			else {
				$(this).removeClass('active')
			}

			var $parent = $(this).parent();

			if( $(this).hasClass('permit_edit') && !$(this).hasClass('active') ) {
				$('.permit_move,.permit_copy', $parent).removeClass('active');
				$('.permit_move input', $parent).prop("checked", false);
				$('.permit_copy input', $parent).prop("checked", false);
				return;
			}
			if( ( $(this).hasClass('permit_move') ||  $(this).hasClass('permit_copy') ) &&  !$parent.find('.permit_edit').hasClass('active') ) {
				$(this).removeClass('active');
				$input.prop("checked", false);
				return;
			}

			var active_length = ($parent.find('label.active').length) ? $parent.find('label.active').length : 0;

			// if clicked other than read permission and read button has not an active class
			if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent.find('.permit_read').hasClass('active') ) {

				$parent.find('.permit_read').addClass('active');
				$parent.find('.permit_read').find('input[type=checkbox]').prop('checked', true);

			}
			// if only one permission is given but its not the read permission
			else if( $(this).is($('.permit_read')) && active_length > 0 ) {

				$parent.find('.permit_read').addClass('active');
				$parent.find('.permit_read').find('input[type=checkbox]').prop('checked', true);
			}


	})

	$('body').delegate('label.applied_permissions', 'click', function(event) {
		event.preventDefault()
		var e = $(this);
		// console.log('applied_permissions')
	})

	// DEACTIVATE ALL ICONS AND ITS INPUT CHECKBOX AFTER HIDE PROPOPGATION DIV
	var deactivate_icons = function( ) {
		var $this = $(this);

		if( !$this.hasClass('show') ) {
			$this.addClass('show')
		}
		else {
			$this.removeClass('show')
			console.log($this)
			var $read = $this.find('.permit_read'),
				$edit = $this.find('.permit_edit'),
				$add = $this.find('.permit_add'),
				$delte = $this.find('.permit_delete'),
				$copy = $this.find('.permit_copy'),
				$move = $this.find('.permit_move');
			if( $read.length ) {
				$read.removeClass('active');
				$read.find('input').prop('checked', false)
			}
			if( $edit.length ) {
				$edit.removeClass('active');
				$edit.find('input').prop('checked', false)
			}
			if( $add.length ) {
				$add.removeClass('active');
				$add.find('input').prop('checked', false)
			}
			if( $delte.length ) {
				$delte.removeClass('active');
				$delte.find('input').prop('checked', false)
			}
			if( $copy.length ){
				$copy.removeClass('active');
				$copy.find('input').prop('checked', false)
			}
			if( $move.length ) {
				$move.removeClass('active');
				$move.find('input').prop('checked', false)
			}
		}

	}

	/******************************* Get all events of an element ***************************************/

		$.fn.output = function(){
			var $btn = $(this);

			var e = $._data($btn.get(0),"events"),
			str = "All Events"
			$.each(e,function(i,v){
				str+="\n" + i + ":" + v.toString();
				console.log(v);
				$.each(v,function(ii,vv){
					str+="\n\t" + ii + ":" + vv.handler.toString();
					console.log(vv.handler);
				})
			})


		};
	// USES:
	// $('.propogation:first').on("mouseover mousedown blur",function(){
		// $(this).output()
	// });

	/**********************************************************************/

	// TOGGLE CHECKBOX CHECKED PROPERTY
	$.fn.toggleCheck = function() {
		$(this).prop('checked', !$(this).prop('checked'));
	};

	// HIDE/SHOW PROPOPGATION ICON BOX
	$('body').delegate(".propogation", 'click', function(event){
		event.preventDefault();
		var $this = $(this);
		// var $checkbox = $(this).find('input[type=checkbox]');

		var $propogat_wrapper = $(this).parent('.propogation-wrapper'),
			$pro_perm = $propogat_wrapper.find('.propogat_permisions'),
			$pro_perm_inner = $pro_perm.find('.options-inner');


		$(this).toggleClass('activated')
		// $pro_perm.slideFadeToggle(300, 'swing', deactivate_icons );
		$pro_perm.slideFadeToggle(1500, 'easeOutElastic' );

	})
	$('.propogation').trigger('click');


	setTimeout(function(){

		$('.dis_props.active').each( function(i, j) {
			var $t = $(this),
				$checkbox = $t.find('input[type=checkbox]');
			if( $checkbox.length )
				$checkbox.prop('checked', true);
		})
	},600)



	$('body').delegate('#add_sharing', 'click', function(event) {
		event.preventDefault();
		var $form = $('#frm_share_user'),
			$user_input = $form.find('#share_user_id'),
			$share_action = $form.find('#share_action'),
			user_id = 0;

		if( $(this).is($('#add_sharing')) ) {
			$share_action.val(1);
		}

		if( $user_input.length > 0 ) {
			user_id = $user_input.val();
		}

		if( user_id <= 0 ) {
			if( $form.find('.error').length )
				$form.find('.error').remove();

			var $span = $('<span>')
						.attr('class', 'error')
						.text('Please select a user.')

			$form.append($span)
		}
		else {

			$form.submit()
		}

		return false;
	})


	function resizeStuff() {
		//$('.tree_links').ellipsis_word();
	}

	var TO = false;
	$(window).on('resize', function(){

		if(TO !== false) {
			clearTimeout(TO);
		}

		TO = setTimeout(resizeStuff, 800); //800 is time in miliseconds
	});




	$('body').delegate('#sbmt_propagate_tree', 'click', function(event){
		event.preventDefault();

		var $t = $(this),
			$that = $('#frm_propagate_tree');

			$t.addClass('disabled');
			$t.css('pointer-events','none');
		// console.log($that.serializeObjects())
		$that.submit()
		// return true;
	})

	setTimeout(function(){

	}, 600)



})
$(window).on('load', function(event){
	setTimeout(function(){
		$('input[type=checkbox][name=sharing_level]').prop('checked', true);
		$('.propogat_permisions > .options-inner').find('label.permissions.active').each(function() {
			$(this).find('input[type=checkbox]').prop('checked', true);
		})
	}, 600)
})
</script>



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
                            <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
                        </div>

		<div class="box-body clearfix list-shares" style="min-height: 800px">


<?php if( isset($shareUser) && !empty($shareUser)) {

	$user_detail = $this->ViewModel->get_user($shareUser, ['hasOne' => array('UserInstitution'), 'hasMany' => array('UserProject', 'UserPlan', 'UserTransctionDetail')], 1);
?>

                            <div class="panel panel-default" id="user_detail_section">
								<div class="panel-heading clearfix" id="">
									<span class="time pull-left">
										User Detail
									</span>
								</div>

                                <div class="table-responsive">

                                    <table class="table table-bordered">

                                        <tr>
                                            <th width="10%" class="text-center">User Status</th>
                                            <th width="20%" class="text-left">Team Member</th>
                                            <th width="50%" class="text-left">Organization</th>
                                            <th width="20%" class="text-center">Role Level</th>

                                        </tr>
                                        <tr>
                                            <td class="text-center">
												<label class="goto btn-goto block" title="">
													<?php echo ($user_detail['User']['status']) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'; ?>
												</label>
											</td>
                                            <td class="text-left"><?php echo $user_detail['UserDetail']['first_name'].' '. $user_detail['UserDetail']['last_name']; ?></td>
                                            <td class="text-left"><?php echo  !empty($user_detail['UserDetail']['org_name']) ? $user_detail['UserDetail']['org_name'] : "N/A"; ?></td>
                                            <td class="text-center">
												<label class="goto btn-goto block unchange" title="">
													<i class="fa fa-arrow-down"></i>
												</label> Sharer
											</td>

                                        </tr>
                                    </table>
                                </div>
                            </div>


					<!--   stage 2 -->
					<?php echo $this->Form->create('ShareTree', array('url' => array('controller' => 'shares', 'action' => 'update_propagation', $project_id, $shareUser, $share_action, $ppermit_insert_id ), 'class' => 'formUpdate', 'id' => 'frm_propagate_tree', 'enctype' => 'multipart/form-data')); ?>

						<div class="panel panel-default" id="share_section_submit">
							<div class="panel-heading clearfix" id="">

								<h5 class="">Project Sharing</h5>

								<span class="pull-right option-buttons">

									<a href="" class="btn btn-warning btn-sm" id="rst_share_tree">Toggle Tree</a>
									<a href="#" class="btn btn-success btn-sm" id="sbmt_propagate_tree" >Save</a>
									<a href="<?php echo Router::url(['controller' => 'shares', 'action' => 'sharing_map', $project_id]); ?>" class="btn btn-danger btn-sm " id="caneel_propagation" >Cancel</a>

								</span>
							</div>
						</div>
						<div class="panel panel-default" id="share_detail_section">

							<div class="panel-body no-padding" id="">
								<div class="table-responsive">
									<table class="table">
										<tr>
											<th>
												<div class="shares-list header">
													<ul>
														<li class="shares-tree-heading">
															<label>Path</label>
															<div>Permissions</div>
														</li>
														<li class="tree-options">
															<div class="col_exp">
																<div class="btn-group action-group">
																	<a class="btn btn-sm action_buttons btn-primary" data-action="collapse_all" href="#" >Collapse All</a>
																	<a class="btn btn-sm action_buttons btn-primary" data-action="expand_all" href="#" >Expand All</a>
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

													echo $this->Form->input('Share.project_level', ['type' => 'hidden', 'value' => 0]);

													echo $this->Form->input('Share.project_id', ['type' => 'hidden', 'value' => $project_id]);
													echo $this->Form->input('Share.share_by_id', ['type' => 'hidden', 'value' => CakeSession::read( "Auth.User.id" )]);
													echo $this->Form->input('Share.share_for_id', ['type' => 'hidden', 'value' => $shareUser]);

$prjWrks = $this->ViewModel->project_workspaces($project_id);


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

	$share_file = (isset($share_action) && !empty($share_action)  && $share_action == 1 ) ? 'add_propagate' : 'edit_propagate';

	$display = '';
	$icon_class = 'tree_icons opened fa fa-minus';
	if( $collapse ) {
		$display = ' style="display: none;" ';
		$icon_class = 'tree_icons closed fa fa-plus';
	}
		$prj_status_text = '';
		$prj_status = $this->Permission->project_status($project_id);
		if(isset($prj_status) && !empty($prj_status)) {
			$prj_status = $prj_status[0][0]['prj_status'];
			if($prj_status == 'not_spacified'){
				$prj_status_text = ': Not Set';
			}
			else if($prj_status == 'progress'){
				$prj_status_text = ': In Progress';
			}
			else if($prj_status == 'overdue'){
				$prj_status_text = ': Overdue';
			}
			else if($prj_status == 'completed'){
				$prj_status_text = ': Completed';
			}
			else if($prj_status == 'not_started'){
				$prj_status_text = ': Not Started';
			}
		}


	$prjData = $prjWrks[$project_id]['project'];
	$wrkData = $prjWrks[$project_id]['workspace'];

	echo  '<li class="has-sub-cat">';
		echo  '<a ' .
				' data-id="' . $prjData['id'] .
				'" data-parent-id=""' .
				'title="Project Status'.$prj_status_text.'" class="tipText tree-toggler nav-header tree_links">' .
					'<i class="'.$icon_class.'"></i>  ' .
					$prjData['title'].
			'</a>' ;

		// show project icons
		echo $this->element('../Shares/partials/'.$share_file, ['type' => 'project', 'model' => 'ProjectPropagate']);

	// start ws list
	echo '<ul class="nav nav-list tree" '.$display.' >';

if( isset($prjWrks) && !empty($prjWrks) ) {
	foreach($wrkData as $key => $val) {
		// pr($val);
		$ws_sign_off = false;
		if(isset($val['sign_off']) && !empty($val['sign_off'])) {
			$ws_sign_off = true;
		}

		$ws_status_text = '';
		$ws_status = $this->Permission->wsp_status($val['id']);
		if(isset($ws_status) && !empty($ws_status)) {
			$ws_status = $ws_status[0][0]['ws_status'];
			if($ws_status == 'not_spacified'){
				$ws_status_text = ': Not Set';
			}
			else if($ws_status == 'progress'){
				$ws_status_text = ': In Progress';
			}
			else if($ws_status == 'overdue'){
				$ws_status_text = ': Overdue';
			}
			else if($ws_status == 'completed'){
				$ws_status_text = ': Completed';
			}
			else if($ws_status == 'not_started'){
				$ws_status_text = ': Not Started';
			}
		}

		// create ws and el if area exists
		if( isset($val['area']) && !empty($val['area']) ) {

			$areas = array_keys($val['area']);

			echo  '<li class="has-sub-cat">';
				echo  '<a ' .
						' data-id="' . $val['id'] .
						'" data-parent-id=""' .
						'" title="Workspace Status'.$ws_status_text.'" class="tipText tree-toggler nav-header tree_links"><i class="'.$icon_class.'"></i>  ' . $val['title'].
					'</a>' ;

					echo $this->element('../Shares/partials/'.$share_file, ['type' => 'workspace', 'model' => 'WorkspacePropagate', 'workspace_id' => $val['id'], 'ws_sign_off' => $ws_sign_off] );

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
											' data-id="' . $akey .
											'" data-parent-id=""' .
											'" title="Area" class="tipText tree-toggler nav-header tree_links area-title"><i class="'.$icon_class.'"></i>  ' . $aval.
										'</a>' ;

									// start el list
									echo '<ul class="nav nav-list tree" '.$display.' >';
								foreach($arEls as $ekey => $evals) {
									$eval = $evals['Element'];
									$task_sign_off = false;
									if(isset($eval['sign_off']) && !empty($eval['sign_off'])) {
										$task_sign_off = true;
									}

									$ele_status_text = '';
									$ele_status = $this->Permission->task_status($eval['id']);
									if(isset($ele_status) && !empty($ele_status)) {
										$ele_status = $ele_status[0][0]['ele_status'];
										if($ele_status == 'not_spacified'){
											$ele_status_text = ': Not Set';
										}
										else if($ele_status == 'progress'){
											$ele_status_text = ': In Progress';
										}
										else if($ele_status == 'overdue'){
											$ele_status_text = ': Overdue';
										}
										else if($ele_status == 'completed'){
											$ele_status_text = ': Completed';
										}
										else if($ele_status == 'not_started'){
											$ele_status_text = ': Not Started';
										}
									}
									echo  '<li class="has-sub-cat">';
									echo  '<a ' .
											' data-id="' . $eval['id'] .
											'" title="Task Status'.$ele_status_text.'"' .
											'" class="tree-toggler nav-header tree_links tipText">'.
											'<span class="ico_element" data-original-title=""></span>'.
											$eval['title'] .
										'</a>' ;

										echo $this->element('../Shares/partials/'.$share_file, ['type' => 'element', 'model' => 'ElementPropagate', 'workspace_id' => $val['id'], 'area_id' => $akey, 'element_id' => $eval['id'], 'ws_sign_off' => $ws_sign_off, 'task_sign_off' => $task_sign_off]);

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
}
		echo '</ul>';
		// end ws list

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


					<?php echo $this->Form->end(); ?>
				<?php } ?>

                </div>
            </div>
        </div>
	</div>
</div>

<script type="text/javascript" >
$(function() {
	// $('.tree_links').ellipsis_word()
})
</script>

<script type="text/javascript">
	$(function(){

		$.fn.toggleText = function(text1, text2) {
			($(this).text() === text1) ? $(this).text(text2) : $(this).text(text1);
			return this;
		}

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

	})
</script>