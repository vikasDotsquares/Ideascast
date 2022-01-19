<?php

$ws_exists = true;
$ws_count = $prj_count = 0;
if( isset($menu_project_id) && !empty($menu_project_id)) {
	// echo $menu_project_id;
	$prj_count = $this->ViewModel->user_project_count();
	$ws_count = $this->ViewModel->project_workspace_count($menu_project_id);
	if( empty($ws_count) ){
		$ws_exists = false;
	}
}

 ?>
 <?php
    $myprojects = myprojects($this->Session->read('Auth.User.id'));
    $groupprojects = groupprojects($this->Session->read('Auth.User.id'));
    $receivedprojects = receivedprojects($this->Session->read('Auth.User.id'));


    $all_projects = [];
    if (is_array($myprojects)) {
        $myprojects = array_keys($myprojects);
        $all_projects = array_merge_recursive($all_projects, $myprojects);
    }
    if (is_array($groupprojects)) {
        $groupprojects = array_keys($groupprojects);
        $all_projects = array_merge_recursive($all_projects, $groupprojects);
    }
    if (is_array($receivedprojects)) {
        $receivedprojects = array_keys($receivedprojects);
        $all_projects = array_merge_recursive($all_projects, $receivedprojects);
    }

    $all_project = $this->ViewModel->project_signoff($all_projects);
    $user_projects = array_map(function($v){
        return trim($v);
    }, $all_project);


$p_permission = null;
if( isset($project_id) && !empty($project_id) ){
	 $uusde = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));
	 $p_permission = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));
}

 ?>
<section class="content-header nopadding clearfix sb_blog_parent">
	<div class="pull-left project-detail " >
		<?php /* ?>
		<span class="bg-blakish nomargin sb_blog">Created: <?php
		//echo date( 'd M Y h:i:s', $project_detail['Project']['created'] );
		echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['created']),$format = 'd M Y');
		?></span>
		<span class="bg-black sb_blog">Updated: <?php
		//echo date( 'd M Y h:i:s', strtotime( $project_detail['UserProject']['modified'] ) );
		echo $this->Wiki->_displayDate($date = date('Y-m-d',strtotime( $project_detail['UserProject']['modified'] )),$format = 'd M Y');
		?></span>
		<?php */ ?>
		<div class="col-sm-12 nopadding">
			<label class="custom-dropdown" style="width: 100%;">
                <?php
                    echo $this->Form->select('Project.id', $user_projects, array('escape' => false, 'empty' => 'Select Project', 'class' => 'form-control aqua', 'id' => 'user_projects', 'default' => $project_id));
                ?>
            </label>
        </div>
	</div>
	<?php

		if(isset($p_permission['ProjectPermission']['share_by_id']) && !empty($p_permission['ProjectPermission']['share_by_id'])){
		?>

        <div class="bg-blue sb_blog">Shared by: <?php echo  $this->Common->userFullname($p_permission['ProjectPermission']['share_by_id']); ?>, <?php
		 //echo date('d M Y h:i:s', strtotime($p_permission['ProjectPermission']['created']));
		 echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($p_permission['ProjectPermission']['created'])),$format = 'd M Y');
		 ?>

		 </div>
          <?php  } ?>


		<?php /*$toc = 'bg-green';
			$total_daysN = daysLeft(date('Y-m-d',strtotime($project_detail['Project']['start_date'])), date('Y-m-d',strtotime($project_detail['Project']['end_date'])));
			if(date('Y-m-d') > date('Y-m-d',strtotime($project_detail['Project']['start_date']))){
				 $total_complete_days = daysLeft($project_detail['Project']['start_date'], date('Y-m-d 12:00:00'));
			}else{
				 $total_complete_days = daysLeft($project_detail['Project']['start_date'], date('Y-m-d 12:00:00'));
			}

			if(date('Y-m-d')  <= date('Y-m-d',strtotime($project_detail['Project']['end_date'])) && date('Y-m-d')  >= date('Y-m-d',strtotime($project_detail['Project']['start_date']))){
				 $total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $project_detail['Project']['end_date'], 1);
			}else{
				 $total_remain_days = daysLeft(date('Y-m-d 12:00:00'), $project_detail['Project']['end_date'], 1)  ;
			}

			if(!empty($total_daysN) && !empty($total_complete_days)) {
				$dataP =   (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)) > 0 ?   (round( ( ($total_complete_days  * 100 ) / $total_daysN ), 0, 1)): 0;
				if($dataP > 100){
				$dataP = 100;
				$toc = 'bg-red';
				}

			}else{
				$dataP = 0;
			}

			if(isset($project_detail['Project']['sign_off']) && !empty($project_detail['Project']['sign_off'])){
				$dataP = 100;
				$toc = 'bg-red';
			}*/


		?>
		<?php /*if( isset($project_id) && !empty($project_id) ){
				 $uusde = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));
				 $pp_perm = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));

				if( ((isset($uusde)) && (!empty($uusde))) || (isset($project_level) && $project_level==1)  ||  (isset($pp_perm['ProjectPermission']['project_level'])   && $pp_perm['ProjectPermission']['project_level'] == 1)   ){  ?>
			<div class="progress-unique ">
				<div class="border bg-white ideacast-project-progress project-elapsed-bar" ><span class="pull-left hidden-sm hidden-md">Project Elapsed</span>
				<div  class="progress tipText" title="Days Remaining: <?php echo $total_remain_days; ?>">
					<div style="width:<?php echo $dataP."%"; ?>" aria-valuemax="100" aria-valuemin="50" aria-valuenow="50" role="progressbar" class="progress-bar <?php echo $toc ?>">

					</div>
				</div><span class="pull-left"><?php echo $dataP."%"; ?></span>
				</div>
			</div>
			<?php } }*/ ?>

		<div class=" pull-right pt2">

			<?php if ( (isset($project_id) && !empty($project_id)))
                    $dataOwner = $this->ViewModel->projectPermitType($project_id  , $this->Session->read('Auth.User.id') );
                    if ( (isset($project_id) && !empty($project_id))  && $dataOwner == 1 ) {

						$project_workspace_details = $this->ViewModel->getProjectWorkspaces( $project_id, 1 );
						if (isset($project_workspace_details) && !empty($project_workspace_details)) {
						?>
						<a data-toggle="modal" data-modal-width="600" class="btn btn-sm btn-success tipText" title="<?php tipText('Generate Report'); ?>" href="<?php echo SITEURL ?>export_datas/index/project_id:<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip" style="padding-right: 0;padding-left: 4px;padding-top: 3px; padding-bottom: 4px;" ><i class="icon-file-export"></i></a>

					<?php }
					}	?>


			<?php  if( isset($project_id) && !empty($project_id) ){
				$uusde = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));
				$pp_perm = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));

				if( ((isset($uusde)) && (!empty($uusde))) || (isset($project_level) && $project_level==1)  ||  (isset($pp_perm['ProjectPermission']['project_level'])   && $pp_perm['ProjectPermission']['project_level'] == 1)   ){

				?>

				<a class="btn btn-sm bg-black tipText" title="Results" type="button" href="<?php echo Router::Url(array("controller" => "projects", "action" => "task_results", $project_id), true); ?>">
					Results
				</a>


				<?php }
			} ?>
			<div class="btn-group action">
				<!-- Project Options -->

				<?php  if( isset($project_id) && !empty($project_id) ){
				 $uusde = $this->Common->userproject($project_id,$this->Session->read('Auth.User.id'));
				 $pp_perm = $this->Common->project_permission_details($project_id,$this->Session->read('Auth.User.id'));

				//********************* More Button ************************
				echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'reports' ));

				}
				?>
				<div class="btn-group action ">
					<a id="btn_go_back" data-original-title="Go Back" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'index', $this->params['pass']['0'], 'admin' => FALSE ), TRUE); ?>" class="btn btn-warning tipText pull-right btn-sm" > <i class="fa fa-fw fa-chevron-left"></i> Back </a>
				</div>
			</div>
		</div>

</section>



