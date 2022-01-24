<?php
echo $this->Html->css('projects/mission_room', array('inline' => true));
echo $this->Html->css('projects/dropdown');

echo $this->Html->script('projects/mission_room', array('inline' => true));

echo $this->Html->script('projects/plugins/slider/jquery.bxslider', array('inline' => true));

echo $this->Html->script( 'projects/plugins/editInPlace', array( 'inline' => true ) );

echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));

?>
<?php echo $this->Html->script( 'projects/plugins/wysihtml5.editor', array( 'inline' => true ) ); ?>

<?php



echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

// echo $this->Html->script('projects/ajax_cache/ajax.cache', array('inline' => true));
?>


<script type="text/javascript">
$(function() {

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
	/* End Popover
	 * ******************************** BOOTSTRAP HACK *************************************
	 * */

	$("#modal_box").on('hidden.bs.modal', function(){
		$(this).removeData('bs.modal');
		$(this).find(".modal-content").html("");
	})

	setTimeout(function(){
		var numericallyOrderedDivs = $('.idea-bucket-inner').sort(function (a, b) {
			return $(a).data('order') > $(b).data('order');
		});
		$("#wsp_buckets").html(numericallyOrderedDivs);
	}, 100)

	// PASSWORD DELETE
	$.current_delete = {};
	$('body').delegate('.delete-an-item', 'click', function(event) {
		event.preventDefault();
		$.current_delete = $(this);
		/*var data = $(this).data(),
			elid = data.elid;

        $('#modal_delete').modal({
            remote: $js_config.base_url + 'entities/delete_an_item/' + elid
        }).modal('show');
        $('#close_options').trigger('click');*/
	});

	$('#modal_delete').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
        $.current_delete = {};
    });

})
</script>
<style type="text/css">
	.popover .popover-title span.close_on_spot {
	    width: auto !important;
	}

	/* cog-setting css */
	.ul-menus {
		display: block;
	    list-style: none;
	    margin: 0;
	    padding: 0;
	    line-height: normal;
	    direction: ltr;
	    -webkit-tap-highlight-color: rgba(0,0,0,0);
	    font-size: 13px !important;
	}
	.ul-menus li {
	    display: block;
	    list-style: none;
	    margin: 0;
	    padding: 0;
	    line-height: normal;
	    direction: ltr;
	    -webkit-tap-highlight-color: rgba(0,0,0,0);
        border-bottom: 1px solid #d9d9d9;
	}
	.ul-menus li:last-child {
	    border-bottom: none;
	}
	.ul-menus li a {
    	background-color: #eeeeee;
    	display: block;
	    padding: 5px 10px;
	    color: #333;
	}
	.ul-menus li a:hover {
	    background: #DDDDDD;
    	color: #222222;
	}
	.ul-menus li a span {
	    display: inline-block;
	    margin-left: 5px;
	}

	.ul-menus .open ul.dropdown-menu>li a {
	    padding: 8px 10px;
	    font-size: 13px;
	}
	.ul-menus .open ul.dropdown-menu>li a i {
	    margin-right: 5px;
	}

	.setting-dropdown { /* dropdown-toggle */
		position: relative;
	}
	.setting-dropdown-menu { /* dropdown-menu */
	    position: absolute;
	    top: 100%;
	    left: 100%;
	    z-index: 1000;
	    display: none;
	    float: left;
	    min-width: 160px;
	    padding: 5px 0;
	    margin: 2px 0 0;
	    font-size: 14px;
	    text-align: left;
	    list-style: none;
	    background-color: #fff;
	    -webkit-background-clip: padding-box;
	    background-clip: padding-box;
	    border: 1px solid #ccc;
	    border: 1px solid rgba(0,0,0,.15);
	    border-radius: 0;
	    -webkit-box-shadow: none;
	    box-shadow: none;
	}
	.setting-dropdown-submenu { /* dropdown-submenu */
	    position: relative;
	}
	.dropdown-menu > li > a { /* .dropdown-menu > li > a */
	    display: block;
	    clear: both;
	    font-weight: 400;
	    line-height: 1.42857143;
	    white-space: nowrap;
	}

	.ul-menus a.dropdown-toggle:hover:after {
	    border-left: 7px solid #000;
	}
	.ul-menus .open a.dropdown-toggle:after {
	    border-left: 7px solid #000;
	}

	.ul-menus a.dropdown-toggle::after {
	    content: '';
	    position: absolute;
	    left: 90%;
	    top: 35%;
	    width: 0;
	    height: 0;
	    border-top: 4px solid transparent;
	    border-bottom: 4px solid transparent;
	    border-left: 7px solid #c00;
	    clear: both;
	}
	.ul-menus a.delete-an-item:hover {
	    color: #c00;
	}
	li.dropdown-header {
	    padding: 5px 20px;
	    background-color: #585858;
	    color: #fff;
	}
	.custom-drop {
		top: -30px ;
		left: 100% ;
		box-shadow: none !important;
    	border-radius: 0 !important;
    	font-size: 13px;
	}
	.custom-drop li.dropdown-submenu > .dropdown-menu {
	    left: 100%;
	    top: -29px;
		box-shadow: none !important;
    	border-radius: 0 !important;
	}
	.paste-menus {
		box-shadow: none !important;
	    border-radius: 0;
	    border-color: #999 !important;
	    min-width: 100px;
	}
	.paste-menus a {
	    background-color: #eeeeee !important;
	    padding: 5px 10px !important;
	    color: #333 !important;
	}
	.paste-menus a:hover {
	    background: #DDDDDD !important;
	    color: #222222 !important;
	}
	#copy_to_list,#move_to_list {
		display: none;
	}
	.btn-paste {
		transition: all 0.5s;
	}
	.fade-out {
		display: none;
		/*transform: scale(1.2);*/
	}
	.fade-in {
		display: inline-block;
		/*transform: scale(1.2);*/
	}
	/* cog-setting css */
	
	.idea-workspace-top{ max-height:84px;}
	.idea-workspace-carousel{ height:43px; overflow:hidden;}
</style>
<?php
	$user_id = $this->Session->read('Auth.User.id');
	$user_setting = mission_settings($user_id);

	$bucket_setting = mission_settings($user_id, null, ['links','notes','documents','mindmaps','feedbacks','decisions','votes']); 

	$prj_disabled = '';
	$prj_tip = '';
	$prj_cursor = '';
	if( isset($project_detail) && isset($project_detail['Project']['sign_off']) && !empty($project_detail['Project']['sign_off']) && $project_detail['Project']['sign_off'] == 1 ){
		$prj_disabled = 'disable';
		$prj_tip = 'Project Is Signed Off';
		$prj_cursor = ' cursor:default !important;';
	}


?>
<!-- // PASSWORD DELETE -->
<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>


<div class="btn-group pull-right"   id="element_option" style="display: none;" >

	<!-- <button  title="Remove Element" data-remote="<?php echo SITEURL.'entities/remove_element'; ?>" id="btn_remove_element" class="btn bg-black btn-sm delete-an-item tipText"><i class="fa fa-trash"></i></button> -->
	<button  title="Remove Element" id="btn_remove_element" class="btn bg-black btn-sm delete-an-item tipText"><i class="fa fa-trash"></i></button>

	<input type="hidden" name="element_id" id="element_id" value="" />

	<button  title="<?php echo tipText('Cut') ?>" id="btn_cut" class="btn bg-black btn-sm btn_cut tipText" style="border-right: 2px solid #fff;"><i class="fa fa-cut"></i></button>

	<button  title="<?php echo tipText('Copy') ?>" id="btn_copy" class="btn bg-black btn-sm tipText btn_copy"><i class="fa fa-copy"></i></button>

	<span class="btn bg-black btn-sm color_box_wrapper" style="border-radius: 0px 3px 4px 0px !important; z-index: 0; border-right: 1px solid #000;" >
		<span class="color_bucket tipText" title="<?php echo tipText('Edit Colors') ?>" ><i class="fa fa-paint-brush"></i></span>
		<div class="el_colors" style="display: none;">
			<div class="colors btn-group" style="width:100%;">
				<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
				<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
				<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
				<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
				<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
				<a href="#" data-color="panel-green" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
				<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
				<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>

				<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
			</div>
		</div>
	</span>
	<button  title="<?php echo tipText('Close Options') ?>" class="btn btn-danger btn-sm tipText" id="close_options"><i class="fa fa-times"></i></button>
</div>


<div class="row mission-room">

	<!-- Modal -->
	<div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
	<div class="modal modal-success fade" id="modal_box"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
	<div class="modal modal-success fade" id="modal_box2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-lgr">
			<div class="modal-content"></div>
		</div>
	</div>

	<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
	<!-- /.modal -->

	<div class="col-xs-12">

		<div class="row">
		   <section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time" style="padding: 6px ;">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
			</section>
		</div>

		<?php echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_id)); ?>

		<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder">
						<div class="box-header" style="background: rgb(239, 239, 239) none repeat scroll 0px 0px; cursor: move; border-bottom: none; border: 1px solid #dddddd;">
							<?php echo $this->element('../Missions/partials/page_header'); ?>
						</div>
						<div class="box-body clearfix main-box-body <?php echo ( isset($user_setting['bg_theme']) && !empty($user_setting['bg_theme']) ) ? $user_setting['bg_theme']['bg_theme'] : 'color_default'; ?>" style="min-height: 800px; background-color: <?php echo ( isset($user_setting['bg_theme']) && !empty($user_setting['bg_theme']) ) ? $user_setting['bg_theme']['bg_color'] : '#000000'; ?> ;">
							<div class="idea-workspace-top">
                            	<div class="col-md-9 col-sm-12" id="list-scroller">
                                    <div class="idea-workspace-carousel">

									<?php

									$project_workspaces = get_project_workspace($project_id);

									if( isset($project_workspaces) && !empty($project_workspaces) ) { ?>

                                    	<ul class="list-inline bxslider" >

										<?php foreach( $project_workspaces as $key => $val ) {

											$workspace = $val['Workspace'];

										?>
                                        	<li class="list-workspace <?php echo $workspace['color_code']; ?> selectable" data-id="<?php echo $workspace['id']; ?>">

												<a href="#" class="trim_text">
													<i class="fa fa-check selected"></i>
													<?php echo htmlentities($workspace['title']); ?>
												</a>

											</li>

										<?php } ?>

                                        </ul>

                                        <div class="idea-work-nav prev"></div>
                                        <div class="idea-work-nav next"></div>

									<?php } ?>

                                    </div>
									<div class="loader_bar hide"></div>
                                </div>
                                <div class="pull-right btns">
                                	<button class="btn btn-default btn-idea tipText" title="Show/Hide Workspace" id="collapse_expand_wsp"><span class="show-close">Close</span> <span class="hidden-md">Workspaces</span></button>

									<div class="btn-group themes">
										<a type="button" class="btn btn-success btn-sm tipText dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Change Page Theme"><i class="fa fa-fw fa-adjust"></i></a>
										<div class="dropdown-menu button-menus">

											<a class="dropdown-item color-theme color_iridium"  data-theme="#3D3C3A" data-cls="color_iridium" href="#">
												<i class="fa fa-check" style="<?php echo ( isset($user_setting['bg_theme']) && !empty($user_setting['bg_theme']) && $user_setting['bg_theme']['bg_theme'] == 'color_iridium') ? 'display: block;' : 'display: none;'; ?>" ></i> Iridium
											</a>

											<a class="dropdown-item color-theme color_battleship_gray"  data-theme="#848482" data-cls="color_battleship_gray" href="#">
												<i class="fa fa-check" style="<?php echo ( isset($user_setting['bg_theme']) && !empty($user_setting['bg_theme']) && $user_setting['bg_theme']['bg_theme'] == 'color_battleship_gray') ? 'display: block;' : 'display: none;';  ?>"></i> Battleship Gray
											</a>

											<a class="dropdown-item color-theme color_black"  data-theme="#000000" data-cls="color_black" href="#">
												<i class="fa fa-check" style="<?php echo ( isset($user_setting['bg_theme']) && !empty($user_setting['bg_theme']) && $user_setting['bg_theme']['bg_theme'] == 'color_black') ? 'display: block;' : 'display: none;';  ?>"></i> Black
											</a>

											<a class="dropdown-item color-theme color_default"  data-theme="#FFFFFF" data-cls="color_default" href="#">
												<i class="fa fa-check" style="<?php echo ( isset($user_setting['bg_theme']) && !empty($user_setting['bg_theme']) && $user_setting['bg_theme']['bg_theme'] == 'color_default') ? 'display: block;' : 'display: none;'; ?>"></i> Default
											</a>

										</div>
									</div>
									
								<?php if( isset($prj_disabled) && !empty($prj_disabled) ) { ?>
										<a class="btn btn-sm btn-success tipText <?php echo $prj_disabled;?>" title="<?php echo $prj_tip;?>" style="<?php echo $prj_cursor;?>">
                                    	<span class="fa fa-plus"></span>
                                    </a>
								<?php } else { ?>
                                    <a href="#" data-target="#modal_box" data-remote="<?php echo Router::Url( array( 'controller' => 'missions', 'action' => 'create_workspace', $project_id, 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" class="btn btn-sm btn-success tipText " title="Add Workspace">
                                    	<span class="fa fa-plus"></span>
                                    </a>
								<?php } ?>
                                </div>
                            </div>

							<!-- Template -->
                            <div class="idea-workspace-middle form-group" id="workspace_template">
								<div class="none-selection" id="">Select a Workspace from above to view its Tasks</div>
                            </div>
							<!-- Template -->

                            <div class="idea-workspace-bottom form-group">
                            	<div class="row">
                                	<div class="col-sm-6 filter_button nopadding-right">
                                        <div class="form-group pull-right">
                                            <button class="btn btn-default btn-sm" id="close_filters">Close Filters</button>
                                        </div>
                                	</div>
                                	<div class="form-group col-sm-6 mission-room-tabs">
										<button class="btn btn-default btn-sm hidden" id="show_filters">Show Filters</button>

                                		<a class="btn btn-sm btn-default" id="add_comment" data-id="">Add Comment</a>

										<ul class="nav nav-tabs comments" style="cursor: move; display: inline-block;">
											<li class="active">
												<a data-toggle="tab" class="active" href="#all" aria-expanded="true">All</a>
											</li>
											<li class="">
												<a data-toggle="tab" href="#people" aria-expanded="false">Team Members</a>
											</li>
										</ul>
                                	</div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6 mission-room-filters nopadding-right">
                                        <div class="list-unstyled list-group filter-list" id="">
                                            <div class="list-group-item" id="zone_filter_wrapper" style="">
                                                <label class="custom-dropdown" style="width: 100%;">
													<select class="aqua" name="filter_zones" id="filter_zones">
														<option value="">Areas</option>
													</select>
												</label>
												<i class="fa fa-times text-red remove-all-tokens tipText" title="Clear Areas Filters"></i>
												<div class="filter-tags"></div>
                                            </div>
                                            <div class="list-group-item" id="task_filter_wrapper">
                                                <label class="custom-dropdown" style="width: 100%;">
													<select class="aqua" name="filter_tasks" id="filter_tasks">
														<option value="">Tasks</option>
													</select>
												</label>
												<i class="fa fa-times text-red remove-all-tokens tipText" title="Clear Tasks Filters"></i>
												<div class="filter-tags"></div>
                                            </div>
                                            <div class="list-group-item" id="people_filter_wrapper">

                                                <label class="custom-dropdown" style="width: 100%;">
													<select class="aqua" name="filter_people" id="filter_people">
														<option value="">Team Members</option>
													</select>
												</label>
												<i class="fa fa-times text-red remove-all-tokens tipText" title="Clear People Filters"></i>
												<div class="filter-tags"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mission-room-tabs">

										<div class="tab-content" id="myTabContent">
											<div id="all" class="tab-pane fade active in">
												<h5 class="no-comments">Select a workspace to view its comments</h5>
											</div>
											<div id="people" class="tab-pane fade">
												<select name="people_select" id="people_select" multiple="multiple"></select>
												<div class="people_comments" id="people_comments">
													<h5 class="no-comments">Select Team Members to view their comments</h5>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
							</div>

							<div class="row" id="activity_button">
								<div class="col-sm-12 form-group">
									<a class="btn btn-sm btn-default" id="bucket_activity" data-id="">Activity View</a>
									<div class="loader_bar hide bucket_activity_bar"></div>
								</div>
							</div>

                            <div class="idea-buckets-resposnive">
                                <div class="idea-buckets" id="wsp_buckets">
									<div class="idea-bucket-inner link_bucket" data-order="<?php if( isset($user_setting['links']['sort_order']) && !empty($user_setting['links']['sort_order']) ) {  echo $user_setting['links']['sort_order']; } ?>" data-slug="links">
										<div class="panel panel-default links" id="links">
											<div class="panel-heading">
												<i class="asset-all-icon linkwhite"></i> Links
											</div>
											<div class="panel-body"><div class="no-data">No Links</div></div>
										</div>
									</div>

									<div class="idea-bucket-inner note_bucket" data-order="<?php if( isset($user_setting['notes']['sort_order']) && !empty($user_setting['notes']['sort_order']) ) {  echo $user_setting['notes']['sort_order']; } ?>" data-slug="notes">
										<div class="panel panel-default notes" id="notes">
											<div class="panel-heading"><i class="asset-all-icon notewhite"></i> Notes</div>
											<div class="panel-body"><div class="no-data">No Notes</div></div>
										</div>
									</div>

									<div class="idea-bucket-inner document_bucket" data-order="<?php if( isset($user_setting['documents']['sort_order']) && !empty($user_setting['documents']['sort_order']) ) {  echo $user_setting['documents']['sort_order']; } ?>" data-slug="documents">
										<div class="panel panel-default documents" id="documents">
										  <div class="panel-heading"><i class="asset-all-icon documentwhite"></i> Documents</div>
										  <div class="panel-body"><div class="no-data">No Documents</div></div>
										</div>
									</div>

									<div class="idea-bucket-inner decision_bucket" data-order="<?php if( isset($user_setting['decisions']['sort_order']) && !empty($user_setting['decisions']['sort_order']) ) {  echo $user_setting['decisions']['sort_order']; } ?>" data-slug="decisions">
										<div class="panel panel-default decisions" id="decisions">
										  <div class="panel-heading"><i class="asset-all-icon decisionwhite"></i> Decisions</div>
										  <div class="panel-body"><div class="no-data">No Decisions</div></div>
										</div>
									</div>

									<div class="idea-bucket-inner feedback_bucket" data-order="<?php if( isset($user_setting['feedbacks']['sort_order']) && !empty($user_setting['feedbacks']['sort_order']) ) {  echo $user_setting['feedbacks']['sort_order']; } ?>" data-slug="feedbacks">
										<div class="panel panel-default feedbacks" id="feedbacks">
											<div class="panel-heading"><i class="asset-all-icon feedbackwhite"></i> Feedback</div>
											<div class="panel-body"><div class="no-data">No Feedback</div></div>
										</div>
									</div>

									<div class="idea-bucket-inner vote_bucket" data-order="<?php if( isset($user_setting['votes']['sort_order']) && !empty($user_setting['votes']['sort_order']) ) {  echo $user_setting['votes']['sort_order']; } ?>" data-slug="votes">
										<div class="panel panel-default votes" id="votes">
											<div class="panel-heading"><i class="asset-all-icon votewhite"></i> Votes</div>
											<div class="panel-body"><div class="no-data">No Votes</div></div>
										</div>
									</div>

									<div class="idea-bucket-inner mindmap_bucket" data-order="<?php if( isset($user_setting['mindmaps']['sort_order']) && !empty($user_setting['mindmaps']['sort_order']) ) {  echo $user_setting['mindmaps']['sort_order']; } ?>" data-slug="mindmaps">
										<div class="panel panel-default mindmaps" id="mindmaps">
											<div class="panel-heading"><i class="asset-all-icon mindmapwhite"></i> Mind Maps</div>
											<div class="panel-body"><div class="no-data">No Mind Maps</div></div>
										</div>
									</div>
                                </div>

								<div class="idea-activities" id="activities" style="display: none;">

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
    	</div>
	</div>
</div>
