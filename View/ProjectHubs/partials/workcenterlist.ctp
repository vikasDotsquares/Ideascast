<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script type="text/javascript" > var SITEURL='<?php echo SITEURL; ?>'</script>
<?php
        echo $this->Html->meta('icon');
        echo $this->Html->css(
                array(

				/* 	'/twitter-cal/components/bootstrap3/css/bootstrap.min',
					'/twitter-cal/components/bootstrap3/css/bootstrap-theme', */
					//'/twitter-cal/css/calendar',

                )
        );

		// echo $this->Html->css('/plugins/fullcalendar/fullcalendar.print', array('media' => 'print'));

	?>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script  type="text/javascript" src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
      <?php  echo $this->Html->script(array(
           // '/plugins/jQuery/jQuery-2.1.3.min',
			//'jquery-ui.min.js',
			//'moment.min',
			//'/twitter-cal/components/underscore/underscore-min',
			//'/twitter-cal/js/calendar',
			'dashboard',
			//'jstz',

			)
		   );
	   ?>
<style>
.ui-datepicker-inline{display: none !important; }
#workspace {
    margin-top: 0px;
}
.projectuserdetail{
	font-weight:700;
}
</style>
    <!-------------------------------- divid--------------------------------------- -->
    <!--<div class="opt">
		<a href="#" id="" class="btn btn-primary btn-xs toggle-accordion tipText" title="" accordion-id="#accordion" data-original-title="Expand All">
            <i class="fa "></i>
        </a>
	</div>-->

    <div class="panel-group panel-custom" id="accordion" style="height: 590px; overflow-y:scroll; ">
        <?php 
			$current_user_id = $this->Session->read("Auth.User.id");
			 
			if( isset($projects) && !empty($projects) ){
			$projectlists = $this->ViewModel->get_projectbyidwithorder($projects);
			$i=0; 			 
			foreach($projectlists as  $projectdetail){
			 
			$project_id = $projectdetail['Project']['id'];
			$cky = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id'));
			$projectPermissionType = $this->ViewModel->projectPermitType($project_id, $current_user_id);
			
			$projectCreator = $this->Common->ProjectOwner($project_id);			
			$creatorDetail = $this->ViewModel->get_user( $projectCreator['UserProject']['user_id'], null, 1 );	

			$program_cnt = $this->ViewModel->project_program_cnt($this->Session->read('Auth.User.id'), $project_id);
			
			$projectRiskCnt = $this->ViewModel->projectRisksCnt($project_id);
			$projectTodoCnt = $this->ViewModel->projectTodoCnt($project_id,$current_user_id);
			$projectSktechCnt = $this->ViewModel->projectSktechCnt($project_id,$current_user_id);
			
			$programtip = '';
			if( empty($program_cnt) && $program_cnt == 0 ){
				$programtip = 'Project Not In A Program';	
			}
			$risktip = '';
			if( empty($projectRiskCnt) && $projectRiskCnt == 0 ){
				$risktip = 'No Project Risks Involving Me';	
			}			
			$todotip = '';
			if( empty($projectTodoCnt) && $projectTodoCnt == 0 ){
				$todotip = 'No Project To-dos Involving Me';	
			}
			$sktchtip = '';
			if( empty($projectSktechCnt) && $projectSktechCnt == 0 ){
				$sktchtip = 'No Project Sketches Involving Me';	
			}
			
			
			/*========================================================== */	
		?>
                    <div class="panel panel-default list-panel ">
                        <div class="panel-heading" >
                            <h4 class="panel-title">
								<a class="accordion-anchor" >
									<?php
										$last_opned = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$projectdetail['Project']['modified']),$format = 'd M Y h:iA'); 
									?>
									<span class="program-title" data-pid="<?php echo $projectdetail['Project']['id'];?>"><i class="fa fa-briefcase"></i> <strong><?php echo strip_tags($projectdetail['Project']['title']);?></strong><span class="<?php if(empty($projectElementCount)){ echo 'blank'; } ?>"> (<?php echo "Last opened: ".$last_opned;?>)</span></span>
									
									<div class="right-options">
										<span class="show-hide-program btn btn-xs btn-white tipText" title="" data-toggle="collapse" data-parent="#accordion" data-accordionid="collapse<?php echo $i;?>" href="#collapse<?php echo $i;?>" aria-expanded="false" data-original-title="Expand">
											<i class="fa"></i>
										</span>
									</div>
									
								</a>
							</h4>
                    	</div>
                        <div id="collapse<?php echo $i;?>" class="panel-collapse collapse"  >
                            <div class="panel-body">
                                <div class="work-project-info-tab">
                                   <div class="row work-project-info-mainrow projectuserdetail">
										<div class="col-sm-6">Project Creator: <?php echo $creatorDetail['UserDetail']['full_name']; ?></div>		
										<div class="col-sm-6">
											<div class="col-sm-6 text-right">
												Permission: <?php 
													if( $projectPermissionType == true ){
														echo "Owner";
													} else {
														echo "Sharer";
													}
												?>
											</div>
											<div class="col-sm-6 text-right">
												Start: <?php 
												echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectdetail['Project']['start_date'])),$format = 'd M Y');?>, End:<?php echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectdetail['Project']['end_date'])),$format = 'd M Y');?>
											</div>											
										</div>		
								   </div>
								<?php if( $projectPermissionType == true ){ ?>
								   <div class="row work-project-info-mainrow">
										<ul>										
										   <li><a href="<?php echo SITEURL;?>dashboards/program_center/<?php echo $project_id; ?>" class="tipText" title="<?php echo $programtip;?>" ><span><i class="fa program_center_icon_logo"></i></span> Program Center <span class="count-w"><?php echo " (".$program_cnt.")";?></span> </a></li>
										   <li><a href="<?php echo SITEURL;?>risks/index/<?php echo $project_id;?>" class="tipText" title="<?php echo $risktip;?>" ><span><i class="fa fa-exclamation" aria-hidden="true"></i></span>Risk Center <span class="count-w"><?php echo "(".$projectRiskCnt.")" ;?></span></a></li>
										   
										   <li><a href="<?php echo SITEURL;?>users/event_gantt/<?php echo $cky.":".$project_id;?>" class=""><span><i class="fa fa-calendar" aria-hidden="true"></i></span> Gantt </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>users/projects/<?php echo $cky.":".$project_id;?>" class=""><span><i class="fa fa-file" aria-hidden="true"></i></span> Show Resources </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>shares/index/<?php echo $project_id;?>" class=""><span><i class="fa fa-users" aria-hidden="true"></i></span> Project Sharing </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>dashboards/project_center"><span><i class="fa fa-dot-circle-o"></i></span> Project Center </a><span></span></li>
										   
										   <li><a href="<?php echo SITEURL;?>work_centers" class=""><span class="light-bulb"> &nbsp; </span> Work Center </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>entities/task_list/project:<?php echo $project_id;?>"><span><i class="fa fa-tasks"></i></span> Task Lists </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>projects/reports/<?php echo $project_id;?>"><span><i class="fa fa-fw fa-bar-chart-o"></i></span> Project Report </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>shares/sharing_map/<?php echo $project_id;?>"><span><i class="fa fa-fw fa-share"></i></span> Sharing Map </a></li>
										   
										   <li class="task-center"><a href="<?php echo SITEURL;?>dashboards/task_center/<?php echo $project_id; ?>"><span><i class="ico-task-center"></i></span> Task Center </a><span></span></li>
										   <?php if( isset($projectdetail['UserProject']['is_rewards']) && $projectdetail['UserProject']['is_rewards'] == 1 ) { ?>
											<li class="reward">
												<a href="<?php echo SITEURL;?>rewards">
												<i class="icn-reward-center"></i> Reward Center
												</a>
											</li>
										   <?php } else { ?>
											<li class="reward disable">
												<a href="javascript:void(0);">
												<i class="icn-reward-center"></i> Reward Center
												</a>
											</li>
										   <?php } ?>
										   
										   <li><a href="<?php echo SITEURL;?>projects/objectives/<?php echo $project_id; ?>"><span><i class="fa fa-dashboard"></i></span> Dashboards </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>missions/index/project:<?php echo $project_id; ?>"><span><i class="mission-icon"></i></span>  Mission Room </a></li>
										    <?php if( isset($projectdetail['UserProject']['is_rewards']) && $projectdetail['UserProject']['is_rewards'] == 1 ) { ?>
												<li><a href="<?php echo SITEURL;?>team_talks/index/project:<?php echo $project_id; ?>"><span><i class="fa fa-fw fa-microphone"></i></span> Team Talk </a></li>
											<?php } else { ?>
												<li class="disable"><a href="javascript:void(0);"><span><i class="fa fa-fw fa-microphone"></i></span> Team Talk </a></li>
											<?php } ?>
										   <li><a href="<?php echo SITEURL;?>costs/index/<?php echo $cky.":".$project_id;?>"><span><i class="fa-manage-cost"></i></span> Cost Center </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>studios/index/<?php echo $project_id;?>"><span><i class="fa fa-sitemap fa-rotate-270"></i></span> Studio </a></li>
										   
										   <li><a href="<?php echo SITEURL;?>todos/index/project:<?php echo $project_id;?>"  class="tipText" title="<?php echo $todotip;?>" ><span><i class="fa fa-fw fa-list-ul border-alt-big"></i></span> To-dos <span class="count-w"><?php echo "(".$projectTodoCnt.")" ;?></span></a></li>
										   
										   <li class="">
											  <a href="<?php echo SITEURL;?>skts/index/project_id:<?php echo $project_id;?>" class="tipText" title="<?php echo $sktchtip;?>" >
											  <span><i class="fa fa-pencil-square-o"></i></span> Sketches <span class="count-w"><?php echo "(".$projectSktechCnt.")" ;?></span></a>
										   </li>
										</ul>	
									</div>
								<?php } else { ?>
								
									<div class="row work-project-info-mainrow">
										<ul>
										
										   <li><a href="<?php echo SITEURL;?>dashboards/project_center"><span><i class="fa fa-dot-circle-o"></i></span> Project Center </a><span></span></li>
										   <li><a href="<?php echo SITEURL;?>risks/index/<?php echo $project_id;?>" class="tipText" title="<?php echo $risktip;?>"><span><i class="fa fa-exclamation" aria-hidden="true"></i></span> Risk Center <span class="count-w"> <?php echo ( count($projectRiskCnt) > 0 && !empty($projectRiskCnt) )? "(".count($projectRiskCnt).")" : "(0)";?></span></a></li>  
										   <li class="reward">
											  <a href="<?php echo SITEURL;?>rewards">
											  <i class="icn-reward-center"></i> Reward Center
											  </a>
										   </li>
										   
											<li><a href="<?php echo SITEURL;?>todos/index/project:<?php echo $project_id;?>" class="tipText" title="<?php echo $todotip;?>"><span><i class="fa fa-fw fa-list-ul border-alt-big"></i></span> To-dos <span class="count-w"><?php echo "(".$projectTodoCnt.")" ;?></span></a></li>
										   
											<li class="">
											  <a href="<?php echo SITEURL;?>skts/index/project_id:<?php echo $project_id;?>" class="tipText" title="<?php echo $sktchtip;?>" >
											  <span><i class="fa fa-pencil-square-o"></i></span> Sketches <span class="count-w"><?php echo "(".$projectSktechCnt.")" ;?></span></a>
											</li>
											
											<li class="task-center"><a href="<?php echo SITEURL;?>dashboards/task_center/<?php echo $project_id; ?>"><span><i class="ico-task-center"></i></span> Task Center </a><span></span></li>
											
											<li><a href="<?php echo SITEURL;?>work_centers" class=""><span class="light-bulb"> &nbsp; </span> Work Center </a></li>
											
											<li><a href="<?php echo SITEURL;?>users/event_gantt/<?php echo $cky.":".$project_id;?>" class=""><span><i class="fa fa-calendar" aria-hidden="true"></i></span> Gantt </a></li>	

											<li><a href="<?php echo SITEURL;?>projects/reports/<?php echo $project_id;?>"><span><i class="fa fa-fw fa-bar-chart-o"></i></span> Project Report </a></li>
										   
											<?php if( isset($projectdetail['UserProject']['is_rewards']) && $projectdetail['UserProject']['is_rewards'] == 1 ) { ?>
												<li><a href="<?php echo SITEURL;?>team_talks/index/project:<?php echo $project_id; ?>"><span><i class="fa fa-fw fa-microphone"></i></span> Team Talk </a></li>
											<?php } else { ?>
												<li class="disable"><a href="javascript:void(0);"><span><i class="fa fa-fw fa-microphone"></i></span> Team Talk </a></li>
											<?php } ?>
										   
										</ul>	
									</div>	
								<?php } ?>
								</div>
							</div>
                        </div>
                    </div>
                <?php $i++;
            }
		}
		?>
		
		<div class="select_msg col-sm-12 no-record-found" style="display:none;">
			<div>
				No Project Found	
			</div>
		</div>
		
    </div>
<script>
$(function() {

	$('.panel-collapse').on('hidden.bs.collapse', function(e) {
		$('.show-hide-program',$('.panel.list-panel:not(.no-data-avail)')).each(function(index, el) {
			if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
				$(this).tooltip('hide').attr('title', 'Expand').attr('data-original-title', 'Expand');
				$(".toggle-accordion").removeClass('toggle-active').attr('data-original-title','Expand All');
			} else {
				$(this).attr('title', 'Collapse').attr('data-original-title', 'Collapse');
			}
		});
		$('.icon-chart,.project-multi-select', $(this).parents('.panel.list-panel')).hide();
	});
	
	$('.panel-collapse').on('shown.bs.collapse', function(e) {
		var $parent = $(this);
		$('.show-hide-program',$('.panel.list-panel:not(.no-data-avail)')).each(function(index, el) {
			if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
				$(this).tooltip('hide').attr('title', 'Expand').attr('data-original-title', 'Expand');
			} else {
				$(this).tooltip('hide').attr('title', 'Collapse').attr('data-original-title', 'Collapse');
			}
		});
		$('.icon-chart', $(this).parents('.panel.list-panel')).show();
		
	});

});
</script>

