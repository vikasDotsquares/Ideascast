<?php
	$current_user_id = $this->Session->read('Auth.User.id');

	$userID = (isset($userId) && !empty($userId)) ? $userId : 0;

	$all_tasks = $this->ViewModel->getUserTasksProjectPaging($project, 0, $userID);

	$elids = $element_users = [];
	if( isset($all_tasks) && !empty($all_tasks) ) {
		foreach($all_tasks as $key => $row) {
			$user_permissions = $row['user_permissions'];
			$elids[] = $user_permissions['e_id'];
		}
		$els_users = $this->ViewModel->elementAllUsers($elids);
		if( isset($els_users) && !empty($els_users) ) {
			foreach($els_users as $key => $row) {
				$element_users[$row['user_permissions']['element_id']][] = $row;
			}
		}
	}
	// pr(count($all_tasks));
?>

<h3 class="tabing-head">Latest Task Updates <span class="btn btn-default btn-xs tipText" title="Total Tasks in Project"><i class="check-icon" aria-hidden="true"></i>  <span class="total-tasks"><?php echo ( isset($all_tasks) && !empty($all_tasks) ) ? count($all_tasks) : 0; ?></span></span></h3>
<div class="blog_wrapper paging-wrapper">
<?php if( isset($all_tasks) && !empty($all_tasks) ) {  ?>
	<?php foreach($all_tasks as $key => $row) {

		$user_permissions = $row['user_permissions'];

		$elements = $row['elements'];
		$status_short_term = $row[0]['e_status_short_term'];
		$owner_user['user_id'] = $elements['updated_user_id'];

		$userDetail = $this->ViewModel->get_user( $owner_user['user_id'], null, 1 );
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'Not Available';
		$job_title = 'Not Available';
		$html = '';
		if(isset($userDetail) && !empty($userDetail)) {

			$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
			$profile_pic = $userDetail['UserDetail']['profile_pic'];
			$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);
			// pr($user_permissions);
			/*$last_updated = $this->ViewModel->getElementLastActivity( $project, $user_permissions['workspace_id'], $user_permissions['e_id']);
			if(isset($last_updated['Activity']['updated_user_id']) && !empty($last_updated['Activity']['updated_user_id'])) {
				$owner_user['user_id'] = $last_updated['Activity']['updated_user_id'];
			}*/

			if( $owner_user['user_id'] != $current_user_id ) {
				$html = CHATHTML($owner_user['user_id'], $project);
			}

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}

	?>
			<div class="items" style="min-height: 72px;">
				<div class="thumb" style="text-align: center;">

					<?php if(isset($owner_user['user_id']) && !empty($owner_user['user_id'])) { ?>
						<a href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $owner_user['user_id'], $project, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					<?php }
					else { ?>
						<a href="#" class="pophover not-avail" data-content="<div><p><?php echo htmlentities($user_name); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo $html; ?></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  >
						</a>
					<?php } ?>

					<?php
					$el_users = $element_users[$user_permissions['e_id']];//$this->ViewModel->elementUsersFromUserPermission($user_permissions['e_id'] );

					// $el_users = element_users( [$user_permissions['e_id']], $project);
					$el_users_html = '';
					$el_users_html .= "<div class='el_users' >";
					?>

						<?php
						$user_found = false;
						if(isset($el_users) && !empty($el_users)) {

							$element_assigned = element_assigned( $user_permissions['e_id'] );
							$assign_receiver_user_id = $element_assigned['ElementAssignment']['assigned_to'];

							foreach($el_users as $ou => $d) {
								$ov = $d['user_details']['user_id'];
								$fullname = $d[0]['fullname'];
								if( $owner_user['user_id'] != $ov ) {

									$assinghtml = '';
									if( $assign_receiver_user_id == $ov ){
										$assinghtml = "&nbsp;<i class='fa fa-check text-blue' style='float:right;#0073b7 !important'></i>";
									}

									$user_found = true;
									$user_name = $fullname;
									$el_users_html .= "<a data-toggle='modal' data-target='#popup_modal' data-remote='".SITEURL."shares/show_profile/".$ov."' href='#'><i class='fa fa-user text-maroon'></i> " . $user_name .$assinghtml. "</a><br />";
								}
							?>
							<?php }
						} ?>

						<?php
							$el_users_html .= '</div>';
						?>

					<a href="#" class="btn btn-default btn-xs users_popovers" id="" style="margin: 4px 0px 0px;" data-content="<?php if($user_found) {echo $el_users_html;}else{ echo 'N/A'; } ?>">
						<i class="fa fa-user-victor"></i>
					</a>
				</div>
				<div class="description ">
					<div style="font-size: 13px; line-height: 16px;" class="description-inner">
						<a href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $user_permissions['e_id'], 'admin' => FALSE ), TRUE ); ?>" class="open-url">
							<?php echo htmlentities($elements['e_title']); ?>
						</a>
					</div>

					<div>
						<span class="element-endon status_<?php echo element_status( $user_permissions['e_id'] ); ?>">End: <?php echo (isset($elements['end_date']) && !empty($elements['end_date'])) ? _displayDate($elements['end_date'], 'd M Y') : 'Not Specified'; ?> </span>
					</div>

					<span style="font-weight: 600; font-size: 12px">Update: <?php echo (isset($elements['modified']) && !empty($elements['modified'])) ? _displayDate($elements['modified']) : 'N/A'; ?></span>

				</div>
			</div>

			<!--  -->

	<?php } ?>
	<div class="no-row-found no-more-data" >No More Data.</div>
	<?php } else { ?>
	<div class="no-row-found" >No Tasks</div>
<?php }  ?>
</div>

<script type="text/javascript" >
$(function(){

	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
	$('a[href="#"][data-toggle="modals"]').attr('href', 'javascript:;');

/* 
	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
	$('.users_popovers').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */

	$('.users_popovers,.not-avail').on('click', function(e){
			e.preventDefault();
	} )


	var outerPane = $('.paging-wrapper'),
        didScroll = false;


    outerPane.scroll(function() { //watches scroll of the div
        didScroll = true;
    });

    //Sets an interval so your div.scroll event doesn't fire constantly. This waits for the user to stop scrolling for not even a second and then fires the pageCountUpdate function (and then the getPost function)
    setInterval(function() {
        if (didScroll){
           didScroll = false;
           // if(($(document).height()-$(window).height()) - $(window).scrollTop() < 10){
            if(outerPane.scrollTop() + outerPane.innerHeight() >= outerPane[0].scrollHeight)
            {
                $.pageCountUpdate();
            }
       }
    }, 250);

})
</script>