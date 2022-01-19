<?php
echo $this->Html->css('projects/list_projects');
echo $this->Html->script('projects/manage_sharing', array('inline' => true));
echo $this->Html->script('projects/propagate_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/colored_tooltip', array('inline' => true)); ?>

<!-- Modal Confirm -->
<div class="modal modal-warning fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> </div>
	</div>
</div>

<!-- /.modal -->
<style>
	.objectivies {
	    border-bottom: 1px solid #ccc;
	    margin: 0 0 10px;
	    padding: 0 0 10px;
	}
	.objectivies h5, .descriptions h5 {
	    margin: 3px 0 4px;
	}
	.text-dark-gray {
	    color: #828282 !important;
	}
	.padding-sm {
	    padding: 5px 10px;
	}

	.grid li .panel .panel-body {
	    padding: 0;
	   overflow: visible;
	}
	.li-listing .panel .panel-body-inner {
	    position: absolute;
	    background: #fff;
	    z-index: 999;
	    display: none;
	    width: 100%;
	    border: 1px solid #ccc;
	    border-radius: 0px 0px 3px 3px;
	    margin-top: -1px;
	}
	ul.grid li .panel-heading .btns {
	    display: inline-block;
	    position: absolute;
	    right: 10px;
	    top: 7px;
	}
</style>
<script type="text/javascript">
jQuery(function($) {

	$('#show_profile_modal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

	$("#ul_list_grid > li > div.box-success").on('click', function(event) {
		var $this = $(this);
		$("#ul_list_grid > li > div.selected").removeClass("selected")
		$this.addClass("selected")
	})
	$('.share_propagation')
			.tooltip({ container: 'body', placement: 'top'})

	$('#permissionModal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

	$('.share_propagation').on('hide.bs.popover', function () {
        // $(this).removeData('bs.popover');
		// $(this).popover('destroy')
		// console.log($(this).popover())
    });

	function resizeStuff() {
		$('.ellipsis').ellipsis();
	}

	var TO = false;
	$(window).on('resize', function(){

		if(TO !== false) {
			clearTimeout(TO);
		}

		TO = setTimeout(resizeStuff, 1000); //200 is time in miliseconds
	});

		var bodyHeight = ($('.box-body').height() < 600) ? 600 : $('.box-body').height();
		$('#ul_list_grid .panel').each(function() {
	        var $projectslistwrap = $(this).find('.panel-body-inner');
	        $(this).data('projectslistwrap', $projectslistwrap)
	        $projectslistwrap.data('title', $(this))
	    })

	    $('body').on('click', function(e) {
	        $('#ul_list_grid .panel').each(function() {
	            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.panel-body-inner').has(e.target).length === 0) {
	                var $projectslistwrap = $(this).data('projectslistwrap')
	                $projectslistwrap.slideUp(300, function(){
	                	$('.box-body').css({ 'minHeight': bodyHeight + 'px'});
	                	// $("html, body").stop().animate({scrollTop: 0}, 500, 'swing', function() { });
	                })
	            }
	        });
	    });

})

	$(window).load(function () {
		setTimeout(function(){
			$('.ellipsis').ellipsis();
		}, 1000)

	})
</script>

<div class="modal fade  modal-success" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
		</div>
  </div>
</div>

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

			<?php if( isset($projects) && !empty($projects)) { ?>

<div id="list_grid_container" >

	<!-- LIST AND GRID VIEW START	-->
	<ul class="grid clearfix" id="ul_list_grid">

		<?php
		$row_counter = 0;
		$project_counter = ( isset($projects) && !empty($projects) ) ? count($projects) : 0;

		foreach( $projects as $key => $val ) {
			$permit_edit = $permit_delete = $permit_propagate  = $full_permit = 0;
			$item = $val['Projects'];
            $upitem = $val['UserProject'];

			$gpp = $this->Group->group_permission_details($project_id, $group_id);
			$p_permission = (isset($gpp['ProjectPermission'])) ? $gpp : null;
			// pr($p_permission);
			// $p_permission = $this->Common->project_permission_details($item['id'], $this->Session->read('Auth.User.id'));



			$share_by_id = $p_permission['ProjectPermission']['share_by_id'];

			/* if( $p_permission['ProjectPermission']['project_level'] == 1) {
				$permit_edit = 1;
				$permit_delete = 1;
				$full_permit = 1;
			}
			else
			{

				if( $p_permission['ProjectPermission']['share_permission'] == 1) {
					$p_propagate = $this->ViewModel->project_propagation( $item['id'], $this->Session->read('Auth.User.id'));

					if( isset($p_propagate) && !empty($p_propagate) ) {
						$permit_propagate = 1;
					}

				}
				$p_permission = $this->Common->project_permission_details($item['id'],$this->Session->read('Auth.User.id'));
				$user_project = $this->Common->userproject($item['id'],$this->Session->read('Auth.User.id'));

				$permit_edit = (isset($p_permission['ProjectPermission']['permit_edit'])) ? $p_permission['ProjectPermission']['permit_edit'] : 0;

				$permit_delete = (isset($p_permission['ProjectPermission']['permit_delete'])) ? $p_permission['ProjectPermission']['permit_delete'] : 0;
			} */

			$project_propagation = $this->ViewModel->project_propagation( $item['id'], $this->Session->read('Auth.User.id') );
			$permit_propagate = ( isset($project_propagation) && !empty($project_propagation)) ? 1 : 0;

			$open_project_link =  SITEURL.'projects/index/'.$item['id']; ?>

		<li class="li-listing">
			<div class="panel <?php echo $item['color_code'] ?>">

				<div class="panel-heading" style="position: relative;">
					<h4 class="panel-title" style="display: inline-block; width: 95%; max-width:100%;">
						<span class="p-list-h tipText" title="<?php echo strip_tags($item['title']); ?>"><?php echo strip_tags($item['title']); ?></span>
						<!-- <a href="<?php echo $open_project_link; ?>" class="project_title" data-full-title="<?php echo strip_tags($item['title']); ?>">
							<span class="ellipsis"><?php echo strip_tags( $item['title'] ); ?> </span>
						</a> -->
						<div class="dates p-list-deta">
							<span><b>Start:</b>
							<?php
								echo ( isset($item['start_date']) && !empty($item['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['start_date'])),$format = 'd M, Y') : 'N/A';
	                        ?></span>
							<span><b>End:</b> <?php

							echo ( isset($item['end_date']) && !empty($item['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['end_date'])),$format = 'd M, Y') : 'N/A';?></span>
						</div>
						<div class="btns" >
							<a class="btn btn-default btn-xs tipText" style="" title="Open Project" href="<?php echo $open_project_link; ?>" >
								<i class="fa fa-folder-open"></i>
							</a>
						</div>
					</h4>
				</div>

				<div class="panel-body">
					<div class="panel-body-inner">
						<div class="project-image" id="image_file_<?php echo $item['id']; ?>">

							<?php
								$project_image = $item['image_file'];
								if( !empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)){
									$project_image = SITEURL . PROJECT_IMAGE_PATH . $project_image;
									// echo $this->Html->image( $project_image, ['class' => 'project_image', 'alt' => 'Project Image' ] );
									echo $this->Image->resize( $item['image_file'], 780, 150, array('style'=>'width: 100%', 'width' => '100%'), 100);
									if( $p_permission['ProjectPermission']['project_level'] == 1) {
									?>
									<div class="img-options" >
										<a class="btn btn-primary btn-xs image-upload project_image_upload" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>" style="right: 70px;">Update</a>

										<a class="btn btn-danger btn-xs image-upload remove_pimage"  data-id="<?php echo $item['id']; ?>">Remove</a>
									</div>
									<?php
									}
								}
								else {
									if( $p_permission['ProjectPermission']['project_level'] == 1) {
								?>
								<div class="upload-text">
									Add a photo here to show off your project.<br />
								<a class="btn btn-primary btn-sm project_image_upload" style="margin-top: 10px;" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>">Upload</a>
								</div>
									<?php
									}
									else {
											?>
									<div class="upload-text">
										No Project Image
									</div>
											<?php
										// echo $this->Image->resize( 'noimage.jpg', 750, 150, array("style" => 'width: 100%'), 100);
										// echo $this->Html->image(  SITEURL . PROJECT_IMAGE_PATH . 'noimage.jpg', ['class' => '', 'alt' => 'Image not available',  'style' => 'margin: 0 auto' ] );
									}
								?>
							<?php } ?>
						</div>
						<div class="projectslistwrap" style="">
							<div class="projectslistinside" style="">
								<div class="main-content" style="">
									<div class="left-content" style="">

										<div class="text-content" style="">

											<h5  class="text-black sub-heading padding-sm" style="">Project Objective</h5>
											<div class="text-detail objective" style="">
												<?php
													echo $item['objective'];
												?>
											</div>

											<h5 style="" class="text-black sub-heading padding-sm">Alignment</h5>
											<div class="text-detail alignement" style="">
												<?php
													$alignement = get_alignment($item['aligned_id']);
													if( !empty($alignement) )
														echo $alignement['title'];
													else
														echo "N/A";
												?>
											</div>

											<h5 style="" class="text-black sub-heading padding-sm">Description</h5>
											<div class="text-detail description" style="">
												<?php
													echo $item['description'];
												?>
											</div>

										</div>

									</div><!-- end left-content -->

									<div class="right-content" style="">
										<div class="text-content" style="">

											<h5 style="" class="text-black sub-heading padding-sm">Tasks (Next 14 days and Overdue)

											</h5>
											<div class="text-detail tasks" data-pid="<?php echo $item['id']; ?>">



												<div class="task" style="">
													<?php
														$elements = $this->ViewModel->ending_elements($item['id']);
														// pr($elements);
														if( isset($elements) && !empty($elements) ) {
														?>
														<ul class="list-unstyled">
															<?php
																foreach($elements as $index => $element_data ) {
																	$element = $element_data['element'];
																	$element_id = $element['id'];
																	$area_id = $element['area_id'];
																	$area_detail = $this->ViewModel->getAreaDetail( $area_id );
																	$ws_id = area_workspace( $area_id );
																	$ws_detail = $this->ViewModel->getWorkspaceDetail( $ws_id );
																?>
																<li>
																	<div class="el-detail">
																		<div class="detail-head">
																			<?php
																				$class_name = 'undefined';
																				$overclass = 'text-black';
																				if( isset( $element['date_constraints'] ) && !empty( $element['date_constraints'] ) && $element['date_constraints'] > 0 ) {
																					if( ((isset( $element['start_date'] ) && !empty( $element['start_date'] )) && date( 'Y-m-d', strtotime( $element['start_date'] ) ) > date( 'Y-m-d' )  )  && $element['sign_off'] != 1 ) {
																						$class_name = 'not_started';
																					}
																					else if( ( (isset( $element['end_date'] ) && !empty( $element['end_date'] )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) < date( 'Y-m-d' ) )  && $element['sign_off'] != 1 ) {
																						$class_name = 'overdue';
																						$overclass ='text-red';
																					}
																					else if( isset( $element['sign_off'] ) && !empty( $element['sign_off'] ) ) {
																						$class_name = 'completed';
																					}
																					else if( (((isset( $element['end_date'] ) && !empty( $element['end_date'] )) && (isset( $element['start_date'] ) && !empty( $element['start_date'] ))) && (date( 'Y-m-d', strtotime( $element['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) >= date( 'Y-m-d' ) )  && $element['sign_off'] != 1 ) {
																						$class_name = 'progressing';
																					}
																				}
																				else {
																					$class_name = 'undefined';
																				}

																			?>
																			<!--<div class="el-box element-<?php echo $class_name ; ?>">
																				<a href="<?php echo SITEURL . 'entities/update_element/'.$element['id'].'#tasks' ?>" class="tipText" title="Open Task"></a>
																			</div>-->
																			<h5><a href="<?php echo SITEURL . 'entities/update_element/'.$element['id'].'#tasks' ?>" class="tipText text-black" title="Open Task"><?php echo $element['title'] ?></a></h5>
																		</div>
																		<div class="detail-body">
																			<p>
																				<span class="text-bold">Key Result Target: </span>
																				<span class=""><?php echo strip_tags($ws_detail['Workspace']['title']); ?></span>
																			</p>
																			<p>
																				<span class="text-bold">Zone: </span>
																				<span class=" "><?php echo $area_detail['title'] ; ?></span>
																			</p>
																			<p>
																				<?php
																					if( isset($element['date_constraints']) && !empty($element['date_constraints']) ) {
																						// if( date( 'Y-m-d', strtotime( $element['end_date'] ) ) < date( 'Y-m-d' )  ) {
																						$daysLeft = daysLeft($element['start_date'], $element['end_date']);
																					?>

																					<span class="text-blakish">End date: </span>
																					<span class="<?php echo $overclass; ?> text-bold" style="margin-right: 10px; "><?php echo _displayDate($element['end_date'], 'd M, Y'); ?></span>

																					<?php
																						// }
																					}
																				?>
																			</p>
																		</div>
																	</div>
																</li>

																<?php
																}
															?>
														</ul>
														<?php
														}
													?>
												</div>

											</div>
										</div>
									</div><!-- end right-content -->
								</div><!-- end main-content -->
							</div><!-- end projectslistinside -->
						</div><!-- end projectslistwrap -->


						<div class="panel-footer clearfix">

							<div class="btn-group grid-btn-group pull-left">
								<?php if ($p_permission['ProjectPermission']['permit_edit']) { ?>
								<a href="#" class="btn btn-default btn-xs color_bucket tipText" title="Color Options" ><i class="fa fa-paint-brush"></i></a>

								<div class="color_box color_box_bottom" style="display:none; width: 72px;">
									<div class="colors btn-group">
										<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
										<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
										<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
										<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
										<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
										<a href="#" data-color="panel-green" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
										<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
										<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
										<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
									</div>
								</div>

								<a class="btn btn-default btn-xs tipText" title="Edit Project Details" href="<?php echo SITEURL.'projects/manage_project/'.$item['id']; ?>" >
									<i class="fa fa-pencil"></i>
								</a>
								<?php } ?>

								<?php if ($p_permission['ProjectPermission']['permit_delete']) { ?>
								<!--<a data-target="<?php echo $item['id']; ?>" id="confirm_delete" data-remote="<?php echo SITEURL.'users/project_delete/'.$item['id'] ?>" type="button" class="btn btn-default btn-xs tipText " title="Remove Project">
									<i class="fa fa-trash"></i>
								</a>-->
								<?php } ?>

								<?php if ($p_permission['ProjectPermission']['permit_read']) { ?>
								<a class="btn btn-default btn-xs tipText" title="Gantt" data-original-title="Gantt" href="<?php echo SITEURL; ?>users/event_gantt/r_project:<?php echo $item['id'] ?>" >
									<i class="fa fa-calendar"></i>
								</a>
								<?php } ?>

								<?php  if( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1) { ?>
								<a class="btn btn-default btn-xs tipText" title="Show Resources" href="<?php echo SITEURL; ?>users/projects/r_project:<?php echo $item['id'] ?>" >
									<i class="fa fa-file"></i>
								</a>
								<?php } ?>

								<?php if( $full_permit ) { ?>
									<a href="<?php echo SITEURL . 'shares/index/' . $item['id'] ?>" title="Project Sharing" class="btn btn-default btn-xs text-bold more tipText"   > <i class="fa fa-fw fa-users"></i></a>
									<?php }
								 else if( $permit_propagate ) { ?>

									<!-- <a href="javascript:;" class="btn btn-default btn-xs text-bold share_propagation tipText" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'save_propagate_sharing', 'admin' => FALSE ), TRUE ); ?>" data-pop-form="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'propagate_sharing', $item['id'], 3, 'admin' => FALSE ), TRUE ); ?>" title="Project Sharing"  > <i class="fa fa-fw fa-users"></i></a> -->

									<a href="javascript:;" class="btn btn-default btn-xs text-bold share_propagations tipText" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'save_propagate_sharing', 'admin' => FALSE ), TRUE ); ?>" data-pop-form="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'propagate_sharing', $item['id'], 3, $share_by_id, 'admin' => FALSE ), TRUE ); ?>" title="Project Propagation"  > <i class="fa fa-fw fa-retweet" style="font-size: 14px"></i></a>
								<?php } ?>


								<?php if( $p_permission['ProjectPermission']['project_level'] == 1) {  ?>
									<a href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'sharing_map', $item['id'], 'admin' => FALSE ), TRUE); ?>" title="Sharing Map" class="btn btn-default btn-xs text-bold more tipText" > <i class="fa fa-fw fa-share"></i></a>
								<?php }   ?>

								<?php //ppr($p_permission);
									/*
		 							if( $full_permit ) {
									if( $this->ViewModel->is_shared($item['id']) ) { ?>
										<a href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'sharing_map', $item['id'], 'admin' => FALSE ), TRUE); ?>" title="Sharing Map" class="btn btn-default btn-xs text-bold more tipText" > <i class="fa fa-fw fa-share"></i></a>
									<?php } }  */?>

							</div>
							<div class="btn-group pull-right">
							<?php  if(project_settings($item['id'], 'is_teamtalk')) { ?>
							<a class="btn btn-default tipText btn-xs  tipText" title="Team Talk" href="<?php echo SITEURL . 'team_talks/index/project:' . $item['id'] ?>"><i class="fa fa-fw fa-microphone">&nbsp;</i> </a>
							<?php } ?>

							<?php  if((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level']==1)) { ?>
								  <a href="<?php echo SITEURL . 'entities/task_list/project:' . $item['id']; ?>" class="btn btn-default btn-xs text-bold more tipText" title="Task List" >
									<i class="fa fa-fw fa-tasks"></i>
								  </a>

								  <a href="<?php echo SITEURL . 'projects/objectives/' . $item['id']; ?>" class="btn btn-default btn-xs text-bold more tipText" title="Dashboard" >
									<i class="fa fa-fw fa-dashboard"></i>
								  </a>
								<?php } ?>

								<?php if ($p_permission['ProjectPermission']['permit_read']) { ?>
								<a href="<?php echo SITEURL . 'projects/reports/' . $item['id'] ?>" title="Project Report" class="btn btn-default tipText btn-xs text-bold more" > <i class="fa fa-fw fa-bar-chart-o"></i></a>
								<?php } ?>
							</div>

						</div>
					</div>

				</div>
			</li>
			<?php

			} ?>
		</ul>
	</div>
					    </div>
                    </div>
				<?php }
				else if( isset($this->params['pass'][0])) {
					echo "Project not found.";
				}
				else {
					echo "Project not found.";
				}
				?>


<style>
.project_title {
    display: inline-block !important;
}
.project_title span.ellipsis {
    width: 100% !important;
    height: 18px;
    display: inline-block;
}
.ico_propagate:after {
	content: "\f005\f005\f005"; /* 3 Stars */
    font-family: FontAwesome;
}
</style>

<?php //echo $this->Html->script('templates/list-grid', array('inline' => true)) ?>
<script type="text/javascript" >
$(function() {

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

	$('#popup_model_box').on('hidden.bs.modal', function () {
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

		var $t            = $(this),
		$w            = $(window),
		viewTop       = $w.scrollTop(),
		viewBottom    = viewTop + $w.height(),
		elTop         = $t.offset().top,
		elVisibility  = (viewBottom - elTop) / $t.height();

		return ( elVisibility >= partial);
	}

	$('.color_bucket').on('click', function (event) {
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
				if(color_box.length)
					color_box.hide();
			}
		});


		/* $('.share_propagation').each(function () {
			//the 'is' for buttons that trigger popups
			//the 'has' for icons within a button that triggers a popup
			if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
				$(this).popover('hide');
			}
		});  */


	});

	$('a#confirm_delete').click(function(event) {
			event.preventDefault()
			var data = $(this).data();
			console.log(data);
			var target = data.target;
            var url = data.remote;
            var id = data.target;
			var tis = $(this);

			$.when( $.confirm({message: 'Are you sure you want to delete this Project?', title: 'Delete Project'}) ).then(
			function() {

				$.ajax({
					url: url,
					data: $.param({
						'action': 'delete' , 'id': id
					}),
					type: 'post',
					//dataType: 'json',
					success: function (response) {
						if(response =='success') {
							// Remove list item related to list after delete workspace
							tis.parent().parent().parent().parent().remove();
							//location.reload();
						}else{
						   location.reload();
						}
					}
				});

			},
			function( ) {
				console.log('Error!!!')
			});
		});


})
$(window).load( function() {
	setTimeout(function() {
		$(".project_title").find('br').remove();
	}, 500)
})
</script>
