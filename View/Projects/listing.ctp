<?php echo $this->Html->css('projects/list_projects') ?>
<?php echo $this->Html->css('projects/alert') ?>

<?php echo $this->Html->script('projects/plugins/colored_tooltip', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));



 ?>


<style>
	.prj-title-tip {
		cursor: pointer;
	}

	.calendar_trigger i.fa {
		font-size: 16px;
	}
	.btns .btn:focus, .btns .btn:active {
		color: #333 !important;
	}

	#ul_list_grid .tipText{ text-transform: none !important; }

	.tooltip{ text-transform: none !important; }

	#viewcontrols a:not(.active) {
	    background-color: #d3d3d3 !important;
	    border-color: #c8c8c8;
	    color: #9c9c9c;
	}
	.row section.content-header h1 p.text-muted span {
	    color: #7c7c7c;
	    font-weight: normal;
	    text-transform: none;
	}
	.box-header.filters {
	    background-color: #f1f3f4;
	    border-color: transparent  #ddd #ddd;
	    border-image: none;
	    border-style: none solid solid;
	    border-width: medium 1px 1px;
	    cursor: move;
	}
	.text-dark-gray { color: #828282 !important; }
	.objectivies {
		margin: 0 0 10px 0;
		padding: 0 0 10px 0;
		border-bottom: 1px solid #ccc;
	}
	.objectivies h5, .descriptions h5 {
		margin: 3px 0px 4px;
	}

	.ui-datepicker.ui-datepicker-multi{

	margin-right: 30px !important;
	}
	.ui-datepicker-range > .ui-state-default {
		background: #f39c12 none repeat scroll 0 0;
		border-color: #c36c10;
		color: #fff;
		font-size: 12px;
	}
	.ui-datepicker .ui-datepicker-title {
		font-size: 12px;
		line-height: 1.8em;
		margin: 0 2.3em;
		text-align: center;
	}
	.ui-datepicker th {
		border: 0 none;
		font-size: 12px;
		font-weight: bold;
		padding: 0.7em 0.3em;
		text-align: center;
	}
	.ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next {
		height: 1.4em;
	}
	.ui-datepicker td {
		border: 0 none;
		padding: 0.5px;
	}
	.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
		font-size: 12px;
	}
	.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
		font-size: 12px;
	}
	.ui-datepicker-group {
		width: 25.3%;
	}

	.empty-dates {
		background: #fff none repeat scroll 0 0;
	    border-radius: 50%;
	    cursor: pointer;
	    float: right;
	    font-size: 13px;
	    margin: 2px 20px 0 0;
	    padding: 2px 3px;
	}
	.project_image {max-height: 150px; width: 100%;}
	.img-options {
	    background: rgba(255, 255, 255, 0) none repeat scroll 0 0;
	    border: 1px solid rgba(255, 255, 255, 0.3);
	    border-radius: 3px;
	    display: inline-block;
	    padding: 5px;
	    position: absolute;
	    right: 10px;
	    top: 10px;
	    transition: all 0.5s ease-in-out 0s;
	    z-index: 99;
	}
	.project-image:hover .img-options {
		background: rgba(255, 255, 255, 0.7) none repeat scroll 0 0;
	}

	.select-project-sec {
    /* border: 1px solid #ccc; */
    min-height: 177px;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
	}

	.select-project-sec .no-data {
    color: #bbbbbb;
    font-size: 30px;
    text-align: center;
    text-transform: uppercase;
	}


	@media (min-width:992px) and (max-width:1199px) {
		.panel .selected-dates {
		  font-size: 10px;
		}

		.empty-dates {
		  font-size: 13px;
		  line-height: 12px;
		  margin: 0 8px 2px 0;
		}
	}

	.pos-absolute {
	    position: absolute;
	    z-index: 1001;
	    width: 100%;
	    border: 1px solid #00c0ef !important;
    	background-color: #fff;
    	max-height: 300px;
    	overflow: auto;
	}
	.list-group.panel {
		position: relative;
		border: 1px solid #00c0ef !important;
    	border-radius: 0;
	}
	.list-group-submenu > a {
	    padding: 10px 0 10px 25px;
	}
	.menu-ico-collapse {
	    float: right;
	    color: #b5b5b5;
	}
	.col-menu {
		font-weight: 600;
	}
	.list-group.panel > a {
	    border-radius: 0;
    	margin-bottom: 0;
	}
	.main-menu {
	    padding: 6px 15px 5px 14px;
	}
	a.main-menu:focus {
	    background-color: #ffffff;
	}
	span.first-selected {
	    display: inline-block;
	    max-width: 95%;
	    overflow: hidden;
	    vertical-align: top;
	    text-overflow: ellipsis;
	    white-space: nowrap;
	}
	/*.has-submenu {
	    background-color: #f0f0f0;
	}*/
	.has-submenu i:before, .has-submenu[aria-expanded="false"] i:before {
	    content: "\e114";
	}
	.has-submenu[aria-expanded="true"] i:before {
	    content: "\e113";
	}
	.recent_projects {
		padding: 10px 15px;
	    font-size: 14px;
	    font-weight: 600;
	}
	.more_projects {
		margin: 10px 15px;
	    font-weight: 600;
	    border-top: 1px solid #ccc;
	    padding-top: 10px;
	}

	.btn-control {
	    background-color: #FFFFFF;
	    color: #444;
	    border-color: #cccccc;
	}
	.clear_prj_filter {
	    padding: 3px;
	    margin-right: -4px;
	}
	/*#prj-dd .dropdown-menu {
	    opacity: .3;
	    -webkit-transform-origin: top;
	    transform-origin: top;
	    -webkit-animation-fill-mode: forwards;
	    animation-fill-mode: forwards;
	    -webkit-transform: scale(1, 0);
	    display: block;
	    transition: all 0.2s linear;
	    -webkit-transition: all 0.2s linear;
	    min-width: 100px;
	    font-size: 11px;
	}*/
	.dropdown-menu {
	    color: #333 !important;
	}
	#prj-dropdown li a i.fa-check {
	    display: none;
	}
	#mainmenu .list-group-item {
		border: none;
	}
	/*.fav-current-project.active {
		background-color: #67a028;
		color: #fff;
	}*/
	.fav-current-project a:hover {
		color: #fff;
		background-color: #7f7f7f;
	}
</style>
<script type="text/javascript">
	jQuery(function($) {
		$.selectedDates;

		$('body').on('click', ".calendar_trigger", function(event) {
			event.preventDefault()
			$(this).parent('.sub-heading').find('.datepick').trigger('focus');
	    });

		$('body').delegate(".empty-dates", 'click', function(event) {
			event.preventDefault()

			var $that = $(this),
				$input = $(this).parents('.tasks:first').prev('.sub-heading:first').find('.datepick'),
				$tasks = $(this).parents('.tasks:first'),
				project_id = $tasks.data('pid'),
				$task = $tasks.find('.task'),
				datePicker = $input.data('datepicker'),
				$cal_icon = $input.parent('.sub-heading').find('.calendar_trigger:first'),
				$hidden_md = $input.parent('.sub-heading').find('.hidden-md:first');

			datePicker.input.val('');

			$.ajax({
				type:'POST',
				dataType:'json',
				data: $.param({ project_id: project_id }),
				url: $js_config.base_url + 'projects/ending_elements',
				global: false,
				success: function( response, status, jxhr ) {
					$task.html(response).css('padding', '0px 10px 10px');
					setTimeout(function(){
							$that.parent().css('display', 'none')
							$cal_icon.attr('data-original-title', 'Select Dates');
							$hidden_md.text('(Next 14 days and Overdue)');
					}, 1)
				},
			});
	    });

		$('body').delegate(".project_image_upload", 'click', function(event) {
			event.preventDefault()

			var $that = $(this),
				data = $that.data(),
				url = data.remote,
				runAjax = true;

				$('#upload_model_box').modal({
					remote: url
				})
				.show()
				.on('hidden.bs.modal', function(event) {

					$(this).removeData('bs.modal');
					$(this).find('.modal-content').html('')
				})
	    });

		$("body").delegate(".remove_pimage", "click", function(event){
			event.preventDefault();

			var project_id = $(this).data('id');
			$.when(
				$.ajax({
					type: 'POST',
					dataType: 'JSON',
					data: $.param({ 'project_id': project_id }),
					url: $js_config.base_url + 'projects/remove_project_image/' + project_id,
					global: false,
					success: function (response) {
						if(response.success) {
							$("#project_image_wrapper").slideUp(300, function() {
								$(this).remove()
							})
						}
					}
				})
			)
			.then(function( rdata, textStatus, jqXHR ) {
				$.ajax({
					type: 'POST',
					dataType: 'JSON',
					data: $.param({ 'project_id': project_id }),
					url: $js_config.base_url + 'projects/get_project_image/' + project_id,
					global: false,
					success: function (response) {
						$("#image_file_"+project_id).hide().html(response).fadeIn(500)
					},
				});
			})

		})


		$("body").delegate("#upload_image", "click", function(event){
			event.preventDefault();

			var $t = $(this),
			$form = $("#modelFormProjectImage");

			var formData = new FormData($form[0]),
			$fileInput = $form.find("#doc_file"),
			file = $fileInput[0].files[0],
			$pidInput = $form.find("#project_id"),
			project_id = $pidInput.val(),
			url = $js_config.base_url + "projects/image_upload/" + project_id;

			if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name,
				size = file.size,
				type = file.type;

				formData.append('image_file', $fileInput[0].files[0]);

			}

			if ( $fileInput.val() !== "" ) {

				$.ajax({
					type: 'POST',
					dataType: "JSON",
					url: url,
					data: formData,
					global: true,
					cache: false,
					contentType: false,
					processData: false,
					xhr: function () {

						var xhr = new window.XMLHttpRequest();

						//Upload progress
						xhr.upload.addEventListener("progress", function (event) {
							if (event.lengthComputable) {
								var percentComplete = Math.round(event.loaded / event.total * 100);
								$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text(percentComplete + "%")
							}
						}, false);
						return xhr;
					},
					beforeSend: function () {

					},
					complete: function () {
						$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text("Loading...")
					},
					success: function (response) {

						if (response.success) {
							$.ajax({
								type: 'POST',
								dataType: 'JSON',
								data: $.param({ 'project_id': project_id }),
								url: $js_config.base_url + 'projects/get_project_image/' + project_id,
								global: false,
								success: function (response) {
									$("#image_file_"+project_id).hide().html(response).fadeIn(500)
								},
							});
							setTimeout(function(){
								$('#upload_model_box').modal('hide')
							}, 500)
						}
						else {
							$('.image_error').text(response.msg)
						}
					}
				});
			}
			else {
				$('.image_error').text('Please select a file.')
			}
		})
		$('body').delegate('.re_confirm', 'click', function(event) {
			event.preventDefault();
		});


		var bodyHeight = ($('.box-body').height() < 600) ? 600 : $('.box-body').height();

		$('#ul_list_grid .panel').each(function() {
	        var $projectslistwrap = $(this).find('.panel-body-inner');
	        $(this).data('projectslistwrap', $projectslistwrap)
	        $projectslistwrap.data('title', $(this))
	    })

	    /*$(document).on('click', function(e) {
	    	var $targets = $(e.target).parents('.ui-datepicker-group');
	        $('#ul_list_grid .panel').each(function() {
	            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.panel-body-inner').has(e.target).length === 0) {
	                var $projectslistwrap = $(this).data('projectslistwrap');
		            if($targets.length <= 0) {
		                $projectslistwrap.slideUp(300, function(){
		                	$('.box-body').css({ 'minHeight': bodyHeight + 'px'});
		                	// $("html, body").stop().animate({scrollTop: 0}, 500, 'swing', function() { });
		                })
		            }
	            }
	        });
	    });*/




		$('body').delegate('.li-listing .fav-current-project', 'click', function(event) {
			event.preventDefault();


			var $superparents = $(this).parents('#list_grid_container');
			var $parents = $(this).parents('li.li-listing');
			var $that = $parents.find('.btns a:first');
			var project_id = $parents.data('project');
			var projecttitle = $that.data('ptitle');
			var projecttitlefull = $that.data('ptitlefull');

			var pinCount = $(this).parents('#list_grid_container').find('li.li-listing .fav-current-project.active').length;

			var cpliHtml = '';
			//if( pinCount < 5 && !$(this).hasClass('remove_pin') ){
			if(  !$(this).hasClass('remove_pin') ){

				if( project_id > 0 && project_id !== ""  ){
					var cpliHtml = '';
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id ,'status': 'add'}),
						url: $js_config.base_url + 'projects/current_project/' + project_id,
						global: false,
						success: function (response) {
							if( response.success ){

								$that.attr('title','');
								$that.attr('data-original-title','Clear Bookmark');
								$that.find('i').removeClass('far fa-bookmark').addClass('fas fa-bookmark');
								$that.addClass('active');
								$that.addClass('remove_pin');

								var newUrl = $js_config.base_url+"projects/index/"+project_id;
								cpliHtml = '<li class="currentproject" data-ptitles="'+projecttitlefull+'" id="currentpid'+project_id+'" style="display:block;"><a class="" href="'+newUrl+'"><span class="left-icon-all"><i class="left-nav-icon project-left-icon"></i></span> '+projecttitle+'</a></li>';

								setTimeout(function(){
									if( $superparents.find('li.li-listing .fav-current-project.active').length >= 5 ){
										//$superparents.find('li.li-listing .fav-current-project').not('.active').addClass('disable');
										//$superparents.find('li.li-listing .fav-current-project').not('.active').removeAttr('title');
										//$superparents.find('li.li-listing .fav-current-project').not('.active').attr('data-original-title',"Max 5 Projects Bookmarked");
										//$superparents.find('li.li-listing .fav-current-project').not('.active').css('cursor','default');;
									}
									$.add_project_sidebar(cpliHtml);
								},100)


								/* cpliHtml = '<li class="currentproject" data-ptitles="'+projecttitlefull.replace(/([,.'"])+/g, '')+'" id="currentpid'+project_id+'" style="display:block;"><a class="" href="'+newUrl+'"><span class="left-icon-all"><i class="left-nav-icon project-left-icon"></i></span> '+projecttitle+'</a></li>';
								$after = $(".sidebar").find("#sidebar_menu li.prevcrntprjt ul.project-items");

								if($(".sidebar").find("#sidebar_menu li.prevcrntprjt ul.project-items").parent().find('.currentproject:last').length > 0){
									$after = $(".sidebar").find("#sidebar_menu li.prevcrntprjt ul.project-items").parent().find('.currentproject:last');
								}
								//$after.append(cpliHtml);

								var elem = $(".sidebar").find("#sidebar_menu .prevcrntprjt .project-items").parent().find('.currentproject'); */

							}
						},
					});
				}
			}

			if( $(this).hasClass('remove_pin') ){
				if( project_id > 0 && project_id !== ""  ){
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id,'status': 'remove' }),
						url: $js_config.base_url + 'projects/current_project/' + project_id,
						global: false,
						success: function (response) {
							if( response.success ){
								$that.removeClass('active');
								$that.removeClass('remove_pin');
								$that.find('i').removeClass('fas fa-bookmark').addClass('far fa-bookmark');
								// $(".sidebar").find("#sidebar_menu li.prevcrntprjt").after(cpliHtml);

								$("#currentpid"+project_id).remove();

								$superparents.find('li.li-listing .fav-current-project').not('.active').attr('data-original-title','Set Bookmark');
								var elem = $(".sidebar").find("#sidebar_menu li.prevcrntprjt ul.project-items").parent().find('.currentproject');
								setTimeout(function(){

									$superparents.find('li.li-listing .fav-current-project').not('.active').removeClass('disable');
									$superparents.find('li.li-listing .fav-current-project').not('.active').removeAttr('title');
									$superparents.find('li.li-listing .fav-current-project').not('.active').css('cursor','pointer');
								},100)

							}
						},
					});
				}
			}

		})

		setTimeout(function(){
			if( $('#list_grid_container').find('li.li-listing .fav-current-project.active').length >= 5 ){
				//$('#list_grid_container').find('li.li-listing .fav-current-project').not('.active').addClass('disable');
				//$('#list_grid_container').find('li.li-listing .fav-current-project').not('.active').removeAttr('title');
				//$('#list_grid_container').find('li.li-listing .fav-current-project').not('.active').attr('data-original-title','Max 5 Projects Bookmarked');
				//$('#list_grid_container').find('li.li-listing .fav-current-project').not('.active').css('cursor','default');
			}
		},10)


		$('body').delegate('.li-listing .open-project-detail', 'click', function(event) {
			event.preventDefault();

			var $this = $(this),
				$li = $this.parents('li.li-listing:first'),
				lidata = $li.data(),
				project_id = lidata.project,
				type = lidata.type;

			if(project_id !== undefined && !$li.hasClass('loaded')){
				$.ajax({
					url: $js_config.base_url + 'projects/project_detail/' + project_id,
					type: 'POST',
					global: false,
					data: {project_id: project_id, type: type},
					success: function(response){
						$('.panel-body-inner', $li).html(response);
						$li.addClass('loaded');
						var $this_list_wrap = $this.parents('.panel').find('.panel-body-inner');
						//if($(event.target).hasClass('btn') || $(event.target).hasClass('fa')) return;
						$('.panel-body-inner').not($this_list_wrap).slideUp('slow');

						$('.box-body').css({ 'minHeight': bodyHeight + 'px'});
						$this.parents('.panel').find('.panel-body-inner').slideToggle('slow', function() {
							if($(this).is(":visible")){
								// $("html, body").stop().animate({scrollTop: ($(this).parents('.panel').offset().top - 65)}, 1000, 'swing', function() { });
							}
							else{
								// $("html, body").stop().animate({scrollTop: 0}, 500, 'swing', function() { });
							}
						    var element = $(this);
							setTimeout(function(){
								var documentHeight = $(document).height();
							    var distanceFromBottom = documentHeight - (element.offset().top + element.outerHeight(true));
							    if(distanceFromBottom <= 0) {
							    	$('.box-body').css({ 'minHeight': ($('.box-body').height() + 100 + (Math.ceil(Math.abs(distanceFromBottom))))+'px' } )
							    }
							    else{
							    	$('.box-body').css({ 'minHeight': bodyHeight + 'px'})
							    }
						    }, 100)
						})
					}
				})
			}
			else{
				var $this_list_wrap = $this.parents('.panel').find('.panel-body-inner');
				// if($(event.target).hasClass('btn') || $(event.target).hasClass('fa')) return;
				$('.panel-body-inner').not($this_list_wrap).slideUp('slow');

				$('.box-body').css({ 'minHeight': bodyHeight + 'px'});
				$this.parents('.panel').find('.panel-body-inner').slideToggle('slow', function() {
					if($(this).is(":visible")){
						// $("html, body").stop().animate({scrollTop: ($(this).parents('.panel').offset().top - 65)}, 1000, 'swing', function() { });
					}
					else{
						// $("html, body").stop().animate({scrollTop: 0}, 500, 'swing', function() { });
					}
				    var element = $(this);
					setTimeout(function(){
						var documentHeight = $(document).height();
					    var distanceFromBottom = documentHeight - (element.offset().top + element.outerHeight(true));
					    if(distanceFromBottom <= 0) {
					    	$('.box-body').css({ 'minHeight': ($('.box-body').height() + 100 + (Math.ceil(Math.abs(distanceFromBottom))))+'px' } )
					    }
					    else{
					    	$('.box-body').css({ 'minHeight': bodyHeight + 'px'})
					    }
				    }, 100)
				})
			}
		});

		// PROJECTS DD CODE
		$('.main-menu').each(function () {
			$(this).data('menu', $(this).parent().find('.pos-absolute'))
			$(this).parent().find('.pos-absolute').data('trigger', $(this))
		})

		$('body').on('click', function (e) {
			$('.main-menu').each(function () {
				if ( !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.pos-absolute').has(e.target).length === 0) {
					var $menu = $(this).data('menu')
					if($menu.length && $menu.hasClass('in')){
						$('.main-menu').trigger('click');
					}
				}
			});
		});

		$('.sub-sub-item').on('click', function(event) {
			event.preventDefault();
			var $selOpt = $(this),
				data = $selOpt.data(),
				value = data.value;

			$(".clear_prj_filter").trigger('click');
			if(value == "" || value === undefined) {
	            $('.li-listing').show();
	        }
	        else{
	        	$('.li-listing').hide();
	        	$('.li-listing[data-project="'+data.value+'"]').show();
	        }
			$('.main-menu').trigger('click');
			$('.first-selected').text($(this).text());
			$.headers_view();
		});

	/*
		$('body').delegate('.li-listing .panel-title', 'click', function(event) {
			// event.preventDefault();
			var $this_list_wrap = $(this).parents('.panel').find('.panel-body-inner');
			if($(event.target).hasClass('btn') || $(event.target).hasClass('fa')) return;
			$('.panel-body-inner').not($this_list_wrap).slideUp('slow');


			$(this).parents('.panel').find('.panel-body-inner').slideToggle('slow', function() {
				if($(this).is(":visible")){
					// $("html, body").stop().animate({scrollTop: ($(this).parents('.panel').offset().top - 65)}, 1000, 'swing', function() { });
				}
				else{
					// $("html, body").stop().animate({scrollTop: 0}, 500, 'swing', function() { });
				}
			    var element = $(this);
				setTimeout(function(){
					var documentHeight = $(document).height();
				    var distanceFromBottom = documentHeight - (element.offset().top + element.outerHeight(true));
				    if(distanceFromBottom <= 0) {
				    	$('.box-body').css({ 'minHeight': ($('.box-body').height() + 100 + (Math.ceil(Math.abs(distanceFromBottom))))+'px' } )
				    }
				    else{
				    	$('.box-body').css({ 'minHeight': bodyHeight + 'px'})
				    }
			    }, 100)
			})
		});
	*/

	$.headers_view = function(){
		if($('.recent_list li:visible').length <= 0){
    		$('.recent_projects').hide();
    	}
    	else{
    		$('.recent_projects').show();
    	}
    	if($('.more_list li:visible').length <= 0){
    		$('.more_projects').hide();
    	}
    	else{
    		$('.more_projects').show();
    	}
	}
    $("#prj-dropdown li a").click(function(e) {
        e.preventDefault();
        var status = $(this).data('status');
        $("#prj-dropdown li a i.fa-check").hide();
        $('#prj-drop').html($(this).data('text') + ' <span class="fa fa-times bg-red clear_prj_filter"></span>');
        $("i.fa-check", $(this)).show();

        $('.li-listing').hide();
        if(status != '' && status == 'live') {
        	$('.li-listing[data-status="1"]').show();
			if($('.li-listing[data-status="1"]').length ==0){
				if($('.partial_data_no:visible').length < 1)
				$('.partial_data_no').show();
			}else{
				$('.partial_data_no').hide();
			}
        }
        else if(status != '' && status == 'signedoff') {
        	$('.li-listing[data-status="2"]').show();
			if($('.li-listing[data-status="2"]').length ==0){
				if($('.partial_data_no:visible').length < 1)
				$('.partial_data_no').show();
			}else{
				$('.partial_data_no').hide();
			}
        }
    	$.headers_view();
    });

    $('body').delegate(".clear_prj_filter", 'click', function(e) {

        $('#prj-drop').html('All Projects <span class="fa fa-times bg-red clear_prj_filter"></span>');
        $("#prj-dropdown li a i.fa-check").hide();

    	$('.li-listing').show();

		$('.partial_data_no.else').hide();

    	$.headers_view();

        return false;
    });
})
</script>

<div class="row">
	<div class="col-xs-12">
  		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="box-title pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span>Projects you are working on</span>
					</p>
				</h1>
			</section>
		</div>

<?php
	$my_projects_list = $this->ViewModel->my_projects_list_projectCenter(['Project.id', 'Project.title' ], null );
	if(isset($my_projects_list) && !empty($my_projects_list)){
		usort($my_projects_list, function($a, $b) {
		    return $a['Project']['title'] > $b['Project']['title'];
		});
	}

	$received_projects_list = $this->ViewModel->received_projects_list_projectCenter(['Project.id', 'Project.title' ], null );
	if(isset($received_projects_list) && !empty($received_projects_list)){
		usort($received_projects_list, function($a, $b) {
		    return $a['Project']['title'] > $b['Project']['title'];
		});
	}

	$group_received_projects_list = $this->ViewModel->group_received_projects_list_projectCenter(['Project.id', 'Project.title' ], null );

	if(isset($group_received_projects_list) && !empty($group_received_projects_list)){
		usort($group_received_projects_list, function($a, $b) {
		    return $a['projects']['title'] > $b['projects']['title'];
		});
	}


?>
    <div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">
                        <div class="box-header filters" style="">
							<!-- Modal Confirm // PASSWORD DELETE-->
							<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-warning fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-danger fade" id="confirm_deletion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
											<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
										</div>
										<div class="modal-body">body</div>
										<div class="modal-footer">
											<div class="btn-group pull-right" style="margin-left: 5px;">
												<button type="button" class="btn btn-success re_confirm" ><i class="fa fa-check"></i></button>
												<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i></button>
											</div>
											<div class="dropdiv pull-right" style=" color: #333; ">
												<span class="reconfirm-message pull-left text-left">The delete is permanent.<br />Please confirm deletion.</span>
												<a href="#" class="btn btn-success" style=" margin-left: 5px; ">Confirm</a>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- /.modal -->
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="upload_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

							<div class="col-sm-12 col-md-12 col-lg-12 nopadding-left my-pro-header">
								<div for="" class="padding-left col-sm-7 col-md-4" >
									<div id="mainmenu" class="">
									    <div class="list-group panel">
									        <a href="#menupos1" class="list-group-item main-menu" data-toggle="collapse" data-parent="#mainmenu"><span class="first-selected">Select a Project</span> <span class="menu-ico-collapse"><i class="glyphicon glyphicon-chevron-down"></i></span></a>
									        <div class="collapse pos-absolute" id="menupos1">

									        	<a href="#" class="list-group-item sub-sub-item" data-value="" data-permitid="" data-slug="" data-text="Select a Project" >Select a Project</a>

									            <a href="#submenu_my" class="list-group-item sub-item <?php if(!empty($my_projects_list)) { ?>has-submenu<?php } ?>"   data-toggle="collapse" data-parent="#submenu_my"><span class="col-menu">Created (<?php echo !empty($my_projects_list)? count($my_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon  "></i></span></a>
								            	<?php if($my_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_my">
												    	<?php
														foreach($my_projects_list as $key => $val ) {
															$prj = $val['Project'];
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_my"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo htmlentities($prj['title']); ?>" data-permitid="" data-share="<?php echo $this->ViewModel->getProjectPermit( $prj['id'], 'projects' ); ?>" data-slug="projects"><?php echo htmlentities($prj['title']); ?></a>
												      	<?php } ?>
										            </div>
												<?php } ?>
												<a href="#submenu_received" class="list-group-item sub-item <?php if(!empty($received_projects_list)) { ?>has-submenu<?php } ?>"  data-toggle="collapse" data-parent="#submenu_received"><span class="col-menu">Received (<?php echo !empty($received_projects_list)? count($received_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon "></i></span></a>
								            	<?php if($received_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_received">
												    	<?php
														foreach($received_projects_list as $key => $val ) {
															$prj = $val['Project'];
															$prj_permit = (isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) ) ? $val['ProjectPermission'] : null;
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_received"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo strip_tags($prj['title']); ?>" data-permitid="<?php echo $prj_permit['id']; ?>" data-share="<?php echo $this->ViewModel->getProjectPermit( $prj['id'], 'projects' ); ?>" data-slug="received_projects"><?php echo strip_tags($prj['title']); ?></a>
												      	<?php } ?>
										            </div>
												<?php } ?>
												<a href="#submenu_grp_received" class="list-group-item sub-item <?php if(!empty($group_received_projects_list)) { ?>has-submenu<?php } ?>" data-toggle="collapse" data-parent="#submenu_grp_received"><span class="col-menu">Group Received (<?php echo !empty($group_received_projects_list)? count($group_received_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon "></i></span></a>
								            	<?php if($group_received_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_grp_received">
												    	<?php
														foreach($group_received_projects_list as $key => $val ) {

															$prj = $val['projects'];
															$prj_permit = (isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) ) ? $val['ProjectPermission'] : null;
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_grp_received"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo strip_tags($prj['title']); ?>" data-permitid="<?php echo $prj_permit['id']; ?>" data-share="<?php echo $this->ViewModel->getProjectPermit( $prj['id'], 'projects' ); ?>" data-slug="group_received_projects"><?php echo strip_tags($prj['title']); ?></a>
												      	<?php }  ?>
										            </div>
												<?php } ?>
									        </div>
									    </div>
									</div>
								</div>
								<div for="" class="padding-left col-sm-3 col-md-2" >
									<span class="prj-filter">
										<span href="#" class="btn btn-xs btn-control dropdown" id="prj-dd">
										    <span href="#" class="dropdown-toggle" id="prj-drop" data-toggle="dropdown" aria-controls="prj-dropdown" aria-expanded="false">All Projects <span class="fa fa-times bg-red clear_prj_filter"></span></span>
							                <ul class="dropdown-menu" aria-labelledby="prj-drop" id="prj-dropdown">
							                    <li><a id="dropdown1-tab" aria-controls="dropdown1" data-text="Live Projects" data-status="live">Live Projects <i class="fa fa-check"></i></a></li>
							                    <li><a id="dropdown1-tab" aria-controls="dropdown1" data-text="Signed Off Projects" data-status="signedoff">Signed Off Projects <i class="fa fa-check"></i></a></li>
							                </ul>
						                </span>
					            	</span>
								</div>
								<!-- <div class="col-sm-12 col-md-6 myproject-filter">
									<div class="category_wrapper">
										<div class="col-1-list">
											<?php
											/*echo $this->Form->input('Project.category_id', array(
												'options' => $categories,
												'empty' => 'All Categories',
												'type'=>'select',
												'default'=> (isset($this->params['pass'][0])) ? $this->params['pass'][0] : '',
												'label' => false,
												'div' => false,
												'class' => 'form-control'
											));*/ ?>
										</div>
										<div class="col-2-list ipad-padding-right">
											<a id="filter_list" class="btn btn-success btn-sm"> Apply Filter </a>
											<a id="filter_reset" class="btn btn-danger btn-sm"> Reset </a>
										</div>
									</div>
								</div> -->
							</div>
                        </div>
						<div class="box-body clearfix list-acknowledge" style="min-height: 600px;">
							<div style="display: none;">

							</div>
							<div id="list_grid_container" class="">
								<?php
								$flagCheck = false;
								if( isset($updated_projects) && !empty($updated_projects)) { ?>
									<div class="sap recent_projects">Recent Projects</div>
									<ul class="grid clearfix recent_list" id="ul_list_grid">
										<?php
										foreach( $updated_projects as $key => $val ) {
											$flagCheck = true;

											$item = $val['Project'];
											$sign_off = 1;
											if (isset($item['sign_off']) && !empty($item['sign_off']) && $item['sign_off'] > 0) {
												$sign_off = 2;
											}
											$open_project_link =  SITEURL.'projects/index/'.$item['id']; ?>
										<li class="li-listing" data-project="<?php echo $item['id']; ?>" data-type="<?php echo $item['type']; ?>" data-status="<?php echo $sign_off; ?>">
											<div class="panel <?php echo $item['color_code'] ?>">
												<?php
												 $projecttip =  $item['title'];
												//$projecttip = str_replace("'", "", $item['title']);
												//$projecttip = str_replace('"', "", $projecttip);
												?>
												<div class="panel-heading" style="position: relative">
													<h4 class="panel-title" style="">
														<span class="p-list-h prj-title-tip" title="<?php echo htmlentities($projecttip); ?>"><?php $t = (strlen($item['title'])>24) ? substr($item['title'],0,24).'...' : $item['title'];echo htmlentities($item['title']); ?></span>
														<?php /* ?><div class="dates p-list-deta">
															<span><b>Start:</b>
															<?php
																echo ( isset($item['start_date']) && !empty($item['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['start_date'])),$format = 'd M, Y') : 'N/A';
									                        ?></span>
															<span><b>End:</b> <?php echo ( isset($item['end_date']) && !empty($item['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['end_date'])),$format = 'd M, Y') : 'N/A';?></span>
														</div><?php */

													$favproject = $this->ViewModel->checkCurrentProjectid($item['id']);

													if( isset($favproject) && !empty($favproject) ){
														$tiptextw = 'Clear Bookmark';
														$removecls = " remove_pin";
														$activecls = " active";
														$bookmarkIcon = '<i class="fas fa-bookmark"></i>';
													 } else {
														 $tiptextw = 'Set Bookmark';
														 $removecls = "";
														 $activecls = "";
														 $bookmarkIcon = '<i class="far fa-bookmark"></i>';
													 }



													 $dpt = (strlen($item['title'])>20) ? substr(strip_tags($item['title']),0,20).'...' : strip_tags($item['title']);
												?>

														<div class="btns" style="">
															<!--<a data-ptitle="<?php echo $dpt;?>" data-ptitlefull="<?php echo strip_tags(htmlentities($item['title']));?>" class="btn btn-default btn-xs tipText fav-current-project remove-mark <?php echo $activecls.$removecls;?>"  title="<?php echo tipText($tiptextw); ?>" >-->
															<a data-ptitle="<?php echo $dpt;?>" data-ptitlefull="<?php echo strip_tags($item['title']);?>" class="btn btn-default btn-xs tipText fav-current-project remove-mark <?php echo $activecls.$removecls;?>"  title="<?php echo tipText($tiptextw); ?>" >
																<?php echo $bookmarkIcon;?>
															</a>
															<a class="btn btn-default btn-xs tipText open-project-detail"  title="<?php tipText('Show Summary' ); ?>" href="#" style="padding: 1px 9px;" >
																<i class="fa fa-arrows-v"></i>
															</a>
															<a class="btn btn-default btn-xs tipText open-project"  title="<?php tipText('Open Project' ); ?>" href="<?php echo $open_project_link; ?>" >
																<i class="fa fa-folder-open"></i>
															</a>
														</div>
													</h4>
												</div>
												<div class="panel-body">
													<div class="panel-body-inner"></div>
												</div>
											</div>
										</li>
										<?php
										}
										?>
									</ul>
								<?php } ?>
								<?php  if( isset($projects) && !empty($projects)) { ?>
									<div class="sap saparator more_projects">More Projects</div>
									<ul class="grid clearfix more_list" id="ul_list_grid">
										<?php
										foreach( $projects as $key => $val ) {
											$flagCheck = true;
											$item = $val['Project'];
											$sign_off = 1;
											if (isset($item['sign_off']) && !empty($item['sign_off']) && $item['sign_off'] > 0) {
												$sign_off = 2;
											}
											$open_project_link =  SITEURL.'projects/index/'.$item['id']; ?>
										<li class="li-listing" data-project="<?php echo $item['id']; ?>" data-type="<?php echo $item['type']; ?>" data-status="<?php echo $sign_off; ?>">
											<div class="panel <?php echo $item['color_code'] ?>">

												<div class="panel-heading" style="position: relative">
													<h4 class="panel-title" style="display: inline-block; width: 95%; max-width:100%;">
														<span class="p-list-h prj-title-tip" title="<?php echo strip_tags($item['title']); ?>"><?php $t = (strlen($item['title'])>24) ? substr($item['title'],0,24).'...' : $item['title'];echo strip_tags($item['title']); ?></span>
														<?php /* ?><div class="dates p-list-deta">
															<span><b>Start:</b>
															<?php
																echo ( isset($item['start_date']) && !empty($item['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['start_date'])),$format = 'd M, Y') : 'N/A';
									                        ?></span>
															<span><b>End:</b> <?php echo ( isset($item['end_date']) && !empty($item['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['end_date'])),$format = 'd M, Y') : 'N/A';?></span>
														</div><?php */
														$favproject = $this->ViewModel->checkCurrentProjectid($item['id']);
														if( isset($favproject) && !empty($favproject) ){
															$tiptextw = 'Clear Bookmark';
															$removecls = " remove_pin";
															$activecls = " active";
															$bookmarkIcon = '<i class="fas fa-bookmark"></i>';
														 } else {
															 $tiptextw = 'Set Bookmark';
															 $removecls = "";
															 $activecls = "";
															 $bookmarkIcon = '<i class="far fa-bookmark"></i>';
														 }
														  $dpt = (strlen($item['title'])>20) ? str_replace(".", "", substr(strip_tags($item['title']),0,20)).'...' : str_replace(".", "", strip_tags($item['title']));
														?>

														<div class="btns" style="margin : -3px 0 0 0">
															<a data-ptitle="<?php echo $dpt;?>" data-ptitlefull="<?php echo strip_tags($item['title']);?>" class="btn btn-default btn-xs tipText fav-current-project remove-mark <?php echo $activecls.$removecls;?>"  title="<?php echo tipText($tiptextw); ?>" href="#">
																<?php echo $bookmarkIcon; ?>
															</a>
															<a class="btn btn-default btn-xs tipText open-project-detail"  title="<?php tipText('Show Summary' ); ?>" href="#" style="padding: 1px 9px;" >
																<i class="fa fa-arrows-v"></i>
															</a>
															<a class="btn btn-default btn-xs tipText open-project"  title="<?php tipText('Open Project' ); ?>" href="<?php echo $open_project_link; ?>" >
																<i class="fa fa-folder-open"></i>
															</a>
														</div>
													</h4>
												</div>
												<div class="panel-body">
													<div class="panel-body-inner"></div>
												</div>
											</div>
										</li>
										<?php } ?>
									</ul>
								<?php } ?>
							</div>

						<?php

						if(($flagCheck==false)){?>

						<div class="col-sm-12 partial_data partial_data_no box-borders " style="padding: 0 10px 10px 10px;">
							<div class="col-sm-12 box-borders select-project-sec">

										<div class="no-data">No Projects</div>

							</div>
						</div>

						<?php } ?>

						<div class="col-sm-12 partial_data partial_data_no else box-borders " style="padding: 0 10px 10px 10px;display:none;">
							<div class="col-sm-12 box-borders select-project-sec">

										<div class="no-data">No Projects</div>

							</div>
						</div>


					    </div>
                    </div>
				<?php /*}
				else if( isset($this->params['pass'][0])) {
					echo $this->element('../Projects/partials/error_data', array(
									'error_data' => [
									'message' => "You have not created a project under selected category.",
									'html' => "Click <a class='' href='".Router::Url(
											array('controller' => 'projects',
												'action' => 'manage_project',
												'admin' => FALSE
											), TRUE
										)."'>here</a> to create a new Project."
								]
							));
				}
				else {
					echo $this->element('../Projects/partials/error_data', array(
									'error_data' => [
									'message' => "You have not created a project yet.",
									'html' => "Click<a class='' href='".Router::Url(
											array('controller' => 'projects',
												'action' => 'manage_project',
												'admin' => FALSE
											), TRUE
										)."'> here </a>to create a project now."
								]
							));
				}*/
				?>
                </div>
            </div>
        </div>
	</div>
</div>



<?php //echo $this->Html->script('templates/list-grid', array('inline' => true)) ?>
<script type="text/javascript" >
	$(function() {
		$('.prj-title-tip').tooltip({
			placement: 'top-left'
		})

		$('body').on('click', '.prj-title-tip', function(event) {
			event.preventDefault();
			location.href = $(this).parents('.panel-title:first').find('.open-project').attr('href');
		});

		$('#modal_delete').on('hidden.bs.modal', function () {
	        $(this).removeData('bs.modal');
	        $(this).find('.modal-content').html('');
	        $.current_delete = {};
	    });


		$(window).scroll(function() {
			var $active_element = $('.color_box').filter(function() {
				return $(this).is(":visible") == true;
			});
			if( $active_element.length ) {

				var s =  $active_element.isScrolledIntoView();
				var p =  $active_element.parent().isScrolledIntoView();
				if ( ( s.top + $active_element.height() ) > $(window).height() ) {
					$active_element.removeClass('color_box_bottom').addClass('color_box_top')
				}
				else if ( ( p.top ) < 100 ) {
					$active_element.removeClass('color_box_top').addClass('color_box_bottom')
				}
			}
		});


		$('#popup_modal').on('hidden.bs.modal', function () {
	        $(this).removeData('bs.modal');
	    });

		$('.color_bucket').each(function () {
			$(this).data('color_box', $(this).parent().find('.color_box'))
			$(this).parent().find('.color_box').data('color_bucket', $(this))
		})

		$.getViewportOffset = function($e) {
		  var $window = $(window),
			scrollLeft = $window.scrollLeft(),
			scrollTop = $window.scrollTop() + 200,
			offset = $e.offset(),
			rect1 = { x1: scrollLeft, y1: scrollTop, x2: scrollLeft + $window.width(), y2: scrollTop + $window.height() },
			rect2 = { x1: offset.left, y1: offset.top, x2: offset.left + $e.width(), y2: offset.top + $e.height() };

				return {
					left: (offset.left) - scrollLeft,
					top: (offset.top) - scrollTop,
					insideViewport: rect1.x1 < rect2.x2 && rect1.x2 > rect2.x1 && rect1.y1 < rect2.y2 && rect1.y2 > rect2.y1
				};
			}

		$.fn.isScrolledIntoView = function() {
			var offset = $(this).offset();

			return {
				left: offset.left - $(window).scrollLeft(),
				top: offset.top -  $(window).scrollTop()
			};
		};

		$.fn.visible = function(partial) {

			var $t        = $(this),
			$w            = $(window),
			viewTop       = $w.scrollTop(),
			viewBottom    = viewTop + $w.height(),
			elTop         = $t.offset().top,
			elVisibility  = (viewBottom - elTop) / $t.height();

			return ( elVisibility >= partial);
		}

		$('body').on('click', '.color_bucket', function (event) {
			event.preventDefault();
			var $color_box = $(this).parent().find('div.color_box'),
				vars = {
					offset: $color_box.offset(),
					top: $color_box.offset().top,
					left: $color_box.offset().left,
					w: $color_box.width(),
					h: $color_box.height(),
					sidebar_width: $('aside.main-sidebar').width(),
					panel: $(".color_bucket:first").parents(".panel:first"),
					panel_offset: $(".color_bucket:first").parents(".panel:first").offset(),
					wWidth: $(window).width(),
					wHeight: $(window).height(),
					dHeight: $(document).height(),
					scroll: $(window).scrollTop()
				}

				$color_box.slideToggle(200)
				if( $color_box.offset().left < 230 ) { // set right
					$color_box.removeClass('color_box_left').addClass('color_box_right')
				}
				if( vars.panel_offset > ($(window).width() - 300) ) {// set left
					$color_box.removeClass('color_box_right').addClass('color_box_left')
				}

				var s =  $color_box.isScrolledIntoView();
				var p =  $color_box.parent().isScrolledIntoView();
				if ( ( p.top + $color_box.height() + $color_box.parent().height() ) > ( $(window).height() - 80 ) ) { // set on top
					$color_box.removeClass('color_box_bottom').addClass('color_box_top')
				}
				else if ( ( p.top ) < 100 ) { // set to bottom
					$color_box.removeClass('color_box_top').addClass('color_box_bottom')
				}
				// console.log(( p.top + $color_box.height() + $color_box.parent().height() ))

		});

		$("body").on('click', ".el_color_box", function( event ) {
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
				global: false,
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
					if(color_box.length)
						color_box.hide();
				}
			});


		});

		$('a#confirm_delete').click(function(event) {
				event.preventDefault()
				var data = $(this).data();
				var target = data.target;
	            var url = data.remote;
	            var id = data.target;
				var tis = $(this);

				BootstrapDialog.show({
		            title: 'Delete Project',
		            message: 'Are you sure you want to delete this Project?',
		            type: BootstrapDialog.TYPE_DANGER,
		            draggable: true,
		            buttons: [{
		                    icon: 'fa fa-check',
		                    label: ' Yes',
		                    cssClass: 'btn-success',
		                    autospin: true,
		                    action: function(dialogRef) {
		                        $.when(
		                        	$.ajax({
										url: url,
										global: true,
										data: $.param({
											'action': 'delete' , 'id': id
										}),
										type: 'post',
										dataType: 'json',
										success: function (response) {
											$.show_reward_graphs();
											if(response.success) {
											}else{
											   location.reload();
											}
										}
									})
	                        	)
	                            .then(function(data, textStatus, jqXHR) {
	                                tis.parents('.panel:first').parent().slideUp(400, function(){
										tis.parents('li:first').remove();
									})
	                                dialogRef.enableButtons(false);
	                                dialogRef.setClosable(false);
	                                dialogRef.getModalBody().html('<div class="loader"></div>');
	                                setTimeout(function() {
	                                    dialogRef.close();
	                                }, 500);
	                            })
		                    }
		                },
		                {
		                    label: ' No',
		                    icon: 'fa fa-times',
		                    cssClass: 'btn-danger',
		                    action: function(dialogRef) {
		                        dialogRef.close();
		                    }
		                }
		            ]
		        });
			});

		$('#filter_reset').on('click', function(event) {
			$('#ProjectCategoryId').val('');
			$('#filter_list').trigger('click');
		})
		$('#filter_list').on('click', function(event) {
			event.preventDefault();

			var $cat_list = $('#ProjectCategoryId'),
				cat_id = $cat_list.val(),
				loc = '';

			if( cat_id !== undefined && cat_id > 0 ) {
				loc = '/' + cat_id;
			}
			window.location.href = $js_config.base_url + 'projects/lists' + loc;

		})

	})
	$(window).load( function() {
		setTimeout(function() {
			$(".project_title").find('br').remove()
		}, 500)
	})
</script>
