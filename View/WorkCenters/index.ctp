<?php
	echo $this->Html->css('projects/work_center');
	echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));
	echo $this->Html->script('projects/work_center');


	$current_user_id = $this->Session->read('Auth.User.id');
	 $alluserarray = [];


?>
<style type="text/css">
	.right-close:focus {
	    background-color: #67a028 !important;
	    border-color: #5F9323 !important;

	}
	.fc-sat,.fc-sun{
		opacity: 0.4;
	}

	.cal-day-outmonth span[data-cal-date] {
	    opacity: 0.5 !important;
	    cursor: default;
	}

	.fc-event {
	    border: none;
	    background-color: none;
		color:#000;
	}
	.toggle_header_menus {
		display:none;
	}
	.cal-events-num {
	    font-size: 15px !important;
	    font-weight: 660 !important;
	    position: absolute;
	    bottom: 0;
	}
	.page-header {
	    border-bottom: none !important;
	}
	.notavailusers{
			text-align: center;
			padding-top:12px;

	}
	.notavailusers .nots{
		border-bottom: solid 1px #5F9323 !important;
		font-size: 17px;
		font-weight: 600;
	}
	.datestartlist{
		text-align :left;
		font-size: 13px;
	}
	.datestartlist span{
		font-weight: 550;
	}
	.dateendlist{
		text-align :left;
		margin-bottom:10px;
		font-size: 13px;
	}
	.dateendlist span{
		font-weight: 550;
	}

	.saldom {
		padding: 10px 10px 6px 14px;
		border: solid 1px #ccc;
		margin: 10px 15px 0;
		text-align: left;
	}
	.con {

		border: solid 1px #ccc;
		overflow: hidden;
		padding: 6px 0 6px 0;
		margin: 8px 0;
	}
	.noavailreason {
		text-align: left;
		font-size: 13px;
	}
	.noavailreason span {
		font-weight: 550;
	}

	.freeuserlists .show_calendar_byuser{
		width: 100%;
		border: 1px solid #ccc;
		padding: 5px;
		margin-top: 10px;
	}
	.freeuserdatereasonstatus {
		max-height: 270px;
		overflow: auto;
	}
	.me-label {
		padding-right:15px;
	}

	.cal-events-num {
		padding-left: 2px;
	}

	#cal-week-box {
	    display: none !important;
	}
	.tasktext,.tcount{
clear:both;
}

.taskborder{
	cursor:default;
	margin-left:2px;
	color: limegreen;
}

.prgtask{
	clear:both;
	margin-left:25px;
	cursor: pointer;
}
.first-block{
	bottom: 20px;
}
.second-block{
	font-size: 14px !important;
    font-weight: 660 !important;
    position: absolute;
    bottom: 0px;
    left: -24px;
}
.second-block span, .second-block i {
    min-width: 10px;
}
.second-block i {
    margin-left: 5px;
}



	/***************/
	.cal-month-day {
	    height: 70px !important;
	}
	.cal-year-box [class*="span"], .cal-month-box [class*="cal-cell"] {
	    min-height: 70px !important;
	}
	.showelementlist {
	    cursor: pointer;
	}

	/***************/
@media (max-width:1199px) {
	.cal-events-num {
    font-size: 12px !important;
	}
	.cal-month-box .cal-day-today span[data-cal-date]{
		margin-top: 4px;
		font-size: 1.5em;
	}
	.cal-cell span[data-cal-date] {
		margin-right: 10px;
		margin-top:10px;
	}
	}

	@media (max-width:767px) {

	.work-element-info-col .task-info-box {
			width: 41.66666667%;
		float: left;
	}
	.work-element-info-col .task-date-box {
			width: 58.33333333%;
		float: left
	}
	.work-element-info-col  .task-date-box .info-box-content {
		text-align: right;
	}
	}
	@media (max-width:540px) {
	.cal-events-num .t-title {
		display: none;
	}
	.work-element-info-col .task-info-box, .work-element-info-col .task-date-box {
		width: 100%;
		float: none;
	}
	.work-element-info-col  .task-date-box .info-box-content {
		text-align: left;
	}
	.modal-tasks-title {
			font-size: 20px;
		}
	}
	@media (max-width:479px) {
		.cal-cell span[data-cal-date] {
		    font-size: 1em;
		}
		.cal-events-num {
		    font-size: 9px !important;
		}
		.cal-month-box .cal-day-today span[data-cal-date] {
			font-size: 1.4em !important;
			margin-right: 7px;
		}
		.cal-year-box [class*="span"], .cal-month-box [class*="cal-cell"] {
			min-height: 75px !important;
		}
		.cal-month-day {
			height: 75px !important;
		}
		.modal-tasks-title {
			font-size: 15px;
		}
	}
@media (max-width:400px) {
	.cal-cell span.taskborder {
	display: none;
}
.second-block {
	font-size: 14px !important;
}
.work-element-info-col .info-box-content .startdate {
	margin-bottom: 2px;
}

	}


</style>
<script type="text/javascript">
	$(function(){
		$('#modal_small').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		});
	})
</script>
<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-success fade" id="events-element-modal">
        <div class="modal-dialog">
            <div class="modal-content">

            </div>
        </div>
    </div>

<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time" style=" ">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
			</section>
		</div>
		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">

                    <div class="box noborder margin-top" style="border-radius: 0px 0px 3px 3px;">
                        <div class="box-header filter-header border formdatacontrol col-sm-custom-wrap" style="">
						<!-- MODAL BOX WINDOW -->
						<div class="modal modal-success fade" id="project_free_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content"></div>
							</div>
						</div>
							<div class="modal modal-success fade" id="model_scenario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="model_bx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

                            <div class="col-sm-custom">
                            	<!-- <input id="mychk" type="checkbox" checked="" class="gs_input"><label for="mychk" class="gs_label">My</label> -->
									<div class="input-group my-select select-user">
									  <span class="input-group-addon">
										<?php /* <div class="my-data-chk"><input id="myc" type="checkbox" checked  />My</div> */?>
										<input id="myc" type="checkbox" checked disabled="disabled"  />&nbsp;<label style="position:relative; top :-1px;margin:0;">My</label>
									  </span>
									  <label class="custom-dropdown" style="width:100%; float:left;">
										  <select class="form-control aqua" name="owner_users" id="project_owner_users">
											<option value="<?php echo $current_user_id; ?>">Select User</option>
											<?php if( isset($alluserslist) && !empty($alluserslist) ){
											foreach( $alluserslist as $listusers ){
											?>
											<option value="<?php echo $listusers['UserDetail']['user_id']; ?>" data-ownerid="<?php echo $listusers['UserDetail']['user_id']; ?>"><?php echo $listusers['UserDetail']['full_name']; ?>
											</option>
											<?php }
											}?>
										 </select>
									  </label>
									</div>
                            </div>
							<div class="col-sm-custom">
                            	<label class="project-dates" style="width:100%;">
									<div class="input-group">
										 <input type="text" class="datepick form-control" readonly="" style="opacity: 2; width: 100%; margin: 0; font-weight: normal; " id="projectdates" >

										<div class="input-group-addon cross" style="border-color: #00c0ef; border-left: none; display:none;">
											<span class="input-group-text ">
												<i class="fa fa-times text-red cross"></i>
											</span>
										</div>

										<div class="input-group-addon calendar_trigger" id="calendar-triggerss" style="border-top: 1px solid #00c0ef; border-bottom: 1px solid #00c0ef; border-right: 1px solid #00c0ef; font-size:15px;position: relative;">
											 <span class="fa-stack fa-xs" style="font-size:12px;">
												<i class="fa fa-calendar" style="font-size: 23px; margin-right: -2px;"></i>
												<?php /*<i class="fa fa-calendar-o fa-stack-2x"></i>
												<i class="fa fa-times fa-stack-1x" style="top: 3px; font-size: 14px;"></i>*/ ?>
											</span>
										</div>

									</div>
								</label>
                            </div>
							<div class="col-sm-custom">
                            	<label class="custom-dropdown" style="width:100%;">
									<select class="form-control aqua" name="user_projects" id="owner_user_projects">
										<option value="">Select a Project</option>
										<?php if( isset($projects) && !empty($projects) ){
											foreach($projects as $key => $myProjectlist){
										?>
										<option value="<?php echo $key?>"><?php echo $myProjectlist; ?></option>
										<?php }
										}
										?>
									</select>
								</label>
                            </div>
                            <div class="col-sm-custom-but">

								<div class="s-buttons form-group pull-right ipad-button-filter" style="margin-bottom: 0; padding: 2px 15px">
								    <a href="#" class="btn btn-success btn-sm right-close tipText" title="Project Workload" data-target="#model_scenario" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "work_centers", "action" => "project_scenario", 'admin' => FALSE ), true ); ?>">
								    	<i class="icon_scenraio" aria-hidden="true"></i>
								    </a>
									<a href="#" id="reset_filters" class="btn btn-danger btn-sm" style=" ">Reset</a>
									<!-- <a href="#" id="submit_filter" class="btn btn-success btn-sm  " style=" ">Apply Filter</a> -->
								</div>
                            </div>
                        </div>
                        <div class="box-body clearfix list-shares" id="work-center-task-list">
							<p class="loading-bar"></p>
							<div class="work-center-task"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    $(function() {

		$('#model_scenario').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		});

		$('#project_free_user_modal').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
		});

		$("body").on('mouseover',
			function(event){
				event.preventDefault();
				if(!$(event.target).is('.show_calendar') && $(event.target).parents('.show_calendar').length <= 0){
					$(".show_calendar").tooltip('hide');
				}
			}
		)
		$("body").on('mouseenter', ".show_calendar",
			function(event){
				event.preventDefault();
				$(".show_calendar").not(this).tooltip('hide')
				$(this).tooltip('show');
			}
		)
		$("body").on('mouseleave', ".show_calendar",
			function(event){
				event.preventDefault();
				$(".show_calendar").tooltip('hide')
			}
		)
		var calendar_click = true;
		$('body').on("click", ".show_calendar", function(event, value) {

			$("#ajax_overlay").show();
			var $thiss = $(this);
			var $that = $thiss.parents(".list-panel");
		//if(calendar_click  ) {
			//calendar_click = false;

			$(".show_calendar").not(this).tooltip('hide');

			if( value != 'triggerfunction' ){
				$that.find('.work-project-info-tab').toggle();
				$that.find('.work-project-info-tab-calendar').toggle();

				$thiss.tooltip('hide').attr('data-original-title', 'Project Availability');

				if( $thiss.data('calname') == 'prjavail'  ){

					$thiss.data('calname','prjtasks');
					$thiss.tooltip('hide').attr('data-original-title', 'Project Tasks');
					setTimeout(function(){
						$thiss.tooltip('show');
					},200);


				} else {

					$thiss.data('calname','prjavail');
					$thiss.tooltip('hide').attr('data-original-title', 'Project Availability');
					setTimeout(function(){
						$thiss.tooltip('show');
					},200);

				} }

			$thiss.parents('.list-panel').find('.notavailmyself').prop("checked", true);
			$thiss.parents('.list-panel').find('.show_calendar_byuser').remove();

			//alert($thiss.parents('.list-panel').find('.notavailmyself:checked').length);

			if( $thiss.parents('.list-panel').find('.notavailmyself:checked').length == 0 ){
				//$thiss.parents('.list-panel').find('.freeuserdatereasonstatus').html('');
			}

			var eleids = $(this).data('eleids');
			var listdata = $(this).data('listdata');
			//var userid = $(this).data('userid');
			var userid = ($('#project_owner_users').val())? $('#project_owner_users').val() : $(this).data('userid');

				var options = {
						events_source:  $js_config.base_url + 'work_centers/taskcalendar/',
						view: 'month',
						tmpl_path: SITEURL + 'twitter-cal/tmpls/',
						tmpl_cache: false,
						views: {
								year: {
									slide_events: 1,
									enable: 1
								},
								month: {
									slide_events: 1,
									enable: 1
								},
								week: {
									enable: 1
								},
								day: {
									enable: 0
								}
						},
						datas: {
							id: eleids,
							// datelists : listdata,
							userid : userid,
							color_codex:"#ff0000"
						},
						onAfterEventsLoad: function (events) {
							if (!events) {
								return;
							}
							var list = $('#eventlist');
							list.html('');

							$.each(events, function (key, val) {
								$(document.createElement('li'))
										.html('<a href="' + val.url + '">' + val.title + '</a>')
										.appendTo(list);
							});
						},
						onAfterViewLoad: function (view) {
							$thiss.parents('.list-panel').find('.page-header h3').text(this.getTitle());
							$thiss.parents('.list-panel').find('.btn-group button').removeClass('active');
							$thiss.parents('.list-panel').find('button[data-calendar-view="' + view + '"]').addClass('active');

								$('.cal-month-day').each(function(){
									var $that = $(this);
									var $tod = $that.find('span:first').data('calDate');
									var cc = $that.find('.events-list a').length;
									var ElmIDs = $that.find('.events-list a').map(function() {
										return $(this).data('eventId');
									}).get().join(',');
								});
								//var user_id = ($thiss.data('userid'))? $thiss.data('userid') : $('#project_owner_users').val();

								var user_id = ($('#project_owner_users').val())? $('#project_owner_users').val() : $thiss.data('userid');

								var user_n = $("#project_owner_users option:selected").text();

								$thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html('');
								if($('.show_calendar_byuser',$thiss.parents('.list-panel')).length <= 0){

								$.ajax({
									type: 'POST',
									data: $.param({ 'user_id':user_id ,'pid': $thiss.data('pid') }),
									url: $js_config.base_url + 'work_centers/usernotavaildateswithstatus',
									global: false,
									dataType:'json',
									success: function(response) {
										$thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html('');
										var memberExtraHtml = '';
										if( response.success ){

											if( response.userdates ){
												calendar_click = true;
												var resultdates = response.userdates;
												var stdate = 'N/A';
												var endate = 'N/A';

												$.each(resultdates , function (index,value){

													var firstdate = '';
													var lastdate = '';
													var fullday = '';
													var lastfullday = '';

													if( isDate(value.Availability.avail_start_date) ){

														firstdate = value.Availability.avail_start_date.split(" ");
														if ( firstdate[1] == '00:00:00' ){
															fullday = '';
															stdate = moment(value.Availability.avail_start_date).format('DD MMM YYYY')+" "+fullday;
														} else {
															stdate = moment(value.Availability.avail_start_date).format('DD MMM YYYY h:mm a');
														}

													}
													if( isDate(value.Availability.avail_end_date) ){

														lastdate = value.Availability.avail_end_date.split(" ");
														if ( lastdate[1] == '00:00:00' ){
															lastfullday = '';
															endate = moment(value.Availability.avail_end_date).format('DD MMM YYYY')+" "+lastfullday;
														} else {
															endate = moment(value.Availability.avail_end_date).format('DD MMM YYYY h:mm a');
														}
													}

													memberExtraHtml += '<div class="con" ><div class="col-sm-12">';

													if( stdate != 'N/A' ){
														memberExtraHtml += '<div class="datestartlist"><span>Start:</span> ' + stdate + '</div>';
													}
													if( endate != 'N/A' ){
														memberExtraHtml += '<div class="dateendlist"><span>End:</span> ' + endate + '</div>';
													}
													if( stdate != 'N/A' && endate != 'N/A' ){
														if( value.Availability.avail_reason.length > 0 ){
															memberExtraHtml += '<div class="noavailreason"><span>Reason:</span> ' + value.Availability.avail_reason + '</div>';
														} else {
															memberExtraHtml += '<div class="noavailreason  "><span>Reason:</span> N/A</div>';
														}
													} else {
														memberExtraHtml += '<div class="noavailreason">None Scheduled</div>';
													}
													memberExtraHtml += '</div></div>';

												});

												$thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html(memberExtraHtml);
											}



											if(user_id != $js_config.USER.id  ){
											//$thiss.parents('.list-panel:first').find('.notavailusers .memberid').trigger('click');
											$thiss.parents('.list-panel:first').find('.saldom').html('').html(user_n);
											setTimeout(function(){
											//	$thiss.parents('.list-panel:first').find('.show_calendar_byuser').val(user_id).trigger('change');

											},300)

											}

											$thiss.parents('.list-panel:first').find('.saldom').show();

										} else {


												if(user_id != $js_config.USER.id  ){
												//$thiss.parents('.list-panel:first').find('.notavailusers .memberid').trigger('click');
													$thiss.parents('.list-panel:first').find('.saldom').html('').html(user_n);


												}

												$thiss.parents('.list-panel:first').find('.saldom').show();

												memberExtraHtml += '<div class="con" ><div class="col-sm-12">';
												memberExtraHtml += '<div class="noavailreason  ">None Scheduled</div>';
												memberExtraHtml += '</div></div>';
												$thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html(memberExtraHtml);

										}

										$("#ajax_overlay").hide();
									}
								});
							}

						},
						classes: {
							months: {
								general: 'label'
							}
						}
					};


				var calendar = $thiss.parents('.list-panel').find('.calendars').calendar(options);
				//$thiss.parents('.list-panel').find('.calendars').calendar();
				// console.log($thiss.parents('.list-panel').find('.calendars'));

					$thiss.parents('.list-panel').find('.btn-group button[data-calendar-nav]').each(function () {
						var $this = $(this);
						$this.click(function () { console.log(0);
							calendar.navigate($this.data('calendar-nav'));
						});
					});

					$thiss.parents('.list-panel').find('.btn-group button[data-calendar-view]').each(function () {
						var $this = $(this);
						$this.click(function () {
							//calendar.view($this.data('calendar-view'));
						});
					});

					$('#first_day').change(function () {
						var value = $(this).val();
						value = value.length ? parseInt(value) : null;
						calendar.setOptions({first_day: value});
						calendar.view();
					});

					$('#language').change(function () {
						calendar.setLanguage($(this).val());
						calendar.view();
					});


			//	}

			})

		$('body').on('click', '[data-calendar-nav]', function(event){
			event.preventDefault();
			console.log(3);
			if($(this).parents('.panel-body:first').find('.work-project-info-tab-calendar .row.work-project-info-mainrow > div:not(.notavailusers)').height() > 410) {
				$(this).parents('.panel-body:first').find('.freeuserdatereasonstatus').css('max-height', '370px');
			}
			else{
				$(this).parents('.panel-body:first').find('.freeuserdatereasonstatus').css('max-height', '270px');
			}
		})


		$("body").on("change", ".show_calendar_byuser", function(event) {

			var $thiss = $(this);
			var $that = $(this).parents(".list-panel");
			var user_id = ($thiss.val())? $thiss.val() : '';
			var projectids = $(this).data('projectid');
			var start_date = $(this).data('pstartdate');
			var end_date = $(this).data('penddate');
			var memberHtml = '';
			var memberExtraHtml = '';
			$("#ajax_overlay").show();

				if( user_id ){

				var eleids = $thiss.parents('.list-panel').find('.show_calendar').data('eleids');
				if( projectids.length <= 0 ){
					projectids = $thiss.parents('.list-panel').find('.show_calendar').data('pid');
				}


				var options = {

					events_source:  $js_config.base_url + 'work_centers/taskcalendar/',
					view: 'month',
					tmpl_path: SITEURL + 'twitter-cal/tmpls/',
					tmpl_cache: false,
					views: {
							year: {
								slide_events: 1,
								enable: 1
							},
							month: {
								slide_events: 1,
								enable: 1
							},
							week: {
								enable: 0
							},
							day: {
								enable: 0
							}
					},
					strings:{
						week:false
					},
					datas: {
						id: eleids,
						datelists : eleids,
						userid:user_id
					},
					onAfterEventsLoad: function (events) {
						if (!events) {
							return;
						}
						var list = $('#eventlist');
						list.html('');

						$.each(events, function (key, val) {
							$(document.createElement('li'))
									.html('<a href="' + val.url + '">' + val.title + '</a>')
									.appendTo(list);
						});
					},
					onAfterViewLoad: function (view) {

						$thiss.parents('.list-panel').find('.page-header h3').text(this.getTitle());
						$thiss.parents('.list-panel').find('.btn-group button').removeClass('active');
						$thiss.parents('.list-panel').find('button[data-calendar-view="' + view + '"]').addClass('active');


							$('.cal-month-day').each(function(){
								var $that = $(this);
								var $tod = $that.find('span:first').data('calDate');
								var cc = $that.find('.events-list a').length;
								var ElmIDs = $that.find('.events-list a').map(function() {
									return $(this).data('eventId');
								}).get().join(',');

							});


							if($('.show_calendar_byuser',$thiss.parents('.list-panel')).length > 0){
							$.ajax({
								type: 'POST',
								data: $.param({'user_id':user_id, 'pid':projectids }),
								url: $js_config.base_url + 'work_centers/usernotavaildateswithstatus',
								global: false,
								dataType:'json',
								success: function(response) {
									var memberExtraHtml = '';
									var nomemberExtraHtml = '';
									if( response.success ){
										if( response.userdates ){
											var resultdates = response.userdates;
											var stdate = 'N/A';
											var endate = 'N/A';

											$.each(resultdates , function (index,value){

												var firstdate = '';
												var lastdate = '';
												var fullday = '';
												var lastfullday = '';

												if( isDate(value.Availability.avail_start_date) ){

													firstdate = value.Availability.avail_start_date.split(" ");
													if ( firstdate[1] == '00:00:00' ){
														fullday = '';
														stdate = moment(value.Availability.avail_start_date).format('DD MMM YYYY')+" "+fullday;
													} else {
														stdate = moment(value.Availability.avail_start_date).format('DD MMM YYYY h:mm a');
													}

												}
												if( isDate(value.Availability.avail_end_date) ) {

													lastdate = value.Availability.avail_end_date.split(" ");
													if ( lastdate[1] == '00:00:00' ){
														lastfullday = '';
														endate = moment(value.Availability.avail_end_date).format('DD MMM YYYY')+" "+lastfullday;
													} else {
														endate = moment(value.Availability.avail_end_date).format('DD MMM YYYY h:mm a');
													}
												}

												memberExtraHtml += '<div class="con" ><div class="col-sm-12">';
												if( stdate != 'N/A' ){
													memberExtraHtml += '<div class="datestartlist"><span>Start:</span> ' + stdate + '</div>';
												}
												if( endate != 'N/A' ){
													memberExtraHtml += '<div class="dateendlist"><span>End:</span> ' + endate + '</div>';
												}
												if( stdate != 'N/A' && endate != 'N/A' ){
													if( value.Availability.avail_reason.length > 0 ){
														memberExtraHtml += '<div class="noavailreason"><span>Reason:</span> ' + value.Availability.avail_reason + '</div>';
													} else {
														memberExtraHtml += '<div class="noavailreason"><span>Reason:</span> N/A</div>';
													}
												} else {
													memberExtraHtml += '<div class="noavailreason">None Scheduled</div>';
												}
												memberExtraHtml += '</div></div>';

											});
											// $thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html(memberExtraHtml);
											$thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html('').html(memberExtraHtml);
										}

									} else {


											nomemberExtraHtml += '<div class="con" ><div class="col-sm-12">';
											nomemberExtraHtml += '<div class="noavailreason  ">None Scheduled</div>';
											nomemberExtraHtml += '</div></div>';

											// $thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html(nomemberExtraHtml);
											$thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html(nomemberExtraHtml);

									}
									$("#ajax_overlay").hide();

								}
						});
						}

					},
					classes: {
						months: {
							general: 'label'
						}
					}
				};

				var calendar = $thiss.parents('.list-panel').find('.calendars').calendar(options);

					$thiss.parents('.list-panel').find('.btn-group button[data-calendar-nav]').each(function () {
						var $this = $(this);
						$this.click(function () {
							calendar.navigate($this.data('calendar-nav'));
						});
					});

					$thiss.parents('.list-panel').find('.btn-group button[data-calendar-view]').each(function () {
						var $this = $(this);
						$this.click(function () {
							calendar.view($this.data('calendar-view'));
						});
					});

					$('#first_day').change(function () {
						var value = $(this).val();
						value = value.length ? parseInt(value) : null;
						calendar.setOptions({first_day: value});
						calendar.view();
					});

					$('#language').change(function () {
						calendar.setLanguage($(this).val());
						calendar.view();
					});
				} else {


					$thiss.parents('.list-panel:first').find('.freeuserdatereasonstatus').html('');
					$("#ajax_overlay").hide();

				}

		})


		$("body").on("change", "#projectpeople", function(event) {
			var $thiss = $(this);
			var user_id = $thiss.val();

			var projectid = $thiss.data('projectid');
			var projectlevel = $thiss.find(':selected').attr('data-plevel');
			var elementids = $thiss.find(':selected').attr('data-ids');
			var dates = $thiss.data('dates');
			/* 	var pstartdate = userdata.data('pstartdate');
			var penddate = userdata.data('penddate'); */

			$.ajax({
				type: 'POST',
				data: $.param({ 'user_id': user_id, 'project_id':projectid,'projectlevel':projectlevel,'dates':dates  }),
				url: $js_config.base_url + 'work_centers/userprojectelements',
				global: false,
				success: function(response) {
					$('.elementlists').html(response);
				}
			});
		});
		//
		$("body").on("click", ".program-title", function(event) {

			var pid = $(this).data('pid');
			var project_url = $js_config.base_url + 'projects/index/'+pid;
			location.href = project_url;


		});

	});

	function isDate(val) {
		var d = new Date(val);
		return !isNaN(d.valueOf());
	}
</script>
<?php /* data-target="#model_scenario" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "work_centers", "action" => "project_scenario", 'admin' => FALSE ), true ); ?>" */ ?>