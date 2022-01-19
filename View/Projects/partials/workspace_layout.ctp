<script type="text/javascript" >
 $(document).ready(function(){

	$('.area_info').tooltip({
		placement : 'bottom',
		container : 'body',
	});
	// $('#ajax_overlay').show();
	$('.pophover_txt').popover({
        placement : 'top',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

})
</script>
<style type="text/css">
	#workspace td .box-body.over {
		/*background: #f5efda none repeat scroll 0 0;*/
		border: 1px dashed #d9a602;
		transition: all 0.3s ease 0s;
	}

	#workspace td .box-body.drop_element {
		position: relative;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
		align-content: flex-start;
		flex-direction: row;
	}
	#workspace td .box-body.drop_element .el.panel{
		border: 1px solid #80848c;
		border-radius: 4px;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-flow: column;
		-webkit-flex-flow: column;
		flex-flow: column;
	}
	#workspace td .box-body.drop_element .el.panel .inner-el {
		width: 100%;
		border: 1px solid #80848c;
		border-radius: 4px;
	}

	.area_elements_toggle {
		min-width: 42px;
		min-height: 30px;
	}
	.area_elements_toggle .fa-bars {
		transition: opacity .3s, transform .3s;
	}

	.area_elements_toggle .fa-th-large {
		transition: opacity .3s, transform .3s;
		transform: rotate(-180deg) scale(1);
		position: absolute;
		top: 12px;
    	left: 18px;
	}

	/*.area_elements_toggle:hover .fa-th-large {
		transform: rotate(0deg) scale(1);
		opacity: 1;
	}
	.area_elements_toggle:hover .fa-bars {
		transform: rotate(180deg) scale(.5);
		opacity: 0;
	}*/
</style>


<?php echo $this->Html->script( 'projects/plugins/editInPlace', array( 'inline' => true ) ); ?>

<?php
$elements_details = [];
if( isset( $data['templateRows'] ) && !empty( $data['templateRows'] ) ) {  ?>
<div class=" ">
	<table class="table table-bordered " id="tbl" style="table-layout: fixed;">

		<tbody>
			<tr>
					<?php
					$max_boxes = max( array_map( 'count', $data['templateRows'] ) );
					$setWidth = 0;
					$row_group = $data['templateRows'];
					foreach( $row_group as $row_id => $row_data ) {
						$setWidth++;

						foreach( $row_data as $row_index => $row_detail ) {
							$last = false;
							$colspan = $rowspan = '';

							if( $row_detail['size_w'] > 0 && $row_detail['size_h'] > 0 ) {
								if( $row_detail['size_w'] > 1 ) {
									$colspan = ' colspan="' . $row_detail['size_w'] . '" ';
								}
								if( $row_detail['size_h'] > 1 ) {
									$rowspan = ' rowspan="' . $row_detail['size_h'] . '" ';
								}
							}

							$tdWidth = 0;
							if( isset( $setWidth ) && !empty( $setWidth ) ) {
								$tdWidth = ( 100 / $max_boxes );
								$tdWidth = number_format( $tdWidth, 4 );
								$tdWidth = $tdWidth . '%';
							}
							?>
					<td <?php echo (!empty( $colspan )) ? $colspan : ''; ?>
						<?php echo (!empty( $rowspan )) ? $rowspan : ''; ?> valign="top"
						data-elem="<?php echo (isset($row_detail['elements_counter']) && !empty($row_detail['elements_counter']))? $row_detail['elements_counter']:1;  ?>"
						class="area_box" data-pid="<?php echo $project_id ?>" id="<?php echo $row_detail['area_id']; ?>"
						style="" <?php if( $setWidth == 1 ) { ?>
						width="<?php echo $tdWidth; ?>" <?php } ?> >

						<div class="box box-success box-area no-margin">
							<div class="box-header">

								<a href="#"
									class="btn btn-xs area_elements_toggle el-toggle-tooltip"
									data-toggle="tooltip" data-trigger="hover"
									data-placement="right" role="tooltip"
									data-original-title="Toggle All Tasks" style=" float: left;">
									<i class="fa fa-fw fa-bars icon-default" style="font-size: 24px;"></i>
									<!-- <i class="fa fa-fw fa-th-large icon-hover" style="font-size: 24px;"></i> -->
								</a>
								<span class="popover-markup" style="display: block; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; margin-right: 52px; vertical-align: top;">

									<h3 class="box-title area-box-title truncate" id="area_<?php echo $row_detail['area_id'] ?>-toggler">

										<a href="javascript:;"
											data-pop-form="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'popover', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
											data-remote="<?php echo Router::Url( array( 'controller' => 'workspaces', 'action' => 'update_area', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
											rel="areaInfo_<?php echo $row_detail['area_id']; ?>" id="<?php echo $row_detail['area_id']; ?>" class="area_title"  data-full-title="<?php echo $row_detail['title']; ?>"> <?php echo $row_detail['title']; ?></a>
								</h3>

								<div id="content_<?php echo $row_detail['area_id']; ?>" class="popover_content" style="display: none">
									<p>
										<input type="text" id="txtTitle" name="data[Area][title]" value="<?php echo $row_detail['title'] ?>" class="form-control input_holder" />
									</p>
									<p class="text-center">
										<input type="submit" name="Submit" value="Update" id="" class="btn btn-jeera-submit" />
										<button type="submit" onclick="" class="btn btn-jeera-dismiss">Cancel</button>
									</p>
								</div>

								</span>

								<a class="my-menu toggle_area_menus" href="#" style="position: absolute; right: 10px; top: 10px; margin: 0;" >
									<span class="fa fa-list"></span>
								</a>

								<div class="pull-right area_menus" style="position: absolute; right: 11px; top: 14px;">

									<a href="#" class="btn btn-default btn-xs btn-paste fade-out dropdown-toggle" data-toggle="dropdown" style="position: relative; ">
										<i class="far fa-clone"></i>
									</a>
									<ul class="dropdown-menu paste-menus">
										<li><a href="#" name="paste" id="paste"><i class="fa fa-paste"></i> Paste</a></li>
										<li><a href="#" class="cancel-paste" style="color: #c00 !important;"><i class="fa fa-times"></i> Cancel</a></li>
									</ul>


									<div class="btn-group ">
										<?php
										$user_id = $this->Session->read('Auth.User.id');

										$wsp_permission = $this->Common->wsp_permission_details($this->ViewModel->workspace_pwid($workspace_id), $project_id,$this->Session->read('Auth.User.id'));

										$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

										$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

										$grp_id = $this->Group->GroupIDbyUserID($project_id,$user_id);

										if(isset($grp_id) && !empty($grp_id)){

										$group_permission = $this->Group->group_permission_details($project_id,$grp_id);
											if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
												$project_level = $group_permission['ProjectPermission']['project_level'];
											}
											$wsp_permission = $this->Group->group_wsp_permission_details($this->ViewModel->workspace_pwid($workspace_id),$project_id,$grp_id);
										}

										$message = '';


										if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) || (isset($wsp_permission['0']['WorkspacePermission']['permit_add']) && $wsp_permission['0']['WorkspacePermission']['permit_add']) ) {

											$data['workspace']['Workspace']['start_date'] = date('Y-m-d',strtotime($data['workspace']['Workspace']['start_date']));
											$data['workspace']['Workspace']['end_date'] = date('Y-m-d',strtotime($data['workspace']['Workspace']['end_date']));

										if($data['workspace']['Workspace']['sign_off'] !=1){

										$curdate =  $this->Wiki->_displayDate(date("Y-m-d h:i:s A"),$format = 'Y-m-d');
										$wspStartDate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['workspace']['Workspace']['start_date'])),$format = 'd M, Y h:i:s A');

										if( FUTURE_DATE == 'on' ){

										?>
										<button
											class="btn btn-success text-white btn-xs tipText add_element "
											data-original-title="Quick Task Create"
											data-toggle="modal" data-target="#popup_modal"
											id="add_element"
											data-area="<?php echo $row_detail['area_id']; ?>"
											data-id="<?php echo $row_detail['area_id']; ?>"
											data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>">
											<i class="fa fa-plus"></i>
										</button>

										<?php } else {

												if((isset($data['workspace']['Workspace']['end_date']) && $data['workspace']['Workspace']['end_date'] >= date('Y-m-d')) && (isset($data['workspace']['Workspace']['start_date']) && $data['workspace']['Workspace']['start_date'] <= $curdate )){ ?>

												<button
													class="btn btn-success text-white btn-xs tipText add_element "
													data-original-title="Quick Task Create"
													data-toggle="modal" data-target="#popup_modal"
													id="add_element"
													data-area="<?php echo $row_detail['area_id']; ?>"
													data-id="<?php echo $row_detail['area_id']; ?>"
													data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>">
													<i class="fa fa-plus"></i>
												</button>
										<?php 	}
											}

											if( FUTURE_DATE == 'off' ){
												 if((isset($data['workspace']['Workspace']['start_date']) && $data['workspace']['Workspace']['start_date'] > $curdate ) ){

													$message ="Workspace schedule has not reached the start date.";
												 }
											}

											if((isset($data['workspace']['Workspace']['end_date']) && $data['workspace']['Workspace']['end_date'] < $curdate ) ){

												$message ="You cannot add an Task because Workspace end date has passed.";
										 	}

										}
										 else if(isset($data['workspace']['Workspace']['start_date'])){
										 	$message ="You cannot add an Task because Workspace has Signoff.";
										 }


										if(!isset($data['workspace']['Workspace']['start_date'])){
	                                        $message ="Please add a schedule to this workspace first.";
										}

										}


										if(isset($message) && !empty($message)){
										 ?>

										 <button data-title="<?php echo $message;?>"
											class="btn btn-success text-white btn-xs workspace tipText add_element disable"
											data-original-title="Quick Task Create" id="add_element"
											data-area="<?php echo $row_detail['area_id']; ?>"
											data-id="<?php echo $row_detail['area_id']; ?>"
											data-remote="">
											<i class="fa fa-plus"></i>

										</button>
										<?php
										}
										$tooltip_text = ( isset( $row_detail['tooltip_text'] ) && !empty( $row_detail['tooltip_text'] ) ) ? $row_detail['tooltip_text'] : 'N/A';

										?>
										<button class="btn btn-default btn-xs pophover_txt area_info"
											data-content="<?php echo htmlentities($tooltip_text); ?>" >
											<i class="fa fa-fw fa-info"></i>
										</button>

									</div>
								</div>
							</div>
						</div>

						<script type="text/javascript" >
						        $(document).on("click", ".workspace", function(e) {
						            e.preventDefault()
						            var message = $(this).attr("data-title");
									  $(".modal-body").html('<p>'+message+'</p>');
						            $.model = $("#modal-alert")
						            $.model.modal('show');
						        });
						</script>
						<div id="modal-alert" class="modal fade">
						  <div class="modal-dialog modal-md">
						    <div class="modal-content border-radius-top">
						        <div class="modal-header border-radius-top bg-red">
						        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						        <i class="fa fa-exclamation-triangle"></i>&nbsp;Warning
						        </div>
						      <!-- dialog body -->


						      <div class="modal-body">
						        <button type="button" class="close" data-dismiss="modal">&times;</button>
						        Please set project dates before setting workspace dates.
						      </div>
						      <!-- dialog buttons -->
						      <div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">OK</button></div>
						    </div>
						  </div>
						</div>
						<div class="box-body clearfix in drop_element">
							<div class="ovrelay"></div>

								<?php
								// CREATE ELEMENTS OF EACH AREA IF EXISTS
								if( isset( $row_detail['elements'] ) && !empty( $row_detail['elements'] ) ) {

									$elements_data = $row_detail['elements'];
									foreach( $elements_data as $element_index => $element_detail ) {
									?>
									<?php
										$response = [ 'id' => $element_detail['Element']['id'], 'data' => ['Element' => $element_detail ] ];
										?>
									<?php
									}
								}
								?>

								<?php
								$fstatus = false;
								if(isset($statuses) && !empty($statuses)){
									$fstatus = $statuses;
								}
								$area_detail = getByDbId('Area', $row_detail['area_id']);
								echo $this->element('../Projects/partials/area_elements', [ 'area_id' => $row_detail['area_id'], 'area_detail' => $area_detail, 'user_project' => $user_project, 'p_permission' => $p_permission, 'fstatus' => $fstatus]); ?>
							</div>
						</td>
							<?php
						}
						// end first foreach
						// End table row started just after first foreach
						// It prints till the total number of rows reaches
						$row_group_tot = ( isset($row_group) && !empty($row_group) ) ? count( $row_group ) - 1 : 0;
						if( $row_id < $row_group_tot )
							echo '</tr><tr>';
					} // end second foreach
					?>
		</tbody>
	</table>
<?php }  ?>
</div>

<?php
$areaElements = null;
if( isset( $areas ) && !empty( $areas ) ) {
	foreach( $areas as $k => $v ) {

		$elements_details_temp = null;
			if((isset($e_permission) && !empty($e_permission)))
			{
				$all_elements = $this->ViewModel->area_elements_permissions($v['Area']['id'], false,$e_permission);
			}

			if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
				$all_elements = $this->ViewModel->area_elements($v['Area']['id']);
			}

			if( isset( $all_elements ) && !empty( $all_elements ) ) {

				foreach( $all_elements as $element_index => $e_data ) {

					$element = $e_data['Element'];

					$element_decisions = $element_feedbacks = [];
					if( isset($element['studio_status']) && empty($element['studio_status']) ) {
						$element_decisions = _element_decisions( $element['id'], 'decision' );
						$element_feedbacks = _element_decisions( $element['id'], 'feedback' );
						$element_statuses = _element_statuses( $element['id'] );

						$self_status['self_status'] = element_status($element['id']);


						$element_assets = element_assets( $element['id'], true );
						$arraySearch = arraySearch( $all_elements, 'id', $element['id'] );

						if( isset( $arraySearch ) && !empty( $arraySearch ) ) {
							$elements_details_temp[] = array_merge( $arraySearch[0], $element_assets, $element_decisions, $element_feedbacks, $element_statuses, $self_status );
						}
					}
				}

				$areaElements[$v['Area']['id']]['el'] = $elements_details_temp;
			}
	}
}

?>


<script type="text/javascript" >

	$(function () {

		$(".area_box").each(function () {
			$(this).data("clonedObject", {});
		})

		$('body').delegate('#area_tooltip_text', 'keyup focus', function(e){
			var characters = 250;
			var $parent = $(this).parents('.form-group.col-md-12:first');
			var error_el = $();
			if($parent.find('.error').length <= 0){
				$error_el = $('<span />', {'class': 'error chars_left'});
				$parent.append($error_el)
			}
			else{
				$error_el = $parent.find('.error.chars_left');
			}

            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
		})

		$('body').delegate('#inpAreaTitle', 'keyup focus', function(e){
			var characters = 100;
			var $parent = $(this).parents('.input-group.has-loader.margin-tb:first');
			var error_el = $();
			if($parent.find('.error').length <= 0){
				$error_el = $('<span />', {'class': 'error chars_left'});
				$parent.append($error_el)
			}
			else{
				$error_el = $parent.find('.error.chars_left');
			}

            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
		})

		$.on_spot('.area_info', {
			debug: true, // Turn logging on/off
			on: 'click', // The event to trigger
			duration: 500, // The time to show/hide
			type: 'area_info',
		})

		// SET ALL DATA PASSED FROM PHP SCRIPT ON PAGE LOAD
		$js_config.elements_details = <?php echo json_encode( $areaElements ); ?>;

		$.data_form_btn = $();
		$('*[data-pop-form]').on('click', function () {
			var e = $(this);
			$.data_form_btn = e;
			// $(".fix-progress-wrapper").data_loader()
			var p = e.parents("td:first").find('.box');

			$('.popover').remove()
			if(e.data('bs.popover')) {
				e.popover('destroy');
			}
			setTimeout(function(){

				$.get(e.data('pop-form'), function (d) {
					e.removeData('bs.popover')
					$('.popover').remove()
					e.popover('destroy').popover({
							content: d,
							html: true,
							placement: 'bottom',
							container: 'body',
							title: function() {
								return 'Update Title <span onclick="$(this).parent().parent().hide();" class="close">&times;</span>';
							}}
						).popover('show');
				});
			}, 300)

		})

		$('body').delegate("#popover_close", 'click', function (event) {
			var $el = $(this),
					$popover = $el.parents(".popover:first");
			$popover.hide()
		})



		$("body").delegate(".summary_icons ul li span.btn:not(.blocked)", 'click', function (event) {
			$.save_filter_cookie();
			var $span = $(this),
					data = $span.data(),
					URL = data.remote;

			if (URL.trim() !== '') {
				window.location.href = URL
			}
		})
		$("body").delegate(".summary_icons ul li span.btn.blocked", 'click', function (event) {
			console.log('Permission Denied!!!')
		})

		$("body").delegate(".toggle_area_menus", 'click', function (event) {
			event.preventDefault()
			if( $(this).parents('.box-header').find('.area_menus').is(":visible") )
				$(this).parents('.box-header').find('.area_menus').hide('slow');
			else
				$(this).parents('.box-header').find('.area_menus').show('slow');
		})

		$("body").delegate(".toggle_top_menus", 'click', function (event) {
			event.preventDefault()
			var $el = $(this).parents(".el:first"),
				elData = $el.data(),
				$hdrOpt = $(this).parents(".panel-heading:first").find('.elbox_options'),
				$flash = $hdrOpt.find('a.flash'),
				$hdrOptOthers = $('.elbox_options').not($hdrOpt[0]);

				$hdrOptOthers.fadeOut('slow')

			if( !elData.permit_add && !elData.permit_copy && !elData.permit_delete && !elData.permit_edit && !elData.permit_move ) {
				if( elData.project_level == 0 )	{
					$hdrOpt.find('.flash').addClass('disabled')
				}

			}

			if( $hdrOpt.is(":visible") ) {
				$hdrOpt.hide( "drop", { direction: "down" }, 400, function(){  } );
			}
			else {
				$hdrOpt.hide()
				$hdrOpt.show("drop", { direction: "up" }, 400, function(){
					$(this).find('a.tipText').each(function(){
						$(this).data('tooltip',false) // Delete the tooltip
							.tooltip({ placement: 'right', container : 'body', delay: 1000  });
					})
				});
			}

			$(this).data('menus', $hdrOpt[0])
			$hdrOpt.data('triggered', $(this)[0])

		})


		$('body').on('click', function (e) {
			$('.toggle_top_menus').each(function () {
				var $hdrOpt = $($(this).data('menus'))

				if ( !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.elbox_options').has(e.target).length === 0 ) {

					if( $hdrOpt.length && $hdrOpt.is(':visible') )
						$hdrOpt.hide( "drop", { direction: "down" }, 400, function(){  } );
				}
			});
		});

		/*$('#elementContextMenu').smartmenus({
			mainMenuSubOffsetX: 6,
			mainMenuSubOffsetY: -8,
			subMenusSubOffsetX: 6,
			subMenusSubOffsetY: -8
		});*/
})



	$(window).load(function () {
		setTimeout(function(){
			$('.el-title-text').ellipsis()
		}, 1500)
	})

</script>

<!-- Strikethrough on the icon -->
<!-- <span style=" position: absolute; width: 100%; border-top: 1px solid red; left: 0; top: 50%;"></span> -->



<div class="areaContextMenuWrapper">
	<ul id="areaContextMenu" class="sm sm-vertical sm-mint" style="display: none;">
		<li><a href="#" name="paste" class="disabled" id="paste"> <i
				class="fa fa-fw fa-paste"></i> Paste
		</a></li>
	</ul>
</div>


<?php $context_list = $this->ViewModel->project_workspaces(); // Get all projects, workspaces and areas ?>
<ul id="elementContextMenu" class="sm sm-vertical sm-mint" style="display: none">
	<li><a href="#" name='cut'> <i class="fa fa-fw fa-cut"></i> Cut </a></li>
	<li><a href="#" name='copy'> <i class="fa fa-fw fa-copy"></i> Copy </a></li>

	<li class=""><a href="#" name='copyTo'> <i class="fa fa-fw fa-mail-reply-all"></i> Copy To </a>
		<?php
		if( isset( $context_list ) && !empty( $context_list ) ) {
			?>

			<ul class="dropdown-menu">
			<li class="lio"><a>Projects</a></li>
	<?php foreach( $context_list as $key => $data ) { ?>

					<li class="" data-id="<?php echo $data['project']['id']; ?>"><a
				href="#"> <i class="fa fa-fw fa-briefcase"></i> <?php echo _substr_text( $data['project']['title'], 20, true ); ?>
						</a>

						<?php
						// If any workspace is there in this project
						if( isset( $data['workspace'] ) && !empty( $data['workspace'] ) ) {
							?>

							<ul class="dropdown-menu">
					<li class="lio"><a>Workspaces</a></li>
			<?php foreach( $data['workspace'] as $wk => $wv ) { ?>

									<li class=" " data-id="<?php echo $wv['id']; ?>"><a href="#"> <i
							class="fa fa-fw fa-th"></i> <?php echo _substr_text( $wv['title'], 20, true ); ?>
										</a>

				<?php if( isset( $wv['area'] ) && !empty( $wv['area'] ) ) { ?>

											<ul class="dropdown-menu">
							<li class="lio"><a>Areas</a></li>
					<?php foreach( $wv['area'] as $ak => $av ) { ?>
							<li class=""><a href="#" data-id="<?php echo $ak ?>"
								data-list-id="<?php echo $data['project']['id'] . '_' . $wv['id'] . '_' . $ak ?>"
								name='copy_to' class="target"> <i class="fa fa-fw fa-list-alt"></i> <?php echo _substr_text( $av, 20, true ); ?></a>
							</li>
											<?php } ?>
											</ul>
									<?php } ?>
									</li>
							<?php } ?>
							</ul>

					<?php } ?>
					</li>

			<?php }
			?>
			</ul>
<?php } ?>
	</li>

	<li class=""><a href="#" name='moveTo'> <i class="fa fa-fw fa-mail-reply"></i> Move To </a>
		<?php

		if( isset( $context_list ) && !empty( $context_list ) ) {
			?>

			<ul class="dropdown-menu">
			<li class="lio"><a>Projects</a></li>
	<?php foreach( $context_list as $key => $data ) { ?>

					<li class="" data-id="<?php echo $data['project']['id']; ?>"><a
				href="#"> <i class="fa fa-fw fa-briefcase"></i> <?php echo _substr_text( $data['project']['title'], 20, true ); ?>
						</a>

						<?php // If any workspace is there in this project
						if( isset( $data['workspace'] ) && !empty( $data['workspace'] ) ) {
						?>

							<ul class="dropdown-menu">
					<li class="lio"><a>Workspaces</a></li>
							<?php foreach( $data['workspace'] as $wk => $wv ) {  ?>

								<li class=" " data-id="<?php echo $wv['id']; ?>"><a href="#"> <i class="fa fa-fw fa-th"></i> <?php echo _substr_text( $wv['title'], 20, true ); ?>
										</a>

							<?php if( isset( $wv['area'] ) && !empty( $wv['area'] ) ) { ?>

							<ul class="dropdown-menu">
							<li class="lio"><a>Areas</a></li>
							<?php foreach( $wv['area'] as $ak => $av ) { ?>
							<li class=""><a href="#" data-id="<?php echo $ak ?>"
								data-list-id="<?php echo $data['project']['id'] . '_' . $wv['id'] . '_' . $ak ?>"
								name='move_to' class="target"> <i class="fa fa-fw fa-list-alt"></i> <?php echo _substr_text( $av, 20, true ); ?></a>
							</li>
							<?php } ?>
							</ul>
				<?php } ?>
									</li>
							<?php } ?>
							</ul>

					<?php } ?>
					</li>

			<?php }
			?>
			</ul>
<?php } ?>
	</li>
</ul>



<div id="copy_to_list">
    <?php

	if( isset( $context_list ) && !empty( $context_list ) ) {
	?>
    <ul class="dropdown-menu custom-drop">
        <li class="dropdown-header">Projects</li>
        <?php foreach( $context_list as $key => $data ) { ?>
        <li class="dropdown-submenu" data-id="<?php echo $data['project']['id']; ?>">
            <a tabindex="-1" href="#">
            	<i class="fa fa-briefcase"></i> <?php echo _substr_text( $data['project']['title'], 20, true ); ?>
            </a>
            <?php if( isset( $data['workspace'] ) && !empty( $data['workspace'] ) ) { ?>
            	<?php if( (count($data['workspace']) - 1) > 0 ){ ?>
	            <ul class="dropdown-menu">
	            	<li class="dropdown-header">Workspaces</li>
	            	<?php foreach( $data['workspace'] as $wk => $wv ) {
	            	if($workspace_id != $wv['id'] && empty($wv['studio_status'])) {  ?>
	                <li class="dropdown-submenu" data-id="<?php echo $wv['id']; ?>">
	                    <a href="#">
	                    	<i class="fa fa-th"></i> <?php echo _substr_text( $wv['title'], 20, true ); ?>
	                    </a>
	                    <?php if( isset( $wv['area'] ) && !empty( $wv['area'] ) ) { ?>
	                    <ul class="dropdown-menu">
	                        <li class="dropdown-header">Areas</li>
	                        <?php foreach( $wv['area'] as $ak => $av ) { ?>
	                        <li>
	                        	<a href="#" data-id="<?php echo $ak ?>"
								data-list-id="<?php echo $data['project']['id'] . '_' . $wv['id'] . '_' . $ak ?>"
								name='copy_to' class="target">
									<i class="fa fa-list-alt"></i> <?php echo _substr_text( $av, 20, true ); ?>
								</a>
							</li>
	                        <?php } // foreach $wv['area'] ?>
	                    </ul>
	                    <?php } // if $wv['area'] ?>
	                </li>
	                <?php } ?>
	                <?php } // foreach $data['workspace'] ?>
	            </ul>
            	<?php } // if count( ?>
            <?php } // if $data['workspace'] ?>
        </li>
        <?php } // foreach context_list ?>
    </ul>
    <?php } // if context_list ?>
</div>


<div id="move_to_list">
    <?php

	if( isset( $context_list ) && !empty( $context_list ) ) {
	?>
    <ul class="dropdown-menu custom-drop">
        <li class="dropdown-header">Projects</li>
        <?php foreach( $context_list as $key => $data ) { ?>
        <li class="dropdown-submenu" data-id="<?php echo $data['project']['id']; ?>">
            <a tabindex="-1" href="#">
            	<i class="fa fa-briefcase"></i> <?php echo _substr_text( $data['project']['title'], 20, true ); ?>
            </a>
            <?php if( isset( $data['workspace'] ) && !empty( $data['workspace'] ) ) { ?>
            <?php if( (count($data['workspace']) - 1) > 0 ){ ?>
            <ul class="dropdown-menu">
            	<li class="dropdown-header">Workspaces</li>
            	<?php foreach( $data['workspace'] as $wk => $wv ) {
            	if($workspace_id != $wv['id'] && empty($wv['studio_status'])) {  ?>
                <li class="dropdown-submenu" data-id="<?php echo $wv['id']; ?>">
                    <a href="#">
                    	<i class="fa fa-th"></i> <?php echo _substr_text( $wv['title'], 20, true ); ?>
                    </a>
                    <?php if( isset( $wv['area'] ) && !empty( $wv['area'] ) ) { ?>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">Areas</li>
                        <?php foreach( $wv['area'] as $ak => $av ) { ?>
                        <li>
                        	<a href="#" data-id="<?php echo $ak ?>"
							data-list-id="<?php echo $data['project']['id'] . '_' . $wv['id'] . '_' . $ak ?>"
							name='move_to' class="target">
								<i class="fa fa-list-alt"></i> <?php echo _substr_text( $av, 20, true ); ?>
							</a>
						</li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
                <?php } ?>
            </ul>
            <?php } ?>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>


<script type="text/javascript">
	$(function(){

		$js_config.workspace_id = <?php echo json_encode( $workspace_id ); ?>;

        // Set all elements data value
        $(".area_box").each(function () {
            var area_id = $(this).attr('id')
            $('.el', $(this)).each(function(i, j) {
                var element_id = $(j).data('element');
                // result = $.grep(dataValue['data'], function(e) { return e.id == data_ele; });
                result = $.grep($js_config.elements_details[area_id]['el'], function(e) {
                    return e.id == element_id;
                });
                if (result.length == 1) {
                    result = result[0];
                }
                var currentData = $(j).data();

                currentData = $.extend(currentData, result)

                $(j).data(currentData)
            })
            $(this).data("clonedObject", {});
        })

        $.box_height = 0;
        ($.setMaxHeight = function(){
        	$.box_height = 0;
	        $('#tbl td .box-body').each(function(index, el) {
	        	var h = $(this).find('.el:first').outerHeight(true);
	        	if(h && $(this).find('.el').length > 15){
	        		var new_height = h * 15;
	        		if(new_height > $.box_height){
	        			$.box_height = new_height;
	        		}
	        	}
	        });

			$('#tbl td .box-body').each(function(index, el) {
				if($(this).find('.el').length > 15){
					$(this).css({'height': $.box_height , 'max-height': $.box_height })
				}
			})
		})();

		$(window).on('resize', $.setMaxHeight);

	})
</script>