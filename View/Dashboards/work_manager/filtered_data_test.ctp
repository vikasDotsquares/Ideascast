<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>
<div class="buttons-container">
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-1">
		<div class="panel panel-default"> 
			<div class="panel-heading people-section">People
				<div class="btn-group pull-right">
					<a class="btn btn-xs btn-control alphabetical tipText disabled" title="Alphabetical Sort" data-sorted="asc" data-parent=".filter_people_by_project" data-type="people">AZ</a>
				</div>
			</div> 
		</div>
	</div>
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-2">
		<div class="panel panel-default"> 
			<div class="panel-heading task-section">Task
				<input type="text" class="select_dates" style="opacity: 0; width: 0; height: 0;">
				<div class="btn-group pull-right">
					<a class="btn btn-xs btn-control tipText calendar_trigger disabled" title="Select Dates" data-type="element"><i class="fa fa-calendar-check-o"></i></a>
					<a class="btn btn-xs btn-control alphabetical tipText disabled" title="Alphabetical Sort" data-sorted="asc" data-parent=".filter_task_by_project" data-type="element">AZ</a>
					<a class="btn btn-xs btn-control start_date_sort sort tipText disabled" title="Start Date First"  data-parent=".filter_task_by_project" data-type="element"><i class="fa fa-chevron-circle-up"></i></a>
					<a class="btn btn-xs btn-control end_date_sort sort tipText disabled" title="End Date First"  data-parent=".filter_task_by_project" data-type="element"><i class="fa fa-chevron-circle-down"></i></a>
				</div>
				<div class="selected_dates">
					<i class="fa fa-times pull-right" style=""></i>
				</div>
			</div> 
		</div>
	</div>
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-3">
		<div class="panel panel-default"> 
			<div class="panel-heading wsp-section">Workspace
				<div class="btn-group pull-right">
					<a class="btn btn-xs btn-control alphabetical tipText disabled" title="Alphabetical Sort" data-sorted="asc" data-parent=".filter_wsp_by_project" data-type="workspace">AZ</a>
					<a class="btn btn-xs btn-control start_date_sort sort tipText disabled" title="Start Date First" data-parent=".filter_wsp_by_project" data-type="workspace"><i class="fa fa-chevron-circle-up"></i></a>
					<a class="btn btn-xs btn-control end_date_sort sort tipText disabled" title="End Date First" data-parent=".filter_wsp_by_project" data-type="workspace"><i class="fa fa-chevron-circle-down"></i></a>
				</div>
			</div> 
		</div>
	</div>
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-4">
		<div class="panel panel-default"> 
			<div class="panel-heading project-section">Project
				<div class="btn-group pull-right">
					<a class="btn btn-xs btn-control project_alphabetical tipText disabled" title="Alphabetical Sort" data-sorted="asc" data-parent=".filter_selected_project" data-type="project">AZ</a>
					<a class="btn btn-xs btn-control project_start_date_sort sort tipText disabled" title="Start Date First" data-parent=".filter_selected_project" data-type="project"><i class="fa fa-chevron-circle-up"></i></a>
					<a class="btn btn-xs btn-control project_end_date_sort sort tipText disabled" title="End Date First" data-parent=".filter_selected_project" data-type="project"><i class="fa fa-chevron-circle-down"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="col-sm-12 projects-line-wrapper">
	<div class="no-result">No Result</div>
	<?php $element_keys = $element_users = [];
	if (isset($filter_projects) && !empty($filter_projects)) {
		 
		foreach ($filter_projects as $prjid => $pvalue) {
			
			/********************************* GET PROJECT DATA *****************************************/
			$project_detail = getByDbId('Project', $prjid, ['title', 'id', 'start_date', 'end_date', 'color_code']);
			$project_title = strip_tags($project_detail['Project']['title']);
			$project_start_date = $project_detail['Project']['start_date'];
			$project_end_date = $project_detail['Project']['end_date'];
			$project_color_code = $project_detail['Project']['color_code'];

			$project_permit_type = $this->TaskCenter->project_permit_type( $prjid, $current_user_id );

			$prj_start_date = (isset($project_start_date) && !empty($project_start_date)) ? $this->TaskCenter->_displayDate_new($project_start_date,'Y-m-d') : false;
			$prj_end_date = (isset($project_end_date) && !empty($project_end_date)) ? $this->TaskCenter->_displayDate_new($project_end_date,'Y-m-d') : false;
	?>
	<div class="projects-line" data-id="<?php echo($prjid); ?>" data-project-title="<?php echo strtolower($project_title); ?>"  <?php if($prj_start_date) { ?> data-project-start-date="<?php echo $prj_start_date; ?>" <?php } ?> <?php if($prj_end_date) { ?> data-project-end-date="<?php echo $prj_end_date; ?>" <?php } ?> > 
    <div style="padding: 20px; font-size: 15px; display: block; text-align: center;">Loading...</div> </div> 
		<?php 
		}
	}
	else {
	?>
		<div class="no-row-wrapper">Select Project</div>
	<?php 
	}
	?> 
	 
</div> 