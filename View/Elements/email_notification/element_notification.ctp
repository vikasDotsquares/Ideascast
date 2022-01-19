<div class="bhoechie-list-wrap">
<?php 
$personlize = array(
	'task_sharing'=>'Task sharing',
	'element_reminders'=>'Reminders',
	'element_schedule_change'=>'Task schedule change',
	'element_deleted'=>'Task deleted',
	'element_schedule_overdue'=>'Task schedule overdue',
	'element_sign_off'=>'Task sign-off',
	'element_reopened'=>'Task re-opened',
	'vote_invitation_request'=>'Vote invitation request',
	'vote_reminder'=>'Vote reminder',
	'vote_removed'=>'Vote removed',
	'feedback_invitation_request'=>'Feedback invitation request',
	'feedback_reminder'=>'Feedback reminder',
	'feedback_received'=>'Feedback received',
	'assignment'=>'Assignment',
	'assignment_removed'=>'Assignment removed'
	); 
	
	if( isset($element_data) && !empty($element_data) && count($element_data) > 0 ){
		$i =0; 
		
		foreach($personlize as $pkey => $listpersonlize){
			 
			 $notification = $this->Common->check_notification($this->Session->read("Auth.User.id"),'element',$pkey );
?>
	
			<div class="bhoechie-list">
					<h5><?php echo $listpersonlize;?></h5>
					<div class="row">			
						<div class="col-xs-4">
						<div class="switch-wrap">
						<label class="lable-l">Email</label>
							<div class="switch">
							<?php $checked = ''; 								
								if(isset($notification['EmailNotification']['email']) && $notification['EmailNotification']['email'] == 1 ){
									$checked = 'checked="checked"';
									$labeltxt = 'On';
								} else if( !isset($notification['EmailNotification']['email']) ){
									$checked = 'checked="checked"';
									$labeltxt = 'On';
								} else {
									$checked = '';
									$labeltxt = 'Off';
								}
							?>
							<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_<?php echo $pkey;?>" name="data[EmailNotification][element][<?php echo $pkey;?>][email]" <?php echo $checked;?> > 
								
								<label for="email_<?php echo $pkey;?>"></label>
							</div>
							 <label class="lable-l"><?php echo $labeltxt;?></label>
						</div>	 
						</div>
						<div class="col-xs-4">
						<div class="switch-wrap switch_wrap_web">
						<label class="lable-l">Web</label>	
							<div class="switch">								
								<?php 
									//$wchecked = (isset($notification['EmailNotification']['web']) && $notification['EmailNotification']['web'] == 1 )? 'checked="checked"':'';
									
									if(isset($notification['EmailNotification']['web']) && $notification['EmailNotification']['web'] == 1 ){
										$wchecked = 'checked="checked"';
										$wlabeltxt = 'On';
									} else if( !isset($notification['EmailNotification']['email']) ){
										$wchecked = 'checked="checked"';
										$wlabeltxt = 'On';
									} else {
										$wchecked = '';
										$wlabeltxt = 'Off';
									}
									
									
								?>
								<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="web_<?php echo $pkey;?>" name="data[EmailNotification][element][<?php echo $pkey;?>][web]" <?php echo $wchecked;?> >
								<label for="web_<?php echo $pkey;?>"></label>
							</div>
							<label class="lable-l"><?php echo $wlabeltxt; //echo (isset($notification['EmailNotification']['web']) && $notification['EmailNotification']['web'] == 1 )? 'On':'Off';?></label>
						</div>
						</div>
						<!--<div class="col-xs-4">
						<div class="switch-wrap">
						<label class="lable-l">Mobile</label>	
							<div class="switch">
								<?php 
									$mchecked = (isset($notification['EmailNotification']['mob']) && $notification['EmailNotification']['mob'] == 1 )? 'checked="checked"':'';
								?>
								<input disabled="disabled" type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_<?php echo $pkey;?>" name="data[EmailNotification][element][<?php echo $pkey;?>][mob]" <?php echo $mchecked;?> >
								<label for="mob_<?php echo $pkey;?>"></label>
							</div>
							<label class="lable-l"><?php echo (isset($notification['EmailNotification']['mob']) && $notification['EmailNotification']['mob'] == 1 )? 'On':'Off';?></label>
						</div>
						</div>-->
						<input type="hidden" name="data[EmailNotification][element][<?php echo $pkey;?>][checking]" value="1">
						
						<input type="hidden" class="cmn-toggle cmn-toggle-round" id="email_<?php echo $pkey;?>" name="data[EmailNotification][element][<?php echo $pkey;?>][id]"  value="<?php echo (isset($notification['EmailNotification']['id'])) ? $notification['EmailNotification']['id'] : '';  ?>" >
						
					</div>
				</div>	
<?php		$i++;	
		} 
	}  
?>
</div>