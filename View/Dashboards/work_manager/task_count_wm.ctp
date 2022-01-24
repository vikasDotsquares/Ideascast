<?php 
// e('filter projects');
// pr($named_params);
	$current_user_id = $this->Session->read('Auth.User.id');


	$projects = $els = $element_keys = null;
	
	if(isset($filter_users) && !empty($filter_users)){
		// e('filter_users');
		$projects = array_values($allprojects);
		// 
		$els = $this->TaskCenter->usersElements($filter_users, $projects);
	}else{
		// e('not');
		$projects  = array_values($allprojects);
		// pr($projects);
		$els = $this->TaskCenter->userElements($current_user_id, $projects);
	}

	if (isset($els) && !empty($els)) {
		foreach ($els as $ekey => $evalue) {
			$wsp_area_studio_status = wsp_area_studio_status($evalue);
			if(!$wsp_area_studio_status) {
				if(isset($named_params) && !empty($named_params)) {

					if($named_params == 1) {
						// GET ALL OVERDUE ELEMENT'S PROJECTS
						$element_status = element_status($evalue);
						if( $element_status == 'overdue' ) {
							$element_keys[] = $evalue;
						}
					}
					else if($named_params == 2) {
						// GET PROJECTS THOSE HAVE ELEMENTS THAT ARE COMPLETING TODAY AND TOMORROW
						if(completing_tdto($evalue) > 0){
							$element_keys[] = $evalue;
						}
					}
					else if($named_params == 3) {
						// ALL NOT ENGAGED AND DISENGAGED ELMENT'S PROJECTS
						$el_is_engaged = getByDbId('Element', $evalue, ['is_engaged', 'date_constraints']);
						$element_status = element_status($evalue);
						if(isset($el_is_engaged) && !empty($el_is_engaged)) {
							if(($el_is_engaged['Element']['is_engaged'] == 0 || $el_is_engaged['Element']['is_engaged'] == 1) && ($element_status == 'progress' || $element_status == 'overdue')) {
								$element_keys[] = $evalue;
							}
						}
					}
				}
				else{
					$element_keys[] = $evalue;
				}
				
			}
		}
	}
	$task_status = _elements_status($element_keys);

	$non = arraySearch($task_status, 'status', 'NON');
	$pnd = arraySearch($task_status, 'status', 'PND');
	$prg = arraySearch($task_status, 'status', 'PRG');
	$ovd = arraySearch($task_status, 'status', 'OVD');
	$cmp = arraySearch($task_status, 'status', 'CMP');
?>
<ul class="list-unstyled">
		 <li class="bg-non tipText" title="Not Specified">
			  <span class="label">NON</span>
			  <span class="btn btn-xs bg-undefined" href="#" ><?php echo isset($non) && !empty($non) ? count($non) : 0;?></span>
		 </li>
		 <li class="bg-pnd tipText" title="Not Started">
			  <span class="label">PND</span>
			  <span class="btn btn-xs bg-not_started" href="#"><?php echo isset($pnd) && !empty($pnd) ? count($pnd) : 0;?></span>
		 </li>
		 <li class="bg-prg tipText" title="Progressing">
			  <span class="label">PRG</span>
			  <span class="btn btn-xs bg-progressing" href="#"><?php echo isset($prg) && !empty($prg) ? count($prg) : 0;?></span>
		 </li>
		 <li class="bg-ovd tipText" title="Overdue">
			  <span class="label">OVD</span>
			  <span class="btn btn-xs bg-overdue" href="#"><?php echo isset($ovd) && !empty($ovd) ? count($ovd) : 0;?></span>
		 </li>
		 <li class="bg-cmp tipText" title="Completed">
			  <span class="label">CMP</span>
			  <span class="btn btn-xs bg-completed" href="#"><?php echo isset($cmp) && !empty($cmp) ? count($cmp) : 0;?></span>
		 </li>
	</ul>