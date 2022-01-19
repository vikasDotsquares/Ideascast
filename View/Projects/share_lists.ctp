
<?php //echo $this->Html->css('projects/list-grid') ?>

<?php echo $this->Html->script('projects/plugins/colored_tooltip', array('inline' => true)); ?>
<style>
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
	    background-color: #ebebeb;
	    border-color: transparent  #ddd #ddd;
	    border-image: none;
	    border-style: none solid solid;
	    border-width: medium 1px 1px;
	    cursor: move;
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
		vertical-align: top;
	}
	.uinfo .user-img {
		display: inline-block;
		width: 100%;
		border: 1px solid #ccc;
		vertical-align: top;
	}
	.uinfo .detail-icons {
		border: 1px solid #ccc;
		display: block;
		margin: -1px 0 0;
		padding: 5px 3px;
		width: 100%;
	}
	.popover ul.project_list {
		list-style: decimal;
		margin: 0;
		padding: 1px 15px;
		font-size: 12px;
	}
	.popover ul.project_list li {
		list-style: decimal;
		margin: 0;
	}
	.popover ul.project_list li {
		padding: 2px;
		margin: 0;
	}
	.popover ul.project_list li a {
		color: #333;
		text-decoration: none;
	}
	.popover ul.project_list li a:hover {
		color: #313140;
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

</style>
<!-- Modal Confirm -->
<div class="modal modal-warning fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

		</div>
	</div>
</div>

<!-- /.modal -->
<script type="text/javascript">
jQuery(function($) {


	$("#ul_list_grid > li > div.box-success").on('click', function(event) {
		var $this = $(this);
		$("#ul_list_grid > li > div.selected").removeClass("selected")
		$this.addClass("selected")
	})

	$('body').delegate('.show_projects', 'click', function(event){
			event.preventDefault();
		var $this = $(this),
			data = $this.data(),
			url = data.remote,
			user = data.user,
			share = data.share,
			$icon = $(this).find('i.fa');

		$('.detail-icons i.fa').not($icon[0]).removeClass('fa-level-up').addClass('fa-level-down');

		if( $icon.hasClass('fa-level-down') ) {
			$.when(
			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				data: $.param({ 'user': user, 'share': share }),
				url: url,
				global: true,
				success: function (response) {

				}
			})
			)
			.then(function(response){
				setTimeout(function(){
					$(".rec-projects").html(response).fadeIn(1000)
				})
			})
			$icon.removeClass('fa-level-down').addClass('fa-level-up');
		}
		else {
			$(".rec-projects").html('');
			$icon.removeClass('fa-level-up').addClass('fa-level-down');
		}


		$('.detail-icons .show_projects')
			.not(this)
			.tooltip('destroy')
			.data('original-title', 'Show Projects')
			.attr('title', 'Show Projects');

		var t = ($(this).data('original-title') == 'Hide Projects') ? 'Show Projects' : 'Hide Projects';

		$(this)
			.tooltip('destroy')
			.data('original-title', t)
			.attr('title', t);

		setTimeout(function(){
			$('.detail-icons .show_projects')
				.not(this)
				.tooltip({
					container: 'body',
					placement: 'bottom'
				})
			$this.tooltip({
				container: 'body',
				placement: 'bottom'
			})
			.show()
		}, 200)
	})

	$('body').delegate('.total_shared_link', 'click', function(event){
		event.preventDefault()
	})
	// SHOW TOOLTIP ON EACH COLORBOX ON HOVER
	// SET BOX BACKGROUND COLOR CLASS WITH CONTENT WRAPPER DIV
	/* $('.el_color_box').colored_tooltip();

	$('body').delegate('.panel-body .text-content', 'mouseenter', function(event){
		$(this).css({'overflow-y': 'scroll' })
	})
	$('body').delegate('.panel-body .text-content', 'mouseout', function(event){
			$(this).css({'overflow-y': 'hidden' })
	}) */

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

	$('.show_projects').tooltip({
		container: 'body',
		placement: 'bottom'
	})
	$('.popovers').popover({
        placement : 'bottom',
        trigger : 'hover',
		container: 'body',
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

	$('.pophover-extra,.pophover').popover({
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
				$arrow.css('left', '31.2%')
			}

		})
	}

	if( !$('body').hasClass('sidebar-collapse') ) {
		$.popover_hack();
	}
	/*$('body').delegate('.li-listing .panel-title', 'click', function(event) {
		// event.preventDefault();
		var $this_list_wrap = $(this).parents('.panel').find('.projectslistwrap');
		if($(event.target).hasClass('btn') || $(event.target).hasClass('fa')) return;
		$('.projectslistwrap').not($this_list_wrap).fadeOut('slow');

		$(this).parents('.panel').find('.projectslistwrap').slideToggle('slow', function(){
			var documentHeight = jQuery(document).height();
		    var element = $(this)
		    var distanceFromBottom = documentHeight - (element.offset().top + element.outerHeight(true));
			console.log(element.outerHeight(true))
		    if(distanceFromBottom <= 0) {
		    	// $(this).css('bottom', 0)
		    	$('.box-body').css('minHeight', (600 + (-distanceFromBottom) + 100) + 'px')
		    }
		    else{
		    	$('.box-body').css('minHeight', 600 + 'px')
		    }
		})
	});*/


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

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">Received Projects
					<p class="text-muted date-time">
						<span>View Projects shared by other users</span>
					</p>
				</h1>
			</section>
		</div>

		<div class="row">
			<section class="content-header clearfix" style="margin:5px 15px 0 15px;  border-top-left-radius: 3px; background-color: #f5f5f5;     border: 1px solid #ddd;  border-top-right-radius: 3px;" >

					<?php

					$total_sharing = $total_recieved =  $total_propagate = $total_shared =  $total_grp_rec = 0;
					$total_recieved = $this->requestAction(array('controller' => 'projects', 'action' => 'total_recieved'));

					$total_propagate = $this->requestAction(array('controller' => 'shares', 'action' => 'total_propagate'));

					$total_grp_rec = $this->requestAction(array('controller' => 'groups', 'action' => 'shared_Totprojects'));
					?>

				<div class="box-tools pull-left">
					<div class="box-tools pull-right" style="padding: 5px 0 10px 0">

						<a class="btn btn-warning btn-sm tipText selected" title="Direct Share" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'share_lists', 'admin' => FALSE ), TRUE); ?>"><i class="fa fa-user"></i> Received (<?php echo $total_recieved; ?>) </a>

						<a class="btn btn-success btn-sm tipText" style="position: relative;" title="Group Share" href="<?php echo Router::Url(array('controller' => 'groups', 'action' => 'shared_projects', 'admin' => FALSE ), TRUE); ?>">
							<!--<i style="position: absolute; z-index: 1; color: rgb(255, 255, 255); top: 5px; left: 9px;" class="fa fa-group"></i>
							<i style="position: absolute; z-index: 0; top: 11px; left: 15px;" class="fa fa-group"></i>-->
							<i  class="fa fa-group"></i>
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
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

						<div class="box-body clearfix list-shares " style="transition: all 0.2s ease-in-out 0s; min-height: 800px">
							<div class="clearfix">
								<div class="col-sm-12 text-bold nopadding-left">Shared By: </div>

								<div class="col-sm-12 rec-users">
								<?php
								if(isset($projects) && !empty($projects)){
									$i=1;
									foreach($projects as $project){
										$detail = $project['ProjectPermission'];

									?>

									<div class="uinfo">
										<div class="user-img">
											<?php
											$userDetail = $this->ViewModel->get_user( $detail['share_by_id'], null, 1 );
											$user_image = SITEURL . 'images/placeholders/user/user_1.png';
											$user_name = 'N/A';
											$job_title = 'N/A';
											if(isset($userDetail) && !empty($userDetail)) {
												$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
												$profile_pic = $userDetail['UserDetail']['profile_pic'];
												$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

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
												$class = 'pophover';
											}
											?>
											<a href="" class="<?php echo $class; ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $detail['share_by_id'])); ?>" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p><?php echo $user_name; ?></p> <p><?php echo $job_title; ?></p><?php echo CHATHTML($detail['share_by_id']); ?></div>"  >
											<img src="<?php echo $user_image; ?>" class="">
											</a>
										</div>

										<?php
										$project_titles = '';

										$total_shared = $this->Common->total_shared($detail['user_id'], $detail['share_by_id']);
										if( isset($total_shared) && !empty($total_shared) ) {
											$project_list = $this->Common->shared_projects($detail['user_id'], $detail['share_by_id']);

											if( isset($project_list) && !empty($project_list) ) {
												$project_list = Set::extract($project_list, '/Projects');

												$project_titles .= "<ul class='project_list'>";
												foreach($project_list as $prj) {
													$title = ucfirst($prj['Projects']['title']);
													$project_titles .= "<li><a href='".Router::url(array('controller' => 'projects', 'action' => 'index', $prj['Projects']['id']))."'>".strip_tags($title)."</a></li>";
												}
												$project_titles .= '</ul>';
											}
										}
										?>
										<div class="detail-icons">
										<div class="btn-group">
											<a href="#" class="btn btn-sm btn-default <?php echo $class; ?> total_shared_link" title="Received Projects" data-content="<div><?php echo $project_titles; ?></div>">
												<?php echo $total_shared; ?>
											</a>
											<a href="#" class="btn btn-sm btn-default show_projects " title="Show Projects" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'shared_projects')); ?>" data-user="<?php echo $detail['user_id']; ?>" data-share="<?php echo $detail['share_by_id']; ?>">
												<i class="fa fa-level-down"></i>
											</a>
										</div>
										</div>
									</div>

								<?php $i++;
									}
								}
								else {
								?>
									<div>No Projects have been shared with you.</div>
								<?php }  ?>
								</div>
							</div>

							<div class="clearfix rec-projects">

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$('a[href="#"],a[href=""]').attr('href', 'javascript:;');
	})
</script>