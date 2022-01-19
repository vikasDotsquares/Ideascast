<?php
$current_user_id = $this->Session->read('Auth.User.id');

$project_ids = [];
if(!is_array($project_id)) {
	$project_ids = [$project_id];
}
else {
	$project_ids = array_keys($project_id);
}
$allProjectUsers = [];
foreach ($project_ids as $key => $p_id) {

	$allUsersList = null;
	$projectUsers = $this->TaskCenter->userByProject($p_id);
	if (isset($projectUsers) && !empty($projectUsers)) {
		$allProjectUsers = array_merge($allProjectUsers, array_unique($projectUsers['all_project_user']));

	}
}

foreach ($project_ids as $key => $p_id) {

	$project_detail = getByDbId('Project', $p_id);
	$project_detail = $project_detail['Project'];
	$project_title = strip_tags($project_detail['title']);

	$allUsersList = null;
	$projectUsers = $this->TaskCenter->userByProject($p_id);
	if (isset($projectUsers) && !empty($projectUsers)) {
		$projectUsers = array_unique($projectUsers['all_project_user']);
		$allUsers = $this->TaskCenter->user_exists($projectUsers);

		$allUsersList = $this->Common->usersFullname($allUsers);
		$allUsersList = Set::combine($allUsersList, '/UserDetail/user_id', '/UserDetail/full_name');
	}

?>
<?php if(isset($allUsersList) && !empty($allUsersList)) {
?>

<div class="panel panel-default">
  	<div class="panel-heading">
		<h4 class="panel-title">
	  		<span class="project-name"><i class="fa fa-briefcase"></i> <?php echo htmlentities($project_title, ENT_QUOTES, "UTF-8"); ?></span>
		</h4>
  	</div>
  	<div class="panel-collapse collapse in">
		<div class="panel-body">
			<div class="ov-list-member" style="display: none;">
			  	<ul class="ov-list-ul">
			  		<?php foreach ($allUsersList as $user_id => $user_name) {

			  			if(user_table_opt_status($user_id)) {

				  			$project_rewards = project_reward_assignments($p_id, $user_id, $type);
				  			$by_acclerate = project_accelerated_points($p_id, $user_id);
				  			$total_allocated = 0;
							if($project_rewards) {
								foreach ($project_rewards as $key => $value) {
									$amount = $value['RewardAssignment']['allocated_rewards'];
									$total_allocated += $amount;
								}
							}
							$total_allocated += $by_acclerate;

							$user_chat_popover = user_chat_popover($user_id, $p_id);
							$html = '';
							if( $user_id != $current_user_id ) {
							    $html = CHATHTML($user_id, $p_id);
							}
							$userDetail = $this->ViewModel->get_user_data($user_id);
							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'Not Available';
							$job_title = 'Not Available';
							if(isset($userDetail) && !empty($userDetail)) {
							    $user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
								$profile_pic = $userDetail['UserDetail']['profile_pic'];
								$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							    if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
							        $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							    }
							}


						?>
					  	<li class="ov-list" data-total="<?php echo $total_allocated; ?>" data-user="<?php echo $user_id; ?>">
							<span class="ov-list-img tipText" title="<?php echo $user_name; ?>" ><img src="<?php echo $user_image; ?>" alt=""></span>
							<span class="count"><?php echo $total_allocated; ?></span>
					  	</li>
					  	<?php } //END FOREACH. ?>
				  	<?php } //END USER TABLE OPT SETTING CHECK. ?>
				</ul>
			</div>
		</div>
  	</div>
</div>

<?php }
}
/*else{ ?>
	<div class="info-msg">No data found.</div>
<?php }*/ ?>
<script type="text/javascript">
    $(function(){
    	$('.popover').remove()
    	/*$('.ov-list-img').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });*/
    	setTimeout(function(){
    		$('.ov-list-member').each(function(){
    			var $this = $(this);
    			var $list = $(this).find('.ov-list-ul');
		        $('.ov-list', $this).sort(function (a, b) {
			      	var contentA =parseInt( $(a).attr('data-total'));
			      	var contentB =parseInt( $(b).attr('data-total'));
			      	return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
			   	}).prependTo($list);
		        $this.show();
	        })
        },50)

    })// END document ready
</script>