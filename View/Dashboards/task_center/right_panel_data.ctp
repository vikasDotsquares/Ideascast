<div class="col-sm-12 col-md-3 col-lg-3 projects-icons" style="">
	<span class="text-center task-status">asfsdf
		<ul class="list-unstyled">
			 <li class="bg-non tipText" title="Not Set">
				  <span class="label">NON</span>
				  <span class="btn btn-xs bg-undefined tipText" href="#" ><?php echo isset($statusArray['non']) && !empty($statusArray['non']) ? count($statusArray['non']) : 0;?></span>
			 </li>
			 <li class="bg-pnd tipText" title="Not Started">
				  <span class="label">PND</span>
				  <span class="btn btn-xs bg-not_started tipText" href="#"><?php echo isset($statusArray['pnd']) && !empty($statusArray['pnd']) ? count($statusArray['pnd']) : 0;?></span>
			 </li>
			 <li class="bg-prg tipText" title="In Progressing">
				  <span class="label">PRG</span>
				  <span class="btn btn-xs bg-progressing tipText" href="#"><?php echo isset($statusArray['prg']) && !empty($statusArray['prg']) ? count($statusArray['prg']) : 0;?></span>
			 </li>
			 <li class="bg-ovd tipText" title="Overdue">
				  <span class="label">OVD</span>
				  <span class="btn btn-xs bg-overdue tipText" href="#"><?php echo isset($statusArray['ovd']) && !empty($statusArray['ovd']) ? count($statusArray['ovd']) : 0;?></span>
			 </li>
			 <li class="bg-cmp tipText" title="Completed">
				  <span class="label">CMP</span>
				  <span class="btn btn-xs bg-completed tipText" href="#"><?php echo isset($statusArray['cmp']) && !empty($statusArray['cmp']) ? count($statusArray['cmp']) : 0;?></span>
			 </li>
		</ul>
	</span>
	<div class="projects-list">
	<div class="tt"></div>
	<?php if( $all_projects ) { ?>

	<?php 
		echo $this->element('../Dashboards/task_center/filter_projects', array('filter_users' => [$this->Session->read("Auth.User.id")], 'allprojects' => $projects, 'start' => true, 'named_params' => $named_params ));
	 ?>
	<?php } ?>
	</div>
</div>