<?php //echo $user_id;
if (isset($project_id) && !empty($project_id)) {
 
    ?>

    <?php if(isset($wsp_permissions[0]['user_permissions']) && in_array($wsp_permissions[0]['user_permissions']['role'],array('Creator','Group Owner','Owner')) ){
				   ?>
        <a data-toggle="dropdown" style="margin: 0 0 0 2px;" class="btn btn-sm btn-success dropdown-toggle tipText" title="More Project Options" type="button" href="javascript:void(0);">
            More <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <?php /* <li><a href="<?php echo SITEURL; ?>studios/index/project:<?php echo $project_id ?>"><span class="more-align"><i class="fa fa-sitemap fa-rotate-270"></i></span> Studio</a></li> */?>

            <li><a href="<?php echo SITEURL; ?>projects/objectives/<?php echo $project_id ?>"><span class="more-align"><i class="fa fa-dashboard"></i></span>  Status</a></li> 
			
			<?php if (isset($project_id) && !empty($project_id)) {
				$cky = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id'));
				?>
				<li><a id="costplanner" href="<?php echo SITEURL; ?>users/event_gantt/<?php echo $cky.':'.$project_id ?>"><span class="more-align"> <i class="fa fa-calendar"></i></span> Gantt</a></li>
			<?php } ?>
			
			<?php /* if( $controllerName != 'task_list'){ ?>
            <li><a href="<?php echo Router::Url(array("controller" => "entities", "action" => "task_list", 'project' => $project_id), true); ?>"><span class="more-align"><i class="fa fa-tasks"></i></span> Task Lists</a></li><?php } */ ?>
            <?php $cky = $this->requestAction('/projects/CheckProjectType/' . $project_id . '/' . $this->Session->read('Auth.User.id')); ?>
			
			<?php if( $controllerName != 'resources'){ ?>
            <li><a href="<?php echo SITEURL; ?>users/projects/<?php echo $cky . ':' . $project_id ?>"><span class="more-align"><i class="projectassetsicon-btn"></i></span> Project Assets </a></li><?php } ?>
			
			<?php if( $controllerName != 'reports'){ ?>
            <li><a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'reports', $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Project Report" id="project_reports" ><span class="more-align"><i class="fa fa-fw fa-bar-chart-o"></i></span> Project Report</a></li><?php } ?>

            <li><a href="<?php  echo Router::Url(array('controller' => 'todos', 'action' => 'index','project'=> $project_id,   'admin' => FALSE), TRUE); ?>" data-title="To-dos" id="todos" ><span class="more-align"><i class="to-do-icon"></i></span> To-dos</a></li>

            <?php  /*if(project_settings($project_id, 'is_teamtalk')) { ?>
            <li><a href="<?php  echo Router::Url(array('controller' => 'team_talks', 'action' => 'index', 'project'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Team-Talks" id="teamtalks" ><span class="more-align"><i class="fa fa-fw fa-microphone"></i></span> Team Talk</a></li>
            <?php }*/ ?>

			<?php if( $controllerName != 'missions'){ ?>
            <li><a href="<?php  echo Router::Url(array('controller' => 'missions', 'action' => 'index', 'project'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Mission-Room" id="mission_room" ><span class="more-align"><i class="mission-icon"></i></span> Mission Room</a></li><?php } ?>

           <!-- <li><a href="<?php  echo Router::Url(array('controller' => 'skts', 'action' => 'index', 'project_id'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Sketcher" id="Sketchers" ><span class="more-align"><i class="fa fa fa-pencil-square-o"></i></span> Sketcher</a></li>-->
        </ul>
		
    <?php } else { ?>
	

        <a data-toggle="dropdown" style="margin: 0 0 0 2px;" class="btn btn-sm btn-success dropdown-toggle tipText" title="More Project Options" type="button" href="javascript:void(0);">
            More <span class="caret"></span>
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
			<?php if( $controllerName != 'reports'){ ?>	
            <li><a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'reports', $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Project Report" id="project_reports" ><span class="more-align"><i class="fa fa-fw fa-bar-chart-o"></i></span> Project Report</a></li><?php } ?>
		
           <!-- <li><a href="<?php  echo Router::Url(array('controller' => 'skts', 'action' => 'index', 'project_id'=> $project_id, 'admin' => FALSE), TRUE); ?>" data-title="Sketcher" id="Sketchers" ><span class="more-align"><i class="fa fa fa-pencil-square-o"></i></span> Sketcher</a></li>-->

        </ul>

    <?php }
}
?>