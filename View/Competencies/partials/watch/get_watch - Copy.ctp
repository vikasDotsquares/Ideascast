<?php $current_org = $this->Permission->current_org(); ?>
<?php if(isset($data) && !empty($data)){

	$dd = [];
	foreach ($data as $key => $value) {
		// pr($value);
		if(!isset($dd[$value['comp_users']['user_id']])){
			$dd[$value['comp_users']['user_id']]['user'] = ['name' => $value[0]['fullname'], 'profile_pic' => $value['ud']['profile_pic'], 'job_title' => $value['ud']['job_title'], 'organization_id' => $value['ud']['organization_id']];
		}
		/*if($value['cm']['comp_type'] == 'Skill'){
			$dd[$value['comp_users']['user_id']]['cmp']['skills'][] = $value['cm'];
		}
		else if($value['cm']['comp_type'] == 'Subject'){
			$dd[$value['comp_users']['user_id']]['cmp']['subjects'][] = $value['cm'];
		}
		else if($value['cm']['comp_type'] == 'Domain'){
			$dd[$value['comp_users']['user_id']]['cmp']['domains'][] = $value['cm'];
		}*/
		$dd[$value['comp_users']['user_id']]['cmp'][] = $value['cm'];
		// pr($value['cm']);
	}
	// pr($dd);

	// die;
	foreach ($data as $key => $value) {
		$user_id = $value['comp_users']['user_id'];
		$user_details = $value['ud'];
        $user_name = $value[0]['fullname'];

        $profile_pic = (!empty($user_details['profile_pic'])) ? $user_details['profile_pic'] : SITEURL . 'images/placeholders/user/user_1.png';
        $user_image = SITEURL . 'images/placeholders/user/user_1.png';
        $job_title = (!empty($user_details['job_title'])) ? $user_details['job_title'] : 'Not Available';
        if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
            $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
        }
		$user_org = $this->Permission->current_org($user_id);

		// pr($coloumn);
		$competency = json_decode($value[0]['detail'], true);
		/*if(isset($coloumn) && !empty($coloumn)){
			if($coloumn == 'comp_experience'){
				usort($competency, function($a, $b) {
					if ($a['exp'] == "1") return -1;
					if ($b['exp'] == "1") return 1;

					if ($a['exp'] == "2") return -1;
					if ($b['exp'] == "2") return 1;

					if ($a['exp'] == "3") return -1;
					if ($b['exp'] == "3") return 1;

					if ($a['exp'] == "4") return -1;
					if ($b['exp'] == "4") return 1;

					if ($a['exp'] == "5") return -1;
					if ($b['exp'] == "5") return 1;

					if ($a['exp'] == "6-10") return -1;
					if ($b['exp'] == "6-10") return 1;

					if ($a['exp'] == "11-15") return -1;
					if ($b['exp'] == "11-15") return 1;

					if ($a['exp'] == "16-20") return -1;
					if ($b['exp'] == "16-20") return 1;

					if ($a['exp'] == "Over 20") return -1;
					if ($b['exp'] == "Over 20") return 1;

				    return strcmp($a['exp'], $b['exp']);
				});
			}
			else if($coloumn == 'comp_level'){
				usort($competency, function($a, $b) {
					if ($a['clevel'] == "Beginner") return -1;
					if ($b['clevel'] == "Beginner") return 1;

					if ($a['clevel'] == "Intermediate") return -1;
					if ($b['clevel'] == "Intermediate") return 1;

					if ($a['clevel'] == "Advanced") return -1;
					if ($b['clevel'] == "Advanced") return 1;

					if ($a['clevel'] == "Expert") return -1;
					if ($b['clevel'] == "Expert") return 1;

				    return strcmp($a['exp'], $b['exp']);
				});
			}
		}*/
		/*usort($competency, function($a, $b) {
			if ($a['type'] == "Skill") {
		        return -1;
		    }
		    if ($b['type'] == "Skill") {
		        return 1;
		    }
			if ($a['type'] == "Subject") {
		        return -1;
		    }
		    if ($b['type'] == "Subject") {
		        return 1;
		    }
		    return strcmp($a['type'], $b['type']);
		});*/
		$skill_data = arraySearch($competency, 'type', 'Skill');
		$sub_data = arraySearch($competency, 'type', 'Subject');
		$dom_data = arraySearch($competency, 'type', 'Domain');
		// $competency1 = $skill_data + $sub_data + $dom_data;
		$competency1 = array_merge($skill_data, $sub_data, $dom_data);
		// pr($competency1);
		// pr($sub_data);
		// pr($dom_data);

?>
	<div class="watch-col-row">
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
				<?php foreach ($competency1 as $key => $value) {
					$ctip = (isset($value['created']) && !empty($value['created'])) ? 'Added: '.date('d M, Y', strtotime($value['created'])) : '';
					$level_icon = $this->Permission->level_exp_icon($value['clevel']);
                    $exp_icon = $this->Permission->level_exp_icon($value['exp'], false);
                    $exp_num = $this->Permission->exp_number($value['exp']);
                    $comp_icon = ($value['type'] == 'Skill') ? 'skills-icon' : ( ($value['type'] == 'Subject') ? 'subjects-icon' : 'domain-icon' );
                    $comp_tip = ($value['type'] == 'Skill') ? 'Skills' : ( ($value['type'] == 'Subject') ? 'Subjects' : 'Domains' );
                    $comp_bg = ($value['type'] == 'Skill') ? 'watch-competencies-list-bg-skill' : ( ($value['type'] == 'Subject') ? 'watch-competencies-list-bg-subject' : 'watch-competencies-list-bg-domain' );
                    if($value['type'] == 'Skill'){
                    	$remote = Router::Url( array( 'controller' => 'competencies', 'action' => 'view_skills', $value['cid'], 'admin' => FALSE ), TRUE );
                    }
                    else if($value['type'] == 'Subject'){
                    	$remote = Router::Url( array( 'controller' => 'competencies', 'action' => 'view_subjects', $value['cid'], 'admin' => FALSE ), TRUE );
                    }
                    else{
                    	$remote = Router::Url( array( 'controller' => 'competencies', 'action' => 'view_domains', $value['cid'], 'admin' => FALSE ), TRUE );
                    }
				?>
				<span class="watch-competencies-list-bg <?php echo $comp_bg; ?> comp-select">
					<i class="<?php echo $comp_icon; ?> tipText" title="<?php echo $comp_tip; ?>"></i>
					<i class="<?php echo $level_icon; ?> tipText" title="" data-original-title="Level: <?php echo $value['clevel']; ?>"></i>
					<i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$value['exp'].' Years':$value['exp'].' Year'; ?>"></i>
					<span data-remote="<?php echo $remote; ?>" data-target="#modal_view_skill" data-toggle="modal" title="<?php echo $ctip; ?>" class="watch-title tipText"><?php echo htmlentities($value['cname'], ENT_QUOTES, "UTF-8"); ?></span>
				</span>
				<?php } ?>
			</span>
		</div>
		<div class="watch-col watch-col-3">
			<a href="#" class="tipText" title="Tag" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "add_tags_team_members", "type" => "search_compare", "selected" => $user_id, 'admin' => FALSE ), true ); ?>" data-target="#com_modal"><i class="tagblack"></i></a>
			<a href="#" class="tipText watch-collapse-expand" title="" data-original-title="Expand"><i class="showmoreblack"></i></a>
			<a href="<?php echo Router::Url( array( "controller" => "searches", "action" => "people", "user" => $user_id, 'admin' => FALSE ), true ); ?>" class="tipText" title="" data-original-title="Go To People"><i class="peopleblack18"></i></a>
		</div>
	</div>
	<?php } // END FOREACH ?>
<?php }else{ ?>
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