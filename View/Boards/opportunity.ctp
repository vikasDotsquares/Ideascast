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
                                    <li class="active" >
                                        <a data-toggle="tab" data-type="project" class="active competencies_tab project-tab" data-target="#tab_project" href="#tab_project" aria-expanded="true">PROJECTS</a>
                                    </li>
                                     <li >
                                        <a data-toggle="tab" data-type="request" class="competencies_tab request-tab" data-target="#tab_request" href="#tab_request" aria-expanded="false">REQUESTS</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3 right text-right">
                                <div class="input-group search-skills-box">
                                    <input type="text" class="form-control search-box" data-type="project" placeholder="Search for Projects...">
									<input type="text" class="form-control search-box" data-type="request" placeholder="Search for Projects...">
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

                                                <span class="h-name-one sort_order  tipText" title="Sort By Absences" data-coloumn="unavailable_count" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>

												<span class="h-name-one sort_order  tipText" title="Sort by Work Blocks" data-coloumn="block_count" data-order=""  data-type="project"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>

                                            </div>
                                            <div class="loc-col opp-col opp-col-6">
                                                Actions
                                            </div>
                                        </div>
                                        <div class="ssd-data opp-list-wrapper list-wrapper" data-flag="true" >
										</div>
                                    </div>
                                </div>
                                <div id="tab_request" data-type="request" class="tab-pane fade  ssd-tabs">
								<input type="hidden" name="paging_offset" id="paging_offset" value="1">
			                     <input type="hidden" name="paging_total" id="paging_total" value="0">
							<div class="requests-tabs">

<div class="requests-left-sec">
<div class="requests-col-header" >
   <div class="requests-col">
      <div class="opp-heading">Projects<span class="total-rows"> (0)</span>
         <span class="h-name-one sort_order tipText " data-coloumn="title" data-order="" title="Sort By Name" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText active " data-coloumn="start_date" data-order="asc" title="Sort By Start Date" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="end_date" data-order="" title="Sort By End Date" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order tipText " data-coloumn="project_status" data-order="" title="Sort By Schedule Status" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="opp-short">
         Requests
         <span class="h-name-one sort_order tipText" title="Sort By Accepted Requests" data-coloumn="accept_request_count" data-order="" data-type="request">
			<i class="fa fa-sort" aria-hidden="true"></i>
			<i class="fa fa-sort-asc" aria-hidden="true"></i>
			<i class="fa fa-sort-desc" aria-hidden="true"></i></span>
         <span class="h-name-one sort_order tipText" title="Sort By Declined Requests" data-coloumn="decline_request_count" data-order="" data-type="request"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
         <span class="h-name-one sort_order tipText" title="Sort By Pending Requests" data-coloumn="pending_request_count" data-order="" data-type="request"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
         </span>
      </div>
   </div>
</div>
<div class="opportunities-request-lists list-request-wrapper list-wrapper" data-flag="true" style="overflow: auto;" ></div>


</div>

<div class="requests-right-sec ">
<div class="requests-col-header" >
   <div class="requests-col req-col-1">
      <div class="opp-heading">People <span class="request_users"> (0)</span>
         <span class="h-name-one sort_order_user tipText " data-coloumn="first_name" data-order="" title="Sort By First Name" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order_user tipText " data-coloumn="last_name" data-order="" title="Sort By Last Name" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order_user tipText " data-coloumn="job_title" data-order="" title="Sort By Job Title" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-2">
      <div class="opp-heading">Competencies Match
         <span class="h-name-one sort_order_user tipText " data-coloumn="skill_match_percent" data-order="" title="Sort By Skills" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order_user tipText " data-coloumn="subject_match_percent" data-order="" title="Sort By Subjects" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order_user tipText " data-coloumn="domain_match_percent" data-order="" title="Sort By Domains" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-3">
      <div class="opp-heading">Engagements Match
         <span class="h-name-one sort_order_user tipText " data-coloumn="match_project_counts" data-order="" title="Sort By Projects" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order_user tipText " data-coloumn="match_tasks_counts" data-order="" title="Sort By Tasks" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
         <span class="sort_order_user tipText " data-coloumn="unavailable_count" data-order="" title="Sort By Absences" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
		 <span class="sort_order_user tipText " data-coloumn="block_count" data-order="" title="Sort By Work Blocks" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc" aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-4">
      <div class="opp-heading">Status
         <span class="h-name-one sort_order_user active tipText" data-coloumn="created" data-order="asc" title="Sort By Request Date" data-type="request">
         <i class="fa fa-sort" aria-hidden="true"></i>
         <i class="fa fa-sort-asc" aria-hidden="true"></i>
         <i class="fa fa-sort-desc " aria-hidden="true"></i>
         </span>
      </div>
   </div>
   <div class="requests-col req-col-5">
      Actions
   </div>
</div>
	<div class="requests-wrap-list requests-wrap-user-list" data-prjid="" >

		<div class="">
			<div class="no-summary-found">SELECT PROJECT</div>
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
<div class="modal modal-success fade" id="popup_model_box_decline" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<script>
$(function(){
$("[href='"+$js_config.tab+"']").trigger('click');
	
$("#popup_model_box_decline").on('show.bs.modal', function(event){
	$.modal_data = $(event.relatedTarget);
	$.update_row = $(event.relatedTarget).parents('.ssd-data-row:first');
})
$("#popup_model_box_decline").on('hidden.bs.modal', function(event){
	
  /* var project_id = $.modal_data.data('project');
  var sender_id = $.modal_data.data('sender');
  $.get_request_user(project_id,sender_id, $.update_row ); */
  
})
	
	
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
				if( response > 0 ){
					$(".requests-left-sec").find(".requests-col-header").show();
				}	
                $('.total-rows', parent).text(' ('+response+')');
                dfd.resolve('paging count');
            }
        })
        return dfd.promise();
    }
    // $.countRows('project', $('#tab_project'));
    /* TABS PAGINATION */

	$.countUserRows = function(project_id,type) {
        var dfd = $.Deferred();

        var order = '',
            coloumn = '';
        if( $('.sort_order_user.active').length > 0 ) {
            order = ($('.sort_order_user.active').data('order') == 'asc') ? 'desc' : 'asc',
            coloumn = $('.sort_order_user.active').data('coloumn');

            if( order == 'asc' ){
                order = 'desc';
            } else {
                order = 'asc';
            }
        }

        var data = {project_id: project_id,page: 0, order: order, type: type, coloumn: coloumn}

        $.ajax({
            url: $js_config.base_url + 'boards/count_request_project_user',
            data: data,
            type: 'post',
            dataType: 'JSON',
            success: function(response) {
				if( response > 0 ){
					//$('#paging_offset', parent).val(0);
					//$('#paging_total', parent).val(response);
					//$(".requests-col-header").show();					
					$('.request_users').text(' ('+response+')');

				} else {
					$('.request_users').text(' ('+response+')');
					//$(".requests-col-header").hide();
				}

                dfd.resolve('paging count');
            }
        })
        return dfd.promise();
    }

	$( 'body' ).on( "click", ".opp-project-name.action", function(e, req_prj) {
        $('.list-request-wrapper .opp-project-details').removeClass('active')
		$(this).parents('.list-request-wrapper .opp-project-details:first').addClass('active');
		
		$('.tooltip').remove();
		
		var project_id = $(this).data('project');
		if( req_prj && req_prj != undefined ){
			project_id = req_prj;
		}
		var data = {project_id: project_id}
		
        $.ajax({
			url: $js_config.base_url + 'boards/get_project_data',
			type: 'POST',
			dataType: 'JSON',
			data: data,
			success: function(response){

				$.countUserRows(project_id,'request');
				setTimeout(function(){
					$(".sort_order_user").data('projectid',project_id);
					$('.requests-wrap-user-list').data('prjid', project_id);
					$('.requests-wrap-user-list').html(response);
				},200);
				$('.tooltip').remove();

			}
		})
	}); 


	$('body').on('click', '.sort_order_user', function(event) {
		var $that = $(this);
		var project_id = $('.requests-wrap-user-list').data('prjid');

		order = $that.data('order') || 'asc',
		type = $that.data('type'),
		coloumn = $that.data('coloumn');
		
		var people_r_count = $(".requests-wrap-user-list").find(".requests-row").length;
		
		if( people_r_count > 0 ){
		
			if( order == 'desc' ){
				$(this).attr('data-order', 'asc');
				$that.data('order', 'asc');
			}
			else{
				$(this).attr('data-order', 'desc');
				$that.data('order', 'desc');
			}

			$(this).parents("#tab_"+type).find('.sort_order_user.active').not(this).removeClass('active');

			$that.addClass('active');
			$('.tooltip').remove();

			$.ajax({
				url: $js_config.base_url + 'boards/get_project_data',
				type: 'POST',
				dataType: 'JSON',
				data: {project_id: project_id,order: order, type: type, coloumn: coloumn},
				success: function(response){
					
					$('.requests-wrap-user-list').html(response);
					$('.tooltip').remove();

				}
			})
		}
	})



	 $( 'body' ).on( "click", ".project_request_accept", function() {
		var project_id = $(this).data('project');
		var board_id = $(this).data('projectboard');
		// var data_url =  $(this).data('redirect');
		var sender_id =  $(this).data('sender');
		var data = {project_id: project_id, board_id:board_id, sender_id:sender_id};
		
		//console.log(data);
		//return;
		
        $.ajax({
			url: $js_config.base_url + 'boards/update_activity',
			type: 'POST',
			dataType: 'JSON',
			data: data,
			success: function(response){
				if( response.success ){
					var data_url =   $js_config.base_url + 'shares/update_sharing/'+project_id+'/'+sender_id+'/2/'+response.permission+'/?refer='+$js_config.subdomain_base_url +'boards/opportunity/request/'+project_id;
					location.href = data_url;
				}
			}
		})
	})

})
</script>