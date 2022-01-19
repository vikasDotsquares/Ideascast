<?php $all_users = [];
	$current_org = $this->Permission->current_org();
    if (isset($users) && !empty($users)) {
        foreach ($users as $key => $value) {
            $all_users[] = $value['users']['id'];
        }
        $all_users = implode('~', $all_users);
        foreach ($users as $key => $value) {
            $user_id = $value['users']['id'];
            $user_details = $value['user_details'];
            $user_name = $value[0]['fullname'];

            $member_skills = $value[0]['member_skills'];
            $member_subjects = $value[0]['member_subjects'];
            $member_domains = $value[0]['member_domains'];
            $chat_html = CHATHTML($user_id);

            $profile_pic = (!empty($user_details['profile_pic'])) ? $user_details['profile_pic'] : SITEURL . 'images/placeholders/user/user_1.png';
            $user_image = SITEURL . 'images/placeholders/user/user_1.png';
            $job_title = (!empty($user_details['job_title'])) ? $user_details['job_title'] : 'Not Available';
            if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
                $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
            }
			$user_org = $this->Permission->current_org($user_id);
    ?>
    <div class="competencies-data-row" data-fname="<?php echo $user_details['first_name']; ?>" data-lname="<?php echo $user_details['last_name']; ?>">
        <div class="competencies-col-data competencies-col-data-1">
				<div class="style-people-com ">
					<span class="style-popple-icon-out">
						<a  class="style-popple-icon" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true ); ?>" class="pophover" data-content="<div class='com-user'><p><?php echo $user_name; ?></p><p><?php echo htmlentities($job_title,ENT_QUOTES); ?></p><?php echo $chat_html; ?></div>" data-original-title="" title="">
							<img src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40">
						</a>
					<?php if($current_org['organization_id'] != $user_org['organization_id']){ ?>
						<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"></i>
					<?php } ?>
					</span>

					<div class="style-people-info"  >

						<a   data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"   >
						<span class="style-people-name"><?php echo $user_details['first_name'].' '.$user_details['last_name']; ?></span>
						<span class="style-people-title"> <?php echo  $job_title; ?></span>
						</a>
					</div>
			 	</div>
        </div>
        <div class="competencies-col-data competencies-col-data-2">
            <ul class="competencies-list" data-matches="0">
                <?php // SKILL TAGS
                if(isset($member_skills) && !empty($member_skills)){
                    $member_skills_arr = explode("/;/", $member_skills);
                    // pr($member_skills_arr);
                    foreach ($member_skills_arr as $key => $value) {
                        $all = explode("/,/", $value);
                        $sk_title = (isset($all[0])) ? $all[0] : "";
                        $sk_level = (isset($all[1])) ? $all[1] : "";
                        $sk_exp = (isset($all[2])) ? $all[2] : "";
                        $sk_id = (isset($all[3])) ? $all[3] : "";
                        $sk_date = (isset($all[4])) ? $all[4] : "";

                        $ctip = (isset($sk_date) && !empty($sk_date)) ? 'Added: '.date('d M, Y', strtotime($sk_date)) : '';
                        $selected = "";
                        if(in_array($sk_id, $selected_skills, true)) {
                            $selected = "selected";
                        }
                        // $sk_detail = $this->Permission->skill_detail($user_id, $sk_id);
                        // pr($sk_detail);
                        $level_icon = $this->Permission->level_exp_icon($sk_level);
                        $exp_icon = $this->Permission->level_exp_icon($sk_exp, false);
                        $exp_num = $this->Permission->exp_number($sk_exp);
                ?>
                    <li class="<?php echo $selected; ?> data-tags" data-type="skills" data-level="<?php echo $sk_level; ?>" data-exp="<?php echo $exp_num; ?>" data-name="<?php echo htmlentities($sk_title, ENT_QUOTES, "UTF-8"); ?>">
                        <i class="skills-icon tipText" title="Skill"></i>
                        <i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $sk_level; ?>"></i>
                        <i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$sk_exp.' Years':$sk_exp.' Year'; ?>"></i>
                        <span data-remote="<?php echo SITEURL;?>competencies/view_skills/<?php echo $sk_id;?>" data-target="#modal_view_skill" data-toggle="modal" class="tipText" title="<?php echo $ctip; ?>" ><?php echo htmlentities($sk_title, ENT_QUOTES, "UTF-8"); ?></span>
                    </li>
                <?php
                    }
                }
                ?>
                <?php // SUBJECT TAGS
                if(isset($member_subjects) && !empty($member_subjects)){
                    $member_subjects_arr = explode("/;/", $member_subjects);
                    foreach ($member_subjects_arr as $key => $value) {
                        $all = explode("/,/", $value);
                        $sb_title = (isset($all[0])) ? $all[0] : "";
                        $sb_level = (isset($all[1])) ? $all[1] : "";
                        $sb_exp = (isset($all[2])) ? $all[2] : "";
                        $sb_id = (isset($all[3])) ? $all[3] : "";
                        $sb_date = (isset($all[4])) ? $all[4] : "";

                        $ctip = (isset($sb_date) && !empty($sb_date)) ? 'Added: '.date('d M, Y', strtotime($sb_date)) : '';
                        $selected = "";
                        if(in_array($sb_id, $selected_subjects, true)) {
                            $selected = "selected";
                        }
                        // $sb_detail = $this->Permission->subject_detail($user_id, $sb_id);
                        // pr($sk_detail);
                        $level_icon = $this->Permission->level_exp_icon($sb_level);
                        $exp_icon = $this->Permission->level_exp_icon($sb_exp, false);
                        $exp_num = $this->Permission->exp_number($sb_exp);
                ?>
                    <li class="<?php echo $selected; ?> data-tags" data-type="subjects" data-level="<?php echo $sb_level; ?>" data-exp="<?php echo $exp_num; ?>" data-name="<?php echo htmlentities($sb_title, ENT_QUOTES, "UTF-8"); ?>">
                        <i class="subjects-icon tipText" title="Subject"></i>
                        <i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $sb_level; ?>"></i>
                        <i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$sb_exp.' Years':$sb_exp.' Year'; ?>"></i>
                        <span data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $sb_id;?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-type="Subject" class="tipText" title="<?php echo $ctip; ?>"><?php echo htmlentities($sb_title, ENT_QUOTES, "UTF-8"); ?></span>
                    </li>
                <?php
                    }
                }
                ?>
                <?php // DOMAIN TAGS
                if(isset($member_domains) && !empty($member_domains)){
                    $member_domains_arr = explode("/;/", $member_domains);
                    foreach ($member_domains_arr as $key => $value) {
                        $all = explode("/,/", $value);
                        $dm_title = (isset($all[0])) ? $all[0] : "";
                        $dm_level =  (isset($all[1])) ? $all[1] : "";
                        $dm_exp = (isset($all[2])) ? $all[2] : "";
                        $dm_id = (isset($all[3])) ? $all[3] : "";
                        $dm_date = (isset($all[4])) ? $all[4] : "";

                        $ctip = (isset($dm_date) && !empty($dm_date)) ? 'Added: '.date('d M, Y', strtotime($dm_date)) : '';
                        $selected = "";
                        if(in_array($dm_id, $selected_domains, true)) {
                            $selected = "selected";
                        }
                        // $dm_detail = $this->Permission->domain_detail($user_id, $dm_id);
                        // pr($sk_detail);
                        $level_icon = $this->Permission->level_exp_icon($dm_level);
                        $exp_icon = $this->Permission->level_exp_icon($dm_exp, false);
                        $exp_num = $this->Permission->exp_number($dm_exp);
                ?>
                    <li class="<?php echo $selected; ?> data-tags" data-type="domains" data-level="<?php echo $dm_level; ?>" data-exp="<?php echo $exp_num; ?>" data-name="<?php echo htmlentities($dm_title, ENT_QUOTES, "UTF-8");; ?>">
                        <i class="domain-icon tipText" title="Domain"></i>
                        <i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $dm_level; ?>"></i>
                        <i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$dm_exp.' Years':$dm_exp.' Year'; ?>"></i>
                        <span data-remote="<?php echo SITEURL;?>competencies/view_domains/<?php echo $dm_id;?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" class="tipText" title="<?php echo $ctip; ?>"><?php echo htmlentities($dm_title, ENT_QUOTES, "UTF-8");; ?></span>
                    </li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
        <div class="competencies-col-data competencies-col-data-3 actionlink">
            <a href="#" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "add_tags_team_members", "type" => "competency", "cusers" => $all_users, "selected" => $user_id, 'admin' => FALSE ), true ); ?>" data-target="#com_modal" class="action-tag tipText" title="Tag"> <i class="comptag"></i> </a>
            <a href="#" class="action-col-exp tipText" title="Expand">
                <i class="more-icon"></i>
                <i class="less-icon"></i>
            </a>
        </div>
    </div>
    <?php }
    }else{ ?>
    <div class="no-res-found">NO PEOPLE</div>
    <?php } ?>



<script type="text/javascript">
    $(function(){
        $('.pophover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });

        $.showHideCollapseBtn();

        $('.competencies-list').each(function(index, el) {
            var matches = $(this).find('li.data-tags.selected').length;
            $(this).data('matches', matches);
        });

    })
</script>

<style>
.thumb {
    position: relative;
}
.reminder_user{
	padding-left: 7px;
}

</style>