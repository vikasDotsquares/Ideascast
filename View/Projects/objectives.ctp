<style>
	.error-message {
		display:block;
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

	.no-data {
	    color: #bbbbbb;
	    font-size: 30px;
	    left: 4px;
	    position: absolute;
	    text-align: center;
	    text-transform: uppercase;
	    top: 35%;
	    width: 98%;
	}
	.custom-dropdown select.aqua[disabled] {
	    border-color: #ccc;
	}

	.el-icons ul.list-unstyled>li span, .prjct-rprt-icons ul.list-unstyled>li span{ cursor: default;}



</style>

<?php

	echo $this->Html->css('projects/dropdown');
	echo $this->Html->css('projects/objectives');

	echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
	echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));

	echo $this->Html->script('projects/plugins/scroll_to/jquery.scrollTo', array('inline' => true));

	echo $this->Html->css('projects/circle_progress');

	echo $this->Html->css('projects/range-slider');
	echo $this->Html->script('plugins/morris/morris', array('inline' => true));
	echo $this->Html->script('plugins/morris/raphael.min', array('inline' => true));

	echo $this->Html->css('projects/task_results', array('inline' => true));

	echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
	echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));

	echo $this->Html->css('projects/circle_progress');

?>
<style type="text/css">
	#project_cards {
		min-height: 358px;
	}
</style>
<script type="text/javascript" >
	$(function(){
		$('#model_bx').on('hidden.bs.modal', function(e){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		})



		$('body').on("click", "#update_program", function(event){

			var ariaexpanded = $(this).attr('aria-expanded');

			if( !$(this).parent().hasClass('active') ){

			$("#submit_program").hide();

				$.when(
					$.ajax({
						url: $js_config.base_url + 'projects/update_program',
						type: "POST",
						data: {program_name:''},
						global: true,
						success: function (response) {
							if(response) {
								$("#program_update").html(response);
							}
						}
					})
				).then(function( data, textStatus, jqXHR ) {

				})

			}

		});


		$.dec_donut = {};
		$.fed_donut = {};
		$.vot_donut = {};

		/* ******************************** BOOTSTRAP HACK *************************************
		 * Overwrite Bootstrap Popover's hover event that hides popover when mouse move outside of the target
		 * */
		var originalLeave = $.fn.popover.Constructor.prototype.leave;
		$.fn.popover.Constructor.prototype.leave = function(obj){

			var self = obj instanceof this.constructor ?
								obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
			var container, timeout;

			originalLeave.call(this, obj);
			if(obj.currentTarget) {
				container = $(obj.currentTarget).data('bs.popover').tip();
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
		$('body').delegate('#submit_filter', 'click', function(event, project_param) {
			event.preventDefault();
			$.load_partials = true;
			$(".objective_details").html('');
			$(".showalltext").text('Show All');
			var $that = $(this),
				$form = $("#frm_objective_project"),
				$iProject = $("[name='data[Project][id]']"),
				$iAligned = $("[name='data[Aligned][id]']"),
				$iRAG = $("[name='data[Project][rag_status]']"),
				$iCategoryid = $("[name='data[Project][program_id]']"),
				project_id = ($iProject.val() != '') ? $iProject.val() : 0,
				aligned_id = ($iAligned.val() != '') ? $iAligned.val() : 0,
				rag_status = ($iRAG.val() != '') ? $iRAG.val() : 0,
				program_id = ($iCategoryid.val() != '') ? $iCategoryid.val() : 0,
				budgets = '',
				data = $form.serializeArray(),
				error1 = false,
				error2 = false;

			$(".error-message").text('')

			if( $iProject.val() == '' ) {
				error1 = true;
			}
			if( $iAligned.val() == '' ) {
				error2 = true;
			}

			if(typeof project_param == 'object') {
				if( project_param.p_id != '' && project_param.p_id > 0 ) {
					project_id = project_param.p_id;
					$('select[name="data[Project][id]"] option').filter(function() {
						return $(this).val() == project_param.p_id;
					}).prop('selected', true);
				}
				if( project_param.rag_status != '' && project_param.rag_status > 0 ) {
					rag_status = project_param.rag_status;
				}
				if( project_param.budgets != '' && project_param.budgets > 0 ) {
					budgets = project_param.budgets;
				}
			}


			var url_params = {project_id: project_id, aligned_id: aligned_id, rag_status: rag_status, program_id: program_id, budgets: budgets };
			//url_params = {project_id: project_id, aligned_id: aligned_id, rag_status: rag_status, budgets: budgets};

				$.when($.ajax({
					url: $js_config.base_url + 'projects/project_cards',
					type: "POST",
					data: $.param(url_params),
					dataType: "JSON",
					global: false,
					success: function (response) {
						$("#project_cards").html(response);
						// console.log('url_params', $('.project-block'))
						/*
							show only the selected cards when comming from JAI popup
						*/
						if(budgets !== undefined && budgets > 0) {
							$(".no-project").hide();
							$('.project-block').each(function (a) {
									$(this).hide();

									if(  ($('.detail-text span',$(this)).text() == 'Over Budget' || $('.detail-text span',$(this)).text() == 'On Budget, at Risk')){
										$(this).show();
									}
								})
							if( $('.project-block:visible').length == 0 ) {
								console.log('cost in')
								$(".no-project").show();
							}
							$("#cost_type_search_count").html($('.project-block:visible').length);
						}
						/*
							End JAI popup Functionality
						*/

					}
				}))
				.then(function(){
					if( project_id != '' && project_id != 0 && project_id != undefined ) {
						// setTimeout(function(){
							if($('.project-block[data-id="'+project_id+'"]').length > 0) {
								$('.project-block[data-id="'+project_id+'"] .clickable').trigger('click');
							}
						// }, 500)
					}
				})
			return;
		})


		$("[name='data[Project][program_id]']").on('change', function(e){
			$("#project_header_image").html("");
			var $program_id = $(this).val();
			//console.log($category_id);
			if( $program_id != '' && $program_id != 0 && $program_id != undefined ) {
				$.ajax({
					url: $js_config.base_url + 'projects/getProjectList/'+$program_id,
					type: "POST",
					data: $.param({program_id:$program_id}),
					dataType: "JSON",
					global: false,
					success: function (response) {
						$("#listCategories").html(response)
					}
				})
				$('[name="data[Aligned][id]"]').prop('disabled', false)
			} else {

				$.ajax({
					url: $js_config.base_url + 'projects/getProjectList/',
					type: "POST",
					data: $.param({program_id:''}),
					dataType: "JSON",
					global: false,
					success: function (response) {
						$("#listCategories").html(response)
					}
				})

				$('[name="data[Project][id]"] :nth-child(1)').prop('selected', true);
				$('[name="data[Aligned][id]"] :nth-child(1)').prop('selected', true);
				$('[name="data[Aligned][id]"]').prop('disabled', true)
			}

		})

		$("body").on('change', "[name='data[Project][id]']", function(e){
			$("#project_header_image").html("");
			var sel_prg = $("[name='data[Project][program_id]']").val();
			if(sel_prg != '' && sel_prg !== undefined) {
				return;
			}
			var val = $(this).val()
			if(val == 'none'){
				$('[name="data[Aligned][id]"]').val('')
				$('[name="data[Aligned][id]"]').prop('disabled', true)
				$('#project_cards').html('<div class="panel-body scroll-vertical toggle-scrolling">\
					<div class="inner-horizontal">\
						<div class="no-data no-project">SELECT A PROGRAM OR PROJECT</div>\
					</div>\
				</div>');

			}
			else if(val == '') {
				$('[name="data[Aligned][id]"]').prop('disabled', false)
				$('#project_cards').html('<div class="panel-body scroll-vertical toggle-scrolling">\
					<div class="inner-horizontal">\
						<div style="" class="no-project loading-rays"></div>\
					</div>\
				</div>');

				$('#submit_filter').trigger('click');

			}
			else if($.isNumeric(val)) {
				$('[name="data[Aligned][id]"]').prop('disabled', false)
				$('#project_cards').html('<div class="panel-body scroll-vertical toggle-scrolling">\
					<div class="inner-horizontal">\
						<div style="" class="no-project loading-rays"></div>\
					</div>\
				</div>');
				$('#submit_filter').trigger('click');
				// console.log('numeric')
			}

		})

		$('body').delegate("[name='data[Project][rag_status]']", 'change', function(event) {
			$("#project_header_image").html("");
			$('#submit_filter').trigger('click');
			$("[name='data[Project][rag_status]']", this).prop('selected', true);

		})


		<?php if( !isset($project_id) || empty($project_id) ) { ?>
			$("[name='data[Project][id]'] option:first").prop('selected', true);
			$("[name='data[Aligned][id]'] option:first").prop('selected', true);
			setTimeout(function(){
				//$('#submit_filter').trigger('click');
			}, 200)
		<?php } ?>
		$("[name='data[Project][rag_status]'] option:first").prop('selected', true);


		// JAI Functionality
		if( $js_config.objectives['project_id'] )
		{
			$("[name='data[Project][rag_status]']").val($js_config.objectives['ragstatus']);

			setTimeout(function(){
				$('[name="data[Aligned][id]"]').prop('disabled', false)
				$('#submit_filter').trigger('click', {'p_id': $js_config.objectives['project_id'], 'rag_status': $js_config.objectives['ragstatus'], 'budgets': $js_config.objectives['budgets']});
				// $('#submit_filter').trigger('click')
			}, 200)
		}
		else{
			//$('#submit_filter').trigger('click');
		}

		$('#reset_filter').on('click', function(event) {
			event.preventDefault();
			window.history.pushState("object or string", "Manage Costs", $js_config.base_url + 'projects/objectives');
			location.reload()
		})

		$("body").delegate('.sidebar-toggle', 'click', function(event) {

			if($(window).width() > 768 && $(window).width() <= 1024) {
				if($('body').hasClass('sidebar-collapse')) {
					$('.field-area-select input.form-control').css('max-width', '180px');
				}
				else {
					$('.field-area-select input.form-control').css('max-width', '115px');
				}
			}

			$('.objective_projects').find('.panel-body').each(function(){
				if($(this).is(':visible')) {
					var $dfv_wrapper = $(this).find('.dfv_wrapper');
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

			if($('.project-block.panel-success').length > 0) {
				var project_id = $('.project-block.panel-success').data('id')
				setTimeout(function(){
					$.scroll_projects(project_id);
				}, 500);

			}

		})

		$("body").delegate('.show_hide_panel', 'click', function(event) {

			event.preventDefault();

			var $panel = $(this).parents('.panel:first'),
				$other_panels = $('.objective_projects').find('.panel[class*=panel-]').not($panel),
				$panel_body = $panel.find('.panel-body'),
				$icon = $(this).find('i.fa'),
				pid = $(this).data('id'),
				ddonut_data = $(this).find('.hide.dec').html(),
				fdonut_data = $(this).find('.hide.fed').html(),
				vdonut_data = $(this).find('.hide.vot').html(),
				run_first = true;

			$icon.toggleClass('fa-minus fa-plus');

			$other_panels.find('.panel-body').slideUp(500);

			$panel_body.slideToggle('slow');

			$other_panels.find('.show_hide_icon').removeClass('fa-minus').addClass('fa-plus');

			$other_panels.find('.show_dfv').trigger('click', [1]);

			$(".show_full_toggle").hide();

			setTimeout(function() {
				var $panels = $('.objective_projects').find('.panel');
				if( $panel_body.css('display') == 'block' ) {
					$(".show_full_toggle", $panel).show();
				}
				else {
					$(".show_full_toggle", $panel).hide();
					$('.show_full').hide();
					$('.show_full_toggle').tooltip('hide')
										.data('original-title', 'Show Full')
										.attr('data-original-title', 'Show Full');
				}


				var $clickable_grid = $('.clickable_grid');
				if( $clickable_grid.hasClass('clicked_grid') ) {
					$clickable_grid.removeClass('clicked_grid');

					// set scrolling
					var all = 0;
					$(".inner-horizontal div.project-block").each(function() {
						var w = $(this).outerWidth(true);
						all = all + w;
					})
					$(".inner-horizontal").css('min-width', all);

					// set scrolling to panel-body
					$('.toggle-scrolling').addClass('scroll-horizontal');
					$('.toggle-scrolling').removeClass('scroll-vertical');

					setTimeout(function(){
						$.scroll_projects(pid);
					}, 500);

				}
				else {
					$.scroll_projects(pid);
				}


				$('.clickable').removeClass('clicked');
				$('.project-block').removeClass('panel-success').addClass('panel-default');

				$('.clickable[data-project='+pid+']').addClass('clicked');
				$('.clickable[data-project='+pid+']').parents('.project-block:first').removeClass('panel-default').addClass('panel-success');

			}, 1000);

		})

		$.load_partials = true;

		$("body").delegate(".show_full_toggle", 'click', function (event) {
			event.preventDefault();

			var $that = $(this),
			$panel = $that.parents('.panel:first'),
			$show_full = $panel.find(".show_full");


			var project_id = $(this).parents('.panels-workspace:first').data('project');

			if($.load_partials){
				$.load_partials = false;
				$.get_participants(project_id).done(function(msg){
					$.get_wsp_detail(project_id).done(function(msg){
						$show_full.slideDown('slow');
						$that
						.tooltip('hide')
						.data('original-title', 'Show Less')
						.attr('data-original-title', 'Show Less');
						setTimeout(function(){
							$that.tooltip('show');
						}, 200)
						$.get_task_detail(project_id).done(function(msg){
						})
					})



				});
			}
			else{
				if( $show_full.is(':visible') ) {
					$show_full.slideUp('slow');
					$that
					.tooltip('hide')
					.data('original-title', 'Show Full')
					.attr('data-original-title', 'Show Full');
					setTimeout(function(){
						$that.tooltip('show');
					}, 200)
				}
				else {
					$show_full.slideDown('slow');
					$that
					.tooltip('hide')
					.data('original-title', 'Show Less')
					.attr('data-original-title', 'Show Less');
					setTimeout(function(){
						$that.tooltip('show');
					}, 200)
				}
			}

		})

		$.get_participants = function(project_id){
			var dfd = new $.Deferred();
			$.ajax({
				url: $js_config.base_url + 'projects/objective_creator_sharers',
				type: 'POST',
				// dataType: 'json',
				data: { project_id: project_id},
				success: function(res){
					$('.project-owner-sharer').html(res);
					dfd.resolve('done');
				}
			})
			return dfd.promise();
		}

		$.get_wsp_detail = function(project_id){
			var dfd = new $.Deferred();
			$.ajax({
				url: $js_config.base_url + 'projects/objective_workspaces',
				type: 'POST',
				// dataType: 'json',
				data: { project_id: project_id},
				success: function(res){
					$('.obj-wsps').html(res);
					dfd.resolve('done');
				}
			})
			return dfd.promise();
		}

		$.get_task_detail = function(project_id){
			var dfd = new $.Deferred();
			$.ajax({
				url: $js_config.base_url + 'projects/objective_task_detail',
				type: 'POST',
				// dataType: 'json',
				data: { project_id: project_id},
				success: function(res){
					$('.task_detail').html(res);
					dfd.resolve('done');
				}
			})
			return dfd.promise();
		}

		$("body").delegate(".show_graphs", 'click', function (event) {
			event.preventDefault();

			var $this = $(this),
			data = $this.data(),
			$heading = $this.parents('.idcomp-heading:first'),
			$desc = $heading.next('.idcomp-descp:first'),
			$graphs = $desc.find('.idcomp-graphs:first'),
			$lists = $desc.find('.idcomp-lists:first'),
			project_id = $heading.find('input[name='+data.type+']').val();
			// console(project_id)
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
			$dfv_more = $this.parent('.dfv_more:first'),
			$dfv_wrapper = $this.parent().next('.dfv_wrapper:first');
			$dfv_more.find('.t').remove()

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
				var txt = 'Show less <span style="text-decoration; none; font-weight: normal;">(Decisions/Feedback/Votes)</span>';
				if( $dfv_wrapper.is(':visible') ) {
					txt = 'Show more <span style="text-decoration; none;  font-weight: normal;">(Decisions/Feedback/Votes)</span>';
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
				$this.text('Show more').attr('data-original-title', 'Show more')

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

				//Show Data on Donut Chart
				$.ajax({
					url: $js_config.base_url + 'projects/objective_decision_chart',
					data: $.param(params),
					type: 'post',
					dataType: 'json',
					success: function (response) {
						$main.find('.chart_overlay').hide()
						if( response.success ) {

							if( response.content.chart_filled ) {
								if($.dec_donut[project_id]){
									$.dec_donut[project_id].setData(response.content.chart_data);
									$.dec_donut[project_id].redraw();
								}

								$main.find('.dc_detail_'+project_id).find('.donut-detail.first a').text(response.content.chart_data[0].value + ' Live')

								$main.find('.dc_detail_'+project_id).find('.donut-detail.first a').attr('data-count',response.content.chart_data[0].value )

								$main.find('.dc_detail_'+project_id).find('.donut-detail.second a').text(response.content.chart_data[1].value + ' Completed')

								$main.find('.dc_detail_'+project_id).find('.donut-detail.second a').attr('data-count',response.content.chart_data[1].value )

							}
							else {
								$main.find('.chart_overlay').show()

								$main.find('.dc_detail_'+project_id).find('.donut-detail.first a').text('0 Live')

								$main.find('.dc_detail_'+project_id).find('.donut-detail.first a').attr('data-count','0' )

								$main.find('.dc_detail_'+project_id).find('.donut-detail.second a').text('0 Completed')

								$main.find('.dc_detail_'+project_id).find('.donut-detail.second a').attr('data-count','0' )

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
							//if( response.content != null ) {
							if( response.content.chart_filled != false ) {

								if($.fed_donut[project_id]){
									$.fed_donut[project_id].setData(response.content.chart_data);
									$.fed_donut[project_id].redraw();
								}

								$main.find('.fed_detail_'+project_id).find('.donut-detail.first a').text(response.content.chart_data[0].value + ' Live')

								$main.find('.fed_detail_'+project_id).find('.donut-detail.first a').attr('data-count',response.content.chart_data[0].value)

								$main.find('.fed_detail_'+project_id).find('.donut-detail.second a').text(response.content.chart_data[1].value + ' Completed')

								$main.find('.fed_detail_'+project_id).find('.donut-detail.second a').attr('data-count',response.content.chart_data[1].value )
							}
							else {
								$main.find('.chart_overlay').show()

								$main.find('.fed_detail_'+project_id).find('.donut-detail.first a').text('0 Live')

								$main.find('.fed_detail_'+project_id).find('.donut-detail.first a').attr('data-count','0' )


								$main.find('.fed_detail_'+project_id).find('.donut-detail.second a').text('0 Completed')

								$main.find('.fed_detail_'+project_id).find('.donut-detail.second a').attr('data-count','0' )
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
							//if( response.content != null ) {
							if( response.content.chart_filled != false ) {

								if($.vot_donut[project_id]){
									$.vot_donut[project_id].setData(response.content.chart_data);
									$.vot_donut[project_id].redraw();
								}

								$main.find('.vot_detail_'+project_id).find('.donut-detail.first a').text(response.content.chart_data[0].value + ' Live')

								$main.find('.vot_detail_'+project_id).find('.donut-detail.first a').attr('data-count',response.content.chart_data[0].value  )

								$main.find('.vot_detail_'+project_id).find('.donut-detail.second a').text(response.content.chart_data[1].value + ' Completed')

								$main.find('.vot_detail_'+project_id).find('.donut-detail.second a').attr('data-count',response.content.chart_data[1].value )
							}
							else {
								$main.find('.chart_overlay').show()

								$main.find('.vot_detail_'+project_id).find('.donut-detail.first a').text('0 Live')

								$main.find('.vot_detail_'+project_id).find('.donut-detail.first a').attr('data-count','0' )

								$main.find('.vot_detail_'+project_id).find('.donut-detail.second a').text('0 Completed')

								$main.find('.vot_detail_'+project_id).find('.donut-detail.second a').attr('data-count','0' )

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
			var dcount = $this.attr('data-count');

			if(dcount > 0){

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

			}

		})

		$.scroll_projects = function(project_id) {

			var $pblock = $('.project-block[data-id='+project_id+']');

			$(".toggle-scrolling").scrollTo($pblock, {
				easing: 'swing',
				duration: 1000
			});
		}


		$.submit_filter = function(project_id, aligned_id, rag_status,program_id) {

			var project_id = project_id || 0,
				program_id = program_id || 0,
				aligned_id = aligned_id || 0;


			var url_params = {project_id: project_id, aligned_id: aligned_id, rag_status: rag_status,program_id: program_id };


			// $(".ajax_overlay_preloader").show()
				$.ajax({
					url: $js_config.base_url + 'projects/filtered_data',
					type: "POST",
					data: $.param(url_params),
					// dataType: "JSON",
					global: false,
					success: function (response) {
						$(document).find( ".objective_details" ).empty().append(response);
					}
				})
			return;
		}

		$('body').delegate('.clickable', 'click', function(event) {
			event.preventDefault();
			$.load_partials = true;
			$(".objective_details").html('');
			$(".showalltext").text('Show All');

			var project_id = 0;
			var aligned_id = 0;
			var rag_status = 0;
			var program_id = 0;


			var $iAligned = $("[name='data[Aligned][id]']"),
				$iRAG = $("[name='data[Project][rag_status]']"),
				$iCategoryid = $("[name='data[Project][program_id]']"),
				aligned_id = ($iAligned.val() != '') ? $iAligned.val() : 0,
				rag_status = ($iRAG.val() != '') ? $iRAG.val() : 0,
				program_id = ($iCategoryid.val() != '') ? $iCategoryid.val() : 0;

			<?php if( isset($aligned_id) && !empty($aligned_id) ) { ?>
					aligned_id = '<?php echo $aligned_id; ?>';
			<?php } ?>
			<?php if( isset($rag_status) && !empty($rag_status) ) { ?>
					rag_status = '<?php echo $rag_status; ?>';
			<?php } ?>
			<?php if( isset($program_id) && !empty($program_id) ) { ?>
					program_id = '<?php echo $program_id; ?>';
			<?php } ?>

			var project_id = $(this).data('project');
			setTimeout(function(){
				$.submit_filter(project_id, aligned_id, rag_status, program_id);
				var o = $("#project_cards").offset();

					// auto scrolling
					var $clickable_grid = $('.clickable_grid');
					if( $clickable_grid.hasClass('clicked_grid') ) {
						$clickable_grid.removeClass('clicked_grid');

						// set scrolling
						var all = 0;
						$(".inner-horizontal div.project-block").each(function() {
							var w = $(this).outerWidth(true);
							all = all + w;
						})
						$(".inner-horizontal").css('min-width', all);


						// set scrolling to panel-body
						$('.toggle-scrolling').addClass('scroll-horizontal');
						$('.toggle-scrolling').removeClass('scroll-vertical');

						setTimeout(function(){
							$.scroll_projects(project_id);
						}, 1000);

					}
					else {
						setTimeout(function(){
							$.scroll_projects(project_id);
						}, 500);
					}
			}, 1200)

			/*return;
			var project_id = $(this).data('project');
			$.ajax({
				url: $js_config.base_url + 'projects/objectives_filters',
				type: "POST",
				data: $.param({project_id: project_id}),
				dataType: "JSON",
				global: false,
				success: function (response) {
					$(".objective_details").html(response);

					setTimeout(function(){
						if($(window).width() > 768 && $(window).width() <= 1024) {
							if($('body').hasClass('sidebar-collapse')) {
								$('.field-area-select input.form-control').css('max-width', '180px');
							}
							else {
								$('.field-area-select input.form-control').css('max-width', '115px');
							}
						}
					}, 1000);

					var o = $("#project_cards").offset();

					// auto scrolling
					var $clickable_grid = $('.clickable_grid');
					if( $clickable_grid.hasClass('clicked_grid') ) {
						$clickable_grid.removeClass('clicked_grid');

						// set scrolling
						var all = 0;
						$(".inner-horizontal div.project-block").each(function() {
							var w = $(this).outerWidth(true);
							all = all + w;
						})
						$(".inner-horizontal").css('min-width', all);


						// set scrolling to panel-body
						$('.toggle-scrolling').addClass('scroll-horizontal');
						$('.toggle-scrolling').removeClass('scroll-vertical');

						setTimeout(function(){
							$.scroll_projects(project_id);
						}, 1000);

					}
					else {
						setTimeout(function(){
							$.scroll_projects(project_id);
						}, 500);
					}
				}
			})*/

			$('.clickable').removeClass('clicked');
			$('.project-block').removeClass('panel-success').addClass('panel-default');

			if( $(this).hasClass('clicked') ) {
				$(this).removeClass('clicked');
				$(this).parents('.project-block:first').removeClass('panel-success').addClass('panel-default');
			}
			else {
				$(this).addClass('clicked');
				$(this).parents('.project-block:first').removeClass('panel-default').addClass('panel-success');
			}

		})

		$('body').delegate('.showalltext', 'click', function(event) {
			event.preventDefault();
			var txt = $(this).text(),
				project_id = $("[name='data[Project][id]']").val(),
				rag_status = $("[name='data[Project][rag_status]']").val(),
				program_id = $("[name='data[Project][program_id]']").val();

			<?php if( isset($rag_status) && !empty($rag_status) ) { ?>
				rag_status = '<?php echo $rag_status; ?>';
			<?php } ?>
			<?php if( isset($project_id) && !empty($project_id) ) { ?>
				project_id = '<?php echo $project_id; ?>';
			<?php } ?>
			<?php if( isset($program_id) && !empty($program_id) ) { ?>
				program_id = '<?php echo $program_id; ?>';
			<?php } ?>


			$('.clickable').removeClass('clicked');
			$('.project-block').removeClass('panel-success').addClass('panel-default');

			if(txt == 'Show All') {
				$.ajax({
					url: $js_config.base_url + 'projects/filtered_data',
					type: "POST",
					data: $.param({ project_id: project_id, rag_status: rag_status, program_id: program_id  }),
					// dataType: "JSON",
					global: false,
					success: function (response) {
						$(".objective_details").html(response);
					}
				})
				$(this).text("Show Less");
			}
			else {
				$(this).text("Show All");
				$(".objective_details").html('');
			}


			var $clickable_grid = $('.clickable_grid');
			if( $clickable_grid.hasClass('clicked_grid') ) {
				$clickable_grid.removeClass('clicked_grid');

				// set scrolling
				var all = 0;
				$(".inner-horizontal div.project-block").each(function() {
					var w = $(this).outerWidth(true);
					all = all + w;
				})
				$(".inner-horizontal").css('min-width', all);

				// set scrolling to panel-body
				$('.toggle-scrolling').addClass('scroll-horizontal');
				$('.toggle-scrolling').removeClass('scroll-vertical');

			}

		})

		$('body').delegate('.clickable_grid', 'click', function(event) {
			event.preventDefault();

			if( $(this).hasClass('clicked_grid') ) {
				$(this).removeClass('clicked_grid');

				// set scrolling
				var all = 0;
				$(".inner-horizontal div.project-block").each(function() {
					var w = $(this).outerWidth(true);
					all = all + w;
				})
				$(".inner-horizontal").css('min-width', all);


				// set scrolling to panel-body
				$('.toggle-scrolling').addClass('scroll-horizontal');
				$('.toggle-scrolling').removeClass('scroll-vertical');

			}
			else {
				$(this).addClass('clicked_grid');

				// remove scrolling
				$(".inner-horizontal").removeAttr('style');

				// set scrolling to panel-body
				$('.toggle-scrolling').removeClass('scroll-horizontal')
				$('.toggle-scrolling').addClass('scroll-vertical')

			}

		})

		$(window).on('resize', function(){

			// auto scrolling
			var $clickable_grid = $('.clickable_grid');
			if( !$clickable_grid.hasClass('clicked_grid') ) {
				$clickable_grid.removeClass('clicked_grid');

				// set scrolling
				var all = 0;
				$(".inner-horizontal div.project-block").each(function() {
					var w = $(this).outerWidth(true);
					all = all + w;
				})
				$(".inner-horizontal").css('min-width', all);


				// set scrolling to panel-body
				$('.toggle-scrolling').addClass('scroll-horizontal');
				$('.toggle-scrolling').removeClass('scroll-vertical');

				if($('.project-block.panel-success').length > 0) {
					var project_id = $('.project-block.panel-success').data('id')
					setTimeout(function(){
						$.scroll_projects(project_id);
					}, 500);

				}
			}
		})

		$.opened_model = $();

		$('#model_bx').on('show.bs.modal', function(event) {
			$.opened_model = $(event.relatedTarget);
		});
		//========== 5th Jan =============
		$("#model_bx").on("hidden.bs.modal", function(){
			$(".modal-body").html("");
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
							$form.find('#clear_annotate').hide();
							$that.removeClass('disabled');
						}
						else {
							if( ! $.isEmptyObject( response.content ) ) {

								$.each( response.content, function( ele, msg) {

									var $element = $form.find('[name="data[ProjectComment]['+ele+']"]')
									var $parent = $element.parent();

									if( $parent.find('span.error-message.text-danger').length  ) {
										$parent.find('span.error-message.text-danger').text(msg)

									}
								})
								$form.find('#ProjectCommentComments').val('');
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




		//======= Search by Cost Type =========================================
		$('body').delegate("#cost_type_search","change", function (e) {
			var searchTerms = $(this).val();
			console.log('cost', searchTerms)

			if($(".no-project:first").length <= 0){
				$('.inner-horizontal').append('<div style="" class="no-project">No Projects</div>');
			}
			$(".no-project").hide();

			if(searchTerms == ''){
				$('.project-block').show();
			}else{
				$('.project-block').each(function (a) {
					 $(this).hide();

					if(searchTerms.length > 0 && $('.detail-text span.filterD',$(this)).text() == searchTerms){
						$(this).show();
					}
				})
			}
			if( $('.project-block:visible').length == 0 ) {

				$(".no-project:first").show();
			}
			$("#cost_type_search_count").html($('.project-block:visible').length);
		})

	})
</script>

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
		<span id="project_header_image">
			<?php
				if( isset( $project_id ) && !empty( $project_id ) ) {
					//echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_id));
				}
			?>
		</span>

		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">

					<div class="panel panel-default bg-lgray" style="margin-bottom: -9px; border-radius: 3px 3px 0px 0px; border-bottom: 2px solid #dddddd; ">
						<div class="panel-body" style="padding: 15px 0 0;">
							<?php
							echo $this->Form->create('ProjectObjective', array('url' => ['controller' => 'projects', 'action' => 'objectives'], 'style' =>'', 'class' => 'form', 'id' => 'frm_objective_project' )); ?>


							<div class="form-group obj-filter-dropdown obj-filter-dropdown-three">
								<div class=" ">
									<label class="custom-dropdown " id="aligned_id_label" style=" ">
											<?php
											$programlist = $this->Common->program_lists();

											// pr($programlist,1);

											echo $this->Form->input('Project.program_id', array(
												'options' => $programlist,
												'empty' => 'Select Program',
												'type' => 'select',
												'default' => (isset($this->data['Project']['program_id'])) ? $this->data['Project']['program_id'] : $programlist, 'required' => false,
												'label' => false,
												'div' => false,
												'class' => 'aqua'
											));
												?>

									</label>
									<span class="error-message text-danger" id="iAligned"></span>
								</div>
							</div>


							<div class="form-group obj-filter-dropdown obj-filter-dropdown-one">

								<div class=" ">
									<label class="custom-dropdown" style=" " id="listCategories" >
										<select class="aqua" name="data[Project][id]">
											<option value="none">Select Project</option>
											<option value="">All Projects</option>
											<?php if( isset($projects) && !empty($projects) ) { ?>
												<?php foreach($projects as $key => $value ) { ?>
													<option value="<?php echo $key; ?>">
														<?php echo ( strlen($value) > 25 ) ? substr(html_entity_decode($value), 0, 25).'...' : html_entity_decode($value); ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</label>
									<span class="error-message text-danger" id="iProject"></span>
								</div>
							</div>

							<div class="form-group obj-filter-dropdown obj-filter-dropdown-two">
								<div class=" ">
									<label class="custom-dropdown" id="aligned_id_label" style=" ">
										<select class="aqua" disabled="" name="data[Aligned][id]">
											<option value="">Select Project Type</option>
											<?php if( isset($aligned) && !empty($aligned) ) { ?>
												<?php foreach($aligned as $key => $value ) { ?>
													<option value="<?php echo $key; ?>"><?php echo htmlentities($value); ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</label>
									<span class="error-message text-danger" id="iAligned"></span>
								</div>
							</div>




							<div class="s-buttons form-group status-center-buttons">
								<a href="#" id="submit_filter" class="btn btn-success btn-sm" style=" ">Apply Filter</a>
								<a href="#" id="reset_filter" class="btn btn-danger btn-sm" style=" ">Reset</a>
							</div>
							<?php  echo $this->Form->end(); ?>
						</div>
					</div>

                    <div class="box noborder margin-top" style="border-radius: 0px 0px 3px 3px;">

                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="model_bx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

                            <div class="modal modal-success fade" id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <div class="box-body clearfix list-shares" style="min-height: 650px;">

							<div class="panel panel-default" id="project_cards">
								<?php if(!isset($project_id) || empty($project_id)){ ?>
								<div class="panel-body scroll-vertical toggle-scrolling">
									<div class="inner-horizontal">
										<div class="no-data no-project">SELECT A PROGRAM OR PROJECT</div>
									</div>
								</div>
								<?php }
								?>
							</div>

							<!-- <div class="showalltext " >Show All</div> -->

							<div class="objective_details" > </div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	select#rag_status option[value="1"] {
	    background: rgba(221, 75, 57, 0.7);
	}

	select#rag_status option[value="2"] {
	    background: rgba(243, 156, 18, 0.7);
	}

	select#rag_status option[value="3"] {
	    background: rgba(103, 160, 40, 0.7);
	}

	select#cost_type_search option[value="Over Budget"], select#cost_type_search option[value="On Budget, at Risk"] { /* value not val */
	    background: rgba(221, 75, 57,0.7);
	}

</style>

<script type="text/javascript" >
$(function() {

	$.programlist = function(){
		 $.when(
			$.ajax({
				url: $js_config.base_url + 'projects/program_list_update',
				type: "POST",
				data: {program_name:''},
				dataType: "JSON",
				global: true,
				success: function (response) {
					if(response) {
						$('#ProjectProgramId').html(response);

						/* $('#ProjectProgramId').css("box-shadow","0px 0px 15px 2px #f00");setTimeout(function(){
							$('#ProjectProgramId').removeAttr("style");
						},1500) */
					} else {
						if( !$.isEmptyObject( response.content ) ) {

						}
					}
				}
			})
		).then(function( data, textStatus, jqXHR ) {

		})
	};

	$("#update_submit_program").hide();
	$("#submit_program").show();

	$("#create_program").click(function(){
		$("#update_submit_program").removeClass('show').hide();
		$("#submit_program").show();

	});

	// console.clear();
	$('#model_bx').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
		//console.log("close box");
		if($.save_clicked == true ){
			$.programlist();
		}
	});

	//============ Update Program ============================
	$("body").on("click", "#update_submit_program", function(e){
		e.preventDefault();

			 $('.program-manager ul li').removeAttr('style');
			var program_id = $("input[name='data[Program][program_name_update]']").data('program_id');

			var program_name = $("input[name='data[Program][program_name_update]']").val();
			var selected_value = $('#multiple_program_project_update option:selected').map(function() {
				return this.value;
			}).get();
			$(".error-message-upname").text('');
			$(".error-message-pid").text('');
			var $form = $('#modelFormProjectProgram');
			$.when(
				$.ajax({
					url: $js_config.base_url + 'projects/update_save_program',
					type: "POST",
					data: {program_name:program_name, project_id:selected_value, program_id:program_id},
					dataType: "JSON",
					global: true,
					success: function (response) {

						if(response.success == true) {
							$("#update_submit_program").addClass('hide');
							$.save_clicked = true;
							var updatedTitle = $('.igngroup #pgtitle').val();
							$('.program-manager ul li.activeC .programtitle').text(updatedTitle);

								$('.program-manager ul li.activeC .programtitle').show();
								$('.program-manager ul li.activeC .deleteprogram').show();
								$('.program-manager ul li.activeC .editprogram').show();
								$('.program-manager ul li.activeC').css('box-shadow','inset 1px 1px 15px 0px #ccc');

								$('.igngroup').remove();
								$('.update_program_projectlist').empty();


						} else {

							//console.log(response);

							if( !$.isEmptyObject( response ) ) {

								if( (response.program_name!='undefined' ||response.program_name !='' ) && response.program_name.length > 0 ){

									var program_msg = response.program_name;
									$(".error-message-upname").text(program_msg);

								} else {
									$(".error-message-upname").text('');
								}

								if( (response.project_id!='undefined' || response.project_id !='') && response.project_id.length > 0  ){

								var projectid_msg = response.project_id;

								$(".error-message-pid").text(projectid_msg);

								} else {
									$(".error-message-pid").text('');
								}
							}
						}
					}
				})
			).then(function( data, textStatus, jqXHR ) {

			})
	})

	//============ Submit Program ============================
	$('body').delegate('#submit_program', 'click', function(event) {
			event.preventDefault();

			var selected_value = $('input[name="data[ProjectProgram][project_id]"]:checked').map(function() {
				return this.value;
			}).get();
			var program_name = $("input[name='data[Program][program_name]']").val();

			var $form = $('#modelFormProjectProgram');
				//data = {program_name:program_name, project_id:selected_value},
				//data = $form.serialize(),
				//data_array = $form.serializeArray();
				//console.log(data);
			$(".error-message-pname").text('');
			$(".error-message-pid").text('');
			$.when(
				$.ajax({
					url: $js_config.base_url + 'projects/save_program',
					type: "POST",
					data: {program_name:program_name, project_id:selected_value},
					dataType: "JSON",
					global: true,
					success: function (response) {

						if(response.success == true) {
							$.save_clicked = true;
							$form.find('#ProgramProgramName').val('');
							$('#multiple_program_project').multiselect('destroy');
							$('#multiple_program_project').multiselect('rebuild');

							//$("#update_program").trigger( "click" );

							$('#model_bx').modal('hide');


						} else {

							if( !$.isEmptyObject( response.content ) ) {

								if( (response.content.program_name!='undefined' ||response.content.program_name !='' ) ){

									var program_msg = response.content.program_name;
									$(".error-message-pname").text(program_msg);

								} else {
									$(".error-message-pname").text('');
								}

								if( (response.content.project_id!='undefined' || response.content.project_id !='' ) ){

								var projectid_msg = response.content.project_id;

								$(".error-message-pid").text(projectid_msg);

								} else {
									$(".error-message-pid").text('');
								}
							}
						}
					}
				})
			).then(function( data, textStatus, jqXHR ) {

			})
		})

	//============ Edit Program ===================================
	$('body').on("click",".editprogram", function(event){
		event.preventDefault();
		$("#update_submit_program").removeClass('hide').show();

		var programtitle = '';
		var $inputGroup = '';
		$('.program-manager ul li').removeClass('activeC')
		$('.program-manager ul li').removeAttr('style');


		 var latestli = $(this).parent("li");
		 $(this).parent("li").find(".deleteprogram").hide('slow');
		 $(this).hide('slow');




		var program_id = $(this).data('programid');
		programtitle = $(this).parents('li').find(".programtitle").text();

		$(this).parents('ul.list-group:first').find('li').not(latestli).each(function(i,e){
			if($(this).find(".pgtitle").length > 0 ){

				var program_name = $(this).find(".pgtitle").val();
				$that = $(this);
				$(this).find(".igngroup").animate({
					width: "0%",
					height: "0%",
					opacity: 0.1,
					marginLeft: "0",
				}, 1000, function(){

					  $(".deleteprogram,.editprogram",$that).show()
					  $('.programtitle',$that).show();
					  $(this).remove()

				})

			}
		})

		//<span class="input-group-addon bg-green edit_program"><i class="fa fa-check"></i></span>

		$inputGroup = '<span class="input-group igngroup" style="width:20%;" ><input id="pgtitle" data-program_id="'+program_id+'" name="data[Program][program_name_update]" type="text" style="height: 30px;" class="form-control pgtitle" placeholder="Program Title" value="'+programtitle+'"><span class="input-group-addon exitprogram bg-red"><i class="fa fa-close"></i></span></span><span class="error-message-upname text-danger"></span>';

		$(this).parents('li').find(".programtitle").before($inputGroup).fadeOut('fast');

		$(this).parents('li').addClass('activeC');
		//$(this).parents('li').find(".programtitle").before($inputGroup);

		 $(".igngroup",latestli).animate({
			width: "100%",
			opacity: 1,
			marginLeft: "0",
		  }, 1000);

		  $.when(
			$.ajax({
				url: $js_config.base_url + 'projects/update_project_list',
				type: "POST",
				data: {program_id:program_id},
				dataType: "JSON",
				global: true,
				success: function (response) {

					if(response) {
						 $(".update_program_projectlist").html(response);
					} else {

					}
				}
			})
		).then(function( data, textStatus, jqXHR ) {

		})


	});

	$('body').delegate(".exitprogram", "click", function(event){
		$("#update_submit_program").addClass('hide');
		var programtitle = '';
		var $inputGroup = '';
		var $li = $(this).parents("li.justify-content-between:first");

		$li.removeClass('activeC');

		var program_name = $(this).parents('.input-group:first').find(".pgtitle").val();

		$(".update_program_projectlist").empty();

		$li.find(".igngroup").animate({
			width: "0%",
			height: "0%",
			opacity: 0.1,
			marginLeft: "0",
		}, 1000, function(){
			$li.find(".deleteprogram,.editprogram").show()
			$li.find('.programtitle').show();
			$(this).remove()
			$('.igngroup').remove();
		})
	});


	//============ Delete Program ===================================
	$(".del-options").hide();
	$('body').delegate(".deleteprogram", "click", function(event){

		$(this).hide();
		$(this).parents("li").find(".del-options").show('slow');
		$(this).parents("li").find(".editprogram").hide('slow');

	});

	$('body').delegate(".reject ", "click", function(event){

		$(this).parents("li").find(".deleteprogram").show('slow');
		$(this).parent().hide('slow');
		$(this).parents("li").find(".editprogram").show('slow');

	});

	$('body').on("click", ".accept", function(event){
		var $that = $(this);
		var program_id = $(this).data('deleteprogramid');
		console.log(program_id);
		$.when(
			$.ajax({
				url: $js_config.base_url + 'projects/delete_program',
				type: "POST",
				data: {program_id:program_id},
				dataType: "JSON",
				global: true,
				success: function (response) {

					if(response.success == true) {



						$that.parents('li').css("background-color","#ff9696").dequeue();
						$that.parents('li').fadeOut(900, function(){

							$( this ).remove();
							$(this,".tipText").remove();
							  $('.tooltip').hide()
						});

						//console.log($('#program_update .list-group li').length);
						setTimeout(function(){
						if( $('#program_update .list-group li').length ==1 ){
							$("#programNamelist").html('<span class="no-data-found">No Program Found</span>');
						}
						},500)


					} else {

						if( !$.isEmptyObject( response.content ) ) {

						}

					}
				}
			})
		).then(function( data, textStatus, jqXHR ) {

		})



	});

})
</script>


