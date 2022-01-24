<?php
$cid = $this->Common->element_creator($element_id, $project_id);
$ud = $this->Common->userDetail($element['updated_user_id']);
$project_owner_id = $this->Common->userprojectOwner($project_id );

if(isset($ud) && !empty($ud)){

} else if(isset($cid) && $cid != 'N/A' ){
	$ud = $this->Common->userDetail($cid);
}else{
	if($project_owner_id !='N/A'){
		$ud = $this->Common->userDetail($project_owner_id);
	}
}


$task_disabled = '';
$task_tooltip = '';
$task_cursor =  '';
if( isset($element['sign_off']) && !empty($element['sign_off']) && $element['sign_off'] == 1 ){
	$task_disabled = 'disable';
	$task_tooltip = "Task Is Signed Off";
	$task_cursor = " cursor:default !important; ";
}
//if(isset($ud['UserDetail']) && !empty($ud['UserDetail']) ){
?>
<li class="seldm" data-etitle="<?php echo ucfirst(strip_tags($element['title'])); ?>"  data-start="<?php echo _displayDate(date('Y-m-d',strtotime($element['start_date'])),'Y-m-d');?>" data-end="<?php echo _displayDate(date('Y-m-d',strtotime($element['end_date'])),'Y-m-d');?>" >
											<div class="user-contant-area seldm_<?php echo $element_id; ?> <?php echo str_replace('panel-', 'border-', $element['color_code']); ?>">
											  <div class="user-inner-contant">
												<div class="userimage">
												<?php
												/* if(!isset($cid) ||  $cid == 'N/A' ){

												} else {
												} */


										$current_user_id = $this->Session->read('Auth.User.id');
										$element_assigned = element_assigned($element_id );
										$element_project = $project_id;
										$click_html = '';
										$profile_pic = '';
										$receiver_name = 'N/A';
										$receiver_job_title = 'N/A';
										if($element_assigned) {
											$assign_receiver = $element_assigned['ElementAssignment']['assigned_to'];
											$receiver_detail = get_user_data($assign_receiver);
											if(isset($receiver_detail) && !empty($receiver_detail['UserDetail'])){
											$profile_pic = $receiver_detail['UserDetail']['profile_pic'];
											$receiver_name = $receiver_detail['UserDetail']['full_name'];
											$receiver_job_title = $receiver_detail['UserDetail']['job_title'];
											}
											if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
												$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
											}
											if( $assign_receiver != $current_user_id ) {
												$click_html = CHATHTML($assign_receiver, $element_project);
											}

										/*
										<span class="assign">
											<img src="<?php echo $user_image; ?>" class="assign-user-image" style="border: 1px solid #ccc" data-hover-content="Hover Content" data-click-content="<div><p><?php echo $receiver_name; ?></p><p><?php echo $receiver_job_title; ?></p><p><?php echo $click_html; ?></p></div>">
										</span>
										  */
										}


										$el_users = element_users( [$element_id], $project_id);

										$el_users_html = '';
										$el_users_html .= "<div class='el_users' >";

									  $user_found = false;
									   if(isset($el_users) && !empty($el_users)) {

										$assign_receiver = $element_assigned['ElementAssignment']['assigned_to'];
										foreach($el_users as $ou => $ov) {

										$assinghtml = '';
										if( $assign_receiver == $ov ){
											$assinghtml = "&nbsp;<i class='fa fa-check text-blue' style='float:right;#0073b7 !important'></i>";
										}

										  $unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
										  $userDetail = $this->ViewModel->get_user( $ov, $unbind, 1 );
										  if(isset($userDetail) && !empty($userDetail)) {
										   $user_found = true;
										   $user_name = htmlentities($userDetail['UserDetail']['first_name'], ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'], ENT_QUOTES);


										   $el_users_html .= "<a href='javascript:' class='text-black' data-toggle='modal' data-target='#popup_modal' data-remote='".SITEURL."shares/show_profile/".$ov."' ><i class='fa fa-user text-maroon'></i> " . $user_name .$assinghtml. "</a><br />";


										  }

										?>
										<?php }
									   } ?>



									   <?php


										if(!isset($user_image)){
											//$user_image =  SITEURL.'uploads/user_images/'.$ud['UserDetail']['profile_pic'];
											$user_image =  SITEURL.'uploads/user_images/no_photo_small.png';
										}

										$el_users_html .= '</div>';

										$current_user_id = $this->Session->read('Auth.User.id');

										$html = '';
										/* if( $ud['UserDetail']['user_id'] != $current_user_id ) {
											$html = CHATHTML($ud['UserDetail']['user_id'], $project_id);
										} */

										if( isset($assign_receiver) &&  $assign_receiver!= $current_user_id ) {
												$html = CHATHTML($assign_receiver, $project_id);
										}

										 $user_name = 'Not Available';
										$job_title = 'Not Available';
										if(isset($receiver_name) && !empty($receiver_name)) {
											$user_name = htmlentities($receiver_name, ENT_QUOTES);
											$profile_pic = $ud['UserDetail']['profile_pic'];
											$job_title = htmlentities($receiver_job_title, ENT_QUOTES);
										}

										/* Assignment  */

										$element_assigned = element_assigned( $element_id );
										$element_project = $project_id;
										$click_html = '';
										$hover_html = '';
										$profile_pic = '';
										$receiver_name = 'N/A';
										$receiver_job_title = 'N/A';
										if($element_assigned) {
											$hover_html .= '<div class="assign-hover">';
											$assign_creator = $element_assigned['ElementAssignment']['created_by'];
											$assign_receiver = $element_assigned['ElementAssignment']['assigned_to'];
											$reaction = $element_assigned['ElementAssignment']['reaction'];

											$creator_detail = get_user_data($assign_creator);
											$receiver_detail = get_user_data($assign_receiver);
											if(isset($receiver_detail) && !empty($receiver_detail['UserDetail'])){
											$profile_pic = $receiver_detail['UserDetail']['profile_pic'];
											$receiver_name = $receiver_detail['UserDetail']['full_name'];
											$receiver_job_title = $userDetail['UserDetail']['job_title'];
											}
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

										/*======================================================*/


									   ?>


											<?php /* <a id="trigger_edit_profile" href="<?php echo SITEURL;?>shares/show_profile/<?php echo $ud['UserDetail']['user_id']; ?>" data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div class='popBoard'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"> */ ?>

											<a id="trigger_edit_profiles" class="pophover assigned" data-click-content='<?php echo $hover_html; ?>'  data-hover-content="<div class='popBoard'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>">

											<!--<img src="<?php echo SITEURL;?>uploads/user_images/<?php echo $ud['UserDetail']['profile_pic']; ?>">-->
											<img src="<?php echo $user_image; ?>" >

											</a> <?php } else {
											$hover_html= '<span style="font-size:12px;">No Assignment</span>';
											?>

											<a id="trigger_edit_profiles" data-placement="bottom" class="tipTextBoard " title="Unassigned"><img src="<?php echo $user_image; ?>" ></a>

											<?php } ?>

											<a href="javascript:" class="btn btn-default btn-xs users_popovers" id="" style="margin: 4px 0px 0px;" data-content="<?php if($user_found) {echo $el_users_html;}else{ echo 'N/A'; } ?>">

											<i class="fa fa-user-victor" aria-hidden="true"></i></a>

                                            <span class="tab-cal-md">
                                            <a class="ico_cal ico_cal_el tipText pull-right" title="" data-toggle="modal" data-target="#modal_medium" data-remote="<?php echo SITEURL; ?>boards/board_element_date/<?php echo $workspaceDetails['Workspace']['id']; ?>/<?php echo $element['area_id']; ?>/<?php echo $element['id']; ?>" data-original-title="Task Schedule"></a>

                                            </span>

												<span class="wrok-schedule">
											<?php
											if( isset($task_disabled) && !empty($task_disabled) ){
											?>
											<a class="ico_cal ico_cal_el tipText hidden-md-tab <?php echo $task_disabled;?>" title="" data-original-title="<?php echo $task_tooltip;?>" style="<?php echo $task_cursor;?>"></a>
											<?php } else { ?>
											<a class="ico_cal ico_cal_el tipText hidden-md-tab" title="" data-toggle="modal" data-target="#modal_medium" data-remote="<?php echo SITEURL; ?>boards/board_element_date/<?php echo $workspaceDetails['Workspace']['id']; ?>/<?php echo $element['area_id']; ?>/<?php echo $element['id']; ?>" data-original-title="Task Schedule"></a>
											<?php } ?>
														</span>


											</div>
											 <div class="user-right-cont">
												  <a class="read" data-toggle="tooltip"  data-placement="left" title="<?php echo ucfirst(strip_tags($element['title'])); ?>" href="<?php echo SITEURL;?>entities/update_element/<?php echo $element['id'];?>"><h6><?php echo ucfirst($element['title']); ?></h6></a>

												  <aside class="areas" style="display:none">
												  <strong>Area:</strong> <span class="tipText" title="<?php echo strip_tags($areaDeatail['title']); ?>"><?php echo $areaDeatail['title']; ?></span>
												  </aside>

												  <aside class="wsp">
												  <strong>Workspace:</strong> <span class="tipText" title="<?php echo strip_tags($workspaceDetails['Workspace']['title']); ?>"><?php echo $workspaceDetails['Workspace']['title']; ?></span>
												  </aside>

												  <div class="non-user-fluid">
												  <div class="update">Update: <span class="start">
												  <input type="hidden" name="mode" value="<?php echo $element['modified']; ?>" >

												  <?php

												 //echo _displayDate(date('Y-m-d',strtotime($element['modified'])),'d M, Y H:i');
												  echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($element['modified'])),$format = 'd M, Y H:i');
												  ?></span></div>
                                                  <div class="start-date">
												<?php
													if( isset($element['start_date']) && !empty($element['start_date']) ) {
												?>
												Start: <span class="start"><?php
												echo _displayDate(date('Y-m-d',strtotime($element['start_date'])),'d M, Y');?></span>
												<?php
													}else {
												?>Start: <span class="start">Unknown</span>
												<?php
													}
												?></div>
												<div class="end-date">
												<?php
													if( isset($element['end_date']) && !empty($element['end_date']) ) {
												?>
												End: <span class="start">
												<?php
												echo _displayDate(date('Y-m-d',strtotime($element['end_date'])),'d M, Y');?></span>
												<?php
													} else {
												?>End: <span class="start">Unknown</span>
												<?php } ?>

												</div>
												</div>

												</div>

												<div class="user-fluid">
												<div class="update">Update: <span class="start">
												 <input type="hidden" name="mode" value="<?php echo $element['modified']; ?>" >
												<?php
												//echo $element['modified'].'<br>';
												//echo _displayDate(date('Y-m-d',strtotime($element['modified'])),'d M, Y H:i');

												echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($element['modified'])),$format = 'd M, Y H:i');

												?></span></div>
                                                  <div class="start-date">
												<?php
													if( isset($element['start_date']) && !empty($element['start_date']) ) {
												?>
												Start: <span class="start"><?php
												echo _displayDate(date('Y-m-d',strtotime($element['start_date'])),'d M, Y');?></span>
												<?php
													}else {
												?>Start: <span class="start">Unknown</span>
												<?php
													}
												?></div>
												<div class="end-date">
												<?php
													if( isset($element['end_date']) && !empty($element['end_date']) ) {
												?>
												End: <span class="start">
												<?php
												echo _displayDate(date('Y-m-d',strtotime($element['end_date'])),'d M, Y');?></span>
												<?php
													} else {
												?>End: <span class="start">Unknown</span>
												<?php } ?>
												<?php /*?><a class="ico_cal ico_cal_el tipText pull-right " title="" data-toggle="modal" data-target="#modal_medium" data-remote="<?php echo SITEURL; ?>boards/board_element_date/<?php echo $workspaceDetails['Workspace']['id']; ?>/<?php echo $element['area_id']; ?>/<?php echo $element['id']; ?>" data-original-title="Task Schedule"></a><?php */?>
												</div>
												</div>

											  </div>

											</div>
										  </li>
<?php //} else {$b=false;} ?>


<script>
$( document ).ready(function() {

	$(".tipTextBoard").tooltip({
        template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>',
        container: "body",
        placement: "bottom"
    });

	setTimeout(function(){

		$('.users_popovers,.pophover, .assigned').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		});
	/* ===============start assignment popover========================== */

			var showClickPopover = function () {
				$(this).data('bs.popover').options.content = $(this).data('click-content');
				$(this).data('bs.popover').options.title = "Task Leader";
				$(this).popover("show");
				$('.popover-title').show();
			};

			$('.assigned').popover({
				placement : 'bottom',
				trigger : 'hover',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
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
	},1000)
})
</script>