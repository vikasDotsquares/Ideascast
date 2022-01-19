<?php echo $this->Html->css('projects/dropdown', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/status_board', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/task_lists', array('inline' => true));
 echo $this->Html->script('projects/plugins/marks/jquery.mark.min', array('inline' => true));?>
<div class="row">
    <div class="col-xs-12">

        <div class="row">

            <section class="content-header clearfix">
                <h1 class="box-title pull-left"><?php echo $page_heading; ?>
                   <?php /*  <p class="text-muted date-time">
                        <span><?php echo $page_subheading; ?></span>
                    </p>  */
					?>
                </h1>
				<p class="text-muted date-time pull-left " style="min-width:100%;padding:5px 0;">Project:
     			    <span>Created: <?php echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$projects['Project']['created']),$format = 'd M Y h:i:s'); ?></span>
     			    <span>Updated: <?php echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$projects['Project']['modified']),$format = 'd M Y h:i:s'); ?></span>
			 	</p>
            </section>
        </div>


			<span id="project_header_image" class="">
		<?php
		$style = '';
		if(isset($p_permission['ProjectPermission']['share_by_id']) && !empty($p_permission['ProjectPermission']['share_by_id'])){
			$style = 'top: -31px !important;';
		}
		echo $this->element('../Projects/partials/project_header_image', array( 'p_id' => $project_id, 'style' => $style ));
		?>
	</span>
        <?php echo $this->Session->flash(); ?>
        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
					<div class="fliter margin-top stausbordtopbar">
					<?php  echo $this->element('../Boards/partial/project_settings', array('project_id' => $project_id)); ?>
					</div>
                    <div class="box noborder">
                        <div class="box-header filters" style="">

                            <!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

							 <input type="hidden" id="sel_element" />
							 <input type="hidden" id="elementOrder" />
							<div class="col-sm-12 col-md-12 col-lg-12 nopadding-left nopadding-right">
								<div class="row">
									<div class="col-sm-12 col-md-7 col-lg-7">
										<div class="input-group1 idea-custom-align" style="margin-top:5px;">
											<label style="width: 50%; vertical-align:middle;" class="custom-dropdown">

													<select class="aqua" id="WorkspaceId" name="data[Workspace][id]">
														<option value="">All Workspaces</option>
														<?php
														if( isset($wsps) && !empty($wsps) ) { ?>
															<?php foreach($wsps as $key => $value ) { ?>
																<option value="<?php echo $key; ?>"><?php echo html_entity_decode($value); ?></option>
															<?php } ?>
														<?php } ?>
													</select>

											</label>

											<?php /* <div class="input-group-addon">
												<a class="btn btn-success btn-sm" id="filter_list"> Apply Filter </a>
												<a class="btn btn-danger btn-sm" id="filter_reset"> Reset </a>
											</div> */?>

										</div>
									</div>
									<div class="col-sm-12 col-md-5 col-lg-5">
										<div class="pull-right ttsearch">
											<div class="input-group">
												<input class="form-control search-box-bord" value="" placeholder="Search" type="text">
												<div class="input-group-btn">
													<button class="btn btn-success btn-flat task-search  tipText" title="Search">
														<i class="fa fa-search " ></i>
													</button>
													</div>
											</div>
												<!--<input class="search-box-bord"   value="" placeholder="Search" type="text">
                                                <span class="remove-iocn"><i class="fa fa-search tipText" title="Search"></i></span>-->
                                               <?php /*  <button class="searc-but"><i class="fa fa-search"></i></button> */ ?>
										</div>
									</div>
								</div>
							</div>
							<div class="btn-group pull-right center_options" style="" id="center_options"> </div>
                        </div>
                        <div class="box-body clearfix" >
							<div class="sbwrapper">
								<?php
								//pr($allworkspace);
								echo $this->element('../Boards/task_list', array('workspace_area' => $workspace_area)); ?>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


     				   <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     					<div class="modal-dialog modal-lg">
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
		<?php
			if(isset($project_id) && !empty($project_id)) {
				$cky = CheckProjectType($project_id, $this->Session->read('Auth.User.id'));
			}
		?>
<script type="text/javascript" >



 $(function(){

 	if($.isMobile) {
 		$('.sortOrderStartFirst,.sortOrderEndFirst').removeClass('tipText');
 	}
 	else {
 		$('.sortOrderStartFirst,.sortOrderEndFirst').addClass('tipText');
 	}
 	$(window).on('resize', function(e){
 		if($.isMobile) {
 			$('.sortOrderStartFirst,.sortOrderEndFirst').removeClass('tipText');
 		}
 	})


	$('.users_popovers,.pophover, .assigned').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
	/* ===============start assignment popover========================== */

			var showClickPopover = function () {
				$(this).data('bs.popover').options.content = $(this).data('click-content');
				$(this).data('bs.popover').options.title = "Task Leader";
				$(this).popover("show");
				$('.popover-title').show();
			};

			$('.assigned').popover({
				placement : 'bottom',
				trigger : 'hover',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
			})
			.click(showClickPopover)
			.on("mouseenter", function () {
				var _this = this;
				$(this).data('bs.popover').options.content = $(this).data('hover-content');
				$(this).data('bs.popover').options.title = '';
				$(this).data('original-title', '');
				$(this).attr('data-original-title', '');
				$(this).popover('show');
				setTimeout(function(){
					$(".popover").on("mouseleave", function () {
						$(_this).popover('hide');
					});
				}, 300)
			})
			.on("mouseleave", function () {
				var _this = this;
				setTimeout(function () {
					if (!$(".popover:hover").length) {
						$(_this).popover("hide");
					}
				}, 300);
			});

	/* ================================================================= */




	$('.workspace-contant-sec').each(function(){
	   var $cur = $(this);
		var count = $('.seldm',$cur).length;
		$cur.prev().find('.con').html(' ('+count+')');
		if(count <= 1){
			$cur.prev().find('.sorted').addClass('disabled');
			$cur.prev().find('.sortOrderStartFirst').css('pointer-events','none');
			$cur.prev().find('.sortOrderEndFirst').css('pointer-events','none');

		}else{
			$cur.prev().find('.sorted').removeClass('disabled');
			$cur.prev().find('.sortOrderStartFirst').removeAttr('style');
			$cur.prev().find('.sortOrderEndFirst').removeAttr('style');
		}

	})


	$c_status ='<?php echo $cky; ?>';


	$('#modal_medium').on('show.bs.modal', function (e) {

	 $(this).find('.modal-content').css({
		  width: $(e.relatedTarget).data('modal-width'), //probably not needed
	 });
	});
	$('#modal_small').on('show.bs.modal', function (e) {

	 $(this).find('.modal-content').css({
		  width: $(e.relatedTarget).data('modal-width'), //probably not needed
	 });
	});

	$('#modal_medium').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	});
	$('#modal_small').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	});
	$('#modal_large').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	});

	$.get_counters = function(project_id){
		$.ajax({
			url: $js_config.base_url+'boards/counters/'+project_id,
			type: 'POST',
			dataType: 'json',
			success: function(response) {
				$('.wsp-task-counters').html(response);
			}
		})
	}

		$('body').delegate('.submit_element','click', function(e){

			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormUpdateElement'),
				add_ws_url = $form.attr('action'),
				runAjax = true;

			if( runAjax ) {
				runAjax = false;
				$.ajax({
					url: add_ws_url,
					type:'POST',
					data: $form.serialize(),
					dataType: 'json',
					beforeSend: function( response, status, jxhr ) {
						// Add a spinner in button html just after ajax starts
						$this.html('<i class="fa fa-spinner fa-pulse"></i>')
					},
					success: function( response, status, jxhr ) {

						$this.html('Save')
						// REMOVE ALL ERROR SPAN
						$form.find('span.error-message.text-danger').text("")

						if( response.success ) {
							$('#modal_medium').modal('hide');
							var seldm  = $('#ElementId').val();

							$('#sel_element').val(seldm);

							setTimeout(function(){
								//$('#filter_list').trigger('click');

								var $that = $(this),
								$projectId 		= '<?php echo $project_id;?>',
								$workspace	 	= $("#WorkspaceId").val(),
								params			= {
									project_id: '<?php echo $project_id;?>',
									ws_id: $workspace,
								}

								$.ajax({
									url: $js_config.base_url + 'boards/filtered_list',
									type: "POST",
									data: $.param(params),
									dataType: "JSON",
									global: false,
									success: function (response) {
										$('.sbwrapper').html(response)
										$.get_counters($projectId)

										$('.workspace-contant-sec').each(function(){
											var $cur = $(this);
											var count = $('ul li.seldm:visible',$cur).length;

											$cur.prev().find('.con').html(' ('+count+')');
										})

									}
								})

							},300)


						}
						else {
							$this.html('Save')
							if( ! $.isEmptyObject( response.content ) ) {

								$('.date_constraints_wrappers').find('.err').text('')
								$.each( response.content, function( ele, msg) {

									if( msg.hasOwnProperty("start_date") ) {
										$('.date_constraints_wrappers[data-section="'+ele+'"]')
											.find('.start-date-errors')
											.text("Task start date can't be less than workspace start date.")
									}

									if( msg.hasOwnProperty("end_date") ) {
										$('.date_constraints_wrappers[data-section="'+ele+'"]')
											.find('.end-date-errors')
											.text("Task end date can't be greater than workspace end date.")
									}

									if( msg.hasOwnProperty("start_end_date") ) {
										$('.date_constraints_wrappers[data-section="'+ele+'"]')
											.find('.start-end-date-errors')
											.text("Task start date can't be greater than end date.")
									}

								})

							}

							if( ! $.isEmptyObject(response.date_error ) ) {
								$("#date-error-message").html('<div id="successFlashMsg" class="box box-solid bg-red" style="overflow: hidden;  "><div class="box-body"><p>'+response.date_error+'</p></div></div>')
							   setTimeout(function(){
									$("#date-error-message").fadeOut("500");
								},2000)
							}
						}
					}
				});
				// end ajax

			}
		})





	//var area_params = {workspace_id: 2};

	jQuery.fn.toggleText = function (value1, value2) {
	    return this.each(function () {
	        var $this = $(this),
	            text = $this.text();

	        if (text.indexOf(value1) > -1)
	            $this.text(text.replace(value1, value2));
	        else
	            $this.text(text.replace(value2, value1));
	    });
	};



	setTimeout(function(){
		$('.sorted').trigger('click');
	},500)


	$('body').delegate('.sorted', 'click', function(e) {
		var current =  $(this).parents('.workspace-sec:first');
		$(this).addClass('already_sorteds');


		$('ul li', current).sort(function (a, b) {

			var contentA = $(a).attr('data-etitle');
			var contentB = $(b).attr('data-etitle');
			return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
		}).appendTo($('ul', current))




		//var elems = $.makeArray($('li',current));


/* 		var elemsData =  $.map( elems, function( n, i ) {
						  return ( $(n).data()   );
						 }); */
		//var ascData = _.orderBy(elemsData, ['etitle'], ['asc']);//, 'desc'
		//console.log(ascData);
		/* elems.sort(function(a, b) {
		    $('#ajax_overlay').show();
			//return $(a).data("etitle") > $(b).data("etitle");
			 console.log($(a).data("etitle").toLowerCase(),$(b).data("etitle").toLowerCase() )
			  if($.type($(a)) !== undefined ){
				return $(a).data("etitle").toLowerCase().localeCompare($(b).data("etitle").toLowerCase());
			 }
		});


		$('.workspace-contant-sec  ul',current).html(elems); */


		$('#ajax_overlay').hide();
		$('.users_popovers,.pophover').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});
		$('.sorted',current).text("AZ");

	})


	$('body').delegate('.sorted.already_sorteds', 'click', function(e) {
		var current =  $(this).parents('.workspace-sec:first');
		$(this).removeClass('already_sorteds');
		var elems = $.makeArray($('li',current));

		elems.sort(function(a, b) {
		    $('#ajax_overlay').show();

			//return $(a).data("etitle") < $(b).data("etitle");
			/* if($(b).length > 0 && $(a).length > 0 ){
				return $(b).data("etitle").toLowerCase().localeCompare($(a).data("etitle").toLowerCase());
			} */
		});


		// $('.workspace-contant-sec  ul',current).html(elems);


		$('ul li', current).sort(function (a, b) {

			var contentA = $(a).attr('data-etitle');
			var contentB = $(b).attr('data-etitle');
			return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
		}).appendTo($('ul', current))



		$('#ajax_overlay').hide();
		$('.users_popovers,.pophover').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});
		$('.sorted',current).text("ZA" );

	})




	$('body').delegate('.sortOrderStartFirst', 'click', function(e) {
		var current =  $(this).parents('.workspace-sec:first');
		var elems = $.makeArray($('li',current));
		elems.sort(function(a, b) {
		    $('#ajax_overlay').show();
		    // alert('start date')
		    // return ($(a).attr('data-start') ) > ($(b).attr('data-start')  ? 1 : -1);
		    return (new Date($(a).attr('data-start')) < new Date($(b).attr('data-start'))) ? -1 : (new Date($(a).attr('data-start')) > new Date($(b).attr('data-start'))) ? 1 : 0;
			// return new Date( $(a).attr("data-start") ) > new Date( $(b).attr("data-start") );
		});
		$('.workspace-contant-sec  ul',current).html(elems);
		$('#ajax_overlay').hide();
		$('.users_popovers,.pophover').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});
	})


	$('body').delegate('.sortOrderEndFirst', 'click', function(e) {
	 	var current =  $(this).parents('.workspace-sec:first');
		var elems = $.makeArray($('li',current));
		elems.sort(function(a, b) {
			$('#ajax_overlay').show();
		    console.log('end date')
			return (new Date($(a).attr('data-end')) < new Date($(b).attr('data-end'))) ? -1 : (new Date($(a).attr('data-end')) > new Date($(b).attr('data-end'))) ? 1 : 0;
			// return new Date( $(a).attr("data-end") ) > new Date( $(b).attr("data-end") );
		});
		$('.workspace-contant-sec  ul',current).html(elems);
		$('#ajax_overlay').hide();
		$('.users_popovers,.pophover').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});



	})



	$('body').delegate('#filter_reset', 'click', function(e) {
		$('#WorkspaceId').prop('selectedIndex',0);
		location.reload();
	})


    $('body').delegate('#WorkspaceId', 'change', function(e) {

        $('.remove-iocn').trigger('click');

		var $that 			= $(this),
			$projectId 		= $('#ProjectId'),
			$workspace	 	= $('#WorkspaceId'),
			$sort_by_toggle	= $('input:checkbox[name=sort_by_toggle]'),
			$sort_by 		= $('input:radio[name=sort_by]:checked'),
			params			= {
				project_id: 	'<?php echo $project_id;?>',
				ws_id: 			$workspace.val(),
			}




                $("#project_report_link").attr("href", $js_config.base_url +"projects/reports/"+$projectId.val())
                $("#dashboard_link").attr("href", $js_config.base_url +"projects/objectives/"+$projectId.val())
                if($c_status != null)
				$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/"+$c_status+':'+$projectId.val())


		if($sort_by_toggle.is(':checked'))
			params['sort_by'] = $sort_by.val()

		$.when(
			$.ajax({
				url: $js_config.base_url + 'boards/filtered_list',
				type: "POST",
				data: $.param(params),
				dataType: "JSON",
				global: false,
				success: function (response) {
					$('.sbwrapper').html(response)
				}
			})

		).then(function( data, textStatus, jqXHR ) {
			// console.log('request completed.')

			if($workspace.val() > 0){
				$('.areas').show();
				$('.wsp').hide();
			}else{
				$('.areas').hide();
				$('.wsp').show();
			}

			$('.workspace-contant-sec').each(function(){
			    var $cur = $(this);
				var count = $('.seldm',$cur).length;
				$cur.prev().find('.con').html(' ('+count+')');
				if(count <= 1){
					$cur.prev().find('.sorted').addClass('disabled');
					$cur.prev().find('.sortOrderStartFirst').css('pointer-events','none');
					$cur.prev().find('.sortOrderEndFirst').css('pointer-events','none');

				}else{
					$cur.prev().find('.sorted').removeClass('disabled');
					$cur.prev().find('.sortOrderStartFirst').removeAttr('style');
					$cur.prev().find('.sortOrderEndFirst').removeAttr('style');
				}

			})

			var seldmn = $('#sel_element').val();
			$('.seldm .seldm_'+seldmn).css('box-shadow','1px 2px 10px 5px #3f3f52');
			$('.seldm .seldm_'+seldmn).parents('.workspace-contant-sec').addClass('active');
			setTimeout(function(){
				if($('.workspace-contant-sec.active').length > 0){
				 $( '.workspace-contant-sec.active').scrollTop($('.workspace-contant-sec.active .seldm_'+seldmn).offset().top - $('.workspace-contant-sec.active').offset().top);

				 $('html, body').animate({
					 scrollTop: $('.workspace-contant-sec.active .seldm_'+seldmn).offset().top -100
				  }, 'slow');

				  }

            $('#sel_element').val('');



			},1500)

	$('.users_popovers,.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });


		})
	})


	$('body').delegate('.seldm .user-contant-area', 'click mouseover', function(e) {

		$('.seldm .user-contant-area').removeAttr('style');
	})

	$(document).on('click',function(){

		$('.seldm .user-contant-area').removeAttr('style');
	})

	//$('.not-specified .user-right-cont a.read').tooltip({ container: 'body', placement: 'top'});)
	$('.not-specified .user-right-cont a.read').tooltip({
		template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"></div></div>',
		'container': 'body',
		'placement': 'top',
	})


	 var mark = function () {
        var keyword = $(".ttsearch input").val();
		//console.log(keyword);
        $(".workspace-contant-sec .seldm").unmark();
        $(".workspace-contant-sec .seldm").mark(keyword);

			$allListElements = $('.sbwrapper .workspace-contant-sec .seldm');
            var $matchingListElements = $allListElements.filter(function (i, li) {
                var listItemText = $(li).text().toUpperCase(), searchText = keyword.toUpperCase();
                return ~listItemText.indexOf(searchText);
            });

            $allListElements.hide();
            $matchingListElements.show();
            var searchText = keyword.toUpperCase();
            $(".sbwrapper .workspace-contant-sec .seldm").unmark();
            $(".sbwrapper .workspace-contant-sec .seldm").mark(searchText);


    };


    //$("body").delegate(".ttsearch  > input", 'keyup', mark);


	$('body').delegate('.task-search  .fa-times', 'click', function(e) {
		 $('.search-box-bord').val('');
		 $('.ttsearch  input.search-box-bord').trigger('keyup');
		 $('ul li.just_append').remove();
	})


	$('.ttsearch input').val('');


    $('body').delegate('.ttsearch  input', 'keyup', function (event) {
  // $('body').delegate('.searc-but', 'click', function (event) {
        event.preventDefault();
            var keyword = $(".ttsearch input").val();

			if (keyword && keyword.length > 0 && keyword !== undefined) {

				if( !$('.task-search i').hasClass('fa-times') ){
					$('.task-search i').removeClass('fa-search').addClass('fa-times').parent().attr('data-original-title','Clear Search');

				}
			}else{
				if( !$('.task-search i').hasClass('fa-search') ){

					$('.task-search i').removeClass('fa-times').addClass('fa-search').parent().attr('data-original-title','Search');
				}
			}

            $allListElements = $('.sbwrapper .workspace-contant-sec li.seldm');
            var $matchingListElements = $allListElements.filter(function (i, li) {
                var listItemText = $(li).text().toUpperCase(), searchText = keyword.toUpperCase();
                return ~listItemText.indexOf(searchText);
            });

            $allListElements.hide();
            $matchingListElements.show();
            var searchText = keyword.toUpperCase();
            $(".sbwrapper .workspace-contant-sec .seldm").unmark();
            $(".sbwrapper .workspace-contant-sec .seldm").mark(searchText);

			// $('.workspace-sec .workspace-contant-sec').trigger('each');


			$('.workspace-sec .workspace-contant-sec').each(function(event) {

					 var cur = $(this);
					 var count = $('ul li:not(.just_append):visible',cur).length;
					console.log(count,cur);
					 if(count < 1){
					    $('ul li.just_append',cur).remove();
						$('ul',cur).append('<li class="just_append"><div class="user-contant-area " style="border-right-width:1px;">None</div></li>');
					 }else if(count > 0){
					 console.log(count,cur);
						  $('ul li.just_append',cur).remove();

					 }

				})


					$('.workspace-contant-sec').each(function(){
						var $cur = $(this);
						var count = $('ul li.seldm:visible',$cur).length;

						$cur.prev().find('.con').html(' ('+count+')');
						console.log(count);
					})


    });






});



</script>
<style>
.icon_status_board {
    background-attachment: scroll !important;
    background-image: url("../../images/icons/spinner-2.png") !important;
    background-position: center center;
    background-repeat: no-repeat !important;
    background-size: 100% auto !important;
    display: inline-block;
    height: 18px;
    vertical-align: middle;
    width: 20px;
}
</style>