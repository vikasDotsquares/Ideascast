<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

echo $this->Html->css('projects/task_center');
echo $this->Html->script('projects/task_centers');

echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));

echo $this->Html->script('projects/plugins/owl/owl.carousel', array('inline' => true));
echo $this->Html->css('projects/owl/owl.carousel', array('inline' => true));

//$this->Permission->currentUserTasks();

?>
<style type="text/css">
    .no-scroll {
        overflow: hidden;
    }
    .box-body.clearfix {
        overflow-x: hidden;
        overflow-y: auto;
    }
</style>
<script type="text/javascript">
	$(function(){

        // RESIZE MAIN FRAME
        $('html').addClass('no-scroll');
        ;($.adjust_resize = function(){
            $(".box-body.clearfix").animate({
                minHeight: (($(window).height() - $(".box-body.clearfix").offset().top) ) - 17,
                maxHeight: (($(window).height() - $(".box-body.clearfix").offset().top) ) - 17
            }, 1, () => {
                // RESIZE LIST
                $('.projects-list').animate({
                    maxHeight: (($(window).height() - $('.projects-list').offset().top) ) - 37
                }, 1)
                $('.filtered_data').animate({
                    maxHeight: (($(window).height() - $('.filtered_data').offset().top) ) - 37
                }, 1)
            });
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
            $.adjust_resize();
            const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
            setTimeout( () => clearInterval(fix), 1500);
        })

        // RESIZE FRAME ON WINDOW RESIZE EVENT
        $(window).resize(function() {
            $.adjust_resize();
        })


	    $('#modal_small, #modal_large').on('hidden.bs.modal', function () {
	    	$(this).removeData('bs.modal');
	    	$(this).find('.modal-content').html('');
	    	// $.reminder_settings();
	    });




	$.pageCountUpdate = function(outerPane){

		var project = outerPane.data('project');
        var current_page = parseInt($('#paging_page_'+project).val());
        var max_page = parseInt($('#paging_max_page_'+project).val());
        var last_page = Math.ceil(max_page/10);

		if( current_page < last_page - 1 && $.loading_data ){
			current_page++;
           $('#paging_page_'+project).val(current_page);
           offset = ( parseInt($('#paging_page_'+project).val()) * 10);
           $.getPosts(offset, outerPane, current_page);
		   // $.pagingData(1);
        }

    }

    $.getPosts = function(offset, outerPane, page){

			$.loading_data = false;
			//var outerPane = $('.projects-line');
			$('#loading').remove();
			//var $selOpt = $("#mainmenu a.list-group-item.selected"),
			//sel_data = $selOpt.data(),
			var project = outerPane.data('project');
			var total_count = outerPane.find('input[name="total_count"]');
			// console.log('total_count',total_count)
			var user_id = $("#users_list").val();
			var task_status = [];
			$("input:checked", $('#status-dropdown')).each(function(){
				task_status.push($(this).val());
			});
			var ass_status = $("#assign_status_dropdown").find('.sel_assign').data('status');
            var assigned_user_id = new Array();
            assigned_user_id = $('#assigned_users_list').val();

			var startDateSorting = '';
			if( $(".start_date_sort.sort").hasClass("usedSort") ){
				startDateSorting = $(".start_date_sort.sort.usedSort").data('type');
			}

			var endDateSorting = '';
			if( $(".end_date_sort.sort").hasClass("usedSort") ){
				endDateSorting = $(".end_date_sort.sort.usedSort").data('type');
			}

			var previousDiv = outerPane.prev('.line-header:first');

			var element_sorting = '';
			var wsp_sorting = '';
			var assign_sorting = '';
			if( $(".alphabetical").data() && $(".alphabetical").data('sorted') != null && $(".alphabetical").hasClass('alphaSelected') ) {

				if( $(".alphabetical").data('sorted') == 'desc' ){
					element_sorting = 'asc';
				} else {
					element_sorting = 'desc';
				}

			} else if( $(".wsp_alphabetical").data() && $(".wsp_alphabetical").data('sorted') != null && $(".wsp_alphabetical").hasClass('alphaSelected') ) {

				if( $(".wsp_alphabetical").data('sorted') == 'desc' ){
					wsp_sorting = 'asc';
				} else {
					wsp_sorting = 'desc';
				}


			} else if( $(".assign_alphabetical").data() && $(".assign_alphabetical").data('sorted') != null && $(".assign_alphabetical").hasClass('alphaSelected')) {

				if( $(".assign_alphabetical").data('sorted') == 'desc' ){
					assign_sorting = 'asc';
				} else {
					assign_sorting = 'desc';
				}

			}


			var selectedDates = $.trim($(".selected_dates").text());
			var element_text = $("input[name='task_search']").val();

			var ele_task_type = [];
			ele_task_type = $('input[name="eletasktype"]:checked', previousDiv).map(function()
			{
				return this.value;
			}).get();

			//console.log("set data start", ele_task_type);

			var data = {
				page: offset,
				project: project,
				user_id:user_id,
				assigned_userid:[assigned_user_id],
				assigned_status:ass_status,
				task_status:task_status,
				dateStartSort_type: startDateSorting,
				dateEndSort_type: endDateSorting,
				assign_sorting:assign_sorting,
				wsp_sorting:wsp_sorting,
				element_sorting:element_sorting,
				selectedDates: selectedDates,
				element_title:element_text,
				eletasktype:ele_task_type,
				next_page: page+1
			}

			var $icon = $('.user-check.selected-user');

			if($icon.length > 0) {
				var user_id = $icon.parent().data('user');
				data.user_id = user_id;
			}

			$.ajax({
				type: "POST",
				url: $js_config.base_url + "Dashboards/get_paging_taskcenter",
				data: data,
				dataType: 'JSON',
				beforeSend: function(){
					outerPane.append('<div class="loader_bar" style="bottom:10px;" id="loading"></div>');
				},
				complete: function(){
					$('#loading').remove();
				},
				success: function(html) {

					outerPane.append(html);
					setTimeout(function(){
						/* outerPane.animate({
							scrollTop: outerPane.innerHeight()-50
						}, 300) */
						// outerPane.scrollTop(outerPane.innerHeight()-50)
					}, 500)
						$.loading_data = true;


				}
			 });

    }

})
</script>
<style type="text/css">
	/*.sel-filter i {
		display: none;
	}*/
	.ui-datepicker {
		z-index: 20;
	}
	.tasks-ipad {
		display: none;
	}
	.owl-carousel.owl-loaded {
    display: flow-root ;
	}
	.reset-jai-filters {
	    float: right;
	}

	.open ul.dropdown-menu>li:nth-child(2){
		/* padding-top:0; */
	}

	.multiselect.dropdown-toggle.btn.btn-default.aqua{
		background-color: #fff !important;
	}
	.multiselect-container>li>a>label {
	    padding: 3px 20px 3px 20px;
	    display: block;
	}

	.assigned_users_dd .multiselect-container.dropdown-menu li:nth-child(2) a span.span_profile {
		display: none;
	}

	@media (min-width:768px) and (max-width:1199px) {
		.tasks-ipad{
			display:inline;
		}
	}
	@media (max-width:768px) {
		.reset-jai-filters {
		    margin: 5px 0px 3px 0px;
		}
	}
	@media (max-width:667px) {
		.reset-jai-filters {
		    margin: 5px 0px 3px 0px;
		    float: left;
		}
	}

	.user-name-n {
		padding-left: 8px;
		flex-grow: 1;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
		text-align:left;
	}
	.data-block .thumb {
		width: 95%;
	}
	.project_select{
		font-size: 11px;
		margin: 4px 0 0 4px;
		position: absolute;
		font-weight:600;
	}
	.top-cols .btn-sm {
		padding: 3px 10px 4px 10px;
		font-size:14px ;
	}

	#select_project{
		margin-top:6px;
	}
	.task-search i.fa-times{
		padding: 0 2px;
	}

	/* .close {
		 float: inherit;
		 font-size:inherit;
		 font-weight: inherit;
		  line-height: inherit;
		  color: inherit;

		filter: inherit
		opacity: inherit;
	} */
	.projects-list span.btn-xs,.projects-list span.clear_projects {
		padding: 2px 5px;
	}

	#el-status-dd .dropdown-toggle span {
		font-size: 12px;
	}

	.list-group .pull-right{
		margin-top:-1px !important;
	}

</style>
<div class="row">
    <div class="col-xs-12">
		<div class="row">
	       <section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
	       </section>
		</div>

     	<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margin-top">
						<div class="box-header filters" style="">
							<!-- Modal Boxes -->
							<div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog ">
									<div class="modal-content"></div>
								</div>
							</div>
							<!-- /.modal -->
						<?php
							$all_projects = true;
							$all_elements = null;
							$element_keys = null;
							$allusers = null;
							$total_projects = 0;
							$total_elements = 0;
						 ?>

							<div class="col-sm-12 top-cols ">
								<div class="col-sm-6 col-md-4 col-lg-4 user_select " >
								    <span class=" tipText" style="padding:3px 0;" title="Shared With Team Member" >

									<select id="users_list"  name="users_list" class="hidden " placeholder="Select Users"  >
										<?php
										$ascUser = [];

										$ascUser = $this->ViewModel->projectTotalUsers($this->Session->read("Auth.User.id"));

										foreach($ascUser as $id => $user){
											$user_id = $user['user_details']['user_id'];
											if($user_id == 'N/A') continue;

											$username = $user[0]['fullname'];
											$current = ($user_id == $this->Session->read("Auth.User.id")) ? 'selected="selected"' : '';

										?>
											<option value="<?php echo $user_id;?>" <?php echo $current; ?>>
												<?php echo $username;?>
											</option>
										<?php } ?>
									</select>
									</span>
									<a href="javascript:void(0);" class="btn btn-danger btn-sm clear-all tipText" title="Clear Users" style="line-height: 1.8;">
									    <i class="fa fa-times "></i>
									</a>
								</div>

								<div class="col-sm-6 col-md-4 col-lg-4 user_select assigned_users_dd  "  >
								     <span class=" tipText" style="padding:3px 0;" title="Assigned to Team Member" >
									<select id="assigned_users_list" name="assigned_users_list" class="hidden" placeholder="Select Users"  >
									</select>
									</span>
									<a href="javascript:void(0);" class="btn btn-danger btn-sm assigned-clear-all tipText" title="Clear Users" style="line-height: 1.8;">
									    <i class="fa fa-times "></i>
									</a>
								</div>

								<div class="col-sm-12 col-md-4 col-lg-4" style="padding-right: 0;">
								    <div class="taskcentersearch">
								    	<div class="input-group">
						                    <input type="text" class="form-control " placeholder="Task Search" name="task_search" onkeyup="if ((event.keyCode === 13) || (this.value.length===0)) { $.findsearch() };">
						                    <span class="input-group-btn">
						                      	<button class="btn btn-success btn-flat task-search tipText" data-original-title="Search" type="button">
						                      		<i class="fa fa-search"></i>
						                      	</button>
						                    </span>


					                  	</div>
                                        <button class="btn btn-danger task-reset" type="button">
						                      		Reset
						                      	</button>
								    </div>
								</div>
							</div>
						</div>

						<?php
							// $taskStatusCount = $this->ViewModel->taskStatusCount($this->Session->read("Auth.User.id",'',$named_params));
							$taskStatusCount = $this->ViewModel->taskStatusCount($this->Session->read("Auth.User.id"));

							/* if( isset($named_params) && !empty($named_params) ){
							$non = 0;
							$pnd = 0;
							$prg = 0;
							$ovd = 0;
							$cmp = 0;
							} else { */
							$non = $taskStatusCount[0][0]['NON'];
							$pnd = $taskStatusCount[0][0]['PND'];
							$prg = $taskStatusCount[0][0]['PRG'];
							$ovd = $taskStatusCount[0][0]['OVD'];
							$cmp = $taskStatusCount[0][0]['CMP'];
							//}
						?>
						<div class="box-body clearfix" style="min-height: 800px">
							<div class="right-panel">
								<div class="col-sm-12 col-md-3 col-lg-3 projects-icons" style="">
									<span class="text-center task-status">
										<ul class="list-unstyled">
											 <li class="bg-non tipText taskstatus" title="Not Set" data-text="NON" data-taskcnt="<?php echo isset($non) && !empty($non) ? $non : 0;?>" >
												  <span class="label">NON</span>
												  <span class="btn btn-xs bg-undefined tipText " ><?php echo isset($non) && !empty($non) ? $non : 0;?></span>
											 </li>
											 <li class="bg-pnd tipText taskstatus" title="Not Started" data-text="PND" data-taskcnt="<?php echo isset($pnd) && !empty($pnd) ? $pnd : 0;?>">
												  <span class="label">PND</span>
												  <span class="btn btn-xs bg-not_started tipText " ><?php echo isset($pnd) && !empty($pnd) ? $pnd : 0;?></span>
											 </li>
											 <li class="bg-prg tipText taskstatus" title="In Progress" data-text="PRG" data-taskcnt="<?php echo isset($prg) && !empty($prg) ? $prg : 0;?>">
												  <span class="label">PRG</span>
												  <span class="btn btn-xs bg-progressing tipText "><?php echo isset($prg) && !empty($prg) ? $prg : 0;?></span>
											 </li>
											 <li class="bg-ovd tipText taskstatus" title="Overdue" data-text="OVD" data-taskcnt="<?php echo isset($ovd) && !empty($ovd) ? $ovd : 0;?>">
												  <span class="label">OVD</span>
												  <span class="btn btn-xs bg-overdue tipText "><?php echo isset($ovd) && !empty($ovd) ? $ovd : 0;?></span>
											 </li>
											 <li class="bg-cmp tipText taskstatus" title="Completed" data-text="CMP" data-taskcnt="<?php echo isset($cmp) && !empty($cmp) ? $cmp : 0;?>">
												  <span class="label">CMP</span>
												  <span class="btn btn-xs bg-completed tipText " ><?php echo isset($cmp) && !empty($cmp) ? $cmp : 0;?></span>
											 </li>
										</ul>
									</span>
									<div class="projects-list">
									<div class="tt"></div>
									<?php
									if( $all_projects ) {
//pr($program_projects);

$named_params = (isset($this->params['named']['status']) && !empty($this->params['named']['status'])) ? $this->params['named']['status'] : 0;

		//$viewData['assigned'] = (isset($this->params['named']['assigned']) && !empty($this->params['named']['assigned'])) ? $this->params['named']['assigned'] : 0;


echo $this->element('../Dashboards/task_center/filter_projectss', array('filter_users' => [$this->Session->read("Auth.User.id")], 'allprojects' => $projects, 'start' => true, 'named_params' => $named_params,'project_program'=>$project_program ));
									}
									?>
									</div>
								</div>
								<div class="col-sm-12 col-md-9 col-lg-9 projects-data filtered_data">
									<?php

echo $this->element('../Dashboards/task_center/filtered_datas', array('allprojects' => $projects, 'start' => true, 'named_params' => $named_params));
									 ?>
								</div>
								<input type="hidden" id="paginglivedata" class="livedata" value="0" />
						</div><!-- /.box-body -->
					</div><!-- /.box -->
					</div>
				</div>
		   </div>
		</div>
    </div>
</div>