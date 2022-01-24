<?php

echo $this->Html->css('projects/dropdown'); ?>
<?php echo $this->Html->css('projects/studio.min'); ?>
<?php echo $this->Html->script('projects/color.options'); ?>
<?php echo $this->Html->script('projects/studio.min', array('inline' => true));  ?>
<?php echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true)); ?>

<?php echo $this->Html->script('projects/plugins/context-menu/studio.context.min' , array('inline' => true)); ?>

<?php echo $this->Html->script('projects/plugins/ui.touch/jquery.ui.touch-punch.min' , array('inline' => true)); ?>


<style>
	.chars_left {
		font-size: 11px;
	}
    .no-scroll {
        overflow: hidden;
    }

</style>



<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">

				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time" style="padding: 6px 0"><span style="text-transform: none;"><?php echo $page_subheading; ?></span></p>
				</h1>
			</section>
		</div>

		<!-- END HEADING AND MENUS -->

		<span id="project_header_image">
			<?php
				if( isset( $project_id ) && !empty( $project_id ) ) {
					echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_id));
				}
			?>
		</span>

		<!-- MAIN CONTENT -->
		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder">

						<!-- CONTENT HEADING -->
                        <div class="box-header filter" style="">

							<!-- MODAL BOX WINDOW // PASSWORD DELETE-->
							<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							    <div class="modal-dialog">
							        <div class="modal-content"></div>
							    </div>
							</div>
                            <div class="modal modal-success fade " id="create_model"   role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
                                <div class="modal-dialog add-update-modal">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="zoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

							<!-- FILTER BOX -->
							<div class="col-sm-12 col-md-12 col-lg-12 row-first">
								<div class="studios-row-h">
								<div class="col-sm-12 col-md-8 col-lg-7">
									<label>Projects </label>
									<label class="custom-dropdown">

									<select class="aqua" name="project_id" id="projectId">
										<option value="">Select Project</option>
									<?php if(isset($my_projects) && !empty($my_projects)){ ?>
										<?php foreach($my_projects as $key => $val ) { ?>
										<?php if( !empty($key)){
										$v = ( strlen($val) > 65 ) ? substr($val, 0, 65).'...' : $val;
										$selected = '';
										if(isset($project_id) && !empty($project_id)) {
											if($key == $project_id){
												$selected = 'selected="selected"';
											}
										}
										?>
										<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo htmlentities($v); ?></option>
										<?php } ?>
										<?php } ?>
									<?php } ?>
									</select>
									</label>
								</div>
								<div style="margin-top: 5px;" class="col-sm-12 col-md-4 col-lg-5 text-right btns">

									<a class="btn bg-purple btn-sm disabled gen-btn tipText" title="Generate Changes Made"  id="generate_project" data-toggle="modal" data-target="#create_model" data-remote="<?php echo Router::Url(array("controller" => "studios", "action" => "get_selected_templates"), true); ?>" data-total="0"> Generate </a>

									<!-- <a data-user="" id="display-horizontal" class="btn btn-success btn-sm change-display tipText selected" data-type="horizontal" title="Top-Down View"><i class="fa fa-arrows-h"></i></a>
									<a data-user="" id="display-vertical" class="btn btn-success btn-sm change-display tipText" data-type="vertical" title="Left-Right View"><i class="fa fa-arrows-v"></i></a> -->

								</div>
							</div>
								</div>
							<!-- END FILTER BOX -->


                        </div>
						<!-- END CONTENT HEADING -->


                        <div class="box-body studio-wrap" style="min-height: 500px">

							<div class="row horizontal view-wrapper">

								<!-- PROJCET BOX -->
								<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3 project-block">
									<div class="studio studio-default" >
										<div class="studio-heading" >
											<h4 class="studio-title"><i class="pjblack"></i> Project</h4>
											<?php /*  ?><span data-block=".project_wrapper" class="btn btn-xs pull-right tipText to_mission no-selection" data-original-title="Mission Room" data-remote=""><i class="mission-icon" style="margin-right: 0"></i></span><?php  */ ?>
										</div>
										<div class="studio-body">
											<div class="heading">
												Work
											</div>

											<div class="data-wrapper project_wrapper">
											<?php echo $this->element('../Studios/partials/project_box', ['project_id' => $project_id]); ?>
											</div>

										</div>
									</div>
								</div><!-- END PROJCET BOX -->


								<!-- WORKSPACE BOX -->
								<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3 workspace-block">
									<div class="studio studio-default workspace_panel">
										<div class="studio-heading">
											<h4 class="studio-title"><i class="wspblack18"></i> Workspaces</h4>

											<span class="btn btn-success btn-xs pull-right disabled tipText" data-original-title="Add Workspaces"  id="wsp_add" data-toggle="modal" data-target="#create_model" data-id="" data-remote=""><i class="fa fa-plus"></i></span>

											<span class="btn btn-primary btn-xs pull-right expand_compress tipText disabled" title="Collapse All" data-collapse="true" data-block=".workspace_wrapper"><i class="fa fa-compress"></i></span>

											<span class="btn btn-primary btn-xs pull-right expand_compress tipText" title="Expand All" data-collapse="false" data-block=".workspace_wrapper"><i class="fa fa-expand"></i></span>

										</div>
										<div class="studio-body " >
											<div class="heading">
												Work Packages
												<span class=" btn-group pull-right">
												<span class="btn btn-xs btn-control tipText">
												<i class="fa fa-chevron-circle-up   date-sort tipText" title="Ending First" data-type="end" data-block=".workspace_wrapper" style=""></i>
												</span>
												<span class="btn btn-xs btn-control tipText">
												<i class="fa fa-chevron-circle-down   date-sort tipText" title="Starting First" data-type="start" data-block=".workspace_wrapper" style=" "></i>
												</span>
												</span>
											</div>

											<div class="data-wrapper workspace_wrapper">
												<?php echo $this->element('../Studios/partials/workspace_box', ['project_id' => $project_id]); ?>
											</div>

										</div>
									</div>
								</div><!-- END WORKSPACE BOX -->

								<!-- ZONE BOX -->
								<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3 zone-block">
									<div class="studio studio-default zone_panel" >
										<div class="studio-heading" >
											<h4 class="studio-title"><i class="areablack18"></i> Areas</h4>

											<span class="btn btn-success btn-xs pull-right disabled tipText" title="Add Area To Workspace"  id="zone_add" data-toggle="modal" data-target="#create_model" data-id="" data-remote=""><i class="fa fa-plus"></i></span>

											<span class="btn btn-primary btn-xs pull-right expand_compress tipText disabled" title="Collapse All"  data-collapse="true" data-block=".zone_wrapper"><i class="fa fa-compress"></i></span>

											<span class="btn btn-primary btn-xs pull-right expand_compress tipText" title="Expand All"  data-collapse="false" data-block=".zone_wrapper"><i class="fa fa-expand"></i></span>

											<div class="btn-group pull-right ">

												<?php /*  */ ?> <span class=" drag_drop_area tipText icon-blue disabled" id="drag_drop_area" title="Area Move To Different Workspace" > </span>

												<span class="drag_sort_area tipText icon-blue" id="drag_sort_area" title="Sort Areas In Column" style="border-radius: 3px; border-radius: 3px; display: none; "> </span>
											</div>

										</div>
										<div class="studio-body ">
											<div class="heading">
												Activity Groups
											</div>

											<div class="data-wrapper zone_wrapper">
												<?php echo $this->element('../Studios/partials/zone_box', ['project_id' => $project_id]); ?>
											</div>

										</div>
									</div>
								</div><!-- END ZONE BOX -->

								<!-- ELEMENT BOX -->
								<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3 element-block">
									<div class="studio studio-default element_panel" >
										<div class="studio-heading" >
											<h4 class="studio-title"><i class="taskblack18"></i> Tasks</h4>

											<span class="btn btn-success btn-xs pull-right disabled tipText" title="Add Task to Area" id="element_add" data-toggle="modal" data-target="#create_model" data-id="" data-remote=""><i class="fa fa-plus"></i></span>

											<span class="btn btn-primary btn-xs pull-right expand_compress tipText disabled" title="Collapse All"  data-collapse="true" data-block=".element_wrapper"><i class="fa fa-compress"></i></span>

											<span class="btn btn-primary btn-xs pull-right expand_compress tipText" title="Expand All" data-collapse="false" data-block=".element_wrapper"><i class="fa fa-expand"></i></span>

											<div class="btn-group pull-right ">

												<span class="drag_drop_element tipText icon-blue disabled" id="drag_drop_element" title="Task Move To Different Area" > </span>

												<span class="drag_sort_element tipText icon-blue " id="drag_sort_element" title="Sort Tasks In Column" style="border-radius: 3px;  border-radius: 3px; display: none;"> </span>

											</div>

										</div>
										<div class="studio-body " >
											<div class="heading">
												Activities
												<span class=" btn-group pull-right">
												<span class="btn btn-xs btn-control tipText">
												<i class="fa fa-chevron-circle-up  date-sort tipText" title="Ending First" data-type="end" data-block=".element_wrapper" style=""></i>
												</span>
												<span class="btn btn-xs btn-control tipText">
												<i class="fa fa-chevron-circle-down   date-sort tipText" title="Starting First" data-type="start" data-block=".element_wrapper" style=" "></i>
												</span>
												</span>
											</div>

											<div class="data-wrapper element_wrapper">
												<?php echo $this->element('../Studios/partials/element_box', ['project_id' => $project_id]); ?>
											</div>

										</div>
									</div>
								</div><!-- END ELEMENT BOX -->

							</div>
						</div>



                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->

	</div>
</div>
<!-- END OUTER WRAPPER -->
<script type="text/javascript">
	$(function(){
		$('html').addClass('no-scroll');

	    // RESIZE MAIN FRAME
	    ($.adjust_resize = function(){
	        var $scroll_wrapper = $('.box-body');
	        $scroll_wrapper.animate({
	            minHeight: (($(window).height() - $scroll_wrapper.offset().top) ) - 22,
	            maxHeight: (($(window).height() - $scroll_wrapper.offset().top) ) - 22
	        }, 1, ()=>{
	        	var wh = (($(window).height() - $('.data-wrapper').offset().top) - 37);
	        	$('.data-wrapper').css({'minHeight': wh, 'maxHeight': wh});
	        })
	    })();

	    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
	    var interval = setInterval(function() {
	        if (document.readyState === 'complete') {
	            $.adjust_resize();
	            clearInterval(interval);
	        }
	    }, 1);

	    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
	    $(".sidebar-toggle").on('click', function() {
	        // $.adjust_resize();
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1500);
	    })

	    // RESIZE FRAME ON WINDOW RESIZE EVENT
	    $(window).resize(function() {
	        $.adjust_resize();
	    })
	})
</script>

<div class="contextMenuWrapper" >
	<ul id="studioElementContextMenu" class="dropdown-menu" style="display: none;">
		<li><a href="#" name="cut" id="cut" class="el-cut"> <i class="fa fa-cut"></i> Cut </a></li>
		<li><a href="#" name="copy" id="copy" class="el-copy"> <i class="fa fa-copy"></i> Copy </a></li>
	</ul>
</div>
<div class="contextMenuWrapper" >
	<ul id="studioAreaContextMenu" class="dropdown-menu" style="display: none;">
		<li><a href="#" name="cut" id="cut" class="area-cut"> <i class="fa fa-cut"></i> Cut </a></li>
		<li><a href="#" name="copy" id="copy" class="area-copy"> <i class="fa fa-copy"></i> Copy </a></li>
		<li><a href="#" name="paste" id="paste" class="area-paste disabled"> <i class="fa fa-paste"></i> Paste </a></li>
	</ul>
</div>
<div class="contextMenuWrapper" >
	<ul id="studioWorkspaceContextMenu" class="dropdown-menu" style="display: none;">
		<li><a href="#" name="paste" id="paste" class="wsp-paste disabled"> <i class="fa fa-paste"></i> Paste </a></li>
	</ul>
</div>

<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-md">
	     <div class="modal-content" style="width: 600px;"></div>
	</div>
</div>

<small style="display: none;" class="colors_box abs">
	<small style="width:100%;" class="colors color-group">
        <ul class="color-ul">
        	<li class="color-items">
				<a href="#" data-color="panel-color-lightred" data-preview-color="bg-color-lightred" class="squares squares-default squares-xs el_color_box tipText" title="Light Red"><i class="square-color panel-text-lightred"></i></a>
				<a href="#" data-color="panel-color-red" data-preview-color="bg-color-red" class="squares squares-default squares-xs el_color_box tipText" title="Red"><i class="square-color panel-text-red"></i></a>
				<a href="#" data-color="panel-color-maroon" data-preview-color="bg-color-maroon" class="squares squares-default squares-xs el_color_box tipText" title="Maroon"><i class="square-color panel-text-maroon"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightorange" data-preview-color="bg-color-lightorange" class="squares squares-default squares-xs el_color_box tipText" title="Light Orange"><i class="square-color panel-text-lightorange"></i></a>
				<a href="#" data-color="panel-color-orange" data-preview-color="bg-color-orange" class="squares squares-default squares-xs el_color_box tipText" title="Orange"><i class="square-color panel-text-orange"></i></a>
				<a href="#" data-color="panel-color-darkorange" data-preview-color="bg-color-darkorange" class="squares squares-default squares-xs el_color_box tipText" title="Dark Orange"><i class="square-color panel-text-darkorange"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightyellow" data-preview-color="bg-color-lightyellow" class="squares squares-default squares-xs el_color_box tipText" title="Light Yellow"><i class="square-color panel-text-lightyellow"></i></a>
				<a href="#" data-color="panel-color-yellow" data-preview-color="bg-color-yellow" class="squares squares-default squares-xs el_color_box tipText" title="Yellow"><i class="square-color panel-text-yellow"></i></a>
				<a href="#" data-color="panel-color-darkyellow" data-preview-color="bg-color-darkyellow" class="squares squares-default squares-xs el_color_box tipText" title="Dark Yellow"><i class="square-color panel-text-darkyellow"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightgreen" data-preview-color="bg-color-lightgreen" class="squares squares-default squares-xs el_color_box tipText" title="Light Green"><i class="square-color panel-text-lightgreen"></i></a>
				<a href="#" data-color="panel-color-green" data-preview-color="bg-color-green" class="squares squares-default squares-xs el_color_box tipText" title="Green"><i class="square-color panel-text-green"></i></a>
				<a href="#" data-color="panel-color-darkgreen" data-preview-color="bg-color-darkgreen" class="squares squares-default squares-xs el_color_box tipText" title="Dark Green"><i class="square-color panel-text-darkgreen"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightteal" data-preview-color="bg-color-lightteal" class="squares squares-default squares-xs el_color_box tipText" title="Light Teal"><i class="square-color panel-text-lightteal"></i></a>
				<a href="#" data-color="panel-color-teal" data-preview-color="bg-color-teal" class="squares squares-default squares-xs el_color_box tipText" title="Teal"><i class="square-color panel-text-teal"></i></a>
				<a href="#" data-color="panel-color-darkteal" data-preview-color="bg-color-darkteal" class="squares squares-default squares-xs el_color_box tipText" title="Dark Teal"><i class="square-color panel-text-darkteal"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightaqua" data-preview-color="bg-color-lightaqua" class="squares squares-default squares-xs el_color_box tipText" title="Light Aqua"><i class="square-color panel-text-lightaqua"></i></a>
				<a href="#" data-color="panel-color-aqua" data-preview-color="bg-color-aqua" class="squares squares-default squares-xs el_color_box tipText" title="Aqua"><i class="square-color panel-text-aqua"></i></a>
				<a href="#" data-color="panel-color-darkaqua" data-preview-color="bg-color-darkaqua" class="squares squares-default squares-xs el_color_box tipText" title="Dark Aqua"><i class="square-color panel-text-darkaqua"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightblue" data-preview-color="bg-color-lightblue" class="squares squares-default squares-xs el_color_box tipText" title="Light Blue"><i class="square-color panel-text-lightblue"></i></a>
				<a href="#" data-color="panel-color-blue" data-preview-color="bg-color-blue" class="squares squares-default squares-xs el_color_box tipText" title="Blue"><i class="square-color panel-text-blue"></i></a>
				 <a href="#" data-color="panel-color-navy" data-preview-color="bg-color-navy" class="squares squares-default squares-xs el_color_box tipText" title="Navy"><i class="square-color panel-text-navy"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightpurple" data-preview-color="bg-color-lightpurple" class="squares squares-default squares-xs el_color_box tipText" title="Light Purple"><i class="square-color panel-text-lightpurple"></i></a>
				<a href="#" data-color="panel-color-purple" data-preview-color="bg-color-purple" class="squares squares-default squares-xs el_color_box tipText" title="Purple"><i class="square-color panel-text-purple"></i></a>
				<a href="#" data-color="panel-color-darkpurple" data-preview-color="bg-color-darkpurple" class="squares squares-default squares-xs el_color_box tipText" title="Dark Purple"><i class="square-color panel-text-darkpurple"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightmagenta" data-preview-color="bg-color-lightmagenta" class="squares squares-default squares-xs el_color_box tipText" title="Light Magenta"><i class="square-color panel-text-lightmagenta"></i></a>
				<a href="#" data-color="panel-color-magenta" data-preview-color="bg-color-magenta" class="squares squares-default squares-xs el_color_box tipText" title="Magenta"><i class="square-color panel-text-magenta"></i></a>
				<a href="#" data-color="panel-color-darkmagenta" data-preview-color="bg-color-darkmagenta" class="squares squares-default squares-xs el_color_box tipText" title="Dark Magenta"><i class="square-color panel-text-darkmagenta"></i></a>
			</li>
			<li class="color-items">
				<a href="#" data-color="panel-color-lightgray" data-preview-color="bg-color-lightgray" class="squares squares-default squares-xs el_color_box tipText" title="Light Gray"><i class="square-color panel-text-lightgray"></i></a>
				<a href="#" data-color="panel-color-gray" data-preview-color="bg-color-gray" class="squares squares-default squares-xs el_color_box tipText" title="Gray"><i class="square-color panel-text-gray"></i></a>
				<a href="#" data-color="panel-color-darkgray" data-preview-color="bg-color-darkgray" class="squares squares-default squares-xs el_color_box tipText" title="Dark Gray"><i class="square-color panel-text-darkgray"></i></a>
			</li>
		</ul>

	</small>
</small>


