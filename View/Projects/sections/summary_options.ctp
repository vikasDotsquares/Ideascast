<?php
$summary_options = $this->Permission->summary_options($project_id);
$summary_options = $summary_options[0];
$prj_role = $summary_options['up']['prj_role'];
$permit_edit = $summary_options['up']['permit_edit'];
$other_data = $summary_options[0];
$user_role = $other_data['user_role'];
$project_status = $other_data['prj_status'];
$projectSignoffComments = $other_data['signoff_comments'];

$ws_exists = (isset($summary_options['wsps']['prg_wsp_count']) && !empty($summary_options['wsps']['prg_wsp_count'])) ? true : false;
$projectSignOffWsp = (isset($summary_options['wsps']['prg_wsp_count']) && !empty($summary_options['wsps']['prg_wsp_count'])) ? $summary_options['wsps']['prg_wsp_count'] : 0;
$wsp_count = (isset($summary_options['all_wsp']['wsp_count']) && !empty($summary_options['all_wsp']['wsp_count'])) ? $summary_options['all_wsp']['wsp_count'] : 0;
$risk_count = $summary_options['rsk']['risk_count'];
$project_data = $summary_options['projects'];

$prj_disabled = '';
$prj_disabled_tip = '';
$prj_disabled_cursor = '';
$icons_disabled = false;
if(isset($project_data['sign_off']) && !empty($project_data['sign_off']) && $project_data['sign_off'] == 1 ){
	$prj_disabled = 'disable';
	$prj_disabled_tip = 'Project Is Signed Off';
	$prj_disabled_cursor = 'cursor:default !important;';
	$icons_disabled = true;
}

$quick_share_disable = '';
$quick_share_disable_tip = '';
$quick_share_disable_cursor = '';

$edit_button_disable = '';
$edit_button_disable_tip = '';
$edit_button_disable_cursor = '';
if(isset($project_data['sign_off']) && !empty($project_data['sign_off']) && $project_data['sign_off'] == 1 && $ws_exists){

	$quick_share_disable = 'disable';
	$quick_share_disable_tip = 'Project Is Signed Off';
	$quick_share_disable_cursor = 'cursor:default !important;';

}

$toc = 'bg-green';


if (isset($project_id) && !empty($project_id)) {

$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
}

// $project_status = $this->Permission->project_status($project_id)[0][0]['prj_status'];
// $projectSignOffWsp = $this->Permission->WspSOCount($project_id)[0][0]['PRG'];
// pr($projectSignOffWsp, 1);
// $risk_count = project_risk_status($project_id);
// $projectSignoffComments = $this->Permission->projectSignoffComments($project_id);
?>

<div class="header-link-top-right">

<?php
if (!empty($user_role)) {



if($project_status != 'not_spacified' && $project_status != 'not_started' && $project_status != 'completed'){ ?>
        <?php
			/* if( !empty($projectSignOffWsp) && $risk_count > 0  ){
				$signoffmsg = "This Project cannot be signed off because it has Workspaces and Risks in progress.";
			} else if( $risk_count > 0 ){
				$signoffmsg = "This Project cannot be signed off because it has active project Risks.";
			} else {
				$signoffmsg = "This Project cannot be signed off because it has Workspaces in progress.";
			} */
			
			if( !empty($projectSignOffWsp)   ){
				$signoffmsg = "This Project cannot be signed off because it has Workspaces in progress.";
			} 

			if( !empty($projectSignOffWsp) ){ ?>


            	<a href="#" class="tipText signoff-btn h-common-btn disable element-sign-off-restrict" title="Sign Off" data-msg="<?php echo $signoffmsg;?>" data-type="Share"><i class="signoffblack"></i></a>

            <?php
        	}  else { ?>

            	<a href="#" class="tipText signoff-btn h-common-btn" data-toggle="modal" data-target="#signoff_comment_box" data-remote="<?php echo SITEURL;?>projects/tasks_signoff/<?php echo $project_id; ?>" title="Sign Off"  data-type="Share"><i class="signoffblack"></i></a>
        <?php } ?>

<?php } else if ($project_status == 'completed') {
		$flipclass = '';
		if( isset($projectSignoffComments) && $projectSignoffComments != 0 ){
			$flipclass ='fa-rotate-180';
		?>
			<a href="#" class="tipText signoff-btn h-common-btn disable" title="Click To See Comment and Evidence"  data-toggle="modal" data-target="#signoff_comment_show" data-remote="<?php echo SITEURL;?>projects/show_signoff/<?php echo $project_id; ?>"><i class="signoffblack"></i></a>

		<?php } ?>

			<a href="#" class="tipText reopen-btn h-common-btn project-sign-off"  title="Reopen" data-msg="Are you sure you want to reopen this Project?" data-toggle="confirmation" data-header="Reopen Project"  data-id="<?php echo $project_id; ?>"><i class="reopenblack"></i></a>

    <?php } ?>



<?php } ?>


<?php
    $dataOwner = $user_role;
    if ( !empty($user_role) ) {
    	$project_type = CheckProjectType($project_id, $this->Session->read('Auth.User.id'));
		?>
		<span class="hlt-sep">

			<a href="<?php echo SITEURL ?>export_datas/index/project_id:<?php echo $project_id; ?>" class="tipText report-button h-common-btn" title="Generate Report" data-target="#modal_medium" data-toggle="modal"><i class="reportblack"></i></a>

			<a href="#"  class="tipText" title="Generate Costs Spreadsheet" data-toggle="modal" data-target="#modal_medium" data-remote="<?php echo Router::Url(array('controller' => 'costs', 'action' => 'export_xls', $project_id, 'admin' => FALSE), TRUE); ?>"> <i class="financesblack"></i></a>

		</span>

		<span class="hlt-sep">
			<?php if ($project_status != 'completed') { ?>
			<a class="opp-icon tipText" title="Project Opportunity" href="<?php echo SITEURL ?>projects/project_opportunity/<?php echo $project_id; ?>" data-toggle="modal" data-target="#opportunity_model_box" > <i class="opportunityblack18"></i></a>
			<?php } ?>
			
			<a class="rate-space tipText" title="Rate Cards" href="#" data-toggle="modal" data-target="#model_cost" data-remote="<?php echo Router::Url(array('controller' => 'costs', 'action' => 'user_rates', $project_id, 'admin' => FALSE), TRUE); ?>"> <i class="ratesblack"></i></a>

			<?php  ?><a href="<?php echo Router::Url(array('controller' => 'users', 'action' => 'event_gantt', $project_type => $project_id, 'admin' => FALSE), TRUE); ?>" class="tipText report-button h-common-btn" title="View Gantt" ><i class="ganttblack"></i></a>

			<?php /* ?><a href="<?php echo Router::Url(array('controller' => 'users', 'action' => 'projects', $project_type => $project_id, 'admin' => FALSE), TRUE); ?>" class="tipText report-button h-common-btn" title="View Assets"  ><i class="assetsblack"></i></a>

			<a href="<?php echo Router::Url(array('controller' => 'todos', 'action' => 'index','project'=> $project_id, 'admin' => FALSE), TRUE); ?>" class="tipText report-button h-common-btn" title="View To-dos"><i class="to-dosblack"></i></a><?php */ ?>
			
			<a href="<?php echo Router::Url(array('controller' => 'studios', 'action' => 'index',$project_id, 'admin' => FALSE), TRUE); ?>" class="tipText report-button h-common-btn" title="Design Board"><i class="designboardblack18"></i></a>

		</span>
		<span class="hlt-sep">
			<?php if($icons_disabled) { ?>
				<a href="#" data-toggle="modal" class="tipText report-buttons h-common-btn not-in-use" title="Project is Signed Off"><i class="imageblack"></i></a>

				<a href="#" data-toggle="modal" class="tipText report-buttons h-common-btn  not-in-use" title="Project is Signed Off"><i class="documentblack"></i></a>

				<a href="#" data-toggle="modal" class="tipText report-buttons h-common-btn  not-in-use" title="Project is Signed Off"><i class="noteblack"></i></a>

				<a href="#" data-toggle="modal" class="tipText report-buttons h-common-btn  not-in-use" title="Project is Signed Off"><i class="linkblack"></i></a>

				<a href="#" data-toggle="modal" class="tipText report-buttons h-common-btn  not-in-use" title="Project is Signed Off"><i class="competenciesblackicon"></i></a>
			<?php }else{ ?>
				<a href="<?php echo SITEURL ?>projects/project_pic/<?php echo $project_id; ?>" data-toggle="modal" data-target="#upload_model_box" class="tipText report-buttons h-common-btn" title="Project Image"><i class="imageblack"></i></a>

				<a href="<?php echo Router::url(['controller' => 'projects', 'action' => 'project_documents', $project_id, 'admin' => FALSE], TRUE) ?>" data-toggle="modal" data-target="#model_bx" class="tipText report-buttons h-common-btn <?php echo $icons_disabled; ?>" title="Project Documents"><i class="documentblack"></i></a>

				<a href="<?php echo SITEURL ?>projects/add_note/<?php echo $project_id; ?>" data-toggle="modal" data-target="#model_bx" class="tipText report-buttons h-common-btn <?php echo $icons_disabled; ?>" title="Project Notes"><i class="noteblack"></i></a>

				<a href="<?php echo Router::url(['controller' => 'projects', 'action' => 'project_links', $project_id, 'admin' => FALSE], TRUE) ?>" data-toggle="modal" data-target="#model_bx" class="tipText report-buttons h-common-btn <?php echo $icons_disabled; ?>" title="Project Links"><i class="linkblack"></i></a>

				<a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_competencies', $project_id, 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#model_bx" class="tipText report-buttons h-common-btn <?php echo $icons_disabled; ?>" title="Project Competencies"><i class="competenciesblackicon"></i></a>
			<?php } ?>
         </span>

<?php }	?>



		<?php
			if (!empty($user_role)) {

				if( isset($quick_share_disable) && !empty($quick_share_disable) ){
			?>    <span class="hlt-sep">
				<?php if($icons_disabled){ ?>
					<a class="share-button h-common-btn tipText not-in-use" title="Project is Signed Off" rel="tooltip" ><i class="share-icon"></i></a>
				<?php }else{ ?>
					<a class="share-button h-common-btn tipText <?php echo $quick_share_disable;?> " title="<?php echo $quick_share_disable_tip;?>" rel="tooltip" style="<?php echo $quick_share_disable_cursor;?>" ><i class="share-icon"></i></a>
				<?php } ?>
			<?php } else { ?>
			    <span class="hlt-sep">
			    	<?php if($icons_disabled){ ?>
			    		<a data-toggle="modal" class="share-button h-common-btn tipText not-in-use" title="Project is Signed Off" rel="tooltip" ><i class="share-icon"></i></a>
			    	<?php }else{ ?>
						<a data-toggle="modal" class="share-button h-common-btn tipText <?php echo $icons_disabled; ?>" title="Share Project" href="<?php echo SITEURL ?>projects/quick_share/<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip" ><i class="share-icon"></i></a>
					<?php } ?>
			<?php	}
			}

if (isset($project_id) && !empty($project_id)) {
 if (!empty($user_role) && !empty($permit_edit)) {

        ?>



                    <?php if (!empty($user_role)) {


                        $message = null;

                        $startdate = isset($project_data['start_date']) && !empty($project_data['start_date']) ? date("Y-m-d",strtotime($project_data['start_date'])) : '';
                        $enddate = isset($project_data['end_date']) && !empty($project_data['end_date']) ? date("Y-m-d",strtotime($project_data['end_date'])) : '';


						$curdate = date("Y-m-d");

						$curdate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime(date("Y-m-d"))),$format = 'Y-m-d');

                        $class = '';
                        $url = SITEURL.'templates/create_workspace/'.$project_id;
						$prj_tooltip = 'Add workspace';
						if( FUTURE_DATE == 'on' ){

							if(isset($project_data['sign_off']) && $project_data['sign_off'] == 1){
								$message = 'Cannot create Workspace, Project is Signed off.';
								$url ='';
								$class = 'workspace disable';
								$prj_tooltip = "Project Is Signed Off";

							}else if(!empty($enddate) && $enddate < $curdate){
								$message = 'Cannot add a Workspace because the Project end date is overdue.';
								$url ='';$class = 'workspace disable';
							}else if(isset ($startdate) && empty($startdate)){
								$message = 'Please add a schedule to this Project first.';
								$url ='';$class = 'workspace disable';
							}

						} else {

							if(isset($project_data['sign_off']) && $project_data['sign_off'] == 1){
								$message = 'Cannot create Workspace, Project is Signed off.';
								$url ='';$class = 'workspace disable';
								$prj_tooltip = "Project Is Signed Off";
							}else if(!empty($startdate) && $startdate > $curdate ){

								$message = 'Cannot add Workspace because Project is not live (start date not reached)';
								$url ='';$class = 'workspace disable';
							}else if(!empty($enddate) && $enddate < $curdate){
								$message = 'Cannot add a Workspace because the Project end date is overdue.';
								$url ='';$class = 'workspace disable';
							}else if(isset ($startdate) && empty($startdate)){
								$message = 'Please add a schedule to this Project first.';
								$url ='';$class = 'workspace disable';
							}else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
								$message = 'You are not allowed to add workspace because project hasn\'t started yet.';
								$url ='';$class = 'workspace disable';
							}

						}

						if( isset($prj_disabled) && !empty($prj_disabled) ){
                        ?>
							<a class="workspace-button h-common-btn tipText <?php echo $prj_disabled;?>" title="<?php echo $prj_disabled_tip;?>" rel="tooltip" style="<?php echo $prj_disabled_cursor;?>" ><i class="workspace-icon"></i> </a>

						<?php } else { ?>

							<a  data-title="<?php echo $message;?>" data-headertitle="Add Workspace" class="workspace-button h-common-btn tipText <?php echo $class;?>" href="<?php echo $url; ?>" title="<?php echo $prj_tooltip;?>" rel="tooltip"  ><i class="workspace-icon"></i> </a>

				   <?php }
					}

                     ?>

					<?php
                    if (!empty($user_role) && !empty($permit_edit)) {

                    	if( isset($edit_button_disable) && !empty($edit_button_disable) ){


						?><span class="hlt-sep">
							<?php if($icons_disabled){ ?>
							<a class="edit-button h-common-btn tipText not-in-use" rel="tooltip"  title="Project is Signed Off" ><i class="edit-icon"></i> </a>
							<?php } else { ?>
							<a class="edit-button h-common-btn tipText <?php echo $edit_button_disable; ?> <?php echo $icons_disabled; ?>" rel="tooltip"  title="<?php echo tipText($edit_button_disable_tip) ?>" style="<?php echo $edit_button_disable_cursor; ?>" ><i class="edit-icon"></i> </a>
							<?php } ?>
						</span>
						<?php


						} else { ?>
						<span class="hlt-sep">
							<?php if($icons_disabled){ ?>
								<a class="edit-button h-common-btn tipText not-in-use" rel="tooltip"  title="Project is Signed Off"><i class="edit-icon"></i> </a>
							<?php } else { ?>
								<a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'manage_project', $project_id, 'admin' => FALSE), TRUE); ?>" class="edit-button h-common-btn tipText <?php echo $icons_disabled; ?>" rel="tooltip"  title="Edit Project"><i class="edit-icon"></i> </a>
							<?php } ?>
						</span>
						<?php


						}
					   //}
					} ?>




	  <?php  } } ?>

	  		<?php if (!empty($user_role) && !empty($permit_edit)) {


			?>
				<a href="#" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "projects", "action" => "delete_an_item", $project_id, 'admin' => FALSE ), true ); ?>" class="workspace-button h-common-btn tipText delete-an-item" title="Delete Project"><i class="deleteblack"></i></a>

			<?php } ?>

				</div>
