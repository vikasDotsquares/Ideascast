<?php
$current_user_id = $this->Session->read('Auth.User.id');
$current_org = $this->Permission->current_org();
$project_id = $viewData['project_id'];
$workspace_id = $viewData['workspace_id'];
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
			$html = ($this->ViewModel->is_project_shared(workspace_pid($workspace_id), $userDetail['user_id'])) ? CHATHTML($userDetail['user_id'], workspace_pid($workspace_id)) : CHATHTML($userDetail['user_id']);
		}
		if(isset($userDetail) && !empty($userDetail)) {
			$user_name = htmlentities($value[0]['name'], ENT_QUOTES);
			$profile_pic = $userDetail['profile_pic'];
			$job_title = htmlentities($userDetail['job_title'], ENT_QUOTES);

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
		?>
		<li class="list-group-item users" data-value=""  >
			<span class="quick-org-icon">
				<img  src="<?php echo $user_image; ?>" class="user-image pophover1 tipText" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div class='user-pophover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
				<?php if($current_org != $user_org){ ?>
					<i class="communitygray18 tipText" title="Not In Your Organization"></i>
				<?php } ?>
			</span>
			<?php echo $user_name; ?>
			<input type="checkbox" data-id="0" value="<?php echo $userDetail['user_id']; ?>" class="user-check" name="data[Share][user_id]" data-status="no">
		</li>
		<?php
	}
} else {
	$lCount = 0;
	echo '<li><div class="no-people-text">NO PEOPLE</div></li>';
}	?>
<script type="text/javascript" >
$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
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

		if( $('.perm_users li.list-group-item.active').length > 0 && $('.other_wsp li.list-group-item.active').length > 0 ) {
			$('.submit_sharing').removeClass('disabled');
			$('#advance').removeClass('disabled');

			//Call Ajax here
			if($('.other_wsp li.list-group-item.active').length == 1) {
				var u_id = $(this).find('input[type=checkbox]:checked').val();
				var wsp_id = $('.other_wsp li.list-group-item.active').find('input[type=checkbox]:checked').val();
				if(u_id > 0 && wsp_id > 0) {
					checkWSPUser(u_id, wsp_id);
				}
			}
		}
		if( $('.perm_users li.list-group-item.active').length > 0 && $('.other_wsp li.list-group-item.active').length > 0 ) {
			$('.sharing-icon label').css({'pointer-events':'unset', 'opacity': 1});
		}
		else {
			$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
		}

		if( $('.perm_users li.list-group-item.active').length > 0 && $('.perm_users li.list-group-item.active input').data('status') =='not' ) {
			$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
			$('.submit_sharing').addClass('disabled');
			$('#advance').addClass('disabled');
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