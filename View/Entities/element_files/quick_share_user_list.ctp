<?php
$current_user_id = $this->Session->read('Auth.User.id');
$current_org = $this->Permission->current_org();
$project_id = $viewData['project_id'];
$element_id = $viewData['element_id'];
$perm_users = $viewData['perm_users'];
$lCount = $viewData['tot_perm_users'];
$type = $viewData['type'];
if(!empty($perm_users)) {
	foreach($perm_users as $key => $value ) {
		$userDetail = $value['UserDetail'];
		$user_org = $this->Permission->current_org($userDetail['user_id']);
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'Not Available';
		$job_title = 'Not Available';
		$html = '';
		if( $userDetail['user_id'] != $current_user_id ) {
			// $html = CHATHTML($userDetail['user_id'], $project_id);
			$html = ($this->ViewModel->is_project_shared($project_id, $userDetail['user_id'])) ? CHATHTML($userDetail['user_id'], $project_id) : CHATHTML($userDetail['user_id']);
		}
		if(isset($userDetail) && !empty($userDetail)) {
			$user_name = htmlentities($value[0]['name'], ENT_QUOTES);
			$profile_pic = $userDetail['profile_pic'];
			$job_title = htmlentities($userDetail['job_title'],ENT_QUOTES);

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
		?>
		<li class="list-group-item users" data-value="">
			<span class="quick-org-icon">
				<img  src="<?php echo $user_image; ?>" class="user-image pophover1 tipText" title="<?php echo $user_name; ?>" align="left" width="20" height="20" data-content="<div class='user-pophover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
				<?php if($current_org != $user_org){ ?>
					<i class="communitygray18 tipText" title="Not In Your Organization"></i>
				<?php } ?>
			</span>
			<?php echo $user_name; ?>
			<input type="checkbox" data-status="no" data-id="0" value="<?php echo $userDetail['user_id']; ?>" class="user-check" name="data[Share][user_id]">
		</li>
		<?php
	}
} else {
	$lCount = 0;
	echo '<li><div class="no-people-text">NO PEOPLE</div></li>';
}	?>
<script type="text/javascript" >
$('.sharing-icon').html('Select a user');
$('.submit_sharing').addClass('disabled');
$('#advance').addClass('disabled');
$('.pophover').popover({
	placement : 'bottom',
	trigger : 'hover',
	html : true,
	container: 'body',
	delay: {show: 50, hide: 400}
});
/*
* select/deselect users list items
*/
$('.perm_users li.list-group-item').on('click', function(e){
	e.preventDefault();
	if( !$(this).hasClass('active') ) {
		$('.perm_users li.list-group-item').removeClass('active');
		$('.perm_users li.list-group-item').find('input.user-check').prop('checked', false);
		$(this).toggleClass('active');

		if( $(this).hasClass('active') ) {
			$(this).find('input.user-check').prop('checked', true);
		}
		else {
			$(this).find('input.user-check').prop('checked', false);
		}

		$('#advance').addClass('disabled');
		if( $('.perm_users li.list-group-item.active').length > 0 ) {
			$('.submit_sharing').removeClass('disabled');
			$('#advance').removeClass('disabled');
		}

		if( $(this).hasClass('active') ) {
			$('.sharing-icon').html('<i class="fa fa-spinner fa-pulse" style="font-size: 23px;"></i>');
			$.ajax({
				url: $js_config.base_url + 'entities/quick_share_permissions/' + $js_config.currentProjectId + '/' + $(this).find('input.user-check').val() + '/' + $js_config.currentElementId,
				type:'POST',
				data: $.param({}),
				success: function( response, status, jxhr ) {
					setTimeout( function(){
						$('.sharing-icon').html(response);
					}, 200 )
				}
			});
		}
	}
})
/*
 * search within users list
 */
$.expr[":"].contains = $.expr.createPseudo(function(arg) {
	return function( elem ) {
		return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
	};
});

$('body').delegate('.clear-filter', 'click', function(event){
	event.preventDefault();
	$('.filter-search').val('').trigger('keyup');
	return false;
})

$('#paging_max_page').val(<?php echo $lCount;?>);
$('#paging_type').val('<?php echo $type;?>');
</script>