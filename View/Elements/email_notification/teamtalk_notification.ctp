<div class="bhoechie-list-wrap">
<?php 
$personlize = array(
		//'wiki_created'=>'Wiki created',
		//'wiki_page_request'=>'Wiki updated',
		//'wiki_deleted'=>'Wiki deleted',
		'blog_created'=>'Blog created',
		'blog_updated'=>'Blog updated',
		'blog_deleted'=>'Blog deleted',		
	);
 
		$i =0;		
			foreach($personlize as $pkey => $listpersonlize){
			$notification = $this->Common->check_notification($this->Session->read("Auth.User.id"),'team_talk',$pkey );
?>
			<div class="bhoechie-list">
					<h5><?php echo $listpersonlize;?></h5>
					<div class="row">
						<div class="col-xs-4">
						<div class="switch-wrap">
						<label class="lable-l">Email</label>
							<div class="switch">
								<?php $checked = '';
										if( (isset($notification['EmailNotification']['email']) && $notification['EmailNotification']['email'] == 1 ) || !isset($notification['EmailNotification']['email']) ){
											$checked = 'checked="checked"';
											$labeltxt = 'On';
										} else {
											$checked = '';
											$labeltxt = 'Off';
										}
									?>
								<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_<?php echo $pkey;?>" name="data[EmailNotification][team_talk][<?php echo $pkey;?>][email]" <?php echo $checked;?> >
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
									$wchecked = '';
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
								<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="web_<?php echo $pkey;?>" name="data[EmailNotification][team_talk][<?php echo $pkey;?>][web]" <?php echo $wchecked; ?>>
								<label for="web_<?php echo $pkey;?>"></label>
							</div>
							<label class="lable-l"><?php echo $wlabeltxt; ?></label>
						</div>
						</div> 
						<input type="hidden" name="data[EmailNotification][team_talk][<?php echo $pkey;?>][checking]" value="1">
						<input type="hidden" name="data[EmailNotification][team_talk][<?php echo $pkey;?>][id]"  value="<?php echo (isset($notification['EmailNotification']['id']) && !empty($notification['EmailNotification']['id']) ) ? $notification['EmailNotification']['id']: '';  ?>" >
					</div>
				</div>

	<?php
	$i++;
	}
//}
?>
</div>