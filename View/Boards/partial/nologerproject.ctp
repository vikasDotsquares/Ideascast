<?php
//pr($listBoardProject);
?>

<script type="text/javascript" >

$(window).load(function(){
	var flag = $('#cc_count').val();
	if(flag > 0){
		$('.box_body_check').show();
	}
})
</script>
<div id="box_body" class="box-body box_body_check" style="display:none;">
<div class="project-boards">

    <h4> No Longer Project</h4>
    <!-- Row Start -->
	<?php 	//pr($viewData['projectsBoard']);
			$projectCount = false;
			if(!empty($listBoardProject['boardProject']) && count($listBoardProject['boardProject']) > 0 ){

				//pr($viewData['projectsBoard']);

				$bsr =1;
				$uid = $this->Session->read('Auth.User.id');
				$flag = 0;
				$boardsCount = count($listBoardProject['boardProject']);

				foreach($listBoardProject['boardProject'] as $boardData){

					if((isset($boardData['Project']['UserProject']['0']) && !empty($boardData['Project']['UserProject']['0']))  && $boardData['Project']['UserProject']['0']['is_board'] == 0){

						$boardData['UserProject']  = $boardData['Project']['UserProject']['0'];


						$level = $this->Common->project_permission_details($boardData['Project']['id'],$uid);

						if(isset($level) && !empty($level)){
							$one = isset($level['ProjectPermission']['project_level']) ? $level['ProjectPermission']['project_level'] : 0;
						}

						$gpid =0;
						if(empty($level)){

							$gpid = $this->Group->GroupIDbyUserID($boardData['Project']['id'],$uid);

							if(!empty($gpid))
							$level = $this->Group->group_permission_details($boardData['Project']['id'],$gpid);

						}

						if(empty($level) || ( isset($level) && ($level['ProjectPermission']['project_group_id']!=$gpid && $level['ProjectPermission']['user_id']!=$uid) ) ){

						$projectOwner = $boardData['UserProject']['user_id'];

						$ownerProjectStatus = false;

						$pid = $boardData['Project']['id'];


						$data1 = $this->Common->project_permission_details($pid,$uid);
						$data2 = $this->Group->group_permission_details($pid,$uid );
						$data3 = $this->Common->userproject($pid, $uid);

						if( !empty($data1) && $data1['ProjectPermission']['project_level']!=1 ){
							$ownerProjectStatus = true;
						}
						if(!empty($data2) && $data2['ProjectPermission']['project_level']!=1 ){
							$ownerProjectStatus = true;
						}
						if( empty($data3) ) {
							$ownerProjectStatus = true;
						}
						$projectCount = true;
						$plusMinusCounter = 1;
						if( $ownerProjectStatus ){

						$boardCount= $this->Common->checkProjectBoardStatus($boardData['Project']['id']);
						//echo $boardCount."boardCount== ID == ".$boardData['Project']['id'];

	?>

							<div data-id="panels-<?php echo $bsr; ?>" style="clear: both" class="panel <?php echo $boardData['Project']['color_code'];?>">
								<div class="panel-heading">
										<h4 class="panel-title col-md-6">
											<span class="trim-text">

												<a  data-original-title="Show/Hide" style="margin-right: 10px" class="btn btn-default btn-xs tipText text-black show_hide_panel pull-left" title="" href="#">

												<i data-toggle="collapse" data-parent="#accordion" href="#open_by<?php echo $bsr; ?>" style="cursor:pointer" class="glyphicon  fa panel-collapse accordion-toggle text-black fa-minus" aria-expanded="true" ></i>
													</a>

												<?php echo htmlentities($boardData['Project']['title'], ENT_QUOTES, "UTF-8");; ?>




											</span>
										</h4>
										<span class="pull-right" style="display:inline-block;" >
												Start: <?php
												echo ( isset($boardData['Project']['start_date']) && !empty($boardData['Project']['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($boardData['Project']['start_date'])),$format = 'd M, Y') : 'N/A';
												//echo date('d M, Y', strtotime($boardData['Project']['start_date'])); ?> End: <?php
												echo ( isset($boardData['Project']['end_date']) && !empty($boardData['Project']['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($boardData['Project']['end_date'])),$format = 'd M, Y') : 'N/A';
												//echo date('d M, Y', strtotime($boardData['Project']['end_date'])); ?>
										</span>

								</div>

								<div data-toggle="collapse" id="open_by<?php echo $bsr; ?>" class="panel-body panel-collapse collapse in" style="" aria-expanded="true">
									<div class="panel-cols-wrap">
										<div class="col-md-3 no-padding panel-boxes">

												<div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Project Objective</div>

																<div class="project-boards-panel-colms  project-objective-desc"><?php echo $boardData['Project']['objective'];  ?></div>


												<div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Alignment</div>


											<div class="project-boards-panel-colms ">	<?php
												  $alignement = get_alignment($boardData['Project']['aligned_id']);
												  if( !empty($alignement) )
												   echo $alignement['title'];
												  else
												   echo "N/A";
												 ?></div>

										</div>
										<div class=" col-md-3 no-padding panel-boxes">

												<div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Description</div>

											<div class="project-boards-panel-colms fix-height"><?php echo $boardData['Project']['description'];  ?></div>
										</div>
										<div class=" col-md-2 no-padding panel-boxes">

												<div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Project Team</div>

											<div class="project-boards-panel-colms fix-height">
												<?php
												//echo ucfirst($boardData['User']['UserDetail']['first_name']).' '.ucfirst($boardData['User']['UserDetail']['last_name']);

												$participants = participants($boardData['Project']['id']);


												$show_sharer = [];
												$allparticip = array();
												if(isset($participants) && !empty($participants)) {
													foreach($participants as $k => $part) {
														$show_sharer[$part] = $this->Common->userFullname($part);
													}

													foreach($participants as $part) {
														$allparticip[] = $this->Common->userFullname($part);
													}
												}

												if(isset($allparticip) && !empty($allparticip)) {

													$key = array_search($this->Common->userFullname($boardData['UserProject']['user_id']), $allparticip);
														if(isset($key) && !empty($key)) {
															$tmp = $allparticip[$key];
															unset($allparticip[$key]);
															$allparticip = array($key => $tmp) + $allparticip;
														}
												}

												//pr($allparticip);
												$allParticipateUser = array();
												$owner = $this->Common->ProjectOwner($boardData['Project']['id'],$projectOwner);

												$allParticipateUser[] = $owner;

												$participants_owners = participants_owners($boardData['Project']['id'], $owner['UserProject']['user_id']);
												$allParticipateUser[] = $participants_owners;

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
												<label> Creator: </label>
													<ul style="list-style:none; padding-left:0;">
														<?php
														$ownerFullName = $this->Common->userFullname($owner['UserProject']['user_id']);
														?>
														<li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $owner['UserProject']['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $ownerFullName; ?></li>

													</ul>

													<label> <?php
													//pr($show_owners);
													$OwnerCounter = 0;
													$totalSharerCount = 0;
													if( count($show_sharer)>0 || count($show_owners) > 0 ){

															$totalSharerCount = count($show_sharer) + count($show_owners) ;

															/* if((isset($owner['UserProject']['user_id']) && !empty($owner['UserProject']['user_id'])) && $totalSharerCount > 0){
																$totalSharerCount  = $totalSharerCount  - 1 ;

															} */
															echo $totalSharerCount;

													} else { echo "0";} ?> People on Project  </label>

													<label> Owner: </label>
													<ul style="list-style:none; padding-left:0;">
														<?php if( isset($show_owners) && !empty($show_owners) ) { ?>
															<?php
															$totalOwnerCounter = 0;
															foreach($show_owners as $key => $val ) {
																if( $owner['UserProject']['user_id'] != $key ){
																	$totalOwnerCounter++;
															?>
																	<li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $val; ?></li>
															<?php }
															}
															if( $totalOwnerCounter == 0 ){
																echo '<li class="not_avail">N/A</li>';
															}
															?>
														  <?php } else{ ?>
															  <li class="not_avail">N/A</li>
														<?php } ?>
													</ul>

													<label> Sharer: </label>
													<ul  style="list-style:none; padding-left:0;">
														<?php if(isset($show_sharer) && !empty($show_sharer)) { ?>
															<?php foreach($show_sharer as $key => $val ) { ?>
															  <li> <a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i></a> <?php echo $val; ?></li>
															<?php } ?>
														<?php } else { ?>
															<li class="not_avail">N/A</li>
														<?php } ?>
													</ul>

											</div>


										</div>
										<div class="col-md-2 no-padding panel-boxes">

												<div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Participant Skills</div>


											<div class="project-boards-panel-colms fix-height">
                                            <ul style="list-style:none; padding-left:0;">
												<?php //pr($allParticipateUser[1]);

													$userTopSkill = get_userSkills($allParticipateUser[1]);
													$UserSkillList = array();
													foreach($userTopSkill as $skillID){
														/* $skillsname =  get_SkillName($skillID['UserSkill']['skill_id']);
														echo "<li>";
															echo $skillsname['Skill']['title'];
														echo "</li>"; */

														$skillsname =  get_SkillName($skillID['UserSkill']['skill_id']);
														$UserSkillList[] =  $skillsname['Skill']['title'];
													}
													sort($UserSkillList);

													foreach($UserSkillList as $skillIDList){
														echo "<li>";
															echo $skillIDList;
														echo "</li>";
													}
												?>
												</ul>

                                                </div>

										</div>
										<div class="col-md-2 no-padding panel-boxes">

												<div class="sub-heading" style="padding: 5px; font-weight: normal; color: black;">Action</div>


												<div class="project-boards-panel-colms fix-height actions">If you are interested in taking part, send interest.
												<br>

												<span class="small fixed_span_block">
												<?php

													$checkProjectexists = $this->Common->ProjectBoardData($boardData['Project']['id'] );

													$dds = $this->Common->ProjectBoardData($boardData['Project']['id']);
													if(isset($dds) && !empty($dds)){
												?>
												<label>Sent Interest:</label>
												<?php

													$SentInterestDate = ( isset($dds['created']) && !empty($dds['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($dds['created'])),$format = 'd M Y h:i:sA') : 'N/A';

													  echo "<p>".$SentInterestDate."</p>";
													  //echo "<p>".date('d M Y h:i:s', strtotime($dds['created']))."</p>";
													}
												?>
												</span>

												<span class="small">

												<?php if( isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] < 1  ){
												?>
												<label>Removed:</label>
												<?php
													$RemovedDate = ( isset($boardData['Project']['modified']) && !empty($boardData['Project']['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($boardData['Project']['modified'])),$format = 'd M Y h:i:sA') : 'N/A';

													echo "<p>".$RemovedDate."</p>";
													//echo "<p>".date('d M Y h:i:s', $boardData['Project']['modified'])."</p>";;

												}

												?>

												</span>

												</div>
                                                <div class="btn-section">
												<?php


												// pr($checkProjectexists);
 												if( isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] < 1  ){
												?>
													<a class="btn btn-success btn-sm disable tipText" title="Interest already Sent."   > Send Interest</a>
												<?php }if( isset($checkProjectexists['project_status']) && $checkProjectexists['project_status'] > 1  ){?>

													<a class="btn btn-success btn-sm disable tipText" title="Not Possible."   >Send Interest</a>
												<?php }else if( !isset($checkProjectexists['project_status'])  ){?>
                                                	<a class="btn btn-success btn-sm" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_interest', $projectOwner,'project:'.$boardData['Project']['id'])); ?>" data-target="#popup_model_box_new" data-toggle="modal" id="filter_list"> Send Interest</a>
												<?php } ?>


                                                </div>
										</div>
									</div>

								</div>

							</div>
							<script type="text/javascript">
								var selectIds<?php echo $bsr; ?> = $('#open_by<?php echo $bsr; ?>');

								$(function ($) {
									selectIds<?php echo $bsr; ?>.on('show.bs.collapse hidden.bs.collapse ', function (e) {
									   $(this).prev().find('.glyphicon').toggleClass('fa-plus fa-minus');
									})
								});
							</script>
						<?php
						       $flag++;
								$bsr++;
										$projectCount = true;
										$plusMinusCounter++;
									}
									//$projectCount = false;
								}
								//$projectCount = false;
							}

						}

						echo "<input type=hidden value='".$flag."' id='cc_count' >";

						}
						?>
						<?php if( isset($projectCount) && $projectCount == false ){?>
							<div data-id="panels-1" class="border padding" align="center">NO PROJECTS</div>
						<?php } ?>
							<!-- Row End -->
                        <div>
						</div>
						<!-- <div class="box-body clearfix list-acknowledge" style="min-height: 600px;"></div> -->
                    </div>
                    </div>