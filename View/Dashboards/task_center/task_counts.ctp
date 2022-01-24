<?php
	$user_ids = '';
	if( isset($filter_users) && !empty($filter_users) ){
		$dataUsers = array();
		foreach($filter_users as $users){
			$dataUsers[]=$users;
		}
		$user_ids = implode(",",$dataUsers);
	} else {
		$user_ids = $this->Session->read('Auth.User.id');
	}
	$projectids = '';
	if( isset($project_ids) && !empty($project_ids) ){
		$projectids = implode(",",$project_ids);
	}
	//$named_params
	$taskStatusCount = $this->ViewModel->taskStatusCount($user_ids,$projectids,$named_params);
 // pr($taskStatusCount, 1);
	$non = $taskStatusCount[0][0]['NON'];
	$pnd = $taskStatusCount[0][0]['PND'];
	$prg = $taskStatusCount[0][0]['PRG'];
	$ovd = $taskStatusCount[0][0]['OVD'];
	$cmp = $taskStatusCount[0][0]['CMP'];
?>
<ul class="list-unstyled">
		 <li class="bg-non tipText taskstatus" data-text="NON" data-taskcnt="<?php echo isset($non) && !empty($non) ? $non : 0;?>" title="Not Set">
			  <span class="label">NON</span>
			  <span class="btn btn-xs bg-undefined status-filters" data-status="not_spacified"><?php echo isset($non) && !empty($non) ? $non : 0;?></span>
		 </li>
		 <li class="bg-pnd tipText taskstatus" data-text="PND" data-taskcnt="<?php echo isset($pnd) && !empty($pnd) ? $pnd : 0;?>" title="Not Started">
			  <span class="label">PND</span>
			  <span class="btn btn-xs bg-not_started status-filters" data-status="not_started"><?php echo isset($pnd) && !empty($pnd) ? $pnd : 0;?></span>
		 </li>
		 <li class="bg-prg tipText taskstatus" data-text="PRG" data-taskcnt="<?php echo isset($prg) && !empty($prg) ? $prg : 0;?>" title="In Progress">
			  <span class="label">PRG</span>
			  <span class="btn btn-xs bg-progressing status-filters" data-status="progress"><?php echo isset($prg) && !empty($prg) ? $prg : 0;?></span>
		 </li>
		 <li class="bg-ovd tipText taskstatus" data-text="OVD" data-taskcnt="<?php echo isset($ovd) && !empty($ovd) ? $ovd : 0;?>" title="Overdue">
			  <span class="label">OVD</span>
			  <span class="btn btn-xs bg-overdue status-filters" data-status="overdue"><?php echo isset($ovd) && !empty($ovd) ? $ovd : 0;?></span>
		 </li>
		 <li class="bg-cmp tipText taskstatus" data-text="CMP" data-taskcnt="<?php echo isset($cmp) && !empty($cmp) ? $cmp : 0;?>" title="Completed">
			  <span class="label">CMP</span>
			  <span class="btn btn-xs bg-completed status-filters" data-status="completed"><?php echo isset($cmp) && !empty($cmp) ? $cmp : 0;?></span>
		 </li>
	</ul>