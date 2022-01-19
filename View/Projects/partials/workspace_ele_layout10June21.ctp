<?php echo $this->Html->script( 'projects/plugins/editInPlace', array( 'inline' => true ) );
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
//echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
?>
<style type="text/css">
	#workspace {
		overflow: auto;
	}
	#workspace td .box-body.over {
		/*background: #f5efda none repeat scroll 0 0;*/
		border: 1px dashed #d9a602;
		transition: all 0.3s ease 0s;
	}
	.ellipsis-words{ height:25px; display:block;overflow:hidden; white-space: nowrap; }



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
        align-items: flex-start;
	}
	#workspace td .box-body.drop_element .el.panel{
		/*border: 1px solid #80848c;*/
		border-radius: 4px;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-flow: column;
		-webkit-flex-flow: column;
		flex-flow: column;
		margin-left: 1%;
	}
	#workspace td .box-body.drop_element .el.panel .inner-el{
		width: 100%;
		/*border: 1px solid #80848c;*/
		border-radius: 5px;
	}

	.area_elements_toggle {
		min-width: 30px;
		min-height: 24px;
		padding: 0;
		border: none;
		line-height: 24px;
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
    $('.text-ellipsis').tooltip({
		placement: 'top-left'
	})

})
</script>
<div id="workspace">
<?php
	$workspaceArray = $data['workspace']['Workspace'];
	$class_name = (isset($workspaceArray['color_code']) && !empty($workspaceArray['color_code'])) ? $workspaceArray['color_code'] : 'bg-gray';
?>
<style type="text/css">
	.theader {
		border-right: 1px solid #ccc;
	}
	.wsp-link {
		overflow: hidden;
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    width: 100%;
	    display: inline-block;
	    color: #fff;
	}

    .workspace-tasks-sec-top{
        display: flex;
        width: 100%;
    }
    .workspace-tasks-sec-top .small-box {
    	margin-bottom: 0;
    	transition: none !important;
    }
    .padd8{
        padding: 8px;
    }

    .workspace-col-5 {
        width: 30%;
    }
    .workspace-col-4 {
        width: 24%;
        display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    }
    .workspace-col-3 {
        width: 24.5%;
    }
     .workspace-col-2 {
        width: 13%;
         display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    }
    .workspace-col-1 {
        width: 8.5%;
        display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    }

    .workspace-tasks-sec-top .theader {
        background-color: #f5f5f5;
        border-top: 1px solid #dcdcdc;
        font-weight: bold;
        border-right: none;
    }
    .workspace-tasks-sec-top .tcont {
            border: 1px solid #f4f4f4;
    }
	.workspace-tasks-sec-top .text-ellipsis {
	    white-space: nowrap;
	    overflow: hidden;
	    display: block;
	    padding-bottom: 5px ;
	}
    .workspace-tasks-sec-top .reminder-sharing-d-in{
        padding-left: 0;
    }
    .workspace-tasks-sec-top .description-wroks{
         font-size: 12px; line-height: 16px;
    }

	.add-task-message-text {
		    line-height: 1.42857;
	}


    @media(max-width:991px){
        .workspace-tasks-sec-wrap{
            overflow: auto;
        }
        .workspace-tasks-sec-wrap .workspace-tasks-sec-top{
        min-width: 990px;
        }

    }
    .area_elements_toggle .fa-chevron-up {
    	display: none;
    }
    .area_elements_toggle.details .fa-chevron-down {
    	display: none;
    }
    .area_elements_toggle.details .fa-chevron-up {
    	display: inline-block;
    }
    .board_area_title:active, .board_area_title:focus{
    	color: #fff;
    }

</style>

<?php

if( isset($viewData) && !empty($viewData) ){
	// Get Area Element
	$area_element = $this->ViewModel->getAreaTask($workspace_id, null, $viewData);

} else {
	$area_element = $this->ViewModel->getAreaTask($workspace_id, null);
}
foreach ($area_element as $element) {

	$result[$element['user_permissions']['a_id']][] = $element;
}

// pr($area_element,1);
$elements_details = [];
if( isset( $data['templateRows'] ) && !empty( $data['templateRows'] ) ) {

 ?>
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

						<div class="box  box-area no-margin">
							<div class="box-header">

								<a href="#"
									class="btn btn-xs area_elements_toggle el-toggle-tooltip"
									data-toggle="tooltip" data-trigger="hover"
									data-placement="right" role="tooltip"
									data-original-title="Toggle All Tasks" style=" float: left;">
									<i class="fa fa-fw fa-chevron-up icon-default"></i>
									<i class="fa fa-fw fa-chevron-down icon-default"></i>
									<!-- <i class="fa fa-fw fa-th-large icon-hover" style="font-size: 24px;"></i> -->
								</a>
								<span class="popover-markup" style="display: block; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; margin-right: 52px; vertical-align: top;">

									<h3 class="box-title area-box-title truncate" id="area_<?php echo $row_detail['area_id'] ?>-toggler">

										<a href="javascript:;"
											data-pop-form="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'popover', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
											data-remote="<?php echo Router::Url( array( 'controller' => 'workspaces', 'action' => 'update_area', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>"
											rel="areaInfo_<?php echo $row_detail['area_id']; ?>" id="<?php echo $row_detail['area_id']; ?>" class="board_area_title"  data-full-title="<?php echo htmlentities($row_detail['title']); ?>"> <?php echo htmlentities($row_detail['title']); ?></a>
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

								<div class="pull-right area_menus" style="position: absolute; right: 11px; top: 12px;">

									<a href="#" class="btn btn-default btn-xs btn-paste fade-out dropdown-toggle" data-toggle="dropdown" style="position: relative; ">
										<i class="far fa-clone"></i>
									</a>
									<ul class="dropdown-menu paste-menus">
										<li><a href="#" name="paste" id="paste"><i class="fa fa-paste"></i> Paste</a></li>
										<li><a href="#" class="cancel-paste" style="color: #c00 !important;"><i class="fa fa-times"></i> Cancel</a></li>
									</ul>


									<div class="btn-group task-el-btn">
										<?php
										$modal_title = '<i class="fa fa-exclamation-triangle"></i>&nbsp;Warning';
										$user_id = $this->Session->read('Auth.User.id');
										$message = '';
										if( isset($wsp_permissions[0]['user_permissions']['p_task_add']) && $wsp_permissions[0]['user_permissions']['p_task_add'] == 1 ) {

											$data['workspace']['Workspace']['start_date'] = date('Y-m-d',strtotime($wsp_permissions[0]['workspaces']['wsp_start_date']));
											$data['workspace']['Workspace']['end_date'] = date('Y-m-d',strtotime($wsp_permissions[0]['workspaces']['wsp_end_date']));

										if( $wsp_permissions[0]['workspaces']['wsp_sign_off'] !=1 ){

										$curdate =  $this->Wiki->_displayDate(date("Y-m-d h:i:s A"),$format = 'Y-m-d');
										$wspStartDate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['workspace']['Workspace']['start_date'])),$format = 'd M, Y h:i:s A');



												if((isset($data['workspace']['Workspace']['end_date']) && !empty($data['workspace']['Workspace']['end_date']) && $data['workspace']['Workspace']['end_date'] != '1970-01-01' && $data['workspace']['Workspace']['end_date'] < $curdate ) ){

													$message ="You cannot add a Task because the Workspace end date has passed.";
												}

												if(
													( !isset($data['workspace']['Workspace']['start_date']) || $data['workspace']['Workspace']['start_date'] == '1970-01-01' ) &&
													( !isset($data['workspace']['Workspace']['end_date']) ||  $data['workspace']['Workspace']['end_date'] == '1970-01-01' ) ){
													$message ="You cannot add a Task because the Workspace is not scheduled.";
												}

												if(!isset($data['workspace']['Workspace']['start_date'])){
													$message ="Please add a schedule to this workspace first.";

												}

											} else if(isset($data['workspace']['Workspace']['start_date'])){
												$message ="You cannot add a Task because the Workspace has been signed off.";
												$modal_title = 'Add Task';
											}

											$t_disabled  = '';
											$t_cursor = '';
											if( isset($data['workspace']['Workspace']['sign_off']) && $data['workspace']['Workspace']['sign_off'] == 1 ){
												$t_disabled = 'disable';
												$modal_title = "Workspace Is Signed Off";
												$t_cursor ="cursor:default !important; ";
												$message ="You cannot add a Task because the Workspace has been signed off.";

											}
											if(isset($message) && !empty($message)){
												if( isset($t_disabled) && !empty($t_disabled) ){
											 ?>
													<button
														class="btn tipText disable"
														title="<?php echo $modal_title;?>"
														style="<?php echo $t_cursor; ?>">
														<i class="addwhite" style="<?php echo $t_cursor; ?>"></i>
													</button>
												<?php } else { ?>
													<button data-title="<?php echo $message;?>"
														class="btn workspace tipText add_element disable"
														data-original-title="Add Task" id="add_element"
														data-area="<?php echo $row_detail['area_id']; ?>"
														data-id="<?php echo $row_detail['area_id']; ?>"
														data-remote="">
														<i class="addwhite"></i>
													</button>
												<?php } ?>
											<?php } else { ?>
												<button
													class="btn tipText add_element "
													data-original-title="Add Task"
													data-toggle="modal" data-target="#popup_model_box"
													id="add_element"
													data-area="<?php echo $row_detail['area_id']; ?>"
													data-id="<?php echo $row_detail['area_id']; ?>"
													data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', $row_detail['area_id'], 'admin' => FALSE ), TRUE ); ?>">
													<i class="addwhite"></i>
												</button>
											<?php
											}
										}
										$tooltip_text = ( isset( $row_detail['tooltip_text'] ) && !empty( $row_detail['tooltip_text'] ) ) ? $row_detail['tooltip_text'] : 'N/A';

										?>
										<button class="btn pophover_txt area_info"
											data-content="<?php echo htmlentities($tooltip_text); ?>" >
											<i class="infowhite"></i>
										</button>

									</div>
								</div>
							</div>
						</div>

						<script type="text/javascript" >
				        $(document).on("click", ".workspace", function(e) {
				            e.preventDefault()
				            var message = $(this).attr("data-title");
							$("#modal-alert .modal-content").html('<div class="modal-header">\
										        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
													<h3 class="modal-title">Add Task</h3>\
										        </div>\
										      	<div class="modal-body">\
											        <button type="button" class="close" data-dismiss="modal">&times;</button>\
											        Please set project dates before setting workspace dates.\
										      	</div>\
										      	<div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">Close</button></div>');
						  	$("#modal-alert .modal-body").html('<div class="add-task-message-text">'+message+'</div>');
				            $.model = $("#modal-alert")
				            $.model.modal('show');
				        });
						</script>
						<!-- <div id="modal-alert" class="modal modal-danger fade" tabindex="-1">
						  <div class="modal-dialog modal-md">
						    <div class="modal-content">
						        <div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="modal-title">Add Task</h3>
						        </div>
						      	<div class="modal-body">
							        <button type="button" class="close" data-dismiss="modal">&times;</button>
							        Please set project dates before setting workspace dates.
						      	</div>
						      	<div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">Close</button></div>
						    </div>
						  </div>
						</div> -->
						<div class="box-body clearfix in drop_element">
							<div class="ovrelay"></div>

								<?php
								// CREATE ELEMENTS OF EACH AREA IF EXISTS
								if( isset( $row_detail['elements'] ) && !empty( $row_detail['elements'] ) ) {

									$elements_data = $row_detail['elements'];
									foreach( $elements_data as $element_index => $element_detail ) {

										$response = [ 'id' => $element_detail['Element']['id'], 'data' => ['Element' => $element_detail ] ];

									}
								}

								if(isset($result[$row_detail['area_id']]) && !empty($result[$row_detail['area_id']])){

								 $area_element  = $result[$row_detail['area_id']];

								}else{
									$area_element  = array();
								}
								$area_detail = getByDbId('Area', $row_detail['area_id']);
								echo $this->element('../Projects/partials/area_task_elements', [ 'area_id' => $row_detail['area_id'], 'area_detail' => $area_detail, 'all_elements' => $area_element,"wsp_sign_off"=>$data['workspace']['Workspace']['sign_off'] ]);

								?>
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
		//$js_config.elements_details = <?php echo json_encode( $areaElements ); ?>;

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

<?php
$context_list = array();
 //$context_list = $this->ViewModel->project_workspaces(); // Get all projects, workspaces and areas
$projectArray = $this->ViewModel->getMoveCopyArea($workspace_id);

$project_list = array();
$wsp_list = array();
$area_list = array();
if( isset($projectArray) && !empty($projectArray) ){
	foreach($projectArray as $list_data){

		$project_list[$list_data['projects']['id']]['project_title'] = $list_data['projects']['project_title'];


		$wsp_list[$list_data['projects']['id']][$list_data['workspaces']['workspace_id']]['workspace_id']=$list_data['workspaces']['workspace_id'];
		$wsp_list[$list_data['projects']['id']][$list_data['workspaces']['workspace_id']]['wsp_title']=$list_data['workspaces']['wsp_title'];

		$area_list[$list_data['workspaces']['workspace_id']][$list_data['areas']['area_id']]['id'] = $list_data['areas']['area_id'];
		$area_list[$list_data['workspaces']['workspace_id']][$list_data['areas']['area_id']]['area_title'] = $list_data['areas']['area_title'];

	}
}
// pr($wsp_list);
?>



<div id="copy_to_list">
    <?php

	if( isset( $project_list ) && !empty( $project_list ) ) {
	?>
    <ul class="dropdown-menu custom-drop">
        <li class="dropdown-header">Projects</li>
        <?php foreach( $project_list as $key => $data ) { ?>

        <li class="dropdown-submenu" data-id="<?php echo $key; ?>">
            <a tabindex="-1" href="#">
            	<i class="fa fa-briefcase"></i> <?php echo _substr_text( $data['project_title'], 20, true ); ?>
            </a>
            <?php if( isset( $wsp_list[$key] ) && !empty( $wsp_list[$key] ) ) { ?>
            <ul class="dropdown-menu">

            	<li class="dropdown-header">Workspaces</li>
            	<?php foreach( $wsp_list[$key] as $wk => $wv ) {   ?>
                <li class="dropdown-submenu" data-id="<?php echo $wv['workspace_id']; ?>">
                    <a href="#">
                    	<i class="fa fa-th"></i> <?php echo _substr_text( $wv['wsp_title'], 20, true ); ?>
                    </a>
                    <?php if( isset( $area_list[$wk] ) && !empty( $area_list[$wk] ) ) { ?>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">Areas</li>
                        <?php foreach( $area_list[$wk] as $ak => $av ) { ?>
                        <li>
                        	<a href="#" data-id="<?php echo $ak ?>"
							data-list-id="<?php echo $key . '_' . $wk . '_' . $ak ?>"
							name='copy_to' class="target">
								<i class="fa fa-list-alt"></i> <?php echo _substr_text( $av['area_title'], 20, true ); ?>
							</a>
						</li>
                        <?php } // foreach $wv['area'] ?>
                    </ul>
                    <?php }  // if $wv['area'] ?>
                </li>

                <?php } // foreach $data['workspace'] ?>
            </ul>
            <?php } // if $data['workspace'] ?>
        </li>
        <?php } // foreach context_list ?>
    </ul>
    <?php } // if context_list ?>
</div>


<div id="move_to_list">
    <?php

	if( isset( $project_list ) && !empty( $project_list ) ) {
	?>
    <ul class="dropdown-menu custom-drop">
        <li class="dropdown-header">Projects</li>
        <?php foreach( $project_list as $key => $data ) { ?>
        <li class="dropdown-submenu" data-id="<?php echo $key; ?>">
            <a tabindex="-1" href="#">
            	<i class="fa fa-briefcase"></i> <?php echo _substr_text( $data['project_title'], 20, true ); ?>
            </a>
            <?php if( isset( $wsp_list[$key] ) && !empty( $wsp_list[$key] ) ) { ?>
            <ul class="dropdown-menu">
            	<li class="dropdown-header">Workspaces</li>
            	<?php foreach( $wsp_list[$key] as $wk => $wv ) { ?>
                <li class="dropdown-submenu" data-id="<?php echo $wv['workspace_id']; ?>">
                    <a href="#">
                    	<i class="fa fa-th"></i> <?php echo _substr_text( $wv['wsp_title'], 20, true ); ?>
                    </a>
                    <?php if( isset( $area_list[$wk] ) && !empty( $area_list[$wk] ) ) { ?>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">Areas</li>
                        <?php foreach( $area_list[$wk] as $ak => $av ) { ?>
                        <li>
							<a href="#" data-id="<?php echo $ak ?>"
							data-list-id="<?php echo $key . '_' . $wk . '_' . $ak ?>"
							name='move_to' class="target">
								<i class="fa fa-list-alt"></i> <?php echo _substr_text( $av['area_title'], 20, true ); ?>
							</a>
						</li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>

                <?php } ?>
            </ul>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>


<script type="text/javascript">
	$(window).load(function () {
	setTimeout(function(){
		$('.ellipsis-word').ellipsis_word();
		//$('.key_target').textdot();
	}, 100)

})


	$(function(){

		function resizeStuff() {
			$('.ellipsis-word').ellipsis_word();
			//$('.key_target').textdot();
		}

		$('body').delegate('.sidebar-toggle', 'click', function() {
			if( !$('body').hasClass('sidebar-collapse') ) {
				//$.popover_hack();
			}
			setTimeout(function(){

				$('.ellipsis-word').ellipsis_word();
				$('.ellipsis-word').ellipsis_word();
				$('.ellipsis-word').ellipsis_word();
				console.log("");
			},1500);
		})

		$js_config.workspace_id = <?php echo json_encode( $workspace_id ); ?>;



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
					//$(this).css({'height': $.box_height , 'max-height': $.box_height })
					$(this).css({  'max-height': $.box_height })
				}
			})
		})();

		$(window).on('resize', $.setMaxHeight);

	})
</script>