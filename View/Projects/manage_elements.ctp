<?php
$task_time_start = microtime_float();
echo $this->Html->css('projects/list-grid.min'); ?>
<?php echo $this->Html->css('projects/manage_elements.min'); ?>
<?php echo $this->Html->script('projects/plugins/jquery.dot.min', array('inline' => true));  ?>
<?php echo $this->Html->script('projects/plugins/ellipsis-word.min', array('inline' => true));  ?>
<?php echo $this->Html->script('projects/manage_tasks.min', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/color_changer.min', array('inline' => true)); ?>

<?php
$workspaceArray = $data['workspace']['Workspace'];
$ws_start =  ( isset($workspaceArray['start_date']) && !empty($workspaceArray['start_date'])) ? date('d M, Y',strtotime($workspaceArray['start_date'])) : 'N/A';
$ws_end = ( isset($workspaceArray['end_date']) && !empty($workspaceArray['end_date'])) ? date('d M, Y',strtotime($workspaceArray['end_date'])) : 'N/A';


?>
<script type="text/javascript">
	$('html').addClass('no-scroll');

</script>
<div class="row">

	<div class="col-xs-12">

				       <section class="main-heading-wrap pb6">
<div class="main-heading-sec">
				<h1><?php echo htmlentities($data['workspace']['Workspace']['title'], ENT_QUOTES); ?></h1>
					<?php
					$menu_project_id = null;
					if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0])) {
						$menu_project_id = $this->params['pass'][0];
					}
				?>
				<div class="subtitles">
					<span> <?php
					   if(isset($workspaceArray['start_date']) && !empty($workspaceArray['start_date'])){
					  echo $ws_start;
					  ?>
					  </span> → <span><?php
						echo $ws_end;
					   }else{  echo "No Schedule"; }?></span>
				</div>
				<div class="header-right-side-icon">
					<span class="headertag ico-project-summary tipText" title="Tag Team Members" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'tags', 'action' => 'add_tags_team_members', 'project' => $project_id, 'workspace' => $workspace_id, 'type' => 'workspace', 'admin' => false)); ?>"></span>
					<span class="ico-nudge ico-workspace tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $project_id, 'workspace' => $workspace_id, 'type' => 'workspace', 'admin' => false)); ?>"></span>

						<?php


							$currentTasks = $this->ViewModel->checkCurrentWorkspace($project_id, $workspace_id);

							$showTip = 'Set Bookmark';
							$pinClass = '';
							$pinitag = '<i class="headerbookmark"></i>';
							if( $currentTasks > 0 ){
								$showTip = 'Clear Bookmark';
								$pinClass = 'remove_pin';
								//$pinitag = '<i class="current_task_icon_logo"></i>';
								$pinitag = '<i class="headerbookmarkclear"></i>';
							}

						 ?>
					          <a class="tipText fav-current-task bookmark-wsp <?php echo $pinClass;?>" data-projectid="<?php echo $project_id; ?>" data-wspid="<?php echo $workspace_id; ?>" href="#" data-original-title="<?php echo $showTip;?>"><?php echo $pinitag; ?></a>
				</div>
	</div>
			</section>


	<?php echo $this->element('../Projects/partials/project_header_image', array('p_id' => $this->params['pass'][0]));
	 $wsp_permissions = $this->ViewModel->getWspPermission($data['workspace']['Workspace']['id']);

	?>

		<div class="box-content postion">
			<div class="header-link-top-right ws-header-drop">
			<div class="wsp-icon-wrap">

<?php

	if (isset($project_id) && !empty($project_id)) {

	$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

	$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
	}
	################################################################################################################
	 //$wsp_signoff_comment = wsp_signoff_comment($data['workspace']['Workspace']['id']);
	$project_status = $this->Permission->project_status($project_id)[0][0]['prj_status'];
	$worksapce_status = $this->Permission->worksapce_status($workspace_id)[0][0]['prj_status'];

	$wsp_signoff_comment = wsp_signoff_comment($workspace_id);

	$get_progressing_workspace_element = get_progressing_workspace_element($workspace_id);

?>

<a href="#" class="tipText ws-filter-button h-common-btn" title="filter Tasks" data-target="#mid_model_box" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'workspaces', 'action' => 'filter_tasks', $project_id, $workspace_id, 'admin' => FALSE ), TRUE ); ?>"><i class="filter-icon filterblack"></i></a>
<?php
if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) {



if($worksapce_status != 'not_spacified' && $worksapce_status != 'not_started' && $worksapce_status != 'completed'){ ?>
        <?php
			if( !empty($get_progressing_workspace_element)  ){
				$signoffmsg = "This Workspace cannot be signed off because it has Tasks in progress.";
			}


			if( !empty($get_progressing_workspace_element)  ){ ?>
			    <span class="hlt-sep">
            	<a href="#" class="tipText signoff-btn h-common-btn disable element-sign-off-restrict" title="Sign Off" data-msg="<?php echo $signoffmsg;?>" data-type="Share"><i class="signoffblack"></i></a>
				</span>
            <?php
        	}  else { ?>
			   <span class="hlt-sep">
            	<a href="#" class="tipText signoff-btn h-common-btn" data-toggle="modal" data-target="#signoff_comment_box" data-remote="<?php echo SITEURL;?>workspaces/tasks_signoff/<?php echo $workspace_id; ?>" title="Sign Off"  data-type="Share"><i class="signoffblack"></i></a>
        <?php } ?>

<?php } else if ($worksapce_status == 'completed') {
		$flipclass = '';
		if( isset($wsp_signoff_comment) && $wsp_signoff_comment != 0 ){
			$flipclass ='fa-rotate-180';
		?>   <span class="hlt-sep">
			<a href="#" class="tipText signoff-btn h-common-btn disable" title="Click To See Comment and Evidence"  data-toggle="modal" data-target="#signoff_comment_show" data-remote="<?php echo SITEURL;?>workspaces/show_signoff/<?php echo $workspace_id; ?>"><i class="signoffblack"></i></a>
		<?php }  if($project_status != 'completed'){ ?>

			<a href="#" class="tipText reopen-btn h-common-btn element-sign-off"  title="Reopen" data-msg="Are you sure you want to reopen this Workspace?" data-toggle="confirmation" data-header="Reopen Workspace"  data-id="<?php echo $workspace_id; ?>"><i class="reopenblack"></i></a>
			</span>
<?php    }

 } ?>

 </span>

<?php } ?>

			<?php if(isset($wsp_permissions[0]['user_permissions']) && in_array($wsp_permissions[0]['user_permissions']['role'],array('Creator','Group Owner','Owner')) ){
				 if($data['workspace']['Workspace']['sign_off'] != 1){

			?>
			<span class="hlt-sep">
				<a data-toggle="modal" class="share-button h-common-btn wssb tipText" title="" href="<?php echo SITEURL ?>workspaces/quick_share/<?php echo $project_id; ?>/<?php echo $this->params['pass']['1']; ?>" data-target="#modal_medium" rel="tooltip" data-original-title="Share Workspace"><i class="share-icon"></i></a>	</span>

			<?php }else{ ?>
				<span class="hlt-sep">
					<a class="share-button h-common-btn wssb progres-mt-btn disable" title="This Workspace Is Signed Off" rel="tooltip" style="cursor: default;" ><i class="share-icon"></i></a>
				</span>
			<?php }
			} ?>

			<?php
			$wspPermit = $wsp_permissions[0]['user_permissions'];
			$wspData = $wsp_permissions[0]['workspaces'];
			$modal_title = '<i class="fa fa-exclamation-triangle"></i>&nbsp;Warning';
			$user_id = $this->Session->read('Auth.User.id');
			$message = '';
			if( isset($wspPermit['p_task_add']) && $wspPermit['p_task_add'] == 1 ) {
				if( $wspData['wsp_sign_off'] !=1 ){
					$curdate =  $this->Wiki->_displayDate(date("Y-m-d h:i:s A"),$format = 'Y-m-d');
					$wspStartDate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($wspData['wsp_start_date'])),$format = 'd M, Y h:i:s A');
/* 					if((isset($wspData['wsp_end_date']) && !empty($wspData['wsp_end_date']) && $wspData['wsp_end_date'] != '1970-01-01' && $wspData['wsp_end_date'] < $curdate ) ){
						$message ="You cannot add a Task because the Workspace end date has passed.";
					} */
					if(
					( !isset($wspData['wsp_start_date']) || $wspData['wsp_start_date'] == '1970-01-01' ) &&
					( !isset($wspData['wsp_end_date']) ||  $wspData['wsp_end_date'] == '1970-01-01' ) ){
						$message ="You cannot add a Task because the Workspace is not scheduled.";
					}
					if(!isset($wspData['wsp_start_date'])){
						$message ="Please add a schedule to this workspace first.";
					}
				}
				else if(isset($wspData['wsp_start_date'])){
					$message ="You cannot add a Task because the Workspace has been signed off.";
					$modal_title = 'Add Task';
				}
				$t_disabled  = '';
				$t_cursor = '';
				if( isset($wspData['sign_off']) && $wspData['sign_off'] == 1 ){
					$t_disabled = 'disable';
					$modal_title = "Workspace Is Signed Off";
					$t_cursor ="cursor:default !important; ";
					$message ="You cannot add a Task because the Workspace has been signed off.";
				}
				if(isset($message) && !empty($message)){
					if( isset($t_disabled) && !empty($t_disabled) ){
				?>
						<a href="#" class="share-button h-common-btn wssb progres-mt-btn tipText disable add-task-disable" title="<?php echo $modal_title;?>" style="<?php echo $t_cursor; ?>" rel="tooltip" ><i class="workspace-icon"></i></a>
					<?php } else { ?>
						<a href="#" class="share-button h-common-btn wssb progres-mt-btn tipText disable add-task-disable" title="Add Task" rel="tooltip" data-title="<?php echo $message;?>"><i class="workspace-icon"></i></a>
					<?php } ?>
				<?php } else { ?>
					<a href="#" class="share-button h-common-btn wssb progres-mt-btn tipText" title="Add Task" rel="tooltip" data-toggle="modal" data-target="#popup_model_box" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'get_popup', 0, $workspace_id, 'admin' => FALSE ), TRUE ); ?>"><i class="workspace-icon"></i></a>
				<?php
				}
			}
			 ?>




			<?php

							$wsp_disabled = '';
							$wsp_tip = '';
							$cursor = '';
							if(isset($workspaceArray['sign_off']) && !empty($workspaceArray['sign_off']) && $workspaceArray['sign_off'] == 1 ){

								$wsp_disabled = 'disable';
								$wsp_tip = "Workspace Is Signed Off";
								$cursor =" cursor:default !important; ";

							}?>


	<?php
			//pr($wsp_permissions[0]);
			 if( !empty($wsp_permissions[0]['user_permissions']['p_edit']) && $wsp_permissions[0]['user_permissions']['p_edit'] == 1 ) {

			 if($data['workspace']['Workspace']['sign_off'] != 1){
			 ?>
			<span class="hlt-sep">
			<a href="#" data-remote="<?php echo Router::url(array('controller' => 'workspaces', 'action' => 'edit_workspace', $project_id, $workspace_id)); ?>" data-toggle="modal" data-target="#modal_edit_wsp" class="edit-button h-common-btn tipText " rel="tooltip" title="" data-original-title="Edit Workspace"><i class="edit-icon"></i> </a></span>
			 <?php }else{ ?>
			 <span class="hlt-sep">
			<a class="edit-button h-common-btn tipText <?php echo $wsp_disabled;?>" title="This Workspace Is Signed Off" rel="tooltip" href="javascript:void(0);" id="btn_select_workspace" style="<?php echo $cursor;?>" >
							<i class="edit-icon"></i>
			</a></span>

			<?php  } }

			?>

			<?php //pr($wsp_permissions[0]);
			 if( !empty($wsp_permissions[0]['user_permissions']['p_delete']) && $wsp_permissions[0]['user_permissions']['p_delete'] == 1 ) { ?>
			<a  data-toggle="modal" data-target="#modal_delete"    class="workspace-button h-common-btn tipText delete-an-item" data-remote="<?php echo Router::Url( array( "controller" => "workspaces", "action" => "delete_an_item", $project_id, $workspace_id, workspace_pwid($project_id,$workspace_id),  'admin' => FALSE ), true ); ?>" title="" rel="tooltip" data-original-title="Delete Workspace" href=""><i class="deleteblack"></i> </a>
			<?php } ?>


				</div>
			</div>
			<div class="row ">


				<div class="col-xs-12">

				<div class="sep-header-fliter">


<?php
				$taskCount = $this->ViewModel->getTaskCount($data['workspace']['Workspace']['id']);


				echo $this->element('../Projects/partials/task_settings', array('menu_project_id' => $menu_project_id,'wsp_permissions'=>$wsp_permissions,'taskCount'=>$taskCount)); ?>

				</div>

				<div class="competencies-tab mt0">
					<div class="row">
						<div class="col-md-9">
							<ul class="nav nav-tabs" id="wsp_tabs">
								<li class="active">
									<a data-toggle="tab" data-type="info" class="active tab_info" data-target="#tab_info" href="#tab_info" aria-expanded="true">INFORMATION</a>
								</li>
								<li class="">
									<a data-toggle="tab" data-type="teams" class="active tab_teams" data-target="#tab_teams" href="#tab_teams" aria-expanded="true">team</a>
								</li>
								<li class="">
									<a data-toggle="tab" data-type="task_list" class=" tab_task_list" data-target="#tab_task_list" href="#tab_task_list" aria-expanded="true">Activities</a>
								</li>
								<li>
									<a data-toggle="tab" data-type="searchs" class="tab_wsp " data-target="#tab_wsp" href="#tab_wsp" aria-expanded="true">Work Board</a>
								</li>
							</ul>
						</div>
						<div class="col-md-3">
							<div class="input-group search-skills-box" style="display: none;">
								<input id="temp_search " type="text" class="form-control search-box" data-type="task_list" placeholder="Search for Tasks..." style="display: block;" autocomplete="off">
								<span class="input-group-btn">
									<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
									<button class="btn clear-btn" type="button" style="display: none;"><i class="clearblackicon search-clear"></i></button>
								</span>
							</div>
						</div>

					</div>
				</div>

					<div class="box noborder background-gray">
                        <div class="box-header nopadding noborder" style="background: none repeat scroll 0 0 #ecf0f5; height: auto">

                            <div class="modal modal-success fade " id="lg_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
								</div>
                            </div>
                        <!-- // PASSWORD DELETE -->
						<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content"></div>
							</div>
						</div>
							<!-- END MODAL BOX -->
                        </div>
						<!-- END CONTENT HEADING -->
					<div class="box noborder">
					<div class="box-body nopadding wsp-task-scroll ts-data-wrap">

						<div class="tab-content">
							<div id="tab_info" class="tab-pane fade active in" data-type="info">
								<?php echo $this->element('../Projects/sections/workspace_info'); ?>
							</div>
							<div id="tab_teams" class="tab-pane fade" data-type="teams">
								<input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        		<input type="hidden" name="paging_total" id="paging_total" value="0">
								<div class="project-summary-wrap">
								   <div class="ps-col-header">
								      <div class="ps-col tm-col-1">
								         <span class="ps-h-one">Name <span class="total-data"></span>
									         <span class="sort_order active" data-by="first_name" data-order="desc" data-type="teams" title="" data-original-title="Sort By First Name">
										         <i class="fa fa-sort" aria-hidden="true"></i>
										         <i class="fa fa-sort-asc" aria-hidden="true"></i>
										         <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
									         <span class="sort_order" data-by="last_name" data-order="desc" data-type="teams" title="" data-original-title="Sort By Last Name">
										         <i class="fa fa-sort" aria-hidden="true"></i>
										         <i class="fa fa-sort-asc" aria-hidden="true"></i>
										         <i class="fa fa-sort-desc" aria-hidden="true"></i>
									         </span>
								         </span>
								         <span class="ps-h-two sort_order" data-by="job_title" data-order="desc" data-type="teams" title="" data-original-title="Sort By Job Title">
									         Title
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="ps-h-two sort_order" data-by="role" data-order="desc" data-type="teams" title="" data-original-title="Sort By Role">
									         Role
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-2">
								         Effort
								         <span class="sort_order" data-by="completed_hours" data-order="desc" data-type="teams" title="" data-original-title="Sort By Completed Hours">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="remaining_hours" data-order="desc" data-type="teams" title="" data-original-title="Sort By Remaining Hours">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="change_hours" data-order="desc" data-type="teams" title="" data-original-title="Sort By Change">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-3">
								         Costs
								         <span class="sort_order" data-by="escost" data-order="desc" data-type="teams" title="" data-original-title="Sort By Budget">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="spcost" data-order="desc" data-type="teams" title="" data-original-title="Sort By Actual">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <!-- <span class="sort_order" data-by="cost_status" data-order="desc" data-type="teams" title="" data-original-title="Sort By Status">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span> -->
								      </div>
								      <div class="ps-col tm-col-4">
								         Risks
								         <span class="sort_order" data-by="high_pending_risks" data-order="desc" data-type="teams" title="" data-original-title="Sort By High Pending Risks">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="severe_pending_risks" data-order="desc" data-type="teams" title="" data-original-title="Sort By Severe Pending Risks">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="total_risks" data-order="desc" data-type="teams" title="" data-original-title="Sort By Total Risks Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-5">
								         Competencies
								         <span class="sort_order" data-by="user_skills" data-order="desc" data-type="teams" title="" data-original-title="Sort By Skills Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="user_subjects" data-order="desc" data-type="teams" title="" data-original-title="Sort By Subjects Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="user_domains" data-order="desc" data-type="teams" title="" data-original-title="Sort By Domains Count">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-6">
								         Last Activity
								         <span class="sort_order" data-by="message" data-order="desc" data-type="teams" title="" data-original-title="Sort By Last Activity">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								         <span class="sort_order" data-by="updated" data-order="desc" data-type="teams" title="" data-original-title="Sort By Last Activity Date">
									         <i class="fa fa-sort" aria-hidden="true"></i>
									         <i class="fa fa-sort-asc" aria-hidden="true"></i>
									         <i class="fa fa-sort-desc" aria-hidden="true"></i>
								         </span>
								      </div>
								      <div class="ps-col tm-col-7">
								         Actions
								      </div>
								   </div>
								   <div class="project-summary-data team-data list-wrapper1" data-flag="true">
								   	<?php
									// echo $this->element('../Projects/sections/wsp_team');
									?>
								   </div>
								</div>

							</div>

							<div id="tab_task_list" class="tab-pane fade  " data-type="task_list">
								<input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        		<input type="hidden" name="paging_total" id="paging_total" value="0">
								<div class="project-summary-wrap">

									<div class="ps-col-header">
										<div class="ps-col ts-col-1">
											<span class="ps-h-one sort_order" data-type="task_list" data-by="ele_title" data-order="">Tasks <span class="total-data">(0)</span>
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="ps-h-two sort_order" data-type="task_list" data-by="start_date" data-order="">
												Start
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="ps-h-two sort_order" data-type="task_list" data-by="end_date" data-order="">
												End
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="ps-h-two sort_order active" data-type="task_list" data-by="el_status" data-order="desc">
												Status
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="ps-h-two sort_order tipText" title="Sort By Confidence Level" data-type="task_list" data-by="confidence_level" data-order="desc">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
										</div>
										<div class="ps-col ts-col-2">
											Area
											<span class="com-short sort_order tipText" title="Sort By Area" data-type="task_list" data-by="area_title" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>

										</div>
										<div class="ps-col ts-col-3"> Team
											<span class="com-short sort_order tipText" title="Sort By Owner Count" data-type="task_list" data-by="owner_count" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Sharer Count" data-type="task_list" data-by="sharer_count" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Role" data-type="task_list" data-by="el_role" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
										</div>

										<div class="ps-col ps-col-8">
										    Effort
                                            <span class="sort_order tipText" title="Sort By Completed Hours" data-by="completed_hours" data-order="desc" data-type="teams">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span class="sort_order tipText" title="Sort By Remaining Hours" data-by="remaining_hours" data-order="desc" data-type="teams">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span class="sort_order tipText" title="Sort By Change" data-by="change_hours" data-order="desc" data-type="teams">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span class="sort_order tipText" title="Sort By Total Hours" data-by="total_hours" data-order="desc" data-type="teams">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
										</div>



										<div class="ps-col ts-col-4">
											Assets
											<span class="com-short sort_order tipText" title="Sort By Links Count" data-type="task_list" data-by="el_tot" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Notes Count" data-type="task_list" data-by="en_tot" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Documents Count" data-type="task_list" data-by="ed_tot" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Mindmups Count" data-type="task_list" data-by="em_tot" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Decision Count" data-type="task_list" data-by="dc_tot" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Feedbacks Count" data-type="task_list" data-by="fb_tot" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Votes Count" data-type="task_list" data-by="vt_tot" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
										</div>
										<div class="ps-col ts-col-5">
											Costs
											<span class="com-short sort_order tipText" title="Sort By Budget" data-type="task_list" data-by="escost" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Actual" data-type="task_list" data-by="spcost" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Status" data-type="task_list" data-by="c_status" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
										</div>
										<div class="ps-col ts-col-6"> Risks
											<span class="com-short sort_order tipText" title="Sort By High Pending Risks" data-type="task_list" data-by="high_risk_total" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Severe Pending Risks" data-type="task_list" data-by="severe_risk_total" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
											<span class="com-short sort_order tipText" title="Sort By Total Risks Count" data-type="task_list" data-by="all_risk_total" data-order="">
												<i class="fa fa-sort" aria-hidden="true"></i>
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
												<i class="fa fa-sort-desc" aria-hidden="true"></i>
											</span>
										</div>
										<div class="ps-col ts-col-7"> Actions </div>
									</div>
									<div class="project-summary-data list-wrapper wsp-activities" data-flag="true" >
										<?php
										// echo $this->element('../Projects/sections/wsp_activities');
										?>
									</div>


								</div>
							</div>
							<div id="tab_wsp" class="tab-pane fade  work-bord-wrap">
							<?php $time_layout_start = microtime_float();
								// LOAD PARTIAL WORKSPACE LAYOUT FILE FOR LOADING DYNAMIC WORKSPACE AREAS
								echo $this->element('../Projects/partials/workspace_ele_layout', ['load' => true,'wsp_permissions'=>$wsp_permissions,'taskCount'=>$taskCount]);

								$time_layout_start = microtime_float();
								$time_layout_end = microtime_float();
								$time_layout = $time_layout_end - $time_layout_start;
								//echo "Workspace Layout load Total time : ".$time_layout." seconds\n";
							?>
							</div>
							</div>
					</div><!-- /.box-body -->
					</div>
					</div><!-- /.box -->
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="popup_modal" style="display: none;" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content border-radius"><div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="<?php echo SITEURL;?>images/ajax-loader-1.gif" style="margin: auto;"></div></div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>
<style>
	.popover span:first-child {
		width: auto !important;
	}
	section.content {
		padding-top: 0;
	}
	.sort_order {
		cursor: pointer;
	}

</style>
<?php
$task_time_end = microtime_float();
$task_load_time = $task_time_end - $task_time_start;

?>
<input type="hidden" id="taskloadtime" value="<?php echo $task_load_time; ?>" >


	<span id="loadtimevalues" style="display:none;">
		<p id="sidebartimes">loading...</p>
		<p id="headertimes">loading...</p>
		<p id="taskpagetime">loading...</p>
	</span>
<script type="text/javascript">
	$(function(){

		$('body').delegate(".add-task-disable", "click", function(e) {
            e.preventDefault()
            var message = $(this).attr("data-title");
		  	$("#modal-alert .modal-content").html('<div class="modal-header">\
						        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
									<h3 class="modal-title">Add Task</h3>\
						        </div>\
						      	<div class="modal-body">\
							        <button type="button" class="close" data-dismiss="modal">&times;</button>\
							        Please set project dates before setting workspace dates.\
						      	</div>\
						      	<div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">Close</button></div>');
		  	$("#modal-alert .modal-body").html('<div class="add-task-message-text">'+message+'</div>');
            $("#modal-alert").modal('show');
        });

		$.filterByType = false;
		$.selected_filters = {status: [], type: [], assign: []};

		$.show_filtered_data = function(data){
			$.ajax({
				type: 'POST',
				data: data,
				url: $js_config.base_url + 'entities/get_workspace_task_template',
				global: false,
				success: function (response) {
						$("#workspace").html(response);
		                $('.tooltip').remove();
		                // $.bind_context_menu();
		                $.bind_dragDrop();
		                $('.color_bucket').each(function() {
		                    var $color_box = $(this).parent().find('.ws_color_box');
		                    $(this).data('ws_color_box', $color_box);
		                    $color_box.data('color_bucket', $(this));
		                })
		                if(!$.filterByType) {
		                	$('.el').show();
		                }
				},
			});
		}

		$('#mid_model_box').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
			if($.filterByType) {
				$('.filter-icon').addClass('filterblue').removeClass('filterblack');
				var data = {
						project_id: $js_config.project_id,
						workspace_id: $js_config.workspace_id,
						project_task_type: $.selected_filters.type,
						assigned_user: $.selected_filters.assign,
						task_type: 'project_type',
						status: $.selected_filters.status,
						generalflag: 0
					};
				$.filterByType = false;
				$.show_filtered_data(data);
				$.getWspTaskActivities();
			}
		});

		$.signoff_btn = false;
		$.show_wsp_options = () => {
			$.ajax({
					type: 'POST',
					data: {
						project_id: $js_config.project_id,
						workspace_id: $js_config.workspace_id,
					},
					url: $js_config.base_url + 'workspaces/get_workspace_options',
					success: function (response) {
						$(".header-link-top-right.ws-header-drop").html(response);
					},
				});
				$.signoff_btn = false;
		}
		$('#signoff_comment_box').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
			if($.signoff_btn) {
				$.show_wsp_options();
				$.reload_workspace();
			}
		});

	$.current_delete = {};
		$('body').delegate('.delete-an-item', 'click', function(event) {
			event.preventDefault();
			$.current_delete = $(this);
	});

	$('#modal_delete').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
		$.current_delete = {};
	});

	$('body').delegate('.bookmark-wsp', 'click', function(event) {
			event.preventDefault();
			var $that = $(this);
			var project_id = $that.data('projectid');
			var workspace_id = $that.data('wspid');

			if( !$(this).hasClass('remove_pin') ){

				if( project_id > 0 && project_id !== "" && workspace_id > 0 && workspace_id !== ""  ){
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id, 'workspace_id': workspace_id ,'status': 'add'}),
						url: $js_config.base_url + 'workspaces/current_workspace',
						global: false,
						success: function (response) {
							if( response.success ){
								$that.tooltip('hide')
								          .attr('data-original-title', 'Clear Bookmark')
								          .tooltip('fixTitle')
								          .tooltip('show');
								$that.find('i').removeClass('headerbookmark').addClass('headerbookmarkclear');
								$that.addClass('remove_pin');

							}
						},
					});
				}
			}

			if( $(this).hasClass('remove_pin') ){
				if( project_id > 0 && project_id !== "" && workspace_id > 0 && workspace_id !== ""  ){
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id, 'workspace_id': workspace_id, 'status': 'remove' }),
						url: $js_config.base_url + 'workspaces/current_workspace',
						global: false,
						success: function (response) {
							if( response.success ){

								$that.removeClass('remove_pin');
								$that.find('i').removeClass('headerbookmarkclear').addClass('headerbookmark');
								$that.tooltip('hide')
								          .attr('data-original-title', 'Set Bookmark')
								          .tooltip('fixTitle')
								          .tooltip('show');
							}
						},
					});
				}
			}

		})


		$('body').on('click', '.element-sign-off-restrict', function (e) {
            e.preventDefault();

            var $this = $(this),
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes'),
                    $no = $cbox.find('#sign_off_no');

            var $span_text = $yes.find('span.text'),
            $div_progress = $yes.find('div.btn_progressbar');

            // set message
            var body_text = $this.attr("data-msg");
			BootstrapDialog.show({
	            title: '<h3 class="h3_style">Sign Off</h3>',
	            message: body_text,
	            type: BootstrapDialog.TYPE_DANGER,
	            draggable: false,
	            buttons: [
	                {
	                    label: ' Close',
	                   // icon: 'fa fa-times',
	                    cssClass: 'btn-danger',
	                    action: function(dialogRef) {
	                        dialogRef.close();
	                    }
	                }
	            ]
	        });

        });



        $('body').on('click', '.element-sign-off', function (e) {
            e.preventDefault();

            var $this = $(this),
                    data = $this.data(),
                    id = data.id,
                    title = data.header,
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');

            // set message
            var body_text = $this.attr('data-msg');

			var post = {'data[Workspace][id]': id, 'data[Workspace][sign_off]': data.value},
                        data_string = $.param(post);


			BootstrapDialog.show({
			title: title,
			message: body_text,
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Reopen',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'workspaces/workspace_signoff',
								type: "POST",
								data: data_string,
								dataType: "JSON",
								global: false,
								success: function (response) {
                                    if(response.success) {
                                        if(response.content){
                                            // send web notification
                                            $.socket.emit('socket:notification', response.content.socket, function(userdata){});
                                        }
                                    }
									$.signoff_btn = true;
									$.show_wsp_options();
									$.reload_workspace();
									$.reload_wsp_progress().done(function(res){
				                    	$.adjust_resize();
				                    });
								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							setTimeout(function () {
								dialogRef.close();
							}, 1500);
						})
					}
				},
				{
					label: ' Cancel',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}]
			});
		});

	    ($.wsp_teams = function(){

	        var data = {project_id: $js_config.project_id, workspace_id: $js_config.workspace_id }

	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'projects/wsp_teams',
	            data: data,
	            success: function(html) {
	                $('.project-summary-data.team-data').html(html);
	            }
	         });
	    })();

	    ($.wsp_activities = function(){

	        var data = {project_id: $js_config.project_id, workspace_id: $js_config.workspace_id }

	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'projects/wsp_activities',
	            data: data,
	            success: function(html) {
	                $('.project-summary-data.wsp-activities').html(html);
	            }
	         });
	    })();
	})
</script>

<div class="modal modal-success fade " id="mid_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog sm-modal-box">
        <div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-danger fade" id="signoff_comment_box" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-danger fade" id="signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-warning fade" id="confirm_signoff" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="modal_task_assignment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div id="modal-alert" class="modal modal-danger fade" tabindex="-1">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
		    <div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title">Add Task</h3>
		    </div>
		  	<div class="modal-body">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        Please set project dates before setting workspace dates.
		  	</div>
		  	<div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">Close</button></div>
		</div>
	</div>
</div>
<div class="modal modal-success fade" id="modal_reminder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="modal_edit_wsp" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog add-update-modal">
        <div class="modal-content"></div>
    </div>
</div>
<style type="text/css">
	.no-scroll {
	    overflow: hidden;
	}
	.tab-pane#tab_info {
		overflow-x: hidden;
		overflow-y: overlay;
		background-color: #f1f3f4;
	}
	.popover p:first-child {
	    font-weight: 600 !important;
	    width: 170px !important;
	}
	.popover p:nth-child(2) {
	    font-size: 11px;
	}

	.popover p {
	    margin-bottom: 2px !important;
	}

	.paint-box {
		position: absolute;
	    background: none repeat scroll 0 0 #fff;
	    width: 268px;;
	    padding: 5px;
	    margin: 0;
	    border: 1px solid #ddd;
	    border-radius: 5px;
	    z-index: 9999;
	    right: 0;
	    display: none;
	}
	.ps-data-row.opened .ps-col.ts-col-7 a {
	    display: inline-block;
	}

	.text-panel-maroon {
	    background-color:  #8a0000;
	    color:  #8a0000;
	}

	.text-panel-red {
	    background-color: #ca0000;
	    color: #ca0000;
	}

	.text-panel-lightred {
	    background-color: #fa0000;
	    color: #fa0000;
	}

	.text-panel-darkorange {
	    background-color:  #843c0c;
	    color:  #843c0c;

	}

	.text-panel-orange {
	   background-color:  #c55a11;
	    color:  #c55a11;
	}

	.text-panel-lightorange {
	    background-color:  #ee8640;
	    color:  #ee8640;
	}

	.text-panel-darkyellow {
	    background-color:  #7f6000;
	    color:  #7f6000;
	}

	.text-panel-yellow {
	    background-color:  #c89800;
	    color:  #c89800;
	}

	.text-panel-lightyellow {
	     background-color: #ffc000;
	    color: #ffc000;
	}

	.text-panel-darkgreen {
	    background-color:  #385723;
	    color:  #385723;
	}

	.text-panel-green {
	    background-color:  #548235;
	    color:  #548235;
	}

	.text-panel-lightgreen {
	    background-color:  #77b64c;
	    color:  #77b64c;
	}

	.text-panel-darkteal {
	    background-color:  #1b6d6b;
	    color:  #1b6d6b;
	}

	.text-panel-teal {
	    background-color:  #29a3a0;
	    color:  #29a3a0;
	}

	.text-panel-lightteal {
	    background-color:  #3cd0cc;
	    color:  #3cd0cc;
	}

	.text-panel-darkaqua {
	    background-color:  #1f4e79;
	    color:  #1f4e79;
	}

	.text-panel-aqua {
	    background-color:  #2e75b6;
	    color:  #2e75b6;
	}

	.text-panel-lightaqua {
	    background-color:  #74a9da;
	    color:  #74a9da;
	}

	.text-panel-navy {
	    background-color:  #000080;
	    color:  #000080;
	}

	.text-panel-blue {
	    background-color:  #0000f0;
	    color:  #0000f0;
	}

	.text-panel-lightblue {
	    background-color:  #6363ff;
	    color:  #6363ff;
	}

	.text-panel-darkpurple {
	    background-color:  #522375;
	    color:  #522375;
	}

	.text-panel-purple {
	    background-color:  #7b35af;
	    color:  #7b35af;
	}

	.text-panel-lightpurple {
	    background-color:  #af7ad6;
	    color:  #af7ad6;
	}

	.text-panel-darkmagenta {
	    background-color:  #7d0552;
	    color:  #7d0552;
	}

	.text-panel-magenta {
	    background-color:  #bc087c;
	    color:  #bc087c;
	}

	.text-panel-lightmagenta {
	    background-color:  #f72bae;
	    color:  #f72bae;
	}

	.text-panel-darkgray {
	    background-color:  #3b3838;
	    color:  #3b3838;
	}

	.text-panel-gray {
	    background-color:  #7f7f7f;
	    color:  #7f7f7f;
	}

	.text-panel-lightgray {
	    background-color:  #b5b5b5;
	    color:  #b5b5b5;
	}
	.ps-col-header.none-selection {
		pointer-events: none;
	}
	.list-assets-icons {
		cursor: pointer !important;
	}
	.opp-project-left .clockwhite[data-toggle="modal"] {
		cursor: pointer;
	}
</style>