<?php
if(isset($nudges) && !empty($nudges)){
	$current_org = $this->Permission->current_org();
	foreach ($nudges as $index => $vals) {
		$nudge = $vals['Nudge'];
		$nudge_users = $vals['NudgeUser'];

		$data_nudge_type = 'sent';
		$data_nudge_status = 'pending';

		// CHECK IF CURRENT USER IS SENDER OR RECEIVER
		$sender_id = null;
		if($nudge_users['sender_id'] == $this->Session->read('Auth.User.id')){
			$sender_id = $this->Session->read('Auth.User.id');
			$data_nudge_type = 'sent';
		}
		else {
			$sender_id = $nudge_users['sender_id'];
			$data_nudge_type = 'received';
		}

		// POPULATE SENDER AND RECEIVER POPOVERS
		$sender_chat_popover = user_chat_popover($sender_id, $nudge['project_id']);
		$receiver_chat_popover = user_chat_popover($nudge_users['receiver_id'], $nudge['project_id']);

		$sender_org = $this->Permission->current_org($sender_id);
		$receiver_org = $this->Permission->current_org($nudge_users['receiver_id']);


		// CHECK IF USER RESPONDED(GOT IT) OR NOT(PENDING)
		$nudge_status = 'Pending';
		$archive_status = $archive_done = 0;
		if(!empty($nudge_users['status']) && !empty($nudge_users['response'])) {
			$nudge_status = 'Got It';
			$data_nudge_status = 'gotit';
		}
			// IF USER RESPONDED THAN ALWAYS ARCHIVE STATUS IS ON
			// AFTER ON SHOW ARCHIVE ICON
			// CHECK THE PARTICULAR USER SEND IT TO ARCHIVED OR NOT
			$archive_status = 0;
			if($nudge_users['sender_id'] == $this->Session->read('Auth.User.id')){
				$archive_done = $nudge_users['sender_archive'];
			}
			else {
				$archive_done = $nudge_users['receiver_archive'];
			}

			if($archive_done) {
				$data_nudge_status = 'archived';
				// $nudge_status = 'Archived';
				$archive_status = 1;
			}
?>

<div class="nudge-list-data" style="display: block;">
<?php //echo $nudge_users['id'] ?>
	<div class="nudges-data-row" data-nudge-user="<?php echo $nudge_users['id']; ?>" data-type="<?php echo $data_nudge_type; ?>" data-status="<?php echo $data_nudge_status; ?>" data-from="<?php echo htmlentities($sender_chat_popover['user_name'],ENT_QUOTES); ?>" data-to="<?php echo htmlentities($receiver_chat_popover['user_name'],ENT_QUOTES); ?>" data-date="<?php echo $nudge['created']; ?>">
		<div class="col-data col-data-1">
			<div class="col-contant-n">
			<?php //echo $this->Wiki->_displayDate($nudge['created'], $format = 'd M, Y h:i A'); ?>
			<?php echo $this->Wiki->_displayDate($nudge['created'], $format = 'd M, Y'); ?>
			<br />
			<?php echo $this->Wiki->_displayDate($nudge['created'], $format = 'h:i A'); ?>

			<?php //echo date('d M, Y h:i A',strtotime($nudge['created'])); ?></div>
		</div>
		<div class="col-data col-data-2">
			<div class="col-contant-n">
				<div class="style-people-com">
					<span class="style-popple-icon-out">
						<a class="style-popple-icon" href="#" data-toggle="modal"
                            data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $sender_id, 'admin' => FALSE ), true ); ?>"
                            data-target="#popup_modal">
							<img alt="sender image" src="<?php echo $sender_chat_popover['user_image']; ?>" data-content="<div class='nudge-popover'><p><?php echo htmlentities($sender_chat_popover['user_name'],ENT_QUOTES); ?></p><p><?php echo htmlentities($sender_chat_popover['job_title'],ENT_QUOTES); ?></p><?php echo $sender_chat_popover['html']; ?></div>" class="user-image sender" align="left"  />
						</a>
						<?php if($current_org['organization_id'] != $sender_org['organization_id']){ ?>
							<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $sender_id, 'admin' => FALSE ), true ); ?>"></i>
						<?php } ?>
					</span>

					<div class="style-people-info">
						<span class="style-people-name"> <?php echo $this->common->userFirstname($sender_id); ?> <?php echo $this->common->userLastname($sender_id); ?></span>
						<span class="style-people-title"><?php echo htmlentities($sender_chat_popover['job_title'],ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
			 	</div>
			</div>
		</div>
		<div class=" col-data col-data-3">
			<div class="col-contant-n">
				<div class="style-people-com">
					<span class="style-popple-icon-out">
						<a class="style-popple-icon" href="#" data-toggle="modal"
                            data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $nudge_users['receiver_id'], 'admin' => FALSE ), true ); ?>"
                            data-target="#popup_modal">
							<img alt="receiver image" src="<?php echo $receiver_chat_popover['user_image']; ?>" data-content="<div class='nudge-popover'><p><?php echo htmlentities($receiver_chat_popover['user_name'],ENT_QUOTES); ?></p><p><?php echo htmlentities($receiver_chat_popover['job_title'],ENT_QUOTES); ?></p><?php echo $receiver_chat_popover['html']; ?></div>" class="user-image receiver" align="left"  />
						</a>
						<?php if($current_org['organization_id'] != $receiver_org['organization_id']){ ?>
						<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $nudge_users['receiver_id'], 'admin' => FALSE ), true ); ?>"></i>
						<?php } ?>
					</span>
					<div class="style-people-info">
						<span class="style-people-name">  <?php echo $this->common->userFirstname($nudge_users['receiver_id']); ?> <?php echo $this->common->userLastname($nudge_users['receiver_id']); ?></span>
						<span class="style-people-title"><?php echo htmlentities($receiver_chat_popover['job_title'],ENT_QUOTES, "UTF-8"); ?></span>
					</div>
			 	</div>
			</div>
		</div>
		<div class="col-data col-data-4">
			<div class="col-contant-n">
                <span class="text-ellipsis-n"><?php echo htmlentities($nudge['subject'],ENT_QUOTES, "UTF-8"); ?></span>
			</div>
		</div>
		<div class="col-data col-data-5">
			<div class="col-contant-n ">
	           <span class="text-ellipsis-n"><?php echo htmlentities($nudge['message'],ENT_QUOTES, "UTF-8"); ?></span>
			</div>
		</div>
		<div class="col-data col-data-6">
			<div class="col-contant-n ">
				<?php
				if($nudge['page_link']){
					$nudge_link = nudge_link($nudge);
				?>
				<a href="<?php echo $nudge_link; ?>"><i class="link-icon"></i> </a>
				<?php
				}else{
					echo 'None';
				}
				?>
	        </div>
			</div>

		<div class="col-data col-data-7">
			<div class="col-contant-n ">
	            <?php echo ($nudge['email']) ? 'Yes' : 'No'; ?>
	        </div>
		</div>
		<div class="col-data col-data-8">
			<div class="col-contant-n ">
				<?php
				echo $nudge_status;
				?>
			</div>
		</div>
		<div class="col-data col-data-9 col-action-n">
			<div class="col-contant-n">
				<?php
				if(($nudge_users['sender_id'] == $this->Session->read('Auth.User.id') && $nudge_users['receiver_id'] == $this->Session->read('Auth.User.id')) ){
					if((empty($nudge_users['status']) && empty($nudge_users['response']))){
						echo '<a href="#"><i class="gotit-icon tipText action-gotit" title="Got It"></i></a>';
					}
				}
				if(($nudge_users['sender_id'] != $this->Session->read('Auth.User.id') && $nudge_users['receiver_id'] == $this->Session->read('Auth.User.id')) ){
					if(empty($nudge_users['status']) && empty($nudge_users['response'])){
						echo '<a href="#"><i class="gotit-icon tipText action-gotit" title="Got It"></i></a>';
					}
				}
				?>

				<?php
					if($archive_done){ ?>
						<a href="#" class="tipText action-unarchive" title="Unarchive"><i class="unarchive-icon"></i></a>
				<?php }else{ ?>
						<a href="#" class="tipText action-archive" title="Archive"><i class="archive-icon"></i></a>
				<?php } ?>

				<a href="#" class="btn btn-xs btn-default1 action-more-less tipText" title="Show More"><i class="more-icon"></i><i class="less-icon"></i></a>
			</div>
			<?php /* ?><div class="col-contant-n">
				<?php
				if($archive_status){
					if($archive_done){ ?>
						<a href="#" class="tipText action-unarchive" title="Unarchive"><i class="unarchive-icon"></i></a>
				<?php }else{ ?>
						<a href="#" class="tipText action-archive" title="Archive"><i class="archive-icon"></i></a>
				<?php }
				} else{
					if(($nudge_users['sender_id'] == $this->Session->read('Auth.User.id') && $nudge_users['receiver_id'] == $this->Session->read('Auth.User.id')) ){
						if((empty($nudge_users['status']) && empty($nudge_users['response']))){
							echo '<a href="#"><i class="gotit-icon tipText action-gotit" title="Got It"></i></a>';
						}
						else if($nudge_users['sender_archive'] == 1 && $nudge_users['receiver_archive'] == 1) {
							echo '<a href="#" class="tipText action-unarchive" title="Unarchive"><i class="unarchive-icon"></i></a>';
						}
						else {
							echo '<a href="#" class="tipText action-archive" title="Archive"><i class="archive-icon"></i></a>';
						}
					}
					if(($nudge_users['sender_id'] == $this->Session->read('Auth.User.id') && $nudge_users['receiver_id'] != $this->Session->read('Auth.User.id')) ){
						if(empty($nudge_users['status']) && empty($nudge_users['response'])){
							echo '<a href="#" class="tipText action-archive" title="Archive"><i class="archive-icon"></i></a>';
						}
						else if($nudge_users['sender_archive'] == 1){
							echo '<a href="#" class="tipText action-unarchive" title="Unarchive"><i class="unarchive-icon"></i></a>';
						}
						else{
							echo '<a href="#" class="tipText action-archive" title="Archive"><i class="archive-icon"></i></a>';
						}
					}
					if(($nudge_users['sender_id'] != $this->Session->read('Auth.User.id') && $nudge_users['receiver_id'] == $this->Session->read('Auth.User.id')) ){
						if(empty($nudge_users['status']) && empty($nudge_users['response'])){
							echo '<a href="#"><i class="gotit-icon tipText action-gotit" title="Got It"></i></a>';
						}
						else if($nudge_users['receiver_archive'] == 1){
							echo '<a href="#" class="tipText action-unarchive" title="Unarchive"><i class="unarchive-icon"></i></a>';
						}
						else{
							echo '<a href="#" class="tipText action-archive" title="Archive"><i class="archive-icon"></i></a>';
						}
					}
				}

				?>

				<a href="#" class="btn btn-xs btn-default1 action-more-less tipText" title="Show More"><i class="more-icon"></i><i class="less-icon"></i></a>
			</div><?php */ ?>
		</div>
	</div>

</div>
	<?php }//foreach nudges ?>
<?php }//if nudges
else{ ?>
	<div class="no-data"  > No Nudges </div>
<?php } ?>

<script type="text/javascript">
	$(function(){
	    /* $('.user-image.sender,.user-image.receiver').popover({
	        placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
	    }); */

	    $('.text-ellipsis-n', $('.col-data-4')).each(function(index, el) {
	    	var $parent = $(this).parents('.nudges-data-row:first');
	    	var $btn = $(this).parents('.nudges-data-row:first').find('.action-more-less');
	    	if ($(this)[0].scrollWidth >  $(this).innerWidth()) {
			    $btn.show();
			}
			else if ($parent.find('.col-data-5').find('.text-ellipsis-n')[0].scrollWidth >  $parent.find('.col-data-5').find('.text-ellipsis-n').innerWidth()) {
				$btn.show();
			}
			else{
				$btn.hide();
			}
	    });

	})
</script>