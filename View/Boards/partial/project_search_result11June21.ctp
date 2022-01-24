<style>
.tipText {
    text-transform: none !important;
}
.names-wrapper{ display:inline-block; }
.names-wrapper span{ width:100%;float:left; margin :0 0 5px; }
</style>
<?php // PROJECTS THAT ARE OWNED BY THE CURRENT USER ARE DISPLAYING PROJECTWISE ?>
<?php // Used in my_sharing.ctp as Element View

// echo $this->Html->css('projects/scroller');

//pr($permit_data['pp_data']);

if(isset($permit_data['pp_data']) && !empty($permit_data['pp_data'])) {

	foreach($permit_data['pp_data'] as $key => $val ) {

		$Project = $val['Project'];

		if(isset($Project['title']) && !empty($Project['title'])){
		//
		//$UserProject = $val['Project'];
		$Board = $val['ProjectBoard'];
		$projectBoardStatus = $Board['project_status'];
		$User = $val['User'];
		$UserDetail = $val['UserDetail'];
		$message = $val['ProjectBoard']['board_msg'];
		$openProjectPanel = $project_key = 0;
		// pr($Board);
 //if(isset($User['id'])){
		if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) ) {
			$user_project_id = project_upid($Project['id']);
			if( $Project['id'] == $this->params['pass'][0] ) {
				$openProjectPanel = $this->params['pass'][0];
				$project_key = $this->params['pass'][0];
			}
		}

		$projectList = $this->Common->ProjectBoardProjectUserList($Project['id']);

		$countRequest = ( isset($projectList) && !empty($projectList) )? count($projectList) : 0;
		//echo $Project['id'];
	?>
	<div class="panel panel-default" data-id="<?php echo $Project['id']; ?>">

		<div class="panel-heading" data-key="<?php echo $project_key; ?>">
			<h4 class="panel-title">
				<a href="#" class="open_panel" data-toggle="collapse" data-parent="#project_accordion"  data-target="#collapse_<?php echo $key; ?>_project"><i class="fa fa-briefcase"></i> <?php echo  strip_tags($Project['title']); ?>&nbsp;(<?php echo $countRequest;?>)
				</a>
<!-- 				<i class="pull-right plus-minus-icon" data-plus="&#xf067;" data-minus="&#xf068;"></i>
	<span class="cst" href="login.php"><i class="fa fa-key"></i><span>Login</span></span> -->
				<a title="Sharing Map" class="btn btn-xs tipText pull-right view_share_map bg-purple" title="" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'sharing_map', $Project['id'], 'admin' => FALSE ), TRUE); ?>" style= "" > <i class="fa fa-share" style="font-size: 11px"></i></a>
			</h4>

		</div>

		<div id="collapse_<?php echo $key; ?>_project" class="panel-collapse collapse <?php if( $openProjectPanel <= 0 ) { ?> close_panel <?php } ?>">
			<div class="panel-body">

				<div class="table-responsive">
<?php
			if(isset($User['id'])){

		 ?>
					<table class="table table-bordered custom-table-border" data-id="" >
						<thead>
							<tr>
								<th width="25%"   class="text-left">Request From</th>
								<th width="25%" class="text-center">Received On</th>
								<th width="35%" class="text-center">Message</th>
								<th width="15%" class="text-center">Action</th>

							</tr>
						</thead>
						<tbody>


				<?php if( isset($User ) && !empty($User ) && isset($projectList) ) {

						$unbind = [
									'hasOne' => ['UserInstitution'],
									'hasMany' => ['UserProject', 'UserPlan', 'UserTransctionDetail'],
								];
				?>
				<?php if( isset($User) && !empty($User) && isset($projectList) ) {

						foreach($projectList as $UserPlist){
							// pr($UserPlist);
							$requestSenderID = $UserPlist['ProjectBoard']['sender'];
							//$userFullName = $this->Common->userFullname($UserPlist['ProjectBoard']['sender']);
							$userFullName = $this->Common->userDetail($UserPlist['ProjectBoard']['sender']);

							$updatedDate = $this->Common->boardUpdated($UserPlist['ProjectBoard']['sender'],$UserPlist['ProjectBoard']['receiver'],$UserPlist['ProjectBoard']['project_id']);



							$projectBoard_projectID = $UserPlist['ProjectBoard']['project_id'];
							$projectBStatus = $UserPlist['ProjectBoard']['project_status'];
							$projectBoardCreated = $UserPlist['ProjectBoard']['created'];
							$projectBoardMessage = $UserPlist['ProjectBoard']['board_msg'];

							$userDetail = $this->ViewModel->get_user( $UserPlist['ProjectBoard']['sender'], null, 1 );
							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'Not Available';
							$job_title = 'Not Available';
							$html = '';
							if(isset($userDetail) && !empty($userDetail)) {

								//$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

								if( isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name']) ){
									$user_name .= htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES);
								}
								if( isset($userDetail['UserDetail']['last_name']) && !empty($userDetail['UserDetail']['last_name']) ){
									$user_name .= ' '.htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
								}

								$profile_pic = $userDetail['UserDetail']['profile_pic'];
								$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

									$html = CHATHTML($this->Session->read("Auth.User.id"),$projectBoard_projectID);


								if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
									$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
								}
							}


					?>
							<tr class=" ">
								<td class="text-left">

								 <!--<a href="#" data-remote="<?php //echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $UserPlist['ProjectBoard']['sender'])); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a>&nbsp;&nbsp;<?php //echo $userFullName; ?>  -->
<div class="style-people-com requestfromtitle">
					<span class="style-popple-icon-out">
								<a  class="pophover not-avail style-popple-icon" data-toggle="modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $UserPlist['ProjectBoard']['sender'])); ?>"  data-target="#popup_modal" data-toggle="modal" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
									<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
								</a>
	</span>
								<div class="style-people-info">
								<span class="style-people-name"><?php echo (isset($userFullName['UserDetail']['first_name']) && !empty($userFullName['UserDetail']['first_name'])) ? $userFullName['UserDetail']['first_name'] : 'N/A' ; ?> <?php echo (isset($userFullName['UserDetail']['last_name']) && !empty($userFullName['UserDetail']['last_name'])) ? ' '.$userFullName['UserDetail']['last_name'] : '' ; ?></span>
								<span class="style-people-title"><?php echo $job_title; ?></span>
								</div>

								</div>
								</td>

								<td class="text-center">
								<?php
								echo ( isset($projectBoardCreated) && !empty($projectBoardCreated)) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectBoardCreated)),$format = 'd M, Y g:iA') : 'N/A';

								//echo (isset($projectBoardCreated) && !empty($projectBoardCreated)) ? date('d M, Y g:iA', strtotime($projectBoardCreated)) : "N/A"; ?>
								</td>

								<td class="text-center"><?php echo (isset($projectBoardMessage) && !empty($projectBoardMessage) ) ? $projectBoardMessage :   'N/A'  ; ?></td>



								<td class="text-center project-requests-but">
									<?php

									$upd = project_upid($Project['id'],true);


									if( $projectBStatus == 0 && $upd['UserProject']['is_board']==1){



									?>
										<a class="btn btn-success btn-sm tipText" title="Share" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_sharing', $Project['id'],$requestSenderID, 2, $user_login, 'admin' => FALSE ), TRUE); ?>" >
											Accept<!--<i class="fa fa-group"></i>-->
										</a>
									<?php

										/*if( ( isset($project_permit['project_level']) && empty($project_permit['project_level'])  ) && ( isset($project_permit['share_permission']) && $project_permit['share_permission'] == 1 ) ) { ?>
											<a class="btn btn-success btn-sm tipText"  title="Update Propagation" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_propagation', $Project['id'], $permit_user_data['User']['id'], 2, 'admin' => FALSE ), TRUE); ?>" >
												<i class="fa fa-pagelines"></i>
											</a>
										<?php } */
										?>
										<a href="javascript:void(0);" title="Decline Interest" class="btn tipText btn-success btn-sm decline_list1" data-id="<?php echo $UserPlist['ProjectBoard']['id']; ?>"  style="margin: 0px;" data-toggle="modal" data-target="#popup_model_box_decline" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'decline_interest', 'project_id'=>$Project['id'], 'board_id'=>$UserPlist['ProjectBoard']['id'], 'login_user'=>$user_login, 'project_status'=>2, 'admin' => FALSE ), TRUE); ?>" > Decline</a>




									<?php }else if($projectBStatus == 0  && $upd['UserProject']['is_board'] < 1){

										echo '<span class="small"><strong>Project No Longer Available</strong><br>';
										echo ( isset($upd['UserProject']['modified']) && !empty($upd['UserProject']['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($upd['UserProject']['modified'])),$format = 'd M, Y h:iA') : 'N/A'.'</span>';

										//echo '<span class="small"><strong>Project No Longer Available</strong><br>'.date('d M, Y h:iA', strtotime($upd['UserProject']['modified'])).'</span>';
									}

									if( $projectBStatus == 1 ){ ?>
										<!--<a class="btn btn-success btn-sm tipText" title="Update Sharing" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_sharing', $Project['id'],$requestSenderID, 2, $user_login, 'admin' => FALSE ), TRUE); ?>" >
											<i class="fa fa-group"></i>
										</a>-->
									<?php
									$project_permit = $this->Common->project_permission_details($Project['id'],$requestSenderID );



										/* if( ( isset($project_permit['ProjectPermission']['project_level']) && empty($project_permit['ProjectPermission']['project_level'])  ) && ( isset($project_permit['ProjectPermission']['share_permission']) && $project_permit['ProjectPermission']['share_permission'] == 1 ) ) { ?>
											<a class="btn btn-success btn-sm tipText"  title="Update Propagation" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_propagation', $Project['id'], $User['id'], 2, 'admin' => FALSE ), TRUE); ?>" >
												<i class="fa fa-pagelines"></i>
											</a>
										<?php }  */


										if( isset($project_permit['ProjectPermission']['project_level']) && $project_permit['ProjectPermission']['project_level'] == 1 ){
											$tipText = "Accepted. Role Given: Owner";
										} else {
											$tipText = "Accepted. Role Given: Sharer";
										}


										echo '<span class="small"><strong>Accepted</strong><br>';
										//echo ( isset($Board['updated']) && !empty($Board['updated'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($Board['updated'])),$format = 'd M, Y h:iA') : 'N/A'.'</span>';

										echo ( isset($updatedDate['ProjectBoard']['updated']) && !empty($updatedDate['ProjectBoard']['updated'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($updatedDate['ProjectBoard']['updated'])),$format = 'd M, Y h:iA') : 'N/A'.'</span>';
										?>

										<br>
										<span class="response-green declineres" title="<?php echo $tipText; ?>" ></span>

									<?php
									}

									if( $projectBStatus == 2 ){
										//receiver
										//project_id
										//pr($Board);
										echo '<span class="small"><strong>Declined</strong><br>';
										echo ( isset($Board['updated']) && !empty($Board['updated'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($Board['updated'])),$format = 'd M, Y h:iA') : 'N/A'.'</span>';

										//$reasonresponse = $this->Common->board_data_by_project_receiver($UserPlist['ProjectBoard']['project_id'],$UserPlist['ProjectBoard']['receiver']);
										
										$reasonresponse = $this->Common->board_data_by_project_sender($UserPlist['ProjectBoard']['project_id'],$requestSenderID);
										
										$declinereason = '';
										if( isset($reasonresponse) && !empty($reasonresponse) ){
											$declinereasons = $this->Common->show_reason($reasonresponse['BoardResponse']['reason']);
												
												

											if( isset($declinereasons) && !empty($declinereasons) ){
												$declinereason = "Declined: ".$declinereasons['DeclineReason']['reasons'];

												if($declinereasons['DeclineReason']['reasons']=='Give no reason'){
													$declinereason = "Declined: No reason given" ;

												}
											}

										?>
										<br>
										<span class="response-red declineres" title="<?php echo $declinereason;?>" ></span>

									<?php }
									} ?>
								</td>
							</tr>

				<?php 	}

					}

				} ?>

						</tbody>
					</table>
			<?php } else{  echo '<div  style="margin: 10px;text-align: center">No Requests.</div>'; }  ?>
			</div>
			</div>
		</div>

	</div>


 <?php } } } else{ ?>
<div  style="margin-bottom: -17px;text-align: center">No Requests.</div>
<?php } ?>
<script>
$(function () {
	$('.pophover1').popover({
		placement : 'bottom',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
	});

	$(".response-red, .response-green").tooltip({
		placement:'top',
		template:'<div class="tooltip declineres" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
		container:'body'
	})
})
</script>