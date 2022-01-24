
<?php echo $this->Html->css('projects/list-grid') ?>
<style type="text/css">

</style>
<script type="text/javascript">
jQuery(function($) {

// PAGE DEPENDENT CONFIGURATIONS -------------------
	$js_config.project_id = '<?php echo $project_id ?>';

	$js_config.template_select_url  = '<?php echo Router::Url(array('controller' => 'templates', 'action' => 'template_select', 'admin' => TRUE ), TRUE); ?>';

	$js_config.get_workspace_url = '<?php echo Router::Url(array('controller' => 'templates', 'action' => 'get_workspace', 'admin' => TRUE ), TRUE); ?>';


	$js_config.add_ws_url = '<?php echo Router::Url(array( "controller" => "templates", "action" => "create_workspace", $project_id ), true); ?>/';

	$js_config.step_2_element_url = '<?php echo $this->Html->url(array( "controller" => "projects", "action" => "manage_elements" ), true); ?>/';

	// END CONFIGURATIONS -------------------


	$( ".el" ).on( "dragcreate", function( event, ui ) {} );
	$( ".area_box" ).on( "dropcreate", function( event, ui ) {} );

	$("#ul_list_grid > li > div.box-success").on('click', function(event) {
		var $this = $(this);
		$("#ul_list_grid > li > div.selected").removeClass("selected")
		$this.addClass("selected")
	})

	$.fn.setTemplateGrid = function(event) {
			var $w = $(window),
				$ul = $("#ul_list_grid"),
				outerWidth = $ul.outerWidth(),
				maxNum = 4,
				liWidth = (( outerWidth * maxNum ) / 100) ;
			liWidth = liWidth.toFixed(2) ;


		// $("#list_grid_container li").each( function(i, v) {
			// console.log($(v).outerWidth())
			// $(v).css('width', ( liWidth+"%"))
		// })
	}
	$(window).on('resize', function(event) {
			// $(this).setTemplateGrid()
	})
	// $(this).setTemplateGrid()
})
</script>


<script type="text/javascript" >
$(function() {
	window.chr = 0;

	/* var refreshId = setInterval( function()
    {
		var colors = [ 'text-red', 'text-blue', 'text-maroon', 'text-aqua', 'text-yellow', 'text-orange', 'text-teal', 'text-purple', 'text-navy' ];
        idx = Math.floor(Math.random() * colors.length);
		console.log(colors[idx])

    }, 1000); */



})


</script>



<div class="row">
	<div class="col-xs-12">

	<div class="row">
        <section class="content-header clearfix">
            <h1 class="pull-left"> Templates </h1>
            <div class="btn-group pull-right">


            </div>
        </section>
    </div>


    <div class="box-content">

            <div class="row ">
                <div class="col-xs-12">


				<?php if( isset($data['templates']) && !empty($data['templates'])) { ?>
                    <div class="box ">
                        <div class="box-header" style="min-height: 30px !important">
                            <h3 class="box-title"><?php echo $data['page_heading']; ?></h3>


							<p class="text-muted date-time">
								<span><?php echo $project_detail['Project']['title']; ?></span>
							</p>


                            <div class="box-tools">
								<div id="viewcontrols" class="btn-group">
								<!--
									<a class="gridview btn btn-success btn-sm" id="grid_view"><i class="fa fa-th-large"></i></a>
									<a class="listview active btn btn-success btn-sm" id="list_view"><i class="fa fa-bars"></i></a>
								-->
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

                        <div id="template_list_container" class="box-body clearfix nopadding-top">

							<!-- LIST AND GRID VIEW START	-->
							<ul id="template_list" class="clearfix">
								<?php foreach( $data['templates'] as $key => $val ) {

									$item = $val['Template'];
									?>
									<li class="col-lg-3 col-md-4 col-sm-6">
										<div class="box box-success" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'popups', 'workspace', $project_id, $item['id'], 'admin' => TRUE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>" data-toggle="modal" data-target="#popup_modal1">

											<div class="box-header"> <h3 class="box-title "><?php echo $item['title'] ?> </h3> </div>
											<div class="box-body clearfix">
												<a title="Select" href="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'popups', 'workspace', $project_id, $item['id'], 'admin' => TRUE ), TRUE); ?>" class="btn btn-success btn-sm select-btn" id="btn_select_workspace" data-id="<?php echo $item['id']; ?>" data-toggle="modal" data-target="#modal_manage_templates"> <i class="fa fa-check"></i> Select </a>
													<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$item['layout_preview'], ['class' => 'thumb']); ?></div>


											</div>

										</div>

									</li>
								<?php } ?>
							</ul>

							<div id="loading_model"></div>

					    </div>
                    </div>
				<?php } ?>
                </div>
            </div>
        </div>
	</div>
</div>


<?php echo $this->Html->script('templates/create.workspace', array('inline' => true)) ?>
<?php echo $this->Html->script('templates/list-grid', array('inline' => true)) ?>
<script type="text/javascript" >
    $('#popup_modal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });
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
