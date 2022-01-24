<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multi', array('inline' => true));

echo $this->Html->css('projects/dropdown', array('inline' => true));

echo $this->Html->css('projects/project_center', array('inline' => true));
echo $this->Html->script('projects/project_center', array('inline' => true));

echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));


 ?>

<style>
	.pos-absolute {
	    position: absolute;
	    z-index: 999;
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
	.pos-absolute .list-group-item {
		margin-bottom: 0px;
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
	.has-submenu i:before, .has-submenu[aria-expanded="false"] i:before {
	    content: "\e114";
	}
	.has-submenu[aria-expanded="true"] i:before {
	    content: "\e113";
	}

</style>
<script type="text/javascript">

$(function(){

	$.current_delete = {};
	$('body').delegate('.delete-an-item', 'click', function(event) {
		event.preventDefault();
		$.current_delete = $(this);
	});

	$('#modal_delete').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
		$.current_delete = {};
	});

	$(window).on('load', function(){
		$('body').delegate('.list-heading h4', 'click', function(event) {
			if($(this).parent().find('.show_more').length > 0 )
				$(this).parent().find('.show_more').trigger('click')
		})
	})
	$('#modal_small').on('hidden.bs.modal', function(event){
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html("");
	})

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
	/*
	 * End Popover
	 * ******************************** BOOTSTRAP HACK *************************************
	 * */

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
				$(this).find('.modal-content').html('');
			})
    });

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
							url: $js_config.base_url + 'projects/project_center_image/' + project_id,
							global: false,
							success: function (response) {
								$(".project-image-section-col").hide().html(response).fadeIn(500)
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


	$("body").delegate(".remove_center_image", "click", function(event){
		event.preventDefault();
		var project_id = $("#modelFormProjectImage").find("#project_id").val();
		if($(this).is($('.image-upload'))) {
			project_id = $(this).data('id');
		}
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
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id }),
						url: $js_config.base_url + 'projects/project_center_image/' + project_id,
						global: false,
						success: function (response) {
							$(".project-image-section-col").hide().html(response).fadeIn(500)
						},
					});
				}
			}
		});
	})


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
        $("#mainmenu a.list-group-item").not(this).removeClass('selected');
        $(this).addClass('selected');

		var $selOpt = $(this),
			data = $selOpt.data(),
			value = data.value;

		if(value == "" || value === undefined) {
            return;
        }
		var params = { id: data.value, slug: data.slug };

        if (data.slug !== 'projects') {
            // params.permit_id = data.permitid;
        }
		$('.main-menu').trigger('click');
		$('.first-selected').text($(this).text());
		$.show_center_data(params, true);

	});



})
</script>

<div class="row">
    <div class="col-xs-12">
		<?php echo $this->Session->flash(); ?>
		<div class="row">
	       <section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
	       </section>
		</div>
	  <?php echo $this->Session->flash(); ?>
	  <?php

			$projects = $shared_projects = $received_projects = $group_received_projects = $propagated_projects = null;

			if( isset($user_settings) && !empty($user_settings) ) {
				$projects = ( isset($user_settings['projects']) && !empty($user_settings['projects']) ) ? $user_settings['projects'] : null;

				$shared_projects = ( isset($user_settings['shared_projects']) && !empty($user_settings['shared_projects']) ) ? $user_settings['shared_projects'] : null;

				$received_projects = ( isset($user_settings['received_projects']) && !empty($user_settings['received_projects']) ) ? $user_settings['received_projects'] : null;

				$group_received_projects = ( isset($user_settings['group_received_projects']) && !empty($user_settings['group_received_projects']) ) ? $user_settings['group_received_projects'] : null;

				$propagated_projects = ( isset($user_settings['propagated_projects']) && !empty($user_settings['propagated_projects']) ) ? $user_settings['propagated_projects'] : null;
			}


			$my_projects_list = $this->ViewModel->created_projects_UP();

			$shared_projects_list = $this->ViewModel->shared_projects_UP();

			$received_projects_list = $this->ViewModel->received_projects_UP();

			$group_received_projects_list = $this->ViewModel->group_received_projects_list_projectCenter(['Project.id', 'Project.title' ]);

			$propagated_projects_list = $this->ViewModel->propagated_projects_UP();


		?>
     	<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margin-top">
						<div class="box-header filters" style="padding: 5px 10px;">
							<!-- Modal Boxes -->
							<div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog ">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-success fade" id="upload_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
						<!-- /.modal -->
							<div class="col-sm-12 col-md-12 col-lg-12 nopadding">
								<div style="margin-top: 2px;" class="col-sm-12 col-md-4 col-lg-4 nopadding-left project-center-padding">


									<div id="mainmenu" class="">
									    <div class="list-group panel">
									        <a href="#menupos1" class="list-group-item main-menu" data-toggle="collapse" data-parent="#mainmenu"><span class="first-selected">All Projects</span> <span class="menu-ico-collapse"><i class="glyphicon glyphicon-chevron-down"></i></span></a>
									        <div class="collapse pos-absolute" id="menupos1">
									            <a href="#submenu_my" class="list-group-item sub-item <?php if(!empty($my_projects_list)) { ?>has-submenu<?php } ?>"   data-toggle="collapse" data-parent="#submenu_my"><span class="col-menu">Created (<?php echo !empty($my_projects_list)? count($my_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon  "></i></span></a>
								            	<?php if($my_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_my">
												    	<?php
														foreach($my_projects_list as $key => $val ) {

															$prj = $val['projects'];
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_my"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo strip_tags($prj['title']); ?>" data-slug="projects"><?php echo strip_tags($prj['title']); ?></a>
												      	<?php } ?>
										            </div>
												<?php } ?>
												<a href="#submenu_shared" class="list-group-item sub-item <?php if(!empty($shared_projects_list)) { ?>has-submenu<?php } ?>"  data-toggle="collapse" data-parent="#submenu_shared"><span class="col-menu">Shared (<?php echo !empty($shared_projects_list)? count($shared_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon "></i></span></a>
								            	<?php if($shared_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_shared">
												    	<?php
														foreach($shared_projects_list as $key => $val ) {
															$prj = $val['projects'];
															// $prj_permit = (isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) ) ? $val['ProjectPermission'] : null;
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_shared"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo strip_tags($prj['title']); ?>" data-slug="shared_projects"><?php echo strip_tags($prj['title']); ?></a>
												      	<?php } ?>
										            </div>
												<?php } ?>
												<a href="#submenu_received" class="list-group-item sub-item <?php if(!empty($received_projects_list)) { ?>has-submenu<?php } ?>"  data-toggle="collapse" data-parent="#submenu_received"><span class="col-menu">Received (<?php echo !empty($received_projects_list)? count($received_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon "></i></span></a>
								            	<?php if($received_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_received">
												    	<?php
														foreach($received_projects_list as $key => $val ) {
															$prj = $val['projects'];
															// $prj_permit = (isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) ) ? $val['ProjectPermission'] : null;
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_received"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo strip_tags($prj['title']); ?>" data-slug="received_projects"><?php echo strip_tags($prj['title']); ?></a>
												      	<?php } ?>
										            </div>
												<?php } ?>
												<a href="#submenu_grp_received" class="list-group-item sub-item <?php if(!empty($group_received_projects_list)) { ?>has-submenu<?php } ?>" data-toggle="collapse" data-parent="#submenu_grp_received"><span class="col-menu">Group Received (<?php echo !empty($group_received_projects_list)? count($group_received_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon "></i></span></a>
								            	<?php if($group_received_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_grp_received">
												    	<?php
														foreach($group_received_projects_list as $key => $val ) {
															$prj = $val['projects'];
															// $prj_permit = (isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) ) ? $val['ProjectPermission'] : null;
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_grp_received"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo strip_tags($prj['title']); ?>" data-slug="group_received_projects"><?php echo strip_tags($prj['title']); ?></a>
												      	<?php } ?>
										            </div>
												<?php } ?>
												<a href="#submenu_propageted" class="list-group-item sub-item <?php if(!empty($propagated_projects_list)) { ?>has-submenu<?php } ?>" data-toggle="collapse" data-parent="#submenu_propageted"><span class="col-menu">Propagated (<?php echo !empty($propagated_projects_list)? count($propagated_projects_list) : 0; ?>)</span> <span class=" menu-ico-collapse"><i class="glyphicon "></i></span></a>
								            	<?php if($propagated_projects_list) { ?>
										            <div class="collapse list-group-submenu" id="submenu_propageted">
												    	<?php
														foreach($propagated_projects_list as $key => $val ) {
															$prj = $val['projects'];
															//$prj_permit = (isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) ) ? $val['ProjectPermission'] : null;
														?>
															<a href="#" class="list-group-item sub-sub-item" data-parent="#submenu_propageted"  data-value="<?php echo $prj['id']; ?>" data-text="<?php echo strip_tags($prj['title']); ?>" data-slug="propagated_projects"><?php echo strip_tags($prj['title']); ?></a>
												      	<?php } ?>
										            </div>
												<?php } ?>
									        </div>
									    </div>
									</div>
								</div>
							</div>

							<div class="btn-group pull-right center_options" style="" id="center_options"> </div>

						</div>

						<div class="box-body clearfix" style="min-height: 800px">

							<div class="all-sections">

							</div>



							<div class="col-sm-12 partial_data box-borders">
								<div class="overview-box" id="projects">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 project_data">
										<div class="no-data">SELECT PROJECT</div>
									</div>
								</div>

							</div>

						</div><!-- /.box-body -->
					</div><!-- /.box -->
     		    </div>
		   </div>
		</div>
    </div>
</div>
<input type="hidden" id="paging_page" value="0" />
<input type="hidden" id="paging_max_page" value="" />
<style>
    .create_todolink{
        margin-right: 3px;
    }
	.boxes {
			border: 1px solid #ccc;
			margin: 1px solid #ccc;
	}
	.no-more-data {
		display: none;
	}
	.total-tasks {
		float: none !important;
	    border: none !important;
	    margin-top: 0 !important;
	}
</style>

<script type="text/javascript">

    $.loading_data = true;
    $.pageCountUpdate = function(){
        var page = parseInt($('#paging_page').val());
        var max_page = parseInt($('#paging_max_page').val());
        var last_page = Math.ceil(max_page/15);
        if(page < last_page - 1 && $.loading_data){
            $('#paging_page').val(page + 1);
            offset = ( parseInt($('#paging_page').val()) * 15);
            $.getPosts(offset);
        }
    }

    $.getPosts = function(page){
    	$.loading_data = false;
    	var outerPane = $('.paging-wrapper');
        $('#loading').remove();
        var $selOpt = $("#mainmenu a.list-group-item.selected"),
	        sel_data = $selOpt.data(),
	        project = sel_data.value;

        var data = { page: page, project: project }
        var $icon = $('.user-check.selected-user');
        if($icon.length > 0) {
            var user_id = $icon.parent().data('user');
            data.user_id = user_id;
        }

        $.ajax({
            type: "POST",
            url: $js_config.base_url + "dashboards/get_paging_tasks",
            data: data,
            dataType: 'JSON',
            beforeSend: function(){
                // outerPane.append('<div class="loader_bar" id="loading"></div>');
            },
            complete: function(){
                $('#loading').remove();
            },
            success: function(html) {
                $('.no-more-data', outerPane).before(html);
                $.loading_data = true;
            }
         });

    }
</script>