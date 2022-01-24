<?php
$current_user_id = $this->Session->read('Auth.User.id');

$wsp_error = false;
$ele_error = false;

if(isset($params) && !empty($params)) {
	if(!isset($params['ws_ids']) || empty($params['ws_ids'])) {
		$wsp_error = true;
	}
$add_icon = $record_found = 0;
?>

	<?php if(isset($params['area_ids']) && !empty($params['area_ids'])) {

		foreach($params['area_ids'] as $key => $val ) {
			$ele_error = false;

			$area_id = $val;
			// e($area_id);

			if( isset($area_id) && !empty($area_id)) {

				$area_detail = $this->ViewModel->getAreaDetail($area_id);

				$ws_id = area_workspace($area_id);

				if( isset($ws_id) && !empty($ws_id)) {

					$ws_pwid = workspace_pwid($params['project_id'], $ws_id);

					$upid = project_upid($params['project_id']);

					$ws_permit_users = $this->Group->wsp_users( $upid, $ws_pwid );
					$owner_id = $this->Common->userprojectOwner($params['project_id'] );

					$participants_owners = participants_owners($params['project_id'], $owner_id);

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
					$ws_detail = $this->ViewModel->getWorkspaceDetail( $ws_id );

					$ws_detail = ( isset($ws_detail['Workspace']) && !empty($ws_detail['Workspace'])) ? $ws_detail['Workspace'] : null;

					$options = [
						'area' 			=> $area_id,
						'sort_by' 		=> (isset($params['sort_by']) && !empty($params['sort_by'])) ? $params['sort_by'] : null,
						'task_status' 	=> (isset($params['task_status']) && !empty($params['task_status'])) ? $params['task_status'] : null,
					];

					$elements = $this->ViewModel->getTaskListElements( $options );
	if( isset($elements) && !empty($elements) ) {

			$record_found = 1;
				if( $add_icon != $ws_id ) {
					$add_icon = $ws_id;
					// echo $add_icon;
		?>
				<div class="exp-col-wrapper text-right">
					<a href="#" class="exp-col-ws tipText" title="Expand/Collapse Workspace" data-target="panels-<?php echo $ws_id; ?>">
						<i class="fa fa-plus plus-one"></i>
						<i class="fa fa-plus plus-two"></i>
						<i class="fa fa-plus plus-three"></i>
					</a>
				</div>
				<?php } ?>
				<div class="panel panel-<?php echo str_replace('bg-', '', $ws_detail['color_code']) ; ?>  " style="clear: both" data-id="panels-<?php echo $ws_id; ?>">
					<div class="panel-heading" >

						<h4 class="panel-title workshop-task-list-h">
							<span class="trim-text">
								<i class="fa fa-th text-white"></i> <?php echo strip_tags($ws_detail['title']); ?>
							</span>
							<span class="trim-text trim-two">
								<i class="fa fa fa-list-alt text-white"></i> <?php echo strip_tags($area_detail['title']) ; ?>
							</span>

							<span class="pull-right tipText" style="margin-right:0" title=" Open">
								<a href="<?php echo SITEURL.'projects/manage_elements/'.$params['project_id'].'/'.$ws_id; ?>"><i class="fa  fa-folder-open text-white">&nbsp;</i></a>
								<i class="glyphicon fa fa-plus panel-collapse  accordion-toggle text-white" style="cursor:pointer" href="#open_by<?php echo $area_id;?>" data-parent="#accordion" data-toggle="collapse"></i>
							</span>
						</h4>
					</div>
					<div class="panel-body panel-collapse collapse" id="open_by<?php echo $area_id;?>" data-toggle="collapse">

						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-3 col-md-3 col-lg-2 no-padding">

								<?php echo $this->element('../Entities/partials/area_template', ['workspace_id' => $ws_detail['id'], 'area_id' => $area_detail['id']]); ?>

									<?php //echo workspace_template( $ws_detail['template_id'], true );  ?>
								</div>

				<div class="col-sm-9 col-md-9 col-lg-10">
					<div class="row project-owners-row">
						<span style="margin: 13px 0px 0px; font-weight: 700;" class="col-sm-12 col-md-2 project-owners-tital">Project Owners: </span>
						<div class="participants_users col-sm-12 col-md-10">
						  <?php if(isset($show_owners) && !empty($show_owners)) {

								foreach($show_owners as $key => $v ) {
									$html = '';
									if( $key != $current_user_id ) {
										$html = CHATHTML($key, $params['project_id']);
								  	}
								  $style = '';

									if( $owner_id == $key ) {
										$style = 'border: 3px solid #333';
									}

									$userDetail = $this->ViewModel->get_user( $key, null, 1 );
									$user_image = SITEURL . 'images/placeholders/user/user_1.png';
									$user_name = 'N/A';
									$job_title = 'N/A';
									if(isset($userDetail) && !empty($userDetail)) {
										$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
										$profile_pic = $userDetail['UserDetail']['profile_pic'];
										$job_title = $userDetail['UserDetail']['job_title'];

										if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
											$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
										}
									}
								?>
									<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
									  <img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
									</a>
							  <?php } ?>
						  <?php }
						  else {
								echo 'N/A';
						  }
						  ?>
						</div>
					</div>
                </div>
								<div class="col-sm-12 col-md-9 col-lg-10 project-owners-block">
									<div class="row">
										<!-- <div class="col-sm-12 nopadding-right">
											<span><b> Project Owners</b></span>
											<span class="participants_box no-bold margin-left pull-right">

												<?php if(isset($show_owners) && !empty($show_owners)) { ?>
												<?php foreach($show_owners as $key => $v ) { ?>
												<span class="bg-gray participants" style="">
													<?php   echo $v; ?>
													<a class="show_profile btn btn-sm" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" href="#"><i class="fa fa-user text-maroon " ></i></a>
												</span>
												<?php } ?>
												<?php }else{
												?>
												<span class="bg-gray participants" style="padding: 4px 6px 5px; margin-bottom: 2px;">N/A </span>
												<?php } ?>
											</span>
										</div> -->
										<div class="col-sm-12 ">
											<span class="start-end-dates col-xs-12 col-sm-12 col-md-12 col-lg-12" style="">
												<span><b> Workspace</b></span>
												<span class="margin-left"><b> Start:</b>
												<?php //echo ( isset($ws_detail['start_date']) && !empty($ws_detail['start_date'])) ? date('d M Y', strtotime($ws_detail['start_date'])) : 'N/A';
												echo ( isset($ws_detail['start_date']) && !empty($ws_detail['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($ws_detail['start_date'])),$format = 'd M Y') : 'N/A';

												?></span>
												<span class="margin-left"><b> End:</b>
												<?php //echo ( isset($ws_detail['end_date']) && !empty($ws_detail['end_date'])) ? date('d M Y', strtotime($ws_detail['end_date'])) : 'N/A';
												echo ( isset($ws_detail['end_date']) && !empty($ws_detail['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($ws_detail['end_date'])),$format = 'd M Y') : 'N/A';
												?></span>
												<span class="start-end-dates margin-left wsp_<?php echo workspace_status($ws_detail['id']); ?>" style="padding: 5px;">
													<b class="hidden-md">Status: </b><?php echo $this->Common->workspace_status($ws_detail['id']);?>	</span>

												<div class="pull-right btn-group" style="">

												<?php
												$riskelementcount = $this->ViewModel->wsp_permission_risk_area_element_count($params['project_id'],$ws_id,$area_id);


												if( isset($riskelementcount) && $riskelementcount > 0 ){
													$riskcounttext = "Risks in Workspace Area: ".$riskelementcount;
												?>
													<span data-toggle="modal" class="btn btn-sm  exclamation-risk green tipText" title="<?php echo $riskcounttext;?>" data-remote="<?php echo SITEURL ?>entities/area_risks/<?php echo $params['project_id']; ?>/<?php echo $ws_id;?>/<?php echo $area_id;?>" data-target="#popup_modal" rel="tooltip" ><i class="fa fa-exclamation" aria-hidden="true"></i></span>
												<?php } else { $riskcounttext = "No Risks in Workspace Area"; ?>
													<a href="<?php echo Router::url(array('controller' => 'risks', 'action' => 'manage_risk', 0, $params['project_id'] )); ?>" class="btn btn-sm  exclamation-risk no-risk tipText" title="<?php echo $riskcounttext;?>"><i class="fa fa-exclamation" aria-hidden="true"></i></a>
												<?php } ?>


													<span class="ico_cal ico_cal_ws tipText" title="Workspace Schedule" data-toggle="modal" data-target="#myModal" data-remote="<?php echo Router::url(array('controller' => 'entities', 'action' => 'task_list_ws_date', $params['project_id'], $ws_id)); ?>"></span>
													<span class="ico_cal ico_cal_el tipText" title="Task Schedules" data-toggle="modal" data-target="#myModal" data-remote="<?php echo Router::url(array('controller' => 'entities', 'action' => 'task_list_el_date', $ws_id, $area_detail['id'])); ?>"></span>
													<!-- <a href="#" class="btn btn-default btn-sm">
														<i class="fa fa-th" ></i>
														<i class="fa fa-calendar" ></i>
													</a> -->
												</div>
											</span>

										</div>
									</div>
								</div>
							</div>
						</div>
						<script type="text/javascript">
								var selectIds = $('<?php echo '#open_by'.$area_id; ?>');

								$(function ($) {
									selectIds.on('show.bs.collapse hidden.bs.collapse ', function (e) {
									   $(this).prev().find('.glyphicon').toggleClass('fa-plus fa-minus');
									   $(this).prev().find('.glyphicon').parent().toggleAttr('data-original-title', 'Close', 'Open');
									})


									$('#popup_modal').on('hidden.bs.modal', function () {
										$(this).removeData('bs.modal');
										$(this).find('.modal-content').html('');
									});

								});</script>
						<div class="col-sm-12">
							<div class="row">
								<?php if( isset($elements) && !empty($elements)) { ?>
								<div class="table-responsive clearfix elements-table">
									<table class="table table-striped">
										<tbody>
									<?php foreach($elements as $k_el => $v_el) { ?>
										<?php $element_status = element_status($v_el['element']['id']); ?>
											<tr class="data">
												<td width="40%"><b>Task:</b> <?php echo strip_tags($v_el['element']['title']); ?></td>
												<td width="15%"><b>Start: </b><?php	echo ( isset($v_el['element']['start_date']) && !empty($v_el['element']['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($v_el['element']['start_date'])),$format = 'd M Y') : 'N/A';?></td>
												<td width="15%"><b>End: </b><?php echo ( isset($v_el['element']['end_date']) && !empty($v_el['element']['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($v_el['element']['end_date'])),$format = 'd M Y') : 'N/A';?></td><td width="20%" class="cell_<?php echo $element_status; ?>"><b class="hidden-md">Status: </b><?php echo $this->Common->element_status($v_el['element']['id']);  ?>
													<?php
													$user_image = SITEURL . 'images/placeholders/user/user_1.png';
													$element_assigned = element_assigned( $v_el['element']['id'] );
													$element_project = $params['project_id'];
													$click_html = '';
													$hover_html = '';
													$receiver_name = 'N/A';
													$receiver_job_title = 'N/A';
													if($element_assigned) {
														$hover_html .= '<div class="assign-hover">';
														$assign_creator = $element_assigned['ElementAssignment']['created_by'];
														$assign_receiver = $element_assigned['ElementAssignment']['assigned_to'];
														$reaction = $element_assigned['ElementAssignment']['reaction'];

														$creator_detail = get_user_data($assign_creator);
														$receiver_detail = get_user_data($assign_receiver);
														$profile_pic = $receiver_detail['UserDetail']['profile_pic'];
														$receiver_name = $receiver_detail['UserDetail']['full_name'];
														$receiver_job_title = $userDetail['UserDetail']['job_title'];
														if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
															$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
														}
														if( $assign_receiver != $current_user_id ) {
															$click_html = CHATHTML($assign_receiver, $element_project);
													  	}

													  	$assigned_label = 'Asssigned to: ';
													  	$assigned_to = $receiver_name;
													  	if( $assign_receiver == $current_user_id ) {
													  		$assigned_to = 'Me';
													  	}
													  	if($reaction == 3){
													  		$assigned_label = 'Disengaged by: ';
													  	}
													  	$hover_html .= '<span>'.$assigned_label.$assigned_to.'</span>';

													  	$assigned_by = $creator_detail['UserDetail']['full_name'];
													  	if( $assign_creator == $current_user_id ) {
													  		$assigned_by = 'Me';
													  	}
													  	$hover_html .= '<span>Assigned by: '.$assigned_by.'</span>';

													  	$reaction_label = '';
													  	if($reaction == 1){
													  		$reaction_label = 'Schedule: Accepted';
													  	}
													  	else if($reaction == 2){
													  		$reaction_label = 'Schedule: Not Accepted';
													  	}
													  	$hover_html .= '<span>'.$this->Wiki->_displayDate($element_assigned['ElementAssignment']['modified'], 'd M, Y g:iA').'</span>';
													  	$hover_html .= $reaction_label . '</div>';
													?>
													<span class="assign">
													    <img src="<?php echo $user_image; ?>" class="assign-user-image" style="border: 1px solid #ccc" data-click-content='<?php echo $hover_html; ?>' data-hover-content="<div><p><?php echo $receiver_name; ?></p><p><?php echo $receiver_job_title; ?></p><p><?php echo $click_html; ?></p></div>">
													</span>
													<?php } ?>

												</td>
												<td width="10%" align="center" class="more-opnetask-but">
													<a class="btn btn-sm btn-default tipText show_el_detail" title="More">
														<i class="fa fa-arrow-down show_el_detail_icon"></i>
													</a>
													<a class="btn btn-sm btn-default tipText open_el" title="Open Task" href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $v_el['element']['id']), true); ?>">
														<i class="fa fa-folder-open"></i>
													</a>
												</td>
											</tr>
											</tr>

											<tr style="display: none" class="el_detail">

												<?php
												$ownerEle = $this->Common->element_creator($v_el['element']['id'],$params['project_id']);
												$element_sharers = $this->Common->element_sharers($v_el['element']['id'],$params['project_id']);

												?>

												<td colspan="5">
													<div class="margin-top" style=" ">
                                                    <!-- <div class="participants_owner-block">
														<label>Creator:</label>&nbsp;
                                                      <span class="participants_owner">
                                                      <span style="" class="bg-gray participants">
                                                      <?php if(isset($ownerEle['user_id']) && $ownerEle['user_id'] >0){
													echo $ownerEle['username'] ; ?>
                                                      <a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL.'/shares/show_profile/'.$ownerEle['user_id'];?>" class="show_profile btn btn-sm"><i class="fa fa-user text-maroon "></i></a>
                                                      <?php }else{  echo "N/A"; }  ?>
                                                      </span>
                                                      </span>
                                                      </div> -->


														<div class="clearfix" >
															<span style="margin: 20px 0px 0px; font-weight: 700;" class="col-sm-12 col-md-1 no-padding sharers-tital">Sharers:</span>
															<div class="participants_users col-sm-12 col-md-11">
																<?php if(isset($element_sharers) && !empty($element_sharers)){

																	foreach($element_sharers as $shr){
																		if( isset($shr) && !empty($shr)) {
																		$html = '';
																		if( $shr != $current_user_id ) {
																			$html = CHATHTML($shr, $params['project_id']);
																		}
																		$style = '';

																		if( $owner_id == $shr ) {
																			$style = 'border: 3px solid #333';
																		}

																		$userDetail = $this->ViewModel->get_user( $shr, null, 1 );
																		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
																		$user_name = 'N/A';
																		$job_title = 'N/A';
																		if(isset($userDetail) && !empty($userDetail)) {
																			$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
																			$profile_pic = $userDetail['UserDetail']['profile_pic'];
																			$job_title = $userDetail['UserDetail']['job_title'];

																			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
																				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
																			}
																		}
																	?>
																		<a href="#"  data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $shr)); ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
																			<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
																		</a>
																		<?php } ?>
																	<?php } ?>
																<?php }else{
																?>
																N/A
																<?php
																} ?>
															</div>
														</div>


                                                    <!-- <div class="participants_box-block">
															<label >Sharers:</label>&nbsp;
														  <span class="participants_box" >
														  <?php if(isset($element_sharers) && !empty($element_sharers)){ ?>


														  <?php foreach($element_sharers as $shr){
																if( isset($shr) && !empty($shr)) {
														 ?>
														  <span style="padding: 5px 7px 2px 6px;" class="bg-gray participants"><?php echo $this->Common->userFullname($shr) ; ?>
														  <a href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL.'/shares/show_profile/'.$shr;?>" class="show_profile btn btn-sm"><i class="fa fa-user text-maroon "></i></a>

														  <a  data-toggle="modal" data-target="#popup_modal_new"  data-remote="<?php echo SITEURL.'shares/my_element_permissions/'.$params['project_id'].'/'.$ws_id.'/'.$v_el['element']['id'].'/'.$shr; ?>" data-original-title="Permissions" style="padding:5px" title=""    class="btn btn-primary btn-xs text-bold more tipText">
															  <i class="fa fa-fw fa-share text-blue"></i>
														  </a>
														  </span>
														  <?php }} ?>


														  <?php }else{ ?>
														  <span style="" class="bg-gray participants">N/A</span>
														  <?php } ?>
														  </span>
                                                      </div> -->

													</div>
													<br />
													<label class="">Description:</label>
													<span class="task_desc">
														<?php echo $v_el['element']['description']; ?>
													</span>

													<label class="">Outcome:</label>
													<span class="task_comments">
														<?php echo $v_el['element']['comments']; ?>
													</span>

												</td>
											</tr>
									<?php } ?>
										</tbody>
									</table>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

			<?php
				}
				else{
					$ele_error = true;
				}

			}
		}
	}
} ?>

<?php } ?>
<?php

$msg = '';
if($wsp_error) {
	$msg = 'No Workspace in Project';
}
else if(!$wsp_error && $ele_error) {
	$msg = 'No Tasks in Workspace';
}

if( ($params['wsp_selected']) && (isset($params['area_ids']) && !empty($params['area_ids']))) {
	if( !isset($elements) || empty($elements) ){
		$msg = 'No Tasks in Project';
	}
}


if(empty($record_found)){ ?>
	<div width="100%" style="padding: 10px; color: #bbbbbb; font-size: 30px; text-align: center; text-transform: uppercase;"  ><?php echo $msg; ?></div>
<?php } ?>

 <!-- MODAL BOX WINDOW -->
              <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content"></div>
                </div>
              </div>

              <div class="modal modal-success fade " id="popup_modal_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content"></div>
                </div>
              </div>
<!-- END MODAL BOX -->
<!-- END MODAL BOX -->
<script type="text/javascript" >
$(function(){

	$('.exclamation-risk.no-risk').on('click', function(event) {
		event.preventDefault();
		location.href = $(this).attr('href');
	});


	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });


    var showClickPopover = function () {
        $(this).data('bs.popover').options.content = $(this).data('click-content');
        $(this).data('bs.popover').options.title = "Task Leader";
        $(this).popover("show");
        $('.popover-title').show();
    };

    $('.assign-user-image').popover({
        placement: "bottom",
        container: 'body',
        trigger: 'manual',
        html: true,
        delay: {show: "50", hide: "400"}
    })
    .click(showClickPopover)
    .on("mouseenter", function () {
        var _this = this;
        $(this).data('bs.popover').options.content = $(this).data('hover-content');
        $(this).data('bs.popover').options.title = '';
        $(this).data('original-title', '');
        $(this).attr('data-original-title', '');
        $(this).popover('show');
        setTimeout(function(){
	        $(".popover").on("mouseleave", function () {
	            $(_this).popover('hide');
	        });
        }, 300)
    })
    .on("mouseleave", function () {
        var _this = this;
        setTimeout(function () {
            if (!$(".popover:hover").length) {
                $(_this).popover("hide");
            }
        }, 300);
    });




})
</script>