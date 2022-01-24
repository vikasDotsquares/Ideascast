<style>
.error-message {
	display: inline-block;
}
.participant-boxes{
	display: block;
	margin: 0 0 5px;
	width: 100%;
}
#frm_objective_project .form-group {
	margin-bottom: 10px;
}
.btn-white {
	background-color: #FFFFFF;
	border-color: #dedede;
	color: #333;
}
.inset-shadow {
	box-shadow: 3px 3px 4px 1px #999 inset !important;
}
#summary_dashboard input {
	display: none;
}
.objective_projects .panel .panel-body {
	padding: 0;
}
.objective_projects .panel .collapse_expand {
	padding: 15px;
}
</style>

<?php


	echo $this->Html->css('projects/dropdown');
	echo $this->Html->css('projects/objectives');

	echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
	echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));

	echo $this->Html->css('projects/circle_progress');

?>

<script type="text/javascript" >


$(function(){

	/* ******************************** BOOTSTRAP HACK *************************************
	 * Overwrite Bootstrap Popover's hover event that hides popover when mouse move outside of the target
	 * */
	var originalLeave = $.fn.popover.Constructor.prototype.leave;
	$.fn.popover.Constructor.prototype.leave = function(obj){
		console.log(obj)
		var self = obj instanceof this.constructor ?
		obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
		var container, timeout;

		originalLeave.call(this, obj);
		if(obj.currentTarget) {
			container = $(obj.currentTarget).data('bs.popover').tip()
			timeout = self.timeout;
			container.one('mouseenter', function(){
				//We entered the actual popover – call off the dogs
				clearTimeout(timeout);
				//Let's monitor popover content instead
				container.one('mouseleave', function(){
					$.fn.popover.Constructor.prototype.leave.call(self, self);
				});
			})
		}
	};
	/* End Popover
	 * ******************************** BOOTSTRAP HACK *************************************
	 * */


	$("body").delegate("#summary_dashboard", 'click', function (event) {
		event.preventDefault();
		location.href = $js_config.base_url + 'projects/objectives';
		// if( $(this).hasClass('inset-shadow') ) {
			// $(this).find('input[type=checkbox]').prop('checked', false);
			// $(this).removeClass('inset-shadow');
		// }
		// else{
			// $(this).find('input[type=checkbox]').prop('checked', true);
			// $(this).addClass('inset-shadow');
		// }

		// $('#submit_filter').trigger('click');

	})

	$("body").delegate("ul.element-icons-list li span.btn:not(.blocked)", 'click', function (event) {

		var $span = $(this),
			data = $span.data(),
			URL = data.remote;

		if (URL.trim() !== '') {
			window.location.href = URL
		}
	})

	$('#submit_filter').on('click', function(event, project_param) {
		event.preventDefault();

		var $that = $(this),
			$form = $("#frm_objective_project"),
			$iProject = $("[name='data[Project][id]']"),
			$iAligned = $("[name='data[Aligned][id]']"),
			project_id = ($iProject.val() != '') ? $iProject.val() : 0,
			aligned_id = ($iAligned.val() != '') ? $iAligned.val() : 0,
			data = $form.serializeArray(),
			error1 = false,
			error2 = false;

		var summary_view_state = $("#summary_dashboard").find('input[type=checkbox]').prop('checked');
		var summary_view = (summary_view_state == true) ? 1 : 0;


		$(".error-message").text('')

		if( $iProject.val() == '' ) {
			// $("#iProject").text('Please select a project.')
			error1 = true;
		}
		if( $iAligned.val() == '' ) {
			// $("#iAligned").text('Please select project alignment.')
			error2 = true;
		}

		if( project_param != '' && project_param > 0 ) {
			project_id = project_param;
			$('select[name="data[Project][id]"] option').filter(function() {
				//may want to use $.trim in here
				return $(this).val() == project_param;
			}).prop('selected', true);
		}

		var box_urls = {
				filtered_data: $js_config.base_url + 'projects/filtered_data'
			},
			url_params = {project_id: project_id, aligned_id: aligned_id, summary_view: summary_view};

		// $( ".objective_details" ).css('margin', 'auto 40%')


		// $( '.objective_details' ).html('<img src="'+$js_config.base_url+'images/ajax-loader-1.gif" style="margin: auto 40%;">')
		$(".ajax_overlay_preloader").show()
		setTimeout(function(){
			$( ".objective_details" ).load( box_urls.filtered_data, url_params, function( response, status, xhr ) {
				$(".ajax_overlay_preloader").hide()
				var $panels = $('.objective_projects').find('.panel');
				console.log($panels.length)
				if($panels.length == 1) {
					$('#summary_dashboard').show();
				}
				else {
					$('#summary_dashboard').hide();
				}
			})

		}, 1000)

		return;
	})

	<?php if( !isset($project_id) || empty($project_id) ) { ?>
		$("[name='data[Project][id]'] option:first").prop('selected', true);
		$("[name='data[Aligned][id]'] option:first").prop('selected', true);
		setTimeout(function(){
			$('#submit_filter').trigger('click');
		}, 200)
	<?php } ?>

	var href = window.location.href,
	splited = href.split('/'),
	last = splited[splited.length-1];

	if( $.isNumeric(last))
	{
		$('#submit_filter').trigger('click', [last])
	}


	$('#reset_filter').on('click', function(event) {
		event.preventDefault()
		$('[name="data[Project][id]"] :nth-child(1)').prop('selected', true);
		$('[name="data[Aligned][id]"] :nth-child(1)').prop('selected', true);
		// $('#selectBox :nth-child(4)')
	})


	$("body").delegate('.sidebar-toggle', 'click', function(event) {
		$('.objective_projects').find('.panel-body').each(function(){
			if($(this).is(':visible')) {
				var $dfv_wrapper = $(this).find('.dfv_wrapper')
				if( $dfv_wrapper.length > 0 && $dfv_wrapper.is(':visible') ) {

					$dfv_wrapper.find('.idcomp-graphs').each(function(){
						if( $(this).is(':visible') ) {
							var $main = $(this).parents('.main-container:first');
							setTimeout(function(){
								$main
									.find('.show_graphs')
									.trigger('click');
							}, 400)
						}
					})


				}
			}
		})
	})

		/* $("body").delegate('.show_hide_panel', 'click', function(event) {

				event.preventDefault();

				var $panel = $(this).parents('.panel:first'),
					$other_panels = $('.objective_projects').find('.panel[class*=panel-]').not($panel),
					// $panel_body = $panel.find('.panel-body'),
					$panel_body = $panel.find('.collapse_expand'),
					$summary_mode = $panel.find('.summary_mode'),
					$icon = $(this).find('i.fa'),
					pid = $(this).data('id'),
					ddonut_data = $(this).find('.hide.dec').html(),
					fdonut_data = $(this).find('.hide.fed').html(),
					vdonut_data = $(this).find('.hide.vot').html(),
					run_first = true;

			var summary_view_state = $("#summary_dashboard").find('input[type=checkbox]').prop('checked');
			var summary_view = (summary_view_state == true) ? 1 : 0;
			if( summary_view > 0 ) {
				$other_panels.find('.summary_mode').slideUp(1000);
				$summary_mode.slideToggle('slow');
			}
			else {
				$other_panels.find('.collapse_expand').slideUp(1000);
				$panel_body.slideToggle('slow');
			}

				$icon.toggleClass('fa-minus fa-plus');

				// $other_panels.find('.panel-body').slideUp(1000);


				$other_panels.find('.show_hide_icon').removeClass('fa-minus').addClass('fa-plus');

				$other_panels.find('.show_dfv').trigger('click', [1])

		}) */
	$("body").delegate('.show_hide_panel', 'click', function(event) {

		event.preventDefault();

		var $panel = $(this).parents('.panel:first'),
			$panel_body = $panel.find('.collapse_expand'),
			$summary_mode = $panel.find('.summary_mode'),
			$icon = $(this).find('i.fa'),
			$panels = $('.objective_projects').find('.panel');
			console.log($panels.length)
			if($panels.length == 1) {
				$('#summary_dashboard').show();
				$panel_body.slideToggle('slow');
			}
			else {
				$('#summary_dashboard').hide();
				$summary_mode.slideToggle('slow');
			}
		$icon.toggleClass('fa-minus fa-plus');
	})

		$("body").delegate(".show_graphs", 'click', function (event) {
			event.preventDefault();

			var $this = $(this),
				data = $this.data(),
				$heading = $this.parents('.idcomp-heading:first'),
				$desc = $heading.next('.idcomp-descp:first'),
				$graphs = $desc.find('.idcomp-graphs:first'),
				$lists = $desc.find('.idcomp-lists:first'),
				project_id = $heading.find('input[name='+data.type+']').val();

			setTimeout(function(){

				if( data.type != '' ) {
						if(data.type == 'dec_project_id' && !$.isEmptyObject($.dec_donut[project_id]) )
							$.dec_donut[project_id].redraw();

						if(data.type == 'fed_project_id' && !$.isEmptyObject($.fed_donut[project_id]) )
							$.fed_donut[project_id].redraw();

						if(data.type == 'vot_project_id' && !$.isEmptyObject($.vot_donut[project_id]) )
							$.vot_donut[project_id].redraw();
				}

			}, 400)

			$graphs.slideDown(300)
			$lists.slideUp(300)

		})

		$("body").delegate(".show_listing", 'click', function (event) {
			event.preventDefault();

			var $this = $(this),
				$heading = $this.parents('.idcomp-heading:first'),
				$select = $heading.find('select:first'),
				$desc = $heading.next('.idcomp-descp:first'),
				$graphs = $desc.find('.idcomp-graphs:first'),
				$lists = $desc.find('.idcomp-lists:first');

			$select.trigger('change');

			$lists.slideDown(300)
			$graphs.slideUp(300)

		})

		$("body").delegate(".show_dfv", 'click', function (event, params) {
			event.preventDefault();

			var $this = $(this),
				data = $this.data(),
				$dfv_more = $this.parent(),
				$dfv_wrapper = $this.parent().next('.dfv_wrapper:first');

			if( params == undefined ) {
				if( !data.ajax ) {
					$dfv_more.find('.loader').removeClass('hide')
					$.ajax({
						url: $js_config.base_url + 'projects/objective_dfv',
						data: $.param({
								project_id: data.pid
						}),
						global: true,
						type: 'post',
						dataType: 'json',
						success: function (response) {
							$dfv_more.find('.loader').addClass('hide')
							$dfv_wrapper.html(response)

						}
					});

					$this.data('ajax', true)
				}
				var tooltip = 'Show Less';
				var txt = 'Show less <span style="text-decoration; none; color: #fff; font-weight: normal;">(Decisions/Feedback/Votes)</span>';
				if( $dfv_wrapper.is(':visible') ) {
					txt = 'Show more <span style="text-decoration; none; color: #fff; font-weight: normal;">(Decisions/Feedback/Votes)</span>';
					$dfv_wrapper.fadeOut('slow')
					tooltip = 'Show More';
				}
				else {
					$dfv_wrapper.fadeIn('slow')
				}
				$this.html(txt).attr('data-original-title', tooltip )
			}
			else {
				$dfv_wrapper.fadeOut('slow')
				$this.html('Show more <span style="text-decoration; none; color: #fff; font-weight: normal;">(Decisions/Feedback/Votes)</span>').attr('data-original-title', 'Show more')
				// $dfv_wrapper.load( $js_config.base_url + 'projects/objective_dfv', {project_id: data.pid}, function( response, status, xhr ) { })
			}
		})

	$('body').delegate('[name=dec_workspace_id]', 'change', function(event) {
		var run_ajax = true,
			$wrapper = $(this).parent(),
			val = $(this).val(),
			project_id = $wrapper.prev('[name=dec_project_id]').val(),
			params = {
				workspace_id: val,
				project_id: project_id
			},
			$main = $(this).parents('.idcomp-inner-first:first');

		if( run_ajax ) {

			// Show Data on Donut Chart
			$.ajax({
				url: $js_config.base_url + 'projects/objective_decision_chart',
				data: $.param(params),
				type: 'post',
				dataType: 'json',
				success: function (response) {
					$main.find('.chart_overlay').hide()
					if( response.success ) {
						if( response.content.chart_filled ) {
							$.dec_donut[project_id].setData(response.content.chart_data)
							$.dec_donut[project_id].redraw()

							$main.find('.dc_detail_'+project_id).find('.donut-detail.first a').text(response.content.chart_data[0].value + ' Progressing')
							$main.find('.dc_detail_'+project_id).find('.donut-detail.second a').text(response.content.chart_data[1].value + ' Completed')

						}
						else {
							$main.find('.chart_overlay').show()

							$main.find('.dc_detail_'+project_id).find('.donut-detail.first a').text('0 Progressing')
							$main.find('.dc_detail_'+project_id).find('.donut-detail.second a').text('0 Completed')

						}
					}
				}
			});

			// Show Listing Data
			$.ajax({
				url: $js_config.base_url + 'projects/objective_dfv_decisions',
				data: $.param(params),
				type: 'post',
				dataType: 'json',
				success: function (response) {
					$main.find('.idcomp-lists').html(response)
				}
			});


			run_ajax = false;
		}
	})

	$('body').delegate('[name=fed_workspace_id]', 'change', function(event) {
		var run_ajax = true,
		$wrapper = $(this).parent(),
		val = $(this).val(),
		project_id = $wrapper.prev('[name=fed_project_id]').val(),
		params = {
			workspace_id: val,
			project_id: project_id
		},
		$main = $(this).parents('.idcomp-inner-first:first')

		if( run_ajax ) {

			// Show donut chart data
			$.ajax({
				url: $js_config.base_url + 'projects/objective_feedback_chart',
				data: $.param(params),
				type: 'post',
				dataType: 'json',
				success: function (response) {
					$main.find('.chart_overlay').hide()
					if( response.success ) {
						if( response.content.chart_filled ) {

							$.fed_donut[project_id].setData(response.content.chart_data)
							$.fed_donut[project_id].redraw()

							$main.find('.fed_detail_'+project_id).find('.donut-detail.first a').text(response.content.chart_data[0].value + ' Live')
							$main.find('.fed_detail_'+project_id).find('.donut-detail.second a').text(response.content.chart_data[1].value + ' Completed')
						}
						else {
							$main.find('.chart_overlay').show()

							$main.find('.fed_detail_'+project_id).find('.donut-detail.first a').text('0 Live')
							$main.find('.fed_detail_'+project_id).find('.donut-detail.second a').text('0 Completed')
						}
					}
				}
			});


			// Show Listing Data
			$.ajax({
				url: $js_config.base_url + 'projects/objective_dfv_feedbacks',
				data: $.param(params),
				type: 'post',
				dataType: 'json',
				success: function (response) {
					$main.find('.idcomp-lists').html(response)
				}
			});

			run_ajax = false;
		}
	})

	$('body').delegate('[name=vot_workspace_id]', 'change', function(event) {
		var run_ajax = true,
			$wrapper = $(this).parent(),
			val = $(this).val(),
			project_id = $wrapper.prev('[name=vot_project_id]').val(),
			params = {
				workspace_id: val,
				project_id: project_id
			},
			$main = $(this).parents('.idcomp-inner-first:first')

		if( run_ajax ) {

			$.ajax({
				url: $js_config.base_url + 'projects/objective_vote_chart',
				data: $.param(params),
				type: 'post',
				dataType: 'json',
				success: function (response) {
					$main.find('.chart_overlay').hide()
					if( response.success ) {
						if( response.content.chart_filled ) {

							$.vot_donut[project_id].setData(response.content.chart_data)
							$.vot_donut[project_id].redraw()

							$main.find('.vot_detail_'+project_id).find('.donut-detail.first a').text(response.content.chart_data[0].value + ' Live')
							$main.find('.vot_detail_'+project_id).find('.donut-detail.second a').text(response.content.chart_data[1].value + ' Completed')
						}
						else {
							$main.find('.chart_overlay').show()
							$main.find('.vot_detail_'+project_id).find('.donut-detail.first a').text('0 Live')
							$main.find('.vot_detail_'+project_id).find('.donut-detail.second a').text('0 Completed')

						}
					}
				}
			});

			// Show Listing Data
			$.ajax({
				url: $js_config.base_url + 'projects/objective_dfv_votes',
				data: $.param(params),
				type: 'post',
				dataType: 'json',
				success: function (response) {
					$main.find('.idcomp-lists').html(response)
				}
			});

			run_ajax = false;
		}
	})

	$('body').delegate('.square-text', 'click', function(event){
		event.preventDefault();

		var $this = $(this),
			data = $this.data(),
			$main = $(this).parents('.main-container'),
			$list_icon = $main.find('.show_listing'),
			url = '',
			$select = $({}),
			$graphs = $main.find('.idcomp-graphs:first'),
			$lists = $main.find('.idcomp-lists:first');

		if( data.value != '' ) {
				if( data.value == 'decision') {
					$select = $main.find('[name=dec_workspace_id]');
					url = $js_config.base_url + 'projects/dfv_decisions_list';
				}
				else if( data.value == 'feedback') {
					$select = $main.find('[name=fed_workspace_id]');
					url = $js_config.base_url + 'projects/dfv_feedbacks_list';
				}
				else if( data.value == 'vote') {
					$select = $main.find('[name=vot_workspace_id]');
					url = $js_config.base_url + 'projects/dfv_votes_list';
				}
		}
		console.log(data);

		var params = {
				workspace_id:  ($select.length > 0) ? $select.val() : 0,
				project_id: data.pid,
				type: data.type,
			};

		if( url != '' ) {
			// Show Listing Data
			$.ajax({
				url: url,
				data: $.param(params),
				global: true,
				type: 'post',
				dataType: 'json',
				success: function (response) {
					$main.find('.idcomp-lists').html(response)

					$graphs.fadeOut(350, function(){
						$lists.fadeIn(300)
					})

				}
			});
		}



	})


})
</script>
<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>

			</section>
		</div>

		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">

					<div class="panel panel-default bg-lgray" style="margin-bottom: -9px; border-radius: 3px 3px 0px 0px; border-bottom: 2px solid #dddddd; margin-top: 10px;">
						<div class="panel-body" style="padding: 15px 0 0;">
							<?php
							echo $this->Form->create('ProjectObjective', array('url' => ['controller' => 'projects', 'action' => 'objectives'], 'style' =>'', 'class' => 'form', 'id' => 'frm_objective_project' )); ?>

							<div class="form-group obj-filter obj-filter-first">

								<div class=" ">
									<label class="" style="min-width: 80px">Projects: </label>
									<label class="custom-dropdown" style=" ">
										<select class="aqua" name="data[Project][id]">
											<option value="">Select a Project</option>
											<?php if( isset($projects) && !empty($projects) ) { ?>
												<?php foreach($projects as $key => $value ) { ?>
													<option value="<?php echo $key; ?>"><?php echo ( strlen($value) > 25 ) ? substr(html_entity_decode($value), 0, 25).'...' : html_entity_decode($value); ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</label>
									<span class="error-message text-danger" id="iProject"></span>
								</div>
							</div>
							<div class="form-group  obj-filter obj-filter-sec">
								<div class=" ">
									<label class="" style="min-width: 80px">Aligned To: </label>
									<label class="custom-dropdown" style=" ">
										<select class="aqua" name="data[Aligned][id]">
											<option value="">Select Alignment</option>
											<?php if( isset($aligned) && !empty($aligned) ) { ?>
												<?php foreach($aligned as $key => $value ) { ?>
													<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</label>
									<span class="error-message text-danger" id="iAligned"></span>
								</div>
							</div>
							<div class="s-buttons form-group  obj-filter obj-filter-third">
								<a href="#" id="submit_filter" class="btn btn-success" style=" ">Apply Filter</a>
								<a href="#" id="reset_filter" class="btn btn-danger" style=" ">Reset</a>

								<a href="#" id="summary_dashboard" class="btn btn-white btn-xs tipText" title="Summary Dashboards" style="font-size: 18px; <?php if(isset($project_id) && !empty($project_id)) { ?>display:inline-block;<?php }else{ ?>display:none;<?php } ?>">
									<i class="fa fa-dashboard "></i>
									<input type="checkbox" name="summary_view" value="1" />
								</a>

							</div>
							<!-- <div class="form-group col-sm-12 col-md-2 col-lg-2"> </div> -->
							<?php  echo $this->Form->end(); ?>
						</div>
					</div>

                    <div class="box border-top margin-top" style="border-radius: 0px 0px 3px 3px;">

                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <div class="box-body clearfix list-shares">

							<!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-12">
								<canvas class="" id="canvas2"  width="100" height="100" ></canvas>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-12">
								<div class="" id="canvas-legend2" ></div>
							</div> -->

							<!-- <div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div> -->

							<!-- <div id="donut-chart" style="height: 300px;"></div>

							<div class="sparkline" data-type="pie" data-offset="90" data-width="100px" data-height="100px">
								6,4,8
							</div>
							<div class="outer-circle">
								<div class="inner-content">

								</div>
							</div> -->
						<div class="objective_details" > </div> <!-- END objective_details -->
					</div>
            </div>
        </div>
			</div>
		</div>
	</div>
</div>