
<?php
// $project_data = $this->ViewModel->getProjectDetail($project_id, 1);
$project = $project_data;
// pr($project_data);
 ?>
	<div class="col-sm-12 bg-aqua text-bolder">
		Objective
	</div>
	<div class="col-sm-12 text-data scroll-200">
		<?php echo $project['objective']; ?>
	</div>

	<div class="col-sm-12 bg-aqua text-bolder">
		Project Type
	</div>
	<div class="col-sm-12 text-data scroll-200">
		<?php echo ( !empty($project['aligned_id']) ) ? $align['atitle'] : 'N/A'; ?>
	</div>

	<div class="col-sm-12 bg-aqua text-bolder">
		Description
	</div>
	<div class="col-sm-12 text-data" style="max-height: 250px; overflow-x: hidden; overflow-y: auto; height: 250px">
		<?php echo $project['description']; ?>
	</div>