<?php
$dataOptions = ['ownerUser' => $ownerUser, 'propagatePermission' => $propagatePermission ];

// Set it to true to collapse all tree levels on page load.
// Also assign this value to the global js variable
$collapse = false;
?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
 ?>
<script type="text/javascript">
	jQuery(function($) {

		$js_config.start_collapse = '<?php echo $collapse; ?>';

		$js_config.share_action = '<?php echo (isset($share_action)) ? $share_action : 0; ?>';

		$('#show_profile_modal').on('hidden.bs.modal', function () {
	        $(this).removeData('bs.modal')//
	        $(this).find(".modal-content").html('');
	    });


	})
</script>

<style>

	.btn-select ul li {
		border-bottom: 1px solid #b5bbc8;
		padding: 5px 6px;
		text-align: left;
	}
	.btn-select-list span.text-value {
		display: inline-block;
		float:right;
		padding : 6px 0px;
	}

	.btn-select-list span:first-child {
		width: 90%;
	}


	.input-group-addon.btn-filter, .input-group-addon.btn-times, .input-group-addon.btn-progress {
		color: #fff;
		cursor: pointer;
	}
	.input-group-addon.btn-progress {
		border-color: #00acd6 !important;
		background-color: #00c0ef !important;
		display: none;
	}
	.input-group-addon.btn-filter {
		border-color: #478008 !important;
		background-color: #67a028 !important;
	}
	.input-group-addon.btn-times {
		border-color: #c12f1d !important;
		background-color: #dd4b39  !important;
	}


	.input-group.controls {
		border: 1px solid #cccccc;
		border-collapse: separate;
		display: table;
		float: right;
		position: relative;
		transition: all 0.6s ease-in-out 0s;
		width: 80px;
	}
	@media screen and (max-width: 1023px) {
		.mgbottom #select_user{
			margin-bottom: 10px;
		}
	}
	.show_profile {
		display: inline-block;
		padding: 5px 10px;
		cursor: default;
	}
	.show_profile i {
		cursor: pointer;
	}
	.show_profile i:hover {
		color: #333;
	}


	.multiselect.dropdown-toggle.btn.btn-white {
	    background-color: #fff !important;
	    color: #444 !important;
	    border-color: #ddd !important;
	}
	.multiselect.dropdown-toggle.btn .multiselect-selected-text {
	    font-size: 12px !important;
	}

	.mgbottom {
	    margin-bottom: 15px;
	    margin-left: -5px;
	}

	.input-label{
		font-weight:normal;
	}

</style>

<?php
echo $this->Html->css('projects/tooltip');
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/manage_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');

echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));

echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));
?>

<script type="text/javascript">
$(function(){

	$("input#skills").tokenfield({
		autocomplete: {
			// source: ['red','blue','green','yellow','violet','brown','purple','black','white'],
			source: function( request, response ) {
				var selectedSkills = $('#selectedSkills').val();

				if( request.term != '' && request.term.length > 2 ) {
					$.getJSON( $js_config.base_url + 'groups/get_skills', { term: request.term, selectedSkills: selectedSkills }, function(response_data) {
						var items = [];

						if( response_data.success ) {
                                if( response_data.content != null ) {
                                    $.each( response_data.content, function( key, val ) {
                                        var item ;
                                        item = {'label': val, 'value': key}
                                        items.push(item);
                                    });
                                    response(items)
                                }
						}
						else {
							response(items)
						}

						// cl(items)
					} );
				}
			},
			focus: function( event, ui ) {
                // $( ".project" ).val( ui.item.label );
                // console.log(ui.item.label);
				return false;
			},
			delay: 100
		},
		// limit: 1,
		showAutocompleteOnFocus: true,
		allowEditing: false,
		delimiter: ''
	})
	.on('tokenfield:createtoken', function (event) {
		var existingTokens = $(this).tokenfield('getTokens');
		$.each(existingTokens, function(index, token) {
			if (token.value === event.attrs.value)
			event.preventDefault();
		});
	})
	.on('tokenfield:createdtoken', function (event) {
		//$('.token-input').removeAttr('placeholder')
		var selectedSkills = $('#selectedSkills').val();
		if(selectedSkills != '') {
			var selectedSkills = selectedSkills.split(',');
		} else {
			var selectedSkills = [];
		}
		selectedSkills.push(event.attrs.value);

		selectedSkills = selectedSkills.join(',');
		$('#selectedSkills').val(selectedSkills);
	})
	.on('tokenfield:createdtoken tokenfield:removedtoken', function (event) {
		event.preventDefault()
		$('#get_users').trigger('click')
	})
	.on('tokenfield:removedtoken', function (event) {
		event.preventDefault();


		var selectedSkills = $('#selectedSkills').val();
		if(selectedSkills != '') {
			var selectedSkills = selectedSkills.split(',');
		} else {
			var selectedSkills = [];
		}
		selectedSkills.splice($.inArray(event.attrs.value, selectedSkills),1);

		selectedSkills = selectedSkills.join(',');
		$('#selectedSkills').val(selectedSkills);
		setTimeout(function(){
			if(selectedSkills != '') {
			//	$('.token-input').removeAttr('placeholder')
			}
			else{
			//	$('.token-input').attr('placeholder', 'Begin typing here to search for a Skill') ;
			}
		}, 100)
	})

	// clear skills field on page load
	$('#skills').tokenfield('setTokens', []);

	// clear skills field on click of clear skills button
	$('body').delegate('#clear_skills', 'click', function(event){
		event.preventDefault()
		$('#skills').tokenfield('setTokens', []);
		$('#skills-tokenfield').val('');
		$('#selectedSkills').val('');
		$('#get_users').trigger('click')
	})

	$('body').delegate('.token-input', 'blur', function(event){
		setTimeout(function(){
			$(this).attr('placeholder', 'Begin typing here to search for a Skill') ;
		}, 100)
	})

	$('body').delegate('.fa-times', 'click', function(event){
		event.preventDefault();
		$(this).prev('input.search_list').val('').focus();
		$(this).removeClass('fa-times').addClass('fa-times');
	})

	// Get users according to the skills entered
	$('body').delegate('#get_users', 'click', function(event){

		event.preventDefault();

		// set blank to select box label
		$('.btn-select .btn-select-value').text('Select User')

		var tokens = $('#skills').tokenfield('getTokens');

		$('#progress_bar').css({'display': 'table-cell'})
		var titems = [],
		params = {}
		url = '';
		var project_id = $js_config.project_id,
		userIds = $('#userIds').val()
		if( tokens.length ) {
			$.each(tokens, function(key, data){
				titems.push(data.value)
			} )
			params = {'project_id': project_id, 'skills': titems, 'userIds': userIds};
			url = $js_config.base_url + "groups/get_users_by_skills/" + project_id;
		}
		else {
			url = $js_config.base_url + "groups/get_users/" + project_id
			params = { 'project_id': project_id }
		}

		$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html('');

		$.ajax({
			url: url,
			type: "POST",
			data: $.param(params),
			dataType: "JSON",
			global: false,
			success: function (response) {

				if (response.success) {

					var search_handler = '<li class="search-handler">' +
													'<div class="input-group">' +
													'<input type="text" class="form-control input-search" placeholder="Search for User">' +
													'<span class="input-group-addon" style="opacity: 0">' +
												'</span>' +
											'</div>' +
										'</li>';

					var selectValues = response.content;

					$('#user_select').empty();
					console.log(selectValues)

					$('#user_select').append(search_handler)
					if( selectValues != null ) {
						$('#user_select').append(function() {
							var output = '';

							$.each(selectValues, function(key, value) {

								output += '<li data-value="'+key+'" >' +
											'<span class="text-value">'+value+'</span>' +
											'<span style="" class="show_profile text-maroon" data-remote="'+$js_config.base_url+'shares/show_profile/'+key+'"  data-target="#popup_modal" data-toggle="modal" href="#">' +
												'<i class="fa fa-user "></i>' +
											'</span>' +
										'</li>';

								// output += '<option value="' + key + '">' + value + '</option>';
							});
							return output;
						});
					}
					else {
						$('#user_select').empty().append(search_handler);
					}

				}
				else {

					var search_handler_no = '<li class="search-handler">' +
													'<div class="input-group">' +
													'<input type="text" class="form-control input-search" placeholder="No Users">' +
													'<span class="input-group-addon" style="opacity: 0">' +
												'</span>' +
											'</div>' +
										'</li>';

					$('#user_select').empty().append(search_handler_no);
				}

			},// end success
			complete: function() {
				$('#progress_bar').hide()
			}

		})// end ajax
	})


	$(window).on('resize', function(){
		$("#skills-tokenfield").css('width', '100%')
	})

	$('.input-group').keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
            event.preventDefault();
         }
    });

	$('.input-group').on("contextmenu",function(){
       return false;
    });

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
                            <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

                            <div class="modal modal-success fade " id="show_profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
                        </div>

		<div class="box-body clearfix" style="min-height: 800px">


				<?php if( isset($users_list) && !empty($users_list)) {

					$userIds = array_keys($users_list);
				?>
				<input type="hidden" id="userIds" name="userIds" value="<?php echo implode(",", $userIds); ?>" />
				<input type="hidden" id="projectId" name="projectId" value="<?php echo $project_id; ?>" />
				<?php
				}
				$url = array('controller' => 'shares', 'action' => 'index' );
				if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0])) {
					$url = array_merge($url, [$this->params['pass'][0]]);
				}
				?>


	<?php if( isset($shareUser) && !empty($shareUser)) {

		$user_detail = $this->ViewModel->get_user($shareUser, ['hasOne' => array('UserInstitution'), 'hasMany' => array('UserProject', 'UserPlan', 'UserTransctionDetail')], 1);

	?>

	<div class="panel panel-default" id="user_detail_section">
		<div class="panel-heading clearfix" style="padding: 10px">
			<span class="time pull-left" style="font-weight: 600;">
				Team Member: Role Level
			</span>
		</div>

		<div class="table-responsive" style="overflow-y: hidden;">
			<table class="table table-bordered">

				<tr>
					<th width="25%" class="text-left">Team Member</th>
					<th width="30%" class="text-left">Organization</th>
					<th width="15%" class="text-center">Active/Inactive</th>
					<th width="15%" class="text-center">Role Level</th>
					<th width="15%" class="text-center">Propagation</th>
				</tr>
				<tr>
					<td class="text-left"><?php echo $user_detail['UserDetail']['first_name'].' '. $user_detail['UserDetail']['last_name']; ?></td>
					<td class="text-left"><?php echo  !empty($user_detail['UserDetail']['org_name']) ? $user_detail['UserDetail']['org_name'] : "N/A";  //echo $user_detail['User']['email']; ?></td>
					<td class="text-center"><?php echo ($user_detail['User']['status']) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'; ?></td>
					<td class="text-center">

							<div class="btn-group toggle-group propogation-on-off">

								<a href=""  title="All Permissions Granted" data-container="body" class="tipText btn btn-xs btn-toggle toggle-on">Owner</i></a>

								<a href="" title="Set Sharing Permissions" data-container="body" class="tipText btn btn-xs btn-toggle toggle-off">Sharer</a>

							</div>

					</td>
					<td class="text-center">
						<div data-toggle="" class="toggle" style="">
							<input type="checkbox" name="share_permission" value="1">
							<div class="toggle-handle propogation-handle on option-disabled">
								<label class="btn btn-success btn-xs tipText" title="Further sharing is allowed">On</label>
								<label class="btn btn-danger btn-xs tipText" title="Further sharing not allowed">Off</label>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!--   stage 2 -->
	<?php echo $this->Form->create('ShareTree', array('url' => array('controller' => 'shares', 'action' => 'manage_sharing', $project_id, $shareUser, $share_action ), 'class' => 'formAddSharing', 'id' => 'frm_share_tree', 'enctype' => 'multipart/form-data')); ?>

<?php if(isset($this->params->query['refer']) && !empty($this->params->query['refer'])){
	echo $this->Form->input('ShareRefer.refer', array('type' => 'hidden', 'label' => false,'value'=>$this->params->query['refer'], 'div' => false, 'class' => 'form-control'));} ?>
	<div class="panel panel-default" id="share_section_submit">
		<div class="panel-heading clearfix" style="padding: 10px 10px;">
			<h5 class="">Project Sharing</h5>


			<span class="pull-right option-buttons">

				<div class="btn-group action-group">
					<a class="btn btn-sm action_buttons btn-primary" data-action="collapse_all" href="#" style="margin-left: 5px;">Collapse All</a>
					<a class="btn btn-sm action_buttons btn-primary" data-action="expand_all" href="#" >Expand All</a>
				</div>

					<a href="" class="btn btn-warning btn-sm hide" id="rst_share_tree">Toggle Tree</a>
					<a href="#" class="btn btn-success btn-sm" id="sbmt_share_tree" >Save</a>

			</span>
			<div class="sel-opt pull-right" style="display: inline-block; min-width: 200px;">
				<!-- <label>Group Permissions: </label> -->
				<select class="form-control" id="all_sharing_options" multiple="">
					<option value="read">Read</option>
					<option value="edit">Update</option>
					<option value="delete">Delete</option>
					<option value="copy" disabled="">Copy</option>
					<option value="move" disabled="">Cut/Move</option>
					<option value="add">Add Task</option>
				</select>
		    </div>

		</div>
	</div>


	<div class="panel panel-default collapse" id="share_detail_section">

		<div class="panel-body no-padding" id="">
			<div class="table-responsive" style="min-height: 300px">
				<table class="table">
					<tr>
						<th>
							<div class="shares-list">
								<ul>
									<li class="shares-tree-heading">
										<label>Path</label>
										<div>Permissions</div>
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

								if( isset($share_action) && !empty($share_action)) {
									echo $this->Form->input('Share.share_action', ['type' => 'hidden', 'value' => $share_action]);
								}

								echo $this->Form->input('Share.project_id', [ 'type' => 'hidden', 'value' => $project_id]);
								echo $this->Form->input('Share.user_id', ['type' => 'hidden', 'value' => $shareUser]);
		$project_detail = getByDbId('Project', $project_id);

		$prjWrks = $this->ViewModel->project_workspaces($project_id);

		$area_icons = '' .
				    '<label class="ap-permissions permit_read btn-circle btn-xs tipText" title="Read" data-class=".permit_read">' .
				        '<input type="checkbox" name="area_permit_read" value="" >' .
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
				'';
	 ?>
					<ul class="nav nav-list tree" >
					<?php


						$share_file = (isset($share_action) && !empty($share_action)  && $share_action == 1 ) ? 'share_icons_add' : 'share_icons_edit';

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

						echo  '<li class="has-sub-cat prj-list">';
							echo  '<a ' .
									' data-id="' . $prjData['id'] . '"' .
									' class="tipText tree-toggler nav-header tree_links">' .
										'<i class="'.$icon_class.'"></i> ' .
										'<span class="tipText tree_text" title="Project Status'.$prj_status_text.'" style="">'.strip_tags($project_detail['Project']['title']).'</span>' .
								'</a>' ;

							// show project icons
							echo $this->element('../Shares/partials/'.$share_file, ['type' => 'project', 'model' => 'ProjectPermission']);

						// start ws list
						echo '<ul class="nav nav-list tree" '.$display.' >';
					if( isset($prjWrks) && !empty($prjWrks) ) {
						foreach($wrkData as $key => $val) {
							// pr($val );
							$ws_sign_off = false;
							$adis = '';
							if(isset($val['sign_off']) && !empty($val['sign_off'])) {
								$ws_sign_off = true;
								$adis = 'disabled';
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

								echo  '<li class="has-sub-cat wsp-list">';
									echo  '<a ' .
											' data-id="' . $val['id'] . '"' .
											' class="tree-toggler nav-header tree_links">'.
											'<i class="'.$icon_class.'"></i>' .
											'<span class="tipText tree_text" title="Workspace Status'.$ws_status_text.'" style="">'.strip_tags($val['title']).'</span>' .
										'</a>';

										echo $this->element('../Shares/partials/'.$share_file, ['type' => 'workspace', 'model' => 'WorkspacePermission', 'workspace_id' => $val['id'], 'ws_sign_off' => $ws_sign_off] );

										$wsEls = $this->ViewModel->area_elements($areas);

										if( isset($wsEls) && !empty($wsEls) ) {

											// if elements exists, print area
											echo '<ul class="nav nav-list tree" '.$display.' >';

											foreach($val['area'] as $akey => $aval) {

												// get each area elements
												$arEls = $this->ViewModel->area_elements($akey);

												if( isset($arEls) && !empty($arEls) ) {

													echo  '<li class="has-sub-cat area-list">';
														echo  '<a ' .
																' data-id="' . $akey . '"' .
																' class="tree-toggler nav-header tree_links">'.
																'<i class="'.$icon_class.'"></i>' .
																'<span class="tipText tree_text area-title" title="Area" >'.strip_tags($aval).'</span>' .
															'</a>' ;

													if(!$ws_sign_off){
														echo '<div class="sharing-icon area-icons '.$adis.'">';
														echo($area_icons);
														echo '</div>';
													}
														// start el list
														echo '<ul class="nav nav-list tree" '.$display.' >';
													// pr($arEls, 1);
													foreach($arEls as $ekey => $evals) {
														$eval = $evals['Element'];
														// signoff pr($eval);
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
														echo  '<li class="has-sub-cat elm-list">';
														echo  '<a ' .
																' data-id="' . $eval['id'] . '"' .
																' title=""' .
																' class="tree-toggler nav-header tree_links">'.
																'<span class="ico_element pull-left"></span>'.
																'<span class="tipText tree_text" title="Task Status'.$ele_status_text.'">'.strip_tags($eval['title']).'</span>'.
															'</a>' ;

															echo $this->element('../Shares/partials/'.$share_file, ['type' => 'element', 'model' => 'ElementPermission', 'workspace_id' => $val['id'], 'area_id' => $akey, 'element_id' => $eval['id'], 'ws_sign_off' => $ws_sign_off, 'task_sign_off' => $task_sign_off]);

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
      	<div class="all-permit">Owner: All Permissions Granted</div>


						<?php echo $this->Form->end(); ?>
					<?php } ?>

	                </div>
	            </div>
	        </div>
		</div>
</div>

<script type="text/javascript">
	jQuery(function($) {
		// update sharing with propogation
		$('#all_sharing_options').on('change', function(event) {
			event.preventDefault();
			$('label.propogation').each(function(index, el) {
				$(this).removeClass('activate');
				$(this).find('input').prop('checked', false);
			});
		});

		if( $js_config.share_action !== undefined && $js_config.share_action > 0 ) {
			$js_config.start_collapse = false;
			if( $js_config.share_action == 1 ) {
				$('.action_buttons,.sel-opt').hide()
				if( !$('#share_detail_section').hasClass('collapse') ){
					$('#share_detail_section').addClass('collapse')
				}
				$('.all-permit').show();
			}
			else if( $js_config.share_action == 2 ) {

				$('.action_buttons,.sel-opt').show()
				if( $('#share_detail_section').hasClass('collapse') )
					$('#share_detail_section').removeClass('collapse')
				$('.all-permit').hide();
			}
		}
		else if( $js_config.start_collapse !== undefined  ) {
			$('.action_buttons,.sel-opt').hide()
			$js_config.start_collapse = false;

			if( !$('#share_detail_section').hasClass('collapse') ){
				$('#share_detail_section').addClass('collapse');
			}
			$('.all-permit').show();
		}

		// Set checked to false of all checkboxes on page load
		$('input[type=checkbox]').prop('checked', false);

		$('body').delegate('.propogation-on-off', 'click', function(event){
			setTimeout(function(){
				if( !$('#share_detail_section').is(':visible') ) {
					$('.all-permit').show();
					$('.option-buttons label.goto').hide();
					$('.option-buttons span.label-text').hide();
					// update sharing with propogation
					$('.toggle-handle').addClass('on').removeClass('off');
					$('[name="data[Share][share_permission]"]').prop('checked', true);
				}
				else {
					$('.all-permit').hide();
					$('.option-buttons label.goto').css({'display': 'inline-block'});
					$('.option-buttons span.label-text').show();
					// update sharing with propogation
					if($('.toggle-handle').hasClass('on')) {
						$("a.propogation").removeClass('not-editable').addClass('activated');
						$('.propogat_permisions').show("slide", { direction: "left" }, 500);
					}
				}
			},1000)
		})

		$('body').delegate('.goto', 'click', function(event) {
				var $t = $(this);
				console.log($t.attr('class'))
				if( $t.hasClass('checked') )
					$t.removeClass('checked')
				else
					$t.addClass('checked')
		})

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
				$area_input = $area_labels.find('input'),
				$all_el_labels = $child_ul.find('label.permissions').not('.disabled'),
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

			// update sharing with propogation
			var $prop_labels = $child_ul.find('label.propogation')
			$prop_labels.each(function(){
				$(this).removeClass('activate');
				$(this).find('input').prop('checked', false);
			})
		})

		// update sharing with propogation
		$('body').delegate('.area-icons label.ap-permissions', 'click', function(event) {
			event.preventDefault();
			var $this = $(this),
				$parent_ul = $(this).parents('.area-icons:first'),
				$parent_li = $parent_ul.parents('li.has-sub-cat:first'),
				$child_ul = $parent_li.find('ul.tree'),
				set_class = $(this).data('class')+'.permissions',
				$this_input = $(this).find('input');

			$this_input.prop("checked", !$this_input.prop("checked"));

			var $all_el_labels = $child_ul.find(set_class).not('.disabled'),
				$input = $all_el_labels.find('input');

			$input.each(function(){

				$(this).prop("checked", ($this.hasClass('active') ? false : true));

				if($(this).prop("checked")) {
					$(this).parent('label:first').addClass('active');
				}
				else {
					$(this).parent('label:first').removeClass('active');
				}
			})

			if($(this).hasClass('active')){
				$(this).removeClass('active');

				if( $(this).hasClass('permit_read')) {
					$parent_li.find('.permit_read.propogation').removeClass('activate');
					$parent_li.find('.permit_read.propogation').find('input[type=checkbox]').prop('checked', false);
				}
				if( $(this).hasClass('permit_edit')) {
					$parent_li.find('.permit_edit.propogation').removeClass('activate');
					$parent_li.find('.permit_edit.propogation').find('input[type=checkbox]').prop('checked', false);
				}
				if( $(this).hasClass('permit_delete')) {
					$parent_li.find('.permit_delete.propogation').removeClass('activate');
					$parent_li.find('.permit_delete.propogation').find('input[type=checkbox]').prop('checked', false);
				}
				if( $(this).hasClass('permit_copy')) {
					$parent_li.find('.permit_copy.propogation').removeClass('activate');
					$parent_li.find('.permit_copy.propogation').find('input[type=checkbox]').prop('checked', false);
				}
				if( $(this).hasClass('permit_move')) {
					$parent_li.find('.permit_move.propogation').removeClass('activate');
					$parent_li.find('.permit_move.propogation').find('input[type=checkbox]').prop('checked', false);
				}

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
					// $child_ul.find('.permit_copy,.permit_move').find('input').prop('checked', true);
				}
			}
			var active_length = ($parent_li.find('label.active.ap-permissions').length) ? $parent_li.find('label.active.ap-permissions').length : 0;

			if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent_li.find('.permit_read.ap-permissions').hasClass('active') ) {
				$parent_li.find('.permit_read.ap-permissions').addClass('active');
				$parent_li.find('.permit_read.ap-permissions').find('input[type=checkbox]').prop('checked', true);
				// set ON to all element read permissions
				$parent_li.find('.permit_read.permissions').not('.disabled').addClass('active');
				$parent_li.find('.permit_read.permissions').not('.disabled').find('input[type=checkbox]').prop('checked', true);

			}
			// if only one permission is given but its not the read permission
			else if( $(this).is($('.permit_read')) && active_length > 0 ) {

				$parent_li.find('.permit_read.ap-permissions').addClass('active');
				$parent_li.find('.permit_read.ap-permissions').find('input[type=checkbox]').prop('checked', true);
				// set ON to all element read permissions
				$parent_li.find('.permit_read.permissions').not('.disabled').addClass('active');
				$parent_li.find('.permit_read.permissions').not('.disabled').find('input[type=checkbox]').prop('checked', true);

			}

			if($(this).parents('.area-list:first').find('.ap-permissions.active').length > 0) {
				$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').not('.disabled').addClass('active');
				$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').not('.disabled').find('input').prop('checked', true);
			}

		})

	    $.all_sharing_options = $('#all_sharing_options').multiselect({
	        buttonClass: 'btn btn-white aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 0,
	        maxHeight: '318',
	        checkboxName: 'select_opts',
	        includeSelectAllOption: true,
	        enableFiltering: false,
	        enableCaseInsensitiveFiltering: false,
	        enableUserIcon: false,
	        nonSelectedText: 'Group Permissions',
	        allSelectedText: false,
	        onChange: function(element, checked) {

	        	$.toggle_one_permission(element, checked);
	        	if($(element).val() == 'edit' && checked) {
	        		$('[value="copy"]').removeAttr('disabled');
	        		$('[value="move"]').removeAttr('disabled');
	        		$('.multiselect-container input[value="copy"]').parents('li:first').removeClass('disabled');
	        		$('.multiselect-container input[value="move"]').parents('li:first').removeClass('disabled');
	        	}
	        	else if($(element).val() == 'edit' && !checked) {
	        		$('[value="copy"]').attr('disabled','disabled').prop('checked', false);
	        		$('[value="move"]').attr('disabled','disabled').prop('checked', false);
	        		$('#all_sharing_options [value="copy"]').prop("selected", false)
	        		$('#all_sharing_options [value="move"]').prop("selected", false)
	        		$('.multiselect-container input[value="copy"]').parents('li:first').removeClass('active').addClass('disabled');
	        		$('.multiselect-container input[value="move"]').parents('li:first').addClass('disabled').removeClass('active');
	        	}
	        },
	        onSelectAll: function(checked) {
	            if(checked) {
		            $('[value="copy"]').removeAttr('disabled').prop('checked', true);
		    		$('[value="move"]').removeAttr('disabled').prop('checked', true);
		    		$('.multiselect-container input[value="copy"]').parents('li:first').removeClass('disabled').addClass('active');
	    			$('.multiselect-container input[value="move"]').parents('li:first').removeClass('disabled').addClass('active');
	    			$('#all_sharing_options option').removeAttr('disabled').prop("selected", true);
	    			$('#all_sharing_options').multiselect('selectAll', true).multiselect('updateButtonText');
		    	}
		    	else {
		            $('[value="copy"]').attr('disabled', 'disabled').prop('checked', false);
		    		$('[value="move"]').attr('disabled', 'disabled').prop('checked', false);
		    		$('#all_sharing_options option').prop("selected", false)
		    		$('.multiselect-container input[value="copy"]').parents('li:first').addClass('disabled').removeClass('active');
	    			$('.multiselect-container input[value="move"]').parents('li:first').addClass('disabled').removeClass('active');
		    	}

		        if(checked) {
		        	var selected = ['read', 'edit', 'delete', 'copy', 'move', 'add'];
			        $.update_all_permissions(selected);
			    }
			    else{
			    	$.remove_all_permissions();
			    }
	        }
	    });

		$.toggle_one_permission = function(option, checked) {
			// On/Off clicked permission.
			// $('#all_sharing_options').multiselect('select', ['read', 'copy'] );
			var $option = $(option),
				permit_text = $option.val();
			if(!checked) {
				$('label.permissions.permit_'+permit_text).not('.disabled').each(function(index, el) {
					if( !$(this).hasClass('unchangable') ) {
						var $input = $(this).find('input[type=checkbox]');
						if(permit_text == 'edit') {
							var $parent = $(this).parents('.sharing-icon:first'),
								$copy_icon = $parent.find('label.permissions.permit_copy'),
								$move_icon = $parent.find('label.permissions.permit_move');

							if( $copy_icon.hasClass('active') ) {
								$copy_icon.removeClass('active');
								$copy_icon.find('input').prop("checked", false);
							}
							if( $move_icon.hasClass('active') ) {
								$move_icon.removeClass('active');
								$move_icon.find('input').prop("checked", false);
							}
							$input.prop("checked", false);
							$(this).removeClass('active');
						}
						else{
							$input.prop("checked", false);
							$(this).removeClass('active');
						}
					}
				});
			}
			else{
				$('label.permissions.permit_'+permit_text).not('.disabled').each(function(index, el) {
					if( !$(this).hasClass('unchangable') ) {
						var $input = $(this).find('input[type=checkbox]');
						if(permit_text == 'copy') {
							var $parent = $(this).parents('.sharing-icon:first'),
								$edit_icon = $parent.find('label.permissions.permit_edit');

							if( $edit_icon.hasClass('active') ) {
								$(this).addClass('active');
								$(this).find('input').prop("checked", true);
							}
						}
						else if(permit_text == 'move') {
							var $parent = $(this).parents('.sharing-icon:first'),
								$edit_icon = $parent.find('label.permissions.permit_edit');

							if( $edit_icon.hasClass('active') ) {
								$(this).addClass('active');
								$(this).find('input').prop("checked", true);
							}
						}
						else{
							$input.prop("checked", true);
							$(this).addClass('active');
						}
					}
				});
			}
		}

		$.remove_all_permissions = function() {
			// Remove all selected permission.
			$('label.permissions').each(function(index, el) {
				if( !$(this).hasClass('unchangable') ) {
					var $input = $(this).find('input[type=checkbox]');

					$input.prop("checked", false);
					$(this).removeClass('active');
				}
			});
		}

		$.update_all_permissions = function(selected) {
			if(selected.length <= 0) return;
			// return;
			for(var i = 0; i < selected.length; i++) {
				// console.log(selected[i])
				var permit_text = selected[i];
				$('label.permissions.permit_'+permit_text).not('.disabled').each(function(index, el) {
					if( !$(this).hasClass('unchangable') ) {
						var $input = $(this).find('input[type=checkbox]'),
							iName = $input.attr('name');
						if(permit_text == 'copy') {
							var $edit_icon = $('.sharing-icon').find('.permit_edit')
							if( $edit_icon.hasClass('active') ){
								$input.prop("checked", true);
								$(this).addClass('active');
							}
						}
						else {
							$input.prop("checked", true);
							$(this).addClass('active');
						}
					}
				});
			}
		}

		$('body').delegate('label.permissions', 'click', function(event) {
			//console.log($(this));
			var e = $(this);
			if( e.hasClass('unchangable') ) return;

			var $input = $(this).find('input[type=checkbox]');

			$input.prop("checked", !$input.prop("checked"));

			if($input.prop("checked")) {
				$(this).addClass('active');
			}
			else {
				$(this).removeClass('active');
			}

			var $parent = $(this).parent();

			var active_length = ($parent.find('label.active').length) ? $parent.find('label.active').length : 0;

			// if edit permission is to be deactivated then deactivate copy and move permissions also
			if( $(this).is($('.permit_edit')) ) {

				if( !$(this).hasClass( 'active' ) ) {

					// deactivate copy permission
					var $copy = $parent.find('.permit_copy')
					if( $copy.hasClass('active') ){
						$copy.removeClass('active');
						$copy.find('input[type=checkbox]').prop("checked", false);
					}

					// deactivate move permission
					var $move = $parent.find('.permit_move');
					if( $move.hasClass('active') ) {
						$move.removeClass('active');
						$move.find('input[type=checkbox]').prop("checked", false);
					}

				}

			}

			// if edit permission is not activated then restrict move and copy permissions
			if( ( $(this).hasClass('permit_move') ||  $(this).hasClass('permit_copy') ) &&  !$parent.find('.permit_edit').hasClass('active') ) {

				$(this).removeClass('active');
				$input.prop("checked", false);
				return;

			}

			// if clicked other than read permission and read button has not an active class
			// add it manually
			if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent.find('.permit_read.permissions').hasClass('active') ) {

				$parent.find('.permit_read.permissions').addClass('active');
				$parent.find('.permit_read.permissions').find('input[type=checkbox]').prop('checked', true);

			}
			// if only one permission is given but its not the read permission
			else if( $(this).is($('.permit_read')) && active_length > 0 ) {

				$parent.find('.permit_read.permissions').addClass('active');
				$parent.find('.permit_read.permissions').find('input[type=checkbox]').prop('checked', true);

			}

			if(!$input.prop("checked")) {
				if($(this).hasClass('permit_read')){
					$(this).parents('.sharing-icon:first').find('.propogation.permit_read').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_read').find('input[type=checkbox]').prop('checked', false);
				}
				if($(this).hasClass('permit_edit')){
					$(this).parents('.sharing-icon:first').find('.propogation.permit_edit').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_edit').find('input[type=checkbox]').prop('checked', false);
					$(this).parents('.sharing-icon:first').find('.propogation.permit_copy').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_copy').find('input[type=checkbox]').prop('checked', false);
					$(this).parents('.sharing-icon:first').find('.propogation.permit_move').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_move').find('input[type=checkbox]').prop('checked', false);
				}
				if($(this).hasClass('permit_delete')){
					$(this).parents('.sharing-icon:first').find('.propogation.permit_delete').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_delete').find('input[type=checkbox]').prop('checked', false);
				}
				if($(this).hasClass('permit_add')){
					$(this).parents('.sharing-icon:first').find('.propogation.permit_add').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_add').find('input[type=checkbox]').prop('checked', false);
				}
				if($(this).hasClass('permit_copy')){
					$(this).parents('.sharing-icon:first').find('.propogation.permit_copy').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_copy').find('input[type=checkbox]').prop('checked', false);
				}
				if($(this).hasClass('permit_move')){
					$(this).parents('.sharing-icon:first').find('.propogation.permit_move').removeClass('activate');
					$(this).parents('.sharing-icon:first').find('.propogation.permit_move').find('input[type=checkbox]').prop('checked', false);
				}
			}

			// console.log('area', $(this).parents('.area-list:first').find('.permissions'))
			if($(this).parents('.area-list:first').find('.permissions.active').length > 0) {
				$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').addClass('active');
				$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').find('input').prop('checked', true);
			}

			if($(this).hasClass('workspace') && $(this).hasClass('permit_read')) {
				if($(this).parents('.wsp-list:first').find('.permissions.active.element').length > 0) {
					$(this).addClass('active');
					$(this).find('input').prop('checked', true);
				}
			}
		})
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$.check_area_icons = function(label) {
			var $label = $(label),
				$parent_ul = $label.parents('.nav.nav-list.tree:first'),
				$parent_li = $parent_ul.parents('.has-sub-cat:first'),
				$area_icons = $parent_li.find('.area-icons'),
				read_checked = $parent_ul.find('.permit_read.active').length,
				read_all = $parent_ul.find('.permit_read').length,
				edit_checked = $parent_ul.find('.permit_edit.active').length,
				edit_all = $parent_ul.find('.permit_edit').length,
				dele_checked = $parent_ul.find('.permit_delete.active').length,
				dele_all = $parent_ul.find('.permit_delete').length,
				copy_checked = $parent_ul.find('.permit_copy.active').length,
				copy_all = $parent_ul.find('.permit_copy').length,
				move_checked = $parent_ul.find('.permit_move.active').length,
				move_all = $parent_ul.find('.permit_move').length;

			if($parent_ul.find('.permit_read.active').length == $parent_ul.find('.permit_read').length) {
				$area_icons.find('.permit_read').addClass('active')
			}
			else {
				$area_icons.find('.permit_read').removeClass('active')
			}

			if($parent_ul.find('.permit_edit.active').length == $parent_ul.find('.permit_edit').length) {
				$area_icons.find('.permit_edit').addClass('active')
			}
			else {
				$area_icons.find('.permit_edit').removeClass('active')
			}

			if($parent_ul.find('.permit_delete.active').length == $parent_ul.find('.permit_delete').length) {
				$area_icons.find('.permit_delete').addClass('active')
			}
			else {
				$area_icons.find('.permit_delete').removeClass('active')
			}

			if($parent_ul.find('.permit_copy.active').length == $parent_ul.find('.permit_copy').length) {
				$area_icons.find('.permit_copy').addClass('active')
			}
			else {
				$area_icons.find('.permit_copy').removeClass('active')
			}

			if($parent_ul.find('.permit_move.active').length == $parent_ul.find('.permit_move').length) {
				$area_icons.find('.permit_move').addClass('active')
			}
			else {
				$area_icons.find('.permit_move').removeClass('active')
			}

			// console.log(read_all, read_checked)
		}

		$('body').delegate('#add_sharing', 'click', function(event) {
			event.preventDefault();
			var $form = $('#frm_share_user'),
			$user_input = $form.find('#share_user_id'),
			$share_action = $form.find('#share_action'),
			user_id = 0;

			// $share_action.val(1);

			if( $user_input.length > 0 ) {
				user_id = $user_input.val();
			}

			if( user_id <= 0 ) {
				if( $form.find('.error').length )
				$form.find('.error').remove();

				var $span = $('<span>')
				.attr('class', 'error')
				.text('Please select a user.')

				$("#select_user").append($span)
			}
			else {

				$form.submit()

			}

			return false;
		})

		function resizeStuff() {
			//$('.tree_links').ellipsis_word();
		}
		resizeStuff()
		var TO = false;
		$(window).on('resize', function(){

			if(TO !== false) {
				clearTimeout(TO);
			}

			TO = setTimeout(resizeStuff, 800); //800 is time in miliseconds
		});

		$.click_count = 0;
		$( "#rst_share_tree" ).on( "options:toggle", function( event ) {
			event.preventDefault();
			var $that = $( this );

			if( $('#share_detail_section').hasClass('in') ) {
				$that.trigger( "options:off" );
			} else {
				$that.trigger( "options:on" );
			}

		})
		.on( "options:on", function( event ) {
			event.preventDefault();
			$('#share_detail_section').addClass('in')
			$('.action_buttons').show('slow')
			$('.sel-opt').show('slow')
		})
		.on( "options:off", function( event ) {
			event.preventDefault();
			$('#share_detail_section').removeClass('in')
			$('.action_buttons').hide('slow')
			$('.sel-opt').hide('slow')
		});


		$('body').delegate('#rst_share_tree', 'click', function(event){
			event.preventDefault();
			$(this).trigger( "options:toggle");
		})

		$('body').delegate('#sbmt_share_tree', 'click', function(event){
			event.preventDefault();

			var $t = $(this),
			$that = $('#frm_share_tree');
			$propogation_switch = $('input[name=share_permission]');

			$t.addClass('disabled');
			$t.css('pointer-events','none');


			var $last = $('.toggle-group.propogation-on-off').children('.btn-toggle:last-child');

			// PROPOPGATION ON/OFF SWITCH VALUE
			// STORE IN OTHER FIELD THAT IS UNDER THIS FORM
			// CREATE IT DYNAMICALLY AFTER REMOVE
			if( $last.hasClass('toggle-on') ) {

				var projectPermitIcons = $('#sharing_list li.has-sub-cat:first .sharing-icon:first .permissions.active');
				if( projectPermitIcons.length <= 0 ) {
					$.modal_alert('Please select atleast one project permission before submit.', 'Information', 'btn-sm')
				}

				$("<input>")
				.attr("type", "hidden")
				.attr("id", "project_level")
				.attr("name", "data[Share][project_level]")
				.val('0')
				.appendTo($that);

				if( $propogation_switch.prop('checked') ) {
					$("<input>")
					.attr("type", "hidden")
					.attr("id", "share_permission")
					.attr("name", "data[Share][share_permission]")
					.val('1')
					.appendTo($that);
				}
			}
			else {
				$("<input>")
				.attr("type", "hidden")
				.attr("id", "project_level")
				.attr("name", "data[Share][project_level]")
				.val('1')
				.appendTo($that);
			}
			var formData = $that.serializeArray()
			$.socket.emit("project:share", {creator: $js_config.USER.id, project: $js_config.project_id, sharer: '<?php echo $shareUser; ?>'});
			// All Done!!!
			// Submit Form
			$that.submit()
			// return true;
		})

		setTimeout(function(){

			// CHECK ALL SELECTED PERMISSIONS AND SET CHECKED TO ASSOCIATED CHECKBOX TO TRUE
			$('.permissions.active').each( function(i, j) {
				var $t = $(this),
				$checkbox = $t.find('input[type=checkbox]');
				if( $checkbox.length )
				$checkbox.prop('checked', true);
			})
		}, 600)
	})
	$(window).on('load', function(event) {
		setTimeout(function(){
			// $('input[type=checkbox][name=sharing_level]').prop('checked', true)
			$('.tipText.tree_text').tooltip({ container: 'body', placement: 'top'})
			$('#sharing_list li.has-sub-cat:first .sharing-icon:first .permissions.permit_read').addClass('unchangable active');
			$('#sharing_list li.has-sub-cat:first .sharing-icon:first .permissions.permit_read.unchangable').find('input[type=checkbox]').prop('checked', true);
		}, 600)
	})

</script>

<script type="text/javascript" >
	// update sharing with propogation
$(function() {

	/*
	--------------------------------------------------------------------------
	PROPOGATION ON/OFF INPUT+ICON EVENTS
	--------------------------------------------------------------------------
	*/
	$('body').delegate('.propogation-handle', 'click', function(event){
		event.preventDefault();

		var $this = $(this);
		var $chk = $(this).parent().find('input[type=checkbox]')
		$chk.prop('checked', false)

		if($(this).hasClass('option-disabled')){
			return;
		}

		if($(this).hasClass('on')){
			BootstrapDialog.show({
                title: 'Propagation',
                type: BootstrapDialog.TYPE_DANGER,
                message: 'Are you sure you want to deny propagation?<br /><br />This Team Member will not be allowed to share onwards.',
                draggable: true,
                buttons: [{
                    label: 'Deny',
                    // icon: 'fa fa-times',
                    cssClass: 'btn-success',
                    action: function(dialogRef) {
                        $this.addClass('off').removeClass('on');
                        $("a.propogation").addClass('activated');
						$('.propogat_permisions').slideFadeToggle(500, 'linear' );
                        dialogRef.close();
                    }
                },{
                    label: 'Cancel',
                    // icon: 'fa fa-times',
                    cssClass: 'btn-danger',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                }]
            });
		}
		else {
			$this.addClass('on').removeClass('off');
			$chk.prop('checked', !$chk.prop('checked'))
			$("a.propogation").removeClass('activated');
			$('.propogat_permisions').slideFadeToggle(500, 'linear' );
		}



		// TOGGLE PROPOGATION ICON AND CHECKBOX TO ON/OFF
		// if( !$(this).hasClass('option-disabled'))
		// 	$(this).toggleClass('on off', 300, "easeOutSine");

		// var $chk = $(this).parent().find('input[type=checkbox]')
		// $chk.prop('checked', false)
		// if($(this).hasClass('on')) {
		// 	$chk.prop('checked', !$chk.prop('checked'))
		// }

	})

	/*$('.propogation-handle').on('click', function(event){
		event.preventDefault();
		// console.log( $("a.propogation").attr('class'))
		// console.log( $("a.propogation") )
		// $("a.propogation").toggleClass('not-editable');
		// $("a.propogation").toggleClass('activated');
		// $('.propogat_permisions').slideFadeToggle(500, 'linear' );
	})*/
	// HIDE/SHOW PROPOPGATION ICON BOX
	/*$('body').delegate(".propogation", 'click', function(event){
		event.preventDefault();
		var $this = $(this);

		var $propogat_wrapper = $(this).parent('.propogation-wrapper'),
			$pro_perm = $propogat_wrapper.find('.propogat_permisions'),
			$pro_perm_inner = $pro_perm.find('.options-inner');


		$(this).toggleClass('activated')
		// $pro_perm.slideFadeToggle(300, 'swing', deactivate_icons );
		// $pro_perm.slideFadeToggle(1500, 'easeOutElastic' );
		$pro_perm.slideFadeToggle(200, 'linear' );
		// $pro_perm.slideToggle();

	})*/
	$('label.propogation').on('click', function(event){
		event.preventDefault();
		var $top_parent = $(this).parents('.sharing-icon:first'),
			$pread = $top_parent.find('.permissions.permit_read'),
			$pedit = $top_parent.find('.permissions.permit_edit'),
			$pdelete = $top_parent.find('.permissions.permit_delete'),
			$padd = $top_parent.find('.permissions.permit_add'),
			$pcopy = $top_parent.find('.permissions.permit_copy'),
			$pmove = $top_parent.find('.permissions.permit_move'),
			$parent = $top_parent.find('.propogat_permisions');

		if( !$pread.hasClass('active')  && $(this).hasClass('permit_read')) {
			return;
		}
		if(!$pedit.hasClass('active') && $(this).hasClass('permit_edit')) {
			return;
		}
		if(!$pdelete.hasClass('active') && $(this).hasClass('permit_delete')) {
			return;
		}
		if(!$padd.hasClass('active') && $(this).hasClass('permit_add')) {
			return;
		}
		if(!$pcopy.hasClass('active') && $(this).hasClass('permit_copy')) {
			return;
		}
		if(!$pmove.hasClass('active') && $(this).hasClass('permit_move')) {
			return;
		}

		var $input = $(this).find('input[type=checkbox]');
		$input.prop("checked", !$input.prop("checked"));

		if($input.prop("checked")) {
			$(this).addClass('activate');
		}
		else {
			$(this).removeClass('activate');
		}

		if( $(this).is($('.permit_edit')) ) {

			if( !$(this).hasClass( 'activate' ) ) {

				// deactivate copy permission
				var $copy = $parent.find('.permit_copy')
				if( $copy.hasClass('activate') ){
					$copy.removeClass('activate');
					$copy.find('input[type=checkbox]').prop("checked", false);
				}

				// deactivate move permission
				var $move = $parent.find('.permit_move');
				if( $move.hasClass('activate') ) {
					$move.removeClass('activate');
					$move.find('input[type=checkbox]').prop("checked", false);
				}

			}

		}

		// if edit permission is not activated then restrict move and copy permissions
		if( ( $(this).hasClass('permit_move') ||  $(this).hasClass('permit_copy') ) &&  !$parent.find('.permit_edit').hasClass('activate') ) {
			$(this).removeClass('activate');
			$input.prop("checked", false);
			return;
		}

		var active_length = ($parent.find('label.activate').length) ? $parent.find('label.activate').length : 0;
		// if clicked other than read permission and read button has not an activate class
		// add it manually
		if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent.find('.permit_read').hasClass('activate') ) {

			$parent.find('.permit_read.propogation').addClass('activate');
			$parent.find('.permit_read.propogation').find('input[type=checkbox]').prop('checked', true);
		}
		// if only one permission is given but its not the read permission
		else if( $(this).is($('.permit_read.propogation')) && active_length > 0 ) {

			$parent.find('.permit_read.propogation').addClass('activate');
			$parent.find('.permit_read.propogation').find('input[type=checkbox]').prop('checked', true);
		}
	})
})
</script>

<style>
	.option-buttons label.goto, .option-buttons span.label-text {
			display: none;
	}
	.sharing-help .applied_permissions {
		margin-right: 3px !important;
	}
	.search-select li a i{
	visibility:visible !important;
	}
	.mgbottom .btn.btn-success {
	  font-size: 12px;
	  padding: 5px 10px;
	}
	.not-editable {
		cursor: not-allowed;
		pointer-events: none;
	}
	label.propogation {
	    background: #cecece none repeat scroll 0 0;
	    color: #939393;
	    display: inline-block;
	    margin: 0 !important;
	    text-align: center;
	    cursor: pointer;
	    transition: all 0.5s ease-in-out;
	    border: 2px solid rgb(255, 255, 255);
    	padding: 3px 0 !important;
	}
</style>