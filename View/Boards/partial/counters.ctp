<?php

// wsp and task counter
$projectWspCounters = $this->Permission->projectWspCounters($project_id);
$totalWsp = 0;
$totalWsp += (isset($projectWspCounters[0][0]['NON']) && !empty($projectWspCounters[0][0]['NON'])) ? $projectWspCounters[0][0]['NON'] : 0;
$totalWsp += (isset($projectWspCounters[0][0]['PRG']) && !empty($projectWspCounters[0][0]['PRG'])) ? $projectWspCounters[0][0]['PRG'] : 0;
$totalWsp += (isset($projectWspCounters[0][0]['OVD']) && !empty($projectWspCounters[0][0]['OVD'])) ? $projectWspCounters[0][0]['OVD'] : 0;
$totalWsp += (isset($projectWspCounters[0][0]['CMP']) && !empty($projectWspCounters[0][0]['CMP'])) ? $projectWspCounters[0][0]['CMP'] : 0;
$totalWsp += (isset($projectWspCounters[0][0]['PND']) && !empty($projectWspCounters[0][0]['PND'])) ? $projectWspCounters[0][0]['PND'] : 0;
$wspPercent = ($totalWsp > 0) ? ( ($projectWspCounters[0][0]['CMP']/$totalWsp) * 100 ) : 0;

$projectTaskCounters = $this->Permission->projectTaskCounters($project_id);
$totalTasks = 0;
$totalTasks += (isset($projectTaskCounters[0][0]['NON']) && !empty($projectTaskCounters[0][0]['NON'])) ? $projectTaskCounters[0][0]['NON'] : 0;
$totalTasks += (isset($projectTaskCounters[0][0]['PRG']) && !empty($projectTaskCounters[0][0]['PRG'])) ? $projectTaskCounters[0][0]['PRG'] : 0;
$totalTasks += (isset($projectTaskCounters[0][0]['OVD']) && !empty($projectTaskCounters[0][0]['OVD'])) ? $projectTaskCounters[0][0]['OVD'] : 0;
$totalTasks += (isset($projectTaskCounters[0][0]['CMP']) && !empty($projectTaskCounters[0][0]['CMP'])) ? $projectTaskCounters[0][0]['CMP'] : 0;
$totalTasks += (isset($projectTaskCounters[0][0]['PND']) && !empty($projectTaskCounters[0][0]['PND'])) ? $projectTaskCounters[0][0]['PND'] : 0;
$tasksPercent = ($totalTasks > 0) ? ( ($projectTaskCounters[0][0]['CMP']/$totalTasks) * 100 ) : 0;


?>


    <div class="progresscol1">
	<div class="progress-col-heading">
		<span class="prog-h">Work <i class="arrow-down"></i></span>
		<span class="percent-text tipText" title="Percentage Complete"><?php echo ceil($wspPercent); ?>%</span>
	</div>
	<!-- WSP COUNTER -->
	<div class="progress-col-cont">
			<ul class="workcount">
                <li class="light-gray tipText" title="Not Set"><?php echo (isset($projectWspCounters[0][0]['NON']) && !empty($projectWspCounters[0][0]['NON'])) ? $projectWspCounters[0][0]['NON'] : 0; ?></li>
                <li class="dark-gray tipText" title="Not Started"><?php echo (isset($projectWspCounters[0][0]['PND']) && !empty($projectWspCounters[0][0]['PND'])) ? $projectWspCounters[0][0]['PND'] : 0; ?></li>
                <li class="yellow tipText" title="In Progress"><?php echo (isset($projectWspCounters[0][0]['PRG']) && !empty($projectWspCounters[0][0]['PRG'])) ? $projectWspCounters[0][0]['PRG'] : 0; ?></li>
                <li class="red tipText" title="Overdue"><?php echo (isset($projectWspCounters[0][0]['OVD']) && !empty($projectWspCounters[0][0]['OVD'])) ? $projectWspCounters[0][0]['OVD'] : 0; ?></li>
                <li class="green-bg tipText" title="Completed"><?php echo (isset($projectWspCounters[0][0]['CMP']) && !empty($projectWspCounters[0][0]['CMP'])) ? $projectWspCounters[0][0]['CMP'] : 0; ?></li>
            </ul>
		<div class="proginfotext">Workspaces</div>
        </div>
	</div>
    <div class="progresscol2">
	<div class="progress-col-heading">
		<span class="prog-h"></span> <span class="percent-text tipText" title="Percentage Complete"><?php echo ceil($tasksPercent); ?>%</span>
	</div>
	<!-- TASK COUNTERS -->
	<div class="progress-col-cont">
			<ul class="workcount taskcounters">
                <li class="light-gray tipText task_count" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:4'; ?>" title="Not Set"><?php echo (isset($projectTaskCounters[0][0]['NON']) && !empty($projectTaskCounters[0][0]['NON'])) ? $projectTaskCounters[0][0]['NON'] : 0; ?></li>
                <li class="dark-gray tipText task_count" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:6'; ?>" title="Not Started"><?php echo (isset($projectTaskCounters[0][0]['PND']) && !empty($projectTaskCounters[0][0]['PND'])) ? $projectTaskCounters[0][0]['PND'] : 0; ?></li>
                <li class="yellow tipText task_count" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:7'; ?>" title="In Progress"><?php echo (isset($projectTaskCounters[0][0]['PRG']) && !empty($projectTaskCounters[0][0]['PRG'])) ? $projectTaskCounters[0][0]['PRG'] : 0; ?></li>
                <li class="red tipText task_count" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:1'; ?>" title="Overdue"><?php echo (isset($projectTaskCounters[0][0]['OVD']) && !empty($projectTaskCounters[0][0]['OVD'])) ? $projectTaskCounters[0][0]['OVD'] : 0; ?></li>
                <li class="green-bg tipText task_count" data-url="<?php echo SITEURL.'dashboards/task_centers/'.$project_id.'/status:5'; ?>" title="Completed"><?php echo (isset($projectTaskCounters[0][0]['CMP']) && !empty($projectTaskCounters[0][0]['CMP'])) ? $projectTaskCounters[0][0]['CMP'] : 0; ?></li>
            </ul>
		<div class="proginfotext">Tasks</div>
        </div>
	</div>

