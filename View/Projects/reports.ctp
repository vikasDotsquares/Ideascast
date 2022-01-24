
<style type="text/css">
	.col-sm-12 #user_projects {
		min-width: 290px;
	}
	.pull-left.project-detail {

	}
	.btn[class*='bg-']:hover{

		box-shadow:none;
	}

	.bg-blue.sb_blog {
		float: left;
		padding: 7px;
		margin-left: 5px;
	}
	@media (max-width:1366px) {
	.bg-blue.sb_blog {
		white-space: nowrap;
		text-overflow: ellipsis;
		max-width: 24%;
		overflow: hidden;
	}
	}

	@media (min-width:1024px) and (max-width:1366px) {
		.col-sm-12 #user_projects {
			min-width: 250px;
		}
	}
	@media (max-width:991px) {
	.pull-left.project-detail {
		width: 53%;
	}
	.bg-blue.sb_blog {
		max-width: 45%;
	}
	.progress-unique {
		clear: both;
	}
	.border.bg-white.ideacast-project-progress.project-elapsed-bar {
		margin-left: 0;
	}
	.col-sm-12 #user_projects {
	min-width: 100%;
	}
	}
	@media (max-width:767px) {
	.progress-unique .ideacast-project-progress .progress {
		max-width: 68%;
	}
	}
	@media (max-width:479px) {
	.progress-unique .ideacast-project-progress .progress {
		max-width: 48%;
	}
	}
	@media (min-width:992px) and (max-width:1023px) {
		.col-sm-12 #user_projects {
			min-width: 350px;
		}
	}
	.filter-box {
		padding :10px 15px 5px 15px;
		margin:  0;
		border-top-left-radius: 3px;
		background-color: #f5f5f5;
		text-align:right;
		overflow:visible;
		border: 1px solid #ddd;
		border-top-right-radius: 3px;
	}
    .pt2{
        padding-top: 2px;
    }

</style>
<?php

$current_user_id = $this->Session->read('Auth.User.id');
$owner_user = '';
if( isset( $project_detail ) && !empty( $project_detail ) ) {

	if( isset($project_detail['User']['UserDetail']['first_name']) && !empty($project_detail['User']['UserDetail']['first_name']) ){
		$owner_user = htmlentities($project_detail['User']['UserDetail']['first_name'],ENT_QUOTES);
	}

	if( isset($project_detail['User']['UserDetail']['last_name']) && !empty($project_detail['User']['UserDetail']['last_name']) ){
		$owner_user .= ' '.htmlentities($project_detail['User']['UserDetail']['last_name'],ENT_QUOTES);
	}

	//$owner_user = htmlentities($project_detail['User']['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($project_detail['User']['UserDetail']['last_name'],ENT_QUOTES);
}
?>
<div class="row">

  <div class="col-xs-12">
    <div class="row">
      <section class="content-header clearfix">
        <?php
			if( isset( $project_detail ) && !empty( $project_detail ) ) {
				echo '<h1 class="pull-left">'.$this->ViewModel->_substr( $project_detail['Project']['title'], 60, array( 'html' => true, 'ending' => '...' ) );
?>
        </h1>
        <?php
				// LOAD PARTIAL FILE FOR TOP DD-MENUS
			$pid = ( isset( $project_detail ) && !empty( $project_detail ) ) ? $project_detail['Project']['id'] : 0;
			?>
        <p class="text-muted date-time pull-left " style="min-width:100%;padding: 5px 0;">Information about your Project</span>
          <?php $p_permission = $this->Common->project_permission_details($this->params['pass']['0'],$this->Session->read('Auth.User.id'));?>
        </p>
        <?php } ?>
      </section>
    </div>

	<?php
	if( isset( $project_detail ) && !empty( $project_detail ) ) {
		echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_detail['Project']['id']));
	}
	?>

    <div class="box-content">
      <div class="row ">
        <div class="col-xs-12">
				<div style=" " class="fliter margin-top filter-box">
				<?php  echo $this->element( '../Projects/partials/report_settings', array( 'menu_project_id' => $pid ) ); ?>
				</div>
          <div class="box noborder  ">
            <div class="box-header nopadding" style="">

              <!-- MODAL BOX WINDOW -->
              <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content"></div>
                </div>
              </div>
              <!-- END MODAL BOX -->

            </div>
            <div class="box-body nopadding">
              <div class="box box-success noborder-radius nomargin-bottom noborder">
                <div class="box-header">
                  <!-- <h3 class="box-title"></h3> 	-->
                </div>
                <div class="box-body ">
                  <?php
// Get all workspaces of the project and count assets

				$totalEle = $totalWs = 0;
				$totalAssets = null;
				$projectData = $this->ViewModel->getProjectDetail( $project_detail['Project']['id'] );
				$wsList = Set::extract( $projectData, '/ProjectWorkspace/workspace_id' );
				$totalWs = ( isset($wsList) && !empty($wsList) ) ? count( $wsList ) : 0;

				if( isset( $project_workspaces_all ) && !empty( $project_workspaces_all ) ) {
					$row_counter = 0;

				foreach( $project_workspaces_all as $key => $val ) {


					$project_data = $val['Project'];
					$workspace_data = $val['Workspace'];
					$project_workspace_data = $val['ProjectWorkspace'];
							$wsData = $this->ViewModel->countAreaElements( $workspace_data['id'] );

							$totalEle += $wsData['active_element_count'];
							if( isset( $wsData['assets_count'] ) && !empty( $wsData['assets_count'] ) ) {

								foreach( $wsData as $k => $subArray ) {
									if( is_array( $subArray ) ) {
										foreach( $subArray as $m => $value ) {
											if( !isset( $totalAssets[$m] ) )
												$totalAssets[$m] = $value;
											else
												$totalAssets[$m] += $value;
										}
									}
								}
							}
						}

				}

									?>
                  <div class="row">
                    <div class="col-sm-6">
                       <?php /* ?><div class="form-group DJJ row clearfix">
                        <label class="col-lg-12 control-label" for="">Category Path:</label>
                        <div class="col-lg-12"> <span class="descr">
                          <?php
							echo $this->element( 'front/category_breadcrumb', array( 'category_bread' => $category_bread ) ); ?>
                          </span> </div>
                      </div>  <?php */ ?>
                      <div class="form-group DJ row clearfix">
                        <label class="col-lg-12 control-label" for="objective">Project Objective:</label>
                        <div class="col-lg-12"> <span class="descr"> <?php echo nl2br( $project_detail['Project']['objective'] ) ?> </span> </div>
                      </div>
                      <div class="form-group DJ row clearfix">
                        <label class="col-lg-12 control-label" for="objective">Project Type:</label>
                        <div class="col-lg-12"> <span class="descr" >
                          <?php  echo $this->Common->alignedName( $project_detail['Project']['aligned_id'] ); ?>
                          </span> </div>
                      </div>
                      <div class="form-group DJ row clearfix">
                        <label class="col-lg-12 control-label" for="textArea">Description:</label>
                        <div class="col-lg-12"> <span class="descr" style="min-height:173px; max-height:173px; overflow:auto"> <?php echo nl2br( $project_detail['Project']['description'] ) ?> </span> </div>
                      </div>
                    </div>
                    <div class="col-sm-6"> <b>Summary:</b> <?php echo (isset( $pwcount ) && !empty( $pwcount )) ? $pwcount : 0; ?> Workspaces
                      <div class=" report-summary">
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="overdues border-radius i-block text-center tipText" title="Tasks Overdue in Project">
                          <h6 class="text-white text-center">Total Tasks <span class="hidden-md hidden-sm">Overdue</span></h6>
                          <a class="btn btn-lg bg-white hidden-md hidden-sm" href="#" style="padding: 10px 19px;"> <i class="asset-all-icon overdueblack"></i> </a> <a class="btn btn-lg bg-white" href="#">
                          <?php
							$due_status_ind = _due_status( $project_detail['Project']['id'], 'overdue_status' );
							if( !empty( $due_status_ind ) && is_array( $due_status_ind ) ) {
								echo array_sum( $due_status_ind );
							} else {
								echo '0';
							}
							?>
                          </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb col-summary"> <span class="elements border-radius i-block text-center tipText" title="Tasks in Project">
                          <h6 class="text-white text-center">Total Tasks</h6>
                          <a class="btn btn-lg bg-white hidden-md hidden-sm" href="#"> <i class="asset-all-icon taskblack"></i> </a> <a class="btn btn-lg bg-white " href="#"> <?php echo (!empty( $totalEle )) ? $totalEle : 0; ?> </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="links border-radius i-block  text-center tipText" title="Links in Project">
                          <h6 class="text-white text-center">Total Links</h6>
                          <a class="btn btn-lg bg-white  hidden-md hidden-sm" href="#"> <i class="asset-all-icon re-LinkBlack"></i> </a> <a class="btn btn-lg bg-white" href="#"> <?php echo (isset( $totalAssets['links'] ) && !empty( $totalAssets['links'] )) ? $totalAssets['links'] : 0; ?> </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="notes border-radius i-block text-center tipText" title="Notes in Project">
                          <h6 class="text-white text-center">Total Notes</h6>
                          <a class="btn btn-lg bg-white  hidden-md hidden-sm" href="#"> <i class="asset-all-icon re-NoteBlack"></i> </a> <a class="btn btn-lg bg-white" href="#"> <?php echo (isset( $totalAssets['notes'] ) && !empty( $totalAssets['notes'] )) ? $totalAssets['notes'] : 0; ?> </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="documents border-radius i-block text-center tipText" title="Documents in Project">
                          <h6 class="text-white text-center">Total Documents</h6>
                          <a class="btn btn-lg bg-white  hidden-md hidden-sm" href="#"> <i class="asset-all-icon re-DocumentBlack"></i> </a> <a class="btn btn-lg bg-white" href="#"> <?php echo (isset( $totalAssets['docs'] ) && !empty( $totalAssets['docs'] )) ? $totalAssets['docs'] : 0; ?> </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="mindmaps border-radius i-block text-center tipText" title="Mind Maps in Project">
                          <h6 class="text-white text-center">Total Mind Maps</h6>
                          <a class="btn btn-lg bg-white  hidden-md hidden-sm" href="#"> <i class="asset-all-icon re-MindMapBlack"></i> </a> <a class="btn btn-lg bg-white" href="#"> <?php echo (isset( $totalAssets['mindmaps'] ) && !empty( $totalAssets['mindmaps'] )) ? $totalAssets['mindmaps'] : 0; ?> </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="decisions border-radius i-block text-center tipText" title="Decisions in Project">
                          <h6 class="text-white text-center">Total Decisions</h6>
                          <a class="btn btn-lg bg-white  hidden-md hidden-sm" href="#"> <i class="asset-all-icon re-DecisionBlack" style="font-size: 17px;"></i> </a> <a class="btn btn-lg bg-white" href="#"> <?php echo (isset( $totalAssets['decisions'] ) && !empty( $totalAssets['decisions'] )) ? $totalAssets['decisions'] : 0; ?> </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="feedbacks border-radius i-block text-center tipText" title="Live Feedbacks in Project">
                          <h6 class="text-white text-center">Total Feedbacks</h6>
                          <a class="btn btn-lg bg-white  hidden-md hidden-sm" href="#"> <i class="asset-all-icon re-FeedbackBlack"></i> </a> <a class="btn btn-lg bg-white" href="#"> <?php echo (isset( $totalAssets['feedbacks'] ) && !empty( $totalAssets['feedbacks'] )) ? $totalAssets['feedbacks'] : 0; ?> </a> </span> </div>
                        <div class="col-lg-4 col-md-4 col-xs-6 thumb no-padding col-summary"> <span class="votes border-radius i-block text-center tipText" title="Live Votes in Project">
                          <h6 class="text-white text-center">Total Votes</h6>
                          <a class="btn btn-lg bg-white  hidden-md hidden-sm" href="#"> <i class="asset-all-icon re-VoteBlack"></i> </a> <a class="btn btn-lg bg-white" href="#"> <?php echo (isset( $totalAssets['votes'] ) && !empty( $totalAssets['votes'] )) ? $totalAssets['votes'] : 0; ?> </a> </span> </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- / inner form .box -->

              <!--
							<div class="box box-primary noborder-radius">
								<div class="box-header">
									<i class="fa fa-bar-chart-o"></i>
									<h3 class="box-title"> Charts </h3>
								</div>
								<div class="box-body ">
									<div class="row">
										<div class="col-md-12">
											<div id="bar_chart" style="height: 300px;"></div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div id="line_chart" style="height: 300px;"></div>
										</div>
									</div>
								</div>
							</div>	-->
              <!-- / graph .box -->

              <div class="box box-primary noborder-radius no-box-shadow">
                <div class="box-header" style="padding-bottom:0px">
                  <?php
								$owner = $this->Common->ProjectOwner($this->params['pass']['0'],$this->Session->read('Auth.User.id'));

								$participants = participants($this->params['pass']['0'],$owner['UserProject']['user_id']);

								$participantsGpOwner = participants_group_owner($this->params['pass']['0'] );



								$showG_owners = null;

								if(isset($participantsGpOwner) && !empty($participantsGpOwner)) {
									foreach($participantsGpOwner as $participantsGpOwnerU){
										$allparticipOWN[] = $this->Common->userFullname($participantsGpOwnerU);
										if( !empty($participantsGpOwnerU) )
											$showG_owners[$participantsGpOwnerU] = $this->Common->userFullname($participantsGpOwnerU);
									}
								}



								$participantsGpSharer = participants_group_sharer($this->params['pass']['0'] );

								$showG_sharers = null;

								if(isset($participantsGpSharer) && !empty($participantsGpSharer)) {
									foreach($participantsGpSharer as $participantsGpsharerU){
										$allparticipOWNS[] = $this->Common->userFullname($participantsGpsharerU);
										if( !empty($participantsGpsharerU) )
											$showG_sharers[$participantsGpsharerU] = $this->Common->userFullname($participantsGpsharerU);
									}
								}

								//pr($participantsGp);

								$show_sharer = null;
								if(isset($participants) && !empty($participants)) {
									foreach($participants as $k => $part) {
										$show_sharer[$part] = $this->Common->userFullname($part);
									}

									foreach($participants as $part) {
										$allparticip[] = $this->Common->userFullname($part);
									}
								}

								if(isset($allparticip) && !empty($allparticip)) {

									$key = array_search($this->Common->userFullname($this->Session->read('Auth.User.id')), $allparticip);
										if(isset($key) && !empty($key)) {
											$tmp = $allparticip[$key];
											unset($allparticip[$key]);
											$allparticip = array($key => $tmp) + $allparticip;
										}
								}

								// pr($allparticip);

								$participants_owners = participants_owners($this->params['pass']['0'], $owner['UserProject']['user_id']);

								$show_owners = null;

								if(isset($participants_owners) && !empty($participants_owners)) {
									foreach($participants_owners as $participantss){
										$allparticipOW[] = $this->Common->userFullname($participantss);
										if( !empty($participantss) )
											$show_owners[$participantss] = $this->Common->userFullname($participantss);
									}
								}

								if(isset($allparticipOW) && !empty($allparticipOW)){
									$keyW = array_search($this->Common->userFullname($this->Session->read('Auth.User.id')), $allparticipOW);
									if(isset($keyW) && !empty($keyW)){
										$tmpW = $allparticipOW[$keyW];
										unset($allparticipOW[$keyW]);
										$allparticipOW = array($keyW => $tmpW) + $allparticipOW;
									}
								}
								?>

                    <div class="clearfix"></div>
                    <div class=" project-boxes col-md-3 project-owners">
                      <div class="box-wrap">
                      	<label> Project Owners: </label>
							<div class="users">

								<?php if(isset($show_owners) && !empty($show_owners)) { ?>
									<?php foreach($show_owners as $key => $val ) {
										$html = '';

										if( $key != $current_user_id ) {
											$html = CHATHTML($key, $this->params['pass']['0']);
										}
										$style = '';

										if( $owner['UserProject']['user_id'] == $key ) {
											$style = 'border: 2px solid #333';
										}

										$userDetail = $this->ViewModel->get_user( $key, null, 1 );
										$user_image = SITEURL . 'images/placeholders/user/user_1.png';
										$user_name = '';
										$job_title = 'N/A';
										if(isset($userDetail) && !empty($userDetail)) {
											//$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);

											if( isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name']) ){
												$user_name .= htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES);
											}
											if( isset($userDetail['UserDetail']['last_name']) && !empty($userDetail['UserDetail']['last_name']) ){
												$user_name .= ' '.htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
											}

											$profile_pic = $userDetail['UserDetail']['profile_pic'];
											$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

											if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
												$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

										 }
										 if($user_name == ''){
										 	$user_name = 'N/A';
										 }

										 ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
												<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
											</a>
									<?php } ?>


									<?php } ?>
									<?php }else{ ?>
									<li class="not_avail">None</li>
								<?php } ?>
							</div>
                      </div>
                    </div>

                    <div class=" project-boxes col-md-3 project-sharers">
                      <div class="box-wrap">
                      <label> Project Sharers: </label>

							<div class="users">
								<?php if(isset($show_sharer) && !empty($show_sharer)) { ?>
									<?php foreach($show_sharer as $key => $val ) {
										$html = '';
										if( $key != $current_user_id ) {
											$html = CHATHTML($key, $this->params['pass']['0']);
										}

											$userDetail = $this->ViewModel->get_user( $key, null, 1 );
											$user_image = SITEURL . 'images/placeholders/user/user_1.png';
											$user_name = '';
											$job_title = 'N/A';
									  if(isset($userDetail) && !empty($userDetail)) {
											if( isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name']) ){
												$user_name .= htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES);
											}
											if( isset($userDetail['UserDetail']['last_name']) && !empty($userDetail['UserDetail']['last_name']) ){
												$user_name .= ' '.htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
											}

											if($user_name == ''){
											 	$user_name = 'N/A';
											 }


										  $profile_pic = $userDetail['UserDetail']['profile_pic'];
										  $job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

										  if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
											  $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
										} ?>
										  <a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
												<img src="<?php echo $user_image; ?>" class="user-image" >
											</a>
										<?php } ?>

									<?php } ?>
									<?php }else{ ?>
									<li class="not_avail">None</li>
								<?php } ?>
							</div>
                      </div>

                    </div>

                    <div class=" project-boxes col-md-3  project-group-owners ">
                      <div class="box-wrap">
                      <label> Project Group Owners: </label>
							<div class="users">
								<?php if(isset($showG_owners) && !empty($showG_owners)) { ?>
									<?php foreach($showG_owners as $key => $val ) {
										$html = '';
										if( $key != $current_user_id ) {
											$html = CHATHTML($key, $this->params['pass']['0']);
										}

											$userDetail = $this->ViewModel->get_user( $key, null, 1 );
											$user_image = SITEURL . 'images/placeholders/user/user_1.png';
											$user_name = '';
											$job_title = 'N/A';
									  if(isset($userDetail) && !empty($userDetail)) {
											if( isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name']) ){
												$user_name .= htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES);
											}
											if( isset($userDetail['UserDetail']['last_name']) && !empty($userDetail['UserDetail']['last_name']) ){
												$user_name .= ' '.htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
											}
										if($user_name == ''){
										 	$user_name = 'N/A';
										 }


										  $profile_pic = $userDetail['UserDetail']['profile_pic'];
										  $job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

										  if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
											  $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
										  } ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
												<img src="<?php echo $user_image; ?>" class="user-image" >
											</a>
										<?php } ?>

									<?php } ?>
									<?php }else{ ?>
									<li class="not_avail">None</li>
								<?php } ?>
							</div>
                      </div>

                    </div>

                    <div class=" project-boxes col-md-3 project-group-sharers ">
                      <div  class="box-wrap">
                      	<label> Project Group Sharers: </label>
							<div class="users">
								<?php if(isset($showG_sharers) && !empty($showG_sharers)) { ?>
									<?php foreach($showG_sharers as $key => $val ) {
										$html = '';
										if( $key != $current_user_id ) {
											$html = CHATHTML($key, $this->params['pass']['0']);
										}

											$userDetail = $this->ViewModel->get_user( $key, null, 1 );
											$user_image = SITEURL . 'images/placeholders/user/user_1.png';
											$user_name = '';
											$job_title = 'N/A';
									  if(isset($userDetail) && !empty($userDetail)) {
										   if( isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name']) ){
												$user_name .= htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES);
											}
											if( isset($userDetail['UserDetail']['last_name']) && !empty($userDetail['UserDetail']['last_name']) ){
												$user_name .= ' '.htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
											}
										if($user_name == ''){
										 	$user_name = 'N/A';
										 }

										  $profile_pic = $userDetail['UserDetail']['profile_pic'];
										  $job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

										  if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
											  $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
										  } ?>
											<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
												<img src="<?php echo $user_image; ?>" class="user-image" >
											</a>
										<?php } ?>

									<?php } ?>
									<?php }else{ ?>
									<li class="not_avail">None</li>
								<?php } ?>
							</div>
                      </div>

                    </div>


                  </div>
                </div>
                <div class="box-body" style="padding-top:0px">
                  <?php
									if( isset( $project_workspaces ) && !empty( $project_workspaces ) ) {
										$row_counter = 0;
										?>
                  <div class="preloader_backdrop text-center" style="display: none" id="bar"><span class="btn btn-sm border pagination-progress-container" style="width: 50%; background-color: rgba(255, 255, 255, 0.3);" >
                    <div class="pagination-progress">
                      <div class="progress-inner"></div>
                    </div>
                    </span> </div>
                  <div class="data_container" >
                    <div class="ajax-pagination clearfix" style="border-bottom: 1px solid #67a028;"> <?php echo $this->element( 'jeera_paging', [ 'project_workspaces' => $project_workspaces, 'project_detail' => $project_detail ] ); ?> </div>
                    <div class="row padding-top">
                      <?php
												// pr($project_workspaces, 1);
												$graph_data = null;
												foreach( $project_workspaces as $key => $val ) {
													$project_data = $val['Project'];
													$workspace_data = $val['Workspace'];
													$project_workspace_data = $val['ProjectWorkspace'];


													// get areas
													$element_detail = null;
													$sum_value = 0;
													// echo $workspace_data['id'];
													$area_id = $this->ViewModel->workspace_areas( $workspace_data['id'], false, true );
													// $area_id = $this->ViewModel->workspace_areas(116, false, true);


													$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

													$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

													$grp_id = $this->Group->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));

													$el_permission = $this->Common->element_permission_data($project_id,$this->Session->read('Auth.User.id'));

													if(isset($grp_id) && !empty($grp_id)){

													$p_permission = $this->Group->group_permission_details($project_id,$grp_id);
													$el_permission = $this->Group->group_element_permission_data($project_id,$grp_id);


													}

													//pr($el_permission );

													if((isset($el_permission) && !empty($el_permission)))
													{
														$el = $this->ViewModel->area_elements_permissions($area_id, false,$el_permission);
													}

													if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
														$el = $this->ViewModel->area_elements($area_id);
													}


													if( !empty( $el ) ) {
														$element_detail = _element_detail( null, $el );

														if( !empty( $element_detail ) ) {
															// $counts = $this->ViewModel->ws_el_detail_count($element_detail, $workspace_data['id'], true);
															$filter = arraySearch( $element_detail, 'date_constraint_flag' );
															// pr($filter);
															if( !empty( $filter ) ) {
																// $data[$workspace_data['id']] = $filter[0];

																$sum_value = array_sum( array_columns( $element_detail, 'date_constraint_flag' ) );
																if( !empty( $sum_value ) ) {
																	// echo $workspace_data['id'].' = '.$sum_value.'<br />';
																	// pr($element_detail);
																}
															}
														}
													}

													// Get Color Code of the panel
													$color_code = 'bg-gray';
													if( isset( $project_workspaces ) && !empty( $project_workspaces ) ) {
														$color_code = $workspace_data['color_code'];
													}
													$colorBG = explode( '-', $color_code );
													$color_code = (!empty( $colorBG ) ) ? 'panel-' . $colorBG[1] : 'panel-gray';

													// Get total areas in workspace
													$ws_area_total = $this->ViewModel->workspace_areas( $workspace_data['id'], true );

													$total_elements = $total_links = $total_notes = $total_docs = $total_overdue_status = $total_mindmaps = $total_votes = $total_decisions = $total_feedbacks = 0;
													$total_overdue_status = $sum_value;
													if( $ws_area_total > 0 ) {
														// Get all assets in workspace
														$count_data = $this->ViewModel->countAreaElements( $workspace_data['id'] );
														// pr($count_data,1);
														if( isset( $count_data ) && !empty( $count_data ) ) {

															$total_elements = ( isset( $count_data['active_element_count'] ) && !empty( $count_data['active_element_count'] )) ? $count_data['active_element_count'] : 0;

															$total_links = ( isset( $count_data['assets_count'] ) && !empty( $count_data['assets_count'] )) ? ( ( isset( $count_data['assets_count']['links'] ) && !empty( $count_data['assets_count']['links'] )) ? $count_data['assets_count']['links'] : 0 ) : 0;

															$total_notes = ( isset( $count_data['assets_count'] ) && !empty( $count_data['assets_count'] )) ? ( ( isset( $count_data['assets_count']['notes'] ) && !empty( $count_data['assets_count']['notes'] )) ? $count_data['assets_count']['notes'] : 0 ) : 0;

															$total_docs = ( isset( $count_data['assets_count'] ) && !empty( $count_data['assets_count'] )) ? ( ( isset( $count_data['assets_count']['docs'] ) && !empty( $count_data['assets_count']['docs'] )) ? $count_data['assets_count']['docs'] : 0 ) : 0;

															$total_mindmaps = ( isset( $count_data['assets_count'] ) && !empty( $count_data['assets_count'] )) ? ( ( isset( $count_data['assets_count']['mindmaps'] ) && !empty( $count_data['assets_count']['mindmaps'] )) ? $count_data['assets_count']['mindmaps'] : 0 ) : 0;

															$total_decisions = ( isset( $count_data['assets_count'] ) && !empty( $count_data['assets_count'] )) ? ( ( isset( $count_data['assets_count']['decisions'] ) && !empty( $count_data['assets_count']['decisions'] )) ? $count_data['assets_count']['decisions'] : 0 ) : 0;

															$total_feedbacks = ( isset( $count_data['assets_count'] ) && !empty( $count_data['assets_count'] )) ? ( ( isset( $count_data['assets_count']['feedbacks'] ) && !empty( $count_data['assets_count']['feedbacks'] )) ? $count_data['assets_count']['feedbacks'] : 0 ) : 0;

															$total_votes = ( isset( $count_data['assets_count'] ) && !empty( $count_data['assets_count'] )) ? ( ( isset( $count_data['assets_count']['votes'] ) && !empty( $count_data['assets_count']['votes'] )) ? $count_data['assets_count']['votes'] : 0 ) : 0;

															$graph_data[] = [
																'ybar' => _substr_text( $workspace_data['title'], 5, false ),
																'yline' => ($total_links + $total_notes + $total_docs),
																'links' => $total_links,
																'notes' => $total_notes,
																'documents' => $total_docs,
																'due_status' => $total_overdue_status,
																	// 'votes' => $total_votes,
																	// 'mindmaps' => $total_mindmaps,
															];
														}
													}
													?>
                      <div class="col-md-4 fix-height">
                        <div class="panel clearfix <?php echo $color_code; ?>">
                          <div class="panel-heading clearfix">
                            <h4 class="panel-title pull-left" style="width: 80%"> <span class="ws-heading"> <!-- <i class="fa fa-tasks"></i>
						   <p class="ui-ellipsis"><?php echo $workspace_data['title']; ?></p>
																		-->
                              <?php echo $workspace_data['title']; ?>
                              <?php //echo _substr_text( $workspace_data['title'], 30, false ); ?>
                              </span> </h4>
                            <div class="btn-group pull-right"> <a class="btn btn-default btn-xs tipText" title="Open Workspace" href="<?php echo Router::url( array( 'controller' => 'projects', 'action' => 'manage_elements', $project_data['id'], $workspace_data['id'] ) ); ?>"> <i class="fa fa-folder-open"></i> </a> </div>
                          </div>
                          <div class="panel-footer padding noborder-radius clearfix">
                            <div class="prjct-rprt-icons rept-icon-align">
                              <ul class="list-unstyled text-center">
                                <li class="odue"> <span class="label bg-mix"><?php echo $total_overdue_status; ?></span> <span href="#" title="" class="btn btn-xs bg-mix bg-navy tipText" data-original-title="Tasks Overdue"><i class="asset-all-icon overduewhite"></i></span> </li>
                                <li class="iele"> <span title="" class="label bg-mix "><?php echo $total_elements; ?></span> <span href="#" data-original-title="Tasks " class="btn btn-xs bg-mix bg-dark-gray tipText task-reportalign"><i class="asset-all-icon taskwhite"></i></span></li>
                                <li class="ico_links"> <span class="label bg-mix"><?php echo $total_links; ?></span> <span href="#" title="" class="btn btn-xs bg-mix tipText bg-maroon" data-original-title="Links "><i class="asset-all-icon linkwhite"></i></span> </li>
                                <li class="inote"> <span class="label bg-mix"><?php echo $total_notes; ?></span> <span href="#" title="" class="btn btn-xs bg-mix tipText bg-purple" data-original-title="Notes "><i class="asset-all-icon notewhite"></i></span> </li>
                                <li class="idoc"> <span class="label bg-mix"><?php echo $total_docs; ?></span> <span href="#" title="Documents " class="btn bg-blue btn-xs bg-mix tipText"><i class="asset-all-icon documentwhite"></i></span> </li>
                                <li class="green"> <span class="label bg-mix"><?php echo $total_mindmaps; ?></span> <span href="#" title="" class="btn btn-xs bg-mix bg-green tipText" data-original-title="Mind Maps "><i class="asset-all-icon mindmapwhite"></i></span> </li>
                                <li class="orang"> <span class="label bg-mix"><?php echo $total_decisions; ?></span> <span href="#" title="" class="btn btn-xs bg-mix bg-orange  tipText decisions" data-original-title="Decisions "><i class="asset-all-icon decisionwhite"></i></span> </li>
                                <li class="l-blue"> <span class="label bg-mix">
                                  <?php echo $total_feedbacks; ?>
                                  </span> <span href="#" title="" class="btn btn-xs bg-mix bg-aqua tipText" data-original-title="Live Feedbacks "><i class="asset-all-icon feedbackwhite"></i></span> </li>
                                <li class="l-orang"> <span class="label bg-mix"><?php echo $total_votes; ?></span> <span href="#" title="Live Votes " class="btn btn-xs bg-mix tipText bg-orange-active"><i class="asset-all-icon votewhite"></i></span> </li>
                              </ul>
                            </div>
                          </div>
                          <div class="panel-body report-ws-desc">
                            <!-- workspace-template detail -->
                            <div class="row">
                              <div class="col-md-7 nopadding-right">
                                <p class="text-muted timing"> <span>Created: <?php
								echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime( $workspace_data['created'] )),$format = 'd M Y h:i:s');

								?></span> <span>Updated: <?php
								echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspace_data['modified'])),$format = 'd M Y h:i:s');
								?></span> <span>Created By: <?php echo $owner_user; ?></span> <span>Updated By: <?php echo $owner_user; ?></span> <span>Start Date: <?php

								echo ( isset($workspace_data['start_date']) && !empty($workspace_data['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspace_data['start_date'])),$format = 'd M Y') : 'N/A';

								?></span> <span>End Date: <?php

								echo ( isset($workspace_data['end_date']) && !empty($workspace_data['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspace_data['end_date'])),$format = 'd M Y') : 'N/A';

								?></span> </p>
                              </div>
                              <div class="col-md-5 nopadding-left"> <?php echo workspace_template( $workspace_data['template_id'], true ); // template image  ?> </div>
                            </div>

                            <!-- workspace-description -->
                            <div class="row ws-desc-row">
                              <div class="sub-heading clearfix noborder-radius nopadding margin-top " style="position: relative">
                                <h6 class="panel-title pull-left opacity_content" style="font-size: 12px; margin: 3px;"> <span>Key Result Target</span> </h6>
                              </div>
                              <div class="col-md-12 ws-desc mar_s"> <?php echo nl2br( $workspace_data['description'] ); ?> </div>
                            </div>

                            <!-- workspace-areas -->
                            <div class="row">
                              <div class="sub-heading clearfix noborder-radius nopadding margin-top " style="position: relative">
                                <h6 class="panel-title pull-left opacity_content" style="font-size: 12px; margin: 3px;"> <span>Area information</span> </h6>
                              </div>
                              <div class="col-md-12">
                                <p class="area_detail">
                                  <?php
									if( $ws_area_total > 0 ) {
										// Get all assets in workspace
										$workspace_areas = $this->ViewModel->workspace_area_data( $workspace_data['id'] );
										if( isset( $workspace_areas ) && !empty( $workspace_areas ) ) {
											foreach( $workspace_areas as $val ) {
												$area = $val['Area'];
												?>
                                  <span class="title"> <?php echo $area['title']; ?> </span> <span class="tooltip_text"> <?php echo $area['tooltip_text']; ?> </span>
                                  <?php
											}
										}
									}
								   ?>
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- /.col-md-4 -->

                      <?php
							if( $row_counter == 2 ) {
									echo '</div><div class="row">';
									$row_counter = 0;
							} else {
									$row_counter++;
							}
						} // END FOREACH
						?>
                    </div>
                    <!-- /.row -->

                    <!-- -->
                    <div class="ajax-pagination clearfix">
						<?php echo $this->element( 'jeera_paging', [ 'project_workspaces' => $project_workspaces, 'project_detail' => $project_detail ] ); ?>
					</div>
                    <?php } // END CHECK PROJECT-WORKSPACES  ?>
                  </div>
                  <!-- / .data_container -->
                </div>
                <!-- / inner .box-body -->
              </div>
            </div>
            <!-- / main .box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// echo $this->Html->css(array( 'projects/mcustom_scroll/mcustom' ));
// echo $this->Html->script('projects/plugins/mcustom_scroll/mcustom');
 ?>
<script type="text/javascript" >
	$(function () {
		$('h4.panel-title').find('br').remove();

		$('.panel-title').ellipsis();


		$(".fix-height .panel .panel-body").slimscroll({
			height: "400px",
			alwaysVisible: false,
			color: '#67a028',
			size: "6px",
			borderRadius: "4px"
		}).css("width", "100%");
		/*$(".fix-height .panel .panel-body").mCustomScrollbar({
			set_height: 400,
			theme:"dark",
			scrollButtons:{enable:true},
		});*/
		$(".panel-body.report-ws-desc .ws-desc").slimscroll({
			height: "120px",
			alwaysVisible: false,
			color: '#67a028',
			size: "6px",
			borderRadius: "4px"
		}).css("width", "100%");

		$('body').delegate('.btn.btn-lg.bg-white', 'click', function (event) {
			event.preventDefault();
		})

		$(window).on('resize', function (event) {
			$('.panel-title').ellipsis();
		})
	})
	$(window).load(function () {

		$('.panel-title').ellipsis();

	})
</script>

<script type="text/javascript" >
$(function(){

	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

	$('body').on('change', '#user_projects', function(event){
		console.log('this', $(this).val())
		if($(this).val() != '' && $(this).val() !== undefined){
			location.href = $js_config.base_url + 'projects/reports/' + $(this).val();
		}
	})
})
</script>
<style>
.pophover {
	float: left;
}

.popover {
	z-index: 999999 !important;
}
.popover p {
	margin-bottom: 2px !important;
}
.popover p:first-child {
	font-weight: 600 !important;
	width: 170px !important;
}
.popover p:nth-child(2) {
	font-size: 11px;
}


.mar_s { font-size: 13px; margin: 4px 0 0 0 !important }
.list-unstyled span { cursor: default !important }
.report-summary a { cursor: default !important }
.prjct-rprt-icons ul.list-unstyled > li span.bg-mix { border-color: inherit; border-image: none; border-style: none; border-width: 1px 1px medium; color: #1f1f1f; }
.list-unstyled span.btn { color : #fff }
.preloader_backdrop { background: rgba(0, 0, 0, 0.6) none repeat scroll 0 0; height: 100%; left: 0; position: fixed; top: 0; width: 100%; z-index: 10000; }
.pagination-progress-container { margin: 300px 0 0; }
.pagination-progress { width: 0%; /* width as the % value */ background-color: #337ab7; box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.15) inset; color: #fff; float: left; font-size: 12px; height: 100%; line-height: 20px; text-align: center; -webkit-transition: width 3s ease; -moz-transition: width 3s ease; -ms-transition: width 3s ease; -o-transition: width 3s ease; transition: width 3s ease; }
.pagination-progress .progress-inner { width: 100%; /* width as the % value */ background-color: #67A028; height: 20px; overflow: hidden; box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.9) inset !important; -webkit-transition: width 3s ease; -moz-transition: width 3s ease; -ms-transition: width 3s ease; -o-transition: width 3s ease; transition: width 3s ease; }
.row.bg .col-md-4 { margin-bottom: 2px !important; padding: 0 !important; }
.row.bg .col-md-4 p { background-color: #ccc !important; margin: 1px !important; }
/*********************************/

.counter-button { display: inline-block; height: 50px; line-height: 50px; padding-right: 30px; padding-left: 70px; position: relative; background-color: rgb(41,127,184); color: rgb(255,255,255); text-decoration: none; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; text-shadow: 0px 1px 0px rgba(0,0,0,0.5); -ms-filter: "progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ff123852,Positive=true)"; zoom: 1;  filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0, OffY=1, Color=#ff123852, Positive=true);
-moz-box-shadow: 0px 2px 2px rgba(0,0,0,0.2); -webkit-box-shadow: 0px 2px 2px rgba(0,0,0,0.2); box-shadow: 0px 2px 2px rgba(0,0,0,0.2); -ms-filter: "progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=2,Color=#33000000,Positive=true)";  filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0, OffY=2, Color=#33000000, Positive=true);
}
.counter-button span { position: absolute; left: 0; width: 50px; background-color: rgba(0,0,0,0.5); -webkit-border-top-left-radius: 5px; -webkit-border-bottom-left-radius: 5px; -moz-border-radius-topleft: 5px; -moz-border-radius-bottomleft: 5px; border-top-left-radius: 5px; border-bottom-left-radius: 5px; border-right: 1px solid rgba(0,0,0,0.15); }
.counter-button:hover span, .counter-button.active span { background-color: rgb(0,102,26); border-right: 1px solid rgba(0,0,0,0.3); }
.counter-button:active { margin-top: 2px; margin-bottom: 13px; -moz-box-shadow: 0px 1px 0px rgba(255,255,255,0.5); -webkit-box-shadow: 0px 1px 0px rgba(255,255,255,0.5); box-shadow: 0px 1px 0px rgba(255,255,255,0.5); -ms-filter: "progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ccffffff,Positive=true)";  filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0, OffY=1, Color=#ccffffff, Positive=true);
}
.counter-button.orange { background: #FF7F00; }
.counter-button.purple { background: #8e44ad; }
.counter-button.turquoise { background: #1abc9c; }
/**/

.el-butns-container { background-color: #cdcdcd; }
.el-butns { border-color: transparent transparent transparent #0073b7; border-style: none none none solid; border-width: medium medium medium 4px; }
.panel-title { display: block; width: 250px; height: 25px; max-height: 25px; }
.participants { display: inline-block; padding: 3px 4px 0 6px; margin: 0 0 3px; }
.participants a.btn { background-color: #f2f2f2; border-color: #cdcdcd; margin-right: -3px; margin-top: -3px; }
.descr.no-bold { padding: 4px 0 0 5px; }


.project-boxes .users {
	border: 1px solid rgb(188, 188, 188);
	max-height: 120px;
	min-height: 120px;
	overflow-x: hidden;
	overflow-y: auto;
	padding: 5px;
}
.project-boxes .users .user-image {
	border: 2px solid #ccc;
	float: left;
	margin: 1px;
	width: 40px;
	border-radius: 50%;
}

.ws-desc{ word-break: break-word; }

</style>
<script type="text/javascript" >


	$(function () {
		$.loc = window.location.href;

		$('body').delegate('.ajax-pagination a', 'click', function (e) {
			e.preventDefault()

			var $this = $(this),
					$parent = $this.parents('.data_container').filter(':first'),
					post = {'project_id': '<?php echo $project_id; ?>', 'limit': '<?php echo $JeeraPaging['limit']; ?>'},
			pageUrl = $this.attr('href');
			$.ajax({
				type: 'POST',
				data: $.param(post),
				url: pageUrl,
				global: true,
				beforeSend: function (response, status, jxhr) {
				},
				success: function (response, status, jxhr) {
							$parent.html(response);
							$('.panel-title').ellipsis();
				}
			})
			return false;
		});
		$('.ws-heading').find('br').remove()


		$('#popup_modal').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal')//.find(".modal-content").html('<div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="<?php echo SITEURL; ?>images/ajax-loader-1.gif" style="margin: auto;"></div>');
		});

	})
</script>

<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_medium" class="modal modal-success fade">
        <div class="modal-dialog modal-md">
             <div class="modal-content"></div>
        </div>
</div>