<style>
    .lbl {
        margin-bottom: 10px; padding: 0px; text-align: left; font-weight: 600;
    }

    .group_users .table {
        margin: 2px;
        max-width: 100%;
        width: 99.7%;
        border-collapse: separate;
    }
    .group_users .table tr:first-child td {
        background-color: #57a8d7;
        color: #fff;
    }
    .group_users .table tr:first-child td:first-child {
        border-radius: 5px 0 0;
    }
    .group_users .table tr:first-child td:last-child {
        border-radius: 0 5px 0 0;
    }
    .view_group_users > i {
        font-size: 12px;
        margin: 3px 5px 3px -2px;
        color: #c3c3c3;
    }
    .table-rows [class^="col-sm-"], .table-rows [class*=" col-sm-"] {
        margin: 7px 0;
    }
    .view_profile {
        /* background: #ed5b49 none repeat scroll 0 0;
        border: 1px solid transparent;
        border-radius: 3px; */
        color: #fff;
        margin: 0px;
        padding: 0 4px;
        float:left;
    }
    .view_profile:hover, .view_profile:focus {
        /* 	background: #dd4b39 none repeat scroll 0 0;
                border: 1px solid #dd4b39; */
        color: #fff;
    }


	.dates_started, .dates_end {
		display: inline-block;
	}
	.project_info .table {
		margin: 2px;
		max-width: 100%;
		width: 99.7%;
		border-collapse: separate;
	}
	.project_info .table tr:first-child td {
		background-color: #57a8d7;
		color: #fff;
	}
	.project_info .table tr:first-child td:first-child {
		border-radius: 5px 5px 0 0;
	}

	.show_info i.fa {
		margin-right: 5px;
	}
	.show_info i.fa-plus {
		color: #cccccc;
	}
	.show_info i.fa-minus {
		color: #5F9323;
	}


	.panel.group_propagate { border-radius: 0; margin:0; }
	.panel.panel-default h4.panel-title { display:inline-block; width:100%; }
	.add-user-heading .add-user-title {
		background-color: rgb(245, 245, 245);
		border-bottom: 1px solid rgb(221, 221, 221);
		border-bottom-left-radius: 3px;
		border-bottom-right-radius: 3px;
		margin-top: 0;
		padding: 10px 15px;
	}
	@media (max-width:567px) {

	.panel.panel-default h4.panel-title { padding:0 10px; }
	.accordion-group .panel .panel-heading .panel-title a:not(.view_share_map) { width:100%; padding:10px 0; }
	.panel.panel-default .view_share_map, .panel.panel-default.expanded .view_share_map { float:left !important; margin:0; margin-right:5px; margin-bottom:10px; }

	}




	.rec-users {
		border: 1px solid #ccc;
		padding: 10px 15px;
		margin: 10px 0;
	}
	.uinfo {
		display: inline-block;
		margin: 2px;
		padding: 5px;
		text-align: center;
		width: 88px;
	}
	.uinfo .user-img {
		display: block;
		width: 100%;
		border: 1px solid #ccc;
	}
	.uinfo .detail-icons {
		border: 1px solid #ccc;
		display: block;
		margin: -1px 0 0;
		padding: 5px 3px;
		width: 100%;
	}
	.popover ul.project_list {
		list-style: outside none n;
		margin: 0;
	}
	.popover ul.project_list li {
		padding: 2px;
		margin: 0;
	}

	.panel-title .project_title:hover {
		color: inherit !important;
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
		z-index: 9999999;
	}
	.project-image:hover .img-options {
		background: rgba(255, 255, 255, 0.7) none repeat scroll 0 0;
	}




	.pop-content p {
		margin-bottom: 5px;
		font-size: 12px !important;
	}

	.pophover {
	float: left;
	}
	.popover {
	z-index: 999999 !important;
	}
	.popover p {
		margin-bottom: 2px !important;
	}

	.popover p:first-child {
		font-weight: 600 !important;
		width: 170px !important;
	}
	.popover p:nth-child(2) {
		font-size: 11px !important;
	}


	.popover a.group_title {
		color: #333;
		text-decoration: none;
	}
	.popover a.group_title:hover {
	color: #313140;
	}

</style>
<?php
echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->css('projects/bootstrap-input');
// echo $this->Html->script('projects/plugins/select-multiple', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));
?>

<script type="text/javascript">
$(function () {

	$('#popup_model_box,#model_box').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	});


	$('body').delegate('.show_projects', 'click', function(event){
		event.preventDefault();
		var $this = $(this),
			data = $this.data(),
			url = data.remote,
			user = data.user,
			share = data.share,
			$icon = $(this).find('i.fa');
		$('.detail-icons .show_projects i.fa').not($icon[0]).removeClass('fa-level-up').addClass('fa-level-down');

		if( $icon.hasClass('fa-level-down') ) {
			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				data: $.param({ 'user': user, 'share': share }),
				url: url,
				global: true,
				success: function (response) {
					$(".rec-projects").html(response).fadeIn(1000)
				}
			});
			$icon.removeClass('fa-level-down').addClass('fa-level-up');
		}
		else {
			$(".rec-projects").html('');
			$icon.removeClass('fa-level-up').addClass('fa-level-down');
		}


	})

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
			if( runAjax ) {
				runAjax = false;

				// Get newly uploaded project image
				$.ajax({
					type: 'POST',
					dataType: 'JSON',
					data: $.param({ 'project_id': $that.data('id') }),
					url: $js_config.base_url + 'projects/get_project_image/' + $that.data('id'),
					global: false,
					success: function (response) {
						$("#image_file_"+$that.data('id')).hide().html(response).fadeIn(500)
					},
				});
			}
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

	$("body").delegate("#upload_image", "click", function(event) {
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


	$('body').delegate('.sidebar-toggle', 'click', function(){
		if( !$('body').hasClass('sidebar-collapse') ) {
			$.popover_hack();
		}
	})

	$('.pophover-extra').popover({
		trigger: 'hover',
		placement: 'bottom',
		html: true,
		container: 'body',
		delay: {show: 50, hide: 400}
	})

	$.popover_hack = function() {

		$('.pophover-extra').on('shown.bs.popover', function () {
			var data = $(this).data('bs.popover'),
				$tip = data.$tip,
				$arrow = data.$arrow;

			if( !$('body').hasClass('sidebar-collapse') ) {
				$tip.animate({
						left: parseInt($tip.css('left')) + 45 + 'px'
					}, 200, function(){
				})
				// $arrow.css('left', '31%')
				$arrow.css('left', (($(this).outerWidth()/2))+10+'px');

			}

		})
	}

	if( !$('body').hasClass('sidebar-collapse') ) {
		$.popover_hack();
	}

		var bodyHeight = ($('.box-body').height() < 600) ? 600 : $('.box-body').height();

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

})
</script>

<?php echo $this->Session->flash(); ?>

<div class="row">
    <div class="col-xs-12">

        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
                    <p class="text-muted date-time">
                        <span><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
        </div>

        <?php
        $total_sharing = $total_recieved = $total_propagate = $total_shared = $total_grp_rec = 0;
        $total_recieved = $this->requestAction(array('controller' => 'projects', 'action' => 'total_recieved'));

        $total_propagate = $this->requestAction(array('controller' => 'shares', 'action' => 'total_propagate'));

        $total_grp_rec = $this->requestAction(array('controller' => 'groups', 'action' => 'shared_Totprojects'));
        ?>

        <div class="row">
            <section class="content-header clearfix" style="margin:15px 15px 0 15px;  border-top-left-radius: 3px;    background-color: #f5f5f5;     border: 1px solid #ddd;  border-top-right-radius: 3px;" >
				<!-- <span class="text-bold " style="display: inline-block; margin-top: 9px;">Shared By</span> -->

                <div class="box-tools pull-left">
                    <div class="box-tools pull-right" style="padding: 5px 0 10px 0">

                        <a class="btn btn-success btn-sm tipText" title="Direct Share" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'share_lists', 'admin' => FALSE), TRUE); ?>"><i class="fa fa-user"></i> Received (<?php echo $total_recieved; ?>) </a>


                        <a class="btn btn-warning btn-sm tipText selected" style="position: relative;" title="Group Share" href="<?php echo Router::Url(array('controller' => 'groups', 'action' => 'shared_projects', 'admin' => FALSE), TRUE); ?>">
                                <!--<i style="position: absolute; z-index: 1; color: rgb(255, 255, 255); top: 5px; left: 9px;" class="fa fa-group"></i>
                                <i style="position: absolute; z-index: 0; top: 11px; left: 15px;" class="fa fa-group"></i><!-- -->
                            <i class="fa fa-group"></i>
                            <span style="margin: 0px 0px 0px 0px;"> Group (<?php echo $total_grp_rec; ?>)</span>
                        </a>

                    </div>
                </div>

            </section>
        </div>



        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top ">
                        <div class="box-header no-padding" style="">
                            <!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- END MODAL BOX -->
                            <!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
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
                        </div>

                        <div class="box-body clearfix" style="min-height: 800px; transition: all 0.5s ease-in-out 0s;" >


							<div class="clearfix">
								<div class="col-sm-12 text-bold nopadding-left">Shared By: </div>

								<div class="col-sm-12 rec-users">
									<?php
										if(isset($list) && !empty($list)){
											$i=1;
											foreach($list as $project){
												// pr($project);
												if(isset($project['ProjectGroup']) && !empty($project['ProjectGroup']['id'])) {
													$pdata = project_primary_id($project['ProjectGroup']['user_project_id'], 1);
													if( dbIdExists('Project', $pdata['id']) ) {

											?>

													<div class="uinfo">
														<div class="user-img">
															<?php
															$share_by_id = null;
																if(isset($project['ProjectGroupUser']['request_by']) &&  $project['ProjectGroupUser']['request_by'] > 0){
																	$share_by_id = $project['ProjectGroupUser']['request_by'];
																}
																else{
																	$share_by_id = $project['ProjectGroup']['group_owner_id'];
																}
															?>
															<?php
																$userDetail = $this->ViewModel->get_user( $share_by_id, null, 1 );
																$user_image = SITEURL . 'images/placeholders/user/user_1.png';
																$user_name = 'N/A';
																$job_title = 'N/A';
																if(isset($userDetail) && !empty($userDetail)) {
																	$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
																	$profile_pic = $userDetail['UserDetail']['profile_pic'];
																	$job_title = $userDetail['UserDetail']['job_title'];

																	if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
																		$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
																	}
																}
															?>
															<?php
																if( $i == 1 ) {
																	$class = 'pophover-extra';
																}
																else {
																	$class = 'pophovers';
																}
															?>
															<a href="" class="<?php echo $class; ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $share_by_id)); ?>" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p><?php echo $user_name; ?></p> <p><?php echo $job_title; ?></p><?php echo CHATHTML($share_by_id); ?></div>">
																<img src="<?php echo $user_image; ?>">
															</a>
														</div>
														<div class="detail-icons">

															<div class="btn-group">
																<a href="#" class="btn btn-sm btn-default text-maroon" title="Group Users" data-toggle="modal" data-target="#modal_people" data-remote="<?php echo Router::url(['controller' => 'groups', 'action' => 'get_group_users', $project['ProjectGroup']['id'], $pdata['id']]); ?>">
																	<i class="fa fa-users"></i>
																</a>
																<?php
																$group_title = "<a class='group_title' href='".Router::url(array('controller' => 'projects', 'action' => 'index', project_primary_id($project['ProjectGroup']['user_project_id'])))."'>".ucfirst($project['ProjectGroup']['title'])."</a>";
																?>
																<a href="#" class="btn btn-sm btn-default <?php echo $class; ?> show_projects" title="Group Info" data-content="<div style='font-size: 12px;'><?php echo $group_title; ?></div>" data-remote="<?php echo Router::url(['controller' => 'groups', 'action' => 'group_project', $project['ProjectGroup']['id'], $pdata['id'], $project['ProjectGroup']['group_owner_id'] ]); ?>">
																	<i class="fa fa-level-down"></i>
																</a>
															</div>
														</div>
													</div>

												<?php }
												}
												$i++;
											}
										}
										else {
										?>
										<div>No Projects have been shared with you.</div>
									<?php } ?>
								</div>
							</div>

							<div class="clearfix rec-projects">

							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
