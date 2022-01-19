<script type="text/javascript" >
 $(document).ready(function(){

	$('.area_info').tooltip({
		placement : 'bottom',
		container : 'body',
	});
	$('#ajax_overlay').show();
$('.pophover_txt').popover({
        placement : 'top',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

})



 </script>
<?php echo $this->Html->script( 'projects/plugins/wysihtml5.editor', array( 'inline' => true ) ); ?>
<?php echo $this->Html->script( 'projects/plugins/editInPlace', array( 'inline' => true ) );
// echo $this->Html->css('projects/scroller');
 ?>

<?php
$elements_details = [];
// pr($data['templateRows'], 1);


if( isset( $data['templateRows'] ) && !empty( $data['templateRows'] ) ) {

if(isset($template_id) && !empty($template_id) && $template_id=='14'){
	$spl = "spl_wsp";
}else{
	$spl = "";
}
	?>
<div class=" ">
	<table class="table table-bordered <?php  echo $spl; ?>" id="tbl"  >

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
						class="area_box" data-pid="<?php echo $this->params['pass']['0'] ?>" id="<?php echo $row_detail['area_id']; ?>"
						style="" <?php if( $setWidth == 1 ) { ?>
						width="<?php echo $tdWidth; ?>" <?php } ?> >

						<div class="box box-success box-area no-margin">
							<div class="box-header">

								<a href="#"
									class="btn btn-xs area_elements_toggle el-toggle-tooltip"
									data-toggle="tooltip" data-trigger="hover"
									data-placement="right" role="tooltip"
									data-original-title="Toggle All Elements"> <i
									class="fa fa-fw fa-bars" style="font-size: 24px;"></i>
								</a>
								<span class="popover-markup">

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

								<a class="my-menu toggle_area_menus" href="#">
									<span class="fa fa-list"></span>
								</a>

								<h3 class="box-title area-box-title mob_scrn" id="area_<?php echo $row_detail['area_id'] ?>-toggler">

										<a href="javascript:;"
											data-pop-form="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'popover', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
											data-remote="<?php echo Router::Url( array( 'controller' => 'workspaces', 'action' => 'update_area', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
											rel="areaInfo_<?php echo $row_detail['area_id']; ?>" id="<?php echo $row_detail['area_id']; ?>" class="area_title"  data-full-title="<?php echo $row_detail['title']; ?>"> <?php echo $row_detail['title']; ?></a>
								</h3>

								<div class="pull-right area_menus" style="">
									<div class="btn-group ">
										<?php
										$user_id = $this->Session->read('Auth.User.id');

										$wsp_permission = $this->Common->wsp_permission_details($this->ViewModel->workspace_pwid($this->params['pass']['1']),$this->params['pass']['0'],$this->Session->read('Auth.User.id'));

										$p_permission = $this->Common->project_permission_details($this->params['pass']['0'],$this->Session->read('Auth.User.id'));

										$user_project = $this->Common->userproject($this->params['pass']['0'],$this->Session->read('Auth.User.id'));

										$grp_id = $this->Group->GroupIDbyUserID($this->params['pass']['0'],$user_id);

										if(isset($grp_id) && !empty($grp_id)){

										$group_permission = $this->Group->group_permission_details($this->params['pass']['0'],$grp_id);
											if(isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level']==1){
												$project_level = $group_permission['ProjectPermission']['project_level'];
											}
											$wsp_permission = $this->Group->group_wsp_permission_details($this->ViewModel->workspace_pwid($this->params['pass']['1']),$this->params['pass']['0'],$grp_id);
										}

										$message = '';


										if((isset($user_project) && !empty($user_project)) ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) || (isset($project_level) && $project_level==1) || (isset($wsp_permission['0']['WorkspacePermission']['permit_add']) && $wsp_permission['0']['WorkspacePermission']['permit_add']) ) {



										if($data['workspace']['Workspace']['sign_off'] !=1) {


										if((isset($data['workspace']['Workspace']['end_date']) && $data['workspace']['Workspace']['end_date'] >= date('Y-m-d')) && (isset($data['workspace']['Workspace']['start_date']) && $data['workspace']['Workspace']['start_date'] <= date('Y-m-d 00:00:00'))){

										?>
										<button
											class="btn btn-success text-white btn-xs tipText add_element "
											data-original-title="<?php echo tipText( 'Add Element' ) ?>"
											data-toggle="modal" data-target="#popup_modal"
											id="add_element"
											data-area="<?php echo $row_detail['area_id']; ?>"
											data-id="<?php echo $row_detail['area_id']; ?>"
											data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>">
											<i class="fa fa-plus"></i>
										</button>

										<?php }

										if((isset($data['workspace']['Workspace']['start_date']) && $data['workspace']['Workspace']['start_date'] > date('Y-m-d 00:00:00')) ){

										$message ="Workspace schedule has not reached the start date.";
										 }

										if((isset($data['workspace']['Workspace']['end_date']) && $data['workspace']['Workspace']['end_date'] < date('Y-m-d')) ){

										$message ="You cannot add an Element because Workspace end date has passed.";
										 }

										}
										 else if(isset($data['workspace']['Workspace']['start_date'])){
										 $message ="You cannot add an Element because Workspace has Signoff.";
										 }


										if(!isset($data['workspace']['Workspace']['start_date'])){
	                                        $message ="Please add a schedule to this workspace first.";
										}

										}
										if(isset($message) && !empty($message)){
										 ?>

										 <button data-title="<?php echo $message;?>"
											class="btn btn-success text-white btn-xs workspace tipText add_element disable"
											data-original-title="<?php echo tipText( 'Add Element' ) ?>"id="add_element"
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
											data-content="<?php echo $tooltip_text; ?>"
											data-placement="left">
											<i class="fa fa-fw fa-info"></i>
										</button>

									</div>
								</div>


								<div class="modal fade" id="PostCommentsModal" tabindex="-1"
									role="dialog" aria-labelledby="helpModalLabel"
									aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">
													<span aria-hidden="true">&times; </span><span
														class="sr-only">Close</span>
												</button>
												<h4 class="modal-title" id="myModalLabel"></h4>
											</div>
											<div class="modal-body">
												<div class="input-group">
													<span class="input-group-addon">@</span> <input type="text"
														class="form-control" placeholder="Your Name" />
												</div>
												<p></p>
												<div class="input-group">
													<span class="input-group-addon">@</span> <input type="text"
														class="form-control" placeholder="Your Email" />
												</div>
												<p></p>
												<div class="input-group">
													<span class="input-group-addon">@</span>
													<textarea rows="4" cols="50" class="form-control"
														placeholder="Your Message"></textarea>
												</div>
												<button type="button" class="btn-primary">Send</button>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default"
													data-dismiss="modal">Close</button>
											</div>
										</div>
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

								<?php
								$e_permission = $this->Common->element_permission_details($this->params['pass']['1'],$this->params['pass']['0'],$this->Session->read('Auth.User.id'));

								$ws_id = $this->params['pass']['1'];


								if((isset($grp_id) && !empty($grp_id))){

										if(isset($e_permission) && !empty($e_permission)){
										$e_permissions =  $this->Group->group_element_permission_details( $ws_id, $this->params['pass']['0'], $grp_id);
										$e_permission = array_merge($e_permission,$e_permissions);
										}else{
										$e_permission =  $this->Group->group_element_permission_details( $ws_id, $this->params['pass']['0'], $grp_id);
										}
								}

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
							</div>
						</td>
							<?php
						}
						// end first foreach
						// End table row started just after first foreach
						// It prints till the total number of rows reaches
						if( $row_id < (count( $row_group ) - 1) )
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
	// pr($areas, 1);
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

		$.on_spot('.area_info', {
			debug: true, // Turn logging on/off
			on: 'click', // The event to trigger
			duration: 500, // The time to show/hide
			type: 'area_info',
		})

		// SET ALL DATA PASSED FROM PHP SCRIPT ON PAGE LOAD
		$js_config.elements_details = <?php echo json_encode( $areaElements ); ?>;

		$('*[data-pop-form]').on('click', function () {
			var e = $(this);
			// $(".fix-progress-wrapper").data_loader()
			var p = e.parents("td:first").find('.box')
			// $.load_ripple(p[0])
			setTimeout(function(){

				$.get(e.data('pop-form'), function (d) {
					e.removeData('bs.popover')
					// $('.popover').hide()
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

			/*var s = $("#tbl td:first").find('.area_title').data('bs.popover');
			if(s.length > 0)
				s.tip().hide()*/

			$popover.hide()
		})

		$('body').delegate("#popover_submit", 'click', function (event) {
			var $el = $(this),
					$popover = $(this).parents(".popover:first"),
					$content = $(this).parents(".popover:first").find('.popover-content'),
					pop_data = $popover.data('bs.popover'),
					$area_title = pop_data.$element,
					$id = $content.find('input[type=hidden]'),
					$title = $content.find('input[type=text]'),
					$icon = $content.find('#icon'),
					data = $el.data(),
					url = data.remote;

			var form_data = {
				'data[Area][id]': $id.val(),
				'data[Area][title]': $title.val()
			};

			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				data: form_data,
				url: url,
				global: false,
				beforeSend: function (res) {
					$icon.parent('.has-loader').addClass('active')
				},
				complete: function (data, textStatus, jqXHR) {
					$icon.parent('.has-loader').removeClass('active')
				},
				success: function (response, status, jxhr) {
					$content.find('.trigger_error').remove()
					if (response.success) {

						$el.parents('.popover:first').hide()
						$area_title.attr('data-full-title', response.content.title);
						setTimeout(function () {
							$area_title.effect("highlight", {
								// times: 3
								color: "#D90000"
							}, 1000, function () {
								$area_title.text(response.content.title);
								// $.trim_title( $('#tbl td .box-header'), '.popover-markup', '.area_title', 8)
							});
						}, 500);


					}
					else {
						$content.append('<span style="color: red; font-size: 11px;" class="trigger_error"><i class="fa fa-times"></i> ' + response.content.title + '<br>(' + $title.val().length + ')</span>')
					}

				},
			});

		});

		$("body").delegate(".summary_icons ul li span.btn:not(.blocked)", 'click', function (event) {

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

	$(window).on('resize', function (event) {

		if( $('.el-title-text').length ) {
			// $('.el-title-text').ellipsis();
		}
	})


		var heights = $("td.area_box").map(function() {
			return $(this).height();
		}).get(),
		maxHeight = Math.max.apply(null, heights);
		// console.log(maxHeight)
		$(".inside").height(maxHeight);

})



	$(window).load(function () {
		setTimeout(function(){
			$('.el-title-text').ellipsis()
		}, 1500)
	})

</script>

<!-- Strikethrough on the icon -->
<!-- <span style=" position: absolute; width: 100%; border-top: 1px solid red; left: 0; top: 50%;"></span> -->

<script id="elementTemplate" type="jeera/tpl">

	<div id="el_{area_id}_{id}" data-element="{id}"  data-prev-area="0" data-current-area="{area_id}" data-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "create_element", 'admin' => FALSE ), true ); ?>"  data-remove-remote="<?php echo Router::Url( array( "controller" => "entities", "action" => "remove_element", 'admin' => FALSE ), true ); ?>" class="el panel no-box-shadow {color_code} {allowed}" {style} >

	<div class="frontdrop" > </div>

	<div class="panel-heading clearfix" style="padding: 0 !important;">

		<h3 style="z-index: 0; position: relative;" class="panel-title el-title tipText"  data-original-title="Double Click to Collapse/Expand">
			<span class="el-title-text truncate"  >{title}</span>
		</h3>
		<a class="my-menu toggle_top_menus text-white" href="#" style="z-index: 999; position: relative; display: none;">
			<span class="fa fa-list"></span>
		</a>

		<div style="display: none;" class="btn-group pull-right hide elbox_options" >

			<a class="btn btn-default btn-xs tipText option_blue btn_open" title="Open Element" href="<?php echo SITEURL . 'entities/update_element'; ?>/{id}">
			<i class="fa fa-folder-open"></i>
			</a>

			<a href="#" class="btn btn-default btn-xs tipText move_up sort_order option_maroon btn_sort_up" data-original-title="To Preceding Position" href="#" data-sort-order="{sort_order}" data-id="{id}" data-area-id="{area_id}">
			<i class="fa fa-arrow-up"></i>
			</a>

			<a class="btn btn-default btn-xs tipText move_down sort_order option_purple btn_sort_down" data-original-title="To Next Position" href="#" data-id="{id}" data-sort-order="{sort_order}" data-area-id="{area_id}">
			<i class="fa fa-arrow-down"></i>
			</a>


			<a href="#" class="btn btn-default btn-xs flash tipText option_green btn_highlight" data-original-title="Highlight">
			<i class="fa fa-lightbulb-o"></i>
			</a>

			<!-- <a class="btn btn-default btn-xs flash tipText option_green cst" href="login.php"><i class="fa fa-key"></i><span>Login</span></a> -->

			</div>
	</div>

	<div class="panel-footer clearfix padding-top el_total_votes el_{self_status}" >


		<div class="text-center el-icons summary_icons icon-grid-view">
			<ul class="list-unstyled element-icons-list">
				<li class="istatus">
					<span class="label bg-mix">{status_short_term}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#tasks"  data-original-title="{status_tiptext}" class="btn btn-xs bg-element tipText" data-original-title="{status_tiptext}"><i class="fa fa-exclamation"></i></span>
				</li>
				<li class="ico_links">
					<span class="label bg-mix ">{total_links}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#links"  data-original-title=" Links" class="btn btn-xs bg-maroon tipText {is_blocked}"><i class="fa fa-link"></i></span>
				</li>
				<li class="inote">
					<span class="label bg-mix">{total_notes}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#notes"  data-original-title=" Notes" class="btn btn-xs bg-purple tipText {is_blocked}"><i class="fa fa-file-text-o"></i></span>
				</li>

				<li class="idoc">
					<span class="label bg-mix">{total_docs}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#documents"  data-original-title=" Documents" class="btn btn-xs bg-blue tipText {is_blocked}"><i class="fa fa-folder-o"></i></span>
				</li>


			</ul>

			<ul class="list-unstyled element-icons-list">
				<li class="imup">
					<span class="label bg-mix">{total_mindmaps}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#mind_maps"  data-original-title="" class="btn btn-xs bg-green tipText {is_blocked}" title=" Mind Maps"><i class="fa fa-sitemap"></i></span>
				</li>

			<?php
			// $wsp_permission['0']['WorkspacePermission']

								//pr($element);
			?>
				<li class="idiss">
					<span class="label bg-mix">{decision_short_term}</span>

					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#decisions"  data-original-title="{decision_tiptext}" class="btn btn-xs bg-orange tipText {is_permited}" data-original-title="{decision_tiptext}"><i class="fa fa-expand"></i></span>

				</li>

				<li class="ifeed">
				<span class="label bg-mix">{total_feedbacks}</span>

				<span data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#feedbacks"  data-original-title=" Live Feedbacks" class="btn btn-xs bg-teal tipText {is_permited}" data-original-title=" Feedbacks"><i class="fa fa-bullhorn"></i></span>

				</li>

				<li class="ivote">
				<span class="label bg-mix">{total_votes}</span>

				<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#votes"  data-original-title=" Live Votes" class="btn btn-xs bg-yellow tipText {is_permited}" data-original-title=" Votes"><i class="fa fa-inbox"></i></span>

				</li>
			</ul>

		</div>

		<div class="text-center el-icons summary_icons icon-list-view">


			<ul class="list-unstyled element-icons-list">
				<li class="istatus">
					<span class="label bg-mix">{status_short_term}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#tasks"  data-original-title="{status_tiptext}" class="btn btn-xs bg-element tipText" data-original-title="{status_tiptext}"><i class="fa fa-exclamation"></i></span>
				</li>

				<li class="ico_links">
					<span class="label bg-mix ">{total_links}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#links"  data-original-title=" Links" class="btn btn-xs bg-maroon tipText "><i class="fa fa-link"></i></span>
				</li>
			</ul>
			<ul class="list-unstyled element-icons-list">

				<li class="inote">
					<span class="label bg-mix">{total_notes}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#notes"  data-original-title=" Notes" class="btn btn-xs bg-purple tipText"><i class="fa fa-file-text-o"></i></span>
				</li>
				<li class="idoc">
					<span class="label bg-mix">{total_docs}</span>
					<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#documents"  data-original-title=" Documents" class="btn btn-xs bg-blue tipText"><i class="fa fa-folder-o"></i></span>
				</li>


			</ul>

			<ul class="list-unstyled element-icons-list">
				<li class="imup">
				<span class="label bg-mix">{total_mindmaps}</span>
				<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#mind_maps"  data-original-title="" class="btn btn-xs bg-green tipText" title=" Mindmaps"><i class="fa fa-sitemap"></i></span>
				</li>

				<li class="idiss">
				<span class="label bg-mix">{decision_short_term}</span>
				<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#decisions"  data-original-title="{decision_tiptext}" class="btn btn-xs bg-orange tipText" data-original-title="{decision_tiptext}"><i class="fa fa-expand"></i></span>
				</li>
			</ul>
			<ul class="list-unstyled element-icons-list">
				<li class="ifeed">
				<span class="label bg-mix">{feedbacks}</span>
				<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#feedbacks"  data-original-title="{feedback_tiptext}" class="btn btn-xs bg-teal tipText" data-original-title="{feedback_tiptext}"><i class="fa fa-bullhorn"></i></span>
				</li>

				<li class="ivote">
				<span class="label bg-mix">{votes} </span>
				<span  data-remote="<?php echo SITEURL . 'entities/update_element'; ?>/{id}#votes"  data-original-title=" Votes" class="btn btn-xs bg-yellow tipText" data-original-title=" Votes"><i class="fa fa-inbox"></i></span>
				</li>
			</ul>

		</div>




	</div>

	<div class="panel-body collapse fade no-padding" id="collapse_body_{area_id}_{id}">

	<div class="sub-heading clearfix" style="">

	<span>Task Description</span>

	</div>

	<div class="body-content" >{description} </div>

	<div class="sub-heading clearfix" style="">

	<span>Task Outcome</span>

	</div>

	<div class="body-content" >{comments} </div>

	</div>



</script>

<div class="areaContextMenuWrapper">
	<ul id="areaContextMenu" class="sm sm-vertical sm-mint"
		style="display: none;">
		<li><a href="#" name="paste" class="disabled" id="paste"> <i
				class="fa fa-fw fa-paste"></i> Paste
		</a></li>
	</ul>
</div>



<ul id="elementContextMenu" class="sm sm-vertical sm-mint" style="display: none">
	<li><a href="#" name='cut'> <i class="fa fa-fw fa-cut"></i> Cut </a></li>
	<li><a href="#" name='copy'> <i class="fa fa-fw fa-copy"></i> Copy </a></li>

	<li class=""><a href="#" name='copyTo'> <i class="fa fa-fw fa-mail-reply-all"></i> Copy To </a>
		<?php
		$context_list = $this->ViewModel->project_workspaces(); // Get all projects, workspaces and areas
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
		$context_list = $this->ViewModel->project_workspaces(); // Get all projects, workspaces and areas
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
<style>
.option-panel {
	display: block;
	text-align: center;
	margin-bottom: 5px;
}
.option-panel .btn {
	padding: 4px 10px;
}
</style>
