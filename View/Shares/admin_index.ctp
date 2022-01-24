<style>
.connectedSortable td {
	vertical-align: inherit !important;
}
.small-box.bg-green.panel .inner {
		cursor: pointer !important;
}
.small-box.bg-green.panel .inner p.text-muted.date-time {
		font-size: 11px !important;
}
.small-box.bg-green.panel .inner p.text-muted.date-time:nth-child(1) {
	border-right: 1px solid #ffffff !important;
	margin-right: 5px !important;
	padding-right: 5px !important;
}

</style>

<?php //pr($this->Session->read('Auth'));

echo $this->Html->script('templates/create.workspace', array('inline' => true)); ?>

<?php echo $this->Html->script('projects/colored_tooltip', array('inline' => true)); ?>

<script type="text/javascript">
jQuery(function($) {
	
	$("body").delegate("#slide_out_trigger1", "click", function(event) {
			var $slide_out_menus = $(this).parents('#top_menus').find("#slide_out_menus"); 
			
			var $icon = $(this).find("i");  
			
			$slide_out_menus.animate({ opacity: 1 }, 500, function() { 
				$slide_out_menus.toggleClass('opened closed')
				$icon.toggleClass("fa-chevron-left fa-chevron-right")
			})
	})

	$js_config.add_ws_url = '<?php echo Router::Url(array( "controller" => "templates", "action" => "create_workspace", $projects['Project']['id'] ), true); ?>/';

	$js_config.step_2_element_url = '<?php echo $this->Html->url(array( "controller" => "projects", "action" => "manage_elements" ), true); ?>/';


	$('.has-create-referer').tooltip();
	$('.has-create-referer')
		.tooltip('show')
		.click(function(event) {
				event.preventDefault();
				$(this).removeData('bs.tooltip')
		})

// SHOW TOOLTIP ON EACH COLORBOX ON HOVER
// SET BOX BACKGROUND COLOR CLASS WITH CONTENT WRAPPER DIV
	$('.el_color_box').colored_tooltip();
	
	var url = '<?php echo Router::Url(array( "controller" => "projects", "action" => "lists"), true); ?>';
	$('body').find("#btn_go_back").attr('href', url)
	$('body').delegate("#btn_go_back", 'click', function(event) {
		var newurl = '<?php echo Router::Url(array( "controller" => "projects", "action" => "lists"), true); ?>';
		$(this).attr("href", newurl);
		window.location = url;
	})
})
</script>

<?php 
$summary = null;
 
?>

<div class="row">
	<div class="col-xs-12">

	<div class="row">
		<section class="content-header clearfix">
			<h1 class="pull-left">
				
				<?php
					
				if( isset($projects) && !empty($projects) ){
						$project_detail = $projects;
				 
				echo $this->ViewModel->_substr( $project_detail['Project']['title'], 60, array('html' => true, 'ending' => '...')); ?>
				
				<p class="text-muted date-time">
					<span>Created: <?php 
					//echo date('d M Y h:i:s', $project_detail['Project']['created']);
					echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['created']),$format = 'd M Y h:i:s');					
					?></span>
					<span>Updated: <?php 
					//echo date('d M Y h:i:s', $project_detail['Project']['modified']); 
					echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['modified']),$format = 'd M Y h:i:s');
					?></span>
				</p>
					<?php }
				else {
					echo "Project Summary";
				}	?>
			</h1>
			<?php
				// LOAD PARTIAL FILE FOR TOP DD-MENUS
				echo $this->element('../Projects/partials/admin_project_dd_menus', array('val' => 'testing'));
			?>

		</section>
    </div>


    <?php

	if( isset($projects) && !empty($projects) ){
		$project_detail = $projects;
		// pr($project_detail,0);
		?>
        <script type="text/javascript">
            ajaxObject = {
                ajaxUrl : '<?php echo Router::url(array('controller' => 'projects', 'action' => 'sortOrderWorkspaces')); ?>',
                id : '<?php echo $projects['Project']['id'] ?>',
            }
        </script>

    <div class="box-content">
		<div class="row ">
			<div class="col-xs-12">
				<div class="box noborder margin-top">
				
				
					<div class="box-header nopadding">
					
						<!-- Modal Boxes -->
						<!-- Modal Large -->
						<div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm">
								<div class="modal-content"></div>
							</div>
						</div>
						<!-- /.modal -->

						<!-- Modal Large -->
						<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<div class="modal-content"></div>
							</div>
						</div>
						<!-- /.modal -->

						<!-- Modal Large -->
						<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm">
								<div class="modal-content"></div>
							</div>
						</div>
						<!-- /.modal -->

						<!-- Modal Confirm -->
						<div class="modal modal-warning fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">

								</div>
							</div>
						</div>

						<!-- /.modal -->
</div>
					

					<div class="box-body nopadding">

					<?php
					
				if( isset($project_detail['Project']['ProjectWorkspace']) && !empty($project_detail['Project']['ProjectWorkspace']) ) {
						
						

					$ProjectWorkspaces = $project_detail['Project']['ProjectWorkspace'];

					?>
						<?php echo $this->Form->create('Project', array( 'url' => array('controller'=>'projects', 'action'=>'sortOrderWorkspaces'),  'class' => 'form-horizontal form-bordered', 'id' => 'dd-form')); ?>

						<?php echo $this->Form->input('ProjectWorkspace.project_id', array('label' => false, 'id' => 'project_id', 'type' => 'hidden', 'value' => $project_detail['Project']['id']));?>

						<?php echo $this->Form->input('ProjectWorkspace.sort_order', array('label' => false, 'type' => 'hidden', 'value' => '', 'id' => 'sort_order'));?>

						<!-- <input type="checkbox" style="display:none;" value="1" name="autoSubmit" id="autoSubmit" checked="checked" /> -->
						<?php echo $this->Form->end(); ?>

						<table  class="table table-bordered" >
							<colgroup>
								<col width="1"/>
								<col width="1"/>
								<col width="1"/>
								<col width="1"/>
							</colgroup>
							<thead>
								<tr>
									<th width="30%" style="text-align:left">Workspaces</th>
									<th width="40%" style="text-align:center">Status</th>
									<th width="15%" style="text-align:center">Detail</th>
									<th width="15%" style="text-align:right">Actions</th>
								</tr>
							</thead>

							<tbody class="connectedSortable" id="sortable-list">
								<?php
								$pw_id_counter = 0;
								
								foreach ($project_detail['Project']['ProjectWorkspace'] as $project_workspace ) {
								
								// $wrsp = $this->ViewModel->getProjectWorkspaces($project_workspace['project_id']); 
								
								
								// pr($project_workspace, 1);
								$workspaceArray = ( isset($project_workspace['Workspace']) && !empty($project_workspace['Workspace']))
															? $project_workspace['Workspace']
															: null;
										
								// Show only the workspaces that are selected to display into the list. This status field is also used to show workspace names in leftbar menus. 
								$leftbar_status = $project_workspace['leftbar_status'];
								
							if( $leftbar_status ) {

								if( isset($workspaceArray['id']) && !empty($workspaceArray['id'])) {

									$totalValidAreas = $totalElements = $inUsed = $percent = 0;

										//
									if( isset($workspaceArray['Area']) && !empty($workspaceArray['Area']) ) {

										$areas = $workspaceArray['Area'];
										$areas_tot = ( isset($areas) && !empty($areas) ) ? count($areas) : 0;
 										
										if ( $totalValidAreas = $areas_tot ) {

											$progress_data = $this->ViewModel->countAreaElements($workspaceArray['id']);

											if( isset($progress_data) && !empty($progress_data) ) {
												$totalValidAreas 	= $progress_data['area_count'];
												$inUsed 			= $progress_data['area_used'];
												$totalElements 		+= $progress_data['element_count'];
											}
										}
										
										$totalValidAreas = ($totalValidAreas == 0) ? $totalValidAreas + 1 : $totalValidAreas;
										
										$totalValidAreas = ($totalValidAreas > 1) ? $totalValidAreas - 1 : $totalValidAreas;
										$percent = ($inUsed) ? ($inUsed * 100) / $totalValidAreas : 0;
									}

									$class_name = (isset($workspaceArray['color_code']) && !empty($workspaceArray['color_code']))
														? $workspaceArray['color_code'] : 'bg-gray';
								?>
								<?php $create_elements_link =  Router::url(array('controller' => 'projects', 'action' => 'manage_elements', $project_detail['Project']['id'], $workspaceArray['id'])); ?>
								<tr id="<?php echo $project_workspace['id']; ?>">
<td >
	<div class="small-box panel <?php echo $class_name ?>">

		<a class="inner" href="<?php echo $create_elements_link; ?>">

				<strong><?php echo _substr_text( $workspaceArray['title'], 20 );
				 ?></strong>

			<span class="text-muted date-time">
				<span>Created:
				<?php 
				//echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? date('d M Y', strtotime($workspaceArray['created'])) : 'N/A'; 
				echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['created'])),$format = 'd M Y') : 'N/A'; 
				
				?></span>
				<span>Updated:
				<?php 
				//echo  ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? date('d M Y', strtotime($workspaceArray['modified'])) : 'N/A';
				echo  ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['modified'])),$format = 'd M Y') : 'N/A';

				?></span>
			</span>

			<!--
				<span class="last"><?php echo $totalElements . ' Elements'; ?> <i class="fa fa-arrow-circle-right"></i></span>
			-->
			<span class="pull-right el-icons" style="margin: -60px 0 0 0">

				<span class="btn btn-default btn-xs color_bucket tipText" title="<?php echo tipText('Color Options') ?>" href="#"  data-placement="auto" ><i class="fa fa-paint-brush"></i></span>

				<small class="display_none ws_color_box">
					<small class="colors btn-group">
						<b data-color="bg-red" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
						<b data-color="bg-blue" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></b>
						<b data-color="bg-maroon" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
						<b data-color="bg-aqua" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
						<b data-color="bg-yellow" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
						<!-- <b data-color="bg-orange" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Orange"><i class="fa fa-square text-orange"></i></b>	-->
						<b data-color="bg-teal" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></b>
						<b  data-color="bg-purple" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></b>
						<b data-color="bg-navy" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></b>
						<b data-color="bg-gray" data-remote="<?php echo SITEURL.'workspaces/update_color/'.$workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Remove Color"><i class="fa fa-times"></i></b>
					</small>
				</small>
			</span>
		</a>
	</div>
</td>

									<td> 
										<h5><?php echo $inUsed . '/' . $totalValidAreas; ?> Areas</h5>
										<div class="progress-wrapper <?php echo $class_name ?>">
											<div class="progress progress-xs" style="<?php if( empty($percent)){ ?> <?php } ?>">
												<div style="width: <?php echo $percent; ?>%; " class="progress-bar progress-bar-danger"></div>
											</div>
										</div>
									</td>

									<td>
										<span class="pull-right el-icons">

											<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Number of elements created') ?>" href="#">E <span class="label bg-black"><?php echo $totalElements; ?></span></span>

											<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Number of links in all elements') ?>"  href="#"><i class="fa fa-link"></i><span class="label bg-black">0</span></span>

											<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Number of notes in all elements') ?>"  href="#"><i class="fa fa-file-text-o"></i><span class="label bg-black">0</span></span>

											<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Number of documents in all elements') ?>"  href="#"><i class="fa fa-folder-o"></i><span class="label bg-black">0</span></span>

											<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Number of elements status') ?>"  href="#"><i class="fa fa-info"></i><span class="label bg-black">0</span></span>

										</span>


									</td>

									<td>
										<div class="btn-group pull-right">

	<a class="btn <?php echo $class_name ?> tipText" title="<?php tipText('Edit Workspace', false ); ?>"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_detail['Project']['id'], $workspaceArray['id'], 'admin' => TRUE ), TRUE); ?>" id="btn_select_workspace" >
		<i class="fa fa-fw fa-pencil"></i>
	</a>


	<a class="btn <?php echo $class_name ?> tipText" title="<?php tipText('View/Open Workspace', false ); ?>"  href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'manage_elements', $project_detail['Project']['id'], $workspaceArray['id'])); ?>" >
		<i class="fa fa-fw fa-folder-open"></i>
	</a>

	<button class="btn <?php echo $class_name ?> tipText" title="<?php tipText('Move to Trash', false ); ?>"   type="button" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'trashWorkspace', $workspaceArray['id'])); ?>" id="confirm_delete" data-confirm-msg="Are you sure you want to send this item to trash?" data-target="<?php echo $workspaceArray['id']; ?>" >
		<i class="fa  fa-trash-o"></i>
	</button>

										</div>
									</td>

								</tr>
								<?php }
								}
							} ?>
							</tbody>

						</table>

					<?php }
					else {
						$message = $html = '';
						if( isset($create_referer) && !empty($create_referer) ) {
								
							$message = 'You have successfully created a new project.';
							
							$link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => TRUE ), TRUE);
							
							$html = "Click<a class='' href='".$link."'> here </a>to create workspace for this project.";
						}
						else {
								
							$message = 'You have not created any workspace yet.';
							
							$link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => TRUE ), TRUE);
							
							$html = "Click<a class='' href='".$link."'> here </a> to create a new workspace now.";
							 
						}
						echo $this->element('../Projects/partials/error_data', array(
								'error_data' => [
									'message' => $message,
									'html' => $html
							]
						));
					 } 
					 ?>


				   </div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</div>


<script  type="text/javascript">
    $(document).ready(function () {

		$('#modal_medium').on('show.bs.modal', function (e) {

			$(this).find('.modal-content').css({
				width: $(e.relatedTarget).data('modal-width'), //probably not needed
			});
		});

		$('#modal_medium').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});

		$("body").delegate('#dataConfirmOK', 'click', function(event){

            event.preventDefault()
			var data = $( this ).data();

            var target = data.target;
            var url = data.remote;
			$.ajax({
				url: url,
				data: $.param({
							'data[Workspace][project_id]': <?php echo $project_detail['Project']['id'] ?>,
							'data[Workspace][id]': target
						}),
                type: 'post',
                complete: function (response) {
					$('#dataConfirmModal').modal('hide');
                    
					$('#dataConfirmModal').fadeOut("300", function() {
						setTimeout(function() {
							$('tr#'+target).children('td')
								.animate(
									{
										padding: 0,
										backgroundColor: "#D73925",
										opacity: 0.1
									}, 800
								)
								.wrapInner('<div />')
								.children()
								.hide( 1000, function() {
									$('tr#'+target).remove();

									if( $("#sortable-list").children('tr').length <= 0 ) {

										var loc = window.location.href;
										window.location.replace(loc);
									}

								});

						}, 1500)
					})
                }
            });
        })

		$.create_bt_modal = function($el) {

			var modal = '<div class="modal modal-warning fade" id="dataConfirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
					'<div class="modal-dialog">' +
					'<div class="modal-content"> ' +

					'<div class="modal-header">' +
					'	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
					'			<h4 class="modal-title" id="myModalLabel">Delete Confirm</h4>' +
					'</div>' +
					'<div class="modal-body">' +
					'</div>' +
					'<div class="modal-footer">' +
					'	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
					'	<a class="btn btn-danger btn-ok" id="dataConfirmOK">Delete</a>' +
					'</div>' +

					'</div>' +
					'</div>' +
			'</div>';

			$el.append(modal);
		}

		$('button#confirm_delete').click(function(ev) {
			// var href = $(this).data('remote');
			var data = $(this).data();
			if ( !$('#dataConfirmModal').length ) {
				$.create_bt_modal($('.box-header'))

			}
			$('#dataConfirmModal').find('.modal-body').html($(this).data('confirm-msg'));
			$('#dataConfirmOK').data(data);
			$('#dataConfirmOK').data('href', data.remote);

			$('#dataConfirmModal').modal({show:true});

			// return false;
		});


		$('.color_bucket').each(function () {
			var $color_box = $(this).parents(".panel").find('.ws_color_box')

			$(this).data('ws_color_box', $color_box)
			$color_box.data('color_bucket', $(this))
		})

		$('.color_bucket').on('click', function (event) {
			event.preventDefault();
			var $color_box = $(this).parents(".panel").find('.ws_color_box')

			$color_box.slideToggle(200);
		});

		$(".el_color_box").on('click', function( event ) {
			event.preventDefault();

			var $cb = $(this)
			var applyClass = $cb.data('color')
			var $tr = $cb.parents("tr.ui-sortable-handle:first")
			var $td0 = $("td:eq(0)", $tr)
			var $td1 = $("td:eq(1)", $tr)
			var $td2 = $("td:eq(2)", $tr)
			var $td3 = $("td:eq(3)", $tr)


			var $panel = $td0.find('.panel:first')
			var cls = $panel.attr('class')

			var foundClass = (cls.match (/(^|\s)bg-\S+/g) || []).join('')
			if( foundClass != '' ) {
				$panel.removeClass(foundClass)
			}

			$panel.addClass(applyClass);

			$.each( $('.progress-wrapper', $td1), function(i, v) {
				var $_btn = $(v);
				var _cls = $_btn.attr('class')
				var foundClass = ( _cls.match (/(^|\s)bg-\S+/g) || []).join('')
				if( foundClass != '' ) {
					$_btn.removeClass(foundClass)
				}

				$_btn.addClass(applyClass);
			} )


			$.each( $('.btn', $td2), function(i, v) {
				var $span_btn = $(v);
				var span_cls = $span_btn.attr('class')
				var foundClass = (span_cls.match (/(^|\s)bg-\S+/g) || []).join('')
				if( foundClass != '' ) {
					$span_btn.removeClass(foundClass)
				}
				$span_btn.addClass(applyClass);
			} )

			$.each( $('.btn', $td3), function(i, v) {
				var $_btn = $(v);
				var _cls = $_btn.attr('class')
				var foundClass = ( _cls.match (/(^|\s)bg-\S+/g) || []).join('')
				if( foundClass != '' ) {
					$_btn.removeClass(foundClass)
				}

				$_btn.addClass(applyClass);
			} )


			$(this).setPanelColorClass();


			// SEND AJAX HERE TO CHANGE THE COLOR OF THE ELEMENT
		})

		$.fn.setPanelColorClass = function() {

			var url = $(this).data('remote');
			var color_code = $(this).data('color');
			var data = $.param({'color_code': color_code});

			$.ajax({
				type:'POST',
				data: data,
				url: url,
				global: true,
				beforeSend: function(res){
					// $("#loader_div").fadeIn(100)
				},
				complete: function(data, textStatus, jqXHR) {

					// $("#loader_div").fadeOut(300)
				},
				success: function( response, status, jxhr ) {
					if( status == 'success' ) {
						console.log('success')
					}
					else {
						console.log('error')
					}

				},
			});
		}

		$('body').on('click', function (e) {
			$('.color_bucket').each(function () {
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if ( !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.ws_color_box').has(e.target).length === 0) {
					var color_box = $(this).data('ws_color_box')
					color_box.slideUp(300)
				}
			});
		});

	});



</script>

    <?php }
		else{
			echo $this->element('../Projects/partials/error_data', array(
							'error_data' => [
									'message' => "You have not created any project yet.",
									'html' => "Click<a class='' href='".Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => TRUE ), TRUE)."'> here </a>to create project now."
								]
						));
						
			?>



       <!--  <div class="box project_box" style="height:400px"><a href="#"> Create Project! </a> </div>	-->

		<script type="text/javascript" >
			$(function() {
				// var loc = '<?php echo SITEURL ?>projects';
				// window.location.replace(loc);
			})
		</script>
    <?php } ?>
</div>
</div>
