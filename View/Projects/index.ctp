<!-- <script src="https://cdn.jsdelivr.net/npm/raphael@2.3.0/raphael.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/morris.js06@0.6.8/dist/morris.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script> -->
<?php
echo $this->Html->script('plugins/morris/raphael.min');
echo $this->Html->script('plugins/morris/morris.min');

echo $this->Html->css('jquery.treeview');
echo $this->Html->script('jquery.treeview', array('inline' => true));
echo $this->Html->script('projects/project_costs', array('inline' => true));
echo $this->Html->css('projects/bs.checkbox');
echo $this->Html->css('projects/my_summary');
echo $this->Html->css('projects/project_risks');

echo $this->Html->script('projects/my_summary');
echo $this->Html->script('projects/d3.v6.min', array('inline' => true));
?>

<?php
$tab = (isset($current_tab) && !empty($current_tab)) ? $current_tab : false;
$tabPermit = false;
// e($project_permission);
if($project_permission == 'Creator' || $project_permission == 'Owner' || $project_permission == 'Group Owner'){
	$tabPermit = true;
}
?>

<style>
	section.content{
		padding-top: 0;
	}
	.opp-project-left {
		cursor: ns-resize;
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

    .morris-hover {
      position: absolute;
      z-index: 1000;
    }
    .morris-hover.morris-default-style {
      border-radius: 5px;
      padding: 4px;
      color: #fff;
      background: #444;
      font-family: Open Sans;
      font-size: 11px;
      text-align: center;
      min-width: 75px;
    }
    .morris-hover.morris-default-style .morris-hover-row-label {
      font-weight: bold;
      margin: 50px 0;
    }
    .morris-hover.morris-default-style .morris-hover-point {
      white-space: nowrap;
      margin: 0.1em 0;
    }
    .prj-detail-header .up-arrow-white {
    	display: none;
    }
    .prj-detail-header.opened .up-arrow-white {
    	display: inline-block;
    }
    .prj-detail-header.opened .down-arrow-white {
    	display: none;
    }


    .section-heading .up-arrow-white {
    	display: none;
    }
    .section-heading.opened .up-arrow-white {
    	display: inline-block;
    }
    .section-heading.opened .down-arrow-white {
    	display: none;
    }
    .bgtext {
        position: relative;
    }
    .bgtext:after {
        content: "No Project Image";
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
        color: #bbbbbb;
	    display: block;
	    font-size: 30px;
	    height: 40px;
    	margin: 35px 0;
	    text-align: center;
	    vertical-align: middle;
	    width: 100%;
	    text-transform: uppercase;
    }
    .popover p:first-child {
	    font-weight: 600 !important;
	    min-width: 100px !important;

	}
    .popover p:nth-child(2) {
	    font-size: 11px;
	}
	.popover p {
	    margin-bottom: 2px !important;
	}
	.tooltip-custom .tooltip-inner {
		text-align: left;
	}
	.com-list-wrap .activegreen, .com-list-wrap .inactivered {
		cursor: default !important;
	}
	.open-comp-modal {
		cursor: pointer ;
	}

	.stopped {
		pointer-events: none;
	}
	.stopped a {
		opacity: 0.7;
	}

	#opportunity_model_box .modal-footer .btn-default {
		background-color: #b2b2b2;
		color: #fff;
	}

  </style>
<div class="row">
	<div class="col-xs-12">
		<section class="main-heading-wrap pb6">
			<div class="main-heading-sec">
				<h1><?php echo htmlentities($this->ViewModel->_substr($project_detail['title'], 60, array('html' => true, 'ending' => '...')),ENT_QUOTES, "UTF-8"); ?></h1>
				<div class="subtitles">
					<?php if( (isset($project_detail['start_date']) && !empty($project_detail['start_date']) )  && (isset($project_detail['end_date']) && !empty($project_detail['end_date']) ) ){ ?>
					<span> <?php echo  date('d M, Y', strtotime($project_detail['start_date']));
					                        ?></span> →
					<span> <?php echo date('d M, Y', strtotime($project_detail['end_date'])); ?></span>
					<?php }else {
						echo 'No Schedule';
					}  ?>
				</div>
			</div>
			<div class="header-right-side-icon">
				<span class="headertag ico-project-summary tipText" title="Tag Team Members" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'tags', 'action' => 'add_tags_team_members', 'project' => $project_id, 'type' => 'project', 'admin' => false)); ?>"></span>
				<span class="ico-nudge ico-project-summary tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $project_id, 'type' => 'project', 'admin' => false)); ?>"></span>
				<?php
					$currentProject = $this->ViewModel->checkCurrentProjectid($project_id);
					$showTip = 'Set Bookmark';
					$pinClass = '';
					$pinitag = '<i class="headerbookmark"></i>';
					if( $currentProject > 0 ){
						$showTip = 'Clear Bookmark';
						$pinClass = 'remove_pin';
						$pinitag = '<i class="headerbookmarkclear"></i>';
					}
				?>
				<a class="tipText fav-current-task bookmark-project <?php echo $pinClass;?>" data-projectid="<?php echo $project_id; ?>" href="#" data-original-title="<?php echo $showTip;?>"><?php echo $pinitag; ?></a>
			</div>
		</section>
		<div class="box-content postion projects-summary-details">
			<div class="sep-header-fliter" style="">
				<?php echo $this->element('../Projects/sections/summary_options' ); ?>
				<div class="project-detail header-progressbar" >
					<div class="progressbar-sec project_progress_bar" >
						<?php
						$detail['projects'] = ['Project' => $project_detail];
						echo $this->element('../Projects/partials/project_progress_bar', $detail);
						?>
					</div>
				</div>
			</div>
			<div class="competencies-tab">
				<div class="row">
					<div class="col-md-12">
						<ul class="nav nav-tabs" id="summary_tabs" >
							<li <?php if(!$tab){ ?> class="active" <?php } ?>>
								<a data-toggle="tab" data-type="searchs" class="active tab_wsp_information" data-target="#tab_wsp_information" href="#tab_wsp_information" aria-expanded="true">Information</a>
							</li>
							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_team" data-target="#tab_team" href="#tab_team" aria-expanded="false">Team</a>
							</li>
							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_wsp" data-target="#tab_wsp" href="#tab_wsp" aria-expanded="false">Work Packages</a>
							</li>
							<li>
								<a data-toggle="tab" data-type="searchs" class="tab_breakdown" data-target="#tab_breakdown" href="#tab_breakdown" aria-expanded="false">Breakdown</a>
							</li>
							<?php if($tabPermit){ ?>
							<li <?php if($tab && $tab == 'cost'){ ?> class="active" <?php } ?>>
								<a data-toggle="tab" data-type="searchs" class="tab_cost" data-target="#tab_cost" href="#tab_cost" aria-expanded="false">Costs</a>
							</li>
							<?php } ?>
							<li <?php if($tab && $tab == 'risk'){ ?> class="active" <?php } ?>>
								<a data-toggle="tab" data-type="risks" class="tab_risk" data-target="#tab_risk" href="#tab_risk" aria-expanded="false">Risks</a>
							</li>
							<?php if($tabPermit){ ?>
							<li>
								<a data-toggle="tab" data-type="perform" class="tab_perform" data-target="#tab_perform" href="#tab_perform" aria-expanded="false">Performance</a>
							</li>

							<?php } ?>

						</ul>
					</div>
				</div>
			</div>

			<div class="box noborder">
				<div class="box-body clearfix nopadding wsp-task-scroll" style="" id="box_body">
					<div class="tab-content">
						<div class="tab-pane fade <?php if(!$tab){ ?> active in <?php } ?>" id="tab_wsp_information">
							<div class="wsp-task-information" style="min-height: 800px;">
								<div class="wsp-task-info-top-sec">

									<div class="wsp-task-info-top-left">
										<?php echo $this->element('../Projects/sections/project_summary'); ?>
									</div>

									<div class="wsp-task-info-top-right">
										<div class="wsp-task-info-top-right-sec document-section">
										  	<div class="wsp-task-info-details-heading docs-heading section-heading"> <h4>Project Documents</h4> <span class="ts-count">0</span><i class="down-arrow-white"></i><i class="up-arrow-white"></i></div>
											<div class="wttr-inner-common" style="display: none;">
												<?php //echo $this->element('../Projects/sections/summary_docs'); ?>
											</div>
										</div>
										<div class="wsp-task-info-top-right-sec notes-section">
										  	<div class="wsp-task-info-details-heading section-heading notes-heading"> <h4>Project Notes</h4> <span class="ts-count">0</span><i class="down-arrow-white"></i><i class="up-arrow-white"></i></div>
											<div class="wttr-inner-common" style="display: none">
												<?php //echo $this->element('../Projects/sections/summary_notes'); ?>
											</div>
										</div>
										<?php  ?><div class="wsp-task-info-top-right-sec links-section">
										  	<div class="wsp-task-info-details-heading section-heading links-heading"> <h4>Project Links</h4> <span class="ts-count">0</span><i class="down-arrow-white"></i><i class="up-arrow-white"></i></div>
											<div class="wttr-inner-common link-list-wrap" style="display: none">
												<?php //echo $this->element('../Projects/sections/summary_links'); ?>
											</div>
										</div><?php  ?>
										<?php  ?><div class="wsp-task-info-top-right-sec competency-section">
										  	<div class="wsp-task-info-details-heading section-heading competency-heading "> <h4>Project Competencies</h4> <span class="ts-count">0</span><i class="down-arrow-white"></i><i class="up-arrow-white"></i></div>
											<div class="wttr-inner-common com-list-wrap" style="display: none">
												<?php //echo $this->element('../Projects/sections/summary_competency'); ?>
											</div>
										</div><?php  ?>
									</div>
								</div>
								<div class="wsp-task-info-bottom-sec">
									<div class="task-charts-sec-row">
										<?php if(isset($project_tiles) && !empty($project_tiles)){
											foreach ($project_tiles as $key => $value) {
												if(!empty($value['ProjectTile']['status'])){
													//echo $this->element('../Projects/sections/'.$value['ProjectTile']['filename'], ['id' => $value['ProjectTile']['id']]);
												}
											}
										} ?>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="tab_team">
							<input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        		<input type="hidden" name="paging_total" id="paging_total" value="0">
								<div class="project-summary-wrap">
								   <div class="ps-col-header">
								      <div class="ps-col tm-col-1">
								         <span class="ps-h-one">Name <span class="total-data"></span>
									         <span class="sort_order active" data-by="first_name" data-order="desc" data-type="teams" title="" data-original-title="Sort By First Name">
										         <i class="fa fa-sort" aria-hidden="true"></i>
										         <i class="fa fa-sort-asc" aria-hidden="true"></i>
										         <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									         <span class="sort_order" data-by="last_name" data-order="desc" data-type="teams" title="" data-original-title="Sort By Last Name">
										         <i class="fa fa-sort" aria-hidden="true"></i>
										         <i class="fa fa-sort-asc" aria-hidden="true"></i>
										         <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
								         </span>
								         <span class="ps-h-two sort_order" data-by="job_title" data-order="desc" data-type="teams" title="" data-original-title="Sort By Job Title">
									         Title
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="ps-h-two sort_order" data-by="role" data-order="desc" data-type="teams" title="" data-original-title="Sort By Role">
									         Role
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-2">
								         Effort
								         <span class="sort_order" data-by="completed_hours" data-order="desc" data-type="teams" title="" data-original-title="Sort By Completed Hours">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="remaining_hours" data-order="desc" data-type="teams" title="" data-original-title="Sort By Remaining Hours">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="change_hours" data-order="desc" data-type="teams" title="" data-original-title="Sort By Change">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-3">
								         Costs
								         <span class="sort_order" data-by="escost" data-order="desc" data-type="teams" title="" data-original-title="Sort By Budget">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="spcost" data-order="desc" data-type="teams" title="" data-original-title="Sort By Actual">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <!-- <span class="sort_order" data-by="cost_status" data-order="desc" data-type="teams" title="" data-original-title="Sort By Status">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span> -->
								      </div>
								      <div class="ps-col tm-col-4">
								         Risks
								         <span class="sort_order" data-by="high_pending_risks" data-order="desc" data-type="teams" title="" data-original-title="Sort By High Pending Risks">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="severe_pending_risks" data-order="desc" data-type="teams" title="" data-original-title="Sort By Severe Pending Risks">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="total_risks" data-order="desc" data-type="teams" title="" data-original-title="Sort By Total Risks Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-5">
								         Competencies
								         <span class="sort_order" data-by="user_skills" data-order="desc" data-type="teams" title="" data-original-title="Sort By Skills Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="user_subjects" data-order="desc" data-type="teams" title="" data-original-title="Sort By Subjects Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="user_domains" data-order="desc" data-type="teams" title="" data-original-title="Sort By Domains Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-6">
								         Last Activity
								         <span class="sort_order" data-by="message" data-order="desc" data-type="teams" title="" data-original-title="Sort By Last Activity">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="updated" data-order="desc" data-type="teams" title="" data-original-title="Sort By Last Activity Date">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-7">
								         Actions
								      </div>
								   </div>
								   <div class="project-summary-data team-data" data-flag="true">
								   	<?php
										//echo $this->element('../Projects/sections/project_teams');
									?>
								   </div>
								</div>
						</div>
						<div class="tab-pane fade" id="tab_breakdown">
							<div class="tab_breakdown_wrap">
								<div class="input-group search-skills-box">
	                                <input type="text" class="form-control search-box" id="br_search" data-type="cost" placeholder="Search for Work...">
									<span class="input-group-btn">
										<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
										<button class="btn clear-btn" type="button"><i class="clearblackicon search-clear"></i></button>
									</span>
							 	</div>
							 	<div class="breakdown-wrap"> </div>

							</div>

						</div>
						<div class="tab-pane fade" id="tab_wsp">
							<div class="wsp-task-wrap ">
								<input type="hidden" id="paging_page" value="0" />
								<input type="hidden" id="paging_max_page" value="" />
								<div class="wsp-col-header">
									<div class="wsp-col wsp-col-1">Workspaces <span class="total-data">(0)</span></div>
									<div class="wsp-col wsp-col-2"> Team </div>
									<div class="wsp-col wsp-col-3"> Work </div>
									<div class="wsp-col wsp-col-8">
										Effort
									</div>
									<div class="wsp-col wsp-col-4"> Assets </div>
									<div class="wsp-col wsp-col-5"> Costs </div>
									<div class="wsp-col wsp-col-6"> Risks </div>
									<div class="wsp-col wsp-col-7"> Actions </div>
								</div>
								<div class="wsp-data" style="min-height: 600px;">
									<?php //echo $this->element('../Projects/sections/summary_data'); ?>
								</div>
							</div>
						</div>

						<div class="tab-pane fade <?php if($tab && $tab == 'cost' && $tabPermit){ ?> active in <?php } ?>" id="tab_cost">
							<div class="tab_cost_wrap">
							 	<div class="input-group search-skills-box">
	                                <input type="text" class="form-control search-box" id="search_tree" data-type="cost" placeholder="Search...">
									<span class="input-group-btn">
										<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
										<button class="btn clear-btn" type="button"><i class="clearblackicon search-clear"></i></button>
									</span>
							 	</div>
								<div class="tab_cost_data">
									<?php // echo $this->element('../Projects/cost/tree'); ?>
								</div>
							</div>

						</div>
						<div class="tab-pane fade <?php if($tab && $tab == 'risk'){ ?> active in <?php } ?>" id="tab_risk" data-type="risks">
							<input type="hidden" id="paging_page" value="0" />
							<input type="hidden" id="paging_max_page" value="" />
							<div class="risks_wrap" id="tab_risk_wrap">
								<div class="risk-details-wrap">

									<div class="risks-multi-select-sec">
									   <div class="risks-multi-select-left ">
									      <div class="risks-multi-select1">
									         <label class="custom-dropdown" style="width: 100%;">
									            <select class="form-control aqua" id="dd_risk_types"></select>
									         </label>
									      </div>
									      <div class="risks-multi-select2">
									         <label class="custom-dropdown" style="width: 100%;">
									            <label class="custom-dropdown" style="width: 100%;">
									               <select class="form-control aqua" id="dd_statuses">
									                  <option value="">All Statuses</option>
									                  <option value="Open">Open</option>
									                  <option value="Review">In Progress</option>
									                  <option value="Overdue">Overdue</option>
									                  <option value="Completed">Completed</option>
									               </select>
									            </label>
									         </label>
									      </div>
									      <div class="risks-multi-select2">
									         <label class="custom-dropdown" style="width: 100%;">
									            <select class="form-control aqua" id="dd_impacts">
									               <option value="">All Impacts</option>
									               <option value="Not Set">Not Set</option>
									               <option value="1">Negligible</option>
									               <option value="2">Minor</option>
									               <option value="3">Moderate</option>
									               <option value="4">Major</option>
									               <option value="5">Critical</option>
									            </select>
									         </label>
									      </div>
									      <div class="risks-multi-select2">
									         <label class="custom-dropdown" style="width: 100%;">
									            <select class="form-control aqua" id="dd_percentages">
									               <option value="">All Probabilities</option>
									               <option value="Not Set">Not Set</option>
									               <option value="1">Rare</option>
									               <option value="2">Unlikely</option>
									               <option value="3">Possible</option>
									               <option value="4">Likely</option>
									               <option value="5">Almost Certain</option>
									            </select>
									         </label>
									      </div>
									      <div class="risks-multi-select2">
									         <label class="custom-dropdown" style="width: 100%;">
									            <select class="form-control aqua" id="dd_exposers">
									               <option value="">All Exposures</option>
									               <option value="severe">Severe</option>
									               <option value="high">High</option>
									               <option value="medium">Medium</option>
									               <option value="low">Low</option>
									            </select>
									         </label>
									      </div>
									   </div>
									   <div class="risks-multi-right-icon">
									      <a href="" class="reset-btn-rc tipText" title="" id="reset_filters" data-original-title="Reset"><i class="resetblack"></i></a>
									      <a class="tipText risk-switch map" href="#" title="Risk Map" id="btn_risk_map"><i class="heatmapblack"></i></a>
									      <a class="tipText" id="btn_manage_risk" title="Add Risk" href="<?php echo Router::Url( array( "controller" => "risks", "action" => "manage_risk", 'project' => $project_id,  'admin' => FALSE ), true ); ?>"> <i class="workspace-icon"></i></a>
									   </div>
									</div>
									<div class="risks-summary-wrap">
									   <div class="rs-col-header">
									      <div class="rs-col rs-col-1">
									         <span class="ps-h-one">Risk Title <span class="total-risk-data">(0)</span>
									            <span class="sort_order tipText" data-coloumn="title" data-order="desc" data-type="risks" title="" data-original-title="Sort By Risk">
									               <i class="fa fa-sort" aria-hidden="true"></i>
									               <i class="fa fa-sort-asc" aria-hidden="true"></i>
									               <i class="fa fa-sort-desc" aria-hidden="true"></i>
									            </span>
									            <!-- <span class="sort_order tipText" data-coloumn="ptitle" data-order="desc" data-type="risks" title="" data-original-title="Sort By Project">
									               <i class="fa fa-sort" aria-hidden="true"></i>
									               <i class="fa fa-sort-asc" aria-hidden="true"></i>
									               <i class="fa fa-sort-desc" aria-hidden="true"></i>
									            </span> -->
									         </span>
									      </div>
									      <div class="rs-col rs-col-2">
									         People <span class="sort_order tipText" data-coloumn="creator_name" data-order="desc" data-type="risks" title="" data-original-title="Sort By Creator">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									         <span class="sort_order tipText" data-coloumn="ruser_count" data-order="desc" data-type="risks" title="" data-original-title="Sort By Total Assignee">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									      </div>
									      <div class="rs-col rs-col-3">
									         Risk Type <span class="sort_order tipText" data-coloumn="risk_type" data-order="desc" data-type="risks" title="" data-original-title="Sort By Type">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									         <span class="sort_order tipText" data-coloumn="rtask_count" data-order="desc" data-type="risks" title="" data-original-title="Sort By Total Task">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									      </div>
									      <div class="rs-col rs-col-4">
									         Status <span class="sort_order tipText" data-coloumn="rd_status" data-order="desc" data-type="risks" title="" data-original-title="Sort By Status">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									      </div>
									      <div class="rs-col rs-col-5">
									         Impact <span class="sort_order tipText" data-coloumn="rd_impact" data-order="desc" data-type="risks" title="" data-original-title="Sort By Impact">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									      </div>
									      <div class="rs-col rs-col-6">
									         Probability <span class="sort_order tipText" data-coloumn="rd_percent" data-order="desc" data-type="risks" title="" data-original-title="Sort By Probability">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									      </div>
									      <div class="rs-col rs-col-7">
									         When <span class="sort_order tipText active" data-coloumn="rdate" data-order="asc" data-type="risks" title="" data-original-title="Sort By Possible Occurrence By">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									         <span class="sort_order tipText" data-coloumn="rd_exposure" data-order="desc" data-type="risks" title="" data-original-title="Sort By Exposure">
									            <i class="fa fa-sort" aria-hidden="true"></i>
									            <i class="fa fa-sort-asc" aria-hidden="true"></i>
									            <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									      </div>
									      <div class="rs-col rs-col-7">
									         Actions
									      </div>
									   </div>
									   <div class="risks-summary-data" data-flag="true">
											<?php //echo $this->element('../Projects/sections/project_risks'); ?>
										</div>
									</div>
								</div>
								<div class="risk-map-wrap" style="display: none;">
									<div class="project-risk-map">
									    <div class="projectrisk-top">
									        <div class="projectrisk-button">
									            <a class="tipText risk-detail risk-switch detail" title="Risk Details" href="#"><i class="listblack"></i></a>
									            <a class="tipText" title="Add Risk" href="<?php echo Router::Url( array( "controller" => "risks", "action" => "manage_risk", 'project' => $project_id, 'status', 'admin' => FALSE ), true ); ?>"> <i class="workspace-icon"></i></a>
									        </div>
									    </div>
									    <div class="projectrisk-outer risk-map-data">
											<?php echo $this->element('../Projects/sections/risks_map'); ?>
										</div>
									</div>

								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="tab_perform">
							<div class="perform_wrap" id="perform_wrap">
								<?php echo $this->element('../Projects/sections/project_performance'); ?>
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
<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="model_bx_project" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="model_bx_wsp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="model_cost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog rate-card-popup">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="upload_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="opportunity_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal modal-success fade" id="signoff_comment_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>


<div class="modal modal-danger fade" id="signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius"></div>
    </div>
</div>

<script>
$(function(){

	if($js_config.type && $js_config.type == 'annotate'){
		setTimeout(()=>{
			$.open_annotate = false;
			$('.project_progress_bar .annotation').trigger('click');
			history.pushState(null, '', $js_config.base_url + 'projects/index/'+$js_config.project_id);
		}, 100)
	}

	$('.project-sign-off').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                    data = $this.data(),
                    id = data.id,
                    title = data.header,
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');
            $span_text.css({ 'padding': '0', 'font-size': '14px'})
            // set message
            var body_text = $this.attr('data-msg');
            console.log('data', $yes)

            $('#confirm-box').find('#modal_body').text(body_text)
            $('#confirm-box').find('#modal_header').css('background-color','#d9534f');
            $('#confirm-box').find('#modal_header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><span style="font-size:16px;color:#fff;" id="myModalLabel" class="modal-title">'+title+'</span>');


			BootstrapDialog.show({
	            title: title,
	            type: BootstrapDialog.TYPE_DANGER,
	            message: body_text,
	            draggable: true,
	            buttons: [{
	                label: 'Reopen',
	                cssClass: 'btn-success',
	                autospin: true,
	                action: function(dialogRef) {
	                    var post = { 'data[Element][id]': id, 'data[Element][sign_off]': data.value },
	                    data_string = $.param(post);
	                     // Ajax request to sign-off/reopen
	                        var post = {'data[Project][id]': id, 'data[Project][sign_off]': data.value},
	                        data_string = $.param(post);

	                        $.ajax({
	                            type: 'POST',
	                            data: data_string,
	                            url: $js_config.base_url + 'projects/project_signoff',
	                            global: false,
	                            dataType: 'JSON',
	                            beforeSend: function () {
	                                $span_text.css({'opacity': 0.5, 'color': '#222222'})
	                                $div_progress.css({'width': '100%'})
	                            },
	                            complete: function () {
	                                setTimeout(function () {
	                                    $('#confirm-box').modal('hide')
	                                    $span_text.css({'opacity': 1, 'color': '#ffffff'})
	                                    $div_progress.css({'width': '0%'})
	                                }, 3000)
	                            },
	                            success: function (response, statusText, jxhr) {

	                                //return
	                                if (response.success) {
	                                    if(response.content){
	                                        // send web notification
	                                        $.socket.emit('socket:notification', response.content.socket, function(userdata){});
	                                    }
	                                    location.reload();
	                                    setTimeout(function () {
	                                        // location.reload(true)

	                                    }, 2500)
	                                    location.reload();
	                                }
	                                else {
	                                    console.log('fail')
	                                }

	                            }
	                        })
	                }
	            },
	            {
	                label: ' Cancel',
	                //icon: '',
	                cssClass: 'btn-danger',
	                action: function(dialogRef) {
	                    dialogRef.close();
	                }
	            }]
	        });
        });

	;($.create_tree = () => {
		$.ajax({
            url: $js_config.base_url + 'projects/tree',
            type: 'POST',
            dataType: 'JSON',
            data: { project: $js_config.project_id },
            success: function(response) {
                $('.tab_cost_data').html(response);
            }
        })
	})();

	;($.load_breakdown = () => {
		$.ajax({
            url: $js_config.base_url + 'projects/project_breakdown',
            type: 'POST',
            // dataType: 'JSON',
            data: { project: $js_config.project_id },
            success: function(response) {
                $('.breakdown-wrap').html(response);
            }
        })
	})();

	$('.open-cost').on('click', function(event) {
		event.preventDefault();
		$('#summary_tabs a[href="#tab_cost"]').tab('show');
	});

	$('#opportunity_model_box').on('hidden.bs.modal', function(event) {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
	});

	$('body').delegate('#remove_org',  'click', function(e){
		e.preventDefault()
		var  $this = $(this);
		var project_id = $('#project_id').val();
		var  selected_ids = $('#all_org').val();

		if(project_id){
			$.ajax({
				url: $js_config.base_url + 'projects/remove_project_opportunity',
				type: 'POST',
				dataType: 'JSON',
				data: { project_id: project_id, org_id:selected_ids },
				success: function(response) {
					if( response.success ){
						//$("#count_opp_req").text(response.opp_count);
						if( response.opp_count > 0 ){
							$("#count_opp_req").removeClass('hide').addClass('show');
						} else {
							$("#count_opp_req").addClass('hide').removeClass('show');
						}
						$('#opportunity_model_box').modal('hide')
					}
				}
			})
		}


	});

	$('body').delegate('#post_data',  'click', function(e){
		e.preventDefault()
		$('.item-selection .error-text').text('');
		var  $this = $(this);

		var selected_ids = $('#all_org').val();
		if(selected_ids){
			var project_id = $('#project_id').val();
			//$('#all_org').multiselect("disable");
			$("#post_data").addClass('btn-default disabled').removeClass('btn-success');
			var data_string = $.param({ project_id: project_id, org_id:selected_ids });
			$.ajax({
				url: $js_config.base_url + 'projects/save_org_opportunity',
				type: 'POST',
				dataType: 'JSON',
				data: { project_id: project_id, org_id:selected_ids },
				success: function(response) {
					if( response.success ){
						if( response.opp_count > 0 ){
							$("#count_opp_req").removeClass('hide').addClass('show');

						} else {
							$("#count_opp_req").addClass('hide').removeClass('show');
						}
						$("#post_data").addClass('btn-default disabled').removeClass('btn-success');;
						$('#opportunity_model_box').modal('hide')
					}
				}
			})


		} else {
			$('.item-selection .error-text').text('Please select at least one opportunity').addClass('text-danger').removeClass('text-success');
		}
	})



	/******* PROJECT TEAM ******/
	$.tabTeam = $('#tab_team');
	// SORTING
	$('body').on('click', '#tab_team .sort_order', function(event) {
		var $that = $(this),
			$parent = $(this).parents('.tab-pane:first'),
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

		var data = {order: order, coloumn: coloumn, project_id: $js_config.project_id, workspace_id: $js_config.workspace_id}

		$.ajax({
			url: $js_config.base_url + 'projects/filter_project_team',
			type: 'POST',
			// dataType: 'json',
			data: data,
			success: function(response){

				$parent.find("#paging_offset").val(0);

				if( order == 'asc' ){
					$that.data('order', 'desc');
				} else {
					$that.data('order', 'asc');
				}
				$parent.find('.team-data').html(response);
				$('.tooltip').remove();

			}
		})
	})

	// TABS PAGINATION
    $('.team-data').scroll(function() {
        $('.tooltip').hide()
        var $this = $(this);
        var $parent = $this.parents('.tab-pane:first');
        clearTimeout($.data(this, 'scrollTimer'));

        $.data(this, 'scrollTimer', setTimeout(function() {
            if($this.scrollTop() + $this.innerHeight()+15 >= $this[0].scrollHeight)  {
				$.updateTeamOffset($this, $parent);
            }
        }, 250));
    });

    $.countTeamSize = function(parent) {
        var dfd = $.Deferred();

        var order = 'asc',
        	coloumn = 'first_name';
        if( $('.sort_order.active', parent).length > 0 ) {
            order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
            coloumn = $('.sort_order.active', parent).data('by');

            if( order == 'asc' ){
                order = 'desc';
            } else {
                order = 'asc';
            }
        }

        var data = {order: order, coloumn: coloumn, project_id: $js_config.project_id}

        $.ajax({
            url: $js_config.base_url + 'projects/project_team_count',
            data: data,
            type: 'post',
            dataType: 'JSON',
            success: function(response) {
                $('#paging_offset', parent).val(0);
                $('#paging_total', parent).val(response);
                $('.total-data', parent).html('('+response+')');
				if(response <= 0){
            		$('.ps-col-header', parent).addClass('none-selection');
				}
				else{
					$('.ps-col-header', parent).removeClass('none-selection');
				}
                dfd.resolve('paging count');
            }
        })
        return dfd.promise();
    }
    $.countTeamSize($.tabTeam);

    $.project_team_offset = $js_config.project_team_offset;
    $.updateTeamOffset = function(wrapper, parent){
        var page = parseInt($('#paging_offset', parent).val());
        var max_page = parseInt($('#paging_total', parent).val());
        var last_page = Math.ceil(max_page/$.project_team_offset);

        if(page < last_page - 1 && wrapper.data('flag')){
            $('#paging_offset', parent).val(page + 1);
            offset = ( parseInt($('#paging_offset', parent).val()) * $.project_team_offset);
            $.getTeamPages(offset, wrapper, parent);
        }
    }

    $.getTeamPages = function(page, wrapper, parent){
        wrapper.data('flag', false);
        var $wrapper = wrapper;
		//added by me ******************
		var order = 'asc',
			coloumn = 'first_name';
		if( $('.sort_order.active', parent).length > 0 ) {
			order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
			coloumn = $('.sort_order.active', parent).data('by');
		}
        var data = {page: page, order: order, coloumn: coloumn, project_id: $js_config.project_id }

        $.ajax({
            type: "POST",
            url: $js_config.base_url + 'projects/filter_project_team',
            data: data,
            // dataType: 'JSON',
            success: function(html) {
                $wrapper.append(html);
                wrapper.data('flag', true);
            }
         });
    }

    $.refreshTeam = function(page, wrapper, parent){
        wrapper.data('flag', false);
        var $wrapper = wrapper;
		//added by me ******************
		var order = 'asc',
			coloumn = 'first_name';
		if( $('.sort_order.active', parent).length > 0 ) {
			order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
			coloumn = $('.sort_order.active', parent).data('by');
		}

        var data = {page: 0, order: order, coloumn: coloumn, project_id: $js_config.project_id }

        $.ajax({
            type: "POST",
            url: $js_config.base_url + 'projects/filter_project_team',
            data: data,
            success: function(html) {
                $wrapper.html(html);
                wrapper.data('flag', true);
            }
         });
    }
    $.refreshTeam(0, $('.team-data'), $('.tab-pane#tab_team'))

    $('.sort_order').tooltip({
        placement: 'top',
        container: 'body'
    })
	/******* WORKSPACE TEAM ******/

	$('#availability_modal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
        $.countTeamSize($.tabTeam);
        $.refreshTeam(0, $('.team-data'), $('.tab-pane#tab_team'))
    })



    $.get_project_summary = function(){

        var data = {project_id: $js_config.project_id }

        $.ajax({
            type: "POST",
            url: $js_config.base_url + 'projects/get_project_summary',
            data: data,
            success: function(html) {
                $('.wsp-data').html(html);
            }
         });
    }
    $.get_project_summary();
})

$(()=>{

})
</script>
