<?php $current_org = $this->Permission->current_org(); ?>
<?php if(isset($data) && !empty($data)){

	foreach ($data as $key => $details) {
		$user_id = $details['comp_users']['user_id'];
		$user_details = $details['ud'];
        $user_name = $details[0]['fullname'];

        $profile_pic = (!empty($user_details['profile_pic'])) ? $user_details['profile_pic'] : SITEURL . 'images/placeholders/user/user_1.png';
        $user_image = SITEURL . 'images/placeholders/user/user_1.png';
        $job_title = (!empty($user_details['job_title'])) ? $user_details['job_title'] : 'Not Available';
        if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
            $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
        }
		$user_org = $this->Permission->current_org($user_id);

		$comp = $details[0]['competencies'];
		$competency = explode('$$$', $comp );


?>
	<div class="watch-col-row" data-user="<?php echo $user_id; ?>">
		<div class="watch-col watch-col-1">
			<div class="style-people-com">
				<span class="style-popple-icon-out">
					<a class="style-popple-icon" href="#" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true ); ?>">
						<img alt="User Profile Pic" src="<?php echo $user_image; ?>"  class="user-image sender" align="left" data-original-title="" title="">
					</a>
					<?php if($current_org['organization_id'] != $user_org['organization_id']){ ?>
						<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"></i>
					<?php } ?>
				</span>
				<div class="style-people-info">
					<a href="" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>">
						<span class="style-people-name" data-toggle="modal"><?php echo $user_name; ?></span>
					</a>
					<span class="style-people-title"><?php echo $job_title; ?></span>
				</div>
			</div>
		</div>
		<div class="watch-col watch-col-2">
			<span class="watch-competencies-list">
				<?php
				foreach ($competency as $key => $cdata) {
					$values = explode('$$', $cdata);
					// pr($values);
					$value = [];
					$value['comp_type'] = array_shift($values);
					$value['comp_id'] = array_shift($values);
					$value['comp_name'] = array_shift($values);
					$value['comp_level'] = array_shift($values);
					$value['comp_experience'] = array_shift($values);
					$value['comp_added_on'] = array_shift($values);

					$ctip = (isset($value['comp_added_on']) && !empty($value['comp_added_on'])) ? 'Added: '.date('d M, Y', strtotime($value['comp_added_on'])) : '';
					$level_icon = $this->Permission->level_exp_icon($value['comp_level']);
                    $exp_icon = $this->Permission->level_exp_icon($value['comp_experience'], false);
                    $exp_num = $this->Permission->exp_number($value['comp_experience']);
                    $comp_icon = ($value['comp_type'] == 'Skill') ? 'skills-icon' : ( ($value['comp_type'] == 'Subject') ? 'subjects-icon' : 'domain-icon' );
                    $comp_tip = ($value['comp_type'] == 'Skill') ? 'Skills' : ( ($value['comp_type'] == 'Subject') ? 'Subjects' : 'Domains' );
                    $comp_bg = ($value['comp_type'] == 'Skill') ? 'watch-competencies-list-bg-skill' : ( ($value['comp_type'] == 'Subject') ? 'watch-competencies-list-bg-subject' : 'watch-competencies-list-bg-domain' );
                    if($value['comp_type'] == 'Skill'){
                    	$remote = Router::Url( array( 'controller' => 'competencies', 'action' => 'view_skills', $value['comp_id'], 'admin' => FALSE ), TRUE );
                    }
                    else if($value['comp_type'] == 'Subject'){
                    	$remote = Router::Url( array( 'controller' => 'competencies', 'action' => 'view_subjects', $value['comp_id'], 'admin' => FALSE ), TRUE );
                    }
                    else{
                    	$remote = Router::Url( array( 'controller' => 'competencies', 'action' => 'view_domains', $value['comp_id'], 'admin' => FALSE ), TRUE );
                    }
				?>
				<span class="watch-competencies-list-bg <?php echo $comp_bg; ?> comp-select">
					<i class="<?php echo $comp_icon; ?> tipText" title="<?php echo $comp_tip; ?>"></i>
					<i class="<?php echo $level_icon; ?> tipText" title="" data-original-title="Level: <?php echo $value['comp_level']; ?>"></i>
					<i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$value['comp_experience'].' Years':$value['comp_experience'].' Year'; ?>"></i>
					<span data-remote="<?php echo $remote; ?>" data-target="#modal_view_skill" data-toggle="modal" title="<?php echo $ctip; ?>" class="watch-title tipText"><?php echo htmlentities($value['comp_name'], ENT_QUOTES, "UTF-8"); ?></span>
				</span>
				<?php }
				 ?>
			</span>
		</div>
		<div class="watch-col watch-col-3">
			<a href="#" class="tipText" title="Tag" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "add_tags_team_members", "type" => "search_compare", "selected" => $user_id, 'admin' => FALSE ), true ); ?>" data-target="#com_modal"><i class="tagblack"></i></a>
			<a href="#" class="tipText watch-collapse-expand" title="" data-original-title="Expand"><i class="showmoreblack"></i></a>
			<a href="<?php echo Router::Url( array( "controller" => "searches", "action" => "people", "user" => $user_id, 'admin' => FALSE ), true ); ?>" class="tipText" title="" data-original-title="Go To People"><i class="peopleblack18"></i></a>
		</div>
	</div>
	<?php } // END FOREACH ?>
<?php
}else{ ?>
<div class="no-res-found">no results</div>
<?php } ?>

<script type="text/javascript">
	$(() => {

        ;($.showHideScrollBtn = function(){
            $('.watch-col.watch-col-2', $('.watch-summary-data')).each(function(index, el) {
                var $parent = $(this).parents('.watch-col-row:first');
                var $btn = $parent.find('.watch-collapse-expand');
                if ($(this)[0].scrollHeight >  $(this).innerHeight()) {
                    $btn.show();
                }
                else{
                    $btn.hide();
                }
            });
        })();

        $(window).resize(function(event) {
            $.showHideScrollBtn();
        });
	})
</script>