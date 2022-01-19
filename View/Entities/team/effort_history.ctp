<?php
	$current_user_id = $this->Session->read("Auth.User.id");
	$current_org = $this->Permission->current_org();
	$effort_history = $this->Permission->effort_history($task_id, $user_id);
	$taskRole = taskRole($task_id, $current_user_id);
	$owner = ($taskRole == 'Creator' || $taskRole == 'Owner' || $taskRole == 'Group Owner') ? true : false;
	$el_signoff = (isset($el_signoff) && !empty($el_signoff)) ? true : false;
?>
<div class="effort-history-info">
<?php
if(isset($effort_history) && !empty($effort_history)) { ?>
	<?php foreach ($effort_history as $key => $value) {
		$data = $value['ef'];
		$user_data = $value['ud'];

		$user_name = htmlentities($value[0]['user_name'], ENT_QUOTES);
		$profile_pic = $user_data['profile_pic'];
		$job_title = htmlentities($user_data['job_title'], ENT_QUOTES);

		$html = '';
		if( $user_data['user_id'] != $current_user_id ) {
			$html = CHATHTML($user_data['user_id'], $data['project_id']);
		}

		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
			$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
		}
		$completed = ($data['completed_hours'] == 1) ? $data['completed_hours'].' Hour' : ((empty($data['completed_hours'])) ? '0 Hours' : $data['completed_hours'].' Hours');
		$remaining = ($data['remaining_hours'] == 1) ? $data['remaining_hours'].' Hour' : ((empty($data['remaining_hours'])) ? '0 Hours' : $data['remaining_hours'].' Hours');
	?>
	<div class="style-people-com effort-row" data-id="<?php echo $data['id']; ?>">
		<span class="style-popple-icon-out">
			<a class="style-popple-icon" style="cursor: default">
				<img src="<?php echo $user_image; ?>" class="" align="left" width="36" height="36" >
			</a>
			<?php   if($current_org['organization_id'] != $user_data['organization_id']){ ?>
				<i class="communitygray18 tipText community-g" title="Not In Your Organization"></i>
			<?php } ?>
		</span>
		<div class="style-people-info">
			<span class="style-people-name" style="cursor: default">Completed: <?php echo $completed; ?>, Remaining: <?php echo $remaining; ?></span>
			<span class="style-people-name" style="cursor: default"><?php echo htmlentities($data['comment'], ENT_QUOTES, "UTF-8"); ?></span>
			<span class="style-people-title">
			<?php echo $this->Wiki->_displayDate( date('Y-m-d H:i',strtotime($data['created'])), 'd M, Y h:i A'); ?>
			</span>
			<?php if($owner && !$el_signoff){ ?>
				<span class="effortdelete"><a href="#" class="remove-effort tipText" title="Delete"><i class="deleteblack"></i></a></span>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
<?php }else{ ?>
	<div class="no-summary-found">No Effort</div>
<?php } ?>
</div>
<script type="text/javascript">
	$(() => {
		$('.effort-history-info').slimScroll({height: 200, alwaysVisible: true});

		$('.remove-effort').off('click').on('click', function(event) {
			event.preventDefault();
			var $parent = $(this).parents('.effort-row'),
				id = $parent.data('id');

			$.ajax({
				url: $js_config.base_url + 'entities/remove_effort',
				type: 'POST',
				data: {id: id},
				dataType: 'json',
				success:function(response) {
					if(response.success){
						$.save_effort = true;
						$parent.slideUp(400, ()=>{
							$parent.remove();
							$('.tooltip').remove();
							if($('.effort-row').length <= 0){
								$('#chours').addClass('disabled').val('');
								$.cval_disabled = true;
								$('.effort-history-info').html('<div class="no-summary-found">No Effort</div>');
							}
						})
					}
				}
			})
		});
	})
</script>