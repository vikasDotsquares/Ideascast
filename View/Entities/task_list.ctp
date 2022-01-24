
<style>
	.pophover {
		float: left;
	}
	.popover {
		z-index: 999999 !important;
	}
	.popover p {
		margin-bottom: 2px !important;
	}
	.popover p:first-child {
		font-weight: 600 !important;
		width: 170px !important;
	}
	.popover p:nth-child(2) {
		font-size: 11px;
	}


	.table-wrapper {
		border: 1px solid #a9a9a9;
		border-radius: 4px;
		padding: 1px;
		width: 100%;
		max-width:120px;
		display:table;
	}
	.table-wrapper .table {
		margin: 0;
		width: 120px;
		height:82px;
	}
	.table-wrapper .table td {
		border: 1px solid #fff;
		/* border-radius: 5px; */
		padding: 0px 0;
		vertical-align: middle;
	}


	.project_selection .btn-group .aqua {
	    border-color: #00c0ef;
		background:#fff;
		border-radius: 0 !important;
	}
	.project_selection .open ul.dropdown-menu > li{
	border: medium none !important;
	}

	.project_selection .btn-group .dropdown-toggle:active, .btn-group.open .dropdown-toggle {
	  box-shadow: none !important;
	  outline: 0 none;
	}

	.project_selection .multiselect.dropdown-toggle .caret {
	   border-top-color: #b7b7b7;
	   }

	.project_selection ul.multiselect-container.dropdown-menu {
	  border-radius: 0 !important;
	  box-shadow: none !important;
	  height: auto;
	  max-height: 300px;
	  overflow: auto;
	  width: 100%;
	}

	.project_selection .open ul.dropdown-menu > li a {
	    border: medium none !important;
	    padding: 0 0 0 2px;
		color:#000;
	}

	.project_selection .open ul.dropdown-menu > li > a > label {
	  cursor: pointer;
	  font-weight: 400;
	  height: 100%;
	  margin: 0;
	  padding: 0 !important;
	}

	.project_selection .multiselect-container > li > a > label {
	    cursor: pointer;
	    font-weight: 400;
	    height: 100%;
	    margin: 0;
	    padding: 0 !important;
	}

	.project_selection .open ul.dropdown-menu > li a:hover {
	    background: #3399FF;
		color:#fff;
	}

	.project_selection .dropdown-menu > .active > a, .dropdown-menu > .active > a:focus, .dropdown-menu > .active > a:hover {
	    background: #3399FF !important;
	  color: #fff !important;
	  outline: 0 none;
	  text-decoration: none;
	}




	.column-sm-2 {
		min-height: 1px;
	    padding-left: 15px;
	    padding-right: 15px;
	    position: relative;
		width: 13.6667%;
		float: left;
	}
	.column-sm-10 {
		width: 83.3333%;
		min-height: 1px;
	    padding-left: 15px;
	    padding-right: 15px;
	    position: relative;
		float: left;
	}

	@media (min-width:768px) and (max-width:991px) {
		.table-wrapper {
			max-width:110px;
		}
		.table-wrapper .table {
			width: 110px;
		}
		.project-owners-tital {
	  padding: 0;
	  margin: 0px 0px 3px 0px !important;
	}
		.project-owners-row{
			margin:0px;
		}
	.project-owners-block .wsp_progress{
		margin:10px 0px 0px 0px !important;
	}
	.project-owners-block .start-end-dates .pull-right.btn-group{
		margin-top:10px;
	}
	.sharers-tital{
		margin-top:0px !important;
	}
	.workshop-task-list-h{
		padding-right:50px;
		position:relative;
	}
	.workshop-task-list-h .trim-text{
		float:left;
		clear:both;

	}
	.workshop-task-list-h .tipText{
		position:absolute;
		top:0px;
		right:0px;
	}
	.workshop-task-list-h .tipText i{
		vertical-align:top;
	}
	.workshop-task-list-h .trim-two{
		padding-top:4px;
	}
	}
</style>


<script type="text/javascript" >
$(function(){
	var $c_status;

	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

})
</script>
<?php
echo $this->Html->css('projects/dropdown', array('inline' => true));

echo $this->Html->css('projects/task_lists', array('inline' => true));
echo $this->Html->script('projects/task_lists', array('inline' => true));

echo $this->Html->css('projects/bs-selectbox/bootstrap-multi', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multi', array('inline' => true));

echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));

?>
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ?>
<script type="text/javascript" >
$(function(){
    $c_status = null;
	$(".checkbox_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offTitle: "Off",
		onTitle: "On",
		offLabel: 'Off',
		onLabel: 'On',
	})


	$('#myModal').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
	});
})

</script>

<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding: 6px 0">
                        <span><?php echo $page_subheading; ?></span>
                    </p>
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
                    <div class="box noborder ">

						<!-- CONTENT HEADING -->
                        <div class="box-header" style="background: #efefef none repeat scroll 0 0; border: 1px solid #d2d6de;">

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

							<!-- FILTER BOX -->

							<?php
							if(isset($project_id) && !empty($project_id)) {
								$cky = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id'));
							?>
							<script type="text/javascript" >
							$(function(){
								$c_status ='<?php echo $cky; ?>';
								project_id ='<?php echo $project_id; ?>';


								if($c_status == 'm_project') {
									//$('[for=project_type_my]').trigger('click', [{'project_id': project_id}]);
									$('#project_type_my').trigger('change', [{'project_id': project_id}]).prop('checked', true);
								}
								else if($c_status == 'r_project'){
									//$('[for=project_type_rec]').trigger('click', [{'project_id': project_id}]);
									$('#project_type_rec').trigger('change', [{'project_id': project_id}]).prop('checked', true);
								}
								else if($c_status == 'g_project'){
									//$('[for=project_type_group]').trigger('click', [{'project_id': project_id}]);
									$('#project_type_group').trigger('change', [{'project_id': project_id}]).prop('checked', true);
								}

								/*$(".fancy_input").click(function(e) {
									$thisdata = $(this);
									//console.log('thisdata', $thisdata);
									setTimeout(function(){
										$("#project_report_link").attr("href", $js_config.base_url +"projects/reports/"+$("#ProjectId").val())
										$("#dashboard_link").attr("href", $js_config.base_url +"projects/objectives/"+$("#ProjectId").val())


										if($thisdata.attr("id") == 'project_type_my'){
											$c_status = 'm_project';
											$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/m_project:"+$("#ProjectId").val())
										}
										else if($thisdata.attr("id") == 'project_type_rec'){
											$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/r_project:"+$("#ProjectId").val())
											 $c_status = 'r_project';
										}
										else if($thisdata.attr("id") == 'project_type_group'){
											$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/g_project:"+$("#ProjectId").val())
											$c_status = 'g_project';
										}
									},2000)
								}) */
							})

							</script>
							<?php }

							?>
							<div class="task_list-row-heading">
							<div class="col-sm-12 col-md-12 col-lg-12 studios-row-h">
									<!-- <select id="ProjectId"  name="project_id" class="project_select"  placeholder="Select Project"  ></select> -->
									<label class="task-list-h-t">Projects </label>
								<div class="col-sm-7 col-md-6 col-lg-3" style="padding: 0;">
									<label class="custom-dropdown" style="width: 100%">
										<select class="aqua project_select" name="project_id" id="ProjectId">
											<option value="">No Project Selected</option>
											<?php if( isset($list_projects) && !empty($list_projects) ){
											foreach($list_projects as $key => $pval ){ ?>
											<option value="<?php echo $key;?>" <?php if( !empty($project_id) && $project_id == $key ){?> selected="selected" <?php } ?> ><?php echo $pval;?></option>
										<?php }
										}?>

										</select>
									</label>
									</div>
									<!-- <span class="loader-icon fa fa-spinner fa-pulse"></span> -->
								</div>


						<!--	<div class="col-sm-12 col-md-12 col-lg-12 task-list-first row-first">


							</div>-->

							<div class="col-sm-12 col-md-12 col-lg-12 nopadding-left row-second tasklist-ipad">
								<?php
								//echo $this->Form->create('ProjectCenter', array('url' => ['controller' => 'projects', 'action' => 'objectives'], 'style' =>'', 'class' => 'form', 'id' => 'frm_objective_project' )); ?>


								<div class="col-sm-12 col-md-3 col-lg-3 project_selection">
									<!-- <select id="WorkspaceId"  name="workspace_id" class="project_select"  placeholder="Select Workspace"  ></select> -->
									<label class="custom-dropdown" style="width: 100%; ">
										<select class="aqua project_select" name="workspace_id" id="WorkspaceId">
										<option>No Workspace Selected</option>
										</select>
									</label>
									<span class="loader-icon fa fa-spinner fa-pulse"></span>
								</div>

								<div class="col-sm-12 col-md-3 col-lg-3 project_selection">
									<select id="AreaId"  name="area_id" class="project_select" placeholder="Select Area" multiple="multiple" ></select>
									<span class="loader-icon fa fa-spinner fa-pulse"></span>
								</div>
								<div class="col-sm-12 col-md-3 col-lg-3 project_selection">
									<select id="ElementStatus"  name="element_status" class="project_select"  placeholder="Select Status">
										<option value="">All Task Statuses</option>
										<option value="1">Not Specified</option>
										<option value="2">Not Started</option>
										<option value="3">Progressing</option>
										<option value="4">Completed</option>
										<option value="5">Overdue</option>
									</select>
								</div>


								<!--<div style="margin-top: 2px;" class="col-sm-12 col-md-3 col-lg-3 hidden-sm text-right">
                                    <a class="btn btn-success btn-sm " id="filter_list" data-user="<?php //echo $this->Session->read('Auth.User.id');?>"> Apply Filter </a>
									<a class="btn btn-danger btn-sm" id="filter_reset"> Reset </a>
								</div>-->
								<?php  //echo $this->Form->end(); ?>
							</div>

							<div class="col-sm-12 col-md-12 col-lg-12 row-third taskstatus-ipad" style="">


								<div class="col-sm-12 col-md-12 col-lg-12 " style="padding: 7px 15px 0px;">

									<div class="form-group clearfix">
											<label class="" for="sort_by_toggle">Sort By:</label>
											<input type="checkbox" value="1" class="checkbox_on_off tipText" name="sort_by_toggle" id="sort_by_toggle">

										<input type="radio" id="sort_by_soonest" name="sort_by" class="fancy_input" value="1"   />
										<label class="fancy_label margin-left" for="sort_by_soonest">Ending Soon</label>

										<input type="radio" id="sort_by_last" name="sort_by" class="fancy_input"  value="2" checked />
										<label class="fancy_label margin-left" for="sort_by_last">Ended Last</label>
										<span class="reset-ipad-but task-list-more-three">
											<a class="btn btn-success btn-sm " id="filter_list" data-user="<?php echo $this->Session->read('Auth.User.id');?>"> Apply <span class="hidden-sm">Filter</span> </a>
									<a class="btn btn-danger btn-sm" id="filter_reset"> Reset </a>
										<div class="form-group task-list-more-btn">
								<!-- <div class="radio radio-warning">
                                    <input type="radio" id="project_type_my" name="project_type" class="fancy_input" value="1" />
									<label class="fancy_labels" for="project_type_my">My Projects</label>
								</div>
								<div class="radio radio-warning">
									<input type="radio" id="project_type_rec" name="project_type" class="fancy_input"  value="2" />
									<label class="fancy_labels" for="project_type_rec">Received Projects</label>
								</div>
								<div class="radio radio-warning">
									<input type="radio" id="project_type_group" name="project_type" class="fancy_input"  value="3" />
									<label class="fancy_labels" for="project_type_group">Group Received Projects</label>
								</div> -->
                                <div class="radio-warning pull-right">
									<?php include 'partials/task_settings.ctp';?>
								</div>

								</div>

										</span>
									</div>
								</div>
							</div>
							</div>
							<!-- END FILTER BOX -->

                        </div>
						<!-- END CONTENT HEADING -->


					<div class="box-body" id="box_body">

					</div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->

	</div>
</div>
<!-- END OUTER WRAPPER -->



