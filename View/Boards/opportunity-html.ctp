<?php echo $this->Html->css(array(
    'projects/opportunity',
));
echo $this->Html->script('projects/opportunity', array('inline' => true));
?>
<div class="row">
    <div class="col-xs-12">

        <section class="main-heading-wrap">
            <div class="main-heading-sec">
                <h1><?php echo $page_heading; ?></h1>
                <div class="subtitles"><?php echo $page_subheading; ?></div>
            </div>

            <div class="header-right-side-icon">
            	<span class="ico-nudge ico-project-summary tipText" title="Send Nudge" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'type' => 'nudge', 'admin' => false)); ?>"></span>
            </div>
        </section>

        <div class="box-content opp-list-wrap opportunities-lists">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="competencies-tab">
                        <div class="row">
                            <div class="col-md-9">
                                <ul class="nav nav-tabs" id="opp_tabs">
                                    <li class="active">
                                        <a data-toggle="tab" data-type="project" class="active competencies_tab project-tab" data-target="#tab_project" href="#tab_project" aria-expanded="true">PROJECTS</a>
                                    </li>
                                     <li >
                                        <a data-toggle="tab" data-type="request" class="competencies_tab request-tab" data-target="#tab_request" href="#tab_request" aria-expanded="true">REQUESTS</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3 right text-right">
                                <div class="input-group search-skills-box">
                                    <input type="text" class="form-control search-box" data-type="project" placeholder="Search for Projects...">
                                    <span class="input-group-btn" >
                                        <button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
                                        <button class="btn clear-btn" type="button"><i class="clearblackicon search-clear"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box noborder ">

                        <div class="box-body p0 clearfix" id="box_body">
                            <div class="tab-content">
                                <div id="tab_project" data-type="project" class="tab-pane fade active in ssd-tabs">
			                        <input type="hidden" name="paging_offset" id="paging_offset" value="1">
			                        <input type="hidden" name="paging_total" id="paging_total" value="0">
                                    <div class="ssd-wrap">
                                        <div class="ssd-col-header">
                                            <div class="loc-col opp-col opp-col-1">
                                                <div class="opp-heading updated-head">Title<span class="total-rows"> (0)</span>
                                                    <span class="h-name-one sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title"  data-type="project">
                                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                    </span>
													<span class="opp-short">
													Schedule
                                                    <span class="h-name-one sort_order active tipText" title="Sort By Start Date" data-coloumn="start_date" data-order="asc" data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>

													<span class="h-name-one sort_order tipText" title="Sort By End Date" data-coloumn="end_date" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
														</span>
													</span>
													<span class="opp-short">
													Status
													<span class="h-name-one sort_order tipText" data-coloumn="project_status" data-order=""  data-type="project" title="Sort By Schedule Status"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
													</span>
                                                </div>

                                            </div>
											<div class="loc-col opp-col opp-col-2">
                                                Team
                                                <span class="h-name-one sort_order tipText" title="Sort By Owners" data-coloumn="total_owners" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                                <span class="h-name-one sort_order tipText" title="Sort By Sharers" data-coloumn="total_shares" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
												<span class="h-name-one sort_order tipText" title="Sort By People" data-coloumn="total_people" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                            </div>
											<div class="loc-col opp-col opp-col-3">
                                                Breakdown
                                                <span class="h-name-one sort_order  tipText" title="Sort By Workspaces" data-coloumn="total_workspaces" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                                <span class="h-name-one sort_order  tipText" title="Sort By Tasks" data-coloumn="total_tasks" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                            </div>
											<div class="loc-col opp-col opp-col-4">
                                                Competencies Match
                                                <span class="h-name-one sort_order  tipText" title="Sort By Skills" data-coloumn="skill_match_percent" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                                <span class="h-name-one sort_order  tipText" title="Sort By Subjects" data-coloumn="subject_match_percent" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                                <span class="h-name-one sort_order  tipText" title="Sort By Domains" data-coloumn="domain_match_percent" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                            </div>
											<div class="loc-col opp-col opp-col-5">
                                                Engagements Match
                                                <span class="h-name-one sort_order  tipText" title="Sort By Projects" data-coloumn="match_project_counts" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
												
												 <span class="h-name-one sort_order  tipText" title="Sort by Tasks" data-coloumn="match_tasks_counts" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
												
                                                <span class="h-name-one sort_order  tipText" title="Sort By Absences" data-coloumn="unvailable_days" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
												
												<span class="h-name-one sort_order  tipText" title="Sort by Work Blocks" data-coloumn="block_days" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
												
                                            </div>
                                            <div class="loc-col opp-col opp-col-6">
                                                Actions
                                            </div>
                                        </div>
                                        <div class="ssd-data opp-list-wrapper list-wrapper" data-flag="true" >
										</div>
                                    </div>
                                </div>
                                <div id="tab_request" class="tab-pane fade  ssd-tabs">
							<div class="requests-tabs">
							
<div class="requests-left-sec">
<div class="requests-col-header">
   <div class="requests-col">
      <div class="opp-heading">Projects<span class="total-rows"> (2)</span>
         <span class="h-name-one sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="opp-short">
         Requests
         <span class="h-name-one sort_order active tipText" title="Sort By Start Date" data-coloumn="start_date" data-order="asc" data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
         <span class="h-name-one sort_order tipText" title="Sort By End Date" data-coloumn="end_date" data-order="" data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
         <span class="h-name-one sort_order tipText" title="Sort By End Date" data-coloumn="end_date" data-order="" data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
         </span>
      </div>
   </div>
</div>

<div class="requests-project-info">
   <div class="opp-project-details">
      <div class="opp-project-left project-yellow"> <i class="projectwhite-icon"></i></div>
      <div class="requests-project-info-inner">
         <div class="requests-project-first">
            <div class="opp-project-middle">
               <span class="opp-project-name" data-remote="/ideascast/boards/more_information/211/details" data-target="#modal_information" data-toggle="modal">29 feb - 2024</span>
               <span class="opp-project-date">29 Feb, 2024 → 21 Mar, 2024 </span>
            </div>
            <div class="opp-pss fl-icon">
               <i class="flag not_started tipText" data-original-title="Not Started"></i>
               <div class="progress-col-cont">
                  <ul class="workcount">
                     <li class="yellow tipText" title="" data-original-title="In Progress">0</li>
                     <li class="red tipText" title="" data-original-title="Overdue">0</li>
                     <li class="green-bg tipText" title="" data-original-title="Completed">0</li>
                  </ul>
               </div>
            </div>
         </div>
         <div class="requests-project-second">
            <div class="progress-col-cont">
               <div class="compet-proj-bar-col">
                  <div class="schedule-bar" data-original-title="" title="">
                     <span class="blue barTip bar-border ctip" title="" style="width:2%" data-original-title="Team Member has 0 of 0 Project Skills (0%)"></span>
                  </div>
                  <div class="proginfotext ctip" title="" data-original-title="No Project Skills">0</div>
               </div>
               <div class="compet-proj-bar-col">
                  <div class="schedule-bar" data-original-title="" title="">
                     <span class="red2 barTip bar-border ctip" title="" style="width: 10%" data-original-title="Team Member has 0 of 0 Project Subjects (0%)"></span>
                  </div>
                  <div class="proginfotext ctip" title="" data-original-title="No Project Subjects">0</div>
               </div>
               <div class="compet-proj-bar-col">
                  <div class="schedule-bar" data-original-title="" title="">
                     <span class="green-bg barTip bar-border ctip" title="" style="width:20%" data-original-title="Team Member has 0 of 0 Project Domains (0%)"></span>
                  </div>
                  <div class="proginfotext ctip" title="" data-original-title="No Project Domains">0</div>
               </div>
            </div>
            <div class="progress-col-cont">
               <ul class="workcount">
                  <li class="dark-gray tipText" title="1 Owner">1</li>
                  <li class="light-gray tipText" title="" data-original-title="0 Sharers" aria-describedby="tooltip261850">0</li>
               </ul>
               <div class="proginfotext "><span class="tipText" title="" data-original-title="Your Role">5 People</span></div>
            </div>
         </div>
      </div>
   </div>
</div>

</div>	

<div class="requests-right-sec">
<div class="requests-col-header">
   <div class="requests-col req-col-1">
      <div class="opp-heading">Popele <span> (2)</span>
         <span class="h-name-one sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-2">
      <div class="opp-heading">Competencies Match 
         <span class="h-name-one sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-3">
      <div class="opp-heading">Engagements Match 
         <span class="h-name-one sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-4">
      <div class="opp-heading">Status 
         <span class="h-name-one sort_order tipText " data-coloumn="title" data-order="" title="Sort by Title" data-type="project">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-5">
      Actions
   </div>
</div>
<div class="requests-wrap-list">
   <div class="requests-row ssd-data-row ">
      <div class="requests-col req-col-1">
         <div class="style-people-com">
            <a class="style-popple-icons" data-remote="http://192.168.7.20/ideascast/shares/show_profile/2" data-target="#popup_modal" data-toggle="modal">
            <span class="style-popple-icon-out">
            <span class="style-popple-icon">
            <img src="http://192.168.7.20/ideascast/uploads/user_images/1621250701.png" class="user-image" align="left" width="36" height="36" data-content="<div><p>Soofie Hayat </p><p>`~!@#$%^&amp;*()_-=+\|]}[{'&quot;;:/?.>,<<br>\&quot;Hello&quot;\nWorl</p></div>" data-original-title="" title="">
            </span>
            <i class="communitygray18 community-g "></i>
            </span>
            </a>
            <div class="style-people-info">
               <span class="style-people-name" data-remote="http://192.168.7.20/ideascast/shares/show_profile/2" data-target="#popup_modal" data-toggle="modal">Soofie Hayat </span>
               <span class="style-people-title">`~!@#$%^&amp;*()_-=+\|]}[{'";:/?.&gt;,&lt;&lt;br&gt;\"Hello"\nWorl</span>
            </div>
         </div>
      </div>
      <div class="requests-col req-col-2">	
         <span class="competencies-list">
         <span class="competencies-list-bg competencies-list-bg-skill tipText" title="" data-remote="/ideascast/boards/more_information/211/oppskills" data-target="#modal_information" data-toggle="modal" data-original-title="Skills" aria-describedby="tooltip998836">
         <i class="skills-icon"></i>
         <span class="sks-title">0%</span>
         </span>
         <span class="competencies-list-bg competencies-list-bg-subject tipText" title="Subjects" data-remote="/ideascast/boards/more_information/211/oppsubjects" data-target="#modal_information" data-toggle="modal">
         <i class="subjects-icon"></i>
         <span class="sks-title">0%</span>
         </span>
         <span class="competencies-list-bg competencies-list-bg-domain tipText" title="Domains" data-remote="/ideascast/boards/more_information/211/oppdomains" data-target="#modal_information" data-toggle="modal">
         <i class="domain-icon"></i>
         <span class="sks-title">0%</span>
         </span>
         </span>
      </div>
      <div class="requests-col req-col-3">
         <div class="opp-work-info-wrap">
            <div class="oppinfo-eng">
               <div class="opp-work-item"> <i class="opp-icon projectblack tipText" data-original-title="Your Project Count In Project Period"></i>0 </div>
               <div class="opp-work-item"> <i class="opp-icon taskblack tipText" data-original-title="Your Task Count In
                  Project Period"></i> 
                  0				
               </div>
            </div>
            <div class="oppinfo-match">
               <span><i class="opp-unavailableBlack tipText" data-original-title="Your Absence Count
                  In Project Period"></i> 0</span>
               <span><i class="blocksmblack18 tipText" data-original-title="Your Work Block Count
                  In Project Period"></i> 0 </span>
            </div>
         </div>
      </div>
      <div class="requests-col req-col-4 requests-status">	
         <a class="request-sent tipText" href="#" title="" data-original-title="Request: I would like to take part in your project."><i class="requestsent-icon"></i>&nbsp;15 Jun, 2021</a>
         <a  class="request-sent tipText" href="#" title="" data-original-title="Request: I would like to take part in your project."><i class="activegreen"></i>&nbsp;15 Jun, 2021</a>
      </div>
      <div class="requests-col req-col-5 requests-actions">	
         <a href="#"><i class="activegreen"></i></a>
         <a href="#"><i class="inactivered"></i></a>
         <a href="#"><i class="infoblack-icon"></i></a>
      </div>
   </div>
</div>
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
    </div>
</div>
<div class="modal modal-success fade " id="modal_information" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg  opp-profile">
        <div class="modal-content "></div>
    </div>
</div>
<div class="modal modal-success fade " id="modal_request" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content "></div>
    </div>
</div>
<script>
$(function(){
  /* TABS PAGINATION */
    $('.list-wrapper').scroll(function() {
        $('.tooltip').hide()
        var $this = $(this);
        var $parent = $this.parents('.tab-pane:first');
        clearTimeout($.data(this, 'scrollTimer'));
        $.data(this, 'scrollTimer', setTimeout(function() {
            if($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 10)  {
                $.updateOffset($this, $parent);
            }
        }, 250));
    });

	$.tab_paging_offset = $js_config.opp_offset;
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

    $.pagingFlag = true;
    $.getPagingData = function(page, wrapper, parent){
        wrapper.data('flag', false);
        var $wrapper = wrapper;
        var order = 'asc',
            coloumn = 'start_date';
        if( $('.sort_order.active', parent).length > 0 ) {
            order =  ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
            coloumn = $('.sort_order.active', parent).data('coloumn');
        }

        var type = parent.data('type');
        var search_text = $('.search-box[data-type="'+type+'"]').val();

        var data = {page: page, order: order, type: type, coloumn: coloumn, q: search_text}

        $.ajax({
            type: "POST",
            url: $js_config.base_url + 'boards/filter_data',
            data: data,
            // dataType: 'JSON',
            success: function(html) {
                $wrapper.append(html);
                wrapper.data('flag', true);
            }
         });
    }

    $.countRows = function(type, parent, searchfilter = 0) {
        var dfd = $.Deferred();

        var order = '',
            coloumn = '';
        if( $('.sort_order.active', parent).length > 0 ) {
            order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
            coloumn = $('.sort_order.active', parent).data('coloumn');

            if( order == 'asc' ){
                order = 'desc';
            } else {
                order = 'asc';
            }
        }

        var search_text = $('.search-box[data-type="'+type+'"]').val();
        if(search_text != ''){

        }
 
        var data = {page: 0, order: order, type: type, coloumn: coloumn, search_text: search_text}

        $.ajax({
            url: $js_config.base_url + 'boards/count_opp',
            data: data,
            type: 'post',
            dataType: 'JSON',
            success: function(response) {
                $('#paging_offset', parent).val(0);
                $('#paging_total', parent).val(response);
                $('.total-rows', parent).text(' ('+response+')');
                dfd.resolve('paging count');
            }
        })
        return dfd.promise();
    }
    // $.countRows('project', $('#tab_project'));
    /* TABS PAGINATION */
})
</script>