<style>
	section.content{
		padding-top: 0;
	}
	.limited-access .opp-project-left {
		cursor: default;
	}
	.ui-state-highlight {
		border: 1px dashed #dcdcdc;
		display: flex;
		height: 51px;
		background: transparent;
	}
	.no-scroll {
		overflow: hidden;
	}
	.paint-box {
		position: absolute;
		background: none repeat scroll 0 0 #fff;
		width: 126px;
		padding: 5px;
		margin: 0;
		border: 1px solid #ddd;
		border-radius: 5px;
		z-index: 9999;
		right: 0;
		display: none;
	}
	.not-shown {
		display: none !important;
	}
	.paint-box .btn-group .btn{
		border-radius: 0;
	}
	.paint-box .btn-group .btn:nth-child(5){
		margin-left: 0;
	}
	.right-side-progress-bar ul {
		right: 0;
		left: auto;
	}
	.wsp-data-row.opened .wsp-col.wsp-col-7 a {
		display: inline-block;
	}
	.ps-col-header .sort_order {
	    cursor: pointer;
	}
	.ps-data-row .ps-col-7 a {
	    display: none;
	}
	.ps-data-row:hover .ps-col-7 a {
	    display: inline-block;
	}
	.ps-data-row.opened .ps-col.ps-col-7 a {
	    display: inline-block;
	}
	.people-counter li.dark-gray, .people-counter li.light-gray {
		cursor: pointer;
	}
	/*.paint-box.project-colors {
		width: 76px;
	}
	.paint-box.project-colors .colors-boxs-popup li {
		width: 33.33%;
	}*/
	.none-selection {
		pointer-events: none;
		cursor: default;
		opacity: .5;
	}
	.prog-paint-box {
	    position: absolute;
	    background: none repeat scroll 0 0 #fff;
	    width: 282px;
	    padding: 5px;
	    margin: 0;
	    border: 1px solid #ddd;
	    border-radius: 5px;
	    z-index: 9999;
	    right: 0;
	    display: none;
	}
	/*.text-right[data-type="projects"] {
		display: none;
	}*/
</style>
<?php
echo $this->Html->script('projects/my_listing');
echo $this->Html->css('projects/programs');
?>
<script type="text/javascript">
	$('html').addClass('no-scroll');
</script>
<div class="row">
	<div class="col-xs-12">
		<section class="main-heading-wrap pb6">
			<div class="main-heading-sec">
				<h1><?php echo $page_heading; ?></h1>
				<div class="subtitles">
					<span><?php echo $page_subheading; ?></span>
				</div>
			</div>
		</section>
		<div class="box-content postion projects-summary-details">
			<!--<div class="sep-header-fliter" style="">
					<div class="header-link-top-right"></div>
			</div>-->
			<div class="competencies-tab">
				<div class="row">
					<div class="col-md-9">
						<ul class="nav nav-tabs" id="summary_tabs" >
							<li  class="<?php if(!isset($sel_tab) || empty($sel_tab)){ ?> active <?php } ?>" >
								<a data-toggle="tab" data-type="programs" class="active tab_programs " data-target="#tab_programs" href="#tab_programs" aria-expanded="true">Programs</a>
							</li>
							<li class="<?php if(isset($sel_tab) && $sel_tab == 'tab_projects'){ ?> active <?php } ?>" >
								<a data-toggle="tab" data-type="projects" class="tab_projects " data-target="#tab_projects" href="#tab_projects" aria-expanded="false">Projects</a>
							</li>
						</ul>
					</div>
					<div class="col-md-3 right text-right" data-type="programs" <?php if(isset($sel_tab) && $sel_tab == 'tab_projects'){ ?> style="display: none;" <?php } ?>>
						<div class="project-link-top-right">
							<a href="#" data-type="programs"  class="tipText skills-button common-btns analytic-btn" title="Filter Programs" data-target="#prog_filter_model_box" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'filter_programs', 'admin' => FALSE ), TRUE ); ?>"><i class="filter-icon filterblack"></i></a>
							<span class=" hlt-sep">
							<a href="" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'add_program', 'admin' => FALSE ), TRUE ); ?>" data-toggle="modal" data-target="#modal_program" class="tipText" title="Add Program" data-type="programs"> <i class="workspace-icon"></i></a>
							</span>
						</div>
						<div class="input-group search-skills-box">
							<input id="program_search" type="text" class="form-control search-box" autocomplete="off" data-type="programs" placeholder="Search for Programs..." style="display: block;">
							<span class="input-group-btn">
								<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
								<button class="btn clear-btn" type="button" style="display: none;"><i class="clearblackicon search-clear"></i></button>
							</span>
						</div>
					</div>
					<div class="col-md-3 right text-right" data-type="projects"<?php if(!isset($sel_tab) || empty($sel_tab)){ ?> style="display: none;"  <?php } ?>>
						<div class="project-link-top-right">
							<a href="" data-type="projects" data-target="#mid_model_box" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'projects', 'action' => 'filter_projects', 'admin' => FALSE ), TRUE ); ?>" class="tipText skills-button common-btns analytic-btn" title="Filter Projects"><i class="filter-icon filterblack"></i></a>
							<span class="hlt-sep">
							<?php
							$listdomainusers = $this->Common->userDetail($this->Session->read('Auth.User.id'));

							if($listdomainusers['UserDetail']['create_project'] == 1){ ?>
								 <a href="<?php echo Router::url(['controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE ], true); ?>" class="tipText" title="Add Project" data-type="projects"> <i class="workspace-icon"></i></a>
							<?php }else{ ?>
								<a   class="tipText disable" title="Access Denied" data-type="projects"> <i class="workspace-icon"></i></a>
							<?php } ?>





							</span>
						</div>
						<div class="input-group search-skills-box">
							<input id="project_search" type="text" class="form-control search-box" autocomplete="off" data-type="projects" placeholder="Search for Projects..." style="display: block;">
							<span class="input-group-btn">
								<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
								<button class="btn clear-btn" type="button" style="display: none;"><i class="clearblackicon search-clear"></i></button>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="box noborder">
				<div class="box-body clearfix nopadding wsp-task-scroll" style="" id="box_body">
					<div class="tab-content">

						<div class="tab-pane fade <?php if(!isset($sel_tab) || empty($sel_tab)){ ?>active in <?php } ?>" id="tab_programs" data-type="programs">
							<input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        	<input type="hidden" name="paging_total" id="paging_total" value="0">
							<div class="programs-summary_wrap">
								<div class="prog-col-header">
									<div class="prog-col prog-col-1">
										<span class="pg-h-one">Name <span class="total-data">(0)</span>
										<span class="pg-h-one sort_order" title="Sort By Name" data-type="program" data-by="name" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="pg-h-two">Dates</span>
										<span class="pg-h-one sort_order" title="Sort By Start Date" data-type="program" data-by="stdate" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="pg-h-one sort_order" title="Sort By End Date" data-type="program" data-by="endate" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="pg-h-two">Status</span>
										<span class="pg-h-one sort_order active" title="Sort By Schedule Status" data-type="program" data-by="status" data-order="desc">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="pg-h-one sort_order" title="Sort By Confidence Level" data-type="program" data-by="level" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="prog-col prog-col-2">
										<span class="pg-h-two">Team</span>
										<span class="pg-h-one sort_order" title="Sort By Team Count" data-type="program" data-by="team_count" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="pg-h-one sort_order" title="Sort By Total Effort" data-type="program" data-by="total_hours" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="prog-col prog-col-3">
										<span class="pg-h-one ">Projects</span>
										<span class="pg-h-one sort_order" title="Sort By Project Count" data-type="program" data-by="total_projects" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="prog-col prog-col-4">
										<span class="pg-h-one ">Workspaces</span>
										<span class="pg-h-one sort_order" title="Sort By Workspace Count" data-type="program" data-by="total_workspaces" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="pg-h-two ">Tasks</span>
										<span class="pg-h-one sort_order" title="Sort By Task Count" data-type="program" data-by="total_tasks" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="prog-col prog-col-5">
										Costs
									</div>
									<div class="prog-col prog-col-6">
										<span class="pg-h-two ">Risks</span>
										<span class="pg-h-one sort_order" title="Sort By Risk Count" data-type="program" data-by="risk_total" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="prog-col prog-col-7">
										Actions
									</div>
								</div>
								<div class="prog-summary-data" data-flag="true" style="min-height: 600px;">
									<?php //echo $this->element('../Projects/programs/programs_rows'); ?>
								</div>
							</div>
						</div>

						<div class="tab-pane fade <?php if(isset($sel_tab) && $sel_tab == 'tab_projects'){ ?>active in <?php } ?>" id="tab_projects" data-type="projects">
							<input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        	<input type="hidden" name="paging_total" id="paging_total" value="0">
							<div class="project-summary-wrap">
								<div class="ps-col-header">
									<div class="ps-col ps-col-1">
										<span class="ps-h-one sort_order" data-type="projects" data-by="ptitle" data-order="">Name <span class="total-data">(0)</span>
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="ps-h-two sort_order" data-type="projects" data-by="psdate" data-order="">
											Start
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="ps-h-two sort_order" data-type="projects" data-by="pedate" data-order="">
											End
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="ps-h-two sort_order active" data-type="projects" data-by="prj_status" data-order="desc">
											Status
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="ps-h-two sort_order tipText" title="Sort By Confidence Level" data-type="projects" data-by="confidence_level" data-order="desc">

											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="ps-col ps-col-2">
										RAG
										<span class="com-short sort_order tipText" title="Sort By Status" data-type="projects" data-by="p_rag" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Annotation Count" data-type="projects" data-by="comments_count" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="ps-col ps-col-3"> Team
										<span class="com-short sort_order tipText" title="Sort By Owner Count" data-type="projects" data-by="owner_count" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Sharer Count" data-type="projects" data-by="sharer_count" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Opportunity Request Count" data-type="projects" data-by="pb_count" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Role" data-type="projects" data-by="prj_role" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="ps-col ps-col-4">
										Workspaces
										<span class="com-short sort_order tipText" title="Sort By Not Set Count" data-type="projects" data-by="wnon" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Not Started Count" data-type="projects" data-by="wpnd" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By In Progress Count" data-type="projects" data-by="wprg" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Overdue Count" data-type="projects" data-by="wovd" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Completed Count" data-type="projects" data-by="wcmp" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Total Workspaces" data-type="projects" data-by="total_workspaces" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="ps-h-two"> Tasks</span>
										<span class="sort_order tipText" title="Sort By Not Set Count" data-type="projects" data-by="enon" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Not Started Count" data-type="projects" data-by="epnd" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By In Progress Count" data-type="projects" data-by="eprg" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Overdue Count" data-type="projects" data-by="eovd" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Completed Count" data-type="projects" data-by="ecmp" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Total Tasks" data-type="projects" data-by="total_tasks" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="ps-col ps-col-8">
									    Effort
                                        <span class="sort_order tipText" title="Sort By Completed Hours" data-by="completed_hours" data-order="desc" data-type="teams">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="sort_order tipText" title="Sort By Remaining Hours" data-by="remaining_hours" data-order="desc" data-type="teams">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="sort_order tipText" title="Sort By Change" data-by="change_hours" data-order="desc" data-type="teams">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="sort_order tipText" title="Sort By Total Hours" data-by="total_hours" data-order="desc" data-type="teams">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
									</div>


									<div class="ps-col ps-col-5">
										Costs
										<span class="com-short sort_order tipText" title="Sort By Budget" data-type="projects" data-by="escost" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Actual" data-type="projects" data-by="spcost" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Status" data-type="projects" data-by="c_status" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="ps-col ps-col-6"> Risks
										<span class="com-short sort_order tipText" title="Sort By High Pending Risks" data-type="projects" data-by="high_risk_total" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Severe Pending Risks" data-type="projects" data-by="severe_risk_total" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
										<span class="com-short sort_order tipText" title="Sort By Total Risks Count" data-type="projects" data-by="risk_count" data-order="">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="ps-col ps-col-7"> Actions </div>
								</div>
								<div class="project-summary-data list-wrapper" data-flag="true" style="min-height: 600px;">
									<?php //echo $this->element('../Projects/sections/listing_rows'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
	</div>
</div>
<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="model_bx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="mid_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog sm-modal-box">
        <div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="prog_filter_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog sm-modal-box">
        <div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="modal_program" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog add-program-popup">
        <div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade " id="modal_view_program" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg  open-program-popup">
        <div class="modal-content"></div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		// $.open_annotate = true;

	    /* TABS PAGINATION */
	    $('.list-wrapper').scroll(function() {
	        $('.tooltip').hide()
	        var $this = $(this);
	        var $parent = $this.parents('.tab-pane:first');
	        clearTimeout($.data(this, 'scrollTimer'));
	        $.data(this, 'scrollTimer', setTimeout(function() {
	            if($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight)  {
					$.updateOffset($this, $parent);
	            }
	        }, 250));
	    });

	    $.countRows = function(type, parent, searchfilter = 0) {
	        var dfd = $.Deferred();

	        var data_filtered = false;

	        var order = 'asc',
	        	coloumn = 'prj_status';
	        if( $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').length > 0 ) {
	            order = $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').data('order'),
	            coloumn = $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').data('coloumn');

	            if( order == 'asc' ){
	                order = 'desc';
	            } else {
	                order = 'asc';
	            }
	        }
	        $('.list-wrapper', parent).data('flag', true);

	        var search_text = $('#project_search[data-type="'+type+'"]').val();
	        if(search_text != ''){
	            $('.list-wrapper', parent).scrollTop(0)
	        }

	        var data = {order: order, type: type, coloumn: coloumn, search_text: search_text}
			if($.selected_filters && $.selected_filters.programs.length > 0){
				data.programs = $.selected_filters.programs;
				data_filtered = true;
			}
			if($.selected_filters && $.selected_filters.projects.length > 0){
				data.projects = $.selected_filters.projects;
				data_filtered = true;
			}
			if($.selected_filters && $.selected_filters.status.length > 0){
				data.status = $.selected_filters.status;
				data_filtered = true;
			}
			if($.selected_filters && $.selected_filters.rag.length > 0){
				data.rag = $.selected_filters.rag;
				data_filtered = true;
			}
			if($.selected_filters && $.selected_filters.roles.length > 0){
				data.roles = $.selected_filters.roles;
				data_filtered = true;
			}
			if($.selected_filters && $.selected_filters.task_types.length > 0){
				data.task_types = $.selected_filters.task_types;
				data_filtered = true;
			}
			if($.selected_filters && $.selected_filters.members.length > 0){
				data.members = $.selected_filters.members;
				data_filtered = true;
			}

	        $.ajax({
	            url: $.module_url + 'tab_paging_count',
	            data: data,
	            type: 'post',
	            dataType: 'JSON',
	            success: function(response) {
	                $('#paging_offset', parent).val(0);
	                $('#paging_total', parent).val(response);
	                $('.total-data', parent).text('('+response+')');

					if( searchfilter == 0 ){
						if(response <= 0){
							$('.list-wrapper', parent).html('<div class="no-summary-found">No Projects</div>');
	                		$('.ps-col-header', parent).addClass('none-selection');
	                		if(!data_filtered){
	                			$('.analytic-btn[data-type="projects"]').addClass('none-selection');
	                		}
						}
						else{
							$('.ps-col-header', parent).removeClass('none-selection');
							$('.analytic-btn[data-type="projects"]').removeClass('none-selection');
						}
					}
	                dfd.resolve('paging count');
	            }
	        })
	        return dfd.promise();
	    }
	    $.countRows('projects', $('#tab_projects'));

	    $.tab_paging_offset = $js_config.listing_offset;
	    $.updateOffset = function(wrapper, parent){
	        var page = parseInt($('#paging_offset', parent).val());
	        var max_page = parseInt($('#paging_total', parent).val());
	        var last_page = Math.ceil(max_page/$.tab_paging_offset);

	        if(page < last_page - 1 && wrapper.data('flag')){
	            $('#paging_offset', parent).val(page + 1);
	            offset = ( parseInt($('#paging_offset', parent).val()) * $.tab_paging_offset);
	            $.getPagingData(offset, wrapper, parent);
	        }
	    }

	    $.getPagingData = function(page, wrapper, parent){
	        wrapper.data('flag', false);
	        var $wrapper = wrapper;
			var order = 'asc',
				coloumn = 'prj_status';
			if( $('.sort_order.active', parent).length > 0 ) {
				order = $('.sort_order.active', parent).data('order'),
				coloumn = $('.sort_order.active', parent).data('by');
				if( order == 'asc' ){
	                order = 'desc';
	            } else {
	                order = 'asc';
	            }
			}

	        var type = parent.data('type');
			var search_text = $('#project_search[data-type="'+type+'"]').val();

	        var data = {page: page, order: order, type: type, coloumn: coloumn, search_text: search_text}
			if($.selected_filters && $.selected_filters.programs.length > 0){
				data.programs = $.selected_filters.programs
			}
			if($.selected_filters && $.selected_filters.projects.length > 0){
				data.projects = $.selected_filters.projects
			}
			if($.selected_filters && $.selected_filters.status.length > 0){
				data.status = $.selected_filters.status
			}
			if($.selected_filters && $.selected_filters.rag.length > 0){
				data.rag = $.selected_filters.rag
			}
			if($.selected_filters && $.selected_filters.roles.length > 0){
				data.roles = $.selected_filters.roles
			}
			if($.selected_filters && $.selected_filters.task_types.length > 0){
				data.task_types = $.selected_filters.task_types
			}
			if($.selected_filters && $.selected_filters.members.length > 0){
				data.members = $.selected_filters.members
			}

	        $.ajax({
	            type: "POST",
	            url: $.module_url + "filter_list",
	            data: data,
	            success: function(html) {
	                $wrapper.append(html);
	                wrapper.data('flag', true);
	            }
	         });
	    }
	    /* TABS PAGINATION */
    })

	$(()=>{
	    /******* PROGRAMS ******/
	    $.program_filters = { roles: [], status: [], types: [] };
	    $.program_filtered = false;

		$('#prog_filter_model_box').on('hidden.bs.modal', () => {
			$(this).find('.modal-content').html("");
			$(this).removeData('bs.modal');
			if($.program_filtered) {
				$('.analytic-btn[data-type="programs"] .filter-icon').addClass('filterblue').removeClass('filterblack');
				var data = $.program_filters;

				$.program_filtered = false;
				$.show_filtered_programs(data);
			}
		})

		$.tabProgram = $('#tab_programs');
		$.filterProgram = $('.right.text-right[data-type="programs"]');
		// SORTING
		$('body').on('click', '#tab_programs .sort_order', function(event) {
			var $that = $(this),
				$parent = $.tabProgram,
				order = $that.data('order') || 'asc',
				coloumn = $that.data('by');

			if( order == 'desc' ){
				$(this).attr('data-order', 'asc');
			}
			else{
				$(this).attr('data-order', 'desc');
			}


			$parent.find('.sort_order.active').not(this).removeClass('active');

			$that.addClass('active');
			$('.tooltip').remove();

			var search = $('#program_search').val();
			var data = {order: order, coloumn: coloumn, search: search}
	        if($.program_filters && $.program_filters.roles.length > 0){
				data.roles = $.program_filters.roles;
			}
			if($.program_filters && $.program_filters.status.length > 0){
				data.status = $.program_filters.status;
			}
			if($.program_filters && $.program_filters.types.length > 0){
				data.types = $.program_filters.types;
			}

			$.ajax({
				url: $js_config.base_url + 'projects/programs_list',
				type: 'POST',
				data: data,
				success: function(response){

					$parent.find("#paging_offset").val(0);

					if( order == 'asc' ){
						$that.data('order', 'desc');
					} else {
						$that.data('order', 'asc');
					}
					$parent.find('.prog-summary-data').scrollTop(0);
					$parent.find('.prog-summary-data').html(response);
					$('.tooltip').remove();

				}
			})
		})

		// TABS PAGINATION
	    $('.prog-summary-data').scroll(function() {
	        $('.tooltip').hide()
	        var $this = $(this);
	        var $parent = $.tabProgram;
	        clearTimeout($.data(this, 'scrollTimer'));

	        $.data(this, 'scrollTimer', setTimeout(function() {
	            if($this.scrollTop() + $this.innerHeight()+15 >= $this[0].scrollHeight)  {
					$.updateProgramOffset($this, $parent);
	            }
	        }, 250));
	    });

	    $.countProgramSize = function(parent) {
	        var dfd = $.Deferred();

	        var order = 'asc',
	        	coloumn = 'status';
	        if( $('.sort_order.active', parent).length > 0 ) {
	            order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
	            coloumn = $('.sort_order.active', parent).data('by');

	            if( order == 'asc' ){
	                order = 'desc';
	            } else {
	                order = 'asc';
	            }
	        }

	        var search = $('#program_search').val();
	        var data = {order: order, coloumn: coloumn, search: search}
	        var data_filtered = false;
	        if($.program_filters && $.program_filters.roles.length > 0){
				data.roles = $.program_filters.roles;
				data_filtered = true;
			}
			if($.program_filters && $.program_filters.status.length > 0){
				data.status = $.program_filters.status;
				data_filtered = true;
			}
			if($.program_filters && $.program_filters.types.length > 0){
				data.types = $.program_filters.types;
				data_filtered = true;
			}

	        $.ajax({
	            url: $js_config.base_url + 'projects/programs_count',
	            data: data,
	            type: 'post',
	            dataType: 'JSON',
	            success: function(response) {
	                $('#paging_offset', parent).val(0);
	                $('#paging_total', parent).val(response);
	                $('.total-data', parent).html('('+response+')');
					if(response <= 0){
	            		$('.prog-col-header', parent).addClass('none-selection');
	            		if(!data_filtered){
                			$('.analytic-btn[data-type="programs"]').addClass('none-selection');
                		}
					}
					else{
						$('.prog-col-header', parent).removeClass('none-selection');
						$('.analytic-btn[data-type="programs"]').removeClass('none-selection');
					}
	                dfd.resolve('paging count');
	            }
	        })
	        return dfd.promise();
	    }
	    $.countProgramSize($.tabProgram);

	    $.program_offset = $js_config.program_offset;
	    $.updateProgramOffset = function(wrapper, parent){
	        var page = parseInt($('#paging_offset', parent).val());
	        var max_page = parseInt($('#paging_total', parent).val());
	        var last_page = Math.ceil(max_page/$.program_offset);

	        if(page < last_page - 1 && wrapper.data('flag')){
	            $('#paging_offset', parent).val(page + 1);
	            offset = ( parseInt($('#paging_offset', parent).val()) * $.program_offset);
	            $.getProgramPages(offset, wrapper, parent);
	        }
	    }

	    $.getProgramPages = function(page, wrapper, parent){
	        wrapper.data('flag', false);
	        var $wrapper = wrapper;
			//added by me ******************
			var order = 'asc',
				coloumn = 'status';
			if( $('.sort_order.active', parent).length > 0 ) {
				order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
				coloumn = $('.sort_order.active', parent).data('by');
			}

			var search = $('#program_search').val();
	        var data = {page: page, order: order, coloumn: coloumn, search: search}
	        if($.program_filters && $.program_filters.roles.length > 0){
				data.roles = $.program_filters.roles;
				data_filtered = true;
			}
			if($.program_filters && $.program_filters.status.length > 0){
				data.status = $.program_filters.status;
				data_filtered = true;
			}
			if($.program_filters && $.program_filters.types.length > 0){
				data.types = $.program_filters.types;
				data_filtered = true;
			}

	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'projects/programs_list',
	            data: data,
	            success: function(html) {
	                $wrapper.append(html);
	                wrapper.data('flag', true);
	            }
	         });
	    }

	    ;($.refreshPrograms = function(page, wrapper, parent){
	        wrapper.data('flag', false);
	        var $wrapper = wrapper;
			//added by me ******************
			var order = 'asc',
				coloumn = 'name';
			if( $('.sort_order.active', parent).length > 0 ) {
				order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
				coloumn = $('.sort_order.active', parent).data('by');
			}

			var search = $('#program_search').val();
	        var data = {page: 0, order: order, coloumn: coloumn, search: search}

	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'projects/programs_list',
	            data: data,
	            success: function(html) {
	                $wrapper.html(html);
	                wrapper.data('flag', true);
	            }
	         });
	    })(0, $('.prog-summary-data'), $.tabProgram);

	    $.refreshProgramCount = function(){
	        $.ajax({
	            type: "POST",
	            dataType: "JSON",
	            url: $js_config.base_url + 'projects/program_counter',
	            data: {},
	            success: function(response) {
	                $('.menu-my-programs .lhs-counters').html(response);
	            }
	         });
	    }

		$.show_filtered_programs = (data) => {
			var dfd = $.Deferred();
			var type = 'programs',
				order = $("#tab_"+type).find('.sort_order.active').data('order'),
				coloumn = $("#tab_"+type).find('.sort_order.active').data('by'),
				search_text = '';
			if( order == 'asc' ){
				order = 'desc';
			} else {
				order = 'asc';
			}
			if( $('#program_search').val() != '' ){
				search_text = $('#program_search[data-type="'+type+'"]').val();
			}
			data.order = order;
			data.coloumn = coloumn;
			data.search = search_text;
			// console.log(data);return;
			$.ajax({
				url: $.module_url + 'programs_list',
				type: 'POST',
				data: data,
				success: function(response){
					$("#tab_"+type).find("#paging_offset").val(0);
					$.countProgramSize($.tabProgram);
					$('.prog-summary-data').html(response);
					$('.tooltip').remove();
					dfd.resolve('paging count');
				}
			})
			return dfd.promise();
		}

	    $('.sort_order').tooltip({
	        placement: 'top',
	        container: 'body'
	    })

	    var typingTimer;                //timer identifier
		var doneTypingInterval = 300;  //time in ms
		var $input = $('#program_search');

		$('body').on('keyup', '#program_search', function(event) {
			event.preventDefault();
			var search_text = $(this).val(),
				type = $(this).data('type');

			var $thisParent = $(this).parent();

			$("#tab_"+type).find("#paging_offset").val(0);

			if( search_text.length == 0 ){
				$(".clear-btn", $thisParent).hide();
				$(".search-btn", $thisParent).show();
			}
			clearTimeout(typingTimer);

	  		//user is "finished typing," do something
	  		typingTimer = setTimeout($.proxy(function(){

				var order = 'asc',
					coloumn = 'status';

				if( $("#tab_"+type).find('.ps-col-header .sort_order.active') ) {

					order = $("#tab_"+type).find('.ps-col-header .sort_order.active').data('order'),
					coloumn = $("#tab_"+type).find('.ps-col-header .sort_order.active').data('by');
					if( order == 'asc' ){
						order = 'desc';
					} else {
						order = 'asc';
					}
				}
				$('.tooltip').remove();

	        	var data = {page: 0, order: order, coloumn: coloumn, search: search_text}
		        if($.program_filters && $.program_filters.roles.length > 0){
					data.roles = $.program_filters.roles;
					data_filtered = true;
				}
				if($.program_filters && $.program_filters.status.length > 0){
					data.status = $.program_filters.status;
					data_filtered = true;
				}
				if($.program_filters && $.program_filters.types.length > 0){
					data.types = $.program_filters.types;
					data_filtered = true;
				}

				$.ajax({
					url: $.module_url + 'programs_list',
					type: 'POST',
					data: data,
					success: function(response){
						$('.prog-summary-data').html(response);
						$.countProgramSize($.tabProgram);
					}
				})

	  		},this), doneTypingInterval);

		});
		//on keydown, clear the countdown
		$('body').on('keydown', '#program_search', function(event) {
		  	clearTimeout(typingTimer);
		  	var $thisParent = $(this).parent();
			$(".clear-btn", $thisParent).show();
			$(".search-btn", $thisParent).hide();
		});

		$('body').on('click', '.right.text-right[data-type="programs"] .clear-btn',function(event){
			event.preventDefault();

			var $thisParent = $(this).parent();

			$(this).hide();
			$(".search-btn", $thisParent).show();

			var type = $("#summary_tabs li.active a").data('type');
			$('#program_search').val('').trigger('keyup');
			$("#tab_"+type).find("#paging_offset").val(0);

			window.history.pushState('My Work', 'Title', $.module_url + 'lists');

		});

	    // CREATE AND ADD EVENTS TO PAINT BOX
	    $.create_painter = function(){
	    	dfd = new $.Deferred();

	    	if($('.prog-paint-box').length > 0){
	    		$('.prog-paint-box').remove();
	    	}

	    	var $inner_box = $('<ul />', {
		   		'class' : 'project-color-box'
		   	})

	    	var colors = [
		        'lightred', 'lightorange', 'lightyellow', 'lightgreen', 'lightteal', 'lightaqua', 'lightblue', 'lightpurple', 'lightmagenta', 'lightgray',
		        'red', 'orange', 'yellow', 'green', 'teal', 'aqua', 'blue', 'purple', 'magenta', 'gray',
		        'maroon', 'darkorange', 'darkyellow', 'darkgreen', 'darkteal', 'darkaqua', 'navy', 'darkpurple', 'darkmagenta', 'darkgray'
	        ];
	    	for (var i = 0; i < colors.length; i++) {
	    		var val = colors[i];
	    		var str = val.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				    return letter.toUpperCase();
				});
	    		var $squares = $('<li />', {
			   		'class' : 'color-square tipText',
			   		'title': str
			   	})
			   	.data('color', val)
			   	.html('<i class="square-color prog-text-'+val+'"></i>')
			   	.appendTo($inner_box)
			   	.on('click', function(event) {
			   		event.preventDefault();
			   		var $box = $(this).parents('.prog-paint-box'),
			   			box_data = $box.data(),
			   			this_data = $(this).data(),
			   			$brush = box_data.brush,
			   			brush_data = $brush.data(),
			   			color_code = this_data.color,
			   			program_id = $brush.parents('.pg-data-row').data('prog');

		   			var $handle = $brush.parents('.pg-data-row').find('.prog-project-left');
		   			var cls = $handle.attr('class');
			        var foundClass = (cls.match(/(^|\s)programs-\S+/g) || []).join('')
			        if (foundClass != '') {
			            $handle.removeClass(foundClass);
			        }
			        $handle.addClass('programs-' + color_code);

			        var box = $brush.data('box');
	                /*if(box) {
		                box.slideUp(100, function(){
		                	$(this).remove();
		                	$brush.parents('.pg-data-row').removeClass('opened');
		                })
		            }*/

		   			$.ajax({
			            type: 'POST',
			            data: { 'program_id': program_id, 'color_code': color_code },
			            url: $js_config.base_url + 'projects/program_color',
			            global: false,
			            success: function(response) {

			            },
			        });

			   	});
	    	}
		   	var $box = $('<small />', {
		   		'class' : 'prog-paint-box prog-colors'
		   	})
		   	;
		   	$inner_box.appendTo($box);
		   	$box.appendTo('body');
		   	dfd.resolve($box)
		   	return dfd.promise();
	    }

	    // ACTIVATE PAINT BOX
	    $('body').on('click', '.prog-paint', function(event) {
	        event.preventDefault();
	        var $brush = $(this);
	        $brush.removeData('box');
	        var coordinates = {};
	        $.create_painter().done(function(box){
	        	coordinates = $.getCoordinates($brush, box, 'bottom');
	        	if((box.outerWidth(true) + coordinates.left) >= $(window).width()) {
	        		coordinates = $.getCoordinates($brush, box, 'left');
	        	}
	        	if((box.outerHeight(true) + coordinates.top) >= $(window).height()) {
	        		coordinates = $.getCoordinates($brush, box, 'top');
	        	}
	        	box.css(coordinates).slideDown(100);
	        	$brush.data('box', box);
	        	box.data('brush', $brush);
	        });
	        $(this).parents('.pg-data-row').addClass('opened');
	    });

	    // SHOW/HIDE PAINT BOX
	    $('body').on('click', function(e) {
	        $('.prog-paint').each(function() {
	            //the 'is' for buttons that trigger popups
	            //the 'has' for icons within a button that triggers a popup
	            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.prog-paint-box').has(e.target).length === 0) {
	                var brush = $(this);
	                var box = $(this).data('box');
	                if(box) {
		                box.slideUp(100, function(){
		                	$(this).remove();
		                	brush.parents('.pg-data-row').removeClass('opened');
		                })
		            }
	            }
	        });
	    })

	    // filter projects with status
	    $('body').on('click', '.goto-projects', function(event) {
	    	event.preventDefault();
	    	programs = $(this).data('prog');
	    	$.selected_filters = {programs: [], projects: [], status: [], roles: [], rag: [], task_types: [], members: []};
	    	$.selected_filters.programs = [programs];
	    	$.show_filtered_data($.selected_filters).done((msg)=>{
	    		$('#summary_tabs a[href="#tab_projects"]').tab('show');
	    		$('.analytic-btn[data-type="projects"] .filter-icon').addClass('filterblue').removeClass('filterblack');
	    	});
	    });

	})
</script>