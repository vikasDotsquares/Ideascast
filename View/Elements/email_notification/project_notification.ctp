<div class="bhoechie-list-wrap">
<?php
$personlize = array(
		'project_interest_board'=>'Project interest',
		'annotation_add'=>'Annotation added',
		'project_sharing'=>'Project sharing',
		'project_rag'=>'RAG status change',
		'project_deleted'=>'Project deleted',
		'project_schedule_change'=>'Project schedule change',
		'project_schedule_overdue'=>'Project schedule overdue',
		'project_new_member'=>'New project member',
		'project_complete'=>'Project complete',
		'project_reopen'=>'Project re-opened'
);

?>

<?php
if( isset($project_data) && !empty($project_data) && count($project_data) > 0 ){
		$i =0;
		foreach($personlize as $pkey => $listpersonlize){
			 $notification = $this->Common->check_notification($this->Session->read("Auth.User.id"),'project',$pkey );

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
								<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_<?php echo $pkey;?>" name="data[EmailNotification][project][<?php echo $pkey;?>][email]" <?php echo $checked;?> >



								<label for="email_<?php echo $pkey;?>"></label>
							</div>
							 <label class="lable-l"><?php echo $labeltxt;?></label>
						</div>
						</div>
						<div class="col-xs-4">
						<div class="switch-wrap switch_wrap_web">
						<label class="lable-l">Web</label>
							<div class="switch">
								<?php $wchecked = '';
									if( (isset($notification['EmailNotification']['web']) && $notification['EmailNotification']['web'] == 1 ) || !isset($notification['EmailNotification']['web'])  ){
										$wchecked = 'checked="checked"';
										$wlabeltxt = 'On';
									} else {
										$wchecked = '';
										$wlabeltxt = 'Off';
									}
								?>
								<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="web_<?php echo $pkey;?>" name="data[EmailNotification][project][<?php echo $pkey;?>][web]" <?php echo $wchecked;?> >
								<label for="web_<?php echo $pkey;?>"></label>
							</div>
							<label class="lable-l"><?php echo $wlabeltxt;?></label>
						</div>
						</div>
						<input type="hidden" name="data[EmailNotification][project][<?php echo $pkey;?>][checking]" value="1" >
						<input type="hidden" class="cmn-toggle cmn-toggle-round" id="email_<?php echo $pkey;?>" name="data[EmailNotification][project][<?php echo $pkey;?>][id]"  value="<?php echo (isset($notification['EmailNotification']['id'])) ? $notification['EmailNotification']['id'] : '';  ?>" >
					</div>
				</div>
	<?php $i++;
	}
}
 ?>
</div>