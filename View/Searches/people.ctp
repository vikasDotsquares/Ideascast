<?php
$resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
$resourcer_permit = (isset($resourcer) && !empty($resourcer)) ? $resourcer : false;
?>
<?php
echo $this->Html->css('projects/people');
// e($selected_tab,1);
?>
<script type="text/javascript">
    $('html').addClass('no-scroll');
    $.resourcer_permit = '<?php echo $resourcer_permit; ?>'
</script>
<div class="row">
    <div class="col-xs-12">
        <section class="main-heading-wrap pb6">
            <div class="main-heading-sec">
                <h1><?php echo $page_heading; ?></h1>
                <div class="subtitles"><?php echo $page_subheading; ?></div>
            </div>
			<div class="header-right-side-icon">
				<span class="headertag ico-project-summary tipText" title="Tag" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "add_tags_team_members", 'type' => 'people', 'admin' => FALSE ), true ); ?>" data-original-title="Tag Team Members"></span>
				<span class="ico-nudge ico-project-summary tipText" title="Send Nudge" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::Url( array( "controller" => "boards", "action" => "send_nudge_board", 'type' => 'people', 'admin' => FALSE ), true ); ?>" data-original-title="Send Nudge"></span>
			</div>

        </section>
        <div class="box-content postion ">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="competencies-tab people-info-wrap mt0">
                        <div class="row">
                            <div class="col-md-9">
                                <ul class="nav nav-tabs" id="people_list_tabs">
                                    <li class="active" >
                                        <a data-toggle="tab" data-type="people" class="t-people" data-target="#tab_people" href="#tab_people" aria-expanded="true">PROFILES</a>
                                    </li>
									<li>
                                        <a data-toggle="tab" data-type="Engagements" class="t-engagement" data-target="#tab_engagements" href="#tab_engagements" aria-expanded="true">Engagements</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3">

                                <div class="project-link-top-right">
                                    <a href="" class="tipText" title="Filter People" data-toggle="modal" data-target="#modal_filter_people" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "filter_people", 'admin' => FALSE ), true ); ?>"><i class="filter-icon filterblack"></i></a>
                                    <?php if($resourcer_permit){ ?>
									  <span class="hlt-sep">
									<a href="#" class="tipText people-to-planning" data-type="Resources" title="Go To Planning"><i class="planningblack18"></i></a>
									</span>
                                    <?php } ?>
                                    <span class="hlt-sep">

                                        <a href="" class="tipText" title="Work Blocks" data-toggle="modal" data-target="#modal_work" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "work_block", 'admin' => FALSE ), true ); ?>"> <i class="blockblack18"></i></a>
                                        <a href="" class="tipText" title="Absences" data-toggle="modal" data-target="#availability_modal" data-remote="<?php echo Router::Url( array( "controller" => "settings", "action" => "availability", 'admin' => FALSE ), true ); ?>"> <i class="absenceblack18"></i></a>
                                    </span>
                                    <span class="hlt-sep sh-icons">
                                        <a href="" class="tipText expand-list" title="Show Details"> <i class="showmoreblack"></i></a>
                                        <a href="" class="tipText collapse-list" title="Hide Details"> <i class="showlessblack"></i></a>
                                    </span>
                                    <?php if($this->Session->read('Auth.User.UserDetail.administrator') == 2){ ?>
                                    <span class="hlt-sep">
                                        <a href="<?php echo Router::Url( array( "controller" => "organisations", "action" => "user_add", 'people', 'admin' => FALSE ), true ); ?>" class="tipText" title="Add Profile"> <i class="workspace-icon"></i></a>
                                    </span>
                                    <?php } ?>
                                </div>
                                <div class="input-group search-skills-box pp-search">
                                    <input id="temp_search " type="text" class="form-control search-box" data-type="people" placeholder="Search for People..." style="display: block;" autocomplete="off">
                                    <span class="input-group-btn">
                                        <button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
                                        <button class="btn clear-btn" type="button" style="display: none;"><i class="clearblackicon search-clear"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box noborder">

                        <div class="box-body clearfix nopadding people-tab-scroll" >
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="tab_people" data-type="people">
                                    <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                                    <input type="hidden" name="paging_total" id="paging_total" value="0">
                                    <div class="people-lsit-wrap">

                                        <div class="people-col-header">
                                            <div class="people-col-1">
                                                <span class="pp-h-one">Name <span class="total-data">(0)</span>
                                                <span class="sort_order tipText active " title="Sort By First Name" data-type="people" data-by="first_name" data-order="desc">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Last Name" data-type="people" data-by="last_name" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                            </span>
                                            <span class="pp-h-two">Title
                                                <span class="sort_order tipText" title="Sort By Job Title" data-type="people" data-by="job_title" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                            </span>
                                            <span class="pp-h-two">Reports
                                                <span class="sort_order tipText" title="Sort By Reports To" data-type="people" data-by="reports_to_user" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Reports From Count" data-type="people" data-by="count_report_to" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Dotted Lines To Count" data-type="people" data-by="count_dline" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Dotted Lines From Count" data-type="people" data-by="count_dotted_line" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                            </span>
                                            <span class="pp-h-two">Community
                                                <span class="sort_order tipText" title="Sort By Organization" data-type="people" data-by="organization" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Location" data-type="people" data-by="location" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Department" data-type="people" data-by="department" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>

                                            </span>
                                            <span class="pp-h-two sort_order tipText" title="Sort By Tags Count" data-type="people" data-by="count_tag" data-order="">Tags
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span class="pp-h-two"> Competencies
                                                <span class="sort_order tipText" title="Sort By Skills Count" data-type="people" data-by="count_skill" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Subjects Count" data-type="people" data-by="count_subject" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span class="sort_order tipText" title="Sort By Domains Count" data-type="people" data-by="count_domain" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                            </span>
                                            <span class="pp-h-two">Stories
                                                <span class="sort_order tipText" title="Sort By Stories Count" data-type="people" data-by="count_story" data-order="">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </div>


                                    <div class="people-col-cont list-wrapper" data-flag="true" style="min-height: 700px;">
                                        <ul class="people-cont-list">
                                            <?php echo $this->element('../Searches/people/people_list'); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

							<div class="tab-pane fade" id="tab_engagements" data-type="engagements">
								<div class="box-header engagements-filters">
                                    <div class="engagements-header-box">
                                        <div class="engagements-start-date">
                                            <div class="input-group">
                                                <input name="range_start_date" value="<?php echo $start_date; ?>" id="range_start_date" class="form-control input-small" type="text" autocomplete="off">
                                                <div class="input-group-addon open-range-cal">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="engagements-end-date">
                                            <div class="input-group">
                                                <input name="range_end_date" value="<?php echo $end_date; ?>" id="range_end_date" class="form-control input-small" type="text" autocomplete="off">
                                                <div class="input-group-addon open-range-cal">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="engagements-btn">
                                            <button class="btn btn-success show-engagement " type="button">Show</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="engagements-chart">
                                    <div class="tab_engagement_data" id="tab_engagement_data"><?php if(!isset($selected_tab) || empty($selected_tab)){ ?><div class="no-summary-found">No People</div><?php } ?></div>
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
</div>
</div>
<?php
echo $this->Html->script('projects/people', array('inline' => true));
echo $this->Html->script('projects/timeline-chart.min', array('inline' => true));
echo $this->Html->script('projects/d3.v5.min', array('inline' => true));
?>

<style type="text/css">
    section.content {
        padding-top: 0;
    }
    .wpop p {
    margin-bottom: 2px !important;
    }
    .wpop p:first-child {
        font-weight: 600 !important;
        width: 170px !important;
    }
    .wpop p:nth-child(2) {
        font-size: 11px;
    }
</style>

<!-- Modal Boxes -->
<div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="modal_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="modal_filter_people" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog people-filter-modal">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xs">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="modal_work" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog people-workblock-modal">
        <div class="modal-content"></div>
    </div>
</div>
<!-- /.modal -->