<?php //echo $user_id;
if (isset($project_id) && !empty($project_id)) {
    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
    $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
	$gpid = $this->Group->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));
    if (isset($gpid) && !empty($gpid)) {
        $p_permission = $this->Group->group_permission_details($project_id, $gpid);
    }
    ?>

    <?php if ((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) { ?>
        <a data-toggle="dropdown" style="margin: 0 0 0 2px;" class="btn btn-sm btn-success dropdown-toggle tipText" title="More Project Options" type="button" href="javascript:void(0);">
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <?php /* <li><a href="<?php echo SITEURL; ?>studios/index/project:<?php echo $project_id ?>"><span class="more-align"><i class="fa fa-sitemap fa-rotate-270"></i></span> Studio</a></li> 
			<li><a href="<?php echo Router::Url(array( "controller" => "boards", "action" => "status_board", $project_id, 'admin' => FALSE ), true); ?>"><span class="more-dp-icon"><i class="more-all-icon workboardblack"></i></span>  Work Board</a></li>

            <li><a href="<?php echo SITEURL; ?>projects/objectives/<?php echo $project_id ?>"><span class="more-dp-icon"><i class="more-all-icon b-StatusBlack"></i></span>  Status</a></li>*/?>

			<?php if (isset($project_id) && !empty($project_id)) {
				$cky = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id'));
				//$cky = CheckProjectType($project_id);
				?>
				<li><a id="costplanner" href="<?php echo SITEURL; ?>users/event_gantt/<?php echo $cky.':'.$project_id ?>"><span class="more-dp-icon"><i class="more-all-icon b-GanttBlack"></i></span> Gantt</a></li>
			<?php } ?>

			<?php /* if( $controllerName != 'task_list'){ ?>
            <li><a href="<?php echo Router::Url(array("controller" => "entities", "action" => "task_list", 'project' => $project_id), true); ?>"><span class="more-align"><i class="fa fa-tasks"></i></span> Task Lists</a></li><?php } */ ?>
            <?php $cky = $this->requestAction('/projects/CheckProjectType/' . $project_id . '/' . $this->Session->read('Auth.User.id')); ?>

			<?php if( $controllerName != 'resources'){ ?>
            <li><a href="<?php echo SITEURL; ?>users/projects/<?php echo $cky . ':' . $project_id ?>"><span class="more-dp-icon"><i class="more-all-icon more-all-icon b-ProjectAssetsBlack"></i></span> Assets</a></li><?php } ?>

			<?php /*if( $controllerName != 'reports'){ ?>
            <li><a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'reports', $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Project Report" id="project_reports" ><span class="more-dp-icon"><i class="more-all-icon b-ProjectReportBlack"></i></span> Project Report</a></li><?php }*/ ?>

            <li><a href="<?php  echo Router::Url(array('controller' => 'todos', 'action' => 'index','project'=> $project_id,   'admin' => FALSE), TRUE); ?>" data-title="To-dos" id="todos" ><span class="more-dp-icon"><i class="more-all-icon b-To-dosBlack"></i></span> To-dos</a></li>

            <?php  /*if(project_settings($project_id, 'is_teamtalk')) { ?>
            <li><a href="<?php  echo Router::Url(array('controller' => 'team_talks', 'action' => 'index', 'project'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Team-Talks" id="teamtalks" ><span class="more-align"><i class="fa fa-fw fa-microphone"></i></span> Team Talk</a></li>
            <?php }

			<?php if( $controllerName != 'missions'){ ?>
            <li><a href="<?php  echo Router::Url(array('controller' => 'missions', 'action' => 'index', 'project'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Mission-Room" id="mission_room" ><span class="more-dp-icon"><i class="more-all-icon b-MissionRoomBlack"></i></span> Mission Room</a></li><?php } ?>*/ ?>

           <!-- <li><a href="<?php  echo Router::Url(array('controller' => 'skts', 'action' => 'index', 'project_id'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Sketcher" id="Sketchers" ><span class="more-align"><i class="fa fa fa-pencil-square-o"></i></span> Sketcher</a></li>-->
        </ul>

    <?php } else { ?>


        <a data-toggle="dropdown" style="margin: 0 0 0 2px;" class="btn btn-sm btn-success dropdown-toggle tipText" title="More Project Options" type="button" href="javascript:void(0);">
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">

			<?php if (isset($project_id) && !empty($project_id)) {
				$cky = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id'));
				?>
				<li><a id="costplanner" href="<?php echo SITEURL; ?>users/event_gantt/<?php echo $cky.':'.$project_id ?>"><span class="more-align"> <i class="fa fa-calendar"></i></span> Gantt</a></li>
			<?php } ?>


           <li><a href="<?php  echo Router::Url(array('controller' => 'todos', 'action' => 'index','project'=> $project_id,   'admin' => FALSE), TRUE); ?>" data-title="To-dos" id="todos" ><span class="more-align"><i class="fa fa-fw fa-list-ul border-alt-big"></i></span> To-dos</a></li>

            <?php /* if(project_settings($project_id, 'is_teamtalk')) { ?>
            <li><a href="<?php  echo Router::Url(array('controller' => 'team_talks', 'action' => 'index', 'project'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Team-Talks" id="teamtalks" ><span class="more-align"><i class="fa fa-fw fa-microphone"></i></span> Team Talk</a></li>
            <?php }*/ ?>
			<?php /*if( $controllerName != 'reports'){ ?>
            <li><a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'reports', $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Project Report" id="project_reports" ><span class="more-align"><i class="fa fa-fw fa-bar-chart-o"></i></span> Project Report</a></li><?php }*/ ?>

           <!-- <li><a href="<?php  echo Router::Url(array('controller' => 'skts', 'action' => 'index', 'project_id'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Sketcher" id="Sketchers" ><span class="more-align"><i class="fa fa fa-pencil-square-o"></i></span> Sketcher</a></li>-->

        </ul>

    <?php }
}
?>