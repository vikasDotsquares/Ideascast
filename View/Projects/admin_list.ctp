
<?php echo $this->Html->css('projects/list-grid') ?>

<?php echo $this->Html->script('projects/colored_tooltip', array('inline' => true)); ?>

<script type="text/javascript">
jQuery(function($) {


	$("#ul_list_grid > li > div.box-success").on('click', function(event) {
		var $this = $(this);
		$("#ul_list_grid > li > div.selected").removeClass("selected")
		$this.addClass("selected")
	})


	// SHOW TOOLTIP ON EACH COLORBOX ON HOVER
	// SET BOX BACKGROUND COLOR CLASS WITH CONTENT WRAPPER DIV
	$('.el_color_box').colored_tooltip();


	/*

	$(window).resize(function() {
		var $columns = $('.column'),
		numberOfColumns = $columns.length,
		marginAndPadding = 0,
		newColumnWidth = ($('#container').width() / numberOfColumns) - marginAndPadding,
		newColumnWidthString = newColumnWidth.toString() + "px";

		$columns.css('width', newColumnWidthString);
	}).resize();
 */

})
</script>



<div class="row">
	<div class="col-xs-12">
<?php /* ?>
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"> Projects </h1>
				 <?php
					// LOAD PARTIAL FILE FOR TOP DD-MENUS
			//echo $this->element('../Projects/partials/project_settings', array('val' => 'testing'));
				 ?>

			</section>
		</div>
<?php */ ?>

    <div class="box-content">

            <div class="row ">
                <div class="col-xs-12">


				<?php

				if( isset($projects) && !empty($projects)) {
				?>
                    <div class="box ">
                        <div class="box-header" style="min-height: 30px !important">
                            <h3 class="box-title"><?php echo $page_heading; ?></h3>
							<p class="text-muted date-time">
								<span>View your all projects here.</span>
							</p>
                            <div class="box-tools">
								<div id="viewcontrols" class="btn-group tipText" title="<?php tipText('change-layout' ); ?>">
									<a class="gridview active btn btn-success btn-sm" id="grid_view" data-limit="140"><i class="fa fa-th-large"></i></a>
									<a class="listview btn btn-success btn-sm" id="list_view" data-limit="470"><i class="fa fa-bars"></i></a>

								</div>
								<div class="btn-group">

									<a class="btn btn-warning btn-sm tipText" href="<?php echo $this->request->referer(); ?>"   title="<?php tipText('go-back' ); ?>"><i class="fa fa-fw fa-chevron-left"></i> Back</a>

								</div>
							</div>


							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

                        </div>

                        <div id="list_grid_container" class="box box-body clearfix list-acknowledge">

							<!-- LIST AND GRID VIEW START	-->
							<ul class="grid clearfix" id="ul_list_grid">
								<div class="new-line">
								<?php
								$row_counter = 0;
								$project_counter = ( isset($projects) && !empty($projects) ) ? count($projects) : 0;
								foreach( $projects as $key => $val ) {

									$item = $val['Project'];
								?>
									<?php $open_project_link =  SITEURL.'sitepanel/projects/index/'.$item['id']; ?>


									<li>

										<div class="panel <?php echo $item['color_code'] ?>" data-project="<?php echo $open_project_link; ?>" >

											<div class="panel-heading clearfix">
												<h4 class="panel-title  pull-left tipText" title="<?php tipText($item['title']); ?>">
													<a href="<?php echo $open_project_link; ?>">
												<?php

													echo $this->Text->truncate(
														$item['title'],
														30,
														array(
															'ending' => '...',
															'exact' => false,
															'html' => false
														)
													);
												?>
														</a>
												</h4>

												<div class="btn-group pull-right">

													<a href="#" class="btn btn-default btn-xs color_bucket tipText" title="<?php tipText('Color Options' ); ?>" ><i class="fa fa-paint-brush"></i></a>


												<div class="display_none color_box">
													<div class="colors btn-group">
														<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
														<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
														<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
														<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
														<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
														<a href="#" data-color="panel-orange" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Orange"><i class="fa fa-square text-orange"></i></a>
														<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
														<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
														<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
													</div>
												</div>

													<a class="btn btn-default btn-xs tipText" title="<?php tipText('Edit Project' ); ?>" href="<?php echo SITEURL.'sitepanel/projects/manage_project/'.$item['id']; ?>" >
														<i class="fa fa-pencil"></i>
													</a>

													<a class="btn btn-default btn-xs tipText" title="<?php tipText('View/Open Project' ); ?>" href="<?php echo $open_project_link; ?>" >
														<i class="fa fa-folder-open"></i>
													</a>

												</div>
											</div>

											<div class="panel-body">


												<!-- <div class="list-textcontents">
													<?php
													echo $this->Text->truncate(
															$item['objective'] ,
															150,
															array(
																'ending' => '...',
																'exact' => false,
																'html' => true
															)
														);
													?>

												</div>	-->
												<div class="list-textcontents">

													<div class="pull-right grid-dates" style="">
														<span>Created: <?php echo date('d M Y', $item['created']); ?></span>
														<span>Updated: <?php echo date('d M Y', $item['modified']); ?></span>
													</div>

													<div class="pull-right list-info" style="">
														<span>Created: <?php echo date('d M Y', $item['created']); ?></span>
														<span class="below">Updated: <?php echo date('d M Y', $item['modified']); ?></span>

														<a href="<?php echo SITEURL . 'projects/reports/' . $item['id'] ?>" title="More Detail" class="btn btn-default pull-right btn-sm text-bold more" >More <i class="fa fa-fw fa-angle-double-right"></i></a>
													</div>

													<span class="text_hidden">
														<?php
															echo $this->Text->truncate(
																strip_tags($item['description'], '<br />'),
																	470, // list
																	// 140, // grid
																	array(
																		'ending' => '...',
																		'exact' => false,
																		'html' => true
																)
															);
														?>
													</span>
													<span class="text">
													<?php
													/* echo $this->Text->truncate(
															strip_tags($item['description'], '<br />'),
															470, // list
															// 140, // grid
															array(
																'ending' => '...',
																'exact' => false,
																'html' => true
															)
														); */
													?>
													</span>
													<div class="pull-right grid-button" style="">
														<a href="<?php echo SITEURL . 'projects/reports/' . $item['id'] ?>" title="More Detail" class="btn btn-default pull-right btn-sm text-bold more" >More <i class="fa fa-fw fa-angle-double-right"></i></a>
													</div>

												</div>

											</div>

										</div>

									</li>
									<?php if( ( ($key+1) % 3) == 0 ) {
											echo '</div> <div class="new-line">';
										}
									?>
								<?php

								}
								?>
								</div>
							</ul>

							<div id="loading_model"></div>

					    </div>
                    </div>
				<?php }else {
					echo $this->element('../Projects/partials/error_data', array(
					'error_data' => [
					'message' => "You have not created any project yet.",
					'html' => "Click<a class='' href='".Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE ), TRUE)."'> here </a>to create project now."
					]
					));
				}
				?>
                </div>
            </div>
        </div>
	</div>
</div>


<?php echo $this->Html->script('templates/create.workspace', array('inline' => true)) ?>
<?php echo $this->Html->script('templates/list-grid', array('inline' => true)) ?>
<script type="text/javascript" >
$(function() {


	$('#popup_modal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

	$('#ul_list_grid li .box').on('click', function (e) {

		var loc = $(this).data('project');
		window.location.href = loc;

	});

	$('.color_bucket').each(function () {
		$(this).data('color_box', $(this).next('.color_box'))
		$(this).next('.color_box').data('color_bucket', $(this))
	})

	$('.color_bucket').on('click', function (event) {
		event.preventDefault();

		// $('div.color_box:visible').hide();
		$(this).next('div.color_box').slideToggle(200);
	});

	$(".el_color_box").on('click', function( event ) {
		event.preventDefault();

		var $cb = $(this)
		var $hd = $cb.parents('.panel:first')
		var cls = $hd.attr('class')

		var foundClass = (cls.match (/(^|\s)panel-\S+/g) || []).join('')
		if( foundClass != '' ) {
			$hd.removeClass(foundClass)
		}
		var applyClass = $cb.data('color')

		$hd.addClass(applyClass);

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
			if ( !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.color_box').has(e.target).length === 0) {
				var color_box = $(this).data('color_box')
				color_box.hide()
			}
		});


	});


})
$(window).load( function() {
	setTimeout(function() {
		$.setLimitedText( 140 )
	}, 500)
})
</script>

<style>


.error-inline {
	display: inline-block;
	padding-left: 5px;
	vertical-align: middle;
	font-size: 11px;
	color: #cc0000;
	margin-top: -5px;
}
textarea { resize: none; }
</style>
<script type="text/javascript" >
$(function() {
	// $("body").delegate('.fancy_label', 'click', function(e) {
		// var i = $(this).prev('input:first')
		// i.prop('checked', true)
	// })
	/*
	$("body").on('click', '#add_project', function(e) {
		var $this = $(this);

		var mb = $('#modelBox');
		var tpl = $('#tpl_add_project');
		var data = $this.data();

		// Load template
		if( tpl.length > 0 && !tpl.is(":visible") ) {
			mb.find('.modal-body').html(tpl.html());
			mb.find('.modal-title').text( data.title );

			var form = mb.find('.modal-body').closest('form');
			form.attr('id', "modelForm" + 'AddProject')
				.attr('role', 'form')

			var cancel_btn = form.find('button[data-dismiss=modal]')
			cancel_btn.attr('id', "modelDismiss" + 'AddProject');

			var subm_sc = form.find('button[type=submit]')
			subm_sc.attr('id', "modelSubmit" + 'AddProject');
			subm_sc.html('Save');

			mb.modal('show');

		}
	})

	$('body').delegate('#modelFormAddProject', "submit", function(e){

		e.preventDefault();

		var $form = $(this);

		var mb = $('#modelBox');

		var add_project_url = '<?php echo Router::Url(array('controller' => 'projects', 'action' => 'add_project', 'admin' => FALSE ), TRUE); ?>'

		$.ajax({
			type:'POST',
			dataType: 'json',
			data: $form.serialize(),
			url: add_project_url,
			success: function( response, status, jxhr ) {

				if( response.success ) {
					console.log(response)
					if( !$.isEmptyObject(response.content) ) {
						var insert_id = response.content.id;
						if( insert_id ) {
							mb.modal('hide');
							setTimeout(function() {
								var loc = '<?php echo $this->Html->url(array( "controller" => "templates", "action" => "create_workspace" )); ?>/'+insert_id;
								window.location.replace(loc);
							}, 300)
						}
					}
				}
				else {
					// REMOVE ALL ERROR SPAN
					$form.find("span.error-inline").remove()
					if( ! $.isEmptyObject( response.content ) ) {


						$.each( response.content,
							function( ele, msg) {
								// var elem = ele.replace(/^(.)|\s(.)/g, function($1){ return $1.toUpperCase( ); });

								var $this = $("#"+ele, $form);

								$errHtml = '<span class="error-inline" style="">'+msg+'</span>';
								var $parent = $this.parent();

								if( $parent.children('span.error-inline').length  ) {
									$parent.children('span.error-inline').remove()
								}

								$parent.append($errHtml);
							}
						)

					}
				}


			},
		});
	}) */

})

</script>
