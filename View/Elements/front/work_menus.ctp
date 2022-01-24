<li class="dropdown custom-drp-menu aap-center-nav"><a href="javascript:void(0)"  class="dropdown-toggle drop-icon tipText" title="App Center" data-toggle="dropdown" ><span class="nav-icon-all"><i  class="icon-size-nav ico-work"></i></span></a>
    <ul class="dropdown-menu" id="main-dropmenu">
		<?php /*?><li>
			<a href="<?php echo SITEURL . 'dashboards/program_center'; ?>"  ><span>
				<i class="app-center-menu-all-icon app-center-program-center"></i></span> Program Center </a>
		</li>

		<li>
			<a href="<?php echo SITEURL . 'projects/objectives'; ?>"  ><span>
				<i class="app-center-menu-all-icon app-center-status-center"></i></span> Status Center </a>
		</li>
		<li><a href="<?php echo SITEURL . 'dashboards/project_center'; ?>"  ><span>
			<i class="app-center-menu-all-icon app-center-project-center" ></i></span> Project Center </a>
		</li><?php */?>
		<li class="task-center"><a href="<?php echo TASK_CENTERS; ?>"><span>
			<i class="app-center-menu-all-icon app-center-task-center"></i></span> Task Center </a>
		</li>
		<?php /* ?><li><a   href="<?php echo SITEURL . 'costs'; ?>"><span>
			<i class="app-center-menu-all-icon app-center-cost-center"></i></span> Cost Center </a>
		</li><?php */ ?>
		<li><a href="<?php echo Router::Url(array('controller' => 'risks','action' => 'index'), TRUE); ?>" class="" ><span>
			<i class="app-center-menu-all-icon app-center-risk-center"></i></span> Risk Center </a>
		</li>
		<?php /* ?><li class="reward"><a href="<?php echo Router::Url(array('controller' => 'rewards','action' => 'index'), TRUE); ?>" class=""><span>
			<i class="app-center-menu-all-icon app-center-reward-center"></i></span> Reward Center </a>
		</li>
		<li class="task-reminder"><a href="<?php echo SITEURL . 'team_talks'; ?>"><span>
			<i class="app-center-menu-all-icon app-center-info-center"></i></span> Info Center </a>
		</li>
		<li><a href="<?php echo SITEURL . 'studios'; ?>"><span>
			<i class="app-center-menu-all-icon app-center-design-center"></i></span> Design Center </a>
		</li><?php */ ?>

         <?php /* ?><li>
            <a href="<?php echo Router::Url(array('controller' => 'shares','action' => 'my_sharing','#' =>'people_view'), TRUE); ?>">
                <i class="app-center-menu-all-icon app-center-people-center"></i> People Center
            </a>
        </li> <?php */ ?>

		<?php /*?><li><a  href="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', 0 ), TRUE); ?>" class="" ><span><i class="app-center-menu-all-icon app-center-work-center"></i></span> Knowledge Center </a>
		</li><?php */?>

	</ul>
</li>