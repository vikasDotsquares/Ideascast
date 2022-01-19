<?php
$current_user_id = $this->Session->read('Auth.User.id');
$current_org = $this->Permission->current_org();
$project_id = $viewData['project_id'];
$perm_users = $viewData['perm_users'];
$lCount = $viewData['tot_perm_users'];
$type = $viewData['type'];
if(!empty($perm_users)) {
	foreach($perm_users as $key => $value ) {
		$userDetail = $this->ViewModel->get_user( $key, null, 1 );
		$user_org = $this->Permission->current_org($key);
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'Not Available';
		$job_title = 'Not Available';
		$html = '';

		if( $key != $current_user_id ) {
			$html = ($this->ViewModel->is_project_shared($project_id, $key)) ? CHATHTML($key, $project_id) : CHATHTML($key);
		}
		if(isset($userDetail) && !empty($userDetail)) {
			$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
			$profile_pic = $userDetail['UserDetail']['profile_pic'];
			$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
		?>
		<li class="list-group-item users" data-value="">
			<span class="quick-org-icon">
				<img  src="<?php echo $user_image; ?>" class="user-image pophover1 tipText" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div class='user-pophover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
				<?php if($current_org != $user_org){ ?>
					<i class="communitygray18 tipText" title="Not In Your Organization"></i>
				<?php } ?>
			</span>
			<?php echo $value; ?>
			<input type="checkbox" value="<?php echo $key; ?>" class="user-check" name="data[Share][user_id]">
		</li>
		<?php
	}
} else {
	$lCount = 0;
	echo '<li><div class="no-people-text">NO PEOPLE</div></li>';
}?>
<script type="text/javascript" >
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

	// if( $(e.target).is('img.user-image') )
	// return;
	$('.perm_users li.list-group-item').removeClass('active')
	$('.perm_users li.list-group-item').find('input.user-check').prop('checked', false)
	$(this).toggleClass('active');

	if( $(this).hasClass('active') ) {
		$(this).find('input.user-check').prop('checked', true);
	}
	else {
		$(this).find('input.user-check').prop('checked', false);
	}

	$('#advance').addClass('disabled');
	if( $('.perm_users li.list-group-item.active').length > 0 ) {
		if( !$('.owner_level').prop('checked') ) {
			$('#advance').removeClass('disabled')
		}
		else {
			$('.submit_sharing').removeClass('disabled');
		}
	}
} )

/* search within users list */
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
/*var outerPane = $('.list-group.perm_users'),
            //didScroll = false;

		outerPane.scroll(function() { //watches scroll of the div
            //didScroll = true;
        });

        //Sets an interval so your div.scroll event doesn't fire constantly. This waits for the user to stop scrolling for not even a second and then fires the pageCountUpdate function (and then the getPost function)
        setInterval(function() {
            if (didScroll){
				//didScroll = false;
				//console.log(outerPane.scrollTop() +"+ "+outerPane.innerHeight() +">="+ outerPane[0].scrollHeight);
				// if(($(document).height()-$(window).height()) - $(window).scrollTop() < 10){
                if(outerPane.scrollTop() + outerPane.innerHeight() >= outerPane[0].scrollHeight)
                {
                   // $.pageCountUpdate();
                }
           }
        }, 250);*/
</script>