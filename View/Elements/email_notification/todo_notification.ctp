<div class="bhoechie-list-wrap">
<?php
	$personlize = array(
		'todo_request'=>'To-do request',
		'todo_signoff'=>'To-do sign-off',
		'todo_overdue'=>'To-do overdue',
		'todo_delete'=>'To-do deleted'
	);

if( isset($todo_data) && !empty($todo_data) && count($todo_data) > 0 ){
		$i =0;
		foreach($personlize as $pkey => $listpersonlize){
			$notification = $this->Common->check_notification($this->Session->read("Auth.User.id"),'to_dos',$pkey );
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
									<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_<?php echo $pkey;?>" name="data[EmailNotification][to_dos][<?php echo $pkey;?>][email]" <?php echo $checked; ?> >
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
									if(isset($notification['EmailNotification']['web']) && $notification['EmailNotification']['web'] == 1 ){
										$wchecked = 'checked="checked"';
										$wlabeltxt = 'On';
									} else if( !isset($notification['EmailNotification']['web']) ){
										$wchecked = 'checked="checked"';
										$wlabeltxt = 'On';
									} else {
										$wchecked = '';
										$wlabeltxt = 'Off';
									}
								?>
									<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="web_<?php echo $pkey;?>" name="data[EmailNotification][to_dos][<?php echo $pkey;?>][web]" <?php echo $wchecked; ?>>
									<label for="web_<?php echo $pkey;?>"></label>
								</div>
								<label class="lable-l"><?php echo $wlabeltxt; ?></label>
							</div>
							</div>
							<!--<div class="col-xs-4">
							<div class="switch-wrap">
							<label class="lable-l">Mobile</label>
								<div class="switch">
									<?php $mchecked = '';
									if(isset($notification['EmailNotification']['web']) && $notification['EmailNotification']['web'] == 1 ){
										/* $mchecked = 'checked="checked"';
										$mlabeltxt = 'On'; */
										$mchecked = '';
										$mlabeltxt = 'Off';
									} else if( !isset($notification['EmailNotification']['web']) ){
										$mchecked = '';
										$mlabeltxt = 'Off';
									} else {
										$mchecked = '';
										$mlabeltxt = 'Off';
									}
								?>
									<input disabled="disabled" type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_<?php echo $pkey;?>" name="data[EmailNotification][to_dos][<?php echo $pkey;?>][mob]" <?php echo $mchecked; ?>>
									<label for="mob_<?php echo $pkey;?>"></label>
								</div>
								<label class="lable-l"><?php echo $mlabeltxt; ?></label>
							</div>
							</div>-->
							<input type="hidden" name="data[EmailNotification][to_dos][<?php echo $pkey;?>][checking]" value="1">
							<input type="hidden" name="data[EmailNotification][to_dos][<?php echo $pkey;?>][id]"  value="<?php echo (isset($notification['EmailNotification']['id'])) ? $notification['EmailNotification']['id'] : '';  ?>" >
						</div>
					</div>
<?php
		$i++;
		}
	}
?>
</div>