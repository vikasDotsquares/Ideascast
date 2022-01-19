<?php
	echo $this->Html->css('projects/dropdown');
	echo $this->Html->css('projects/program_center.min');

	echo $this->Html->css('projects/gs_multiselect/multi.select');
	echo $this->Html->script('projects/plugins/gs_multiselect/multi.select', array('inline' => true));

	echo $this->Html->script('projects/program_center.min', array('inline' => true));

$disable_class = "";
if(!isset($programs) || empty($programs)){
	$disable_class = "no-data-row";
}
?>
<style type="text/css">
	.no-data-row {
		pointer-events: none;
		opacity: 0.7;
	}
</style>
<div class="row">

	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time" style="padding: 6px 0">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>

			</section>
		</div>

		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">

                    <div class="box noborder nomargin" style="border-radius: 0px 0px 3px 3px;">

                        <div class="box-header filter-header border" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="model_bx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-4">
                            	<div class="multi-select <?php echo $disable_class; ?>">
									<label class="custom-dropdown" style="width: 100%;" id="listCategories" >
									<select class="aqua" name="data[Project][id]"> </select>
									</label>
								</div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
								<label class="custom-dropdown" style="width: 100%;" id="listRAG" >
									<select class="aqua <?php echo $disable_class; ?>" id="rag_status" name="data[Project][rag_status]">
										<option value="">All RAG</option>
										<option value="1">RED</option>
										<option value="2">AMBER</option>
										<option value="3">GREEN</option>
									</select>
								</label>
                            </div>
                            <div class="col-sm-4 col-md-6 col-lg-6 padding-right9">
                            	<div class="s-buttons form-group pull-right ipad-button-filter" style="margin-bottom: 0; padding-left:15px">
									<a href="javascript:void(0);" id="createprogram" class="btn btn-success btn-sm tipText " data-toggle="modal" data-target="#model_bx" data-backdrop="static" data-remote="<?php echo SITEURL; ?>projects/create_program" data-original-title="Program Manager" >
										<i class="icon_program_white"></i>
									</a>
									<a href="#" id="submit_filter" class="btn btn-success btn-sm <?php echo $disable_class; ?>" style=" ">Apply Filter</a>
									<a href="#" id="reset_filters" class="btn btn-danger btn-sm <?php echo $disable_class; ?>" style=" ">Reset</a>
									<a href="<?php echo SITEURL; ?>dashboards/add_program" title="" class="btn btn-sm btn-warning tipText" data-original-title="Create Program">
									<i class="fa fa-plus"></i> Create Program
								</a>
								</div>
                            </div>
                        </div>
                        <div class="box-body clearfix list-shares" style="min-height: 550px;">
                        	<?php $programs_tot = ( isset($programs) && !empty($programs) )? count($programs) : 0; ?>
							<div class="" id="programs_body" >

								<?php

								echo $this->element('../Dashboards/partials/program_center/filter_program_center', ['program_data' => $program_data, 'program_data_without_proj' => $program_data_without_proj, 'prog_cnt' => $programs_tot]);
								?>
								<!-- <div class="panel-heading" style="position: relative;">
									<h3 class="panel-title">PROGRAMS (0)</h3>
									<a href="#" id="" class="btn btn-primary btn-xs toggle-accordion" accordion-id="#accordion">
										<i class="fa"></i>
									</a>
								</div>
								<div class="panel-body">
									<p class="loading-bar"></p>
								</div> -->
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" >
$(function() {

	$("#model_bx").on("hidden.bs.modal", function(){
			$(".modal-body").html("");
		});

		$.opened_model = $();
		$('#model_bx').on('show.bs.modal', function(event) {
			$.opened_model = $(event.relatedTarget);
		});

		$('#model_bx').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			if( $.opened_model.is('span.annotation') ) {
				var id = $.opened_model.parents('.project-block:first').data('id');
				$.ajax({
					url: $js_config.base_url + 'projects/get_annotation_count/' + id,
					type: "POST",
					data: $.param({}),
					dataType: "JSON",
					global: false,
					success: function (response) {

						if( $.parseJSON(response) > 0 ) {
							$.opened_model.addClass('annotation-black').removeClass('annotation-grey');
							$.opened_model.attr('title', 'Annotations')
						}
						else {
							$.opened_model.addClass('annotation-grey').removeClass('annotation-black');
							$.opened_model.attr('title', 'Annotate')
						}

					}
				})
			}
		});


		$('body').delegate('.delete_annotate', 'click', function(event) {
			event.preventDefault();

			var $parent = $(this).parents('.annotate-item:first'),
				id = $parent.data('id'),
				project_id = $js_config.project_id,
				$annotate_list = $parent.parents('#annotate-list:first');

			$.ajax({
				url: $js_config.base_url + 'projects/delete_annotate/' + id +'/'+ project_id,
				type: "POST",
				data: $.param({}),
				dataType: "JSON",
				global: true,
				success: function (response) {
					$parent.fadeOut(1000, function(){
						$(this).remove();
						if( $annotate_list.find('.annotate-item').length <= 0 ) {
							$annotate_list.html('<div id="no-annotate-list" >No Annotations</div>')
						}
					})
				}
			})

		})

		//Submit Annotate
		$('body').delegate('#submit_annotate', 'click', function(event) {
			event.preventDefault();
			$that = $(this);

			$that.addClass('disabled');

			var selected_value = $('input.project_name:checked').map(function() {
				return this.value;
			}).get();

			var $form = $('#modelFormProjectComment'),
				data = $form.serialize(),
				data_array = $form.serializeArray();

			$.when(
				$.ajax({
					url: $js_config.base_url + 'projects/save_annotate',
					type: "POST",
					data: data,
					dataType: "JSON",
					global: false,
					success: function (response) {
						if(response.success) {
							if(response.content){
                                // send web notification
                                $.socket.emit('socket:notification', response.content.socket, function(userdata){});
                            }
							$form.find('#ProjectCommentId').val('');
							$form.find('#ProjectCommentComments').val('');
							$form.find('#ProjectCommentComments').next().html('');
							$form.find('#clear_annotate').hide()
						}
						else {
							if( ! $.isEmptyObject( response.content ) ) {

								$.each( response.content, function( ele, msg) {

									var $element = $form.find('[name="data[ProjectComment]['+ele+']"]')
									var $parent = $element.parent();
									$form.find('#ProjectCommentComments').val('');
									if( $parent.find('span.error-message.text-danger').length  ) {
										$parent.find('span.error-message.text-danger').text(msg)
									}
								})
								$that.removeClass('disabled');

							}
						}

					}
				})
			).then(function( data, textStatus, jqXHR ) {
				if(data.success) {
					$.ajax({
						url: $js_config.base_url + 'projects/get_annotations/' + data.content[0],
						type: "POST",
						data: $.param({}),
						dataType: "JSON",
						global: false,
						success: function (responses) {
							$('#annotate-list', $('body')).html(responses)
							$that.removeClass('disabled');
						}
					})
				}
			})
		})


        $(document).on('click.bs.dropdown.data-api', '.select-drop-wrap', function(e) {
            e.stopPropagation();
        });


})
</script>
<style type="text/css">
    .project-multi-select {
        margin-right: 5px;
    }
    .select-drop-wrap {
        font-size: 12px;
    }
    .select-drop {
        border: 1px solid #00acd6;
        border-radius: 0px;
        padding: 2px 10px;
        width: 100%;
        display: inline-block;
        cursor: pointer;
        background: #fff;
    }

    .select-drop .arrow {
        display: inline-block;
        width: 0;
        height: 0;
        margin-left: 2px;
        vertical-align: middle;
        border-top: 4px solid;
        border-right: 4px solid transparent;
        border-left: 4px solid transparent;
        float: right;
        margin-top: 6px;
    }

    .select-drop-wrap.open>.dropdown-menu {
        width: 100% !important;
        box-shadow: 2px 2px 28px #ccc !important;
    }

    .select-drop-list>li>span {
        padding: 4px 15px;
        display: block;
        line-height: 1.22857143;
        cursor: pointer;
    }

    .select-drop-list>li>span:hover,
    .select-drop-list li.selected>span {
        background-color: #00c0ef;
        color: #ffffff;
        background-image: none !important;
    }

    .select-drop-list li>span span.check-mark {
        position: absolute;
        display: none;
        right: 15px;
        margin-top: 0;
        color: #00ffd2;
    }

    .select-drop-list li.selected span span.check-mark {
        display: inline-block;
    }
    .select-drop-list li span.pname {
        display: inline-block;
        position: relative;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 94%;
        font-size: 12px;
    }
    .select-drop-wrap span.select-span {
        display: inline-block;
        position: relative;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 94%;
    }
</style>