<?php // PROJECTS THAT ARE OWNED BY THE CURRENT USER ARE DISPLAYING PROJECTWISE ?>
<?php // Used in my_sharing.ctp as Element View

// echo $this->Html->css('projects/scroller');

//pr($pp_data_users);


if(isset($pp_data_users) && !empty($pp_data_users)) {

	foreach($pp_data_users as $key => $val ) {

		// pr($val['ProjectBoard']);
		 //  pr($val);

		$Project = $val['Project'];
		$Board = $val['ProjectBoard'];
		$projectBoardStatus = $Board['project_status'];
		$User = $val['User'];
		$UserDetail = $val['UserDetail'];
		$message = $val['ProjectBoard']['board_msg'];


		$openProjectPanel = $project_key = 0;

 //if(isset($User['id'])){
		if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) ) {
			$user_project_id = project_upid($Project['id']);
			if( $Project['id'] == $this->params['pass'][0] ) {
				$openProjectPanel = $this->params['pass'][0];
				$project_key = $this->params['pass'][0];
			}
		}

//pr($UserDetail);
		$user_name = '';
		 if(isset($UserDetail) && !empty($UserDetail)) {
			$user_name = $UserDetail['first_name'].' '.$UserDetail['last_name'];
		}
		//echo $Board['receiver'].'='.$Board['sender'];
		$projectList = $this->Common->ProjectBoardProjectListNew($Board['receiver'],$Board['sender']);
// pr($projectList);
		if(isset($user_name) && !empty($user_name) && isset($Project) && !empty($Project)){
		$projectCount = ( isset($projectList) && !empty($projectList) )? count($projectList) : 0;
	?>
	<div class="panel panel-default">
		<div class="panel-heading" data-key="<?php echo $project_key; ?>" >
			<h4 class="panel-title">
			     <a style="width:auto;padding-right:0;" href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $UserDetail['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a>
				<a href="#" class="open_panel nopadding-left" data-toggle="collapse" data-parent="#user_accordion" data-target="#collapse_<?php echo $key; ?>_user">
					<?php  echo $user_name; ?>&nbsp;(<?php echo $projectCount; ?>)
				</a>
				<a title="Sharing Map" class="btn btn-xs tipText pull-right view_share_map bg-purple" title="" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'sharing_map', $Project['id'], 'admin' => FALSE ), TRUE); ?>" style="margin:9px 9px 0 0 !important ; padding:1px 5px;" > <i class="fa fa-share" style="font-size: 11px; margin:0"></i></a>
			</h4>
		</div>


		<div id="collapse_<?php echo $key; ?>_user" class="panel-collapse collapse <?php if( $openProjectPanel <= 0 ) { ?> close_panel <?php } ?>" data-value="<?php  echo $user_name ?>">
			<div class="panel-body">

				<div class="table-responsive">
<?php
			
			
			if(isset($User['id'])){

		 ?>
					<table class="table table-bordered custom-table-border" data-id="" >
						<thead>
							<tr>
								<th width="25%"   class="text-left">Project</th>
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
							$userFullName = $this->Common->userFullname($UserPlist['ProjectBoard']['sender']);
							$projectBoard_projectID = $UserPlist['ProjectBoard']['project_id'];
							$projectBStatus = $UserPlist['ProjectBoard']['project_status'];
							$projectBoardCreated = $UserPlist['ProjectBoard']['created'];
							$projectBoardUpdated = $UserPlist['ProjectBoard']['updated'];
							$projectBoardMessage = $UserPlist['ProjectBoard']['board_msg'];
							$prjectName = $this->Common->get_project($projectBoard_projectID);
							
					?>
							<tr class=" ">
								<td class="text-left"><?php echo strip_tags($prjectName['Project']['title']);  //echo $Project['title']; ?></td>

								<td class="text-center">
								<?php 
								echo ( isset($projectBoardCreated) && !empty($projectBoardCreated)) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectBoardCreated)),$format = 'd M, Y g:iA') : 'N/A';

								//echo (isset($projectBoardCreated) && !empty($projectBoardCreated)) ? date('d M, Y g:iA', strtotime($projectBoardCreated)) : "N/A";
								?>
								</td>

								<td class="text-center"><?php echo (isset($projectBoardMessage) && !empty($projectBoardMessage) ) ? $projectBoardMessage :   'N/A'  ; ?></td>



								<td class="text-center project-requests-but">
									<?php $upd = project_upid($UserPlist['ProjectBoard']['project_id'],true);


									//if( $projectBStatus == 0 && $upd['UserProject']['is_board']==1){
									if( $projectBStatus == 0 ){


									?>
										<a class="btn btn-success btn-sm tipText" title="Share" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_sharing', $UserPlist['ProjectBoard']['project_id'],$requestSenderID, 2, $user_login, 'admin' => FALSE ), TRUE); ?>" >
											Accept<!--<i class="fa fa-group"></i>-->
										</a>
									<?php

										/*if( ( isset($project_permit['project_level']) && empty($project_permit['project_level'])  ) && ( isset($project_permit['share_permission']) && $project_permit['share_permission'] == 1 ) ) { ?>
											<a class="btn btn-success btn-sm tipText"  title="Update Propagation" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_propagation', $Project['id'], $permit_user_data['User']['id'], 2, 'admin' => FALSE ), TRUE); ?>" >
												<i class="fa fa-pagelines"></i>
											</a>
										<?php }

										<a href="javascript:void(0);" title="Decline Interest" class="btn tipText btn-success btn-sm decline_list1" data-id="<?php echo $UserPlist['ProjectBoard']['id']; ?>"  style="margin: 0px;" > Decline</a>	 */
										?>



										<a href="javascript:void(0);" title="Decline Interest" class="btn tipText btn-success btn-sm decline_list1" data-id="<?php echo $UserPlist['ProjectBoard']['id']; ?>"  style="margin: 0px;" data-toggle="modal" data-target="#popup_model_box_decline" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'decline_interest', 'project_id'=>$Project['id'], 'board_id'=>$UserPlist['ProjectBoard']['id'], 'login_user'=>$user_login, 'project_status'=>2, 'admin' => FALSE ), TRUE); ?>" > Decline</a>


									<?php }
									/* else if($projectBStatus == 0  && $upd['UserProject']['is_board'] < 1){


										echo '<span class="small"><strong>Project No Longer Available</strong><br>';
										echo ( isset($upd['UserProject']['modified']) && !empty($upd['UserProject']['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($upd['UserProject']['modified'])),$format = 'd M, Y h:iA') : 'N/A'.'</span>';
										//echo '<span class="small"><strong>Project No Longer Available</strong><br>'.date('d M, Y h:iA', strtotime($upd['UserProject']['modified'])).'</span>';
									} */

									if( $projectBStatus == 1 ){ ?>
										<!--<a class="btn btn-success btn-sm tipText" title="Update Sharing" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_sharing', $UserPlist['ProjectBoard']['project_id'],$requestSenderID, 2, $user_login, 'admin' => FALSE ), TRUE); ?>" >
											<i class="fa fa-group"></i>
										</a>-->
									<?php
									$project_permit = $this->Common->project_permission_details($UserPlist['ProjectBoard']['project_id'],$requestSenderID );



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

										if( isset($projectBoardUpdated) && !empty($projectBoardUpdated) ){
											echo '<span class="small"><strong>Accepted</strong><br>';
											echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectBoardUpdated)),$format = 'd M, Y h:i A').'</span>';
										} else {
											echo '<span class="small"><strong>Accepted</strong><br>N/A</span>';
										} ?>

										<br>
										<span class="response-green declineres" title="<?php echo $tipText; ?>" ></span>

									<?php
										//echo '<span class="small"><strong>Accepted</strong><br>'.date('d M, Y h:iA', strtotime($Board['updated'])).'</span>';
									}

									if( $projectBStatus == 2 ){

										$reasonresponse = $this->Common->board_data_by_project_receiver($UserPlist['ProjectBoard']['project_id'],$UserPlist['ProjectBoard']['receiver']);

										echo '<span class="small"><strong>Declined</strong><br>';
										echo ( isset($projectBoardUpdated) && !empty($projectBoardUpdated)) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectBoardUpdated)),$format = 'd M, Y h:i A') : 'N/A'.'</span>';


										$declinereason = '';
										if( isset($reasonresponse) && !empty($reasonresponse) ){
											$declinereasons = $this->Common->show_reason($reasonresponse['BoardResponse']['reason']);
											if( isset($declinereasons) && !empty($declinereasons) ){
												$declinereason = "Declined: ".$declinereasons['DeclineReason']['reasons'];
											}

									?>

										<br>
										<span class="response-red declineres" title="<?php echo $declinereason;?>" ></span>

										<?php } //echo '<span class="small"><strong>Declined</strong><br>'.date('d M, Y h:iA', strtotime($Board['updated'])).'</span>';
									} ?>
								</td>
							</tr>

				<?php	}
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
<div  style="margin-bottom: -17px; text-align: center">No Requests.</div>
<?php } ?>
<script>
$(function () {

	$(".response-red, .response-green").tooltip({
		placement:'top',
		template:'<div class="tooltip declineres" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
		container:'body'
	})

})
</script>